<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $isManagerCopy ? (($clubName ?: 'Venue') . ' - BOOKING') : ('Booking Confirmed - ' . ($clubName ?: 'Venue')) }}</title>
    <style>
        body { font-family: 'Segoe UI', Helvetica, Arial, 'DejaVu Sans', sans-serif; background: #f5f7fb; color: #172033; margin: 0; padding: 24px 0; }
        .container { background: #ffffff; max-width: 680px; margin: 0 auto; border-radius: 14px; box-shadow: 0 8px 28px rgba(15, 23, 42, 0.08); padding: 32px; }
        h1, h2, h3 { margin-top: 0; color: #0f172a; }
        p { line-height: 1.6; color: #334155; }
        .eyebrow { font-size: 12px; font-weight: 800; letter-spacing: 0.12em; text-transform: uppercase; color: #9a6700; margin-bottom: 10px; }
        .hero { border: 1px solid #fde68a; background: linear-gradient(135deg, #fffaf0 0%, #fff7db 100%); border-radius: 14px; padding: 20px 22px; margin-bottom: 24px; }
        .hero strong { color: #7c5400; }
        .summary { width: 100%; border-collapse: collapse; margin-top: 18px; }
        .summary th, .summary td { text-align: left; padding: 10px 8px; border-bottom: 1px solid #e5e7eb; vertical-align: top; }
        .summary th { width: 180px; color: #475569; font-weight: 700; background: #f8fafc; }
        .section-title { margin: 28px 0 12px; font-size: 16px; font-weight: 800; color: #0f172a; }
        .ticket-box { margin-top: 22px; padding: 18px; border: 1px solid #cbd5e1; border-radius: 12px; background: #f8fbff; text-align: center; }
        .ticket-code { display: inline-block; margin-top: 12px; padding: 9px 14px; border-radius: 999px; background: #0f172a; color: #fff; font-weight: 800; letter-spacing: 0.06em; }
        ul { margin: 10px 0 0; padding-left: 20px; color: #334155; }
        li { margin-bottom: 8px; }
        .muted { margin-top: 26px; color: #64748b; font-size: 13px; }
    </style>
</head>
<body>
@php
    $confirmationNumber = $mailData['transaction_id'] ?? ($transaction->transaction_id ?? 'Pending');
    $venueName = $clubName ?: ($mailData['website_name'] ?? 'Venue');
    $guestName = trim(($mailData['package_first_name'] ?? '') . ' ' . ($mailData['package_last_name'] ?? '')) ?: 'N/A';
    $reservationDateRaw = $mailData['package_use_date'] ?? $mailData['reservation_date'] ?? null;
    $reservationDateFormatted = 'N/A';
    if (!empty($reservationDateRaw)) {
        try {
            $reservationDateFormatted = \Carbon\Carbon::parse($reservationDateRaw)->format('M d, Y');
        } catch (\Throwable $e) {
            $reservationDateFormatted = (string) $reservationDateRaw;
        }
    }
    $menCount = (int) ($mailData['men'] ?? 0);
    $womenCount = (int) ($mailData['women'] ?? 0);
    $guestCount = (int) ($mailData['guest_count'] ?? ($menCount + $womenCount));
    $isReservationType = strtolower((string) ($mailData['type'] ?? ($transaction->type ?? 'booking'))) === 'reservation';
    $rawMailCartItems = $mailData['cart_items'] ?? [];
    if (is_string($rawMailCartItems)) {
        $decoded = json_decode($rawMailCartItems, true);
        if (is_string($decoded)) {
            $decoded = json_decode($decoded, true);
        }
        $rawMailCartItems = is_array($decoded) ? $decoded : [];
    }
    $mailCartItems = is_array($rawMailCartItems) ? $rawMailCartItems : [];
    $mailPriceBreakdown = is_array($mailData['price_breakdown'] ?? null) ? $mailData['price_breakdown'] : null;
    $eventName = $mailData['event_name'] ?? optional($transaction->event ?? null)->name;
    $bookingType = (string) ($mailData['type'] ?? ($transaction->type ?? 'booking'));
@endphp
<div class="container">
    @if($isManagerCopy)
        <div class="eyebrow">Manager Booking Notice</div>
        <h2>{{ $venueName }} - BOOKING</h2>
        <div class="hero">
            <p style="margin:0;"><strong>Confirmation #:</strong> {{ $confirmationNumber }}</p>
        </div>

        <table class="summary">
            <tr><th>Confirmation #</th><td>{{ $confirmationNumber }}</td></tr>
            <tr><th>Venue</th><td>{{ $venueName }}</td></tr>
            <tr><th>Booking Type</th><td>{{ ucfirst(str_replace('_', ' ', $bookingType)) }}</td></tr>
            <tr><th>Guest Name</th><td>{{ $guestName }}</td></tr>
            <tr><th>Email</th><td>{{ $mailData['package_email'] ?? 'N/A' }}</td></tr>
            <tr><th>Phone</th><td>{{ $mailData['package_phone'] ?? 'N/A' }}</td></tr>
            <tr><th>Reservation Date</th><td>{{ $reservationDateFormatted }}</td></tr>
            @if(!empty($eventName))
            <tr><th>Event</th><td>{{ $eventName }}</td></tr>
            @endif
            @if($guestCount > 0)
            @if($isReservationType && ($menCount > 0 || $womenCount > 0))
            <tr><th>Guest Breakdown</th><td><strong>{{ $menCount }} Men + {{ $womenCount }} Women = {{ $guestCount }} Total Guests</strong></td></tr>
            @else
            <tr><th>Guest Count</th><td>{{ $guestCount }}</td></tr>
            @endif
            @endif
            @if(!empty($mailData['package_note']))
            <tr><th>Booking Note</th><td>{{ $mailData['package_note'] }}</td></tr>
            @endif
            @if(!empty($mailData['transportation_note']))
            <tr><th>Transportation Note</th><td>{{ $mailData['transportation_note'] }}</td></tr>
            @endif
            @if(!empty($mailData['transportation_pickup_time']) || !empty($mailData['transportation_address']) || !empty($mailData['transportation_guest']))
            <tr><th colspan="2" style="background: #dbeafe; padding: 14px; border-radius: 6px;"><strong>Transportation Details</strong></th></tr>
            @if(!empty($mailData['transportation_pickup_time']))
            <tr><th>Pickup Time</th><td>{{ $mailData['transportation_pickup_time'] }}</td></tr>
            @endif
            @if(!empty($mailData['transportation_address']))
            <tr><th>Pickup Location</th><td>{{ $mailData['transportation_address'] }}</td></tr>
            @endif
            @if(!empty($mailData['host_name']))
            <tr><th>Host Name</th><td>{{ $mailData['host_name'] }}</td></tr>
            @endif
            @endif
            @if(!empty($mailData['business_company']) || !empty($mailData['business_vat']) || !empty($mailData['business_address']))
            <tr><th colspan="2" style="background: #fef3c7; padding: 14px; border-radius: 6px;"><strong>Business Details</strong></th></tr>
            @if(!empty($mailData['business_company']))
            <tr><th>Company Name</th><td>{{ $mailData['business_company'] }}</td></tr>
            @endif
            @if(!empty($mailData['business_vat']))
            <tr><th>VAT/Tax ID</th><td>{{ $mailData['business_vat'] }}</td></tr>
            @endif
            @if(!empty($mailData['business_address']))
            <tr><th>Business Address</th><td>{{ $mailData['business_address'] }}</td></tr>
            @endif
            @endif
            <tr><th>Amount Paid</th><td>${{ number_format((float) ($mailPriceBreakdown['amount_paid_now'] ?? ($mailData['total'] ?? 0)), 2) }}</td></tr>
        </table>

        @if(!empty($mailCartItems))
            <div class="section-title">Booked Items</div>
            <table class="summary">
                @foreach($mailCartItems as $item)
                    <tr>
                        <th>{{ html_entity_decode($item['package_name'] ?? ('Package #' . ($item['package_id'] ?? '')), ENT_QUOTES | ENT_HTML5, 'UTF-8') }}</th>
                        <td>
                            Guests: {{ max(1, (int) ($item['guests'] ?? 1)) }}<br>
                            Unit Price: ${{ number_format((float) ($item['unit_price'] ?? 0), 2) }}<br>
                            <strong>Total: ${{ number_format((float) ($item['line_total'] ?? ($item['unit_price'] ?? 0)), 2) }}</strong>
                        </td>
                    </tr>
                    @if(!empty($item['addons']))
                        @foreach($item['addons'] as $addon)
                            @if(!empty($addon['name']))
                            <tr style="background: #f8fbff;">
                                <td style="padding-left: 24px;"><strong>+</strong> {{ $addon['name'] }} x{{ $addon['qty'] }}</td>
                                <td>
                                    @if((float) ($addon['price'] ?? 0) > 0)
                                        ${{ number_format((float) ($addon['unit_price'] ?? 0), 2) }} each<br>
                                        <strong>${{ number_format((float) ($addon['price'] ?? 0), 2) }}</strong>
                                    @else
                                        <em>Included</em>
                                    @endif
                                </td>
                            </tr>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </table>
        @endif
    @else
        <div class="eyebrow">Booking Confirmed</div>
        <h1>Your CartVIP Booking Confirmation</h1>
        <p>Thank you for booking with CartVIP. Your reservation has been successfully secured and submitted to the venue.</p>

        <div class="hero">
            <p style="margin:0 0 8px;"><strong>Confirmation #:</strong> {{ $confirmationNumber }}</p>
            <p style="margin:0;"><strong>Venue:</strong> {{ $venueName }}</p>
        </div>

        <div class="section-title">Booking Details</div>
        <table class="summary">
            <tr><th>Confirmation #</th><td>{{ $confirmationNumber }}</td></tr>
            <tr><th>Venue</th><td>{{ $venueName }}</td></tr>
            <tr><th>Guest Name</th><td>{{ $guestName }}</td></tr>
            <tr><th>Reservation Date</th><td>{{ $reservationDateFormatted }}</td></tr>
            @if(!empty($eventName))
            <tr><th>Event</th><td>{{ $eventName }}</td></tr>
            @endif
            @if($guestCount > 0)
            @if($isReservationType && ($menCount > 0 || $womenCount > 0))
            <tr><th>Guest Breakdown</th><td><strong>{{ $menCount }} Men + {{ $womenCount }} Women = {{ $guestCount }} Total Guests</strong></td></tr>
            @else
            <tr><th>Guest Count</th><td>{{ $guestCount }}</td></tr>
            @endif
            @endif
            <tr><th>Email</th><td>{{ $mailData['package_email'] ?? 'N/A' }}</td></tr>
            <tr><th>Phone</th><td>{{ $mailData['package_phone'] ?? 'N/A' }}</td></tr>
            @if(!empty($mailData['package_note']))
            <tr><th>Booking Note</th><td>{{ $mailData['package_note'] }}</td></tr>
            @endif
            @if(!empty($mailData['transportation_note']))
            <tr><th>Transportation Note</th><td>{{ $mailData['transportation_note'] }}</td></tr>
            @endif
            @if(!empty($mailData['transportation_pickup_time']) || !empty($mailData['transportation_address']) || !empty($mailData['transportation_guest']))
            <tr><th colspan="2" style="background: #dbeafe; padding: 14px; border-radius: 6px;"><strong>Transportation Details</strong></th></tr>
            @if(!empty($mailData['transportation_pickup_time']))
            <tr><th>Pickup Time</th><td>{{ $mailData['transportation_pickup_time'] }}</td></tr>
            @endif
            @if(!empty($mailData['transportation_address']))
            <tr><th>Pickup Location</th><td>{{ $mailData['transportation_address'] }}</td></tr>
            @endif
            @if(!empty($mailData['host_name']))
            <tr><th>Host Name</th><td>{{ $mailData['host_name'] }}</td></tr>
            @endif
            @endif
            @if(!empty($mailData['business_company']) || !empty($mailData['business_vat']) || !empty($mailData['business_address']))
            <tr><th colspan="2" style="background: #fef3c7; padding: 14px; border-radius: 6px;"><strong>Business Details</strong></th></tr>
            @if(!empty($mailData['business_company']))
            <tr><th>Company Name</th><td>{{ $mailData['business_company'] }}</td></tr>
            @endif
            @if(!empty($mailData['business_vat']))
            <tr><th>VAT/Tax ID</th><td>{{ $mailData['business_vat'] }}</td></tr>
            @endif
            @if(!empty($mailData['business_address']))
            <tr><th>Business Address</th><td>{{ $mailData['business_address'] }}</td></tr>
            @endif
            @endif
        </table>

        @if(!empty($mailData['ticket_qr_code']))
            <div class="section-title">Important Check-In Instructions</div>
            <p>Your confirmation includes a unique QR code for venue check-in.</p>
            <p>Please present:</p>
            <ul>
                <li>your QR code</li>
                <li>a valid government-issued ID</li>
                <li>your booking confirmation upon arrival</li>
            </ul>
            <p>Your QR code is valid for <strong>one-time use only</strong> and may only be scanned once at the venue. Please do not share, duplicate, alter, or tamper with your QR code in any way, as invalid or previously scanned codes may be denied entry.</p>

            <div class="ticket-box">
                @if(!empty($mailData['ticket_qr_image_url']))
                    <img src="{{ $mailData['ticket_qr_image_url'] }}" alt="Ticket QR Code" width="220" height="220" style="max-width:100%;height:auto;border-radius:10px;border:1px solid #cbd5e1;">
                @endif
                <div class="ticket-code">{{ $mailData['ticket_qr_code'] }}</div>
            </div>

            <div style="margin-top: 20px; padding: 14px 16px; border: 1px solid #fecaca; background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); border-radius: 10px; color: #7f1d1d; font-size: 13px; line-height: 1.6;">
                <strong style="display: block; margin-bottom: 8px;">⚠️ Important Notice</strong>
                <p style="margin: 0;">A valid government-issued ID matching the purchaser or guest may be required at check-in. Identification may be photographed and retained for verification, fraud prevention, and chargeback dispute purposes.</p>
            </div>
        @endif

        @if(!empty($mailCartItems))
            <div class="section-title">Order Summary</div>
            <table class="summary">
                @foreach($mailCartItems as $item)
                    <tr>
                        <th>{{ html_entity_decode($item['package_name'] ?? ('Package #' . ($item['package_id'] ?? '')), ENT_QUOTES | ENT_HTML5, 'UTF-8') }}</th>
                        <td>
                            Guests: {{ max(1, (int) ($item['guests'] ?? 1)) }}<br>
                            Unit Price: ${{ number_format((float) ($item['unit_price'] ?? 0), 2) }}<br>
                            <strong>Total: ${{ number_format((float) ($item['line_total'] ?? ($item['unit_price'] ?? 0)), 2) }}</strong>
                        </td>
                    </tr>
                    @if(!empty($item['addons']))
                        @foreach($item['addons'] as $addon)
                            @if(!empty($addon['name']))
                            <tr style="background: #f8fbff;">
                                <td style="padding-left: 24px;"><strong>+</strong> {{ $addon['name'] }} x{{ $addon['qty'] }}</td>
                                <td>
                                    @if((float) ($addon['price'] ?? 0) > 0)
                                        ${{ number_format((float) ($addon['unit_price'] ?? 0), 2) }} each<br>
                                        <strong>${{ number_format((float) ($addon['price'] ?? 0), 2) }}</strong>
                                    @else
                                        <em>Included</em>
                                    @endif
                                </td>
                            </tr>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                @if(!empty($mailPriceBreakdown))
                <!-- Price Breakdown Section -->
                <tr style="border-top: 2px solid #ddd;"></tr>
                <tr><th>Subtotal</th><td>${{ number_format((float) (($mailPriceBreakdown['packages_subtotal'] ?? 0) + ($mailPriceBreakdown['addons_subtotal'] ?? 0)), 2) }}</td></tr>
                @if(!empty($mailPriceBreakdown['gratuity']['enabled']))
                <tr><th>{{ $mailPriceBreakdown['gratuity']['name'] ?? 'Gratuity' }} ({{ number_format((float) ($mailPriceBreakdown['gratuity']['rate'] ?? 0), 2) }}%)</th><td>${{ number_format((float) ($mailPriceBreakdown['gratuity']['amount'] ?? 0), 2) }}</td></tr>
                @endif
                @if(!empty($mailPriceBreakdown['service_charge']['enabled']))
                <tr><th>{{ $mailPriceBreakdown['service_charge']['name'] ?? 'Service Charge' }} ({{ number_format((float) ($mailPriceBreakdown['service_charge']['rate'] ?? 0), 2) }}%)</th><td>${{ number_format((float) ($mailPriceBreakdown['service_charge']['amount'] ?? 0), 2) }}</td></tr>
                @endif
                @if(!empty($mailPriceBreakdown['sales_tax']['enabled']))
                <tr><th>{{ $mailPriceBreakdown['sales_tax']['name'] ?? 'Sales Tax' }} ({{ number_format((float) ($mailPriceBreakdown['sales_tax']['rate'] ?? 0), 2) }}%)</th><td>${{ number_format((float) ($mailPriceBreakdown['sales_tax']['amount'] ?? 0), 2) }}</td></tr>
                @endif
                @if((float) ($mailPriceBreakdown['promo_discount'] ?? 0) > 0)
                <tr><th>Promo Discount</th><td>-${{ number_format((float) $mailPriceBreakdown['promo_discount'], 2) }}</td></tr>
                @endif
                @if(!empty($mailPriceBreakdown['processing_fee']['enabled']))
                <tr><th>{{ $mailPriceBreakdown['processing_fee']['name'] ?? 'Processing Fee' }} @if(($mailPriceBreakdown['processing_fee']['type'] ?? 'percentage') === 'percentage')({{ number_format((float) ($mailPriceBreakdown['processing_fee']['rate'] ?? 0), 2) }}%)@endif</th><td>${{ number_format((float) ($mailPriceBreakdown['processing_fee']['amount'] ?? 0), 2) }}</td></tr>
                @endif
                <tr style="background: #f0f9ff; font-weight: 700; font-size: 16px;"><th>Total Amount</th><td>${{ number_format((float) ($mailPriceBreakdown['grand_total'] ?? $mailData['total'] ?? 0), 2) }}</td></tr>
                @endif
            </table>
        @endif

        <p>For questions regarding check-in, arrival times, upgrades, or venue policies, please contact the venue directly.</p>
        <p>Thank you for booking with CartVIP.</p>
    @endif

    <p class="muted">This is an automated email. Please do not reply.</p>
</div>
</body>
</html>
