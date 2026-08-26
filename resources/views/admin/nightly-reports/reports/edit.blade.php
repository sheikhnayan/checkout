@extends('admin.nightly-reports.layout')

@section('content')
<div class="container-fluid p-0">
  <div class="d-flex align-items-center justify-content-between mb-4">
    <div>
      <a href="{{ route('admin.nightly-reports.reports.show', ['type' => 'nightly', 'id' => $report->id]) }}" class="btn btn-sm btn-outline-secondary mb-2">
        <i class="fas fa-arrow-left me-1"></i> Cancel & Return
      </a>
      <h4 class="text-white fw-bold mb-0">Edit Shift Report — {{ $report->location->name ?? 'Venue' }}</h4>
      <p class="text-muted small mb-0">Business Date: {{ $report->business_date->format('M d, Y') }}</p>
    </div>
  </div>

  <form method="POST" action="{{ route('admin.nightly-reports.reports.update', ['type' => 'nightly', 'id' => $report->id]) }}">
    @csrf
    @method('PUT')

    <div class="row g-4">
      <!-- Financials -->
      <div class="col-lg-6">
        <div class="card h-100">
          <div class="card-header"><h5 class="card-title mb-0">Financial Sales Figures</h5></div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label text-white small text-uppercase fw-bold">Net Sales ($)</label>
              <input type="number" step="0.01" name="net_sales" class="form-control" value="{{ $report->net_sales }}" required />
            </div>
            <div class="mb-3">
              <label class="form-label text-white small text-uppercase fw-bold">Nightly Goal ($)</label>
              <input type="number" step="0.01" name="nightly_goal" class="form-control" value="{{ $report->nightly_goal }}" />
            </div>
            <div class="row g-2 mb-3">
              <div class="col-6">
                <label class="form-label text-white small text-uppercase fw-bold">Actual Deposit ($)</label>
                <input type="number" step="0.01" name="deposit" class="form-control" value="{{ $report->deposit }}" />
              </div>
              <div class="col-6">
                <label class="form-label text-white small text-uppercase fw-bold">Safe Balance ($)</label>
                <input type="number" step="0.01" name="safe_balance" class="form-control" value="{{ $report->safe_balance }}" />
              </div>
            </div>
            <div class="row g-2">
              <div class="col-4">
                <label class="form-label text-white small text-uppercase fw-bold">Taxi ($)</label>
                <input type="number" step="0.01" name="taxi_payout" class="form-control" value="{{ $report->taxi_payout }}" />
              </div>
              <div class="col-4">
                <label class="form-label text-white small text-uppercase fw-bold">ATM ($)</label>
                <input type="number" step="0.01" name="atm_payout" class="form-control" value="{{ $report->atm_payout }}" />
              </div>
              <div class="col-4">
                <label class="form-label text-white small text-uppercase fw-bold">Other ($)</label>
                <input type="number" step="0.01" name="other_payouts" class="form-control" value="{{ $report->other_payouts }}" />
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Attendance & Operations -->
      <div class="col-lg-6">
        <div class="card h-100">
          <div class="card-header"><h5 class="card-title mb-0">Attendance & Operations</h5></div>
          <div class="card-body">
            <div class="row g-2 mb-3">
              <div class="col-6">
                <label class="form-label text-white small text-uppercase fw-bold">Total Guests</label>
                <input type="number" name="total_guests" class="form-control" value="{{ $report->total_guests }}" required />
              </div>
              <div class="col-6">
                <label class="form-label text-white small text-uppercase fw-bold">Paid Guests</label>
                <input type="number" name="paid_guests" class="form-control" value="{{ $report->paid_guests }}" />
              </div>
            </div>
            <div class="row g-2 mb-3">
              <div class="col-6">
                <label class="form-label text-white small text-uppercase fw-bold">Free / Discount Guests</label>
                <input type="number" name="free_discount_guests" class="form-control" value="{{ $report->free_discount_guests }}" />
              </div>
              <div class="col-6">
                <label class="form-label text-white small text-uppercase fw-bold">Passes Redeemed</label>
                <input type="number" name="passes_redeemed" class="form-control" value="{{ $report->passes_redeemed }}" />
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label text-white small text-uppercase fw-bold">Weather</label>
              <input type="text" name="weather" class="form-control" value="{{ $report->weather }}" />
            </div>
            <div class="mb-3">
              <label class="form-label text-white small text-uppercase fw-bold">Superstar Nomination</label>
              <input type="text" name="super_star_nomination" class="form-control" value="{{ $report->super_star_nomination }}" />
            </div>
            <div class="mb-3">
              <label class="form-label text-white small text-uppercase fw-bold">Additional Recipient (Email)</label>
              <input type="email" name="additional_recipient" class="form-control" value="{{ $report->additional_recipient }}" />
            </div>
          </div>
        </div>
      </div>

      <!-- Narrative Shift Notes -->
      <div class="col-12">
        <div class="card">
          <div class="card-header"><h5 class="card-title mb-0">Shift Narrative & Notes</h5></div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label text-white small text-uppercase fw-bold">Executive Night Summary</label>
              <textarea name="night_summary" class="form-control" rows="3">{{ $report->night_summary }}</textarea>
            </div>
            <div class="mb-3">
              <label class="form-label text-white small text-uppercase fw-bold">Incident Details</label>
              <textarea name="incident_notes" class="form-control" rows="2">{{ $report->incident_notes }}</textarea>
            </div>
            <div class="mb-3">
              <label class="form-label text-white small text-uppercase fw-bold">Nightly Checklists / Forms</label>
              <textarea name="nightly_checklists" class="form-control" rows="2">{{ $report->nightly_checklists }}</textarea>
            </div>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label text-white small text-uppercase fw-bold">Team Member Notes</label>
                <textarea name="team_member_notes" class="form-control" rows="2">{{ $report->team_member_notes }}</textarea>
              </div>
              <div class="col-md-6">
                <label class="form-label text-white small text-uppercase fw-bold">IPE / Entertainer Notes</label>
                <textarea name="ipe_notes" class="form-control" rows="2">{{ $report->ipe_notes }}</textarea>
              </div>
            </div>
            <div class="mt-4 d-flex justify-content-end gap-2">
              <a href="{{ route('admin.nightly-reports.reports.show', ['type' => 'nightly', 'id' => $report->id]) }}" class="btn btn-secondary">Cancel</a>
              <button type="submit" class="btn btn-gold px-4"><i class="fas fa-save me-1"></i> Save Changes</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
@endsection
