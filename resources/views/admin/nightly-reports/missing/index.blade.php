@extends('admin.nightly-reports.layout')

@section('content')
<div class="container-fluid p-0">
  <div class="card mb-4">
    <div class="card-body">
      <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
          <h4 class="text-white mb-1 fw-bold"><i class="fas fa-exclamation-triangle text-warning me-2"></i> Missing Reports Tracker</h4>
          <p class="text-muted small mb-0">Automated compliance audit of venues with unsubmitted shift reports.</p>
        </div>
        <div>
          <span class="badge {{ count($missingList) > 0 ? 'bg-danger' : 'bg-success' }} px-3 py-2">
            {{ count($missingList) }} Missing Submissions Detected
          </span>
        </div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>Venue / Club</th>
            <th>Expected Date</th>
            <th>Days Late</th>
            <th>GM Contact</th>
            <th>Phone</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($missingList as $item)
          <tr>
            <td>
              <div class="fw-bold text-white">{{ $item['location_name'] }}</div>
              <div class="small text-muted">{{ $item['location_type'] }}</div>
            </td>
            <td><span class="text-danger fw-bold">{{ \Carbon\Carbon::parse($item['business_date'])->format('M d, Y') }}</span></td>
            <td><span class="badge bg-danger">{{ $item['days_ago'] }} day(s) ago</span></td>
            <td>
              <div class="text-white small">{{ $item['gm_name'] ?? 'General Manager' }}</div>
              <div class="text-muted small">{{ $item['gm_email'] ?? '—' }}</div>
            </td>
            <td><small class="text-muted">{{ $item['phone'] ?? '—' }}</small></td>
            <td class="text-end">
              <form method="POST" action="{{ route('admin.nightly-reports.missing.reminder') }}" class="d-inline">
                @csrf
                <input type="hidden" name="location_id" value="{{ $item['location_id'] }}" />
                <input type="hidden" name="business_date" value="{{ $item['business_date'] }}" />
                <button type="submit" class="btn btn-sm btn-outline-warning">
                  <i class="fas fa-paper-plane me-1"></i> Send Nudge
                </button>
              </form>
              <a href="{{ route('nightly.submit.nightly', ['location' => $item['location_id']]) }}" target="_blank" class="btn btn-sm btn-gold ms-1">
                <i class="fas fa-plus me-1"></i> Submit
              </a>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="6" class="text-center py-5 text-success">
              <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
              <div class="fw-bold fs-5">100% Submission Compliance!</div>
              <div class="text-muted small">All active venues have submitted their scheduled shift reports.</div>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
