@extends('admin.nightly-reports.layout')

@section('content')
<div class="container-fluid p-0">
  <div class="card mb-4">
    <div class="card-body">
      <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <div>
          <h4 class="text-white mb-1 fw-bold"><i class="fas fa-store text-info me-2"></i> Boutique Retail Summary</h4>
          <p class="text-muted small mb-0">Daily retail merchandise, arcade/theater ticketing, returns, and POS safe reconciliations.</p>
        </div>
        <div class="d-flex gap-2">
          <a href="{{ route('admin.nightly-reports.boutique-import.index') }}" class="btn btn-sm btn-outline-info">
            <i class="fas fa-file-import me-1"></i> Import Retail Logs
          </a>
          <a href="{{ route('nightly.submit.boutique') }}" target="_blank" class="btn btn-sm btn-gold">
            <i class="fas fa-plus me-1"></i> Submit Boutique Report
          </a>
        </div>
      </div>

      <!-- Filters -->
      <form method="GET" action="{{ route('admin.nightly-reports.boutique.index') }}" class="row g-2">
        <div class="col-md-3">
          <select name="location_id" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="">All Boutique Stores</option>
            @foreach($locations as $loc)
              <option value="{{ $loc->id }}" {{ (string)$selectedLocationId === (string)$loc->id ? 'selected' : '' }}>
                {{ $loc->name }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}" />
        </div>
        <div class="col-md-3">
          <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}" />
        </div>
        <div class="col-md-3">
          <button type="submit" class="btn btn-sm btn-primary w-100"><i class="fas fa-filter me-1"></i> Filter</button>
        </div>
      </form>
    </div>
  </div>

  <!-- KPI Strip -->
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
      <div class="nr-kpi-card">
        <div class="nr-kpi-label">Gross Retail Sales</div>
        <div class="nr-kpi-value text-success">${{ number_format($totalSales, 2) }}</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="nr-kpi-card">
        <div class="nr-kpi-label">Total Store Traffic</div>
        <div class="nr-kpi-value text-white">{{ number_format($totalGuests) }}</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="nr-kpi-card">
        <div class="nr-kpi-label">Total Returns</div>
        <div class="nr-kpi-value text-danger">${{ number_format($totalReturns, 2) }}</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="nr-kpi-card">
        <div class="nr-kpi-label">Total Payouts</div>
        <div class="nr-kpi-value text-warning">${{ number_format($totalPayouts, 2) }}</div>
      </div>
    </div>
  </div>

  <!-- Reports Table -->
  <div class="card">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>Date</th>
            <th>Boutique Store</th>
            <th>Gross Sales</th>
            <th>Traffic / Avg</th>
            <th>Returns</th>
            <th>Said Deposit</th>
            <th>Actual Deposit</th>
            <th>Direction</th>
            <th>Submitter</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($reports as $r)
          <tr>
            <td class="fw-bold text-white">{{ $r->business_date->format('M d, Y') }}</td>
            <td class="fw-bold text-white">{{ $r->location->name ?? 'Store' }}</td>
            <td><span class="text-success fw-bold">${{ number_format($r->gross_daily_sales, 2) }}</span></td>
            <td>
              {{ number_format($r->total_guest_count) }}
              <span class="small text-muted">(${{ number_format($r->guest_average_ticket, 2) }}/ea)</span>
            </td>
            <td><span class="text-danger">${{ number_format($r->total_returns, 2) }}</span></td>
            <td>${{ number_format($r->said_deposit, 2) }}</td>
            <td><span class="fw-bold text-white">${{ number_format($r->actual_deposit, 2) }}</span></td>
            <td>
              @if($r->sales_direction === 'UP')
                <span class="badge bg-success"><i class="fas fa-arrow-up me-1"></i> UP</span>
              @else
                <span class="badge bg-danger"><i class="fas fa-arrow-down me-1"></i> DOWN</span>
              @endif
            </td>
            <td><small class="text-muted">{{ $r->submitter_name }}</small></td>
            <td class="text-end">
              <a href="{{ route('admin.nightly-reports.reports.show', ['type' => 'boutique', 'id' => $r->id]) }}" class="btn btn-sm btn-outline-light" title="View Details">
                <i class="fas fa-eye"></i>
              </a>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="10" class="text-center py-5 text-muted">No boutique shift logs found.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
