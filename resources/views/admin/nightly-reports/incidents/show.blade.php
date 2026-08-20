@extends('admin.nightly-reports.layout')

@section('content')
<div class="container-fluid p-0">
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
      <a href="{{ route('admin.nightly-reports.incidents.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
        <i class="fas fa-arrow-left me-1"></i> Back to Incident Register
      </a>
      <h4 class="text-white fw-bold mb-0">
        Incident File: {{ $incident->report_type_field }} ({{ $incident->location->name ?? 'Venue' }})
      </h4>
      <p class="text-muted small mb-0">Date: {{ $incident->incident_date->format('l, F j, Y') }} at {{ $incident->time_of_incident }} • Reported by {{ $incident->submitter_name }}</p>
    </div>
    <div class="d-flex align-items-center gap-2">
      <form method="POST" action="{{ route('admin.nightly-reports.incidents.status', $incident->id) }}" class="d-flex align-items-center gap-2">
        @csrf
        <select name="status" class="form-select form-select-sm">
          <option value="pending" {{ $incident->status === 'pending' ? 'selected' : '' }}>Pending Review</option>
          <option value="under_review" {{ $incident->status === 'under_review' ? 'selected' : '' }}>Under Review</option>
          <option value="legal_hold" {{ $incident->status === 'legal_hold' ? 'selected' : '' }}>Legal Hold</option>
          <option value="resolved" {{ $incident->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
        </select>
        <button type="submit" class="btn btn-sm btn-gold">Update</button>
      </form>
    </div>
  </div>

  <div class="row g-4">
    <!-- Incident Narrative -->
    <div class="col-lg-8">
      <div class="card mb-4">
        <div class="card-header"><h5 class="card-title mb-0">Chronological Narrative</h5></div>
        <div class="card-body">
          <div class="p-3 rounded" style="background: var(--nr-surface-2); border: 1px solid var(--nr-border); line-height: 1.6;">
            {{ $incident->incident_description }}
          </div>
        </div>
      </div>

      <!-- Involved Persons & Witnesses -->
      <div class="card mb-4">
        <div class="card-header"><h5 class="card-title mb-0">Involved Parties & Witness Statements</h5></div>
        <div class="card-body">
          @if($incident->involved_persons)
          <div class="mb-3">
            <label class="small text-muted text-uppercase fw-bold">Involved Persons & Descriptions</label>
            <div class="p-3 rounded" style="background: var(--nr-surface-2); border: 1px solid var(--nr-border);">
              {{ $incident->involved_persons }}
            </div>
          </div>
          @endif

          <label class="small text-muted text-uppercase fw-bold">Attached Witness Statements ({{ $incident->witnessStatements->count() }})</label>
          <div class="list-group">
            @forelse($incident->witnessStatements as $w)
            <div class="list-group-item p-3" style="background: var(--nr-surface-2); border-color: var(--nr-border);">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="fw-bold text-white">{{ $w->witness_name }} <span class="badge bg-secondary ms-1">{{ $w->witness_type }}</span></div>
                <small class="text-muted">{{ $w->witness_phone ?? $w->witness_email }}</small>
              </div>
              <div class="small text-muted">{{ $w->statement_text }}</div>
            </div>
            @empty
            <div class="text-muted small p-3 rounded" style="background: var(--nr-surface-2);">No witness statements linked to this incident file.</div>
            @endforelse
          </div>
        </div>
      </div>
    </div>

    <!-- Police & Surveillance Metadata -->
    <div class="col-lg-4">
      <div class="card mb-4">
        <div class="card-header"><h5 class="card-title mb-0">Police & Law Enforcement</h5></div>
        <div class="card-body">
          <table class="table table-sm table-borderless mb-0">
            <tr><td class="text-muted">Police Report #:</td><td class="text-end text-white fw-bold">{{ $incident->police_report_number ?? 'None' }}</td></tr>
            <tr><td class="text-muted">Officers / Badges:</td><td class="text-end text-white">{{ $incident->police_officers_badges ?? 'None' }}</td></tr>
            <tr><td class="text-muted">Duty Managers:</td><td class="text-end text-white">{{ $incident->managers_on_duty ?? '—' }}</td></tr>
            <tr><td class="text-muted">Manager Phone:</td><td class="text-end text-white">{{ $incident->manager_phone ?? '—' }}</td></tr>
          </table>
        </div>
      </div>

      <div class="card">
        <div class="card-header"><h5 class="card-title mb-0">Surveillance Video Logs</h5></div>
        <div class="card-body">
          <table class="table table-sm table-borderless mb-0">
            <tr><td class="text-muted">Camera Angles:</td><td class="text-end text-white">{{ $incident->camera_angles ?? 'Not specified' }}</td></tr>
            <tr><td class="text-muted">Footage Timestamp:</td><td class="text-end text-warning fw-bold">{{ $incident->camera_timestamp ?? '—' }}</td></tr>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
