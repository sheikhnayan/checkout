<?php

namespace App\Http\Controllers;

use App\Models\Entertainer;
use App\Models\Setting;
use Illuminate\Support\Collection;
use Carbon\Carbon;

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
            ->get()
            ->filter(function ($mapping) {
                if (!$mapping->package || !$mapping->package->website) {
                    return false;
                }

                $isCategoryArchived = (int) ($mapping->package->category->is_archieved ?? 0) === 1;

                return (int) $mapping->package->status === 1
                    && (int) ($mapping->package->is_archieved ?? 0) === 0
                    && !$isCategoryArchived
                    && (int) ($mapping->package->website->status ?? 0) === 1
                    && (int) ($mapping->package->website->is_archieved ?? 0) === 0;
            })
            ->sortBy(function ($mapping) {
                $categorySort = $mapping->package->category?->sort_order;
                $packageSort = $mapping->package->sort_order;

                return [
                    $categorySort === null ? 999999 : (int) $categorySort,
                    $mapping->package->package_category_id ? 0 : 1,
                    $packageSort === null ? 999999 : (int) $packageSort,
                    strtolower((string) $mapping->package->name),
                ];
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
                    'icon' => $package->category?->icon ?? null,
                    'color' => $package->category?->color ?? null,
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
        $today = Carbon::today();
        for ($i = 0; $i < 30; $i++) {
            $date = $today->copy()->addDays($i);
            $dateOptions[] = [
                'value' => $date->format('Y-m-d'),
                'label' => $date->format('M d, Y')
            ];
        }

        // Get the first entertainer website for fees and checkout info
        $website = $entertainer->website;

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
                'sales_tax_name' => 'Tax',
                'service_charge_name' => 'Service Charge',
                'gratuity_name' => 'Gratuity',
                'gratuity_fee' => 0,
                'processing_fee' => 0,
                'processing_fee_type' => 'percentage',
                'refundable_fee' => 0,
                'refundable_name' => 'Non Refundable Processing Fees',
                'package_section_title' => 'Select Your Package',
                'package_section_subtext' => 'All packages include free ride, club entry, and priority access.',
                'color' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                'secondary_color' => '#764ba2',
                'background_color' => '#06090f',
                'font_color' => '#e8edf8'
            ];
        }

        return view('entertainer.public-page', compact('entertainer', 'packageMappings', 'packageCategories', 'clubGroups', 'data', 'setting', 'uniqueClubsForFilter', 'dateOptions'));
    }
}
