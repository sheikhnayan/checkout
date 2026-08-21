@extends('admin.nightly-reports.layout')

@section('content')
<div class="container-fluid p-0">

  <!-- Breadcrumb & Actions -->
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
      <a href="{{ route('admin.nightly-reports.reports.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
        <i class="fas fa-arrow-left me-1"></i> Back to Reports List
      </a>
      <h4 class="text-white fw-bold mb-0">
        {{ $report->location->name ?? 'Venue Report' }} — {{ $report->business_date->format('l, F j, Y') }}
      </h4>
      <p class="text-muted small mb-0">Submitted by {{ $report->submitter_name }} ({{ $report->submitter_email }}) on {{ $report->created_at->format('M d, Y h:i A') }}</p>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('admin.nightly-reports.reports.email-preview', ['type' => 'nightly', 'id' => $report->id]) }}" class="btn btn-sm btn-outline-info">
        <i class="fas fa-envelope me-1"></i> Email Preview
      </a>
      <a href="{{ route('admin.nightly-reports.reports.edit', ['type' => 'nightly', 'id' => $report->id]) }}" class="btn btn-sm btn-gold">
        <i class="fas fa-edit me-1"></i> Edit Report
      </a>
    </div>
  </div>

  <!-- Metric Strip -->
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
      <div class="nr-kpi-card">
        <div class="nr-kpi-label">Net Sales</div>
        <div class="nr-kpi-value text-success">${{ number_format($report->net_sales, 2) }}</div>
        <div class="nr-kpi-sub text-muted">Goal: ${{ number_format($report->nightly_goal, 2) }}</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="nr-kpi-card">
        <div class="nr-kpi-label">Total Guests</div>
        <div class="nr-kpi-value text-white">{{ number_format($report->total_guests) }}</div>
        <div class="nr-kpi-sub text-info">Paid: {{ number_format($report->paid_guests ?? 0) }}</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="nr-kpi-card">
        <div class="nr-kpi-label">Guest Average</div>
        <div class="nr-kpi-value text-warning">${{ number_format($report->guest_average, 2) }}</div>
        <div class="nr-kpi-sub text-muted">Spend per head</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="nr-kpi-card">
        <div class="nr-kpi-label">Ending Safe</div>
        <div class="nr-kpi-value text-white">${{ number_format($report->safe_balance, 2) }}</div>
        <div class="nr-kpi-sub text-muted">Deposit: ${{ number_format($report->deposit, 2) }}</div>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <!-- Section 1: Financials -->
    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header d-flex align-items-center gap-2">
          <i class="fas fa-dollar-sign text-success"></i>
          <h5 class="card-title mb-0">Financial Revenue Breakdown</h5>
        </div>
        <div class="card-body">
          <table class="table table-sm table-borderless mb-0">
            <tbody>
              <tr><td class="text-muted">Net Sales ($):</td><td class="text-end fw-bold text-success">${{ number_format($report->net_sales, 2) }}</td></tr>
              <tr><td class="text-muted">Nightly Goal ($):</td><td class="text-end text-white">${{ number_format($report->nightly_goal, 2) }}</td></tr>
              <tr><td class="text-muted">Last Year Net Sales ($):</td><td class="text-end text-white">${{ number_format($report->last_year_net_sales, 2) }}</td></tr>
              <tr><td class="text-muted">Weekly Running Net Sales ($):</td><td class="text-end text-white">${{ number_format($report->weekly_running_net_sales, 2) }}</td></tr>
              <tr><td class="text-muted">Day Shift Net Sales ($):</td><td class="text-end text-white">${{ number_format($report->day_shift_net_sales, 2) }}</td></tr>
              <tr><td class="text-muted">POS Voids ($):</td><td class="text-end text-danger">${{ number_format($report->voids, 2) }}</td></tr>
              <tr><td class="text-muted">Manager Comps ($):</td><td class="text-end text-danger">${{ number_format($report->comps, 2) }}</td></tr>
              @if($report->dance_dollars_sold)
                <tr><td class="text-muted">Dance Dollars Sold ($):</td><td class="text-end text-warning">${{ number_format($report->dance_dollars_sold, 2) }}</td></tr>
                <tr><td class="text-muted">Dance Dollars Redeemed ($):</td><td class="text-end text-warning">${{ number_format($report->dance_dollars_redeemed, 2) }}</td></tr>
                <tr><td class="text-muted">VIP Rooms Booked (#):</td><td class="text-end text-white">{{ $report->vip_rooms_sold }}</td></tr>
              @endif
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Section 2: Attendance & Cash Flow -->
    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header d-flex align-items-center gap-2">
          <i class="fas fa-users text-info"></i>
          <h5 class="card-title mb-0">Attendance, Payouts & Vault</h5>
        </div>
        <div class="card-body">
          <table class="table table-sm table-borderless mb-0">
            <tbody>
              <tr><td class="text-muted">Total Guests (Headcount):</td><td class="text-end fw-bold text-white">{{ number_format($report->total_guests) }}</td></tr>
              <tr><td class="text-muted">Paid Admissions:</td><td class="text-end text-white">{{ number_format($report->paid_guests ?? 0) }}</td></tr>
              <tr><td class="text-muted">Free / Discount Guests:</td><td class="text-end text-white">{{ number_format($report->free_discount_guests ?? 0) }}</td></tr>
              <tr><td class="text-muted">Passes Redeemed:</td><td class="text-end text-white">{{ number_format($report->passes_redeemed ?? 0) }}</td></tr>
              <tr><td class="text-muted">IPEs on Shift:</td><td class="text-end text-warning">{{ $report->ipes ?? '—' }}</td></tr>
              <tr><td class="text-muted">Taxi / Rideshare Payout:</td><td class="text-end text-white">${{ number_format($report->taxi_payout, 2) }}</td></tr>
              <tr><td class="text-muted">ATM Payouts:</td><td class="text-end text-white">${{ number_format($report->atm_payout, 2) }}</td></tr>
              <tr><td class="text-muted">Other Payouts:</td><td class="text-end text-white">${{ number_format($report->other_payouts, 2) }}</td></tr>
              <tr><td class="text-muted">Total Paid Outs ($):</td><td class="text-end fw-bold text-warning">${{ number_format($report->total_payouts, 2) }}</td></tr>
              <tr><td class="text-muted">Sealed Bank Deposit ($):</td><td class="text-end fw-bold text-success">${{ number_format($report->deposit, 2) }}</td></tr>
              <tr><td class="text-muted">Ending Safe Balance ($):</td><td class="text-end fw-bold text-white">${{ number_format($report->safe_balance, 2) }}</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Section 3: Shift Notes & Operations Narrative -->
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center gap-2">
            <i class="fas fa-sticky-note text-warning"></i>
            <h5 class="card-title mb-0">Managerial Shift Notes & Operations</h5>
          </div>
          <div>
            @if($report->incident_flag)
              <span class="badge bg-danger"><i class="fas fa-exclamation-triangle me-1"></i> Incidents Logged Tonight</span>
            @endif
            @if($report->weather)
              <span class="badge bg-secondary ms-1"><i class="fas fa-cloud me-1"></i> {{ $report->weather }}</span>
            @endif
          </div>
        </div>
        <div class="card-body">
          <div class="row g-3">
            @if($report->night_summary)
            <div class="col-md-12">
              <label class="small text-white text-uppercase fw-bold">Executive Night Summary</label>
              <div class="p-3 rounded" style="background: var(--nr-surface-2); border: 1px solid var(--nr-border);">
                {{ $report->night_summary }}
              </div>
            </div>
            @endif

            @if($report->super_star_nomination)
            <div class="col-md-6">
              <label class="small text-white text-uppercase fw-bold">Superstar Shift MVP Nomination</label>
              <div class="p-3 rounded text-warning fw-bold" style="background: var(--nr-surface-2); border: 1px solid var(--nr-border);">
                <i class="fas fa-star me-2"></i> {{ $report->super_star_nomination }}
              </div>
            </div>
            @endif

            @if($report->team_member_notes)
            <div class="col-md-6">
              <label class="small text-white text-uppercase fw-bold">Staff & Team Member Notes</label>
              <div class="p-3 rounded" style="background: var(--nr-surface-2); border: 1px solid var(--nr-border);">
                {{ $report->team_member_notes }}
              </div>
            </div>
            @endif

            @if($report->ipe_notes)
            <div class="col-md-6">
              <label class="small text-white text-uppercase fw-bold">IPE / Entertainer Shift Notes</label>
              <div class="p-3 rounded" style="background: var(--nr-surface-2); border: 1px solid var(--nr-border);">
                {{ $report->ipe_notes }}
              </div>
            </div>
            @endif

            @if($report->ordering_notes)
            <div class="col-md-6">
              <label class="small text-white text-uppercase fw-bold">Inventory & Supply Ordering Notes</label>
              <div class="p-3 rounded" style="background: var(--nr-surface-2); border: 1px solid var(--nr-border);">
                {{ $report->ordering_notes }}
              </div>
            </div>
            @endif

            @if($report->social_media_content)
            <div class="col-md-6">
              <label class="small text-white text-uppercase fw-bold">Social Media Content & Promotions</label>
              <div class="p-3 rounded" style="background: var(--nr-surface-2); border: 1px solid var(--nr-border);">
                {{ $report->social_media_content }}
              </div>
            </div>
            @endif

            @if($report->pass_distribution_locations)
            <div class="col-md-6">
              <label class="small text-white text-uppercase fw-bold">Pass Distribution Locations</label>
              <div class="p-3 rounded" style="background: var(--nr-surface-2); border: 1px solid var(--nr-border);">
                {{ $report->pass_distribution_locations }}
              </div>
            </div>
            @endif

            @if($report->shift_comments)
            <div class="col-md-12">
              <label class="small text-white text-uppercase fw-bold">Additional GM Closing Comments</label>
              <div class="p-3 rounded" style="background: var(--nr-surface-2); border: 1px solid var(--nr-border);">
                {{ $report->shift_comments }}
              </div>
            </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection
