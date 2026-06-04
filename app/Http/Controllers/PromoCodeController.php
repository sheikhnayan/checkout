<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Affiliate;
use App\Models\Entertainer;
use App\Models\Package;
use App\Models\PromoCode;
use App\Models\Website;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PromoCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $targetedPromos = collect();
        
        if ($user->isAdmin()) {
            $data = Website::where('is_archieved',0)->get();
            $targetedPromos = PromoCode::with(['promoter.user', 'entertainer.user'])
                ->whereIn('audience', [PromoCode::AUDIENCE_AFFILIATE, PromoCode::AUDIENCE_ENTERTAINER])
                ->orderByDesc('id')
                ->get();
        } elseif ($user->isWebsiteUser() && $user->website_id) {
            // Website users can only see their own website
            $data = Website::where('id', $user->website_id)->where('is_archieved',0)->get();
        } else {
            $data = collect();
        }

        return view('admin.promo_code.index', compact('data', 'targetedPromos'));
    }

    public function archive($id)
    {
        $user = auth()->user();
        $data = PromoCode::where('id',$id)->first();
        
        // Check authorization for website users
        if ($user->isWebsiteUser() && $data->website_id != $user->website_id) {
            abort(403, 'Access denied. You can only manage promo codes for your own website.');
        }

        $this->ensureTargetedPromoPermission($data);
        
        $data->is_archieved = 1;
        $data->update();

        return back();
    } 

    public function unarchive($id)
    {
        $user = auth()->user();
        $data = PromoCode::where('id',$id)->first();
        
        // Check authorization for website users
        if ($user->isWebsiteUser() && $data->website_id != $user->website_id) {
            abort(403, 'Access denied. You can only manage promo codes for your own website.');
        }

        $this->ensureTargetedPromoPermission($data);
        
        $data->is_archieved = 0;
        $data->update();

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
            abort(403, 'Access denied. You can only create promo codes for your own website.');
        }

        $promoAudience = PromoCode::AUDIENCE_CLUB;
        $title = 'Club Promo Code';
        $packageOptions = $this->websitePackages((int) $id);

        return view('admin.promo_code.create', compact('id', 'promoAudience', 'title', 'packageOptions'));
    }

    public function createTargeted(Request $request, string $audience)
    {
        $this->ensureTargetedPromoPermission(null, $audience);

        if (!in_array($audience, [PromoCode::AUDIENCE_AFFILIATE, PromoCode::AUDIENCE_ENTERTAINER], true)) {
            abort(404);
        }

        $promoAudience = $audience;
        $id = null;
        $title = $audience === PromoCode::AUDIENCE_AFFILIATE
            ? 'Promoter Promo Code'
            : 'Entertainer Promo Code';
        $user = auth()->user();
        $websiteOptions = collect();
        $selectedWebsiteId = null;
        $canSelectWebsite = false;

        if ($audience === PromoCode::AUDIENCE_ENTERTAINER) {
            if ($user->isAdmin()) {
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
                $id = $selectedWebsiteId;
            } else {
                abort(403, 'Access denied.');
            }
        }

        $targetOptions = $this->promoTargetOptions($selectedWebsiteId);
        $packageOptions = $this->websitePackages($selectedWebsiteId);

        return view('admin.promo_code.create', compact(
            'id',
            'promoAudience',
            'title',
            'targetOptions',
            'websiteOptions',
            'selectedWebsiteId',
            'canSelectWebsite',
            'packageOptions'
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
            abort(403, 'Access denied. You can only create promo codes for your own website.');
        }

        $audience = (string) $request->input('audience', PromoCode::AUDIENCE_CLUB);
        $this->ensureTargetedPromoPermission(null, $audience);

        $request->validate([
            'website_id' => [Rule::requiredIf(in_array($audience, [PromoCode::AUDIENCE_CLUB, PromoCode::AUDIENCE_ENTERTAINER], true)), 'nullable', 'integer', 'exists:websites,id'],
            'name' => 'nullable|string|max:255',
            'audience' => ['required', Rule::in(PromoCode::ALLOWED_AUDIENCES)],
            'affiliate_id' => 'nullable|integer|exists:promoters,id',
            'entertainer_id' => 'nullable|integer|exists:entertainers,id',
                'promo_code' => [
                    Rule::requiredIf($request->input('discount_method') !== PromoCode::DISCOUNT_METHOD_AUTOMATIC),
                    'nullable',
                    'string',
                    'max:100',
                ],
            'discount_method' => ['required', Rule::in([PromoCode::DISCOUNT_METHOD_CODE, PromoCode::DISCOUNT_METHOD_AUTOMATIC])],
            'discount_value_type' => ['required', Rule::in([PromoCode::DISCOUNT_TYPE_PERCENTAGE, PromoCode::DISCOUNT_TYPE_FIXED])],
            'discount_value' => 'required|numeric|min:0',
            'applies_to' => ['required', Rule::in([PromoCode::APPLIES_TO_ALL_PACKAGES, PromoCode::APPLIES_TO_SPECIFIC_PACKAGES])],
            'applies_to_package_ids' => 'nullable|array',
            'applies_to_package_ids.*' => 'nullable|integer',
            'eligibility' => 'nullable|string|max:50',
            'min_requirement_type' => ['nullable', Rule::in([PromoCode::MIN_REQUIREMENT_NONE, PromoCode::MIN_REQUIREMENT_AMOUNT, PromoCode::MIN_REQUIREMENT_QUANTITY])],
            'min_purchase_amount' => 'nullable|numeric|min:0',
            'min_purchase_quantity' => 'nullable|integer|min:1',
            'usage_limit_total' => 'nullable|integer|min:1',
            'limit_one_per_customer' => 'nullable|boolean',
            'combine_product_discounts' => 'nullable|boolean',
            'combine_order_discounts' => 'nullable|boolean',
            'combine_shipping_discounts' => 'nullable|boolean',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'is_active' => 'nullable|boolean',
            'description' => 'nullable|string|max:1000',
        ]);

        [$affiliateId, $entertainerId] = $this->normalizeTargetIds($request);
        $websiteId = $this->resolvePromoWebsiteId($audience, $request, $affiliateId, $entertainerId);
        $this->validateTargetSelection($audience, $affiliateId, $entertainerId, $websiteId);
        if ($request->input('discount_method') !== PromoCode::DISCOUNT_METHOD_AUTOMATIC || $request->filled('promo_code')) {
            $this->ensurePromoCodeIsUnique($websiteId, strtoupper(trim((string) $request->promo_code)), $audience, $affiliateId, $entertainerId);
        }

        $packageIds = $this->normalizePackageIdsForWebsite($request->input('applies_to_package_ids', []), $websiteId);
        if ($request->input('applies_to') === PromoCode::APPLIES_TO_SPECIFIC_PACKAGES && empty($packageIds)) {
            throw ValidationException::withMessages([
                'applies_to_package_ids' => 'Select at least one package when Applies To is set to specific packages.',
            ]);
        }
        
        $add = new PromoCode;
        $add->name = $request->name;
        $add->percentage = $request->discount_value;
            $add->promo_code = ($request->input('discount_method') === PromoCode::DISCOUNT_METHOD_AUTOMATIC && !$request->filled('promo_code'))
                ? null
                : strtoupper(trim((string) $request->promo_code));
        $add->type = $request->discount_value_type;
        $add->audience = $audience;
        $add->affiliate_id = $affiliateId;
        $add->entertainer_id = $entertainerId;
        $add->description = $request->description;
        $add->website_id = $websiteId;
        $add->discount_method = $request->input('discount_method', PromoCode::DISCOUNT_METHOD_CODE);
        $add->discount_value_type = $request->discount_value_type;
        $add->discount_value = $request->discount_value;
        $add->applies_to = $request->input('applies_to', PromoCode::APPLIES_TO_ALL_PACKAGES);
        $add->applies_to_package_ids = !empty($packageIds) ? array_values($packageIds) : null;
        $add->eligibility = $request->input('eligibility', 'all_customers');
        $add->min_requirement_type = $request->input('min_requirement_type', PromoCode::MIN_REQUIREMENT_NONE);
        $add->min_purchase_amount = $request->filled('min_purchase_amount') ? $request->input('min_purchase_amount') : null;
        $add->min_purchase_quantity = $request->filled('min_purchase_quantity') ? (int) $request->input('min_purchase_quantity') : null;
        $add->usage_limit_total = $request->filled('usage_limit_total') ? (int) $request->input('usage_limit_total') : null;
        $add->limit_one_per_customer = $request->boolean('limit_one_per_customer');
        $add->combine_product_discounts = $request->boolean('combine_product_discounts');
        $add->combine_order_discounts = $request->boolean('combine_order_discounts');
        $add->combine_shipping_discounts = $request->boolean('combine_shipping_discounts');
        $add->starts_at = $request->input('starts_at');
        $add->ends_at = $request->input('ends_at');
        $add->is_active = $request->has('is_active') ? $request->boolean('is_active') : true;
        $add->save();

        return $audience === PromoCode::AUDIENCE_CLUB
            ? redirect()->route('admin.promo_code.show', $add->website_id)
            : redirect()->route('admin.promo_code.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = auth()->user();
        
        // Check authorization for website users
        if ($user->isWebsiteUser() && $id != $user->website_id) {
            abort(403, 'Access denied. You can only view promo codes for your own website.');
        }
        
        $data = PromoCode::where('website_id', $id)
        ->with(['promoter.user', 'entertainer.user'])
        ->get();

        $website_id = $id;

        return view('admin.promo_code.show', compact('data', 'website_id'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = auth()->user();
        $data = PromoCode::find($id);
        
        // Check authorization for website users
        if ($user->isWebsiteUser() && $data->website_id != $user->website_id) {
            abort(403, 'Access denied. You can only edit promo codes for your own website.');
        }

        $this->ensureTargetedPromoPermission($data);

        $promoAudience = $data->audience ?? PromoCode::AUDIENCE_CLUB;
        $title = match ($promoAudience) {
            PromoCode::AUDIENCE_AFFILIATE => 'Promoter Promo Code',
            PromoCode::AUDIENCE_ENTERTAINER => 'Entertainer Promo Code',
            default => 'Club Promo Code',
        };
        $targetOptions = $promoAudience === PromoCode::AUDIENCE_CLUB ? [] : $this->promoTargetOptions((int) $data->website_id);
        $packageOptions = $this->websitePackages((int) $data->website_id);

        return view('admin.promo_code.edit', compact('data', 'id', 'targetOptions', 'promoAudience', 'title', 'packageOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = auth()->user();
        $add = PromoCode::findOrFail($id);
        
        // Check authorization for website users
        if ($user->isWebsiteUser() && $add->website_id != $user->website_id) {
            abort(403, 'Access denied. You can only update promo codes for your own website.');
        }

        $audience = (string) $add->audience;
        $this->ensureTargetedPromoPermission($add, $audience);

        $request->validate([
            'name' => 'nullable|string|max:255',
            'affiliate_id' => 'nullable|integer|exists:promoters,id',
            'entertainer_id' => 'nullable|integer|exists:entertainers,id',
                'promo_code' => [
                    Rule::requiredIf($request->input('discount_method') !== PromoCode::DISCOUNT_METHOD_AUTOMATIC),
                    'nullable',
                    'string',
                    'max:100',
                ],
            'discount_method' => ['required', Rule::in([PromoCode::DISCOUNT_METHOD_CODE, PromoCode::DISCOUNT_METHOD_AUTOMATIC])],
            'discount_value_type' => ['required', Rule::in([PromoCode::DISCOUNT_TYPE_PERCENTAGE, PromoCode::DISCOUNT_TYPE_FIXED])],
            'discount_value' => 'required|numeric|min:0',
            'applies_to' => ['required', Rule::in([PromoCode::APPLIES_TO_ALL_PACKAGES, PromoCode::APPLIES_TO_SPECIFIC_PACKAGES])],
            'applies_to_package_ids' => 'nullable|array',
            'applies_to_package_ids.*' => 'nullable|integer',
            'eligibility' => 'nullable|string|max:50',
            'min_requirement_type' => ['nullable', Rule::in([PromoCode::MIN_REQUIREMENT_NONE, PromoCode::MIN_REQUIREMENT_AMOUNT, PromoCode::MIN_REQUIREMENT_QUANTITY])],
            'min_purchase_amount' => 'nullable|numeric|min:0',
            'min_purchase_quantity' => 'nullable|integer|min:1',
            'usage_limit_total' => 'nullable|integer|min:1',
            'limit_one_per_customer' => 'nullable|boolean',
            'combine_product_discounts' => 'nullable|boolean',
            'combine_order_discounts' => 'nullable|boolean',
            'combine_shipping_discounts' => 'nullable|boolean',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'is_active' => 'nullable|boolean',
            'description' => 'nullable|string|max:1000',
        ]);

        [$affiliateId, $entertainerId] = $this->normalizeTargetIds($request);
        $websiteId = $this->resolvePromoWebsiteId($audience, $request, $affiliateId, $entertainerId, $add);
        $this->validateTargetSelection($audience, $affiliateId, $entertainerId, $websiteId);
            if ($request->input('discount_method') !== PromoCode::DISCOUNT_METHOD_AUTOMATIC || $request->filled('promo_code')) {
                $this->ensurePromoCodeIsUnique($websiteId, strtoupper(trim((string) $request->promo_code)), $audience, $affiliateId, $entertainerId, $add->id);
            }

        $packageIds = $this->normalizePackageIdsForWebsite($request->input('applies_to_package_ids', []), $websiteId);
        if ($request->input('applies_to') === PromoCode::APPLIES_TO_SPECIFIC_PACKAGES && empty($packageIds)) {
            throw ValidationException::withMessages([
                'applies_to_package_ids' => 'Select at least one package when Applies To is set to specific packages.',
            ]);
        }
        
        $add->name = $request->name;
        $add->percentage = $request->discount_value;
            $add->promo_code = ($request->input('discount_method') === PromoCode::DISCOUNT_METHOD_AUTOMATIC && !$request->filled('promo_code'))
                ? null
                : strtoupper(trim((string) $request->promo_code));
        $add->type = $request->discount_value_type;
        $add->affiliate_id = $affiliateId;
        $add->entertainer_id = $entertainerId;
        $add->description = $request->description;
        $add->website_id = $websiteId;
        $add->discount_method = $request->input('discount_method', PromoCode::DISCOUNT_METHOD_CODE);
        $add->discount_value_type = $request->discount_value_type;
        $add->discount_value = $request->discount_value;
        $add->applies_to = $request->input('applies_to', PromoCode::APPLIES_TO_ALL_PACKAGES);
        $add->applies_to_package_ids = !empty($packageIds) ? array_values($packageIds) : null;
        $add->eligibility = $request->input('eligibility', 'all_customers');
        $add->min_requirement_type = $request->input('min_requirement_type', PromoCode::MIN_REQUIREMENT_NONE);
        $add->min_purchase_amount = $request->filled('min_purchase_amount') ? $request->input('min_purchase_amount') : null;
        $add->min_purchase_quantity = $request->filled('min_purchase_quantity') ? (int) $request->input('min_purchase_quantity') : null;
        $add->usage_limit_total = $request->filled('usage_limit_total') ? (int) $request->input('usage_limit_total') : null;
        $add->limit_one_per_customer = $request->boolean('limit_one_per_customer');
        $add->combine_product_discounts = $request->boolean('combine_product_discounts');
        $add->combine_order_discounts = $request->boolean('combine_order_discounts');
        $add->combine_shipping_discounts = $request->boolean('combine_shipping_discounts');
        $add->starts_at = $request->input('starts_at');
        $add->ends_at = $request->input('ends_at');
        $add->is_active = $request->has('is_active') ? $request->boolean('is_active') : ($add->is_active ?? true);
        $add->update();

        return $audience === PromoCode::AUDIENCE_CLUB
            ? redirect()->route('admin.promo_code.show', $add->website_id)
            : redirect()->route('admin.promo_code.index');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    private function ensureTargetedPromoPermission(?PromoCode $promo = null, ?string $audience = null): void
    {
        $user = auth()->user();
        $resolvedAudience = $audience ?: ($promo->audience ?? PromoCode::AUDIENCE_CLUB);

        if ($resolvedAudience === PromoCode::AUDIENCE_CLUB) {
            return;
        }

        if ($resolvedAudience === PromoCode::AUDIENCE_AFFILIATE && (!$user || !$user->isAdmin())) {
            abort(403, 'Only super admin can create or manage promoter-specific promo codes.');
        }

        if ($resolvedAudience === PromoCode::AUDIENCE_ENTERTAINER) {
            if ($user && $user->isAdmin()) {
                return;
            }

            if ($user && $user->isWebsiteUser()) {
                if ($promo && (int) $promo->website_id !== (int) $user->website_id) {
                    abort(403, 'Access denied. You can only manage entertainer promo codes for your own club.');
                }

                return;
            }

            abort(403, 'Only super admin and website admins can create or manage entertainer-specific promo codes.');
        }
    }

    private function promoTargetOptions(?int $websiteId = null): array
    {
        $promoters = Affiliate::with('user')
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
            'promoters' => $promoters,
            'entertainers' => $entertainers,
        ];
    }

    private function normalizeTargetIds(Request $request): array
    {
        $audience = (string) $request->input('audience', PromoCode::AUDIENCE_CLUB);
        $affiliateId = $audience === PromoCode::AUDIENCE_AFFILIATE ? (int) $request->input('affiliate_id') : 0;
        $entertainerId = $audience === PromoCode::AUDIENCE_ENTERTAINER ? (int) $request->input('entertainer_id') : 0;

        return [
            $affiliateId > 0 ? $affiliateId : null,
            $entertainerId > 0 ? $entertainerId : null,
        ];
    }

    private function validateTargetSelection(string $audience, ?int $affiliateId, ?int $entertainerId, ?int $websiteId = null): void
    {
        if ($audience === PromoCode::AUDIENCE_CLUB) {
            return;
        }

        if ($audience === PromoCode::AUDIENCE_AFFILIATE) {
            if (!$affiliateId) {
                throw ValidationException::withMessages([
                    'affiliate_id' => 'Select the promoter this promo code belongs to.',
                ]);
            }

            $validAffiliate = Affiliate::where('id', $affiliateId)
                ->where('status', 'approved')
                ->where('is_active', true)
                ->exists();

            if (!$validAffiliate) {
                throw ValidationException::withMessages([
                    'affiliate_id' => 'Selected promoter is not active for this club.',
                ]);
            }

            return;
        }

        if (!$entertainerId) {
            throw ValidationException::withMessages([
                'entertainer_id' => 'Select the entertainer this promo code belongs to.',
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

    private function ensurePromoCodeIsUnique(?int $websiteId, string $promoCode, string $audience, ?int $affiliateId, ?int $entertainerId, ?int $ignoreId = null): void
    {
        $query = PromoCode::query()
            ->whereRaw('LOWER(promo_code) = ?', [strtolower($promoCode)])
            ->where('audience', $audience);

        if ($audience === PromoCode::AUDIENCE_CLUB) {
            $query->where('website_id', $websiteId);
        }

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        if ($audience === PromoCode::AUDIENCE_AFFILIATE) {
            $query->where('affiliate_id', $affiliateId)->whereNull('entertainer_id');
        } elseif ($audience === PromoCode::AUDIENCE_ENTERTAINER) {
            $query->where('entertainer_id', $entertainerId)->whereNull('affiliate_id');
        } else {
            $query->whereNull('affiliate_id')->whereNull('entertainer_id');
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'promo_code' => 'This promo code already exists for the selected destination.',
            ]);
        }
    }

    private function resolvePromoWebsiteId(string $audience, Request $request, ?int $affiliateId, ?int $entertainerId, ?PromoCode $existingPromo = null): ?int
    {
        if ($audience === PromoCode::AUDIENCE_CLUB) {
            return (int) $request->input('website_id', $existingPromo?->website_id);
        }

        if ($audience === PromoCode::AUDIENCE_ENTERTAINER) {
            $user = auth()->user();

            if ($user && $user->isWebsiteUser() && $user->website_id) {
                return (int) $user->website_id;
            }

            $requestedWebsiteId = (int) $request->input('website_id', $existingPromo?->website_id);
            if ($requestedWebsiteId > 0) {
                return $requestedWebsiteId;
            }

            if ($entertainerId) {
                return (int) optional(Entertainer::find($entertainerId))->website_id ?: null;
            }

            return null;
        }

        return null;
    }

    private function normalizePackageIdsForWebsite(array $packageIds, ?int $websiteId): array
    {
        $ids = collect($packageIds)
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return [];
        }

        $query = Package::query()->whereIn('id', $ids->all());
        if ($websiteId) {
            $query->where('website_id', $websiteId);
        }

        return $query->pluck('id')->map(fn ($id) => (int) $id)->all();
    }

    private function websitePackages(?int $websiteId): array
    {
        if (!$websiteId) {
            return [];
        }

        return Package::where('website_id', $websiteId)
            ->where('is_archieved', 0)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($pkg) => ['id' => (int) $pkg->id, 'name' => (string) $pkg->name])
            ->all();
    }
}
