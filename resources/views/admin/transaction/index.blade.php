@extends('admin.main')

@section('content')

<style>
/* ─── Transaction Dashboard ──────────────────────────────────────────── */
.txn-date-range-wrap {
    display: flex; align-items: center;
    background: rgba(255,255,255,0.07);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 10px; padding: 7px 14px; cursor: pointer;
}
.txn-date-input {
    background: transparent; border: none; color: #fff;
    font-size: 0.85rem; outline: none; width: 180px; cursor: pointer;
}
.txn-date-input::placeholder { color: rgba(255,255,255,0.4); }
.txn-filters-btn, .txn-export-btn {
    background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.1);
    color: #fff; border-radius: 10px; font-size: 0.85rem; padding: 7px 16px; transition: background 0.2s;
}
.txn-filters-btn:hover, .txn-export-btn:hover { background: rgba(255,255,255,0.13); color: #fff; }
.txn-export-btn::after { display: none !important; }
/* Stat Cards */
.txn-stat-card {
    background: linear-gradient(145deg, rgba(255,255,255,0.05), rgba(255,255,255,0.02));
    border: 1px solid rgba(255,255,255,0.08); border-radius: 16px;
    padding: 20px; display: flex; align-items: center; gap: 16px;
    transition: transform 0.2s, box-shadow 0.2s;
}
.txn-stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(0,0,0,0.3); }
.txn-stat-icon {
    width: 52px; height: 52px; border-radius: 14px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center; font-size: 1.3rem;
}
.txn-stat-label { font-size: 0.68rem; font-weight: 700; letter-spacing: 0.08em; color: rgba(255,255,255,0.45); text-transform: uppercase; margin-bottom: 4px; }
.txn-stat-value { font-size: 1.7rem; font-weight: 800; color: #fff; line-height: 1.1; margin-bottom: 6px; }
.txn-stat-trend { font-size: 0.75rem; font-weight: 600; }
.txn-stat-trend span { color: rgba(255,255,255,0.4); font-weight: 400; }
.txn-stat-note { font-size: 0.75rem; color: rgba(255,255,255,0.4); }
.trend-up { color: #10b981; }
.trend-down { color: #ef4444; }
/* Charts */
.txn-chart-card {
    background: linear-gradient(145deg, rgba(255,255,255,0.05), rgba(255,255,255,0.02));
    border: 1px solid rgba(255,255,255,0.08); border-radius: 16px; padding: 22px;
}
.txn-chart-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; }
.txn-period-select {
    background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.12);
    color: #fff; border-radius: 8px; font-size: 0.8rem; padding: 5px 10px; outline: none;
}
.txn-period-select option { background: #1e293b; }
.txn-chart-legend { display: flex; align-items: center; gap: 6px; font-size: 0.78rem; color: rgba(255,255,255,0.55); }
.txn-chart-legend span { width: 10px; height: 10px; border-radius: 50%; display: inline-block; }
.txn-pkg-legend-item { display: flex; align-items: center; padding: 6px 0; border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 0.8rem; }
.txn-pkg-legend-item:last-child { border-bottom: none; }
.pkg-dot { width: 10px; height: 10px; border-radius: 50%; margin-right: 8px; flex-shrink: 0; }
.pkg-name { color: rgba(255,255,255,0.8); flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.pkg-pct { color: rgba(255,255,255,0.45); margin-right: 10px; min-width: 38px; text-align: right; }
.pkg-amt { color: #fff; font-weight: 600; min-width: 85px; text-align: right; }
/* Table */
.txn-table-card {
    background: linear-gradient(145deg, rgba(255,255,255,0.05), rgba(255,255,255,0.02));
    border: 1px solid rgba(255,255,255,0.08); border-radius: 16px; padding: 22px;
}
.txn-search-wrap { position: relative; }
.txn-search-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: rgba(255,255,255,0.3); font-size: 0.8rem; }
.txn-search-input {
    background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1);
    border-radius: 10px; color: #fff; font-size: 0.85rem;
    padding: 8px 14px 8px 34px; outline: none; width: 300px; transition: border-color 0.2s;
}
.txn-search-input:focus { border-color: rgba(124,58,237,0.6); }
.txn-search-input::placeholder { color: rgba(255,255,255,0.3); }
.txn-filter-select {
    width: 100%; background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.1); color: #fff;
    border-radius: 10px; font-size: 0.82rem; padding: 7px 12px; outline: none;
}
.txn-filter-select option { background: #1e293b; }
.txn-table { border-collapse: separate; border-spacing: 0; width: 100%; }
.txn-table thead th {
    font-size: 0.68rem; font-weight: 700; letter-spacing: 0.07em;
    color: rgba(255,255,255,0.35); text-transform: uppercase;
    padding: 10px 12px; border-bottom: 1px solid rgba(255,255,255,0.07);
    background: transparent; white-space: nowrap;
}
.txn-table tbody tr { border-bottom: 1px solid rgba(255,255,255,0.04); transition: background 0.15s; }
.txn-table tbody tr.odd  { background: transparent; }
.txn-table tbody tr.even { background: rgba(255,255,255,0.015); }
.txn-table tbody tr:hover { background: rgba(255,255,255,0.05) !important; }
.txn-table tbody td { padding: 12px 12px; vertical-align: middle; }
.txn-table tfoot th { padding: 10px 12px; border-top: 1px solid rgba(255,255,255,0.08); }
.txn-order-id { font-weight: 700; color: rgba(255,255,255,0.9); font-size: 0.85rem; }
.txn-venue { font-size: 0.82rem; font-weight: 600; color: rgba(255,255,255,0.9); }
.txn-pkg-type { font-size: 0.75rem; color: rgba(255,255,255,0.4); }
.txn-customer-name { font-size: 0.82rem; font-weight: 600; color: rgba(255,255,255,0.9); }
.txn-customer-email { font-size: 0.75rem; color: rgba(255,255,255,0.4); }
.txn-amount { font-weight: 700; color: #fff; font-size: 0.9rem; }
.txn-commission { font-weight: 600; color: rgba(255,255,255,0.75); font-size: 0.85rem; }
.txn-date-main { font-size: 0.82rem; color: rgba(255,255,255,0.85); }
.txn-date-time { font-size: 0.75rem; color: rgba(255,255,255,0.4); }
.badge-direct { background: rgba(107,114,128,0.2); color: #9ca3af; border: 1px solid rgba(107,114,128,0.3); font-size: 0.7rem; font-weight: 700; letter-spacing: 0.05em; padding: 3px 8px; border-radius: 6px; }
.badge-affiliate { background: rgba(124,58,237,0.15); color: #a78bfa; border: 1px solid rgba(124,58,237,0.25); font-size: 0.75rem; padding: 3px 8px; border-radius: 6px; max-width: 130px; display: inline-block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.badge-completed { background: rgba(16,185,129,0.15); color: #34d399; border: 1px solid rgba(16,185,129,0.25); font-size: 0.72rem; font-weight: 700; letter-spacing: 0.04em; padding: 4px 10px; border-radius: 20px; }
.badge-canceled  { background: rgba(239,68,68,0.15);  color: #f87171; border: 1px solid rgba(239,68,68,0.25);  font-size: 0.72rem; font-weight: 700; padding: 4px 10px; border-radius: 20px; }
.badge-refunded  { background: rgba(245,158,11,0.15); color: #fbbf24; border: 1px solid rgba(245,158,11,0.25); font-size: 0.72rem; font-weight: 700; padding: 4px 10px; border-radius: 20px; }
.badge-checkin-yes { background: rgba(16,185,129,0.15); color: #34d399; border: 1px solid rgba(16,185,129,0.2); font-size: 0.7rem; padding: 3px 9px; border-radius: 6px; }
.badge-checkin-no  { background: rgba(107,114,128,0.15); color: #9ca3af; border: 1px solid rgba(107,114,128,0.2); font-size: 0.7rem; padding: 3px 9px; border-radius: 6px; }
.txn-action-eye { background: rgba(99,102,241,0.12); border: 1px solid rgba(99,102,241,0.2); color: #818cf8; border-radius: 8px; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.82rem; transition: background 0.2s; cursor: pointer; }
.txn-action-eye:hover { background: rgba(99,102,241,0.28); color: #a5b4fc; }
.txn-action-more { background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); color: rgba(255,255,255,0.55); border-radius: 8px; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.82rem; transition: background 0.2s; }
.txn-action-more:hover { background: rgba(255,255,255,0.12); color: #fff; }
/* DataTable overrides */
.dataTables_wrapper .dataTables_paginate .paginate_button { color: rgba(255,255,255,0.55) !important; border-radius: 6px !important; border: 1px solid transparent !important; padding: 4px 9px !important; font-size: 0.82rem !important; }
.dataTables_wrapper .dataTables_paginate .paginate_button.current { background: rgba(124,58,237,0.3) !important; color: #fff !important; border-color: rgba(124,58,237,0.4) !important; }
.dataTables_wrapper .dataTables_paginate .paginate_button:hover { background: rgba(255,255,255,0.08) !important; color: #fff !important; }
.dataTables_wrapper .dataTables_info { color: rgba(255,255,255,0.4) !important; font-size: 0.8rem; }
.dataTables_wrapper .dataTables_length select { background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.1); color: #fff; border-radius: 6px; padding: 3px 6px; }
.dataTables_wrapper .dataTables_length label { color: rgba(255,255,255,0.45); font-size: 0.8rem; }
.dataTables_wrapper .dataTables_paginate { padding-top: 14px; }
.dt-buttons, .dataTables_filter { display: none !important; }
#viewTransactionModal .modal-header { background: #0f172a; border-bottom: 1px solid #1e293b; }
#viewTransactionModal .modal-content,
#viewTransactionModal .modal-body { background: #0f172a; }
#viewTransactionModal .modal-footer { background: #0f172a; border-top: 1px solid #1e293b; }
#viewTransactionModal .modal-title { color: #f8fafc !important; }
#viewTransactionModal .btn-close { filter: invert(1) grayscale(100%); }
#viewTransactionModal .list-group-item {
    background: #0f172a;
    border-color: #1e293b;
    color: #f8fafc !important;
}
#viewTransactionModal .list-group-item strong,
#viewTransactionModal .list-group-item span,
#viewTransactionModal .list-group-item a,
#viewTransactionModal #transaction-modal-content,
#viewTransactionModal #transaction-modal-content * {
    color: #f8fafc !important;
}

/* Prevent mobile admin menu toggle from covering modal close button */
body.modal-open .admin-mobile-menu-toggle {
    opacity: 0;
    pointer-events: none;
}

@media (max-width: 1199.98px) {
    #viewTransactionModal .modal-header .btn-close {
        position: relative;
        z-index: 2;
        margin-right: 8px;
    }
}
</style>
    <!-- Content wrapper -->
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y pt-4">

        @php
            $tz = 'America/Los_Angeles';
            $now = now()->timezone($tz);
            $isPayoutPage = (bool) ($isPayoutPage ?? false);
            $weekStart     = $now->copy()->startOfWeek();
            $prevWeekStart = $weekStart->copy()->subWeek();
            $prevWeekEnd   = $prevWeekStart->copy()->endOfWeek();

            $thisWeekData = $data->filter(fn($t) => $t->created_at->timezone($tz)->between($weekStart, $now));
            $prevWeekData = $data->filter(fn($t) => $t->created_at->timezone($tz)->between($prevWeekStart, $prevWeekEnd));

            $totalTxns         = $data->count();
            $completedTxns     = $data->where('status', 1)->count();
            $totalRevenue      = (float) $data->sum('total');
            $pendingCommission = $data->filter(fn($t) =>
                ($t->affiliate_commission_status === 'pending') ||
                ($t->entertainer_commission_status === 'pending')
            )->sum(fn($t) => (float)($t->affiliate_commission_amount ?? 0) + (float)($t->entertainer_commission_amount ?? 0));

            $pendingPayoutAmount = $data->sum(function ($t) use ($now) {
                $amount = 0.0;
                if ($t->affiliate_commission_status === 'pending' && $t->affiliate_commission_hold_until && $t->affiliate_commission_hold_until->gt($now)) {
                    $amount += (float) ($t->affiliate_commission_amount ?? 0);
                }
                if ($t->entertainer_commission_status === 'pending' && $t->entertainer_commission_hold_until && $t->entertainer_commission_hold_until->gt($now)) {
                    $amount += (float) ($t->entertainer_commission_amount ?? 0);
                }
                return $amount;
            });

            $payoutAmount = $data->sum(function ($t) {
                $amount = 0.0;
                if ($t->affiliate_commission_status === 'paid') {
                    $amount += (float) ($t->affiliate_commission_amount ?? 0);
                }
                if ($t->entertainer_commission_status === 'paid') {
                    $amount += (float) ($t->entertainer_commission_amount ?? 0);
                }
                return $amount;
            });

            $totalEarning = $data->sum(function ($t) {
                $amount = 0.0;
                if (($t->affiliate_commission_status ?? null) !== 'reversed') {
                    $amount += (float) ($t->affiliate_commission_amount ?? 0);
                }
                if (($t->entertainer_commission_status ?? null) !== 'reversed') {
                    $amount += (float) ($t->entertainer_commission_amount ?? 0);
                }
                return $amount;
            });

            $twTxns = $thisWeekData->count();
            $pwTxns = $prevWeekData->count();
            $txnTrend = $pwTxns > 0 ? round((($twTxns - $pwTxns) / $pwTxns) * 100, 1) : 0;

            $twCompleted = $thisWeekData->where('status', 1)->count();
            $pwCompleted = $prevWeekData->where('status', 1)->count();
            $completedTrend = $pwCompleted > 0 ? round((($twCompleted - $pwCompleted) / $pwCompleted) * 100, 1) : 0;

            $twRevenue = (float) $thisWeekData->sum('total');
            $pwRevenue = (float) $prevWeekData->sum('total');
            $revenueTrend = $pwRevenue > 0 ? round((($twRevenue - $pwRevenue) / $pwRevenue) * 100, 1) : 0;

            // 30-day chart data
            $chartDays = collect();
            for ($i = 29; $i >= 0; $i--) {
                $dateStr = $now->copy()->subDays($i)->format('Y-m-d');
                $dayData = $data->filter(fn($t) => $t->created_at->timezone($tz)->format('Y-m-d') === $dateStr);
                $chartDays->push([
                    'label'      => $now->copy()->subDays($i)->format('M d'),
                    'revenue'    => (float) $dayData->sum('total'),
                    'completed'  => $dayData->where('status', 1)->count(),
                    'commission' => $dayData->sum(fn($t) => (float)($t->affiliate_commission_amount ?? 0) + (float)($t->entertainer_commission_amount ?? 0)),
                ]);
            }
            $chart14 = $chartDays->slice(16)->values();
            $chart7  = $chartDays->slice(23)->values();

            // Top packages donut
            $allPkgGroups = $data->where('type', 'package')
                ->groupBy('package_table_label')
                ->map(fn($g) => ['name' => ($g->first()->package_table_label ?: 'Unknown'), 'revenue' => (float)$g->sum('total')])
                ->sortByDesc('revenue')->values();
            $top4         = $allPkgGroups->take(4);
            $otherRevenue = (float) $allPkgGroups->slice(4)->sum('revenue');
            $topPackages  = $otherRevenue > 0 ? $top4->push(['name' => 'Other', 'revenue' => $otherRevenue]) : $top4;
            $topPackagesTotal = (float) $topPackages->sum('revenue');

            // Affiliate names for filter
            $referralRows = $data->map(function ($row) {
                if (!empty($row->affiliate_id) && !empty($row->affiliate))
                    return $row->affiliate->display_name ?: optional($row->affiliate->user)->name ?: ('Affiliate #' . $row->affiliate_id);
                if (!empty($row->entertainer_id) && !empty($row->entertainer))
                    return $row->entertainer->display_name ?: optional($row->entertainer->user)->name ?: ('Entertainer #' . $row->entertainer_id);
                return null;
            })->filter()->unique()->values();

            $filterWebsite   = (string) request('website', '');
            $filterType      = (string) request('type', '');
            $filterAffiliate = (string) request('affiliate', '');
            $filterStatus    = (string) request('status', '');
            $filterDateFrom  = (string) request('date_from', '');
            $filterDateTo    = (string) request('date_to', '');

            $initialDateRange = '';
            if ($filterDateFrom !== '' && $filterDateTo !== '') {
                try {
                    $initialDateRange = \Carbon\Carbon::parse($filterDateFrom)->format('m/d/Y')
                        . ' - '
                        . \Carbon\Carbon::parse($filterDateTo)->format('m/d/Y');
                } catch (\Throwable $exception) {
                    $initialDateRange = '';
                }
            }
        @endphp

        {{-- ── HEADER ─────────────────────────────────────────────── --}}
        <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
            <div>
                <h4 class="mb-1 fw-bold text-white">{{ $dashboardTitle ?? 'Transactions Dashboard' }} 📊</h4>
                <p class="mb-0 small" style="color:rgba(255,255,255,0.45)">{{ $dashboardSubtitle ?? "Here's what's happening with your transaction performance." }}</p>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <div class="txn-date-range-wrap" id="txnDateRangeWrap">
                    <i class="fas fa-calendar-alt me-2" style="color:rgba(255,255,255,0.4);font-size:0.85rem"></i>
                    <input type="text" id="txnDateRange" class="txn-date-input" readonly placeholder="All time" value="{{ $initialDateRange }}">
                </div>
            </div>
        </div>

        {{-- ── STAT CARDS ──────────────────────────────────────────── --}}
        <div class="row g-4 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="txn-stat-card">
                    <div class="txn-stat-icon" style="background:rgba(124,58,237,0.15);color:#7c3aed"><i class="fas fa-receipt"></i></div>
                    <div>
                        <div class="txn-stat-label">Total Transactions</div>
                        <div class="txn-stat-value">{{ number_format($totalTxns) }}</div>
                        <div class="txn-stat-trend {{ $txnTrend >= 0 ? 'trend-up' : 'trend-down' }}">
                            <i class="fas fa-arrow-{{ $txnTrend >= 0 ? 'up' : 'down' }} me-1"></i>{{ abs($txnTrend) }}% <span>vs last week</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="txn-stat-card">
                    <div class="txn-stat-icon" style="background:rgba(16,185,129,0.15);color:#10b981"><i class="fas fa-check-circle"></i></div>
                    <div>
                        <div class="txn-stat-label">Completed Transactions</div>
                        <div class="txn-stat-value">{{ number_format($completedTxns) }}</div>
                        <div class="txn-stat-trend {{ $completedTrend >= 0 ? 'trend-up' : 'trend-down' }}">
                            <i class="fas fa-arrow-{{ $completedTrend >= 0 ? 'up' : 'down' }} me-1"></i>{{ abs($completedTrend) }}% <span>vs last week</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="txn-stat-card">
                    <div class="txn-stat-icon" style="background:rgba(245,158,11,0.15);color:#f59e0b"><i class="fas fa-dollar-sign"></i></div>
                    <div>
                        <div class="txn-stat-label">Total Revenue</div>
                        <div class="txn-stat-value">${{ number_format($totalRevenue, 2) }}</div>
                        <div class="txn-stat-trend {{ $revenueTrend >= 0 ? 'trend-up' : 'trend-down' }}">
                            <i class="fas fa-arrow-{{ $revenueTrend >= 0 ? 'up' : 'down' }} me-1"></i>{{ abs($revenueTrend) }}% <span>vs last week</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="txn-stat-card">
                    <div class="txn-stat-icon" style="background:rgba(249,115,22,0.15);color:#f97316"><i class="fas fa-clock"></i></div>
                    <div>
                        <div class="txn-stat-label">Pending Commission</div>
                        <div class="txn-stat-value">${{ number_format($pendingCommission, 2) }}</div>
                        <div class="txn-stat-note">Awaiting hold period</div>
                    </div>
                </div>
            </div>

            @if($isPayoutPage)
            <div class="col-sm-6 col-xl-3">
                <div class="txn-stat-card">
                    <div class="txn-stat-icon" style="background:rgba(245,158,11,0.15);color:#f59e0b"><i class="fas fa-hourglass-half"></i></div>
                    <div>
                        <div class="txn-stat-label">Pending Amount</div>
                        <div class="txn-stat-value">${{ number_format($pendingPayoutAmount, 2) }}</div>
                        <div class="txn-stat-note">Still in hold window</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="txn-stat-card">
                    <div class="txn-stat-icon" style="background:rgba(16,185,129,0.15);color:#10b981"><i class="fas fa-hand-holding-dollar"></i></div>
                    <div>
                        <div class="txn-stat-label">Payout Amount</div>
                        <div class="txn-stat-value">${{ number_format($payoutAmount, 2) }}</div>
                        <div class="txn-stat-note">Completed payouts</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="txn-stat-card">
                    <div class="txn-stat-icon" style="background:rgba(56,189,248,0.15);color:#38bdf8"><i class="fas fa-sack-dollar"></i></div>
                    <div>
                        <div class="txn-stat-label">Total Earning</div>
                        <div class="txn-stat-value">${{ number_format($totalEarning, 2) }}</div>
                        <div class="txn-stat-note">Includes paid + pending</div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- ── CHARTS ───────────────────────────────────────────────── --}}
        <div class="row g-4 mb-4">
            <div class="col-lg-8">
                <div class="txn-chart-card" id="performanceChartCard">
                    <div class="txn-chart-header">
                        <div class="fw-semibold text-white" style="font-size:0.85rem;letter-spacing:0.05em">PERFORMANCE OVER TIME</div>
                        <select class="txn-period-select" id="chartPeriod">
                            <option value="7">By Day (7d)</option>
                            <option value="14">By Day (14d)</option>
                            <option value="30" selected>By Day (30d)</option>
                        </select>
                    </div>
                    <div class="d-flex flex-wrap gap-4 mb-3">
                        <div class="txn-chart-legend"><span style="background:#7c3aed"></span>Revenue</div>
                        <div class="txn-chart-legend"><span style="background:#f59e0b"></span>Commission</div>
                    </div>
                    <canvas id="txnLineChart" style="max-height:220px"></canvas>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="txn-chart-card" id="topPackagesChartCard" style="height:100%">
                    <div class="txn-chart-header">
                        <div class="fw-semibold text-white" style="font-size:0.85rem;letter-spacing:0.05em">TOP PERFORMING PACKAGES</div>
                    </div>
                    <canvas id="txnDonutChart" style="max-height:170px" class="mx-auto d-block mb-3"></canvas>
                    <div id="txnPkgLegend"></div>
                </div>
            </div>
        </div>

        {{-- ── TRANSACTIONS TABLE ──────────────────────────────────── --}}
        <div class="txn-table-card mb-5">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
                <div class="fw-semibold text-white" style="font-size:0.85rem;letter-spacing:0.05em">RECENT TRANSACTIONS</div>
                <div class="d-flex align-items-center gap-2 flex-wrap justify-content-end">
                    <div class="txn-search-wrap">
                        <i class="fas fa-search txn-search-icon"></i>
                        <input type="text" id="txnSearch" class="txn-search-input" placeholder="Search by name, email, order ID…">
                    </div>
                    <div class="dropdown">
                        <button class="txn-export-btn btn dropdown-toggle" data-bs-toggle="dropdown" type="button">
                            <i class="fas fa-download me-2"></i>Export Table
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" style="background:#1e293b;border:1px solid rgba(255,255,255,0.1)">
                            <li><a class="dropdown-item" style="color:rgba(255,255,255,0.7);font-size:0.85rem" id="expCsv"   href="#"><i class="fas fa-file-csv me-2"></i>Export CSV</a></li>
                            <li><a class="dropdown-item" style="color:rgba(255,255,255,0.7);font-size:0.85rem" id="expExcel" href="#"><i class="fas fa-file-excel me-2"></i>Export Excel</a></li>
                            <li><a class="dropdown-item" style="color:rgba(255,255,255,0.7);font-size:0.85rem" id="expPdf"   href="#"><i class="fas fa-file-pdf me-2"></i>Export PDF</a></li>
                            <li><a class="dropdown-item" style="color:rgba(255,255,255,0.7);font-size:0.85rem" id="expPrint" href="#"><i class="fas fa-print me-2"></i>Print</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Filters row (toggled) --}}
            <div class="row g-3 mb-3" id="txnFiltersRow" style="display:flex">
                @if(auth()->user()->isAdmin())
                <div class="col-md-3 col-sm-6">
                    <select id="websiteFilter" class="txn-filter-select">
                        <option value="">All Websites</option>
                        @foreach(\App\Models\Website::all() as $website)
                            <option value="{{ $website->name }}" {{ $filterWebsite === $website->name ? 'selected' : '' }}>{{ $website->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="col-md-3 col-sm-6">
                    <select id="typeFilter" class="txn-filter-select">
                        <option value="">All Types</option>
                        <option value="Package" {{ $filterType === 'Package' ? 'selected' : '' }}>Package</option>
                        <option value="Reservation" {{ $filterType === 'Reservation' ? 'selected' : '' }}>Reservation</option>
                    </select>
                </div>
                <div class="col-md-3 col-sm-6">
                    <select id="affiliateFilter" class="txn-filter-select">
                        <option value="">All Affiliates</option>
                        @foreach($referralRows as $rn)
                            <option value="{{ $rn }}" {{ $filterAffiliate === $rn ? 'selected' : '' }}>{{ $rn }}</option>
                        @endforeach
                        <option value="Direct" {{ $filterAffiliate === 'Direct' ? 'selected' : '' }}>Direct (No Affiliate)</option>
                    </select>
                </div>
                <div class="col-md-3 col-sm-6">
                    <select id="statusFilter" class="txn-filter-select">
                        <option value="">All Statuses</option>
                        <option value="Completed" {{ $filterStatus === 'Completed' ? 'selected' : '' }}>Completed</option>
                        <option value="Canceled" {{ $filterStatus === 'Canceled' ? 'selected' : '' }}>Canceled</option>
                        <option value="Refunded" {{ $filterStatus === 'Refunded' ? 'selected' : '' }}>Refunded</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="txn-table w-100" id="txnDataTable">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAll"></th>
                            <th>Order ID</th>
                            <th>Event / Package</th>
                            <th>Customer</th>
                            <th>Affiliate</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Checked In</th>
                            <th>Commission</th>
                            @if($isPayoutPage)
                            <th style="min-width:130px">Hold Until</th>
                            @endif
                            <th>Date</th>
                            <th>Action</th>
                            <th class="d-none">_website</th>
                            <th class="d-none">_type</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $item)
                        @php
                            $affiliateName = null;
                            if (!empty($item->affiliate_id) && !empty($item->affiliate))
                                $affiliateName = $item->affiliate->display_name ?: optional($item->affiliate->user)->name ?: ('Affiliate #' . $item->affiliate_id);
                            elseif (!empty($item->entertainer_id) && !empty($item->entertainer))
                                $affiliateName = $item->entertainer->display_name ?: optional($item->entertainer->user)->name ?: ('Entertainer #' . $item->entertainer_id);

                            $commission  = (float)($item->affiliate_commission_amount ?? 0) + (float)($item->entertainer_commission_amount ?? 0);
                            $packageName = $item->type === 'package' ? ($item->package_table_label ?: 'Package') : 'Reservation';
                            $venueName   = $item->website->name ?? ($item->event->name ?? 'N/A');

                            $cartItems = is_array($item->cart_items ?? null) ? $item->cart_items : json_decode($item->cart_items ?? '[]', true);
                            $packageDetails = collect($cartItems)->map(function ($ci) {
                                if (!is_array($ci)) {
                                    return null;
                                }

                                $name = trim((string) ($ci['package_name'] ?? $ci['packageName'] ?? $ci['pkgName'] ?? ''));
                                if ($name === '') {
                                    return null;
                                }

                                $quantity = max(1, (int) ($ci['guests'] ?? $ci['quantity'] ?? 1));
                                $packageType = strtolower(trim((string) ($ci['package_type'] ?? $ci['type'] ?? $ci['packageType'] ?? '')));
                                $isTicketPkg = $packageType === 'ticket';

                                if ($isTicketPkg) {
                                    return $name . ($quantity > 1 ? ' x' . $quantity : '');
                                }

                                return $name . ' ' . $quantity . ' ' . ($quantity === 1 ? 'guest' : 'guests');
                            })->filter()->values();

                            $packageDetailsText = $packageDetails->isNotEmpty()
                                ? ($packageDetails->count() > 1 ? $packageDetails->implode(', ') : $packageDetails->first())
                                : $packageName;

                            $addons = collect($cartItems)->flatMap(fn($ci) => $ci['addons'] ?? [])->pluck('name')->filter()->implode(', ');
                            if ($addons === '') {
                                foreach (explode(',', (string)$item->addons) as $av) {
                                    $ao = \App\Models\Addon::find(trim($av));
                                    if ($ao) $addons .= ($addons !== '' ? ', ' : '') . $ao->name;
                                }
                            }
                            $promo_obj = \App\Models\PromoCode::where('id', $item->promo_code)->first();
                            $promo_code_name = $promo_obj ? $promo_obj->name : null;

                            // Payout lifecycle
                            $commStatus = $item->affiliate_commission_status ?? $item->entertainer_commission_status ?? null;
                            $holdUntil  = $item->affiliate_commission_hold_until ?? $item->entertainer_commission_hold_until ?? null;
                            $now        = \Carbon\Carbon::now();
                            $isEligible = $holdUntil && $holdUntil->lte($now);
                        @endphp
                        <tr>
                            <td><input type="checkbox" class="row-check" value="{{ $item->id }}"></td>
                            <td class="txn-order-id">#{{ str_pad($item->id, 3, '0', STR_PAD_LEFT) }}</td>
                            <td>
                                <div class="txn-venue">{{ $venueName }}</div>
                                <div class="txn-pkg-type">
                                    @if($packageDetails->count() > 1)
                                        Multiple packages: {{ $packageDetailsText }}
                                    @else
                                        {{ $packageDetailsText }}
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="txn-customer-name">{{ $item->package_first_name }} {{ $item->package_last_name }}</div>
                                <div class="txn-customer-email">{{ $item->package_email }}</div>
                            </td>
                            <td>
                                @if($affiliateName)
                                    <span class="badge-affiliate" title="{{ $affiliateName }}">{{ $affiliateName }}</span>
                                @else
                                    <span class="badge-direct">DIRECT</span>
                                @endif
                            </td>
                            <td class="txn-amount">${{ number_format((float)$item->total, 2) }}</td>
                            <td>
                                @if($item->status == 1)     <span class="badge-completed">Completed</span>
                                @elseif($item->status == 0) <span class="badge-canceled">Canceled</span>
                                @elseif($item->status == 2) <span class="badge-refunded">Refunded</span>
                                @else                       <span class="badge-canceled">Unknown</span>
                                @endif
                            </td>
                            <td>
                                @if($item->checked_in_status)
                                    <span class="badge-checkin-yes">YES</span>
                                @else
                                    <span class="badge-checkin-no">NO</span>
                                @endif
                            </td>
                            <td class="txn-commission">
                                <div>${{ number_format($commission, 2) }}</div>
                                @if($commStatus === 'pending')
                                    <span class="badge-payout-pending">PENDING</span>
                                @elseif($commStatus === 'approved')
                                    <span class="badge-payout-approved">APPROVED</span>
                                @elseif($commStatus === 'paid')
                                    <span class="badge-payout-paid">PAID</span>
                                @elseif($commStatus === 'reversed')
                                    <span class="badge-payout-reversed">REVERSED</span>
                                @endif
                            </td>
                            @if($isPayoutPage)
                            <td>
                                @if($commission == 0)
                                    <span style="color:rgba(255,255,255,0.25);font-size:0.78rem">N/A</span>
                                @elseif($commStatus === 'paid')
                                    <span class="badge-payout-paid">PAID OUT</span>
                                @elseif($commStatus === 'reversed')
                                    <span class="badge-payout-reversed">REVERSED</span>
                                @elseif($holdUntil && !$isEligible)
                                    <div style="font-size:0.82rem;color:#fbbf24;font-weight:700"><i class="fas fa-lock me-1"></i>{{ $holdUntil->timezone('America/Los_Angeles')->format('M d, Y') }}</div>
                                    <div style="font-size:0.68rem;color:rgba(255,255,255,0.35);margin-top:2px">Not yet eligible</div>
                                @else
                                    <div class="txn-payout-eligible" style="font-size:0.8rem;font-weight:700"><i class="fas fa-check-circle me-1"></i>Eligible now</div>
                                    @if($holdUntil)
                                        <div style="font-size:0.68rem;color:rgba(255,255,255,0.35);margin-top:2px">Hold ended {{ $holdUntil->timezone('America/Los_Angeles')->format('M d, Y') }}</div>
                                    @endif
                                @endif
                            </td>
                            @endif
                            <td>
                                <div class="txn-date-main">{{ optional($item->created_at)->timezone('America/Los_Angeles')->format('M d, Y') }}</div>
                                <div class="txn-date-time">{{ optional($item->created_at)->timezone('America/Los_Angeles')->format('h:i A') }}</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-1">
                                    <button type="button" class="txn-action-eye view-btn"
                                        data-bs-toggle="modal" data-bs-target="#viewTransactionModal"
                                        data-transaction_id="{{ $item->transaction_id ?? 'Free' }}"
                                        data-package_id="{{ $packageDetailsText }}"
                                        data-package_first_name="{{ $item->package_first_name }}"
                                        data-package_last_name="{{ $item->package_last_name }}"
                                        data-package_phone="{{ $item->package_phone }}"
                                        data-package_email="{{ $item->package_email }}"
                                        data-package_dob="{{ $item->package_dob }}"
                                        data-package_note="{{ $item->package_note }}"
                                        data-package_number_of_guest="{{ $item->package_number_of_guest }}"
                                        data-transportation_pickup_time="{{ $item->transportation_pickup_time }}"
                                        data-transportation_address="{{ $item->transportation_address }}"
                                        data-transportation_phone="{{ $item->transportation_phone }}"
                                        data-transportation_guest="{{ $item->transportation_guest }}"
                                        data-transportation_note="{{ $item->transportation_note }}"
                                        data-payment_first_name="{{ $item->payment_first_name }}"
                                        data-payment_last_name="{{ $item->payment_last_name }}"
                                        data-payment_phone="{{ $item->payment_phone }}"
                                        data-payment_email="{{ $item->payment_email }}"
                                        data-payment_address="{{ $item->payment_address }}"
                                        data-payment_city="{{ $item->payment_city }}"
                                        data-payment_state="{{ $item->payment_state }}"
                                        data-payment_country="{{ $item->payment_country }}"
                                        data-payment_dob="{{ $item->payment_dob }}"
                                        data-payment_zip_code="{{ $item->payment_zip_code }}"
                                        data-type="{{ $item->type }}"
                                        data-status="{{ $item->status }}"
                                        data-ip_address="{{ $item->ip_address }}"
                                        data-website_id="{{ $item->website->name ?? '' }}"
                                        data-event_id="{{ $item->event->name ?? '' }}"
                                        data-addons="{{ $addons }}"
                                        data-business_company="{{ $item->business_company }}"
                                        data-business_vat="{{ $item->business_vat }}"
                                        data-business_address="{{ $item->business_address }}"
                                        data-business_purpose="{{ $item->business_purpose }}"
                                        data-total="{{ $item->total }}"
                                        data-subtotal="{{ $item->actual_total }}"
                                        data-refundable="{{ number_format(($item->actual_total / 100) * ($item->website->refundable_fee ?? 0), 2) }}"
                                        data-gratuity="{{ number_format(($item->actual_total / 100) * ($item->website->gratuity_fee ?? 0), 2) }}"
                                        data-due="{{ $item->actual_total - $item->total }}"
                                        data-promo_code="{{ $promo_code_name }}"
                                        data-discounted_amount="{{ $item->discounted_amount }}"
                                        data-package_use_date="{{ $item->package_use_date }}"
                                        data-date="{{ optional($item->created_at)->timezone('America/Los_Angeles')->format('Y-m-d h:i A T') }}"
                                        data-men="{{ $item->men ?? '' }}"
                                        data-women="{{ $item->women ?? '' }}"
                                        data-affiliate_name="{{ !empty($item->affiliate_id) && !empty($item->affiliate) ? ($item->affiliate->display_name ?: optional($item->affiliate->user)->name ?: ('Affiliate #' . $item->affiliate_id)) : '' }}"
                                        data-entertainer_name="{{ !empty($item->entertainer_id) && !empty($item->entertainer) ? ($item->entertainer->display_name ?: optional($item->entertainer->user)->name ?: ('Entertainer #' . $item->entertainer_id)) : '' }}"
                                        data-affiliate_commission_percentage="{{ (float) ($item->affiliate_commission_percentage ?? 0) }}"
                                        data-affiliate_commission_amount="{{ (float) ($item->affiliate_commission_amount ?? 0) }}"
                                        data-affiliate_commission_status="{{ $item->affiliate_commission_status ?? '' }}"
                                        data-affiliate_commission_hold_until="{{ $item->affiliate_commission_hold_until ? optional($item->affiliate_commission_hold_until)->timezone('America/Los_Angeles')->format('M d, Y h:i A T') : '' }}"
                                        data-entertainer_commission_percentage="{{ (float) ($item->entertainer_commission_percentage ?? 0) }}"
                                        data-entertainer_commission_amount="{{ (float) ($item->entertainer_commission_amount ?? 0) }}"
                                        data-entertainer_commission_status="{{ $item->entertainer_commission_status ?? '' }}"
                                        data-entertainer_commission_hold_until="{{ $item->entertainer_commission_hold_until ? optional($item->entertainer_commission_hold_until)->timezone('America/Los_Angeles')->format('M d, Y h:i A T') : '' }}"
                                        data-total_commission="{{ (float) $commission }}"
                                        title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <div class="dropdown">
                                        <button class="txn-action-more btn p-0" data-bs-toggle="dropdown" type="button" style="border:none;background:none">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end" style="background:#1e293b;border:1px solid rgba(255,255,255,0.1)">
                                            @if($item->type === 'package')
                                            <li><a class="dropdown-item" style="color:rgba(255,255,255,0.7);font-size:0.82rem" href="/admins/transaction/change/{{ $item->id }}/1"><i class="fas fa-check me-2 text-success"></i>Mark Completed</a></li>
                                            <li><a class="dropdown-item" style="color:rgba(255,255,255,0.7);font-size:0.82rem" href="/admins/transaction/change/{{ $item->id }}/0"><i class="fas fa-times me-2 text-danger"></i>Mark Canceled</a></li>
                                            <li><a class="dropdown-item" style="color:rgba(255,255,255,0.7);font-size:0.82rem" href="/admins/transaction/change/{{ $item->id }}/2"><i class="fas fa-undo me-2 text-warning"></i>Mark Refunded</a></li>
                                            @else
                                            <li><a class="dropdown-item" style="color:rgba(255,255,255,0.7);font-size:0.82rem" href="/transaction/{{ $item->id }}/1"><i class="fas fa-check me-2 text-success"></i>Mark Completed</a></li>
                                            <li><a class="dropdown-item" style="color:rgba(255,255,255,0.7);font-size:0.82rem" href="/transaction/{{ $item->id }}/0"><i class="fas fa-times me-2 text-danger"></i>Mark Canceled</a></li>
                                            <li><a class="dropdown-item" style="color:rgba(255,255,255,0.7);font-size:0.82rem" href="/transaction/{{ $item->id }}/2"><i class="fas fa-undo me-2 text-warning"></i>Mark Refunded</a></li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </td>
                            <td class="d-none">{{ $venueName }}</td>
                            <td class="d-none">{{ $packageName }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="13" class="text-center py-5" style="color:rgba(255,255,255,0.3)">
                                <i class="fas fa-inbox fa-2x mb-3 d-block"></i>No transactions found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5" class="text-end" style="color:rgba(255,255,255,0.5);font-size:0.82rem">Total (filtered):</th>
                            <th id="amount-total" style="color:#fff;font-weight:700;font-size:0.9rem"></th>
                            <th colspan="7"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        </div>
    </div>
            <!-- / Content -->



            <!-- View Transaction Modal -->
            <div class="modal fade" id="viewTransactionModal" tabindex="-1" aria-labelledby="viewTransactionModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="viewTransactionModalLabel">Transaction Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="transaction-modal-content">
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="list-group">
                                        <li class="list-group-item"><strong>Transaction ID:</strong> <span id="modal-transaction_id"></span></li>
                                        <li class="list-group-item"><strong>IP Address:</strong> <span id="modal-ip_address"></span></li>
                                        <li class="list-group-item"><strong>Order Items:</strong> <span id="modal-package_id"></span></li>
                                        <li class="list-group-item"><strong>Package Date Of Use:</strong> <span id="modal-package_date_of_use"></span></li>
                                        <li class="list-group-item"><strong>First Name:</strong> <span id="modal-package_first_name"></span></li>
                                        <li class="list-group-item"><strong>Last Name:</strong> <span id="modal-package_last_name"></span></li>
                                        <li class="list-group-item"><strong>Phone:</strong> <span id="modal-package_phone"></span></li>
                                        <li class="list-group-item"><strong>Email:</strong> <span id="modal-package_email"></span></li>
                                        <li class="list-group-item"><strong>DOB:</strong> <span id="modal-package_dob"></span></li>
                                        <li class="list-group-item"><strong>Note:</strong> <span id="modal-package_note"></span></li>
                                        <li class="list-group-item"><strong>Number of Guests:</strong> <span id="modal-package_number_of_guest"></span></li>
                                        <li class="list-group-item"><strong>Male Guests:</strong> <span id="modal-package_men_guest"></span></li>
                                        <li class="list-group-item"><strong>Female Guests:</strong> <span id="modal-package_women_guest"></span></li>
                                        <li class="list-group-item"><strong>Transportation Pickup Time:</strong> <span id="modal-transportation_pickup_time"></span></li>
                                        <li class="list-group-item"><strong>Transportation Address:</strong> <span id="modal-transportation_address"></span></li>
                                        <li class="list-group-item"><strong>Transportation Phone:</strong> <span id="modal-transportation_phone"></span></li>
                                        <li class="list-group-item"><strong>Transportation Guest:</strong> <span id="modal-transportation_guest"></span></li>
                                        <li class="list-group-item"><strong>Transportation Note:</strong> <span id="modal-transportation_note"></span></li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="list-group">
                                        <li class="list-group-item"><strong>Payment First Name:</strong> <span id="modal-payment_first_name"></span></li>
                                        <li class="list-group-item"><strong>Payment Last Name:</strong> <span id="modal-payment_last_name"></span></li>
                                        <li class="list-group-item"><strong>Payment Phone:</strong> <span id="modal-payment_phone"></span></li>
                                        <li class="list-group-item"><strong>Payment Email:</strong> <span id="modal-payment_email"></span></li>
                                        <li class="list-group-item"><strong>Payment Address:</strong> <span id="modal-payment_address"></span></li>
                                        <li class="list-group-item"><strong>Payment City:</strong> <span id="modal-payment_city"></span></li>
                                        <li class="list-group-item"><strong>Payment State:</strong> <span id="modal-payment_state"></span></li>
                                        <li class="list-group-item"><strong>Payment Country:</strong> <span id="modal-payment_country"></span></li>
                                        <li class="list-group-item"><strong>Payment DOB:</strong> <span id="modal-payment_dob"></span></li>
                                        <li class="list-group-item"><strong>Payment Zip Code:</strong> <span id="modal-payment_zip_code"></span></li>
                                        <li class="list-group-item"><strong>Business Company Name:</strong> <span id="modal-business_company"></span></li>
                                        <li class="list-group-item"><strong>Business Vat Number:</strong> <span id="modal-business_vat"></span></li>
                                        <li class="list-group-item"><strong>Business Address:</strong> <span id="modal-business_address"></span></li>
                                        <li class="list-group-item"><strong>Business Purpose:</strong> <span id="modal-business_purpose"></span></li>
                                        <li class="list-group-item"><strong>Type:</strong> <span id="modal-type"></span></li>
                                        <li class="list-group-item"><strong>Status:</strong> <span id="modal-status-badge"></span></li>
                                        <li class="list-group-item"><strong>Website ID:</strong> <span id="modal-website_id"></span></li>
                                        <li class="list-group-item"><strong>Event ID:</strong> <span id="modal-event_id"></span></li>
                                        <li class="list-group-item"><strong>Add-ons:</strong> <span id="modal-addons"></span></li>
                                        <li class="list-group-item"><strong>Promo Code:</strong> <span id="modal-promo_code"></span></li>
                                        <li class="list-group-item"><strong>Discounted Amount:</strong> <span id="modal-discounted_amount"></span></li>
                                        <li class="list-group-item"><strong>Total Amount:</strong> <span id="modal-sub_total"></span></li>
                                        <li class="list-group-item"><strong>Gratuity:</strong> <span id="modal-gratuity"></span></li>
                                        <li class="list-group-item"><strong>Non refundable deposit:</strong> <span id="modal-refundable"></span></li>
                                        <li class="list-group-item"><strong>Total Amount Paid:</strong> <span id="modal-total"></span></li>
                                        <li class="list-group-item"><strong>Total Due:</strong> <span id="modal-total_due"></span></li>
                                        <li class="list-group-item"><strong>Total Commission:</strong> <span id="modal-total_commission"></span></li>
                                        <li class="list-group-item"><strong>Commission Source:</strong> <span id="modal-commission_source"></span></li>
                                        <li class="list-group-item" id="modal-affiliate-commission-row"><strong>Affiliate Commission:</strong> <span id="modal-affiliate_commission"></span></li>
                                        <li class="list-group-item" id="modal-entertainer-commission-row"><strong>Entertainer Commission:</strong> <span id="modal-entertainer_commission"></span></li>
                                        <li class="list-group-item"><strong>Date (Pacific Time):</strong> <span id="modal-date"></span></li>
                                        <li class="list-group-item"><strong>Accepted Terms and Conditions:</strong> <span id="modal-terms">Yes</span></li>
                                        <li class="list-group-item"><strong>Accepted SMS:</strong> <span id="modal-sms">Yes</span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="download-transaction-pdf">
                                <i class="fas fa-file-pdf"></i> Download PDF
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endsection

@push('styles')
            <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
            <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
            <style>
            /* ── DateRangePicker dark theme ─────────────────────────────────── */
            .daterangepicker {
                background-color: #1e293b !important;
                border: 1px solid rgba(255,255,255,0.12) !important;
                color: #e2e8f0 !important;
                border-radius: 12px !important;
                box-shadow: 0 8px 32px rgba(0,0,0,0.5) !important;
            }
            .daterangepicker::before, .daterangepicker::after { border-bottom-color: #1e293b !important; }
            .daterangepicker .calendar-table {
                background-color: #1e293b !important;
                border: none !important;
            }
            .daterangepicker .calendar-table th,
            .daterangepicker .calendar-table td { color: #e2e8f0 !important; }
            .daterangepicker td.available:hover,
            .daterangepicker th.available:hover { background-color: rgba(255,204,0,0.15) !important; color: #fff !important; border-radius: 6px !important; }
            /* Keep days from adjacent months readable in dark custom-range mode */
            .daterangepicker td.off,
            .daterangepicker td.off.available,
            .daterangepicker td.off.in-range,
            .daterangepicker td.off.start-date,
            .daterangepicker td.off.end-date {
                color: #94a3b8 !important;
                background-color: rgba(255,255,255,0.03) !important;
            }
            .daterangepicker td.in-range { background-color: rgba(255,204,0,0.12) !important; color: #fff !important; }
            .daterangepicker td.start-date,
            .daterangepicker td.end-date,
            .daterangepicker td.active,
            .daterangepicker td.active:hover { background-color: #ffcc00 !important; color: #1a1400 !important; border-radius: 6px !important; font-weight: 700 !important; }
            .daterangepicker .ranges li {
                background-color: rgba(255,255,255,0.05) !important;
                color: #e2e8f0 !important;
                border-radius: 6px !important;
                margin-bottom: 3px !important;
            }
            .daterangepicker .ranges li:hover,
            .daterangepicker .ranges li.active { background-color: #ffcc00 !important; color: #1a1400 !important; font-weight: 700 !important; }
            .daterangepicker select.monthselect,
            .daterangepicker select.yearselect {
                background-color: #0f1524 !important;
                color: #e2e8f0 !important;
                border: 1px solid rgba(255,255,255,0.15) !important;
                border-radius: 6px !important;
            }
            .daterangepicker .drp-buttons {
                border-top: 1px solid rgba(255,255,255,0.1) !important;
                background: #1e293b !important;
            }
            .daterangepicker .drp-buttons .btn { border-radius: 6px !important; }
            .daterangepicker .drp-buttons .applyBtn { background: #ffcc00 !important; border-color: #ffcc00 !important; color: #1a1400 !important; font-weight: 700 !important; }
            .daterangepicker .drp-buttons .cancelBtn { background: rgba(255,255,255,0.08) !important; border-color: rgba(255,255,255,0.15) !important; color: #e2e8f0 !important; }
            .daterangepicker .drp-calendar .prev span,
            .daterangepicker .drp-calendar .next span { border-color: #e2e8f0 !important; }
            /* ── Payout badge styles ─────────────────────────────────────────── */
            .badge-payout-pending  { background: rgba(245,158,11,0.15); color: #fbbf24; border: 1px solid rgba(245,158,11,0.3); font-size: 0.68rem; font-weight: 700; padding: 2px 7px; border-radius: 6px; letter-spacing: 0.04em; }
            .badge-payout-approved { background: rgba(99,102,241,0.15); color: #818cf8; border: 1px solid rgba(99,102,241,0.3); font-size: 0.68rem; font-weight: 700; padding: 2px 7px; border-radius: 6px; letter-spacing: 0.04em; }
            .badge-payout-paid     { background: rgba(16,185,129,0.15); color: #34d399; border: 1px solid rgba(16,185,129,0.3); font-size: 0.68rem; font-weight: 700; padding: 2px 7px; border-radius: 6px; letter-spacing: 0.04em; }
            .badge-payout-reversed { background: rgba(239,68,68,0.15);  color: #f87171; border: 1px solid rgba(239,68,68,0.3);  font-size: 0.68rem; font-weight: 700; padding: 2px 7px; border-radius: 6px; letter-spacing: 0.04em; }
            .txn-payout-hold { font-size: 0.7rem; color: rgba(255,255,255,0.4); margin-top: 2px; }
            .txn-payout-eligible { font-size: 0.7rem; color: #34d399; margin-top: 2px; }
            </style>
@endpush

@push('scripts')
<!-- DataTables JS -->
            <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
            <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
            <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
            <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
            <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>

            <script>
            // ── Chart.js global defaults ─────────────────────────────────────
            Chart.defaults.color = 'rgba(255,255,255,0.5)';
            Chart.defaults.borderColor = 'rgba(255,255,255,0.06)';

            // ── Sync chart heights ───────────────────────────────────────────
            function syncChartHeights() {
                var perf = document.getElementById('performanceChartCard');
                var top = document.getElementById('topPackagesChartCard');
                if (perf && top) {
                    // Reset heights to auto to get natural height
                    perf.style.height = 'auto';
                    top.style.height = 'auto';
                    // Get computed height of top
                    var topHeight = top.offsetHeight;
                    if (topHeight > 0) {
                        perf.style.height = topHeight + 'px';
                    }
                }
            }
            window.addEventListener('load', syncChartHeights);
            window.addEventListener('resize', syncChartHeights);
            setTimeout(syncChartHeights, 400); // In case of late rendering

            // Custom plugin: show total in donut center
            const donutCenterPlugin = {
                id: 'donutCenter',
                afterDraw(chart) {
                    if (chart.config.type !== 'doughnut') return;
                    const { ctx, chartArea: { left, top, right, bottom } } = chart;
                    const cx = (left + right) / 2, cy = (top + bottom) / 2;
                    const total = chart.config.options._centerTotal || '';
                    ctx.save();
                    ctx.font = 'bold 15px Inter,sans-serif';
                    ctx.fillStyle = '#fff';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(total, cx, cy - 8);
                    ctx.font = '11px Inter,sans-serif';
                    ctx.fillStyle = 'rgba(255,255,255,0.4)';
                    ctx.fillText('Total Rev', cx, cy + 10);
                    ctx.restore();
                }
            };
            Chart.register(donutCenterPlugin);

            // ── PHP → JS data ────────────────────────────────────────────────
            const allChartData = @json($chartDays);
            const chart14Data  = @json($chart14);
            const chart7Data   = @json($chart7);
            const donutLabels  = @json($topPackages->pluck('name'));
            const donutData    = @json($topPackages->pluck('revenue'));
            const donutTotal   = '$' + Number({{ $topPackagesTotal }}).toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 0});

            // ── Line chart ───────────────────────────────────────────────────
            function buildLineChart(chartData) {
                return {
                    type: 'line',
                    data: {
                        labels: chartData.map(d => d.label),
                        datasets: [
                            {
                                label: 'Revenue ($)',
                                data: chartData.map(d => d.revenue),
                                borderColor: '#7c3aed',
                                backgroundColor: 'rgba(124,58,237,0.08)',
                                fill: true,
                                tension: 0.4,
                                pointRadius: 3,
                                pointHoverRadius: 5,
                                yAxisID: 'yRevenue'
                            },
                            {
                                label: 'Commission ($)',
                                data: chartData.map(d => d.commission),
                                borderColor: '#f59e0b',
                                backgroundColor: 'rgba(245,158,11,0.08)',
                                fill: false,
                                tension: 0.4,
                                pointRadius: 3,
                                pointHoverRadius: 5,
                                yAxisID: 'yRevenue'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#1e293b',
                                titleColor: '#fff',
                                bodyColor: 'rgba(255,255,255,0.7)',
                                borderColor: 'rgba(255,255,255,0.1)',
                                borderWidth: 1
                            }
                        },
                        scales: {
                            x: { grid: { color: 'rgba(255,255,255,0.04)' }, ticks: { maxTicksLimit: 10 } },
                            yRevenue: {
                                type: 'linear', position: 'left',
                                grid: { color: 'rgba(255,255,255,0.04)' },
                                ticks: { callback: v => '$' + v.toLocaleString() }
                            }
                        }
                    }
                };
            }

            const lineCtx = document.getElementById('txnLineChart').getContext('2d');
            let lineChart = new Chart(lineCtx, buildLineChart(allChartData));

            document.getElementById('chartPeriod').addEventListener('change', function() {
                const period = this.value;
                const data = period === '7' ? chart7Data : period === '14' ? chart14Data : allChartData;
                lineChart.destroy();
                lineChart = new Chart(lineCtx, buildLineChart(data));
                setTimeout(syncChartHeights, 200);
            });

            // ── Donut chart ──────────────────────────────────────────────────
            const donutColors = ['#7c3aed','#f59e0b','#10b981','#ef4444','#6b7280'];
            const donutCtx = document.getElementById('txnDonutChart').getContext('2d');
            new Chart(donutCtx, {
                type: 'doughnut',
                data: {
                    labels: donutLabels,
                    datasets: [{
                        data: donutData,
                        backgroundColor: donutColors,
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    cutout: '68%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            titleColor: '#fff',
                            bodyColor: 'rgba(255,255,255,0.7)',
                            borderColor: 'rgba(255,255,255,0.1)',
                            borderWidth: 1,
                            callbacks: { label: ctx => ' $' + Number(ctx.parsed).toLocaleString(undefined, {minimumFractionDigits: 2}) }
                        }
                    },
                    _centerTotal: donutTotal
                }
            });

            // ── Package legend ───────────────────────────────────────────────
            (function() {
                const container = document.getElementById('txnPkgLegend');
                const total = {{ $topPackagesTotal ?: 1 }};
                const names = @json($topPackages->pluck('name'));
                const revenues = @json($topPackages->pluck('revenue'));
                names.forEach((name, i) => {
                    const pct = total > 0 ? ((revenues[i] / total) * 100).toFixed(1) : '0.0';
                    const amt = '$' + Number(revenues[i]).toLocaleString(undefined, {minimumFractionDigits: 2});
                    const div = document.createElement('div');
                    div.className = 'txn-pkg-legend-item';
                    div.innerHTML = `
                        <div class="d-flex align-items-center gap-2 flex-grow-1 overflow-hidden">
                            <span class="txn-pkg-dot" style="background:${donutColors[i % donutColors.length]}"></span>
                            <span class="txn-pkg-name text-truncate">${name}</span>
                        </div>
                        <div class="d-flex align-items-center gap-3 ms-2 flex-shrink-0">
                            <span class="txn-pkg-pct">${pct}%</span>
                            <span class="txn-pkg-amt">${amt}</span>
                        </div>`;
                    container.appendChild(div);
                });
            })();
            </script>

            <script>
            $(document).ready(function() {

                // ── DataTable ────────────────────────────────────────────────
                let table = null;
                try {
                    const totalColumns = $('#txnDataTable thead th').length;
                    const hiddenMetaTargets = totalColumns >= 2
                        ? [totalColumns - 2, totalColumns - 1]
                        : [];
                    const actionTarget = totalColumns >= 3 ? totalColumns - 3 : -1;
                    const nonOrderableTargets = [0]
                        .concat(actionTarget >= 0 ? [actionTarget] : [])
                        .concat(hiddenMetaTargets);

                    table = $('#txnDataTable').DataTable({
                        dom: 'rtip',
                        pageLength: 10,
                        columnDefs: [
                            { orderable: false, targets: nonOrderableTargets },
                            { visible: false, targets: hiddenMetaTargets }
                        ],
                        language: {
                            paginate: {
                                previous: '<i class="fas fa-chevron-left"></i>',
                                next: '<i class="fas fa-chevron-right"></i>'
                            }
                        }
                    });
                } catch (error) {
                    console.error('Transaction table init failed:', error);
                }

                // ── Custom search ────────────────────────────────────────────
                $('#txnSearch').on('keyup', function() {
                    if (!table) return;
                    table.search(this.value).draw();
                });

                // Filters always visible, remove toggle logic
                function reloadWithServerFilters() {
                    const params = new URLSearchParams(window.location.search);

                    const setOrDelete = function(key, value) {
                        const normalized = String(value || '').trim();
                        if (normalized) params.set(key, normalized);
                        else params.delete(key);
                    };

                    setOrDelete('website', $('#websiteFilter').val());
                    setOrDelete('type', $('#typeFilter').val());
                    setOrDelete('affiliate', $('#affiliateFilter').val());
                    setOrDelete('status', $('#statusFilter').val());

                    const rangeStr = String($('#txnDateRange').val() || '').trim();
                    if (rangeStr && rangeStr.includes(' - ')) {
                        const parts = rangeStr.split(' - ');
                        const start = moment(parts[0], 'MM/DD/YYYY', true);
                        const end = moment(parts[1], 'MM/DD/YYYY', true);
                        if (start.isValid() && end.isValid()) {
                            params.set('date_from', start.format('YYYY-MM-DD'));
                            params.set('date_to', end.format('YYYY-MM-DD'));
                        } else {
                            params.delete('date_from');
                            params.delete('date_to');
                        }
                    } else {
                        params.delete('date_from');
                        params.delete('date_to');
                    }

                    const query = params.toString();
                    window.location.href = query ? (window.location.pathname + '?' + query) : window.location.pathname;
                }

                // Filters always visible, no toggle needed

                $('#websiteFilter, #typeFilter, #affiliateFilter, #statusFilter').on('change', function() {
                    reloadWithServerFilters();
                });

                $('#txnDateRange').daterangepicker({
                    autoUpdateInput: false,
                    linkedCalendars: false,
                    opens: 'left',
                    locale: { cancelLabel: 'Clear', format: 'MM/DD/YYYY' },
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    }
                });

                $('#txnDateRange').on('apply.daterangepicker', function(ev, picker) {
                    $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
                    reloadWithServerFilters();
                });
                $('#txnDateRange').on('cancel.daterangepicker', function() {
                    $(this).val('');
                    reloadWithServerFilters();
                });

                // ── Export button wiring (custom, reliable across pages) ─────
                const exportColumnIndexes = [1, 2, 3, 4, 5, 6, 7, 8, 9];

                function stripHtml(value) {
                    const tmp = document.createElement('div');
                    tmp.innerHTML = value == null ? '' : String(value);
                    return (tmp.textContent || tmp.innerText || '').replace(/\s+/g, ' ').trim();
                }

                function csvEscape(value) {
                    const safe = String(value ?? '').replace(/"/g, '""');
                    return '"' + safe + '"';
                }

                function getExportDataset() {
                    const selected = $('.row-check:checked');
                    const selectedOnly = selected.length > 0;

                    const headers = exportColumnIndexes.map(function (idx) {
                        return stripHtml($('#txnDataTable thead th').eq(idx).text());
                    });

                    const rows = [];
                    if (table) {
                        table.rows({ search: 'applied' }).every(function () {
                            const rowNode = this.node();
                            if (selectedOnly && !$(rowNode).find('.row-check').prop('checked')) {
                                return;
                            }

                            const rowData = this.data();
                            const row = exportColumnIndexes.map(function (idx) {
                                return stripHtml(rowData[idx]);
                            });
                            rows.push(row);
                        });
                    } else {
                        $('#txnDataTable tbody tr').each(function () {
                            const rowNode = $(this);
                            if (selectedOnly && !rowNode.find('.row-check').prop('checked')) {
                                return;
                            }

                            const row = exportColumnIndexes.map(function (idx) {
                                return stripHtml(rowNode.find('td').eq(idx).html());
                            });
                            rows.push(row);
                        });
                    }

                    return { headers, rows };
                }

                function downloadBlob(filename, content, type) {
                    const blob = new Blob([content], { type: type });
                    const url = URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.href = url;
                    link.download = filename;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    URL.revokeObjectURL(url);
                }

                function exportCsv() {
                    const dataset = getExportDataset();
                    if (!dataset.rows.length) {
                        alert('No rows available to export.');
                        return;
                    }

                    const lines = [];
                    lines.push(dataset.headers.map(csvEscape).join(','));
                    dataset.rows.forEach(function (row) {
                        lines.push(row.map(csvEscape).join(','));
                    });

                    downloadBlob('transactions.csv', lines.join('\n'), 'text/csv;charset=utf-8;');
                }

                function exportExcel() {
                    const dataset = getExportDataset();
                    if (!dataset.rows.length) {
                        alert('No rows available to export.');
                        return;
                    }

                    let tableHtml = '<table><thead><tr>';
                    dataset.headers.forEach(function (h) {
                        tableHtml += '<th>' + h + '</th>';
                    });
                    tableHtml += '</tr></thead><tbody>';

                    dataset.rows.forEach(function (row) {
                        tableHtml += '<tr>';
                        row.forEach(function (cell) {
                            tableHtml += '<td>' + cell + '</td>';
                        });
                        tableHtml += '</tr>';
                    });

                    tableHtml += '</tbody></table>';

                    const excelContent =
                        '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">' +
                        '<head><meta charset="UTF-8"></head><body>' + tableHtml + '</body></html>';

                    downloadBlob('transactions.xls', excelContent, 'application/vnd.ms-excel;charset=utf-8;');
                }

                function exportPdf() {
                    const dataset = getExportDataset();
                    if (!dataset.rows.length) {
                        alert('No rows available to export.');
                        return;
                    }

                    const { jsPDF } = window.jspdf;
                    const doc = new jsPDF({ orientation: 'landscape' });
                    doc.setFontSize(12);
                    doc.text('Transactions', 14, 12);
                    doc.autoTable({
                        head: [dataset.headers],
                        body: dataset.rows,
                        startY: 16,
                        styles: { fontSize: 8 },
                        headStyles: { fillColor: [41, 128, 185] },
                    });
                    doc.save('transactions.pdf');
                }

                function printTable() {
                    const dataset = getExportDataset();
                    if (!dataset.rows.length) {
                        alert('No rows available to print.');
                        return;
                    }

                    let html = '<table border="1" cellspacing="0" cellpadding="6" style="border-collapse:collapse;width:100%;font-family:Arial,sans-serif;font-size:12px;">';
                    html += '<thead><tr>';
                    dataset.headers.forEach(function (h) { html += '<th>' + h + '</th>'; });
                    html += '</tr></thead><tbody>';
                    dataset.rows.forEach(function (row) {
                        html += '<tr>';
                        row.forEach(function (cell) { html += '<td>' + cell + '</td>'; });
                        html += '</tr>';
                    });
                    html += '</tbody></table>';

                    const w = window.open('', '_blank');
                    if (!w) {
                        alert('Please allow popups to print transactions.');
                        return;
                    }

                    w.document.write('<html><head><title>Transactions</title></head><body>' + html + '</body></html>');
                    w.document.close();
                    w.focus();
                    w.print();
                }

                $('#expCsv').on('click', function(e) { e.preventDefault(); exportCsv(); });
                $('#expExcel').on('click', function(e) { e.preventDefault(); exportExcel(); });
                $('#expPdf').on('click', function(e) { e.preventDefault(); exportPdf(); });
                $('#expPrint').on('click', function(e) { e.preventDefault(); printTable(); });

                // ── Select all ───────────────────────────────────────────────
                $('#selectAll').on('change', function() {
                    $('.row-check').prop('checked', this.checked);
                });

                // ── Running total ────────────────────────────────────────────
                function updateTotal() {
                    if (!table) return;
                    let total = 0;
                    table.rows({ search: 'applied' }).every(function() {
                        const cell = this.data()[5];
                        const tmp = document.createElement('div');
                        tmp.innerHTML = cell;
                        const text = (tmp.textContent || tmp.innerText || '').replace(/[^0-9.-]+/g, '');
                        total += parseFloat(text) || 0;
                    });
                    $('#amount-total').text('$' + total.toLocaleString(undefined, { minimumFractionDigits: 2 }));
                }
                if (table) {
                    table.on('draw', updateTotal);
                    updateTotal();
                }

            }); // end document.ready
            </script>

            <script>
            $(document).on('click', '.view-btn', function() {
                $('#modal-package_date_of_use').text($(this).data('package_use_date'));
                $('#modal-promo_code').text($(this).data('promo_code'));
                $('#modal-discounted_amount').text($(this).data('discounted_amount'));
                $('#modal-package_men_guest').text($(this).data('men'));
                $('#modal-package_women_guest').text($(this).data('women'));

                $('#modal-transaction_id').text($(this).data('transaction_id'));
                $('#modal-package_id').text($(this).data('package_id'));
                $('#modal-package_first_name').text($(this).data('package_first_name'));
                $('#modal-package_last_name').text($(this).data('package_last_name'));
                $('#modal-package_phone').text($(this).data('package_phone'));
                $('#modal-package_email').text($(this).data('package_email'));
                $('#modal-package_dob').text($(this).data('package_dob'));
                $('#modal-package_note').text($(this).data('package_note'));
                $('#modal-package_number_of_guest').text($(this).data('package_number_of_guest'));
                $('#modal-transportation_pickup_time').text($(this).data('transportation_pickup_time'));
                $('#modal-transportation_address').text($(this).data('transportation_address'));
                $('#modal-transportation_phone').text($(this).data('transportation_phone'));
                $('#modal-transportation_guest').text($(this).data('transportation_guest'));
                $('#modal-transportation_note').text($(this).data('transportation_note'));
                $('#modal-payment_first_name').text($(this).data('payment_first_name'));
                $('#modal-payment_last_name').text($(this).data('payment_last_name'));
                $('#modal-payment_phone').text($(this).data('payment_phone'));
                $('#modal-payment_email').text($(this).data('payment_email'));
                $('#modal-payment_address').text($(this).data('payment_address'));
                $('#modal-payment_city').text($(this).data('payment_city'));
                $('#modal-payment_state').text($(this).data('payment_state'));
                $('#modal-payment_country').text($(this).data('payment_country'));
                $('#modal-payment_dob').text($(this).data('payment_dob'));
                $('#modal-payment_zip_code').text($(this).data('payment_zip_code'));
                $('#modal-type').text($(this).data('type'));
                var status = $(this).data('status');
                var badge = '';
                if (status == 1 || status === 'Completed' || status === 'Approved') {
                    badge = '<span class="badge bg-success">Completed</span>';
                } else if (status == 0 || status === 'Canceled' || status === '0') {
                    badge = '<span class="badge bg-danger">Canceled</span>';
                } else if (status == 2 || status === 'Refunded') {
                    badge = '<span class="badge bg-warning text-dark">Refunded</span>';
                } else {
                    badge = '<span class="badge bg-secondary">Unknown</span>';
                }
                $('#modal-status-badge').html(badge);
                $('#modal-website_id').text($(this).data('website_id'));
                $('#modal-ip_address').text($(this).data('ip_address'));
                $('#modal-event_id').text($(this).data('event_id'));
                $('#modal-addons').text($(this).data('addons'));
                $('#modal-sub_total').text($(this).data('subtotal'));
                $('#modal-business_company').text($(this).data('business_company'));
                $('#modal-business_vat').text($(this).data('business_vat'));
                $('#modal-business_address').text($(this).data('business_address'));
                $('#modal-business_purpose').text($(this).data('business_purpose'));
                $('#modal-refundable').text($(this).data('refundable'));
                $('#modal-gratuity').text($(this).data('gratuity'));
                $('#modal-total').text($(this).data('total'));
                $('#modal-total_due').text($(this).data('due'));
                $('#modal-date').text($(this).data('date'));

                var affiliateName = String($(this).data('affiliate_name') || '').trim();
                var entertainerName = String($(this).data('entertainer_name') || '').trim();
                var affPct = parseFloat($(this).data('affiliate_commission_percentage')) || 0;
                var affAmt = parseFloat($(this).data('affiliate_commission_amount')) || 0;
                var affStatus = String($(this).data('affiliate_commission_status') || '').trim();
                var affHold = String($(this).data('affiliate_commission_hold_until') || '').trim();
                var entPct = parseFloat($(this).data('entertainer_commission_percentage')) || 0;
                var entAmt = parseFloat($(this).data('entertainer_commission_amount')) || 0;
                var entStatus = String($(this).data('entertainer_commission_status') || '').trim();
                var entHold = String($(this).data('entertainer_commission_hold_until') || '').trim();
                var totalCommission = parseFloat($(this).data('total_commission')) || 0;

                var source = 'Direct';
                if (affiliateName) {
                    source = 'Affiliate - ' + affiliateName;
                } else if (entertainerName) {
                    source = 'Entertainer - ' + entertainerName;
                }

                $('#modal-total_commission').text('$' + totalCommission.toFixed(2));
                $('#modal-commission_source').text(source);

                if (affiliateName || affAmt > 0 || affPct > 0 || affStatus) {
                    var affText = (affiliateName || 'N/A')
                        + ' | ' + affPct.toFixed(2) + '%'
                        + ' | $' + affAmt.toFixed(2)
                        + (affStatus ? (' | ' + affStatus.toUpperCase()) : '')
                        + (affHold ? (' | Hold Until: ' + affHold) : '');
                    $('#modal-affiliate_commission').text(affText);
                    $('#modal-affiliate-commission-row').show();
                } else {
                    $('#modal-affiliate-commission-row').hide();
                }

                if (entertainerName || entAmt > 0 || entPct > 0 || entStatus) {
                    var entText = (entertainerName || 'N/A')
                        + ' | ' + entPct.toFixed(2) + '%'
                        + ' | $' + entAmt.toFixed(2)
                        + (entStatus ? (' | ' + entStatus.toUpperCase()) : '')
                        + (entHold ? (' | Hold Until: ' + entHold) : '');
                    $('#modal-entertainer_commission').text(entText);
                    $('#modal-entertainer-commission-row').show();
                } else {
                    $('#modal-entertainer-commission-row').hide();
                }
            });
            </script>

            <script>
            $(document).on('click', '#download-transaction-pdf', function() {
                var rows = [];
                $('#transaction-modal-content ul.list-group').each(function() {
                    $(this).find('li').each(function() {
                        var label = $(this).find('strong').text().replace(':', '').trim();
                        var value = '';
                        if ($(this).find('span').attr('id') === 'modal-status-badge') {
                            value = $(this).find('span .badge').text().trim();
                        } else {
                            value = $(this).find('span').text().trim();
                        }
                        rows.push([label, value]);
                    });
                });
                var { jsPDF } = window.jspdf;
                var doc = new jsPDF();
                doc.text('Transaction Details', 14, 14);
                doc.autoTable({
                    head: [['Field', 'Value']],
                    body: rows,
                    startY: 20,
                    styles: { fontSize: 10, cellPadding: 2 },
                    headStyles: { fillColor: [41, 128, 185] }
                });
                doc.save('transaction-details.pdf');
            });
            </script>
@endpush
