<?php

namespace App\Http\Controllers;

use App\Models\Affiliate;
use App\Models\AffiliatePackage;
use App\Models\AffiliateWebsite;
use App\Models\Package;
use App\Models\Transaction;
use App\Models\Website;
use App\Services\CommissionLifecycleRunner;
use Illuminate\Http\Request;

class AffiliatePortalController extends Controller
{
    private function decodeGalleryImages(?string $value): array
    {
        if (!$value) {
            return [];
        }

        $decoded = json_decode($value, true);
        if (!is_array($decoded)) {
            return [];
        }

        return array_values(array_filter(array_map(function ($image) {
            return is_string($image) ? trim($image) : '';
        }, $decoded), function ($image) {
            return $image !== '';
        }));
    }

    private function normalizeImageFiles($files): array
    {
        if (!$files) {
            return [];
        }

        $flattened = [];
        $stack = is_array($files) ? $files : [$files];

        array_walk_recursive($stack, function ($file) use (&$flattened) {
            if ($file instanceof \Illuminate\Http\UploadedFile) {
                $flattened[] = $file;
            }
        });

        return $flattened;
    }

    private function getAffiliateOrAbort(): affiliate
    {
        $user = auth()->user();

        if (!$user || !$user->isAffiliate() || !$user->affiliate || $user->affiliate->status !== 'approved' || !$user->affiliate->is_active) {
            abort(403, 'affiliate access denied.');
        }

        return $user->affiliate;
    }

    public function dashboard()
    {
        app(CommissionLifecycleRunner::class)->runSafely();

        $affiliate = $this->getAffiliateOrAbort();
        $affiliate->loadCount('affiliatePackages');

        $commissions = $affiliate->walletTransactions()->where('type', 'commission')->sum('amount');

        return view('affiliate.dashboard', compact('affiliate', 'commissions'));
    }

    public function packages()
    {
        $affiliate = $this->getAffiliateOrAbort();

        $allowedWebsiteIds = AffiliateWebsite::where('affiliate_id', $affiliate->id)
            ->where('is_active', true)
            ->pluck('website_id')
            ->toArray();

        $websites = Website::where('is_archieved', 0)
            ->where('status', 1)
            ->whereIn('id', $allowedWebsiteIds)
            ->with(['packages' => function ($query) {
                $query->clubVisible()
                    ->where('status', 1)
                    ->where(function ($q) {
                        $q->whereNull('is_archieved')->orWhere('is_archieved', 0);
                    });
            }])
            ->get();

        $selected = AffiliatePackage::where('affiliate_id', $affiliate->id)
            ->whereIn('website_id', $allowedWebsiteIds)
            ->pluck('package_id')
            ->toArray();

        return view('affiliate.packages', compact('affiliate', 'websites', 'selected'));
    }

    public function savePackages(Request $request)
    {
        $affiliate = $this->getAffiliateOrAbort();

        $request->validate([
            'package_ids' => 'nullable|array',
            'package_ids.*' => 'integer|exists:packages,id',
        ]);

        $allowedWebsiteIds = AffiliateWebsite::where('affiliate_id', $affiliate->id)
            ->where('is_active', true)
            ->pluck('website_id')
            ->toArray();

        if (empty($allowedWebsiteIds)) {
            AffiliatePackage::where('affiliate_id', $affiliate->id)->delete();
            return redirect()->back()->with('success', 'No clubs assigned yet. Package selection cleared.');
        }

        $requestedPackageIds = collect($request->input('package_ids', []))->map(fn ($id) => (int) $id)->unique()->values();

        $packageIds = Package::whereIn('id', $requestedPackageIds->all())
            ->clubVisible()
            ->whereIn('website_id', $allowedWebsiteIds)
            ->where('status', 1)
            ->where(function ($q) {
                $q->whereNull('is_archieved')->orWhere('is_archieved', 0);
            })
            ->pluck('id')
            ->values();

        $clubPackageIds = Package::query()
            ->clubVisible()
            ->whereIn('website_id', $allowedWebsiteIds)
            ->pluck('id')
            ->values();

        AffiliatePackage::where('affiliate_id', $affiliate->id)
            ->whereIn('website_id', $allowedWebsiteIds)
            ->whereIn('package_id', $clubPackageIds->all())
            ->whereNotIn('package_id', $packageIds->all())
            ->delete();

        foreach ($packageIds as $packageId) {
            $package = Package::find($packageId);
            if (!$package) {
                continue;
            }

            AffiliatePackage::updateOrCreate(
                [
                    'affiliate_id' => $affiliate->id,
                    'package_id' => $packageId,
                ],
                [
                    'website_id' => $package->website_id,
                    'commission_percentage' => $affiliate->default_commission_percentage,
                    'is_active' => true,
                ]
            );
        }

        return redirect()->back()->with('success', 'Packages updated for assigned clubs successfully.');
    }

    public function settings()
    {
        $affiliate = $this->getAffiliateOrAbort();
        return view('affiliate.settings', compact('affiliate'));
    }

    public function updateSettings(Request $request)
    {
        $affiliate = $this->getAffiliateOrAbort();

        $request->validate([
            'display_name' => 'required|string|max:255',
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string|max:500',
            'hero_badge_1_label' => 'nullable|string|max:80',
            'hero_badge_1_sub' => 'nullable|string|max:120',
            'hero_badge_2_label' => 'nullable|string|max:80',
            'hero_badge_2_sub' => 'nullable|string|max:120',
            'description' => 'nullable|string|max:5000',
            'secondary_description' => 'nullable|string|max:5000',
            'show_location_section' => 'nullable|boolean',
            'featured_icon' => 'nullable|string|max:50',
            'facebook_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'youtube_url' => 'nullable|url|max:255',
            'tiktok_url' => 'nullable|url|max:255',
            'font_family' => 'nullable|string|max:120',
            'profile_image' => 'nullable|image|max:4096',
            'banner_image' => 'nullable|image|max:4096',
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'nullable|image|max:4096',
            'existing_gallery_images' => 'nullable|string',
            'remove_gallery_images' => 'nullable|array',
            'remove_gallery_images.*' => 'nullable|integer|min:0',
        ]);

        $affiliate->fill($request->only([
            'display_name',
            'hero_title',
            'hero_subtitle',
            'hero_badge_1_label',
            'hero_badge_1_sub',
            'hero_badge_2_label',
            'hero_badge_2_sub',
            'description',
            'secondary_description',
            'featured_icon',
            'facebook_url',
            'instagram_url',
            'youtube_url',
            'tiktok_url',
            'font_family',
        ]));

        $affiliate->show_location_section = $request->boolean('show_location_section');

        if ($request->hasFile('profile_image')) {
            $name = 'affiliate_profile_' . $affiliate->id . '_' . time() . '.' . $request->file('profile_image')->getClientOriginalExtension();
            $request->file('profile_image')->move(public_path('uploads'), $name);
            $affiliate->profile_image = $name;
        }

        if ($request->hasFile('banner_image')) {
            $name = 'affiliate_banner_' . $affiliate->id . '_' . time() . '.' . $request->file('banner_image')->getClientOriginalExtension();
            $request->file('banner_image')->move(public_path('uploads'), $name);
            $affiliate->banner_image = $name;
        }

        $currentGalleryImages = array_values(array_filter((array) $affiliate->gallery_images));
        $existingGalleryImages = $this->decodeGalleryImages($request->input('existing_gallery_images'));

        if (!empty($existingGalleryImages)) {
            $existingGalleryImages = array_values(array_filter($existingGalleryImages, function ($image) use ($currentGalleryImages) {
                return in_array($image, $currentGalleryImages, true);
            }));
            $galleryImages = collect($existingGalleryImages);
        } else {
            $galleryImages = collect($currentGalleryImages);
            $removeGalleryKeys = collect((array) $request->input('remove_gallery_images', []))
                ->map(fn ($value) => (int) $value)
                ->unique();

            if ($removeGalleryKeys->isNotEmpty()) {
                $galleryImages = $galleryImages->reject(function ($image, $index) use ($removeGalleryKeys) {
                    return $removeGalleryKeys->contains((int) $index);
                })->values();
            }
        }

        foreach ($this->normalizeImageFiles($request->file('gallery_images')) as $index => $image) {
            if ($galleryImages->count() >= 6) {
                return redirect()->back()
                    ->withErrors(['gallery_images' => 'Gallery is full. Remove one image before uploading another.'])
                    ->withInput();
            }

            $name = 'affiliate_gallery_' . $affiliate->id . '_' . time() . '_' . $index . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads'), $name);
            $galleryImages->push($name);
        }

        $affiliate->gallery_images = $galleryImages->values()->all();

        $affiliate->save();

        return redirect()->back()->with('success', 'affiliate page settings updated successfully.');
    }

    public function wallet()
    {
        app(CommissionLifecycleRunner::class)->runSafely();

        $affiliate = $this->getAffiliateOrAbort();
        $transactions = $affiliate->walletTransactions()
            ->with('transaction')
            ->where(function ($query) {
                $query->whereNull('transaction_id')
                    ->orWhereHas('transaction');
            })
            ->latest()
            ->paginate(20);

        if ($affiliate->isSubAffiliate()) {
            $affiliateIds = [$affiliate->id];
        } else {
            $subIds = Affiliate::where('parent_affiliate_id', $affiliate->id)->pluck('id')->toArray();
            $affiliateIds = array_merge([$affiliate->id], $subIds);
        }

        $bookingTransactions = Transaction::whereIn('affiliate_id', $affiliateIds)
            ->with(['website', 'event', 'package', 'affiliate.user'])
            ->latest()
            ->get();

        return view('affiliate.wallet', compact('affiliate', 'transactions', 'bookingTransactions'));
    }

    /**
     * List parent's sub-affiliates.
     */
    public function subAffiliates()
    {
        $affiliate = $this->getAffiliateOrAbort();
        if ($affiliate->isSubAffiliate()) {
            abort(403, 'Sub-promoters cannot manage sub-promoters.');
        }

        $subAffiliates = Affiliate::where('parent_affiliate_id', $affiliate->id)
            ->with(['user', 'affiliateWebsites', 'affiliatePackages.package'])
            ->orderBy('id', 'desc')
            ->get();

        // Allowed websites and packages for this parent affiliate
        $allowedWebsiteIds = AffiliateWebsite::where('affiliate_id', $affiliate->id)
            ->where('is_active', true)
            ->pluck('website_id')
            ->toArray();

        $websites = Website::where('is_archieved', 0)
            ->where('status', 1)
            ->whereIn('id', $allowedWebsiteIds)
            ->with(['packages' => function ($q) use ($affiliate) {
                $parentPackageIds = AffiliatePackage::where('affiliate_id', $affiliate->id)
                    ->pluck('package_id')
                    ->toArray();
                $q->clubVisible()->where('status', 1)->where('is_archieved', 0)->whereIn('id', $parentPackageIds);
            }])
            ->get();

        return view('affiliate.sub_affiliates', compact('affiliate', 'subAffiliates', 'websites'));
    }

    /**
     * Store new sub-affiliate created by parent promoter.
     */
    public function storeSubAffiliate(Request $request)
    {
        $affiliate = $this->getAffiliateOrAbort();
        if ($affiliate->isSubAffiliate()) {
            abort(403, 'Sub-promoters cannot create sub-promoters.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'display_name' => 'nullable|string|max:255',
            'website_ids' => 'nullable|array',
            'package_ids' => 'nullable|array',
        ]);

        $parentWebsiteIds = AffiliateWebsite::where('affiliate_id', $affiliate->id)->where('is_active', true)->pluck('website_id')->toArray();
        $allowedWebsiteIds = array_values(array_intersect($request->input('website_ids', []), $parentWebsiteIds));

        $parentPackageIds = AffiliatePackage::where('affiliate_id', $affiliate->id)->pluck('package_id')->toArray();
        $allowedPackageIds = array_values(array_intersect($request->input('package_ids', []), $parentPackageIds));

        $requireForm = $request->boolean('require_onboarding_form');

        // Create User
        $user = \App\Models\User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
            'user_type' => 'affiliate',
        ]);

        $displayName = $request->input('display_name') ?: $request->input('name');
        $slug = Affiliate::generateUniqueSlug($displayName);

        // Create Sub-Affiliate
        $sub = Affiliate::create([
            'user_id' => $user->id,
            'parent_affiliate_id' => $affiliate->id,
            'is_sub_affiliate' => true,
            'display_name' => $displayName,
            'slug' => $slug,
            'status' => $requireForm ? 'pending' : 'approved',
            'is_active' => true,
            'approved_at' => $requireForm ? null : now(),
            'approved_by' => $requireForm ? null : auth()->id(),
            'require_onboarding_form' => $requireForm,
            'sub_affiliate_permissions' => [
                'show_packages' => $request->boolean('show_packages', true),
                'show_settings' => $request->boolean('show_settings', true),
                'show_qr_code' => $request->boolean('show_qr_code', true),
                'show_sales_stats' => $request->boolean('show_sales_stats', true),
            ],
        ]);

        // Allocate Websites
        foreach ($allowedWebsiteIds as $webId) {
            AffiliateWebsite::create([
                'affiliate_id' => $sub->id,
                'website_id' => $webId,
                'is_active' => true,
            ]);
        }

        // Allocate Packages
        foreach ($allowedPackageIds as $pkgId) {
            $parentPkg = AffiliatePackage::where('affiliate_id', $affiliate->id)->where('package_id', $pkgId)->first();
            AffiliatePackage::create([
                'affiliate_id' => $sub->id,
                'package_id' => $pkgId,
                'website_id' => $parentPkg->website_id ?? null,
                'commission_percentage' => 0,
                'is_active' => true,
            ]);
        }

        // Mail Notification
        try {
            $subject = "Welcome to CartVIP Promoter Portal";
            if ($requireForm) {
                $onboardUrl = route('entertainer.apply');
                $html = "<div style='font-family:sans-serif;padding:20px;'><h3 style='color:#4f46e5;'>Welcome {$user->name}</h3><p>You have been added as a sub-promoter by <strong>{$affiliate->display_name}</strong>.</p><p>Please complete your onboarding application form to activate your portal: <a href='{$onboardUrl}' style='color:#4f46e5;font-weight:bold;'>Complete Form</a></p><p>Login Email: <strong>{$user->email}</strong></p></div>";
            } else {
                $loginUrl = route('login');
                $html = "<div style='font-family:sans-serif;padding:20px;'><h3 style='color:#4f46e5;'>Welcome {$user->name}</h3><p>You have been added as an active sub-promoter by <strong>{$affiliate->display_name}</strong>.</p><p>Login Email: <strong>{$user->email}</strong><br>Password: <strong>{$request->input('password')}</strong></p><p><a href='{$loginUrl}' style='color:#4f46e5;font-weight:bold;'>Click here to login</a></p></div>";
            }
            \Illuminate\Support\Facades\Mail::html($html, function($msg) use ($user, $subject) {
                $msg->to($user->email)->subject($subject);
            });
        } catch (\Exception $e) {
            // Log mail error silently
        }

        return redirect()->back()->with('success', 'Sub-promoter successfully created and allocated clubs/packages!');
    }

    /**
     * Update existing sub-affiliate permissions, allocated clubs, and packages.
     */
    public function updateSubAffiliate(Request $request, Affiliate $subAffiliate)
    {
        $affiliate = $this->getAffiliateOrAbort();
        if ($subAffiliate->parent_affiliate_id !== $affiliate->id) {
            abort(403, 'Unauthorized sub-promoter edit.');
        }

        $request->validate([
            'display_name' => 'required|string|max:255',
            'website_ids' => 'nullable|array',
            'package_ids' => 'nullable|array',
        ]);

        $parentWebsiteIds = AffiliateWebsite::where('affiliate_id', $affiliate->id)->where('is_active', true)->pluck('website_id')->toArray();
        $allowedWebsiteIds = array_values(array_intersect($request->input('website_ids', []), $parentWebsiteIds));

        $parentPackageIds = AffiliatePackage::where('affiliate_id', $affiliate->id)->pluck('package_id')->toArray();
        $allowedPackageIds = array_values(array_intersect($request->input('package_ids', []), $parentPackageIds));

        $subAffiliate->display_name = $request->input('display_name');
        $subAffiliate->sub_affiliate_permissions = [
            'show_packages' => $request->boolean('show_packages'),
            'show_settings' => $request->boolean('show_settings'),
            'show_qr_code' => $request->boolean('show_qr_code'),
            'show_sales_stats' => $request->boolean('show_sales_stats'),
        ];
        $subAffiliate->require_onboarding_form = $request->boolean('require_onboarding_form');
        $subAffiliate->save();

        // Sync Allocated Websites
        AffiliateWebsite::where('affiliate_id', $subAffiliate->id)->delete();
        foreach ($allowedWebsiteIds as $webId) {
            AffiliateWebsite::create([
                'affiliate_id' => $subAffiliate->id,
                'website_id' => $webId,
                'is_active' => true,
            ]);
        }

        // Sync Allocated Packages
        AffiliatePackage::where('affiliate_id', $subAffiliate->id)->delete();
        foreach ($allowedPackageIds as $pkgId) {
            $parentPkg = AffiliatePackage::where('affiliate_id', $affiliate->id)->where('package_id', $pkgId)->first();
            AffiliatePackage::create([
                'affiliate_id' => $subAffiliate->id,
                'package_id' => $pkgId,
                'website_id' => $parentPkg->website_id ?? null,
                'commission_percentage' => 0,
                'is_active' => true,
            ]);
        }

        return redirect()->back()->with('success', 'Sub-promoter settings and allocated clubs/packages updated successfully!');
    }

    /**
     * Toggle active status of sub-affiliate.
     */
    public function toggleSubAffiliateStatus(Affiliate $subAffiliate)
    {
        $affiliate = $this->getAffiliateOrAbort();
        if ($subAffiliate->parent_affiliate_id !== $affiliate->id) {
            abort(403, 'Unauthorized sub-promoter status update.');
        }

        $subAffiliate->is_active = !$subAffiliate->is_active;
        $subAffiliate->save();

        return redirect()->back()->with('success', 'Sub-promoter status updated.');
    }
}
