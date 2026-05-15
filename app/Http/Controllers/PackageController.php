<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use App\Models\Affiliate;
use App\Models\AffiliatePackage;
use App\Models\AffiliateWebsite;
use App\Models\Website;
use App\Models\Package;
use App\Models\Addon;
use App\Models\GeneralAddon;
use App\Models\Event;
use App\Models\Entertainer;
use App\Models\EntertainerPackage;
use App\Models\PackageCategory;

class PackageController extends Controller
{
    private const SELECT_ALL_TOKEN = '__all__';
    private const PACKAGE_FEATURE_ICON_OPTIONS = [
        'fa-chair',
        'fa-wine-bottle',
        'fa-user-shield',
        'fa-shield-alt',
        'fa-crown',
        'fa-star',
        'fa-gem',
        'fa-fire',
        'fa-bolt',
    ];

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            $data = Website::where('is_archieved',0)->get();
        } elseif ($user->isWebsiteUser() && $user->website_id) {
            // Website users can only see their own website
            $data = Website::where('id', $user->website_id)->where('is_archieved',0)->get();
        } else {
            $data = collect();
        }

        return view('admin.package.index', compact('data'));
    }

    public function archive($id)
    {
        $user = auth()->user();
        $data = Package::findOrFail($id);
        $this->authorizePackageManagement($data, $user);
        
        $data->is_archieved = 1;
        $data->status = 0;
        $data->save();

        return back();
    } 

    public function unarchive($id)
    {
        $user = auth()->user();
        $data = Package::findOrFail($id);
        $this->authorizePackageManagement($data, $user);
        
        $data->is_archieved = 0;
        $data->status = 1;
        $data->save();

        return back();
    } 

    public function toggleStatus($id)
    {
        $user = auth()->user();
        $package = Package::findOrFail($id);

        $this->authorizePackageManagement($package, $user);

        $package->status = $package->status == 1 ? 0 : 1;
        $package->save();

        return back();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        $user = auth()->user();
        
        // Check authorization for website users
        if ($user->isWebsiteUser() && $id != $user->website_id) {
            abort(403, 'Access denied. You can only create packages for your own website.');
        }
        
        [$events, $addons, $categories] = $this->packageFormDependencies((int) $id);

        return view('admin.package.create', compact('id', 'events', 'addons', 'categories'));
    }

    public function createTargeted(Request $request, string $audience)
    {
        $this->ensureTargetedPackagePermission(null, $audience);

        if (!in_array($audience, [Package::AUDIENCE_AFFILIATE, Package::AUDIENCE_ENTERTAINER], true)) {
            abort(404);
        }

        $title = $audience === Package::AUDIENCE_AFFILIATE
            ? 'Affiliate Package'
            : 'Entertainer Package';
        $user = auth()->user();
        $websiteOptions = collect();
        $selectedWebsiteId = null;
        $canSelectWebsite = false;

        if ($audience === Package::AUDIENCE_AFFILIATE) {
            $canSelectWebsite = true;
            $websiteOptions = Website::where('is_archieved', 0)
                ->orderBy('name')
                ->get(['id', 'name']);
            $selectedWebsiteId = (int) $request->query('website_id', 0);
            if ($selectedWebsiteId <= 0 || !$websiteOptions->contains('id', $selectedWebsiteId)) {
                $selectedWebsiteId = null;
            }
        } elseif ($user->isAdmin()) {
            $canSelectWebsite = true;
            $websiteOptions = Website::where('is_archieved', 0)
                ->orderBy('name')
                ->get(['id', 'name']);
            $selectedWebsiteId = (int) $request->query('website_id', 0);
            if ($selectedWebsiteId <= 0 || !$websiteOptions->contains('id', $selectedWebsiteId)) {
                $selectedWebsiteId = null;
            }
        } elseif ($user->isWebsiteUser() && $user->website_id) {
            $selectedWebsiteId = (int) $user->website_id;
        } else {
            abort(403, 'Access denied.');
        }

        [$events, $addons, $categories] = $this->packageFormDependencies($selectedWebsiteId);
        $targetOptions = $this->packageTargetOptions($selectedWebsiteId);

        return view('admin.package.create_targeted', compact(
            'audience',
            'title',
            'websiteOptions',
            'selectedWebsiteId',
            'canSelectWebsite',
            'targetOptions',
            'events',
            'addons',
            'categories'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        // Check authorization for website users
        if ($user->isWebsiteUser() && $request->website_id != $user->website_id) {
            abort(403, 'Access denied. You can only create packages for your own website.');
        }
        
        $validated = $this->validatePackagePayload($request, true);

        $add = new Package;
        $this->fillPackageFromRequest($add, $request, (int) $validated['website_id'], Package::AUDIENCE_CLUB);
        $add->save();
        $this->syncPackageAddons($add, (string) $request->addons);

        return redirect()->route('admin.package.show', $add->website_id);
    }

    public function storeTargeted(Request $request)
    {
        $audience = (string) $request->input('audience');
        $this->ensureTargetedPackagePermission(null, $audience);

        $validated = $this->validatePackagePayload($request, true, true);
        [$affiliateId, $entertainerId, $selectAllAffiliate, $selectAllEntertainer] = $this->normalizeTargetIds($request);
        $websiteId = $this->resolveTargetedPackageWebsiteId($audience, $request, $entertainerId);

        $this->validateTargetSelection($audience, $affiliateId, $entertainerId, $websiteId, $selectAllAffiliate, $selectAllEntertainer);

        $package = new Package;
        $this->fillPackageFromRequest($package, $request, $websiteId, $audience, $affiliateId, $entertainerId);
        $package->save();
        $this->syncPackageAddons($package, (string) $request->addons);
        $this->syncTargetedMappings($package);

        return redirect()->route('admin.package.show', $package->website_id);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = auth()->user();
        
        // Check authorization for website users
        if ($user->isWebsiteUser() && $id != $user->website_id) {
            abort(403, 'Access denied. You can only view packages for your own website.');
        }
        
        $data = Package::with('category')
            ->clubVisible()
            ->where('website_id', $id)
            ->get();

        $targetedPackages = Package::with(['category', 'affiliate.user', 'entertainer.user'])
            ->where('website_id', $id)
            ->whereIn('audience', [Package::AUDIENCE_AFFILIATE, Package::AUDIENCE_ENTERTAINER])
            ->get();

        $website_id = $id;

        $categories = PackageCategory::where('website_id', $id)->orderBy('name')->get();

        return view('admin.package.show', compact('data', 'targetedPackages', 'website_id', 'categories'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = auth()->user();
        $data = Package::findOrFail($id);

        if ($data->audience && $data->audience !== Package::AUDIENCE_CLUB) {
            abort(404);
        }
        
        // Check authorization for website users
        if ($user->isWebsiteUser() && $data->website_id != $user->website_id) {
            abort(403, 'Access denied. You can only edit packages for your own website.');
        }

        [$events, $addons, $categories] = $this->packageFormDependencies((int) $data->website_id);

        return view('admin.package.edit', compact('data', 'id', 'events', 'addons', 'categories'));
    }

    public function editTargeted(string $id)
    {
        $data = Package::findOrFail($id);
        if (!in_array($data->audience, [Package::AUDIENCE_AFFILIATE, Package::AUDIENCE_ENTERTAINER], true)) {
            abort(404);
        }

        $this->ensureTargetedPackagePermission($data);

        $selectedWebsiteId = (int) request()->query('website_id', $data->website_id);
        $websiteOptions = collect();
        $canSelectWebsite = false;
        $title = $data->audience === Package::AUDIENCE_AFFILIATE
            ? 'Affiliate Package'
            : 'Entertainer Package';

        if (auth()->user()->isAdmin()) {
            $canSelectWebsite = true;
            $websiteOptions = Website::where('is_archieved', 0)
                ->orderBy('name')
                ->get(['id', 'name']);

            if (!$websiteOptions->contains('id', $selectedWebsiteId)) {
                $selectedWebsiteId = (int) $data->website_id;
            }
        } else {
            $selectedWebsiteId = (int) $data->website_id;
        }

        [$events, $addons, $categories] = $this->packageFormDependencies($selectedWebsiteId);
        $targetOptions = $this->packageTargetOptions($selectedWebsiteId);
        $audience = $data->audience;
        $selectAllAffiliate = false;
        $selectAllEntertainer = false;

        if ($audience === Package::AUDIENCE_AFFILIATE && !$data->affiliate_id) {
            $mappedCount = AffiliatePackage::where('package_id', $data->id)
                ->where('website_id', $selectedWebsiteId)
                ->count();
            $availableCount = (int) $targetOptions['affiliates']->count();
            $selectAllAffiliate = $availableCount > 0 && $mappedCount === $availableCount;
        }

        if ($audience === Package::AUDIENCE_ENTERTAINER && !$data->entertainer_id) {
            $mappedCount = EntertainerPackage::where('package_id', $data->id)
                ->where('website_id', $selectedWebsiteId)
                ->count();
            $availableCount = (int) $targetOptions['entertainers']->count();
            $selectAllEntertainer = $availableCount > 0 && $mappedCount === $availableCount;
        }

        return view('admin.package.edit_targeted', compact(
            'data',
            'id',
            'audience',
            'title',
            'events',
            'addons',
            'categories',
            'targetOptions',
            'websiteOptions',
            'selectedWebsiteId',
            'canSelectWebsite',
            'selectAllAffiliate',
            'selectAllEntertainer'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = auth()->user();
        $data = Package::findOrFail($id);

        if ($data->audience && $data->audience !== Package::AUDIENCE_CLUB) {
            abort(404);
        }
        
        // Check authorization for website users
        if ($user->isWebsiteUser() && $data->website_id != $user->website_id) {
            abort(403, 'Access denied. You can only update packages for your own website.');
        }
        
        $this->validatePackagePayload($request);
        $this->fillPackageFromRequest($data, $request, (int) $data->website_id, Package::AUDIENCE_CLUB);
        $data->save();
        $this->clearTargetedMappings($data);
        $this->syncPackageAddons($data, (string) $request->addons);

        return redirect()->route('admin.package.show', $data->website_id);

    }

    public function updateTargeted(Request $request, string $id)
    {
        $data = Package::findOrFail($id);
        if (!in_array($data->audience, [Package::AUDIENCE_AFFILIATE, Package::AUDIENCE_ENTERTAINER], true)) {
            abort(404);
        }

        $this->ensureTargetedPackagePermission($data, $data->audience);

        $this->validatePackagePayload($request, true, true);
        [$affiliateId, $entertainerId, $selectAllAffiliate, $selectAllEntertainer] = $this->normalizeTargetIds($request);
        $websiteId = $this->resolveTargetedPackageWebsiteId($data->audience, $request, $entertainerId, $data);

        $this->validateTargetSelection($data->audience, $affiliateId, $entertainerId, $websiteId, $selectAllAffiliate, $selectAllEntertainer);

        $this->fillPackageFromRequest($data, $request, $websiteId, $data->audience, $affiliateId, $entertainerId);
        $data->save();
        $this->syncPackageAddons($data, (string) $request->addons);
        $this->syncTargetedMappings($data);

        return redirect()->route('admin.package.show', $data->website_id);
    }

    private function resolveCategoryId(Request $request, $websiteId)
    {
        $newCategoryName = trim((string) $request->input('new_category_name'));

        if ($newCategoryName !== '') {
            return PackageCategory::firstOrCreate([
                'website_id' => (int) $websiteId,
                'name' => $newCategoryName,
            ])->id;
        }

        $categoryId = $request->input('category_id');

        if (!$categoryId) {
            return null;
        }

        $category = PackageCategory::where('id', $categoryId)
            ->where('website_id', (int) $websiteId)
            ->first();

        return $category ? $category->id : null;
    }

    private function packageFormDependencies(?int $websiteId): array
    {
        if (!$websiteId) {
            return [collect(), collect(), collect()];
        }

        $events = Event::where('website_id', $websiteId)
            ->where('is_archieved', 0)
            ->get();

        $addons = GeneralAddon::where('website_id', $websiteId)
            ->where('is_archieved', 0)
            ->where('status', 1)
            ->get();

        $categories = PackageCategory::where('website_id', $websiteId)
            ->orderBy('name')
            ->get();

        return [$events, $addons, $categories];
    }

    private function validatePackagePayload(Request $request, bool $requireWebsiteId = false, bool $isTargeted = false): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'package_feature_text' => 'nullable|array',
            'package_feature_text.*' => 'nullable|string|max:120',
            'package_feature_icon' => 'nullable|array',
            'package_feature_icon.*' => ['nullable', Rule::in(self::PACKAGE_FEATURE_ICON_OPTIONS)],
            'status' => 'required|in:0,1',
            'website_id' => [$requireWebsiteId ? 'required' : 'nullable', 'integer', 'exists:websites,id'],
            'package_type' => 'required|in:ticket,table',
            'daily_ticket_limit' => 'required_if:package_type,ticket|nullable|integer|min:1',
            'daily_table_limit' => 'required_if:package_type,table|nullable|integer|min:1',
            'guests_per_table' => 'required_if:package_type,table|nullable|integer|min:1',
            'addons' => 'nullable|string',
            'multiple' => 'nullable',
            'transportation' => 'nullable',
            'event_id' => $isTargeted ? 'prohibited' : 'nullable|integer',
            'category_id' => 'nullable|integer',
            'new_category_name' => 'nullable|string|max:255',
        ];

        if ($isTargeted) {
            $rules['audience'] = ['required', Rule::in([Package::AUDIENCE_AFFILIATE, Package::AUDIENCE_ENTERTAINER])];
            $rules['affiliate_id'] = 'nullable|string';
            $rules['entertainer_id'] = 'nullable|string';
        }

        return $request->validate($rules);
    }

    private function fillPackageFromRequest(
        Package $package,
        Request $request,
        int $websiteId,
        string $audience,
        ?int $affiliateId = null,
        ?int $entertainerId = null
    ): void {
        $package->name = $request->name;
        $package->price = $request->price;
        $package->description = $request->description;
        $package->package_features = $this->normalizePackageFeatures(
            (array) $request->input('package_feature_text', []),
            (array) $request->input('package_feature_icon', [])
        );
        $package->status = $request->status;
        $package->multiple = $request->boolean('multiple') ? 1 : 0;
        $package->transportation = $request->boolean('transportation') ? 1 : 0;
        $package->package_type = $request->input('package_type', 'ticket');
        $package->website_id = $websiteId;
        $package->audience = $audience;
        $package->affiliate_id = $affiliateId;
        $package->entertainer_id = $entertainerId;

        if ($package->package_type === 'table') {
            $package->daily_table_limit = $request->input('daily_table_limit');
            $package->guests_per_table = $request->input('guests_per_table');
            $package->daily_ticket_limit = null;
        } else {
            $package->daily_ticket_limit = $request->input('daily_ticket_limit');
            $package->daily_table_limit = null;
            $package->guests_per_table = null;
        }

        $package->package_category_id = $this->resolveCategoryId($request, $websiteId);
        $package->event_id = $audience === Package::AUDIENCE_CLUB
            ? $this->resolveEventId($request, $websiteId)
            : null;
    }

    private function syncPackageAddons(Package $package, string $addonList): void
    {
        Addon::where('package_id', $package->id)->delete();

        $addons = array_filter(explode(',', $addonList));

        foreach ($addons as $value) {
            $addon = GeneralAddon::where('id', $value)->first();
            if (!$addon) {
                continue;
            }

            $addona = new Addon;
            $addona->name = $addon->name;
            $addona->addon_id = $addon->id;
            $addona->price = $addon->price;
            $addona->description = $addon->description;
            $addona->status = $addon->status;
            $addona->package_id = $package->id;
            $addona->save();
        }
    }

    private function normalizePackageFeatures(array $texts, array $icons): ?array
    {
        $features = [];

        foreach ($texts as $index => $text) {
            $label = trim((string) $text);
            $icon = trim((string) ($icons[$index] ?? ''));

            if ($label === '') {
                continue;
            }

            if (!in_array($icon, self::PACKAGE_FEATURE_ICON_OPTIONS, true)) {
                $icon = 'fa-chair';
            }

            $features[] = [
                'icon' => $icon,
                'text' => $label,
            ];
        }

        return !empty($features) ? $features : null;
    }

    private function authorizePackageManagement(Package $package, $user): void
    {
        if ($user->isWebsiteUser() && $package->website_id != $user->website_id) {
            abort(403, 'Access denied. You can only manage packages for your own website.');
        }

        if (in_array($package->audience, [Package::AUDIENCE_AFFILIATE, Package::AUDIENCE_ENTERTAINER], true)) {
            $this->ensureTargetedPackagePermission($package);
        }
    }

    private function ensureTargetedPackagePermission(?Package $package = null, ?string $audience = null): void
    {
        $user = auth()->user();
        $resolvedAudience = $audience ?: ($package->audience ?? Package::AUDIENCE_CLUB);

        if ($resolvedAudience === Package::AUDIENCE_CLUB) {
            return;
        }

        if ($resolvedAudience === Package::AUDIENCE_AFFILIATE && (!$user || !$user->isAdmin())) {
            abort(403, 'Only super admin can create or manage affiliate-specific packages.');
        }

        if ($resolvedAudience === Package::AUDIENCE_ENTERTAINER) {
            if ($user && $user->isAdmin()) {
                return;
            }

            if ($user && $user->isWebsiteUser()) {
                if ($package && (int) $package->website_id !== (int) $user->website_id) {
                    abort(403, 'Access denied. You can only manage entertainer packages for your own club.');
                }

                return;
            }

            abort(403, 'Only super admin and website admins can create or manage entertainer-specific packages.');
        }
    }

    private function packageTargetOptions(?int $websiteId = null): array
    {
        $affiliates = Affiliate::with('user')
            ->where('status', 'approved')
            ->where('is_active', true)
            ->when($websiteId, function ($query) use ($websiteId) {
                $query->whereHas('affiliateWebsites', function ($targetQuery) use ($websiteId) {
                    $targetQuery->where('website_id', $websiteId)
                        ->where('is_active', true);
                });
            })
            ->orderBy('display_name')
            ->get();

        $entertainers = Entertainer::with('user')
            ->when($websiteId, function ($query) use ($websiteId) {
                $query->where('website_id', $websiteId);
            })
            ->where('status', 'approved')
            ->where('is_active', true)
            ->orderBy('display_name')
            ->get();

        return [
            'affiliates' => $affiliates,
            'entertainers' => $entertainers,
        ];
    }

    private function normalizeTargetIds(Request $request): array
    {
        $audience = (string) $request->input('audience');
        $rawAffiliateId = trim((string) $request->input('affiliate_id', ''));
        $rawEntertainerId = trim((string) $request->input('entertainer_id', ''));

        $selectAllAffiliate = $audience === Package::AUDIENCE_AFFILIATE && $rawAffiliateId === self::SELECT_ALL_TOKEN;
        $selectAllEntertainer = $audience === Package::AUDIENCE_ENTERTAINER && $rawEntertainerId === self::SELECT_ALL_TOKEN;

        $affiliateId = ($audience === Package::AUDIENCE_AFFILIATE && !$selectAllAffiliate && ctype_digit($rawAffiliateId))
            ? (int) $rawAffiliateId
            : 0;
        $entertainerId = ($audience === Package::AUDIENCE_ENTERTAINER && !$selectAllEntertainer && ctype_digit($rawEntertainerId))
            ? (int) $rawEntertainerId
            : 0;

        return [
            $affiliateId > 0 ? $affiliateId : null,
            $entertainerId > 0 ? $entertainerId : null,
            $selectAllAffiliate,
            $selectAllEntertainer,
        ];
    }

    private function validateTargetSelection(
        string $audience,
        ?int $affiliateId,
        ?int $entertainerId,
        ?int $websiteId,
        bool $selectAllAffiliate = false,
        bool $selectAllEntertainer = false
    ): void
    {
        if ($audience === Package::AUDIENCE_AFFILIATE) {
            if ($selectAllAffiliate) {
                if (!$websiteId) {
                    throw ValidationException::withMessages([
                        'website_id' => 'Select the club this affiliate package belongs to.',
                    ]);
                }

                $hasAffiliates = Affiliate::where('status', 'approved')
                    ->where('is_active', true)
                    ->whereHas('affiliateWebsites', function ($query) use ($websiteId) {
                        $query->where('website_id', $websiteId)
                            ->where('is_active', true);
                    })
                    ->exists();

                if (!$hasAffiliates) {
                    throw ValidationException::withMessages([
                        'affiliate_id' => 'No active affiliates found for the selected club.',
                    ]);
                }

                return;
            }

            if (!$affiliateId) {
                throw ValidationException::withMessages([
                    'affiliate_id' => 'Select an affiliate or choose Select All Affiliates.',
                ]);
            }

            if (!$websiteId) {
                throw ValidationException::withMessages([
                    'website_id' => 'Select the club this affiliate package belongs to.',
                ]);
            }

            $validAffiliate = Affiliate::where('id', $affiliateId)
                ->where('status', 'approved')
                ->where('is_active', true)
                ->whereHas('affiliateWebsites', function ($query) use ($websiteId) {
                    $query->where('website_id', $websiteId)
                        ->where('is_active', true);
                })
                ->exists();

            if (!$validAffiliate) {
                throw ValidationException::withMessages([
                    'affiliate_id' => 'Selected affiliate is not active for this club.',
                ]);
            }

            return;
        }

        if ($selectAllEntertainer) {
            $hasEntertainers = Entertainer::query()
                ->when($websiteId, function ($query) use ($websiteId) {
                    $query->where('website_id', $websiteId);
                })
                ->where('status', 'approved')
                ->where('is_active', true)
                ->exists();

            if (!$hasEntertainers) {
                throw ValidationException::withMessages([
                    'entertainer_id' => 'No active entertainers found for the selected club.',
                ]);
            }

            return;
        }

        if (!$entertainerId) {
            throw ValidationException::withMessages([
                'entertainer_id' => 'Select an entertainer or choose Select All Entertainers.',
            ]);
        }

        $validEntertainer = Entertainer::where('id', $entertainerId)
            ->when($websiteId, function ($query) use ($websiteId) {
                $query->where('website_id', $websiteId);
            })
            ->where('status', 'approved')
            ->where('is_active', true)
            ->exists();

        if (!$validEntertainer) {
            throw ValidationException::withMessages([
                'entertainer_id' => 'Selected entertainer is not active for this club.',
            ]);
        }
    }

    private function resolveTargetedPackageWebsiteId(string $audience, Request $request, ?int $entertainerId, ?Package $existingPackage = null): int
    {
        $user = auth()->user();

        if ($audience === Package::AUDIENCE_ENTERTAINER) {
            if ($user && $user->isWebsiteUser() && $user->website_id) {
                return (int) $user->website_id;
            }

            $requestedWebsiteId = (int) $request->input('website_id', $existingPackage?->website_id);
            if ($requestedWebsiteId > 0) {
                return $requestedWebsiteId;
            }

            if ($entertainerId) {
                return (int) optional(Entertainer::find($entertainerId))->website_id;
            }
        }

        return (int) $request->input('website_id', $existingPackage?->website_id);
    }

    private function clearTargetedMappings(Package $package): void
    {
        AffiliatePackage::where('package_id', $package->id)->delete();
        EntertainerPackage::where('package_id', $package->id)->delete();
    }

    private function syncTargetedMappings(Package $package): void
    {
        $this->clearTargetedMappings($package);

        if ($package->audience === Package::AUDIENCE_AFFILIATE && $package->affiliate_id) {
            $affiliate = Affiliate::find($package->affiliate_id);
            AffiliatePackage::updateOrCreate(
                [
                    'affiliate_id' => $package->affiliate_id,
                    'package_id' => $package->id,
                ],
                [
                    'website_id' => $package->website_id,
                    'commission_percentage' => $affiliate?->default_commission_percentage ?? 0,
                    'is_active' => true,
                ]
            );
        } elseif ($package->audience === Package::AUDIENCE_AFFILIATE) {
            $affiliates = Affiliate::where('status', 'approved')
                ->where('is_active', true)
                ->whereHas('affiliateWebsites', function ($query) use ($package) {
                    $query->where('website_id', $package->website_id)
                        ->where('is_active', true);
                })
                ->get(['id', 'default_commission_percentage']);

            foreach ($affiliates as $affiliate) {
                AffiliatePackage::updateOrCreate(
                    [
                        'affiliate_id' => $affiliate->id,
                        'package_id' => $package->id,
                    ],
                    [
                        'website_id' => $package->website_id,
                        'commission_percentage' => $affiliate->default_commission_percentage ?? 0,
                        'is_active' => true,
                    ]
                );
            }
        }

        if ($package->audience === Package::AUDIENCE_ENTERTAINER && $package->entertainer_id) {
            EntertainerPackage::updateOrCreate(
                [
                    'entertainer_id' => $package->entertainer_id,
                    'package_id' => $package->id,
                ],
                [
                    'website_id' => $package->website_id,
                    'is_active' => true,
                ]
            );
        } elseif ($package->audience === Package::AUDIENCE_ENTERTAINER) {
            $entertainers = Entertainer::where('website_id', $package->website_id)
                ->where('status', 'approved')
                ->where('is_active', true)
                ->get(['id']);

            foreach ($entertainers as $entertainer) {
                EntertainerPackage::updateOrCreate(
                    [
                        'entertainer_id' => $entertainer->id,
                        'package_id' => $package->id,
                    ],
                    [
                        'website_id' => $package->website_id,
                        'is_active' => true,
                    ]
                );
            }
        }
    }

    private function resolveEventId(Request $request, int $websiteId): ?int
    {
        $rawEventId = $request->input('event_id');

        if ($rawEventId === null || $rawEventId === '' || $rawEventId === 'null') {
            return null;
        }

        $eventId = (int) $rawEventId;

        if ($eventId <= 0) {
            return null;
        }

        $eventQuery = Event::where('id', $eventId)
            ->where('website_id', $websiteId)
            ->where('is_archieved', 0);

        if (\Illuminate\Support\Facades\Schema::hasColumn('events', 'status')) {
            $eventQuery->where('status', 1);
        }

        return $eventQuery->exists() ? $eventId : null;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
