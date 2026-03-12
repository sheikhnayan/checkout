<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Affiliate Application Received</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2937; line-height: 1.6;">
    <h2 style="margin-bottom: 8px;">Thank you for applying to CartVIP Affiliate Program</h2>
    <p>Hello {{ $affiliate->display_name ?? $affiliate->user->name }},</p>
    <p>We received your affiliate application successfully. Our team will review your submission shortly and notify you once a decision is made.</p>
    <p>What happens next:</p>
    <ul>
        <li>We review your profile and intended promotion strategy.</li>
        <li>If approved, you will receive an email confirmation.</li>
        <li>You can then log in and start building your own affiliate page.</li>
    </ul>
    <p>We appreciate your interest and look forward to working together.</p>
    <p>Best regards,<br>CartVIP Team</p>
</body>
</html>
