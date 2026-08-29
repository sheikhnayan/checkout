@extends('admin.main')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h4 class="mb-0">Entertainer Applications</h4>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <form id="bulkDeleteForm" action="{{ route('admin.entertainer.bulk-delete') }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete the selected entertainer application(s)? This action cannot be undone.');">
                        @csrf
                        <div id="bulkDeleteInputs"></div>
                        <button type="submit" id="bulkDeleteBtn" class="btn btn-sm btn-danger d-none align-items-center gap-1">
                            <i class="bx bx-trash"></i> Mass Delete (<span id="selectedCount">0</span>)
                        </button>
                    </form>
                    <span class="badge bg-label-info d-inline-flex align-items-center gap-1">
                        <i class="bx bx-sort-alt-2"></i>
                        Sort by status
                    </span>
                    <a href="{{ route('admin.entertainer.index', ['status' => 'pending']) }}" class="btn btn-sm {{ $status === 'pending' ? 'btn-warning' : 'btn-outline-warning' }} d-inline-flex align-items-center gap-1">
                        <i class="bx bx-time-five"></i>
                        Pending
                    </a>
                    <a href="{{ route('admin.entertainer.index', ['status' => 'approved']) }}" class="btn btn-sm {{ $status === 'approved' ? 'btn-success' : 'btn-outline-success' }} d-inline-flex align-items-center gap-1">
                        <i class="bx bx-check-circle"></i>
                        Approved
                    </a>
                    <a href="{{ route('admin.entertainer.index', ['status' => 'rejected']) }}" class="btn btn-sm {{ $status === 'rejected' ? 'btn-danger' : 'btn-outline-danger' }} d-inline-flex align-items-center gap-1">
                        <i class="bx bx-x-circle"></i>
                        Rejected
                    </a>
                </div>
            </div>

            @if($shareClub && $shareClub->slug)
                <div class="alert alert-info d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <strong>Direct Registration Link ({{ $shareClub->name }}):</strong>
                        {{ route('entertainer.apply', ['club' => $shareClub->slug]) }}
                    </div>
                    <a href="{{ route('entertainer.apply', ['club' => $shareClub->slug]) }}" target="_blank" class="btn btn-sm btn-primary">Open Link</a>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th style="width: 40px;" class="text-center">
                            <input type="checkbox" id="selectAllEntertainers" class="form-check-input">
                        </th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Club</th>
                        <th>Status</th>
                        <th>Application Date</th>
                        @if($status === 'approved')
                            <th>Approved Date</th>
                            <th>Approved By</th>
                        @elseif($status === 'rejected')
                            <th>Rejected Date</th>
                            <th>Rejected By</th>
                        @endif
                        <th>Wallet</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($entertainers as $entertainer)
                        <tr>
                            <td class="text-center">
                                <input type="checkbox" class="form-check-input entertainer-cb" value="{{ $entertainer->id }}">
                            </td>
                            <td>{{ $entertainer->display_name ?: ($entertainer->user->name ?? 'N/A') }}</td>
                            <td>{{ $entertainer->user->email ?? 'N/A' }}</td>
                            <td>{{ $entertainer->website->name ?? 'N/A' }}</td>
                            <td><span class="badge bg-{{ $entertainer->status === 'approved' ? 'success' : ($entertainer->status === 'pending' ? 'warning' : 'danger') }}">{{ ucfirst($entertainer->status) }}</span></td>
                            <td style="white-space: nowrap;">
                                {{ optional($entertainer->created_at)->timezone('America/Los_Angeles')->format('M d, Y') }}<br>
                                <small style="opacity: 0.7;">{{ optional($entertainer->created_at)->timezone('America/Los_Angeles')->format('h:i A') }}</small>
                            </td>
                            @if($status === 'approved')
                                <td style="white-space: nowrap;">
                                    @if($entertainer->approved_at)
                                        {{ optional($entertainer->approved_at)->timezone('America/Los_Angeles')->format('M d, Y') }}<br>
                                        <small style="opacity: 0.7;">{{ optional($entertainer->approved_at)->timezone('America/Los_Angeles')->format('h:i A') }}</small>
                                    @else
                                        <span style="opacity: 0.5;">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($entertainer->approved_by_user)
                                        {{ $entertainer->approved_by_user->name ?? 'Admin' }}
                                    @else
                                        <span style="opacity: 0.5;">—</span>
                                    @endif
                                </td>
                            @elseif($status === 'rejected')
                                <td style="white-space: nowrap;">
                                    @if($entertainer->rejected_at)
                                        {{ optional($entertainer->rejected_at)->timezone('America/Los_Angeles')->format('M d, Y') }}<br>
                                        <small style="opacity: 0.7;">{{ optional($entertainer->rejected_at)->timezone('America/Los_Angeles')->format('h:i A') }}</small>
                                    @else
                                        <span style="opacity: 0.5;">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($entertainer->rejected_by_user)
                                        {{ $entertainer->rejected_by_user->name ?? 'Admin' }}
                                    @else
                                        <span style="opacity: 0.5;">—</span>
                                    @endif
                                </td>
                            @endif
                            <td>${{ number_format($entertainer->wallet_balance, 2) }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-1">
                                    <a href="{{ route('admin.entertainer.show', $entertainer->id) }}" class="btn btn-sm btn-primary">Manage</a>
                                    <form action="{{ route('admin.entertainer.destroy', $entertainer->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this entertainer application?');">
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
                        <tr><td colspan="{{ $status === 'pending' ? 8 : 10 }}" class="text-center">No entertainer records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAllEntertainers');
    const checkboxes = document.querySelectorAll('.entertainer-cb');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    const selectedCount = document.getElementById('selectedCount');
    const bulkDeleteInputs = document.getElementById('bulkDeleteInputs');

    function updateBulkDeleteState() {
        const checked = document.querySelectorAll('.entertainer-cb:checked');
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
