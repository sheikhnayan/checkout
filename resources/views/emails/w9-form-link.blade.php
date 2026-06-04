<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #1e293b; color: #fff; padding: 30px; border-radius: 8px 8px 0 0; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .body { background: #f9fafb; padding: 30px; border-radius: 0 0 8px 8px; }
        .section { margin-bottom: 24px; }
        .section p { margin: 0 0 12px 0; }
        .btn { display: inline-block; background: #3b82f6; color: #fff; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: 600; margin: 20px 0; }
        .highlight { background: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; border-radius: 4px; margin: 20px 0; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
        .requirements { background: #f3f4f6; padding: 15px; border-radius: 6px; margin: 15px 0; }
        .requirements ul { margin: 10px 0; padding-left: 20px; }
        .requirements li { margin: 8px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Complete Your Tax Information</h1>
        </div>

        <div class="body">
            <div class="section">
                <p>Dear {{ $recipientName }},</p>
                <p>Thank you for registering with CartVIP! Your {{ $type }} account has been successfully created and is pending final review.</p>
            </div>

            <div class="section">
                <p><strong>To proceed with account activation, we require you to complete and submit your W-9 Form (Request for Taxpayer Identification Number and Certification).</strong></p>
                <p>This form is essential for tax compliance and payment processing purposes.</p>
            </div>

            <div class="highlight">
                <strong>⚠️ Important:</strong> Your account activation is contingent upon successful submission and review of your W-9 form. Please complete this as soon as possible to avoid delays.
            </div>

            <div class="section">
                <p><strong>What You Need to Provide:</strong></p>
                <div class="requirements">
                    <ul>
                        <li><strong>Complete W-9 Form</strong> - All fields must be filled accurately</li>
                        <li><strong>Tax Identification Number</strong> - SSN (Social Security Number) or EIN (Employer Identification Number)</li>
                        <li><strong>Government-Issued ID</strong> - Front and back photos:
                            <ul>
                                <li><em>Accepted formats:</em> JPG, JPEG, PNG</li>
                                <li><em>Maximum file size:</em> 5 MB per image</li>
                                <li><em>Clear, legible images</em> - Essential for verification</li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="section">
                <p style="text-align: center;">
                    <a href="{{ $formUrl }}" class="btn">Complete W-9 Form</a>
                </p>
                <p style="text-align: center; font-size: 12px; color: #666;">
                    <em>Or copy and paste this link in your browser:</em><br>
                    {{ $formUrl }}
                </p>
            </div>

            <div class="section">
                <p><strong>What happens next:</strong></p>
                <ol>
                    <li>Click the button above to open the W-9 form</li>
                    <li>Complete all required fields with accurate information</li>
                    <li>Upload clear photos of both sides of your government-issued ID</li>
                    <li>Review the certification statement and confirm</li>
                    <li>Submit the form for review</li>
                    <li>Our compliance team will review within 2-3 business days</li>
                    <li>You'll receive confirmation once your account is fully activated</li>
                </ol>
            </div>

            <div class="section">
                <p><strong>Need Help?</strong></p>
                <p>If you have any questions about the W-9 form or the submission process, please don't hesitate to contact our support team at hello@cartvip.com.</p>
            </div>

            <div class="section">
                <p>Best regards,<br>The CartVIP Compliance Team</p>
            </div>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} CartVIP. All rights reserved.</p>
            <p>This email was sent to you as part of your account registration with CartVIP.</p>
        </div>
    </div>
</body>
</html>
