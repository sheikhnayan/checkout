@extends('admin.main')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-3">
        <div>
            <h1 class="h2 mb-1 text-white fw-bold"><i class="fas fa-chart-pie me-2 text-primary"></i>Reports & Analytics</h1>
            <p class="text-muted mb-0">Shopify-powered insights, traffic analysis, and commercial performance reports</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.reports.automation.schedules') }}" class="btn btn-outline-light btn-sm">
                <i class="fas fa-clock me-2"></i>Automation Schedules
            </a>
        </div>
    </div>

    <!-- Switch to Analytics V2 Banner -->
    <div class="alert border-0 shadow-sm mb-4 text-white d-flex flex-column flex-md-row align-items-md-center justify-content-between p-3" style="background: linear-gradient(135deg, #171d2f 0%, #0b0e1a 100%); border-left: 4px solid var(--admin-section-start, #41d1ff) !important; border-radius: 12px;">
        <div class="d-flex align-items-center gap-3 mb-2 mb-md-0">
            <div class="rounded-circle p-2 bg-primary bg-opacity-25 text-info">
                <i class="fas fa-rocket fa-lg"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-0 text-white">Experience Next-Gen Analytics V2 — VIP Executive Intelligence Hub</h6>
                <span class="text-muted small">Real-time revenue waterfall, club heatmaps, affiliate matrix, and geospatial IP conversion analytics.</span>
            </div>
        </div>
        <a href="{{ route('admin.analytics.v2.index') }}" class="btn btn-primary btn-sm px-4 fw-bold text-nowrap" style="background: linear-gradient(135deg, #41d1ff 0%, #0094ff 100%); border: none; border-radius: 8px;">
            <i class="fas fa-bolt me-1"></i> Switch to Analytics V2 (New)
        </a>
    </div>

    <!-- Search & Category Header (Shopify Style) -->
    <div class="card bg-dark border border-secondary border-opacity-25 shadow-sm mb-4">
        <div class="card-body p-3">
            <div class="row align-items-center g-3">
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-dark-subtle text-muted border-secondary"><i class="fas fa-search"></i></span>
                        <input type="text" id="reportSearchInput" class="form-control bg-dark-subtle text-white border-secondary" placeholder="Search reports (e.g. Sales, Sessions, Packages)..." onkeyup="filterReportCards()">
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="d-flex flex-wrap gap-2 justify-content-md-end" id="categoryFilters">
                        <a href="{{ route('admin.reports.index') }}" class="btn btn-sm {{ empty($selectedCategory) ? 'btn-primary' : 'btn-outline-secondary text-white' }}">
                            All Reports
                        </a>
                        @foreach($categories as $cat)
                            <a href="{{ route('admin.reports.category', $cat) }}" class="btn btn-sm {{ (isset($selectedCategory) ? $selectedCategory : request('category')) === $cat ? 'btn-primary' : 'btn-outline-secondary text-white' }}">
                                {{ $cat }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reports Grid -->
    <div class="row g-4" id="reportsGrid">
        @forelse($reports as $report)
            <div class="col-md-6 col-lg-4 report-card-item" data-name="{{ strtolower($report->name) }}" data-category="{{ strtolower($report->category) }}">
                <div class="card h-100 bg-dark border border-secondary border-opacity-25 shadow-sm hover-lift rounded-3">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <span class="badge bg-primary bg-opacity-25 text-primary border border-primary border-opacity-25 mb-2">{{ $report->category }}</span>
                                <h5 class="card-title text-white fw-bold mb-1">{{ $report->name }}</h5>
                            </div>
                            <span class="badge bg-secondary bg-opacity-50 text-light">{{ strtoupper($report->type) }}</span>
                        </div>
                        <p class="card-text text-muted small mb-0">{{ $report->description }}</p>
                    </div>
                    <div class="card-footer bg-dark-subtle border-top border-secondary border-opacity-25 p-3">
                        <a href="{{ route('admin.reports.show', $report) }}" class="btn btn-sm btn-outline-primary w-100 fw-bold">
                            <i class="fas fa-chart-line me-2"></i>Open Report
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-dark border border-secondary text-white">
                    <i class="fas fa-info-circle me-2 text-info"></i>
                    No reports available for this category.
                </div>
            </div>
        @endforelse
    </div>
</div>

<script>
function filterReportCards() {
    const query = document.getElementById('reportSearchInput').value.toLowerCase();
    const items = document.querySelectorAll('.report-card-item');

    items.forEach(item => {
        const name = item.getAttribute('data-name');
        const cat = item.getAttribute('data-category');
        if (name.includes(query) || cat.includes(query)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}
</script>

<style>
.hover-lift {
    transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
}
.hover-lift:hover {
    transform: translateY(-4px);
    border-color: #0ea5e9 !important;
    box-shadow: 0 10px 20px rgba(0,0,0,0.3) !important;
}
</style>
@endsection
