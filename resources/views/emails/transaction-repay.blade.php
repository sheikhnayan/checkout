<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Your Reservation Payment - {{ $website->name ?? 'CartVIP' }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #0b132b;
            color: #1e293b;
            margin: 0;
            padding: 24px 12px;
        }
        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        .email-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            padding: 32px 24px;
            text-align: center;
            color: #ffffff;
            border-bottom: 3px solid #d97706;
        }
        .email-header h1 {
            margin: 0 0 8px;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: 0.5px;
            color: #f8fafc;
        }
        .email-header .club-name {
            font-size: 15px;
            color: #fbbf24;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .email-body {
            padding: 32px 28px;
        }
        .alert-box {
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-left: 4px solid #f59e0b;
            border-radius: 8px;
            padding: 16px 18px;
            margin-bottom: 24px;
            font-size: 14px;
            line-height: 1.6;
            color: #92400e;
        }
        .alert-box strong {
            display: block;
            margin-bottom: 6px;
            font-size: 15px;
            color: #78350f;
        }
        .section-heading {
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
            margin: 24px 0 12px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 6px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .info-table tr td {
            padding: 8px 4px;
            vertical-align: top;
        }
        .info-table tr td:first-child {
            color: #64748b;
            font-weight: 600;
            width: 40%;
        }
        .info-table tr td:last-child {
            color: #0f172a;
            font-weight: 600;
            text-align: right;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 13px;
        }
        .items-table th {
            background: #f8fafc;
            padding: 10px 8px;
            text-align: left;
            font-weight: 700;
            color: #475569;
            border-bottom: 1px solid #cbd5e1;
        }
        .items-table td {
            padding: 12px 8px;
            border-bottom: 1px solid #f1f5f9;
            color: #1e293b;
        }
        .items-table .text-right {
            text-align: right;
        }
        .summary-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 16px;
            margin: 20px 0 28px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            font-size: 14px;
            color: #475569;
        }
        .summary-row.grand-total {
            border-top: 2px solid #cbd5e1;
            padding-top: 10px;
            margin-top: 8px;
            font-size: 17px;
            font-weight: 800;
            color: #0f172a;
        }
        .cta-container {
            text-align: center;
            margin: 32px 0 24px;
        }
        .btn-pay {
            display: inline-block;
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            color: #ffffff !important;
            font-size: 16px;
            font-weight: 800;
            text-decoration: none;
            padding: 16px 36px;
            border-radius: 30px;
            box-shadow: 0 4px 15px rgba(217, 119, 6, 0.4);
            letter-spacing: 0.5px;
        }
        .email-footer {
            background: #f8fafc;
            padding: 24px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            line-height: 1.5;
        }
        .email-footer p {
            margin: 4px 0;
        }
    </style>
</head>
<body>
<div class="email-wrapper">
    <!-- Header -->
    <div class="email-header">
        <div class="club-name">{{ $website->name ?? 'CartVIP' }}</div>
        <h1>Action Required: Complete Payment</h1>
    </div>

    <!-- Body -->
    <div class="email-body">
        <!-- Main Required Notice Message -->
        <div class="alert-box">
            <strong>⚠️ Payment Resubmission Notice</strong>
            Due to a processing issue, your original CartVIP transaction was not completed successfully. Your reservation information was received, but the payment will need to be resubmitted. You were not charged for the original transaction.
        </div>

        @if(!empty($customMessage))
        <div style="background:#f1f5f9;border-radius:8px;padding:12px 16px;margin-bottom:20px;font-size:14px;color:#334155;">
            <strong>Note from Venue Staff:</strong><br>
            {{ $customMessage }}
        </div>
        @endif

        <!-- Big Action CTA Button -->
        <div class="cta-container">
            <a href="{{ $repayUrl }}" class="btn-pay" target="_blank">
                Complete Your Payment Securely &rarr;
            </a>
            <div style="font-size: 12px; color: #64748b; margin-top: 10px;">
                Amount Due: <strong>${{ number_format((float) $transaction->total, 2) }}</strong> &bull; Secure 256-Bit SSL Encrypted
            </div>
        </div>

        <!-- Order & Reservation Details -->
        <div class="section-heading">Reservation Details</div>
        <table class="info-table">
            <tr>
                <td>Confirmation ID</td>
                <td>{{ $transaction->transaction_id }}</td>
            </tr>
            <tr>
                <td>Venue / Club</td>
                <td>{{ $website->name ?? 'CartVIP' }}</td>
            </tr>
            <tr>
                <td>Guest Name</td>
                <td>{{ $transaction->package_first_name }} {{ $transaction->package_last_name }}</td>
            </tr>
            @if(!empty($transaction->package_use_date))
            <tr>
                <td>Reservation / Visit Date</td>
                <td>{{ \Carbon\Carbon::parse($transaction->package_use_date)->format('M d, Y') }}</td>
            </tr>
            @endif
            @if(!empty($transaction->transportation_arrival_time))
            <tr>
                <td>Estimated Arrival Time</td>
                <td>{{ $transaction->transportation_arrival_time }}</td>
            </tr>
            @endif
            @if(!empty($transaction->created_at))
            @php
                $clubTime = $transaction->created_at->copy()->timezone($timezone ?? 'America/Los_Angeles');
            @endphp
            <tr>
                <td>Original Order Timestamp</td>
                <td>{{ $clubTime->format('M d, Y h:i A T') }}</td>
            </tr>
            @endif
        </table>

        <!-- Purchased Items -->
        @php
            $cartItems = is_array($transaction->cart_items) ? $transaction->cart_items : (json_decode($transaction->cart_items, true) ?: []);
            $breakdown = $transaction->resolvePriceBreakdown($website);
            $customInvoice = !empty($transaction->custom_invoice_id) ? \App\Models\CustomInvoice::find($transaction->custom_invoice_id) : null;
        @endphp
        @if(!empty($cartItems))
        <div class="section-heading">Order Summary</div>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Item &amp; Inclusions</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Price</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cartItems as $item)
                @php
                    $itemName = $item['package_name'] ?? $item['name'] ?? 'Package Item';
                    $itemQty = max(1, (int) ($item['guests'] ?? $item['quantity'] ?? 1));
                    $itemUnitPrice = (float) ($item['unit_price'] ?? $item['price'] ?? 0);
                    $itemTotal = (float) ($item['line_total'] ?? $item['total'] ?? ($itemUnitPrice * $itemQty));
                @endphp
                <tr>
                    <td style="font-weight:700;">{{ html_entity_decode($itemName, ENT_QUOTES | ENT_HTML5, 'UTF-8') }}</td>
                    <td class="text-right">{{ $itemQty }}</td>
                    <td class="text-right">${{ number_format($itemUnitPrice, 2) }}</td>
                    <td class="text-right" style="font-weight:700;">${{ number_format($itemTotal, 2) }}</td>
                </tr>
                @if(!empty($item['addons']))
                    @foreach($item['addons'] as $addon)
                        @if(!empty($addon['name']))
                        <tr style="background:#f8fafc;font-size:12px;color:#475569;">
                            <td style="padding-left:20px;">+ {{ $addon['name'] }}</td>
                            <td class="text-right">{{ $addon['qty'] ?? 1 }}</td>
                            <td class="text-right">
                                @if((float) ($addon['unit_price'] ?? 0) > 0)
                                    ${{ number_format((float) ($addon['unit_price'] ?? 0), 2) }}
                                @else
                                    Included
                                @endif
                            </td>
                            <td class="text-right">
                                @if((float) ($addon['price'] ?? 0) > 0)
                                    ${{ number_format((float) ($addon['price'] ?? 0), 2) }}
                                @else
                                    Included
                                @endif
                            </td>
                        </tr>
                        @endif
                    @endforeach
                @endif
                @endforeach
            </tbody>
        </table>
        @endif

        <!-- Price Summary Breakdown -->
        <table class="info-table" style="background:#f8fafc;padding:14px;border-radius:8px;border:1px solid #e2e8f0;margin-top:16px;">
            @if($customInvoice)
                <tr>
                    <td>Subtotal</td>
                    <td>${{ number_format((float) $customInvoice->subtotal, 2) }}</td>
                </tr>
                @if((float) ($customInvoice->tax ?? 0) > 0)
                <tr>
                    <td>Sales Tax</td>
                    <td>${{ number_format((float) $customInvoice->tax, 2) }}</td>
                </tr>
                @endif
                @if((float) ($customInvoice->service_charge ?? 0) > 0)
                <tr>
                    <td>Service Charge</td>
                    <td>${{ number_format((float) $customInvoice->service_charge, 2) }}</td>
                </tr>
                @endif
                @if((float) ($customInvoice->gratuity ?? 0) > 0)
                <tr>
                    <td>Gratuity</td>
                    <td>${{ number_format((float) $customInvoice->gratuity, 2) }}</td>
                </tr>
                @endif
                @if((float) ($customInvoice->processing_fee ?? 0) > 0)
                <tr>
                    <td>Processing Fee</td>
                    <td>${{ number_format((float) $customInvoice->processing_fee, 2) }}</td>
                </tr>
                @endif
                @if((float) ($customInvoice->deposit_amount ?? 0) > 0)
                <tr>
                    <td style="color:#d97706;font-weight:700;">Required Deposit Due Now</td>
                    <td style="color:#d97706;font-weight:700;">${{ number_format((float) ($transaction->total ?? $customInvoice->deposit_amount), 2) }}</td>
                </tr>
                <tr style="font-size:12px;color:#64748b;">
                    <td>Remaining Balance (Due on Arrival)</td>
                    <td>${{ number_format((float) $customInvoice->remaining_balance, 2) }}</td>
                </tr>
                @endif
            @else
                @php
                    $pkgSubtotal = (float) ($breakdown['packages_subtotal'] ?? 0);
                    $addonSubtotal = (float) ($breakdown['addons_subtotal'] ?? 0);
                    $itemsSubtotal = ($pkgSubtotal + $addonSubtotal) > 0 ? ($pkgSubtotal + $addonSubtotal) : (float) ($transaction->actual_total ?: $transaction->total);
                @endphp
                <tr>
                    <td>Items Subtotal</td>
                    <td>${{ number_format($itemsSubtotal, 2) }}</td>
                </tr>
                @if(!empty($breakdown['sales_tax']['enabled']) && (float) ($breakdown['sales_tax']['amount'] ?? 0) > 0)
                <tr>
                    <td>{{ $breakdown['sales_tax']['name'] ?? 'Sales Tax' }} ({{ number_format((float) ($breakdown['sales_tax']['rate'] ?? 0), 2) }}%)</td>
                    <td>${{ number_format((float) ($breakdown['sales_tax']['amount'] ?? 0), 2) }}</td>
                </tr>
                @endif
                @if(!empty($breakdown['service_charge']['enabled']) && (float) ($breakdown['service_charge']['amount'] ?? 0) > 0)
                <tr>
                    <td>{{ $breakdown['service_charge']['name'] ?? 'Service Charge' }} ({{ number_format((float) ($breakdown['service_charge']['rate'] ?? 0), 2) }}%)</td>
                    <td>${{ number_format((float) ($breakdown['service_charge']['amount'] ?? 0), 2) }}</td>
                </tr>
                @endif
                @if(!empty($breakdown['gratuity']['enabled']) && (float) ($breakdown['gratuity']['amount'] ?? 0) > 0)
                <tr>
                    <td>{{ $breakdown['gratuity']['name'] ?? 'Gratuity' }} ({{ number_format((float) ($breakdown['gratuity']['rate'] ?? 0), 2) }}%)</td>
                    <td>${{ number_format((float) ($breakdown['gratuity']['amount'] ?? 0), 2) }}</td>
                </tr>
                @endif
                @if(!empty($breakdown['processing_fee']['enabled']) && (float) ($breakdown['processing_fee']['amount'] ?? 0) > 0)
                <tr>
                    <td>{{ $breakdown['processing_fee']['name'] ?? 'Processing Fee' }}</td>
                    <td>${{ number_format((float) ($breakdown['processing_fee']['amount'] ?? 0), 2) }}</td>
                </tr>
                @endif
                @if((float) ($breakdown['promo_discount'] ?? $transaction->discounted_amount ?? 0) > 0)
                <tr style="color:#16a34a;">
                    <td>Promo Discount</td>
                    <td>-${{ number_format((float) ($breakdown['promo_discount'] ?? $transaction->discounted_amount), 2) }}</td>
                </tr>
                @endif
            @endif

            <tr style="border-top:2px solid #cbd5e1;">
                <td style="font-size:16px;font-weight:800;color:#0f172a;">Total Amount To Complete</td>
                <td style="font-size:18px;color:#d97706;font-weight:800;">${{ number_format((float) $transaction->total, 2) }}</td>
            </tr>
        </table>

        <!-- Secondary CTA Link -->
        <div style="margin-top: 24px; font-size: 13px; color: #64748b; text-align: center;">
            If the button above does not work, copy and paste this link into your browser:<br>
            <a href="{{ $repayUrl }}" style="color: #d97706; word-break: break-all;">{{ $repayUrl }}</a>
        </div>
    </div>

    <!-- Footer -->
    <div class="email-footer">
        <p>Thank you for choosing {{ $website->name ?? 'CartVIP' }}.</p>
        <p>This is an automated system message. If you have questions, please reply directly or contact support.</p>
        <p style="margin-top: 8px; font-size: 11px;">CartVIP &bull; Secure VIP Hospitality & Payment Systems</p>
    </div>
</div>
</body>
</html>
