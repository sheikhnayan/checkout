@extends('admin.main')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4>{{ $staff->display_name }}</h4>
                    <p class="text-muted mb-0">Current Staff - {{ $staffType }}</p>
                </div>
                <a href="{{ route('admin.staff.index', ['type' => $type]) }}" class="btn btn-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Back to List
                </a>
            </div>

            <hr class="my-3" />

            <div class="row mb-3">
                <div class="col-md-6">
                    <p class="mb-1"><strong>Name:</strong> {{ $staff->display_name }}</p>
                    <p class="mb-1"><strong>Email:</strong> <a href="mailto:{{ $staff->user->email }}">{{ $staff->user->email }}</a></p>
                    <p class="mb-1"><strong>Status:</strong> <span class="badge bg-{{ $staff->status === 'approved' ? 'success' : ($staff->status === 'pending' ? 'warning' : 'danger') }}">{{ ucfirst($staff->status) }}</span></p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1"><strong>Staff Type:</strong> {{ $staffType }}</p>
                    @if($type === 'entertainer')
                        <p class="mb-1"><strong>Club:</strong> <span style="color:#f8fafc;">{{ $staff->website->name ?? 'N/A' }}</span></p>
                    @endif
                    <p class="mb-1"><strong>Submitted:</strong> {{ $staff->created_at->format('M d, Y H:i') }}</p>
                </div>
            </div>

            <!-- Current Staff Info -->
            <div class="alert alert-info d-flex align-items-start gap-2 mb-3">
                <i class="bx bx-info-circle" style="font-size: 1.2rem; flex-shrink: 0;"></i>
                <div>
                    <strong>Current Staff Registration</strong>
                    <p class="mb-0">This is a current staff member registration. Physical W-9 documentation is already on file. No W-9 form submission is required.</p>
                </div>
            </div>

            <!-- Action Buttons -->
            @if($staff->status === 'pending')
                <div class="mb-3">
                    <h6 class="mb-2">Approval Actions</h6>
                    <form method="POST" action="{{ route('admin.staff.approve', ['type' => $type, 'id' => $staff->id]) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="bx bx-check me-1"></i> Approve Application
                        </button>
                    </form>
                    <button type="button" class="btn btn-danger ms-2" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        <i class="bx bx-x me-1"></i> Reject Application
                    </button>
                </div>
            @else
                <div class="alert alert-info">
                    This application has already been <strong>{{ ucfirst($staff->status) }}</strong>.
                </div>
            @endif
        </div>

        <!-- Transactions Section -->
        @if(!empty($transactions) && count($transactions) > 0)
        <div class="card p-4 mt-4">
            <h5 class="mb-3">Transactions</h5>

            <!-- Stat Cards -->
            @php
                $totalComm = collect($transactions)->sum(function($t) {
                    $affComm = (float)($t->affiliate_commission_amount ?? 0);
                    $entComm = (float)($t->entertainer_commission_amount ?? 0);
                    return $affComm + $entComm;
                });
                $completedTxns = collect($transactions)->where('status', 1)->count();
            @endphp
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px;margin-bottom:20px;">
                <div style="background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.3);padding:12px;border-radius:8px;">
                    <div style="font-size:0.75rem;font-weight:600;opacity:0.6;margin-bottom:4px">Total Transactions</div>
                    <div style="font-size:1.4rem;font-weight:700">{{ count($transactions) }}</div>
                </div>
                <div style="background:rgba(56,189,248,0.1);border:1px solid rgba(56,189,248,0.3);padding:12px;border-radius:8px;">
                    <div style="font-size:0.75rem;font-weight:600;opacity:0.6;margin-bottom:4px">Completed</div>
                    <div style="font-size:1.4rem;font-weight:700">{{ $completedTxns }}</div>
                </div>
                <div style="background:rgba(59,130,246,0.1);border:1px solid rgba(59,130,246,0.3);padding:12px;border-radius:8px;">
                    <div style="font-size:0.75rem;font-weight:600;opacity:0.6;margin-bottom:4px">Total Commission</div>
                    <div style="font-size:1.4rem;font-weight:700">${{ number_format($totalComm, 2) }}</div>
                </div>
            </div>

            <!-- Export Selected Button -->
            <div class="mb-3">
                <button type="button" class="btn btn-sm btn-outline-secondary" id="exportSelectedBtn" style="display:none;">
                    <i class="fas fa-download me-2"></i>Export Selected
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-sm table-hover" id="staffTransactionTable">
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

                                $affComm = (float)($transaction->affiliate_commission_amount ?? 0);
                                $entComm = (float)($transaction->entertainer_commission_amount ?? 0);
                                $totalComissionAmount = $affComm + $entComm;
                                $commDisplay = ($totalComissionAmount == intval($totalComissionAmount)) ? number_format($totalComissionAmount, 0) : number_format($totalComissionAmount, 2);

                                $affStatus = $transaction->affiliate_commission_status ?? 'pending';
                                $entStatus = $transaction->entertainer_commission_status ?? 'pending';
                                $status = $affStatus !== 'pending' ? $affStatus : $entStatus;
                                $holdUntil = $transaction->affiliate_commission_hold_until ?? $transaction->entertainer_commission_hold_until;

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
                                    <div style="font-weight:600;margin-bottom:6px;">{{ optional($transaction->website)->name ?? 'N/A' }}</div>
                                    <div style="font-size:0.85rem;">{{ $transaction->type === 'package' ? ($transaction->package_table_label ?: 'Package') : 'Reservation' }}</div>
                                </td>
                                <td>
                                    @php
                                        $paymentText = $paymentStatus;
                                        if ($paymentStatus === 'Partial') {
                                            $paymentText = 'Partial ($' . number_format($paidAmount, 2) . ' paid)';
                                        }
                                    @endphp
                                    @if($paymentStatus === 'Paid')
                                        <span class="badge bg-success" style="font-size:0.85rem;">{{ $paymentText }}</span>
                                    @elseif($paymentStatus === 'Partial')
                                        <span class="badge bg-warning text-dark" style="font-size:0.85rem;">{{ $paymentText }}</span>
                                    @else
                                        <span class="badge bg-secondary" style="font-size:0.85rem;">{{ $paymentText }}</span>
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
                                    <span style="background:{{ $reservationStatusColor }};color:white;padding:4px 8px;border-radius:4px;font-weight:600;font-size:0.8rem;">{{ $reservationStatusValue }}</span>
                                </td>
                                <td>
                                    @if($reservationDate && $reservationDate->isValid())
                                        @if($isToday)
                                            <div style="font-size:0.9rem;font-weight:600;">Today</div>
                                        @elseif($isFuture)
                                            <div style="font-size:0.9rem;">{{ $reservationDate->format('M d, Y') }}</div>
                                        @else
                                            <div style="font-size:0.85rem;opacity:0.7;">{{ $reservationDate->format('M d, Y') }}</div>
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
        </div>
        @endif
    </div>
</div>

<!-- Reject Modal -->
@if($staff->status === 'pending')
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Application</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.staff.reject', ['type' => $type, 'id' => $staff->id]) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Rejection Reason (Optional)</label>
                        <textarea name="rejection_reason" rows="4" class="form-control" placeholder="Briefly explain why this application is being rejected..."></textarea>
                        <small class="text-muted">This will be included in the rejection email to the applicant.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Application</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

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

    const table = document.getElementById('staffTransactionTable');
    if (!table) {
        alert('Transaction table not found');
        return;
    }

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
@endpush

@endsection
