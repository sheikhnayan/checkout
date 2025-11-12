<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Package Purchase</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f8f8f8; color: #222; margin: 0; padding: 0; }
        .container { background: #fff; max-width: 600px; margin: 30px auto; border-radius: 8px; box-shadow: 0 2px 8px #eee; padding: 32px; }
        h2 { color: #2a7ae2; margin-top: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { text-align: left; padding: 8px 6px; border-bottom: 1px solid #eee; }
        th { background: #f0f4f8; color: #333; }
        .section-title { margin-top: 32px; color: #2a7ae2; font-size: 1.1em; border-bottom: 1px solid #e0e0e0; padding-bottom: 4px; }
        .total { font-weight: bold; color: #2a7ae2; font-size: 1.2em; }
    </style>
</head>
<body>
<div class="container">
    <h2>New Package Purchase</h2>
    <p><strong>Transaction ID:</strong> {{ $mailData['transaction_id'] ?? '' }}</p>

    <div class="section-title">Package Holder Information</div>
    <table>
        <tr><th>First Name</th><td>{{ $mailData['package_first_name'] ?? '' }}</td></tr>
        <tr><th>Last Name</th><td>{{ $mailData['package_last_name'] ?? '' }}</td></tr>
        <tr><th>Phone</th><td>{{ $mailData['package_phone'] ?? '' }}</td></tr>
        <tr><th>Email</th><td>{{ $mailData['package_email'] ?? '' }}</td></tr>
        <tr><th>Date of Birth</th><td>{{ $mailData['package_dob'] ?? '' }}</td></tr>
        <tr><th>Note</th><td>{{ $mailData['package_note'] ?? '' }}</td></tr>
    </table>

    <div class="section-title">Transportation</div>
    <table>
        <tr><th>Pickup Time</th><td>{{ $mailData['transportation_pickup_time'] ?? '' }}</td></tr>
        <tr><th>Address</th><td>{{ $mailData['transportation_address'] ?? '' }}</td></tr>
        <tr><th>Phone</th><td>{{ $mailData['transportation_phone'] ?? '' }}</td></tr>
        <tr><th>Guests</th><td>{{ $mailData['transportation_guest'] ?? '' }}</td></tr>
        <tr><th>Note</th><td>{{ $mailData['transportation_note'] ?? '' }}</td></tr>
    </table>

    <div class="section-title">Payment Information</div>
    <table>
        <tr><th>First Name</th><td>{{ $mailData['payment_first_name'] ?? '' }}</td></tr>
        <tr><th>Last Name</th><td>{{ $mailData['payment_last_name'] ?? '' }}</td></tr>
        <tr><th>Phone</th><td>{{ $mailData['payment_phone'] ?? '' }}</td></tr>
        <tr><th>Email</th><td>{{ $mailData['payment_email'] ?? '' }}</td></tr>
        <tr><th>Address</th><td>{{ $mailData['payment_address'] ?? '' }}</td></tr>
        <tr><th>City</th><td>{{ $mailData['payment_city'] ?? '' }}</td></tr>
        <tr><th>State</th><td>{{ $mailData['payment_state'] ?? '' }}</td></tr>
        <tr><th>Country</th><td>{{ $mailData['payment_country'] ?? '' }}</td></tr>
        <tr><th>Date of Birth</th><td>{{ $mailData['payment_dob'] ?? '' }}</td></tr>
        <tr><th>Zip Code</th><td>{{ $mailData['payment_zip_code'] ?? '' }}</td></tr>
    </table>

    <div class="section-title">Order Details</div>
    <table>
        <tr><th>Package ID</th><td>{{ $mailData['package_id'] ?? '' }}</td></tr>
        <tr><th>Add-ons</th><td>{{ $mailData['addons'] ?? '' }}</td></tr>
        <tr><th>Event ID</th><td>{{ $mailData['event_id'] ?? '' }}</td></tr>
        <tr><th>Website ID</th><td>{{ $mailData['website_id'] ?? '' }}</td></tr>
        <tr><th class="total">Total</th><td class="total">${{ $mailData['total'] ?? '0.00' }}</td></tr>
        <tr><th>Type</th><td>{{ $mailData['type'] ?? '' }}</td></tr>
    </table>

    <p style="margin-top: 32px; color: #888; font-size: 13px;">This is an automated email. Please do not reply.</p>
</div>
</body>
</html>
