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
.txn-confirmation-num { font-size: 0.75rem; color: rgba(255,255,255,0.7); max-width: 120px; word-break: break-all; }
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
.badge-reservation-upcoming { background: rgba(59,130,246,0.15); color: #93c5fd; border: 1px solid rgba(59,130,246,0.25); font-size: 0.72rem; font-weight: 700; padding: 4px 10px; border-radius: 20px; }
.badge-reservation-today { background: rgba(245,158,11,0.16); color: #fbbf24; border: 1px solid rgba(245,158,11,0.28); font-size: 0.72rem; font-weight: 700; padding: 4px 10px; border-radius: 20px; }
.badge-reservation-checked-in { background: rgba(16,185,129,0.15); color: #34d399; border: 1px solid rgba(16,185,129,0.25); font-size: 0.72rem; font-weight: 700; padding: 4px 10px; border-radius: 20px; }
.badge-reservation-no-show { background: rgba(249,115,22,0.15); color: #fb923c; border: 1px solid rgba(249,115,22,0.25); font-size: 0.72rem; font-weight: 700; padding: 4px 10px; border-radius: 20px; }
.badge-reservation-refunded { background: rgba(107,114,128,0.18); color: #d1d5db; border: 1px solid rgba(107,114,128,0.28); font-size: 0.72rem; font-weight: 700; padding: 4px 10px; border-radius: 20px; }
.badge-reservation-cancelled { background: rgba(239,68,68,0.18); color: #fca5a5; border: 1px solid rgba(239,68,68,0.28); font-size: 0.72rem; font-weight: 700; padding: 4px 10px; border-radius: 20px; }
.badge-checkin-yes { background: rgba(16,185,129,0.15); color: #34d399; border: 1px solid rgba(16,185,129,0.2); font-size: 0.65rem; padding: 3px 8px; border-radius: 6px; white-space: nowrap; display: inline-block; }
.badge-checkin-no  { background: rgba(107,114,128,0.15); color: #9ca3af; border: 1px solid rgba(107,114,128,0.2); font-size: 0.65rem; padding: 3px 8px; border-radius: 6px; white-space: nowrap; display: inline-block; }
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
.table-responsive { padding-bottom: 20px; }
.dataTables_wrapper .dataTables_paginate { padding-top: 14px; margin-bottom: 0; }
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
        margin-right: 0;
        margin-left: auto;
        width: 2rem !important;
        height: 2rem !important;
        padding: 0.25rem !important;
        opacity: 0.95 !important;
        background-size: 1rem !important;
        background-color: transparent !important;
        border: 0 !important;
        box-shadow: none !important;
        filter: invert(1) !important;
    }

    #viewTransactionModal .modal-header .btn-close:hover,
    #viewTransactionModal .modal-header .btn-close:focus {
        opacity: 1 !important;
        background-color: transparent !important;
    }
}

/* Package Details Button */
.btn-link-package {
    background: linear-gradient(135deg, rgba(124,58,237,0.15) 0%, rgba(99,102,241,0.15) 100%);
    border: 1px solid rgba(124,58,237,0.3);
    color: #818cf8;
    padding: 6px 12px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.85rem;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-block;
    cursor: pointer;
}

.btn-link-package:hover {
    background: linear-gradient(135deg, rgba(124,58,237,0.25) 0%, rgba(99,102,241,0.25) 100%);
    border-color: rgba(124,58,237,0.5);
    color: #a5b4fc;
    transform: translateY(-1px);
}

.btn-link-package:active {
    transform: translateY(0);
}

/* Package Details Modal */
#packageDetailsModal .modal-content {
    background: #111a2e;
    border: 1px solid rgba(255,255,255,0.12);
    color: #f4f6ff;
}

#packageDetailsModal .modal-header {
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

#packageDetailsModal .modal-title {
    color: #f8fafc !important;
    font-weight: 700;
}

#packageDetailsModal .btn-close {
    filter: invert(1) grayscale(100%);
}

#packageDetailsModal .list-group-item {
    background: #0f172a;
    border-color: #1e293b;
    color: #f8fafc !important;
    padding: 12px 16px;
}

#packageDetailsModal .package-item {
    background: rgba(124,58,237,0.1);
    border: 1px solid rgba(124,58,237,0.2);
    border-radius: 8px;
    padding: 12px;
    margin-bottom: 12px;
    color: #e0e7ff;
}

#packageDetailsModal .package-name {
    font-weight: 700;
    color: #a5b4fc;
    margin-bottom: 4px;
}

#packageDetailsModal .addon-item {
    background: rgba(59,130,246,0.1);
    border-left: 3px solid rgba(59,130,246,0.5);
    padding: 8px 12px;
    margin: 8px 0;
    border-radius: 4px;
    font-size: 0.9rem;
    color: #bfdbfe;
}

#viewTransactionModal .txn-detail-card,
#packageDetailsModal .txn-detail-card {
    background: #1e293b;
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 10px;
    padding: 12px;
    margin-bottom: 12px;
}

#viewTransactionModal .txn-hero-card,
#packageDetailsModal .txn-hero-card {
    background: linear-gradient(135deg, rgba(15,23,42,0.98), rgba(30,41,59,0.96));
    border: 1px solid rgba(124,58,237,0.22);
    box-shadow: 0 18px 40px rgba(2,6,23,0.28);
    padding: 18px;
}

#viewTransactionModal .txn-summary-grid,
#packageDetailsModal .txn-summary-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 10px;
    margin-top: 16px;
}

@media (max-width: 992px) {
    #viewTransactionModal .txn-summary-grid,
    #packageDetailsModal .txn-summary-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 576px) {
    #viewTransactionModal .txn-summary-grid,
    #packageDetailsModal .txn-summary-grid {
        grid-template-columns: 1fr;
    }
}

#viewTransactionModal .txn-detail-title,
#packageDetailsModal .txn-detail-title {
    color: #e0e7ff;
    font-weight: 700;
    margin-bottom: 10px;
}

#viewTransactionModal .txn-section-grid,
#packageDetailsModal .txn-section-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
}

@media (max-width: 768px) {
    #viewTransactionModal .txn-section-grid,
    #packageDetailsModal .txn-section-grid {
        grid-template-columns: 1fr;
    }
}

#viewTransactionModal .txn-detail-row,
#packageDetailsModal .txn-detail-row {
    display: flex;
    justify-content: space-between;
    gap: 14px;
    font-size: 0.85rem;
    padding: 5px 0;
    border-bottom: 1px dashed rgba(255,255,255,0.08);
}

#viewTransactionModal .txn-detail-row:last-child,
#packageDetailsModal .txn-detail-row:last-child {
    border-bottom: none;
}

#viewTransactionModal .txn-detail-label,
#packageDetailsModal .txn-detail-label {
    color: #94a3b8;
}

#viewTransactionModal .txn-detail-value,
#packageDetailsModal .txn-detail-value {
    color: #e2e8f0;
    font-weight: 600;
    text-align: right;
}

#packageDetailsModal .txn-detail-card {
    padding: 10px;
    margin-bottom: 8px;
}

#packageDetailsModal .txn-detail-title {
    margin-bottom: 6px;
    font-size: 0.9rem;
}

#packageDetailsModal .txn-detail-row {
    padding: 3px 0;
    font-size: 0.81rem;
}

#viewTransactionModal .txn-status-pill {
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.04em;
}

#viewTransactionModal .txn-status-completed { background: rgba(16,185,129,0.2); color: #34d399; }
#viewTransactionModal .txn-status-canceled { background: rgba(239,68,68,0.2); color: #f87171; }
#viewTransactionModal .txn-status-refunded { background: rgba(245,158,11,0.2); color: #fbbf24; }
#viewTransactionModal .txn-status-unknown { background: rgba(107,114,128,0.2); color: #cbd5e1; }
</style>
    <!-- Content wrapper -->
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y pt-4">

        @php
            $tz = 'America/Los_Angeles';
            $now = now()->timezone($tz);
            $isPayoutPage = (bool) ($isPayoutPage ?? false);
            $canArchiveTransactions = auth()->check()
                && auth()->user()->isAdmin()
                && strtolower(trim((string) (auth()->user()->email ?? ''))) === 'admin@admin.com';
            $isArchivedView = request()->boolean('archived') && $canArchiveTransactions;
            $weekStart     = $now->copy()->startOfWeek();
            $prevWeekStart = $weekStart->copy()->subWeek();
            $prevWeekEnd   = $prevWeekStart->copy()->endOfWeek();

            $reportableData = $data->where('status', 1);

            $guestCountForTransaction = function ($t) {
                $menGuests = (int) ($t->men ?? 0);
                $womenGuests = (int) ($t->women ?? 0);
                if ($menGuests > 0 || $womenGuests > 0) {
                    return max(0, $menGuests + $womenGuests);
                }

                $packageGuests = (int) ($t->package_number_of_guest ?? 0);
                if ($packageGuests > 0) {
                    return $packageGuests;
                }

                return 0;
            };

            $thisWeekData = $reportableData->filter(fn($t) => $t->created_at->timezone($tz)->between($weekStart, $now));
            $prevWeekData = $reportableData->filter(fn($t) => $t->created_at->timezone($tz)->between($prevWeekStart, $prevWeekEnd));

            $totalTxns         = $reportableData->count();
            $completedTxns     = $reportableData->count();
            $totalRevenue      = (float) $reportableData->sum('total');
            $totalGuests       = (int) $reportableData->sum($guestCountForTransaction);
            $pendingCommission = $reportableData->filter(fn($t) =>
                ($t->affiliate_commission_status === 'pending') ||
                ($t->entertainer_commission_status === 'pending')
            )->sum(fn($t) => (float)($t->affiliate_commission_amount ?? 0) + (float)($t->entertainer_commission_amount ?? 0));

            $pendingPayoutAmount = $reportableData->sum(function ($t) use ($now) {
                $amount = 0.0;
                if ($t->affiliate_commission_status === 'pending' && $t->affiliate_commission_hold_until && $t->affiliate_commission_hold_until->gt($now)) {
                    $amount += (float) ($t->affiliate_commission_amount ?? 0);
                }
                if ($t->entertainer_commission_status === 'pending' && $t->entertainer_commission_hold_until && $t->entertainer_commission_hold_until->gt($now)) {
                    $amount += (float) ($t->entertainer_commission_amount ?? 0);
                }
                return $amount;
            });

            $payoutAmount = $reportableData->sum(function ($t) {
                $amount = 0.0;
                if ($t->affiliate_commission_status === 'paid') {
                    $amount += (float) ($t->affiliate_commission_amount ?? 0);
                }
                if ($t->entertainer_commission_status === 'paid') {
                    $amount += (float) ($t->entertainer_commission_amount ?? 0);
                }
                return $amount;
            });

            $totalEarning = $reportableData->sum(function ($t) {
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

            $twCompleted = $thisWeekData->count();
            $pwCompleted = $prevWeekData->count();
            $completedTrend = $pwCompleted > 0 ? round((($twCompleted - $pwCompleted) / $pwCompleted) * 100, 1) : 0;

            $twRevenue = (float) $thisWeekData->sum('total');
            $pwRevenue = (float) $prevWeekData->sum('total');
            $revenueTrend = $pwRevenue > 0 ? round((($twRevenue - $pwRevenue) / $pwRevenue) * 100, 1) : 0;

            // 30-day chart data
            $chartDays = collect();
            for ($i = 29; $i >= 0; $i--) {
                $dateStr = $now->copy()->subDays($i)->format('Y-m-d');
                $dayData = $reportableData->filter(fn($t) => $t->created_at->timezone($tz)->format('Y-m-d') === $dateStr);
                $chartDays->push([
                    'label'      => $now->copy()->subDays($i)->format('M d'),
                    'revenue'    => (float) $dayData->sum('total'),
                    'completed'  => $dayData->count(),
                    'commission' => $dayData->sum(fn($t) => (float)($t->affiliate_commission_amount ?? 0) + (float)($t->entertainer_commission_amount ?? 0)),
                ]);
            }
            $chart14 = $chartDays->slice(16)->values();
            $chart7  = $chartDays->slice(23)->values();

            // Top packages donut
            $allPkgGroups = $reportableData->where('type', 'package')
                ->groupBy('package_table_label')
                ->map(fn($g) => ['name' => ($g->first()->package_table_label ?: 'Unknown'), 'revenue' => (float)$g->sum('total')])
                ->sortByDesc('revenue')->values();
            $top4         = $allPkgGroups->take(4);
            $otherRevenue = (float) $allPkgGroups->slice(4)->sum('revenue');
            $topPackages  = $otherRevenue > 0 ? $top4->push(['name' => 'Other', 'revenue' => $otherRevenue]) : $top4;
            $topPackagesTotal = (float) $topPackages->sum('revenue');

            // affiliate names for filter
            $referralRows = $data->map(function ($row) {
                if (!empty($row->affiliate_id) && !empty($row->affiliate))
                    return $row->affiliate->display_name ?: optional($row->affiliate->user)->name ?: ('affiliate #' . $row->affiliate_id);
                if (!empty($row->entertainer_id) && !empty($row->entertainer))
                    return $row->entertainer->display_name ?: optional($row->entertainer->user)->name ?: ('Entertainer #' . $row->entertainer_id);
                return null;
            })->filter()->unique()->values();

            $filterWebsite   = (string) request('website', '');
            $filterType      = (string) request('type', '');
            $filterAffiliate = (string) request('affiliate', '');
            $filterStatus    = (string) request('status', '');
            $filterReservation = (string) request('reservation', '');
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
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-5 g-4 mb-4">
            <div class="col">
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
            <div class="col">
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
            <div class="col">
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
            <div class="col">
                <div class="txn-stat-card">
                    <div class="txn-stat-icon" style="background:rgba(249,115,22,0.15);color:#f97316"><i class="fas fa-clock"></i></div>
                    <div>
                        <div class="txn-stat-label">Pending Fee</div>
                        <div class="txn-stat-value">${{ number_format($pendingCommission, 2) }}</div>
                        <div class="txn-stat-note">Awaiting hold period</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="txn-stat-card">
                    <div class="txn-stat-icon" style="background:rgba(56,189,248,0.15);color:#38bdf8"><i class="fas fa-users"></i></div>
                    <div>
                        <div class="txn-stat-label">Total Guests</div>
                        <div class="txn-stat-value">{{ number_format($totalGuests) }}</div>
                        <div class="txn-stat-note">Guests in filtered transactions</div>
                    </div>
                </div>
            </div>

            @if($isPayoutPage)
            <div class="col">
                <div class="txn-stat-card">
                    <div class="txn-stat-icon" style="background:rgba(245,158,11,0.15);color:#f59e0b"><i class="fas fa-hourglass-half"></i></div>
                    <div>
                        <div class="txn-stat-label">Pending Amount</div>
                        <div class="txn-stat-value">${{ number_format($pendingPayoutAmount, 2) }}</div>
                        <div class="txn-stat-note">Still in hold window</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="txn-stat-card">
                    <div class="txn-stat-icon" style="background:rgba(16,185,129,0.15);color:#10b981"><i class="fas fa-hand-holding-dollar"></i></div>
                    <div>
                        <div class="txn-stat-label">Payout Amount</div>
                        <div class="txn-stat-value">${{ number_format($payoutAmount, 2) }}</div>
                        <div class="txn-stat-note">Completed payouts</div>
                    </div>
                </div>
            </div>
            <div class="col">
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
                        <div class="txn-chart-legend"><span style="background:#f59e0b"></span>Fee</div>
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
                    @if($canArchiveTransactions)
                    <button type="button" id="selectAllPagesBtn" class="txn-export-btn btn">
                        <i class="fas fa-check-square me-2"></i>Select All Pages
                    </button>
                    <button type="button" id="clearSelectionBtn" class="txn-export-btn btn">
                        <i class="fas fa-square me-2"></i>Clear Selection
                    </button>
                    @if($isArchivedView)
                    <button type="button" id="bulkUnarchiveBtn" class="txn-export-btn btn" style="border-color:rgba(16,185,129,0.35);color:#34d399;">
                        <i class="fas fa-box-open me-2"></i>Unarchive Selected
                    </button>
                    <a href="{{ route('admin.transaction.index') }}" class="txn-export-btn btn" style="text-decoration:none;">
                        <i class="fas fa-list me-2"></i>Back To Active
                    </a>
                    @else
                    <button type="button" id="bulkArchiveBtn" class="txn-export-btn btn" style="border-color:rgba(245,158,11,0.35);color:#fbbf24;">
                        <i class="fas fa-archive me-2"></i>Archive Selected
                    </button>
                    <a href="{{ route('admin.transaction.index', array_merge(request()->except('page'), ['archived' => 1])) }}" class="txn-export-btn btn" style="text-decoration:none;">
                        <i class="fas fa-box-open me-2"></i>View Archived
                    </a>
                    @endif
                    <span id="selectionCount" style="font-size:0.8rem;color:rgba(255,255,255,0.65);">0 selected</span>
                    @endif
                </div>
            </div>

            @if($isArchivedView)
            <div class="mb-3" style="background:rgba(245,158,11,0.12);border:1px solid rgba(245,158,11,0.3);padding:10px 12px;border-radius:10px;color:#fcd34d;font-size:0.85rem;">
                Archived transactions view. Totals and reports elsewhere still exclude these transactions.
            </div>
            @endif

            @if($canArchiveTransactions)
            <form id="bulkArchiveForm" method="POST" action="{{ route('admin.transaction.bulk-archive') }}" class="d-none">
                @csrf
                <div id="bulkArchiveInputs"></div>
            </form>
            <form id="bulkUnarchiveForm" method="POST" action="{{ route('admin.transaction.bulk-unarchive') }}" class="d-none">
                @csrf
                <div id="bulkUnarchiveInputs"></div>
            </form>
            @endif

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
                        <option value="">All affiliates</option>
                        @foreach($referralRows as $rn)
                            <option value="{{ $rn }}" {{ $filterAffiliate === $rn ? 'selected' : '' }}>{{ $rn }}</option>
                        @endforeach
                        @if($filterAffiliate !== '' && $filterAffiliate !== 'Direct' && !$referralRows->contains($filterAffiliate))
                            <option value="{{ $filterAffiliate }}" selected>{{ $filterAffiliate }}</option>
                        @endif
                        <option value="Direct" {{ $filterAffiliate === 'Direct' ? 'selected' : '' }}>Direct (No affiliate)</option>
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
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px;">
                    <select id="reservationFilter" class="form-control" style="background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.1);color:#fff;padding:8px 12px;border-radius:8px;font-size:0.9rem;">
                        <option value="" {{ $filterReservation === '' ? 'selected' : '' }}>All Reservations</option>
                        <option value="upcoming" {{ $filterReservation === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                        <option value="today" {{ $filterReservation === 'today' ? 'selected' : '' }}>Today</option>
                        <option value="past" {{ $filterReservation === 'past' ? 'selected' : '' }}>Past</option>
                        <option value="checked_in" {{ $filterReservation === 'checked_in' ? 'selected' : '' }}>Checked In</option>
                        <option value="no_show" {{ $filterReservation === 'no_show' ? 'selected' : '' }}>No Show</option>
                    </select>
                </div>
            </div>

            <!-- Stat Cards -->
            @php
                $pendingCommission = $reportableData->sum(function($item) {
                    $comm = (float)($item->affiliate_commission_amount ?? 0) + (float)($item->entertainer_commission_amount ?? 0);
                    $status = $item->affiliate_commission_status ?? $item->entertainer_commission_status ?? null;
                    return $status === 'pending' ? $comm : 0;
                });
                $availableNow = $reportableData->sum(function($item) {
                    $comm = (float)($item->affiliate_commission_amount ?? 0) + (float)($item->entertainer_commission_amount ?? 0);
                    $status = $item->affiliate_commission_status ?? $item->entertainer_commission_status ?? null;
                    $holdUntil = $item->affiliate_commission_hold_until ?? $item->entertainer_commission_hold_until ?? null;
                    return ($status === 'approved' || ($holdUntil && $holdUntil->lte(now()))) ? $comm : 0;
                });
                $lifetimeEarned = $reportableData->sum(function($item) {
                    return (float)($item->affiliate_commission_amount ?? 0) + (float)($item->entertainer_commission_amount ?? 0);
                });
            @endphp
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:16px;margin-bottom:24px;">
                <div class="txn-stat-card">
                    <div class="txn-stat-icon" style="background:rgba(249,115,22,0.2);">⏳</div>
                    <div>
                        <div class="txn-stat-label">Pending Fee</div>
                        <div class="txn-stat-value">${{ number_format($pendingCommission, 2) }}</div>
                    </div>
                </div>
                <div class="txn-stat-card">
                    <div class="txn-stat-icon" style="background:rgba(16,185,129,0.2);">✓</div>
                    <div>
                        <div class="txn-stat-label">Available Now</div>
                        <div class="txn-stat-value">${{ number_format($availableNow, 2) }}</div>
                    </div>
                </div>
                <div class="txn-stat-card">
                    <div class="txn-stat-icon" style="background:rgba(59,130,246,0.2);">💰</div>
                    <div>
                        <div class="txn-stat-label">Lifetime Earned</div>
                        <div class="txn-stat-value">${{ number_format($lifetimeEarned, 2) }}</div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="txn-table w-100" id="txnDataTable">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAll"></th>
                            <th>Order ID</th>
                            <th>Sale Date</th>
                            <th>Confirmation #</th>
                            <th>Event / Package</th>
                            <th>Source</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Payment</th>
                            <th>Card Last 4</th>
                            <th>Due Amount</th>
                            <th>Reservation Status</th>
                            <th>Reservation Date</th>
                            <th>Entry Status</th>
                            <th>Fee</th>
                            <th>Action</th>
                            <th class="d-none">_website</th>
                            <th class="d-none">_type</th>
                            <th class="d-none">_promoter</th>
                            <th class="d-none">_fee_available</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $item)
                        @php
                            try {
                                $affiliateName = null;
                                if (!empty($item->affiliate_id) && !empty($item->affiliate))
                                    $affiliateName = $item->affiliate->display_name ?: optional($item->affiliate->user)->name ?: ('affiliate #' . $item->affiliate_id);
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
                                    if ($packageType === '' && !empty($ci['package_id'])) {
                                        $package = \App\Models\Package::find((int) $ci['package_id']);
                                        $packageType = $package ? strtolower(trim((string) ($package->package_type ?? ''))) : '';
                                    }

                                    $isTicketPkg = $packageType === 'ticket';
                                    if ($isTicketPkg) {
                                        return $name . ($quantity > 1 ? ' x' . $quantity : '');
                                    }

                                    return $name . ': ' . $quantity . ' ' . ($quantity === 1 ? 'guest' : 'guests');
                                })->filter()->values();

                                $packageDetailsText = $packageDetails->isNotEmpty()
                                    ? ($packageDetails->count() > 1 ? $packageDetails->implode(', ') : $packageDetails->first())
                                    : $packageName;

                                $packageIds = collect($cartItems)
                                    ->map(fn ($ci) => (int) ($ci['package_id'] ?? 0))
                                    ->filter(fn ($id) => $id > 0)
                                    ->unique()
                                    ->values();

                                $packageRows = $packageIds->isNotEmpty()
                                    ? \App\Models\Package::whereIn('id', $packageIds)->get(['id', 'name', 'description'])
                                    : collect();

                                $packageNames = collect($cartItems)
                                    ->map(fn ($ci) => trim((string) ($ci['package_name'] ?? $ci['packageName'] ?? $ci['pkgName'] ?? '')))
                                    ->filter(fn ($name) => $name !== '')
                                    ->unique()
                                    ->values();

                                $packageRowsByName = $packageNames->isNotEmpty()
                                    ? \App\Models\Package::whereIn('name', $packageNames)->get(['id', 'name', 'description'])
                                    : collect();

                                $packageDescriptionsById = $packageRows
                                    ->mapWithKeys(fn ($pkg) => [(string) $pkg->id => (string) ($pkg->description ?? '')])
                                    ->all();

                                $packageDescriptionsByName = $packageRows
                                    ->mapWithKeys(function ($pkg) {
                                        $key = strtolower(trim((string) ($pkg->name ?? '')));
                                        return $key !== '' ? [$key => (string) ($pkg->description ?? '')] : [];
                                    })
                                    ->all();

                                foreach ($packageRowsByName as $pkgByName) {
                                    $nameKey = strtolower(trim((string) ($pkgByName->name ?? '')));
                                    if ($nameKey === '') {
                                        continue;
                                    }

                                    if (!isset($packageDescriptionsByName[$nameKey]) || trim((string) $packageDescriptionsByName[$nameKey]) === '') {
                                        $packageDescriptionsByName[$nameKey] = (string) ($pkgByName->description ?? '');
                                    }

                                    $idKey = (string) ($pkgByName->id ?? '');
                                    if ($idKey !== '' && (!isset($packageDescriptionsById[$idKey]) || trim((string) $packageDescriptionsById[$idKey]) === '')) {
                                        $packageDescriptionsById[$idKey] = (string) ($pkgByName->description ?? '');
                                    }
                                }

                                foreach ($cartItems as $ci) {
                                    if (!is_array($ci)) {
                                        continue;
                                    }
                                    $cid = (int) ($ci['package_id'] ?? 0);
                                    $cname = strtolower(trim((string) ($ci['package_name'] ?? $ci['packageName'] ?? $ci['pkgName'] ?? '')));
                                    if ($cid > 0 && $cname !== '' && isset($packageDescriptionsById[(string) $cid]) && $packageDescriptionsById[(string) $cid] !== '') {
                                        $packageDescriptionsByName[$cname] = $packageDescriptionsById[(string) $cid];
                                    }
                                }

                                $packageDescriptionsPayload = [
                                    'byId' => $packageDescriptionsById,
                                    'byName' => $packageDescriptionsByName,
                                ];

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
                                $rowError = null;
                            } catch (\Exception $e) {
                                // If there's an error in setup, set defaults
                                $affiliateName = '';
                                $commission = 0;
                                $packageName = 'N/A';
                                $venueName = 'N/A';
                                $packageDetails = collect([]);
                                $packageDetailsText = 'N/A';
                                $packageDescriptionsPayload = ['byId' => [], 'byName' => []];
                                $addons = '';
                                $promo_code_name = null;
                                $commStatus = null;
                                $holdUntil = null;
                                $now = \Carbon\Carbon::now();
                                $isEligible = false;
                                $rowError = $e->getMessage();
                            }
                        @endphp
                        <tr data-row-id="{{ $item->id }}" data-row-error="{{ $rowError ?? '' }}">
                            <td><input type="checkbox" class="row-check" value="{{ $item->id }}"></td>
                            <td class="txn-order-id">#{{ str_pad($item->id, 3, '0', STR_PAD_LEFT) }}</td>
                            @php
                                $transactionWebsite = $item->website ?: optional($item->event)->website ?: optional($item->package)->website;
                                $purchaseTimezone = optional($transactionWebsite)->resolved_timezone ?? 'America/Los_Angeles';
                                $purchaseAtLocal = optional($item->created_at)->copy()?->timezone($purchaseTimezone);
                                $purchaseSortOrder = $purchaseAtLocal?->timestamp ?? 0;
                            @endphp
                            <td data-order="{{ $purchaseSortOrder }}">
                                <div class="txn-date-main">{{ $purchaseAtLocal?->format('M d, Y') ?? '-' }}</div>
                                <div class="txn-date-time">{{ $purchaseAtLocal?->format('h:i A T') ?? '-' }}</div>
                            </td>
                            <td class="txn-confirmation-num">{{ $item->transaction_id ?? 'N/A' }}</td>
                            <td class="txn-pkg-name">
                                <div style="font-size:0.85rem;font-weight:600;margin-bottom:8px;">{{ $venueName }}</div>
                                <button type="button" class="btn btn-sm btn-link-package" data-bs-toggle="modal" data-bs-target="#packageDetailsModal" data-transaction-id="{{ $item->id }}" data-confirmation-number="{{ $item->transaction_id ?? 'N/A' }}" data-cart-items='@json($cartItems)' data-package-descriptions-b64="{{ base64_encode(json_encode($packageDescriptionsPayload)) }}" data-breakdown='@json($item->price_breakdown)' data-transaction-type='{{ $item->type }}' data-men='{{ $item->package_men ?? 0 }}' data-women='{{ $item->package_women ?? 0 }}' data-package-label="{{ $packageDetailsText }}" data-package_use_date="{{ $item->package_use_date ?? '' }}" data-package_number_of_guest="{{ $item->package_number_of_guest ?? 0 }}" data-package_first_name="{{ $item->package_first_name ?? '' }}" data-package_last_name="{{ $item->package_last_name ?? '' }}" data-package_phone="{{ $item->package_phone ?? '' }}" data-package_email="{{ $item->package_email ?? '' }}" data-package_dob="{{ $item->package_dob ?? '' }}" data-package_note="{{ $item->package_note ?? '' }}" data-host_name="{{ $item->host_name ?? '' }}" data-transportation_pickup_time="{{ $item->transportation_pickup_time ?? '' }}" data-transportation_address="{{ $item->transportation_address ?? '' }}" data-transportation_phone="{{ $item->transportation_phone ?? '' }}" data-transportation_note="{{ $item->transportation_note ?? '' }}" data-payment_first_name="{{ $item->payment_first_name ?? '' }}" data-payment_last_name="{{ $item->payment_last_name ?? '' }}" data-payment_phone="{{ $item->payment_phone ?? '' }}" data-payment_email="{{ $item->payment_email ?? '' }}" data-payment_address="{{ $item->payment_address ?? '' }}" data-payment_city="{{ $item->payment_city ?? '' }}" data-payment_state="{{ $item->payment_state ?? '' }}" data-payment_country="{{ $item->payment_country ?? '' }}" data-payment_dob="{{ $item->payment_dob ?? '' }}" data-payment_zip_code="{{ $item->payment_zip_code ?? '' }}" data-type="{{ $item->type }}" data-status="{{ $item->status }}" data-ip_address="{{ $item->ip_address ?? '' }}" data-website_id="{{ $item->website->name ?? '' }}" data-addons="{{ $addons }}" style="font-size:0.85rem;min-width:72px;">View</button>
                            </td>
                            <td>
                                @php
                                    $sourceText = 'Direct';
                                    $sourceBadgeColor = '#6b7280';
                                    $sourceLink = null;
                                    $sourceType = null;

                                    if (!empty($item->affiliate_id) && !empty($item->affiliate)) {
                                        $sourceText = $item->affiliate->display_name ?: optional($item->affiliate->user)->name ?: 'Affiliate #' . $item->affiliate_id;
                                        $sourceBadgeColor = '#8b5cf6';
                                        $sourceLink = route('admin.affiliate.show', $item->affiliate_id);
                                        $sourceType = 'affiliate';
                                    } elseif (!empty($item->entertainer_id) && !empty($item->entertainer)) {
                                        $sourceText = $item->entertainer->display_name ?: optional($item->entertainer->user)->name ?: 'Entertainer #' . $item->entertainer_id;
                                        $sourceBadgeColor = '#ec4899';
                                        $sourceLink = route('admin.entertainer.show', $item->entertainer_id);
                                        $sourceType = 'entertainer';
                                    }
                                @endphp
                                @if($sourceLink)
                                    <a href="{{ $sourceLink }}" style="background:{{ $sourceBadgeColor }};color:white;padding:4px 10px;border-radius:4px;font-size:0.85rem;font-weight:600;text-decoration:none;display:inline-block;cursor:pointer;" title="View {{ $sourceType }} profile">{{ $sourceText }}</a>
                                @else
                                    <span style="background:{{ $sourceBadgeColor }};color:white;padding:4px 10px;border-radius:4px;font-size:0.85rem;font-weight:600;">{{ $sourceText }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="txn-customer-name">{{ $item->package_first_name }} {{ $item->package_last_name }}</div>
                                <div class="txn-customer-email">{{ $item->package_email }}</div>
                            </td>
                            <td class="txn-amount">${{ number_format((float)$item->total, 2) }}</td>
                            <td>
                                @php
                                    $paidAmount = (float)($item->actual_total ?? $item->total ?? 0);
                                    $totalAmount = (float)($item->total ?? 0);
                                    $dueAmount = $totalAmount - $paidAmount;
                                    $paymentStatus = $paidAmount >= $totalAmount ? 'Paid' : ($paidAmount > 0 ? 'Partial' : 'Pending');
                                    $paymentText = $paymentStatus;
                                    if ($paymentStatus === 'Partial') {
                                        $paymentText = 'Partial ($' . number_format($paidAmount, 2) . ' paid)';
                                    }
                                @endphp
                                <span class="badge-{{ $paymentStatus === 'Paid' ? 'completed' : ($paymentStatus === 'Partial' ? 'warning' : 'canceled') }}" style="font-size:0.85rem;">{{ $paymentText }}</span>
                            </td>
                            <td>
                                @php
                                    $cardLast4 = trim((string) ($item->payment_card_last4 ?? ''));
                                @endphp
                                <span style="font-size:0.85rem;font-weight:600;color:{{ $cardLast4 !== '' ? '#fff' : 'rgba(255,255,255,0.3)' }};">{{ $cardLast4 !== '' ? '**** ' . $cardLast4 : '-' }}</span>
                            </td>
                            <td class="txn-amount">
                                @if($dueAmount > 0)
                                    <span style="color:#ef4444;font-weight:600;">${{ number_format($dueAmount, 2) }}</span>
                                @else
                                    <span style="color:rgba(255,255,255,0.3);">-</span>
                                @endif
                            </td>
                            @php
                                    $reservationDate = null;
                                    try {
                                        if (isset($item->package_use_date) && $item->package_use_date) {
                                            $reservationDate = $item->package_use_date;
                                        }
                                    } catch (\Exception $e) {
                                        $reservationDate = null;
                                    }

                                    $nowPacific = \Carbon\Carbon::now('America/Los_Angeles');
                                    $laToday = $nowPacific->copy()->startOfDay();
                                    $reservationDatePacific = null;
                                    $transportAnchorAtPacific = null;
                                    $noShowEligibleAtPacific = null;

                                    if ($reservationDate) {
                                        try {
                                            $reservationDateString = $reservationDate instanceof \Carbon\CarbonInterface
                                                ? $reservationDate->format('Y-m-d')
                                                : trim((string) $reservationDate);

                                            if ($reservationDateString !== '') {
                                                $reservationDatePacific = \Carbon\Carbon::createFromFormat('Y-m-d', $reservationDateString, 'America/Los_Angeles')->startOfDay();
                                            }
                                        } catch (\Throwable $e) {
                                            $reservationDatePacific = null;
                                        }
                                    }

                                    if ($reservationDatePacific) {
                                        $transportTimeRaw = trim((string) ($item->transportation_arrival_time ?: $item->transportation_pickup_time ?: ''));

                                        if ($transportTimeRaw !== '') {
                                            try {
                                                $transportAnchorAtPacific = \Carbon\Carbon::parse(
                                                    $reservationDatePacific->format('Y-m-d') . ' ' . $transportTimeRaw,
                                                    'America/Los_Angeles'
                                                );
                                                $noShowEligibleAtPacific = $transportAnchorAtPacific->copy()->addHours(24);
                                            } catch (\Throwable $e) {
                                                $transportAnchorAtPacific = null;
                                                $noShowEligibleAtPacific = null;
                                            }
                                        }
                                    }

                                    $transportModeLabel = $item->transport_mode_label ?? null;
                                    $reservationSortOrder = $reservationDatePacific?->timestamp ?? 0;

                                    $reservationStatusValue = 'Upcoming';
                                    $reservationStatusClass = 'badge-reservation-upcoming';

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
                            @endphp
                            <td data-order="{{ $reservationSortOrder }}">{{-- RESERVATION STATUS --}}
                                @if($reservationStatusValue === 'Upcoming' && $reservationDatePacific)
                                    <div style="font-size:0.9rem;margin-bottom:0.5rem;">{{ $reservationDatePacific->format('M d, Y') }}</div>
                                    <div style="margin-top:4px;">
                                        <span class="{{ $reservationStatusClass }}">{{ $reservationStatusValue }}</span>
                                    </div>
                                @else
                                    <span class="{{ $reservationStatusClass }}">{{ $reservationStatusValue }}</span>
                                @endif
                            </td>
                            <td data-order="{{ $reservationSortOrder }}">{{-- RESERVATION DATE --}}
                                @if($reservationDatePacific)
                                    @if($reservationDatePacific->equalTo($laToday))
                                        <div style="font-size:0.95rem;font-weight:600;">Today</div>
                                    @elseif($reservationDatePacific->greaterThan($laToday))
                                        <div style="font-size:0.9rem;">{{ $reservationDatePacific->format('M d, Y') }}</div>
                                    @else
                                        <div style="font-size:0.9rem;color:rgba(255,255,255,0.6);">{{ $reservationDatePacific->format('M d, Y') }}</div>
                                    @endif
                                @else
                                    <span style="color:rgba(255,255,255,0.25);font-size:0.78rem">-</span>
                                @endif
                                @if(!empty($transportModeLabel))
                                    <div style="margin-top:4px;display:inline-block;padding:2px 8px;border-radius:999px;font-size:0.72rem;font-weight:700;line-height:1.2;background:{{ $transportModeLabel === 'Self Drive' ? 'rgba(16,185,129,0.14)' : 'rgba(59,130,246,0.14)' }};color:{{ $transportModeLabel === 'Self Drive' ? '#34d399' : '#93c5fd' }};border:1px solid {{ $transportModeLabel === 'Self Drive' ? 'rgba(16,185,129,0.25)' : 'rgba(59,130,246,0.25)' }};">{{ $transportModeLabel }}</div>
                                @endif
                            </td>
                            <td>
                                @if($item->checked_in_status)
                                    <span class="badge-checkin-yes">Redeemed</span>
                                @else
                                    <span class="badge-checkin-no">Not Redeemed</span>
                                @endif
                            </td>
                            <td class="txn-commission">
                                @php
                                    // Smart formatting: show whole number without decimals if it's a whole number
                                    $commissionDisplay = ($commission == intval($commission)) ? number_format($commission, 0) : number_format($commission, 2);
                                    $commissionText = '$' . $commissionDisplay;

                                    if ($commStatus === 'pending' && $holdUntil) {
                                        $daysRemaining = (int)now()->diffInDays($holdUntil, false);
                                        if ($daysRemaining <= 0) {
                                            $commissionText .= ' (Available now)';
                                        } else {
                                            $commissionText .= ' (Available in ' . abs($daysRemaining) . ' days)';
                                        }
                                    } elseif ($commStatus === 'paid') {
                                        $commissionText .= ' (Paid out)';
                                    } elseif ($commStatus === 'approved') {
                                        $commissionText .= ' (Approved)';
                                    } elseif ($commStatus === 'reversed') {
                                        $commissionText .= ' (Reversed)';
                                    }
                                @endphp
                                <div style="font-weight:600;">{{ $commissionText }}</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-1">
                                    @php
                                        $requiresTransportationForRow = false;
                                        $rowCartItems = is_array($item->cart_items ?? null) ? $item->cart_items : [];
                                        foreach ($rowCartItems as $rowCartItem) {
                                            if (!is_array($rowCartItem)) {
                                                continue;
                                            }
                                            $rowTransportValue = $rowCartItem['transportation'] ?? ($rowCartItem['transport'] ?? false);
                                            if (
                                                $rowTransportValue === true ||
                                                $rowTransportValue === 1 ||
                                                $rowTransportValue === '1' ||
                                                $rowTransportValue === 'true' ||
                                                $rowTransportValue === 'on'
                                            ) {
                                                $requiresTransportationForRow = true;
                                                break;
                                            }
                                        }
                                        if (!$requiresTransportationForRow && !empty($item->package)) {
                                            $requiresTransportationForRow = (
                                                $item->package->transportation == 1 ||
                                                $item->package->transportation === true ||
                                                $item->package->transportation === '1'
                                            );
                                        }
                                    @endphp
                                    <button type="button" class="txn-action-eye view-btn"
                                        data-bs-toggle="modal" data-bs-target="#viewTransactionModal"
                                        data-id="{{ $item->id }}"
                                        data-transaction_id="{{ $item->transaction_id ?? 'Free' }}"
                                        data-package_id="{{ $packageDetailsText }}"
                                        data-cart-items='@json($cartItems)'
                                        data-breakdown='@json($item->price_breakdown)'
                                        data-package_first_name="{{ $item->package_first_name }}"
                                        data-package_last_name="{{ $item->package_last_name }}"
                                        data-package_phone="{{ $item->package_phone }}"
                                        data-package_email="{{ $item->package_email }}"
                                        data-package_dob="{{ $item->package_dob }}"
                                        data-package_note="{{ $item->package_note }}"
                                        data-host_name="{{ $item->host_name }}"
                                        data-package_number_of_guest="{{ $item->package_number_of_guest }}"
                                        data-transportation_pickup_time="{{ $item->transportation_pickup_time }}"
                                        data-transportation_arrival_time="{{ $item->transportation_arrival_time }}"
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
                                        data-shipping_same_as_billing="{{ $item->shipping_same_as_billing ? 1 : 0 }}"
                                        data-shipping_first_name="{{ $item->shipping_first_name ?? '' }}"
                                        data-shipping_last_name="{{ $item->shipping_last_name ?? '' }}"
                                        data-shipping_phone="{{ $item->shipping_phone ?? '' }}"
                                        data-shipping_email="{{ $item->shipping_email ?? '' }}"
                                        data-shipping_address="{{ $item->shipping_address ?? '' }}"
                                        data-shipping_city="{{ $item->shipping_city ?? '' }}"
                                        data-shipping_state="{{ $item->shipping_state ?? '' }}"
                                        data-shipping_zip_code="{{ $item->shipping_zip_code ?? '' }}"
                                        data-shipping_country="{{ $item->shipping_country ?? '' }}"
                                        data-payment_card_last4="{{ $item->payment_card_last4 ?? '' }}"
                                        data-payment_card_brand="{{ $item->payment_card_brand ?? '' }}"
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
                                        data-service_charge="{{ number_format(($item->actual_total / 100) * ($item->website->service_charge_fee ?? 0), 2) }}"
                                        data-processing_fee="{{
                                            ($item->website->processing_fee_type ?? 'percentage') === 'flat'
                                                ? number_format($item->website->processing_fee ?? 0, 2)
                                                : number_format(($item->actual_total / 100) * ($item->website->processing_fee ?? 0), 2)
                                        }}"
                                        data-due="{{ $item->actual_total - $item->total }}"
                                        data-promo_code="{{ $promo_code_name }}"
                                        data-discounted_amount="{{ $item->discounted_amount }}"
                                        data-package_use_date="{{ $item->package_use_date }}"
                                        data-date="{{ $purchaseAtLocal?->format('Y-m-d h:i A T') ?? '' }}"
                                        data-men="{{ $item->men ?? '' }}"
                                        data-women="{{ $item->women ?? '' }}"
                                        data-requires_transportation="{{ $requiresTransportationForRow ? 1 : 0 }}"
                                        data-affiliate_name="{{ !empty($item->affiliate_id) && !empty($item->affiliate) ? ($item->affiliate->display_name ?: optional($item->affiliate->user)->name ?: ('affiliate #' . $item->affiliate_id)) : '' }}"
                                        data-entertainer_name="{{ !empty($item->entertainer_id) && !empty($item->entertainer) ? ($item->entertainer->display_name ?: optional($item->entertainer->user)->name ?: ('Entertainer #' . $item->entertainer_id)) : '' }}"
                                        data-affiliate_commission_percentage="{{ (float) ($item->affiliate_commission_percentage ?? 0) }}"
                                        data-affiliate_commission_amount="{{ (float) ($item->affiliate_commission_amount ?? 0) }}"
                                        data-affiliate_commission_status="{{ $item->affiliate_commission_status ?? '' }}"
                                        data-affiliate_commission_hold_until="{{ $item->affiliate_commission_hold_until ? optional($item->affiliate_commission_hold_until)->timezone('America/Los_Angeles')->format('M d, Y h:i A \P\T') : '' }}"
                                        data-entertainer_commission_percentage="{{ (float) ($item->entertainer_commission_percentage ?? 0) }}"
                                        data-entertainer_commission_amount="{{ (float) ($item->entertainer_commission_amount ?? 0) }}"
                                        data-entertainer_commission_status="{{ $item->entertainer_commission_status ?? '' }}"
                                        data-entertainer_commission_hold_until="{{ $item->entertainer_commission_hold_until ? optional($item->entertainer_commission_hold_until)->timezone('America/Los_Angeles')->format('M d, Y h:i A \P\T') : '' }}"
                                        data-total_commission="{{ (float) $commission }}"
                                        data-checked_in_status="{{ $item->checked_in_status ? 1 : 0 }}"
                                        data-checked_in_at_pacific="{{ $item->checked_in_at_pacific ? (optional($item->checked_in_at_pacific)->format('Y-m-d h:i A') . ' PT') : '' }}"
                                        data-checkin_photo_front="{{ $item->checkin_photo_front_path ?? '' }}"
                                        data-checkin_photo_back="{{ $item->checkin_photo_back_path ?? '' }}"
                                        title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <div class="dropdown">
                                        <button class="txn-action-more btn p-0" data-bs-toggle="dropdown" type="button" style="border:none;background:none">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end" style="background:#1e293b;border:1px solid rgba(255,255,255,0.1)">
                                            @if(!$isArchivedView)
                                            <li><a class="dropdown-item" style="color:rgba(255,255,255,0.7);font-size:0.82rem" href="{{ route('admin.transaction.update', ['id' => $item->id, 'status' => 1]) }}"><i class="fas fa-check me-2 text-success"></i>Mark Completed</a></li>
                                            <li><a class="dropdown-item" style="color:rgba(255,255,255,0.7);font-size:0.82rem" href="{{ route('admin.transaction.update', ['id' => $item->id, 'status' => 0]) }}"><i class="fas fa-times me-2 text-danger"></i>Mark Canceled</a></li>
                                            <li><a class="dropdown-item" style="color:rgba(255,255,255,0.7);font-size:0.82rem" href="{{ route('admin.transaction.update', ['id' => $item->id, 'status' => 2]) }}"><i class="fas fa-undo me-2 text-warning"></i>Mark Refunded</a></li>
                                            @endif
                                            @if($canArchiveTransactions)
                                            <li><hr class="dropdown-divider" style="border-color:rgba(255,255,255,0.12)"></li>
                                            @if($isArchivedView)
                                                <li>
                                                    <form method="POST" action="{{ route('admin.transaction.unarchive', $item->id) }}" onsubmit="return confirm('Unarchive this transaction?');">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item" style="color:#34d399;font-size:0.82rem">
                                                            <i class="fas fa-box-open me-2 text-success"></i>Unarchive Transaction
                                                        </button>
                                                    </form>
                                                </li>
                                            @else
                                                <li>
                                                    <form method="POST" action="{{ route('admin.transaction.archive', $item->id) }}" onsubmit="return confirm('Archive this transaction? Archived transactions are removed from totals and reports.');">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item" style="color:#fbbf24;font-size:0.82rem">
                                                            <i class="fas fa-archive me-2 text-warning"></i>Archive Transaction
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </td>
                            <td class="d-none">{{ $affiliateName ?: 'DIRECT' }}</td>
                            <td class="d-none">@if($isPayoutPage)@if($commission == 0)N/A@elseif($commStatus === 'paid')PAID OUT@elseif($commStatus === 'reversed')REVERSED@else{{ $commStatus }}@endif@else-@endif</td>
                            <td class="d-none">{{ $venueName }}</td>
                            <td class="d-none">{{ $packageName }}</td>
                        </tr>
                        @empty
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="7" class="text-end" style="color:rgba(255,255,255,0.5);font-size:0.82rem">Total:</th>
                            <th id="amount-total" style="color:#fff;font-weight:700;font-size:0.9rem"></th>
                            <th colspan="11"></th>
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
                            <div id="transactionDetailsContent"></div>
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

            <!-- Package Details Modal -->
            <div class="modal fade" id="packageDetailsModal" tabindex="-1" aria-labelledby="packageDetailsModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="packageDetailsModalLabel">📦 Package Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div id="packageDetailsContent">
                                <!-- Content will be filled by JavaScript -->
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="download-package-pdf">
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
            .badge-payout-pending  { background: rgba(245,158,11,0.15); color: #fbbf24; border: 1px solid rgba(245,158,11,0.3); font-size: 0.68rem; font-weight: 700; padding: 2px 2px; border-radius: 6px; letter-spacing: 0.04em; }
            .badge-payout-approved { background: rgba(99,102,241,0.15); color: #818cf8; border: 1px solid rgba(99,102,241,0.3); font-size: 0.68rem; font-weight: 700; padding: 2px 2px; border-radius: 6px; letter-spacing: 0.04em; }
            .badge-payout-paid     { background: rgba(16,185,129,0.15); color: #34d399; border: 1px solid rgba(16,185,129,0.3); font-size: 0.68rem; font-weight: 700; padding: 2px 2px; border-radius: 6px; letter-spacing: 0.04em; }
            .badge-payout-reversed { background: rgba(239,68,68,0.15);  color: #f87171; border: 1px solid rgba(239,68,68,0.3);  font-size: 0.68rem; font-weight: 700; padding: 2px 2px; border-radius: 6px; letter-spacing: 0.04em; }
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
                                label: 'Fee ($)',
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

                const actionColumnIndex = $('#txnDataTable thead th').filter(function() {
                    return $(this).text().trim().toLowerCase() === 'action';
                }).first().index();
                const nonOrderableTargets = [0];
                if (actionColumnIndex >= 0) {
                    nonOrderableTargets.push(actionColumnIndex);
                }

                // Initialize DataTable with pagination
                let table = $('#txnDataTable').DataTable({
                    pageLength: 25,
                    searching: true,
                    ordering: true,
                    paging: true,
                    info: true,
                    lengthChange: true,
                    autoWidth: false,
                    language: {
                        emptyTable: 'No transactions found.'
                    },
                    columnDefs: [
                        { orderable: false, targets: nonOrderableTargets }
                    ]
                });

                $('#txnDataTable thead').on('click mousedown', '#selectAll', function(e) {
                    e.stopPropagation();
                });

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
                    const reservationValue = String($('#reservationFilter').val() || '').trim();
                    if (reservationValue) {
                        setOrDelete('reservation', reservationValue);
                    } else {
                        params.delete('reservation');
                    }

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

                $('#websiteFilter, #typeFilter, #affiliateFilter, #statusFilter, #reservationFilter').on('change', function() {
                    reloadWithServerFilters();
                });

                const $txnDateRange = $('#txnDateRange');
                const $txnDateRangeWrap = $('#txnDateRangeWrap');

                if ($txnDateRange.data('daterangepicker')) {
                    $txnDateRange.data('daterangepicker').remove();
                }

                const initialRangeValue = String($txnDateRange.val() || '').trim();
                let initialStartDate = null;
                let initialEndDate = null;

                if (initialRangeValue && initialRangeValue.includes(' - ')) {
                    const initialParts = initialRangeValue.split(' - ');
                    const parsedStart = moment(initialParts[0], 'MM/DD/YYYY', true);
                    const parsedEnd = moment(initialParts[1], 'MM/DD/YYYY', true);
                    if (parsedStart.isValid() && parsedEnd.isValid()) {
                        initialStartDate = parsedStart;
                        initialEndDate = parsedEnd;
                    }
                }

                const dateRangeOptions = {
                    autoUpdateInput: false,
                    linkedCalendars: false,
                    opens: 'left',
                    showDropdowns: true,
                    locale: { cancelLabel: 'Clear', format: 'MM/DD/YYYY' },
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    }
                };

                if (initialStartDate && initialEndDate) {
                    dateRangeOptions.startDate = initialStartDate;
                    dateRangeOptions.endDate = initialEndDate;
                }

                $txnDateRange.daterangepicker(dateRangeOptions);

                if (initialStartDate && initialEndDate) {
                    $txnDateRange.val(initialStartDate.format('MM/DD/YYYY') + ' - ' + initialEndDate.format('MM/DD/YYYY'));
                }

                $txnDateRange.off('apply.daterangepicker.txnDateRange').on('apply.daterangepicker.txnDateRange', function(ev, picker) {
                    $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
                    reloadWithServerFilters();
                });

                $txnDateRange.off('cancel.daterangepicker.txnDateRange').on('cancel.daterangepicker.txnDateRange', function() {
                    $(this).val('');
                    reloadWithServerFilters();
                });

                $txnDateRange.off('click.txnDateRange').on('click.txnDateRange', function() {
                    const picker = $(this).data('daterangepicker');
                    if (picker) {
                        picker.show();
                    }
                });

                // ── Export button wiring (custom, reliable across pages) ─────
                // Export all real transaction columns, even if the layout is hiding some
                // of them at the current viewport size. Keep the checkbox, action column,
                // and internal underscore helper columns out of exports.
                function getExportColumnIndexes() {
                    const indexes = [];
                    $('#txnDataTable thead th').each(function (idx) {
                        const $th = $(this);
                        const headerText = $th.text().trim().toLowerCase();
                        if (idx === 0) return;
                        if (headerText === 'action') return;
                        if (headerText.startsWith('_')) return;

                        // Keep any real data column, even if the responsive layout hides it.
                        if (headerText !== '') {
                            indexes.push(idx);
                        }
                    });
                    return indexes;
                }

                function stripHtml(value) {
                    const tmp = document.createElement('div');
                    tmp.innerHTML = value == null ? '' : String(value);
                    return (tmp.textContent || tmp.innerText || '').replace(/\s+/g, ' ').trim();
                }

                function csvEscape(value) {
                    const safe = String(value ?? '').replace(/"/g, '""');
                    return '"' + safe + '"';
                }

                function parseJsonLike(value) {
                    if (value == null || value === '') {
                        return null;
                    }

                    if (Array.isArray(value) || typeof value === 'object') {
                        return value;
                    }

                    try {
                        return JSON.parse(value);
                    } catch (e) {
                        return null;
                    }
                }

                function getExportPackageDetails(rowNode) {
                    if (!rowNode) {
                        return '';
                    }

                    const packageButton = rowNode.querySelector('.btn-link-package');
                    if (!packageButton) {
                        return '';
                    }

                    const $button = $(packageButton);
                    const transactionType = String($button.data('transaction-type') || '').toLowerCase();
                    const packageLabel = String($button.data('package-label') || packageButton.getAttribute('data-package-label') || '').trim();
                    const menCount = parseInt($button.data('men') || 0, 10) || 0;
                    const womenCount = parseInt($button.data('women') || 0, 10) || 0;
                    const totalGuests = menCount + womenCount;

                    let cartItems = $button.data('cart-items');
                    if (!Array.isArray(cartItems)) {
                        cartItems = parseJsonLike(packageButton.getAttribute('data-cart-items')) || [];
                    }

                    const packageParts = [];
                    if (Array.isArray(cartItems)) {
                        cartItems.forEach(function (item) {
                            if (!item || typeof item !== 'object') {
                                return;
                            }

                            const packageName = String(item.package_name || item.packageName || item.pkgName || '').trim();
                            if (!packageName) {
                                return;
                            }

                            const quantity = Math.max(1, parseInt(item.guests || item.quantity || 1, 10) || 1);
                            const packageType = String(item.package_type || item.type || item.packageType || '').toLowerCase();

                            if (packageType === 'ticket') {
                                packageParts.push(packageName + ' x' + quantity + ' tickets');
                            } else {
                                packageParts.push(packageName + ' x' + quantity + ' guests');
                            }
                        });
                    }

                    const summary = packageParts.length > 0 ? packageParts.join('; ') : packageLabel;
                    const details = [];

                    if (summary) {
                        details.push(summary);
                    }

                    if (transactionType === 'reservation' && totalGuests > 0) {
                        details.push('Guests: M ' + menCount + ', F ' + womenCount + ', Total ' + totalGuests);
                    } else if (totalGuests > 0) {
                        details.push('Guests: ' + totalGuests);
                    }

                    return details.join(' | ');
                }

                function getRowGuestCountFromButton($viewBtn) {
                    const menCount = parseInt($viewBtn.data('men') || 0, 10) || 0;
                    const womenCount = parseInt($viewBtn.data('women') || 0, 10) || 0;
                    const reservationGuests = Math.max(0, menCount + womenCount);
                    if (reservationGuests > 0) {
                        return reservationGuests;
                    }

                    const packageGuests = parseInt($viewBtn.data('package_number_of_guest') || 0, 10) || 0;
                    return Math.max(0, packageGuests);
                }

                function getExportDataset() {
                    const exportColumnIndexes = getExportColumnIndexes();
                    const selected = $('.row-check:checked');
                    const selectedOnly = selected.length > 0;

                    const headers = exportColumnIndexes.map(function (idx) {
                        return stripHtml($('#txnDataTable thead th').eq(idx).text());
                    });
                    headers.push('Guest Count');
                    headers.push('Package Details');

                    const rows = [];
                    const summary = {
                        totalTransactions: 0,
                        completedTransactions: 0,
                        totalRevenue: 0,
                        totalGuests: 0,
                        pendingFee: 0,
                        payoutAmount: 0,
                        totalEarning: 0,
                    };

                    // Get DataTable instance - try both ways
                    let dt = table;
                    if (!dt) {
                        try {
                            dt = $.fn.dataTable.fnTables(true)[0] ? $($.fn.dataTable.fnTables(true)[0]).dataTable().api() : null;
                        } catch (e) {
                            // ignore
                        }
                    }

                    if (!dt) {
                        try {
                            dt = $('#txnDataTable').DataTable();
                        } catch (e) {
                            // ignore
                        }
                    }

                    if (dt && dt.rows && typeof dt.rows === 'function') {
                        try {
                            // Export selected rows if any are checked, otherwise export currently filtered rows.
                            const exportRowsApi = selectedOnly ? dt.rows() : dt.rows({ search: 'applied' });

                            exportRowsApi.every(function (rowIndex) {
                                const rowNode = this.node();
                                const rowData = this.data();

                                // If checkboxes selected, only export checked rows
                                if (selectedOnly && !$(rowNode).find('.row-check').prop('checked')) {
                                    return true; // continue
                                }

                                const $rowNode = $(rowNode);
                                const $viewBtn = $rowNode.find('.view-btn').first();
                                const guestCount = getRowGuestCountFromButton($viewBtn);

                                const statusValue = (typeof normalizeStatusValue === 'function')
                                    ? normalizeStatusValue($viewBtn.data('status'))
                                    : String($viewBtn.data('status') || '').trim().toLowerCase();
                                const isCompleted = statusValue === 'completed' || statusValue === '1';

                                const amountText = String($rowNode.find('td.txn-amount').first().text() || '');
                                const rowRevenue = parseFloat(amountText.replace(/[^0-9.-]+/g, '')) || 0;

                                const affAmount = parseFloat($viewBtn.data('affiliate_commission_amount')) || 0;
                                const entAmount = parseFloat($viewBtn.data('entertainer_commission_amount')) || 0;
                                const affStatus = String($viewBtn.data('affiliate_commission_status') || '').trim().toLowerCase();
                                const entStatus = String($viewBtn.data('entertainer_commission_status') || '').trim().toLowerCase();

                                summary.totalTransactions += 1;
                                summary.totalGuests += guestCount;
                                if (isCompleted) {
                                    summary.completedTransactions += 1;
                                    summary.totalRevenue += rowRevenue;
                                }

                                if (affStatus === 'pending') {
                                    summary.pendingFee += affAmount;
                                }
                                if (entStatus === 'pending') {
                                    summary.pendingFee += entAmount;
                                }

                                if (affStatus === 'paid') {
                                    summary.payoutAmount += affAmount;
                                }
                                if (entStatus === 'paid') {
                                    summary.payoutAmount += entAmount;
                                }

                                if (affStatus !== 'reversed') {
                                    summary.totalEarning += affAmount;
                                }
                                if (entStatus !== 'reversed') {
                                    summary.totalEarning += entAmount;
                                }

                                const row = exportColumnIndexes.map(function (colIdx) {
                                    return stripHtml(rowData[colIdx] || '');
                                });
                                row.push(String(guestCount));
                                row.push(getExportPackageDetails(rowNode));
                                rows.push(row);
                            });
                        } catch (e) {
                            console.error('Export error:', e);
                        }
                    }

                    return {
                        headers,
                        rows,
                        summary,
                        selectedOnly,
                        selectedCount: selected.length,
                    };
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
                    const summary = dataset.summary || {};
                    const money = function(value) {
                        const n = Number(value || 0);
                        return '$' + n.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    };

                    doc.setFontSize(13);
                    doc.text('Transactions Export', 14, 12);
                    doc.setFontSize(9);
                    doc.text('Scope: ' + (dataset.selectedOnly ? ('Selected Rows (' + (summary.totalTransactions || 0) + ')') : 'All Filtered Rows'), 14, 17);
                    doc.text('Generated: ' + new Date().toLocaleString(), 14, 21);

                    doc.autoTable({
                        startY: 24,
                        head: [['Metric', 'Value', 'Metric', 'Value']],
                        body: [[
                            'Total Transactions', String(summary.totalTransactions || 0),
                            'Completed Transactions', String(summary.completedTransactions || 0)
                        ], [
                            'Total Revenue', money(summary.totalRevenue || 0),
                            'Pending Fee', money(summary.pendingFee || 0)
                        ], [
                            'Total Guests', String(summary.totalGuests || 0),
                            'Payout Amount', money(summary.payoutAmount || 0)
                        ], [
                            'Total Earning', money(summary.totalEarning || 0),
                            '', ''
                        ]],
                        styles: { fontSize: 8, cellPadding: 2 },
                        headStyles: { fillColor: [41, 128, 185] },
                    });

                    doc.autoTable({
                        head: [dataset.headers],
                        body: dataset.rows,
                        startY: (doc.lastAutoTable && doc.lastAutoTable.finalY ? doc.lastAutoTable.finalY + 4 : 42),
                        styles: { fontSize: 6.5, cellPadding: 1.5, overflow: 'linebreak', valign: 'top' },
                        headStyles: { fillColor: [41, 128, 185] },
                        bodyStyles: { textColor: [25, 25, 25] },
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

                // ── Selection across all DataTable pages ───────────────────
                const selectedTransactionIds = new Set();

                function getFilteredRowNodes() {
                    if (!table) return $();
                    return $(table.rows({ search: 'applied' }).nodes());
                }

                function applyCheckedStateToVisibleRows() {
                    if (!table) return;
                    table.rows({ page: 'current' }).every(function () {
                        const rowNode = this.node();
                        const checkbox = $(rowNode).find('.row-check');
                        if (!checkbox.length) return;

                        const id = String(checkbox.val() || '');
                        checkbox.prop('checked', selectedTransactionIds.has(id));
                    });
                }

                function updateSelectionUi() {
                    const filteredRows = getFilteredRowNodes();
                    const filteredCheckboxes = filteredRows.find('.row-check');
                    const filteredCount = filteredCheckboxes.length;
                    const checkedFilteredCount = filteredCheckboxes.filter(':checked').length;

                    const selectAll = $('#selectAll');
                    if (filteredCount === 0) {
                        selectAll.prop('checked', false).prop('indeterminate', false);
                    } else if (checkedFilteredCount === 0) {
                        selectAll.prop('checked', false).prop('indeterminate', false);
                    } else if (checkedFilteredCount === filteredCount) {
                        selectAll.prop('checked', true).prop('indeterminate', false);
                    } else {
                        selectAll.prop('checked', false).prop('indeterminate', true);
                    }

                    $('#selectionCount').text(selectedTransactionIds.size + ' selected');
                }

                function setFilteredRowsChecked(checked) {
                    const filteredRows = getFilteredRowNodes();
                    const checkboxes = filteredRows.find('.row-check');

                    checkboxes.each(function () {
                        const id = String($(this).val() || '');
                        $(this).prop('checked', checked);
                        if (!id) return;
                        if (checked) selectedTransactionIds.add(id);
                        else selectedTransactionIds.delete(id);
                    });

                    updateSelectionUi();
                }

                function fillBulkFormInputs(containerSelector, ids) {
                    const container = $(containerSelector);
                    container.empty();

                    ids.forEach(function (id) {
                        $('<input>', {
                            type: 'hidden',
                            name: 'transaction_ids[]',
                            value: id,
                        }).appendTo(container);
                    });
                }

                $(document).on('change', '.row-check', function() {
                    const id = String($(this).val() || '');
                    if (!id) return;

                    if ($(this).is(':checked')) selectedTransactionIds.add(id);
                    else selectedTransactionIds.delete(id);

                    updateSelectionUi();
                });

                $('#selectAll').on('change', function() {
                    setFilteredRowsChecked(this.checked);
                });

                $('#selectAllPagesBtn').on('click', function() {
                    setFilteredRowsChecked(true);
                });

                $('#clearSelectionBtn').on('click', function() {
                    selectedTransactionIds.clear();
                    $('.row-check').prop('checked', false);
                    updateSelectionUi();
                });

                $('#bulkArchiveBtn').on('click', function() {
                    const ids = Array.from(selectedTransactionIds);
                    if (!ids.length) {
                        alert('Select at least one transaction to archive.');
                        return;
                    }

                    if (!confirm('Archive ' + ids.length + ' selected transaction(s)? Archived transactions are excluded from totals and reports.')) {
                        return;
                    }

                    fillBulkFormInputs('#bulkArchiveInputs', ids);
                    $('#bulkArchiveForm').trigger('submit');
                });

                $('#bulkUnarchiveBtn').on('click', function() {
                    const ids = Array.from(selectedTransactionIds);
                    if (!ids.length) {
                        alert('Select at least one transaction to unarchive.');
                        return;
                    }

                    if (!confirm('Unarchive ' + ids.length + ' selected transaction(s)?')) {
                        return;
                    }

                    fillBulkFormInputs('#bulkUnarchiveInputs', ids);
                    $('#bulkUnarchiveForm').trigger('submit');
                });

                // ── Running total ────────────────────────────────────────────
                function updateTotal() {
                    if (!table) return;
                    let total = 0;
                    table.rows({ search: 'applied' }).every(function(index) {
                        const row = this.node();
                        const statusValue = String($(row).find('.view-btn').data('status') ?? '').trim();
                        if (statusValue === '0' || statusValue === '2' || statusValue.toLowerCase() === 'canceled' || statusValue.toLowerCase() === 'refunded') {
                            return;
                        }
                        const amountCell = row.querySelector('.txn-amount');
                        if (amountCell) {
                            const text = amountCell.textContent.replace(/[^0-9.-]+/g, '');
                            total += parseFloat(text) || 0;
                        }
                    });
                    $('#amount-total').text('$' + total.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                }

                function parseRowDateToMoment(rawDate) {
                    const dateStr = String(rawDate || '').trim();
                    if (!dateStr) {
                        return null;
                    }

                    const parsed = moment(dateStr, [
                        'MMM DD, YYYY hh:mm A z',
                        'MMM D, YYYY hh:mm A z',
                        'MMM DD, YYYY h:mm A z',
                        'MMM D, YYYY h:mm A z',
                        'YYYY-MM-DD h:mm A z',
                        'YYYY-MM-DD hh:mm A z',
                        'YYYY-MM-DD h:mm A',
                        'YYYY-MM-DD hh:mm A',
                        'YYYY-MM-DD HH:mm:ss',
                        'YYYY-MM-DD'
                    ], true);

                    return parsed.isValid() ? parsed : null;
                }

                function normalizeStatusValue(raw) {
                    const value = String(raw == null ? '' : raw).trim().toLowerCase();
                    if (value === '1' || value === 'completed') {
                        return 'completed';
                    }
                    if (value === '0' || value === 'canceled' || value === 'cancelled') {
                        return 'canceled';
                    }
                    if (value === '2' || value === 'refunded') {
                        return 'refunded';
                    }
                    return value;
                }

                function setStatValueByLabel(label, valueText) {
                    $('.txn-stat-label').each(function() {
                        if ($(this).text().trim().toLowerCase() !== String(label).trim().toLowerCase()) {
                            return;
                        }
                        const card = $(this).closest('.txn-stat-card');
                        card.find('.txn-stat-value').first().text(valueText);
                    });
                }

                function setTrendByLabel(label, current, previous) {
                    const prev = Number(previous || 0);
                    const curr = Number(current || 0);
                    let pct = 0;
                    if (prev > 0) {
                        pct = ((curr - prev) / prev) * 100;
                    }

                    const absPct = Math.abs(pct).toFixed(1) + '%';
                    const isUp = pct >= 0;

                    $('.txn-stat-label').each(function() {
                        if ($(this).text().trim().toLowerCase() !== String(label).trim().toLowerCase()) {
                            return;
                        }

                        const card = $(this).closest('.txn-stat-card');
                        const trendEl = card.find('.txn-stat-trend').first();
                        if (!trendEl.length) {
                            return;
                        }

                        trendEl.removeClass('trend-up trend-down').addClass(isUp ? 'trend-up' : 'trend-down');
                        trendEl.html('<i class="fas fa-arrow-' + (isUp ? 'up' : 'down') + ' me-1"></i>' + absPct + ' <span>vs last week</span>');
                    });
                }

                function updateDashboardCardsFromFilteredRows() {
                    if (!table) return;

                    const now = moment();
                    const weekStart = now.clone().startOf('week');
                    const prevWeekStart = weekStart.clone().subtract(1, 'week');
                    const prevWeekEnd = prevWeekStart.clone().endOf('week');

                    let totalTransactions = 0;
                    let completedTransactions = 0;
                    let totalRevenue = 0;
                    let totalGuests = 0;
                    let pendingFee = 0;
                    let pendingAmount = 0;
                    let payoutAmount = 0;
                    let totalEarning = 0;

                    let thisWeekTotal = 0;
                    let prevWeekTotal = 0;
                    let thisWeekCompleted = 0;
                    let prevWeekCompleted = 0;
                    let thisWeekRevenue = 0;
                    let prevWeekRevenue = 0;

                    table.rows({ search: 'applied' }).every(function() {
                        const row = this.node();
                        if (!row) return;

                        const $row = $(row);
                        const $viewBtn = $row.find('.view-btn').first();
                        const guestCount = getRowGuestCountFromButton($viewBtn);

                        const normalizedStatus = normalizeStatusValue($viewBtn.data('status'));
                        const isCompleted = normalizedStatus === 'completed';

                        const amountText = String($row.find('td.txn-amount').first().text() || '');
                        const rowRevenue = parseFloat(amountText.replace(/[^0-9.-]+/g, '')) || 0;

                        const affAmount = parseFloat($viewBtn.data('affiliate_commission_amount')) || 0;
                        const entAmount = parseFloat($viewBtn.data('entertainer_commission_amount')) || 0;
                        const affStatus = String($viewBtn.data('affiliate_commission_status') || '').trim().toLowerCase();
                        const entStatus = String($viewBtn.data('entertainer_commission_status') || '').trim().toLowerCase();
                        const affHold = parseRowDateToMoment($viewBtn.data('affiliate_commission_hold_until'));
                        const entHold = parseRowDateToMoment($viewBtn.data('entertainer_commission_hold_until'));

                        totalTransactions += 1;
                        totalGuests += guestCount;
                        if (isCompleted) {
                            completedTransactions += 1;
                            totalRevenue += rowRevenue;
                        }

                        if (affStatus === 'pending') {
                            pendingFee += affAmount;
                            if (affHold && affHold.isAfter(now)) {
                                pendingAmount += affAmount;
                            }
                        }
                        if (entStatus === 'pending') {
                            pendingFee += entAmount;
                            if (entHold && entHold.isAfter(now)) {
                                pendingAmount += entAmount;
                            }
                        }
                        if (affStatus === 'paid') {
                            payoutAmount += affAmount;
                        }
                        if (entStatus === 'paid') {
                            payoutAmount += entAmount;
                        }
                        if (affStatus !== 'reversed') {
                            totalEarning += affAmount;
                        }
                        if (entStatus !== 'reversed') {
                            totalEarning += entAmount;
                        }

                        const createdMoment = parseRowDateToMoment($viewBtn.data('date'));
                        if (createdMoment) {
                            if (createdMoment.isSameOrAfter(weekStart) && createdMoment.isSameOrBefore(now)) {
                                thisWeekTotal += 1;
                                if (isCompleted) {
                                    thisWeekCompleted += 1;
                                    thisWeekRevenue += rowRevenue;
                                }
                            } else if (createdMoment.isSameOrAfter(prevWeekStart) && createdMoment.isSameOrBefore(prevWeekEnd)) {
                                prevWeekTotal += 1;
                                if (isCompleted) {
                                    prevWeekCompleted += 1;
                                    prevWeekRevenue += rowRevenue;
                                }
                            }
                        }
                    });

                    setStatValueByLabel('Total Transactions', totalTransactions.toLocaleString());
                    setStatValueByLabel('Completed Transactions', completedTransactions.toLocaleString());
                    setStatValueByLabel('Total Revenue', '$' + totalRevenue.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                    setStatValueByLabel('Pending Fee', '$' + pendingFee.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                    setStatValueByLabel('Total Guests', totalGuests.toLocaleString());
                    setStatValueByLabel('Pending Amount', '$' + pendingAmount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                    setStatValueByLabel('Payout Amount', '$' + payoutAmount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                    setStatValueByLabel('Total Earning', '$' + totalEarning.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

                    setTrendByLabel('Total Transactions', thisWeekTotal, prevWeekTotal);
                    setTrendByLabel('Completed Transactions', thisWeekCompleted, prevWeekCompleted);
                    setTrendByLabel('Total Revenue', thisWeekRevenue, prevWeekRevenue);
                }

                if (table) {
                    table.on('draw', function() {
                        applyCheckedStateToVisibleRows();
                        updateSelectionUi();
                        updateTotal();
                        updateDashboardCardsFromFilteredRows();
                    });
                    updateTotal();
                    updateSelectionUi();
                    updateDashboardCardsFromFilteredRows();
                }

                // ── AJAX DYNAMIC FILTERING ───────────────────────────────────────────
                const filterSelectors = ['#websiteFilter', '#typeFilter', '#affiliateFilter', '#statusFilter', '#reservationFilter'];
                let filterDebounceTimer = null;
                let isFilteringInProgress = false;

                function getFilterUrl() {
                    const params = new URLSearchParams();
                    
                    if ($('#websiteFilter').length) params.append('website', $('#websiteFilter').val());
                    if ($('#typeFilter').length) params.append('type', $('#typeFilter').val());
                    if ($('#affiliateFilter').length) params.append('affiliate', $('#affiliateFilter').val());
                    if ($('#statusFilter').length) params.append('status', $('#statusFilter').val());
                    if ($('#reservationFilter').length) params.append('reservation', $('#reservationFilter').val());
                    
                    // Get date range from date picker
                    const dateRangeValue = $('#txnDateRange').val();
                    if (dateRangeValue && dateRangeValue.includes(' - ')) {
                        const [fromStr, toStr] = dateRangeValue.split(' - ');
                        try {
                            const from = new Date(fromStr).toISOString().split('T')[0];
                            const to = new Date(toStr).toISOString().split('T')[0];
                            if (from && to) {
                                params.append('date_from', from);
                                params.append('date_to', to);
                            }
                        } catch (e) {}
                    }
                    
                    if ($('input[name="archived"]').length) {
                        params.append('archived', $('input[name="archived"]').val());
                    }
                    
                    return params.toString();
                }

                function applyFilterAjax() {
                    if (isFilteringInProgress) return;
                    
                    isFilteringInProgress = true;
                    
                    // Show loading state
                    const tbody = $('#txnDataTable tbody');
                    tbody.css('opacity', '0.6').css('pointer-events', 'none');
                    
                    $.ajax({
                        url: '{{ route("admin.transaction.filter-ajax") }}',
                        type: 'POST',
                        data: getFilterUrl(),
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                // Update table rows
                                tbody.html(response.rowsHtml || '');
                                
                                // Update stats
                                if (response.stats) {
                                    // Update pending fee card
                                    $('.txn-stat-card:has(.txn-stat-label:contains("Pending Fee")) .txn-stat-value')
                                        .text('$' + response.stats.pendingCommission);
                                    
                                    // Update available now card
                                    $('.txn-stat-card:has(.txn-stat-label:contains("Available Now")) .txn-stat-value')
                                        .text('$' + response.stats.availableNow);
                                    
                                    // Update lifetime earned card
                                    $('.txn-stat-card:has(.txn-stat-label:contains("Lifetime Earned")) .txn-stat-value')
                                        .text('$' + response.stats.lifetimeEarned);
                                }
                                
                                // Reinitialize DataTable
                                if ($.fn.dataTable.isDataTable('#txnDataTable')) {
                                    $('#txnDataTable').DataTable().destroy();
                                }
                                
                                table = $('#txnDataTable').DataTable({
                                    pageLength: 25,
                                    searching: false,
                                    ordering: true,
                                    paging: false,
                                    info: false,
                                    lengthChange: false,
                                    autoWidth: false,
                                    columnDefs: [
                                        { orderable: false, targets: 0 },
                                        { orderable: false, targets: [0, 15] }
                                    ]
                                });
                                
                                applyCheckedStateToVisibleRows();
                                updateSelectionUi();
                            }
                        },
                        error: function(err) {
                            console.error('Filter error:', err);
                            alert('Error updating filters. Please refresh the page.');
                        },
                        complete: function() {
                            tbody.css('opacity', '1').css('pointer-events', 'auto');
                            isFilteringInProgress = false;
                        }
                    });
                }

                // Bind filter change events to trigger AJAX
                filterSelectors.forEach(function(selector) {
                    if ($(selector).length) {
                        $(selector).on('change', function() {
                            clearTimeout(filterDebounceTimer);
                            filterDebounceTimer = setTimeout(applyFilterAjax, 400);
                        });
                    }
                });

                // Bind date range picker close event if it exists
                if (window.fp && window.fp.config && typeof window.fp.config.onClose === 'function') {
                    const originalOnClose = window.fp.config.onClose;
                    window.fp.config.onClose = function(selectedDates, dateStr, instance) {
                        if (typeof originalOnClose === 'function') {
                            originalOnClose(selectedDates, dateStr, instance);
                        }
                        clearTimeout(filterDebounceTimer);
                        filterDebounceTimer = setTimeout(applyFilterAjax, 400);
                    };
                }

            }); // end document.ready
            </script>

            <style>tr[data-row-id]{cursor:pointer;}</style>
            <script>
            // Preserve the table's horizontal scroll position when the details modal
            // opens/closes (Bootstrap returns focus to the far-right view button on close,
            // which would otherwise scroll the table all the way to the right).
            (function() {
                const viewModal = document.getElementById('viewTransactionModal');
                const scrollBox = document.querySelector('tr[data-row-id]')
                    ? document.querySelector('tr[data-row-id]').closest('.table-responsive')
                    : null;
                if (viewModal && scrollBox) {
                    let savedLeft = 0;
                    viewModal.addEventListener('show.bs.modal', function() { savedLeft = scrollBox.scrollLeft; });
                    viewModal.addEventListener('hidden.bs.modal', function() {
                        requestAnimationFrame(function() { scrollBox.scrollLeft = savedLeft; });
                    });
                }
            })();

            $(document).on('click', '.view-btn', function() {
                const transactionId = $(this).data('id');
                var esc = window.txnEsc || function(value) {
                    return String(value == null ? '' : value)
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/"/g, '&quot;')
                        .replace(/'/g, '&#39;');
                };
                var money = function(v){
                    var n = parseFloat(v || 0);
                    return '$' + (isNaN(n) ? 0 : n).toFixed(2);
                };

                var formatPickupTime = function(timeValue) {
                    var raw = String(timeValue || '').trim();
                    if (!raw || raw.indexOf(':') === -1) {
                        return raw || 'N/A';
                    }
                    if (/\b(?:AM|PM)\b/i.test(raw)) {
                        return raw.toUpperCase();
                    }
                    var timeParts = raw.split(':');
                    var hours = parseInt(timeParts[0], 10);
                    var minutes = timeParts[1] || '00';
                    if (isNaN(hours)) {
                        return raw;
                    }
                    var ampm = hours >= 12 ? 'PM' : 'AM';
                    hours = hours % 12 || 12;
                    return (hours < 10 ? '0' : '') + hours + ':' + minutes + ' ' + ampm;
                };

                window.formatDateUS = window.formatDateUS || function(dateValue) {
                    var raw = String(dateValue || '').trim();
                    if (!raw) {
                        return 'N/A';
                    }

                    var match = raw.match(/^(\d{4})-(\d{2})-(\d{2})/);
                    if (match) {
                        return match[2] + '/' + match[3] + '/' + match[1];
                    }

                    var parsed = new Date(raw);
                    if (!isNaN(parsed.getTime())) {
                        var month = String(parsed.getMonth() + 1).padStart(2, '0');
                        var day = String(parsed.getDate()).padStart(2, '0');
                        var year = parsed.getFullYear();
                        return month + '/' + day + '/' + year;
                    }

                    return raw;
                };

                window.txnEsc = window.txnEsc || function(value) {
                    return String(value == null ? '' : value)
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/"/g, '&quot;')
                        .replace(/'/g, '&#39;');
                };

                window.parseJsonLike = window.parseJsonLike || function(value) {
                    if (value == null || value === '') {
                        return null;
                    }
                    if (Array.isArray(value) || typeof value === 'object') {
                        return value;
                    }
                    try {
                        return JSON.parse(value);
                    } catch (e) {
                        return null;
                    }
                };

                window.summarizePackageItems = window.summarizePackageItems || function(cartItems) {
                    var items = [];
                    var totalQuantity = 0;
                    var totalAddons = 0;
                    var summaryParts = [];
                    var addonSummaryParts = [];

                    (Array.isArray(cartItems) ? cartItems : []).forEach(function(item) {
                        if (!item || typeof item !== 'object') {
                            return;
                        }

                        var packageName = String(item.package_name || item.packageName || item.pkgName || 'Unknown Package').trim();
                        var quantity = Math.max(1, parseInt(item.guests || item.quantity || 1, 10) || 1);
                        var packageType = String(item.package_type || item.type || item.packageType || '').toLowerCase();
                        var unitPrice = parseFloat(item.unit_price || 0) || 0;
                        var lineTotal = parseFloat(item.line_total || (unitPrice * quantity) || 0) || 0;
                        var addons = Array.isArray(item.addons) ? item.addons : [];
                        var addonLabels = [];

                        addons.forEach(function(addon) {
                            if (!addon || typeof addon !== 'object') {
                                return;
                            }

                            var addonName = String(addon.name || 'Add-on').trim();
                            if (!addonName) {
                                return;
                            }

                            var addonQty = Math.max(1, parseInt(addon.quantity || 1, 10) || 1);
                            var addonPrice = parseFloat(addon.price || 0) || 0;
                            totalAddons += 1;
                            addonLabels.push(addonName + ' x' + addonQty + (addonPrice > 0 ? ' (' + '$' + addonPrice.toFixed(2) + ')' : ''));
                            addonSummaryParts.push(packageName + ': ' + addonName + ' x' + addonQty + (addonPrice > 0 ? ' (' + '$' + addonPrice.toFixed(2) + ')' : ''));
                        });

                        totalQuantity += quantity;
                        summaryParts.push(packageName + ' x' + quantity + (packageType === 'ticket' ? ' tickets' : ' guests'));
                        items.push({
                            name: packageName,
                            quantity: quantity,
                            packageType: packageType || 'package',
                            unitPrice: unitPrice,
                            lineTotal: lineTotal,
                            addonCount: addons.length,
                            addonLabels: addonLabels,
                        });
                    });

                    return {
                        items: items,
                        totalQuantity: totalQuantity,
                        totalAddons: totalAddons,
                        addonSummaryText: addonSummaryParts.length ? addonSummaryParts.join('; ') : '',
                        summaryText: summaryParts.length ? summaryParts.join('; ') : ''
                    };
                };

                var status = $(this).data('status');
                var statusText = 'Unknown';
                var statusClass = 'txn-status-unknown';
                if (status == 1 || status === 'Completed' || status === 'Approved') {
                    statusText = 'Payment Completed';
                    statusClass = 'txn-status-completed';
                } else if (status == 0 || status === 'Canceled' || status === '0') {
                    statusText = 'Payment Canceled';
                    statusClass = 'txn-status-canceled';
                } else if (status == 2 || status === 'Refunded') {
                    statusText = 'Payment Refunded';
                    statusClass = 'txn-status-refunded';
                }

                var affiliateName = String($(this).data('affiliate_name') || '').trim();
                var entertainerName = String($(this).data('entertainer_name') || '').trim();
                var checkoutEventName = String($(this).data('event_id') || '').trim();
                var checkoutContextLabel = checkoutEventName ? ('Event Checkout - ' + checkoutEventName) : 'General Checkout';
                var source = 'Direct';
                if (affiliateName) source = 'Promoter - ' + affiliateName;
                else if (entertainerName) source = 'Entertainer - ' + entertainerName;

                var affPct = parseFloat($(this).data('affiliate_commission_percentage')) || 0;
                var affAmt = parseFloat($(this).data('affiliate_commission_amount')) || 0;
                var affStatus = String($(this).data('affiliate_commission_status') || '').trim();
                var affHold = String($(this).data('affiliate_commission_hold_until') || '').trim();
                var entPct = parseFloat($(this).data('entertainer_commission_percentage')) || 0;
                var entAmt = parseFloat($(this).data('entertainer_commission_amount')) || 0;
                var entStatus = String($(this).data('entertainer_commission_status') || '').trim();
                var entHold = String($(this).data('entertainer_commission_hold_until') || '').trim();
                var transactionType = String($(this).data('type') || '').trim().toLowerCase();

                var menCount = parseInt($(this).data('men'), 10);
                if (isNaN(menCount)) menCount = 0;
                var womenCount = parseInt($(this).data('women'), 10);
                if (isNaN(womenCount)) womenCount = 0;
                var guestCount = parseInt($(this).data('package_number_of_guest'), 10);
                if (isNaN(guestCount)) guestCount = 0;

                if (transactionType === 'reservation' && guestCount <= 0) {
                    guestCount = Math.max(menCount + womenCount, 0);
                }

                var guestsDisplay = String(guestCount);
                if (transactionType === 'reservation') {
                    guestsDisplay += ' (M: ' + menCount + ', W: ' + womenCount + ')';
                }

                var businessInfo = [
                    $(this).data('business_company'),
                    $(this).data('business_vat'),
                    $(this).data('business_address')
                ].filter(function(v){ return String(v || '').trim() !== ''; }).join(' | ');

                var normalizeField = function(value) {
                    var text = String(value == null ? '' : value).trim();
                    var lower = text.toLowerCase();
                    if (lower === 'null' || lower === 'undefined') {
                        return '';
                    }
                    return text;
                };

                var shippingSameAsBillingRaw = normalizeField($(this).data('shipping_same_as_billing')).toLowerCase();
                var shippingSameAsBilling = shippingSameAsBillingRaw === '1' || shippingSameAsBillingRaw === 'true' || shippingSameAsBillingRaw === 'yes';
                var shippingFirstName = normalizeField($(this).data('shipping_first_name'));
                var shippingLastName = normalizeField($(this).data('shipping_last_name'));
                var shippingName = [shippingFirstName, shippingLastName].filter(Boolean).join(' ');
                var shippingPhone = normalizeField($(this).data('shipping_phone'));
                var shippingEmail = normalizeField($(this).data('shipping_email'));
                var shippingAddress = [
                    normalizeField($(this).data('shipping_address')),
                    normalizeField($(this).data('shipping_city')),
                    normalizeField($(this).data('shipping_state')),
                    normalizeField($(this).data('shipping_zip_code'))
                ].filter(Boolean).join(', ');
                var shippingCountry = normalizeField($(this).data('shipping_country'));
                var hasShippingData = shippingSameAsBilling || [shippingName, shippingPhone, shippingEmail, shippingAddress, shippingCountry].some(function(v) {
                    return normalizeField(v) !== '';
                });

                var requiresTransportation = String($(this).data('requires_transportation') || '').toLowerCase();
                requiresTransportation = requiresTransportation === '1' || requiresTransportation === 'true' || requiresTransportation === 'yes';
                var hasTransportationDetails = [
                    $(this).data('transportation_pickup_time'),
                    $(this).data('transportation_arrival_time'),
                    $(this).data('transportation_address'),
                    $(this).data('transportation_phone'),
                    $(this).data('transportation_note')
                ].some(function(v){ return String(v || '').trim() !== ''; });
                var transportMode = 'Not Required';
                if (requiresTransportation) {
                    transportMode = hasTransportationDetails ? 'Pickup Requested' : 'Self Drive Selected';
                }

                var amountPaid = parseFloat($(this).data('total') || 0);
                var totalAmount = parseFloat($(this).data('subtotal') || 0);
                var dueAmount = parseFloat($(this).data('due') || 0);
                var checkedInStatus = String($(this).data('checked_in_status') || '').toLowerCase();
                checkedInStatus = checkedInStatus === '1' || checkedInStatus === 'true' || checkedInStatus === 'yes';
                var checkedInAtPacific = String($(this).data('checked_in_at_pacific') || '').trim();
                var transportationArrivalRaw = String($(this).data('transportation_arrival_time') || '').trim();
                var transportationArrivalDisplay = formatPickupTime(transportationArrivalRaw);
                if ((transportationArrivalDisplay === 'N/A' || transportationArrivalDisplay === '') && checkedInStatus && checkedInAtPacific) {
                    transportationArrivalDisplay = checkedInAtPacific + ' (Check-In)';
                }

                var rawCartItems = $(this).data('cart-items') || [];
                var parsedCartItems = Array.isArray(rawCartItems) ? rawCartItems : (window.parseJsonLike ? window.parseJsonLike(rawCartItems) : []);
                var normalizeCartItems = function(value) {
                    if (Array.isArray(value)) {
                        return value;
                    }
                    if (!value || typeof value !== 'object') {
                        return [];
                    }
                    if (Array.isArray(value.items)) {
                        return value.items;
                    }
                    if (Array.isArray(value.cart_items)) {
                        return value.cart_items;
                    }
                    if (Array.isArray(value.cartItems)) {
                        return value.cartItems;
                    }
                    var objectValues = Object.values(value || {});
                    if (objectValues.length && objectValues.every(function(v) { return v && typeof v === 'object'; })) {
                        return objectValues;
                    }
                    return [];
                };
                var cartItems = normalizeCartItems(parsedCartItems);
                var breakdownData = $(this).data('breakdown');
                if (!breakdownData || typeof breakdownData !== 'object') {
                    breakdownData = null;
                }

                var purchaseItems = [];
                if (breakdownData && Array.isArray(breakdownData.items) && breakdownData.items.length) {
                    purchaseItems = breakdownData.items.map(function(rawItem) {
                        if (!rawItem || typeof rawItem !== 'object') {
                            return null;
                        }

                        var qty = Math.max(1, parseInt(rawItem.guests || rawItem.quantity || 1, 10) || 1);
                        var packageType = String(rawItem.package_type || rawItem.type || rawItem.packageType || '').toLowerCase() || 'package';
                        var unitPrice = parseFloat(rawItem.unit_price);
                        unitPrice = isNaN(unitPrice) ? null : unitPrice;
                        var packageSubtotal = parseFloat(rawItem.package_subtotal);
                        packageSubtotal = isNaN(packageSubtotal) ? null : packageSubtotal;
                        var lineTotal = parseFloat(rawItem.line_total);
                        lineTotal = isNaN(lineTotal) ? null : lineTotal;

                        var addons = Array.isArray(rawItem.addons) ? rawItem.addons.map(function(addon) {
                            if (!addon || typeof addon !== 'object') {
                                return null;
                            }
                            var addonName = String(addon.name || '').trim();
                            if (!addonName) {
                                return null;
                            }
                            var addonQty = Math.max(1, parseInt(addon.qty || addon.quantity || 1, 10) || 1);
                            var addonUnit = parseFloat(addon.unit_price);
                            addonUnit = isNaN(addonUnit) ? null : addonUnit;
                            var addonLine = parseFloat(addon.price);
                            addonLine = isNaN(addonLine) ? (addonUnit == null ? null : addonUnit * addonQty) : addonLine;
                            return {
                                name: addonName,
                                quantity: addonQty,
                                unitPrice: addonUnit,
                                lineTotal: addonLine
                            };
                        }).filter(Boolean) : [];

                        return {
                            name: String(rawItem.package_name || rawItem.packageName || rawItem.name || 'Package').trim(),
                            quantity: qty,
                            packageType: packageType,
                            unitPrice: unitPrice,
                            packageSubtotal: packageSubtotal,
                            lineTotal: lineTotal,
                            addons: addons
                        };
                    }).filter(Boolean);
                }

                if (!purchaseItems.length) {
                    var summarized = window.summarizePackageItems ? window.summarizePackageItems(cartItems) : { items: [] };
                    purchaseItems = (summarized.items || []).map(function(item) {
                        var sourceCartItem = cartItems.find(function(cartItem) {
                            var cartName = String(cartItem && (cartItem.package_name || cartItem.packageName || cartItem.pkgName || '')).trim().toLowerCase();
                            return cartName && cartName === String(item.name || '').trim().toLowerCase();
                        }) || {};
                        var addons = Array.isArray(sourceCartItem.addons) ? sourceCartItem.addons.map(function(addon) {
                            if (!addon || typeof addon !== 'object') {
                                return null;
                            }
                            var addonName = String(addon.name || '').trim();
                            if (!addonName) {
                                return null;
                            }
                            var addonQty = Math.max(1, parseInt(addon.qty || addon.quantity || 1, 10) || 1);
                            var addonUnit = parseFloat(addon.unit_price);
                            addonUnit = isNaN(addonUnit) ? null : addonUnit;
                            var addonLine = parseFloat(addon.price);
                            addonLine = isNaN(addonLine) ? (addonUnit == null ? null : addonUnit * addonQty) : addonLine;
                            return {
                                name: addonName,
                                quantity: addonQty,
                                unitPrice: addonUnit,
                                lineTotal: addonLine
                            };
                        }).filter(Boolean) : [];
                        return {
                            name: item.name,
                            quantity: item.quantity,
                            packageType: item.packageType || 'package',
                            unitPrice: typeof item.unitPrice === 'number' ? item.unitPrice : null,
                            packageSubtotal: typeof item.lineTotal === 'number' ? item.lineTotal : null,
                            lineTotal: typeof item.lineTotal === 'number' ? item.lineTotal : null,
                            addons: addons
                        };
                    });
                }

                var frontPath = String($(this).data('checkin_photo_front') || '').trim();
                var backPath = String($(this).data('checkin_photo_back') || '').trim();
                var frontPhotoUrl = frontPath ? '{{ route("admin.transaction.id-photo", ["transactionId" => "ID", "side" => "front"]) }}'.replace('ID', transactionId) : '';
                var backPhotoUrl = backPath ? '{{ route("admin.transaction.id-photo", ["transactionId" => "ID", "side" => "back"]) }}'.replace('ID', transactionId) : '';

                window.txnDetailRow = window.txnDetailRow || function(label, value) {
                    var safeEsc = window.txnEsc || esc;
                    return '<div class="txn-detail-row"><span class="txn-detail-label">' + safeEsc(label) + ':</span><span class="txn-detail-value">' + safeEsc(value) + '</span></div>';
                };
                var baseRow = window.txnDetailRow;
                var pdfSections = [];
                var currentPdfSection = null;
                var packageItemsForPdf = [];
                var beginPdfSection = function(name) {
                    currentPdfSection = { name: String(name || 'Details'), rows: [] };
                    pdfSections.push(currentPdfSection);
                };
                var pushPdfRow = function(label, value) {
                    if (!currentPdfSection) {
                        beginPdfSection('Details');
                    }
                    currentPdfSection.rows.push([
                        String(label == null ? '' : label),
                        String(value == null || value === '' ? 'N/A' : value)
                    ]);
                };
                var row = function(label, value) {
                    pushPdfRow(label, value);
                    return baseRow(label, value);
                };
                var line = function(label, value, opts) {
                    pushPdfRow(label, value);
                    opts = opts || {};
                    var valueColor = opts.color || '#e0e7ff';
                    var weight = opts.weight || '600';
                    var border = opts.border ? 'border-top:1px solid rgba(255,255,255,0.12);padding-top:10px;margin-top:8px;' : '';
                    return '<div style="display:flex;justify-content:space-between;gap:16px;margin-bottom:8px;' + border + '">'
                        + '<span style="color:#94a3b8;">' + esc(label) + '</span>'
                        + '<span style="color:' + valueColor + ';font-weight:' + weight + ';white-space:nowrap;">' + esc(value) + '</span></div>';
                };

                var html = '';

                beginPdfSection('Overview');
                pushPdfRow('Transaction', $(this).data('transaction_id') || transactionId);
                pushPdfRow('Status', statusText);
                pushPdfRow('Date', $(this).data('date') || 'N/A');
                pushPdfRow('Website', $(this).data('website_id') || 'N/A');
                if (checkedInStatus) {
                    pushPdfRow('Checked In', checkedInAtPacific || 'Yes');
                }

                html += '<div class="txn-detail-card">';
                html += '<div class="d-flex flex-wrap align-items-center justify-content-between gap-2">';
                html += '<div class="txn-detail-title mb-0">Transaction #' + esc($(this).data('transaction_id') || transactionId) + '</div>';
                html += '<span class="txn-status-pill ' + statusClass + '">' + esc(statusText) + '</span>';
                html += '</div>';
                html += '<div style="margin-top:8px;color:#94a3b8;font-size:0.82rem;">' + esc($(this).data('date') || '') + ' | ' + esc($(this).data('website_id') || '') + '</div>';
                if (checkedInStatus) {
                    html += '<div style="margin-top:8px;color:#86efac;font-size:0.82rem;font-weight:700;">Checked In' + (checkedInAtPacific ? ' | ' + esc(checkedInAtPacific) : '') + '</div>';
                }
                html += '</div>';

                html += '<div class="row g-3">';

                html += '<div class="col-md-6">';
                html += '<div class="txn-detail-card">';
                html += '<div class="txn-detail-title">Purchase Summary</div>';
                beginPdfSection('Purchase Summary');
                pushPdfRow('Guests', guestsDisplay);
                if (purchaseItems.length) {
                    purchaseItems.forEach(function(item, index) {
                        var qtyLabel = String(item.quantity) + ' ' + (item.packageType === 'ticket' ? 'tickets' : 'guests');
                        var itemUnitText = item.unitPrice == null ? 'N/A' : money(item.unitPrice);
                        var itemTotalText = item.packageSubtotal == null ? 'N/A' : money(item.packageSubtotal);
                        var addonLineItems = [];

                        pushPdfRow('Package ' + (index + 1), (item.name || 'Package') + ' | ' + qtyLabel + ' | Unit: ' + itemUnitText + ' | Total: ' + itemTotalText);

                        html += '<div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.08);border-radius:8px;padding:12px;margin-bottom:' + (index === purchaseItems.length - 1 ? '0' : '10px') + ';">';
                        html += '<div style="display:flex;justify-content:space-between;gap:12px;align-items:flex-start;margin-bottom:8px;">';
                        html += '<div style="min-width:0;">';
                        html += '<div style="font-weight:700;color:#e0e7ff;">' + esc(item.name || 'Package') + '</div>';
                        html += '<div style="font-size:0.8rem;color:#94a3b8;margin-top:4px;">' + esc(qtyLabel) + '</div>';
                        html += '</div>';
                        html += '<div style="text-align:right;flex-shrink:0;">';
                        html += '<div style="display:inline-block;background:' + (item.packageType === 'ticket' ? 'rgba(245,158,11,0.18)' : 'rgba(124,58,237,0.18)') + ';color:' + (item.packageType === 'ticket' ? '#fbbf24' : '#a5b4fc') + ';border:1px solid ' + (item.packageType === 'ticket' ? 'rgba(245,158,11,0.3)' : 'rgba(124,58,237,0.28)') + ';border-radius:999px;padding:3px 10px;font-size:0.72rem;font-weight:700;letter-spacing:0.04em;text-transform:uppercase;">' + esc(item.packageType === 'ticket' ? 'Ticket Package' : 'Guest Package') + '</div>';
                        html += '</div>';
                        html += '</div>';
                        html += '<div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:8px;">';
                        html += '<div style="background:rgba(15,23,42,0.45);border:1px solid rgba(255,255,255,0.06);border-radius:8px;padding:10px;">';
                        html += '<div style="font-size:0.72rem;color:#94a3b8;margin-bottom:4px;">Quantity</div>';
                        html += '<div style="font-weight:700;color:#fbbf24;">' + esc(qtyLabel) + '</div>';
                        html += '</div>';
                        html += '<div style="background:rgba(15,23,42,0.45);border:1px solid rgba(255,255,255,0.06);border-radius:8px;padding:10px;">';
                        html += '<div style="font-size:0.72rem;color:#94a3b8;margin-bottom:4px;">Unit Price</div>';
                        html += '<div style="font-weight:700;color:#e0e7ff;">' + (item.unitPrice == null ? 'N/A' : money(item.unitPrice)) + '</div>';
                        html += '</div>';
                        html += '<div style="background:rgba(15,23,42,0.45);border:1px solid rgba(255,255,255,0.06);border-radius:8px;padding:10px;">';
                        html += '<div style="font-size:0.72rem;color:#94a3b8;margin-bottom:4px;">Package Total</div>';
                        html += '<div style="font-weight:700;color:#34d399;">' + (item.packageSubtotal == null ? 'N/A' : money(item.packageSubtotal)) + '</div>';
                        html += '</div>';
                        html += '</div>';
                        if (Array.isArray(item.addons) && item.addons.length) {
                            html += '<div style="margin-top:10px;border-left:2px solid rgba(251,191,36,0.28);padding-left:12px;">';
                            html += '<div style="color:#94a3b8;font-size:0.8rem;margin-bottom:6px;font-weight:600;">Add-ons</div>';
                            item.addons.forEach(function(addon) {
                                var addonText = addon.name + ' x' + addon.quantity;
                                if (addon.unitPrice != null && addon.lineTotal != null) {
                                    addonText += ' @ ' + money(addon.unitPrice) + ' = ' + money(addon.lineTotal);
                                } else if (addon.lineTotal != null) {
                                    addonText += ' = ' + money(addon.lineTotal);
                                }
                                addonLineItems.push(addonText);
                                html += '<div style="color:#e0e7ff;font-size:0.85rem;margin-bottom:4px;">• ' + esc(addonText) + '</div>';
                            });
                            html += '</div>';
                        }

                        packageItemsForPdf.push({
                            name: String(item.name || 'Package'),
                            quantity: qtyLabel,
                            unitPrice: itemUnitText,
                            total: itemTotalText,
                            addons: addonLineItems
                        });

                        if (addonLineItems.length) {
                            pushPdfRow('Add-ons ' + (index + 1), addonLineItems.join('; '));
                        }

                        html += '</div>';
                    });
                } else {
                    html += '<div style="color:#94a3b8;font-size:0.9rem;">No package or add-on details available.</div>';
                    pushPdfRow('Package Details', 'No package or add-on details available');
                }
                html += '</div>';

                html += '<div class="txn-detail-card">';
                html += '<div class="txn-detail-title">Payment & Charges</div>';
                beginPdfSection('Payment & Charges');
                if (breakdownData && typeof breakdownData === 'object') {
                    html += line('Subtotal', money(breakdownData.items_subtotal));
                    if (parseFloat(breakdownData.promo_discount) > 0) {
                        html += line('Discounted Amount', '-' + money(breakdownData.promo_discount), { color: '#34d399' });
                    } else {
                        html += line('Discounted Amount', money(0));
                    }
                    if (!(breakdownData.service_charge && breakdownData.service_charge.enabled)) {
                        html += line('Service Charge', money(0));
                    }
                    if (breakdownData.service_charge && breakdownData.service_charge.enabled) {
                        html += line('Service Charge', money(breakdownData.service_charge.amount));
                    }
                    if (!(breakdownData.gratuity && breakdownData.gratuity.enabled)) {
                        html += line('Gratuity', money(0));
                    }
                    if (breakdownData.gratuity && breakdownData.gratuity.enabled) {
                        html += line('Gratuity', money(breakdownData.gratuity.amount));
                    }
                    if (breakdownData.sales_tax && breakdownData.sales_tax.enabled) {
                        html += line(breakdownData.sales_tax.name || 'Sales Tax', money(breakdownData.sales_tax.amount));
                    }
                    if (!(breakdownData.processing_fee && breakdownData.processing_fee.enabled)) {
                        html += line('Processing Fee', money(0));
                    }
                    if (breakdownData.processing_fee && breakdownData.processing_fee.enabled) {
                        html += line('Processing Fee', money(breakdownData.processing_fee.amount));
                    }
                    if (breakdownData.refundable && breakdownData.refundable.enabled && parseFloat(breakdownData.refundable.amount) > 0) {
                        html += line('Non Refundable Deposit', money(breakdownData.refundable.amount));
                    } else {
                        html += line('Non Refundable Deposit', money(0));
                    }
                    html += line('Grand Total', money(breakdownData.grand_total), { color: '#fbbf24', weight: '700', border: true });
                    if (breakdownData.refundable && breakdownData.refundable.enabled && parseFloat(breakdownData.refundable.amount) > 0) {
                        html += line((breakdownData.refundable.name || 'Non-refundable Deposit') + ' (incl. in total)', money(breakdownData.refundable.amount), { color: '#94a3b8', weight: '500' });
                    }
                    html += line('Amount Paid', money(breakdownData.amount_paid_now), { color: '#34d399', weight: '700' });
                    if (parseFloat(breakdownData.remaining_due) > 0) {
                        html += line('Amount Due', money(breakdownData.remaining_due), { color: '#ef4444', weight: '700' });
                    } else {
                        html += line('Amount Due', money(0), { color: '#ef4444', weight: '700' });
                    }
                } else {
                    html += row('Promo Code', $(this).data('promo_code') || 'N/A');
                    html += row('Discounted Amount', money($(this).data('discounted_amount') || 0));
                    html += row('Subtotal', money(totalAmount));
                    html += row('Gratuity', money($(this).data('gratuity') || 0));
                    html += row('Service Charge', money($(this).data('service_charge') || 0));
                    html += row('Processing Fee', money($(this).data('processing_fee') || 0));
                    html += row('Non Refundable Deposit', money($(this).data('refundable') || 0));
                    html += row('Amount Paid', money(amountPaid));
                    html += row('Amount Due', money(dueAmount));
                }
                html += row('Card Brand', $(this).data('payment_card_brand') || 'N/A');
                html += row('Card Last 4', $(this).data('payment_card_last4') || 'N/A');
                html += '</div>';

                html += '<div class="txn-detail-card">';
                html += '<div class="txn-detail-title">Payment Contact</div>';
                beginPdfSection('Payment Contact');
                html += row('Payment Name', ($(this).data('payment_first_name') || '') + ' ' + ($(this).data('payment_last_name') || ''));
                html += row('Payment Email', $(this).data('payment_email') || '');
                html += row('Payment Phone', $(this).data('payment_phone') || 'N/A');
                html += row('Payment Address', [$(this).data('payment_address'), $(this).data('payment_city'), $(this).data('payment_state'), $(this).data('payment_zip_code')].filter(Boolean).join(', '));
                html += row('Payment Country', $(this).data('payment_country') || 'N/A');
                html += row('Payment DOB', formatDateUS($(this).data('payment_dob')));
                html += '</div>';

                if (hasShippingData) {
                    html += '<div class="txn-detail-card">';
                    html += '<div class="txn-detail-title">Shipping</div>';
                    beginPdfSection('Shipping');
                    html += row('Shipping Same As Billing', shippingSameAsBilling ? 'Yes' : 'No');
                    if (shippingName) {
                        html += row('Shipping Name', shippingName);
                    }
                    if (shippingEmail) {
                        html += row('Shipping Email', shippingEmail);
                    }
                    if (shippingPhone) {
                        html += row('Shipping Phone', shippingPhone);
                    }
                    if (shippingAddress) {
                        html += row('Shipping Address', shippingAddress);
                    }
                    if (shippingCountry) {
                        html += row('Shipping Country', shippingCountry);
                    }
                    html += '</div>';
                }

                html += '</div>';

                html += '<div class="col-md-6">';
                html += '<div class="txn-detail-card">';
                html += '<div class="txn-detail-title">Guest & Reservation</div>';
                beginPdfSection('Guest & Reservation');
                html += row('Guest', ($(this).data('package_first_name') || '') + ' ' + ($(this).data('package_last_name') || ''));
                html += row('Email', $(this).data('package_email') || '');
                html += row('Phone', $(this).data('package_phone') || '');
                html += row('DOB', formatDateUS($(this).data('package_dob')));
                html += row('Date Of Use', formatDateUS($(this).data('package_use_date')));
                html += row('Guests', guestsDisplay);
                html += row('Host Name', $(this).data('host_name') || 'N/A');
                html += row('Notes', $(this).data('package_note') || 'N/A');
                html += '</div>';

                html += '<div class="txn-detail-card">';
                html += '<div class="txn-detail-title">Transportation</div>';
                beginPdfSection('Transportation');
                html += row('Transport Mode', transportMode);
                html += row('Pickup Time', formatPickupTime($(this).data('transportation_pickup_time')));
                html += row('Arrival Time', transportationArrivalDisplay);
                html += row('Transport Phone', $(this).data('transportation_phone') || 'N/A');
                html += row('Transport Address', $(this).data('transportation_address') || 'N/A');
                html += row('Transport Note', $(this).data('transportation_note') || 'N/A');
                html += '</div>';

                html += '<div class="txn-detail-card">';
                html += '<div class="txn-detail-title">Source & Fees</div>';
                beginPdfSection('Source & Fees');
                html += row('Source', source);
                html += row('Type', $(this).data('type') || 'N/A');
                html += row('Checkout Context', checkoutContextLabel);
                html += row('Total Fee', money($(this).data('total_commission') || 0));
                if (affiliateName || affAmt > 0 || affPct > 0 || affStatus) {
                    html += row('Promoter Fee', (affiliateName || 'N/A') + ' | ' + affPct.toFixed(2) + '% | ' + money(affAmt) + (affStatus ? (' | ' + affStatus.toUpperCase()) : '') + (affHold ? (' | ' + affHold) : ''));
                }
                if (entertainerName || entAmt > 0 || entPct > 0 || entStatus) {
                    html += row('Entertainer Fee', (entertainerName || 'N/A') + ' | ' + entPct.toFixed(2) + '% | ' + money(entAmt) + (entStatus ? (' | ' + entStatus.toUpperCase()) : '') + (entHold ? (' | ' + entHold) : ''));
                }
                html += '</div>';

                html += '<div class="txn-detail-card">';
                html += '<div class="txn-detail-title">Audit & Business</div>';
                beginPdfSection('Audit & Business');
                html += row('Check-In Status', checkedInStatus ? 'Checked In' : 'Not Checked In');
                html += row('Check-In Time (PT)', checkedInAtPacific || 'N/A');
                html += row('Terms Accepted', 'Yes');
                html += row('SMS Accepted', 'Yes');
                html += row('Business Info', businessInfo || 'N/A');
                html += row('IP Address', $(this).data('ip_address') || '');
                html += '</div>';
                html += '</div>';
                html += '</div>';

                if (frontPhotoUrl || backPhotoUrl) {
                    html += '<div class="txn-detail-card mt-3">';
                    html += '<div class="txn-detail-title">Check-In ID Photos</div>';
                    html += '<div class="row g-3">';
                    if (frontPhotoUrl) {
                        html += '<div class="col-md-6"><div style="color:#86efac;font-size:12px;margin-bottom:6px;">Front Of ID</div><img src="' + frontPhotoUrl + '" style="width:100%;border-radius:8px;border:1px solid #334155;max-height:280px;object-fit:cover;cursor:pointer;" onclick="window.open(this.src, \"_blank\")"></div>';
                    }
                    if (backPhotoUrl) {
                        html += '<div class="col-md-6"><div style="color:#93c5fd;font-size:12px;margin-bottom:6px;">Back Of ID</div><img src="' + backPhotoUrl + '" style="width:100%;border-radius:8px;border:1px solid #334155;max-height:280px;object-fit:cover;cursor:pointer;" onclick="window.open(this.src, \"_blank\")"></div>';
                    }
                    html += '</div>';
                    html += '</div>';
                }

                $('#transactionDetailsContent').html(html);

                $('#viewTransactionModal').data('pdfPayload', {
                    title: 'Transaction #' + String($(this).data('transaction_id') || transactionId),
                    status: statusText,
                    meta: String($(this).data('date') || '') + ' | ' + String($(this).data('website_id') || ''),
                    sections: pdfSections,
                    packageItems: packageItemsForPdf,
                    photoLinks: [frontPhotoUrl, backPhotoUrl].filter(function(link) {
                        return String(link || '').trim() !== '';
                    })
                });
            });
            </script>

            <script>
            $(document).on('click', '#download-transaction-pdf', function() {
                var source = document.getElementById('transactionDetailsContent');
                if (!source || !source.innerHTML.trim()) {
                    alert('No transaction details available to export.');
                    return;
                }
                var payload = $('#viewTransactionModal').data('pdfPayload') || null;

                var button = this;
                var originalHtml = button.innerHTML;
                button.disabled = true;
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating PDF...';

                try {
                    var jsPDFRef = window.jspdf && window.jspdf.jsPDF ? window.jspdf.jsPDF : null;
                    if (!jsPDFRef || typeof jsPDFRef !== 'function' || typeof window.jspdf.jsPDF.API.autoTable !== 'function') {
                        throw new Error('jsPDF AutoTable is not available');
                    }

                    var doc = new jsPDFRef({ unit: 'mm', format: 'a4', orientation: 'portrait' });
                    var margin = 7;
                    var pageWidth = doc.internal.pageSize.getWidth();
                    var contentWidth = pageWidth - (margin * 2);

                    var titleNode = source.querySelector('.txn-detail-title.mb-0');
                    var statusNode = source.querySelector('.txn-status-pill');
                    var metaNode = titleNode && titleNode.closest('.txn-detail-card')
                        ? titleNode.closest('.txn-detail-card').querySelector('div[style*="margin-top:8px"]')
                        : null;

                    var titleText = payload && payload.title ? String(payload.title) : (titleNode ? titleNode.textContent.trim() : 'Transaction Details');
                    var statusText = payload && payload.status ? String(payload.status) : (statusNode ? statusNode.textContent.trim() : 'N/A');
                    var metaText = payload && payload.meta ? String(payload.meta) : (metaNode ? metaNode.textContent.trim() : '');

                    doc.setFillColor(15, 23, 42);
                    doc.rect(0, 0, pageWidth, 17, 'F');
                    doc.setTextColor(255, 255, 255);
                    doc.setFont('helvetica', 'bold');
                    doc.setFontSize(9);
                    doc.text(titleText, margin, 7);
                    doc.setFont('helvetica', 'normal');
                    doc.setFontSize(5.4);
                    doc.text('Status: ' + statusText, margin, 11);
                    doc.text('Generated: ' + new Date().toLocaleString(), margin, 14);

                    var startY = 19;
                    if (metaText) {
                        doc.setTextColor(71, 85, 105);
                        doc.setFontSize(5.4);
                        doc.text(metaText, margin, startY);
                        startY += 2.5;
                    }

                    doc.setTextColor(15, 23, 42);

                    var blocks = [];
                    if (payload && Array.isArray(payload.sections) && payload.sections.length) {
                        blocks = payload.sections.map(function(section) {
                            var rows = Array.isArray(section.rows) ? section.rows : [];
                            return {
                                title: String(section.name || 'Details'),
                                rows: rows,
                                textLines: [],
                                imageLinks: []
                            };
                        });
                    } else {
                        source.querySelectorAll('.txn-detail-card').forEach(function(card) {
                            var sectionTitleNode = card.querySelector('.txn-detail-title');
                            var sectionTitle = sectionTitleNode ? sectionTitleNode.textContent.trim() : 'Details';
                            var rows = [];

                            card.querySelectorAll('.txn-detail-row').forEach(function(rowEl) {
                                var label = rowEl.querySelector('.txn-detail-label');
                                var value = rowEl.querySelector('.txn-detail-value');
                                var labelText = label ? label.textContent.replace(/:\s*$/, '').trim() : '';
                                var valueText = value ? value.textContent.trim() : '';
                                if (labelText || valueText) {
                                    rows.push([labelText || '-', valueText || 'N/A']);
                                }
                            });

                            blocks.push({
                                title: sectionTitle,
                                rows: rows,
                                textLines: [],
                                imageLinks: []
                            });
                        });
                    }

                    var currentY = startY;

                    if (payload && Array.isArray(payload.packageItems) && payload.packageItems.length) {
                        if (currentY > 282) {
                            doc.addPage();
                            currentY = 10;
                        }
                        doc.setFont('helvetica', 'bold');
                        doc.setFontSize(6.6);
                        doc.setTextColor(30, 41, 59);
                        doc.text('Purchase Summary', margin, currentY);
                        currentY += 1.8;

                        var packageBody = payload.packageItems.map(function(item) {
                            var addonsText = Array.isArray(item.addons) && item.addons.length
                                ? item.addons.join('\n')
                                : 'None';
                            return [
                                String(item.name || 'Package'),
                                String(item.quantity || 'N/A'),
                                String(item.unitPrice || 'N/A'),
                                String(item.total || 'N/A'),
                                addonsText
                            ];
                        });

                        doc.autoTable({
                            startY: currentY,
                            head: [['Package', 'Qty', 'Unit Price', 'Line Total', 'Add-ons']],
                            body: packageBody,
                            theme: 'grid',
                            margin: { left: margin, right: margin },
                            styles: { fontSize: 4.9, cellPadding: 1.2, textColor: [15, 23, 42], valign: 'top' },
                            headStyles: { fillColor: [30, 41, 59], textColor: [255, 255, 255], fontStyle: 'bold' },
                            pageBreak: 'auto',
                            rowPageBreak: 'auto',
                            columnStyles: {
                                0: { cellWidth: 36 },
                                1: { cellWidth: 22 },
                                2: { cellWidth: 24 },
                                3: { cellWidth: 24 },
                                4: { cellWidth: contentWidth - (36 + 22 + 24 + 24) }
                            }
                        });
                        currentY = doc.lastAutoTable.finalY + 2.5;
                    }

                    blocks.forEach(function(block) {
                        if (block.title === 'Purchase Summary') {
                            return;
                        }
                        if (currentY > 286) {
                            doc.addPage();
                            currentY = 10;
                        }

                        doc.setFont('helvetica', 'bold');
                        doc.setFontSize(6.6);
                        doc.setTextColor(30, 41, 59);
                        doc.text(block.title, margin, currentY);
                        currentY += 1.8;

                        if (block.rows.length) {
                            doc.autoTable({
                                startY: currentY,
                                head: [['Field', 'Value']],
                                body: block.rows,
                                theme: 'grid',
                                margin: { left: margin, right: margin },
                                styles: { fontSize: 5.1, cellPadding: 1.2, textColor: [15, 23, 42] },
                                headStyles: { fillColor: [30, 41, 59], textColor: [255, 255, 255], fontStyle: 'bold' },
                                columnStyles: {
                                    0: { cellWidth: 58, fontStyle: 'bold', textColor: [51, 65, 85] },
                                    1: { cellWidth: contentWidth - 58 }
                                },
                                didParseCell: function (data) {
                                    if (data.section === 'body' && data.column.index === 1 && (!data.cell.text || !data.cell.text.length)) {
                                        data.cell.text = ['N/A'];
                                    }
                                }
                            });
                            currentY = doc.lastAutoTable.finalY + 2.5;
                        } else if (block.textLines.length) {
                            var wrapped = [];
                            block.textLines.forEach(function(line) {
                                var split = doc.splitTextToSize(line, contentWidth - 4);
                                wrapped = wrapped.concat(split);
                            });
                            doc.setFont('helvetica', 'normal');
                            doc.setFontSize(5.4);
                            doc.setTextColor(15, 23, 42);
                            doc.text(wrapped, margin + 1.2, currentY + 2.5);
                            currentY += (wrapped.length * 2.5) + 3.5;
                        } else {
                            doc.setFont('helvetica', 'normal');
                            doc.setFontSize(5.4);
                            doc.setTextColor(100, 116, 139);
                            doc.text('No details available.', margin + 1.2, currentY + 2.5);
                            currentY += 5;
                        }

                        if (block.imageLinks.length) {
                            doc.setFont('helvetica', 'italic');
                            doc.setFontSize(5.1);
                            doc.setTextColor(30, 64, 175);
                            block.imageLinks.forEach(function(link, idx) {
                                if (currentY > 289) {
                                    doc.addPage();
                                    currentY = 10;
                                }
                                var text = 'Image ' + (idx + 1) + ': ' + link;
                                var splitText = doc.splitTextToSize(text, contentWidth);
                                doc.text(splitText, margin, currentY);
                                currentY += (splitText.length * 2.5) + 1.2;
                            });
                            currentY += 1;
                        }
                    });

                    var photoLinks = payload && Array.isArray(payload.photoLinks) ? payload.photoLinks : [];
                    if (photoLinks.length) {
                        if (currentY > 286) {
                            doc.addPage();
                            currentY = 10;
                        }
                        doc.setFont('helvetica', 'bold');
                        doc.setFontSize(6.6);
                        doc.setTextColor(30, 41, 59);
                        doc.text('Check-In ID Photos', margin, currentY);
                        currentY += 2.3;
                        doc.setFont('helvetica', 'normal');
                        doc.setFontSize(5.1);
                        doc.setTextColor(30, 64, 175);
                        photoLinks.forEach(function(link, idx) {
                            var txt = (idx === 0 ? 'Front ID: ' : 'Back ID: ') + String(link || '');
                            var wrapped = doc.splitTextToSize(txt, contentWidth);
                            if (currentY + (wrapped.length * 2.5) > 291) {
                                doc.addPage();
                                currentY = 10;
                            }
                            doc.text(wrapped, margin, currentY);
                            currentY += (wrapped.length * 2.5) + 1.2;
                        });
                    }

                    var fileSafeTitle = String(titleText || 'transaction-details')
                        .toLowerCase()
                        .replace(/[^a-z0-9]+/g, '-')
                        .replace(/^-+|-+$/g, '');

                    var pageCount = doc.getNumberOfPages();
                    for (var i = 1; i <= pageCount; i += 1) {
                        doc.setPage(i);
                        doc.setFont('helvetica', 'normal');
                        doc.setFontSize(4.8);
                        doc.setTextColor(100, 116, 139);
                        doc.text('Page ' + i + ' of ' + pageCount, pageWidth - margin - 14, doc.internal.pageSize.getHeight() - 4);
                    }

                    doc.save((fileSafeTitle || 'transaction-details') + '.pdf');
                } catch (error) {
                    console.error('Transaction PDF export failed:', error);
                    alert('PDF export failed. Please try again.');
                } finally {
                    button.disabled = false;
                    button.innerHTML = originalHtml;
                }
            });

            // Handle Package Details Modal
            $(document).on('click', '.btn-link-package', function(e) {
                e.preventDefault();
                var esc = window.txnEsc || function(value) {
                    return String(value == null ? '' : value)
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/"/g, '&quot;')
                        .replace(/'/g, '&#39;');
                };
                var rawCartItems = $(this).data('cart-items') || [];
                var parsedCartItems = Array.isArray(rawCartItems) ? rawCartItems : (window.parseJsonLike ? window.parseJsonLike(rawCartItems) : []);
                var breakdownData = $(this).data('breakdown');
                if (!breakdownData || typeof breakdownData !== 'object') {
                    breakdownData = null;
                }
                var normalizeCartItems = function(value) {
                    if (Array.isArray(value)) {
                        return value;
                    }
                    if (!value || typeof value !== 'object') {
                        return [];
                    }
                    if (Array.isArray(value.items)) {
                        return value.items;
                    }
                    if (Array.isArray(value.cart_items)) {
                        return value.cart_items;
                    }
                    if (Array.isArray(value.cartItems)) {
                        return value.cartItems;
                    }
                    var objectValues = Object.values(value || {});
                    if (objectValues.length && objectValues.every(function(v) { return v && typeof v === 'object'; })) {
                        return objectValues;
                    }
                    return [];
                };
                var cartItems = normalizeCartItems(parsedCartItems);
                var rawPackageDescriptionsB64 = $(this).attr('data-package-descriptions-b64') || $(this).data('package-descriptions-b64') || null;
                var packageDescriptionsPayload = null;
                if (rawPackageDescriptionsB64) {
                    try {
                        var decodedPackageDescriptions = window.atob(String(rawPackageDescriptionsB64));
                        packageDescriptionsPayload = window.parseJsonLike ? window.parseJsonLike(decodedPackageDescriptions) : JSON.parse(decodedPackageDescriptions);
                    } catch (e) {
                        packageDescriptionsPayload = null;
                    }
                }
                if (!packageDescriptionsPayload) {
                    var rawPackageDescriptions = $(this).attr('data-package-descriptions') || $(this).data('package-descriptions') || null;
                    packageDescriptionsPayload = window.parseJsonLike ? window.parseJsonLike(rawPackageDescriptions) : null;
                }
                if (!packageDescriptionsPayload || typeof packageDescriptionsPayload !== 'object') {
                    packageDescriptionsPayload = { byId: {}, byName: {} };
                }
                var packageDescriptionsById = packageDescriptionsPayload.byId && typeof packageDescriptionsPayload.byId === 'object'
                    ? packageDescriptionsPayload.byId
                    : {};
                var packageDescriptionsByName = packageDescriptionsPayload.byName && typeof packageDescriptionsPayload.byName === 'object'
                    ? packageDescriptionsPayload.byName
                    : {};
                var extractDescription = function(source) {
                    if (!source || typeof source !== 'object') {
                        return '';
                    }
                    var sourcePackageId = String(source.package_id || source.packageId || source.id || '').trim();
                    if (sourcePackageId && packageDescriptionsById[sourcePackageId]) {
                        var dbById = String(packageDescriptionsById[sourcePackageId] || '').trim();
                        if (dbById) {
                            return dbById.replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').trim();
                        }
                    }
                    var sourcePackageName = String(source.package_name || source.packageName || source.pkgName || source.name || '').trim().toLowerCase();
                    if (sourcePackageName && packageDescriptionsByName[sourcePackageName]) {
                        var dbByName = String(packageDescriptionsByName[sourcePackageName] || '').trim();
                        if (dbByName) {
                            return dbByName.replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').trim();
                        }
                    }
                    var candidates = [
                        source.package_description,
                        source.packageDescription,
                        source.description,
                        source.package_details,
                        source.packageDetails,
                        source.details,
                        source.package_note,
                        source.note,
                        source.summary,
                        source.package_summary
                    ];
                    for (var i = 0; i < candidates.length; i += 1) {
                        var text = String(candidates[i] == null ? '' : candidates[i]).trim();
                        if (!text) {
                            continue;
                        }
                        return text.replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').trim();
                    }
                    return '';
                };
                var transactionType = $(this).data('transaction-type') || 'package';
                var menCount = $(this).data('men') || 0;
                var womenCount = $(this).data('women') || 0;
                var transactionId = $(this).data('transaction-id');
                var orderId = transactionId || 'N/A';
                var confirmationNumber = $(this).data('confirmation-number') || 'N/A';
                var packageLabel = String($(this).data('package-label') || '').trim();
                var packageSummary = window.summarizePackageItems ? window.summarizePackageItems(cartItems) : { items: [], totalQuantity: 0, totalAddons: 0, addonSummaryText: '', summaryText: packageLabel };
                var breakdownItems = [];
                if (breakdownData && Array.isArray(breakdownData.items)) {
                    breakdownItems = breakdownData.items.map(function(rawItem) {
                        if (!rawItem || typeof rawItem !== 'object') {
                            return null;
                        }

                        var qty = Math.max(1, parseInt(rawItem.guests || rawItem.quantity || 1, 10) || 1);
                        var isMultiple = !!rawItem.is_multiple;
                        var unitPriceRaw = parseFloat(rawItem.unit_price);
                        var packageSubtotalRaw = parseFloat(rawItem.package_subtotal);
                        var lineTotalRaw = parseFloat(rawItem.line_total);
                        var resolvedUnitPrice = isNaN(unitPriceRaw) ? null : unitPriceRaw;
                        var resolvedLineTotal = !isNaN(packageSubtotalRaw)
                            ? packageSubtotalRaw
                            : (!isNaN(lineTotalRaw) ? lineTotalRaw : (resolvedUnitPrice == null ? null : (resolvedUnitPrice * qty)));

                        var structuredAddons = Array.isArray(rawItem.addons) ? rawItem.addons.map(function(addon) {
                            if (!addon || typeof addon !== 'object') {
                                return null;
                            }
                            var addonName = String(addon.name || '').trim();
                            if (!addonName) {
                                return null;
                            }
                            var addonQty = Math.max(1, parseInt(addon.qty || addon.quantity || 1, 10) || 1);
                            var addonUnit = parseFloat(addon.unit_price);
                            var addonLine = parseFloat(addon.price);
                            var resolvedAddonUnit = isNaN(addonUnit) ? null : addonUnit;
                            var resolvedAddonLine = isNaN(addonLine)
                                ? (resolvedAddonUnit == null ? null : (resolvedAddonUnit * addonQty))
                                : addonLine;
                            return {
                                name: addonName,
                                quantity: addonQty,
                                unitPrice: resolvedAddonUnit,
                                lineTotal: resolvedAddonLine
                            };
                        }).filter(Boolean) : [];

                        return {
                            name: String(rawItem.package_name || rawItem.packageName || rawItem.name || 'Package').trim(),
                            packageId: String(rawItem.package_id || rawItem.packageId || '').trim(),
                            quantity: qty,
                            packageType: String(rawItem.package_type || rawItem.type || rawItem.packageType || '').toLowerCase() || 'package',
                            unitPrice: resolvedUnitPrice,
                            lineTotal: resolvedLineTotal,
                            description: extractDescription(rawItem),
                            addonLabels: structuredAddons.map(function(addon) {
                                var label = addon.name + ' x' + addon.quantity;
                                if (addon.unitPrice != null) {
                                    label += ' ($' + addon.unitPrice.toFixed(2) + ')';
                                }
                                return label;
                            }),
                            addonsStructured: structuredAddons
                        };
                    }).filter(Boolean);
                }
                var statusValue = $(this).data('status');
                var statusText = 'Unknown';
                var statusClass = 'txn-status-unknown';
                if (statusValue == 1 || statusValue === 'Completed' || statusValue === 'Approved') {
                    statusText = 'Payment Completed';
                    statusClass = 'txn-status-completed';
                } else if (statusValue == 0 || statusValue === 'Canceled' || statusValue === '0') {
                    statusText = 'Payment Canceled';
                    statusClass = 'txn-status-canceled';
                } else if (statusValue == 2 || statusValue === 'Refunded') {
                    statusText = 'Payment Refunded';
                    statusClass = 'txn-status-refunded';
                }
                var row = window.txnDetailRow || function(label, value) {
                    return '<div class="txn-detail-row"><span class="txn-detail-label">' + esc(label) + ':</span><span class="txn-detail-value">' + esc(value) + '</span></div>';
                };
                var formatDateUS = window.formatDateUS || function(dateValue) {
                    var raw = String(dateValue || '').trim();
                    if (!raw) {
                        return 'N/A';
                    }

                    var match = raw.match(/^(\d{4})-(\d{2})-(\d{2})/);
                    if (match) {
                        return match[2] + '/' + match[3] + '/' + match[1];
                    }

                    var parsed = new Date(raw);
                    if (!isNaN(parsed.getTime())) {
                        var month = String(parsed.getMonth() + 1).padStart(2, '0');
                        var day = String(parsed.getDate()).padStart(2, '0');
                        var year = parsed.getFullYear();
                        return month + '/' + day + '/' + year;
                    }

                    return raw;
                };
                var formatPickupTime = function(timeValue) {
                    var raw = String(timeValue || '').trim();
                    if (!raw) {
                        return 'N/A';
                    }
                    if (/\b(?:AM|PM)\b/i.test(raw)) {
                        return raw.toUpperCase();
                    }

                    var strict = raw.match(/^(\d{1,2}):(\d{2})(?::(\d{2}))?$/);
                    if (strict) {
                        var hours = parseInt(strict[1], 10);
                        var minutes = strict[2];
                        if (!isNaN(hours)) {
                            var ampm = hours >= 12 ? 'PM' : 'AM';
                            hours = hours % 12 || 12;
                            return (hours < 10 ? '0' : '') + hours + ':' + minutes + ' ' + ampm;
                        }
                    }

                    return raw;
                };
                var packageGuestCount = parseInt($(this).data('package_number_of_guest') || 0, 10) || 0;
                var packageCount = packageSummary.items.length || (packageLabel ? packageLabel.split(/\s*,\s*/).filter(Boolean).length : 0) || (packageGuestCount > 0 ? 1 : 0);
                var totalUnits = packageSummary.totalQuantity || packageGuestCount || 0;
                var addonDetails = $(this).data('addons') || packageSummary.addonSummaryText || 'N/A';
                var purchaseSummaryTitle = packageLabel || packageSummary.summaryText || 'Package Details';
                var packageLineupItems = breakdownItems.length ? breakdownItems.slice() : packageSummary.items.slice();
                if (!packageLineupItems.length && purchaseSummaryTitle && purchaseSummaryTitle !== 'Package Details') {
                    purchaseSummaryTitle.split(/\s*[;,]\s*/).filter(Boolean).forEach(function(part) {
                        var text = String(part).trim();
                        if (!text) {
                            return;
                        }

                        var parsedName = text;
                        var parsedQty = 1;
                        var parsedType = 'package';

                        var guestMatch = text.match(/^(.*?):\s*(\d+)\s*(guest|guests|ticket|tickets)\b/i);
                        if (guestMatch) {
                            parsedName = String(guestMatch[1] || '').trim() || text;
                            parsedQty = Math.max(1, parseInt(guestMatch[2] || '1', 10) || 1);
                            parsedType = /ticket/i.test(guestMatch[3] || '') ? 'ticket' : 'package';
                        } else {
                            var xQtyMatch = text.match(/^(.*?)\s*x\s*(\d+)\b/i);
                            if (xQtyMatch) {
                                parsedName = String(xQtyMatch[1] || '').trim() || text;
                                parsedQty = Math.max(1, parseInt(xQtyMatch[2] || '1', 10) || 1);
                                parsedType = /ticket/i.test(parsedName) ? 'ticket' : 'package';
                            }
                        }

                        packageLineupItems.push({
                            name: parsedName,
                            packageId: '',
                            quantity: parsedQty,
                            packageType: parsedType
                        });
                    });
                }
                var addonMapByPackage = {};
                packageSummary.items.forEach(function(item) {
                    var key = String(item.name || '').trim().toLowerCase();
                    if (!key) {
                        return;
                    }
                    if (!addonMapByPackage[key]) {
                        addonMapByPackage[key] = [];
                    }
                    if (Array.isArray(item.addonLabels)) {
                        item.addonLabels.forEach(function(label) {
                            if (label) {
                                addonMapByPackage[key].push(String(label));
                            }
                        });
                    }
                });
                if (packageSummary.addonSummaryText) {
                    packageSummary.addonSummaryText.split(/\s*;\s*/).forEach(function(chunk) {
                        var raw = String(chunk || '').trim();
                        if (!raw) {
                            return;
                        }
                        var idx = raw.indexOf(':');
                        if (idx > 0) {
                            var pkgName = raw.slice(0, idx).trim().toLowerCase();
                            var addonText = raw.slice(idx + 1).trim();
                            if (pkgName && addonText) {
                                if (!addonMapByPackage[pkgName]) {
                                    addonMapByPackage[pkgName] = [];
                                }
                                addonMapByPackage[pkgName].push(addonText);
                            }
                        }
                    });
                }
                packageLineupItems.forEach(function(item, idx) {
                    var key = String(item.name || '').trim().toLowerCase();
                    var mapped = key && addonMapByPackage[key] ? addonMapByPackage[key] : [];
                    if (!item.description) {
                        var sourceByName = cartItems.find(function(cartItem) {
                            if (!cartItem || typeof cartItem !== 'object') {
                                return false;
                            }
                            var cartName = String(cartItem.package_name || cartItem.packageName || cartItem.pkgName || '').trim().toLowerCase();
                            return cartName && cartName === key;
                        });
                        var sourceByIndex = cartItems[idx] && typeof cartItems[idx] === 'object' ? cartItems[idx] : null;
                        item.description = extractDescription(sourceByName || sourceByIndex || item);
                    }
                    if (!Array.isArray(item.addonLabels) || !item.addonLabels.length) {
                        item.addonLabels = mapped.slice();
                    }
                    if ((!item.addonLabels || !item.addonLabels.length) && idx === 0 && addonDetails && addonDetails !== 'N/A') {
                        item.addonLabels = String(addonDetails).split(/\s*,\s*/).filter(Boolean);
                    }
                });
                var orderDateMain = String($(this).closest('tr').find('.txn-date-main').text() || '').trim();
                var orderDateTime = String($(this).closest('tr').find('.txn-date-time').text() || '').trim();
                var orderDate = [orderDateMain, orderDateTime].filter(Boolean).join(' ') || 'N/A';
                var rowViewBtn = $(this).closest('tr').find('.view-btn').first();
                var checkedInStatusRaw = String(rowViewBtn.data('checked_in_status') || '').toLowerCase();
                var checkedInStatus = checkedInStatusRaw === '1' || checkedInStatusRaw === 'true' || checkedInStatusRaw === 'yes';
                var checkedInAtDisplay = String(rowViewBtn.data('checked_in_at_pacific') || '').trim();

                var guestFirstName = String($(this).data('package_first_name') || '').trim();
                var guestLastName = String($(this).data('package_last_name') || '').trim();
                var guestName = [guestFirstName, guestLastName].filter(Boolean).join(' ').trim() || 'N/A';
                var guestEmail = String($(this).data('package_email') || '').trim() || 'N/A';
                var guestPhone = String($(this).data('package_phone') || '').trim() || 'N/A';
                var guestDob = formatDateUS($(this).data('package_dob'));
                var guestUseDate = formatDateUS($(this).data('package_use_date'));
                var guestNote = String($(this).data('package_note') || '').trim() || 'N/A';
                var hostName = String($(this).data('host_name') || '').trim() || 'N/A';

                var transportationDateRaw = String($(this).data('package-use-date') || $(this).data('package_use_date') || '').trim();
                var transportationDate = formatDateUS(transportationDateRaw);
                if (transportationDate === 'N/A' && transportationDateRaw) {
                    transportationDate = transportationDateRaw;
                }
                var transportationPickup = String($(this).data('transportation_pickup_time') || '').trim();
                var transportationPickupDisplay = formatPickupTime(transportationPickup);
                var transportationArrival = String(
                    $(this).data('transportation_arrival_time') ||
                    ($(this).closest('tr').find('.view-btn').data('transportation_arrival_time') || '')
                ).trim();
                var transportationArrivalDisplay = formatPickupTime(transportationArrival);
                if ((transportationArrivalDisplay === 'N/A' || transportationArrivalDisplay === '') && checkedInStatus && checkedInAtDisplay) {
                    transportationArrivalDisplay = checkedInAtDisplay + ' (Check-In)';
                }
                var transportationAddress = String($(this).data('transportation_address') || '').trim();
                var transportationPhone = String($(this).data('transportation_phone') || '').trim();
                var transportationNote = String($(this).data('transportation_note') || '').trim();
                var hasTransportation = [transportationPickup, transportationArrival, transportationAddress, transportationPhone, transportationNote].some(function(v) {
                    return v !== '';
                });
                if (transportationArrivalDisplay !== 'N/A') {
                    hasTransportation = true;
                }
                var parseAddonLabel = function(label) {
                    var raw = String(label || '').trim();
                    if (!raw) {
                        return null;
                    }
                    var match = raw.match(/^(.*?)\s*x\s*(\d+)(?:\s*\(\s*\$?([\d.]+)\s*\))?$/i);
                    if (!match) {
                        return {
                            name: raw,
                            quantity: 1,
                            unitPrice: null,
                            lineTotal: null,
                            raw: raw
                        };
                    }
                    var name = String(match[1] || '').trim() || 'Add-on';
                    var quantity = Math.max(1, parseInt(match[2] || '1', 10) || 1);
                    var unitPrice = match[3] != null ? (parseFloat(match[3]) || 0) : null;
                    return {
                        name: name,
                        quantity: quantity,
                        unitPrice: unitPrice,
                        lineTotal: unitPrice == null ? null : (unitPrice * quantity),
                        raw: raw
                    };
                };

                var sectionRows = function(rows) {
                    return rows.map(function(pair) {
                        return [
                            String(pair[0] == null ? '' : pair[0]),
                            String(pair[1] == null || pair[1] === '' ? 'N/A' : pair[1])
                        ];
                    });
                };

                var bookingRows = sectionRows([
                    ['Order ID', orderId],
                    ['Confirmation #', confirmationNumber],
                    ['Package Summary', purchaseSummaryTitle],
                    ['Package Count', String(packageCount)],
                    ['Total Units', String(totalUnits)],
                    ['Add-ons', addonDetails],
                    ['Transaction Type', transactionType.charAt(0).toUpperCase() + transactionType.slice(1)],
                    ['Order Date', orderDate],
                    ['Website / Venue', $(this).data('website_id') || 'N/A'],
                    ['Payment Status', statusText],
                    ['Package Redemption', checkedInStatus ? 'Redeemed' : 'Not Redeemed'],
                    ['Redeemed At', checkedInStatus ? (checkedInAtDisplay || 'Yes') : 'N/A']
                ]);

                var guestRows = sectionRows([
                    ['Guest Name', guestName],
                    ['Guest Email', guestEmail],
                    ['Guest Phone', guestPhone],
                    ['Date Of Birth', guestDob],
                    ['Date Of Use', guestUseDate],
                    ['Guest Count', String(totalUnits)],
                    ['Male', String(menCount)],
                    ['Female', String(womenCount)],
                    ['Host Name', hostName],
                    ['Guest Note', guestNote]
                ]);

                var transportationRows = sectionRows([
                    ['Transportation', hasTransportation ? 'Provided' : 'Self Drive Selected'],
                    ['Transportation Date', transportationDate || 'N/A'],
                    ['Pickup Time', transportationPickupDisplay],
                    ['Arrival Time', transportationArrivalDisplay],
                    ['Pickup Address', transportationAddress || 'N/A'],
                    ['Transport Phone', transportationPhone || 'N/A'],
                    ['Transport Note', transportationNote || 'N/A']
                ]);

                var priceRows = [];
                if (breakdownData && typeof breakdownData === 'object') {
                    var pushPriceRow = function(label, value) {
                        priceRows.push([String(label), String(value)]);
                    };
                    var moneyPdf = function(v) {
                        var n = parseFloat(v);
                        return '$' + (isNaN(n) ? 0 : n).toFixed(2);
                    };

                    pushPriceRow('Items Subtotal', moneyPdf(breakdownData.items_subtotal));
                    if (parseFloat(breakdownData.promo_discount) > 0) {
                        pushPriceRow('Discount', '-' + moneyPdf(breakdownData.promo_discount));
                    }
                    if (breakdownData.service_charge && breakdownData.service_charge.enabled) {
                        pushPriceRow(breakdownData.service_charge.name || 'Service Charge', moneyPdf(breakdownData.service_charge.amount));
                    }
                    if (breakdownData.gratuity && breakdownData.gratuity.enabled) {
                        pushPriceRow(breakdownData.gratuity.name || 'Gratuity', moneyPdf(breakdownData.gratuity.amount));
                    }
                    if (breakdownData.sales_tax && breakdownData.sales_tax.enabled) {
                        pushPriceRow(breakdownData.sales_tax.name || 'Sales Tax', moneyPdf(breakdownData.sales_tax.amount));
                    }
                    if (breakdownData.processing_fee && breakdownData.processing_fee.enabled) {
                        pushPriceRow('Processing Fee', moneyPdf(breakdownData.processing_fee.amount));
                    }
                    pushPriceRow('Grand Total', moneyPdf(breakdownData.grand_total));
                    if (breakdownData.refundable && breakdownData.refundable.enabled && parseFloat(breakdownData.refundable.amount) > 0) {
                        pushPriceRow((breakdownData.refundable.name || 'Non-refundable Deposit') + ' (incl. in total)', moneyPdf(breakdownData.refundable.amount));
                    }
                    pushPriceRow('Amount Paid', moneyPdf(breakdownData.amount_paid_now));
                    if (parseFloat(breakdownData.remaining_due) > 0) {
                        pushPriceRow('Remaining Due', moneyPdf(breakdownData.remaining_due));
                    }
                }

                var packageItemsForPdf = packageLineupItems.map(function(item) {
                    var itemUnitPrice = typeof item.unitPrice === 'number' ? item.unitPrice : null;
                    var itemLineTotal = typeof item.lineTotal === 'number' ? item.lineTotal : null;
                    var addonEntries = Array.isArray(item.addonsStructured) && item.addonsStructured.length
                        ? item.addonsStructured.map(function(addon) {
                            return {
                                name: addon.name,
                                quantity: addon.quantity,
                                unitPrice: addon.unitPrice,
                                lineTotal: addon.lineTotal
                            };
                        })
                        : (Array.isArray(item.addonLabels) ? item.addonLabels.map(parseAddonLabel).filter(Boolean) : []);

                    var descriptionText = String(item.description || '').trim();
                    if (!descriptionText) {
                        descriptionText = (item.packageType === 'ticket' ? 'Ticket Package' : 'Guest Package')
                            + ' | Qty: ' + String(item.quantity) + ' ' + (item.packageType === 'ticket' ? 'tickets' : 'guests');
                    }
                    if (addonEntries.length) {
                        descriptionText += ' | Add-ons: ' + String(addonEntries.length);
                    }

                    var addonsText = addonEntries.map(function(addon) {
                        var addonLine = addon.name + ' x' + addon.quantity;
                        if (addon.unitPrice != null && addon.lineTotal != null) {
                            addonLine += ' @ $' + addon.unitPrice.toFixed(2) + ' = $' + addon.lineTotal.toFixed(2);
                        }
                        return addonLine;
                    });

                    return {
                        name: String(item.name || 'Package'),
                        description: descriptionText,
                        quantity: String(item.quantity) + ' ' + (item.packageType === 'ticket' ? 'tickets' : 'guests'),
                        unitPrice: itemUnitPrice == null ? 'N/A' : ('$' + itemUnitPrice.toFixed(2)),
                        lineTotal: itemLineTotal == null ? 'N/A' : ('$' + itemLineTotal.toFixed(2)),
                        addons: addonsText.length ? addonsText : ['None']
                    };
                });

                var html = '<div>';

                html += '<div class="row g-2" style="margin-bottom:6px;">';
                html += '<div class="col-md-6">';
                html += '<div class="txn-detail-card" style="margin-bottom:0;">';
                html += '<div class="txn-detail-title">Guest Details</div>';
                html += row('Guest Name', guestName);
                html += row('Guest Email', guestEmail);
                html += row('Guest Phone', guestPhone);
                html += row('Date Of Birth', guestDob);
                html += row('Date Of Use', guestUseDate);
                html += row('Guest Count', String(totalUnits));
                html += row('Male', String(menCount));
                html += row('Female', String(womenCount));
                html += row('Host Name', hostName);
                html += row('Guest Note', guestNote);
                html += '</div>';
                html += '<div class="txn-detail-card" style="margin-top:8px;margin-bottom:0;">';
                html += '<div class="txn-detail-title">Transportation Details</div>';
                html += row('Transportation', hasTransportation ? 'Provided' : 'Self Drive Selected');
                html += row('Transportation Date', transportationDate || 'N/A');
                html += row('Pickup Time', transportationPickupDisplay);
                html += row('Arrival Time', transportationArrivalDisplay);
                html += row('Pickup Address', transportationAddress || 'N/A');
                html += row('Transport Phone', transportationPhone || 'N/A');
                html += row('Transport Note', transportationNote || 'N/A');
                html += '</div>';
                html += '</div>';

                html += '<div class="col-md-6">';
                html += '<div class="txn-detail-card" style="margin-bottom:0;">';
                html += '<div class="txn-detail-title">Booking Details</div>';
                html += row('Order ID', orderId);
                html += row('Confirmation #', confirmationNumber);
                html += row('Package Summary', purchaseSummaryTitle);
                html += row('Package Count', String(packageCount));
                html += row('Total Units', String(totalUnits));
                html += row('Add-ons', addonDetails);
                html += row('Transaction Type', transactionType.charAt(0).toUpperCase() + transactionType.slice(1));
                html += row('Order Date', orderDate);
                html += row('Website / Venue', $(this).data('website_id') || 'N/A');
                html += row('Payment Status', statusText);
                html += row('Package Redemption', checkedInStatus ? 'Redeemed' : 'Not Redeemed');
                html += row('Redeemed At', checkedInStatus ? (checkedInAtDisplay || 'Yes') : 'N/A');
                if (packageLineupItems.length) {
                    html += '<div style="margin-top:8px;background:rgba(15,23,42,0.55);border:1px solid rgba(255,255,255,0.1);border-radius:8px;padding:8px;">';
                    html += '<div style="font-size:0.76rem;color:#cbd5e1;font-weight:700;margin-bottom:6px;letter-spacing:0.03em;">Package Lineup</div>';
                    packageLineupItems.forEach(function(item) {
                        var qtyText = String(item.quantity) + ' ' + (item.packageType === 'ticket' ? 'tickets' : 'guests');
                        var descriptionText = String(item.description || '').trim();
                        var itemAddons = Array.isArray(item.addonLabels) ? item.addonLabels : [];
                        var addonEntries = itemAddons.map(parseAddonLabel).filter(Boolean);
                        var addonQtyTotal = addonEntries.reduce(function(sum, addon) { return sum + (addon.quantity || 0); }, 0);
                        html += '<div style="padding:6px 7px;border-radius:6px;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);margin-bottom:6px;">';
                        html += '<div style="display:flex;justify-content:space-between;gap:10px;align-items:center;">';
                        html += '<span style="color:#e2e8f0;font-weight:600;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' + esc(item.name) + '</span>';
                        html += '<span style="color:#fbbf24;font-weight:700;white-space:nowrap;">x ' + esc(qtyText) + '</span>';
                        html += '</div>';
                        if (descriptionText) {
                            html += '<div style="margin-top:4px;font-size:0.74rem;color:#cbd5e1;line-height:1.35;">' + esc(descriptionText) + '</div>';
                        }
                        if (addonEntries.length) {
                            html += '<div style="margin-top:5px;font-size:0.75rem;color:#93c5fd;line-height:1.35;font-weight:700;">Add-ons: ' + esc(String(addonEntries.length)) + ' | Qty: ' + esc(String(addonQtyTotal)) + '</div>';
                        }
                        html += '</div>';
                    });
                    html += '</div>';
                }
                html += '</div>';
                html += '</div>';
                html += '</div>';

                // Display packages with details
                if (packageLineupItems.length) {
                    html += '<h6 style="color:#e0e7ff;margin-top:16px;margin-bottom:16px;font-weight:700;"><i class="fas fa-boxes-stacked"></i> Package Purchase Breakdown</h6>';

                    packageLineupItems.forEach(function(item, index) {
                        var itemUnitPrice = typeof item.unitPrice === 'number' ? item.unitPrice : null;
                        var itemLineTotal = typeof item.lineTotal === 'number' ? item.lineTotal : null;
                        var addonEntries = Array.isArray(item.addonsStructured) && item.addonsStructured.length
                            ? item.addonsStructured.map(function(addon) {
                                return {
                                    name: addon.name,
                                    quantity: addon.quantity,
                                    unitPrice: addon.unitPrice,
                                    lineTotal: addon.lineTotal
                                };
                            })
                            : (Array.isArray(item.addonLabels) ? item.addonLabels.map(parseAddonLabel).filter(Boolean) : []);
                        var addonQtyTotal = addonEntries.reduce(function(sum, addon) { return sum + (addon.quantity || 0); }, 0);
                        var addonPriceTotal = addonEntries.reduce(function(sum, addon) { return sum + (addon.lineTotal || 0); }, 0);
                        var hasAddonPrice = addonEntries.some(function(addon) { return addon.unitPrice != null; });
                        html += '<div class="package-item" style="background:#1e293b;border:1px solid rgba(255,255,255,0.1);padding:12px;border-radius:8px;margin-bottom:10px;">';
                        html += '<div style="display:flex;justify-content:space-between;align-items:start;gap:12px;margin-bottom:8px;">';
                        html += '<div style="min-width:0;">';
                        html += '<div style="font-weight:700;color:#e0e7ff;">' + esc(item.name) + '</div>';
                        var cardDescription = String(item.description || '').trim();
                        html += '<div style="font-size:0.8rem;color:#94a3b8;margin-top:4px;">Item ' + (index + 1) + ' of ' + packageLineupItems.length + (cardDescription ? ' | ' + esc(cardDescription) : '') + '</div>';
                        html += '</div>';
                        html += '<div style="text-align:right;flex-shrink:0;">';
                        html += '<div style="display:inline-block;background:' + (item.packageType === 'ticket' ? 'rgba(245,158,11,0.18)' : 'rgba(124,58,237,0.18)') + ';color:' + (item.packageType === 'ticket' ? '#fbbf24' : '#a5b4fc') + ';border:1px solid ' + (item.packageType === 'ticket' ? 'rgba(245,158,11,0.3)' : 'rgba(124,58,237,0.28)') + ';border-radius:999px;padding:3px 10px;font-size:0.72rem;font-weight:700;letter-spacing:0.04em;text-transform:uppercase;">' + esc(item.packageType === 'ticket' ? 'Ticket Package' : 'Guest Package') + '</div>';
                        html += '</div>';
                        html += '</div>';

                        html += '<div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:8px;margin-top:10px;">';
                        html += '<div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.08);border-radius:8px;padding:10px;">';
                        html += '<div style="font-size:0.72rem;color:#94a3b8;margin-bottom:4px;">Quantity</div>';
                        html += '<div style="font-weight:700;color:#fbbf24;">' + esc(String(item.quantity)) + ' ' + esc(item.packageType === 'ticket' ? 'tickets' : 'guests') + '</div>';
                        html += '</div>';
                        html += '<div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.08);border-radius:8px;padding:10px;">';
                        html += '<div style="font-size:0.72rem;color:#94a3b8;margin-bottom:4px;">Unit Price</div>';
                        html += '<div style="font-weight:700;color:#e0e7ff;">' + (itemUnitPrice == null ? 'N/A' : ('$' + itemUnitPrice.toFixed(2))) + '</div>';
                        html += '</div>';
                        html += '<div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.08);border-radius:8px;padding:10px;">';
                        html += '<div style="font-size:0.72rem;color:#94a3b8;margin-bottom:4px;">Line Total</div>';
                        html += '<div style="font-weight:700;color:#34d399;">' + (itemLineTotal == null ? 'N/A' : ('$' + itemLineTotal.toFixed(2))) + '</div>';
                        html += '</div>';
                        html += '</div>';

                        if (addonEntries.length) {
                            html += '<div style="margin-top:10px;border-left:2px solid rgba(251,191,36,0.28);padding-left:12px;">';
                            html += '<div style="color:#94a3b8;font-size:0.8rem;margin-bottom:6px;font-weight:600;">Add-ons (' + esc(String(addonEntries.length)) + ') | Qty: ' + esc(String(addonQtyTotal));
                            if (hasAddonPrice) {
                                html += ' | Total: $' + addonPriceTotal.toFixed(2);
                            }
                            html += '</div>';
                            addonEntries.forEach(function(addon) {
                                var addonLine = addon.name + ' x' + addon.quantity;
                                if (addon.unitPrice != null) {
                                    addonLine += ' @ $' + addon.unitPrice.toFixed(2) + ' = $' + addon.lineTotal.toFixed(2);
                                }
                                html += '<div style="color:#e0e7ff;font-size:0.85rem;margin-bottom:4px;">• ' + esc(addonLine) + '</div>';
                            });
                            html += '</div>';
                        }

                        html += '</div>';
                    });
                }

                // Full price / purchase breakdown (server-computed, matches what the customer was charged)
                var breakdown = breakdownData;
                if (breakdown && typeof breakdown === 'object') {
                    var money = function(v){ var n = parseFloat(v); return '$' + (isNaN(n) ? 0 : n).toFixed(2); };
                    var line = function(label, value, opts){
                        opts = opts || {};
                        var valColor = opts.color || '#e0e7ff';
                        var weight = opts.weight || '500';
                        var topBorder = opts.border ? 'border-top:1px solid rgba(255,255,255,0.15);margin-top:6px;padding-top:10px;' : '';
                        var labelColor = opts.muted ? 'rgba(148,163,184,0.7)' : '#94a3b8';
                        return '<div style="display:flex;justify-content:space-between;gap:16px;margin-bottom:8px;' + topBorder + '">'
                            + '<span style="color:' + labelColor + ';">' + label + '</span>'
                            + '<span style="color:' + valColor + ';font-weight:' + weight + ';white-space:nowrap;">' + value + '</span></div>';
                    };

                    html += '<h6 style="color:#e0e7ff;margin-top:20px;margin-bottom:12px;font-weight:700;"><i class="fas fa-receipt"></i> Price Breakdown</h6>';
                    html += '<div style="background:#1e293b;border:1px solid rgba(255,255,255,0.1);padding:14px;border-radius:8px;">';

                    html += line('Items Subtotal', money(breakdown.items_subtotal));
                    if (parseFloat(breakdown.promo_discount) > 0) {
                        html += line('Discount', '-' + money(breakdown.promo_discount), {color:'#34d399'});
                    }
                    if (breakdown.service_charge && breakdown.service_charge.enabled) {
                        html += line(breakdown.service_charge.name || 'Service Charge', money(breakdown.service_charge.amount));
                    }
                    if (breakdown.gratuity && breakdown.gratuity.enabled) {
                        html += line(breakdown.gratuity.name || 'Gratuity', money(breakdown.gratuity.amount));
                    }
                    if (breakdown.sales_tax && breakdown.sales_tax.enabled) {
                        html += line(breakdown.sales_tax.name || 'Sales Tax', money(breakdown.sales_tax.amount));
                    }
                    if (breakdown.processing_fee && breakdown.processing_fee.enabled) {
                        html += line('Processing Fee', money(breakdown.processing_fee.amount));
                    }
                    html += line('Grand Total', money(breakdown.grand_total), {color:'#fbbf24', weight:'700', border:true});
                    if (breakdown.refundable && breakdown.refundable.enabled && parseFloat(breakdown.refundable.amount) > 0) {
                        html += line((breakdown.refundable.name || 'Non-refundable Deposit') + ' (incl. in total)', money(breakdown.refundable.amount), {muted:true});
                    }
                    html += line('Amount Paid', money(breakdown.amount_paid_now), {color:'#34d399', weight:'600'});
                    if (parseFloat(breakdown.remaining_due) > 0) {
                        html += line('Remaining Due', money(breakdown.remaining_due), {color:'#ef4444', weight:'600'});
                    }

                    html += '</div>';
                }

                html += '</div>';

                $('#packageDetailsContent').html(html);
                $('#packageDetailsModal').data('pdfPayload', {
                    title: 'Package Details - Order #' + String(orderId),
                    status: statusText,
                    meta: 'Confirmation: ' + String(confirmationNumber) + ' | ' + String(orderDate),
                    sections: [
                        { name: 'Guest Details', rows: guestRows },
                        { name: 'Booking Details', rows: bookingRows },
                        { name: 'Transportation Details', rows: transportationRows }
                    ],
                    packageItems: packageItemsForPdf,
                    priceRows: priceRows
                });
                var packageModal = new bootstrap.Modal(document.getElementById('packageDetailsModal'));
                packageModal.show();
            });

            $(document).on('click', '#download-package-pdf', function() {
                var payload = $('#packageDetailsModal').data('pdfPayload') || null;
                if (!payload) {
                    alert('No package details available to export.');
                    return;
                }

                var button = this;
                var originalHtml = button.innerHTML;
                button.disabled = true;
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating PDF...';

                try {
                    var jsPDFRef = window.jspdf && window.jspdf.jsPDF ? window.jspdf.jsPDF : null;
                    if (!jsPDFRef || typeof jsPDFRef !== 'function' || typeof window.jspdf.jsPDF.API.autoTable !== 'function') {
                        throw new Error('jsPDF AutoTable is not available');
                    }

                    var doc = new jsPDFRef({ unit: 'mm', format: 'a4', orientation: 'portrait' });
                    var margin = 7;
                    var pageWidth = doc.internal.pageSize.getWidth();
                    var contentWidth = pageWidth - (margin * 2);

                    var titleText = String(payload.title || 'Package Details');
                    var statusText = String(payload.status || 'N/A');
                    var metaText = String(payload.meta || '');

                    doc.setFillColor(15, 23, 42);
                    doc.rect(0, 0, pageWidth, 17, 'F');
                    doc.setTextColor(255, 255, 255);
                    doc.setFont('helvetica', 'bold');
                    doc.setFontSize(9);
                    doc.text(titleText, margin, 7);
                    doc.setFont('helvetica', 'normal');
                    doc.setFontSize(5.4);
                    doc.text('Status: ' + statusText, margin, 11);
                    doc.text('Generated: ' + new Date().toLocaleString(), margin, 14);

                    var currentY = 19;
                    if (metaText) {
                        doc.setTextColor(71, 85, 105);
                        doc.setFontSize(5.4);
                        doc.text(metaText, margin, currentY);
                        currentY += 2.5;
                    }

                    doc.setTextColor(15, 23, 42);

                    (Array.isArray(payload.sections) ? payload.sections : []).forEach(function(section) {
                        var rows = Array.isArray(section.rows) ? section.rows : [];
                        if (!rows.length) {
                            return;
                        }

                        if (currentY > 286) {
                            doc.addPage();
                            currentY = 10;
                        }

                        doc.setFont('helvetica', 'bold');
                        doc.setFontSize(6.6);
                        doc.setTextColor(30, 41, 59);
                        doc.text(String(section.name || 'Details'), margin, currentY);
                        currentY += 1.8;

                        doc.autoTable({
                            startY: currentY,
                            head: [['Field', 'Value']],
                            body: rows,
                            theme: 'grid',
                            margin: { left: margin, right: margin },
                            styles: { fontSize: 5.1, cellPadding: 1.2, textColor: [15, 23, 42] },
                            headStyles: { fillColor: [30, 41, 59], textColor: [255, 255, 255], fontStyle: 'bold' },
                            columnStyles: {
                                0: { cellWidth: 58, fontStyle: 'bold', textColor: [51, 65, 85] },
                                1: { cellWidth: contentWidth - 58 }
                            },
                            didParseCell: function(data) {
                                if (data.section === 'body' && data.column.index === 1 && (!data.cell.text || !data.cell.text.length)) {
                                    data.cell.text = ['N/A'];
                                }
                            }
                        });
                        currentY = doc.lastAutoTable.finalY + 2.5;
                    });

                    if (Array.isArray(payload.packageItems) && payload.packageItems.length) {
                        if (currentY > 282) {
                            doc.addPage();
                            currentY = 10;
                        }
                        doc.setFont('helvetica', 'bold');
                        doc.setFontSize(6.6);
                        doc.setTextColor(30, 41, 59);
                        doc.text('Purchased Packages', margin, currentY);
                        currentY += 1.8;

                        var packageBody = payload.packageItems.map(function(item) {
                            return [
                                String(item.name || 'Package'),
                                String(item.description || 'N/A'),
                                String(item.quantity || 'N/A'),
                                String(item.unitPrice || 'N/A'),
                                String(item.lineTotal || 'N/A'),
                                Array.isArray(item.addons) ? item.addons.join('\n') : 'None'
                            ];
                        });

                        doc.autoTable({
                            startY: currentY,
                            head: [['Package', 'Description', 'Qty', 'Unit', 'Total', 'Add-ons']],
                            body: packageBody,
                            theme: 'grid',
                            margin: { left: margin, right: margin },
                            styles: { fontSize: 4.8, cellPadding: 1.1, textColor: [15, 23, 42], valign: 'top' },
                            headStyles: { fillColor: [30, 41, 59], textColor: [255, 255, 255], fontStyle: 'bold' },
                            pageBreak: 'auto',
                            rowPageBreak: 'auto',
                            columnStyles: {
                                0: { cellWidth: 28 },
                                1: { cellWidth: 42 },
                                2: { cellWidth: 20 },
                                3: { cellWidth: 20 },
                                4: { cellWidth: 20 },
                                5: { cellWidth: contentWidth - (28 + 42 + 20 + 20 + 20) }
                            }
                        });
                        currentY = doc.lastAutoTable.finalY + 2.5;
                    }

                    if (Array.isArray(payload.priceRows) && payload.priceRows.length) {
                        if (currentY > 286) {
                            doc.addPage();
                            currentY = 10;
                        }

                        doc.setFont('helvetica', 'bold');
                        doc.setFontSize(6.6);
                        doc.setTextColor(30, 41, 59);
                        doc.text('Price Breakdown', margin, currentY);
                        currentY += 1.8;

                        doc.autoTable({
                            startY: currentY,
                            head: [['Charge', 'Amount']],
                            body: payload.priceRows,
                            theme: 'grid',
                            margin: { left: margin, right: margin },
                            styles: { fontSize: 5.2, cellPadding: 1.2, textColor: [15, 23, 42] },
                            headStyles: { fillColor: [30, 41, 59], textColor: [255, 255, 255], fontStyle: 'bold' },
                            columnStyles: {
                                0: { cellWidth: contentWidth - 45, fontStyle: 'bold', textColor: [51, 65, 85] },
                                1: { cellWidth: 45, halign: 'right' }
                            }
                        });
                        currentY = doc.lastAutoTable.finalY + 2.5;
                    }

                    var fileSafeTitle = titleText
                        .toLowerCase()
                        .replace(/[^a-z0-9]+/g, '-')
                        .replace(/^-+|-+$/g, '');

                    var pageCount = doc.getNumberOfPages();
                    for (var i = 1; i <= pageCount; i += 1) {
                        doc.setPage(i);
                        doc.setFont('helvetica', 'normal');
                        doc.setFontSize(4.8);
                        doc.setTextColor(100, 116, 139);
                        doc.text('Page ' + i + ' of ' + pageCount, pageWidth - margin - 14, doc.internal.pageSize.getHeight() - 4);
                    }

                    doc.save((fileSafeTitle || 'package-details') + '.pdf');
                } catch (error) {
                    console.error('Package PDF export failed:', error);
                    alert('PDF export failed. Please try again.');
                } finally {
                    button.disabled = false;
                    button.innerHTML = originalHtml;
                }
            });

            // Clean up modal properly when it's fully hidden
            $('#packageDetailsModal').on('hidden.bs.modal', function() {
                $('#packageDetailsContent').empty();
                $('#packageDetailsModal').removeData('pdfPayload');

                // Ensure body is back to normal
                $('body').removeAttr('style');
                $('body').removeClass('modal-open');

                // Remove all modal backdrops
                $('.modal-backdrop').fadeOut(100, function() {
                    $(this).remove();
                });

                // Double-check scroll is enabled
                $('body').css('overflow-y', 'auto');
                document.body.style.overflow = '';
            });
            </script>
@endpush
