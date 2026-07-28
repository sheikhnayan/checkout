@extends('admin.main')

@section('content')
<div class="container-fluid px-4 py-4" style="background-color: var(--admin-bg, #0b0e1a); min-height: 100vh; color: var(--admin-text, #e8eaf6);">

    <!-- HEADER & ACTION BAR -->
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between pb-3 mb-4 border-bottom border-secondary border-opacity-25">
        <div>
            <h1 class="h3 fw-bold text-white mb-1"><i class="fas fa-file-invoice text-primary me-2"></i>Reports</h1>
            <p class="text-muted small mb-0">Overview of all analytics, sales performance, visitor sessions, and venue reports.</p>
        </div>
        <div class="d-flex align-items-center gap-2 mt-3 mt-sm-0">
            <a href="{{ route('admin.reports.automation.schedules') }}" class="btn btn-outline-secondary btn-sm text-white px-3" style="border-radius: 8px;">
                <i class="fas fa-clock me-1 text-info"></i> Automation Schedules
            </a>
            <a href="{{ route('admin.analytics.v2.index') }}" class="btn btn-primary btn-sm px-3" style="background: linear-gradient(135deg, #41d1ff 0%, #0094ff 100%); border: none; border-radius: 8px; font-weight: 600;">
                <i class="fas fa-bolt me-1"></i> Executive Hub (V2)
            </a>
        </div>
    </div>

    <!-- MAIN SHOPIFY-STYLE REPORTS LIST CONTAINER -->
    <div class="card border-0 shadow-sm" style="background-color: var(--admin-surface, #121726); border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.08) !important;">
        
        <!-- SEARCH & FILTER CONTROLS BAR -->
        <div class="card-header bg-transparent p-3 border-bottom border-secondary border-opacity-25">
            <div class="row align-items-center g-2">
                
                <!-- Search Input -->
                <div class="col-12 col-md-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text border-secondary bg-dark text-muted"><i class="fas fa-search"></i></span>
                        <input type="text" id="reportSearchInput" class="form-control border-secondary bg-dark text-white shadow-none" placeholder="Search reports (e.g., Total sales over time, Customers by location)..." onkeyup="filterReportsTable()">
                    </div>
                </div>

                <!-- Filters: Created By & Category Dropdowns -->
                <div class="col-12 col-md-7 d-flex flex-wrap align-items-center justify-content-md-end gap-2">
                    
                    <!-- Category Dropdown Filter -->
                    <div class="dropdown">
                        <button class="btn btn-dark btn-sm dropdown-toggle text-white border border-secondary border-opacity-25 px-3" type="button" id="categoryFilterBtn" data-bs-toggle="dropdown" aria-expanded="false" style="background-color: var(--admin-surface-2, #171d2f); border-radius: 8px;">
                            Category: <span id="selectedCategoryLabel" class="fw-bold text-info">{{ $selectedCategory ?? 'All' }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow" aria-labelledby="categoryFilterBtn">
                            <li><a class="dropdown-item category-filter-item active" href="#" data-cat="">All Categories</a></li>
                            <li><hr class="dropdown-divider"></li>
                            @foreach($categories as $cat)
                            <li><a class="dropdown-item category-filter-item" href="#" data-cat="{{ strtolower($cat) }}">{{ $cat }}</a></li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Created By Filter -->
                    <div class="dropdown">
                        <button class="btn btn-dark btn-sm dropdown-toggle text-white border border-secondary border-opacity-25 px-3" type="button" id="createdByFilterBtn" data-bs-toggle="dropdown" aria-expanded="false" style="background-color: var(--admin-surface-2, #171d2f); border-radius: 8px;">
                            Created by: <span class="fw-bold text-info">System</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow">
                            <li><a class="dropdown-item active" href="#">System Reports</a></li>
                            <li><a class="dropdown-item" href="#">Custom Reports</a></li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>

        <!-- SHOPIFY REPORTS TABLE LIST -->
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle mb-0" id="shopifyReportsTable">
                <thead>
                    <tr style="border-bottom: 2px solid rgba(255,255,255,0.1); background-color: var(--admin-surface-2, #171d2f);">
                        <th class="ps-4 py-3 text-uppercase text-muted small font-monospace fw-bold" style="letter-spacing: 0.5px;">Name</th>
                        <th class="py-3 text-uppercase text-muted small font-monospace fw-bold" style="letter-spacing: 0.5px;">Category</th>
                        <th class="py-3 text-uppercase text-muted small font-monospace fw-bold" style="letter-spacing: 0.5px;">Description</th>
                        <th class="pe-4 py-3 text-end text-uppercase text-muted small font-monospace fw-bold" style="letter-spacing: 0.5px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                    <tr class="report-row-item" data-name="{{ strtolower($report->name) }}" data-category="{{ strtolower($report->category) }}" style="cursor: pointer;" onclick="window.location='{{ route('admin.reports.show', $report) }}'">
                        <td class="ps-4 py-3">
                            <a href="{{ route('admin.reports.show', $report) }}" class="text-white text-decoration-none fw-bold text-hover-primary d-inline-flex align-items-center gap-2" onclick="event.stopPropagation();">
                                <i class="fas fa-chart-line text-info opacity-75"></i>
                                <span>{{ $report->name }}</span>
                            </a>
                        </td>
                        <td class="py-3">
                            <span class="badge bg-secondary bg-opacity-25 text-info px-2 py-1 border border-secondary border-opacity-25 rounded-pill small">
                                {{ $report->category }}
                            </span>
                        </td>
                        <td class="py-3 text-muted small">
                            {{ $report->description }}
                        </td>
                        <td class="pe-4 py-3 text-end" onclick="event.stopPropagation();">
                            <a href="{{ route('admin.reports.show', $report) }}" class="btn btn-sm btn-outline-info rounded-pill px-3" style="font-size: 0.78rem;">
                                View report <i class="fas fa-chevron-right ms-1"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">
                            <i class="fas fa-folder-open fa-2x mb-2 text-secondary"></i>
                            <p class="mb-0">No reports found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function filterReportsTable() {
    const searchVal = document.getElementById('reportSearchInput').value.toLowerCase();
    const rows = document.querySelectorAll('.report-row-item');
    
    rows.forEach(row => {
        const name = row.getAttribute('data-name');
        const cat = row.getAttribute('data-category');
        const activeCategory = document.getElementById('selectedCategoryLabel').innerText.trim().toLowerCase();

        const matchesSearch = name.includes(searchVal) || cat.includes(searchVal);
        const matchesCategory = activeCategory === 'all' || cat === activeCategory;

        if (matchesSearch && matchesCategory) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.category-filter-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelectorAll('.category-filter-item').forEach(el => el.classList.remove('active'));
            this.classList.add('active');

            const catName = this.innerText;
            document.getElementById('selectedCategoryLabel').innerText = catName;
            filterReportsTable();
        });
    });
});
</script>
@endsection
