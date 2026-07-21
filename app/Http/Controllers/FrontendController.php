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
use App\Services\WebsiteSessionAnalyticsService;
use App\Support\WebsiteTimezone;
use Illuminate\Support\Facades\Schema;

class FrontendController extends Controller
{
    private const ENABLED_DEMO_CHECKOUT_TEMPLATES = ['template4'];

    private WebsiteSessionAnalyticsService $sessionAnalytics;

    public function __construct(WebsiteSessionAnalyticsService $sessionAnalytics)
    {
        $this->sessionAnalytics = $sessionAnalytics;
    }

    public function checkoutTemplateOne($slug, Request $request)
    {
        return $this->renderCheckoutForTemplate($slug, $request, 'template1');
    }

    public function checkoutTemplateTwo($slug, Request $request)
    {
        return $this->renderCheckoutForTemplate($slug, $request, 'template2');
    }

    public function checkoutTemplateThree($slug, Request $request)
    {
        return $this->renderCheckoutForTemplate($slug, $request, 'template3');
    }

    public function checkoutTemplateFour($slug, Request $request)
    {
        return $this->renderCheckoutForTemplate($slug, $request, 'template4');
    }

    public function singlePackageCheckout($slug, $packageId, Request $request)
    {
        $resolvedPackageId = $this->resolveSinglePackageId($packageId);
        if (!$resolvedPackageId) {
            abort(404, 'Package not found');
        }

        $request->merge([
            'package' => $resolvedPackageId,
            'single_package_checkout' => 1,
        ]);

        return $this->index($slug, $request);
    }

    public function index($slug, Request $request)
    {
        return $this->renderCheckoutWithViews($slug, $request, 'index', 'index_two');
    }

    private function renderCheckoutForTemplate(string $slug, Request $request, string $templateKey)
    {
        $templateViewMap = [
            'template1' => [
                'event' => 'checkout_templates.template1.index',
                'default' => 'checkout_templates.template1.index_two',
            ],
            'template2' => [
                'event' => 'checkout_templates.template2.index',
                'default' => 'checkout_templates.template2.index_two',
            ],
            'template3' => [
                'event' => 'checkout_templates.template3.index',
                'default' => 'checkout_templates.template3.index_two',
            ],
            'template4' => [
                'event' => 'checkout_templates.template4.index',
                'default' => 'checkout_templates.template4.index_two',
            ],
        ];

        if (!isset($templateViewMap[$templateKey])) {
            abort(404, 'Checkout template not found');
        }

        if (!in_array($templateKey, self::ENABLED_DEMO_CHECKOUT_TEMPLATES, true)) {
            abort(404, 'Checkout template not found');
        }

        return $this->renderCheckoutWithViews(
            $slug,
            $request,
            $templateViewMap[$templateKey]['event'],
            $templateViewMap[$templateKey]['default']
        );
    }

    private function renderCheckoutWithViews(string $slug, Request $request, string $eventView, string $defaultView)
    {
        $isIframeCheckout = $request->boolean('embed');
        $singlePackageCheckout = $request->boolean('single_package_checkout');
        $isSinglePackageCheckout = $singlePackageCheckout;

        // Get only active, non-archived website by slug
        $data = Website::where('slug', $slug)
            ->where('status', 1)
            ->where('is_archieved', 0)
            ->first();
        
        // Return 404 if website not found
        if (!$data) {
            if (auth()->check()) {
                $user = auth()->user();

                if ($user->isAffiliate()) {
                    return redirect()->route('affiliate.portal.dashboard');
                }

                if ($user->isEntertainer()) {
                    return redirect()->route('entertainer.portal.dashboard');
                }

                if ($user->isWebsiteUser() || $user->isBouncer() || $user->isManager()) {
                    return redirect()->route('admin.index');
                }

                return redirect()->route('admin.transaction.index');
            }

            abort(404, 'Website not found');
        }

        $isPhysicalProductCheckoutMode = $this->isPhysicalProductCheckoutMode($data);
        if ($isPhysicalProductCheckoutMode) {
            $productEventView = $eventView . '_product';
            $productDefaultView = $defaultView . '_product';

            if (view()->exists($productEventView)) {
                $eventView = $productEventView;
            }

            if (view()->exists($productDefaultView)) {
                $defaultView = $productDefaultView;
            }
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

        // Session analytics must never interrupt checkout rendering.
        try {
            $this->sessionAnalytics->trackCheckoutPageView($request, $data);
        } catch (\Throwable $exception) {
            report($exception);
        }

        $requestedPackageId = $request->filled('package') ? (int) $request->input('package') : null;

        if ($singlePackageCheckout) {
            if (!$requestedPackageId) {
                abort(404, 'Package not found');
            }

            $singleCheckoutPackage = Package::query()
                ->with(['category', 'event'])
                ->where('id', $requestedPackageId)
                ->where('website_id', $data->id)
                ->where('status', 1)
                ->where('is_archieved', 0)
                ->first();

            if (!$singleCheckoutPackage) {
                abort(404, 'Package not found');
            }

            $packageCategories = $this->buildSinglePackageCategoryPayload($singleCheckoutPackage);

            $checkoutPopup = CheckoutPopup::activeForCheckout((int) $data->id)
                ->latest('id')
                ->first();

            $data->setRelation('events', $this->activeWebsiteEvents($data->id));

            return view($defaultView, compact('data', 'affiliateReferral', 'requestedPackageId', 'packageCategories', 'checkoutPopup', 'isIframeCheckout', 'isSinglePackageCheckout'));
        }

        $checkoutPopup = CheckoutPopup::activeForCheckout((int) $data->id)
            ->latest('id')
            ->first();

        $websiteTimezone = WebsiteTimezone::forWebsite($data);

        if (isset($request->event_name)) {
            $event = Event::where('website_id', $data->id)
                ->where('name', $request->event_name)
                ->where('is_archieved', 0)
                ->when(Schema::hasColumn('events', 'status'), function ($query) {
                    $query->where('status', 1);
                })
                ->first();

            if ($event && $this->isEventCurrentOrUpcoming($event, $websiteTimezone)) {
                $event = $this->decorateEventAttendanceData($event);

                $packageCategories = $this->buildPackageCategories($data, (int) $event->id, false);

                $data->setRelation('events', $this->activeWebsiteEvents($data->id));

                return view($eventView, compact('data', 'event', 'affiliateReferral', 'requestedPackageId', 'packageCategories', 'checkoutPopup', 'isIframeCheckout', 'isSinglePackageCheckout'));
            }

            if ($event) {
                return view('event-expired', [
                    'data' => $data,
                    'event' => $event,
                    'websiteTimezone' => $websiteTimezone,
                ]);
            }
        }

        $packageCategories = $this->buildPackageCategories($data, null, false);

        $data->setRelation('events', $this->activeWebsiteEvents($data->id));

        return view($defaultView, compact('data', 'affiliateReferral', 'requestedPackageId', 'packageCategories', 'checkoutPopup', 'isIframeCheckout', 'isSinglePackageCheckout'));

    }

    private function isPhysicalProductCheckoutMode(Website $website): bool
    {
        if (!Schema::hasColumn('websites', 'is_physical_product_checkout')) {
            return false;
        }

        return (bool) $website->is_physical_product_checkout;
    }

    private function filterPackageCategoriesByPackageId($packageCategories, int $packageId)
    {
        return collect($packageCategories)
            ->map(function ($category) use ($packageId) {
                $filteredPackages = collect($category['packages'] ?? [])->where('id', $packageId)->values();
                if ($filteredPackages->isEmpty()) {
                    return null;
                }

                $category['packages'] = $filteredPackages;
                return $category;
            })
            ->filter()
            ->values();
    }

    private function buildSinglePackageCategoryPayload(Package $package)
    {
        return collect([
            [
                'id' => $package->package_category_id ?: 'uncategorized',
                'name' => optional($package->category)->name ?: 'Uncategorized',
                'icon' => optional($package->category)->icon ?: null,
                'color' => optional($package->category)->color ?: null,
                'packages' => collect([$package]),
            ],
        ]);
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

    public function autoDiscounts($slug)
    {
        $website = Website::where('slug', $slug)
            ->where('status', 1)
            ->where('is_archieved', 0)
            ->first();

        if (!$website) {
            return response()->json(['valid' => false]);
        }

        $source = strtolower((string) request()->query('source', PromoCode::AUDIENCE_CLUB));
        $ownerSlug = trim((string) request()->query('owner_slug', ''));

        if (!in_array($source, PromoCode::ALLOWED_AUDIENCES, true)) {
            return response()->json(['valid' => false]);
        }

        $query = PromoCode::where('website_id', $website->id)
            ->where('is_archieved', 0)
            ->where('discount_method', PromoCode::DISCOUNT_METHOD_AUTOMATIC)
            ->where(function ($q) {
                $q->whereNull('is_active')->orWhere('is_active', 1);
            })
            ->where('audience', $source);

        if ($source === PromoCode::AUDIENCE_AFFILIATE) {
            $affiliate = Affiliate::where('slug', $ownerSlug)
                ->where('status', 'approved')
                ->where('is_active', true)
                ->whereHas('affiliateWebsites', function ($q) use ($website) {
                    $q->where('website_id', $website->id)->where('is_active', true);
                })
                ->first();
            if (!$affiliate) {
                return response()->json(['valid' => false]);
            }
            $query->where('affiliate_id', $affiliate->id)->whereNull('entertainer_id');
        } elseif ($source === PromoCode::AUDIENCE_ENTERTAINER) {
            $entertainer = Entertainer::where('slug', $ownerSlug)
                ->where('website_id', $website->id)
                ->where('status', 'approved')
                ->where('is_active', true)
                ->first();
            if (!$entertainer) {
                return response()->json(['valid' => false]);
            }
            $query->where('entertainer_id', $entertainer->id)->whereNull('affiliate_id');
        } else {
            $query->whereNull('affiliate_id')->whereNull('entertainer_id');
        }

        $candidates = $query->get();

        $packageIds = collect(explode(',', (string) request()->query('package_ids', '')))
            ->map(fn ($id) => (int) trim($id))
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        $subtotal = (float) request()->query('subtotal', 0);
        $totalQty = (int) request()->query('total_qty', 0);
        $now = now();

        foreach ($candidates as $promo) {
            if ($promo->starts_at && $promo->starts_at->gt($now)) continue;
            if ($promo->ends_at && $promo->ends_at->lt($now)) continue;
            if (!empty($promo->usage_limit_total) && (int) ($promo->usage_count ?? 0) >= (int) $promo->usage_limit_total) continue;

            if (($promo->applies_to ?? PromoCode::APPLIES_TO_ALL_PACKAGES) === PromoCode::APPLIES_TO_SPECIFIC_PACKAGES) {
                $allowedIds = collect((array) ($promo->applies_to_package_ids ?? []))
                    ->map(fn ($id) => (int) $id)
                    ->filter(fn ($id) => $id > 0)
                    ->values();
                if (empty($packageIds) || collect($packageIds)->intersect($allowedIds)->isEmpty()) continue;
            }

            $minType = (string) ($promo->min_requirement_type ?? PromoCode::MIN_REQUIREMENT_NONE);
            if ($minType === PromoCode::MIN_REQUIREMENT_AMOUNT) {
                $minAmount = (float) ($promo->min_purchase_amount ?? 0);
                if ($minAmount > 0 && $subtotal < $minAmount) continue;
            }
            if ($minType === PromoCode::MIN_REQUIREMENT_QUANTITY) {
                $minQty = (int) ($promo->min_purchase_quantity ?? 0);
                if ($minQty > 0 && $totalQty < $minQty) continue;
            }

            $discountType = $promo->discount_value_type ?: ($promo->type ?: PromoCode::DISCOUNT_TYPE_PERCENTAGE);
            $discountValue = isset($promo->discount_value) ? (float) $promo->discount_value : (float) ($promo->percentage ?? 0);

            return response()->json([
                'valid' => true,
                'id'    => $promo->id,
                'name'  => $promo->name ?: 'Automatic Discount',
                'discount' => $discountValue,
                'type'     => $discountType,
            ]);
        }

        return response()->json(['valid' => false]);
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
            ->where(function ($query) {
                $query->whereNull('is_active')->orWhere('is_active', 1);
            })
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
            $now = now();
            if ($check->starts_at && $check->starts_at->gt($now)) {
                return response()->json(['valid' => false, 'message' => 'This promo code is not active yet.']);
            }

            if ($check->ends_at && $check->ends_at->lt($now)) {
                return response()->json(['valid' => false, 'message' => 'This promo code has expired.']);
            }

            if (!empty($check->usage_limit_total) && (int) ($check->usage_count ?? 0) >= (int) $check->usage_limit_total) {
                return response()->json(['valid' => false, 'message' => 'This promo code has reached its usage limit.']);
            }

            $packageIds = collect(explode(',', (string) request()->query('package_ids', '')))
                ->map(fn ($id) => (int) trim($id))
                ->filter(fn ($id) => $id > 0)
                ->unique()
                ->values()
                ->all();

            if (($check->applies_to ?? PromoCode::APPLIES_TO_ALL_PACKAGES) === PromoCode::APPLIES_TO_SPECIFIC_PACKAGES) {
                $allowedPackageIds = collect((array) ($check->applies_to_package_ids ?? []))
                    ->map(fn ($id) => (int) $id)
                    ->filter(fn ($id) => $id > 0)
                    ->values();

                $hasMatch = !empty($packageIds) && collect($packageIds)->intersect($allowedPackageIds)->isNotEmpty();
                if (!$hasMatch) {
                    return response()->json(['valid' => false, 'message' => 'This promo code does not apply to the selected package(s).']);
                }
            }

            $subtotal = (float) request()->query('subtotal', 0);
            $totalQty = (int) request()->query('total_qty', 0);
            $minRequirementType = (string) ($check->min_requirement_type ?? PromoCode::MIN_REQUIREMENT_NONE);

            if ($minRequirementType === PromoCode::MIN_REQUIREMENT_AMOUNT) {
                $minAmount = (float) ($check->min_purchase_amount ?? 0);
                if ($minAmount > 0 && $subtotal < $minAmount) {
                    return response()->json(['valid' => false, 'message' => 'Minimum order amount for this code is $' . number_format($minAmount, 2) . '.']);
                }
            }

            if ($minRequirementType === PromoCode::MIN_REQUIREMENT_QUANTITY) {
                $minQty = (int) ($check->min_purchase_quantity ?? 0);
                if ($minQty > 0 && $totalQty < $minQty) {
                    return response()->json(['valid' => false, 'message' => 'Minimum quantity for this code is ' . $minQty . '.']);
                }
            }

            $discountType = $check->discount_value_type ?: ($check->type ?: PromoCode::DISCOUNT_TYPE_PERCENTAGE);
            $discountValue = isset($check->discount_value) ? (float) $check->discount_value : (float) ($check->percentage ?? 0);

            return response()->json([
                'valid' => true,
                'discount' => $discountValue,
                'type' => $discountType,
                'id' => $check->id,
                'message' => 'Promo code applied successfully.',
            ]);
        }

        return response()->json(['valid' => false]);
    }

    private function buildPackageCategories(Website $website, ?int $eventId = null, bool $nullEventOnly = false)
    {
        $packagesQuery = Package::with('category')
            ->where('packages.website_id', $website->id)
            ->clubVisible()
            ->where('packages.status', 1)
            ->where('packages.is_archieved', 0)
            ->when(!$nullEventOnly && $eventId !== null, function ($query) use ($eventId) {
                $query->where('packages.event_id', $eventId);
            })
            ->when($eventId === null, function ($query) {
                if (!Schema::hasColumn('packages', 'only_for_events')) {
                    return;
                }

                $query->where(function ($visibilityQuery) {
                    $visibilityQuery->whereNull('packages.only_for_events')
                        ->orWhere('packages.only_for_events', 0);
                });
            });

        if (Schema::hasTable('package_categories') && Schema::hasColumn('package_categories', 'is_archieved')) {
            $packagesQuery->where(function ($query) {
                $query->whereNull('packages.package_category_id')
                    ->orWhereHas('category', function ($categoryQuery) {
                        $categoryQuery->where(function ($statusQuery) {
                            $statusQuery->whereNull('is_archieved')
                                ->orWhere('is_archieved', 0);
                        });
                    });
            });
        }

        if (
            Schema::hasColumn('packages', 'package_category_id')
            && Schema::hasTable('package_categories')
            && Schema::hasColumn('package_categories', 'sort_order')
        ) {
            $packagesQuery
                ->leftJoin('package_categories as pc', 'packages.package_category_id', '=', 'pc.id')
                ->select('packages.*')
                ->orderByRaw('CASE WHEN packages.package_category_id IS NULL THEN 1 ELSE 0 END')
                ->orderBy('pc.sort_order')
                ->orderBy('pc.name');
        } elseif (Schema::hasColumn('packages', 'package_category_id')) {
            $packagesQuery
                ->orderByRaw('CASE WHEN packages.package_category_id IS NULL THEN 1 ELSE 0 END')
                ->orderBy('packages.package_category_id');
        }

        if (Schema::hasColumn('packages', 'sort_order')) {
            $packagesQuery->orderBy('packages.sort_order');
        }

        if (Schema::hasColumn('packages', 'is_most_popular')) {
            $packagesQuery->orderByDesc('packages.is_most_popular');
        }

        $packages = $packagesQuery
            ->orderBy('packages.name')
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
                    'icon' => optional($firstPackage->category)->icon ?: null,
                    'color' => optional($firstPackage->category)->color ?: null,
                    'packages' => $group->values(),
                ];
            })
            ->values();
    }

    private function activeWebsiteEvents(int $websiteId)
    {
        $websiteTimezone = WebsiteTimezone::forWebsite(Website::select('id', 'timezone')->find($websiteId));

        return Event::where('website_id', $websiteId)
            ->where('is_archieved', 0)
            ->when(Schema::hasColumn('events', 'status'), function ($query) {
                $query->where('status', 1);
            })
            ->orderByRaw('COALESCE(start_date, date) ASC')
            ->get()
            ->filter(function (Event $event) use ($websiteTimezone) {
                return $this->isEventCurrentOrUpcoming($event, $websiteTimezone);
            })
            ->values()
            ->map(function (Event $event) {
                return $this->decorateEventAttendanceData($event);
            });
    }

    private function isEventCurrentOrUpcoming(Event $event, string $timezone): bool
    {
        $end = $event->end_date_value ?: $event->start_date_value ?: $event->date_value;
        if (!$end) {
            return false;
        }

        try {
            $todayLocal = \Carbon\Carbon::now($timezone)->startOfDay();

            return \Carbon\Carbon::createFromFormat('Y-m-d', $end, $timezone)->startOfDay()->gte($todayLocal);
        } catch (\Throwable $e) {
            return false;
        }
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

        $websiteTimezone = WebsiteTimezone::forWebsite(
            $package->website ?? Website::select('id', 'timezone')->find($package->website_id)
        );

        $targetDate = null;
        if ($request->filled('use_date')) {
            try {
                $targetDate = \Carbon\Carbon::parse((string) $request->query('use_date'))->startOfDay();
            } catch (\Throwable $exception) {
                $targetDate = null;
            }
        }

        if (!$targetDate) {
            $targetDate = \Carbon\Carbon::today($websiteTimezone);
        }

        $capacity = \App\Helpers\PackageLimitHelper::getAvailableCapacity($package, $targetDate);
        $eventRemaining = null;
        $perPurchaseCap = null;

        if ($package->package_type === 'table') {
            $perPurchaseCap = max(0, (int) ($package->guests_per_table ?? 0));
        }

        if ($package->event_id) {
            $event = Event::find($package->event_id);
            if ($event && $event->attendee_limit !== null) {
                $eventLimit = (int) $event->attendee_limit;
                if ($eventLimit > 0) {
                    $confirmed = $this->countConfirmedEventAttendeesForDate($event, $targetDate);
                    $eventRemaining = max($eventLimit - $confirmed, 0);
                }
            }
        }

        $maxSelect = $capacity;
        if ($perPurchaseCap !== null && $perPurchaseCap > 0) {
            $maxSelect = min($maxSelect, $perPurchaseCap);
        }
        if ($eventRemaining !== null) {
            $maxSelect = min($maxSelect, $eventRemaining);
        }

        $requestedQuantity = max(1, (int) $request->query('requested_quantity', 1));
        
        if ($maxSelect <= 0) {
            $message = $eventRemaining !== null && $eventRemaining <= 0
                ? 'This event is sold out for the selected date.'
                : ($package->package_type === 'table'
                    ? 'No tables are available for this package on the selected date.'
                    : 'No tickets are available for this package on the selected date.');

            return response()->json([
                'available' => false,
                'message' => $message,
                'event_remaining' => $eventRemaining,
                'capacity' => max(0, (int) $capacity),
                'max_select' => 0,
                'per_purchase_cap' => $perPurchaseCap,
                'sold_out' => true,
            ]);
        }

        if ($requestedQuantity > $maxSelect) {
            return response()->json([
                'available' => false,
                'message' => 'The quantity you entered is unavailable for the selected date. Please choose up to ' . $maxSelect . ' guest(s).',
                'event_remaining' => $eventRemaining,
                'capacity' => max(0, (int) $capacity),
                'max_select' => max(0, (int) $maxSelect),
                'per_purchase_cap' => $perPurchaseCap,
                'sold_out' => false,
            ]);
        }

        return response()->json([
            'available' => true,
            'capacity' => $capacity,
            'event_remaining' => $eventRemaining,
            'max_select' => max(0, (int) $maxSelect),
            'per_purchase_cap' => $perPurchaseCap,
            'sold_out' => false,
            'package_type' => $package->package_type,
            'daily_limit' => $package->package_type === 'table' 
                ? $package->daily_table_limit 
                : $package->daily_ticket_limit
        ]);
    }

    private function countConfirmedEventAttendeesForDate(Event $event, \Carbon\Carbon $targetDate): int
    {
        $dateString = $targetDate->toDateString();

        return Transaction::query()
            ->where('event_id', $event->id)
            ->where('status', 1)
            ->where(function ($query) use ($dateString) {
                $query->whereDate('package_use_date', $dateString)
                    ->orWhere(function ($fallbackQuery) use ($dateString) {
                        $fallbackQuery->whereNull('package_use_date')
                            ->whereDate('created_at', $dateString);
                    });
            })
            ->get(['type', 'package_number_of_guest', 'men', 'women'])
            ->sum(function (Transaction $transaction) {
                if ($transaction->type === 'reservation') {
                    return max(0, (int) $transaction->men) + max(0, (int) $transaction->women);
                }

                return max(1, (int) $transaction->package_number_of_guest);
            });
    }

    private function resolveSinglePackageId($packageIdentifier): ?int
    {
        $rawIdentifier = trim((string) $packageIdentifier);
        if ($rawIdentifier === '') {
            return null;
        }

        // Backward compatibility: old links still pass numeric package IDs.
        if (ctype_digit($rawIdentifier)) {
            return (int) $rawIdentifier;
        }

        // New format: "{package-name-slug}-{id}".
        if (preg_match('/-(\d+)$/', $rawIdentifier, $matches) === 1) {
            return (int) $matches[1];
        }

        return null;
    }
}
