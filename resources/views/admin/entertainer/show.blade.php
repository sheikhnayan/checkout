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

        <!-- W-9 Form Status -->
        @if($entertainer->w9Form)
        <div class="card p-4 mb-4" style="border-left: 4px solid {{ $entertainer->w9Form->status === 'approved' ? '#10b981' : ($entertainer->w9Form->status === 'submitted' ? '#f59e0b' : '#6b7280') }};">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h5 class="mb-2">W-9 Form Status</h5>
                    <p class="mb-0">
                        @if($entertainer->w9Form->status === 'approved')
                            <span class="badge bg-success">✓ Approved</span>
                        @elseif($entertainer->w9Form->status === 'submitted')
                            <span class="badge bg-warning text-dark">⏳ Pending Review</span>
                        @elseif($entertainer->w9Form->status === 'rejected')
                            <span class="badge bg-danger">✗ Rejected</span>
                        @else
                            <span class="badge bg-secondary">◯ Not Started</span>
                        @endif
                    </p>
                </div>
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#w9Modal" onclick="loadW9Form({{ $entertainer->w9Form->id }})">
                    <i class="fas fa-file-invoice"></i> View W-9 Form
                </button>
            </div>
        </div>
        @endif

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
                                    <td>${{ number_format((float) ($transaction->entertainer_commission_amount ?? 0), 2) }}</td>
                                    <td>
                                        @php
                                            $status = $transaction->entertainer_commission_status ?? 'pending';
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
                <p class="text-muted">No transactions found for this entertainer.</p>
            @endif
        </div>
    </div>
</div>

<!-- W-9 Form Modal -->
<div class="modal fade" id="w9Modal" tabindex="-1" aria-labelledby="w9ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="w9ModalLabel">W-9 Form Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="w9ModalBody">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
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
window.loadW9Form = function(formId) {
    const modalBody = document.getElementById('w9ModalBody');
    modalBody.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>';

    fetch('/admin/w9/' + formId + '/modal')
        .then(response => response.text())
        .then(html => {
            modalBody.innerHTML = html;
        })
        .catch(error => {
            modalBody.innerHTML = '<div class="alert alert-danger">Error loading W-9 form details</div>';
            console.error('Error:', error);
        });
};

window.downloadW9PDF = function(formId) {
    window.location.href = '/admin/w9/' + formId + '/download-pdf';
};

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
