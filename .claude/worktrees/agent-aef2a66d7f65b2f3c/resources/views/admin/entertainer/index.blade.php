@extends('admin.main')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h4 class="mb-0">Entertainer Applications</h4>
                <div class="d-flex align-items-center gap-2 flex-wrap">
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
                        <th>Name</th>
                        <th>Email</th>
                        <th>Club</th>
                        <th>Status</th>
                        <th>Wallet</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($entertainers as $entertainer)
                        <tr>
                            <td>{{ $entertainer->display_name ?: $entertainer->user->name }}</td>
                            <td>{{ $entertainer->user->email }}</td>
                            <td>{{ $entertainer->website->name ?? 'N/A' }}</td>
                            <td><span class="badge bg-{{ $entertainer->status === 'approved' ? 'success' : ($entertainer->status === 'pending' ? 'warning' : 'danger') }}">{{ ucfirst($entertainer->status) }}</span></td>
                            <td>${{ number_format($entertainer->wallet_balance, 2) }}</td>
                            <td>
                                <a href="{{ route('admin.entertainer.show', $entertainer->id) }}" class="btn btn-sm btn-primary">Manage</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center">No entertainer records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
