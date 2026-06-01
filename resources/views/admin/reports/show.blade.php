@extends('admin.main')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-6">
        <div>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-outline-secondary mb-3">
                <i class="fas fa-arrow-left me-2"></i>Back to Reports
            </a>
            <h1 class="h2 mb-2">{{ $report->name }}</h1>
            <p class="text-muted mb-0">{{ $report->description }}</p>
        </div>
    </div>

    <div class="row">
        <!-- Filters Sidebar -->
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Filters</h5>
                </div>
                <div class="card-body">
                    <form id="filterForm" method="GET">
                        <!-- Date Range -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Date Range</label>
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
                                <label class="form-label small">From</label>
                                <input type="date" name="custom_from" class="form-control form-control-sm" value="{{ request('custom_from') }}">
                            </div>
                            <div class="mb-2">
                                <label class="form-label small">To</label>
                                <input type="date" name="custom_to" class="form-control form-control-sm" value="{{ request('custom_to') }}">
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

                    <hr class="my-4">

                    <!-- Save/Export Options -->
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Actions</label>
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#saveReportModal">
                                <i class="fas fa-save me-2"></i>Save Report
                            </button>
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-success" id="exportCsv" title="Export as CSV">
                                    <i class="fas fa-file-csv"></i>
                                </button>
                                <button type="button" class="btn btn-outline-success" id="exportExcel" title="Export as Excel">
                                    <i class="fas fa-file-excel"></i>
                                </button>
                                <button type="button" class="btn btn-outline-success" id="exportPdf" title="Export as PDF">
                                    <i class="fas fa-file-pdf"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Saved Reports -->
            @if($savedReports->count())
                <div class="card shadow-sm mt-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Saved Reports</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            @foreach($savedReports as $saved)
                                <a href="{{ route('admin.reports.show', ['report' => $report, 'saved' => $saved->id]) }}" class="list-group-item list-group-item-action py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="small">{{ $saved->name }}</span>
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
            <div class="card shadow-sm">
                <div class="card-body">
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
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Report Details</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">Category</small>
                            <p class="mb-0">{{ $report->category }}</p>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">Type</small>
                            <p class="mb-0">{{ $report->type }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Save Report Modal -->
<div class="modal fade" id="saveReportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Save This Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.reports.preferences.save', $report) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Report Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g., Q4 Sales Report" required>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="is_favorite" value="1" class="form-check-input" id="isFavorite">
                        <label class="form-check-label" for="isFavorite">
                            Mark as favorite
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Report</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show/hide custom date range
    document.getElementById('dateRange').addEventListener('change', function() {
        document.getElementById('customDateRange').style.display = 
            this.value === 'custom' ? 'block' : 'none';
    });

    // Trigger on page load if custom is selected
    if (document.getElementById('dateRange').value === 'custom') {
        document.getElementById('customDateRange').style.display = 'block';
    }

    // Load and render report data
    loadReportData();

    // Export handlers
    document.getElementById('exportCsv').addEventListener('click', function() {
        exportReport('csv');
    });
    document.getElementById('exportExcel').addEventListener('click', function() {
        exportReport('excel');
    });
    document.getElementById('exportPdf').addEventListener('click', function() {
        exportReport('pdf');
    });
});

function loadReportData() {
    const url = new URL(window.location.href);
    const params = new URLSearchParams(url.search);
    
    fetch('{{ route("admin.reports.show", $report) }}?ajax=1&' + params.toString(), {
        headers: {
            'Accept': 'application/json'
        }
    })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                document.getElementById('reportContainer').innerHTML =
                    '<div class="alert alert-danger">Error loading report: ' + (data.error || 'Unknown response') + '</div>';
                return;
            }

            const payload = data.report ? { ...data.report, ...data.data } : data;
            renderReport(payload);
        })
        .catch(error => {
            document.getElementById('reportContainer').innerHTML = 
                '<div class="alert alert-danger">Error loading report: ' + error + '</div>';
        });
}

function renderReport(data) {
    const container = document.getElementById('reportContainer');

    if (!data || !data.type) {
        container.innerHTML = '<div class="alert alert-danger">Unable to load report data.</div>';
        return;
    }
    
    if (data.type === 'line_chart' || data.type === 'bar_chart' || data.type === 'stacked_bar') {
        renderChart(data);
    } else if (data.type === 'pie_chart') {
        renderPieChart(data);
    } else if (data.type === 'table') {
        renderTable(data);
    } else if (data.type === 'metric') {
        renderMetrics(data);
    } else {
        container.innerHTML = '<div class="alert alert-danger">Unsupported report type: ' + data.type + '</div>';
    }
}

function renderChart(data) {
    const container = document.getElementById('reportContainer');
    container.innerHTML = '<canvas id="reportChart"></canvas>';
    
    const ctx = document.getElementById('reportChart').getContext('2d');
    const chartType = data.type === 'stacked_bar' ? 'bar' : (data.type === 'bar_chart' ? 'bar' : 'line');
    
    new Chart(ctx, {
        type: chartType,
        data: data.data,
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                title: {
                    display: true,
                    text: data.title
                }
            },
            scales: {
                x: {
                    stacked: data.type === 'stacked_bar'
                },
                y: {
                    stacked: data.type === 'stacked_bar'
                }
            }
        }
    });
}

function renderPieChart(data) {
    const container = document.getElementById('reportContainer');
    container.innerHTML = '<canvas id="reportChart"></canvas>';
    
    const ctx = document.getElementById('reportChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: data.data,
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: data.title
                }
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

    let html = '<div class="table-responsive"><table class="table table-hover"><thead class="table-light"><tr>';
    
    const headers = Object.keys(data.data[0]);
    headers.forEach(header => {
        html += '<th>' + header + '</th>';
    });
    html += '</tr></thead><tbody>';
    
    data.data.forEach(row => {
        html += '<tr>';
        headers.forEach(header => {
            html += '<td>' + (row[header] || '-') + '</td>';
        });
        html += '</tr>';
    });
    
    html += '</tbody></table></div>';
    container.innerHTML = html;
}

function renderMetrics(data) {
    const container = document.getElementById('reportContainer');
    let html = '<div class="row">';
    
    if (data.metrics) {
        data.metrics.forEach(metric => {
            html += `
                <div class="col-md-4 mb-4">
                    <div class="card border-0 bg-light">
                        <div class="card-body text-center">
                            <small class="text-muted">${metric.label}</small>
                            <h3 class="mb-0 mt-2">${metric.value}</h3>
                        </div>
                    </div>
                </div>
            `;
        });
    }
    
    html += '</div>';
    container.innerHTML = html;
}

function exportReport(format) {
    const url = new URL(window.location.href);
    const params = new URLSearchParams(url.search);
    params.set('export', format);
    
    window.location.href = '{{ route("admin.reports.export", $report) }}?' + params.toString();
}
</script>

<style>
.hover-lift {
    transition: transform 0.2s;
}
.hover-lift:hover {
    transform: translateY(-2px);
}
</style>
@endsection
