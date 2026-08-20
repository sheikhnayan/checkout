@extends('admin.nightly-reports.layout')

@section('content')
<div class="container-fluid p-0">

  <!-- 1. Daily Quote Carousel / Banner -->
  @if($quote)
  <div class="card mb-4" style="background: linear-gradient(90deg, #142238 0%, #0d1a2e 100%); border-left: 4px solid var(--nr-gold) !important;">
    <div class="card-body py-3 d-flex align-items-center justify-content-between">
      <div class="d-flex align-items-center gap-3">
        <i class="fas fa-quote-left text-warning fa-lg"></i>
        <div>
          <span class="text-white fw-medium font-italic">"{{ $quote->quote_text }}"</span>
          <span class="text-muted ms-2 small">— {{ $quote->author }}</span>
        </div>
      </div>
      <span class="badge badge-gold d-none d-md-inline-block">{{ $quote->category ?? 'Daily Inspiration' }}</span>
    </div>
  </div>
  @endif

  <!-- 2. Controls / Filter Toolbar -->
  <div class="card mb-4">
    <div class="card-body py-3">
      <form method="GET" action="{{ route('admin.nightly-reports.dashboard') }}" class="row g-3 align-items-center">
        <!-- Location Selector -->
        <div class="col-md-4 col-lg-3">
          <label class="form-label small text-muted text-uppercase fw-bold mb-1">Club / Location</label>
          <select name="location_id" class="form-select form-select-sm" onchange="this.form.submit()">
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
          <label class="form-label small text-muted text-uppercase fw-bold mb-1">Period</label>
          <select name="date_range" class="form-select form-select-sm" id="period-selector" onchange="toggleCustomDates(this.value)">
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
            <label class="form-label small text-muted text-uppercase fw-bold mb-1">Start</label>
            <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}" />
          </div>
          <div>
            <label class="form-label small text-muted text-uppercase fw-bold mb-1">End</label>
            <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}" />
          </div>
        </div>

        <div class="col-md-2 col-lg-2 mt-auto pb-1">
          <button type="submit" class="btn btn-sm btn-gold w-100">
            <i class="fas fa-filter me-1"></i> Apply Filter
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- 3. KPI Metric Strip -->
  <div class="row g-3 mb-4">
    <!-- Net Sales -->
    <div class="col-6 col-md-4 col-lg-2">
      <div class="nr-kpi-card">
        <div class="nr-kpi-label">Total Net Sales</div>
        <div class="nr-kpi-value text-white">${{ number_format($totalNetSales, 0) }}</div>
        <div class="nr-kpi-sub {{ $yoyGrowthPct >= 0 ? 'text-success' : 'text-danger' }}">
          <i class="fas fa-arrow-{{ $yoyGrowthPct >= 0 ? 'up' : 'down' }} me-1"></i>
          {{ number_format(abs($yoyGrowthPct), 1) }}% YoY
        </div>
      </div>
    </div>

    <!-- Prior Year -->
    <div class="col-6 col-md-4 col-lg-2">
      <div class="nr-kpi-card">
        <div class="nr-kpi-label">Prior Year Sales</div>
        <div class="nr-kpi-value" style="color: var(--nr-text-muted);">${{ number_format($priorYearNetSales, 0) }}</div>
        <div class="nr-kpi-sub text-muted">Same day last year</div>
      </div>
    </div>

    <!-- Total Guests -->
    <div class="col-6 col-md-4 col-lg-2">
      <div class="nr-kpi-card">
        <div class="nr-kpi-label">Total Guests</div>
        <div class="nr-kpi-value text-white">{{ number_format($totalGuests) }}</div>
        <div class="nr-kpi-sub text-info">Door headcount</div>
      </div>
    </div>

    <!-- Guest Average -->
    <div class="col-6 col-md-4 col-lg-2">
      <div class="nr-kpi-card">
        <div class="nr-kpi-label">Guest Average</div>
        <div class="nr-kpi-value" style="color: var(--nr-gold);">${{ number_format($guestAverage, 2) }}</div>
        <div class="nr-kpi-sub text-muted">Spend per head</div>
      </div>
    </div>

    <!-- Total Payouts -->
    <div class="col-6 col-md-4 col-lg-2">
      <div class="nr-kpi-card">
        <div class="nr-kpi-label">Total Payouts</div>
        <div class="nr-kpi-value text-warning">${{ number_format($totalPayouts, 0) }}</div>
        <div class="nr-kpi-sub text-muted">Taxi, ATM, Other</div>
      </div>
    </div>

    <!-- Break-Even Pace Gauge -->
    <div class="col-6 col-md-4 col-lg-2">
      <div class="nr-kpi-card">
        <div class="nr-kpi-label">Break-Even Pace</div>
        <div class="nr-kpi-value text-success">{{ $breakEvenPacePct }}%</div>
        <div class="nr-kpi-sub text-muted">${{ number_format($mtdSales, 0) }} / ${{ number_format($totalBreakEven, 0) }}</div>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <!-- 4. Daily Financial Matrix Grid Table -->
    <div class="col-lg-8">
      <div class="card h-100">
        <div class="card-header d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center gap-2">
            <i class="fas fa-table text-warning"></i>
            <h5 class="card-title mb-0">Daily Financial Grid ({{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }})</h5>
          </div>
          <span class="badge badge-gold">{{ count($dailyGrid) }} Venues Active</span>
        </div>
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead>
              <tr>
                <th>Venue / Location</th>
                <th>Net Sales</th>
                <th>Nightly Goal</th>
                <th>Variance</th>
                <th>Guests</th>
                <th>Avg Spend</th>
                <th>Status</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($dailyGrid as $row)
              <tr>
                <td>
                  <div class="fw-bold text-white">{{ $row['location_name'] }}</div>
                  <div class="small text-muted">{{ $row['location_type'] }} @if($row['incident_flag']) <span class="badge bg-danger ms-1">Incident</span> @endif</div>
                </td>
                <td>
                  <span class="fw-bold {{ $row['net_sales'] > 0 ? 'text-white' : 'text-muted' }}">
                    ${{ number_format($row['net_sales'], 2) }}
                  </span>
                </td>
                <td>
                  <span class="text-muted">${{ number_format($row['nightly_goal'], 2) }}</span>
                </td>
                <td>
                  @if($row['has_report'])
                    <span class="fw-semibold {{ $row['variance'] >= 0 ? 'text-success' : 'text-danger' }}">
                      {{ $row['variance'] >= 0 ? '+' : '' }}${{ number_format($row['variance'], 0) }}
                      <small>({{ number_format($row['variance_pct'], 1) }}%)</small>
                    </span>
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>
                <td>{{ number_format($row['total_guests']) }}</td>
                <td>${{ number_format($row['guest_average'], 2) }}</td>
                <td>
                  @if(!$row['has_report'])
                    <span class="badge bg-secondary">No Report</span>
                  @elseif($row['met_goal'])
                    <span class="badge-met"><i class="fas fa-check me-1"></i> Met Goal</span>
                  @else
                    <span class="badge-below"><i class="fas fa-arrow-down me-1"></i> Below Goal</span>
                  @endif
                </td>
                <td class="text-end">
                  @if($row['has_report'])
                    <a href="{{ route('admin.nightly-reports.reports.show', ['type' => 'nightly', 'id' => $row['report_id']]) }}" class="btn btn-xs btn-outline-light" title="View Full Report">
                      <i class="fas fa-eye"></i>
                    </a>
                  @else
                    <a href="{{ route('nightly.submit.nightly', ['location' => $row['location_id']]) }}" target="_blank" class="btn btn-xs btn-outline-warning" title="Submit Report">
                      <i class="fas fa-plus"></i> Submit
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

    <!-- 5. Real-Time Submissions Live Stream -->
    <div class="col-lg-4">
      <div class="card h-100">
        <div class="card-header d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center gap-2">
            <i class="fas fa-bolt text-warning"></i>
            <h5 class="card-title mb-0">Recent Submissions Feed</h5>
          </div>
          <span class="badge bg-success">Live</span>
        </div>
        <div class="card-body p-0">
          <div class="list-group list-group-flush">
            @forelse($recentSubmissions as $sub)
            <a href="{{ $sub['url'] }}" class="list-group-item list-group-item-action d-flex align-items-start gap-3 p-3" style="background: transparent; border-color: var(--nr-border);">
              <div class="avatar avatar-sm rounded-circle p-2 d-flex align-items-center justify-content-center" style="background: {{ $sub['type'] === 'incident' ? 'rgba(244,63,94,0.2)' : 'rgba(201,168,76,0.2)' }};">
                <i class="fas {{ $sub['type'] === 'incident' ? 'fa-shield-alt text-danger' : 'fa-moon text-warning' }} fa-sm"></i>
              </div>
              <div class="flex-grow-1">
                <div class="d-flex align-items-center justify-content-between">
                  <div class="fw-bold text-white small">{{ $sub['location_name'] }}</div>
                  <small class="text-muted">{{ $sub['created_at']->diffForHumans() }}</small>
                </div>
                <div class="small text-muted mb-1">{{ $sub['summary'] }}</div>
                <div class="small text-muted"><i class="fas fa-user-circle me-1"></i> {{ $sub['submitter_name'] }}</div>
              </div>
            </a>
            @empty
            <div class="text-center py-5 text-muted">
              <i class="fas fa-inbox fa-2x mb-2 opacity-50"></i>
              <div>No recent shift reports logged yet.</div>
            </div>
            @endforelse
          </div>
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
