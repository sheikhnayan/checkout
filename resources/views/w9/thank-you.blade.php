<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You - Form W-9 Submitted</title>
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

        .success-icon {
            width: 80px;
            height: 80px;
            background: #4caf50;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            font-size: 48px;
        }

        h1 {
            color: #333;
            font-size: 32px;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .subtitle {
            color: #4caf50;
            font-size: 18px;
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
            background: #f0f8ff;
            border-left: 4px solid #0066cc;
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 30px;
            text-align: left;
        }

        .info-box strong {
            color: #0066cc;
            display: block;
            margin-bottom: 10px;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-box p {
            color: #333;
            font-size: 13px;
            line-height: 1.6;
            margin-bottom: 8px;
        }

        .info-box p:last-child {
            margin-bottom: 0;
        }

        .next-steps {
            background: #fffacd;
            border-left: 4px solid #ffc107;
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 30px;
            text-align: left;
        }

        .next-steps strong {
            color: #ff9800;
            display: block;
            margin-bottom: 10px;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .next-steps ol {
            margin-left: 20px;
            color: #333;
            font-size: 13px;
            line-height: 1.8;
        }

        .next-steps li {
            margin-bottom: 8px;
        }

        .button-group {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .btn {
            padding: 12px 28px;
            font-size: 13px;
            font-weight: bold;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: #0066cc;
            color: white;
        }

        .btn-primary:hover {
            background: #0052a3;
            box-shadow: 0 4px 12px rgba(0, 102, 204, 0.3);
        }

        .btn-secondary {
            background: #f0f0f0;
            color: #333;
            border: 1px solid #ddd;
        }

        .btn-secondary:hover {
            background: #e0e0e0;
        }

        .footer {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid #eee;
            color: #999;
            font-size: 12px;
            line-height: 1.6;
        }

        .reference-number {
            background: #f5f5f5;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-family: monospace;
            color: #0066cc;
            font-weight: bold;
        }

        @media (max-width: 600px) {
            .container {
                padding: 30px 20px;
            }

            h1 {
                font-size: 24px;
            }

            .button-group {
                flex-direction: column;
            }

            .btn {
                width: 100%;
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

        <!-- Success Icon -->
        <div class="success-icon">✓</div>

        <!-- Main Message -->
        <h1>Thank You!</h1>
        <div class="subtitle">Your Form W-9 Has Been Submitted Successfully</div>

        <!-- Message -->
        <div class="message">
            <p>We have received your Form W-9 submission along with your government-issued ID verification documents. Thank you for completing this required tax certification form.</p>
            <p>Your submission is now under review by our administrative team. We will verify all information and notify you once your account has been approved.</p>
        </div>

        <!-- Reference Number -->
        <div class="reference-number">
            Submission ID: {{ $submissionId ?? 'W9-' . strtoupper(uniqid()) }}
        </div>

        <!-- Info Box -->
        <div class="info-box">
            <strong>📋 What You Submitted</strong>
            <p>✓ Completed Form W-9 (via PDF)</p>
            <p>✓ Government-Issued ID (Front & Back)</p>
            <p>✓ Tax Certification & Declaration</p>
        </div>

        <!-- Next Steps -->
        <div class="next-steps">
            <strong>⏳ What Happens Next</strong>
            <ol>
                <li><strong>Review Period:</strong> Our team will review your submission within 1-3 business days</li>
                <li><strong>Verification:</strong> We'll verify your government ID and tax information</li>
                <li><strong>Notification:</strong> You'll receive an email once your account has been approved or if we need additional information</li>
                <li><strong>Account Access:</strong> Once approved, you'll have full access to your account</li>
            </ol>
        </div>

        <!-- Buttons -->
        <div class="button-group">
            <a href="/" class="btn btn-primary">← Return to Home</a>
            <a href="https://app.cartvip.com/dashboard" class="btn btn-secondary">Go to Dashboard</a>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Need Help?</strong></p>
            <p>If you have any questions about your submission or the W-9 form, please contact our support team at <a href="mailto:support@cartvip.com" style="color: #0066cc;">support@cartvip.com</a></p>
            <p style="margin-top: 15px; color: #bbb;">CartVIP © {{ date('Y') }} - All Rights Reserved</p>
        </div>
    </div>
</body>
</html>
