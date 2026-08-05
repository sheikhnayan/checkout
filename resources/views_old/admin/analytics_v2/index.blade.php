@extends('admin.main')

@section('content')
<div class="container-fluid px-4 py-4" style="background-color: var(--admin-bg, #0b0e1a); min-height: 100vh; color: var(--admin-text, #e8eaf6);">

    <!-- TOP CONTROL & NAVIGATION HEADER -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between pb-3 mb-4 border-bottom border-secondary border-opacity-25">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-primary bg-opacity-25 text-info px-2 py-1 rounded-pill" style="border: 1px solid var(--admin-section-start, #41d1ff); font-weight: 600; font-size: 0.75rem;">
                    <i class="fas fa-bolt me-1"></i> ANALYTICS V2 — NEXT-GEN HUB
                </span>
                <span class="badge bg-secondary bg-opacity-25 text-light px-2 py-1 rounded-pill">
                    PRODUCION READY
                </span>
            </div>
            <h1 class="h3 fw-bold text-white mb-0">VIP Executive Intelligence Center</h1>
            <p class="text-muted small mb-0">Real-time revenue waterfall, multi-venue comparisons, affiliate attribution, and geospatial customer intelligence.</p>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2 mt-3 mt-md-0">
            <!-- Switch back to classic reports -->
            <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary btn-sm text-white px-3" style="border-radius: 8px; font-weight: 500;">
                <i class="fas fa-arrow-left me-1 text-info"></i> Switch to Classic Reports (V1)
            </a>

            <!-- Venue Switcher Dropdown -->
            <div class="dropdown">
                <button class="btn btn-dark btn-sm dropdown-toggle text-white px-3 border border-secondary border-opacity-25" type="button" id="venueSelectBtn" data-bs-toggle="dropdown" aria-expanded="false" style="background-color: var(--admin-surface-2, #171d2f); border-radius: 8px;">
                    <i class="fas fa-store text-info me-2"></i> <span id="currentVenueLabel">{{ $payload['meta']['venue_name'] }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow" aria-labelledby="venueSelectBtn">
                    <li><a class="dropdown-item venue-option active" href="#" data-id="">All Venues / Clubs</a></li>
                    <li><hr class="dropdown-divider"></li>
                    @foreach($venues as $v)
                    <li><a class="dropdown-item venue-option" href="#" data-id="{{ $v->id }}">{{ $v->name }}</a></li>
                    @endforeach
                </ul>
            </div>

            <!-- Date Range Selector -->
            <div class="btn-group btn-group-sm" role="group" id="periodBtnGroup" style="border-radius: 8px; overflow: hidden; border: 1px solid rgba(255,255,255,0.1);">
                <button type="button" class="btn btn-dark period-btn active" data-period="last_30_days" style="background-color: var(--admin-surface-2, #171d2f);">30D</button>
                <button type="button" class="btn btn-dark period-btn" data-period="last_7_days" style="background-color: var(--admin-surface, #121726);">7D</button>
                <button type="button" class="btn btn-dark period-btn" data-period="this_month" style="background-color: var(--admin-surface, #121726);">Month</button>
                <button type="button" class="btn btn-dark period-btn" data-period="this_year" style="background-color: var(--admin-surface, #121726);">Year</button>
            </div>

            <!-- Global Export Button -->
            <button class="btn btn-primary btn-sm px-3" style="background: linear-gradient(135deg, #41d1ff 0%, #0094ff 100%); border: none; border-radius: 8px; font-weight: 600;" onclick="exportCurrentModule()">
                <i class="fas fa-download me-1"></i> Export Data
            </button>
        </div>
    </div>

    <!-- REAL-TIME EXECUTIVE PULSE MARQUEE -->
    <div class="row g-3 mb-4">
        <!-- Gross Sales Card -->
        <div class="col-12 col-sm-6 col-xl-2">
            <div class="card border-0 shadow-sm p-3 h-100" style="background-color: var(--admin-surface, #121726); border-radius: 12px; border-left: 4px solid var(--admin-section-start, #41d1ff) !important;">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="text-muted small fw-semibold">GROSS SALES</span>
                    <span class="badge {{ $payload['executive_pulse']['gross_sales']['delta']['is_positive'] ? 'bg-success bg-opacity-25 text-success' : 'bg-danger bg-opacity-25 text-danger' }} rounded-pill px-2">
                        {{ $payload['executive_pulse']['gross_sales']['delta']['val'] }}
                    </span>
                </div>
                <h3 class="fw-bold text-white mb-0 mt-1" id="pulseGrossSales">{{ $payload['executive_pulse']['gross_sales']['val'] }}</h3>
                <span class="text-muted text-truncate" style="font-size: 0.72rem;">vs {{ $payload['meta']['prev_period_label'] }}</span>
            </div>
        </div>

        <!-- Orders Card -->
        <div class="col-12 col-sm-6 col-xl-2">
            <div class="card border-0 shadow-sm p-3 h-100" style="background-color: var(--admin-surface, #121726); border-radius: 12px; border-left: 4px solid #38bdf8 !important;">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="text-muted small fw-semibold">TOTAL ORDERS</span>
                    <span class="badge {{ $payload['executive_pulse']['orders_count']['delta']['is_positive'] ? 'bg-success bg-opacity-25 text-success' : 'bg-danger bg-opacity-25 text-danger' }} rounded-pill px-2">
                        {{ $payload['executive_pulse']['orders_count']['delta']['val'] }}
                    </span>
                </div>
                <h3 class="fw-bold text-white mb-0 mt-1" id="pulseOrders">{{ $payload['executive_pulse']['orders_count']['val'] }}</h3>
                <span class="text-muted text-truncate" style="font-size: 0.72rem;">Completed checkout bookings</span>
            </div>
        </div>

        <!-- Average Order Value (AOV) Card -->
        <div class="col-12 col-sm-6 col-xl-2">
            <div class="card border-0 shadow-sm p-3 h-100" style="background-color: var(--admin-surface, #121726); border-radius: 12px; border-left: 4px solid var(--admin-accent, #ffcc00) !important;">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="text-muted small fw-semibold">AVG ORDER VALUE</span>
                    <span class="badge {{ $payload['executive_pulse']['avg_order_value']['delta']['is_positive'] ? 'bg-success bg-opacity-25 text-success' : 'bg-danger bg-opacity-25 text-danger' }} rounded-pill px-2">
                        {{ $payload['executive_pulse']['avg_order_value']['delta']['val'] }}
                    </span>
                </div>
                <h3 class="fw-bold text-white mb-0 mt-1" id="pulseAov">{{ $payload['executive_pulse']['avg_order_value']['val'] }}</h3>
                <span class="text-muted text-truncate" style="font-size: 0.72rem;">Average Cart Size</span>
            </div>
        </div>

        <!-- Total Guest Volume Card -->
        <div class="col-12 col-sm-6 col-xl-2">
            <div class="card border-0 shadow-sm p-3 h-100" style="background-color: var(--admin-surface, #121726); border-radius: 12px; border-left: 4px solid #a855f7 !important;">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="text-muted small fw-semibold">GUEST VOLUME</span>
                    <span class="badge {{ $payload['executive_pulse']['total_guests']['delta']['is_positive'] ? 'bg-success bg-opacity-25 text-success' : 'bg-danger bg-opacity-25 text-danger' }} rounded-pill px-2">
                        {{ $payload['executive_pulse']['total_guests']['delta']['val'] }}
                    </span>
                </div>
                <h3 class="fw-bold text-white mb-0 mt-1" id="pulseGuests">{{ $payload['executive_pulse']['total_guests']['val'] }}</h3>
                <span class="text-muted text-truncate" style="font-size: 0.72rem;">Reserved Guest Attendees</span>
            </div>
        </div>

        <!-- Online Store Sessions Card -->
        <div class="col-12 col-sm-6 col-xl-2">
            <div class="card border-0 shadow-sm p-3 h-100" style="background-color: var(--admin-surface, #121726); border-radius: 12px; border-left: 4px solid #f43f5e !important;">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="text-muted small fw-semibold">STORE SESSIONS</span>
                    <span class="badge {{ $payload['executive_pulse']['sessions']['delta']['is_positive'] ? 'bg-success bg-opacity-25 text-success' : 'bg-danger bg-opacity-25 text-danger' }} rounded-pill px-2">
                        {{ $payload['executive_pulse']['sessions']['delta']['val'] }}
                    </span>
                </div>
                <h3 class="fw-bold text-white mb-0 mt-1" id="pulseSessions">{{ $payload['executive_pulse']['sessions']['val'] }}</h3>
                <span class="text-muted text-truncate" style="font-size: 0.72rem;">Tracked Traffic Visits</span>
            </div>
        </div>

        <!-- Store Conversion Rate Card -->
        <div class="col-12 col-sm-6 col-xl-2">
            <div class="card border-0 shadow-sm p-3 h-100" style="background-color: var(--admin-surface, #121726); border-radius: 12px; border-left: 4px solid var(--admin-section-end, #4ade80) !important;">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="text-muted small fw-semibold">CONVERSION RATE</span>
                    <span class="badge {{ $payload['executive_pulse']['conversion_rate']['delta']['is_positive'] ? 'bg-success bg-opacity-25 text-success' : 'bg-danger bg-opacity-25 text-danger' }} rounded-pill px-2">
                        {{ $payload['executive_pulse']['conversion_rate']['delta']['val'] }}
                    </span>
                </div>
                <h3 class="fw-bold text-white mb-0 mt-1" id="pulseConv">{{ $payload['executive_pulse']['conversion_rate']['val'] }}</h3>
                <span class="text-muted text-truncate" style="font-size: 0.72rem;">Visitors to Bookings %</span>
            </div>
        </div>
    </div>

    <!-- MODULE NAVIGATION TABS -->
    <ul class="nav nav-pills gap-2 pb-3 mb-4 border-bottom border-secondary border-opacity-25" id="v2ModuleTabs">
        <li class="nav-item">
            <button class="nav-link active rounded-pill px-3 py-2 text-white border border-secondary border-opacity-25" data-bs-toggle="pill" data-bs-target="#tabWaterfall" style="background-color: var(--admin-surface-2, #171d2f); font-weight: 500;">
                <i class="fas fa-waterfall text-info me-2"></i> Financial Waterfall
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link rounded-pill px-3 py-2 text-white border border-secondary border-opacity-25" data-bs-toggle="pill" data-bs-target="#tabVenueHeatmap" style="background-color: var(--admin-surface, #121726); font-weight: 500;">
                <i class="fas fa-building text-info me-2"></i> Club Heatmap
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link rounded-pill px-3 py-2 text-white border border-secondary border-opacity-25" data-bs-toggle="pill" data-bs-target="#tabAffiliateAttribution" style="background-color: var(--admin-surface, #121726); font-weight: 500;">
                <i class="fas fa-bullhorn text-warning me-2"></i> Affiliate Matrix
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link rounded-pill px-3 py-2 text-white border border-secondary border-opacity-25" data-bs-toggle="pill" data-bs-target="#tabEntertainers" style="background-color: var(--admin-surface, #121726); font-weight: 500;">
                <i class="fas fa-star text-purple me-2" style="color: #a855f7;"></i> Entertainer Performance
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link rounded-pill px-3 py-2 text-white border border-secondary border-opacity-25" data-bs-toggle="pill" data-bs-target="#tabGeospatial" style="background-color: var(--admin-surface, #121726); font-weight: 500;">
                <i class="fas fa-globe text-success me-2"></i> Geospatial & IP Heatmap
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link rounded-pill px-3 py-2 text-white border border-secondary border-opacity-25" data-bs-toggle="pill" data-bs-target="#tabGateways" style="background-color: var(--admin-surface, #121726); font-weight: 500;">
                <i class="fas fa-credit-card text-info me-2"></i> Gateway Matrix
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link rounded-pill px-3 py-2 text-white border border-secondary border-opacity-25" data-bs-toggle="pill" data-bs-target="#tabAIInsights" style="background-color: var(--admin-surface, #121726); font-weight: 500;">
                <i class="fas fa-brain text-warning me-2"></i> Executive AI Insights
            </button>
        </li>
    </ul>

    <!-- TAB CONTENTS -->
    <div class="tab-content" id="v2TabContent">

        <!-- 1. FINANCIAL WATERFALL TAB -->
        <div class="tab-pane fade show active" id="tabWaterfall">
            <div class="row g-4 mb-4">
                <div class="col-12 col-lg-7">
                    <div class="card border-0 shadow-sm p-4 h-100" style="background-color: var(--admin-surface, #121726); border-radius: 14px;">
                        <h5 class="fw-bold text-white mb-1"><i class="fas fa-chart-bar text-info me-2"></i> Platform Revenue Waterfall Flow</h5>
                        <p class="text-muted small mb-3">Gross sales minus refunds, affiliate commissions, and entertainer payouts to calculate net platform profit.</p>
                        <div style="height: 320px; position: relative;">
                            <canvas id="waterfallChartCanvas"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-5">
                    <div class="card border-0 shadow-sm p-4 h-100" style="background-color: var(--admin-surface, #121726); border-radius: 14px;">
                        <h5 class="fw-bold text-white mb-1"><i class="fas fa-list-ol text-info me-2"></i> Waterfall Summary Table</h5>
                        <p class="text-muted small mb-3">Percentage allocation of gross platform revenue.</p>
                        <div class="table-responsive">
                            <table class="table table-dark table-hover align-middle mb-0" id="waterfallTable">
                                <thead>
                                    <tr style="border-bottom: 2px solid rgba(255,255,255,0.1);">
                                        <th>Component</th>
                                        <th class="text-end">Amount ($)</th>
                                        <th class="text-end">% Share</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payload['revenue_waterfall']['table'] as $row)
                                    <tr>
                                        <td class="fw-semibold text-white">{{ $row['Component'] }}</td>
                                        <td class="text-end text-info fw-bold">${{ number_format($row['Amount'], 2) }}</td>
                                        <td class="text-end text-muted">{{ $row['% of Gross'] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. CLUB & VENUE HEATMAP TAB -->
        <div class="tab-pane fade" id="tabVenueHeatmap">
            <div class="row g-4 mb-4">
                <div class="col-12 col-lg-6">
                    <div class="card border-0 shadow-sm p-4 h-100" style="background-color: var(--admin-surface, #121726); border-radius: 14px;">
                        <h5 class="fw-bold text-white mb-1"><i class="fas fa-building text-info me-2"></i> Club & Venue Gross Revenue Ranking</h5>
                        <p class="text-muted small mb-3">Comparative venue ranking based on total completed checkout bookings.</p>
                        <div style="height: 320px; position: relative;">
                            <canvas id="venueChartCanvas"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="card border-0 shadow-sm p-4 h-100" style="background-color: var(--admin-surface, #121726); border-radius: 14px;">
                        <h5 class="fw-bold text-white mb-1"><i class="fas fa-table text-info me-2"></i> Detailed Venue Performance Matrix</h5>
                        <p class="text-muted small mb-3">Orders count, guest attendees, and avg spend per guest per venue.</p>
                        <div class="table-responsive" style="max-height: 320px; overflow-y: auto;">
                            <table class="table table-dark table-hover align-middle mb-0" id="venueTable">
                                <thead>
                                    <tr style="border-bottom: 2px solid rgba(255,255,255,0.1);">
                                        <th>Club / Venue</th>
                                        <th class="text-center">Orders</th>
                                        <th class="text-center">Guests</th>
                                        <th class="text-end">Gross Sales ($)</th>
                                        <th class="text-end">Avg/Guest ($)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payload['venue_heatmap']['table'] as $row)
                                    <tr>
                                        <td class="fw-semibold text-white">{{ $row['Club / Venue'] }}</td>
                                        <td class="text-center text-light">{{ number_format($row['Total Orders']) }}</td>
                                        <td class="text-center text-info">{{ number_format($row['Total Guests']) }}</td>
                                        <td class="text-end text-success fw-bold">${{ number_format($row['Gross Sales'], 2) }}</td>
                                        <td class="text-end text-warning">${{ number_format($row['Avg Spend per Guest'], 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. AFFILIATE MATRIX TAB -->
        <div class="tab-pane fade" id="tabAffiliateAttribution">
            <div class="row g-4 mb-4">
                <div class="col-12 col-lg-6">
                    <div class="card border-0 shadow-sm p-4 h-100" style="background-color: var(--admin-surface, #121726); border-radius: 14px;">
                        <h5 class="fw-bold text-white mb-1"><i class="fas fa-bullhorn text-warning me-2"></i> Top Affiliates Generated Revenue vs Commission</h5>
                        <p class="text-muted small mb-3">Compare gross sales driven by affiliates against commission expenses.</p>
                        <div style="height: 320px; position: relative;">
                            <canvas id="affiliateChartCanvas"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="card border-0 shadow-sm p-4 h-100" style="background-color: var(--admin-surface, #121726); border-radius: 14px;">
                        <h5 class="fw-bold text-white mb-1"><i class="fas fa-users text-warning me-2"></i> Affiliate Performance Ledger</h5>
                        <p class="text-muted small mb-3">Orders driven, target venue, gross sales, and commission earned.</p>
                        <div class="table-responsive" style="max-height: 320px; overflow-y: auto;">
                            <table class="table table-dark table-hover align-middle mb-0" id="affiliateTable">
                                <thead>
                                    <tr style="border-bottom: 2px solid rgba(255,255,255,0.1);">
                                        <th>Affiliate</th>
                                        <th>Target Venue</th>
                                        <th class="text-center">Orders</th>
                                        <th class="text-end">Revenue ($)</th>
                                        <th class="text-end">Commission ($)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payload['affiliate_attribution']['table'] as $row)
                                    <tr>
                                        <td class="fw-semibold text-white">{{ $row['Affiliate Name'] }}</td>
                                        <td class="text-muted small">{{ $row['Target Venue'] }}</td>
                                        <td class="text-center text-light">{{ number_format($row['Orders Driven']) }}</td>
                                        <td class="text-end text-info fw-bold">${{ number_format($row['Revenue Generated'], 2) }}</td>
                                        <td class="text-end text-warning fw-bold">${{ number_format($row['Commission Earned'], 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. ENTERTAINER PERFORMANCE TAB -->
        <div class="tab-pane fade" id="tabEntertainers">
            <div class="row g-4 mb-4">
                <div class="col-12 col-lg-6">
                    <div class="card border-0 shadow-sm p-4 h-100" style="background-color: var(--admin-surface, #121726); border-radius: 14px;">
                        <h5 class="fw-bold text-white mb-1"><i class="fas fa-star text-purple me-2" style="color:#a855f7;"></i> Top Entertainer Commission Earnings</h5>
                        <p class="text-muted small mb-3">Top entertainer payout distribution across events and package sales.</p>
                        <div style="height: 320px; position: relative;">
                            <canvas id="entertainerChartCanvas"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="card border-0 shadow-sm p-4 h-100" style="background-color: var(--admin-surface, #121726); border-radius: 14px;">
                        <h5 class="fw-bold text-white mb-1"><i class="fas fa-user-check text-info me-2"></i> Entertainer Sales Impact</h5>
                        <p class="text-muted small mb-3">Orders count, venue, gross sales, and payouts.</p>
                        <div class="table-responsive" style="max-height: 320px; overflow-y: auto;">
                            <table class="table table-dark table-hover align-middle mb-0" id="entertainerTable">
                                <thead>
                                    <tr style="border-bottom: 2px solid rgba(255,255,255,0.1);">
                                        <th>Entertainer</th>
                                        <th>Venue</th>
                                        <th class="text-center">Orders</th>
                                        <th class="text-end">Gross Sales ($)</th>
                                        <th class="text-end">Payout ($)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payload['entertainer_performance']['table'] as $row)
                                    <tr>
                                        <td class="fw-semibold text-white">{{ $row['Entertainer / Model'] }}</td>
                                        <td class="text-muted small">{{ $row['Club / Venue'] }}</td>
                                        <td class="text-center text-light">{{ number_format($row['Orders Generated']) }}</td>
                                        <td class="text-end text-info fw-bold">${{ number_format($row['Gross Sales'], 2) }}</td>
                                        <td class="text-end text-warning fw-bold">${{ number_format($row['Commission Payout'], 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. GEOSPATIAL & IP TAB -->
        <div class="tab-pane fade" id="tabGeospatial">
            <div class="row g-4 mb-4">
                <div class="col-12 col-lg-6">
                    <div class="card border-0 shadow-sm p-4 h-100" style="background-color: var(--admin-surface, #121726); border-radius: 14px;">
                        <h5 class="fw-bold text-white mb-1"><i class="fas fa-globe text-success me-2"></i> Geographic Sales Distribution</h5>
                        <p class="text-muted small mb-3">Revenue distribution by IP address region and customer billing country.</p>
                        <div style="height: 320px; position: relative;">
                            <canvas id="geospatialChartCanvas"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="card border-0 shadow-sm p-4 h-100" style="background-color: var(--admin-surface, #121726); border-radius: 14px;">
                        <h5 class="fw-bold text-white mb-1"><i class="fas fa-map-marker-alt text-success me-2"></i> Geographic Regional Breakdown</h5>
                        <p class="text-muted small mb-3">Orders count, target venue, gross sales, and market share.</p>
                        <div class="table-responsive" style="max-height: 320px; overflow-y: auto;">
                            <table class="table table-dark table-hover align-middle mb-0" id="geospatialTable">
                                <thead>
                                    <tr style="border-bottom: 2px solid rgba(255,255,255,0.1);">
                                        <th>Region / IP</th>
                                        <th>Target Venue</th>
                                        <th class="text-center">Orders</th>
                                        <th class="text-end">Gross Sales ($)</th>
                                        <th class="text-end">Share (%)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payload['geospatial_analytics']['table'] as $row)
                                    <tr>
                                        <td class="fw-semibold text-white">{{ $row['Geographic Region / IP'] }}</td>
                                        <td class="text-muted small">{{ $row['Target Venue'] }}</td>
                                        <td class="text-center text-light">{{ number_format($row['Orders']) }}</td>
                                        <td class="text-end text-success fw-bold">${{ number_format($row['Gross Sales'], 2) }}</td>
                                        <td class="text-end text-info">{{ $row['Market Share (%)'] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6. GATEWAY MATRIX TAB -->
        <div class="tab-pane fade" id="tabGateways">
            <div class="row g-4 mb-4">
                <div class="col-12 col-lg-5">
                    <div class="card border-0 shadow-sm p-4 h-100" style="background-color: var(--admin-surface, #121726); border-radius: 14px;">
                        <h5 class="fw-bold text-white mb-1"><i class="fas fa-chart-pie text-info me-2"></i> Gateway Share of Revenue</h5>
                        <p class="text-muted small mb-3">Volume distribution across Stripe, PayPal, Apple Pay, etc.</p>
                        <div style="height: 320px; position: relative;">
                            <canvas id="gatewayChartCanvas"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-7">
                    <div class="card border-0 shadow-sm p-4 h-100" style="background-color: var(--admin-surface, #121726); border-radius: 14px;">
                        <h5 class="fw-bold text-white mb-1"><i class="fas fa-credit-card text-info me-2"></i> Payment Processor Breakdown Table</h5>
                        <p class="text-muted small mb-3">Transaction volumes, gross sales, and gateway shares.</p>
                        <div class="table-responsive">
                            <table class="table table-dark table-hover align-middle mb-0" id="gatewayTable">
                                <thead>
                                    <tr style="border-bottom: 2px solid rgba(255,255,255,0.1);">
                                        <th>Payment Gateway</th>
                                        <th>Club / Venue</th>
                                        <th class="text-center">Orders</th>
                                        <th class="text-end">Gross Sales ($)</th>
                                        <th class="text-end">Share (%)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payload['gateway_matrix']['table'] as $row)
                                    <tr>
                                        <td class="fw-semibold text-white">{{ $row['Payment Gateway'] }}</td>
                                        <td class="text-muted small">{{ $row['Club / Venue'] }}</td>
                                        <td class="text-center text-light">{{ number_format($row['Orders']) }}</td>
                                        <td class="text-end text-info fw-bold">${{ number_format($row['Gross Sales'], 2) }}</td>
                                        <td class="text-end text-muted">{{ $row['Volume Share (%)'] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 7. AI INSIGHTS TAB -->
        <div class="tab-pane fade" id="tabAIInsights">
            <div class="row g-3">
                @foreach($payload['ai_insights'] as $insight)
                <div class="col-12 col-md-6">
                    <div class="card border-0 shadow-sm p-4 h-100" style="background-color: var(--admin-surface, #121726); border-radius: 14px; border-left: 4px solid var(--admin-section-start, #41d1ff) !important;">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <div class="rounded-circle p-3 bg-primary bg-opacity-25 text-info">
                                <i class="fas {{ $insight['icon'] }} fa-lg"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-white mb-0">{{ $insight['title'] }}</h6>
                                <span class="badge bg-secondary bg-opacity-25 text-muted">Automated Executive Intelligence</span>
                            </div>
                        </div>
                        <p class="text-light mb-0 mt-2" style="font-size: 0.95rem; line-height: 1.5;">{{ $insight['text'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </div>

</div>

<!-- CHART.JS INTEGRATION SCRIPT -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let currentFilters = {
    period: 'last_30_days',
    website_id: '',
    start_date: null,
    end_date: null
};

let activeTabModule = 'revenue_waterfall';

let charts = {};

document.addEventListener("DOMContentLoaded", function() {
    initCharts(@json($payload));

    // Venue filter change listener
    document.querySelectorAll('.venue-option').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelectorAll('.venue-option').forEach(el => el.classList.remove('active'));
            this.classList.add('active');
            
            currentFilters.website_id = this.getAttribute('data-id');
            document.getElementById('currentVenueLabel').innerText = this.innerText;
            fetchUpdatedAnalytics();
        });
    });

    // Period selector listener
    document.querySelectorAll('.period-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.period-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            currentFilters.period = this.getAttribute('data-period');
            fetchUpdatedAnalytics();
        });
    });

    // Track active tab
    const tabButtons = document.querySelectorAll('#v2ModuleTabs button[data-bs-toggle="pill"]');
    tabButtons.forEach(button => {
        button.addEventListener('shown.bs.tab', function(e) {
            const target = e.target.getAttribute('data-bs-target');
            if (target === '#tabWaterfall') activeTabModule = 'revenue_waterfall';
            else if (target === '#tabVenueHeatmap') activeTabModule = 'venue_heatmap';
            else if (target === '#tabAffiliateAttribution') activeTabModule = 'affiliate_attribution';
            else if (target === '#tabEntertainers') activeTabModule = 'entertainer_performance';
            else if (target === '#tabGeospatial') activeTabModule = 'geospatial_analytics';
            else if (target === '#tabGateways') activeTabModule = 'gateway_matrix';
        });
    });
});

function initCharts(payload) {
    // 1. Waterfall Chart
    const wfCtx = document.getElementById('waterfallChartCanvas').getContext('2d');
    if (charts.waterfall) charts.waterfall.destroy();
    charts.waterfall = new Chart(wfCtx, {
        type: 'bar',
        data: payload.revenue_waterfall.chart,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { ticks: { color: '#d4d9e8' }, grid: { color: 'rgba(255,255,255,0.05)' } },
                y: { ticks: { color: '#d4d9e8' }, grid: { color: 'rgba(255,255,255,0.05)' } }
            }
        }
    });

    // 2. Venue Chart
    const vCtx = document.getElementById('venueChartCanvas').getContext('2d');
    if (charts.venue) charts.venue.destroy();
    charts.venue = new Chart(vCtx, {
        type: 'bar',
        data: payload.venue_heatmap.chart,
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { ticks: { color: '#d4d9e8' }, grid: { color: 'rgba(255,255,255,0.05)' } },
                y: { ticks: { color: '#d4d9e8' }, grid: { display: false } }
            }
        }
    });

    // 3. Affiliate Chart
    const aCtx = document.getElementById('affiliateChartCanvas').getContext('2d');
    if (charts.affiliate) charts.affiliate.destroy();
    charts.affiliate = new Chart(aCtx, {
        type: 'bar',
        data: payload.affiliate_attribution.chart,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { labels: { color: '#d4d9e8' } } },
            scales: {
                x: { ticks: { color: '#d4d9e8' }, grid: { color: 'rgba(255,255,255,0.05)' } },
                y: { ticks: { color: '#d4d9e8' }, grid: { color: 'rgba(255,255,255,0.05)' } }
            }
        }
    });

    // 4. Entertainer Chart
    const eCtx = document.getElementById('entertainerChartCanvas').getContext('2d');
    if (charts.entertainer) charts.entertainer.destroy();
    charts.entertainer = new Chart(eCtx, {
        type: 'bar',
        data: payload.entertainer_performance.chart,
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { ticks: { color: '#d4d9e8' }, grid: { color: 'rgba(255,255,255,0.05)' } },
                y: { ticks: { color: '#d4d9e8' }, grid: { display: false } }
            }
        }
    });

    // 5. Geospatial Chart
    const gCtx = document.getElementById('geospatialChartCanvas').getContext('2d');
    if (charts.geospatial) charts.geospatial.destroy();
    charts.geospatial = new Chart(gCtx, {
        type: 'bar',
        data: payload.geospatial_analytics.chart,
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { ticks: { color: '#d4d9e8' }, grid: { color: 'rgba(255,255,255,0.05)' } },
                y: { ticks: { color: '#d4d9e8' }, grid: { display: false } }
            }
        }
    });

    // 6. Gateway Chart
    const gwCtx = document.getElementById('gatewayChartCanvas').getContext('2d');
    if (charts.gateway) charts.gateway.destroy();
    charts.gateway = new Chart(gwCtx, {
        type: 'doughnut',
        data: payload.gateway_matrix.chart,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { labels: { color: '#d4d9e8' } } }
        }
    });
}

function fetchUpdatedAnalytics() {
    const params = new URLSearchParams(currentFilters).toString();
    fetch(`{{ route('admin.analytics.v2.data') }}?${params}`)
        .then(res => res.json())
        .then(data => {
            // Update Pulse
            document.getElementById('pulseGrossSales').innerText = data.executive_pulse.gross_sales.val;
            document.getElementById('pulseOrders').innerText = data.executive_pulse.orders_count.val;
            document.getElementById('pulseAov').innerText = data.executive_pulse.avg_order_value.val;
            document.getElementById('pulseGuests').innerText = data.executive_pulse.total_guests.val;
            document.getElementById('pulseSessions').innerText = data.executive_pulse.sessions.val;
            document.getElementById('pulseConv').innerText = data.executive_pulse.conversion_rate.val;

            // Re-init charts with updated data
            initCharts(data);
        })
        .catch(err => console.error("Error updating analytics data:", err));
}

function exportCurrentModule() {
    const params = new URLSearchParams({
        ...currentFilters,
        module: activeTabModule,
        format: 'csv'
    }).toString();
    window.location.href = `{{ route('admin.analytics.v2.export') }}?${params}`;
}
</script>
@endsection
