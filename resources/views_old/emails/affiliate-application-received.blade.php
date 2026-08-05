<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Promoter Application Received</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #1f2937; line-height: 1.6; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; background: #f9fafb; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 32px 24px; border-radius: 8px 8px 0 0; text-align: center; }
        .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
        .header p { margin: 8px 0 0 0; opacity: 0.95; font-size: 16px; }
        .content { background: white; padding: 32px 24px; border-radius: 0 0 8px 8px; }
        .greeting { font-size: 16px; color: #1f2937; margin-bottom: 16px; }
        .message { font-size: 15px; color: #4b5563; line-height: 1.8; margin-bottom: 24px; }
        .section { margin-bottom: 24px; background: #f3f4f6; padding: 16px; border-left: 4px solid #667eea; border-radius: 4px; }
        .section-title { font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; color: #667eea; font-weight: 700; margin-bottom: 8px; }
        .section-content { font-size: 15px; color: #1f2937; font-weight: 500; }
        .timeline { margin-top: 24px; }
        .timeline-item { display: flex; margin-bottom: 16px; }
        .timeline-number { background: #667eea; color: white; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; margin-right: 16px; flex-shrink: 0; }
        .timeline-text { font-size: 14px; color: #4b5563; line-height: 1.6; }
        .cta-btn { display: inline-block; background: #667eea; color: white; padding: 12px 28px; text-decoration: none; border-radius: 6px; margin-top: 24px; font-weight: 600; font-size: 14px; }
        .footer { margin-top: 32px; padding-top: 16px; border-top: 1px solid #e5e7eb; font-size: 12px; color: #6b7280; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Application Received!</h1>
            <p>Your Promoter Application Has Been Submitted Successfully</p>
        </div>

        <div class="content">
            <p class="greeting">Hello {{ $affiliate->display_name ?? $affiliate->user->name }},</p>

            <p class="message">Thank you for applying to CartVIP! We're thrilled about your interest in becoming a promoter on our platform. We've successfully received your application and it's now in our review queue.</p>

            <div class="section">
                <div class="section-title">📋 Application Status</div>
                <div class="section-content">Pending Review</div>
            </div>

            <p style="font-size: 15px; color: #4b5563; margin: 24px 0;">Here's what happens next:</p>

            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-number">1</div>
                    <div class="timeline-text"><strong>Application Review</strong><br>Our admin team will carefully review your profile, promotion strategy, and qualifications.</div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-number">2</div>
                    <div class="timeline-text"><strong>Verification</strong><br>We may contact you with additional questions or to verify your information.</div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-number">3</div>
                    <div class="timeline-text"><strong>Decision Notification</strong><br>You'll receive an email with our decision within 3-5 business days.</div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-number">4</div>
                    <div class="timeline-text"><strong>Account Activation</strong><br>Once approved, you can log in and start building your promoter page.</div>
                </div>
            </div>

            <div style="background: #eef2ff; padding: 16px; border-radius: 6px; border-left: 4px solid #667eea; margin-top: 24px;">
                <p style="margin: 0; font-size: 14px; color: #1f2937;"><strong>💡 Tip:</strong> In the meantime, feel free to reach out if you have any questions. We're here to help!</p>
            </div>

            <div class="footer">
                <p style="margin: 0;">CartVIP Team</p>
                <p style="margin: 8px 0 0 0;">© {{ date('Y') }} CartVIP. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
