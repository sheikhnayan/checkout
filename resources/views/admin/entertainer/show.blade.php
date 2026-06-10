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
.table-responsive {
    padding-bottom: 20px;
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
            <p class="mb-1"><strong>Legal Name:</strong> {{ $entertainer->w9Form?->full_name ?? 'Not provided' }}</p>
            <p class="mb-1"><strong>Club:</strong> <span style="color:#f8fafc;">{{ $entertainer->website->name ?? 'N/A' }}</span></p>
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
                <button type="submit" class="btn btn-outline-primary">Update Fee</button>
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

            <!-- Stat Cards -->
            @php
                $pendingComm = collect($transactions)->sum(function($t) {
                    return $t->entertainer_commission_status === 'pending' ? (float)($t->entertainer_commission_amount ?? 0) : 0;
                });
                $availableComm = collect($transactions)->sum(function($t) {
                    $status = $t->entertainer_commission_status ?? 'pending';
                    return ($status === 'approved' || $status === 'paid') ? (float)($t->entertainer_commission_amount ?? 0) : 0;
                });
                $totalComm = collect($transactions)->sum(function($t) {
                    return (float)($t->entertainer_commission_amount ?? 0);
                });
            @endphp
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px;margin-bottom:20px;">
                <div style="background:rgba(249,115,22,0.1);border:1px solid rgba(249,115,22,0.3);padding:12px;border-radius:8px;">
                    <div style="font-size:0.75rem;font-weight:600;opacity:0.6;margin-bottom:4px">Pending Commission</div>
                    <div style="font-size:1.4rem;font-weight:700">${{ number_format($pendingComm, 2) }}</div>
                </div>
                <div style="background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.3);padding:12px;border-radius:8px;">
                    <div style="font-size:0.75rem;font-weight:600;opacity:0.6;margin-bottom:4px">Available Now</div>
                    <div style="font-size:1.4rem;font-weight:700">${{ number_format($availableComm, 2) }}</div>
                </div>
                <div style="background:rgba(59,130,246,0.1);border:1px solid rgba(59,130,246,0.3);padding:12px;border-radius:8px;">
                    <div style="font-size:0.75rem;font-weight:600;opacity:0.6;margin-bottom:4px">Lifetime Earned</div>
                    <div style="font-size:1.4rem;font-weight:700">${{ number_format($totalComm, 2) }}</div>
                </div>
            </div>

            @if(!empty($transactions) && count($transactions) > 0)
                <!-- Export Selected Button -->
                <div class="mb-3">
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="exportSelectedBtn" style="display:none;">
                        <i class="fas fa-download me-2"></i>Export Selected
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-hover" id="entertainerTransactionTable">
                        <thead class="table-light">
                            <tr>
                                <th style="width:30px;"><input type="checkbox" id="selectAllTxn"></th>
                                <th>Confirmation #</th>
                                <th>Package/Event</th>
                                <th>Payment Status</th>
                                <th>Due Amount</th>
                                <th>Reservation Status</th>
                                <th>Reservation Date</th>
                                <th>Entry Status</th>
                                <th>Commission</th>
                                <th>Booking Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $transaction)
                                @php
                                    $paidAmount = (float)($transaction->actual_total ?? $transaction->total ?? 0);
                                    $totalAmount = (float)($transaction->total ?? 0);
                                    $dueAmount = $totalAmount - $paidAmount;
                                    $paymentStatus = $paidAmount >= $totalAmount ? 'Paid' : ($paidAmount > 0 ? 'Partial' : 'Pending');

                                    $reservationDate = optional($transaction->package_use_date);
                                    $isToday = $reservationDate && $reservationDate->isToday();
                                    $isFuture = $reservationDate && $reservationDate->isFuture();

                                    $reservationStatusValue = 'Upcoming';
                                    $reservationStatusColor = '#3b82f6';
                                    $statusEmoji = '🟦';

                                    if ($reservationDate && $reservationDate->isValid()) {
                                        if ($reservationDate->isToday()) {
                                            $reservationStatusValue = 'Today';
                                        } elseif ($reservationDate->isFuture()) {
                                            $reservationStatusValue = 'Upcoming';
                                        } else {
                                            if ($transaction->checked_in_status) {
                                                $reservationStatusValue = 'Checked In';
                                                $reservationStatusColor = '#10b981';
                                                $statusEmoji = '🟩';
                                            } else {
                                                $reservationStatusValue = 'No Show';
                                                $reservationStatusColor = '#f97316';
                                                $statusEmoji = '🟧';
                                            }
                                        }
                                    }

                                    if ($transaction->status == 2) {
                                        $reservationStatusValue = 'Refunded';
                                        $reservationStatusColor = '#6b7280';
                                    } elseif ($transaction->status == 0) {
                                        $reservationStatusValue = 'Cancelled';
                                        $reservationStatusColor = '#ef4444';
                                    }

                                    $commAmount = (float)($transaction->entertainer_commission_amount ?? 0);
                                    $commDisplay = ($commAmount == intval($commAmount)) ? number_format($commAmount, 0) : number_format($commAmount, 2);
                                    $status = $transaction->entertainer_commission_status ?? 'pending';
                                    $holdUntil = $transaction->entertainer_commission_hold_until;

                                    $commissionText = '$' . $commDisplay;
                                    if ($status === 'pending' && $holdUntil) {
                                        $daysRemaining = (int)now()->diffInDays($holdUntil, false);
                                        if ($daysRemaining <= 0) {
                                            $commissionText .= ' (Available now)';
                                        } else {
                                            $commissionText .= ' (Available in ' . abs($daysRemaining) . ' days)';
                                        }
                                    } elseif ($status === 'paid') {
                                        $commissionText .= ' (Paid out)';
                                    }
                                @endphp
                                <tr data-transaction-id="{{ $transaction->id }}">
                                    <td><input type="checkbox" class="txn-row-check" value="{{ $transaction->id }}"></td>
                                    <td><strong>#{{ str_pad($transaction->id, 3, '0', STR_PAD_LEFT) }}</strong></td>
                                    <td>
                                        <div style="font-weight:600;">{{ $transaction->type === 'package' ? ($transaction->package_table_label ?: 'Package') : 'Reservation' }}</div>
                                        <div style="font-size:0.85rem;color:rgba(0,0,0,0.6);margin-top:4px;">{{ optional($transaction->website)->name ?? 'N/A' }}</div>
                                    </td>
                                    <td>
                                        @if($paymentStatus === 'Paid')
                                            <span class="badge bg-success">{{ $paymentStatus }}</span>
                                        @elseif($paymentStatus === 'Partial')
                                            <span class="badge bg-warning text-dark">{{ $paymentStatus }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $paymentStatus }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($dueAmount > 0)
                                            <span style="color:#dc2626;font-weight:600;">${{ number_format($dueAmount, 2) }}</span>
                                        @else
                                            <span style="opacity:0.4;">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span style="background:{{ $reservationStatusColor }};color:white;padding:4px 8px;border-radius:4px;font-weight:600;font-size:0.8rem;">{{ $statusEmoji }} {{ $reservationStatusValue }}</span>
                                    </td>
                                    <td>
                                        @if($reservationDate && $reservationDate->isValid())
                                            @if($isToday)
                                                <div style="font-size:0.95rem">🔥 Today</div>
                                            @elseif($isFuture)
                                                <div style="font-size:0.9rem">🗓️ {{ $reservationDate->format('M d, Y') }}</div>
                                            @else
                                                <div style="font-size:0.85rem;opacity:0.7">✓ {{ $reservationDate->format('M d, Y') }}</div>
                                            @endif
                                        @else
                                            <span style="opacity:0.3;">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($transaction->checked_in_status)
                                            <span class="badge bg-success">Redeemed</span>
                                        @else
                                            <span class="badge bg-secondary">Not Redeemed</span>
                                        @endif
                                    </td>
                                    <td style="font-weight:600;font-size:0.9rem;">{{ $commissionText }}</td>
                                    <td><small>{{ optional($transaction->created_at)->format('M d, Y') }}</small></td>
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
            <div class="modal-footer" id="transactionModalFooter">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="downloadTransactionPdfBtn" onclick="downloadTransactionPdf()">
                    <i class="fas fa-file-pdf"></i> Download PDF
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
<script>
let currentTransactionId = null;

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

    currentTransactionId = transactionId;

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

window.downloadTransactionPdf = function() {
    if (!currentTransactionId) {
        alert('Unable to download: Transaction ID not found');
        return;
    }

    window.location.href = '/admins/transaction/' + currentTransactionId + '/pdf';
};

// Checkbox functionality
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAllTxn');
    const rowCheckboxes = document.querySelectorAll('.txn-row-check');
    const exportBtn = document.getElementById('exportSelectedBtn');

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            rowCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateExportButton();
        });
    }

    rowCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateExportButton();
            // Update select all checkbox state
            if (selectAllCheckbox) {
                const allChecked = Array.from(rowCheckboxes).every(cb => cb.checked);
                const someChecked = Array.from(rowCheckboxes).some(cb => cb.checked);
                selectAllCheckbox.checked = allChecked;
                selectAllCheckbox.indeterminate = someChecked && !allChecked;
            }
        });
    });

    function updateExportButton() {
        const selectedCount = document.querySelectorAll('.txn-row-check:checked').length;
        if (exportBtn) {
            exportBtn.style.display = selectedCount > 0 ? 'inline-block' : 'none';
        }
    }

    if (exportBtn) {
        exportBtn.addEventListener('click', function() {
            exportSelectedTransactions();
        });
    }
});

function exportSelectedTransactions() {
    const selected = Array.from(document.querySelectorAll('.txn-row-check:checked')).map(cb => cb.value);

    if (selected.length === 0) {
        alert('Please select at least one transaction to export');
        return;
    }

    const table = document.getElementById('entertainerTransactionTable');
    const headers = [];
    const rows = [];

    // Get headers (skip checkbox column)
    table.querySelectorAll('thead th').forEach((th, index) => {
        if (index > 0) { // Skip checkbox column
            headers.push(th.textContent.trim());
        }
    });

    // Get rows for selected transactions
    selected.forEach(txnId => {
        const row = table.querySelector(`tr[data-transaction-id="${txnId}"]`);
        if (row) {
            const rowData = [];
            row.querySelectorAll('td').forEach((td, index) => {
                if (index > 0) { // Skip checkbox column
                    rowData.push(td.textContent.trim());
                }
            });
            rows.push(rowData);
        }
    });

    // Create CSV
    const csv = [headers.map(h => `"${h}"`).join(',')];
    rows.forEach(row => {
        csv.push(row.map(cell => `"${cell}"`).join(','));
    });

    // Download CSV
    const blob = new Blob([csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'transactions.csv';
    link.click();
    URL.revokeObjectURL(link.href);
}
</script>
@endpush    </div>
</div>
@endsection
