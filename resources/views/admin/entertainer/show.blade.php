@extends('admin.main')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card p-3 h-100">
                    <div class="text-muted small mb-1">Pending Amount (Hold)</div>
                    <div class="h4 mb-0 text-warning">${{ number_format((float) ($pendingAmount ?? 0), 2) }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3 h-100">
                    <div class="text-muted small mb-1">Payout Amount (Completed)</div>
                    <div class="h4 mb-0 text-success">${{ number_format((float) ($payoutAmount ?? 0), 2) }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3 h-100">
                    <div class="text-muted small mb-1">Total Earning</div>
                    <div class="h4 mb-0 text-primary">${{ number_format((float) ($totalEarning ?? 0), 2) }}</div>
                </div>
            </div>
        </div>

        <div class="card p-4 mb-4">
            <h4>{{ $entertainer->display_name ?: $entertainer->user->name }}</h4>
            <p class="mb-1"><strong>Email:</strong> {{ $entertainer->user->email }}</p>
            <p class="mb-1"><strong>Club:</strong> {{ $entertainer->website->name ?? 'N/A' }}</p>
            <p class="mb-1"><strong>Status:</strong> {{ ucfirst($entertainer->status) }}</p>
            <p class="mb-3"><strong>Default Commission:</strong> {{ number_format((float) ($entertainer->default_commission_percentage ?? 0), 2) }}%</p>
            @if($entertainer->slug)
                <p class="mb-3"><strong>Public Page:</strong> <a href="{{ route('entertainer.public', $entertainer->slug) }}" target="_blank">{{ route('entertainer.public', $entertainer->slug) }}</a></p>
            @endif

            @if($entertainer->status !== 'approved')
                <form method="POST" action="{{ route('admin.entertainer.approve', $entertainer->id) }}" class="d-flex gap-2 align-items-end mb-3">
                    @csrf
                    <div>
                        <label class="form-label">Default Commission % <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The commission percentage this entertainer earns from referred bookings. Set when approving."></i></label>
                        <input type="number" min="0" max="100" step="0.01" name="default_commission_percentage" class="form-control" value="{{ old('default_commission_percentage', $entertainer->default_commission_percentage ?? 10) }}" required>
                    </div>
                    <button type="submit" class="btn btn-success">Approve</button>
                </form>
            @endif

            <form method="POST" action="{{ route('admin.entertainer.commission.update', $entertainer->id) }}" class="d-flex gap-2 align-items-end mb-3">
                @csrf
                <div>
                    <label class="form-label">Change Commission (%) <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Update the commission percentage this entertainer earns from referred bookings."></i></label>
                    <input type="number" min="0" max="100" step="0.01" name="default_commission_percentage" class="form-control" value="{{ old('default_commission_percentage', $entertainer->default_commission_percentage ?? 10) }}" required>
                </div>
                <button type="submit" class="btn btn-outline-primary">Update Commission</button>
            </form>

            @if($entertainer->status === 'approved')
                <form method="POST" action="{{ route('admin.entertainer.unapprove', $entertainer->id) }}" class="mb-3" onsubmit="return confirm('Unapprove this entertainer? Their public page and feed visibility will be removed until approved again.');">
                    @csrf
                    <button type="submit" class="btn btn-warning">Unapprove</button>
                </form>
            @endif

            @if($entertainer->status !== 'rejected')
                <form method="POST" action="{{ route('admin.entertainer.reject', $entertainer->id) }}" class="mb-2">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label">Rejection Reason (optional) <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Optional note explaining why this entertainer application was rejected. Visible to the entertainer."></i></label>
                        <textarea name="rejection_reason" rows="2" class="form-control"></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger">Reject</button>
                </form>
            @endif
        </div>

        <div class="card p-4">
            <h5 class="mb-3">Transactions</h5>
            @if(!empty($transactions) && count($transactions) > 0)
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Confirmation #</th>
                                <th>Venue</th>
                                <th>Guest Name</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Commission Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $transaction)
                                <tr>
                                    <td><strong>{{ $transaction->transaction_id }}</strong></td>
                                    <td>{{ optional($transaction->website)->name ?? 'N/A' }}</td>
                                    <td>{{ $transaction->package_first_name }} {{ $transaction->package_last_name }}</td>
                                    <td>{{ optional($transaction->created_at)->format('M d, Y') }}</td>
                                    <td>${{ number_format((float) ($transaction->actual_total ?? 0), 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $transaction->entertainer_commission_status === 'paid' ? 'success' : ($transaction->entertainer_commission_status === 'pending' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($transaction->entertainer_commission_status ?? 'pending') }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.transaction.index', ['search' => $transaction->transaction_id]) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted">No transactions found for this entertainer.</p>
            @endif
        </div>
    </div>
</div>
@endsection
