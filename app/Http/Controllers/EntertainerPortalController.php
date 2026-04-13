<?php

namespace App\Http\Controllers;

use App\Models\Entertainer;
use App\Models\EntertainerPackage;
use App\Models\Package;
use Illuminate\Http\Request;

class EntertainerPortalController extends Controller
{
    private function getEntertainerOrAbort(): Entertainer
    {
        $user = auth()->user();

        if (!$user || !$user->isEntertainer() || !$user->entertainer || $user->entertainer->status !== 'approved' || !$user->entertainer->is_active) {
            abort(403, 'Entertainer access denied.');
        }

        return $user->entertainer;
    }

    public function dashboard()
    {
        $entertainer = $this->getEntertainerOrAbort();
        $entertainer->load(['website']);
        $entertainer->loadCount('entertainerPackages');

        $commissions = $entertainer->walletTransactions()->where('type', 'commission')->sum('amount');

        return view('entertainer.dashboard', compact('entertainer', 'commissions'));
    }

    public function packages()
    {
        $entertainer = $this->getEntertainerOrAbort();

        $packages = Package::query()
            ->where('website_id', $entertainer->website_id)
            ->where('status', 1)
            ->where('is_archieved', 0)
            ->orderBy('name')
            ->get();

        $selected = EntertainerPackage::where('entertainer_id', $entertainer->id)
            ->where('website_id', $entertainer->website_id)
            ->pluck('package_id')
            ->toArray();

        return view('entertainer.packages', compact('entertainer', 'packages', 'selected'));
    }

    public function savePackages(Request $request)
    {
        $entertainer = $this->getEntertainerOrAbort();

        $request->validate([
            'package_ids' => 'nullable|array',
            'package_ids.*' => 'integer|exists:packages,id',
        ]);

        $requestedPackageIds = collect($request->input('package_ids', []))->map(fn ($id) => (int) $id)->unique()->values();

        $packageIds = Package::whereIn('id', $requestedPackageIds->all())
            ->where('website_id', $entertainer->website_id)
            ->where('status', 1)
            ->where('is_archieved', 0)
            ->pluck('id')
            ->values();

        EntertainerPackage::where('entertainer_id', $entertainer->id)
            ->where('website_id', $entertainer->website_id)
            ->whereNotIn('package_id', $packageIds->all())
            ->delete();

        foreach ($packageIds as $packageId) {
            EntertainerPackage::updateOrCreate(
                [
                    'entertainer_id' => $entertainer->id,
                    'package_id' => $packageId,
                ],
                [
                    'website_id' => $entertainer->website_id,
                    'is_active' => true,
                ]
            );
        }

        return redirect()->back()->with('success', 'Packages updated successfully.');
    }

    public function settings()
    {
        $entertainer = $this->getEntertainerOrAbort();
        $entertainer->load(['feedModel.performanceDates']);
        return view('entertainer.settings', compact('entertainer'));
    }

    public function updateSettings(Request $request)
    {
        $entertainer = $this->getEntertainerOrAbort();

        $request->validate([
            'display_name' => 'required|string|max:255',
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:5000',
            'secondary_description' => 'nullable|string|max:5000',
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

        $entertainer->fill($request->only([
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

        if ($request->hasFile('profile_image')) {
            $name = 'entertainer_profile_' . $entertainer->id . '_' . time() . '.' . $request->file('profile_image')->getClientOriginalExtension();
            $request->file('profile_image')->move(public_path('uploads'), $name);
            $entertainer->profile_image = $name;
        }

        if ($request->hasFile('banner_image')) {
            $name = 'entertainer_banner_' . $entertainer->id . '_' . time() . '.' . $request->file('banner_image')->getClientOriginalExtension();
            $request->file('banner_image')->move(public_path('uploads'), $name);
            $entertainer->banner_image = $name;
        }

        $galleryImages = collect((array) $entertainer->gallery_images)->values();
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
            $name = 'entertainer_gallery_' . $entertainer->id . '_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads'), $name);
            $galleryImages->push($name);
        }

        $entertainer->gallery_images = $galleryImages->values()->all();

        $entertainer->save();

        if ($entertainer->feed_model_id) {
            $feedModel = $entertainer->feedModel;
            if ($feedModel) {
                $feedModel->name = $entertainer->display_name ?: $entertainer->user->name;
                if (!empty($entertainer->profile_image)) {
                    $feedModel->profile_image = $entertainer->profile_image;
                }
                $feedModel->bio = $entertainer->description;
                $feedModel->save();
            }
        }

        return redirect()->back()->with('success', 'Entertainer page settings updated successfully.');
    }

    public function wallet()
    {
        $entertainer = $this->getEntertainerOrAbort();
        $transactions = $entertainer->walletTransactions()->latest()->paginate(20);

        return view('entertainer.wallet', compact('entertainer', 'transactions'));
    }
}
