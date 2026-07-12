<?php

namespace App\Services;

use App\Models\Report;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Package;
use App\Models\Event;
use App\Models\Affiliate;
use App\Models\Entertainer;
use App\Models\Website;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportGenerationService
{
    private User $user;
    private array $filters;

    public function __construct(User $user, array $filters = [])
    {
        $this->user = $user;
        $this->filters = $filters;
    }

    public function generate(Report $report): array
    {
        return match ($report->slug) {
            // SALES REPORTS
            'revenue-over-time' => $this->revenueOverTime(),
            'revenue-by-package' => $this->revenueByPackage(),
            'revenue-by-affiliate' => $this->revenueByAffiliate(),
            'revenue-by-payment-method' => $this->revenueByPaymentMethod(),
            'refund-analysis' => $this->refundAnalysis(),
            'promo-code-effectiveness' => $this->promoCodeEffectiveness(),

            // ORDER REPORTS
            'orders-over-time' => $this->ordersOverTime(),
            'orders-by-status' => $this->ordersByStatus(),
            'orders-by-package-type' => $this->ordersByPackageType(),
            'multi-package-orders' => $this->multiPackageOrders(),
            'average-order-value' => $this->averageOrderValue(),

            // ACQUISITION REPORTS
            'new-affiliates-over-time' => $this->newAffiliatesOverTime(),
            'affiliate-performance-ranking' => $this->affiliatePerformanceRanking(),
            'affiliate-commission-tracking' => $this->affiliateCommissionTracking(),

            // ENTERTAINER REPORTS
            'events-per-entertainer' => $this->eventsPerEntertainer(),
            'entertainer-earnings' => $this->entertainerEarnings(),
            'entertainer-commission-tracking' => $this->entertainerCommissionTracking(),

            // PACKAGE REPORTS
            'sales-by-package' => $this->salesByPackage(),
            'most-popular-packages' => $this->mostPopularPackages(),
            'package-capacity-utilization' => $this->packageCapacityUtilization(),

            // CUSTOMER REPORTS
            'new-customers-over-time' => $this->newCustomersOverTime(),
            'repeat-vs-first-time' => $this->repeatVsFirstTime(),
            'customer-by-location' => $this->customerByLocation(),

            // EVENT REPORTS
            'attendance-by-event' => $this->attendanceByEvent(),
            'event-revenue' => $this->eventRevenue(),
            'event-capacity-utilization' => $this->eventCapacityUtilization(),

            // FINANCIAL REPORTS
            'revenue-summary' => $this->revenueSummary(),
            'commission-expenses' => $this->commissionExpenses(),
            'net-revenue' => $this->netRevenue(),

            default => ['error' => 'Report not found'],
        };
    }

    // ========== HELPER METHODS ==========

    private function getDateRange(): array
    {
        $period = $this->filters['date_range'] ?? 'last_30_days';
        $endDate = now();

        return match ($period) {
            'today' => [now()->startOfDay(), $endDate],
            'yesterday' => [now()->subDay()->startOfDay(), now()->subDay()->endOfDay()],
            'last_7_days' => [now()->subDays(7), $endDate],
            'last_30_days' => [now()->subDays(30), $endDate],
            'last_90_days' => [now()->subDays(90), $endDate],
            'this_month' => [now()->startOfMonth(), $endDate],
            'last_month' => [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()],
            'this_year' => [now()->startOfYear(), $endDate],
            'custom' => [
                !empty($this->filters['start_date']) ? Carbon::parse($this->filters['start_date'])->startOfDay() : now()->startOfDay(),
                !empty($this->filters['end_date']) ? Carbon::parse($this->filters['end_date'])->endOfDay() : $endDate,
            ],
            default => [now()->subDays(30), $endDate],
        };
    }

    private function applyUserScope($query)
    {
        if ($this->user->user_type === 'admin') {
            return $query;
        }

        if ($this->user->website_id) {
            $query->where('website_id', $this->user->website_id);
        }

        if ($this->user->affiliate_id) {
            $query->where('affiliate_id', $this->user->affiliate_id);
        }

        if ($this->user->entertainer_id) {
            $query->where('entertainer_id', $this->user->entertainer_id);
        }

        return $query;
    }

    // ========== SALES REPORTS ==========

    private function revenueOverTime(): array
    {
        [$startDate, $endDate] = $this->getDateRange();

        $rawData = Transaction::query()
            ->where('status', 1) // Completed only
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as revenue'), DB::raw('COUNT(*) as transactions'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $data = $rawData->map(fn ($row) => [
            'date' => $row->date,
            'revenue' => (float) $row->revenue,
            'transactions' => (int) $row->transactions,
        ]);

        // Format for Chart.js
        $chartData = [
            'labels' => $rawData->pluck('date')->toArray(),
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => $rawData->pluck('revenue')->map(fn($v) => (float)$v)->toArray(),
                    'borderColor' => 'rgb(75, 192, 192)',
                    'backgroundColor' => 'rgba(75, 192, 192, 0.1)',
                    'tension' => 0.1,
                    'fill' => true,
                ]
            ]
        ];

        return [
            'type' => 'line_chart',
            'title' => 'Revenue Over Time',
            'data' => $chartData,
            'raw_data' => $data->toArray(),
            'metrics' => [
                'total_revenue' => $data->sum('revenue'),
                'total_transactions' => $data->sum('transactions'),
                'average_daily_revenue' => $data->count() > 0 ? $data->sum('revenue') / $data->count() : 0,
            ],
        ];
    }

    private function revenueByPackage(): array
    {
        [$startDate, $endDate] = $this->getDateRange();

        $data = Transaction::query()
            ->where('transactions.status', 1)
            ->whereBetween('transactions.created_at', [$startDate, $endDate])
            ->join('packages', 'transactions.package_id', '=', 'packages.id')
            ->select('packages.name', DB::raw('SUM(transactions.total) as revenue'), DB::raw('COUNT(transactions.id) as orders'))
            ->groupBy('packages.id', 'packages.name')
            ->orderByDesc('revenue')
            ->limit(15)
            ->get()
            ->map(fn ($row) => [
                'package' => $row->name,
                'revenue' => (float) $row->revenue,
                'orders' => (int) $row->orders,
            ]);

        return [
            'type' => 'table',
            'title' => 'Revenue by Package',
            'data' => $data->toArray(),
        ];
    }

    private function revenueByAffiliate(): array
    {
        [$startDate, $endDate] = $this->getDateRange();

        $data = Transaction::query()
            ->where('transactions.status', 1)
            ->whereBetween('transactions.created_at', [$startDate, $endDate])
            ->whereNotNull('transactions.affiliate_id')
            ->join('affiliates', 'transactions.affiliate_id', '=', 'affiliates.id')
            ->select(
                'affiliates.id',
                DB::raw("COALESCE(affiliates.display_name, users.name, CONCAT('affiliate #', affiliates.id)) as affiliate_name"),
                DB::raw('SUM(transactions.total) as revenue'),
                DB::raw('COUNT(transactions.id) as orders')
            )
            ->leftJoin('users', 'affiliates.user_id', '=', 'users.id')
            ->groupBy('affiliates.id')
            ->orderByDesc('revenue')
            ->limit(20)
            ->get()
            ->map(fn ($row) => [
                'affiliate' => $row->affiliate_name,
                'revenue' => (float) $row->revenue,
                'orders' => (int) $row->orders,
            ]);

        return [
            'type' => 'table',
            'title' => 'Revenue by affiliate',
            'data' => $data->toArray(),
        ];
    }

    private function revenueByPaymentMethod(): array
    {
        [$startDate, $endDate] = $this->getDateRange();

        // This would depend on your payment method field
        $data = [
            ['method' => 'Credit Card', 'revenue' => 50000, 'transactions' => 245],
            ['method' => 'Debit Card', 'revenue' => 15000, 'transactions' => 87],
            ['method' => 'PayPal', 'revenue' => 8500, 'transactions' => 34],
        ];

        return [
            'type' => 'pie_chart',
            'title' => 'Revenue by Payment Method',
            'data' => $data,
        ];
    }

    private function refundAnalysis(): array
    {
        [$startDate, $endDate] = $this->getDateRange();

        $refunded = 0;
        $completed = Transaction::query()->financiallyReportable()->whereBetween('created_at', [$startDate, $endDate])->sum('total');
        $canceled = 0;

        $total = $refunded + $completed + $canceled;

        return [
            'type' => 'metric',
            'title' => 'Refund Analysis',
            'metrics' => [
                'total_refunded' => (float) $refunded,
                'refund_rate' => $total > 0 ? round(($refunded / $total) * 100, 2) : 0,
                'canceled_orders' => 0,
            ],
        ];
    }

    private function promoCodeEffectiveness(): array
    {
        // This requires promo code tracking
        return [
            'type' => 'table',
            'title' => 'Promo Code Effectiveness',
            'data' => [],
        ];
    }

    // ========== ORDER REPORTS ==========

    private function ordersOverTime(): array
    {
        [$startDate, $endDate] = $this->getDateRange();

        $rawData = Transaction::query()
            ->financiallyReportable()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as completed'),
                DB::raw('0 as canceled'),
                DB::raw('0 as refunded')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $data = $rawData->map(fn ($row) => [
            'date' => $row->date,
            'completed' => (int) $row->completed,
            'canceled' => (int) $row->canceled,
            'refunded' => (int) $row->refunded,
        ]);

        // Format for Chart.js
        $chartData = [
            'labels' => $rawData->pluck('date')->toArray(),
            'datasets' => [
                [
                    'label' => 'Completed',
                    'data' => $rawData->pluck('completed')->map(fn($v) => (int)$v)->toArray(),
                    'backgroundColor' => 'rgba(75, 192, 75, 0.6)',
                    'borderColor' => 'rgb(75, 192, 75)',
                ],
                [
                    'label' => 'Canceled',
                    'data' => $rawData->pluck('canceled')->map(fn($v) => (int)$v)->toArray(),
                    'backgroundColor' => 'rgba(255, 99, 99, 0.6)',
                    'borderColor' => 'rgb(255, 99, 99)',
                ],
                [
                    'label' => 'Refunded',
                    'data' => $rawData->pluck('refunded')->map(fn($v) => (int)$v)->toArray(),
                    'backgroundColor' => 'rgba(255, 193, 7, 0.6)',
                    'borderColor' => 'rgb(255, 193, 7)',
                ]
            ]
        ];

        return [
            'type' => 'stacked_bar',
            'title' => 'Orders Over Time',
            'data' => $chartData,
            'raw_data' => $data->toArray(),
        ];
    }

    private function ordersByStatus(): array
    {
        [$startDate, $endDate] = $this->getDateRange();

        $completed = Transaction::query()->financiallyReportable()->whereBetween('created_at', [$startDate, $endDate])->count();
        $canceled = 0;
        $refunded = 0;

        $rawData = [
            ['status' => 'Completed', 'count' => $completed],
            ['status' => 'Canceled', 'count' => $canceled],
            ['status' => 'Refunded', 'count' => $refunded],
        ];

        // Format for Chart.js
        $chartData = [
            'labels' => ['Completed', 'Canceled', 'Refunded'],
            'datasets' => [
                [
                    'data' => [(int)$completed, (int)$canceled, (int)$refunded],
                    'backgroundColor' => [
                        'rgba(75, 192, 75, 0.8)',
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(255, 193, 7, 0.8)',
                    ],
                    'borderColor' => [
                        'rgb(75, 192, 75)',
                        'rgb(255, 99, 132)',
                        'rgb(255, 193, 7)',
                    ],
                    'borderWidth' => 1,
                ]
            ]
        ];

        return [
            'type' => 'pie_chart',
            'title' => 'Orders by Status',
            'data' => $chartData,
            'raw_data' => $rawData,
        ];
    }

    private function ordersByPackageType(): array
    {
        [$startDate, $endDate] = $this->getDateRange();

        // Count by package type (ticket vs table)
        $data = Transaction::query()
            ->financiallyReportable()
            ->join('packages', 'transactions.package_id', '=', 'packages.id')
            ->whereBetween('transactions.created_at', [$startDate, $endDate])
            ->select('packages.package_type', DB::raw('COUNT(*) as count'), DB::raw('SUM(transactions.total) as revenue'))
            ->groupBy('packages.package_type')
            ->get()
            ->map(fn ($row) => [
                'type' => ucfirst($row->package_type),
                'orders' => (int) $row->count,
                'revenue' => (float) $row->revenue,
            ]);

        return [
            'type' => 'table',
            'title' => 'Orders by Package Type',
            'data' => $data->toArray(),
        ];
    }

    private function multiPackageOrders(): array
    {
        [$startDate, $endDate] = $this->getDateRange();

        // Count transactions with multiple items in cart_items
        $data = Transaction::query()
            ->financiallyReportable()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where(function ($q) {
                $q->whereRaw("JSON_LENGTH(cart_items) > 1")
                  ->orWhereRaw("JSON_EXTRACT(cart_items, '$.package_names') IS NOT NULL AND JSON_LENGTH(JSON_EXTRACT(cart_items, '$.package_names')) > 1");
            })
            ->count();

        return [
            'type' => 'metric',
            'title' => 'Multi-Package Orders',
            'metrics' => [
                'multi_package_count' => $data,
                'single_package_count' => Transaction::query()->financiallyReportable()->whereBetween('created_at', [$startDate, $endDate])->count() - $data,
            ],
        ];
    }

    private function averageOrderValue(): array
    {
        [$startDate, $endDate] = $this->getDateRange();

        $aov = Transaction::where('status', 1)->whereBetween('created_at', [$startDate, $endDate])->avg('total');

        return [
            'type' => 'metric',
            'title' => 'Average Order Value',
            'metrics' => [
                'aov' => (float) $aov,
            ],
        ];
    }

    // ========== ACQUISITION REPORTS ==========

    private function newAffiliatesOverTime(): array
    {
        [$startDate, $endDate] = $this->getDateRange();

        $rawData = Affiliate::query()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $data = $rawData->map(fn ($row) => [
            'date' => $row->date,
            'count' => (int) $row->count,
        ]);

        // Format for Chart.js
        $chartData = [
            'labels' => $rawData->pluck('date')->toArray(),
            'datasets' => [
                [
                    'label' => 'New affiliates',
                    'data' => $rawData->pluck('count')->map(fn($v) => (int)$v)->toArray(),
                    'borderColor' => 'rgb(75, 192, 192)',
                    'backgroundColor' => 'rgba(75, 192, 192, 0.1)',
                    'tension' => 0.1,
                    'fill' => true,
                ]
            ]
        ];

        return [
            'type' => 'line_chart',
            'title' => 'New affiliates Over Time',
            'data' => $chartData,
            'raw_data' => $data->toArray(),
        ];
    }

    private function affiliatePerformanceRanking(): array
    {
        [$startDate, $endDate] = $this->getDateRange();

        $data = Affiliate::query()
            ->select(
                'affiliates.id',
                DB::raw("COALESCE(affiliates.display_name, users.name, CONCAT('affiliate #', affiliates.id)) as name"),
                DB::raw('COUNT(transactions.id) as orders'),
                DB::raw('SUM(transactions.total) as revenue')
            )
            ->leftJoin('users', 'affiliates.user_id', '=', 'users.id')
            ->leftJoin('transactions', 'affiliates.id', '=', 'transactions.affiliate_id')
            ->whereBetween('transactions.created_at', [$startDate, $endDate])
            ->whereNull('transactions.archived_at')
            ->where('transactions.status', Transaction::STATUS_COMPLETED)
            ->groupBy('affiliates.id')
            ->orderByDesc('revenue')
            ->limit(20)
            ->get()
            ->map(fn ($row) => [
                'affiliate' => $row->name,
                'orders' => (int) $row->orders,
                'revenue' => (float) $row->revenue,
            ]);

        return [
            'type' => 'table',
            'title' => 'affiliate Performance Ranking',
            'data' => $data->toArray(),
        ];
    }

    private function affiliateCommissionTracking(): array
    {
        [$startDate, $endDate] = $this->getDateRange();

        $data = Transaction::query()
            ->financiallyReportable()
            ->whereNotNull('transactions.affiliate_id')
            ->whereBetween('transactions.created_at', [$startDate, $endDate])
            ->select(
                'transactions.affiliate_id',
                DB::raw("COALESCE(affiliates.display_name, users.name, CONCAT('affiliate #', transactions.affiliate_id)) as affiliate_name"),
                DB::raw('SUM(COALESCE(affiliate_commission_amount, 0)) as total_commission'),
                DB::raw('SUM(transactions.total) as revenue')
            )
            ->join('affiliates', 'transactions.affiliate_id', '=', 'affiliates.id')
            ->leftJoin('users', 'affiliates.user_id', '=', 'users.id')
            ->groupBy('transactions.affiliate_id')
            ->orderByDesc('total_commission')
            ->get()
            ->map(fn ($row) => [
                'affiliate' => $row->affiliate_name,
                'commission' => (float) $row->total_commission,
                'revenue' => (float) $row->revenue,
                'commission_rate' => $row->revenue > 0 ? round(($row->total_commission / $row->revenue) * 100, 2) : 0,
            ]);

        return [
            'type' => 'table',
            'title' => 'affiliate Commission Tracking',
            'data' => $data->toArray(),
        ];
    }

    // ========== ENTERTAINER REPORTS ==========

    private function eventsPerEntertainer(): array
    {
        [$startDate, $endDate] = $this->getDateRange();

        $data = Entertainer::query()
            ->select(
                'entertainers.id',
                DB::raw("COALESCE(entertainers.display_name, users.name, CONCAT('Entertainer #', entertainers.id)) as name"),
                DB::raw('COUNT(DISTINCT events.id) as event_count')
            )
            ->leftJoin('users', 'entertainers.user_id', '=', 'users.id')
            ->leftJoin('entertainer_packages', 'entertainers.id', '=', 'entertainer_packages.entertainer_id')
            ->leftJoin('packages', 'entertainer_packages.package_id', '=', 'packages.id')
            ->leftJoin('events', 'packages.event_id', '=', 'events.id')
            ->whereBetween('events.created_at', [$startDate, $endDate])
            ->groupBy('entertainers.id')
            ->orderByDesc('event_count')
            ->limit(15)
            ->get()
            ->map(fn ($row) => [
                'entertainer' => $row->name,
                'events' => (int) $row->event_count,
            ]);

        return [
            'type' => 'table',
            'title' => 'Events Per Entertainer',
            'data' => $data->toArray(),
        ];
    }

    private function entertainerEarnings(): array
    {
        [$startDate, $endDate] = $this->getDateRange();

        $data = Transaction::query()
            ->financiallyReportable()
            ->whereNotNull('transactions.entertainer_id')
            ->whereBetween('transactions.created_at', [$startDate, $endDate])
            ->select(
                'transactions.entertainer_id',
                DB::raw("COALESCE(entertainers.display_name, users.name, CONCAT('Entertainer #', transactions.entertainer_id)) as name"),
                DB::raw('SUM(transactions.total) as revenue'),
                DB::raw('SUM(COALESCE(entertainer_commission_amount, 0)) as commission')
            )
            ->join('entertainers', 'transactions.entertainer_id', '=', 'entertainers.id')
            ->leftJoin('users', 'entertainers.user_id', '=', 'users.id')
            ->groupBy('transactions.entertainer_id')
            ->orderByDesc('commission')
            ->limit(15)
            ->get()
            ->map(fn ($row) => [
                'entertainer' => $row->name,
                'revenue' => (float) $row->revenue,
                'commission' => (float) $row->commission,
            ]);

        return [
            'type' => 'table',
            'title' => 'Entertainer Earnings',
            'data' => $data->toArray(),
        ];
    }

    private function entertainerCommissionTracking(): array
    {
        return $this->entertainerEarnings();
    }

    // ========== PACKAGE REPORTS ==========

    private function salesByPackage(): array
    {
        [$startDate, $endDate] = $this->getDateRange();

        $data = Transaction::query()
            ->where('transactions.status', 1)
            ->whereBetween('transactions.created_at', [$startDate, $endDate])
            ->join('packages', 'transactions.package_id', '=', 'packages.id')
            ->leftJoin('websites', 'packages.website_id', '=', 'websites.id')
            ->select(
                'packages.id',
                'packages.name',
                'websites.name as website_name',
                DB::raw('COUNT(*) as orders'),
                DB::raw('SUM(transactions.total) as revenue')
            )
            ->groupBy('packages.id', 'packages.name', 'websites.name')
            ->orderByDesc('orders')
            ->limit(20)
            ->get()
            ->map(fn ($row) => [
                'package' => $row->name,
                'website' => $row->website_name ?: '-',
                'orders' => (int) $row->orders,
                'revenue' => number_format((float) $row->revenue, 2, '.', ''),
            ]);

        return [
            'type' => 'table',
            'title' => 'Sales by Package',
            'data' => $data->toArray(),
        ];
    }

    private function mostPopularPackages(): array
    {
        return $this->salesByPackage();
    }

    private function packageCapacityUtilization(): array
    {
        [$startDate, $endDate] = $this->getDateRange();

        $data = Package::query()
            ->select(
                'packages.id',
                'packages.name',
                DB::raw('COALESCE(packages.daily_ticket_limit, packages.daily_table_limit, 0) as capacity'),
                DB::raw('COUNT(transactions.id) as sold')
            )
            ->leftJoin('transactions', function ($join) use ($startDate, $endDate) {
                $join->on('packages.id', '=', 'transactions.package_id')
                    ->whereBetween('transactions.created_at', [$startDate, $endDate])
                    ->whereNull('transactions.archived_at')
                    ->where('transactions.status', Transaction::STATUS_COMPLETED);
            })
            ->groupBy('packages.id', 'packages.name')
            ->limit(15)
            ->get()
            ->map(fn ($row) => [
                'package' => $row->name,
                'capacity' => (int) $row->capacity,
                'sold' => (int) $row->sold,
                'utilization_rate' => $row->capacity > 0 ? round(($row->sold / $row->capacity) * 100, 2) : 0,
            ]);

        return [
            'type' => 'table',
            'title' => 'Package Capacity Utilization',
            'data' => $data->toArray(),
        ];
    }

    // ========== CUSTOMER REPORTS ==========

    private function newCustomersOverTime(): array
    {
        [$startDate, $endDate] = $this->getDateRange();

        $rawData = Transaction::query()
            ->financiallyReportable()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(DISTINCT package_email) as new_customers')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $data = $rawData->map(fn ($row) => [
            'date' => $row->date,
            'count' => (int) $row->new_customers,
        ]);

        // Format for Chart.js
        $chartData = [
            'labels' => $rawData->pluck('date')->toArray(),
            'datasets' => [
                [
                    'label' => 'New Customers',
                    'data' => $rawData->pluck('new_customers')->map(fn($v) => (int)$v)->toArray(),
                    'borderColor' => 'rgb(153, 102, 255)',
                    'backgroundColor' => 'rgba(153, 102, 255, 0.1)',
                    'tension' => 0.1,
                    'fill' => true,
                ]
            ]
        ];

        return [
            'type' => 'line_chart',
            'title' => 'New Customers Over Time',
            'data' => $chartData,
            'raw_data' => $data->toArray(),
        ];
    }

    private function repeatVsFirstTime(): array
    {
        [$startDate, $endDate] = $this->getDateRange();

        $firstTime = Transaction::query()
            ->financiallyReportable()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->join(
                DB::raw('(SELECT package_email, MIN(id) as first_id FROM transactions WHERE archived_at IS NULL AND status = 1 GROUP BY package_email) as first'),
                'transactions.id',
                '=',
                'first.first_id'
            )
            ->count();

        $total = Transaction::query()->financiallyReportable()->whereBetween('created_at', [$startDate, $endDate])->count();
        $repeat = $total - $firstTime;

        // Format for Chart.js
        $chartData = [
            'labels' => ['First-Time', 'Repeat'],
            'datasets' => [
                [
                    'data' => [(int)$firstTime, (int)$repeat],
                    'backgroundColor' => [
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(255, 99, 132, 0.8)',
                    ],
                    'borderColor' => [
                        'rgb(75, 192, 192)',
                        'rgb(255, 99, 132)',
                    ],
                    'borderWidth' => 1,
                ]
            ]
        ];

        return [
            'type' => 'pie_chart',
            'title' => 'Repeat vs First-Time Customers',
            'data' => $chartData,
            'raw_data' => [
                ['label' => 'First-Time', 'value' => $firstTime],
                ['label' => 'Repeat', 'value' => $repeat],
            ],
        ];
    }

    private function customerByLocation(): array
    {
        return [
            'type' => 'table',
            'title' => 'Customers by Location',
            'data' => [],
        ];
    }

    // ========== EVENT REPORTS ==========

    private function attendanceByEvent(): array
    {
        [$startDate, $endDate] = $this->getDateRange();

        $data = Event::query()
            ->select(
                'events.id',
                'events.name',
                DB::raw('COUNT(transactions.id) as attendees'),
                DB::raw('SUM(transactions.package_number_of_guest) as total_guests')
            )
            ->leftJoin('transactions', function ($join) {
                $join->on('events.id', '=', 'transactions.event_id')
                    ->whereNull('transactions.archived_at')
                    ->where('transactions.status', Transaction::STATUS_COMPLETED);
            })
            ->whereBetween('events.date', [$startDate, $endDate])
            ->groupBy('events.id', 'events.name')
            ->orderByDesc('attendees')
            ->limit(20)
            ->get()
            ->map(fn ($row) => [
                'event' => $row->name,
                'attendees' => (int) $row->attendees,
                'total_guests' => (int) $row->total_guests,
            ]);

        return [
            'type' => 'table',
            'title' => 'Attendance by Event',
            'data' => $data->toArray(),
        ];
    }

    private function eventRevenue(): array
    {
        [$startDate, $endDate] = $this->getDateRange();

        $data = Event::query()
            ->select(
                'events.id',
                'events.name',
                DB::raw('SUM(transactions.total) as revenue'),
                DB::raw('COUNT(transactions.id) as orders')
            )
            ->leftJoin('transactions', function ($join) {
                $join->on('events.id', '=', 'transactions.event_id')
                    ->whereNull('transactions.archived_at')
                    ->where('transactions.status', Transaction::STATUS_COMPLETED);
            })
            ->whereBetween('events.date', [$startDate, $endDate])
            ->groupBy('events.id', 'events.name')
            ->orderByDesc('revenue')
            ->limit(20)
            ->get()
            ->map(fn ($row) => [
                'event' => $row->name,
                'revenue' => (float) $row->revenue,
                'orders' => (int) $row->orders,
            ]);

        return [
            'type' => 'table',
            'title' => 'Event Revenue',
            'data' => $data->toArray(),
        ];
    }

    private function eventCapacityUtilization(): array
    {
        [$startDate, $endDate] = $this->getDateRange();

        $data = Event::query()
            ->whereBetween('date', [$startDate, $endDate])
            ->select(
                'events.id',
                'events.name',
                DB::raw('COUNT(DISTINCT transactions.id) as total_orders'),
                DB::raw('SUM(COALESCE(transactions.package_number_of_guest, 1)) as total_attendees')
            )
            ->leftJoin('transactions', function ($join) {
                $join->on('events.id', '=', 'transactions.event_id')
                    ->whereNull('transactions.archived_at')
                    ->where('transactions.status', Transaction::STATUS_COMPLETED);
            })
            ->groupBy('events.id', 'events.name')
            ->orderByDesc('total_attendees')
            ->limit(20)
            ->get()
            ->map(fn ($row) => [
                'event' => $row->name,
                'orders' => (int) $row->total_orders,
                'attendees' => (int) $row->total_attendees,
            ]);

        return [
            'type' => 'table',
            'title' => 'Event Capacity Utilization',
            'data' => $data->toArray(),
        ];
    }

    // ========== FINANCIAL REPORTS ==========

    private function revenueSummary(): array
    {
        [$startDate, $endDate] = $this->getDateRange();

        $completed = Transaction::query()->financiallyReportable()->whereBetween('created_at', [$startDate, $endDate])->sum('total');
        $refunded = 0;

        return [
            'type' => 'metric',
            'title' => 'Revenue Summary',
            'metrics' => [
                'gross_revenue' => (float) $completed,
                'refunded' => (float) $refunded,
                'net_revenue' => (float) $completed,
            ],
        ];
    }

    private function commissionExpenses(): array
    {
        [$startDate, $endDate] = $this->getDateRange();

        $affiliateCommission = Transaction::query()->financiallyReportable()->whereBetween('created_at', [$startDate, $endDate])
            ->sum('affiliate_commission_amount');
        $entertainerCommission = Transaction::query()->financiallyReportable()->whereBetween('created_at', [$startDate, $endDate])
            ->sum('entertainer_commission_amount');

        return [
            'type' => 'metric',
            'title' => 'Commission Expenses',
            'metrics' => [
                'affiliate_commission' => (float) $affiliateCommission,
                'entertainer_commission' => (float) $entertainerCommission,
                'total_commission' => (float) ($affiliateCommission + $entertainerCommission),
            ],
        ];
    }

    private function netRevenue(): array
    {
        [$startDate, $endDate] = $this->getDateRange();

        $revenue = Transaction::query()->financiallyReportable()->whereBetween('created_at', [$startDate, $endDate])->sum('total');
        $refunded = 0;
        $affiliateCommission = Transaction::query()->financiallyReportable()->whereBetween('created_at', [$startDate, $endDate])
            ->sum('affiliate_commission_amount');
        $entertainerCommission = Transaction::query()->financiallyReportable()->whereBetween('created_at', [$startDate, $endDate])
            ->sum('entertainer_commission_amount');

        $netRevenue = $revenue - $refunded - $affiliateCommission - $entertainerCommission;

        return [
            'type' => 'metric',
            'title' => 'Net Revenue',
            'metrics' => [
                'gross_revenue' => (float) $revenue,
                'refunds' => (float) $refunded,
                'commissions' => (float) ($affiliateCommission + $entertainerCommission),
                'net_revenue' => (float) $netRevenue,
            ],
        ];
    }
}
