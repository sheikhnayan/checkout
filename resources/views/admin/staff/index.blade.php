@extends('admin.main')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h4 class="mb-0">Current Staff Applications</h4>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge bg-label-info d-inline-flex align-items-center gap-1">
                        <i class="bx bx-sort-alt-2"></i>
                        Sort by type
                    </span>
                    <a href="{{ route('admin.staff.index', ['type' => 'affiliate', 'status' => 'pending']) }}" class="btn btn-sm {{ $type === 'affiliate' ? 'btn-primary' : 'btn-outline-primary' }} d-inline-flex align-items-center gap-1">
                        <i class="bx bx-bullhorn"></i>
                        Promoters
                    </a>
                    <a href="{{ route('admin.staff.index', ['type' => 'entertainer', 'status' => 'pending']) }}" class="btn btn-sm {{ $type === 'entertainer' ? 'btn-primary' : 'btn-outline-primary' }} d-inline-flex align-items-center gap-1">
                        <i class="bx bx-star"></i>
                        Entertainers
                    </a>
                </div>
            </div>

            <div class="d-flex gap-2 mb-3 flex-wrap">
                <span class="badge bg-label-info d-inline-flex align-items-center gap-1">
                    <i class="bx bx-sort-alt-2"></i>
                    Filter by status
                </span>
                <a href="{{ route('admin.staff.index', ['type' => $type, 'status' => 'pending']) }}" class="btn btn-sm {{ $status === 'pending' ? 'btn-warning' : 'btn-outline-warning' }} d-inline-flex align-items-center gap-1">
                    <i class="bx bx-time-five"></i>
                    Pending
                </a>
                <a href="{{ route('admin.staff.index', ['type' => $type, 'status' => 'approved']) }}" class="btn btn-sm {{ $status === 'approved' ? 'btn-success' : 'btn-outline-success' }} d-inline-flex align-items-center gap-1">
                    <i class="bx bx-check-circle"></i>
                    Approved
                </a>
                <a href="{{ route('admin.staff.index', ['type' => $type, 'status' => 'rejected']) }}" class="btn btn-sm {{ $status === 'rejected' ? 'btn-danger' : 'btn-outline-danger' }} d-inline-flex align-items-center gap-1">
                    <i class="bx bx-x-circle"></i>
                    Rejected
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        @if($type === 'entertainer')
                            <th>Club</th>
                        @endif
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($staffList as $staff)
                        <tr>
                            <td>{{ $staff->display_name }}</td>
                            <td>{{ $staff->user->email }}</td>
                            @if($type === 'entertainer')
                                <td>{{ $staff->website->name ?? 'N/A' }}</td>
                            @endif
                            <td><span class="badge bg-{{ $staff->status === 'approved' ? 'success' : ($staff->status === 'pending' ? 'warning' : 'danger') }}">{{ ucfirst($staff->status) }}</span></td>
                            <td>{{ $staff->created_at->format('M d, Y') }}</td>
                            <td>
                                <a href="{{ route('admin.staff.show', ['type' => $type, 'id' => $staff->id]) }}" class="btn btn-sm btn-primary">Manage</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="{{ $type === 'entertainer' ? '6' : '5' }}" class="text-center">No staff submissions found.</td></tr>
                    @endforelse
                </tbody>
            </table>

            {{ $staffList->links() }}
        </div>
    </div>
</div>
@endsection
