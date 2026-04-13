<?php

namespace App\Http\Controllers;

use App\Models\Affiliate;
use App\Models\AffiliatePackage;
use App\Models\AffiliateWebsite;
use App\Models\Package;
use App\Models\Website;
use Illuminate\Http\Request;

class AffiliatePortalController extends Controller
{
    private function getAffiliateOrAbort(): Affiliate
    {
        $user = auth()->user();

        if (!$user || !$user->isAffiliate() || !$user->affiliate || $user->affiliate->status !== 'approved' || !$user->affiliate->is_active) {
            abort(403, 'Affiliate access denied.');
        }

        return $user->affiliate;
    }

    public function dashboard()
    {
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
                $query->where('status', 1)->where('is_archieved', 0);
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
            ->whereIn('website_id', $allowedWebsiteIds)
            ->where('status', 1)
            ->where('is_archieved', 0)
            ->pluck('id')
            ->values();

        AffiliatePackage::where('affiliate_id', $affiliate->id)
            ->whereIn('website_id', $allowedWebsiteIds)
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
            'description' => 'nullable|string|max:5000',
            'secondary_description' => 'nullable|string|max:5000',
            'show_location_section' => 'nullable|boolean',
            'facebook_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'youtube_url' => 'nullable|url|max:255',
            'tiktok_url' => 'nullable|url|max:255',
            'font_family' => 'nullable|string|max:120',
            'profile_image' => 'nullable|image|max:4096',
            'banner_image' => 'nullable|image|max:4096',
            'gallery_image' => 'nullable|image|max:4096',
            'remove_gallery_images' => 'nullable|array',
            'remove_gallery_images.*' => 'nullable|integer|min:0',
        ]);

        $affiliate->fill($request->only([
            'display_name',
            'hero_title',
            'hero_subtitle',
            'description',
            'secondary_description',
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

        $galleryImages = collect((array) $affiliate->gallery_images)->values();
        $removeGalleryKeys = collect((array) $request->input('remove_gallery_images', []))
            ->map(fn ($value) => (int) $value)
            ->unique();

        if ($removeGalleryKeys->isNotEmpty()) {
            $galleryImages = $galleryImages->reject(function ($image, $index) use ($removeGalleryKeys) {
                return $removeGalleryKeys->contains((int) $index);
            })->values();
        }

        if ($request->hasFile('gallery_image')) {
            if ($galleryImages->count() >= 6) {
                return redirect()->back()
                    ->withErrors(['gallery_image' => 'Gallery is full. Remove one image before uploading another.'])
                    ->withInput();
            }

            $image = $request->file('gallery_image');
            $name = 'affiliate_gallery_' . $affiliate->id . '_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads'), $name);
            $galleryImages->push($name);
        }

        $affiliate->gallery_images = $galleryImages->values()->all();

        $affiliate->save();

        return redirect()->back()->with('success', 'Affiliate page settings updated successfully.');
    }

    public function wallet()
    {
        $affiliate = $this->getAffiliateOrAbort();
        $transactions = $affiliate->walletTransactions()->latest()->paginate(20);

        return view('affiliate.wallet', compact('affiliate', 'transactions'));
    }
}
