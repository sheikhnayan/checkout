@extends('layouts.admin')

@section('title', 'Staff Application - ' . $staff->display_name)

@section('content')
<div class="admin-header">
    <div>
        <h1>{{ $staff->display_name }}</h1>
        <p class="text-muted">{{ $staffType }} Staff Application</p>
    </div>
    <div class="header-actions">
        <a href="{{ route('admin.staff.index', ['type' => $type]) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i> Back to List
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row">
    <div class="col-lg-8">
        <!-- Application Details -->
        <div class="admin-card mb-3">
            <div class="card-header">
                <h5><i class="fas fa-user me-2"></i> Application Details</h5>
            </div>
            <div class="card-body">
                <div class="details-grid">
                    <div class="detail-item">
                        <label>Full Name</label>
                        <value>{{ $staff->display_name }}</value>
                    </div>
                    <div class="detail-item">
                        <label>Email</label>
                        <value><a href="mailto:{{ $staff->user->email }}">{{ $staff->user->email }}</a></value>
                    </div>
                    <div class="detail-item">
                        <label>Staff Type</label>
                        <value>
                            <span class="badge" style="background: rgba(244,180,0,0.2); color: #f4b400;">
                                {{ $staffType }}
                            </span>
                        </value>
                    </div>
                    <div class="detail-item">
                        <label>Status</label>
                        <value>
                            <span class="badge bg-{{ $staff->status === 'approved' ? 'success' : ($staff->status === 'rejected' ? 'danger' : 'warning') }}">
                                {{ ucfirst($staff->status) }}
                            </span>
                        </value>
                    </div>
                    @if($type === 'entertainer')
                        <div class="detail-item">
                            <label>Club</label>
                            <value>{{ $staff->website->name ?? 'N/A' }}</value>
                        </div>
                    @endif
                    <div class="detail-item">
                        <label>Submitted On</label>
                        <value>{{ $staff->created_at->format('M d, Y H:i') }}</value>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Staff Note -->
        <div class="admin-card mb-3" style="background: rgba(79, 70, 229, 0.1); border: 1px solid rgba(79, 70, 229, 0.3);">
            <div class="card-body">
                <div style="display: flex; align-items: flex-start; gap: 12px;">
                    <i class="fas fa-info-circle" style="color: #4f46e5; margin-top: 2px;"></i>
                    <div>
                        <p style="margin: 0; font-weight: 600; color: #fff;">Current Staff Registration</p>
                        <p style="margin: 4px 0 0 0; font-size: 0.9rem; color: rgba(255,255,255,0.8);">
                            This is a current staff member registration. No W-9 form is required. Physical W-9 documentation is already on file.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Actions -->
        @if($staff->status === 'pending')
            <div class="admin-card mb-3">
                <div class="card-header">
                    <h5><i class="fas fa-tasks me-2"></i> Actions</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.staff.approve', ['type' => $type, 'id' => $staff->id]) }}" style="margin-bottom: 12px;">
                        @csrf
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-check me-2"></i> Approve Application
                        </button>
                    </form>

                    <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        <i class="fas fa-times me-2"></i> Reject Application
                    </button>
                </div>
            </div>
        @else
            <div class="admin-card mb-3">
                <div class="card-body">
                    <p class="text-muted" style="margin: 0;">
                        <i class="fas fa-info-circle me-2"></i>
                        This application has been {{ $staff->status }}.
                    </p>
                </div>
            </div>
        @endif

        <!-- Account Status -->
        <div class="admin-card">
            <div class="card-header">
                <h5><i class="fas fa-user-check me-2"></i> Account Status</h5>
            </div>
            <div class="card-body">
                <div class="status-item">
                    <label>Login Enabled</label>
                    <value>
                        <i class="fas {{ $staff->user->status === 'approved' ? 'fa-check text-success' : 'fa-times text-muted' }} me-2"></i>
                        {{ $staff->user->status === 'approved' ? 'Yes' : 'No' }}
                    </value>
                </div>
                <div class="status-item">
                    <label>Registration Type</label>
                    <value>Current Staff</value>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="background: #0c1120; border: 1px solid rgba(255,255,255,0.1);">
            <div class="modal-header">
                <h5 class="modal-title">Reject Application</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.staff.reject', ['type' => $type, 'id' => $staff->id]) }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="rejection_reason">Rejection Reason (Optional)</label>
                        <textarea id="rejection_reason" name="rejection_reason" class="form-control" rows="4"
                                  placeholder="Briefly explain why this application is being rejected..."
                                  style="background: #161e2e; border: 1px solid rgba(255,255,255,0.1); color: #e8edf8;"></textarea>
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

<style>
    .admin-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 24px;
    }

    .admin-header h1 {
        margin: 0 0 4px 0;
    }

    .admin-header .text-muted {
        margin: 0;
        font-size: 0.9rem;
    }

    .details-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 24px;
    }

    .detail-item {
        display: flex;
        flex-direction: column;
    }

    .detail-item label {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        opacity: 0.68;
        margin-bottom: 6px;
        font-weight: 600;
    }

    .detail-item value {
        color: #fff;
        font-weight: 500;
    }

    .detail-item a {
        color: #f4b400;
        text-decoration: none;
    }

    .detail-item a:hover {
        text-decoration: underline;
    }

    .status-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid rgba(255,255,255,0.05);
    }

    .status-item:last-child {
        border-bottom: none;
    }

    .status-item label {
        font-size: 0.85rem;
        opacity: 0.7;
    }

    .status-item value {
        color: #fff;
        font-weight: 500;
    }

    .text-success {
        color: #10b981;
    }

    .text-muted {
        color: rgba(255,255,255,0.5);
    }

    .badge {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-block;
    }

    .bg-success { background: rgba(16, 185, 129, 0.2); color: #10b981; }
    .bg-danger { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
    .bg-warning { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
</style>
@endsection
