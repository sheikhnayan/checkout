<?php

namespace App\Http\Controllers;

use App\Models\Entertainer;
use App\Models\Setting;
use Illuminate\Support\Collection;

class EntertainerPublicController extends Controller
{
    public function show($slug)
    {
        $entertainer = Entertainer::with(['user', 'website', 'entertainerPackages.package.website'])
            ->where('slug', $slug)
            ->where('status', 'approved')
            ->where('is_active', true)
            ->firstOrFail();

        $packageMappings = $entertainer->entertainerPackages()
            ->with(['package.website', 'package.addons', 'package.event', 'package.category'])
            ->where('is_active', true)
            ->latest()
            ->get()
            ->filter(function ($mapping) {
                return $mapping->package
                    && $mapping->package->website
                    && (int) $mapping->package->website_id === (int) $mapping->website_id
                    && (int) $mapping->package->status === 1
                    && (int) ($mapping->package->is_archieved ?? 0) === 0
                    && (int) ($mapping->package->website->status ?? 0) === 1
                    && (int) ($mapping->package->website->is_archieved ?? 0) === 0;
            })
            ->values();

        $clubGroups = $packageMappings
            ->groupBy(function ($mapping) {
                return $mapping->package->website->id;
            });

        $packageCategoryGroups = $this->buildPackageCategoryGroups($packageMappings);

        $setting = Setting::find(1);

        // Reuse the affiliate public page template for pixel-identical entertainer pages.
        $affiliate = $entertainer;

        return view('affiliate.public-page', compact('affiliate', 'packageMappings', 'clubGroups', 'packageCategoryGroups', 'setting'));
    }

    private function buildPackageCategoryGroups(Collection $packageMappings)
    {
        return $packageMappings
            ->groupBy(function ($mapping) {
                $package = $mapping->package;

                return $package->website->id . '-' . ($package->package_category_id ?: 'uncategorized');
            })
            ->map(function ($group, $key) {
                $firstMapping = $group->first();
                $package = $firstMapping->package;

                return [
                    'id' => 'category-' . $key,
                    'name' => optional($package->category)->name ?: 'Uncategorized',
                    'club' => $package->website,
                    'mappings' => $group->values(),
                ];
            })
            ->values();
    }
}
