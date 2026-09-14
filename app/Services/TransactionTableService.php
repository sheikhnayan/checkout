<?php

namespace App\Services;

use App\Models\Addon;
use App\Models\Package;
use App\Models\PromoCode;
use App\Models\Transaction;
use App\Models\Website;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionTableService
{
    /**
     * Build the base query based on user permissions.
     */
    public function getBaseQuery(Request $request, $user)
    {
        $showArchivedOnly = $request->boolean('archived') && $this->canManageArchivedTransactions($user);

        if ($user->isAdmin()) {
            return $showArchivedOnly ? Transaction::onlyArchived() : Transaction::query();
        } elseif ($user->isWebsiteUser() && $user->website_id) {
            return Transaction::query()->where(function ($query) use ($user) {
                $query->where('website_id', $user->website_id)
                    ->orWhereHas('event', function ($subQuery) use ($user) {
                        $subQuery->where('website_id', $user->website_id);
                    })
                    ->orWhereHas('package', function ($subQuery) use ($user) {
                        $subQuery->where('website_id', $user->website_id);
                    });
            });
        } elseif ($user->isManager()) {
            $ids = $user->accessibleWebsiteIds();
            return Transaction::query()->where(function ($query) use ($ids) {
                $query->whereIn('website_id', $ids)
                    ->orWhereHas('event', function ($subQuery) use ($ids) {
                        $subQuery->whereIn('website_id', $ids);
                    })
                    ->orWhereHas('package', function ($subQuery) use ($ids) {
                        $subQuery->whereIn('website_id', $ids);
                    });
            });
        }

        return Transaction::query()->whereRaw('1 = 0');
    }

    public function canManageArchivedTransactions($user): bool
    {
        return $user
            && $user->isAdmin()
            && strtolower(trim((string) ($user->email ?? ''))) === 'admin@admin.com';
    }

    /**
     * Build fully filtered query applying all Polaris and custom filters.
     */
    public function buildFilteredQuery(Request $request, $user, ?callable $queryMutator = null)
    {
        $query = $this->getBaseQuery($request, $user);

        if ($queryMutator) {
            $queryMutator($query);
        }

        // 1. Venue / Website filter (single or array)
        $venues = $request->input('venues', $request->query('venues', $request->query('website', [])));
        if (!is_array($venues)) {
            $venues = array_filter(array_map('trim', explode(',', (string) $venues)));
        }
        if (!empty($venues)) {
            $query->where(function ($vq) use ($venues) {
                $vq->whereHas('website', function ($wq) use ($venues) {
                    $wq->whereIn('name', $venues)->orWhereIn('id', $venues);
                })->orWhereHas('event.website', function ($wq) use ($venues) {
                    $wq->whereIn('name', $venues)->orWhereIn('id', $venues);
                })->orWhereHas('package.website', function ($wq) use ($venues) {
                    $wq->whereIn('name', $venues)->orWhereIn('id', $venues);
                });
            });
        }

        // 2. Type filter (single or array)
        $types = $request->input('types', $request->query('types', $request->query('type', [])));
        if (!is_array($types)) {
            $types = array_filter(array_map('trim', explode(',', (string) $types)));
        }
        $types = array_map('strtolower', $types);
        $validTypes = array_intersect($types, ['package', 'reservation', 'custom_invoice']);
        if (!empty($validTypes)) {
            $query->whereIn('type', $validTypes);
        }

        // 3. Status filter (single or array)
        $statuses = $request->input('statuses', $request->query('statuses', $request->query('status', [])));
        if (!is_array($statuses)) {
            $statuses = array_filter(array_map('trim', explode(',', (string) $statuses)));
        }
        if (!empty($statuses)) {
            $statusMap = [
                'completed' => 1,
                '1' => 1,
                'approved' => 1,
                'canceled' => 0,
                'cancelled' => 0,
                '0' => 0,
                'refunded' => 2,
                '2' => 2,
            ];
            $intStatuses = [];
            $commStatuses = [];
            $hasNa = false;

            foreach ($statuses as $st) {
                $stLower = strtolower(trim($st));
                if (array_key_exists($stLower, $statusMap)) {
                    $intStatuses[] = $statusMap[$stLower];
                } elseif (in_array($stLower, ['pending', 'approved', 'paid', 'reversed'], true)) {
                    $commStatuses[] = $stLower;
                } elseif (in_array($stLower, ['na', 'n/a'], true)) {
                    $hasNa = true;
                }
            }

            $query->where(function ($sq) use ($intStatuses, $commStatuses, $hasNa) {
                $hasClause = false;
                if (!empty($intStatuses)) {
                    $sq->whereIn('status', array_unique($intStatuses));
                    $hasClause = true;
                }
                if (!empty($commStatuses)) {
                    $method = $hasClause ? 'orWhere' : 'where';
                    $sq->$method(function ($csq) use ($commStatuses) {
                        $csq->whereIn('affiliate_commission_status', $commStatuses)
                            ->orWhereIn('entertainer_commission_status', $commStatuses);
                    });
                    $hasClause = true;
                }
                if ($hasNa) {
                    $method = $hasClause ? 'orWhere' : 'where';
                    $sq->$method(function ($nsq) {
                        $nsq->where(function ($anq) {
                            $anq->whereNull('affiliate_commission_status')->orWhere('affiliate_commission_status', '');
                        })->where(function ($enq) {
                            $enq->whereNull('entertainer_commission_status')->orWhere('entertainer_commission_status', '');
                        });
                    });
                }
            });
        }

        // 4. Referral / Source / Affiliate filter
        $affiliates = $request->input('affiliates', $request->query('affiliates', $request->query('affiliate', [])));
        if (!is_array($affiliates)) {
            $affiliates = array_filter(array_map('trim', explode(',', (string) $affiliates)));
        }
        if (!empty($affiliates)) {
            $hasDirect = false;
            $searchNames = [];

            foreach ($affiliates as $filterVal) {
                if (strcasecmp($filterVal, 'Direct') === 0) {
                    $hasDirect = true;
                } else {
                    $cleanVal = trim(preg_replace('/\s*\([^)]*\)/', '', $filterVal));
                    if ($cleanVal !== '') {
                        $searchNames[] = strtolower($cleanVal);
                    }
                    $searchNames[] = strtolower($filterVal);
                }
            }
            $searchNames = array_unique($searchNames);

            $query->where(function ($q) use ($hasDirect, $searchNames) {
                if ($hasDirect) {
                    $q->where(function ($directQ) {
                        $directQ->whereNull('affiliate_id')->whereNull('entertainer_id');
                    });
                }

                if (!empty($searchNames)) {
                    $method = $hasDirect ? 'orWhere' : 'where';
                    $q->$method(function ($nameQuery) use ($searchNames) {
                        $nameQuery->whereHas('affiliate', function ($affiliateQuery) use ($searchNames) {
                            $affiliateQuery->where(function ($subQ) use ($searchNames) {
                                foreach ($searchNames as $n) {
                                    $subQ->orWhereRaw('LOWER(display_name) LIKE ?', ["%{$n}%"])
                                        ->orWhereHas('user', function ($uQ) use ($n) {
                                            $uQ->whereRaw('LOWER(name) LIKE ?', ["%{$n}%"]);
                                        });
                                }
                            });
                        })->orWhereHas('entertainer', function ($entertainerQuery) use ($searchNames) {
                            $entertainerQuery->where(function ($subQ) use ($searchNames) {
                                foreach ($searchNames as $n) {
                                    $subQ->orWhereRaw('LOWER(display_name) LIKE ?', ["%{$n}%"])
                                        ->orWhereHas('user', function ($uQ) use ($n) {
                                            $uQ->whereRaw('LOWER(name) LIKE ?', ["%{$n}%"]);
                                        });
                                }
                            });
                        });
                    });
                }
            });
        }

        // 5. Date Range & Target
        $dateRange = trim((string) $request->input('date_range', $request->query('date_range', '')));
        $dateFrom = trim((string) $request->input('date_from', $request->query('date_from', '')));
        $dateTo = trim((string) $request->input('date_to', $request->query('date_to', '')));
        $dateTarget = strtolower(trim((string) $request->input('date_target', $request->query('date_target', 'either'))));

        if ($dateRange !== '' && strpos($dateRange, ' - ') !== false) {
            $parts = explode(' - ', $dateRange);
            if (count($parts) === 2) {
                try {
                    $dateFrom = Carbon::createFromFormat('m/d/Y', trim($parts[0]), 'America/Los_Angeles')->format('Y-m-d');
                    $dateTo = Carbon::createFromFormat('m/d/Y', trim($parts[1]), 'America/Los_Angeles')->format('Y-m-d');
                } catch (\Throwable $e) {
                }
            }
        }

        if ($dateFrom !== '' && $dateTo !== '') {
            try {
                $startUtc = Carbon::parse($dateFrom, 'America/Los_Angeles')->startOfDay()->utc();
                $endUtc = Carbon::parse($dateTo, 'America/Los_Angeles')->endOfDay()->utc();
                $query->where(function ($dateQuery) use ($dateFrom, $dateTo, $dateTarget, $startUtc, $endUtc) {
                    if ($dateTarget === 'sale') {
                        $dateQuery->whereBetween('created_at', [$startUtc, $endUtc]);
                    } elseif ($dateTarget === 'reservation') {
                        $dateQuery->whereBetween('package_use_date', [$dateFrom, $dateTo]);
                    } else {
                        $dateQuery->whereBetween('created_at', [$startUtc, $endUtc])
                            ->orWhereBetween('package_use_date', [$dateFrom, $dateTo]);
                    }
                });
            } catch (\Throwable $exception) {
            }
        }

        // 6. Reservation Filter
        $reservationFilter = strtolower(trim((string) $request->input('reservation', $request->query('reservation', ''))));
        if ($reservationFilter !== '') {
            $today = Carbon::now('America/Los_Angeles')->startOfDay();
            $tomorrow = $today->copy()->addDay();
            $endOfWeek = $today->copy()->endOfWeek();

            if ($reservationFilter === 'upcoming') {
                $query->whereDate('package_use_date', '>', $today->toDateString())
                    ->whereNotIn('status', [0, 2]);
            } elseif ($reservationFilter === 'today') {
                $query->whereDate('package_use_date', $today->toDateString())
                    ->whereNotIn('status', [0, 2]);
            } elseif ($reservationFilter === 'weekend') {
                $query->whereRaw("DATE(package_use_date) >= ? AND DATE(package_use_date) <= ? AND DAYOFWEEK(package_use_date) IN (6, 7)", [
                    $tomorrow->toDateString(),
                    $endOfWeek->toDateString()
                ])->whereNotIn('status', [0, 2]);
            } elseif ($reservationFilter === 'past') {
                $query->whereDate('package_use_date', '<', $today->toDateString());
            } elseif ($reservationFilter === 'no_show') {
                $query->whereDate('package_use_date', '<', $today->toDateString())
                    ->where('status', 1)
                    ->where(function ($noShowQuery) {
                        $noShowQuery->whereNull('checked_in_status')
                            ->orWhere('checked_in_status', 0);
                    });
            } elseif ($reservationFilter === 'checked_in') {
                $query->where('checked_in_status', 1);
            } elseif ($reservationFilter === 'not_checked_in') {
                $query->where(function ($q) {
                    $q->whereNull('checked_in_status')
                        ->orWhere('checked_in_status', 0);
                })->whereNotIn('status', [0, 2]);
            }
        }

        // 7. Global Search (Search box)
        $searchRaw = $request->input('search');
        if (is_array($searchRaw)) {
            $searchVal = trim((string) ($searchRaw['value'] ?? ''));
        } else {
            $searchVal = trim((string) ($request->input('search.value') ?: $searchRaw));
        }
        if ($searchVal !== '') {
            $cleanNumeric = preg_replace('/[^0-9]/', '', $searchVal);
            $query->where(function ($sq) use ($searchVal, $cleanNumeric) {
                if ($cleanNumeric !== '') {
                    $sq->where('id', (int) $cleanNumeric);
                }
                $sq->orWhere('transaction_id', 'LIKE', "%{$searchVal}%")
                    ->orWhere('package_first_name', 'LIKE', "%{$searchVal}%")
                    ->orWhere('package_last_name', 'LIKE', "%{$searchVal}%")
                    ->orWhere('package_email', 'LIKE', "%{$searchVal}%")
                    ->orWhere('package_phone', 'LIKE', "%{$searchVal}%")
                    ->orWhere('payment_first_name', 'LIKE', "%{$searchVal}%")
                    ->orWhere('payment_last_name', 'LIKE', "%{$searchVal}%")
                    ->orWhere('payment_email', 'LIKE', "%{$searchVal}%")
                    ->orWhere('payment_phone', 'LIKE', "%{$searchVal}%")
                    ->orWhere('host_name', 'LIKE', "%{$searchVal}%")
                    ->orWhereHas('website', function ($wq) use ($searchVal) {
                        $wq->where('name', 'LIKE', "%{$searchVal}%");
                    });
            });
        }

        return $query;
    }

    /**
     * Handle DataTables AJAX server-side request.
     */
    public function getAjaxData(Request $request, $user, ?callable $queryMutator = null)
    {
        $draw = (int) $request->input('draw', 1);
        $start = max(0, (int) $request->input('start', 0));
        $length = max(10, min(100, (int) $request->input('length', 25)));

        $baseQuery = $this->getBaseQuery($request, $user);
        if ($queryMutator) {
            $queryMutator($baseQuery);
        }
        $recordsTotal = (clone $baseQuery)->count();

        $filteredQuery = $this->buildFilteredQuery($request, $user, $queryMutator);
        $recordsFiltered = (clone $filteredQuery)->count();

        // Sorting
        $order = $request->input('order');
        if (is_array($order) && isset($order[0])) {
            $orderColIndex = (int) ($order[0]['column'] ?? 2);
            $orderDir = strtolower((string) ($order[0]['dir'] ?? 'desc')) === 'asc' ? 'asc' : 'desc';
        } else {
            $orderColIndex = (int) $request->input('order.0.column', 2);
            $orderDir = strtolower((string) $request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        }

        $sortColMap = [
            1 => 'id',
            2 => 'created_at',
            3 => 'transaction_id',
            5 => 'host_name',
            8 => 'total',
            11 => 'due_amount',
            12 => 'status',
            13 => 'package_use_date',
        ];

        $sortCol = $sortColMap[$orderColIndex] ?? 'created_at';
        $filteredQuery->orderBy($sortCol, $orderDir);
        if ($sortCol !== 'id') {
            $filteredQuery->orderBy('id', $orderDir);
        }

        $pagedTransactions = (clone $filteredQuery)
            ->with([
                'event.website',
                'package.website',
                'website',
                'affiliate.user',
                'affiliate.parent.user',
                'entertainer.user'
            ])
            ->skip($start)
            ->take($length)
            ->get();

        // Preload reference data in 3 fast queries to eliminate N+1 queries completely
        $allPackages = Package::get(['id', 'name', 'description', 'package_type', 'transportation']);
        $allAddons = Addon::get(['id', 'name']);
        $allPromos = PromoCode::get(['id', 'name']);

        $allPackagesById = $allPackages->keyBy('id');
        $allPackagesByName = $allPackages->keyBy(fn($p) => strtolower(trim($p->name)));
        $allAddonsById = $allAddons->keyBy('id');
        $allPromosById = $allPromos->keyBy('id');

        $isPayoutPage = (bool) $request->input('isPayoutPage', false);
        $canArchive = $this->canManageArchivedTransactions($user);
        $isArchivedView = $request->boolean('archived') && $canArchive;
        $laToday = Carbon::now('America/Los_Angeles')->startOfDay();

        $rows = [];
        foreach ($pagedTransactions as $item) {
            $rows[] = $this->formatRowData(
                $item,
                $isPayoutPage,
                $isArchivedView,
                $canArchive,
                $laToday,
                $allPackagesById,
                $allPackagesByName,
                $allAddonsById,
                $allPromosById
            );
        }

        // Summary calculations
        $summary = $this->calculateKpisAndCharts($baseQuery, $filteredQuery, $request);

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $rows,
            'kpis' => $summary['kpis'],
            'chartData' => $summary['chartData'],
            'amountTotal' => '$' . number_format($summary['amountTotal'], 2),
        ];
    }

    /**
     * Compute KPIs and Chart timelines.
     */
    public function calculateKpisAndCharts($baseQuery, $filteredQuery, Request $request): array
    {
        $searchRaw = $request->input('search');
        $searchVal = is_array($searchRaw) ? ($searchRaw['value'] ?? '') : ($request->input('search.value') ?: $searchRaw);
        $searchVal = trim((string) $searchVal);

        $hasActiveFilter = (
            !empty($request->input('venues')) ||
            !empty($request->input('statuses')) ||
            !empty($request->input('types')) ||
            !empty($request->input('affiliates')) ||
            !empty($request->input('date_range')) ||
            !empty($request->input('reservation')) ||
            !empty($searchVal)
        );

        // Fetch only 6 lightweight columns for calculations across filtered records
        $filteredItems = (clone $filteredQuery)
            ->where('status', 1)
            ->select(['id', 'total', 'men', 'women', 'package_number_of_guest', 'created_at', 'package_use_date'])
            ->get();

        $totalSales = 0.0;
        $totalOrders = 0;
        $totalGuests = 0;
        $dailyMap = [];

        $dateTarget = strtolower(trim((string) $request->input('date_target', 'either')));

        foreach ($filteredItems as $item) {
            $g = max(0, (int)($item->men ?? 0) + (int)($item->women ?? 0));
            if ($g === 0) {
                $g = max(0, (int)($item->package_number_of_guest ?? 0));
            }

            $sale = (float)($item->total ?? 0);
            $totalSales += $sale;
            $totalOrders += 1;
            $totalGuests += $g;

            $salePst = $item->created_at ? $item->created_at->timezone('America/Los_Angeles')->format('Y-m-d') : null;
            $resPst = null;
            if ($item->package_use_date) {
                try {
                    $resPst = Carbon::parse($item->package_use_date, 'America/Los_Angeles')->format('Y-m-d');
                } catch (\Throwable $e) {}
            }

            $dateKey = ($dateTarget === 'reservation' && $resPst) ? $resPst : ($salePst ?: $resPst);
            if ($dateKey) {
                if (!isset($dailyMap[$dateKey])) {
                    $dailyMap[$dateKey] = ['sales' => 0.0, 'orders' => 0, 'guests' => 0];
                }
                $dailyMap[$dateKey]['sales'] += $sale;
                $dailyMap[$dateKey]['orders'] += 1;
                $dailyMap[$dateKey]['guests'] += $g;
            }
        }

        // Today specific numbers
        $todayPst = Carbon::now('America/Los_Angeles')->format('Y-m-d');
        $todayStartUtc = Carbon::now('America/Los_Angeles')->startOfDay()->utc();
        $todayEndUtc = Carbon::now('America/Los_Angeles')->endOfDay()->utc();

        $todayItems = (clone $baseQuery)
            ->where('status', 1)
            ->whereBetween('created_at', [$todayStartUtc, $todayEndUtc])
            ->select(['total', 'men', 'women', 'package_number_of_guest'])
            ->get();

        $todaySales = 0.0;
        $todayOrders = $todayItems->count();
        $todayGuests = 0;
        foreach ($todayItems as $ti) {
            $todaySales += (float)($ti->total ?? 0);
            $g = max(0, (int)($ti->men ?? 0) + (int)($ti->women ?? 0));
            if ($g === 0) $g = max(0, (int)($ti->package_number_of_guest ?? 0));
            $todayGuests += $g;
        }

        // All daily map for background reference
        $allDailyMap = $dailyMap;
        if (!isset($allDailyMap[$todayPst])) {
            $allDailyMap[$todayPst] = ['sales' => $todaySales, 'orders' => $todayOrders, 'guests' => $todayGuests];
        }

        return [
            'amountTotal' => $totalSales,
            'kpis' => [
                'totalSales' => $totalSales,
                'totalOrders' => $totalOrders,
                'totalGuests' => $totalGuests,
                'todaySales' => $todaySales,
                'todayOrders' => $todayOrders,
                'todayGuests' => $todayGuests,
                'isFilterActive' => $hasActiveFilter,
            ],
            'chartData' => [
                'dailyMap' => $dailyMap,
                'allDailyMap' => $allDailyMap,
            ],
        ];
    }

    /**
     * Format a single transaction into 21 DataTables cells with complete HTML & data attributes.
     */
    public function formatRowData(
        Transaction $item,
        bool $isPayoutPage,
        bool $isArchivedView,
        bool $canArchive,
        Carbon $laToday,
        $allPackagesById,
        $allPackagesByName,
        $allAddonsById,
        $allPromosById
    ): array {
        try {
            $affiliateName = null;
            $subName = null;
            $parentName = null;

            if (!empty($item->affiliate_id) && !empty($item->affiliate)) {
                if ($item->affiliate->isSubAffiliate()) {
                    $parent = $item->affiliate->parent;
                    $parentName = $parent ? ($parent->display_name ?: optional($parent->user)->name) : 'Main Promoter';
                    $subName = $item->affiliate->display_name ?: optional($item->affiliate->user)->name ?: ('Sub Promoter #' . $item->affiliate_id);
                    $affiliateName = $subName . ' (Main: ' . $parentName . ')';
                } else {
                    $affiliateName = $item->affiliate->display_name ?: optional($item->affiliate->user)->name ?: ('affiliate #' . $item->affiliate_id);
                }
            } elseif (!empty($item->entertainer_id) && !empty($item->entertainer)) {
                $affiliateName = $item->entertainer->display_name ?: optional($item->entertainer->user)->name ?: ('Entertainer #' . $item->entertainer_id);
            }

            $commission = (float)($item->affiliate_commission_amount ?? 0) + (float)($item->entertainer_commission_amount ?? 0);
            $packageName = $item->type === 'package' ? ($item->package_table_label ?: 'Package') : ($item->type === 'custom_invoice' ? 'Custom Invoice' : 'Reservation');
            $venueName = $item->website->name ?? ($item->event->name ?? 'N/A');

            $cartItems = is_array($item->cart_items ?? null) ? $item->cart_items : json_decode($item->cart_items ?? '[]', true);
            if (!is_array($cartItems)) $cartItems = [];

            $packageDetails = collect($cartItems)->map(function ($ci) use ($allPackagesById) {
                if (!is_array($ci)) return null;
                $name = trim((string) ($ci['name'] ?? $ci['package_name'] ?? $ci['packageName'] ?? $ci['pkgName'] ?? ''));
                if ($name === '') return null;
                $quantity = max(1, (int) ($ci['quantity'] ?? $ci['guests'] ?? 1));
                $packageType = strtolower(trim((string) ($ci['package_type'] ?? $ci['type'] ?? $ci['packageType'] ?? '')));
                if ($packageType === '' && !empty($ci['package_id'])) {
                    $pkg = $allPackagesById->get((int) $ci['package_id']);
                    $packageType = $pkg ? strtolower(trim((string) ($pkg->package_type ?? ''))) : '';
                }
                if ($packageType === 'ticket') {
                    return $name . ($quantity > 1 ? ' x' . $quantity : '');
                }
                return $name . ': ' . $quantity . ' ' . ($quantity === 1 ? 'guest' : 'guests');
            })->filter()->values();

            $packageDetailsText = $packageDetails->isNotEmpty()
                ? ($packageDetails->count() > 1 ? $packageDetails->implode(', ') : $packageDetails->first())
                : $packageName;

            $packageDescriptionsById = [];
            $packageDescriptionsByName = [];
            foreach ($cartItems as $ci) {
                if (!is_array($ci)) continue;
                $cid = (int) ($ci['package_id'] ?? 0);
                $cname = strtolower(trim((string) ($ci['package_name'] ?? $ci['packageName'] ?? $ci['pkgName'] ?? '')));
                if ($cid > 0 && $allPackagesById->has($cid)) {
                    $desc = (string) ($allPackagesById->get($cid)->description ?? '');
                    $packageDescriptionsById[(string)$cid] = $desc;
                    if ($cname !== '') $packageDescriptionsByName[$cname] = $desc;
                } elseif ($cname !== '' && $allPackagesByName->has($cname)) {
                    $packageDescriptionsByName[$cname] = (string) ($allPackagesByName->get($cname)->description ?? '');
                }
            }

            $packageDescriptionsPayload = [
                'byId' => $packageDescriptionsById,
                'byName' => $packageDescriptionsByName,
            ];

            $addons = collect($cartItems)->flatMap(fn($ci) => $ci['addons'] ?? [])->pluck('name')->filter()->implode(', ');
            if ($addons === '' && !empty($item->addons)) {
                foreach (explode(',', (string) $item->addons) as $av) {
                    $ao = $allAddonsById->get(trim($av));
                    if ($ao) $addons .= ($addons !== '' ? ', ' : '') . $ao->name;
                }
            }

            $promo_obj = !empty($item->promo_code) ? $allPromosById->get($item->promo_code) : null;
            $promo_code_name = $promo_obj ? $promo_obj->name : null;

            $commStatus = $item->affiliate_commission_status ?? $item->entertainer_commission_status ?? null;
            $holdUntil = $item->affiliate_commission_hold_until ?? $item->entertainer_commission_hold_until ?? null;

            $formatDatePst = function ($dateVal, $format = 'M d, Y h:i A \P\D\T') {
                if (empty($dateVal)) return '';
                try {
                    if ($dateVal instanceof \Carbon\CarbonInterface) {
                        return $dateVal->copy()->timezone('America/Los_Angeles')->format($format);
                    }
                    return Carbon::parse($dateVal)->timezone('America/Los_Angeles')->format($format);
                } catch (\Throwable $e) {
                    return '';
                }
            };

            $transactionWebsite = $item->website ?: optional($item->event)->website ?: optional($item->package)->website;
            $purchaseTimezone = optional($transactionWebsite)->resolved_timezone ?? 'America/Los_Angeles';
            $purchaseAtLocal = optional($item->created_at)->copy()?->timezone($purchaseTimezone);
            $purchaseSortOrder = $purchaseAtLocal?->timestamp ?? 0;

            // Transportation detection
            $requiresTransportationForRow = false;
            foreach ($cartItems as $rowCartItem) {
                if (!is_array($rowCartItem)) continue;
                $val = $rowCartItem['transportation'] ?? ($rowCartItem['transport'] ?? false);
                if ($val === true || $val === 1 || $val === '1' || $val === 'true' || $val === 'on') {
                    $requiresTransportationForRow = true;
                    break;
                }
            }
            if (!$requiresTransportationForRow && !empty($item->package)) {
                $pkg = $allPackagesById->get((int) $item->package_id);
                if ($pkg && ($pkg->transportation == 1 || $pkg->transportation === true || $pkg->transportation === '1')) {
                    $requiresTransportationForRow = true;
                }
            }

            $hasAdminNoteRow = !empty(trim((string) ($item->admin_notes ?? '')));
            $hasAnyNoteRow = $hasAdminNoteRow;

            // Col 0: Checkbox
            $col0 = '<input type="checkbox" class="row-check" value="' . $item->id . '">';

            // Col 1: Order ID
            $col1 = '<div>#' . str_pad($item->id, 3, '0', STR_PAD_LEFT) . '</div>';
            if ($item->is_sandbox) {
                $col1 .= '<div class="mt-1"><span class="badge bg-warning text-dark fw-bold" style="font-size:0.62rem;letter-spacing:0.5px;"><i class="fas fa-vial me-1"></i>SANDBOX</span></div>';
            }

            // Col 2: Sale Date
            $col2 = '<div class="txn-date-main">' . ($purchaseAtLocal?->format('M d, Y') ?? '-') . '</div>'
                  . '<div class="txn-date-time">' . ($purchaseAtLocal?->format('h:i A T') ?? '-') . '</div>';

            // Col 3: Confirmation #
            $col3 = '<div>' . e($item->transaction_id ?? 'N/A') . '</div>';
            if ($item->repay_paid_at) {
                $col3 .= '<div class="mt-1"><span class="badge bg-success text-white fw-bold" style="font-size:0.62rem;"><i class="fas fa-check-circle me-1"></i>REPAID LIVE</span></div>';
            } elseif ($item->is_sandbox) {
                $col3 .= '<div class="mt-1"><span class="badge bg-danger text-white fw-bold" style="font-size:0.62rem;"><i class="fas fa-exclamation-triangle me-1"></i>TEST TRANSACTION</span></div>';
            }

            // Col 4: Event / Package + Quick View Modal Trigger
            $cartJson = htmlspecialchars(json_encode($cartItems), ENT_QUOTES, 'UTF-8');
            $breakdownJson = htmlspecialchars(json_encode($item->price_breakdown), ENT_QUOTES, 'UTF-8');
            $pkgDescB64 = base64_encode(json_encode($packageDescriptionsPayload));

            $col4 = '<div style="font-size:0.85rem;font-weight:600;margin-bottom:8px;">' . e($venueName) . '</div>'
                  . '<button type="button" class="btn btn-sm btn-link-package view-btn" '
                  . 'data-total="' . (float)($item->total ?? 0) . '" '
                  . 'data-guests="' . (int)($item->package_number_of_guest ?? 1) . '" '
                  . 'data-date="' . e($purchaseAtLocal?->format('M d, Y') ?? '') . '" '
                  . 'data-date-iso="' . e($purchaseAtLocal?->format('Y-m-d') ?? '') . '" '
                  . 'data-bs-toggle="modal" data-bs-target="#packageDetailsModal" '
                  . 'data-transaction-id="' . $item->id . '" data-id="' . $item->id . '" '
                  . 'data-requires_transportation="' . ($requiresTransportationForRow ? 1 : 0) . '" '
                  . 'data-admin_notes="' . e($item->admin_notes ?? '') . '" '
                  . 'data-admin_notes_by="' . e($item->admin_notes_by ?? '') . '" '
                  . 'data-admin_notes_at="' . e($formatDatePst($item->admin_notes_at)) . '" '
                  . 'data-confirmation-number="' . e($item->transaction_id ?? 'N/A') . '" '
                  . 'data-cart-items=\'' . $cartJson . '\' '
                  . 'data-package-descriptions-b64="' . $pkgDescB64 . '" '
                  . 'data-breakdown=\'' . $breakdownJson . '\' '
                  . 'data-transaction-type="' . e($item->type) . '" '
                  . 'data-men="' . (int)($item->package_men ?? 0) . '" '
                  . 'data-women="' . (int)($item->package_women ?? 0) . '" '
                  . 'data-package-label="' . e($packageDetailsText) . '" '
                  . 'data-package_use_date="' . e($item->package_use_date ?? '') . '" '
                  . 'data-checked_in_status="' . (int)($item->checked_in_status ?? $item->checked_in ?? 0) . '" '
                  . 'data-package_number_of_guest="' . (int)($item->package_number_of_guest ?? 0) . '" '
                  . 'data-package_first_name="' . e($item->package_first_name ?? '') . '" '
                  . 'data-package_last_name="' . e($item->package_last_name ?? '') . '" '
                  . 'data-package_phone="' . e($item->package_phone ?? '') . '" '
                  . 'data-package_email="' . e($item->package_email ?? '') . '" '
                  . 'data-package_dob="' . e($item->package_dob ?? '') . '" '
                  . 'data-package_note="' . e($item->package_note ?? '') . '" '
                  . 'data-host_name="' . e($item->host_name ?? '') . '" '
                  . 'data-transportation_pickup_time="' . e($item->transportation_pickup_time ?? '') . '" '
                  . 'data-transportation_arrival_time="' . e($item->transportation_arrival_time ?? '') . '" '
                  . 'data-transportation_address="' . e($item->transportation_address ?? '') . '" '
                  . 'data-transportation_phone="' . e($item->transportation_phone ?? '') . '" '
                  . 'data-transportation_note="' . e($item->transportation_note ?? '') . '" '
                  . 'data-clublifter_customer_id="' . e($item->clublifter_customer_id ?? '') . '" '
                  . 'data-payment_first_name="' . e($item->payment_first_name ?? '') . '" '
                  . 'data-payment_last_name="' . e($item->payment_last_name ?? '') . '" '
                  . 'data-payment_phone="' . e($item->payment_phone ?? '') . '" '
                  . 'data-payment_email="' . e($item->payment_email ?? '') . '" '
                  . 'data-payment_address="' . e($item->payment_address ?? '') . '" '
                  . 'data-payment_city="' . e($item->payment_city ?? '') . '" '
                  . 'data-payment_state="' . e($item->payment_state ?? '') . '" '
                  . 'data-payment_country="' . e($item->payment_country ?? '') . '" '
                  . 'data-payment_dob="' . e($item->payment_dob ?? '') . '" '
                  . 'data-payment_zip_code="' . e($item->payment_zip_code ?? '') . '" '
                  . 'data-type="' . e($item->type) . '" data-status="' . e($item->status) . '" '
                  . 'data-ip_address="' . e($item->ip_address ?? '') . '" '
                  . 'data-website_id="' . e($item->website->name ?? '') . '" '
                  . 'data-affiliate_name="' . e($affiliateName ?: '') . '" '
                  . 'data-affiliate_sub_name="' . e($subName ?: '') . '" '
                  . 'data-affiliate_parent_name="' . e($parentName ?: '') . '" '
                  . 'data-entertainer_name="' . e($item->entertainer ? ($item->entertainer->display_name ?: optional($item->entertainer->user)->name) : '') . '" '
                  . 'data-addons="' . e($addons) . '" '
                  . 'style="font-size:0.85rem;min-width:72px;">Quick View</button>';

            // Col 5: Host Name
            $col5 = !empty($item->host_name)
                ? '<span style="font-size:0.85rem;font-weight:600;color:#fff;">' . e($item->host_name) . '</span>'
                : '<span style="color:rgba(255,255,255,0.3);font-size:0.85rem;">-</span>';

            // Col 6: Source / Referral
            $sourceText = 'Direct';
            $sourceBadgeColor = '#6b7280';
            $sourceLink = null;
            $sourceType = null;
            $subAffiliateParentName = null;

            if (!empty($item->affiliate_id) && !empty($item->affiliate)) {
                if ($item->affiliate->isSubAffiliate()) {
                    $parent = $item->affiliate->parent;
                    $subAffiliateParentName = $parent ? ($parent->display_name ?: optional($parent->user)->name) : 'Main Promoter';
                    $sourceText = $item->affiliate->display_name ?: optional($item->affiliate->user)->name ?: ('Sub Promoter #' . $item->affiliate_id);
                } else {
                    $sourceText = $item->affiliate->display_name ?: optional($item->affiliate->user)->name ?: ('Affiliate #' . $item->affiliate_id);
                }
                $sourceBadgeColor = '#8b5cf6';
                $sourceLink = route('admin.affiliate.show', $item->affiliate_id);
                $sourceType = 'affiliate';
            } elseif (!empty($item->entertainer_id) && !empty($item->entertainer)) {
                $sourceText = $item->entertainer->display_name ?: optional($item->entertainer->user)->name ?: ('Entertainer #' . $item->entertainer_id);
                $sourceBadgeColor = '#ec4899';
                $sourceLink = route('admin.entertainer.show', $item->entertainer_id);
                $sourceType = 'entertainer';
            }

            if ($sourceLink) {
                $col6 = '<a href="' . e($sourceLink) . '" style="background:' . $sourceBadgeColor . ';color:white;padding:4px 10px;border-radius:4px;font-size:0.85rem;font-weight:600;text-decoration:none;display:inline-block;cursor:pointer;" title="View ' . $sourceType . ' profile">' . e($sourceText) . '</a>';
            } else {
                $col6 = '<span style="background:' . $sourceBadgeColor . ';color:white;padding:4px 10px;border-radius:4px;font-size:0.85rem;font-weight:600;">' . e($sourceText) . '</span>';
            }
            if ($subAffiliateParentName) {
                $col6 .= '<div style="font-size:0.73rem;color:#c084fc;margin-top:3px;font-weight:500;"><i class="fas fa-sitemap me-1" style="font-size:0.68rem;"></i>Main: ' . e($subAffiliateParentName) . '</div>';
            }

            // Col 7: Customer
            $customerPhone = trim((string) ($item->package_phone ?: $item->payment_phone ?: ''));
            $resMen = (int) ($item->package_men ?? 0);
            $resWomen = (int) ($item->package_women ?? 0);
            $resGuests = $resMen + $resWomen;
            $pkgGuests = (int) ($item->package_number_of_guest ?? 0);
            $cartGuests = 0;
            foreach ($cartItems as $ci) {
                if (is_array($ci)) $cartGuests += max(1, (int) ($ci['guests'] ?? $ci['quantity'] ?? 1));
            }
            $totalGuestsCount = $resGuests > 0 ? $resGuests : ($pkgGuests > 0 ? $pkgGuests : $cartGuests);

            $col7 = '<div class="txn-customer-name">' . e($item->package_first_name . ' ' . $item->package_last_name);
            if ($totalGuestsCount > 1) {
                $col7 .= ' <span class="badge-guest-count">x' . $totalGuestsCount . '</span>';
            }
            $col7 .= '</div><div class="txn-customer-email">' . e($item->package_email) . '</div>';
            if ($customerPhone !== '') {
                $col7 .= '<div class="txn-customer-phone" style="font-size:0.75rem;color:rgba(255,255,255,0.6);margin-top:2px;"><i class="fas fa-phone me-1" style="font-size:0.65rem;color:rgba(255,255,255,0.4);"></i>' . e($customerPhone) . '</div>';
            }

            // Col 8: Amount
            $col8 = '$' . number_format((float)($item->total ?? 0), 2);

            // Col 9: Payment
            $paidAmount = (float)($item->actual_total ?? $item->total ?? 0);
            $totalAmount = (float)($item->total ?? 0);
            $dueAmount = $totalAmount - $paidAmount;
            $paymentStatus = $paidAmount >= $totalAmount ? 'Paid' : ($paidAmount > 0 ? 'Partial' : 'Pending');
            $paymentText = $paymentStatus;
            if ($paymentStatus === 'Partial') {
                $paymentText = 'Partial ($' . number_format($paidAmount, 2) . ' paid)';
            }
            $badgeClass = $paymentStatus === 'Paid' ? 'completed' : ($paymentStatus === 'Partial' ? 'warning' : 'canceled');
            $col9 = '<span class="badge-' . $badgeClass . '" style="font-size:0.85rem;">' . e($paymentText) . '</span>';

            // Col 10: Card Last 4
            $cardLast4 = trim((string) ($item->payment_card_last4 ?? ''));
            $col10 = '<span style="font-size:0.85rem;font-weight:600;color:' . ($cardLast4 !== '' ? '#fff' : 'rgba(255,255,255,0.3)') . ';">' . ($cardLast4 !== '' ? '**** ' . e($cardLast4) : '-') . '</span>';

            // Col 11: Due Amount
            $col11 = $dueAmount > 0
                ? '<span style="color:#ef4444;font-weight:600;">$' . number_format($dueAmount, 2) . '</span>'
                : '<span style="color:rgba(255,255,255,0.3);">-</span>';

            // Col 12: Reservation Status
            $reservationDatePacific = null;
            $transportAnchorAtPacific = null;
            $noShowEligibleAtPacific = null;
            if (!empty($item->package_use_date)) {
                try {
                    $reservationDatePacific = Carbon::parse($item->package_use_date, 'America/Los_Angeles')->startOfDay();
                    $transportTimeRaw = trim((string) ($item->transportation_arrival_time ?: $item->transportation_pickup_time ?: ''));
                    if ($transportTimeRaw !== '') {
                        $transportAnchorAtPacific = Carbon::parse($reservationDatePacific->format('Y-m-d') . ' ' . $transportTimeRaw, 'America/Los_Angeles');
                        $noShowEligibleAtPacific = $transportAnchorAtPacific->copy()->addHours(24);
                    }
                } catch (\Throwable $e) {}
            }

            $reservationStatusValue = 'Upcoming';
            $reservationStatusClass = 'badge-reservation-upcoming';
            $nowPacific = Carbon::now('America/Los_Angeles');

            if ($item->checked_in_status) {
                $reservationStatusValue = 'Checked In';
                $reservationStatusClass = 'badge-reservation-checked-in';
            } else {
                if ($reservationDatePacific) {
                    if ($reservationDatePacific->equalTo($laToday)) {
                        $reservationStatusValue = 'Today';
                        $reservationStatusClass = 'badge-reservation-today';
                    } elseif ($reservationDatePacific->greaterThan($laToday)) {
                        $reservationStatusValue = 'Upcoming';
                        $reservationStatusClass = 'badge-reservation-upcoming';
                    } else {
                        if ($noShowEligibleAtPacific && $nowPacific->greaterThanOrEqualTo($noShowEligibleAtPacific)) {
                            $reservationStatusValue = 'No Show';
                            $reservationStatusClass = 'badge-reservation-no-show';
                        } else {
                            $reservationStatusValue = 'Upcoming';
                            $reservationStatusClass = 'badge-reservation-upcoming';
                        }
                    }
                }
                if ($item->status == 2) {
                    $reservationStatusValue = 'Refunded';
                    $reservationStatusClass = 'badge-reservation-refunded';
                } elseif ($item->status == 0) {
                    $reservationStatusValue = 'Cancelled';
                    $reservationStatusClass = 'badge-reservation-cancelled';
                }
            }

            if ($reservationStatusValue === 'Upcoming' && $reservationDatePacific) {
                $col12 = '<div style="font-size:0.9rem;margin-bottom:0.5rem;">' . $reservationDatePacific->format('M d, Y') . '</div>'
                       . '<div style="margin-top:4px;"><span class="' . $reservationStatusClass . '">' . $reservationStatusValue . '</span></div>';
            } else {
                $col12 = '<span class="' . $reservationStatusClass . '">' . $reservationStatusValue . '</span>';
            }

            // Col 13: Reservation Date
            $transportModeLabel = $item->transport_mode_label ?? null;
            if ($reservationDatePacific) {
                if ($reservationDatePacific->equalTo($laToday)) {
                    $col13 = '<div style="font-size:0.95rem;font-weight:600;">Today</div>';
                } elseif ($reservationDatePacific->greaterThan($laToday)) {
                    $col13 = '<div style="font-size:0.9rem;">' . $reservationDatePacific->format('M d, Y') . '</div>';
                } else {
                    $col13 = '<div style="font-size:0.9rem;color:rgba(255,255,255,0.6);">' . $reservationDatePacific->format('M d, Y') . '</div>';
                }
            } else {
                $col13 = '<span style="color:rgba(255,255,255,0.25);font-size:0.78rem">-</span>';
            }
            if (!empty($transportModeLabel)) {
                $bg = $transportModeLabel === 'Self Drive' ? 'rgba(16,185,129,0.14)' : 'rgba(59,130,246,0.14)';
                $color = $transportModeLabel === 'Self Drive' ? '#34d399' : '#93c5fd';
                $border = $transportModeLabel === 'Self Drive' ? 'rgba(16,185,129,0.25)' : 'rgba(59,130,246,0.25)';
                $col13 .= '<div style="margin-top:4px;display:inline-block;padding:2px 8px;border-radius:999px;font-size:0.72rem;font-weight:700;line-height:1.2;background:' . $bg . ';color:' . $color . ';border:1px solid ' . $border . ';">' . e($transportModeLabel) . '</div>';
            }

            // Col 14: Entry Status
            $col14 = $item->checked_in_status
                ? '<span class="badge-checkin-yes">Redeemed</span>'
                : '<span class="badge-checkin-no">Not Redeemed</span>';

            // Col 15: Fee / Commission
            $commDisplay = ($commission == intval($commission)) ? number_format($commission, 0) : number_format($commission, 2);
            $commissionText = '$' . $commDisplay;
            if ($commStatus === 'pending' && $holdUntil) {
                $daysRemaining = (int) Carbon::now()->diffInDays($holdUntil, false);
                $commissionText .= $daysRemaining <= 0 ? ' (Available now)' : ' (Available in ' . abs($daysRemaining) . ' days)';
            } elseif ($commStatus === 'paid') {
                $commissionText .= ' (Paid out)';
            } elseif ($commStatus === 'approved') {
                $commissionText .= ' (Approved)';
            } elseif ($commStatus === 'reversed') {
                $commissionText .= ' (Reversed)';
            }
            $col15 = '<div style="font-weight:600;">' . e($commissionText) . '</div>';

            // Col 16: Action dropdown & View/Notes buttons
            $viewBtn = '<button type="button" class="txn-action-eye view-btn" '
                . 'data-bs-toggle="modal" data-bs-target="#viewTransactionModal" '
                . 'data-id="' . $item->id . '" '
                . 'data-admin_notes="' . e($item->admin_notes ?? '') . '" '
                . 'data-admin_notes_by="' . e($item->admin_notes_by ?? '') . '" '
                . 'data-admin_notes_at="' . e($item->admin_notes_at ? optional($item->admin_notes_at)->timezone('America/Los_Angeles')->format('M d, Y h:i A \P\D\T') : '') . '" '
                . 'data-transaction_id="' . e($item->transaction_id ?? 'Free') . '" '
                . 'data-package_id="' . e($packageDetailsText) . '" '
                . 'data-cart-items=\'' . $cartJson . '\' '
                . 'data-breakdown=\'' . $breakdownJson . '\' '
                . 'data-package_first_name="' . e($item->package_first_name) . '" '
                . 'data-package_last_name="' . e($item->package_last_name) . '" '
                . 'data-package_phone="' . e($item->package_phone) . '" '
                . 'data-package_email="' . e($item->package_email) . '" '
                . 'data-package_dob="' . e($item->package_dob) . '" '
                . 'data-package_note="' . e($item->package_note) . '" '
                . 'data-host_name="' . e($item->host_name) . '" '
                . 'data-package_number_of_guest="' . (int)($item->package_number_of_guest ?? 0) . '" '
                . 'data-transportation_pickup_time="' . e($item->transportation_pickup_time) . '" '
                . 'data-transportation_arrival_time="' . e($item->transportation_arrival_time) . '" '
                . 'data-transportation_address="' . e($item->transportation_address) . '" '
                . 'data-transportation_phone="' . e($item->transportation_phone) . '" '
                . 'data-transportation_guest="' . e($item->transportation_guest) . '" '
                . 'data-transportation_note="' . e($item->transportation_note) . '" '
                . 'data-clublifter_customer_id="' . e($item->clublifter_customer_id ?? '') . '" '
                . 'data-payment_first_name="' . e($item->payment_first_name) . '" '
                . 'data-payment_last_name="' . e($item->payment_last_name) . '" '
                . 'data-payment_phone="' . e($item->payment_phone) . '" '
                . 'data-payment_email="' . e($item->payment_email) . '" '
                . 'data-payment_address="' . e($item->payment_address) . '" '
                . 'data-payment_city="' . e($item->payment_city) . '" '
                . 'data-payment_state="' . e($item->payment_state) . '" '
                . 'data-payment_country="' . e($item->payment_country) . '" '
                . 'data-payment_dob="' . e($item->payment_dob) . '" '
                . 'data-payment_zip_code="' . e($item->payment_zip_code) . '" '
                . 'data-type="' . e($item->type) . '" '
                . 'data-status="' . e($item->status) . '" '
                . 'data-ip_address="' . e($item->ip_address) . '" '
                . 'data-website_id="' . e($item->website->name ?? '') . '" '
                . 'data-addons="' . e($addons) . '" '
                . 'data-total="' . e($item->total) . '" '
                . 'data-subtotal="' . e($item->actual_total) . '" '
                . 'data-date="' . e($purchaseAtLocal?->format('Y-m-d h:i A T') ?? '') . '" '
                . 'data-affiliate_name="' . e($affiliateName ?: '') . '" '
                . 'data-total_commission="' . (float) $commission . '" '
                . 'title="View Details"><i class="fas fa-eye"></i></button>';

            $notesBtnClass = $hasAnyNoteRow ? 'btn-warning text-dark fw-bold btn-has-note' : 'btn-outline-warning';
            $notesBtn = '<button type="button" class="btn btn-sm ' . $notesBtnClass . ' open-notes-btn px-2 py-1 ms-1" '
                . 'data-bs-toggle="modal" data-bs-target="#txnNotesModal" '
                . 'data-id="' . $item->id . '" '
                . 'data-transaction-id="' . e($item->transaction_id ?? 'Free') . '" '
                . 'data-admin_notes="' . e($item->admin_notes ?? '') . '" '
                . 'data-admin_notes_by="' . e($item->admin_notes_by ?? '') . '" '
                . 'data-admin_notes_at="' . e($formatDatePst($item->admin_notes_at)) . '" '
                . 'data-package_note="' . e($item->package_note ?? '') . '" '
                . 'data-transportation_note="' . e($item->transportation_note ?? '') . '" '
                . 'title="' . ($hasAnyNoteRow ? 'Has Notes - Click to view/edit' : 'Notes') . '" '
                . 'style="font-size:0.75rem;border-radius:6px;font-weight:600;">'
                . '<i class="fas fa-sticky-note me-1"></i>Notes'
                . ($hasAnyNoteRow ? '<span class="badge bg-dark text-warning rounded-circle ms-1 p-1" style="font-size:0.6rem;line-height:1;">!</span>' : '')
                . '</button>';

            $dropdown = '<div class="dropdown">'
                . '<button class="txn-action-more btn p-0" data-bs-toggle="dropdown" type="button" style="border:none;background:none"><i class="fas fa-ellipsis-v"></i></button>'
                . '<ul class="dropdown-menu dropdown-menu-end" style="background:#1e293b;border:1px solid rgba(255,255,255,0.1)">'
                . '<li><a class="dropdown-item open-notes-btn ' . ($hasAnyNoteRow ? 'fw-bold text-warning' : '') . '" style="color:rgba(255,255,255,0.7);font-size:0.82rem" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#txnNotesModal" data-id="' . $item->id . '" data-transaction-id="' . e($item->transaction_id ?? 'Free') . '" data-admin_notes="' . e($item->admin_notes ?? '') . '" data-admin_notes_by="' . e($item->admin_notes_by ?? '') . '" data-admin_notes_at="' . e($formatDatePst($item->admin_notes_at)) . '" data-package_note="' . e($item->package_note ?? '') . '" data-transportation_note="' . e($item->transportation_note ?? '') . '"><i class="fas fa-sticky-note me-2 text-warning"></i>Notes ' . ($hasAnyNoteRow ? '<span class="badge bg-warning text-dark ms-1" style="font-size:0.65rem;">Has Note</span>' : '') . '</a></li>';

            if (!$isArchivedView) {
                $dropdown .= '<li><a class="dropdown-item" style="color:rgba(255,255,255,0.7);font-size:0.82rem" href="' . route('admin.transaction.update', ['id' => $item->id, 'status' => 1]) . '"><i class="fas fa-check me-2 text-success"></i>Mark Completed</a></li>'
                           . '<li><a class="dropdown-item" style="color:rgba(255,255,255,0.7);font-size:0.82rem" href="' . route('admin.transaction.update', ['id' => $item->id, 'status' => 0]) . '"><i class="fas fa-times me-2 text-danger"></i>Mark Canceled</a></li>'
                           . '<li><a class="dropdown-item" style="color:rgba(255,255,255,0.7);font-size:0.82rem" href="' . route('admin.transaction.update', ['id' => $item->id, 'status' => 2]) . '"><i class="fas fa-undo me-2 text-warning"></i>Mark Refunded</a></li>'
                           . '<li><hr class="dropdown-divider" style="border-color:rgba(255,255,255,0.12)"></li>';

                if (auth()->check() && auth()->user()->isAdmin()) {
                    $dropdown .= '<li><a class="dropdown-item btn-send-repay-trigger" style="color:#fbbf24;font-size:0.82rem" href="javascript:void(0)" data-id="' . $item->id . '" data-txnid="' . e($item->transaction_id ?? $item->id) . '" data-email="' . e($item->package_email ?: $item->payment_email) . '" data-amount="$' . number_format((float)$item->total, 2) . '" data-repay-url="' . e($item->repay_url) . '" data-send-url="' . route('admin.transaction.send-repay-email', $item->id) . '"><i class="fas fa-paper-plane me-2 text-warning"></i>Send Payment (Repay) Link</a></li>'
                               . '<li><form action="' . route('admin.transaction.resend-email', $item->id) . '" method="POST" class="d-inline" onsubmit="return confirm(\'Resend receipt confirmation email to purchaser?\');"><input type="hidden" name="_token" value="' . csrf_token() . '"><button type="submit" class="dropdown-item" style="color:#60a5fa;font-size:0.82rem"><i class="fas fa-envelope-open-text me-2 text-info"></i>Resend Confirmation Email</button></form></li>'
                               . '<li><a class="dropdown-item btn-toggle-sandbox-trigger" style="color:rgba(255,255,255,0.7);font-size:0.82rem" href="javascript:void(0)" data-id="' . $item->id . '" data-is-sandbox="' . ($item->is_sandbox ? '1' : '0') . '"><i class="fas fa-vial me-2 ' . ($item->is_sandbox ? 'text-success' : 'text-warning') . '"></i>' . ($item->is_sandbox ? 'Mark as Live' : 'Mark as Sandbox') . '</a></li>';
                }
            }

            if ($canArchive) {
                $dropdown .= '<li><hr class="dropdown-divider" style="border-color:rgba(255,255,255,0.12)"></li>';
                if ($isArchivedView) {
                    $dropdown .= '<li><form method="POST" action="' . route('admin.transaction.unarchive', $item->id) . '" onsubmit="return confirm(\'Unarchive this transaction?\');"><input type="hidden" name="_token" value="' . csrf_token() . '"><button type="submit" class="dropdown-item" style="color:#34d399;font-size:0.82rem"><i class="fas fa-box-open me-2 text-success"></i>Unarchive Transaction</button></form></li>';
                } else {
                    $dropdown .= '<li><form method="POST" action="' . route('admin.transaction.archive', $item->id) . '" onsubmit="return confirm(\'Archive this transaction? Archived transactions are removed from totals and reports.\');"><input type="hidden" name="_token" value="' . csrf_token() . '"><button type="submit" class="dropdown-item" style="color:#fbbf24;font-size:0.82rem"><i class="fas fa-archive me-2 text-warning"></i>Archive Transaction</button></form></li>';
                }
            }

            $dropdown .= '</ul></div>';
            $col16 = '<div class="d-flex align-items-center gap-1">' . $viewBtn . $notesBtn . $dropdown . '</div>';

            // Hidden columns
            $col17 = $item->website->name ?? '';
            $col18 = $item->type ?? '';
            $col19 = $affiliateName ?: 'DIRECT';
            $col20 = $commissionText ?? '';

            return [
                'DT_RowId' => 'txn-row-' . $item->id,
                'DT_RowAttr' => [
                    'data-row-id' => $item->id,
                    'data-row-error' => '',
                ],
                0 => $col0,
                1 => $col1,
                2 => $col2,
                3 => $col3,
                4 => $col4,
                5 => $col5,
                6 => $col6,
                7 => $col7,
                8 => $col8,
                9 => $col9,
                10 => $col10,
                11 => $col11,
                12 => $col12,
                13 => $col13,
                14 => $col14,
                15 => $col15,
                16 => $col16,
                17 => $col17,
                18 => $col18,
                19 => $col19,
                20 => $col20,
            ];

        } catch (\Throwable $e) {
            return [
                'DT_RowId' => 'txn-row-' . $item->id,
                'DT_RowAttr' => [
                    'data-row-id' => $item->id,
                    'data-row-error' => $e->getMessage(),
                ],
                0 => '<input type="checkbox" class="row-check" value="' . $item->id . '">',
                1 => '#' . $item->id,
                2 => optional($item->created_at)->format('M d, Y') ?? '-',
                3 => e($item->transaction_id ?? 'N/A'),
                4 => 'Error rendering row',
                5 => '-',
                6 => 'Direct',
                7 => e($item->package_first_name ?? ''),
                8 => '$' . number_format((float)($item->total ?? 0), 2),
                9 => 'Paid',
                10 => '-',
                11 => '-',
                12 => 'Completed',
                13 => '-',
                14 => 'Not Redeemed',
                15 => '$0.00',
                16 => '',
                17 => '',
                18 => '',
                19 => 'DIRECT',
                20 => '',
            ];
        }
    }

    /**
     * Fetch initial 25 records and summary data for initial page render.
     */
    public function getInitialPageData(Request $request, $user, ?callable $queryMutator = null): array
    {
        $baseQuery = $this->getBaseQuery($request, $user);
        if ($queryMutator) {
            $queryMutator($baseQuery);
        }
        $recordsTotal = (clone $baseQuery)->count();

        $filteredQuery = $this->buildFilteredQuery($request, $user, $queryMutator);
        $recordsFiltered = (clone $filteredQuery)->count();

        // On initial render, pull top 25 recent
        $initialData = (clone $filteredQuery)
            ->with([
                'event.website',
                'package.website',
                'website',
                'affiliate.user',
                'affiliate.parent.user',
                'entertainer.user'
            ])
            ->latest()
            ->take(25)
            ->get();

        $summary = $this->calculateKpisAndCharts($baseQuery, $filteredQuery, $request);

        return [
            'data' => $initialData,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'kpis' => $summary['kpis'],
            'chartData' => $summary['chartData'],
            'amountTotal' => $summary['amountTotal'],
        ];
    }
}

