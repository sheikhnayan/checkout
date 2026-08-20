@extends('admin.nightly-reports.layout')

@section('content')
<div class="container-fluid p-0">
  <div class="card mb-4">
    <div class="card-body">
      <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <div>
          <h4 class="text-white mb-1 fw-bold"><i class="fas fa-file-signature text-primary me-2"></i> Witness Statements Log</h4>
          <p class="text-muted small mb-0">Patron, staff, and performer eyewitness testimony with incident linkage.</p>
        </div>
        <div class="d-flex gap-2">
          <a href="{{ route('nightly.submit.witness') }}" target="_blank" class="btn btn-sm btn-gold">
            <i class="fas fa-plus me-1"></i> New Witness Intake
          </a>
        </div>
      </div>

      <!-- Filters -->
      <form method="GET" action="{{ route('admin.nightly-reports.witness.index') }}" class="row g-2">
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
          <input type="text" name="search" class="form-control form-control-sm" value="{{ $search }}" placeholder="Search witness name, phone, statement..." />
        </div>
        <div class="col-md-2">
          <button type="submit" class="btn btn-sm btn-primary w-100"><i class="fas fa-search me-1"></i> Search</button>
        </div>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>Date</th>
            <th>Witness Name</th>
            <th>Type / Role</th>
            <th>Contact</th>
            <th>Statement Testimony</th>
            <th>Linked Incident</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($statements as $s)
          <tr>
            <td class="fw-bold text-white">{{ $s->incident_date->format('M d, Y') }}</td>
            <td class="fw-bold text-white">{{ $s->witness_name }}</td>
            <td><span class="badge bg-secondary">{{ $s->witness_type }}</span></td>
            <td>
              <div class="small text-white">{{ $s->witness_phone ?? '—' }}</div>
              <div class="small text-muted">{{ $s->witness_email ?? '—' }}</div>
            </td>
            <td>
              <div class="small text-muted" style="max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                {{ $s->statement_text }}
              </div>
            </td>
            <td>
              @if($s->incident)
                <a href="{{ route('admin.nightly-reports.incidents.show', $s->incident_id) }}" class="badge bg-info text-decoration-none">
                  #{{ $s->incident_id }} - {{ $s->incident->report_type_field }}
                </a>
              @else
                <span class="badge bg-dark text-muted">Unlinked</span>
              @endif
            </td>
            <td class="text-end">
              <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#linkModal{{ $s->id }}" title="Link to Incident">
                <i class="fas fa-link"></i> Link
              </button>
            </td>
          </tr>

          <!-- Link to Incident Modal -->
          <div class="modal fade" id="linkModal{{ $s->id }}" tabindex="-1">
            <div class="modal-dialog">
              <div class="modal-content" style="background: var(--nr-surface-2); border-color: var(--nr-border);">
                <form method="POST" action="{{ route('admin.nightly-reports.witness.link', $s->id) }}">
                  @csrf
                  <div class="modal-header">
                    <h5 class="modal-title text-white">Link Witness Statement</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                    <p class="text-muted small">Select an incident to associate with testimony from <strong>{{ $s->witness_name }}</strong>:</p>
                    <select name="incident_id" class="form-select">
                      <option value="">-- No Linked Incident (Unlink) --</option>
                      @foreach($availableIncidents as $inc)
                        <option value="{{ $inc->id }}" {{ $s->incident_id === $inc->id ? 'selected' : '' }}>
                          #{{ $inc->id }} — {{ $inc->incident_date->format('M d, Y') }} ({{ $inc->report_type_field }}) - {{ $inc->location->name ?? '' }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-gold">Save Link</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
          @empty
          <tr>
            <td colspan="7" class="text-center py-5 text-muted">No witness statement logs recorded.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
