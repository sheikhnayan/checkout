@extends('admin.nightly-reports.layout')

@section('content')
<div class="container-fluid p-0">
  <div class="card mb-4">
    <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-3">
      <div>
        <h4 class="text-white mb-1 fw-bold"><i class="fas fa-user-shield text-warning me-2"></i> Admin & Portal Users</h4>
        <p class="text-muted small mb-0">Manage authorized users, assign roles, and handle password resets for the Nightly Reports portal.</p>
      </div>
      <button class="btn btn-sm btn-gold" data-bs-toggle="modal" data-bs-target="#addUserModal">
        <i class="fas fa-plus me-1"></i> Add User
      </button>
    </div>
  </div>

  <div class="card">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>User Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Assigned Clubs</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($users as $u)
          <tr>
            <td class="fw-bold text-white">{{ $u->name }}</td>
            <td><small class="text-muted">{{ $u->email }}</small></td>
            <td><span class="badge bg-secondary">{{ ucfirst($u->user_type) }}</span></td>
            <td>
              <small class="text-white">{{ $u->nrLocations->count() }} club(s)</small>
            </td>
            <td class="text-end">
              <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#resetPassModal{{ $u->id }}" title="Reset Password">
                <i class="fas fa-key"></i>
              </button>
              @if($u->id !== auth()->id())
                <form method="POST" action="{{ route('admin.nightly-reports.users.destroy', $u->id) }}" class="d-inline">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete user account?')" title="Delete User">
                    <i class="fas fa-trash"></i>
                  </button>
                </form>
              @endif
            </td>
          </tr>

          <!-- Reset Password Modal -->
          <div class="modal fade" id="resetPassModal{{ $u->id }}" tabindex="-1">
            <div class="modal-dialog">
              <div class="modal-content" style="background: var(--nr-surface-2); border-color: var(--nr-border);">
                <form method="POST" action="{{ route('admin.nightly-reports.users.reset-password', $u->id) }}">
                  @csrf
                  <div class="modal-header">
                    <h5 class="modal-title text-white">Reset Password for {{ $u->name }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                    <label class="form-label text-white small fw-bold">New Password (leave blank for random)</label>
                    <input type="text" name="password" class="form-control" placeholder="Enter new password or leave blank" />
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-gold">Update Password</button>
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

  <!-- Add User Modal -->
  <div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content" style="background: var(--nr-surface-2); border-color: var(--nr-border);">
        <form method="POST" action="{{ route('admin.nightly-reports.users.store') }}">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title text-white">Create Portal User</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label text-white small fw-bold">Full Name</label>
              <input type="text" name="name" class="form-control" required />
            </div>
            <div class="mb-3">
              <label class="form-label text-white small fw-bold">Email Address</label>
              <input type="email" name="email" class="form-control" required />
            </div>
            <div class="mb-3">
              <label class="form-label text-white small fw-bold">Initial Password</label>
              <input type="password" name="password" class="form-control" required minlength="8" />
            </div>
            <div class="mb-3">
              <label class="form-label text-white small fw-bold">Role</label>
              <select name="user_type" class="form-select" required>
                <option value="admin">Admin / Super Admin</option>
                <option value="manager" selected>Manager / Field Ambassador</option>
                <option value="website_user">Club Staff / Submitter</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-gold">Create User</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
