<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment for Invoice #{{ $invoice->id }}</title>
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
        }
        .payment-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            max-width: 700px;
            width: 100%;
            overflow: hidden;
        }
        .payment-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .payment-header h1 {
            margin: 0;
            font-size: 24px;
        }
        .invoice-details {
            padding: 30px;
            border-bottom: 1px solid #eee;
        }
        .invoice-details h3 {
            font-size: 16px;
            margin-bottom: 20px;
            color: #333;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .detail-row label {
            font-weight: 600;
            color: #666;
        }
        .detail-row span {
            color: #333;
        }
        .items-section {
            padding: 30px;
            border-bottom: 1px solid #eee;
        }
        .items-section h3 {
            font-size: 16px;
            margin-bottom: 15px;
            color: #333;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
        }
        .items-table thead {
            background-color: #f9f9f9;
        }
        .items-table th {
            padding: 10px;
            text-align: left;
            font-weight: 600;
            color: #666;
            border-bottom: 2px solid #eee;
            font-size: 13px;
        }
        .items-table td {
            padding: 12px 10px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 14px;
        }
        .text-right {
            text-align: right;
        }
        .totals-section {
            padding: 30px;
            border-bottom: 1px solid #eee;
        }
        .total-row {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 10px;
        }
        .total-row label {
            width: 150px;
            text-align: right;
            margin-right: 20px;
            color: #666;
        }
        .total-row span {
            width: 100px;
            text-align: right;
            color: #333;
        }
        .total-row.grand-total {
            font-weight: bold;
            font-size: 18px;
            padding-top: 10px;
            border-top: 2px solid #667eea;
            color: #667eea;
        }
        .payment-section {
            padding: 30px;
        }
        .payment-method-selector {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }
        .payment-method {
            flex: 1;
        }
        .payment-method input[type="radio"] {
            display: none;
        }
        .payment-method label {
            display: block;
            padding: 15px;
            border: 2px solid #ddd;
            border-radius: 5px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            margin: 0;
        }
        .payment-method input[type="radio"]:checked + label {
            border-color: #667eea;
            background-color: #f0f4ff;
        }
        .payment-form {
            display: none;
        }
        .payment-form.active {
            display: block;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .pay-button {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
            transition: opacity 0.3s;
        }
        .pay-button:hover {
            opacity: 0.9;
        }
        .pay-button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .alert {
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .alert-danger {
            background-color: #fee;
            color: #c33;
            border: 1px solid #fcc;
        }
        .alert-warning {
            background-color: #ffeaa7;
            color: #856404;
            border: 1px solid #ffd966;
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <div class="payment-header">
            <h1><i class="fas fa-file-invoice"></i> Invoice #{{ $invoice->id }}</h1>
            <p style="margin-bottom: 0;">Payment Required</p>
        </div>

        @if($invoice->status === 'paid')
            <div class="invoice-details">
                <div class="alert alert-success" style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb;">
                    <i class="fas fa-check-circle"></i> <strong>Payment Already Received</strong>
                    <p style="margin-bottom: 0; margin-top: 5px;">This invoice has been paid on {{ $invoice->paid_at->format('M d, Y H:i') }}.</p>
                </div>
            </div>
        @elseif($invoice->status === 'expired')
            <div class="invoice-details">
                <div class="alert alert-danger">
                    <i class="fas fa-times-circle"></i> <strong>Invoice Expired</strong>
                    <p style="margin-bottom: 0; margin-top: 5px;">This invoice has expired and can no longer be paid online. Please contact us.</p>
                </div>
            </div>
        @else
            <div class="invoice-details">
                @if(session('success'))
                    <div class="alert alert-success" style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb;">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">
                        <i class="fas fa-times-circle"></i> {{ session('error') }}
                    </div>
                @endif

                <h3>Bill To</h3>
                <div class="detail-row">
                    <label>Name</label>
                    <span>{{ $invoice->client_name }}</span>
                </div>
                <div class="detail-row">
                    <label>Email</label>
                    <span>{{ $invoice->client_email }}</span>
                </div>
                <div class="detail-row">
                    <label>Invoice Date</label>
                    <span>{{ $invoice->created_at->format('M d, Y') }}</span>
                </div>
            </div>

            <div class="items-section">
                <h3>Items</h3>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th style="text-align: center;">Qty</th>
                            <th class="text-right">Price</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->items as $item)
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td style="text-align: center;">{{ $item->quantity }}</td>
                            <td class="text-right">${{ number_format($item->price, 2) }}</td>
                            <td class="text-right"><strong>${{ number_format($item->getLineTotal(), 2) }}</strong></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="totals-section">
                <div class="total-row">
                    <label>Subtotal</label>
                    <span>${{ number_format($invoice->subtotal, 2) }}</span>
                </div>
                @if($invoice->sales_tax > 0)
                <div class="total-row">
                    <label>{{ $invoice->sales_tax_name ?? 'Sales Tax' }}</label>
                    <span>${{ number_format($invoice->sales_tax, 2) }}</span>
                </div>
                @endif
                @if($invoice->service_charge > 0)
                <div class="total-row">
                    <label>{{ $invoice->service_charge_name ?? 'Service Charge' }}</label>
                    <span>${{ number_format($invoice->service_charge, 2) }}</span>
                </div>
                @endif
                @if($invoice->gratuity > 0)
                <div class="total-row">
                    <label>{{ $invoice->gratuity_name ?? 'Gratuity Fee' }}</label>
                    <span>${{ number_format($invoice->gratuity, 2) }}</span>
                </div>
                @endif
                <div class="total-row grand-total">
                    <label>TOTAL DUE</label>
                    <span id="totalAmount">${{ number_format($invoice->total, 2) }}</span>
                </div>
                @if($invoice->refundable > 0)
                <div class="total-row" style="border-top: 1px dashed #ddd; margin-top: 10px; padding-top: 10px; font-size: 14px; color: #666;">
                    <label style="font-style: italic;">{{ $invoice->refundable_name ?? 'Non-Refundable Deposit' }} ({{ number_format($invoice->website->refundable_fee ?? 0) }}%)</label>
                    <span style="font-style: italic;">${{ number_format($invoice->refundable, 2) }}</span>
                </div>
                @endif
            </div>

            <div class="payment-section">
                @if($invoice->refundable > 0)
                <!-- Payment Type Selection -->
                <div style="margin-bottom: 25px; padding: 20px; background-color: #f9f9f9; border-radius: 5px;">
                    <h4 style="margin: 0 0 15px 0; font-size: 16px; color: #333;">Select Payment Option:</h4>
                    <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                        <label style="display: flex; align-items: center; cursor: pointer; padding: 10px; background: white; border: 2px solid #667eea; border-radius: 5px; flex: 1; min-width: 200px;">
                            <input type="radio" name="payment_type" value="deposit" checked style="margin-right: 10px;">
                            <div>
                                <strong style="display: block; color: #667eea;">Pay Deposit Only</strong>
                                <span style="font-size: 14px; color: #666;">${{ number_format($invoice->refundable, 2) }} now</span>
                            </div>
                        </label>
                        <label style="display: flex; align-items: center; cursor: pointer; padding: 10px; background: white; border: 2px solid #ddd; border-radius: 5px; flex: 1; min-width: 200px;">
                            <input type="radio" name="payment_type" value="full" style="margin-right: 10px;">
                            <div>
                                <strong style="display: block; color: #333;">Pay Full Amount</strong>
                                <span style="font-size: 14px; color: #666;">${{ number_format($invoice->total, 2) }} now</span>
                            </div>
                        </label>
                    </div>
                </div>
                @endif

                <h3 style="margin-bottom: 20px; color: #333;">Payment Information</h3>
                
                @if($website->payment_method === 'stripe')
                    <div id="stripe-form" class="payment-form active">
                        <form id="payment-form" method="POST" action="{{ route('custom-invoice.process-payment', $invoice->payment_token) }}">
                            @csrf
                            <input type="hidden" name="payment_type" id="payment_type_input" value="{{ $invoice->refundable > 0 ? 'deposit' : 'full' }}">
                            <input type="hidden" name="payment_amount" id="payment_amount_input" value="{{ $invoice->refundable > 0 ? $invoice->refundable : $invoice->total }}">
                            
                            <div class="form-group">
                                <label>Full Name</label>
                                <input type="text" name="cardholder_name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Card Number</label>
                                <div id="card-number" style="padding: 12px; border: 1px solid #ddd; border-radius: 5px; background: white;"></div>
                            </div>
                            <div class="form-row" style="display: flex; gap: 15px;">
                                <div class="form-group" style="flex: 1;">
                                    <label>Expiry Date</label>
                                    <div id="card-expiry" style="padding: 12px; border: 1px solid #ddd; border-radius: 5px; background: white;"></div>
                                </div>
                                <div class="form-group" style="flex: 1;">
                                    <label>CVC</label>
                                    <div id="card-cvc" style="padding: 12px; border: 1px solid #ddd; border-radius: 5px; background: white;"></div>
                                </div>
                            </div>
                            <div id="card-errors" style="color: red; margin: 10px 0; font-size: 14px;"></div>
                            <button type="submit" class="pay-button" id="pay-btn">
                                <i class="fas fa-lock"></i> <span id="pay-btn-text">Pay ${{ number_format($invoice->refundable > 0 ? $invoice->refundable : $invoice->total, 2) }} Securely</span>
                            </button>
                        </form>
                    </div>
                @elseif($website->payment_method === 'authorize')
                    <div id="authorize-form" class="payment-form active">
                        <form id="payment-form" method="POST" action="{{ route('custom-invoice.process-payment', $invoice->payment_token) }}">
                            @csrf
                            <input type="hidden" name="payment_type" id="payment_type_input" value="{{ $invoice->refundable > 0 ? 'deposit' : 'full' }}">
                            <input type="hidden" name="payment_amount" id="payment_amount_input" value="{{ $invoice->refundable > 0 ? $invoice->refundable : $invoice->total }}">
                            
                            <div class="form-group">
                                <label>First Name</label>
                                <input type="text" name="firstName" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Last Name</label>
                                <input type="text" name="lastName" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Card Number</label>
                                <input type="text" name="cardNumber" class="form-control" placeholder="4111 1111 1111 1111" required>
                            </div>
                            <div class="form-row" style="display: flex; gap: 15px;">
                                <div class="form-group" style="flex: 1;">
                                    <label>Expiry Date (MMYY)</label>
                                    <input type="text" name="expirationDate" class="form-control" placeholder="1225" required>
                                </div>
                                <div class="form-group" style="flex: 1;">
                                    <label>CVV</label>
                                    <input type="text" name="cvv" class="form-control" placeholder="123" required>
                                </div>
                            </div>
                            <button type="submit" class="pay-button" id="pay-btn">
                                <i class="fas fa-lock"></i> <span id="pay-btn-text">Pay ${{ number_format($invoice->refundable > 0 ? $invoice->refundable : $invoice->total, 2) }} Securely</span>
                            </button>
                        </form>
                    </div>
                @endif

                <div style="margin-top: 20px; padding: 15px; background-color: #f9f9f9; border-radius: 5px;">
                    <p style="margin: 0; font-size: 12px; color: #666;">
                        <i class="fas fa-shield-alt"></i> <strong>Secure Payment:</strong> Your payment information is encrypted and secure. We never store your card details.
                    </p>
                </div>
            </div>
        @endif
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    @if($website->payment_method === 'stripe')
    <script src="https://js.stripe.com/v3/"></script>
    
    @php
        $setting = \App\Models\Setting::find(1);
    @endphp
    
    <script>
        const depositAmount = {{ $invoice->refundable ?? 0 }};
        const fullAmount = {{ $invoice->total }};
        const hasDeposit = {{ $invoice->refundable > 0 ? 'true' : 'false' }};

        // Initialize Stripe
        const stripe = Stripe("{{ $website->stripe_public_key ?? $setting->stripe_key }}");
        const elements = stripe.elements();

        const style = {
            base: {
                fontSize: '16px',
                color: '#32325d',
                '::placeholder': {
                    color: '#aab7c4'
                }
            },
            invalid: {
                color: '#fa755a',
                iconColor: '#fa755a'
            }
        };

        const cardNumber = elements.create('cardNumber', {style: style});
        const cardExpiry = elements.create('cardExpiry', {style: style});
        const cardCvc = elements.create('cardCvc', {style: style});

        cardNumber.mount('#card-number');
        cardExpiry.mount('#card-expiry');
        cardCvc.mount('#card-cvc');

        // Handle payment type selection
        if (hasDeposit) {
            $('input[name="payment_type"]').on('change', function() {
                const type = $(this).val();
                const amount = type === 'deposit' ? depositAmount : fullAmount;
                
                $('#payment_type_input').val(type);
                $('#payment_amount_input').val(amount);
                $('#pay-btn-text').text('Pay $' + amount.toFixed(2) + ' Securely');
                
                // Update border styling
                $('input[name="payment_type"]').parent().css('border-color', '#ddd');
                $(this).parent().css('border-color', '#667eea');
            });
        }

        // Handle form submission
        const form = document.getElementById('payment-form');
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const btn = $('#pay-btn');
            btn.prop('disabled', true);
            btn.html('<i class="fas fa-spinner fa-spin"></i> Processing...');

            const {token, error} = await stripe.createToken(cardNumber);

            if (error) {
                document.getElementById('card-errors').textContent = error.message;
                btn.prop('disabled', false);
                const amount = $('#payment_amount_input').val();
                btn.html('<i class="fas fa-lock"></i> <span id="pay-btn-text">Pay $' + parseFloat(amount).toFixed(2) + ' Securely</span>');
            } else {
                const hiddenInput = document.createElement('input');
                hiddenInput.setAttribute('type', 'hidden');
                hiddenInput.setAttribute('name', 'stripeToken');
                hiddenInput.setAttribute('value', token.id);
                form.appendChild(hiddenInput);
                form.submit();
            }
        });
    </script>
    @else
    <script>
        const depositAmount = {{ $invoice->refundable ?? 0 }};
        const fullAmount = {{ $invoice->total }};
        const hasDeposit = {{ $invoice->refundable > 0 ? 'true' : 'false' }};

        $(document).ready(function() {
            // Handle payment type selection
            if (hasDeposit) {
                $('input[name="payment_type"]').on('change', function() {
                    const type = $(this).val();
                    const amount = type === 'deposit' ? depositAmount : fullAmount;
                    
                    $('#payment_type_input').val(type);
                    $('#payment_amount_input').val(amount);
                    $('#pay-btn-text').text('Pay $' + amount.toFixed(2) + ' Securely');
                    
                    // Update border styling
                    $('input[name="payment_type"]').parent().css('border-color', '#ddd');
                    $(this).parent().css('border-color', '#667eea');
                });
            }

            $('#payment-form').on('submit', function(e) {
                const btn = $('#pay-btn');
                btn.prop('disabled', true);
                btn.html('<i class="fas fa-spinner fa-spin"></i> Processing...');
            });
        });
    </script>
    @endif
</body>
</html>
