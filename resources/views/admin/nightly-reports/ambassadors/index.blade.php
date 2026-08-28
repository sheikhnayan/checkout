@extends('admin.nightly-reports.layout')

@section('content')
<div class="container-fluid p-0">
  <div class="card mb-4">
    <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-3">
      <div>
        <h4 class="text-white mb-1 fw-bold"><i class="fas fa-users-cog text-warning me-2"></i> Manage Ambassadors</h4>
        <p class="text-muted small mb-0">Create ambassador accounts and assign them club access for the Nightly Reports portal.</p>
      </div>
      <button class="btn btn-sm btn-gold" data-bs-toggle="modal" data-bs-target="#addAmbassadorModal">
        <i class="fas fa-plus me-1"></i> Add Ambassador
      </button>
    </div>
  </div>

  <div class="card">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Status</th>
            <th>Clubs Assigned</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($ambassadors as $ambassador)
          <tr>
            <td class="fw-bold text-white">{{ $ambassador->name }}</td>
            <td><small class="text-muted">{{ $ambassador->email }}</small></td>
            <td>
              @if($ambassador->is_active)
                <span class="badge bg-success">Active</span>
              @else
                <span class="badge bg-danger">Disabled</span>
              @endif
            </td>
            <td><small class="text-white">{{ $ambassador->clubs->count() }} club(s)</small></td>
            <td class="text-end">
              <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editAmbassadorModal{{ $ambassador->id }}" title="Edit Access">
                <i class="fas fa-edit"></i>
              </button>
              <a href="{{ route('admin.nightly-reports.ambassadors.impersonate', $ambassador->id) }}" class="btn btn-sm btn-outline-info" title="Login As">
                <i class="fas fa-user-secret"></i>
              </a>
            </td>
          </tr>

          {{-- Edit Modal --}}
          <div class="modal fade" id="editAmbassadorModal{{ $ambassador->id }}" tabindex="-1">
            <div class="modal-dialog">
              <div class="modal-content" style="background: var(--nr-surface-2); border-color: var(--nr-border);">
                <form action="{{ route('admin.nightly-reports.ambassadors.update', $ambassador->id) }}" method="POST">
                  @csrf
                  @method('PUT')
                  <div class="modal-header">
                    <h5 class="modal-title text-white">Edit {{ $ambassador->name }}'s Access</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                    <div class="mb-3">
                      <label class="form-label text-white small fw-bold">Status</label>
                      <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="statusSwitch{{ $ambassador->id }}" name="is_active" {{ $ambassador->is_active ? 'checked' : '' }}>
                        <label class="form-check-label text-muted" for="statusSwitch{{ $ambassador->id }}">Active Account</label>
                      </div>
                    </div>
                    <hr style="border-color: var(--nr-border);">
                    <label class="form-label text-white small fw-bold mb-2">Assign Clubs</label>
                    <div class="row g-2">
                      @foreach($websites as $website)
                      <div class="col-md-6">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox"
                            id="clubCheck{{ $ambassador->id }}_{{ $website->id }}"
                            name="clubs[]"
                            value="{{ $website->id }}"
                            {{ $ambassador->clubs->contains($website->id) ? 'checked' : '' }}>
                          <label class="form-check-label text-muted small" for="clubCheck{{ $ambassador->id }}_{{ $website->id }}">
                            {{ $website->name }}
                          </label>
                        </div>
                      </div>
                      @endforeach
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-gold">Save Changes</button>
                  </div>
                </form>
              </div>
            </div>
          </div>

          @empty
          <tr>
            <td colspan="5" class="text-center py-5 text-muted">
              <i class="fas fa-users-slash fa-2x mb-2 d-block opacity-25"></i>
              No ambassadors found. Add one to get started.
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

{{-- Add Ambassador Modal --}}
<div class="modal fade" id="addAmbassadorModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content" style="background: var(--nr-surface-2); border-color: var(--nr-border);">
      <form action="{{ route('admin.nightly-reports.ambassadors.store') }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title text-white">Add New Ambassador</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p class="text-muted small mb-3">An email will be sent to the ambassador with a link to set their password.</p>
          <div class="mb-3">
            <label class="form-label text-white small fw-bold">Full Name</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label text-white small fw-bold">Email Address</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <hr style="border-color: var(--nr-border);">
          <label class="form-label text-white small fw-bold mb-2">Initial Club Access</label>
          <div class="row g-2">
            @foreach($websites as $website)
            <div class="col-md-6">
              <div class="form-check">
                <input class="form-check-input" type="checkbox"
                  id="newClubCheck{{ $website->id }}"
                  name="clubs[]"
                  value="{{ $website->id }}">
                <label class="form-check-label text-muted small" for="newClubCheck{{ $website->id }}">
                  {{ $website->name }}
                </label>
              </div>
            </div>
            @endforeach
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-gold"><i class="fas fa-paper-plane me-1"></i> Add & Send Invite</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
