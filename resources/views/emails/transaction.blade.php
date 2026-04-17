<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Your CartVIP Booking Confirmation</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f8f8f8; color: #222; margin: 0; padding: 0; }
        .container { background: #fff; max-width: 600px; margin: 30px auto; border-radius: 8px; box-shadow: 0 2px 8px #eee; padding: 32px; }
        h2 { color: #2a7ae2; margin-top: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { text-align: left; padding: 8px 6px; border-bottom: 1px solid #eee; }
        th { background: #f0f4f8; color: #333; }
        .section-title { margin-top: 32px; color: #2a7ae2; font-size: 1.1em; border-bottom: 1px solid #e0e0e0; padding-bottom: 4px; }
        .total { font-weight: bold; color: #2a7ae2; font-size: 1.2em; }
        .ticket-box { margin-top: 24px; padding: 18px; border: 1px solid #dbeafe; border-radius: 10px; background: #f8fbff; text-align: center; }
        .ticket-code { display: inline-block; margin-top: 10px; padding: 8px 12px; border-radius: 8px; background: #0f172a; color: #fff; font-weight: bold; letter-spacing: 0.06em; }
        .summary-grid { margin-top: 16px; display: grid; grid-template-columns: 1fr; gap: 14px; }
        .summary-card { border: 1px solid #e5e7eb; border-radius: 10px; background: #fcfdff; padding: 12px 14px; }
        .summary-card h4 { margin: 0 0 10px; font-size: 14px; color: #0f172a; }
        .summary-list { margin: 0; padding: 0; list-style: none; }
        .summary-list li { display: flex; justify-content: space-between; gap: 12px; padding: 6px 0; border-bottom: 1px solid #eef2f7; font-size: 13px; }
        .summary-list li:last-child { border-bottom: none; }
        .summary-list .k { color: #475569; font-weight: 600; }
        .summary-list .v { color: #111827; text-align: right; }
    </style>
</head>
<body>
<div class="container">
    <h2>Your CartVIP Booking Confirmation</h2>
    <p>Thank you for your purchase. CartVIP has securely processed your booking as the merchant of record.</p>
    <p><strong>Transaction ID:</strong> {{ $mailData['transaction_id'] ?? '' }}</p>

    <div class="section-title">Reservation Summary</div>
    <div class="summary-grid">
        <div class="summary-card">
            <h4>Package Holder</h4>
            <ul class="summary-list">
                <li><span class="k">Name</span><span class="v">{{ trim(($mailData['package_first_name'] ?? '') . ' ' . ($mailData['package_last_name'] ?? '')) ?: 'N/A' }}</span></li>
                <li><span class="k">Phone</span><span class="v">{{ $mailData['package_phone'] ?? 'N/A' }}</span></li>
                <li><span class="k">Email</span><span class="v">{{ $mailData['package_email'] ?? 'N/A' }}</span></li>
                <li><span class="k">Date of Birth</span><span class="v">{{ $mailData['package_dob'] ?? 'N/A' }}</span></li>
                <li><span class="k">Note</span><span class="v">{{ $mailData['package_note'] ?? 'N/A' }}</span></li>
            </ul>
        </div>

        <div class="summary-card">
            <h4>Transportation</h4>
            <ul class="summary-list">
                <li><span class="k">Pickup Time</span><span class="v">{{ $mailData['transportation_pickup_time'] ?? 'N/A' }}</span></li>
                <li><span class="k">Address</span><span class="v">{{ $mailData['transportation_address'] ?? 'N/A' }}</span></li>
                <li><span class="k">Phone</span><span class="v">{{ $mailData['transportation_phone'] ?? 'N/A' }}</span></li>
                <li><span class="k">Guests</span><span class="v">{{ $mailData['transportation_guest'] ?? 'N/A' }}</span></li>
                <li><span class="k">Note</span><span class="v">{{ $mailData['transportation_note'] ?? 'N/A' }}</span></li>
            </ul>
        </div>

        <div class="summary-card">
            <h4>Payment Holder</h4>
            <ul class="summary-list">
                <li><span class="k">Name</span><span class="v">{{ trim(($mailData['payment_first_name'] ?? '') . ' ' . ($mailData['payment_last_name'] ?? '')) ?: 'N/A' }}</span></li>
                <li><span class="k">Phone</span><span class="v">{{ $mailData['payment_phone'] ?? 'N/A' }}</span></li>
                <li><span class="k">Email</span><span class="v">{{ $mailData['payment_email'] ?? 'N/A' }}</span></li>
                <li><span class="k">Address</span><span class="v">{{ trim(implode(', ', array_filter([$mailData['payment_address'] ?? null, $mailData['payment_city'] ?? null, $mailData['payment_state'] ?? null, $mailData['payment_zip_code'] ?? null, $mailData['payment_country'] ?? null]))) ?: 'N/A' }}</span></li>
                <li><span class="k">Date of Birth</span><span class="v">{{ $mailData['payment_dob'] ?? 'N/A' }}</span></li>
            </ul>
        </div>
    </div>

    <div class="section-title">Order Details</div>
    @php
        $rawMailCartItems = $mailData['cart_items'] ?? [];
        if (is_string($rawMailCartItems)) {
            $decoded = json_decode($rawMailCartItems, true);
            if (is_string($decoded)) { $decoded = json_decode($decoded, true); }
            $rawMailCartItems = is_array($decoded) ? $decoded : [];
        }
        $mailCartItems = is_array($rawMailCartItems) ? $rawMailCartItems : [];
        $mailPriceBreakdown = is_array($mailData['price_breakdown'] ?? null) ? $mailData['price_breakdown'] : null;
    @endphp
    @if(!empty($mailCartItems))
    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th style="text-align:right;">Guests</th>
                <th style="text-align:right;">Unit Price</th>
                <th style="text-align:right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
        @foreach($mailCartItems as $ci)
            @php
                $ciIsMultiple = !empty($ci['is_multiple']);
                $ciGuests = max(1, (int)($ci['guests'] ?? 1));
                $ciUnit = (float)($ci['unit_price'] ?? 0);
                $ciPkgSub = $ciIsMultiple ? $ciUnit * $ciGuests : $ciUnit;
                $ciAddonsTotal = collect($ci['addons'] ?? [])->sum(fn($a) => (float)($a['price'] ?? 0));
                $ciLineTotal = (float)($ci['line_total'] ?? ($ciPkgSub + $ciAddonsTotal));
            @endphp
            <tr>
                <td><strong>{{ $ci['package_name'] ?? ('Package #' . ($ci['package_id'] ?? '')) }}</strong>
                    @if($ciIsMultiple && $ciGuests > 1)
                        <br><small style="color:#888;">${{ number_format($ciUnit,2) }} × {{ $ciGuests }} guests</small>
                    @endif
                </td>
                <td style="text-align:right;">{{ $ciGuests }}</td>
                <td style="text-align:right;">${{ number_format($ciUnit,2) }}</td>
                <td style="text-align:right;">${{ number_format($ciPkgSub,2) }}</td>
            </tr>
            @foreach($ci['addons'] ?? [] as $addon)
                @if(!empty($addon['name']))
                <tr style="color:#555;">
                    <td style="padding-left:18px;">+ {{ $addon['name'] }}</td>
                    <td style="text-align:right;">1</td>
                    <td style="text-align:right;">
                        @if(($addon['price'] ?? 0) > 0) ${{ number_format((float)$addon['price'],2) }} @else Included @endif
                    </td>
                    <td style="text-align:right;">
                        @if(($addon['price'] ?? 0) > 0) ${{ number_format((float)$addon['price'],2) }} @else — @endif
                    </td>
                </tr>
                @endif
            @endforeach
            @if($ciAddonsTotal > 0)
            <tr style="background:#f0f4f8;font-weight:bold;">
                <td colspan="3">Item Subtotal</td>
                <td style="text-align:right;">${{ number_format($ciLineTotal,2) }}</td>
            </tr>
            @endif
        @endforeach

        @if(!empty($mailPriceBreakdown))
        <tr>
            <td colspan="3"><strong>Packages Subtotal</strong></td>
            <td style="text-align:right;"><strong>${{ number_format((float) ($mailPriceBreakdown['packages_subtotal'] ?? 0), 2) }}</strong></td>
        </tr>
        <tr>
            <td colspan="3">Add-ons Subtotal</td>
            <td style="text-align:right;">${{ number_format((float) ($mailPriceBreakdown['addons_subtotal'] ?? 0), 2) }}</td>
        </tr>
        <tr style="background:#eef2ff;font-weight:bold;color:#3730a3;">
            <td colspan="3">Items Subtotal (Packages + Add-ons)</td>
            <td style="text-align:right;">${{ number_format((float) ($mailPriceBreakdown['items_subtotal'] ?? 0), 2) }}</td>
        </tr>
        @if(!empty($mailPriceBreakdown['gratuity']['enabled']))
        <tr>
            <td colspan="3">{{ $mailPriceBreakdown['gratuity']['name'] }} ({{ number_format((float) $mailPriceBreakdown['gratuity']['rate'], 2) }}%)</td>
            <td style="text-align:right;">${{ number_format((float) $mailPriceBreakdown['gratuity']['amount'], 2) }}</td>
        </tr>
        @endif
        @if(!empty($mailPriceBreakdown['service_charge']['enabled']))
        <tr>
            <td colspan="3">{{ $mailPriceBreakdown['service_charge']['name'] }} ({{ number_format((float) $mailPriceBreakdown['service_charge']['rate'], 2) }}%)</td>
            <td style="text-align:right;">${{ number_format((float) $mailPriceBreakdown['service_charge']['amount'], 2) }}</td>
        </tr>
        @endif
        @if(!empty($mailPriceBreakdown['sales_tax']['enabled']))
        <tr>
            <td colspan="3">{{ $mailPriceBreakdown['sales_tax']['name'] }} ({{ number_format((float) $mailPriceBreakdown['sales_tax']['rate'], 2) }}%)</td>
            <td style="text-align:right;">${{ number_format((float) $mailPriceBreakdown['sales_tax']['amount'], 2) }}</td>
        </tr>
        @endif
        @if((float) ($mailPriceBreakdown['promo_discount'] ?? 0) > 0)
        <tr>
            <td colspan="3">Promo Code Discount</td>
            <td style="text-align:right;">-${{ number_format((float) $mailPriceBreakdown['promo_discount'], 2) }}</td>
        </tr>
        @endif
        @if(!empty($mailPriceBreakdown['processing_fee']['enabled']))
        <tr>
            <td colspan="3">Processing Fee @if(($mailPriceBreakdown['processing_fee']['type'] ?? 'percentage') === 'percentage')({{ number_format((float) $mailPriceBreakdown['processing_fee']['rate'], 2) }}%)@endif</td>
            <td style="text-align:right;">${{ number_format((float) $mailPriceBreakdown['processing_fee']['amount'], 2) }}</td>
        </tr>
        @endif
        @endif

        <tr style="background:#dbeafe;">
            <td colspan="3" style="font-weight:bold;color:#1d4ed8;">Order Total</td>
            <td style="text-align:right;font-weight:bold;color:#1d4ed8;font-size:1.1em;">${{ number_format((float)($mailPriceBreakdown['grand_total'] ?? ($mailData['total'] ?? 0)), 2) }}</td>
        </tr>
        @if(!empty($mailPriceBreakdown['refundable']['enabled']))
        <tr>
            <td colspan="3">{{ $mailPriceBreakdown['refundable']['name'] }} ({{ number_format((float) $mailPriceBreakdown['refundable']['rate'], 2) }}%)</td>
            <td style="text-align:right;">${{ number_format((float) $mailPriceBreakdown['refundable']['amount'], 2) }}</td>
        </tr>
        @endif
        <tr>
            <td colspan="3"><strong>Amount Paid Now</strong></td>
            <td style="text-align:right;"><strong>${{ number_format((float) ($mailPriceBreakdown['amount_paid_now'] ?? ($mailData['total'] ?? 0)), 2) }}</strong></td>
        </tr>
        @if((float) ($mailPriceBreakdown['remaining_due'] ?? 0) > 0)
        <tr>
            <td colspan="3">Remaining Due</td>
            <td style="text-align:right;">${{ number_format((float) $mailPriceBreakdown['remaining_due'], 2) }}</td>
        </tr>
        @endif
        </tbody>
    </table>
    @else
    <table>
        <tr><th>Package ID</th><td>{{ $mailData['package_id'] ?? 'N/A' }}</td></tr>
        <tr><th>Add-ons</th><td>{{ $mailData['addons'] ?? 'N/A' }}</td></tr>
        <tr><th>Event ID</th><td>{{ $mailData['event_id'] ?? 'N/A' }}</td></tr>
        <tr><th class="total">Total</th><td class="total">${{ number_format((float)($mailData['total'] ?? 0), 2) }}</td></tr>
        <tr><th>Type</th><td>{{ $mailData['type'] ?? '' }}</td></tr>
    </table>
    @endif

    @if(!empty($mailData['ticket_qr_code']))
    <div class="ticket-box">
        <h3 style="margin:0 0 8px;color:#1d4ed8;">Your Entry Ticket</h3>
        <p style="margin:0 0 14px;color:#334155;">Show this QR code at the club entrance for check-in.</p>
        @if(!empty($mailData['ticket_qr_image_url']))
            <img src="{{ $mailData['ticket_qr_image_url'] }}" alt="Ticket QR Code" width="220" height="220" style="max-width:100%;height:auto;border-radius:10px;border:1px solid #cbd5e1;">
        @endif
        <div class="ticket-code">{{ $mailData['ticket_qr_code'] }}</div>
    </div>
    @endif

    <p style="margin-top: 32px; color: #888; font-size: 13px;">This is an automated email. Please do not reply.</p>
</div>
</body>
</html>
