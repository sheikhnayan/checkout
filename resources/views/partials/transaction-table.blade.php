@php
    /**
     * Reusable transaction table (matches the admin transaction pages).
     *
     * Expected variables:
     *   $transactions    - Collection of App\Models\Transaction
     *   $tableId         - unique table id (e.g. 'affiliateWalletTransactionTable')
     *   $detailsBase     - URL base for fetching details (e.g. url('/affiliate-portal/transaction'))
     *   $commissionField - 'affiliate' or 'entertainer'
     *   $emptyText       - optional empty-state text
     */
    $tableId = $tableId ?? 'transactionTable';
    $commissionField = $commissionField ?? 'affiliate';
    $emptyText = $emptyText ?? 'No transactions found.';
@endphp

<style>#{{ $tableId }} tbody tr[data-transaction-id]{cursor:pointer;}</style>

@if($transactions->count() > 0)
<div class="table-responsive">
    <table class="table table-sm table-hover" id="{{ $tableId }}">
        <thead class="table-light">
            <tr>
                <th>Confirmation #</th>
                <th>Booking Date</th>
                <th>Package/Event</th>
                <th>Payment Status</th>
                <th>Due Amount</th>
                <th>Reservation Status</th>
                <th>Reservation Date</th>
                <th>Entry Status</th>
                <th>Commission</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)
                @php
                    $paidAmount   = (float) ($transaction->actual_total ?? $transaction->total ?? 0);
                    $totalAmount  = (float) ($transaction->total ?? 0);
                    $dueAmount    = $totalAmount - $paidAmount;
                    $paymentStatus = $paidAmount >= $totalAmount ? 'Paid' : ($paidAmount > 0 ? 'Partial' : 'Pending');

                    $reservationDate = optional($transaction->package_use_date);
                    $isToday  = $reservationDate && $reservationDate->isValid() && $reservationDate->isToday();
                    $isFuture = $reservationDate && $reservationDate->isValid() && $reservationDate->isFuture();

                    $reservationStatusValue = 'Upcoming';
                    $reservationStatusColor = '#3b82f6';

                    if ($reservationDate && $reservationDate->isValid()) {
                        if ($reservationDate->isToday()) {
                            $reservationStatusValue = 'Today';
                        } elseif ($reservationDate->isFuture()) {
                            $reservationStatusValue = 'Upcoming';
                        } else {
                            if ($transaction->checked_in_status) {
                                $reservationStatusValue = 'Checked In';
                                $reservationStatusColor = '#10b981';
                            } else {
                                $reservationStatusValue = 'No Show';
                                $reservationStatusColor = '#f97316';
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

                    if ($commissionField === 'entertainer') {
                        $commAmount  = (float) ($transaction->entertainer_commission_amount ?? 0);
                        $commStatus  = $transaction->entertainer_commission_status ?? 'pending';
                        $commHold    = $transaction->entertainer_commission_hold_until;
                    } else {
                        $commAmount  = (float) ($transaction->affiliate_commission_amount ?? 0);
                        $commStatus  = $transaction->affiliate_commission_status ?? 'pending';
                        $commHold    = $transaction->affiliate_commission_hold_until;
                    }

                    $commDisplay = ($commAmount == intval($commAmount)) ? number_format($commAmount, 0) : number_format($commAmount, 2);
                    $commissionText = '$' . $commDisplay;
                    if ($commStatus === 'pending' && $commHold) {
                        $daysRemaining = (int) now()->diffInDays($commHold, false);
                        $commissionText .= $daysRemaining <= 0 ? ' (Available now)' : (' (Available in ' . abs($daysRemaining) . ' days)');
                    } elseif ($commStatus === 'paid') {
                        $commissionText .= ' (Paid out)';
                    }

                    $paymentText = $paymentStatus === 'Partial'
                        ? ('Partial ($' . number_format($paidAmount, 2) . ' paid)')
                        : $paymentStatus;
                @endphp
                <tr data-transaction-id="{{ $transaction->id }}">
                    <td><strong>#{{ str_pad($transaction->id, 3, '0', STR_PAD_LEFT) }}</strong></td>
                    <td><small>{{ optional($transaction->created_at)->format('M d, Y') }}</small></td>
                    <td>
                        <div style="font-weight:600;margin-bottom:6px;">{{ optional($transaction->website)->name ?? 'N/A' }}</div>
                        <div style="font-size:0.85rem;">{{ $transaction->type === 'package' ? ($transaction->package_table_label ?: 'Package') : 'Reservation' }}</div>
                    </td>
                    <td>
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
<p class="text-muted mb-0">{{ $emptyText }}</p>
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
                    <div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
window.loadTransactionDetails = function(transactionId) {
    var modalBody = document.getElementById('transactionModalBody');
    modalBody.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>';

    fetch('{{ $detailsBase }}/' + transactionId + '/details', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(function(response) {
            if (!response.ok) throw new Error('HTTP ' + response.status);
            return response.text();
        })
        .then(function(html) { modalBody.innerHTML = html; })
        .catch(function(error) {
            modalBody.innerHTML = '<div class="alert alert-danger mb-0">Unable to load transaction details.</div>';
            console.error('Error loading transaction details:', error);
        });
};

document.addEventListener('DOMContentLoaded', function() {
    var txnTable = document.getElementById('{{ $tableId }}');
    if (!txnTable) return;

    // Any cell in a row opens the details modal (interactive controls keep their behavior)
    txnTable.addEventListener('click', function(e) {
        if (e.target.closest('a, button, input, select, label, .dropdown')) return;
        var row = e.target.closest('tr[data-transaction-id]');
        if (!row) return;
        var viewBtn = row.querySelector('button[data-bs-target="#transactionModal"]');
        if (viewBtn) viewBtn.click();
    });

    // Preserve horizontal scroll position when the details modal opens/closes
    var scrollBox = txnTable.closest('.table-responsive');
    var detailsModal = document.getElementById('transactionModal');
    if (scrollBox && detailsModal) {
        var savedLeft = 0;
        detailsModal.addEventListener('show.bs.modal', function() { savedLeft = scrollBox.scrollLeft; });
        detailsModal.addEventListener('hidden.bs.modal', function() {
            requestAnimationFrame(function() { scrollBox.scrollLeft = savedLeft; });
        });
    }
});
</script>
@endpush
