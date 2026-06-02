@extends('admin.main')

@push('styles')
<style>
#transactionModal .modal-header { background: #0f172a; border-bottom: 1px solid #1e293b; }
#transactionModal .modal-content,
#transactionModal .modal-body { background: #0f172a; }
#transactionModal .modal-footer { background: #0f172a; border-top: 1px solid #1e293b; }
#transactionModal .modal-title { color: #f8fafc !important; }
#transactionModal .btn-close { filter: invert(1) grayscale(100%); }
#transactionModal .list-group-item {
    background: #0f172a;
    border-color: #1e293b;
    color: #f8fafc !important;
}
#transactionModal .list-group-item strong,
#transactionModal .list-group-item span,
#transactionModal .list-group-item a {
    color: #f8fafc !important;
}
#transactionModalBody {
    max-height: 600px;
    overflow-y: auto;
}
</style>
@endpush

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
            <h4>{{ $affiliate->display_name ?: $affiliate->user->name }}</h4>
            <p class="mb-1"><strong>Email:</strong> {{ $affiliate->user->email }}</p>
            <p class="mb-1"><strong>Status:</strong> {{ ucfirst($affiliate->status) }}</p>
            <p class="mb-1"><strong>Default Commission:</strong> {{ number_format((float) ($affiliate->default_commission_percentage ?? 0), 2) }}%</p>
            <p class="mb-3"><strong>Public Page:</strong> <a href="{{ route('affiliate.public', $affiliate->slug) }}" target="_blank">{{ route('affiliate.public', $affiliate->slug) }}</a></p>

            @if($affiliate->status !== 'approved')
                <form method="POST" action="{{ route('admin.affiliate.approve', $affiliate->id) }}" class="d-flex gap-2 align-items-end mb-3">
                    @csrf
                    <div>
                        <label class="form-label">Default Commission % <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The commission percentage this affiliate earns from referred bookings. Set when approving."></i></label>
                        <input type="number" min="0" max="100" step="0.01" name="default_commission_percentage" class="form-control" value="{{ old('default_commission_percentage', $affiliate->default_commission_percentage) }}" required>
                    </div>
                    <button type="submit" class="btn btn-success">Approve</button>
                </form>
            @endif

            <form method="POST" action="{{ route('admin.affiliate.commission.update', $affiliate->id) }}" class="d-flex gap-2 align-items-end mb-3">
                @csrf
                <div>
                    <label class="form-label">Change Commission (%) <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Update the commission percentage this affiliate earns from referred bookings."></i></label>
                    <input type="number" min="0" max="100" step="0.01" name="default_commission_percentage" class="form-control" value="{{ old('default_commission_percentage', $affiliate->default_commission_percentage) }}" required>
                </div>
                <button type="submit" class="btn btn-outline-primary">Update Commission</button>
            </form>

            @if($affiliate->status === 'approved')
                <form method="POST" action="{{ route('admin.affiliate.unapprove', $affiliate->id) }}" class="mb-3" onsubmit="return confirm('Unapprove this affiliate? They will lose access until approved again.');">
                    @csrf
                    <button type="submit" class="btn btn-warning">Unapprove</button>
                </form>
            @endif

            @if($affiliate->status !== 'rejected')
                <form method="POST" action="{{ route('admin.affiliate.reject', $affiliate->id) }}" class="mb-2">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label">Rejection Reason (optional) <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Optional note explaining why this affiliate application was rejected. Visible to the affiliate."></i></label>
                        <textarea name="rejection_reason" rows="2" class="form-control"></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger">Reject</button>
                </form>
            @endif
        </div>

        <div class="card p-4 mb-4">
            <h5 class="mb-3">Assign Clubs / Websites</h5>
            <form method="POST" action="{{ route('admin.affiliate.packages.update', $affiliate->id) }}">
                @csrf
                @foreach($websites as $website)
                    <div class="border rounded p-3 mb-3">
                        <label class="form-check-label d-flex align-items-center justify-content-between">
                            <span>
                                <input class="form-check-input me-2" type="checkbox" name="website_ids[]" value="{{ $website->id }}" {{ in_array($website->id, $selectedWebsiteIds ?? []) ? 'checked' : '' }}>
                                {{ $website->name }}
                            </span>
                            <span class="text-muted" style="font-size:12px;">{{ $website->packages_count }} active packages</span>
                        </label>
                    </div>
                @endforeach

                <button type="submit" class="btn btn-primary">Save Club Access</button>
            </form>
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
                                <th>Commission Amount</th>
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
                                    <td>${{ number_format((float) ($transaction->affiliate_commission_amount ?? 0), 2) }}</td>
                                    <td>
                                        @php
                                            $status = $transaction->affiliate_commission_status ?? 'pending';
                                            $badgeColor = $status === 'paid' ? 'success' : ($status === 'pending' ? 'warning' : ($status === 'reversed' ? 'danger' : 'secondary'));
                                        @endphp
                                        <span class="badge bg-{{ $badgeColor }}">{{ ucfirst($status) }}</span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary" title="View Details" data-bs-toggle="modal" data-bs-target="#transactionModal" onclick="loadTransactionDetails({{ $transaction->id }})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted">No transactions found for this affiliate.</p>
            @endif
        </div>
    </div>
</div>

<!-- Transaction Details Modal -->
<div class="modal fade" id="transactionModal" tabindex="-1" aria-labelledby="transactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transactionModalLabel">Transaction Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="transactionModalBody">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
window.loadTransactionDetails = function(transactionId) {
    const modalBody = document.getElementById('transactionModalBody');
    modalBody.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>';

    fetch('/admins/transaction/' + transactionId + '/details')
        .then(response => response.text())
        .then(html => {
            modalBody.innerHTML = html;
        })
        .catch(error => {
            modalBody.innerHTML = '<div class="alert alert-danger">Error loading transaction details</div>';
            console.error('Error:', error);
        });
};
</script>
@endpush    </div>
</div>
@endsection
