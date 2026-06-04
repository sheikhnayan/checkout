<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Already Submitted - CartVIP</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            max-width: 600px;
            width: 100%;
            padding: 50px 40px;
            text-align: center;
        }

        .logo {
            max-width: 120px;
            height: auto;
            margin-bottom: 30px;
        }

        .icon {
            width: 80px;
            height: 80px;
            background: #ffc107;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            font-size: 48px;
        }

        h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .subtitle {
            color: #ff9800;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 25px;
        }

        .message {
            color: #666;
            font-size: 14px;
            line-height: 1.8;
            margin-bottom: 30px;
        }

        .message p {
            margin-bottom: 12px;
        }

        .info-box {
            background: #fff3e0;
            border-left: 4px solid #ff9800;
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 30px;
            text-align: left;
        }

        .info-box p {
            color: #333;
            font-size: 13px;
            line-height: 1.6;
            margin-bottom: 8px;
        }

        .info-box strong {
            color: #ff9800;
            display: block;
            margin-bottom: 10px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .next-steps {
            background: #f5f5f5;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 30px;
            text-align: left;
        }

        .next-steps strong {
            color: #333;
            display: block;
            margin-bottom: 10px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .next-steps p {
            color: #666;
            font-size: 13px;
            line-height: 1.6;
            margin-bottom: 8px;
        }

        .next-steps p:last-child {
            margin-bottom: 0;
        }

        .footer {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid #eee;
            color: #999;
            font-size: 12px;
            line-height: 1.6;
        }

        .footer p {
            margin-bottom: 8px;
        }

        .footer a {
            color: #0066cc;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        @media (max-width: 600px) {
            .container {
                padding: 30px 20px;
            }

            h1 {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Logo -->
        @if(file_exists(public_path('logo.png')))
            <img src="{{ asset('logo.png') }}" alt="CartVIP Logo" class="logo">
        @else
            <img src="{{ asset('cartvip-logo.png') }}" alt="CartVIP Logo" class="logo" onerror="this.style.display='none'">
        @endif

        <!-- Icon -->
        <div class="icon">✓</div>

        <!-- Main Message -->
        <h1>Form Already Submitted</h1>
        <div class="subtitle">This submission link has already been used</div>

        <!-- Message -->
        <div class="message">
            <p>Your Form W-9 has already been submitted and is under review.</p>
            <p>Each submission link can only be used once for security purposes. Your form has been successfully received and processed.</p>
        </div>

        <!-- Info Box -->
        <div class="info-box">
            <strong>✓ Status</strong>
            <p>Your Form W-9 submission is under review by our administrative team. You will receive an email notification once your account has been approved or if we need additional information.</p>
        </div>

        <!-- Next Steps -->
        <div class="next-steps">
            <strong>📧 What to expect</strong>
            <p>Check your email for updates on your account status. Our team typically reviews submissions within 1-3 business days.</p>
            <p>If you have questions, contact us at <a href="mailto:hello@cartvip.com" style="color: #0066cc;">hello@cartvip.com</a></p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>CartVIP © {{ date('Y') }} - All Rights Reserved</p>
        </div>
    </div>
</body>
</html>
