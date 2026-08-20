@extends('admin.nightly-reports.layout')

@section('content')
<div class="container-fluid p-0">
  <div class="card mb-4">
    <div class="card-body">
      <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
          <h4 class="text-white mb-1 fw-bold"><i class="fas fa-users-cog text-warning me-2"></i> Field Ambassadors & Multi-Club Assignments</h4>
          <p class="text-muted small mb-0">Allocate staff and managers to multiple assigned clubs for dynamic dashboard scoping.</p>
        </div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>User / Ambassador</th>
            <th>Email</th>
            <th>Role</th>
            <th>Assigned Clubs ({{ count($locations) }} Total Available)</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($ambassadors as $user)
          <tr>
            <td class="fw-bold text-white">{{ $user->name }}</td>
            <td><small class="text-muted">{{ $user->email }}</small></td>
            <td><span class="badge bg-secondary">{{ ucfirst($user->user_type) }}</span></td>
            <td>
              <div class="d-flex flex-wrap gap-1">
                @forelse($user->nrLocations as $assignedLoc)
                  <span class="badge badge-gold">{{ $assignedLoc->name }}</span>
                @empty
                  <span class="text-muted small font-italic">No specific clubs allocated (Default scoping)</span>
                @endforelse
              </div>
            </td>
            <td class="text-end">
              <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#assignModal{{ $user->id }}" title="Assign Venues">
                <i class="fas fa-tasks me-1"></i> Assign Clubs
              </button>
            </td>
          </tr>

          <!-- Assign Modal -->
          <div class="modal fade" id="assignModal{{ $user->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
              <div class="modal-content" style="background: var(--nr-surface-2); border-color: var(--nr-border);">
                <form method="POST" action="{{ route('admin.nightly-reports.ambassadors.assign', $user->id) }}">
                  @csrf
                  <div class="modal-header">
                    <h5 class="modal-title text-white">Allocate Assigned Clubs for {{ $user->name }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                    <p class="text-muted small">Select the venues this user is authorized to oversee, submit, and view reports for:</p>
                    <div class="row g-2">
                      @foreach($locations as $loc)
                      <div class="col-md-6">
                        <div class="form-check p-2 rounded" style="background: var(--nr-surface-3);">
                          <input class="form-check-input ms-0 me-2" type="checkbox" name="location_ids[]" value="{{ $loc->id }}" id="user{{ $user->id }}_loc{{ $loc->id }}"
                            {{ $user->nrLocations->contains($loc->id) ? 'checked' : '' }} />
                          <label class="form-check-label text-white small" for="user{{ $user->id }}_loc{{ $loc->id }}">
                            {{ $loc->name }}
                          </label>
                        </div>
                      </div>
                      @endforeach
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-gold">Save Club Assignments</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
