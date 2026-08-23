@extends('admin.nightly-reports.layout')

@section('content')
<style>
  .dash-card {
    background: #0b1320 !important;
    border: 1px solid rgba(255, 255, 255, 0.06) !important;
    border-radius: 16px !important;
    padding: 1.25rem;
  }
  .icon-circle {
    width: 28px;
    height: 28px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .pill-btn {
    border-radius: 20px;
    font-weight: 600;
    padding: 0.35rem 1.15rem;
    font-size: 0.75rem;
    text-decoration: none;
    transition: all 0.2s;
  }
  .pill-btn-active {
    background: var(--nr-gold);
    color: #000;
  }
  .pill-btn-outline {
    background: rgba(255,255,255,0.05);
    color: #fff;
    border: 1px solid rgba(255,255,255,0.1);
  }
  .pill-btn-outline:hover {
    background: rgba(255,255,255,0.1);
    color: #fff;
  }
  .table-dark th {
    background: transparent;
    color: #64748b;
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    border-bottom: 1px solid rgba(255,255,255,0.05);
    padding-bottom: 1rem;
  }
  .table-dark td {
    background: transparent;
    border-bottom: 1px solid rgba(255,255,255,0.02);
    padding: 1rem 0.5rem;
    color: #cbd5e1;
  }
  .badge-dark-outline {
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    color: #94a3b8;
    font-weight: 500;
    padding: 0.3em 0.6em;
    border-radius: 12px;
  }
</style>

<div class="container-fluid p-0">

  <!-- Header -->
  <div class="d-flex justify-content-between align-items-end mb-4">
    <div>
      <h6 class="text-uppercase mb-1" style="color: var(--nr-gold); font-size: 0.75rem; letter-spacing: 0.1em; font-weight: 700;">Admin Dashboard</h6>
      <h2 class="text-white mb-0" style="font-family: 'Playfair Display', serif; font-weight: 700; font-size: 2rem;">Performance Overview</h2>
      <div class="text-muted small mt-1" style="font-size: 0.85rem;">{{ \Carbon\Carbon::parse($endDate)->format('D, M j, Y') }}</div>
    </div>
    
    <div class="d-flex gap-2">
      <a href="?date_range=yesterday" class="pill-btn {{ $dateRange == 'yesterday' ? 'pill-btn-active' : 'pill-btn-outline' }}">Yesterday</a>
      <a href="?date_range=last_7_days" class="pill-btn {{ $dateRange == 'last_7_days' ? 'pill-btn-active' : 'pill-btn-outline' }}">This Week</a>
      <a href="?date_range=mtd" class="pill-btn {{ $dateRange == 'mtd' ? 'pill-btn-active' : 'pill-btn-outline' }}">This Month</a>
      <a href="?date_range=last_month" class="pill-btn {{ $dateRange == 'last_month' ? 'pill-btn-active' : 'pill-btn-outline' }}">Last Month</a>
      <a href="?date_range=custom" class="pill-btn {{ $dateRange == 'custom' ? 'pill-btn-active' : 'pill-btn-outline' }}">Custom</a>
    </div>
  </div>

  <!-- KPI Cards -->
  <div class="row g-4 mb-4">
    <!-- Total Net Sales -->
    <div class="col-md-3">
      <div class="card dash-card h-100">
        <div class="d-flex justify-content-between mb-3">
          <h6 class="text-uppercase text-muted mb-0" style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.05em;">Total Net Sales</h6>
          <div class="icon-circle">
            <i class="fas fa-dollar-sign" style="color: var(--nr-gold); font-size: 0.8rem;"></i>
          </div>
        </div>
        <h2 class="text-white mb-1" style="font-family: 'Playfair Display', serif; font-weight: 700;">${{ number_format($totalNetSales, 0) }}</h2>
        <div class="small text-muted" style="font-size: 0.75rem;">{{ $reportsSubmittedCount }} reports</div>
      </div>
    </div>
    
    <!-- Total Guests -->
    <div class="col-md-3">
      <div class="card dash-card h-100">
        <div class="d-flex justify-content-between mb-3">
          <h6 class="text-uppercase text-muted mb-0" style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.05em;">Total Guests</h6>
          <div class="icon-circle">
            <i class="fas fa-user-friends" style="color: var(--nr-gold); font-size: 0.8rem;"></i>
          </div>
        </div>
        <h2 class="text-white mb-1" style="font-family: 'Playfair Display', serif; font-weight: 700;">{{ number_format($totalGuests) }}</h2>
        <div class="small text-muted" style="font-size: 0.75rem;">&nbsp;</div>
      </div>
    </div>
    
    <!-- Reports Submitted -->
    <div class="col-md-3">
      <div class="card dash-card h-100">
        <div class="d-flex justify-content-between mb-3">
          <h6 class="text-uppercase text-muted mb-0" style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.05em;">Reports Submitted</h6>
          <div class="icon-circle">
            <i class="fas fa-calendar-alt" style="color: var(--nr-gold); font-size: 0.8rem;"></i>
          </div>
        </div>
        <h2 class="text-white mb-1" style="font-family: 'Playfair Display', serif; font-weight: 700;">{{ $reportsSubmittedCount }}</h2>
        <div class="small text-muted" style="font-size: 0.75rem;">of {{ $totalActiveVenues }} active venues</div>
      </div>
    </div>
    
    <!-- Missing Reports -->
    <div class="col-md-3">
      <div class="card dash-card h-100">
        <div class="d-flex justify-content-between mb-3">
          <h6 class="text-uppercase text-muted mb-0" style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.05em;">Missing Reports</h6>
          <div class="icon-circle">
            <i class="fas fa-exclamation-triangle" style="color: #94a3b8; font-size: 0.8rem;"></i>
          </div>
        </div>
        <h2 class="text-white mb-1" style="font-family: 'Playfair Display', serif; font-weight: 700;">{{ $missingReportsCount }}</h2>
        <div class="small text-muted" style="font-size: 0.75rem;">venues with no report</div>
      </div>
    </div>
  </div>

  <!-- Middle Section -->
  <div class="row g-4 mb-4">
    <!-- Top Venues -->
    <div class="col-md-6">
      <div class="card dash-card h-100">
        <div class="d-flex justify-content-between mb-4">
          <h6 class="text-white mb-0" style="font-size: 0.9rem; font-weight: 600;"><i class="fas fa-chart-line text-muted me-2"></i> Top Venues by Sales</h6>
          <div class="text-muted small">{{ \Carbon\Carbon::parse($endDate)->format('D, M j, Y') }}</div>
        </div>
        
        @if(count($topVenuesBySales) > 0)
        <div class="d-flex flex-column gap-3">
          @foreach($topVenuesBySales as $venue)
          <div class="d-flex justify-content-between align-items-center">
            <div class="text-white small">{{ $venue['location_name'] }}</div>
            <div class="text-white fw-bold small">${{ number_format($venue['net_sales'], 0) }}</div>
          </div>
          @endforeach
        </div>
        @else
        <div class="d-flex align-items-center justify-content-center h-100 pb-4">
          <div class="text-muted small">No data for this period</div>
        </div>
        @endif
      </div>
    </div>
    
    <!-- Missing Reports List -->
    <div class="col-md-6">
      <div class="card dash-card h-100">
        <div class="d-flex justify-content-between mb-4">
          <h6 class="text-white mb-0" style="font-size: 0.9rem; font-weight: 600;"><i class="fas fa-exclamation-triangle text-warning me-2"></i> Missing Reports</h6>
          <a href="{{ route('admin.nightly-reports.missing.index') }}" class="small text-decoration-none" style="color: var(--nr-gold);">View all &gt;</a>
        </div>
        
        @if(count($missingReportsList) > 0)
        <div class="d-flex flex-column gap-3" style="max-height: 200px; overflow-y: auto;">
          @foreach($missingReportsList as $missing)
          <div class="d-flex justify-content-between align-items-center">
            <div class="text-white small">{{ $missing['location_name'] }}</div>
            <div class="small" style="color: var(--nr-gold);">Last: {{ $missing['last_report_date'] }}</div>
          </div>
          @endforeach
        </div>
        @else
        <div class="d-flex align-items-center justify-content-center h-100 pb-4">
          <div class="text-muted small">All venues submitted reports for this date</div>
        </div>
        @endif
      </div>
    </div>
  </div>

  <!-- Bottom Section: Recent Submissions -->
  <div class="card dash-card mb-4">
    <div class="d-flex justify-content-between mb-4">
      <h6 class="text-white mb-0" style="font-size: 0.9rem; font-weight: 600;"><i class="fas fa-chart-bar text-muted me-2"></i> Recent Submissions</h6>
      <a href="{{ route('admin.nightly-reports.reports.index') }}" class="small text-decoration-none" style="color: var(--nr-gold);">All reports &gt;</a>
    </div>
    
    <div class="table-responsive">
      <table class="table table-borderless table-dark align-middle mb-0">
        <thead>
          <tr>
            <th class="ps-2">Venue</th>
            <th>Date</th>
            <th class="text-end">Net Sales</th>
            <th class="text-end pe-4">Guests</th>
          </tr>
        </thead>
        <tbody>
          @forelse($recentSubmissions as $sub)
          <tr>
            <td class="ps-2">
              <div class="d-flex align-items-center gap-2">
                <span class="text-white" style="font-size: 0.85rem;">{{ Str::limit($sub['location_name'], 30) }}</span>
                <span class="badge badge-dark-outline" style="font-size: 0.6rem;">{{ $sub['location_type'] }}</span>
              </div>
            </td>
            <td>
              <span class="text-muted" style="font-size: 0.85rem;">{{ $sub['business_date'] }}</span>
            </td>
            <td class="text-end">
              @if($sub['type'] == 'incident')
                <span class="text-muted" style="font-size: 0.85rem;">—</span>
              @else
                <span style="color: var(--nr-gold); font-weight: 600; font-size: 0.85rem;">${{ number_format($sub['net_sales'], 0) }}</span>
              @endif
            </td>
            <td class="text-end pe-4">
              @if($sub['type'] == 'incident')
                <span class="text-muted" style="font-size: 0.85rem;">—</span>
              @else
                <span class="text-muted" style="font-size: 0.85rem;">{{ number_format($sub['guests']) }}</span>
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="4" class="text-center py-4 text-muted">No recent submissions.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection
