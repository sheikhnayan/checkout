@extends('admin.nightly-reports.layout')

@section('content')
<div class="container-fluid p-0">

  <!-- Header -->
  <div class="d-flex justify-content-between align-items-end mb-4">
    <div>
      <div class="small fw-bold mb-1" style="color: var(--nr-gold); letter-spacing: 1px;">ADMIN DASHBOARD</div>
      <h3 class="text-white mb-1 fw-bold">Performance Overview</h3>
      <div class="text-muted small">{{ \Carbon\Carbon::parse($endDate)->format('D, M j, Y') }}</div>
    </div>
  </div>

  <!-- Controls / Filter Toolbar -->
  <div class="card dash-card mb-4">
    <div class="card-body p-3">
      <form method="GET" action="{{ route('admin.nightly-reports.dashboard') }}" class="row g-3 align-items-center">
        <!-- Location Selector -->
        <div class="col-md-4 col-lg-3">
          <label class="form-label text-muted text-uppercase fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 0.05em;">Club / Location</label>
          <select name="location_id" class="form-select form-select-sm" style="background-color: rgba(255,255,255,0.05); color: #fff; border-color: rgba(255,255,255,0.1);" onchange="this.form.submit()">
            <option value="">All Accessible Locations ({{ count($locations) }})</option>
            @foreach($locations as $loc)
              <option value="{{ $loc->id }}" {{ (string)$selectedLocationId === (string)$loc->id ? 'selected' : '' }}>
                {{ $loc->name }}
              </option>
            @endforeach
          </select>
        </div>

        <!-- Date Range Presets -->
        <div class="col-md-4 col-lg-3">
          <label class="form-label text-muted text-uppercase fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 0.05em;">Period</label>
          <select name="date_range" class="form-select form-select-sm" id="period-selector" style="background-color: rgba(255,255,255,0.05); color: #fff; border-color: rgba(255,255,255,0.1);" onchange="toggleCustomDates(this.value)">
            <option value="yesterday" {{ $dateRange === 'yesterday' ? 'selected' : '' }}>Yesterday</option>
            <option value="today" {{ $dateRange === 'today' ? 'selected' : '' }}>Today</option>
            <option value="last_7_days" {{ $dateRange === 'last_7_days' ? 'selected' : '' }}>Last 7 Days</option>
            <option value="mtd" {{ $dateRange === 'mtd' ? 'selected' : '' }}>Month to Date (MTD)</option>
            <option value="ytd" {{ $dateRange === 'ytd' ? 'selected' : '' }}>Year to Date (YTD)</option>
            <option value="custom" {{ $dateRange === 'custom' ? 'selected' : '' }}>Custom Date Range</option>
          </select>
        </div>

        <!-- Custom Dates -->
        <div class="col-md-4 col-lg-4 d-flex gap-2 custom-dates {{ $dateRange === 'custom' ? '' : 'd-none' }}">
          <div>
            <label class="form-label text-muted text-uppercase fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 0.05em;">Start</label>
            <input type="date" name="start_date" class="form-control form-control-sm" style="background-color: rgba(255,255,255,0.05); color: #fff; border-color: rgba(255,255,255,0.1);" value="{{ $startDate }}" />
          </div>
          <div>
            <label class="form-label text-muted text-uppercase fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 0.05em;">End</label>
            <input type="date" name="end_date" class="form-control form-control-sm" style="background-color: rgba(255,255,255,0.05); color: #fff; border-color: rgba(255,255,255,0.1);" value="{{ $endDate }}" />
          </div>
        </div>

        <div class="col-md-2 col-lg-2 mt-auto pb-1">
          <button type="submit" class="btn btn-sm w-100" style="background-color: var(--nr-gold); color: #000; font-weight: 600;">
            <i class="fas fa-filter me-1"></i> Apply Filter
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- KPI Cards -->
  <div class="row g-3 mb-4">
    <!-- Total Net Sales -->
    <div class="col-6 col-md-4 col-lg-2">
      <div class="card dash-card h-100">
        <div class="d-flex justify-content-between mb-3">
          <h6 class="text-uppercase text-muted mb-0" style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.05em;">Total Net Sales</h6>
          <div class="icon-circle">
            <i class="fas fa-dollar-sign" style="color: var(--nr-gold); font-size: 0.8rem;"></i>
          </div>
        </div>
        <h3 class="text-white mb-1" style="font-family: 'Playfair Display', serif; font-weight: 700;">${{ number_format($totalNetSales, 0) }}</h3>
        <div class="small {{ $yoyGrowthPct >= 0 ? 'text-success' : 'text-danger' }}" style="font-size: 0.75rem;">
          <i class="fas fa-arrow-{{ $yoyGrowthPct >= 0 ? 'up' : 'down' }} me-1"></i>
          {{ number_format(abs($yoyGrowthPct), 1) }}% YoY
        </div>
      </div>
    </div>
    
    <!-- Prior Year Sales -->
    <div class="col-6 col-md-4 col-lg-2">
      <div class="card dash-card h-100">
        <div class="d-flex justify-content-between mb-3">
          <h6 class="text-uppercase text-muted mb-0" style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.05em;">Prior Year Sales</h6>
          <div class="icon-circle">
            <i class="fas fa-history" style="color: var(--nr-gold); font-size: 0.8rem;"></i>
          </div>
        </div>
        <h3 class="text-white mb-1" style="font-family: 'Playfair Display', serif; font-weight: 700;">${{ number_format($priorYearNetSales, 0) }}</h3>
        <div class="small text-muted" style="font-size: 0.75rem;">Same day last year</div>
      </div>
    </div>

    <!-- Total Guests -->
    <div class="col-6 col-md-4 col-lg-2">
      <div class="card dash-card h-100">
        <div class="d-flex justify-content-between mb-3">
          <h6 class="text-uppercase text-muted mb-0" style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.05em;">Total Guests</h6>
          <div class="icon-circle">
            <i class="fas fa-user-friends" style="color: var(--nr-gold); font-size: 0.8rem;"></i>
          </div>
        </div>
        <h3 class="text-white mb-1" style="font-family: 'Playfair Display', serif; font-weight: 700;">{{ number_format($totalGuests) }}</h3>
        <div class="small text-muted" style="font-size: 0.75rem;">Door headcount</div>
      </div>
    </div>

    <!-- Guest Average -->
    <div class="col-6 col-md-4 col-lg-2">
      <div class="card dash-card h-100">
        <div class="d-flex justify-content-between mb-3">
          <h6 class="text-uppercase text-muted mb-0" style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.05em;">Guest Average</h6>
          <div class="icon-circle">
            <i class="fas fa-receipt" style="color: var(--nr-gold); font-size: 0.8rem;"></i>
          </div>
        </div>
        <h3 class="text-white mb-1" style="font-family: 'Playfair Display', serif; font-weight: 700;">${{ number_format($guestAverage, 2) }}</h3>
        <div class="small text-muted" style="font-size: 0.75rem;">Spend per head</div>
      </div>
    </div>

    <!-- Total Payouts -->
    <div class="col-6 col-md-4 col-lg-2">
      <div class="card dash-card h-100">
        <div class="d-flex justify-content-between mb-3">
          <h6 class="text-uppercase text-muted mb-0" style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.05em;">Total Payouts</h6>
          <div class="icon-circle">
            <i class="fas fa-money-bill-wave" style="color: var(--nr-gold); font-size: 0.8rem;"></i>
          </div>
        </div>
        <h3 class="text-white mb-1" style="font-family: 'Playfair Display', serif; font-weight: 700;">${{ number_format($totalPayouts, 0) }}</h3>
        <div class="small text-muted" style="font-size: 0.75rem;">Taxi, ATM, Other</div>
      </div>
    </div>

    <!-- Break-Even Pace -->
    <div class="col-6 col-md-4 col-lg-2">
      <div class="card dash-card h-100">
        <div class="d-flex justify-content-between mb-3">
          <h6 class="text-uppercase text-muted mb-0" style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.05em;">Break-Even Pace</h6>
          <div class="icon-circle">
            <i class="fas fa-tachometer-alt" style="color: var(--nr-gold); font-size: 0.8rem;"></i>
          </div>
        </div>
        <h3 class="text-success mb-1" style="font-family: 'Playfair Display', serif; font-weight: 700;">{{ $breakEvenPacePct }}%</h3>
        <div class="small text-muted" style="font-size: 0.75rem;">${{ number_format($mtdSales, 0) }} / ${{ number_format($totalBreakEven, 0) }}</div>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <!-- Daily Financial Matrix Grid Table -->
    <div class="col-lg-8">
      <div class="card dash-card h-100">
        <div class="d-flex justify-content-between mb-4 px-3 pt-3">
          <h6 class="text-white mb-0" style="font-size: 0.9rem; font-weight: 600;"><i class="fas fa-table text-muted me-2"></i> Daily Financial Grid ({{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }})</h6>
          <span class="badge badge-dark-outline" style="color: var(--nr-gold);">{{ count($dailyGrid) }} Venues Active</span>
        </div>
        
        <div class="table-responsive">
          <table class="table table-borderless table-dark align-middle mb-0">
            <thead>
              <tr>
                <th class="ps-3 text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.05em; color: #94a3b8;">Venue / Location</th>
                <th class="text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.05em; color: #94a3b8;">Net Sales</th>
                <th class="text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.05em; color: #94a3b8;">Nightly Goal</th>
                <th class="text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.05em; color: #94a3b8;">Variance</th>
                <th class="text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.05em; color: #94a3b8;">Guests</th>
                <th class="text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.05em; color: #94a3b8;">Avg Spend</th>
                <th class="text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.05em; color: #94a3b8;">Status</th>
                <th class="text-end pe-3 text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.05em; color: #94a3b8;">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($dailyGrid as $row)
              <tr>
                <td class="ps-3">
                  <div class="fw-bold text-white" style="font-size: 0.85rem;">{{ $row['location_name'] }}</div>
                  <div class="small text-muted" style="font-size: 0.7rem;">{{ $row['location_type'] }} @if($row['incident_flag']) <span class="badge bg-danger ms-1">Incident</span> @endif</div>
                </td>
                <td>
                  <span class="fw-bold {{ $row['net_sales'] > 0 ? 'text-white' : 'text-muted' }}" style="font-size: 0.85rem;">
                    ${{ number_format($row['net_sales'], 2) }}
                  </span>
                </td>
                <td>
                  <span class="text-muted" style="font-size: 0.85rem;">${{ number_format($row['nightly_goal'], 2) }}</span>
                </td>
                <td>
                  @if($row['has_report'])
                    <span class="fw-semibold {{ $row['variance'] >= 0 ? 'text-success' : 'text-danger' }}" style="font-size: 0.85rem;">
                      {{ $row['variance'] >= 0 ? '+' : '' }}${{ number_format($row['variance'], 0) }}
                      <small>({{ number_format($row['variance_pct'], 1) }}%)</small>
                    </span>
                  @else
                    <span class="text-muted" style="font-size: 0.85rem;">—</span>
                  @endif
                </td>
                <td style="font-size: 0.85rem;">{{ number_format($row['total_guests']) }}</td>
                <td style="font-size: 0.85rem;">${{ number_format($row['guest_average'], 2) }}</td>
                <td>
                  @if(!$row['has_report'])
                    <span class="badge bg-secondary" style="font-size: 0.7rem;">No Report</span>
                  @elseif($row['met_goal'])
                    <span class="badge" style="background-color: rgba(25, 135, 84, 0.1); color: #28a745; font-size: 0.7rem;"><i class="fas fa-check me-1"></i> Met Goal</span>
                  @else
                    <span class="badge" style="background-color: rgba(220, 53, 69, 0.1); color: #dc3545; font-size: 0.7rem;"><i class="fas fa-arrow-down me-1"></i> Below Goal</span>
                  @endif
                </td>
                <td class="text-end pe-3">
                  @if($row['has_report'])
                    <a href="{{ route('admin.nightly-reports.reports.show', ['type' => 'nightly', 'id' => $row['report_id']]) }}" class="btn btn-sm btn-outline-light py-1 px-2" title="View Full Report">
                      <i class="fas fa-eye"></i>
                    </a>
                  @else
                    <a href="{{ route('nightly.submit.nightly', ['location' => $row['location_id']]) }}" target="_blank" class="btn btn-sm btn-outline-warning py-1 px-2" title="Submit Report">
                      <i class="fas fa-plus"></i>
                    </a>
                  @endif
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="8" class="text-center py-4 text-muted">No venue records match your filter criteria.</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Real-Time Submissions Live Stream -->
    <div class="col-lg-4">
      <div class="card dash-card h-100">
        <div class="d-flex justify-content-between mb-4 px-3 pt-3">
          <h6 class="text-white mb-0" style="font-size: 0.9rem; font-weight: 600;"><i class="fas fa-bolt text-warning me-2"></i> Recent Submissions Feed</h6>
          <span class="badge" style="background-color: rgba(25, 135, 84, 0.2); color: #28a745;">Live</span>
        </div>
        
        <div class="px-3 pb-3 d-flex flex-column gap-3">
          @forelse($recentSubmissions as $sub)
          <a href="{{ $sub['url'] }}" class="text-decoration-none d-flex align-items-start gap-3 p-2 rounded" style="border: 1px solid rgba(255,255,255,0.05); transition: background-color 0.2s;">
            <div class="icon-circle" style="background-color: {{ $sub['type'] === 'incident' ? 'rgba(220, 53, 69, 0.1)' : 'rgba(255, 204, 0, 0.1)' }}; width: 32px; height: 32px;">
              <i class="fas {{ $sub['type'] === 'incident' ? 'fa-shield-alt text-danger' : 'fa-moon' }}" style="color: {{ $sub['type'] === 'incident' ? '' : 'var(--nr-gold)' }}; font-size: 0.8rem;"></i>
            </div>
            <div class="flex-grow-1">
              <div class="d-flex align-items-center justify-content-between">
                <div class="fw-bold text-white small" style="font-size: 0.85rem;">{{ $sub['location_name'] }}</div>
                <small class="text-muted" style="font-size: 0.7rem;">{{ $sub['created_at']->diffForHumans() }}</small>
              </div>
              <div class="text-muted mb-1" style="font-size: 0.75rem;">{{ $sub['summary'] }}</div>
              <div class="text-muted" style="font-size: 0.7rem;"><i class="fas fa-user-circle me-1"></i> {{ $sub['submitter_name'] }}</div>
            </div>
          </a>
          @empty
          <div class="text-center py-5 text-muted">
            <i class="fas fa-inbox fa-2x mb-2 opacity-50"></i>
            <div class="small">No recent shift reports logged yet.</div>
          </div>
          @endforelse
        </div>
      </div>
    </div>
  </div>

</div>

@push('scripts')
<script>
  function toggleCustomDates(val) {
    if (val === 'custom') {
      $('.custom-dates').removeClass('d-none');
    } else {
      $('.custom-dates').addClass('d-none');
      $('#period-selector').closest('form').submit();
    }
  }
</script>
@endpush
@endsection
