@extends('admin.nightly-reports.layout')

@section('content')
@php
  $incDate = $incident->incident_calendar_date ?? $incident->incident_date;
  $incTime = $incident->formatted_incident_time ?? $incident->incident_time ?? $incident->time_of_incident;
  $locationName = $incident->website->name ?? $incident->location_dba_name ?? $incident->location_legal_name ?? $incident->location->name ?? 'Venue';
  $reporterName = $incident->reporter_name ?? $incident->submitter_name ?? 'Staff';
  $incType = $incident->incident_type ?? $incident->report_type_field ?? 'Security Incident';
  $witnesses = $incident->witnessReports ?? $incident->witnessStatements ?? collect();
  $involvedPersons = $incident->involved_injured_persons ?? $incident->involved_persons;
@endphp

<div class="container-fluid p-0">
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
      <a href="{{ route('admin.nightly-reports.incidents.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
        <i class="fas fa-arrow-left me-1"></i> Back to Incident Register
      </a>
      <h4 class="text-white fw-bold mb-0">
        Incident File #{{ $incident->id }}: {{ $incType }} ({{ $locationName }})
      </h4>
      <p class="text-muted small mb-0">
        Date: {{ $incDate ? \Carbon\Carbon::parse($incDate)->format('l, F j, Y') : '—' }} at {{ $incTime }} • Reported by {{ $reporterName }}
      </p>
    </div>
    <div class="d-flex align-items-center gap-2">
      @if(Route::has('admin.incident.export'))
      <a href="{{ route('admin.incident.export', $incident->id) }}" class="btn btn-sm btn-outline-info">
        <i class="fas fa-file-pdf me-1"></i> Export PDF
      </a>
      @endif

      <form method="POST" action="{{ route('admin.nightly-reports.incidents.status', $incident->id) }}" class="d-flex align-items-center gap-2">
        @csrf
        <select name="status" class="form-select form-select-sm">
          <option value="open" {{ in_array($incident->status, ['open', 'pending'], true) ? 'selected' : '' }}>Open / Pending</option>
          <option value="under_review" {{ $incident->status === 'under_review' ? 'selected' : '' }}>Under Review</option>
          <option value="legal_hold" {{ $incident->status === 'legal_hold' ? 'selected' : '' }}>Legal Hold</option>
          <option value="resolved" {{ in_array($incident->status, ['resolved', 'closed'], true) ? 'selected' : '' }}>Resolved / Closed</option>
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
          <div class="p-3 rounded text-white" style="background: var(--nr-surface-2); border: 1px solid var(--nr-border); line-height: 1.6;">
            {{ $incident->incident_description }}
          </div>
        </div>
      </div>

      <!-- Involved Persons & Witnesses -->
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">Involved Parties & Witness Statements</h5>
          @if(!empty($incident->public_witness_token))
          <a href="{{ route('incident.witness.form', $incident->public_witness_token) }}" target="_blank" class="btn btn-sm btn-outline-warning">
            <i class="fas fa-qrcode me-1"></i> Public Witness Form Link
          </a>
          @endif
        </div>
        <div class="card-body">
          @if($involvedPersons)
          <div class="mb-3">
            <label class="small text-muted text-uppercase fw-bold">Involved Persons & Descriptions</label>
            <div class="p-3 rounded text-white" style="background: var(--nr-surface-2); border: 1px solid var(--nr-border);">
              {{ $involvedPersons }}
            </div>
          </div>
          @endif

          <label class="small text-muted text-uppercase fw-bold">Attached Witness Statements ({{ $witnesses->count() }})</label>
          <div class="list-group">
            @forelse($witnesses as $w)
            <div class="list-group-item p-3" style="background: var(--nr-surface-2); border-color: var(--nr-border);">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="fw-bold text-white">
                  {{ $w->full_name ?? $w->witness_name ?? 'Witness' }} 
                  <span class="badge bg-secondary ms-1">{{ $w->witness_type ?? 'Witness' }}</span>
                </div>
                <small class="text-muted">{{ $w->phone_number ?? $w->witness_phone ?? $w->email ?? $w->witness_email ?? '' }}</small>
              </div>
              <div class="small text-white-50">{{ $w->statement_text }}</div>
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

      <div class="card mb-4">
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
