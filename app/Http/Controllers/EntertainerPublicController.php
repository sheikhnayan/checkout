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
            ->with(['package.website', 'package.category', 'package.addons', 'package.event'])
            ->where('is_active', true)
            ->latest()
            ->get()
            ->filter(function ($mapping) {
                if (!$mapping->package || !$mapping->package->website) {
                    return false;
                }

                $isCategoryArchived = (int) ($mapping->package->category->is_archieved ?? 0) === 1;

                return (int) $mapping->package->website_id === (int) $mapping->website_id
                    && (int) $mapping->package->status === 1
                    && (int) ($mapping->package->is_archieved ?? 0) === 0
                    && !$isCategoryArchived
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

        return view('entertainer.public-page', compact('entertainer', 'packageMappings', 'clubGroups', 'packageCategoryGroups', 'setting'));
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
                $categoryColor = optional($package->category)->color ?: '#a774ff';

                return [
                    'id' => 'category-' . $key,
                    'name' => optional($package->category)->name ?: 'Uncategorized',
                    'color' => $categoryColor,
                    'icon' => optional($package->category)->icon,
                    'club' => $package->website,
                    'mappings' => $group->values(),
                ];
            })
            ->values();
    }
}
