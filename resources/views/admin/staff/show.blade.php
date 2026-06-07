@extends('admin.main')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4>{{ $staff->display_name }}</h4>
                    <p class="text-muted mb-0">Current Staff - {{ $staffType }}</p>
                </div>
                <a href="{{ route('admin.staff.index', ['type' => $type]) }}" class="btn btn-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Back to List
                </a>
            </div>

            <hr class="my-3" />

            <div class="row mb-3">
                <div class="col-md-6">
                    <p class="mb-1"><strong>Name:</strong> {{ $staff->display_name }}</p>
                    <p class="mb-1"><strong>Email:</strong> <a href="mailto:{{ $staff->user->email }}">{{ $staff->user->email }}</a></p>
                    <p class="mb-1"><strong>Status:</strong> <span class="badge bg-{{ $staff->status === 'approved' ? 'success' : ($staff->status === 'pending' ? 'warning' : 'danger') }}">{{ ucfirst($staff->status) }}</span></p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1"><strong>Staff Type:</strong> {{ $staffType }}</p>
                    @if($type === 'entertainer')
                        <p class="mb-1"><strong>Club:</strong> {{ $staff->website->name ?? 'N/A' }}</p>
                    @endif
                    <p class="mb-1"><strong>Submitted:</strong> {{ $staff->created_at->format('M d, Y H:i') }}</p>
                </div>
            </div>

            <!-- Current Staff Info -->
            <div class="alert alert-info d-flex align-items-start gap-2 mb-3">
                <i class="bx bx-info-circle" style="font-size: 1.2rem; flex-shrink: 0;"></i>
                <div>
                    <strong>Current Staff Registration</strong>
                    <p class="mb-0">This is a current staff member registration. Physical W-9 documentation is already on file. No W-9 form submission is required.</p>
                </div>
            </div>

            <!-- Action Buttons -->
            @if($staff->status === 'pending')
                <div class="mb-3">
                    <h6 class="mb-2">Approval Actions</h6>
                    <form method="POST" action="{{ route('admin.staff.approve', ['type' => $type, 'id' => $staff->id]) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="bx bx-check me-1"></i> Approve Application
                        </button>
                    </form>
                    <button type="button" class="btn btn-danger ms-2" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        <i class="bx bx-x me-1"></i> Reject Application
                    </button>
                </div>
            @else
                <div class="alert alert-info">
                    This application has already been <strong>{{ ucfirst($staff->status) }}</strong>.
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Reject Modal -->
@if($staff->status === 'pending')
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Application</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.staff.reject', ['type' => $type, 'id' => $staff->id]) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Rejection Reason (Optional)</label>
                        <textarea name="rejection_reason" rows="4" class="form-control" placeholder="Briefly explain why this application is being rejected..."></textarea>
                        <small class="text-muted">This will be included in the rejection email to the applicant.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Application</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection
