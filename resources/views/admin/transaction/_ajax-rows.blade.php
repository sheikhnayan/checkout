@forelse($data as $item)
@php
    try {
        $affiliateName = null;
        if (!empty($item->affiliate_id) && !empty($item->affiliate))
            $affiliateName = $item->affiliate->display_name ?: optional($item->affiliate->user)->name ?: ('affiliate #' . $item->affiliate_id);
        elseif (!empty($item->entertainer_id) && !empty($item->entertainer))
            $affiliateName = $item->entertainer->display_name ?: optional($item->entertainer->user)->name ?: ('Entertainer #' . $item->entertainer_id);

        $commission  = (float)($item->affiliate_commission_amount ?? 0) + (float)($item->entertainer_commission_amount ?? 0);
        $packageName = $item->type === 'package' ? ($item->package_table_label ?: 'Package') : 'Reservation';
        $venueName   = $item->website->name ?? ($item->event->name ?? 'N/A');

        $cartItems = is_array($item->cart_items ?? null) ? $item->cart_items : json_decode($item->cart_items ?? '[]', true);
        $packageDetails = collect($cartItems)->map(function ($ci) {
            if (!is_array($ci)) {
                return null;
            }
            $name = trim((string) ($ci['package_name'] ?? $ci['packageName'] ?? $ci['pkgName'] ?? ''));
            if ($name === '') {
                return null;
            }
            $quantity = max(1, (int) ($ci['guests'] ?? $ci['quantity'] ?? 1));
            return $name . ': ' . $quantity . ' ' . ($quantity === 1 ? 'guest' : 'guests');
        })->filter()->values();

        $packageDetailsText = $packageDetails->isNotEmpty()
            ? ($packageDetails->count() > 1 ? $packageDetails->implode(', ') : $packageDetails->first())
            : $packageName;

        // Payout lifecycle
        $commStatus = $item->affiliate_commission_status ?? $item->entertainer_commission_status ?? null;
        $holdUntil  = $item->affiliate_commission_hold_until ?? $item->entertainer_commission_hold_until ?? null;
        $now        = \Carbon\Carbon::now();
        $isEligible = $holdUntil && $holdUntil->lte($now);
        $rowError = null;
    } catch (\Exception $e) {
        $affiliateName = '';
        $commission = 0;
        $packageName = 'N/A';
        $venueName = 'N/A';
        $packageDetailsText = 'N/A';
        $commStatus = null;
        $holdUntil = null;
        $now = \Carbon\Carbon::now();
        $isEligible = false;
        $rowError = $e->getMessage();
    }
@endphp
<tr data-row-id="{{ $item->id }}" data-row-error="{{ $rowError ?? '' }}">
    <td><input type="checkbox" class="row-check" value="{{ $item->id }}"></td>
    <td class="txn-order-id">#{{ str_pad($item->id, 3, '0', STR_PAD_LEFT) }}</td>
    @php
        $transactionWebsite = $item->website ?: optional($item->event)->website ?: optional($item->package)->website;
        $purchaseTimezone = optional($transactionWebsite)->resolved_timezone ?? 'America/Los_Angeles';
        $purchaseAtLocal = optional($item->created_at)->copy()?->timezone($purchaseTimezone);
    @endphp
    <td>
        <div class="txn-date-main">{{ $purchaseAtLocal?->format('M d, Y') ?? '-' }}</div>
        <div class="txn-date-time">{{ $purchaseAtLocal?->format('h:i A T') ?? '-' }}</div>
    </td>
    <td class="txn-confirmation-num">{{ $item->transaction_id ?? 'N/A' }}</td>
    <td class="txn-pkg-name">
        <div style="font-size:0.85rem;font-weight:600;margin-bottom:8px;">{{ $venueName }}</div>
        <span style="font-size:0.75rem;color:rgba(255,255,255,0.5);">{{ $packageDetailsText }}</span>
    </td>
    <td>
        @php
            $sourceText = 'Direct';
            $sourceBadgeColor = '#6b7280';
            if (!empty($item->affiliate_id) && !empty($item->affiliate)) {
                $sourceText = $item->affiliate->display_name ?: optional($item->affiliate->user)->name ?: 'Affiliate #' . $item->affiliate_id;
                $sourceBadgeColor = '#8b5cf6';
            } elseif (!empty($item->entertainer_id) && !empty($item->entertainer)) {
                $sourceText = $item->entertainer->display_name ?: optional($item->entertainer->user)->name ?: 'Entertainer #' . $item->entertainer_id;
                $sourceBadgeColor = '#ec4899';
            }
        @endphp
        <span style="background:{{ $sourceBadgeColor }};color:white;padding:4px 10px;border-radius:4px;font-size:0.85rem;font-weight:600;display:inline-block;max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $sourceText }}</span>
    </td>
    <td>
        <div class="txn-customer-name">{{ $item->package_first_name }} {{ $item->package_last_name }}</div>
        <div class="txn-customer-email">{{ $item->package_email }}</div>
    </td>
    <td class="txn-amount">${{ number_format((float)$item->total, 2) }}</td>
    <td>
        @php
            $paidAmount = (float)($item->actual_total ?? $item->total ?? 0);
            $totalAmount = (float)($item->total ?? 0);
            $paymentStatus = $paidAmount >= $totalAmount ? 'Paid' : ($paidAmount > 0 ? 'Partial' : 'Pending');
        @endphp
        <span class="badge-{{ $paymentStatus === 'Paid' ? 'completed' : ($paymentStatus === 'Partial' ? 'warning' : 'canceled') }}" style="font-size:0.85rem;">{{ $paymentStatus }}</span>
    </td>
    <td>
        @php $cardLast4 = trim((string) ($item->payment_card_last4 ?? '')); @endphp
        <span style="font-size:0.85rem;font-weight:600;color:{{ $cardLast4 !== '' ? '#fff' : 'rgba(255,255,255,0.3)' }};">{{ $cardLast4 !== '' ? '**** ' . $cardLast4 : '-' }}</span>
    </td>
    <td class="txn-amount">
        @php $dueAmount = $totalAmount - $paidAmount; @endphp
        @if($dueAmount > 0)
            <span style="color:#ef4444;font-weight:600;">${{ number_format($dueAmount, 2) }}</span>
        @else
            <span style="color:rgba(255,255,255,0.3);">-</span>
        @endif
    </td>
    @php
        $reservationDate = null;
        try {
            if (isset($item->package_use_date) && $item->package_use_date) {
                $reservationDate = $item->package_use_date;
            }
        } catch (\Exception $e) {
            $reservationDate = null;
        }
        $nowPacific = \Carbon\Carbon::now('America/Los_Angeles');
        $laToday = $nowPacific->copy()->startOfDay();
        $reservationDatePacific = null;
        if ($reservationDate) {
            try {
                $reservationDateString = $reservationDate instanceof \Carbon\CarbonInterface
                    ? $reservationDate->format('Y-m-d')
                    : trim((string) $reservationDate);
                if ($reservationDateString !== '') {
                    $reservationDatePacific = \Carbon\Carbon::createFromFormat('Y-m-d', $reservationDateString, 'America/Los_Angeles')->startOfDay();
                }
            } catch (\Throwable $e) {
                $reservationDatePacific = null;
            }
        }
        $reservationStatusValue = 'Upcoming';
        $reservationStatusClass = 'badge-reservation-upcoming';
        if ($item->checked_in_status) {
            $reservationStatusValue = 'Checked In';
            $reservationStatusClass = 'badge-reservation-checked-in';
        } elseif ($reservationDatePacific) {
            if ($reservationDatePacific->equalTo($laToday)) {
                $reservationStatusValue = 'Today';
                $reservationStatusClass = 'badge-reservation-today';
            } elseif ($reservationDatePacific->lessThan($laToday)) {
                $reservationStatusValue = 'Past';
                $reservationStatusClass = 'badge-reservation-refunded';
            }
        }
        if ($item->status == 2) {
            $reservationStatusValue = 'Refunded';
            $reservationStatusClass = 'badge-reservation-refunded';
        } elseif ($item->status == 0) {
            $reservationStatusValue = 'Cancelled';
            $reservationStatusClass = 'badge-reservation-cancelled';
        }
    @endphp
    <td><span class="{{ $reservationStatusClass }}">{{ $reservationStatusValue }}</span></td>
    <td>{{ $reservationDatePacific?->format('M d, Y') ?? '-' }}</td>
    <td>
        @if($item->checked_in_status)
            <span class="badge-checkin-yes">Redeemed</span>
        @else
            <span class="badge-checkin-no">Not Redeemed</span>
        @endif
    </td>
    <td class="txn-commission">
        @php
            $commissionDisplay = ($commission == intval($commission)) ? number_format($commission, 0) : number_format($commission, 2);
            $commissionText = '$' . $commissionDisplay;
            if ($commStatus === 'pending' && $holdUntil) {
                $daysRemaining = (int)now()->diffInDays($holdUntil, false);
                $commissionText .= ' (Available in ' . max(0, abs($daysRemaining)) . 'd)';
            } elseif ($commStatus === 'paid') {
                $commissionText .= ' (Paid)';
            }
        @endphp
        <div style="font-weight:600;">{{ $commissionText }}</div>
    </td>
    <td>
        <div class="d-flex align-items-center gap-1">
            <button type="button" class="txn-action-eye" onclick="window.openTransactionModal({{ $item->id }})">
                <i class="fas fa-eye"></i>
            </button>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="20" style="text-align:center;padding:40px;color:rgba(255,255,255,0.3);">
        <i class="fas fa-inbox" style="font-size:2rem;margin-bottom:10px;display:block;"></i>
        No transactions found with current filters.
    </td>
</tr>
@endforelse
