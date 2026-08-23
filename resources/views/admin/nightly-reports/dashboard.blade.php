@extends('admin.nightly-reports.layout')

@section('content')
<div class="container-fluid p-0">

  <!-- Header & Pill Buttons -->
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-4 gap-3">
    <div>
      <div class="small fw-bold mb-1 text-uppercase" style="color: var(--nr-gold); letter-spacing: 1px;">Admin Dashboard</div>
      <h3 class="text-white mb-1" style="font-weight: 600;">Performance Overview</h3>
      <div class="text-muted small">{{ \Carbon\Carbon::parse($endDate)->format('D, M j, Y') }}</div>
    </div>
    
    <div class="d-flex flex-wrap gap-2">
      <a href="?date_range=yesterday" class="btn btn-sm {{ $dateRange == 'yesterday' || $dateRange == '' ? 'btn-gold' : 'btn-outline-light' }}">Yesterday</a>
      <a href="?date_range=last_7_days" class="btn btn-sm {{ $dateRange == 'last_7_days' ? 'btn-gold' : 'btn-outline-light' }}">This Week</a>
      <a href="?date_range=mtd" class="btn btn-sm {{ $dateRange == 'mtd' ? 'btn-gold' : 'btn-outline-light' }}">This Month</a>
      <a href="?date_range=ytd" class="btn btn-sm {{ $dateRange == 'ytd' ? 'btn-gold' : 'btn-outline-light' }}">Year to Date</a>
      <a href="?date_range=custom" class="btn btn-sm {{ $dateRange == 'custom' ? 'btn-gold' : 'btn-outline-light' }}">Custom</a>
    </div>
  </div>

  @if($dateRange == 'custom')
  <div class="card mb-4" style="background: transparent; border: 1px solid rgba(255,255,255,0.06);">
    <div class="card-body p-3">
      <form method="GET" action="{{ route('admin.nightly-reports.dashboard') }}" class="row g-3 align-items-center m-0">
        <input type="hidden" name="date_range" value="custom">
        <div class="col-auto">
          <label class="form-label text-muted text-uppercase fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 0.05em;">Start Date</label>
          <input type="date" name="start_date" class="form-control form-control-sm" style="background-color: rgba(255,255,255,0.05); color: #fff; border-color: rgba(255,255,255,0.1);" value="{{ $startDate }}" />
        </div>
        <div class="col-auto">
          <label class="form-label text-muted text-uppercase fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 0.05em;">End Date</label>
          <input type="date" name="end_date" class="form-control form-control-sm" style="background-color: rgba(255,255,255,0.05); color: #fff; border-color: rgba(255,255,255,0.1);" value="{{ $endDate }}" />
        </div>
        <div class="col-auto mt-auto pb-1">
          <button type="submit" class="btn btn-sm btn-gold">
            <i class="fas fa-filter me-1"></i> Apply
          </button>
        </div>
      </form>
    </div>
  </div>
  @endif

  <!-- KPI Cards (4 metrics) -->
  <div class="row g-3 mb-4">
    <!-- Total Net Sales -->
    <div class="col-md-3">
      <div class="nr-kpi-card position-relative overflow-hidden">
        <div class="d-flex justify-content-between">
          <div>
            <div class="nr-kpi-label">Total Net Sales</div>
            <div class="nr-kpi-value text-white">${{ number_format($totalNetSales, 0) }}</div>
            <div class="nr-kpi-sub text-muted mt-2">{{ $reportsSubmittedCount }} reports</div>
          </div>
          <div class="avatar avatar-sm d-flex align-items-center justify-content-center rounded-circle" style="background: rgba(201,168,76,0.1); width: 36px; height: 36px;">
            <i class="fas fa-dollar-sign" style="color: var(--nr-gold);"></i>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Total Guests -->
    <div class="col-md-3">
      <div class="nr-kpi-card position-relative overflow-hidden">
        <div class="d-flex justify-content-between">
          <div>
            <div class="nr-kpi-label">Total Guests</div>
            <div class="nr-kpi-value text-white">{{ number_format($totalGuests) }}</div>
            <div class="nr-kpi-sub text-muted mt-2">&nbsp;</div>
          </div>
          <div class="avatar avatar-sm d-flex align-items-center justify-content-center rounded-circle" style="background: rgba(201,168,76,0.1); width: 36px; height: 36px;">
            <i class="fas fa-user-friends" style="color: var(--nr-gold);"></i>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Reports Submitted -->
    <div class="col-md-3">
      <div class="nr-kpi-card position-relative overflow-hidden">
        <div class="d-flex justify-content-between">
          <div>
            <div class="nr-kpi-label">Reports Submitted</div>
            <div class="nr-kpi-value text-white">{{ $reportsSubmittedCount }}</div>
            <div class="nr-kpi-sub text-muted mt-2">of {{ $totalActiveVenues }} active venues</div>
          </div>
          <div class="avatar avatar-sm d-flex align-items-center justify-content-center rounded-circle" style="background: rgba(201,168,76,0.1); width: 36px; height: 36px;">
            <i class="fas fa-calendar-alt" style="color: var(--nr-gold);"></i>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Missing Reports -->
    <div class="col-md-3">
      <div class="nr-kpi-card position-relative overflow-hidden">
        <div class="d-flex justify-content-between">
          <div>
            <div class="nr-kpi-label">Missing Reports</div>
            <div class="nr-kpi-value text-white">{{ $missingReportsCount }}</div>
            <div class="nr-kpi-sub text-muted mt-2">venues with no report</div>
          </div>
          <div class="avatar avatar-sm d-flex align-items-center justify-content-center rounded-circle" style="background: rgba(255,255,255,0.05); width: 36px; height: 36px;">
            <i class="fas fa-exclamation-triangle text-muted"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Middle Section -->
  <div class="row g-4 mb-4">
    <!-- Top Venues -->
    <div class="col-md-6">
      <div class="card h-100" style="background: transparent; border: 1px solid rgba(255,255,255,0.06); border-radius: 12px;">
        <div class="card-header border-bottom-0 pb-0 d-flex justify-content-between align-items-center pt-3 px-4">
          <h6 class="text-white mb-0" style="font-weight: 600;"><i class="fas fa-chart-line text-muted me-2"></i> Top Venues by Sales</h6>
          <div class="text-muted small">{{ \Carbon\Carbon::parse($endDate)->format('D, M j, Y') }}</div>
        </div>
        
        <div class="card-body mt-3 px-4 pb-4">
          @if(count($topVenuesBySales) > 0)
          <div class="d-flex flex-column gap-3">
            @foreach($topVenuesBySales as $venue)
            <div class="d-flex justify-content-between align-items-center pb-2" style="border-bottom: 1px solid rgba(255,255,255,0.05);">
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
    </div>
    
    <!-- Missing Reports List -->
    <div class="col-md-6">
      <div class="card h-100" style="background: transparent; border: 1px solid rgba(255,255,255,0.06); border-radius: 12px;">
        <div class="card-header border-bottom-0 pb-0 d-flex justify-content-between align-items-center pt-3 px-4">
          <h6 class="text-white mb-0" style="font-weight: 600;"><i class="fas fa-exclamation-triangle text-warning me-2"></i> Missing Reports</h6>
          <a href="{{ route('admin.nightly-reports.missing.index') }}" class="small text-decoration-none" style="color: var(--nr-gold);">View all &gt;</a>
        </div>
        
        <div class="card-body mt-3 px-4 pb-4">
          @if(count($missingReportsList) > 0)
          <div class="d-flex flex-column gap-3" style="max-height: 200px; overflow-y: auto;">
            @foreach($missingReportsList as $missing)
            <div class="d-flex justify-content-between align-items-center pb-2" style="border-bottom: 1px solid rgba(255,255,255,0.05);">
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
  </div>

  <!-- Bottom Section: Recent Submissions -->
  <div class="card mb-4" style="background: transparent; border: 1px solid rgba(255,255,255,0.06); border-radius: 12px;">
    <div class="card-header border-bottom-0 pb-0 d-flex justify-content-between align-items-center pt-3 px-4 mb-3">
      <h6 class="text-white mb-0" style="font-weight: 600;"><i class="fas fa-chart-bar text-muted me-2"></i> Recent Submissions</h6>
      <a href="{{ route('admin.nightly-reports.reports.index') }}" class="small text-decoration-none" style="color: var(--nr-gold);">All reports &gt;</a>
    </div>
    
    <div class="table-responsive">
      <table class="table table-borderless table-hover align-middle mb-0" style="--bs-table-bg: transparent; --bs-table-color: #e2e8f0; --bs-table-hover-bg: rgba(255,255,255,0.02);">
        <thead>
          <tr style="border-bottom: 1px solid rgba(255,255,255,0.06);">
            <th class="ps-4 text-uppercase text-muted" style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.05em;">Venue</th>
            <th class="text-uppercase text-muted" style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.05em;">Date</th>
            <th class="text-end text-uppercase text-muted" style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.05em;">Net Sales</th>
            <th class="text-end pe-4 text-uppercase text-muted" style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.05em;">Guests</th>
          </tr>
        </thead>
        <tbody>
          @forelse($recentSubmissions as $sub)
          <tr>
            <td class="ps-4 py-3">
              <div class="d-flex align-items-center gap-2">
                <span class="text-white" style="font-size: 0.85rem; font-weight: 500;">{{ Str::limit($sub['location_name'], 30) }}</span>
                @if(isset($sub['location_type']) && $sub['location_type'])
                  <span class="badge badge-dark-outline" style="font-size: 0.65rem;">{{ $sub['location_type'] }}</span>
                @endif
              </div>
            </td>
            <td class="py-3">
              <span class="text-muted" style="font-size: 0.85rem;">{{ $sub['business_date'] }}</span>
            </td>
            <td class="text-end py-3">
              @if($sub['type'] == 'incident')
                <span class="text-muted" style="font-size: 0.85rem;">—</span>
              @else
                <span style="color: var(--nr-gold); font-weight: 600; font-size: 0.85rem;">${{ number_format($sub['net_sales'], 0) }}</span>
              @endif
            </td>
            <td class="text-end pe-4 py-3">
              @if($sub['type'] == 'incident')
                <span class="text-muted" style="font-size: 0.85rem;">—</span>
              @else
                <span class="text-white" style="font-size: 0.85rem;">{{ number_format($sub['guests']) }}</span>
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="4" class="text-center py-5 text-muted">No recent submissions.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection
