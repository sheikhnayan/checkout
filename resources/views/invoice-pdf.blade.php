<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice - {{ $transaction->transaction_id }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Helvetica, Arial, 'DejaVu Sans', sans-serif;
            color: #333;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
        }
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #667eea;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header-content {
            flex: 1;
        }
        .header-title {
            font-size: 28px;
            font-weight: bold;
            color: #667eea;
            margin: 0 0 10px 0;
        }
        .header-subtitle {
            font-size: 14px;
            color: #666;
            margin: 0;
        }
        .header-qr {
            text-align: center;
            margin-left: 20px;
        }
        .header-qr img {
            width: 120px;
            height: 120px;
            border: 1px solid #ddd;
            padding: 5px;
            background: white;
        }
        .header-qr-label {
            font-size: 10px;
            color: #999;
            margin-top: 5px;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 14px;
            font-weight: 700;
            color: #333;
            text-transform: uppercase;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 13px;
        }
        .info-label {
            color: #666;
            font-weight: 600;
        }
        .info-label::after {
            content: ': ';
        }
        .info-value {
            color: #333;
        }
        .grid-2 {
            display: flex;
            gap: 40px;
            margin-bottom: 20px;
        }
        .grid-2 .section {
            flex: 1;
            margin-bottom: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 13px;
        }
        thead {
            background-color: #f5f5f5;
        }
        th {
            padding: 10px 8px;
            text-align: left;
            font-weight: 700;
            color: #333;
            border-bottom: 2px solid #ddd;
        }
        td {
            padding: 10px 8px;
            border-bottom: 1px solid #eee;
        }
        tr:last-child td {
            border-bottom: none;
        }
        .text-right {
            text-align: right;
        }
        .total-section {
            background-color: #f9f9f9;
            padding: 15px;
            border-left: 3px solid #667eea;
            margin-top: 20px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 13px;
        }
        .total-row.grand-total {
            font-size: 16px;
            font-weight: 700;
            color: #667eea;
            border-top: 1px solid #ddd;
            padding-top: 12px;
            margin-top: 8px;
        }
        .addon-row td:first-child {
            padding-left: 24px;
            color: #666;
            font-size: 12px;
        }
        .addons-label {
            color: #999;
            font-size: 11px;
        }
        .footer {
            border-top: 1px solid #e0e0e0;
            padding-top: 20px;
            margin-top: 30px;
            font-size: 11px;
            color: #666;
            text-align: center;
        }
    </style>
</head>
<body>
@php
    $club = $website ?? ($transaction->website ?? null);
    $reservationDateRaw = $transaction->package_use_date ?? null;
    $reservationDateFormatted = 'N/A';
    if (!empty($reservationDateRaw)) {
        try {
            $reservationDateFormatted = \Carbon\Carbon::parse($reservationDateRaw)->format('M d, Y');
        } catch (\Throwable $e) {
            $reservationDateFormatted = (string) $reservationDateRaw;
        }
    }
@endphp
<div class="invoice-container">
    <!-- Header -->
    <div class="header">
        <div class="header-content">
            <h1 class="header-title">INVOICE</h1>
            <p class="header-subtitle">Confirmation ID: {{ $transaction->transaction_id }}</p>
        </div>
        @if(($showQrInPdf ?? true) && $transaction->ticket_qr_code)
        <div class="header-qr">
            @if(!empty($qrCodeBase64))
                <img src="{{ $qrCodeBase64 }}" alt="QR Code" width="120" height="120">
            @endif
            <div class="header-qr-label">Scan for Details</div>
            <div class="header-qr-label">Ticket #: {{ $transaction->ticket_qr_code }}</div>
        </div>
        @endif
    </div>

    <!-- Order Summary Section (appears first) -->
    <div class="section">
        <div class="section-title">Order Summary</div>
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Unit Price</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @if(!empty($priceBreakdown) && !empty($priceBreakdown['items']))
                    @php
                        $totalItemQty = 0;
                        $totalAddonQty = 0;
                    @endphp
                    @foreach($priceBreakdown['items'] as $item)
                        @php
                            $totalItemQty += 1;
                            $totalAddonQty += count($item['addons'] ?? []);
                        @endphp
                        <tr>
                            <td>{{ $item['package_name'] ?? 'Package' }}</td>
                            <td class="text-right">{{ $item['guests'] }}</td>
                            <td class="text-right">${{ number_format($item['unit_price'], 2) }}</td>
                            <td class="text-right">${{ number_format($item['package_subtotal'], 2) }}</td>
                        </tr>
                        @if(!empty($item['is_multiple']) && $item['guests'] > 1)
                        <tr class="addon-row">
                            <td colspan="4"><span class="addons-label">${{ number_format($item['unit_price'], 2) }} x {{ $item['guests'] }} guests</span></td>
                        </tr>
                        @endif
                        @foreach($item['addons'] ?? [] as $addon)
                            @if(!empty($addon['name']))
                            <tr class="addon-row">
                                <td>+ {{ $addon['name'] }} x{{ $addon['qty'] }}</td>
                                <td class="text-right">{{ $addon['qty'] }}</td>
                                <td class="text-right">
                                    @if($addon['price'] > 0)
                                        ${{ number_format($addon['unit_price'], 2) }}
                                    @else
                                        <span class="addons-label">Included</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    @if($addon['price'] > 0)
                                        ${{ number_format($addon['price'], 2) }}
                                    @else
                                        <span class="addons-label">—</span>
                                    @endif
                                </td>
                            </tr>
                            @endif
                        @endforeach
                    @endforeach
                @elseif(!empty($cartItems))
                    @php
                        $grandTotal = 0;
                        $totalAddonQty = 0;
                        $totalItemQty = count($cartItems);
                    @endphp
                    @foreach($cartItems as $cartItem)
                        @php
                            $isMultiple = !empty($cartItem['is_multiple']);
                            $guests = max(1, (int) ($cartItem['guests'] ?? 1));
                            $unitPrice = (float) ($cartItem['unit_price'] ?? 0);
                            $pkgSubtotal = $isMultiple ? $unitPrice * $guests : $unitPrice;
                            $addonsTotal = collect($cartItem['addons'] ?? [])->sum(fn($a) => (float)($a['price'] ?? 0));
                            $itemTotal = (float) ($cartItem['line_total'] ?? ($pkgSubtotal + $addonsTotal));
                            $grandTotal += $itemTotal;
                        @endphp
                        <tr>
                            <td>{{ $cartItem['package_name'] ?? 'Package' }}</td>
                            <td class="text-right">{{ $guests }}</td>
                            <td class="text-right">${{ number_format($unitPrice, 2) }}</td>
                            <td class="text-right">${{ number_format($pkgSubtotal, 2) }}</td>
                        </tr>
                        @if($isMultiple && $guests > 1)
                        <tr class="addon-row">
                            <td colspan="4"><span class="addons-label">${{ number_format($unitPrice, 2) }} x {{ $guests }} guests</span></td>
                        </tr>
                        @endif
                        @foreach($cartItem['addons'] ?? [] as $addon)
                            @if(!empty($addon['name']))
                            @php
                                $addonQty = max(1, (int) ($addon['qty'] ?? 1));
                                $addonLineTotal = (float) ($addon['price'] ?? 0);
                                $addonUnitPrice = isset($addon['unit_price'])
                                    ? (float) $addon['unit_price']
                                    : ($addonQty > 0 ? $addonLineTotal / $addonQty : $addonLineTotal);
                                if (($addonUnitPrice <= 0 || !isset($addon['qty'])) && !empty($addon['id'])) {
                                    $catalogUnit = (float) optional(\App\Models\Addon::find((int) $addon['id']))->price;
                                    if ($catalogUnit > 0) {
                                        if ($addonUnitPrice <= 0) {
                                            $addonUnitPrice = $catalogUnit;
                                        }
                                        if (!isset($addon['qty']) && $addonLineTotal > 0) {
                                            $estimatedQty = (int) round($addonLineTotal / $catalogUnit);
                                            if ($estimatedQty > 0) {
                                                $addonQty = $estimatedQty;
                                            }
                                        }
                                    }
                                }
                            @endphp
                            @php($totalAddonQty += $addonQty)
                            <tr class="addon-row">
                                <td>+ {{ $addon['name'] }} x{{ $addonQty }}</td>
                                <td class="text-right">{{ $addonQty }}</td>
                                <td class="text-right">
                                    @if($addonLineTotal > 0)
                                        ${{ number_format($addonUnitPrice, 2) }}
                                    @else
                                        <span class="addons-label">Included</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    @if($addonLineTotal > 0)
                                        ${{ number_format($addonLineTotal, 2) }}
                                    @else
                                        <span class="addons-label">—</span>
                                    @endif
                                </td>
                            </tr>
                            @endif
                        @endforeach
                    @endforeach
                @endif
            </tbody>
        </table>

        <!-- Price Breakdown -->
        @if(!empty($priceBreakdown))
        <div class="total-section">
            <div class="total-row">
                <span>Total Item Qty</span>
                <span>{{ $totalItemQty ?? 0 }}</span>
            </div>
            <div class="total-row">
                <span>Total Add-on Qty</span>
                <span>{{ $totalAddonQty ?? 0 }}</span>
            </div>
            <div class="total-row">
                <span>Subtotal</span>
                <span>${{ number_format((float) ($priceBreakdown['items_subtotal'] ?? (($priceBreakdown['packages_subtotal'] ?? 0) + ($priceBreakdown['addons_subtotal'] ?? 0))), 2) }}</span>
            </div>

            @if(!empty($priceBreakdown['gratuity']['enabled']))
            <div class="total-row">
                <span>{{ $priceBreakdown['gratuity']['name'] }} ({{ number_format((float) $priceBreakdown['gratuity']['rate'], 2) }}%)</span>
                <span>${{ number_format((float) $priceBreakdown['gratuity']['amount'], 2) }}</span>
            </div>
            @endif

            @if(!empty($priceBreakdown['service_charge']['enabled']))
            <div class="total-row">
                <span>{{ $priceBreakdown['service_charge']['name'] }} ({{ number_format((float) $priceBreakdown['service_charge']['rate'], 2) }}%)</span>
                <span>${{ number_format((float) $priceBreakdown['service_charge']['amount'], 2) }}</span>
            </div>
            @endif

            @if(!empty($priceBreakdown['sales_tax']['enabled']))
            <div class="total-row">
                <span>{{ $priceBreakdown['sales_tax']['name'] }} ({{ number_format((float) $priceBreakdown['sales_tax']['rate'], 2) }}%)</span>
                <span>${{ number_format((float) $priceBreakdown['sales_tax']['amount'], 2) }}</span>
            </div>
            @endif

            @if((float) ($priceBreakdown['promo_discount'] ?? 0) > 0)
            <div class="total-row">
                <span>Promo Code Discount</span>
                <span>-${{ number_format((float) $priceBreakdown['promo_discount'], 2) }}</span>
            </div>
            @endif

            @if(!empty($priceBreakdown['processing_fee']['enabled']))
            <div class="total-row">
                <span>Processing Fee @if(($priceBreakdown['processing_fee']['type'] ?? 'percentage') === 'percentage')({{ number_format((float) $priceBreakdown['processing_fee']['rate'], 2) }}%)@endif</span>
                <span>${{ number_format((float) $priceBreakdown['processing_fee']['amount'], 2) }}</span>
            </div>
            @endif

            <div class="total-row grand-total">
                <span>Amount Due</span>
                <span>${{ number_format((float) ($priceBreakdown['grand_total'] ?? $transaction->total), 2) }}</span>
            </div>
        </div>
        @else
        <div class="total-section">
            <div class="total-row grand-total">
                <span>Amount Due</span>
                <span>${{ number_format((float) $transaction->total, 2) }}</span>
            </div>
        </div>
        @endif
    </div>

    <!-- Billing & Order Info -->
    <div class="grid-2">
        <div class="section">
            <div class="section-title">Bill To</div>
            <div class="info-row">
                <span class="info-label">Name</span>
                <span class="info-value">{{ trim(($transaction->package_first_name ?? '') . ' ' . ($transaction->package_last_name ?? '')) ?: 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Email</span>
                <span class="info-value">{{ $transaction->package_email ?? 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Phone</span>
                <span class="info-value">{{ $transaction->package_phone ?? 'N/A' }}</span>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Order Details</div>
            <div class="info-row">
                <span class="info-label">Reservation Date</span>
                <span class="info-value">{{ $reservationDateFormatted }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Payment Date</span>
                <span class="info-value">{{ $transaction->updated_at->format('M d, Y h:i A') }}</span>
            </div>
            @if($transaction->type === 'package' && $transaction->event)
            <div class="info-row">
                <span class="info-label">Event</span>
                <span class="info-value">{{ $transaction->event->name ?? 'N/A' }}</span>
            </div>
            @endif
        </div>
    </div>

    <div class="section">
        <div class="section-title">Club Details</div>
        <div class="info-row">
            <span class="info-label">Club</span>
            <span class="info-value">{{ $club->name ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Location</span>
            <span class="info-value">{{ $club->location ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Phone</span>
            <span class="info-value">{{ $club->phone ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Email</span>
            <span class="info-value">{{ $club->email ?? 'N/A' }}</span>
        </div>
    </div>

    @if($transaction->package_note || $transaction->transportation_pickup_time || $transaction->transportation_address || $transaction->transportation_phone || $transaction->host_name || $transaction->transportation_note)
    <div class="section">
        <div class="section-title">Booking & Transportation Details</div>
        @if($transaction->package_note)
        <div class="info-row">
            <span class="info-label">Booking Note</span>
            <span class="info-value">{{ $transaction->package_note }}</span>
        </div>
        @endif
        @if($transaction->transportation_pickup_time)
        <div class="info-row">
            <span class="info-label">Pickup Time</span>
            <span class="info-value">{{ \Carbon\Carbon::createFromFormat('H:i', $transaction->transportation_pickup_time)->format('h:i A') }}</span>
        </div>
        @endif
        @if($transaction->transportation_address)
        <div class="info-row">
            <span class="info-label">Pickup Location</span>
            <span class="info-value">{{ $transaction->transportation_address }}</span>
        </div>
        @endif
        @if($transaction->transportation_phone)
        <div class="info-row">
            <span class="info-label">Contact Phone</span>
            <span class="info-value">{{ $transaction->transportation_phone }}</span>
        </div>
        @endif
        @if($transaction->host_name)
        <div class="info-row">
            <span class="info-label">Host Name</span>
            <span class="info-value">{{ $transaction->host_name }}</span>
        </div>
        @endif
        @if($transaction->transportation_note)
        <div class="info-row">
            <span class="info-label">Transportation Note</span>
            <span class="info-value">{{ $transaction->transportation_note }}</span>
        </div>
        @endif
    </div>
    @endif

    @if($transaction->business_company || $transaction->business_vat || $transaction->business_address)
    <div class="section">
        <div class="section-title">Business Information</div>
        @if($transaction->business_company)
        <div class="info-row">
            <span class="info-label">Company Name</span>
            <span class="info-value">{{ $transaction->business_company }}</span>
        </div>
        @endif
        @if($transaction->business_vat)
        <div class="info-row">
            <span class="info-label">Tax ID</span>
            <span class="info-value">{{ $transaction->business_vat }}</span>
        </div>
        @endif
        @if($transaction->business_address)
        <div class="info-row">
            <span class="info-label">Business Address</span>
            <span class="info-value">{{ $transaction->business_address }}</span>
        </div>
        @endif
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Thank you for your purchase! This is an automated invoice. Please do not reply to this email.</p>
        <p style="margin-top: 10px; font-size: 10px; color: #999;">cartvip.com</p>
    </div>
</div>
</body>
</html>
