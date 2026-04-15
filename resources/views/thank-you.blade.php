<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You - Payment Successful</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        .thank-you-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 600px;
            width: 100%;
            overflow: hidden;
            animation: slideUp 0.6s ease-out;
        }
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .thank-you-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 50px 30px;
            text-align: center;
            position: relative;
        }
        .success-icon {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px auto;
            animation: scaleIn 0.5s ease-out 0.3s both;
        }
        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }
        .success-icon i {
            font-size: 40px;
            color: #667eea;
        }
        .thank-you-header h1 {
            margin: 0 0 10px 0;
            font-size: 32px;
            font-weight: 600;
        }
        .thank-you-header p {
            margin: 0;
            font-size: 16px;
            opacity: 0.9;
        }
        .content-section {
            padding: 40px 30px;
        }
        .detail-box {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 25px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-row label {
            color: #6c757d;
            font-weight: 500;
            font-size: 14px;
        }
        .detail-row span {
            color: #212529;
            font-weight: 600;
            font-size: 14px;
        }
        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #667eea;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 25px;
        }
        .info-box i {
            color: #667eea;
            margin-right: 10px;
        }
        .info-box p {
            margin: 0;
            color: #495057;
            font-size: 14px;
            line-height: 1.6;
        }
        .item-list {
            margin: 0;
            padding-left: 18px;
        }
        .item-list li {
            margin-bottom: 8px;
            color: #212529;
            font-size: 14px;
        }
        .item-list small {
            display: block;
            color: #6c757d;
            margin-top: 3px;
        }
        .breakdown-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
            font-size: 14px;
        }
        .breakdown-table th {
            text-align: left;
            padding: 8px 10px;
            background: #f0f4f8;
            color: #495057;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }
        .breakdown-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #e9ecef;
            vertical-align: middle;
        }
        .breakdown-table tr:last-child td {
            border-bottom: none;
        }
        .breakdown-item-header {
            background: #f8f9fa;
            font-weight: 600;
            color: #212529;
        }
        .breakdown-addon-row td {
            color: #6c757d;
            font-size: 13px;
            padding-top: 4px;
            padding-bottom: 4px;
        }
        .breakdown-addon-row td:first-child {
            padding-left: 24px;
        }
        .breakdown-subtotal-row td {
            border-top: 1px solid #dee2e6;
            font-weight: 600;
            background: #eef2ff;
            color: #3730a3;
        }
        .breakdown-grand-total {
            font-weight: 700;
            font-size: 15px;
            background: #dbeafe;
            color: #1d4ed8;
        }
        .price-right {
            text-align: right;
            white-space: nowrap;
        }
        .text-muted-sm {
            color: #6c757d;
            font-size: 12px;
        }
        .action-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        .btn-primary-custom {
            flex: 1;
            min-width: 200px;
            padding: 15px 25px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }
        .btn-secondary-custom {
            flex: 1;
            min-width: 200px;
            padding: 15px 25px;
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        .btn-secondary-custom:hover {
            background: #f8f9fa;
            transform: translateY(-2px);
            color: #667eea;
        }
        .footer-note {
            text-align: center;
            padding: 20px 30px;
            background: #f8f9fa;
            color: #6c757d;
            font-size: 13px;
            border-top: 1px solid #e9ecef;
        }
        .footer-note i {
            color: #667eea;
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <div class="thank-you-container">
        <div class="thank-you-header">
            <div class="success-icon">
                <i class="fas fa-check"></i>
            </div>
            <h1>Thank You!</h1>
            <p>Your payment has been processed successfully</p>
        </div>

        <div class="content-section">
            @if(isset($transaction))
            @php
                $rawCartItems = $transaction->cart_items ?? [];
                if (is_array($rawCartItems)) {
                    $cartItems = $rawCartItems;
                } elseif (is_string($rawCartItems)) {
                    $decodedCartItems = json_decode($rawCartItems, true);
                    if (is_string($decodedCartItems)) {
                        $decodedCartItems = json_decode($decodedCartItems, true);
                    }
                    $cartItems = is_array($decodedCartItems) ? $decodedCartItems : [];
                } else {
                    $cartItems = [];
                }
            @endphp
            <div class="detail-box">
                <div class="detail-row">
                    <label>Transaction ID</label>
                    <span>{{ $transaction->transaction_id }}</span>
                </div>
                @if($transaction->type === 'custom_invoice' && isset($invoice))
                <div class="detail-row">
                    <label>Invoice Number</label>
                    <span>#{{ $invoice->id }}</span>
                </div>
                @endif
                <div class="detail-row">
                    <label>Amount Paid</label>
                    <span>${{ number_format($transaction->total, 2) }}</span>
                </div>
                @if(!empty($priceBreakdown) && !empty($priceBreakdown['grand_total']))
                <div class="detail-row">
                    <label>Order Total</label>
                    <span>${{ number_format((float) $priceBreakdown['grand_total'], 2) }}</span>
                </div>
                @endif
                <div class="detail-row">
                    <label>Payment Date</label>
                    <span>{{ $transaction->created_at->format('M d, Y h:i A') }}</span>
                </div>
                @if($transaction->type === 'custom_invoice' && isset($paymentType) && $paymentType === 'deposit')
                <div class="detail-row">
                    <label>Payment Type</label>
                    <span style="color: #f39c12;">Deposit Payment</span>
                </div>
                @endif
            </div>

            @if(!empty($cartItems))
            @php
                $grandTotal = 0;
            @endphp
            <div class="detail-box" style="padding:0;overflow:hidden;">
                <div style="font-weight:600;padding:14px 20px 10px;border-bottom:1px solid #e9ecef;">Purchase Breakdown</div>
                <table class="breakdown-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th class="price-right">Qty</th>
                            <th class="price-right">Unit Price</th>
                            <th class="price-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
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
                            {{-- Package row --}}
                            <tr class="breakdown-item-header">
                                <td>{{ $cartItem['package_name'] ?? ('Package #' . ($cartItem['package_id'] ?? '')) }}</td>
                                <td class="price-right">{{ $guests }}</td>
                                <td class="price-right">${{ number_format($unitPrice, 2) }}</td>
                                <td class="price-right">${{ number_format($pkgSubtotal, 2) }}</td>
                            </tr>
                            @if($isMultiple && $guests > 1)
                            <tr class="breakdown-addon-row">
                                <td colspan="4" style="padding-left:24px;">
                                    <span class="text-muted-sm">${{ number_format($unitPrice, 2) }} x {{ $guests }} guests</span>
                                </td>
                            </tr>
                            @endif
                            {{-- Addons rows --}}
                            @foreach($cartItem['addons'] ?? [] as $addon)
                                @if(!empty($addon['name']))
                                <tr class="breakdown-addon-row">
                                    <td>+ {{ $addon['name'] }}</td>
                                    <td class="price-right">1</td>
                                    <td class="price-right">
                                        @if(($addon['price'] ?? 0) > 0)
                                            ${{ number_format((float) $addon['price'], 2) }}
                                        @else
                                            <span class="text-muted-sm">Included</span>
                                        @endif
                                    </td>
                                    <td class="price-right">
                                        @if(($addon['price'] ?? 0) > 0)
                                            ${{ number_format((float) $addon['price'], 2) }}
                                        @else
                                            <span class="text-muted-sm">—</span>
                                        @endif
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                            {{-- Item subtotal if addons exist --}}
                            @if(!empty($cartItem['addons']) && $addonsTotal > 0)
                            <tr class="breakdown-subtotal-row">
                                <td colspan="3">Item Subtotal</td>
                                <td class="price-right">${{ number_format($itemTotal, 2) }}</td>
                            </tr>
                            @endif
                        @endforeach
                        @if(!empty($priceBreakdown))
                        <tr>
                            <td colspan="3"><strong>Packages Subtotal</strong></td>
                            <td class="price-right"><strong>${{ number_format((float) ($priceBreakdown['packages_subtotal'] ?? 0), 2) }}</strong></td>
                        </tr>
                        <tr>
                            <td colspan="3">Add-ons Subtotal</td>
                            <td class="price-right">${{ number_format((float) ($priceBreakdown['addons_subtotal'] ?? 0), 2) }}</td>
                        </tr>
                        <tr class="breakdown-subtotal-row">
                            <td colspan="3">Items Subtotal (Packages + Add-ons)</td>
                            <td class="price-right">${{ number_format((float) ($priceBreakdown['items_subtotal'] ?? 0), 2) }}</td>
                        </tr>
                        @if(!empty($priceBreakdown['gratuity']['enabled']))
                        <tr>
                            <td colspan="3">{{ $priceBreakdown['gratuity']['name'] }} ({{ number_format((float) $priceBreakdown['gratuity']['rate'], 2) }}%)</td>
                            <td class="price-right">${{ number_format((float) $priceBreakdown['gratuity']['amount'], 2) }}</td>
                        </tr>
                        @endif
                        @if(!empty($priceBreakdown['service_charge']['enabled']))
                        <tr>
                            <td colspan="3">{{ $priceBreakdown['service_charge']['name'] }} ({{ number_format((float) $priceBreakdown['service_charge']['rate'], 2) }}%)</td>
                            <td class="price-right">${{ number_format((float) $priceBreakdown['service_charge']['amount'], 2) }}</td>
                        </tr>
                        @endif
                        @if(!empty($priceBreakdown['sales_tax']['enabled']))
                        <tr>
                            <td colspan="3">{{ $priceBreakdown['sales_tax']['name'] }} ({{ number_format((float) $priceBreakdown['sales_tax']['rate'], 2) }}%)</td>
                            <td class="price-right">${{ number_format((float) $priceBreakdown['sales_tax']['amount'], 2) }}</td>
                        </tr>
                        @endif
                        @if((float) ($priceBreakdown['promo_discount'] ?? 0) > 0)
                        <tr>
                            <td colspan="3">Promo Code Discount</td>
                            <td class="price-right">-${{ number_format((float) $priceBreakdown['promo_discount'], 2) }}</td>
                        </tr>
                        @endif
                        @if(!empty($priceBreakdown['processing_fee']['enabled']))
                        <tr>
                            <td colspan="3">Processing Fee @if(($priceBreakdown['processing_fee']['type'] ?? 'percentage') === 'percentage')({{ number_format((float) $priceBreakdown['processing_fee']['rate'], 2) }}%)@endif</td>
                            <td class="price-right">${{ number_format((float) $priceBreakdown['processing_fee']['amount'], 2) }}</td>
                        </tr>
                        @endif
                        @endif

                        <tr class="breakdown-grand-total">
                            <td colspan="3" style="padding:12px 10px;"><strong>Order Total</strong></td>
                            <td class="price-right" style="padding:12px 10px;"><strong>${{ number_format((float) ($priceBreakdown['grand_total'] ?? $transaction->total), 2) }}</strong></td>
                        </tr>
                        @if(!empty($priceBreakdown['refundable']['enabled']))
                        <tr>
                            <td colspan="3">{{ $priceBreakdown['refundable']['name'] }} ({{ number_format((float) $priceBreakdown['refundable']['rate'], 2) }}%)</td>
                            <td class="price-right">${{ number_format((float) $priceBreakdown['refundable']['amount'], 2) }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td colspan="3"><strong>Amount Paid Now</strong></td>
                            <td class="price-right"><strong>${{ number_format((float) ($priceBreakdown['amount_paid_now'] ?? $transaction->total), 2) }}</strong></td>
                        </tr>
                        @if((float) ($priceBreakdown['remaining_due'] ?? 0) > 0)
                        <tr>
                            <td colspan="3">Remaining Due</td>
                            <td class="price-right">${{ number_format((float) $priceBreakdown['remaining_due'], 2) }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            @endif
            @endif

            <div class="info-box">
                <p>
                    <i class="fas fa-envelope"></i>
                    <strong>Confirmation Email:</strong> A confirmation email has been sent to your email address with all the transaction details.
                </p>
            </div>

            @if(isset($transaction) && $transaction->type === 'custom_invoice' && isset($paymentType) && $paymentType === 'deposit')
            <div class="info-box" style="background: #fff3cd; border-left-color: #f39c12;">
                <p>
                    <i class="fas fa-info-circle" style="color: #f39c12;"></i>
                    <strong>Deposit Payment:</strong> You have paid the deposit amount. The remaining balance will be due on arrival. Please keep your transaction ID for reference.
                </p>
            </div>
            @endif

            <div class="action-buttons">
                @if(isset($website))
                    <a href="{{ !empty($website->slug) ? route('index', $website->slug) : ($website->url ?? '/') }}" class="btn-primary-custom">
                        <i class="fas fa-home"></i> Return to Website
                    </a>
                @else
                    <a href="/" class="btn-primary-custom">
                        <i class="fas fa-home"></i> Return Home
                    </a>
                @endif
                
                @if(isset($transaction) && $transaction->type === 'custom_invoice' && isset($invoice))
                    <a href="{{ route('custom-invoice.pay', $invoice->payment_token) }}" class="btn-secondary-custom">
                        <i class="fas fa-file-invoice"></i> View Invoice
                    </a>
                @endif
            </div>
        </div>

        <div class="footer-note">
            <i class="fas fa-shield-alt"></i>
            Your payment was securely processed. If you have any questions, please contact us.
        </div>
    </div>

    <script>
        // Optional: Confetti effect on page load
        console.log('Payment successful! Transaction processed.');
    </script>
</body>
</html>
