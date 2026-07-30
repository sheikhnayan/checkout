@extends('admin.main')

@section('content')
<div class="container-fluid px-4 py-6">
    <div class="d-flex align-items-center justify-content-between mb-6">
        <div>
            <h1 class="h2 mb-2" style="color: #fff">Reports & Analytics</h1>
            <p class="text-muted mb-0">View detailed insights and analytics for your business</p>
        </div>
    </div>

    <!-- Category Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="btn-group flex-wrap" role="group" id="categoryFilters">
                <a href="{{ route('admin.reports.index') }}" class="btn btn-sm {{ empty($selectedCategory) ? 'btn-primary' : 'btn-outline-primary' }}">
                    All Reports
                </a>
                @foreach($categories as $cat)
                    <a href="{{ route('admin.reports.category', $cat) }}" class="btn btn-sm {{ (isset($selectedCategory) ? $selectedCategory : request('category')) === $cat ? 'btn-primary' : 'btn-outline-primary' }}">
                        {{ $cat }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Reports Grid -->
    <div class="row">
        @forelse($reports as $report)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm hover-lift">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="card-title mb-1" style="color: #fff">{{ $report->name }}</h5>
                                <span class="badge bg-info">{{ $report->category }}</span>
                            </div>
                            <span class="badge bg-secondary">{{ $report->type }}</span>
                        </div>
                        <p class="card-text text-muted small">{{ $report->description }}</p>
                    </div>
                    <div class="card-footer bg-transparent border-top">
                        <a href="{{ route('admin.reports.show', $report) }}" class="btn btn-sm btn-primary w-100">
                            <i class="fas fa-chart-line me-2"></i>View Report
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    No reports available for this category.
                </div>
            </div>
        @endforelse
    </div>
</div>

<style>
.hover-lift {
    transition: transform 0.2s, box-shadow 0.2s;
}
.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.1) !important;
}

/* Make button group responsive */
#categoryFilters {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

#categoryFilters .btn {
    flex: 1 1 auto;
    min-width: 100px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

@media (max-width: 767px) {
    #categoryFilters {
        gap: 6px;
    }

    #categoryFilters .btn {
        min-width: 80px;
        font-size: 0.75rem;
        padding: 0.35rem 0.5rem !important;
    }
}

@media (max-width: 576px) {
    #categoryFilters {
        gap: 4px;
    }

    #categoryFilters .btn {
        min-width: 70px;
        font-size: 0.7rem;
        padding: 0.3rem 0.4rem !important;
    }
}
</style>
@endsection
