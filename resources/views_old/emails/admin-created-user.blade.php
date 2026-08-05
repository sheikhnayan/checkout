<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Your Portal Account Is Ready</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2937; line-height: 1.6;">
    <h2 style="margin-bottom: 8px;">Your CartVIP Portal Account Is Ready</h2>
    <p>Hello {{ $user->name }},</p>

    <p>An administrator has created your CartVIP portal account.</p>

    <p>
        <strong>Portal role:</strong> {{ $userTypeLabel }}<br>
        @if($user->website)
            <strong>Website:</strong> {{ $user->website->name }} ({{ $user->website->domain }})<br>
        @endif
        @if($user->websiteRole)
            <strong>Assigned role:</strong> {{ $user->websiteRole->name }}
        @endif
    </p>

    <p>
        <strong>Login email:</strong> {{ $user->email }}<br>
        <strong>Password:</strong> {{ $password }}
    </p>

    <p>
        You can sign in here:
        <a href="{{ route('login') }}">{{ route('login') }}</a>
    </p>

    <p>Please change your password after your first login to keep your account secure.</p>

    <p>Thank you,<br>CartVIP Team</p>
</body>
</html>
