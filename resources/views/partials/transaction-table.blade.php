@php
    /**
     * Reusable transaction table (matches the admin transaction pages) with
     * date-range filtering, row selection and CSV export.
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

<style>
#{{ $tableId }} tbody tr[data-transaction-id]{cursor:pointer;}
#{{ $tableId }}_wrap .txn-controls{display:flex;flex-wrap:wrap;align-items:flex-end;gap:10px;margin-bottom:14px;}
#{{ $tableId }}_wrap .txn-controls label{display:block;font-size:0.72rem;font-weight:600;opacity:0.7;margin-bottom:3px;}
#{{ $tableId }}_wrap .txn-controls .form-control-sm{min-width:140px;}
</style>

<div id="{{ $tableId }}_wrap">
@if($transactions->count() > 0)
    <div class="txn-controls">
        <div>
            <label>From</label>
            <input type="date" class="form-control form-control-sm txn-filter-from">
        </div>
        <div>
            <label>To</label>
            <input type="date" class="form-control form-control-sm txn-filter-to">
        </div>
        <div>
            <label>Search</label>
            <input type="text" class="form-control form-control-sm txn-filter-search" placeholder="Name, email, confirmation…">
        </div>
        <button type="button" class="btn btn-sm btn-outline-secondary txn-filter-clear">Clear</button>
        <div class="ms-auto d-flex align-items-center gap-2">
            <span class="text-muted small txn-result-count"></span>
            <button type="button" class="btn btn-sm btn-outline-primary txn-export-csv">Export CSV</button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-sm table-hover no-datatable" id="{{ $tableId }}">
            <thead class="table-light">
                <tr>
                    <th style="width:30px;"><input type="checkbox" class="txn-select-all"></th>
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
                    <tr data-transaction-id="{{ $transaction->id }}" data-date="{{ optional($transaction->created_at)->format('Y-m-d') }}">
                        <td><input type="checkbox" class="txn-row-check" value="{{ $transaction->id }}"></td>
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
    var wrap = document.getElementById('{{ $tableId }}_wrap');
    var txnTable = document.getElementById('{{ $tableId }}');
    if (!wrap || !txnTable) return;

    var rows = Array.prototype.slice.call(txnTable.querySelectorAll('tbody tr[data-transaction-id]'));
    var fromEl = wrap.querySelector('.txn-filter-from');
    var toEl = wrap.querySelector('.txn-filter-to');
    var searchEl = wrap.querySelector('.txn-filter-search');
    var clearBtn = wrap.querySelector('.txn-filter-clear');
    var selectAll = txnTable.querySelector('.txn-select-all');
    var exportBtn = wrap.querySelector('.txn-export-csv');
    var countEl = wrap.querySelector('.txn-result-count');

    function rowVisible(row) {
        var d = row.getAttribute('data-date') || '';
        var from = fromEl.value, to = toEl.value;
        if (from && (!d || d < from)) return false;
        if (to && (!d || d > to)) return false;
        var q = (searchEl.value || '').trim().toLowerCase();
        if (q && row.textContent.toLowerCase().indexOf(q) === -1) return false;
        return true;
    }

    function applyFilters() {
        var shown = 0;
        rows.forEach(function(row) {
            if (rowVisible(row)) {
                row.style.display = '';
                shown++;
            } else {
                row.style.display = 'none';
                var cb = row.querySelector('.txn-row-check');
                if (cb) cb.checked = false; // don't keep hidden rows selected
            }
        });
        if (countEl) countEl.textContent = shown + ' of ' + rows.length + ' shown';
        syncSelectAll();
        updateExportLabel();
    }

    function visibleRows() {
        return rows.filter(function(r) { return r.style.display !== 'none'; });
    }

    function selectedRows() {
        return visibleRows().filter(function(r) {
            var cb = r.querySelector('.txn-row-check');
            return cb && cb.checked;
        });
    }

    function syncSelectAll() {
        if (!selectAll) return;
        var vis = visibleRows();
        var checked = vis.filter(function(r) { var cb = r.querySelector('.txn-row-check'); return cb && cb.checked; });
        selectAll.checked = vis.length > 0 && checked.length === vis.length;
        selectAll.indeterminate = checked.length > 0 && checked.length < vis.length;
    }

    function updateExportLabel() {
        if (!exportBtn) return;
        var n = selectedRows().length;
        exportBtn.textContent = n > 0 ? ('Export Selected (' + n + ')') : 'Export CSV';
    }

    if (fromEl) fromEl.addEventListener('change', applyFilters);
    if (toEl) toEl.addEventListener('change', applyFilters);
    if (searchEl) searchEl.addEventListener('input', applyFilters);
    if (clearBtn) clearBtn.addEventListener('click', function() {
        fromEl.value = ''; toEl.value = ''; searchEl.value = '';
        applyFilters();
    });

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            var on = this.checked;
            visibleRows().forEach(function(r) {
                var cb = r.querySelector('.txn-row-check');
                if (cb) cb.checked = on;
            });
            updateExportLabel();
        });
    }
    txnTable.addEventListener('change', function(e) {
        if (e.target && e.target.classList.contains('txn-row-check')) {
            syncSelectAll();
            updateExportLabel();
        }
    });

    if (exportBtn) {
        exportBtn.addEventListener('click', function() {
            var exportRows = selectedRows();
            if (exportRows.length === 0) exportRows = visibleRows(); // none selected -> export filtered set
            if (exportRows.length === 0) { alert('Nothing to export.'); return; }

            var headers = [];
            var ths = txnTable.querySelectorAll('thead th');
            // skip first (checkbox) and last (actions) columns
            for (var i = 1; i < ths.length - 1; i++) headers.push(ths[i].textContent.trim());

            var lines = [headers.map(csvCell).join(',')];
            exportRows.forEach(function(row) {
                var tds = row.querySelectorAll('td');
                var data = [];
                for (var j = 1; j < tds.length - 1; j++) {
                    data.push(tds[j].textContent.replace(/\s+/g, ' ').trim());
                }
                lines.push(data.map(csvCell).join(','));
            });

            var blob = new Blob(['﻿' + lines.join('\r\n')], { type: 'text/csv;charset=utf-8;' });
            var link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'transactions-' + new Date().toISOString().slice(0, 10) + '.csv';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(link.href);
        });
    }

    function csvCell(v) { return '"' + String(v == null ? '' : v).replace(/"/g, '""') + '"'; }

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

    applyFilters();
});
</script>
@endpush
