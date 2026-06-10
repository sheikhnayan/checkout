<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New {{ $applicantType }} Registration - CartVIP</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #1f2937; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; background: #f9fafb; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 24px; border-radius: 8px 8px 0 0; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { background: white; padding: 24px; border-radius: 0 0 8px 8px; }
        .section { margin-bottom: 24px; }
        .section-title { font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px; color: #667eea; font-weight: 700; margin-bottom: 12px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .info-item { padding: 12px; background: #f3f4f6; border-left: 3px solid #667eea; border-radius: 4px; }
        .info-label { font-size: 12px; color: #6b7280; text-transform: uppercase; margin-bottom: 4px; }
        .info-value { font-size: 14px; color: #1f2937; font-weight: 600; }
        .action-btn { display: inline-block; background: #667eea; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin-top: 16px; font-weight: 600; }
        .footer { margin-top: 24px; padding-top: 16px; border-top: 1px solid #e5e7eb; font-size: 12px; color: #6b7280; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>New {{ $applicantType }} Registration</h1>
            <p style="margin: 8px 0 0 0; opacity: 0.9;">{{ $websiteName ?? 'CartVIP' }}</p>
        </div>

        <div class="content">
            <p>A new {{ strtolower($applicantType) }} has submitted a registration application on CartVIP.</p>

            <div class="section">
                <div class="section-title">📋 Applicant Information</div>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Full Name</div>
                        <div class="info-value">{{ $name }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Email Address</div>
                        <div class="info-value"><a href="mailto:{{ $email }}" style="color: #667eea; text-decoration: none;">{{ $email }}</a></div>
                    </div>
                    @if($phone)
                        <div class="info-item">
                            <div class="info-label">Phone Number</div>
                            <div class="info-value">{{ $phone }}</div>
                        </div>
                    @endif
                    {{-- <div class="info-item">
                        <div class="info-label">Registration Type</div>
                        <div class="info-value">{{ ucfirst($registrationType) }}</div>
                    </div> --}}
                </div>
            </div>

            @if($websiteName)
                <div class="section">
                    <div class="section-title">🏢 Associated Club</div>
                    <div style="padding: 12px; background: #f3f4f6; border-left: 3px solid #667eea; border-radius: 4px;">
                        <strong>{{ $websiteName }}</strong>
                    </div>
                </div>
            @endif

            <div class="section">
                <div class="section-title">⏰ Submission Details</div>
                <div style="padding: 12px; background: #f3f4f6; border-left: 3px solid #667eea; border-radius: 4px;">
                    <strong>Submitted:</strong> {{ $submittedAt }}
                </div>
            </div>

            @if($additionalInfo)
                <div class="section">
                    <div class="section-title">📝 Additional Information</div>
                    <div style="padding: 12px; background: #f3f4f6; border-left: 3px solid #667eea; border-radius: 4px;">
                        {{ $additionalInfo }}
                    </div>
                </div>
            @endif

            <div class="section" style="background: #eef2ff; padding: 16px; border-radius: 6px; border-left: 4px solid #667eea;">
                <p style="margin: 0; font-size: 13px; color: #1f2937;"><strong>Next Steps:</strong> Please log in to the CartVIP admin panel to review this application and take appropriate action (approve, reject, or request more information).</p>
            </div>

            <a href="{{ config('app.url') }}/admin" class="action-btn">Review Application</a>

            <div class="footer">
                <p style="margin: 0;">This is an automated notification from CartVIP. Please do not reply to this email.</p>
                <p style="margin: 8px 0 0 0;">© {{ date('Y') }} CartVIP. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
