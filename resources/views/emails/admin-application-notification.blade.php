<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $applicantType }} Application {{ ucfirst($status) }}</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2937; line-height: 1.6;">
    <h2 style="margin-bottom: 8px;">{{ $applicantType }} Application {{ ucfirst($status) }}</h2>

    <p>An application has been {{ $status }} by an administrator.</p>

    <p>
        <strong>Applicant type:</strong> {{ $applicantType }}<br>
        <strong>Name:</strong> {{ $name }}<br>
        <strong>Email:</strong> {{ $email }}<br>
        @if($websiteName)
            <strong>Website:</strong> {{ $websiteName }}<br>
        @endif
        <strong>Status:</strong> {{ ucfirst($status) }}
    </p>

    @if($rejectionReason)
        <p>
            <strong>Rejection reason:</strong><br>
            {{ $rejectionReason }}
        </p>
    @endif

    <p>Thank you,<br>CartVIP System Notification</p>
</body>
</html>
