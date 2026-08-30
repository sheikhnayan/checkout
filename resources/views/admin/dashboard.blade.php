@extends('admin.main')

@section('content')
@php $authUser = auth()->user(); @endphp

<style>
    :root {
        --dash-gold: #ffcc00;
        --dash-indigo: #6366f1;
        --dash-emerald: #10b981;
        --dash-cyan: #06b6d4;
        --dash-rose: #f43f5e;
        --dash-card-bg: linear-gradient(145deg, rgba(30, 41, 59, 0.7) 0%, rgba(15, 23, 42, 0.8) 100%);
        --dash-card-border: 1px solid rgba(255, 255, 255, 0.08);
    }

    .dash-hero-card {
        background: linear-gradient(135deg, rgba(255, 204, 0, 0.08) 0%, rgba(99, 102, 241, 0.08) 50%, rgba(15, 23, 42, 0.95) 100%);
        border: 1px solid rgba(255, 204, 0, 0.25);
        border-radius: 18px;
        padding: 1.75rem 2rem;
        box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.5);
        margin-bottom: 1.75rem;
        position: relative;
        overflow: hidden;
    }

    .dash-hero-card::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255, 204, 0, 0.12) 0%, rgba(0, 0, 0, 0) 70%);
        pointer-events: none;
    }

    .dash-kpi-card {
        background: var(--dash-card-bg);
        border: var(--dash-card-border);
        border-radius: 16px;
        padding: 1.35rem 1.5rem;
        transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
        height: 100%;
        backdrop-filter: blur(12px);
    }

    .dash-kpi-card:hover {
        transform: translateY(-3px);
        border-color: rgba(255, 204, 0, 0.4);
        box-shadow: 0 12px 25px -8px rgba(255, 204, 0, 0.15);
    }

    .dash-icon-box {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: justify-content;
        justify-content: center;
        font-size: 1.35rem;
        flex-shrink: 0;
    }

    .dash-icon-gold { background: rgba(255, 204, 0, 0.15); color: #ffcc00; }
    .dash-icon-emerald { background: rgba(16, 185, 129, 0.15); color: #10b981; }
    .dash-icon-indigo { background: rgba(99, 102, 241, 0.15); color: #818cf8; }
    .dash-icon-cyan { background: rgba(6, 182, 212, 0.15); color: #22d3ee; }
    .dash-icon-rose { background: rgba(244, 63, 94, 0.15); color: #fb7185; }

    .dash-panel {
        background: var(--dash-card-bg);
        border: var(--dash-card-border);
        border-radius: 18px;
        overflow: hidden;
        backdrop-filter: blur(12px);
        box-shadow: 0 8px 24px -6px rgba(0, 0, 0, 0.4);
    }

    .dash-panel-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .dash-table th {
        background: rgba(15, 23, 42, 0.6);
        color: #94a3b8;
        font-size: 0.76rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 600;
        padding: 0.9rem 1.25rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }

    .dash-table td {
        padding: 0.95rem 1.25rem;
        vertical-align: middle;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        color: #f1f5f9;
        font-size: 0.88rem;
    }

    .dash-table tr:hover td {
        background: rgba(255, 255, 255, 0.025);
    }

    .dash-badge {
        padding: 0.3rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.02em;
    }

    .dash-badge-completed { background: rgba(16, 185, 129, 0.18); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); }
    .dash-badge-pending { background: rgba(245, 158, 11, 0.18); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3); }
    .dash-badge-failed { background: rgba(239, 68, 68, 0.18); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); }

    .dash-quick-btn {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 12px;
        padding: 0.85rem 1rem;
        color: #e2e8f0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        transition: all 0.2s ease;
        text-decoration: none !important;
    }

    .dash-quick-btn:hover {
        background: rgba(255, 204, 0, 0.08);
        border-color: rgba(255, 204, 0, 0.35);
        color: #ffcc00;
        transform: translateX(4px);
    }

    .dash-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: rgba(255, 204, 0, 0.15);
        color: #ffcc00;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.85rem;
    }

    .growth-tag {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.18rem 0.5rem;
        border-radius: 12px;
    }

    .growth-up { background: rgba(16, 185, 129, 0.15); color: #34d399; }
    .growth-down { background: rgba(244, 63, 94, 0.15); color: #fb7185; }
</style>

<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- Hero Welcome Section --}}
        <div class="dash-hero-card d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="dash-avatar" style="width: 54px; height: 54px; font-size: 1.25rem; border: 2px solid rgba(255,204,0,0.4);">
                    {{ strtoupper(substr($authUser->name, 0, 1)) }}
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                        <h4 class="mb-0 fw-bold text-white">Welcome back, {{ $authUser->name }}!</h4>
                        @if($authUser->isAdmin())
                            <span class="dash-badge" style="background: rgba(255, 204, 0, 0.18); color: #ffcc00; border: 1px solid rgba(255, 204, 0, 0.35);">
                                <i class="fas fa-crown me-1"></i> Super Admin
                            </span>
                        @elseif($authUser->isManager())
                            <span class="dash-badge" style="background: rgba(99, 102, 241, 0.18); color: #a5b4fc; border: 1px solid rgba(99, 102, 241, 0.35);">
                                <i class="fas fa-user-tie me-1"></i> Manager
                            </span>
                        @else
                            <span class="dash-badge" style="background: rgba(16, 185, 129, 0.18); color: #6ee7b7; border: 1px solid rgba(16, 185, 129, 0.35);">
                                <i class="fas fa-id-badge me-1"></i> Club Admin
                            </span>
                        @endif
                    </div>
                    <p class="text-muted mb-0 fs-7">
                        <i class="far fa-clock me-1 text-warning"></i> {{ now()->timezone('America/Los_Angeles')->format('l, F j, Y') }} (Pacific Time)
                    </p>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                @if($authUser->isAdmin())
                    <a href="{{ route('admin.website.create') }}" class="btn btn-sm btn-warning text-dark fw-bold px-3">
                        <i class="fas fa-plus me-1"></i> New Club
                    </a>
                @endif
                @if($authUser->isAdmin() || $authUser->hasRoutePermission('admin.custom-invoice.create'))
                    <a href="{{ route('admin.custom-invoice.create') }}" class="btn btn-sm btn-outline-warning px-3">
                        <i class="fas fa-file-invoice me-1"></i> New Invoice
                    </a>
                @endif
                <a href="{{ route('admin.transaction.index') }}" class="btn btn-sm btn-outline-light px-3">
                    <i class="fas fa-list me-1"></i> All Orders
                </a>
            </div>
        </div>

        @if($authUser->isAdmin())
        {{-- SUPER ADMIN KPI CARDS GRID --}}
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="dash-kpi-card">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-muted fs-7 fw-semibold text-uppercase tracking-wider">Total Revenue</span>
                        <div class="dash-icon-box dash-icon-gold"><i class="fas fa-dollar-sign"></i></div>
                    </div>
                    <div class="d-flex align-items-baseline justify-content-between">
                        <h3 class="mb-0 fw-bold text-white">${{ number_format($totalRevenue ?? 0, 2) }}</h3>
                        <span class="growth-tag {{ ($revenueGrowth ?? 0) >= 0 ? 'growth-up' : 'growth-down' }}">
                            <i class="fas fa-arrow-{{ ($revenueGrowth ?? 0) >= 0 ? 'up' : 'down' }} me-1"></i>{{ abs($revenueGrowth ?? 0) }}%
                        </span>
                    </div>
                    <p class="text-muted fs-8 mb-0 mt-2">
                        Monthly: <strong class="text-white">${{ number_format($monthlyRevenue ?? 0, 2) }}</strong>
                    </p>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="dash-kpi-card">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-muted fs-7 fw-semibold text-uppercase tracking-wider">Total Clubs</span>
                        <div class="dash-icon-box dash-icon-indigo"><i class="fas fa-globe"></i></div>
                    </div>
                    <div class="d-flex align-items-baseline justify-content-between">
                        <h3 class="mb-0 fw-bold text-white">{{ $totalClubs ?? 0 }}</h3>
                        <span class="badge bg-indigo-subtle text-indigo fs-8 fw-semibold">Active Venues</span>
                    </div>
                    <p class="text-muted fs-8 mb-0 mt-2">
                        Total Events: <strong class="text-white">{{ $totalEvents ?? 0 }}</strong>
                    </p>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="dash-kpi-card">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-muted fs-7 fw-semibold text-uppercase tracking-wider">Transactions</span>
                        <div class="dash-icon-box dash-icon-emerald"><i class="fas fa-credit-card"></i></div>
                    </div>
                    <div class="d-flex align-items-baseline justify-content-between">
                        <h3 class="mb-0 fw-bold text-white">{{ number_format($totalTransactions ?? 0) }}</h3>
                        <span class="growth-tag growth-up"><i class="fas fa-check me-1"></i>Live</span>
                    </div>
                    <p class="text-muted fs-8 mb-0 mt-2">
                        Avg Ticket: <strong class="text-white">${{ $totalTransactions > 0 ? number_format(($totalRevenue ?? 0) / $totalTransactions, 2) : '0.00' }}</strong>
                    </p>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="dash-kpi-card">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-muted fs-7 fw-semibold text-uppercase tracking-wider">Network & Operations</span>
                        <div class="dash-icon-box dash-icon-cyan"><i class="fas fa-users-cog"></i></div>
                    </div>
                    <div class="d-flex align-items-baseline justify-content-between">
                        <h3 class="mb-0 fw-bold text-white">{{ ($activePromotersCount ?? 0) + ($activeEntertainersCount ?? 0) }}</h3>
                        <span class="badge bg-cyan-subtle text-cyan fs-8 fw-semibold">Active Staff</span>
                    </div>
                    <p class="text-muted fs-8 mb-0 mt-2">
                        Pending Invoices: <strong class="text-warning">{{ $pendingInvoicesCount ?? 0 }}</strong>
                    </p>
                </div>
            </div>
        </div>

        @else

        {{-- MANAGERS / SCOPED USER KPI CARDS --}}
        <div class="row g-3 mb-4">
            <div class="col-xl-4 col-md-6">
                <div class="dash-kpi-card">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-muted fs-7 fw-semibold text-uppercase tracking-wider">Allocated Clubs</span>
                        <div class="dash-icon-box dash-icon-gold"><i class="fas fa-building"></i></div>
                    </div>
                    <h3 class="mb-0 fw-bold text-white">{{ $allocatedWebsites->count() }}</h3>
                    <p class="text-muted fs-8 mb-0 mt-2">Scoped management venues</p>
                </div>
            </div>
            <div class="col-xl-4 col-md-6">
                <div class="dash-kpi-card">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-muted fs-7 fw-semibold text-uppercase tracking-wider">Club Events</span>
                        <div class="dash-icon-box dash-icon-indigo"><i class="fas fa-calendar-alt"></i></div>
                    </div>
                    <h3 class="mb-0 fw-bold text-white">{{ $scopedEventCount ?? 0 }}</h3>
                    <p class="text-muted fs-8 mb-0 mt-2">Scheduled club events</p>
                </div>
            </div>
            <div class="col-xl-4 col-md-6">
                <div class="dash-kpi-card">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-muted fs-7 fw-semibold text-uppercase tracking-wider">Total Sales ($)</span>
                        <div class="dash-icon-box dash-icon-emerald"><i class="fas fa-receipt"></i></div>
                    </div>
                    <h3 class="mb-0 fw-bold text-white">${{ number_format($scopedRevenue ?? 0, 2) }}</h3>
                    <p class="text-muted fs-8 mb-0 mt-2">Transactions: <strong class="text-white">{{ $scopedTransactionCount ?? 0 }}</strong></p>
                </div>
            </div>
        </div>
        @endif

        {{-- VISUAL ANALYTICS SECTION (APEXCHARTS) --}}
        <div class="row g-3 mb-4">
            <div class="col-lg-8">
                <div class="dash-panel h-100">
                    <div class="dash-panel-header">
                        <div>
                            <h5 class="mb-0 text-white fw-bold"><i class="fas fa-chart-area text-warning me-2"></i>14-Day Sales & Booking Trajectory</h5>
                            <small class="text-muted">Daily revenue ($) & transaction count</small>
                        </div>
                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Live Feed</span>
                    </div>
                    <div class="p-3">
                        <div id="revenueTrendChart" style="min-height: 280px;"></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="dash-panel h-100">
                    <div class="dash-panel-header">
                        <div>
                            <h5 class="mb-0 text-white fw-bold"><i class="fas fa-chart-pie text-info me-2"></i>Top Club Sales Share</h5>
                            <small class="text-muted">Revenue breakdown by venue</small>
                        </div>
                    </div>
                    <div class="p-3 d-flex flex-column align-items-center justify-content-center" style="min-height: 280px;">
                        <div id="clubDonutChart" style="width: 100%;"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- RECENT TRANSACTIONS & QUICK LAUNCHER PANEL --}}
        <div class="row g-3">
            <div class="col-lg-8">
                <div class="dash-panel">
                    <div class="dash-panel-header">
                        <div>
                            <h5 class="mb-0 text-white fw-bold"><i class="fas fa-receipt text-warning me-2"></i>Recent Transactions</h5>
                            <small class="text-muted">Latest bookings across active venues</small>
                        </div>
                        <a href="{{ route('admin.transaction.index') }}" class="btn btn-sm btn-outline-warning">
                            View All <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table dash-table mb-0">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Event / Package</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTransactions as $tx)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="dash-avatar">
                                                {{ strtoupper(substr($tx->full_name ?: 'C', 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-semibold text-white">{{ $tx->full_name ?: 'Guest' }}</div>
                                                <small class="text-muted fs-8">{{ $tx->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($tx->event)
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-8">
                                                <i class="fas fa-calendar-alt me-1"></i>{{ \Illuminate\Support\Str::limit($tx->event->name, 22) }}
                                            </span>
                                        @elseif($tx->package)
                                            <span class="badge bg-info-subtle text-info border border-info-subtle fs-8">
                                                <i class="fas fa-box me-1"></i>{{ \Illuminate\Support\Str::limit($tx->package_table_label, 22) }}
                                            </span>
                                        @else
                                            <span class="text-muted fs-8">N/A</span>
                                        @endif
                                    </td>
                                    <td class="fw-bold text-white">${{ number_format($tx->total, 2) }}</td>
                                    <td>
                                        @if($tx->status === 'completed')
                                            <span class="dash-badge dash-badge-completed"><i class="fas fa-check-circle me-1"></i>Completed</span>
                                        @elseif($tx->status === 'pending')
                                            <span class="dash-badge dash-badge-pending"><i class="fas fa-hourglass-half me-1"></i>Pending</span>
                                        @else
                                            <span class="dash-badge dash-badge-failed"><i class="fas fa-times-circle me-1"></i>{{ ucfirst($tx->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="text-muted fs-8">
                                        {{ $tx->created_at->timezone('America/Los_Angeles')->format('M d, g:i A') }} PT
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No recent transactions found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="dash-panel h-100">
                    <div class="dash-panel-header">
                        <h5 class="mb-0 text-white fw-bold"><i class="fas fa-bolt text-warning me-2"></i>Quick Launcher</h5>
                    </div>
                    <div class="p-3 d-flex flex-column gap-2">
                        @if($authUser->isAdmin())
                        <a href="{{ route('admin.website.create') }}" class="dash-quick-btn">
                            <i class="fas fa-plus-circle text-warning fs-5"></i>
                            <div>
                                <div class="fw-semibold">Create New Club</div>
                                <small class="text-muted fs-8">Add new venue website profile</small>
                            </div>
                        </a>
                        @endif

                        @if($authUser->hasRoutePermission('admin.event.index'))
                        <a href="{{ route('admin.event.index') }}" class="dash-quick-btn">
                            <i class="fas fa-calendar-plus text-emerald fs-5"></i>
                            <div>
                                <div class="fw-semibold">Manage Events</div>
                                <small class="text-muted fs-8">Create & schedule club events</small>
                            </div>
                        </a>
                        @endif

                        @if($authUser->isAdmin() || $authUser->hasRoutePermission('admin.custom-invoice.index'))
                        <a href="{{ route('admin.custom-invoice.index') }}" class="dash-quick-btn">
                            <i class="fas fa-file-invoice-dollar text-cyan fs-5"></i>
                            <div>
                                <div class="fw-semibold">Custom Invoices</div>
                                <small class="text-muted fs-8">Generate & dispatch client invoices</small>
                            </div>
                        </a>
                        @endif

                        @if($authUser->isAdmin() || $authUser->hasRoutePermission('admin.transaction.scan'))
                        <a href="{{ route('admin.transaction.scan') }}" class="dash-quick-btn">
                            <i class="fas fa-qrcode text-indigo fs-5"></i>
                            <div>
                                <div class="fw-semibold">Ticket Scanner</div>
                                <small class="text-muted fs-8">Scan & confirm guest entry</small>
                            </div>
                        </a>
                        @endif

                        <a href="{{ route('admin.profile.edit') }}" class="dash-quick-btn">
                            <i class="fas fa-user-cog text-rose fs-5"></i>
                            <div>
                                <div class="fw-semibold">My Account</div>
                                <small class="text-muted fs-8">Profile settings & credentials</small>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    // 14-Day Revenue & Booking Chart
    var chartDates = @json($chartDates ?? []);
    var chartRevenues = @json($chartRevenues ?? []);
    var chartBookings = @json($chartBookings ?? []);

    var revenueOptions = {
        series: [
            {
                name: 'Revenue ($)',
                type: 'area',
                data: chartRevenues
            },
            {
                name: 'Bookings Count',
                type: 'line',
                data: chartBookings
            }
        ],
        chart: {
            height: 290,
            type: 'line',
            toolbar: { show: false },
            background: 'transparent'
        },
        theme: { mode: 'dark' },
        colors: ['#ffcc00', '#6366f1'],
        stroke: {
            curve: 'smooth',
            width: [3, 2]
        },
        fill: {
            type: ['gradient', 'solid'],
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.35,
                opacityTo: 0.05,
                stops: [0, 90, 100]
            }
        },
        xaxis: {
            categories: chartDates,
            labels: { style: { colors: '#94a3b8', fontSize: '11px' } },
            axisBorder: { show: false }
        },
        yaxis: [
            {
                title: { text: 'Revenue ($)', style: { color: '#ffcc00' } },
                labels: {
                    style: { colors: '#94a3b8' },
                    formatter: function (val) { return '$' + val; }
                }
            },
            {
                opposite: true,
                title: { text: 'Bookings', style: { color: '#6366f1' } },
                labels: { style: { colors: '#94a3b8' } }
            }
        ],
        tooltip: {
            theme: 'dark',
            shared: true,
            intersect: false
        },
        legend: {
            position: 'top',
            horizontalAlign: 'right',
            labels: { colors: '#cbd5e1' }
        },
        grid: {
            borderColor: 'rgba(255, 255, 255, 0.08)'
        }
    };

    var revenueChart = new ApexCharts(document.querySelector("#revenueTrendChart"), revenueOptions);
    revenueChart.render();

    // Club Donut Share Chart
    @if($authUser->isAdmin() && isset($topClubs) && $topClubs->count() > 0)
        var clubLabels = @json($topClubs->pluck('name'));
        var clubRevenues = @json($topClubs->pluck('calculated_revenue'));

        var donutOptions = {
            series: clubRevenues,
            labels: clubLabels,
            chart: {
                type: 'donut',
                height: 280,
                background: 'transparent'
            },
            theme: { mode: 'dark' },
            colors: ['#ffcc00', '#10b981', '#6366f1', '#06b6d4', '#f43f5e'],
            legend: {
                position: 'bottom',
                labels: { colors: '#cbd5e1' }
            },
            stroke: { show: false },
            tooltip: {
                theme: 'dark',
                y: {
                    formatter: function (val) { return '$' + parseFloat(val).toFixed(2); }
                }
            },
            dataLabels: { enabled: false }
        };

        var clubChart = new ApexCharts(document.querySelector("#clubDonutChart"), donutOptions);
        clubChart.render();
    @else
        document.querySelector("#clubDonutChart").innerHTML = '<div class="text-center text-muted py-5"><i class="fas fa-info-circle me-1"></i>No venue breakdown available</div>';
    @endif
});
</script>
@endpush

@endsection
