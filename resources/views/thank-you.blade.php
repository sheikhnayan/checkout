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
            <div class="detail-box">
                <div style="font-weight:600;margin-bottom:12px;">Purchased Items</div>
                <ul class="item-list">
                    @foreach($cartItems as $cartItem)
                        <li>
                            <strong>{{ $cartItem['package_name'] ?? ('Package #' . ($cartItem['package_id'] ?? '')) }}</strong>
                            @if(!empty($cartItem['is_multiple']))
                                - ${{ number_format((float) ($cartItem['unit_price'] ?? 0), 2) }} x {{ $cartItem['guests'] ?? 1 }} = ${{ number_format((float) ($cartItem['line_total'] ?? 0), 2) }}
                            @else
                                - ${{ number_format((float) ($cartItem['line_total'] ?? 0), 2) }}
                            @endif
                            @if(!empty($cartItem['addons']))
                                <small>Add-ons: {{ collect($cartItem['addons'])->pluck('name')->filter()->implode(', ') }}</small>
                            @endif
                        </li>
                    @endforeach
                </ul>
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
