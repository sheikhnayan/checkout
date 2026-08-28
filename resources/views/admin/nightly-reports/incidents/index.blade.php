@extends('admin.nightly-reports.layout')

@section('content')
<div class="container-fluid p-0">
  <div class="card mb-4">
    <div class="card-body">
      <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <div>
          <h4 class="text-white mb-1 fw-bold"><i class="fas fa-shield-alt text-danger me-2"></i> Security Incident Reports</h4>
          <p class="text-muted small mb-0">Legal risk management, police reports, surveillance footage timestamps, and witness logs.</p>
        </div>
        <div class="d-flex gap-2">
          <a href="{{ route('nightly.submit.incident') }}" target="_blank" class="btn btn-sm btn-gold">
            <i class="fas fa-plus me-1"></i> Log Incident
          </a>
        </div>
      </div>

      <!-- Filters -->
      <form method="GET" action="{{ route('admin.nightly-reports.incidents.index') }}" class="row g-2">
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
          <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="">All Review Statuses</option>
            <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="under_review" {{ $status === 'under_review' ? 'selected' : '' }}>Under Review</option>
            <option value="legal_hold" {{ $status === 'legal_hold' ? 'selected' : '' }}>Legal Hold</option>
            <option value="resolved" {{ $status === 'resolved' ? 'selected' : '' }}>Resolved</option>
          </select>
        </div>
        <div class="col-md-4">
          <input type="text" name="search" class="form-control form-control-sm" value="{{ $search }}" placeholder="Search description, police #, involved persons..." />
        </div>
        <div class="col-md-2">
          <button type="submit" class="btn btn-sm btn-primary w-100"><i class="fas fa-search me-1"></i> Search</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Incident Table -->
  <div class="card">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>Date & Time</th>
            <th>Location</th>
            <th>Type</th>
            <th>Description Preview</th>
            <th>Police Report #</th>
            <th>Linked Witnesses</th>
            <th>Status</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($incidents as $inc)
          @php
            $incDate = $inc->incident_calendar_date ?? $inc->incident_date;
            $incTime = $inc->formatted_incident_time ?? $inc->incident_time ?? $inc->time_of_incident;
            $locationName = $inc->website->name ?? $inc->location_dba_name ?? $inc->location_legal_name ?? $inc->location->name ?? 'Venue';
            $incType = $inc->incident_type ?? $inc->report_type_field ?? 'Security';
            $witnessCount = isset($inc->witnessReports) ? $inc->witnessReports->count() : (isset($inc->witnessStatements) ? $inc->witnessStatements->count() : 0);
          @endphp
          <tr>
            <td>
              <div class="fw-bold text-white">{{ $incDate ? \Carbon\Carbon::parse($incDate)->format('M d, Y') : '—' }}</div>
              <small class="text-muted">{{ $incTime }}</small>
            </td>
            <td class="fw-bold text-white">{{ $locationName }}</td>
            <td><span class="badge bg-danger">{{ $incType }}</span></td>
            <td>
              <div class="small text-muted" style="max-width: 320px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                {{ $inc->incident_description }}
              </div>
            </td>
            <td><small class="text-white">{{ $inc->police_report_number ?? 'None' }}</small></td>
            <td>
              <span class="badge bg-secondary">{{ $witnessCount }} statement(s)</span>
            </td>
            <td>
              @if(in_array($inc->status, ['resolved', 'closed'], true))
                <span class="badge bg-success">Resolved</span>
              @elseif($inc->status === 'legal_hold')
                <span class="badge bg-warning text-dark">Legal Hold</span>
              @elseif($inc->status === 'under_review')
                <span class="badge bg-info">Under Review</span>
              @else
                <span class="badge bg-secondary">{{ ucwords(str_replace('_', ' ', $inc->status ?? 'Open')) }}</span>
              @endif
              @if($inc->restricted ?? false)
                <span class="badge bg-dark text-danger border border-danger ms-1">Restricted</span>
              @endif
            </td>
            <td class="text-end">
              <a href="{{ route('admin.nightly-reports.incidents.show', $inc->id) }}" class="btn btn-sm btn-outline-light" title="View Full File">
                <i class="fas fa-folder-open"></i>
              </a>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="8" class="text-center py-5 text-muted">No security incident records logged.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
