@extends('admin.main')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">Affiliate Applications</h4>
                <form method="GET" class="d-flex gap-2">
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </form>
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
                            <td>${{ number_format($affiliate->wallet_balance, 2) }}</td>
                            <td>
                                <a href="{{ route('admin.affiliate.show', $affiliate->id) }}" class="btn btn-sm btn-primary">Manage</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center">No affiliate records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
