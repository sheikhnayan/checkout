@extends('admin.nightly-reports.layout')

@section('content')
<div class="container-fluid p-0">
  <div class="card mb-4">
    <div class="card-body">
      <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <div>
          <h4 class="text-white mb-1 fw-bold"><i class="fas fa-folder-open text-warning me-2"></i> Document Clearance Queue</h4>
          <p class="text-muted small mb-0">Review requests from ambassadors and managers for access to restricted security and witness documents.</p>
        </div>
      </div>

      <form method="GET" action="{{ route('admin.nightly-reports.document-requests.index') }}" class="row g-2">
        <div class="col-md-4">
          <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="">All Statuses</option>
            <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending Review</option>
            <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="denied" {{ $status === 'denied' ? 'selected' : '' }}>Denied</option>
          </select>
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
            <th>Requester Name</th>
            <th>Requested Document</th>
            <th>Case Justification</th>
            <th>Status</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($requests as $req)
          <tr>
            <td><small class="text-muted">{{ $req->created_at->format('M d, Y') }}</small></td>
            <td>
              <div class="fw-bold text-white">{{ $req->requester_name }}</div>
              <small class="text-muted">{{ $req->requester_email }} ({{ $req->requester_role }})</small>
            </td>
            <td><span class="badge bg-secondary">{{ ucfirst($req->report_type) }} #{{ $req->report_id }}</span></td>
            <td>
              <div class="text-white small fw-semibold">{{ $req->requested_for }}</div>
              <small class="text-muted">{{ $req->requester_note }}</small>
            </td>
            <td>
              @if($req->status === 'approved')
                <span class="badge bg-success">Approved</span>
              @elseif($req->status === 'denied')
                <span class="badge bg-danger">Denied</span>
              @else
                <span class="badge bg-warning text-dark">Pending</span>
              @endif
            </td>
            <td class="text-end">
              @if($req->status === 'pending')
                <form method="POST" action="{{ route('admin.nightly-reports.document-requests.approve', $req->id) }}" class="d-inline">
                  @csrf
                  <button type="submit" class="btn btn-sm btn-outline-success"><i class="fas fa-check me-1"></i> Approve</button>
                </form>
                <form method="POST" action="{{ route('admin.nightly-reports.document-requests.deny', $req->id) }}" class="d-inline">
                  @csrf
                  <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-times me-1"></i> Deny</button>
                </form>
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="6" class="text-center py-5 text-muted">No pending document requests in clearance queue.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
