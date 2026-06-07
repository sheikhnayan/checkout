@extends('admin.main')

@section('title', $pageTitle)

@section('content')
<div class="admin-header">
    <h1>{{ $pageTitle }}</h1>
    <div class="header-actions">
        <a href="{{ route('admin.staff.index', ['type' => $type === 'affiliate' ? 'entertainer' : 'affiliate']) }}" class="btn btn-secondary">
            <i class="fas fa-{{ $type === 'affiliate' ? 'star' : 'bullhorn' }} me-2"></i>
            Switch to {{ $type === 'affiliate' ? 'Entertainers' : 'Promoters' }}
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="admin-card">
    <div class="card-header">
        <div class="filter-tabs">
            <a href="{{ route('admin.staff.index', ['type' => $type, 'status' => 'pending']) }}"
               class="tab-link {{ $status === 'pending' ? 'active' : '' }}">
                <i class="fas fa-hourglass-half me-1"></i> Pending
            </a>
            <a href="{{ route('admin.staff.index', ['type' => $type, 'status' => 'approved']) }}"
               class="tab-link {{ $status === 'approved' ? 'active' : '' }}">
                <i class="fas fa-check me-1"></i> Approved
            </a>
            <a href="{{ route('admin.staff.index', ['type' => $type, 'status' => 'rejected']) }}"
               class="tab-link {{ $status === 'rejected' ? 'active' : '' }}">
                <i class="fas fa-times me-1"></i> Rejected
            </a>
        </div>
    </div>

    <div class="card-body">
        @if($staffList->count() > 0)
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        @if($type === 'entertainer')
                            <th>Club</th>
                        @endif
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($staffList as $staff)
                    <tr>
                        <td>
                            <strong>{{ $staff->display_name }}</strong>
                        </td>
                        <td>{{ $staff->user->email }}</td>
                        @if($type === 'entertainer')
                            <td>{{ $staff->website->name ?? 'N/A' }}</td>
                        @endif
                        <td>
                            <span class="badge bg-{{ $staff->status === 'approved' ? 'success' : ($staff->status === 'rejected' ? 'danger' : 'warning') }}">
                                {{ ucfirst($staff->status) }}
                            </span>
                        </td>
                        <td>{{ $staff->created_at->format('M d, Y') }}</td>
                        <td>
                            <a href="{{ route('admin.staff.show', ['type' => $type, 'id' => $staff->id]) }}"
                               class="btn btn-sm btn-primary">
                                <i class="fas fa-eye me-1"></i> View
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $staffList->links() }}
        @else
            <div class="empty-state">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <p class="text-muted">No {{ $status }} staff submissions found.</p>
            </div>
        @endif
    </div>
</div>

<style>
    .admin-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }

    .header-actions {
        display: flex;
        gap: 8px;
    }

    .filter-tabs {
        display: flex;
        gap: 16px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        padding-bottom: 12px;
    }

    .tab-link {
        color: rgba(255,255,255,0.6);
        text-decoration: none;
        padding: 8px 0;
        border-bottom: 2px solid transparent;
        transition: all 0.2s ease;
    }

    .tab-link:hover {
        color: #fff;
    }

    .tab-link.active {
        color: #f4b400;
        border-bottom-color: #f4b400;
    }

    .admin-table {
        width: 100%;
        border-collapse: collapse;
    }

    .admin-table th {
        background: rgba(255,255,255,0.05);
        padding: 12px 16px;
        text-align: left;
        font-weight: 600;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }

    .admin-table td {
        padding: 14px 16px;
        border-bottom: 1px solid rgba(255,255,255,0.05);
    }

    .admin-table tr:hover {
        background: rgba(255,255,255,0.02);
    }

    .badge {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .bg-success { background: rgba(16, 185, 129, 0.2); color: #10b981; }
    .bg-danger { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
    .bg-warning { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
    }
</style>
@endsection
