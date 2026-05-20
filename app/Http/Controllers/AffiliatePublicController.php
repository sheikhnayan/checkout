<?php

namespace App\Http\Controllers;

use App\Models\Affiliate;
use App\Models\Setting;
use Illuminate\Support\Collection;

class AffiliatePublicController extends Controller
{
    public function show($slug)
    {
        $affiliate = Affiliate::with(['user', 'affiliatePackages.package.website'])
            ->where('slug', $slug)
            ->where('status', 'approved')
            ->where('is_active', true)
            ->firstOrFail();

        $packageMappings = $affiliate->affiliatePackages()
            ->with(['package.website', 'package.category', 'package.addons'])
            ->where('is_active', true)
            ->latest()
            ->get()
            ->filter(function ($mapping) {
                return $mapping->package
                    && $mapping->package->website
                    && (int) $mapping->package->status === 1
                    && (int) ($mapping->package->is_archieved ?? 0) === 0
                    && (int) ($mapping->package->website->status ?? 0) === 1
                    && (int) ($mapping->package->website->is_archieved ?? 0) === 0;
            })
            ->values();

        $clubGroups = $packageMappings->groupBy(function ($mapping) {
            return $mapping->package->website->id;
        });

        // Build package categories - handle null categories as "Uncategorized"
        $packageCategories = [];
        foreach ($packageMappings as $mapping) {
            $package = $mapping->package;
            $categoryId = $package->package_category_id ?: 'uncategorized';
            $categoryName = $package->category?->name ?? 'Uncategorized';

            if (!isset($packageCategories[$categoryId])) {
                $packageCategories[$categoryId] = [
                    'id' => $categoryId,
                    'name' => $categoryName,
                    'packages' => []
                ];
            }
            $packageCategories[$categoryId]['packages'][] = $package;
        }
        $packageCategories = array_values($packageCategories);

        $setting = Setting::find(1);
        $data = (object)['name' => 'CartVIP', 'logo' => null, 'location' => '', 'back_link' => null, 'back_text' => 'Back', 'reservation' => 0];

        return view('affiliate.public-page', compact('affiliate', 'packageMappings', 'packageCategories', 'clubGroups', 'data', 'setting'));
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
