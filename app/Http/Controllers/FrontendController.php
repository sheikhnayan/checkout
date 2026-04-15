<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Website;
use App\Models\Addon;
use App\Models\Event;
use App\Models\Package;
use App\Models\PromoCode;
use App\Models\Affiliate;
use App\Models\AffiliateWebsite;
use App\Models\Entertainer;
use App\Models\CheckoutPopup;
use App\Models\Transaction;
use Illuminate\Support\Facades\Schema;

class FrontendController extends Controller
{
    public function index($slug, Request $request)
    {
        // Get only active, non-archived website by slug
        $data = Website::where('slug', $slug)
            ->where('status', 1)
            ->where('is_archieved', 0)
            ->first();
        
        // Return 404 if website not found
        if (!$data) {
            abort(404, 'Website not found');
        }

        if ($request->filled('aff')) {
            $affiliate = Affiliate::where('slug', $request->input('aff'))
                ->where('status', 'approved')
                ->where('is_active', true)
                ->whereHas('affiliateWebsites', function ($query) use ($data) {
                    $query->where('website_id', $data->id)->where('is_active', true);
                })
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
                ->whereHas('affiliateWebsites', function ($query) use ($data) {
                    $query->where('website_id', $data->id)->where('is_active', true);
                })
                ->first();

            if (!$affiliateReferral) {
                session()->forget(['affiliate_referral_id', 'affiliate_referral_slug']);
            }
        }

        $requestedPackageId = $request->filled('package') ? (int) $request->input('package') : null;
        $checkoutPopup = CheckoutPopup::activeForCheckout((int) $data->id)
            ->latest('id')
            ->first();

        if (isset($request->event_name)) {
            $event = Event::where('website_id', $data->id)
                ->where('name', $request->event_name)
                ->where('is_archieved', 0)
                ->when(Schema::hasColumn('events', 'status'), function ($query) {
                    $query->where('status', 1);
                })
                ->first();

            if ($event) {
                $event = $this->decorateEventAttendanceData($event);
            }

            $packageCategories = $this->buildPackageCategories($data, $event ? (int) $event->id : -1, false);

            $data->setRelation('events', $this->activeWebsiteEvents($data->id));

            return view('index', compact('data', 'event', 'affiliateReferral', 'requestedPackageId', 'packageCategories', 'checkoutPopup'));
        }

        $packageCategories = $this->buildPackageCategories($data, null, true);

        $data->setRelation('events', $this->activeWebsiteEvents($data->id));

        return view('index_two', compact('data', 'affiliateReferral', 'requestedPackageId', 'packageCategories', 'checkoutPopup'));

    }

    public function addons($slug, $id)
    {
           $data = Addon::where('package_id', $id)
               ->where(function ($query) {
                   $query->where('status', 1)->orWhereNull('status');
               })
               ->get();

        return response()->json($data);
    }

    public function checkCode($slug, $code)
    {
        $website = Website::where('slug', $slug)
            ->where('status', 1)
            ->where('is_archieved', 0)
            ->first();

        if (!$website) {
            return response()->json(['valid' => false]);
        }

        $normalizedCode = trim((string) $code);
        $source = strtolower((string) request()->query('source', PromoCode::AUDIENCE_CLUB));
        $ownerSlug = trim((string) request()->query('owner_slug', ''));

        if (!in_array($source, PromoCode::ALLOWED_AUDIENCES, true)) {
            return response()->json(['valid' => false]);
        }

        $check = PromoCode::where('website_id', $website->id)
            ->where('is_archieved', 0)
            ->where('audience', $source)
            ->whereRaw('LOWER(promo_code) = ?', [strtolower($normalizedCode)]);

        if ($source === PromoCode::AUDIENCE_AFFILIATE) {
            $affiliate = Affiliate::where('slug', $ownerSlug)
                ->where('status', 'approved')
                ->where('is_active', true)
                ->whereHas('affiliateWebsites', function ($query) use ($website) {
                    $query->where('website_id', $website->id)
                        ->where('is_active', true);
                })
                ->first();

            if (!$affiliate) {
                return response()->json(['valid' => false]);
            }

            $check->where('affiliate_id', $affiliate->id)
                ->whereNull('entertainer_id');
        } elseif ($source === PromoCode::AUDIENCE_ENTERTAINER) {
            $entertainer = Entertainer::where('slug', $ownerSlug)
                ->where('website_id', $website->id)
                ->where('status', 'approved')
                ->where('is_active', true)
                ->first();

            if (!$entertainer) {
                return response()->json(['valid' => false]);
            }

            $check->where('entertainer_id', $entertainer->id)
                ->whereNull('affiliate_id');
        } else {
            $check->whereNull('affiliate_id')
                ->whereNull('entertainer_id');
        }

        $check = $check->first();

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
                ->where('is_archieved', 0)
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

    private function activeWebsiteEvents(int $websiteId)
    {
        return Event::where('website_id', $websiteId)
            ->where('is_archieved', 0)
            ->when(Schema::hasColumn('events', 'status'), function ($query) {
                $query->where('status', 1);
            })
            ->orderByRaw('COALESCE(start_date, date) ASC')
            ->get()
            ->filter(function (Event $event) {
                $end = $event->end_date ?: $event->start_date ?: $event->date;
                if (!$end) {
                    return false;
                }

                try {
                    return \Carbon\Carbon::parse($end)->endOfDay()->gte(now());
                } catch (\Throwable $e) {
                    return false;
                }
            })
            ->values()
            ->map(function (Event $event) {
                return $this->decorateEventAttendanceData($event);
            });
    }

    private function decorateEventAttendanceData(Event $event): Event
    {
        $limit = $event->attendee_limit !== null ? (int) $event->attendee_limit : null;
        $confirmedAttendees = $this->countConfirmedEventAttendees($event);
        $remainingCapacity = $limit !== null ? max($limit - $confirmedAttendees, 0) : null;

        $event->setAttribute('confirmed_attendee_count', $confirmedAttendees);
        $event->setAttribute('remaining_attendee_capacity', $remainingCapacity);
        $event->setAttribute('is_sold_out', $limit !== null && $remainingCapacity <= 0);

        return $event;
    }

    private function countConfirmedEventAttendees(Event $event): int
    {
        return Transaction::query()
            ->where('event_id', $event->id)
            ->where('status', 1)
            ->get(['type', 'package_number_of_guest', 'men', 'women'])
            ->sum(function (Transaction $transaction) {
                if ($transaction->type === 'reservation') {
                    return max(0, (int) $transaction->men) + max(0, (int) $transaction->women);
                }

                return max(1, (int) $transaction->package_number_of_guest);
            });
    }

    /**
     * Check if a package can be purchased and get available capacity
     */
    public function checkPackageCapacity($slug, $packageId, Request $request)
    {
        $package = Package::find($packageId);

        if (!$package) {
            return response()->json(['available' => false, 'message' => 'Package not found']);
        }

        $capacity = \App\Helpers\PackageLimitHelper::getAvailableCapacity($package);
        
        if ($capacity <= 0) {
            return response()->json([
                'available' => false,
                'message' => $package->package_type === 'table' 
                    ? 'No tables available for today' 
                    : 'No tickets available for today'
            ]);
        }

        return response()->json([
            'available' => true,
            'capacity' => $capacity,
            'package_type' => $package->package_type,
            'daily_limit' => $package->package_type === 'table' 
                ? $package->daily_table_limit 
                : $package->daily_ticket_limit
        ]);
    }
}
