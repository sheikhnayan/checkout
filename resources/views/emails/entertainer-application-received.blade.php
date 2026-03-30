<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Entertainer Application Received</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2937; line-height: 1.6;">
    <h2 style="margin-bottom: 8px;">Thank you for applying as a CartVIP entertainer</h2>
    <p>Hello {{ $entertainer->display_name ?? $entertainer->user->name }},</p>
    <p>We received your entertainer application successfully. The club admin or super admin will review your submission and notify you once approved.</p>
    <p>Best regards,<br>CartVIP Team</p>
</body>
</html>
