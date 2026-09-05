@php
    try {
        $affiliateName = null;
        $subName = null;
        $parentName = null;
        if (!empty($item->affiliate_id) && !empty($item->affiliate)) {
            if ($item->affiliate->isSubAffiliate()) {
                $parent = $item->affiliate->parent;
                $parentName = $parent ? ($parent->display_name ?: optional($parent->user)->name) : 'Main Promoter';
                $subName = $item->affiliate->display_name ?: optional($item->affiliate->user)->name ?: ('Sub Promoter #' . $item->affiliate_id);
                $affiliateName = $subName . ' (Main: ' . $parentName . ')';
            } else {
                $affiliateName = $item->affiliate->display_name ?: optional($item->affiliate->user)->name ?: ('affiliate #' . $item->affiliate_id);
            }
        }
        elseif (!empty($item->entertainer_id) && !empty($item->entertainer))
            $affiliateName = $item->entertainer->display_name ?: optional($item->entertainer->user)->name ?: ('Entertainer #' . $item->entertainer_id);

        $commission  = (float)($item->affiliate_commission_amount ?? 0) + (float)($item->entertainer_commission_amount ?? 0);
        $packageName = $item->type === 'package' ? ($item->package_table_label ?: 'Package') : ($item->type === 'custom_invoice' ? 'Custom Invoice' : 'Reservation');
        $venueName   = $item->website->name ?? ($item->event->name ?? 'N/A');

        $cartItems = is_array($item->cart_items ?? null) ? $item->cart_items : json_decode($item->cart_items ?? '[]', true);
        $packageDetails = collect($cartItems)->map(function ($ci) {
            if (!is_array($ci)) {
                return null;
            }

            $name = trim((string) ($ci['name'] ?? $ci['package_name'] ?? $ci['packageName'] ?? $ci['pkgName'] ?? ''));
            if ($name === '') {
                return null;
            }

            $quantity = max(1, (int) ($ci['quantity'] ?? $ci['guests'] ?? 1));
            $packageType = strtolower(trim((string) ($ci['package_type'] ?? $ci['type'] ?? $ci['packageType'] ?? '')));
            if ($packageType === '' && !empty($ci['package_id'])) {
                $package = \App\Models\Package::find((int) $ci['package_id']);
                $packageType = $package ? strtolower(trim((string) ($package->package_type ?? ''))) : '';
            }

            if ($packageType === 'ticket') {
                return $name . ($quantity > 1 ? ' x' . $quantity : '');
            }

            return $name . ': ' . $quantity . ' ' . ($quantity === 1 ? 'guest' : 'guests');
        })->filter()->values();

        $packageDetailsText = $packageDetails->isNotEmpty()
            ? ($packageDetails->count() > 1 ? $packageDetails->implode(', ') : $packageDetails->first())
            : $packageName;

        $packageIds = collect($cartItems)
            ->map(fn ($ci) => (int) ($ci['package_id'] ?? 0))
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values();

        $packageRows = $packageIds->isNotEmpty()
            ? \App\Models\Package::whereIn('id', $packageIds)->get(['id', 'name', 'description'])
            : collect();

        $packageNames = collect($cartItems)
            ->map(fn ($ci) => trim((string) ($ci['package_name'] ?? $ci['packageName'] ?? $ci['pkgName'] ?? '')))
            ->filter(fn ($name) => $name !== '')
            ->unique()
            ->values();

        $packageRowsByName = $packageNames->isNotEmpty()
            ? \App\Models\Package::whereIn('name', $packageNames)->get(['id', 'name', 'description'])
            : collect();

        $packageDescriptionsById = $packageRows
            ->mapWithKeys(fn ($pkg) => [(string) $pkg->id => (string) ($pkg->description ?? '')])
            ->all();

        $packageDescriptionsByName = $packageRows
            ->mapWithKeys(function ($pkg) {
                $key = strtolower(trim((string) ($pkg->name ?? '')));
                return $key !== '' ? [$key => (string) ($pkg->description ?? '')] : [];
            })
            ->all();

        foreach ($packageRowsByName as $pkgByName) {
            $nameKey = strtolower(trim((string) ($pkgByName->name ?? '')));
            if ($nameKey === '') {
                continue;
            }

            if (!isset($packageDescriptionsByName[$nameKey]) || trim((string) $packageDescriptionsByName[$nameKey]) === '') {
                $packageDescriptionsByName[$nameKey] = (string) ($pkgByName->description ?? '');
            }

            $idKey = (string) ($pkgByName->id ?? '');
            if ($idKey !== '' && (!isset($packageDescriptionsById[$idKey]) || trim((string) $packageDescriptionsById[$idKey]) === '')) {
                $packageDescriptionsById[$idKey] = (string) ($pkgByName->description ?? '');
            }
        }

        foreach ($cartItems as $ci) {
            if (!is_array($ci)) {
                continue;
            }
            $cid = (int) ($ci['package_id'] ?? 0);
            $cname = strtolower(trim((string) ($ci['package_name'] ?? $ci['packageName'] ?? $ci['pkgName'] ?? '')));
            if ($cid > 0 && $cname !== '' && isset($packageDescriptionsById[(string) $cid]) && $packageDescriptionsById[(string) $cid] !== '') {
                $packageDescriptionsByName[$cname] = $packageDescriptionsById[(string) $cid];
            }
        }

        $packageDescriptionsPayload = [
            'byId' => $packageDescriptionsById,
            'byName' => $packageDescriptionsByName,
        ];

        $addons = collect($cartItems)->flatMap(fn($ci) => $ci['addons'] ?? [])->pluck('name')->filter()->implode(', ');
        if ($addons === '') {
            foreach (explode(',', (string)$item->addons) as $av) {
                $ao = \App\Models\Addon::find(trim($av));
                if ($ao) $addons .= ($addons !== '' ? ', ' : '') . $ao->name;
            }
        }
        $promo_obj = \App\Models\PromoCode::where('id', $item->promo_code)->first();
        $promo_code_name = $promo_obj ? $promo_obj->name : null;

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
        $packageDetails = collect([]);
        $packageDetailsText = 'N/A';
        $packageDescriptionsPayload = ['byId' => [], 'byName' => []];
        $addons = '';
        $promo_code_name = null;
        $commStatus = null;
        $holdUntil = null;
        $now = \Carbon\Carbon::now();
        $isEligible = false;
        $rowError = $e->getMessage();
    }
@endphp
<tr data-row-id="{{ $item->id }}" data-row-error="{{ $rowError ?? '' }}">
    <td><input type="checkbox" class="row-check" value="{{ $item->id }}"></td>
    <td class="txn-order-id">
        <div>#{{ str_pad($item->id, 3, '0', STR_PAD_LEFT) }}</div>
        @if($item->is_sandbox)
            <div class="mt-1"><span class="badge bg-warning text-dark fw-bold" style="font-size:0.62rem;letter-spacing:0.5px;"><i class="fas fa-vial me-1"></i>SANDBOX</span></div>
        @endif
    </td>
    @php
        $transactionWebsite = $item->website ?: optional($item->event)->website ?: optional($item->package)->website;
        $purchaseTimezone = optional($transactionWebsite)->resolved_timezone ?? 'America/Los_Angeles';
        $purchaseAtLocal = optional($item->created_at)->copy()?->timezone($purchaseTimezone);
        $purchaseSortOrder = $purchaseAtLocal?->timestamp ?? 0;

        $requiresTransportationForRow = false;
        $rowCartItems = is_array($item->cart_items ?? null) ? $item->cart_items : [];
        foreach ($rowCartItems as $rowCartItem) {
            if (!is_array($rowCartItem)) {
                continue;
            }
            $rowTransportValue = $rowCartItem['transportation'] ?? ($rowCartItem['transport'] ?? false);
            if (
                $rowTransportValue === true ||
                $rowTransportValue === 1 ||
                $rowTransportValue === '1' ||
                $rowTransportValue === 'true' ||
                $rowTransportValue === 'on'
            ) {
                $requiresTransportationForRow = true;
                break;
            }
        }
        if (!$requiresTransportationForRow && !empty($item->package)) {
            $requiresTransportationForRow = (
                $item->package->transportation == 1 ||
                $item->package->transportation === true ||
                $item->package->transportation === '1'
            );
        }

        $hasAdminNoteRow = !empty(trim((string) ($item->admin_notes ?? '')));
        $hasAnyNoteRow = $hasAdminNoteRow;

        $formatDatePst = function ($dateVal, $format = 'M d, Y h:i A \P\D\T') {
            if (empty($dateVal)) {
                return '';
            }
            try {
                if ($dateVal instanceof \Carbon\CarbonInterface) {
                    return $dateVal->copy()->timezone('America/Los_Angeles')->format($format);
                }
                return \Carbon\Carbon::parse($dateVal)->timezone('America/Los_Angeles')->format($format);
            } catch (\Throwable $e) {
                return '';
            }
        };
    @endphp
    <td data-order="{{ $purchaseSortOrder }}">
        <div class="txn-date-main">{{ $purchaseAtLocal?->format('M d, Y') ?? '-' }}</div>
        <div class="txn-date-time">{{ $purchaseAtLocal?->format('h:i A T') ?? '-' }}</div>
    </td>
    <td class="txn-confirmation-num">
        <div>{{ $item->transaction_id ?? 'N/A' }}</div>
        @if($item->repay_paid_at)
            <div class="mt-1"><span class="badge bg-success text-white fw-bold" style="font-size:0.62rem;"><i class="fas fa-check-circle me-1"></i>REPAID LIVE</span></div>
        @elseif($item->is_sandbox)
            <div class="mt-1"><span class="badge bg-danger text-white fw-bold" style="font-size:0.62rem;"><i class="fas fa-exclamation-triangle me-1"></i>TEST TRANSACTION</span></div>
        @endif
    </td>
    <td class="txn-pkg-name">
        <div style="font-size:0.85rem;font-weight:600;margin-bottom:8px;">{{ $venueName }}</div>
        <button type="button" class="btn btn-sm btn-link-package view-btn" data-total="{{ (float)($item->total ?? 0) }}" data-guests="{{ $item->package_number_of_guest ?? 1 }}" data-date="{{ $purchaseAtLocal?->format('M d, Y') ?? '' }}" data-date-iso="{{ $purchaseAtLocal?->format('Y-m-d') ?? '' }}" data-bs-toggle="modal" data-bs-target="#packageDetailsModal" data-transaction-id="{{ $item->id }}" data-id="{{ $item->id }}" data-requires_transportation="{{ $requiresTransportationForRow ? 1 : 0 }}" data-admin_notes="{{ $item->admin_notes ?? '' }}" data-admin_notes_by="{{ $item->admin_notes_by ?? '' }}" data-admin_notes_at="{{ $formatDatePst($item->admin_notes_at) }}" data-confirmation-number="{{ $item->transaction_id ?? 'N/A' }}" data-cart-items='@json($cartItems)' data-package-descriptions-b64="{{ base64_encode(json_encode($packageDescriptionsPayload)) }}" data-breakdown='@json($item->price_breakdown)' data-transaction-type='{{ $item->type }}' data-men='{{ $item->package_men ?? 0 }}' data-women='{{ $item->package_women ?? 0 }}' data-package-label="{{ $packageDetailsText }}" data-package_use_date="{{ $item->package_use_date ?? '' }}" data-checked_in_status="{{ $item->checked_in_status ?? $item->checked_in ?? 0 }}" data-package_number_of_guest="{{ $item->package_number_of_guest ?? 0 }}" data-package_first_name="{{ $item->package_first_name ?? '' }}" data-package_last_name="{{ $item->package_last_name ?? '' }}" data-package_phone="{{ $item->package_phone ?? '' }}" data-package_email="{{ $item->package_email ?? '' }}" data-package_dob="{{ $item->package_dob ?? '' }}" data-package_note="{{ $item->package_note ?? '' }}" data-host_name="{{ $item->host_name ?? '' }}" data-transportation_pickup_time="{{ $item->transportation_pickup_time ?? '' }}" data-transportation_arrival_time="{{ $item->transportation_arrival_time ?? '' }}" data-transportation_address="{{ $item->transportation_address ?? '' }}" data-transportation_phone="{{ $item->transportation_phone ?? '' }}" data-transportation_note="{{ $item->transportation_note ?? '' }}" data-payment_first_name="{{ $item->payment_first_name ?? '' }}" data-payment_last_name="{{ $item->payment_last_name ?? '' }}" data-payment_phone="{{ $item->payment_phone ?? '' }}" data-payment_email="{{ $item->payment_email ?? '' }}" data-payment_address="{{ $item->payment_address ?? '' }}" data-payment_city="{{ $item->payment_city ?? '' }}" data-payment_state="{{ $item->payment_state ?? '' }}" data-payment_country="{{ $item->payment_country ?? '' }}" data-payment_dob="{{ $item->payment_dob ?? '' }}" data-payment_zip_code="{{ $item->payment_zip_code ?? '' }}" data-type="{{ $item->type }}" data-status="{{ $item->status }}" data-ip_address="{{ $item->ip_address ?? '' }}" data-website_id="{{ $item->website->name ?? '' }}" data-affiliate_name="{{ $affiliateName ?: '' }}" data-affiliate_sub_name="{{ $subName ?: '' }}" data-affiliate_parent_name="{{ $parentName ?: '' }}" data-entertainer_name="{{ $item->entertainer ? ($item->entertainer->display_name ?: optional($item->entertainer->user)->name) : '' }}" data-addons="{{ $addons }}" style="font-size:0.85rem;min-width:72px;">Quick View</button>
    </td>
    <td class="txn-host-name">
        @if(!empty($item->host_name))
            <span style="font-size:0.85rem;font-weight:600;color:#fff;">{{ $item->host_name }}</span>
        @else
            <span style="color:rgba(255,255,255,0.3);font-size:0.85rem;">-</span>
        @endif
    </td>
    <td>
        @php
            $sourceText = 'Direct';
            $sourceBadgeColor = '#6b7280';
            $sourceLink = null;
            $sourceType = null;
            $subAffiliateParentName = null;

            if (!empty($item->affiliate_id) && !empty($item->affiliate)) {
                if ($item->affiliate->isSubAffiliate()) {
                    $parent = $item->affiliate->parent;
                    $parentName = $parent ? ($parent->display_name ?: optional($parent->user)->name) : 'Main Promoter';
                    $subName = $item->affiliate->display_name ?: optional($item->affiliate->user)->name ?: ('Sub Promoter #' . $item->affiliate_id);
                    $sourceText = $subName;
                    $subAffiliateParentName = $parentName;
                } else {
                    $sourceText = $item->affiliate->display_name ?: optional($item->affiliate->user)->name ?: ('Affiliate #' . $item->affiliate_id);
                }
                $sourceBadgeColor = '#8b5cf6';
                $sourceLink = route('admin.affiliate.show', $item->affiliate_id);
                $sourceType = 'affiliate';
            } elseif (!empty($item->entertainer_id) && !empty($item->entertainer)) {
                $sourceText = $item->entertainer->display_name ?: optional($item->entertainer->user)->name ?: ('Entertainer #' . $item->entertainer_id);
                $sourceBadgeColor = '#ec4899';
                $sourceLink = route('admin.entertainer.show', $item->entertainer_id);
                $sourceType = 'entertainer';
            }
        @endphp
        @if($sourceLink)
            <a href="{{ $sourceLink }}" style="background:{{ $sourceBadgeColor }};color:white;padding:4px 10px;border-radius:4px;font-size:0.85rem;font-weight:600;text-decoration:none;display:inline-block;cursor:pointer;" title="View {{ $sourceType }} profile">{{ $sourceText }}</a>
        @else
            <span style="background:{{ $sourceBadgeColor }};color:white;padding:4px 10px;border-radius:4px;font-size:0.85rem;font-weight:600;">{{ $sourceText }}</span>
        @endif
        @if($subAffiliateParentName)
            <div style="font-size:0.73rem;color:#c084fc;margin-top:3px;font-weight:500;">
                <i class="fas fa-sitemap me-1" style="font-size:0.68rem;"></i>Main: {{ $subAffiliateParentName }}
            </div>
        @endif
    </td>
    <td>
        @php
            $customerPhone = trim((string) ($item->package_phone ?: $item->payment_phone ?: ''));
            $resMen = (int) ($item->package_men ?? 0);
            $resWomen = (int) ($item->package_women ?? 0);
            $resGuests = $resMen + $resWomen;
            $pkgGuests = (int) ($item->package_number_of_guest ?? 0);

            $cartGuests = 0;
            if (is_array($cartItems)) {
                foreach ($cartItems as $ci) {
                    if (is_array($ci)) {
                        $cartGuests += max(1, (int) ($ci['guests'] ?? $ci['quantity'] ?? 1));
                    }
                }
            }

            $totalGuestsCount = $resGuests > 0 ? $resGuests : ($pkgGuests > 0 ? $pkgGuests : $cartGuests);
        @endphp
        <div class="txn-customer-name">
            {{ $item->package_first_name }} {{ $item->package_last_name }}
            @if($totalGuestsCount > 1)
                <span class="badge-guest-count">x{{ $totalGuestsCount }}</span>
            @endif
        </div>
        <div class="txn-customer-email">{{ $item->package_email }}</div>
        @if($customerPhone !== '')
            <div class="txn-customer-phone" style="font-size:0.75rem;color:rgba(255,255,255,0.6);margin-top:2px;">
                <i class="fas fa-phone me-1" style="font-size:0.65rem;color:rgba(255,255,255,0.4);"></i>{{ $customerPhone }}
            </div>
        @endif
    </td>
    <td class="txn-amount">${{ number_format((float)$item->total, 2) }}</td>
    <td>
        @php
            $paidAmount = (float)($item->actual_total ?? $item->total ?? 0);
            $totalAmount = (float)($item->total ?? 0);
            $dueAmount = $totalAmount - $paidAmount;
            $paymentStatus = $paidAmount >= $totalAmount ? 'Paid' : ($paidAmount > 0 ? 'Partial' : 'Pending');
            $paymentText = $paymentStatus;
            if ($paymentStatus === 'Partial') {
                $paymentText = 'Partial ($' . number_format($paidAmount, 2) . ' paid)';
            }
        @endphp
        <span class="badge-{{ $paymentStatus === 'Paid' ? 'completed' : ($paymentStatus === 'Partial' ? 'warning' : 'canceled') }}" style="font-size:0.85rem;">{{ $paymentText }}</span>
    </td>
    <td>
        @php
            $cardLast4 = trim((string) ($item->payment_card_last4 ?? ''));
        @endphp
        <span style="font-size:0.85rem;font-weight:600;color:{{ $cardLast4 !== '' ? '#fff' : 'rgba(255,255,255,0.3)' }};">{{ $cardLast4 !== '' ? '**** ' . $cardLast4 : '-' }}</span>
    </td>
    <td class="txn-amount">
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
            $transportAnchorAtPacific = null;
            $noShowEligibleAtPacific = null;

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

            if ($reservationDatePacific) {
                $transportTimeRaw = trim((string) ($item->transportation_arrival_time ?: $item->transportation_pickup_time ?: ''));

                if ($transportTimeRaw !== '') {
                    try {
                        $transportAnchorAtPacific = \Carbon\Carbon::parse(
                            $reservationDatePacific->format('Y-m-d') . ' ' . $transportTimeRaw,
                            'America/Los_Angeles'
                        );
                        $noShowEligibleAtPacific = $transportAnchorAtPacific->copy()->addHours(24);
                    } catch (\Throwable $e) {
                        $transportAnchorAtPacific = null;
                        $noShowEligibleAtPacific = null;
                    }
                }
            }

            $transportModeLabel = $item->transport_mode_label ?? null;
            $reservationSortOrder = $reservationDatePacific?->timestamp ?? 0;

            $reservationStatusValue = 'Upcoming';
            $reservationStatusClass = 'badge-reservation-upcoming';

            if ($item->checked_in_status) {
                $reservationStatusValue = 'Checked In';
                $reservationStatusClass = 'badge-reservation-checked-in';
            } else {
                if ($reservationDatePacific) {
                    if ($reservationDatePacific->equalTo($laToday)) {
                        $reservationStatusValue = 'Today';
                        $reservationStatusClass = 'badge-reservation-today';
                    } elseif ($reservationDatePacific->greaterThan($laToday)) {
                        $reservationStatusValue = 'Upcoming';
                        $reservationStatusClass = 'badge-reservation-upcoming';
                    } else {
                        if ($noShowEligibleAtPacific && $nowPacific->greaterThanOrEqualTo($noShowEligibleAtPacific)) {
                            $reservationStatusValue = 'No Show';
                            $reservationStatusClass = 'badge-reservation-no-show';
                        } else {
                            $reservationStatusValue = 'Upcoming';
                            $reservationStatusClass = 'badge-reservation-upcoming';
                        }
                    }
                }

                if ($item->status == 2) {
                    $reservationStatusValue = 'Refunded';
                    $reservationStatusClass = 'badge-reservation-refunded';
                } elseif ($item->status == 0) {
                    $reservationStatusValue = 'Cancelled';
                    $reservationStatusClass = 'badge-reservation-cancelled';
                }
            }
    @endphp
    <td data-order="{{ $reservationSortOrder }}">
        @if($reservationStatusValue === 'Upcoming' && $reservationDatePacific)
            <div style="font-size:0.9rem;margin-bottom:0.5rem;">{{ $reservationDatePacific->format('M d, Y') }}</div>
            <div style="margin-top:4px;">
                <span class="{{ $reservationStatusClass }}">{{ $reservationStatusValue }}</span>
            </div>
        @else
            <span class="{{ $reservationStatusClass }}">{{ $reservationStatusValue }}</span>
        @endif
    </td>
    <td data-order="{{ $reservationSortOrder }}">
        @if($reservationDatePacific)
            @if($reservationDatePacific->equalTo($laToday))
                <div style="font-size:0.95rem;font-weight:600;">Today</div>
            @elseif($reservationDatePacific->greaterThan($laToday))
                <div style="font-size:0.9rem;">{{ $reservationDatePacific->format('M d, Y') }}</div>
            @else
                <div style="font-size:0.9rem;color:rgba(255,255,255,0.6);">{{ $reservationDatePacific->format('M d, Y') }}</div>
            @endif
        @else
            <span style="color:rgba(255,255,255,0.25);font-size:0.78rem">-</span>
        @endif
        @if(!empty($transportModeLabel))
            <div style="margin-top:4px;display:inline-block;padding:2px 8px;border-radius:999px;font-size:0.72rem;font-weight:700;line-height:1.2;background:{{ $transportModeLabel === 'Self Drive' ? 'rgba(16,185,129,0.14)' : 'rgba(59,130,246,0.14)' }};color:{{ $transportModeLabel === 'Self Drive' ? '#34d399' : '#93c5fd' }};border:1px solid {{ $transportModeLabel === 'Self Drive' ? 'rgba(16,185,129,0.25)' : 'rgba(59,130,246,0.25)' }};">{{ $transportModeLabel }}</div>
        @endif
    </td>
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
                if ($daysRemaining <= 0) {
                    $commissionText .= ' (Available now)';
                } else {
                    $commissionText .= ' (Available in ' . abs($daysRemaining) . ' days)';
                }
            } elseif ($commStatus === 'paid') {
                $commissionText .= ' (Paid out)';
            } elseif ($commStatus === 'approved') {
                $commissionText .= ' (Approved)';
            } elseif ($commStatus === 'reversed') {
                $commissionText .= ' (Reversed)';
            }
        @endphp
        <div style="font-weight:600;">{{ $commissionText }}</div>
    </td>
    <td>
        <div class="d-flex align-items-center gap-1">
            <button type="button" class="txn-action-eye view-btn"
                data-bs-toggle="modal" data-bs-target="#viewTransactionModal"
                data-id="{{ $item->id }}"
                data-admin_notes="{{ $item->admin_notes ?? '' }}"
                data-admin_notes_by="{{ $item->admin_notes_by ?? '' }}"
                data-admin_notes_at="{{ $item->admin_notes_at ? optional($item->admin_notes_at)->timezone('America/Los_Angeles')->format('M d, Y h:i A \P\D\T') : '' }}"
                data-transaction_id="{{ $item->transaction_id ?? 'Free' }}"
                data-package_id="{{ $packageDetailsText }}"
                data-cart-items='@json($cartItems)'
                data-breakdown='@json($item->price_breakdown)'
                data-package_first_name="{{ $item->package_first_name }}"
                data-package_last_name="{{ $item->package_last_name }}"
                data-package_phone="{{ $item->package_phone }}"
                data-package_email="{{ $item->package_email }}"
                data-package_dob="{{ $item->package_dob }}"
                data-package_note="{{ $item->package_note }}"
                data-host_name="{{ $item->host_name }}"
                data-package_number_of_guest="{{ $item->package_number_of_guest }}"
                data-transportation_pickup_time="{{ $item->transportation_pickup_time }}"
                data-transportation_arrival_time="{{ $item->transportation_arrival_time }}"
                data-transportation_address="{{ $item->transportation_address }}"
                data-transportation_phone="{{ $item->transportation_phone }}"
                data-transportation_guest="{{ $item->transportation_guest }}"
                data-transportation_note="{{ $item->transportation_note }}"
                data-payment_first_name="{{ $item->payment_first_name }}"
                data-payment_last_name="{{ $item->payment_last_name }}"
                data-payment_phone="{{ $item->payment_phone }}"
                data-payment_email="{{ $item->payment_email }}"
                data-payment_address="{{ $item->payment_address }}"
                data-payment_city="{{ $item->payment_city }}"
                data-payment_state="{{ $item->payment_state }}"
                data-payment_country="{{ $item->payment_country }}"
                data-payment_dob="{{ $item->payment_dob }}"
                data-payment_zip_code="{{ $item->payment_zip_code }}"
                data-shipping_same_as_billing="{{ $item->shipping_same_as_billing ? 1 : 0 }}"
                data-shipping_first_name="{{ $item->shipping_first_name ?? '' }}"
                data-shipping_last_name="{{ $item->shipping_last_name ?? '' }}"
                data-shipping_phone="{{ $item->shipping_phone ?? '' }}"
                data-shipping_email="{{ $item->shipping_email ?? '' }}"
                data-shipping_address="{{ $item->shipping_address ?? '' }}"
                data-shipping_city="{{ $item->shipping_city ?? '' }}"
                data-shipping_state="{{ $item->shipping_state ?? '' }}"
                data-shipping_zip_code="{{ $item->shipping_zip_code ?? '' }}"
                data-shipping_country="{{ $item->shipping_country ?? '' }}"
                data-payment_card_last4="{{ $item->payment_card_last4 ?? '' }}"
                data-payment_card_brand="{{ $item->payment_card_brand ?? '' }}"
                data-type="{{ $item->type }}"
                data-status="{{ $item->status }}"
                data-ip_address="{{ $item->ip_address }}"
                data-website_id="{{ $item->website->name ?? '' }}"
                data-event_id="{{ $item->event->name ?? '' }}"
                data-addons="{{ $addons }}"
                data-business_company="{{ $item->business_company }}"
                data-business_vat="{{ $item->business_vat }}"
                data-business_address="{{ $item->business_address }}"
                data-business_purpose="{{ $item->business_purpose }}"
                data-total="{{ $item->total }}"
                data-subtotal="{{ $item->actual_total }}"
                data-refundable="{{ number_format(($item->actual_total / 100) * ($item->website->refundable_fee ?? 0), 2) }}"
                data-gratuity="{{ number_format(($item->actual_total / 100) * ($item->website->gratuity_fee ?? 0), 2) }}"
                data-service_charge="{{ number_format(($item->actual_total / 100) * ($item->website->service_charge_fee ?? 0), 2) }}"
                data-processing_fee="{{
                    ($item->website->processing_fee_type ?? 'percentage') === 'flat'
                        ? number_format($item->website->processing_fee ?? 0, 2)
                        : number_format(($item->actual_total / 100) * ($item->website->processing_fee ?? 0), 2)
                }}"
                data-due="{{ $item->actual_total - $item->total }}"
                data-promo_code="{{ $promo_code_name }}"
                data-discounted_amount="{{ $item->discounted_amount }}"
                data-package_use_date="{{ $item->package_use_date }}"
                data-date="{{ $purchaseAtLocal?->format('Y-m-d h:i A T') ?? '' }}"
                data-men="{{ $item->men ?? '' }}"
                data-women="{{ $item->women ?? '' }}"
                data-requires_transportation="{{ $requiresTransportationForRow ? 1 : 0 }}"
                data-affiliate_name="{{ $affiliateName ?: '' }}"
                data-entertainer_name="{{ !empty($item->entertainer_id) && !empty($item->entertainer) ? ($item->entertainer->display_name ?: optional($item->entertainer->user)->name ?: ('Entertainer #' . $item->entertainer_id)) : '' }}"
                data-affiliate_commission_percentage="{{ (float) ($item->affiliate_commission_percentage ?? 0) }}"
                data-affiliate_commission_amount="{{ (float) ($item->affiliate_commission_amount ?? 0) }}"
                data-affiliate_commission_status="{{ $item->affiliate_commission_status ?? '' }}"
                data-affiliate_commission_hold_until="{{ $formatDatePst($item->affiliate_commission_hold_until, 'M d, Y h:i A \P\T') }}"
                data-entertainer_commission_percentage="{{ (float) ($item->entertainer_commission_percentage ?? 0) }}"
                data-entertainer_commission_amount="{{ (float) ($item->entertainer_commission_amount ?? 0) }}"
                data-entertainer_commission_status="{{ $item->entertainer_commission_status ?? '' }}"
                data-entertainer_commission_hold_until="{{ $formatDatePst($item->entertainer_commission_hold_until, 'M d, Y h:i A \P\T') }}"
                data-total_commission="{{ (float) $commission }}"
                data-checked_in_status="{{ $item->checked_in_status ? 1 : 0 }}"
                data-checked_in_at_pacific="{{ $item->checked_in_at_pacific ? (optional($item->checked_in_at_pacific)->format('Y-m-d h:i A') . ' PT') : '' }}"
                data-checkin_photo_front="{{ $item->checkin_photo_front_path ?? '' }}"
                data-checkin_photo_back="{{ $item->checkin_photo_back_path ?? '' }}"
                title="View Details">
                <i class="fas fa-eye"></i>
            </button>
            <button type="button" class="btn btn-sm {{ $hasAnyNoteRow ? 'btn-warning text-dark fw-bold btn-has-note' : 'btn-outline-warning' }} open-notes-btn px-2 py-1 ms-1"
                data-bs-toggle="modal" data-bs-target="#txnNotesModal"
                data-id="{{ $item->id }}"
                data-transaction-id="{{ $item->transaction_id ?? 'Free' }}"
                data-admin_notes="{{ $item->admin_notes ?? '' }}"
                data-admin_notes_by="{{ $item->admin_notes_by ?? '' }}"
                data-admin_notes_at="{{ $formatDatePst($item->admin_notes_at) }}"
                data-package_note="{{ $item->package_note ?? '' }}"
                data-transportation_note="{{ $item->transportation_note ?? '' }}"
                title="{{ $hasAnyNoteRow ? 'Has Notes - Click to view/edit' : 'Notes' }}" style="font-size:0.75rem;border-radius:6px;font-weight:600;">
                <i class="fas fa-sticky-note me-1"></i>Notes @if($hasAnyNoteRow)<span class="badge bg-dark text-warning rounded-circle ms-1 p-1" style="font-size:0.6rem;line-height:1;">!</span>@endif
            </button>
            <div class="dropdown">
                <button class="txn-action-more btn p-0" data-bs-toggle="dropdown" type="button" style="border:none;background:none">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" style="background:#1e293b;border:1px solid rgba(255,255,255,0.1)">
                    <li><a class="dropdown-item open-notes-btn {{ $hasAnyNoteRow ? 'fw-bold text-warning' : '' }}" style="color:rgba(255,255,255,0.7);font-size:0.82rem" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#txnNotesModal" data-id="{{ $item->id }}" data-transaction-id="{{ $item->transaction_id ?? 'Free' }}" data-admin_notes="{{ $item->admin_notes ?? '' }}" data-admin_notes_by="{{ $item->admin_notes_by ?? '' }}" data-admin_notes_at="{{ $formatDatePst($item->admin_notes_at) }}" data-package_note="{{ $item->package_note ?? '' }}" data-transportation_note="{{ $item->transportation_note ?? '' }}"><i class="fas fa-sticky-note me-2 text-warning"></i>Notes @if($hasAnyNoteRow)<span class="badge bg-warning text-dark ms-1" style="font-size:0.65rem;">Has Note</span>@endif</a></li>
                    @if(!($isArchivedView ?? false))
                    <li><a class="dropdown-item" style="color:rgba(255,255,255,0.7);font-size:0.82rem" href="{{ route('admin.transaction.update', ['id' => $item->id, 'status' => 1]) }}"><i class="fas fa-check me-2 text-success"></i>Mark Completed</a></li>
                    <li><a class="dropdown-item" style="color:rgba(255,255,255,0.7);font-size:0.82rem" href="{{ route('admin.transaction.update', ['id' => $item->id, 'status' => 0]) }}"><i class="fas fa-times me-2 text-danger"></i>Mark Canceled</a></li>
                    <li><a class="dropdown-item" style="color:rgba(255,255,255,0.7);font-size:0.82rem" href="{{ route('admin.transaction.update', ['id' => $item->id, 'status' => 2]) }}"><i class="fas fa-undo me-2 text-warning"></i>Mark Refunded</a></li>
                    <li><hr class="dropdown-divider" style="border-color:rgba(255,255,255,0.12)"></li>
                    @if(auth()->check() && auth()->user()->isAdmin())
                    <li>
                        <a class="dropdown-item btn-send-repay-trigger" style="color:#fbbf24;font-size:0.82rem" href="javascript:void(0)"
                           data-id="{{ $item->id }}"
                           data-txnid="{{ $item->transaction_id ?? $item->id }}"
                           data-email="{{ $item->package_email ?: $item->payment_email }}"
                           data-amount="${{ number_format((float)$item->total, 2) }}"
                           data-repay-url="{{ $item->repay_url }}"
                           data-send-url="{{ route('admin.transaction.send-repay-email', $item->id) }}">
                            <i class="fas fa-paper-plane me-2 text-warning"></i>Send Payment (Repay) Link
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item btn-toggle-sandbox-trigger" style="color:rgba(255,255,255,0.7);font-size:0.82rem" href="javascript:void(0)"
                           data-id="{{ $item->id }}"
                           data-is-sandbox="{{ $item->is_sandbox ? '1' : '0' }}">
                            <i class="fas fa-vial me-2 {{ $item->is_sandbox ? 'text-success' : 'text-warning' }}"></i>
                            {{ $item->is_sandbox ? 'Mark as Live' : 'Mark as Sandbox' }}
                        </a>
                    </li>
                    @endif
                    @endif
                    @if($canArchiveTransactions ?? true)
                    <li><hr class="dropdown-divider" style="border-color:rgba(255,255,255,0.12)"></li>
                    @if($isArchivedView ?? false)
                        <li>
                            <form method="POST" action="{{ route('admin.transaction.unarchive', $item->id) }}" onsubmit="return confirm('Unarchive this transaction?');">
                                @csrf
                                <button type="submit" class="dropdown-item" style="color:#34d399;font-size:0.82rem">
                                    <i class="fas fa-box-open me-2 text-success"></i>Unarchive Transaction
                                </button>
                            </form>
                        </li>
                    @else
                        <li>
                            <form method="POST" action="{{ route('admin.transaction.archive', $item->id) }}" onsubmit="return confirm('Archive this transaction? Archived transactions are removed from totals and reports.');">
                                @csrf
                                <button type="submit" class="dropdown-item" style="color:#fbbf24;font-size:0.82rem">
                                    <i class="fas fa-archive me-2 text-warning"></i>Archive Transaction
                                </button>
                            </form>
                        </li>
                    @endif
                    @endif
                </ul>
            </div>
        </div>
    </td>
    <td class="d-none">{{ $affiliateName ?: 'DIRECT' }}</td>
    <td class="d-none">@if($isPayoutPage ?? false)@if($commission == 0)N/A@elseif($commStatus === 'paid')PAID OUT@elseif($commStatus === 'reversed')REVERSED@else{{ $commStatus }}@endif@else-@endif</td>
    <td class="d-none">{{ $venueName }}</td>
    <td class="d-none">{{ $packageName }}</td>
</tr>
