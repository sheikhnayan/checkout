@extends('admin.nightly-reports.layout')

@section('content')
<div class="container-fluid p-0">
  <div class="card mb-4">
    <div class="card-body">
      <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <div>
          <h4 class="text-white mb-1 fw-bold"><i class="fas fa-vault text-success me-2"></i> Cash On Hand (COH) Vault Audits</h4>
          <p class="text-muted small mb-0">Daily cash vault reconciliation across drop safes, main safes, cash registers, and ATM cassettes.</p>
        </div>
        <div class="d-flex gap-2">
          <a href="{{ route('nightly.submit.coh') }}" target="_blank" class="btn btn-sm btn-gold">
            <i class="fas fa-plus me-1"></i> Submit COH Audit
          </a>
        </div>
      </div>

      <!-- Filters -->
      <form method="GET" action="{{ route('admin.nightly-reports.coh.index') }}" class="row g-2">
        <div class="col-md-3">
          <select name="location_id" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="">All Locations</option>
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
    <div class="col-md-6">
      <div class="nr-kpi-card">
        <div class="nr-kpi-label">Total VU Cash On Hand</div>
        <div class="nr-kpi-value text-success">${{ number_format($totalCashOnHand, 2) }}</div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="nr-kpi-card">
        <div class="nr-kpi-label">Total Shift Paid Outs</div>
        <div class="nr-kpi-value text-warning">${{ number_format($totalPaidOuts, 2) }}</div>
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
            <th>Location</th>
            <th>Drop Safe</th>
            <th>Main Safe</th>
            <th>Registers 1–4</th>
            <th>ATMs 1–4</th>
            <th>Paid Outs</th>
            <th>VU Cash On Hand</th>
            <th>Submitter</th>
          </tr>
        </thead>
        <tbody>
          @forelse($reports as $r)
          <tr>
            <td class="fw-bold text-white">{{ $r->business_date->format('M d, Y') }}</td>
            <td class="fw-bold text-white">{{ $r->location->name ?? 'Venue' }}</td>
            <td>${{ number_format($r->drop_safe, 2) }}</td>
            <td>${{ number_format($r->main_safe, 2) }}</td>
            <td>${{ number_format($r->register_1 + $r->register_2 + $r->register_3 + $r->register_4, 2) }}</td>
            <td>${{ number_format($r->atm_1 + $r->atm_2 + $r->atm_3 + $r->atm_4, 2) }}</td>
            <td><span class="text-danger">${{ number_format($r->paid_outs_total, 2) }}</span></td>
            <td><span class="text-success fw-bold">${{ number_format($r->vu_cash_on_hand, 2) }}</span></td>
            <td><small class="text-muted">{{ $r->submitter_name }}</small></td>
          </tr>
          @empty
          <tr>
            <td colspan="9" class="text-center py-5 text-muted">No COH vault audit records logged.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
