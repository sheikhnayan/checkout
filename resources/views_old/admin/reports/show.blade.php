@extends('admin.main')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
                <i class="fas fa-arrow-left me-2"></i>Back to Reports
            </a>
            <h1 class="h2 mb-1" style="color: #fff !important">{{ $report->name }}</h1>
            <p class="text-muted mb-0">{{ $report->description }}</p>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2 w-100 w-md-auto justify-content-start justify-content-md-end">
            @if(isset($canSwitchClubs) && $canSwitchClubs && isset($accessibleWebsites) && $accessibleWebsites->count() > 0)
                <div class="d-flex align-items-center bg-dark p-2 px-3 rounded border border-secondary border-opacity-25 shadow-sm flex-grow-1 flex-md-grow-0" style="background: rgba(18, 23, 38, 0.95) !important; max-width: 100%;">
                    <span class="text-white small fw-bold me-2 text-nowrap"><i class="fas fa-building text-primary me-1"></i>Club:</span>
                    <select class="form-select form-select-sm border-0 bg-transparent text-white fw-bold py-1 pe-4 cursor-pointer flex-grow-1" id="headerClubSwitchSelect" style="box-shadow: none; width: 100%; font-size: 0.875rem; min-width: 0;" onchange="const form = document.getElementById('filterForm'); if(form) { const inp = form.querySelector('[name=website_id]'); if(inp) inp.value = this.value; form.submit(); }">
                        <option value="all" class="bg-dark text-white" {{ ($selectedWebsiteId ?? 'all') == 'all' ? 'selected' : '' }}>
                            🏢 All Accessible Clubs ({{ $accessibleWebsites->count() }})
                        </option>
                        @foreach($accessibleWebsites as $web)
                            <option value="{{ $web->id }}" class="bg-dark text-white" {{ ($selectedWebsiteId ?? 'all') == $web->id ? 'selected' : '' }}>
                                📍 {{ $web->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
            <button type="button" class="btn btn-primary btn-sm text-nowrap flex-grow-1 flex-md-grow-0" data-bs-toggle="modal" data-bs-target="#shopifyExportModal">
                <i class="fas fa-file-export me-2"></i>Export Report
            </button>
        </div>
    </div>

    <!-- Executive Analytics KPI Cards & Comparison Graph (Shopify Analytics Style aligned with Admin Theme) -->
    <div class="card shadow-sm mb-4 border-0" id="executiveAnalyticsCard" style="display: none; background: var(--admin-surface, #121726) !important; border: 1px solid var(--admin-border, rgba(255,255,255,0.1)) !important; border-radius: 12px;">
        <div class="card-body p-4">
            <!-- Metric Tab Buttons -->
            <div class="row g-3 mb-4" id="kpiTabsRow">
                <div class="col-md-3 col-6">
                    <div class="kpi-card p-3 rounded cursor-pointer active-kpi" id="tab-sessions" onclick="switchKpiMetric('sessions')">
                        <div class="small mb-1" style="color: var(--admin-text-muted, #d4d9e8);">Sessions</div>
                        <div class="d-flex align-items-center gap-2">
                            <h3 class="mb-0 text-white fw-bold" id="kpi-val-sessions">-</h3>
                            <span class="badge" id="kpi-badge-sessions"></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="kpi-card p-3 rounded cursor-pointer" id="tab-total_sales" onclick="switchKpiMetric('total_sales')">
                        <div class="small mb-1" style="color: var(--admin-text-muted, #d4d9e8);">Total Sales</div>
                        <div class="d-flex align-items-center gap-2">
                            <h3 class="mb-0 text-white fw-bold" id="kpi-val-total_sales">-</h3>
                            <span class="badge" id="kpi-badge-total_sales"></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="kpi-card p-3 rounded cursor-pointer" id="tab-orders" onclick="switchKpiMetric('orders')">
                        <div class="small mb-1" style="color: var(--admin-text-muted, #d4d9e8);">Orders</div>
                        <div class="d-flex align-items-center gap-2">
                            <h3 class="mb-0 text-white fw-bold" id="kpi-val-orders">-</h3>
                            <span class="badge" id="kpi-badge-orders"></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="kpi-card p-3 rounded cursor-pointer" id="tab-conversion_rate" onclick="switchKpiMetric('conversion_rate')">
                        <div class="small mb-1" style="color: var(--admin-text-muted, #d4d9e8);">Conversion Rate</div>
                        <div class="d-flex align-items-center gap-2">
                            <h3 class="mb-0 text-white fw-bold" id="kpi-val-conversion_rate">-</h3>
                            <span class="badge" id="kpi-badge-conversion_rate"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Comparison Chart -->
            <div class="p-3 rounded" style="background: var(--admin-surface-2, #171d2f) !important; border: 1px solid var(--admin-border, rgba(255,255,255,0.1)) !important;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="text-white mb-0 fw-bold" id="executiveChartTitle">Metrics over time</h5>
                    <div class="small" style="color: var(--admin-text-muted, #d4d9e8);" id="executiveChartPeriodLabel">Comparing to previous period</div>
                </div>
                <div style="height: 280px; position: relative;">
                    <canvas id="executiveComparisonChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Filters Sidebar -->
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark border-bottom border-secondary border-opacity-25">
                    <h5 class="mb-0 text-white">Filters</h5>
                </div>
                <div class="card-body">
                    <form id="filterForm" method="GET">
                        @if(isset($canSwitchClubs) && $canSwitchClubs && isset($accessibleWebsites) && $accessibleWebsites->count() > 0)
                        <!-- Club Switcher -->
                        <div class="mb-4">
                            <label class="form-label fw-bold text-white d-flex align-items-center justify-content-between">
                                <span><i class="fas fa-building text-primary me-1"></i> Club / Venue</span>
                                <span class="badge bg-primary bg-opacity-25 text-primary small">Multi-Club</span>
                            </label>
                            <select name="website_id" class="form-select form-select-sm text-white custom-club-select" id="filterFormClubInput" onchange="this.form.submit();">
                                <option value="all" {{ ($selectedWebsiteId ?? 'all') == 'all' ? 'selected' : '' }}>
                                    🏢 All Accessible Clubs ({{ $accessibleWebsites->count() }})
                                </option>
                                @foreach($accessibleWebsites as $web)
                                    <option value="{{ $web->id }}" {{ ($selectedWebsiteId ?? 'all') == $web->id ? 'selected' : '' }}>
                                        📍 {{ $web->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @else
                            <input type="hidden" name="website_id" value="{{ $selectedWebsiteId ?? 'all' }}">
                        @endif
                        <!-- Date Range -->
                        <div class="mb-4">
                            <label class="form-label fw-bold text-white">Date Range</label>
                            <select name="date_range" class="form-select form-select-sm" id="dateRange">
                                <option value="today" {{ request('date_range') === 'today' ? 'selected' : '' }}>Today</option>
                                <option value="yesterday" {{ request('date_range') === 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                                <option value="last_7_days" {{ request('date_range') === 'last_7_days' ? 'selected' : '' }}>Last 7 Days</option>
                                <option value="last_30_days" {{ request('date_range') === 'last_30_days' ? 'selected' : '' }} selected>Last 30 Days</option>
                                <option value="last_90_days" {{ request('date_range') === 'last_90_days' ? 'selected' : '' }}>Last 90 Days</option>
                                <option value="this_month" {{ request('date_range') === 'this_month' ? 'selected' : '' }}>This Month</option>
                                <option value="last_month" {{ request('date_range') === 'last_month' ? 'selected' : '' }}>Last Month</option>
                                <option value="this_year" {{ request('date_range') === 'this_year' ? 'selected' : '' }}>This Year</option>
                                <option value="custom" {{ request('date_range') === 'custom' ? 'selected' : '' }}>Custom</option>
                            </select>
                        </div>

                        <!-- Custom Date Range (if selected) -->
                        <div id="customDateRange" style="display: none;" class="mb-4">
                            <div class="mb-2">
                                <label class="form-label small text-muted">From</label>
                                <input type="date" name="custom_from" class="form-control form-control-sm bg-dark text-white border-secondary" value="{{ request('custom_from') }}">
                            </div>
                            <div class="mb-2">
                                <label class="form-label small text-muted">To</label>
                                <input type="date" name="custom_to" class="form-control form-control-sm bg-dark text-white border-secondary" value="{{ request('custom_to') }}">
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-filter me-2"></i>Apply Filters
                            </button>
                            <a href="{{ route('admin.reports.show', $report) }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-redo me-2"></i>Reset
                            </a>
                        </div>
                    </form>

                    <hr class="my-4 border-secondary border-opacity-25">

                    <!-- Actions -->
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Actions</label>
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#saveReportModal">
                                <i class="fas fa-save me-2"></i>Save Report View
                            </button>
                            <button type="button" class="btn btn-primary btn-sm" id="previewPdfBtn">
                                <i class="fas fa-eye me-2"></i>Preview PDF
                            </button>
                            <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#shopifyExportModal">
                                <i class="fas fa-download me-2"></i>Export Options
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Saved Reports -->
            @if($savedReports->count())
                <div class="card shadow-sm mt-4 border-0">
                    <div class="card-header bg-dark border-bottom border-secondary border-opacity-25">
                        <h5 class="mb-0 text-white">Saved Reports</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush bg-transparent">
                            @foreach($savedReports as $saved)
                                <a href="{{ route('admin.reports.show', ['report' => $report, 'saved' => $saved->id]) }}" class="list-group-item list-group-item-action bg-transparent text-white border-secondary border-opacity-25 py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="small"><i class="fas fa-bookmark text-warning me-2"></i>{{ $saved->name }}</span>
                                        <form method="POST" action="{{ route('admin.reports.preferences.delete', $saved) }}" style="display: inline;" onsubmit="return confirm('Delete this saved report?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-link btn-sm p-0 text-danger">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Report Display -->
        <div class="col-md-9">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <!-- Top Horizontal Bar Chart Container (if report supports it) -->
                    <div id="topChartContainer" class="mb-4" style="display: none;">
                        <h5 class="text-white mb-3" id="topChartTitle">Top Summary Distribution</h5>
                        <div style="height: 250px; position: relative;">
                            <canvas id="topHorizontalChart"></canvas>
                        </div>
                        <hr class="my-4 border-secondary border-opacity-25">
                    </div>

                    <div id="reportContainer" class="position-relative" style="min-height: 400px;">
                        <div class="d-flex justify-content-center align-items-center" style="height: 400px;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Report Info -->
            <div class="card shadow-sm mt-4 border-0">
                <div class="card-header bg-dark border-bottom border-secondary border-opacity-25">
                    <h6 class="mb-0 text-white">Report Details</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">Category</small>
                            <p class="mb-0 text-white fw-bold">{{ $report->category }}</p>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">Type</small>
                            <p class="mb-0 text-white fw-bold">{{ $report->type }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="shopifyExportModal" tabindex="-1" aria-labelledby="shopifyExportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-white" style="background-color: #121726 !important; border: 1px solid rgba(255, 255, 255, 0.15) !important; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
            <style>
                #shopifyExportModal .export-option-card {
                    background: rgba(255, 255, 255, 0.03) !important;
                    border: 1px solid rgba(255, 255, 255, 0.1) !important;
                    border-radius: 8px;
                    padding: 1rem;
                    transition: all 0.2s ease-in-out;
                    cursor: pointer;
                }
                #shopifyExportModal .export-option-card:hover {
                    background: rgba(255, 255, 255, 0.07) !important;
                    border-color: rgba(65, 209, 255, 0.4) !important;
                }
                #shopifyExportModal .export-option-card:has(input[type="radio"]:checked) {
                    background: rgba(65, 209, 255, 0.08) !important;
                    border-color: #41d1ff !important;
                }
                #shopifyExportModal .export-option-card .form-check-input {
                    cursor: pointer;
                    margin-top: 0.25rem;
                }
            </style>
            <div class="modal-header" style="border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important; padding: 1.25rem 1.5rem;">
                <h5 class="modal-title fs-5 fw-bold text-white d-flex align-items-center mb-0" id="shopifyExportModalLabel" style="background: transparent !important; color: #ffffff !important;">
                    <i class="fas fa-file-export me-2 text-primary"></i>Export report
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 1.5rem;">
                <div class="mb-4">
                    <label class="form-label fw-bold text-white mb-3 fs-6">Select a format:</label>

                    <div class="export-option-card mb-2" onclick="document.getElementById('fmtCsv').click();">
                        <div class="d-flex align-items-start gap-3">
                            <input class="form-check-input flex-shrink-0" type="radio" name="exportFormatChoice" id="fmtCsv" value="csv" checked onclick="event.stopPropagation();">
                            <label class="w-100 cursor-pointer mb-0" for="fmtCsv" onclick="event.stopPropagation();">
                                <strong class="text-white d-block mb-1 fs-6">Comma Separated Values (CSV)</strong>
                                <small style="color: #a0aec0 !important;" class="d-block">Best for importing into spreadsheets (Excel, Google Sheets)</small>
                            </label>
                        </div>
                    </div>

                    <div class="export-option-card mb-2" onclick="document.getElementById('fmtExcel').click();">
                        <div class="d-flex align-items-start gap-3">
                            <input class="form-check-input flex-shrink-0" type="radio" name="exportFormatChoice" id="fmtExcel" value="excel" onclick="event.stopPropagation();">
                            <label class="w-100 cursor-pointer mb-0" for="fmtExcel" onclick="event.stopPropagation();">
                                <strong class="text-white d-block mb-1 fs-6">Microsoft Excel (.xlsx)</strong>
                                <small style="color: #a0aec0 !important;" class="d-block">Structured spreadsheet format with formatting</small>
                            </label>
                        </div>
                    </div>

                    <div class="export-option-card mb-2" onclick="document.getElementById('fmtPdf').click();">
                        <div class="d-flex align-items-start gap-3">
                            <input class="form-check-input flex-shrink-0" type="radio" name="exportFormatChoice" id="fmtPdf" value="pdf" onclick="event.stopPropagation();">
                            <label class="w-100 cursor-pointer mb-0" for="fmtPdf" onclick="event.stopPropagation();">
                                <strong class="text-white d-block mb-1 fs-6">PDF Document (.pdf)</strong>
                                <small style="color: #a0aec0 !important;" class="d-block">Printable document format for executive summaries</small>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="form-label fw-bold text-white mb-2 fs-6">Specify the results you want:</label>
                    <div class="d-flex flex-column gap-2 ps-1">
                        <div class="form-check cursor-pointer" onclick="document.getElementById('scopeAll').click();">
                            <input class="form-check-input cursor-pointer" type="radio" name="exportScopeChoice" id="scopeAll" value="all" checked onclick="event.stopPropagation();">
                            <label class="form-check-label text-white cursor-pointer" for="scopeAll" onclick="event.stopPropagation();">
                                All results from the data query
                            </label>
                        </div>
                        <div class="form-check cursor-pointer" onclick="document.getElementById('scopeDisplayed').click();">
                            <input class="form-check-input cursor-pointer" type="radio" name="exportScopeChoice" id="scopeDisplayed" value="displayed" onclick="event.stopPropagation();">
                            <label class="form-check-label text-white cursor-pointer" for="scopeDisplayed" onclick="event.stopPropagation();">
                                Only results displayed in the report
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid rgba(255, 255, 255, 0.1) !important; padding: 1rem 1.5rem;">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary px-4" id="btnExecuteExport">
                    <i class="fas fa-download me-2"></i>Export
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Save Report Modal -->
<div class="modal fade" id="saveReportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white border border-secondary">
            <div class="modal-header border-secondary border-opacity-50">
                <h5 class="modal-title">Save This Report View</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.reports.preferences.save', $report) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Report Name</label>
                        <input type="text" name="name" class="form-control bg-dark text-white border-secondary" placeholder="e.g., Q4 Sales Report" required>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="is_favorite" value="1" class="form-check-input" id="isFavorite">
                        <label class="form-check-label" for="isFavorite">
                            Mark as favorite
                        </label>
                    </div>
                </div>
                <div class="modal-footer border-secondary border-opacity-50">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Report</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
let executiveData = null;
let executiveChartInstance = null;
let topChartInstance = null;
let activeMetricKey = 'total_sales';

document.addEventListener('DOMContentLoaded', function() {
    // Show/hide custom date range
    document.getElementById('dateRange').addEventListener('change', function() {
        document.getElementById('customDateRange').style.display = 
            this.value === 'custom' ? 'block' : 'none';
    });

    if (document.getElementById('dateRange').value === 'custom') {
        document.getElementById('customDateRange').style.display = 'block';
    }

    loadReportData();

    document.getElementById('btnExecuteExport').addEventListener('click', function() {
        const fmt = document.querySelector('input[name="exportFormatChoice"]:checked').value;
        exportReport(fmt);
        const modalEl = document.getElementById('shopifyExportModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
    });

    var previewBtn = document.getElementById('previewPdfBtn');
    if (previewBtn) {
        previewBtn.addEventListener('click', function() {
            previewReportPdf();
        });
    }
});

function loadReportData() {
    const url = new URL(window.location.href);
    const params = new URLSearchParams(url.search);
    
    fetch('{{ route("admin.reports.show", $report) }}?ajax=1&' + params.toString(), {
        headers: { 'Accept': 'application/json' }
    })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                document.getElementById('reportContainer').innerHTML =
                    '<div class="alert alert-danger">Error loading report: ' + (data.error || 'Unknown response') + '</div>';
                return;
            }

            const payload = data.report ? { ...data.report, ...data.data } : data;
            
            if (payload.executive_metrics) {
                renderExecutiveHeader(payload.executive_metrics);
            }

            renderReport(payload);
        })
        .catch(error => {
            document.getElementById('reportContainer').innerHTML = 
                '<div class="alert alert-danger">Error loading report: ' + error + '</div>';
        });
}

function renderExecutiveHeader(metrics) {
    executiveData = metrics;
    document.getElementById('executiveAnalyticsCard').style.display = 'block';

    const keys = ['sessions', 'total_sales', 'orders', 'conversion_rate'];
    keys.forEach(key => {
        const item = metrics[key];
        if (!item) return;

        document.getElementById(`kpi-val-${key}`).innerText = item.value;
        const badge = document.getElementById(`kpi-badge-${key}`);
        const pct = item.change_pct;

        if (pct >= 0) {
            badge.className = 'badge bg-success bg-opacity-25 text-success border border-success border-opacity-50';
            badge.innerHTML = `<i class="fas fa-arrow-up me-1"></i>+${pct}%`;
        } else {
            badge.className = 'badge bg-danger bg-opacity-25 text-danger border border-danger border-opacity-50';
            badge.innerHTML = `<i class="fas fa-arrow-down me-1"></i>${pct}%`;
        }
    });

    document.getElementById('executiveChartPeriodLabel').innerText = 
        `${metrics.period_labels.current} vs. ${metrics.period_labels.previous}`;

    switchKpiMetric(activeMetricKey);
}

function switchKpiMetric(key) {
    activeMetricKey = key;
    document.querySelectorAll('.kpi-card').forEach(el => el.classList.remove('active-kpi'));
    const activeTab = document.getElementById(`tab-${key}`);
    if (activeTab) activeTab.classList.add('active-kpi');

    if (!executiveData) return;

    const titles = {
        'sessions': 'Sessions over time',
        'total_sales': 'Total sales over time',
        'orders': 'Orders over time',
        'conversion_rate': 'Conversion rate over time'
    };
    document.getElementById('executiveChartTitle').innerText = titles[key] || 'Metrics over time';

    const item = executiveData[key];
    const labels = executiveData.chart_labels || [];
    const currentData = item ? item.chart_current : [];
    const previousData = item ? item.chart_previous : [];

    const ctx = document.getElementById('executiveComparisonChart').getContext('2d');
    if (executiveChartInstance) {
        executiveChartInstance.destroy();
    }

    executiveChartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: executiveData.period_labels.current,
                    data: currentData,
                    borderColor: '#41d1ff',
                    backgroundColor: 'rgba(65, 209, 255, 0.12)',
                    borderWidth: 3,
                    tension: 0.35,
                    fill: true
                },
                {
                    label: executiveData.period_labels.previous,
                    data: previousData,
                    borderColor: '#9fdcff',
                    borderDash: [5, 5],
                    borderWidth: 2,
                    tension: 0.35,
                    fill: false
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: { color: '#d4d9e8', font: { size: 12, family: "'Public Sans', sans-serif" } }
                }
            },
            scales: {
                x: {
                    ticks: { color: '#d4d9e8', font: { family: "'Public Sans', sans-serif" } },
                    grid: { color: 'rgba(255,255,255,0.06)' }
                },
                y: {
                    ticks: { color: '#d4d9e8', font: { family: "'Public Sans', sans-serif" } },
                    grid: { color: 'rgba(255,255,255,0.06)' }
                }
            }
        }
    });
}

function renderReport(data) {
    const container = document.getElementById('reportContainer');

    if (!data || !data.type) {
        container.innerHTML = '<div class="alert alert-danger">Unable to load report data.</div>';
        return;
    }

    if (data.executive_metrics) {
        renderExecutiveHeader(data.executive_metrics);
    } else {
        document.getElementById('executiveAnalyticsCard').style.display = 'none';
    }

    const topChartContainer = document.getElementById('topChartContainer');
    if (data.has_chart && data.chart_type === 'horizontal_bar' && data.chart_data) {
        topChartContainer.style.display = 'block';
        document.getElementById('topChartTitle').innerText = data.title + ' (Top Distribution)';
        renderTopChart('bar', data.chart_data, { indexAxis: 'y' });
    } else if ((data.type === 'line_chart' || data.type === 'bar_chart' || data.type === 'stacked_bar') && data.data && !data.executive_metrics) {
        topChartContainer.style.display = 'block';
        document.getElementById('topChartTitle').innerText = data.title;
        const chartKind = data.type === 'stacked_bar' ? 'bar' : (data.type === 'bar_chart' ? 'bar' : 'line');
        renderTopChart(chartKind, data.data, { stacked: data.type === 'stacked_bar' });
    } else if (data.type === 'pie_chart' && data.data && !data.executive_metrics) {
        topChartContainer.style.display = 'block';
        document.getElementById('topChartTitle').innerText = data.title;
        renderTopChart('doughnut', data.data);
    } else {
        topChartContainer.style.display = 'none';
    }

    if (data.raw_data && data.raw_data.length > 0) {
        renderTable({ data: data.raw_data, summary: data.summary });
    } else if (data.data && Array.isArray(data.data) && data.data.length > 0) {
        renderTable({ data: data.data, summary: data.summary });
    } else if (data.type === 'metric') {
        renderMetrics(data);
    } else if (data.type === 'table' && (!data.data || data.data.length === 0)) {
        container.innerHTML = '<div class="alert alert-info border border-secondary text-white"><i class="fas fa-info-circle me-2 text-info"></i>No data recorded for the selected date range.</div>';
    } else {
        container.innerHTML = '<div class="alert alert-info border border-secondary text-white"><i class="fas fa-chart-line me-2 text-info"></i>Report breakdown displayed above.</div>';
    }
}

function renderTopChart(chartType, chartData, extraOptions = {}) {
    const topCtx = document.getElementById('topHorizontalChart').getContext('2d');
    if (topChartInstance) topChartInstance.destroy();

    const isHorizontal = extraOptions.indexAxis === 'y';
    const isStacked = !!extraOptions.stacked;

    topChartInstance = new Chart(topCtx, {
        type: chartType,
        data: chartData,
        options: {
            indexAxis: isHorizontal ? 'y' : 'x',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: chartType === 'doughnut' || (chartData.datasets && chartData.datasets.length > 1),
                    labels: { color: '#d4d9e8', font: { family: "'Public Sans', sans-serif" } }
                }
            },
            scales: chartType === 'doughnut' ? {} : {
                x: { stacked: isStacked, ticks: { color: '#d4d9e8' }, grid: { color: 'rgba(255,255,255,0.06)' } },
                y: { stacked: isStacked, ticks: { color: '#d4d9e8' }, grid: { color: 'rgba(255,255,255,0.06)' } }
            }
        }
    });
}

function renderChart(data) {
    const container = document.getElementById('reportContainer');
    container.innerHTML = '<div style="height: 380px;"><canvas id="reportChart"></canvas></div>';
    
    const ctx = document.getElementById('reportChart').getContext('2d');
    const chartType = data.type === 'stacked_bar' ? 'bar' : (data.type === 'bar_chart' ? 'bar' : 'line');
    
    new Chart(ctx, {
        type: chartType,
        data: data.data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: { display: true, text: data.title, color: '#ffffff', font: { size: 16 } },
                legend: { labels: { color: '#94a3b8' } }
            },
            scales: {
                x: { stacked: data.type === 'stacked_bar', ticks: { color: '#94a3b8' }, grid: { color: 'rgba(255,255,255,0.05)' } },
                y: { stacked: data.type === 'stacked_bar', ticks: { color: '#94a3b8' }, grid: { color: 'rgba(255,255,255,0.05)' } }
            }
        }
    });
}

function renderPieChart(data) {
    const container = document.getElementById('reportContainer');
    container.innerHTML = '<div style="height: 380px;"><canvas id="reportChart"></canvas></div>';
    
    const ctx = document.getElementById('reportChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: data.data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: { display: true, text: data.title, color: '#ffffff', font: { size: 16 } },
                legend: { labels: { color: '#94a3b8' } }
            }
        }
    });
}

function renderTable(data) {
    const container = document.getElementById('reportContainer');
    if (!data.data || data.data.length === 0) {
        container.innerHTML = '<div class="alert alert-info">No data available</div>';
        return;
    }

    const headers = Object.keys(data.data[0]);

    let html = '<div class="table-responsive"><table class="table table-dark table-hover align-middle mb-0"><thead class="table-dark text-white border-bottom border-secondary"><tr>';
    
    headers.forEach(header => {
        html += '<th class="fw-bold">' + header + '</th>';
    });
    html += '</tr></thead><tbody>';
    
    data.data.forEach(row => {
        html += '<tr>';
        headers.forEach(header => {
            html += '<td>' + formatReportValue(row[header], header) + '</td>';
        });
        html += '</tr>';
    });
    
    html += '</tbody>';

    // Summary footer row
    if (data.summary) {
        html += '<tfoot class="table-dark text-white fw-bold border-top border-2 border-primary"><tr>';
        headers.forEach(header => {
            const val = data.summary[header] !== undefined ? data.summary[header] : '-';
            html += '<td class="text-primary fw-bold">' + formatReportValue(val, header) + '</td>';
        });
        html += '</tr></tfoot>';
    }

    html += '</table></div>';
    container.innerHTML = html;
}

function renderMetrics(data) {
    const container = document.getElementById('reportContainer');
    let html = '<div class="row g-3">';
    let metrics = data.metrics;

    if (!metrics) {
        container.innerHTML = '<div class="alert alert-info">No metric data available.</div>';
        return;
    }

    if (!Array.isArray(metrics)) {
        metrics = Object.entries(metrics).map(([key, value]) => ({
            label: humanizeMetricLabel(key),
            value,
        }));
    }

    if (metrics.length === 0) {
        container.innerHTML = '<div class="alert alert-info">No metric data available.</div>';
        return;
    }

    metrics.forEach(metric => {
        html += `
            <div class="col-md-4 mb-3">
                <div class="card border-0 bg-dark text-white p-3 rounded border border-secondary border-opacity-25">
                    <div class="card-body text-center">
                        <small class="text-muted text-uppercase fw-bold">${metric.label}</small>
                        <h2 class="mb-0 mt-2 text-primary fw-bold">${formatReportValue(metric.value, metric.label)}</h2>
                    </div>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    container.innerHTML = html;
}

function humanizeMetricLabel(key) {
    return key
        .replace(/_/g, ' ')
        .replace(/\b\w/g, char => char.toUpperCase());
}

function formatReportValue(value, header = '') {
    if (value === null || value === undefined || value === '') {
        return '-';
    }

    const hLower = String(header).toLowerCase();
    const isCountField = ['count', 'guests', 'attendees', 'orders', 'items', 'qty', 'quantity', 'views', 'sessions', 'number', 'times', 'tickets'].some(k => hLower.includes(k));
    const isCurrencyField = !isCountField && ['revenue', 'sales', 'total', 'amount', 'price', 'discount', 'commission', 'net', 'gross', 'refund', 'tax', 'earnings', 'payout', 'spend', 'value'].some(k => hLower.includes(k));

    if (typeof value === 'number' && Number.isFinite(value)) {
        if (isCurrencyField) {
            return '$' + value.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
        if (Number.isInteger(value)) {
            return value.toLocaleString('en-US');
        }
        return value.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // String number check
    if (typeof value === 'string' && !isNaN(value) && value.trim() !== '') {
        const num = parseFloat(value);
        if (isCurrencyField) {
            return '$' + num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
        if (Number.isInteger(num)) {
            return num.toLocaleString('en-US');
        }
    }

    return value;
}

function exportReport(format) {
    const url = new URL(window.location.href);
    const params = new URLSearchParams(url.search);
    params.set('format', format);

    const form = document.getElementById('exportReportForm');
    const exportInput = document.getElementById('exportFormat');
    exportInput.value = format;

    document.querySelectorAll('#exportReportForm input.dynamic-param').forEach(el => el.remove());

    params.forEach((value, key) => {
        if (key === 'format') return;
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = value;
        input.classList.add('dynamic-param');
        form.appendChild(input);
    });

    form.submit();
}

function previewReportPdf() {
    const url = new URL(window.location.href);
    const params = new URLSearchParams(url.search);
    const previewUrl = new URL(@json(route('admin.reports.previewPdf', $report)), window.location.origin);
    params.forEach((value, key) => {
        previewUrl.searchParams.set(key, value);
    });
    window.open(previewUrl.toString(), '_blank');
}
</script>

<form id="exportReportForm" method="POST" action="{{ route('admin.reports.export', $report) }}" style="display:none;">
    @csrf
    <input type="hidden" name="format" id="exportFormat" value="" />
</form>

<style>
.kpi-card {
    background: var(--admin-surface-2, #171d2f) !important;
    border: 1px solid var(--admin-border, rgba(255, 255, 255, 0.1)) !important;
    transition: all 0.2s ease-in-out;
}
.kpi-card:hover {
    background: rgba(65, 209, 255, 0.08) !important;
    border-color: rgba(65, 209, 255, 0.4) !important;
}
.active-kpi {
    background: rgba(65, 209, 255, 0.15) !important;
    border-color: var(--admin-section-start, #41d1ff) !important;
    box-shadow: 0 0 12px rgba(65, 209, 255, 0.25) !important;
}
.cursor-pointer {
    cursor: pointer;
}

#dateRange, .custom-club-select {
    appearance: none !important;
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath fill='%23f1f5f9' d='M8 11L3 6h10z'/%3E%3C/svg%3E") !important;
    background-repeat: no-repeat !important;
    background-position: right 8px center !important;
    background-size: 18px !important;
    padding-right: 40px !important;
    color: #fff !important;
    background-color: rgba(255,255,255,0.1) !important;
    border: 1px solid rgba(255,255,255,0.2) !important;
}

#dateRange:hover, .custom-club-select:hover {
    background-color: rgba(255,255,255,0.15) !important;
}

#dateRange:focus, .custom-club-select:focus {
    background-color: rgba(255,255,255,0.15) !important;
    border-color: rgba(124,58,237,0.5) !important;
    box-shadow: 0 0 0 0.2rem rgba(124, 58, 237, 0.25) !important;
}

#dateRange option, .custom-club-select option {
    background-color: #1e293b;
    color: #fff;
}
</style>
@endsection
