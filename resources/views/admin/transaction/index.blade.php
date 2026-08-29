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
.badge-guest-count { background: rgba(124,58,237,0.22); color: #c084fc; border: 1px solid rgba(124,58,237,0.38); font-size: 0.72rem; font-weight: 700; padding: 1px 6px; border-radius: 4px; margin-left: 4px; display: inline-block; vertical-align: middle; }
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

/* ─── Shopify Polaris Style Multi-Select Filters ─── */
.polaris-filter-bar {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
    background: rgba(15, 23, 42, 0.65);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    padding: 10px 14px;
    margin-bottom: 14px;
}
.polaris-filter-pill-btn {
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.12);
    color: #e2e8f0;
    font-size: 0.82rem;
    font-weight: 600;
    padding: 6px 14px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    transition: all 0.2s ease;
    user-select: none;
}
.polaris-filter-pill-btn:hover, .polaris-filter-pill-btn.active {
    background: rgba(124, 58, 237, 0.25);
    border-color: rgba(124, 58, 237, 0.5);
    color: #fff;
}
.polaris-filter-pill-count {
    background: #7c3aed;
    color: #fff;
    font-size: 0.7rem;
    font-weight: 700;
    padding: 1px 6px;
    border-radius: 999px;
}
.polaris-scroll-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    z-index: 1050;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: #1e293b;
    border: 1px solid rgba(255, 255, 255, 0.25);
    color: #ffffff !important;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.72rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.6);
    cursor: pointer;
    transition: all 0.2s ease-in-out;
}
.polaris-scroll-btn:hover {
    background: #7c3aed;
    border-color: #a78bfa;
    color: #ffffff !important;
}
.polaris-scroll-left {
    left: 4px;
}
.polaris-scroll-right {
    right: 4px;
}
.polaris-popover-menu {
    background: #1e293b !important;
    border: 1px solid rgba(255, 255, 255, 0.15) !important;
    border-radius: 12px !important;
    padding: 12px !important;
    min-width: 230px !important;
    max-width: 320px !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5) !important;
    z-index: 99999 !important;
}
.polaris-popover-menu,
.polaris-popover-menu label,
.polaris-popover-menu span,
.polaris-popover-menu p,
.polaris-popover-menu div {
    color: #ffffff !important;
}
.polaris-popover-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    padding-bottom: 8px;
    margin-bottom: 8px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}
.polaris-popover-title {
    font-size: 0.78rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #cbd5e1 !important;
    margin-right: 12px;
    white-space: nowrap;
}
.polaris-popover-action {
    font-size: 0.72rem;
    color: #a78bfa !important;
    cursor: pointer;
    text-decoration: none;
    font-weight: 600;
}
.polaris-popover-action:hover {
    color: #c4b5fd !important;
    text-decoration: underline;
}
.polaris-popover-menu select,
.polaris-popover-menu input {
    color: #ffffff !important;
    background: rgba(15, 23, 42, 0.9) !important;
}
.polaris-popover-menu select option {
    background: #1e293b !important;
    color: #ffffff !important;
}
.polaris-popover-body {
    max-height: 340px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 4px;
    padding-right: 4px;
}
.polaris-popover-body::-webkit-scrollbar {
    width: 6px;
}
.polaris-popover-body::-webkit-scrollbar-track {
    background: rgba(15, 23, 42, 0.6);
    border-radius: 4px;
}
.polaris-popover-body::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.25);
    border-radius: 4px;
}
.polaris-popover-body::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.4);
}
.polaris-checkbox-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.82rem;
    color: #e2e8f0;
    cursor: pointer;
    padding: 4px 6px;
    border-radius: 6px;
    transition: background 0.15s;
}
.polaris-checkbox-label:hover {
    background: rgba(255, 255, 255, 0.06);
}
.polaris-checkbox-label input[type="checkbox"] {
    accent-color: #7c3aed;
    width: 15px;
    height: 15px;
    cursor: pointer;
}
.polaris-chips-bar {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 6px;
    margin-top: 8px;
    width: 100%;
}
@media (max-width: 767.98px) {
    .polaris-chips-bar {
        flex-wrap: nowrap !important;
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch;
        padding-bottom: 4px;
    }
    .polaris-chip, .polaris-clear-all-btn {
        flex-shrink: 0 !important;
        white-space: nowrap !important;
    }
}
.polaris-chip {
    background: rgba(124, 58, 237, 0.18);
    border: 1px solid rgba(124, 58, 237, 0.35);
    color: #c4b5fd;
    font-size: 0.76rem;
    font-weight: 600;
    padding: 3px 10px;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.polaris-chip-remove {
    cursor: pointer;
    color: #a78bfa;
    font-size: 0.75rem;
    transition: color 0.15s;
}
.polaris-chip-remove:hover {
    color: #fff;
}
.polaris-clear-all-btn {
    background: transparent;
    border: none;
    color: #ef4444;
    font-size: 0.76rem;
    font-weight: 600;
    cursor: pointer;
    padding: 3px 8px;
    text-decoration: underline;
}
.polaris-clear-all-btn:hover {
    color: #fca5a5;
}
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

/* ─── Modern Mobile-Friendly DateRangePicker Styles ──────────────────── */
.daterangepicker {
    background: #1e293b !important;
    border: 1px solid rgba(255,255,255,0.15) !important;
    box-shadow: 0 20px 40px rgba(0,0,0,0.5) !important;
    color: #fff !important;
    border-radius: 12px !important;
    font-family: inherit !important;
}
.daterangepicker .calendar-table {
    background: transparent !important;
    border: none !important;
}
.daterangepicker .calendar-table th, 
.daterangepicker .calendar-table td {
    color: #e2e8f0 !important;
    border-radius: 6px !important;
}
.daterangepicker td.off,
.daterangepicker td.off.available,
.daterangepicker td.off.in-range,
.daterangepicker td.off.start-date,
.daterangepicker td.off.end-date,
.daterangepicker td.off.active {
    background-color: transparent !important;
    background: transparent !important;
    color: rgba(255, 255, 255, 0.9) !important;
    opacity: 0.3 !important;
    box-shadow: none !important;
}
.daterangepicker td:not(.off).start-date,
.daterangepicker td:not(.off).end-date,
.daterangepicker td:not(.off).active,
.daterangepicker td:not(.off).active:hover,
.daterangepicker td:not(.off).start-date.in-range,
.daterangepicker td:not(.off).end-date.in-range {
    background-color: #7c3aed !important;
    color: #ffffff !important;
    font-weight: 700 !important;
    opacity: 1 !important;
    border-radius: 6px !important;
}
.daterangepicker td:not(.off).in-range:not(.start-date):not(.end-date):not(.active) {
    background-color: rgba(124, 58, 237, 0.25) !important;
    color: #fff !important;
}
.daterangepicker .ranges li {
    background: rgba(255,255,255,0.06) !important;
    color: #e2e8f0 !important;
    border-radius: 6px !important;
    margin-bottom: 4px !important;
    font-size: 0.8rem !important;
}
.daterangepicker .ranges li.active, .daterangepicker .ranges li:hover {
    background: #7c3aed !important;
    color: #fff !important;
}
.daterangepicker .drp-buttons {
    border-top: 1px solid rgba(255,255,255,0.1) !important;
}
.daterangepicker .drp-buttons .btn {
    font-size: 0.8rem !important;
    font-weight: 600 !important;
    border-radius: 6px !important;
}

@media (max-width: 768px) {
    .txn-table-card .d-flex.justify-content-between {
        flex-direction: column !important;
        align-items: stretch !important;
    }

    .txn-table-actions-group {
        width: 100% !important;
        flex-direction: column !important;
        align-items: stretch !important;
    }

    .txn-search-wrap {
        width: 100% !important;
        max-width: 100% !important;
    }

    .txn-search-input {
        width: 100% !important;
    }

    .txn-action-buttons-wrap {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 8px !important;
        width: 100% !important;
    }

    .txn-action-buttons-wrap .dropdown {
        width: 100% !important;
        display: block !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .txn-action-buttons-wrap .btn,
    .txn-action-buttons-wrap .txn-export-btn,
    .txn-action-buttons-wrap .dropdown-toggle {
        width: 100% !important;
        height: 38px !important;
        line-height: 20px !important;
        margin: 0 !important;
        text-align: center !important;
        justify-content: center !important;
        display: flex !important;
        align-items: center !important;
        font-size: 0.78rem !important;
        padding: 8px 10px !important;
        white-space: nowrap !important;
        box-sizing: border-box !important;
        border-radius: 10px !important;
        background: rgba(255,255,255,0.07) !important;
        border: 1px solid rgba(255,255,255,0.1) !important;
        color: #fff !important;
    }

    .txn-action-buttons-wrap .dropdown-toggle::after {
        display: inline-block !important;
        margin-left: 4px !important;
    }

    #selectionCount {
        grid-column: 1 / -1 !important;
        text-align: center !important;
        font-size: 0.78rem !important;
        padding: 4px 0 !important;
        display: block !important;
    }

    /* Polaris Filter Bar Mobile Touch Scroll & Fixed Viewport Dropdowns */
    .polaris-filter-bar {
        display: flex !important;
        flex-wrap: nowrap !important;
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch !important;
        padding-bottom: 8px !important;
        gap: 8px !important;
        scrollbar-width: none !important;
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
    }
    .polaris-filter-bar::-webkit-scrollbar {
        display: none !important;
    }
    .polaris-filter-bar .dropdown {
        flex: 0 0 auto !important;
    }
    .polaris-filter-pill-btn {
        white-space: nowrap !important;
        font-size: 0.8rem !important;
        padding: 7px 12px !important;
    }
    .polaris-filter-bar .dropdown-menu.polaris-popover-menu {
        z-index: 1080 !important;
        max-width: calc(100vw - 32px) !important;
    }
    .txn-table-card {
        position: relative !important;
        z-index: 1 !important;
    }

    .daterangepicker {
        position: fixed !important;
        top: 50% !important;
        left: 50% !important;
        transform: translate(-50%, -50%) !important;
        width: 92vw !important;
        max-width: 350px !important;
        z-index: 999999 !important;
        box-shadow: 0 20px 50px rgba(0,0,0,0.85), 0 0 0 100vw rgba(0,0,0,0.65) !important;
        padding: 12px !important;
    }
    .daterangepicker .drp-calendar.left {
        width: 100% !important;
        max-width: 100% !important;
        padding: 0 !important;
        float: none !important;
    }
    .daterangepicker .drp-calendar.right {
        display: none !important;
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
            $redeemedTxns      = $reportableData->filter(function ($t) {
                $status = (string) ($t->checked_in_status ?? $t->checked_in ?? '0');
                return $status === '1' || strtolower($status) === 'true' || strtolower($status) === 'checked_in';
            })->count();
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

            // Top packages donut with Club Name
            $allPkgGroups = $reportableData->where('type', 'package')
                ->groupBy(function($t) {
                    $vName = optional($t->website)->name ?: optional(optional($t->event)->website)->name ?: optional(optional($t->package)->website)->name ?: '';
                    $pName = $t->package_table_label ?: 'Package';
                    return $vName !== '' ? ($vName . ' - ' . $pName) : $pName;
                })
                ->map(fn($g, $key) => ['name' => $key, 'revenue' => (float)$g->sum('total')])
                ->sortByDesc('revenue')->values();
            $top4         = $allPkgGroups->take(4);
            $otherRevenue = (float) $allPkgGroups->slice(4)->sum('revenue');
            $topPackages  = $otherRevenue > 0 ? $top4->push(['name' => 'Other', 'revenue' => $otherRevenue]) : $top4;
            $topPackagesTotal = (float) $topPackages->sum('revenue');

            // affiliate names for filter
            $referralRows = $data->map(function ($row) {
                if (!empty($row->affiliate_id) && !empty($row->affiliate)) {
                    if ($row->affiliate->isSubAffiliate()) {
                        $parent = $row->affiliate->parent;
                        $parentName = $parent ? ($parent->display_name ?: optional($parent->user)->name) : 'Main Promoter';
                        $subName = $row->affiliate->display_name ?: optional($row->affiliate->user)->name ?: ('Sub Promoter #' . $row->affiliate_id);
                        return $subName . ' (Main: ' . $parentName . ')';
                    }
                    return $row->affiliate->display_name ?: optional($row->affiliate->user)->name ?: ('affiliate #' . $row->affiliate_id);
                }
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
        </div>

        {{-- ── STAT CARDS ──────────────────────────────────────────── --}}
        <div class="row row-cols-1 row-cols-md-3 g-3 mb-4">
            <div class="col">
                <div class="txn-stat-card">
                    <div class="txn-stat-icon" style="background:rgba(124,58,237,0.15);color:#7c3aed"><i class="fas fa-receipt"></i></div>
                    <div>
                        <div class="txn-stat-label">Total Transactions</div>
                        <div class="txn-stat-value">{{ number_format($totalTxns) }}</div>
                        <div class="txn-stat-trend {{ $txnTrend >= 0 ? 'trend-up' : 'trend-down' }}" style="display:none !important;">
                            <i class="fas fa-arrow-{{ $txnTrend >= 0 ? 'up' : 'down' }} me-1"></i>{{ abs($txnTrend) }}% <span>vs last week</span>
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
                        <div class="txn-stat-trend {{ $revenueTrend >= 0 ? 'trend-up' : 'trend-down' }}" style="display:none !important;">
                            <i class="fas fa-arrow-{{ $revenueTrend >= 0 ? 'up' : 'down' }} me-1"></i>{{ abs($revenueTrend) }}% <span>vs last week</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col" style="display:none !important;">
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
                        <!-- <div class="txn-stat-note">Guests in filtered transactions</div> -->
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
            <div class="col-12">
                <div class="txn-chart-card" id="performanceChartCard">
                    <div class="txn-chart-header">
                        <div class="fw-semibold text-white" style="font-size:0.85rem;letter-spacing:0.05em">PERFORMANCE OVER TIME</div>
                        <select class="txn-period-select" id="chartPeriod" style="display:none !important;">
                            <option value="7">By Day (7d)</option>
                            <option value="14">By Day (14d)</option>
                            <option value="30" selected>By Day (30d)</option>
                        </select>
                    </div>
                    <div class="d-flex flex-wrap gap-4 mb-3">
                        <div class="txn-chart-legend"><span style="background:#7c3aed"></span>Revenue</div>
                        <div class="txn-chart-legend" style="display:none !important;"><span style="background:#f59e0b"></span>Fee</div>
                    </div>
                    <canvas id="txnLineChart" style="max-height:220px"></canvas>
                </div>
            </div>
        </div>

        {{-- ── TRANSACTIONS TABLE ──────────────────────────────────── --}}
        <div class="txn-table-card mb-5">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
                <div class="fw-semibold text-white" style="font-size:0.85rem;letter-spacing:0.05em">RECENT TRANSACTIONS</div>
                <div class="txn-table-actions-group d-flex align-items-center gap-2 flex-wrap justify-content-end">
                    <div class="txn-search-wrap">
                        <i class="fas fa-search txn-search-icon"></i>
                        <input type="text" id="txnSearch" class="txn-search-input" placeholder="Search by name, email, order ID…">
                    </div>
                    <div class="txn-action-buttons-wrap d-flex align-items-center gap-2 flex-wrap">
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

            {{-- Shopify Polaris Style Multi-Select Filter Toolbar --}}
            @php
                $accessibleSitesList = isset($accessibleWebsites) && $accessibleWebsites->count() > 0 
                    ? $accessibleWebsites 
                    : (auth()->user()->isAdmin() ? \App\Models\Website::where('is_archieved', 0)->get() : collect());
            @endphp
            <div class="position-relative mb-3">
                <div class="polaris-filter-bar mb-0" id="polarisFilterContainer">
                @if($accessibleSitesList->count() > 1)
                {{-- 1. Venue Filter --}}
                <div class="dropdown">
                    <button class="polaris-filter-pill-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" id="pillVenueBtn">
                        <i class="fas fa-store"></i> Venue <span class="polaris-filter-pill-count d-none" id="countVenue">0</span>
                    </button>
                    <div class="dropdown-menu polaris-popover-menu" style="min-width: 250px !important;">
                        <div class="polaris-popover-header">
                            <span class="polaris-popover-title me-3">Filter by Venue</span>
                            <div>
                                <a href="javascript:void(0)" class="polaris-popover-action me-2" onclick="polarisToggleSelectAll('venue', true)">Select All</a>
                                <a href="javascript:void(0)" class="polaris-popover-action" onclick="polarisToggleSelectAll('venue', false)">Clear</a>
                            </div>
                        </div>
                        <div class="mb-2">
                            <input type="text" class="form-control form-control-sm bg-dark text-white border-secondary" placeholder="Search venues..." onkeyup="filterVenueDropdownList(this.value)" style="font-size: 0.78rem; padding: 4px 8px; border-radius: 6px;">
                        </div>
                        <div class="polaris-popover-body" id="venuePopoverBody">
                            @foreach($accessibleSitesList as $site)
                            <label class="polaris-checkbox-label venue-item-label">
                                <input type="checkbox" class="polaris-filter-cb" data-category="venue" value="{{ $site->name }}" {{ $filterWebsite === $site->name ? 'checked' : '' }}>
                                <span class="venue-item-name">{{ $site->name }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                {{-- 2. Date Filter --}}
                <div class="dropdown">
                    <button class="polaris-filter-pill-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" id="pillDateRangeBtn">
                        <i class="fas fa-calendar-alt"></i> Date Filter <span class="polaris-filter-pill-count d-none" id="countDateRange">0</span>
                    </button>
                    <div class="dropdown-menu polaris-popover-menu" style="min-width: 280px !important;">
                        <div class="polaris-popover-header">
                            <span class="polaris-popover-title me-3">Filter by Date</span>
                            <div>
                                <a href="javascript:void(0)" class="polaris-popover-action" onclick="clearPolarisDateRange()">Clear</a>
                            </div>
                        </div>
                        <div class="polaris-popover-body">
                            <div class="mb-2">
                                <label class="form-label text-white-50 small mb-1">Date Target:</label>
                                <select id="dateTargetSelect" class="form-select form-select-sm" style="background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.15);color:#fff;font-size:0.8rem;border-radius:6px;">
                                    <option value="either" selected>Either (Sale or Reservation Date)</option>
                                    <option value="sale">Sale Date (Transaction Date)</option>
                                    <option value="reservation">Reservation Date (Usage Date)</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label text-white-50 small mb-1">Date Range:</label>
                                <div class="txn-date-range-wrap w-100" id="txnDateRangeWrap" style="background:rgba(255,255,255,0.08);border-color:rgba(255,255,255,0.15);">
                                    <i class="fas fa-calendar-alt me-2" style="color:rgba(255,255,255,0.4);font-size:0.85rem"></i>
                                    <input type="text" id="txnDateRange" class="txn-date-input w-100" readonly placeholder="All time" value="{{ $initialDateRange }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. Reservation Status --}}
                <div class="dropdown">
                    <button class="polaris-filter-pill-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" id="pillReservationBtn">
                        <i class="fas fa-calendar-check"></i> Reservation Status <span class="polaris-filter-pill-count d-none" id="countReservation">0</span>
                    </button>
                    <div class="dropdown-menu polaris-popover-menu">
                        <div class="polaris-popover-header">
                            <span class="polaris-popover-title me-3">Reservation State</span>
                            <div>
                                <a href="javascript:void(0)" class="polaris-popover-action me-2" onclick="polarisToggleSelectAll('reservation', true)">Select All</a>
                                <a href="javascript:void(0)" class="polaris-popover-action" onclick="polarisToggleSelectAll('reservation', false)">Clear</a>
                            </div>
                        </div>
                        <div class="polaris-popover-body">
                            <label class="polaris-checkbox-label">
                                <input type="checkbox" class="polaris-filter-cb" data-category="reservation" value="upcoming" {{ $filterReservation === 'upcoming' ? 'checked' : '' }}>
                                <span>Upcoming</span>
                            </label>
                            <label class="polaris-checkbox-label">
                                <input type="checkbox" class="polaris-filter-cb" data-category="reservation" value="today" {{ $filterReservation === 'today' ? 'checked' : '' }}>
                                <span>Today</span>
                            </label>
                            <label class="polaris-checkbox-label">
                                <input type="checkbox" class="polaris-filter-cb" data-category="reservation" value="past" {{ $filterReservation === 'past' ? 'checked' : '' }}>
                                <span>Past</span>
                            </label>
                            <label class="polaris-checkbox-label">
                                <input type="checkbox" class="polaris-filter-cb" data-category="reservation" value="checked_in" {{ $filterReservation === 'checked_in' ? 'checked' : '' }}>
                                <span>Checked In</span>
                            </label>
                            <label class="polaris-checkbox-label">
                                <input type="checkbox" class="polaris-filter-cb" data-category="reservation" value="not_checked_in" {{ $filterReservation === 'not_checked_in' ? 'checked' : '' }}>
                                <span>Not Checked In</span>
                            </label>
                            <label class="polaris-checkbox-label">
                                <input type="checkbox" class="polaris-filter-cb" data-category="reservation" value="no_show" {{ $filterReservation === 'no_show' ? 'checked' : '' }}>
                                <span>No Show</span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- 4. Sales Channel --}}
                <div class="dropdown">
                    <button class="polaris-filter-pill-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" id="pillAffiliateBtn">
                        <i class="fas fa-user-tag"></i> Sales Channel <span class="polaris-filter-pill-count d-none" id="countAffiliate">0</span>
                    </button>
                    <div class="dropdown-menu polaris-popover-menu">
                        <div class="polaris-popover-header">
                            <span class="polaris-popover-title me-3">Sales Channel / Source</span>
                            <div>
                                <a href="javascript:void(0)" class="polaris-popover-action me-2" onclick="polarisToggleSelectAll('affiliate', true)">Select All</a>
                                <a href="javascript:void(0)" class="polaris-popover-action" onclick="polarisToggleSelectAll('affiliate', false)">Clear</a>
                            </div>
                        </div>
                        <div class="polaris-popover-body">
                            <label class="polaris-checkbox-label">
                                <input type="checkbox" class="polaris-filter-cb" data-category="affiliate" value="Direct" {{ $filterAffiliate === 'Direct' ? 'checked' : '' }}>
                                <span>Direct (No promoter)</span>
                            </label>
                            @foreach($referralRows as $rn)
                            <label class="polaris-checkbox-label">
                                <input type="checkbox" class="polaris-filter-cb" data-category="affiliate" value="{{ $rn }}" {{ $filterAffiliate === $rn ? 'checked' : '' }}>
                                <span>{{ $rn }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- 5. Transaction Type --}}
                <div class="dropdown">
                    <button class="polaris-filter-pill-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" id="pillTypeBtn">
                        <i class="fas fa-tags"></i> Transaction Type <span class="polaris-filter-pill-count d-none" id="countType">0</span>
                    </button>
                    <div class="dropdown-menu polaris-popover-menu">
                        <div class="polaris-popover-header">
                            <span class="polaris-popover-title me-3">Transaction Type</span>
                            <div>
                                <a href="javascript:void(0)" class="polaris-popover-action me-2" onclick="polarisToggleSelectAll('type', true)">Select All</a>
                                <a href="javascript:void(0)" class="polaris-popover-action" onclick="polarisToggleSelectAll('type', false)">Clear</a>
                            </div>
                        </div>
                        <div class="polaris-popover-body">
                            <label class="polaris-checkbox-label">
                                <input type="checkbox" class="polaris-filter-cb" data-category="type" value="Package" {{ $filterType === 'Package' ? 'checked' : '' }}>
                                <span>Package Purchase</span>
                            </label>
                            <label class="polaris-checkbox-label">
                                <input type="checkbox" class="polaris-filter-cb" data-category="type" value="Reservation" {{ $filterType === 'Reservation' ? 'checked' : '' }}>
                                <span>Table Reservation</span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- 6. Payment Status --}}
                <div class="dropdown">
                    <button class="polaris-filter-pill-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" id="pillStatusBtn">
                        <i class="fas fa-check-circle"></i> Payment Status <span class="polaris-filter-pill-count d-none" id="countStatus">0</span>
                    </button>
                    <div class="dropdown-menu polaris-popover-menu">
                        <div class="polaris-popover-header">
                            <span class="polaris-popover-title me-3">Payment Status</span>
                            <div>
                                <a href="javascript:void(0)" class="polaris-popover-action me-2" onclick="polarisToggleSelectAll('status', true)">Select All</a>
                                <a href="javascript:void(0)" class="polaris-popover-action" onclick="polarisToggleSelectAll('status', false)">Clear</a>
                            </div>
                        </div>
                        <div class="polaris-popover-body">
                            <label class="polaris-checkbox-label">
                                <input type="checkbox" class="polaris-filter-cb" data-category="status" value="Completed" {{ $filterStatus === 'Completed' ? 'checked' : '' }}>
                                <span><i class="fas fa-circle text-success me-1" style="font-size:0.6rem;"></i> Completed</span>
                            </label>
                            <label class="polaris-checkbox-label">
                                <input type="checkbox" class="polaris-filter-cb" data-category="status" value="Canceled" {{ $filterStatus === 'Canceled' ? 'checked' : '' }}>
                                <span><i class="fas fa-circle text-danger me-1" style="font-size:0.6rem;"></i> Canceled</span>
                            </label>
                            <label class="polaris-checkbox-label">
                                <input type="checkbox" class="polaris-filter-cb" data-category="status" value="Refunded" {{ $filterStatus === 'Refunded' ? 'checked' : '' }}>
                                <span><i class="fas fa-circle text-warning me-1" style="font-size:0.6rem;"></i> Refunded</span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- 7. Host Name Filter --}}
                <div class="dropdown">
                    <button class="polaris-filter-pill-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" id="pillHostBtn">
                        <i class="fas fa-user-tie"></i> Host Name <span class="polaris-filter-pill-count d-none" id="countHost">0</span>
                    </button>
                    <div class="dropdown-menu polaris-popover-menu">
                        <div class="polaris-popover-header">
                            <span class="polaris-popover-title me-3">Filter by Host Name</span>
                            <div>
                                <a href="javascript:void(0)" class="polaris-popover-action me-2" onclick="polarisToggleSelectAll('host', true)">Select All</a>
                                <a href="javascript:void(0)" class="polaris-popover-action" onclick="polarisToggleSelectAll('host', false)">Clear</a>
                            </div>
                        </div>
                        <div class="polaris-popover-body">
                            <label class="polaris-checkbox-label">
                                <input type="checkbox" class="polaris-filter-cb" data-category="host" value="has_host">
                                <span><i class="fas fa-check text-success me-1"></i> Has Host Name</span>
                            </label>
                            <label class="polaris-checkbox-label">
                                <input type="checkbox" class="polaris-filter-cb" data-category="host" value="no_host">
                                <span><i class="fas fa-times text-muted me-1"></i> No Host Name</span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Hidden legacy compatibility elements --}}
                <div class="d-none" id="txnFiltersRow">
                    <select id="websiteFilter"><option value="">All</option></select>
                    <select id="typeFilter"><option value="">All</option></select>
                    <select id="affiliateFilter"><option value="">All</option></select>
                    <select id="statusFilter"><option value="">All</option></select>
                    <select id="reservationFilter"><option value="">All</option></select>
                </div>
            </div>
                <button type="button" id="polarisScrollLeftBtn" class="polaris-scroll-btn polaris-scroll-left d-md-none d-none" aria-label="Scroll left"><i class="fas fa-chevron-left"></i></button>
                <button type="button" id="polarisScrollRightBtn" class="polaris-scroll-btn polaris-scroll-right d-md-none d-none" aria-label="Scroll right"><i class="fas fa-chevron-right"></i></button>
            </div>

            {{-- Active Filter Chips Container (rendered below the filter bar) --}}
            <div class="polaris-chips-bar d-none mb-3" id="activeFilterChips">
                <!-- Dynamically rendered active chips -->
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
            <div style="display:none !important;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:16px;margin-bottom:24px;">
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
                            <th>Host Name</th>
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
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="8" class="text-end" style="color:rgba(255,255,255,0.5);font-size:0.82rem">Total:</th>
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

            <!-- Dedicated Transaction Notes Modal -->
            <div class="modal fade" id="txnNotesModal" tabindex="-1" aria-labelledby="txnNotesModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content" style="background:#121726;border:1px solid rgba(255,255,255,0.15);border-radius:12px;">
                        <div class="modal-header border-bottom border-secondary border-opacity-25">
                            <h5 class="modal-title text-white" id="txnNotesModalLabel">
                                <i class="fas fa-sticky-note text-warning me-2"></i>Notes (<span id="txnNotesModalOrderTitle"></span>)
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter:invert(1);"></button>
                        </div>
                        <div class="modal-body" id="txnNotesModalBody">
                            <!-- Dynamically populated notes form -->
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
            .daterangepicker td.off.end-date,
            .daterangepicker td.off.active {
                color: rgba(255, 255, 255, 0.2) !important;
                background-color: transparent !important;
                background: transparent !important;
                opacity: 0.3 !important;
                box-shadow: none !important;
            }
            .daterangepicker td:not(.off).start-date,
            .daterangepicker td:not(.off).end-date,
            .daterangepicker td:not(.off).active,
            .daterangepicker td:not(.off).active:hover,
            .daterangepicker td:not(.off).start-date.in-range,
            .daterangepicker td:not(.off).end-date.in-range {
                background-color: #ffcc00 !important;
                color: #1a1400 !important;
                border-radius: 6px !important;
                font-weight: 700 !important;
                opacity: 1 !important;
            }
            .daterangepicker td:not(.off).in-range:not(.start-date):not(.end-date):not(.active) { background-color: rgba(255,204,0,0.15) !important; color: #fff !important; }
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
            window.lineChartInstance = lineChart;

            document.getElementById('chartPeriod').addEventListener('change', function() {
                if (typeof updateChartsFromFilteredRows === 'function') {
                    updateChartsFromFilteredRows();
                } else {
                    const period = this.value;
                    const data = period === '7' ? chart7Data : period === '14' ? chart14Data : allChartData;
                    lineChart.destroy();
                    lineChart = new Chart(lineCtx, buildLineChart(data));
                    window.lineChartInstance = lineChart;
                    setTimeout(syncChartHeights, 200);
                }
            });

            // ── Dynamic Chart Updater for Filtered Views ─────────────────────
            window.updateChartsFromFilteredRows = function() {
                const activeTable = window.table || (typeof table !== 'undefined' ? table : null);
                if (!activeTable) return;

                const filteredRows = activeTable.rows({ search: 'applied' }).nodes();
                const dateRevenueMap = {};

                $(filteredRows).each(function() {
                    const $row = $(this);
                    const $viewBtn = $row.find('.btn-link-package').first();
                    if (!$viewBtn.length) return;

                    const rawStatus = String($viewBtn.data('status') || '').trim().toLowerCase();
                    const statusMap = { '1': 'completed', 'completed': 'completed', 'approved': 'completed' };
                    const isCompleted = statusMap[rawStatus] === 'completed';

                    const amountText = String($row.find('td.txn-amount').first().text() || '');
                    const rowRevenue = isCompleted ? (parseFloat(amountText.replace(/[^0-9.-]+/g, '')) || 0) : 0;

                    // Date grouping (Sale Date in PST)
                    const saleIso = String($viewBtn.data('date-iso') || '').trim();
                    let saleMom = null;
                    if (saleIso && saleIso.length >= 10) {
                        saleMom = moment(saleIso.substring(0, 10), 'YYYY-MM-DD');
                    } else {
                        const saleDateRaw = String($viewBtn.data('date') || '').trim();
                        if (typeof parseRowDateToMoment === 'function') {
                            saleMom = parseRowDateToMoment(saleDateRaw);
                        } else if (typeof moment !== 'undefined') {
                            saleMom = moment(saleDateRaw, ['MMM DD, YYYY', 'YYYY-MM-DD']);
                        }
                    }
                    if (saleMom && saleMom.isValid()) {
                        const dayKey = saleMom.format('MMM DD');
                        dateRevenueMap[dayKey] = (dateRevenueMap[dayKey] || 0) + rowRevenue;
                    }
                });

                // 1. Update Line Chart (Performance Over Time)
                if (window.lineChartInstance) {
                    const labels = [];
                    const revenues = [];

                    const dateRangeStr = String($('#txnDateRange').val() || '').trim();
                    let customStart = null;
                    let customEnd = null;

                    if (dateRangeStr && dateRangeStr.includes(' - ')) {
                        const parts = dateRangeStr.split(' - ');
                        const sMom = moment(parts[0], 'MM/DD/YYYY', true);
                        const eMom = moment(parts[1], 'MM/DD/YYYY', true);
                        if (sMom.isValid() && eMom.isValid() && eMom.isSameOrAfter(sMom)) {
                            customStart = sMom;
                            customEnd = eMom;
                        }
                    }

                    if (customStart && customEnd) {
                        const curr = customStart.clone();
                        while (curr.isSameOrBefore(customEnd, 'day')) {
                            const dayKey = curr.format('MMM DD');
                            labels.push(dayKey);
                            revenues.push(dateRevenueMap[dayKey] || 0);
                            curr.add(1, 'day');
                        }
                    } else {
                        const period = $('#chartPeriod').val() || '30';
                        const numDays = parseInt(period, 10) || 30;
                        const pstNow = (typeof getPstMoment === 'function') ? getPstMoment() : moment();

                        for (let i = numDays - 1; i >= 0; i--) {
                            const dayStr = pstNow.clone().subtract(i, 'days').format('MMM DD');
                            labels.push(dayStr);
                            revenues.push(dateRevenueMap[dayStr] || 0);
                        }
                    }

                    window.lineChartInstance.data.labels = labels;
                    window.lineChartInstance.data.datasets[0].data = revenues;
                    window.lineChartInstance.update();
                }
            };
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

                // Initialize DataTable with server-side pagination
                let table = $('#txnDataTable').DataTable({
                    processing: true,
                    serverSide: true,
                    pageLength: 25,
                    searching: true,
                    ordering: true,
                    paging: true,
                    info: true,
                    lengthChange: true,
                    autoWidth: false,
                    ajax: {
                        url: "{{ route('admin.transaction.filter-ajax') }}",
                        type: "POST",
                        data: function(d) {
                            d._token = "{{ csrf_token() }}";
                            d.venue = $('.polaris-filter-cb[data-category="venue"]:checked').map(function() { return $(this).val(); }).get();
                            d.status = $('.polaris-filter-cb[data-category="status"]:checked').map(function() { return $(this).val(); }).get();
                            d.type = $('.polaris-filter-cb[data-category="type"]:checked').map(function() { return $(this).val(); }).get();
                            d.affiliate = $('.polaris-filter-cb[data-category="affiliate"]:checked').map(function() { return $(this).val(); }).get();
                            d.reservation = $('.polaris-filter-cb[data-category="reservation"]:checked').map(function() { return $(this).val(); }).get();
                            d.date_range = $('#txnDateRange').val();
                            d.date_target = $('#dateTargetSelect').val();
                            d.search_custom = $('#txnSearch').val();
                            d.is_payout_page = "{{ !empty($isPayoutPage) ? 1 : 0 }}";
                            d.is_affiliate_only = "{{ request()->routeIs('admin.transaction.affiliate') ? 1 : 0 }}";
                            d.is_entertainer_only = "{{ request()->routeIs('admin.transaction.entertainer') ? 1 : 0 }}";
                            d.archived = "{{ request()->boolean('archived') ? 1 : 0 }}";
                        }
                    },
                    language: {
                        emptyTable: 'No transactions found.',
                        processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>'
                    },
                    columnDefs: [
                        { orderable: false, targets: nonOrderableTargets }
                    ]
                });
                window.table = table;

                table.on('xhr.dt', function(e, settings, json, xhr) {
                    if (json && json.stats) {
                        setStatValueByLabel('Total Sales', json.stats.totalTxns);
                        setStatValueByLabel('Total Revenue', '$' + json.stats.totalRevenue);
                        setStatValueByLabel('Pending Commission', '$' + json.stats.pendingCommission);
                        setStatValueByLabel('Total Guests', json.stats.totalGuests);
                        setStatValueByLabel('Pending Payout', '$' + json.stats.pendingPayoutAmount);
                        setStatValueByLabel('Payout Amount', '$' + json.stats.payoutAmount);
                        setStatValueByLabel('Total Earnings', '$' + json.stats.totalEarning);
                        setStatValueByLabel('Available Now', '$' + json.stats.availableNow);
                        setStatValueByLabel('Lifetime Earned', '$' + json.stats.lifetimeEarned);
                    }
                });

                $('#txnDataTable thead').on('click mousedown', '#selectAll', function(e) {
                    e.stopPropagation();
                });

                // ── Custom search & Polaris Multi-Select Filter Controller ──
                window.polarisToggleSelectAll = function(category, selectAll) {
                    $('.polaris-filter-cb[data-category="' + category + '"]').prop('checked', selectAll);
                    updatePolarisUiAndFilterTable();
                };

                window.clearPolarisDateRange = function() {
                    $('#txnDateRange').val('');
                    const picker = $('#txnDateRange').data('daterangepicker');
                    if (picker) {
                        picker.setStartDate(moment());
                        picker.setEndDate(moment());
                    }
                    updatePolarisUiAndFilterTable();
                };

                function updatePolarisUiAndFilterTable() {
                    if (!table) return;

                    const categories = ['venue', 'status', 'type', 'affiliate', 'reservation', 'host'];
                    const activeChipsContainer = $('#activeFilterChips');
                    activeChipsContainer.empty();
                    let totalActiveFilters = 0;

                    categories.forEach(function(cat) {
                        const checkedBoxes = $('.polaris-filter-cb[data-category="' + cat + '"]:checked');
                        const count = checkedBoxes.length;

                        const pillBtn = $('#pill' + cat.charAt(0).toUpperCase() + cat.slice(1) + 'Btn');
                        const countBadge = $('#count' + cat.charAt(0).toUpperCase() + cat.slice(1));

                        if (count > 0) {
                            pillBtn.addClass('active');
                            countBadge.text(count).removeClass('d-none');
                            totalActiveFilters += count;

                            const labels = [];
                            checkedBoxes.each(function() {
                                labels.push($(this).parent().text().trim());
                            });

                            const categoryNameMap = {
                                venue: 'Venue',
                                status: 'Status',
                                type: 'Type',
                                affiliate: 'Referral',
                                reservation: 'Reservation',
                                host: 'Host Name'
                            };

                            const chipHtml = `
                                <div class="polaris-chip">
                                    <span>${categoryNameMap[cat]}: ${labels.join(', ')}</span>
                                    <i class="fas fa-times polaris-chip-remove" onclick="clearPolarisCategory('${cat}')"></i>
                                </div>
                            `;
                            activeChipsContainer.append(chipHtml);
                        } else {
                            pillBtn.removeClass('active');
                            countBadge.text('0').addClass('d-none');
                        }
                    });

                    // Handle Date Range Chip
                    const dateRangeVal = String($('#txnDateRange').val() || '').trim();
                    if (dateRangeVal) {
                        totalActiveFilters += 1;
                        $('#pillDateRangeBtn').addClass('active');
                        $('#countDateRange').text('1').removeClass('d-none');

                        const targetLabelMap = {
                            either: 'Sale/Usage',
                            sale: 'Sale Date',
                            reservation: 'Usage Date'
                        };
                        const currentTarget = String($('#dateTargetSelect').val() || 'either').toLowerCase();
                        const targetLabel = targetLabelMap[currentTarget] || 'Sale/Usage';

                        activeChipsContainer.append(`
                            <div class="polaris-chip">
                                <span>Date (${targetLabel}): ${dateRangeVal}</span>
                                <i class="fas fa-times polaris-chip-remove" onclick="clearPolarisDateRange()"></i>
                            </div>
                        `);
                    } else {
                        $('#pillDateRangeBtn').removeClass('active');
                        $('#countDateRange').text('0').addClass('d-none');
                    }

                    if (totalActiveFilters > 0) {
                        activeChipsContainer.append(`
                            <button type="button" class="polaris-clear-all-btn" onclick="clearAllPolarisFilters()">Clear all filters</button>
                        `);
                        activeChipsContainer.removeClass('d-none');
                    } else {
                        activeChipsContainer.addClass('d-none');
                    }

                    table.draw();
                }

                window.clearPolarisCategory = function(cat) {
                    $('.polaris-filter-cb[data-category="' + cat + '"]').prop('checked', false);
                    updatePolarisUiAndFilterTable();
                };

                window.clearAllPolarisFilters = function() {
                    $('.polaris-filter-cb').prop('checked', false);
                    $('#txnSearch').val('');
                    $('#txnDateRange').val('');
                    const picker = $('#txnDateRange').data('daterangepicker');
                    if (picker) {
                        picker.setStartDate(moment());
                        picker.setEndDate(moment());
                    }
                    table.search('').draw();
                    updatePolarisUiAndFilterTable();
                };

                $(document).on('change', '.polaris-filter-cb, #dateTargetSelect', function() {
                    updatePolarisUiAndFilterTable();
                });

                $('#txnSearch').on('keyup input', function() {
                    if (!table) return;
                    table.search(this.value).draw();
                });

                const $txnDateRange = $('#txnDateRange');

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

                function getPstMoment() {
                    if (typeof moment.tz === 'function') {
                        return moment.tz('America/Los_Angeles');
                    }
                    return moment().utcOffset('-07:00');
                }

                const pstNow = getPstMoment();
                const dateRangeOptions = {
                    autoUpdateInput: false,
                    linkedCalendars: false,
                    alwaysShowCalendars: true,
                    opens: 'left',
                    showDropdowns: true,
                    locale: { cancelLabel: 'Clear', applyLabel: 'Apply', format: 'MM/DD/YYYY' },
                    ranges: {
                        'Today': [pstNow.clone(), pstNow.clone()],
                        'Yesterday': [pstNow.clone().subtract(1, 'days'), pstNow.clone().subtract(1, 'days')],
                        'Last 7 Days': [pstNow.clone().subtract(6, 'days'), pstNow.clone()],
                        'Last 30 Days': [pstNow.clone().subtract(29, 'days'), pstNow.clone()],
                        'This Month': [pstNow.clone().startOf('month'), pstNow.clone().endOf('month')],
                        'Last Month': [pstNow.clone().subtract(1, 'month').startOf('month'), pstNow.clone().subtract(1, 'month').endOf('month')]
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
                    if (picker.setStartDate && picker.setEndDate) {
                        picker.setStartDate(picker.startDate);
                        picker.setEndDate(picker.endDate);
                    }
                    updatePolarisUiAndFilterTable();
                });

                $txnDateRange.off('cancel.daterangepicker.txnDateRange').on('cancel.daterangepicker.txnDateRange', function() {
                    $(this).val('');
                    reloadWithServerFilters();
                });

                $txnDateRange.off('click.txnDateRange').on('click.txnDateRange', function(e) {
                    e.stopPropagation();
                    const picker = $(this).data('daterangepicker');
                    if (picker) {
                        picker.show();
                    }
                });

                // Prevent click / touch events inside DateRangePicker from prematurely closing parent Bootstrap dropdowns
                $(document).on('click mousedown touchstart touchend', '.daterangepicker, .daterangepicker *', function(e) {
                    e.stopPropagation();
                });

                // Body Teleport for Polaris Filter Dropdowns (escapes all overflow & stacking contexts)
                $(document).on('show.bs.dropdown', '#polarisFilterContainer .dropdown', function () {
                    var $dropdown = $(this);
                    var $btn = $dropdown.find('.dropdown-toggle');
                    var $menu = $dropdown.find('.dropdown-menu');

                    if (!$btn.length || !$menu.length) return;

                    $menu.data('orig-parent', $dropdown);
                    $('body').append($menu);

                    var rect = $btn[0].getBoundingClientRect();
                    var menuWidth = $menu.outerWidth() || 260;
                    if (window.innerWidth < 768) {
                        menuWidth = Math.min(menuWidth, window.innerWidth - 32);
                    }

                    var left = rect.left;
                    if (left + menuWidth > window.innerWidth - 16) {
                        left = Math.max(16, window.innerWidth - menuWidth - 16);
                    }
                    if (left < 16) {
                        left = 16;
                    }

                    var top = rect.bottom + 4;

                    $menu.css({
                        'position': 'fixed',
                        'top': top + 'px',
                        'left': left + 'px',
                        'margin': '0',
                        'transform': 'none',
                        'z-index': '99999',
                        'display': 'block'
                    });
                });

                $(document).on('hide.bs.dropdown hidden.bs.dropdown', '#polarisFilterContainer .dropdown', function () {
                    var $dropdown = $(this);
                    var $menu = $('body > .polaris-popover-menu').filter(function() {
                        return $(this).data('orig-parent') && $(this).data('orig-parent')[0] === $dropdown[0];
                    });

                    if ($menu.length) {
                        $menu.css({
                            'position': '',
                            'top': '',
                            'left': '',
                            'margin': '',
                            'transform': '',
                            'z-index': '',
                            'display': ''
                        });
                        $dropdown.append($menu);
                    }
                });

                $(document).on('click mousedown touchstart', '.polaris-popover-menu', function(e) {
                    e.stopPropagation();
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
                        if (headerText === 'reservation status') return;
                        if (headerText === 'entry status') return;
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

                function getCleanCellContent(rowNode, colIdx, rawVal) {
                    if (rowNode) {
                        const $td = $(rowNode).children('td').eq(colIdx);
                        if ($td.length) {
                            const $clone = $td.clone();
                            $clone.find('button, .btn, .view-btn, .dropdown, script, style').remove();
                            const text = $clone.text().replace(/\s+/g, ' ').trim();
                            if (text !== '') {
                                return text;
                            }
                        }
                    }

                    if (rawVal != null) {
                        if (typeof rawVal === 'object') {
                            const disp = rawVal.display || rawVal['@data-order'] || rawVal.text || '';
                            const tmp = document.createElement('div');
                            tmp.innerHTML = String(disp);
                            $(tmp).find('button, .btn, .view-btn, .dropdown, script, style').remove();
                            return (tmp.textContent || tmp.innerText || '').replace(/\s+/g, ' ').trim();
                        }
                        const tmp = document.createElement('div');
                        tmp.innerHTML = String(rawVal);
                        $(tmp).find('button, .btn, .view-btn, .dropdown, script, style').remove();
                        return (tmp.textContent || tmp.innerText || '').replace(/\s+/g, ' ').trim();
                    }

                    return '';
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
                                    return getCleanCellContent(rowNode, colIdx, rowData ? rowData[colIdx] : null);
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

                    const summary = dataset.summary || {};
                    const money = function(val) {
                        return '$' + Number(val || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    };

                    const lines = [];
                    lines.push(csvEscape('Transactions Export Summary'));
                    lines.push([csvEscape('Total Transactions'), csvEscape(String(summary.totalTransactions || 0)), csvEscape('Completed Transactions'), csvEscape(String(summary.completedTransactions || 0))].join(','));
                    lines.push([csvEscape('Total Revenue'), csvEscape(money(summary.totalRevenue)), csvEscape('Pending Fee'), csvEscape(money(summary.pendingFee))].join(','));
                    lines.push([csvEscape('Total Guests'), csvEscape(String(summary.totalGuests || 0)), csvEscape('Payout Amount'), csvEscape(money(summary.payoutAmount))].join(','));
                    lines.push([csvEscape('Total Earning'), csvEscape(money(summary.totalEarning)), csvEscape(''), csvEscape('')].join(','));
                    lines.push('');

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

                    const summary = dataset.summary || {};
                    const money = function(val) {
                        return '$' + Number(val || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    };

                    let tableHtml = '<table border="1" style="border-collapse:collapse;margin-bottom:20px;font-family:Arial,sans-serif;font-size:12px;">';
                    tableHtml += '<thead><tr><th colspan="4" style="background:#2980b9;color:#ffffff;font-size:14px;padding:8px;text-align:left;">Transactions Export Summary</th></tr></thead><tbody>';
                    tableHtml += '<tr><td style="padding:6px;background:#f2f2f2;"><b>Total Transactions</b></td><td style="padding:6px;">' + (summary.totalTransactions || 0) + '</td><td style="padding:6px;background:#f2f2f2;"><b>Completed Transactions</b></td><td style="padding:6px;">' + (summary.completedTransactions || 0) + '</td></tr>';
                    tableHtml += '<tr><td style="padding:6px;background:#f2f2f2;"><b>Total Revenue</b></td><td style="padding:6px;">' + money(summary.totalRevenue) + '</td><td style="padding:6px;background:#f2f2f2;"><b>Pending Fee</b></td><td style="padding:6px;">' + money(summary.pendingFee) + '</td></tr>';
                    tableHtml += '<tr><td style="padding:6px;background:#f2f2f2;"><b>Total Guests</b></td><td style="padding:6px;">' + (summary.totalGuests || 0) + '</td><td style="padding:6px;background:#f2f2f2;"><b>Payout Amount</b></td><td style="padding:6px;">' + money(summary.payoutAmount) + '</td></tr>';
                    tableHtml += '<tr><td style="padding:6px;background:#f2f2f2;"><b>Total Earning</b></td><td style="padding:6px;">' + money(summary.totalEarning) + '</td><td></td><td></td></tr>';
                    tableHtml += '</tbody></table><br/>';

                    tableHtml += '<table border="1" style="border-collapse:collapse;font-family:Arial,sans-serif;font-size:11px;"><thead><tr>';
                    dataset.headers.forEach(function (h) {
                        tableHtml += '<th style="background:#34495e;color:#ffffff;padding:6px;">' + h + '</th>';
                    });
                    tableHtml += '</tr></thead><tbody>';

                    dataset.rows.forEach(function (row) {
                        tableHtml += '<tr>';
                        row.forEach(function (cell) {
                            tableHtml += '<td style="padding:4px;">' + cell + '</td>';
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
                    const doc = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4' });
                    const summary = dataset.summary || {};
                    const money = function(value) {
                        const n = Number(value || 0);
                        return '$' + n.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    };

                    doc.setFontSize(13);
                    doc.text('Transactions Export', 10, 10);
                    doc.setFontSize(8);
                    doc.text('Scope: ' + (dataset.selectedOnly ? ('Selected Rows (' + (summary.totalTransactions || 0) + ')') : 'All Filtered Rows'), 10, 15);
                    doc.text('Generated: ' + new Date().toLocaleString(), 10, 19);

                    doc.autoTable({
                        startY: 22,
                        margin: { left: 10, right: 10 },
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
                        styles: { fontSize: 7, cellPadding: 1.5 },
                        headStyles: { fillColor: [41, 128, 185] },
                    });

                    doc.autoTable({
                        head: [dataset.headers],
                        body: dataset.rows,
                        startY: (doc.lastAutoTable && doc.lastAutoTable.finalY ? doc.lastAutoTable.finalY + 4 : 40),
                        margin: { left: 5, right: 5, top: 10, bottom: 10 },
                        styles: { fontSize: 5, cellPadding: 1, overflow: 'linebreak', valign: 'middle' },
                        headStyles: { fillColor: [41, 128, 185], fontSize: 5.5, fontStyle: 'bold' },
                        bodyStyles: { textColor: [25, 25, 25] },
                        theme: 'grid',
                        horizontalPageBreak: true,
                        horizontalPageBreakRepeat: 0,
                    });
                    doc.save('transactions.pdf');
                }

                function printTable() {
                    const dataset = getExportDataset();
                    if (!dataset.rows.length) {
                        alert('No rows available to print.');
                        return;
                    }

                    const summary = dataset.summary || {};
                    const money = function(value) {
                        const n = Number(value || 0);
                        return '$' + n.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    };

                    let html = '<h2>Transactions Export</h2>';
                    html += '<table border="1" cellspacing="0" cellpadding="6" style="border-collapse:collapse;width:100%;font-family:Arial,sans-serif;font-size:12px;margin-bottom:16px;">';
                    html += '<thead><tr><th colspan="4" style="background:#2980b9;color:#fff;text-align:left;">Summary Report</th></tr></thead><tbody>';
                    html += '<tr><td><b>Total Transactions</b></td><td>' + (summary.totalTransactions || 0) + '</td><td><b>Completed Transactions</b></td><td>' + (summary.completedTransactions || 0) + '</td></tr>';
                    html += '<tr><td><b>Total Revenue</b></td><td>' + money(summary.totalRevenue) + '</td><td><b>Pending Fee</b></td><td>' + money(summary.pendingFee) + '</td></tr>';
                    html += '<tr><td><b>Total Guests</b></td><td>' + (summary.totalGuests || 0) + '</td><td><b>Payout Amount</b></td><td>' + money(summary.payoutAmount) + '</td></tr>';
                    html += '<tr><td><b>Total Earning</b></td><td>' + money(summary.totalEarning) + '</td><td></td><td></td></tr>';
                    html += '</tbody></table>';

                    html += '<table border="1" cellspacing="0" cellpadding="4" style="border-collapse:collapse;width:100%;font-family:Arial,sans-serif;font-size:10px;">';
                    html += '<thead><tr style="background:#34495e;color:#fff;">';
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

                    const isoMatch = dateStr.match(/^(\d{4})-(\d{2})-(\d{2})/);
                    if (isoMatch) {
                        const mIso = moment(dateStr.substring(0, 10), 'YYYY-MM-DD', true);
                        if (mIso.isValid()) return mIso;
                    }

                    const parsed = moment(dateStr, [
                        'MMM DD, YYYY hh:mm A z',
                        'MMM D, YYYY hh:mm A z',
                        'MMM DD, YYYY h:mm A z',
                        'MMM D, YYYY h:mm A z',
                        'MMM DD, YYYY',
                        'MMM D, YYYY',
                        'YYYY-MM-DD h:mm A z',
                        'YYYY-MM-DD hh:mm A z',
                        'YYYY-MM-DD h:mm A',
                        'YYYY-MM-DD hh:mm A',
                        'YYYY-MM-DD HH:mm:ss',
                        'YYYY-MM-DD',
                        'MM/DD/YYYY',
                        'M/D/YYYY'
                    ], false);

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
                        if (typeof updateChartsFromFilteredRows === 'function') {
                            updateChartsFromFilteredRows();
                        }
                    });
                    updateTotal();
                    updateSelectionUi();
                    updateDashboardCardsFromFilteredRows();
                    if (typeof updateChartsFromFilteredRows === 'function') {
                        updateChartsFromFilteredRows();
                    }
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

            window.buildAdminNotesCardHtml = function(txnId, noteText, noteBy, noteAt) {
                var safeEsc = window.txnEsc || function(v) { return String(v == null ? '' : v).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;'); };
                var authorInfo = '';
                if (noteBy || noteAt) {
                    authorInfo = 'Updated' + (noteBy ? ' by <strong style="color:#a78bfa;">' + safeEsc(noteBy) + '</strong>' : '') + (noteAt ? ' on ' + safeEsc(noteAt) : '');
                }

                var cardHtml = '';
                cardHtml += '<div class="txn-detail-card admin-notes-card mb-0" style="background:rgba(30,41,59,0.7);border:1px solid rgba(124,58,237,0.35);border-radius:12px;padding:16px;">';
                cardHtml += '<div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-1">';
                cardHtml += '<div class="txn-detail-title mb-0" style="color:#a78bfa;font-size:0.9rem;font-weight:700;"><i class="fas fa-sticky-note me-2"></i>Notes</div>';
                cardHtml += '<div class="admin-note-author-info text-white-50" style="font-size:0.75rem;">' + (authorInfo ? authorInfo : 'No note saved yet') + '</div>';
                cardHtml += '</div>';
                cardHtml += '<form class="admin-note-form" data-txn-id="' + safeEsc(txnId) + '">';
                cardHtml += '<div class="mb-3">';
                cardHtml += '<textarea class="form-control admin-note-textarea" rows="4" placeholder="Enter notes for this transaction…" style="background:rgba(15,23,42,0.9);border:1px solid rgba(255,255,255,0.15);color:#fff;font-size:0.85rem;border-radius:8px;">' + safeEsc(noteText) + '</textarea>';
                cardHtml += '</div>';
                cardHtml += '<div class="d-flex align-items-center justify-content-between gap-2">';
                cardHtml += '<span class="admin-note-msg text-success small fw-semibold" style="display:none;"><i class="fas fa-check-circle me-1"></i>Saved!</span>';
                cardHtml += '<div class="d-flex align-items-center gap-2 ms-auto">';
                cardHtml += '<button type="button" class="btn btn-sm btn-outline-danger clear-admin-note-btn" style="font-weight:600;padding:5px 14px;border-radius:6px;"><i class="fas fa-trash-alt me-1"></i>Clear Note</button>';
                cardHtml += '<button type="submit" class="btn btn-sm btn-primary save-admin-note-btn" style="background:#7c3aed;border-color:#7c3aed;font-weight:600;padding:5px 16px;border-radius:6px;"><i class="fas fa-save me-1"></i>Save Note</button>';
                cardHtml += '</div>';
                cardHtml += '</div>';
                cardHtml += '</form>';
                cardHtml += '</div>';
                return cardHtml;
            };

            $(document).on('click', '.open-notes-btn', function(e) {
                var $btn = $(this);
                var txnId = $btn.data('id');
                var orderNum = $btn.data('transaction-id') || ('#' + String(txnId));
                var noteText = $btn.data('admin_notes') || '';
                var noteBy = $btn.data('admin_notes_by') || '';
                var noteAt = $btn.data('admin_notes_at') || '';

                $('#txnNotesModalOrderTitle').text(orderNum);
                var cardHtml = window.buildAdminNotesCardHtml(txnId, noteText, noteBy, noteAt);
                $('#txnNotesModalBody').html(cardHtml);
            });

            $(document).on('click', '.clear-admin-note-btn', function(e) {
                e.preventDefault();
                var $form = $(this).closest('.admin-note-form');
                $form.find('.admin-note-textarea').val('');
                $form.submit();
            });

            $(document).on('submit', '.admin-note-form', function(e) {
                e.preventDefault();
                var $form = $(this);
                var txnId = $form.data('txn-id');
                var noteText = $form.find('.admin-note-textarea').val();
                var $btn = $form.find('.save-admin-note-btn');
                var $msg = $form.find('.admin-note-msg');
                var originalBtnText = $btn.html();

                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Saving…');

                $.ajax({
                    url: '{{ url("/admins/transaction") }}/' + txnId + '/update-admin-note',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        admin_notes: noteText
                    },
                    success: function(res) {
                        $btn.prop('disabled', false).html(originalBtnText);
                        if (res.success) {
                            $msg.fadeIn(150).delay(2000).fadeOut(200);

                            var $targetBtns = $('.view-btn[data-id="' + txnId + '"], .view-btn[data-transaction-id="' + txnId + '"], .open-notes-btn[data-id="' + txnId + '"], .open-notes-btn[data-transaction-id="' + txnId + '"]');
                            $targetBtns.data('admin_notes', res.admin_notes || '');
                            $targetBtns.data('admin_notes_by', res.admin_notes_by || '');
                            $targetBtns.data('admin_notes_at', res.admin_notes_at || '');

                            var safeEsc = window.txnEsc || function(v) { return String(v || ''); };
                            var authorHtml = '';
                            if (res.admin_notes_by || res.admin_notes_at) {
                                authorHtml = 'Updated' + (res.admin_notes_by ? ' by <strong style="color:#a78bfa;">' + safeEsc(res.admin_notes_by) + '</strong>' : '') + (res.admin_notes_at ? ' on ' + safeEsc(res.admin_notes_at) : '');
                            } else {
                                authorHtml = 'No note saved yet';
                            }

                            $('.admin-note-form[data-txn-id="' + txnId + '"]').each(function() {
                                $(this).find('.admin-note-textarea').val(res.admin_notes || '');
                                $(this).closest('.admin-notes-card').find('.admin-note-author-info').html(authorHtml);
                            });
                        }
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).html(originalBtnText);
                        alert('Failed to save note. Please try again.');
                    }
                });
            });

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
                var hasPickupDetails = [
                    $(this).data('transportation_pickup_time'),
                    $(this).data('transportation_address'),
                    $(this).data('transportation_phone')
                ].some(function(v){ return String(v || '').trim() !== ''; });
                var transportMode = 'Not Required';
                if (requiresTransportation) {
                    transportMode = hasPickupDetails ? 'Pickup Requested' : 'Self Drive Selected';
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

                var currentNoteText = String($(this).data('admin_notes') || '').trim();
                if (currentNoteText !== '') {
                    beginPdfSection('Notes');
                    pushPdfRow('Note', currentNoteText);
                    var currentNoteBy = String($(this).data('admin_notes_by') || '').trim();
                    var currentNoteAt = String($(this).data('admin_notes_at') || '').trim();
                    if (currentNoteBy || currentNoteAt) {
                        pushPdfRow('Updated', (currentNoteBy ? currentNoteBy : '') + (currentNoteAt ? (' on ' + currentNoteAt) : ''));
                    }
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
                var hasPickupTime = transportationPickup !== '' && transportationPickupDisplay !== 'N/A' && transportationPickupDisplay !== '';
                var transportSectionTitle = hasPickupTime ? 'Transportation Details' : 'Arrival Details';
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
                var requiresTransportationRaw = String(
                    $(this).data('requires_transportation') ||
                    ($(this).closest('tr').find('.view-btn').data('requires_transportation') || '')
                ).toLowerCase();
                var requiresTransportation = requiresTransportationRaw === '1' || requiresTransportationRaw === 'true' || requiresTransportationRaw === 'yes';
                var hasPickupDetails = [transportationPickup, transportationAddress, transportationPhone].some(function(v) {
                    return String(v || '').trim() !== '';
                });
                var transportMode = 'Not Required';
                if (requiresTransportation) {
                    transportMode = hasPickupDetails ? 'Pickup Requested' : 'Self Drive Selected';
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
                    [hasPickupTime ? 'Transport Mode' : 'Transport Mode', transportMode],
                    [hasPickupTime ? 'Transportation Date' : 'Date Of Use', transportationDate || 'N/A'],
                    ['Pickup Time', transportationPickupDisplay],
                    ['Arrival Time', transportationArrivalDisplay],
                    ['Pickup Address', transportationAddress || 'N/A'],
                    [hasPickupTime ? 'Transport Phone' : 'Contact Phone', transportationPhone || 'N/A'],
                    [hasPickupTime ? 'Transport Note' : 'Arrival Note', transportationNote || 'N/A']
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
                html += '<div class="txn-detail-title">' + transportSectionTitle + '</div>';
                html += row(hasPickupTime ? 'Transport Mode' : 'Transport Mode', transportMode);
                html += row(hasPickupTime ? 'Transportation Date' : 'Date Of Use', transportationDate || 'N/A');
                html += row('Pickup Time', transportationPickupDisplay);
                html += row('Arrival Time', transportationArrivalDisplay);
                html += row('Pickup Address', transportationAddress || 'N/A');
                html += row(hasPickupTime ? 'Transport Phone' : 'Contact Phone', transportationPhone || 'N/A');
                html += row(hasPickupTime ? 'Transport Note' : 'Arrival Note', transportationNote || 'N/A');
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

                var rowViewBtn = $(this).closest('tr').find('.view-btn').first();
                var realTxnId = $(this).data('transaction-id') || $(this).data('id') || rowViewBtn.data('id');
                var adminNotesCard = window.buildAdminNotesCardHtml(
                    realTxnId,
                    $(this).data('admin_notes') || rowViewBtn.data('admin_notes'),
                    $(this).data('admin_notes_by') || rowViewBtn.data('admin_notes_by'),
                    $(this).data('admin_notes_at') || rowViewBtn.data('admin_notes_at')
                );
                html = adminNotesCard + html;

                $('#packageDetailsContent').html(html);
                $('#packageDetailsModal').data('pdfPayload', {
                    title: 'Package Details - Order #' + String(orderId),
                    status: statusText,
                    meta: 'Confirmation: ' + String(confirmationNumber) + ' | ' + String(orderDate),
                    sections: [
                        { name: 'Guest Details', rows: guestRows },
                        { name: 'Booking Details', rows: bookingRows },
                        { name: transportSectionTitle, rows: transportationRows }
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
            });

            // Universal modal backdrop & scroll cleanup handler for Quick View & all modals
            $(document).on('hidden.bs.modal', '.modal', function() {
                if ($('.modal.show').length === 0) {
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open').css({
                        'overflow': '',
                        'overflow-y': 'auto',
                        'padding-right': ''
                    });
                }
            });

            // Mobile floating scroll arrows handler for polarisFilterContainer
            (function() {
                var filterContainer = document.getElementById('polarisFilterContainer');
                var scrollLeftBtn = document.getElementById('polarisScrollLeftBtn');
                var scrollRightBtn = document.getElementById('polarisScrollRightBtn');

                function updatePolarisScrollArrows() {
                    if (!filterContainer) return;
                    if (window.innerWidth >= 768) {
                        if (scrollLeftBtn) scrollLeftBtn.classList.add('d-none');
                        if (scrollRightBtn) scrollRightBtn.classList.add('d-none');
                        return;
                    }

                    var scrollLeft = filterContainer.scrollLeft;
                    var maxScrollLeft = filterContainer.scrollWidth - filterContainer.clientWidth;

                    if (scrollLeft > 10) {
                        if (scrollLeftBtn) scrollLeftBtn.classList.remove('d-none');
                    } else {
                        if (scrollLeftBtn) scrollLeftBtn.classList.add('d-none');
                    }

                    if (maxScrollLeft - scrollLeft > 10) {
                        if (scrollRightBtn) scrollRightBtn.classList.remove('d-none');
                    } else {
                        if (scrollRightBtn) scrollRightBtn.classList.add('d-none');
                    }
                }

                if (filterContainer && scrollLeftBtn && scrollRightBtn) {
                    filterContainer.addEventListener('scroll', updatePolarisScrollArrows);
                    window.addEventListener('resize', updatePolarisScrollArrows);

                    scrollLeftBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        filterContainer.scrollBy({ left: -160, behavior: 'smooth' });
                    });

                    scrollRightBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        filterContainer.scrollBy({ left: 160, behavior: 'smooth' });
                    });

                    setTimeout(updatePolarisScrollArrows, 350);
                }
            window.filterVenueDropdownList = function(query) {
                var q = (query || '').toLowerCase().trim();
                var labels = document.querySelectorAll('#venuePopoverBody .venue-item-label');
                labels.forEach(function(label) {
                    var text = (label.textContent || '').toLowerCase();
                    if (!q || text.indexOf(q) !== -1) {
                        label.style.display = 'flex';
                    } else {
                        label.style.display = 'none';
                    }
                });
            };
            })();
            </script>
@endpush
