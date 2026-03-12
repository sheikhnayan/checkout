<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Website;
use App\Models\Addon;
use App\Models\Event;
use App\Models\Package;
use App\Models\PromoCode;
use App\Models\Affiliate;
use Illuminate\Support\Facades\Schema;

class FrontendController extends Controller
{
    public function index($slug, Request $request)
    {
        // Get website by slug instead of domain
        $data = Website::where('slug', $slug)->first();
        
        // Return 404 if website not found
        if (!$data) {
            abort(404, 'Website not found');
        }

        if ($request->filled('aff')) {
            $affiliate = Affiliate::where('slug', $request->input('aff'))
                ->where('status', 'approved')
                ->where('is_active', true)
                ->first();

            if ($affiliate) {
                session([
                    'affiliate_referral_id' => $affiliate->id,
                    'affiliate_referral_slug' => $affiliate->slug,
                ]);
            } else {
                session()->forget(['affiliate_referral_id', 'affiliate_referral_slug']);
            }
        }

        $affiliateReferral = null;
        if (session()->has('affiliate_referral_id')) {
            $affiliateReferral = Affiliate::where('id', session('affiliate_referral_id'))
                ->where('status', 'approved')
                ->where('is_active', true)
                ->first();
        }

        $requestedPackageId = $request->filled('package') ? (int) $request->input('package') : null;

        if (isset($request->event_name)) {
            $event = Event::where('name', $request->event_name)->first();
            $packageCategories = $this->buildPackageCategories($data, $event ? (int) $event->id : -1, false);

            return view('index', compact('data', 'event', 'affiliateReferral', 'requestedPackageId', 'packageCategories'));
        }

        $packageCategories = $this->buildPackageCategories($data, null, true);

        return view('index_two', compact('data', 'affiliateReferral', 'requestedPackageId', 'packageCategories'));

    }

    public function addons($slug, $id)
    {
        $data = Addon::where('package_id', $id)->get();

        return response()->json($data);
    }

    public function checkCode($slug, $code)
    {
        $check = PromoCode::where('promo_code', $code)->first();

        if ($check) {
            return response()->json(['valid' => true, 'discount' => $check->percentage, 'type' => $check->type, 'id' => $check->id]);
        }

        return response()->json(['valid' => false]);
    }

    private function buildPackageCategories(Website $website, ?int $eventId = null, bool $nullEventOnly = false)
    {
        $packagesQuery = Package::with('category')
            ->where('website_id', $website->id)
            ->where('status', 1)
            ->when($nullEventOnly, function ($query) {
                $query->whereNull('event_id');
            })
            ->when(!$nullEventOnly && $eventId !== null, function ($query) use ($eventId) {
                $query->where('event_id', $eventId);
            });

        if (Schema::hasColumn('packages', 'package_category_id')) {
            $packagesQuery->orderBy('package_category_id');
        }

        $packages = $packagesQuery
            ->orderBy('name')
            ->get();

        return $packages
            ->groupBy(function ($package) {
                return $package->package_category_id ?: 'uncategorized';
            })
            ->map(function ($group, $key) {
                $firstPackage = $group->first();

                return [
                    'id' => $key,
                    'name' => optional($firstPackage->category)->name ?: 'Uncategorized',
                    'packages' => $group->values(),
                ];
            })
            ->values();
    }
}
