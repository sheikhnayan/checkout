@extends('admin.main')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h4 class="mb-0">Current Staff Applications</h4>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <form id="bulkDeleteForm" action="{{ route('admin.staff.bulk-delete') }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete the selected staff application(s)? This action cannot be undone.');">
                        @csrf
                        <input type="hidden" name="type" value="{{ $type }}">
                        <div id="bulkDeleteInputs"></div>
                        <button type="submit" id="bulkDeleteBtn" class="btn btn-sm btn-danger d-none align-items-center gap-1">
                            <i class="bx bx-trash"></i> Mass Delete (<span id="selectedCount">0</span>)
                        </button>
                    </form>
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
                        <th style="width: 40px;" class="text-center">
                            <input type="checkbox" id="selectAllStaff" class="form-check-input">
                        </th>
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
                            <td class="text-center">
                                <input type="checkbox" class="form-check-input staff-cb" value="{{ $staff->id }}">
                            </td>
                            <td>{{ $staff->display_name ?: ($staff->user->name ?? 'N/A') }}</td>
                            <td>{{ $staff->user->email ?? 'N/A' }}</td>
                            @if($type === 'entertainer')
                                <td>{{ $staff->website->name ?? 'N/A' }}</td>
                            @endif
                            <td><span class="badge bg-{{ $staff->status === 'approved' ? 'success' : ($staff->status === 'pending' ? 'warning' : 'danger') }}">{{ ucfirst($staff->status) }}</span></td>
                            <td>{{ optional($staff->created_at)->format('M d, Y') }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-1">
                                    <a href="{{ route('admin.staff.show', ['type' => $type, 'id' => $staff->id]) }}" class="btn btn-sm btn-primary">Manage</a>
                                    <form action="{{ route('admin.staff.destroy', ['type' => $type, 'id' => $staff->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this staff application?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Application">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="{{ $type === 'entertainer' ? '7' : '6' }}" class="text-center">No staff submissions found.</td></tr>
                    @endforelse
                </tbody>
            </table>

            {{ $staffList->links() }}
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAllStaff');
    const checkboxes = document.querySelectorAll('.staff-cb');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    const selectedCount = document.getElementById('selectedCount');
    const bulkDeleteInputs = document.getElementById('bulkDeleteInputs');

    function updateBulkDeleteState() {
        const checked = document.querySelectorAll('.staff-cb:checked');
        const count = checked.length;
        if (selectedCount) selectedCount.textContent = count;

        if (count > 0) {
            bulkDeleteBtn.classList.remove('d-none');
            bulkDeleteBtn.classList.add('d-inline-flex');
        } else {
            bulkDeleteBtn.classList.remove('d-inline-flex');
            bulkDeleteBtn.classList.add('d-none');
        }

        if (selectAll) {
            selectAll.checked = checkboxes.length > 0 && count === checkboxes.length;
            selectAll.indeterminate = count > 0 && count < checkboxes.length;
        }

        if (bulkDeleteInputs) {
            bulkDeleteInputs.innerHTML = '';
            checked.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = cb.value;
                bulkDeleteInputs.appendChild(input);
            });
        }
    }

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            updateBulkDeleteState();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkDeleteState);
    });
});
</script>
@endsection
