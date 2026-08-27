<!DOCTYPE html>
<html>
<head>
    <title>Setup your Ambassador Account</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h2 style="color: #333; text-align: center;">Welcome to the Nightly Reports Portal</h2>
        <p style="font-size: 16px; color: #555;">Hello {{ $ambassador->name }},</p>
        <p style="font-size: 16px; color: #555;">An ambassador account has been created for you. To get started, you need to set up a secure password to access your portal.</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('ambassador.setup', $ambassador->setup_token) }}" style="background-color: #007bff; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 16px;">
                Set Your Password
            </a>
        </div>

        <p style="font-size: 14px; color: #777;">If you did not expect this invitation, you can safely ignore this email.</p>
    </div>
</body>
</html>
