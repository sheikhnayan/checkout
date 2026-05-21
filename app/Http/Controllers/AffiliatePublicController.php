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

        // Build unique clubs for location filter
        $uniqueClubsForFilter = $clubGroups
            ->map(function ($group) {
                return $group->first()->package->website;
            })
            ->unique('id')
            ->values();

        // Generate date options for next 30 days
        $dateOptions = [];
        $today = \Carbon\Carbon::today();
        for ($i = 0; $i < 30; $i++) {
            $date = $today->copy()->addDays($i);
            $dateOptions[] = [
                'value' => $date->format('Y-m-d'),
                'label' => $date->format('M d, Y')
            ];
        }

        // Get the first affiliated website for fees and checkout info
        $website = $affiliate->affiliateWebsites()
            ->with('website')
            ->where('is_active', true)
            ->first()
            ?->website;

        // Use website data if available, otherwise create a default object
        if ($website) {
            $data = $website;
        } else {
            $data = (object)[
                'name' => 'CartVIP',
                'slug' => 'cartivp',
                'logo' => null,
                'location' => '',
                'back_link' => null,
                'back_text' => 'Back',
                'reservation' => 0,
                'gratuity_fee' => 0,
                'refundable_fee' => 0,
                'sales_tax_fee' => 10,
                'service_charge_fee' => 10,
                'sales_tax_name' => 'Tax'
            ];
        }

        return view('affiliate.public-page', compact('affiliate', 'packageMappings', 'packageCategories', 'clubGroups', 'data', 'setting', 'uniqueClubsForFilter', 'dateOptions'));
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
