<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Entertainer Application Approved</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2937; line-height: 1.6;">
    <h2 style="margin-bottom: 8px;">Your CartVIP entertainer account is approved</h2>
    <p>Hello {{ $entertainer->display_name ?? $entertainer->user->name }},</p>
    <p>Great news. Your entertainer account has been approved.</p>
    <p>You can now login to your entertainer portal and start selecting packages and posting to your club feed.</p>
    <p>Login URL: <a href="{{ route('login') }}">{{ route('login') }}</a></p>
    <p>Best regards,<br>CartVIP Team</p>
</body>
</html>
