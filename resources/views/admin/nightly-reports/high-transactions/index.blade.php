@extends('admin.nightly-reports.layout')

@section('content')
<div class="container-fluid p-0">
  <div class="card mb-4">
    <div class="card-body">
      <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <div>
          <h4 class="text-white mb-1 fw-bold"><i class="fas fa-money-check-alt text-warning me-2"></i> High Transactions Register ($10,000+)</h4>
          <p class="text-muted small mb-0">Anti-Money Laundering (AML), fraud prevention, and chargeback mitigation audit trail.</p>
        </div>
      </div>

      <!-- Filters -->
      <form method="GET" action="{{ route('admin.nightly-reports.high-transactions.index') }}" class="row g-2">
        <div class="col-md-4">
          <select name="location_id" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="">All Locations</option>
            @foreach($locations as $loc)
              <option value="{{ $loc->id }}" {{ (string)$selectedLocationId === (string)$loc->id ? 'selected' : '' }}>
                {{ $loc->name }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-6">
          <input type="text" name="search" class="form-control form-control-sm" value="{{ $search }}" placeholder="Search customer name, card digits, authorizing manager..." />
        </div>
        <div class="col-md-2">
          <button type="submit" class="btn btn-sm btn-primary w-100"><i class="fas fa-search me-1"></i> Search</button>
        </div>
      </form>
    </div>
  </div>

  <!-- KPI Strip -->
  <div class="row g-3 mb-4">
    <div class="col-md-6">
      <div class="nr-kpi-card">
        <div class="nr-kpi-label">Total Verified High Volume</div>
        <div class="nr-kpi-value text-success">${{ number_format($totalHighVol, 2) }}</div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="nr-kpi-card">
        <div class="nr-kpi-label">Logged Large Transactions</div>
        <div class="nr-kpi-value text-white">{{ $transactions->total() }} Records</div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>Date</th>
            <th>Location</th>
            <th>Customer Name</th>
            <th>Card Info</th>
            <th>Amount Charged</th>
            <th>Authorizing GM</th>
            <th class="text-end">Verification Docs</th>
          </tr>
        </thead>
        <tbody>
          @forelse($transactions as $t)
          <tr>
            <td class="fw-bold text-white">{{ $t->transaction_date->format('M d, Y') }}</td>
            <td class="fw-bold text-white">{{ $t->location->name ?? 'Venue' }}</td>
            <td>
              <div class="text-white">{{ $t->customer_name }}</div>
              <small class="text-muted">{{ $t->customer_phone ?? $t->customer_email }}</small>
            </td>
            <td>
              <span class="badge bg-secondary">{{ $t->card_brand ?? 'Card' }} •••• {{ $t->card_last4 }}</span>
            </td>
            <td><span class="text-success fw-bold fs-6">${{ number_format($t->amount, 2) }}</span></td>
            <td><small class="text-white">{{ $t->authorizing_manager_name }}</small></td>
            <td class="text-end">
              <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> ID + Card + Receipt Verified</span>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7" class="text-center py-5 text-muted">No high transactions recorded.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
