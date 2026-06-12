<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Transaction Details - {{ $transaction->transaction_id }}</title>
    <style>
        @media print {
            body { margin: 0; padding: 10mm; }
            .page-break { page-break-after: always; }
        }
        body { font-family: Arial, sans-serif; color: #333; line-height: 1.4; font-size: 12px; }
        .container { max-width: 900px; margin: 0 auto; }
        .header { border-bottom: 2px solid #333; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { margin: 0 0 10px 0; font-size: 20px; }
        .header p { margin: 3px 0; font-size: 11px; }
        .section { margin-bottom: 20px; page-break-inside: avoid; }
        .section-title { font-size: 12px; font-weight: bold; margin-bottom: 10px; border-bottom: 1px solid #999; padding-bottom: 5px; background: #f5f5f5; padding: 5px 5px 5px 5px; }
        .two-column { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .list-group { list-style: none; padding: 0; margin: 0; }
        .list-group-item { padding: 8px; border: 1px solid #ddd; border-bottom: none; background: #fafafa; }
        .list-group-item:last-child { border-bottom: 1px solid #ddd; }
        .list-group-item strong { display: inline-block; width: 40%; font-weight: bold; }
        .list-group-item span { display: inline-block; width: 60%; word-break: break-word; }
        .badge { display: inline-block; padding: 3px 6px; border-radius: 3px; font-weight: bold; font-size: 10px; }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-danger { background: #f8d7da; color: #721c24; }
        .badge-secondary { background: #e2e3e5; color: #383d41; }
        .footer { margin-top: 40px; border-top: 1px solid #ddd; padding-top: 10px; text-align: center; color: #999; font-size: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Transaction Details</h1>
            <p><strong>Confirmation ID:</strong> {{ htmlspecialchars($transaction->transaction_id ?? '') }}</p>
            <p><strong>Type:</strong> {{ ucfirst($transaction->type ?? 'package') }} | <strong>Generated:</strong> {{ now()->format('M d, Y h:i A') }}</p>
        </div>

        <div class="two-column">
            <div>
                <div class="section">
                    <div class="section-title">📋 Package & Guest Information</div>
                    <ul class="list-group">
                        <li class="list-group-item"><strong>Order Items:</strong> <span>{{ htmlspecialchars($transaction->package_table_label ?? '') }}</span></li>
                        <li class="list-group-item"><strong>Package Date Of Use:</strong> <span>{{ htmlspecialchars($transaction->package_use_date ?? '') }}</span></li>
                        <li class="list-group-item"><strong>First Name:</strong> <span>{{ htmlspecialchars($transaction->package_first_name ?? '') }}</span></li>
                        <li class="list-group-item"><strong>Last Name:</strong> <span>{{ htmlspecialchars($transaction->package_last_name ?? '') }}</span></li>
                        <li class="list-group-item"><strong>Phone:</strong> <span>{{ htmlspecialchars($transaction->package_phone ?? '') }}</span></li>
                        <li class="list-group-item"><strong>Email:</strong> <span>{{ htmlspecialchars($transaction->package_email ?? '') }}</span></li>
                        <li class="list-group-item"><strong>DOB:</strong> <span>{{ htmlspecialchars($transaction->package_dob ?? '') }}</span></li>
                        <li class="list-group-item"><strong>Number of Guests:</strong> <span>{{ htmlspecialchars($transaction->package_number_of_guest ?? '') }}</span></li>
                        <li class="list-group-item"><strong>Male Guests:</strong> <span>{{ htmlspecialchars($transaction->package_men ?? '') }}</span></li>
                        <li class="list-group-item"><strong>Female Guests:</strong> <span>{{ htmlspecialchars($transaction->package_women ?? '') }}</span></li>
                        <li class="list-group-item"><strong>Booking Note:</strong> <span>{{ htmlspecialchars($transaction->package_note ?? '') }}</span></li>
                    </ul>
                </div>

                <div class="section">
                    <div class="section-title">Transportation Details</div>
                    <ul class="list-group">
                        <li class="list-group-item"><strong>Pickup Time:</strong> <span>{{ htmlspecialchars($transaction->transportation_pickup_time ?? '') }}</span></li>
                        <li class="list-group-item"><strong>Pickup Location:</strong> <span>{{ htmlspecialchars($transaction->transportation_address ?? '') }}</span></li>
                        <li class="list-group-item"><strong>Phone:</strong> <span>{{ htmlspecialchars($transaction->transportation_phone ?? '') }}</span></li>
                        <li class="list-group-item"><strong>Host Name:</strong> <span>{{ htmlspecialchars($transaction->host_name ?? '') }}</span></li>
                        <li class="list-group-item"><strong>Note:</strong> <span>{{ htmlspecialchars($transaction->transportation_note ?? '') }}</span></li>
                    </ul>
                </div>

                <div class="section">
                    <div class="section-title">Business Information</div>
                    <ul class="list-group">
                        <li class="list-group-item"><strong>Company Name:</strong> <span>{{ htmlspecialchars($transaction->business_company ?? '') }}</span></li>
                        <li class="list-group-item"><strong>Tax ID:</strong> <span>{{ htmlspecialchars($transaction->business_vat ?? '') }}</span></li>
                        <li class="list-group-item"><strong>Address:</strong> <span>{{ htmlspecialchars($transaction->business_address ?? '') }}</span></li>
                    </ul>
                </div>
            </div>

            <div>
                <div class="section">
                    <div class="section-title">💳 Payment Information</div>
                    <ul class="list-group">
                        <li class="list-group-item"><strong>First Name:</strong> <span>{{ htmlspecialchars($transaction->payment_first_name ?? '') }}</span></li>
                        <li class="list-group-item"><strong>Last Name:</strong> <span>{{ htmlspecialchars($transaction->payment_last_name ?? '') }}</span></li>
                        <li class="list-group-item"><strong>Phone:</strong> <span>{{ htmlspecialchars($transaction->payment_phone ?? '') }}</span></li>
                        <li class="list-group-item"><strong>Email:</strong> <span>{{ htmlspecialchars($transaction->payment_email ?? '') }}</span></li>
                        <li class="list-group-item"><strong>Address:</strong> <span>{{ htmlspecialchars($transaction->payment_address ?? '') }}</span></li>
                        <li class="list-group-item"><strong>City:</strong> <span>{{ htmlspecialchars($transaction->payment_city ?? '') }}</span></li>
                        <li class="list-group-item"><strong>State:</strong> <span>{{ htmlspecialchars($transaction->payment_state ?? '') }}</span></li>
                        <li class="list-group-item"><strong>Zip Code:</strong> <span>{{ htmlspecialchars($transaction->payment_zip_code ?? '') }}</span></li>
                        <li class="list-group-item"><strong>Country:</strong> <span>{{ htmlspecialchars($transaction->payment_country ?? '') }}</span></li>
                        <li class="list-group-item"><strong>DOB:</strong> <span>{{ htmlspecialchars($transaction->payment_dob ?? '') }}</span></li>
                    </ul>
                </div>

            </div>
        </div>

        <div class="section">
            <div class="section-title">Purchased Items</div>
            @php
                $cartItems = is_array($transaction->cart_items) ? $transaction->cart_items : json_decode($transaction->cart_items ?? '[]', true);
            @endphp
            @if(!empty($cartItems))
                <ul class="list-group">
                    @foreach($cartItems as $item)
                        <li class="list-group-item">
                            <strong>{{ $item['package_name'] ?? 'Package' }}</strong><br>
                            Quantity: {{ max(1, (int)($item['guests'] ?? $item['quantity'] ?? 1)) }}<br>
                            Price: ${{ number_format((float)($item['unit_price'] ?? 0), 2) }}
                            @if(!empty($item['addons']))
                                <br><strong>Add-ons:</strong>
                                @foreach($item['addons'] as $addon)
                                    <br>&nbsp;&nbsp;• {{ $addon['name'] ?? 'Add-on' }} x{{ $addon['qty'] ?? 1 }} - ${{ number_format((float)($addon['price'] ?? 0), 2) }}
                                @endforeach
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="section">
            <div class="section-title">💰 Transaction Summary</div>
            <ul class="list-group">
                <li class="list-group-item"><strong>Promo Code:</strong> <span>{{ htmlspecialchars($transaction->promo_code ?? '-') }}</span></li>
                <li class="list-group-item"><strong>Discounted Amount:</strong> <span>${{ number_format((float)($transaction->discount ?? 0), 2) }}</span></li>
                <li class="list-group-item"><strong>Subtotal:</strong> <span>${{ number_format((float)($transaction->sub_total ?? 0), 2) }}</span></li>
                <li class="list-group-item"><strong>Gratuity:</strong> <span>${{ number_format((float)($transaction->gratuity ?? 0), 2) }}</span></li>
                <li class="list-group-item"><strong>Non-Refundable Deposit:</strong> <span>${{ number_format((float)($transaction->refundable ?? 0), 2) }}</span></li>
                <li class="list-group-item"><strong>Total Amount:</strong> <span><strong>${{ number_format((float)($transaction->total ?? 0), 2) }}</strong></span></li>
                <li class="list-group-item"><strong>Total Due:</strong> <span><strong>${{ number_format((float)($transaction->due ?? 0), 2) }}</strong></span></li>
                <li class="list-group-item">
                    <strong>Status:</strong> <span>
                        @if($transaction->status == 1)
                            <span class="badge badge-success">Completed</span>
                        @elseif($transaction->status == 0)
                            <span class="badge badge-danger">Canceled</span>
                        @elseif($transaction->status == 2)
                            <span class="badge badge-warning">Refunded</span>
                        @else
                            <span class="badge badge-secondary">Unknown</span>
                        @endif
                    </span>
                </li>
            </ul>
        </div>

        <div class="section">
            <div class="section-title">🏆 Commission & Dates</div>
            <ul class="list-group">
                @php
                    $affiliateName = $transaction->affiliate ? ($transaction->affiliate->display_name ?: optional($transaction->affiliate->user)->name) : '';
                    $entertainerName = $transaction->entertainer ? ($transaction->entertainer->display_name ?: optional($transaction->entertainer->user)->name) : '';
                    $totalCommission = (float)($transaction->affiliate_commission_amount ?? 0) + (float)($transaction->entertainer_commission_amount ?? 0);
                @endphp
                <li class="list-group-item"><strong>Total Commission:</strong> <span>${{ number_format($totalCommission, 2) }}</span></li>
                @if($affiliateName || $transaction->affiliate_commission_amount)
                <li class="list-group-item"><strong>Affiliate:</strong> <span>{{ $affiliateName ?: 'N/A' }} ({{ $transaction->affiliate_commission_percentage ?? 0 }}% | ${{ number_format((float)($transaction->affiliate_commission_amount ?? 0), 2) }})</span></li>
                @endif
                @if($entertainerName || $transaction->entertainer_commission_amount)
                <li class="list-group-item"><strong>Entertainer:</strong> <span>{{ $entertainerName ?: 'N/A' }} ({{ $transaction->entertainer_commission_percentage ?? 0 }}% | ${{ number_format((float)($transaction->entertainer_commission_amount ?? 0), 2) }})</span></li>
                @endif
                <li class="list-group-item"><strong>Date (Pacific Time):</strong> <span>{{ $transaction->created_at ? $transaction->created_at->timezone('America/Los_Angeles')->format('M d, Y h:i A') : '' }}</span></li>
                <li class="list-group-item"><strong>IP Address:</strong> <span>{{ htmlspecialchars($transaction->ip_address ?? '') }}</span></li>
            </ul>
        </div>

        <div class="footer">
            <p>This is a confidential document. CartVIP © {{ now()->year }} | Page generated automatically</p>
        </div>
    </div>
</body>
</html>
