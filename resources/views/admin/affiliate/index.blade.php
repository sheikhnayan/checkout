@extends('admin.main')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h4 class="mb-0">Promoter Applications</h4>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge bg-label-info d-inline-flex align-items-center gap-1">
                        <i class="bx bx-sort-alt-2"></i>
                        Sort by status
                    </span>
                    <a href="{{ route('admin.affiliate.index', ['status' => 'pending']) }}" class="btn btn-sm {{ $status === 'pending' ? 'btn-warning' : 'btn-outline-warning' }} d-inline-flex align-items-center gap-1">
                        <i class="bx bx-time-five"></i>
                        Pending
                    </a>
                    <a href="{{ route('admin.affiliate.index', ['status' => 'approved']) }}" class="btn btn-sm {{ $status === 'approved' ? 'btn-success' : 'btn-outline-success' }} d-inline-flex align-items-center gap-1">
                        <i class="bx bx-check-circle"></i>
                        Approved
                    </a>
                    <a href="{{ route('admin.affiliate.index', ['status' => 'rejected']) }}" class="btn btn-sm {{ $status === 'rejected' ? 'btn-danger' : 'btn-outline-danger' }} d-inline-flex align-items-center gap-1">
                        <i class="bx bx-x-circle"></i>
                        Rejected
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
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
                    @forelse($affiliates as $affiliate)
                        <tr>
                            <td>{{ $affiliate->display_name ?: $affiliate->user->name }}</td>
                            <td>{{ $affiliate->user->email }}</td>
                            <td><span class="badge bg-{{ $affiliate->status === 'approved' ? 'success' : ($affiliate->status === 'pending' ? 'warning' : 'danger') }}">{{ ucfirst($affiliate->status) }}</span></td>
                            <td style="white-space: nowrap;">
                                {{ optional($affiliate->created_at)->timezone('America/Los_Angeles')->format('M d, Y') }}<br>
                                <small style="opacity: 0.7;">{{ optional($affiliate->created_at)->timezone('America/Los_Angeles')->format('h:i A') }}</small>
                            </td>
                            @if($status === 'approved')
                                <td style="white-space: nowrap;">
                                    @if($affiliate->approved_at)
                                        {{ optional($affiliate->approved_at)->timezone('America/Los_Angeles')->format('M d, Y') }}<br>
                                        <small style="opacity: 0.7;">{{ optional($affiliate->approved_at)->timezone('America/Los_Angeles')->format('h:i A') }}</small>
                                    @else
                                        <span style="opacity: 0.5;">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($affiliate->approved_by_user)
                                        {{ $affiliate->approved_by_user->name ?? 'Admin' }}
                                    @else
                                        <span style="opacity: 0.5;">—</span>
                                    @endif
                                </td>
                            @elseif($status === 'rejected')
                                <td style="white-space: nowrap;">
                                    @if($affiliate->rejected_at)
                                        {{ optional($affiliate->rejected_at)->timezone('America/Los_Angeles')->format('M d, Y') }}<br>
                                        <small style="opacity: 0.7;">{{ optional($affiliate->rejected_at)->timezone('America/Los_Angeles')->format('h:i A') }}</small>
                                    @else
                                        <span style="opacity: 0.5;">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($affiliate->rejected_by_user)
                                        {{ $affiliate->rejected_by_user->name ?? 'Admin' }}
                                    @else
                                        <span style="opacity: 0.5;">—</span>
                                    @endif
                                </td>
                            @endif
                            <td>${{ number_format($affiliate->wallet_balance, 2) }}</td>
                            <td>
                                <a href="{{ route('admin.affiliate.show', $affiliate->id) }}" class="btn btn-sm btn-primary">Manage</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="{{ $status === 'pending' ? 6 : 8 }}" class="text-center">No affiliate records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
