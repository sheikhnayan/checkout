<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Affiliate Application Approved</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2937; line-height: 1.6;">
    <h2 style="margin-bottom: 8px;">Your CartVIP Affiliate Account Is Approved</h2>
    <p>Hello {{ $affiliate->display_name ?? $affiliate->user->name }},</p>
    <p>Great news. Your affiliate application has been approved.</p>
    <p>You can now login to your affiliate portal, customize your affiliate page, select packages to promote, and start earning commissions on successful package sales.</p>
    <p>
        Login URL: <a href="{{ route('login') }}">{{ route('login') }}</a>
    </p>
    <p>Thank you for joining CartVIP.</p>
    <p>Best regards,<br>CartVIP Team</p>
</body>
</html>
