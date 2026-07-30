<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.7/css/bootstrap.min.css"
        integrity="sha512-fw7f+TcMjTb7bpbLJZlP8g2Y4XcCyFZW8uy8HsRZsH/SwbMw0plKHFHr99DN3l04VsYNwvzicUX/6qurvIxbxw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="styles/main.css">
</head>

<body>
    <div class="background-glow"></div>
    <header style="background: url('assets/bg-back.jpeg');">
        <section class="hero" max-width="620px" ;>
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                    @session('success')
                        <div class="alert alert-success mt-4" role="alert">
                            Purchase Successfull!
                        </div>
                    @endsession

                    @session('error')
                        <div class="alert alert-danger mt-4" role="alert">
                            {{ $value }}
                        </div>
                    @endsession
                    </div>
                    <div class="col-md-8">
                        <!-- <div class="background-glow">
                                <img src="assets/bg.png" alt="">
                            </div> -->
                        <div class="logo-section">
                            <img src="{{ asset('uploads/' . $event->image) }}" alt="Peppermint Hippo Logo" class="logo">

                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="event-info">
                            <div class="date">
                                <label>Date</label>
                                <div class="event-date">

                                    <input type="date" value="{{ \Carbon\Carbon::parse($event->date)->format('Y-m-d') }}"
                                        onclick="this.showPicker && this.showPicker()">
                                </div>
                            </div>


                            <div class="event-time">7:00 PM - 5:00 AM</div>
                            <div class="event-day">{{ \Carbon\Carbon::parse($event->date)->format('l') }}</div>
                            <div class="event-desc">
                                <h2>Description</h2>


                                <ul>
                                    <li>{{ $event->description }}</li>

                                </ul>






                            </div>
                        </div>
                    </div>






                </div>
            </div>
        </section>


    </header>
    <nav>
        @if ($data->reservation == 1)
            <button id="button" data-name='guest' class="tab active">
                <p>Guest List</p>
            </button>
            <button id="button" data-name="package" class="tab">
                <p>Packages</p>
            </button>
        @else
        <button id="button" data-name="package" class="tab active">
                <p>Packages</p>
            </button>
        @endif
    </nav>
    <main>
        @if ($data->reservation == 1)
            <div class="guest">
                <form action="{{ route('reservation.store') }}" method="post">
                    @csrf
                <section>
                    <div class="form-container">

                        <div class="row">
                            <div class="col-md-8">
                                <!-- Left: Form Fields -->
                                <div class="form-left">

                                    <div class="form-row">
                                        <div class="form-group" style="width: 50%;">
                                            <label for="firstName">First Name</label>
                                            <input type="text" name="reservation_first_name" id="firstName" placeholder="First Name" required />
                                        </div>
                                        <div class="form-group" style="width: 50%;">
                                            <label for="lastName">Last Name</label>
                                            <input type="text" name="reservation_last_name" id="lastName" placeholder="Last Name" required />
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group" style="width: 50%;">
                                            <label for="phone">Phone Number</label>
                                            <input type="tel" name="reservation_phone" id="phone" placeholder="Phone Number" required />
                                        </div>
                                        <div class="form-group" style="width: 50%;">
                                            <label for="email">Email</label>
                                            <input type="email" name="reservation_email" id="email" placeholder="sample@sample.com" required />
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group" style="width: 50%;">
                                            <label for="dob-month">Date of Birth</label>
                                            <div class="form-row">
                                                <select id="dob-month" name="reservation_day" class="form-select" style="width: 32%; display: inline-block; margin-right: 2%;" required></select>
                                                <select id="dob-day" name="reservation_month" class="form-select" style="width: 32%; display: inline-block; margin-right: 2%;" required></select>
                                                <select id="dob-year" name="reservation_year" class="form-select" style="width: 32%; display: inline-block;" required></select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="note">Booking Note</label>
                                        <textarea id="note" name="reservation_description" placeholder="Your occasion or special request?"></textarea>
                                    </div>
                                </div>

                            </div>
                            <div class="col-md-4">
                                <div class="form-right">
                                    <h2>Location</h2>
                                    <p>{{ $data->location }}</p>
                                    <iframe
                                        src="https://www.google.com/maps?q={{ urlencode($data->location) }}&output=embed"
                                        allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                                    </iframe>
                                    <h2>Contact</h2>
                                    <p><a href="tel:{{ $data->phone }}">{{ $data->phone }}</a></p>
                                    <p><a href="mailto:{{ $data->email }}">{{ $data->email }}</a></p>
                                </div>
                            </div>
                        </div>

                    </div>
                </section>


                <section class="guest-count">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12">
                                <h2>Total Guests</h2>
                                <div class="guest-section">
                                    <span class="label">Women</span>
                                    <div class="counter">
                                        <span class="count" id="womenCount">0</span>
                                        <button class="btn-gray" type="button" onclick="decrements('women')">−</button>
                                        <button class="btn-yellow" type="button" onclick="increments('women')">+</button>
                                    </div>
                                </div>
                                <div class="guest-section">
                                    <span class="label">Men</span>
                                    <div class="counter">
                                        <span class="count" id="menCount">0</span>
                                        <button class="btn-gray" type="button" onclick="decrements('men')">−</button>
                                        <button class="btn-yellow" type="button" onclick="increments('men')">+</button>
                                    </div>
                                </div>
                                <div class="guest-section">
                                    <span class="label">Total Guests</span>
                                    <div class="counter">
                                        <span class="count" id="totalCount">0</span>
                                        <button class="btn-gray" type="button" onclick="resets()">−</button>
                                        <button class="btn-yellow" type="button" onclick="increments('total')">+</button>
                                    </div>
                                </div>
                                @if ($event->is_booking_paid)
                                    <div class="booking-fee-info mt-3" style="font-size: 16px; color: #f5a623;">
                                        <strong>Booking Fee:</strong> ${{ $event->booking_fee ?? '0.00' }}
                                    </div>
                                    <input type="hidden" name="booking_fee" id="booking_fee" value="{{ $event->booking_fee ?? '0.00' }}">
                                @endif
                                <input type="hidden" name="men_count" id="men_count" value="0">
                                <input type="hidden" name="women_count" id="women_count" value="0">
                            </div>
                            <div class="col-md-12 mt-4">
                                @if ($event->is_booking_paid)
                                    <section class="payment-info dynamic-price mt-4">
                                        <div class="">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <h2 style="margin-bottom: 35px; margin-top: 3rem;">Payment</h2>
                                                    <!-- Left: Form Fields -->
                                                    <div class="form-left">
                                                        <div class="form-row">
                                                            <div class="form-group" style="width: 50%;">
                                                                <label for="firstName">First Name</label>
                                                                <input name="payment_first_name" type="text" id="firstName" placeholder="" required />
                                                            </div>
                                                            <div class="form-group" style="width: 50%;">
                                                                <label for="lastName">Last Name</label>
                                                                <input name="payment_last_name" type="text" id="lastName" placeholder="" required />
                                                            </div>
                                                        </div>
                                                        <div class="form-row">
                                                            <div class="form-group" style="width: 50%;">
                                                                <label for="phone">Phone Number</label>
                                                                <input name="payment_phone" type="tel" id="phone" placeholder="" required />
                                                            </div>
                                                            <div class="form-group" style="width: 50%;">
                                                                <label for="email">Email</label>
                                                                <input name="payment_email" type="email" id="email" placeholder="sample@sample.com" required />
                                                            </div>
                                                        </div>
                                                        <div class="form-row">
                                                            <div class="form-group" style="width: 100%;">
                                                                <label for="bill-add">Billing Address</label>
                                                                <input name="payment_address" type="tel" id="bill-add" placeholder="" required />
                                                            </div>
                                                        </div>
                                                        <div class="form-row">
                                                            <div class="form-group" style="width: 50%;">
                                                                <label for="city">City</label>
                                                                <input type="tel" name="payment_city" id="city" placeholder="" required />
                                                            </div>
                                                            <div class="form-group" style="width: 50%;">
                                                                <label for="st-pv">State/ Province</label>
                                                                <input type="text" name="payment_state" id="st-pv" placeholder="" required />
                                                            </div>
                                                        </div>
                                                        <div class="form-row">
                                                            <div class="form-group" style="width: 50%;">
                                                                <label for="country2">Country</label>
                                                                <select id="country2" name="payment_country" class="form-select" required></select>
                                                            </div>
                                                            <div class="form-group" style="width: 50%;">
                                                                <label for="dob-month">Date of Birth</label>
                                                                <div class="form-row">
                                                                    <select id="payment-dob-month2" name="payment_month" class="form-select" style="width: 32%; display: inline-block; margin-right: 2%;" required></select>
                                                                    <select id="payment-dob-day2" name="payment_day" class="form-select" style="width: 32%; display: inline-block; margin-right: 2%;" required></select>
                                                                    <select id="payment-dob-year2" name="payment_year" class="form-select" style="width: 32%; display: inline-block;" required></select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-row">
                                                            <div class="form-group" style="width: 100%;">
                                                                <label for="card-num">Card Number</label>
                                                                <input type="tel" name="card_number" id="card-num" placeholder="" required />
                                                            </div>
                                                        </div>
                                                        <div class="form-row">
                                                            <div class="form-group" style="width: 25%;">
                                                                <input type="tel" maxlength="2" name="card_month" id="city" placeholder="Month" required />
                                                            </div>
                                                            <div class="form-group" style="width: 25%;">
                                                                <input type="tel" maxlength="2" name="card_year" id="st-pv" placeholder="Year" required />
                                                            </div>
                                                            <div class="form-group" style="width: 25%;">
                                                                <input type="tel" name="card_cvv" id="city" placeholder="CVV" required />
                                                            </div>
                                                            <div class="form-group" style="width: 25%;">
                                                                <input type="text" name="payment_zip_code" id="st-pv" placeholder="Zip" required />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                    @endif
                                </div>
                                <div class="col-md-12">
                                <div class="checkbox-container">
                                    <label>
                                        <input type="checkbox" id="smsConsent_two" />
                                        I agree to receive SMS communications from {{ $data->name }} regarding my upcoming reservation. Message and data rates may apply. Messaging frequency may vary. Reply STOP to opt out at any time.
                                    </label>
                                    <label>
                                        <input type="checkbox" id="termsConsent_two" />
                                        I agree to the Mr. Black <a target="_blank" href="{{ $data->terms }}">Terms of Service</a> and <a href="{{ $data->policy }}" target="_blank">Privacy Policy</a>.
                                    </label>
                                </div>
                                <input type="hidden" name="event_id" value="{{ $event->id }}">
                                <button class="submit-btn" type="submit" id="submitBtn_two">Create Reservation</button>

                            </div>
                            <div class="col-md-4"></div>
                        </div>
                    </div>

                </section>
                <input type="hidden" name="type" value="guest">

                </form>
            </div>
        @endif


        <div class="package"
        @if ($data->reservation == 1)
        style="display: none;"
        @endif
        >
            <section class="vip-pack">
                <div class="">

                    <div class="row">
                        <div class="col-md-8">

                            <h2 style="margin-bottom: 35px;">VIP Packages</h2>

                            @foreach ($event->packages as $item)
                                <div class="vip-card d-flex flex-wrap justify-content-between align-items-center">
                                    <div>
                                        <div>
                                            <div class="vip-title" style="float: left">{{ $item->name }} </div>
                                            <div class="items" style="float: right; margin-top: -6px; margin-left: 10px;"
                                                onClick='openPackageModal()' data-description="{!! $item->description !!}"
                                                data-title="{{ $item->name }}">
                                                <svg style="fill: #f5a623; height: 20px; width: 20px; cursor: pointer;"
                                                    version="1.1" xmlns="http://www.w3.org/2000/svg"
                                                    xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                                    viewBox="0 0 512 512" xml:space="preserve">
                                                    <g>
                                                        <path
                                                            d="M256,0C115.39,0,0,115.39,0,256s115.39,256,256,256s256-115.39,256-256S396.61,0,256,0z M286,376
                                                        c0,16.538-13.462,30-30,30c-16.538,0-30-13.462-30-30V226c0-16.538,13.462-30,30-30c16.538,0,30,13.462,30,30V376z M256,166
                                                        c-16.538,0-30-13.462-30-30c0-16.538,13.462-30,30-30c16.538,0,30,13.462,30,30C286,152.538,272.538,166,256,166z">
                                                        </path>
                                                    </g>
                                                </svg>
                                            </div>
                                        </div>
                                        <button class="vip-btn" data-id="{{ $item->id }}" data-price="{{ $item->price }}"
                                            data-gratuity="{{ $data->gratuity_fee }}"
                                            data-refundable="{{ $data->refundable_fee }}"
                                            data-sales_tax="{{ $data->sales_tax_fee ?? 10 }}">Add Package</button>
                                    </div>

                                    <div class="d-flex ">
                                        <div class="vip-price me-3">${{ $item->price }}</div>
                                    </div>
                                    <div class="d-flex flex-column align-items-center " style="margin-right: 30px;">
                                        <p>Guests total</p>
                                        <select id="package_number_of_guest" class="form-select vip-select me-2 gue-1">
                                            @for ($i = 1; $i <= $item->number_of_guest; $i++)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor

                                        </select>

                                    </div>

                                </div>
                            @endforeach

                            <div class="addons" style="display: none;">
                                <h5>Add-ons</h5>
                                <div class="addons-list">
                                    <!-- Addons will be dynamically added here -->
                                </div>
                            </div>

                            <div class="row">
                                <div class="text-start mt-3 col-md-6">
                                    <div style="font-size: 12px;" class="default-price">Package: <span>$0.00</span>
                                    </div>
                                    <div class="dynamic-price" style="display: none;">
                                        <div style="font-size: 12px;" class="default-package-price">Package:
                                            <span>$0.00</span></div>
                                        <div class="addonns"></div>
                                        <div style="font-size: 12px;" class="default-gratuity">{{ $data->gratuity_name }}:
                                            <span>$0.00</span></div>
                                        <div style="font-size: 12px;" class="default-refundable">{{ $data->refundable_name }}: <span>$0.00</span></div>
                                            <div style="font-size: 12px;" class="default-sales-tax">{{ $data->sales_tax_name }}:
                                                <span>$0.00</span></div>
                                        <div style="font-size: 12px; font-weight: bold; display: none" class="default-total">Total:
                                            <span>$0.00</span></div>
                                    </div>


                                    <hr>
                                    <div class="vip-price default-deposit" style="font-size: 12px; font-weight: 700; ">
                                        Total: <span>$0.00</span></div>
                                </div>
                                <div class="col-md-6 dynamic-price" style="display: none;">
                                    <label style="color: #808080; font-size: 14px;">Promo Code</label>
                                    <div class="row">
                                        <div class="col-md-8" style="padding-right: 0%;">
                                            <input type="text" style="color: #fff" placeholder="Enter Promo Code" />
                                        </div>
                                        <div class="col-md-4" style="padding-left: 0%;">
                                            <button class="vip-btn-submit"
                                                style="width: 100%; height: 100%; font-weight: normal;">Submit</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <form action="{{ route('checkout.store') }}" method="post">
                            @csrf
                                <section class="holder-info dynamic-price mt-4" style="display: none; width: 100%;">
                                    <div class="">
                                        <div class="row">

                                            <div class="col-md-12">

                                                <h2 style="margin-bottom: 35px;">Package Holder Information</h2>

                                                <!-- Left: Form Fields -->
                                                <div class="form-left">

                                                    <div class="form-row">
                                                        <div class="form-group" style="width: 50%;">
                                                            <label for="firstName">First Name</label>
                                                            <input type="text" id="firstName" name="package_first_name" placeholder="First Name" required />
                                                        </div>
                                                        <div class="form-group" style="width: 50%;">
                                                            <label for="lastName">Last Name</label>
                                                            <input type="text" id="lastName" name="package_last_name" placeholder="Last Name" required />
                                                        </div>
                                                    </div>

                                                    <div class="form-row">
                                                        <div class="form-group" style="width: 50%;">
                                                            <label for="phone">Phone Number</label>
                                                            <input type="tel" id="phone" name="package_phone" placeholder="Phone Number" required />
                                                        </div>
                                                        <div class="form-group" style="width: 50%;">
                                                            <label for="email">Email</label>
                                                            <input type="email" id="email" name="package_email" placeholder="sample@sample.com" required />
                                                        </div>
                                                    </div>

                                                    <div class="form-row">
                                                        <div class="form-group" style="width: 100%;">
                                                            <label for="dob-month">Date of Birth</label>
                                                            <div class="form-row">
                                                                <select id="package-dob-month" name="package_month" class="form-select" style="width: 32%; display: inline-block; margin-right: 2%;" required></select>
                                                                <select id="package-dob-day" name="package_day" class="form-select" style="width: 32%; display: inline-block; margin-right: 2%;" required></select>
                                                                <select id="package-dob-year" name="package_year" class="form-select" style="width: 32%; display: inline-block;" required></select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="note">Booking Note</label>
                                                        <textarea id="note" name="package_note"
                                                            placeholder="Your occasion or special request?"></textarea>
                                                    </div>
                                                </div>

                                            </div>






                                        </div>
                                    </div>
                                </section>

                                <section class="transport dynamic-price mt-4" style="display: none; width: 100%">
                                    <div class="">
                                        <div class="row">

                                            <div class="col-md-12">

                                                <h2 style="margin-bottom: 35px;">Transportation</h2>

                                                <!-- Left: Form Fields -->
                                                <div class="form-left">
                                                    <div class="from-row ">
                                                        <div class=" trans-group" style="width: 50%; border: none;">
                                                            <label for="Pick-up-time">Pick-up Time *</label>
                                                            <input name="transportation_pickup_time" type="time" id="Pick-up-time" placeholder="" required />
                                                            <br>
                                                            <br>
                                                            <label for="">Pick-up Location</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-row">
                                                        <div class="form-group" style="width: 100%;">
                                                            <label for="address">Address</label>
                                                            <input type="text" name="transportation_address" id="address" placeholder="" required />
                                                        </div>

                                                    </div>

                                                    <div class="form-row">
                                                        <div class="form-group" style="width: 100%;">
                                                            <label for="phone">Phone Number</label>
                                                            <input type="tel" name="transportation_phone" id="phone" placeholder="Phone Number" required />
                                                        </div>

                                                    </div>

                                                    <div class="form-row">
                                                        <div class="num-guest" style="width: 100%; display: flex;">
                                                            <label for="">Number of Guest</label>

                                                            <input type="number" class="form-control" name="transportation_guest" style="width: 50%; color: #fff;" required />

                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="note">Pickup Note</label>
                                                        <textarea name="transportation_note" id="note"
                                                            placeholder="Your occasion or special request?"></textarea>
                                                    </div>
                                                {{--
                                                    <div class="from-group">
                                                        <div class="notification">
                                                            <label>
                                                                <input type="checkbox" id="termsConsent" />
                                                                I agree to receive notifications from the driver
                                                            </label>
                                                        </div>



                                                    </div> --}}



                                                </div>

                                            </div>






                                        </div>
                                    </div>
                                </section>

                                <input type="hidden" name="addons" id="addons">

                                <input type="hidden" name="package_id" id="package_id">

                                <input type="hidden" name="total" id="subtotal">

                                <input type="hidden" name="package_number_of_guest" class="package_number_of_guest">

                                <section class="payment-info dynamic-price mt-4" style="display: none;">
                                    <div class="">
                                        <div class="row">

                                            <div class="col-md-12">
                                                <h2 style="margin-bottom: 35px;">Payment</h2>

                                                <!-- Left: Form Fields -->
                                                <div class="form-left">

                                                    <button type="button" class="same-as-info">Same as package holder
                                                        information</button>

                                                    <div class="form-row">
                                                        <div class="form-group" style="width: 50%;">
                                                            <label for="firstName">First Name</label>
                                                            <input name="payment_first_name" type="text" id="firstName" placeholder="" required />
                                                        </div>
                                                        <div class="form-group" style="width: 50%;">
                                                            <label for="lastName">Last Name</label>
                                                            <input name="payment_last_name" type="text" id="lastName" placeholder="" required />
                                                        </div>
                                                    </div>

                                                    <div class="form-row">
                                                        <div class="form-group" style="width: 50%;">
                                                            <label for="phone">Phone Number</label>
                                                            <input name="payment_phone" type="tel" id="phone" placeholder="" required />
                                                        </div>
                                                        <div class="form-group" style="width: 50%;">
                                                            <label for="email">Email</label>
                                                            <input name="payment_email" type="email" id="email"
                                                                placeholder="sample@sample.com" required />
                                                        </div>
                                                    </div>

                                                    <div class="form-row">
                                                        <div class="form-group" style="width: 100%;">
                                                            <label for="bill-add">Billing Address</label>
                                                            <input name="payment_address" type="tel" id="bill-add" placeholder="" required />
                                                        </div>

                                                    </div>

                                                    <div class="form-row">
                                                        <div class="form-group" style="width: 50%;">
                                                            <label for="city">City</label>
                                                            <input type="tel" name="payment_city" id="city" placeholder="" required />
                                                        </div>
                                                        <div class="form-group" style="width: 50%;">
                                                            <label for="st-pv">State/ Province</label>
                                                            <input type="text" name="payment_state" id="st-pv" placeholder="" required />
                                                        </div>
                                                    </div>

                                                    <div class="form-row">
                                                        <div class="form-group" style="width: 50%;">
                                                            <label for="country">Country</label>
                                                            <select id="country" name="payment_country" class="form-select" required></select>
                                                        </div>
                                                        <div class="form-group" style="width: 50%;">
                                                            <label for="dob-month">Date of Birth</label>
                                                            <div class="form-row">
                                                                <select id="payment-dob-month" name="payment_month" class="form-select" style="width: 32%; display: inline-block; margin-right: 2%;" required></select>
                                                                <select id="payment-dob-day" name="payment_day" class="form-select" style="width: 32%; display: inline-block; margin-right: 2%;" required></select>
                                                                <select id="payment-dob-year" name="payment_year" class="form-select" style="width: 32%; display: inline-block;" required></select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-row">
                                                        <div class="form-group" style="width: 100%;">
                                                            <label for="card-num">Card Number</label>
                                                            <input type="tel" name="card_number" id="card-num" placeholder="" required />
                                                        </div>

                                                    </div>

                                                    <div class="form-row">
                                                        <div class="form-group" style="width: 25%;">

                                                            <input type="tel" maxlength="2" name="card_month" id="city" placeholder="Month" required />
                                                        </div>
                                                        <div class="form-group" style="width: 25%;">

                                                            <input type="tel" maxlength="2" name="card_year" id="st-pv" placeholder="Year" required />
                                                        </div>
                                                        <div class="form-group" style="width: 25%;">

                                                            <input type="tel" name="card_cvv" id="city" placeholder="CVV" required />
                                                        </div>
                                                        <div class="form-group" style="width: 25%;">

                                                            <input type="text" name="payment_zip_code" id="st-pv" placeholder="Zip" required />
                                                        </div>
                                                    </div>

                                                    <div class="checkbox-container">
                                                        <label>
                                                            <input type="checkbox" id="smsConsent" />
                                                            I agree to receive SMS communications from {{ $data->name }}
                                                            regarding my upcoming
                                                            reservation. Message and data rates may apply. Messaging
                                                            frequency may vary. Reply
                                                            STOP to opt out at any time.
                                                        </label>

                                                        <label>
                                                            <input type="checkbox" id="termsConsent" />
                                                            I agree to the {{ $data->name }} <a target="_blank" href="{{ $data->terms }}">Terms of Service</a> and <a
                                                                target="_blank" href="{{ $data->privacy }}">Privacy
                                                                Policy</a>.
                                                        </label>
                                                    </div>

                                                    <button class="submit-btn" id="submitBtn" type="submit">Buy</button>

                                                </div>

                                            </div>






                                        </div>
                                    </div>
                                </section>
                            </form>

                        </div>
                        <div class="col-md-4">
                            <div class="form-right">
                                <h2>Location</h2>
                                <p>{{ $data->location }}</p>
                                <iframe
                                    src="https://www.google.com/maps?q={{ urlencode($data->location) }}&output=embed"
                                    allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                                </iframe>
                                <h2>Contact</h2>
                                <p><a href="">{{ $data->phone }}</a></p>
                                <p><a href="">{{ $data->email }}</a></p>
                            </div>
                        </div>
                    </div>
                </div>


            </section>

            <input type="hidden" name="type" value="package">
        </div>





        <section>
            <div class="container py-5">
                <div class="event-header">
                    <h2>Upcoming Events</h2>
                    <a href="#" class="view-all">View All</a>
                </div>
                <div class="row g-4">
                    <!-- Event Card 1 -->
                    @foreach ($data->events as $item)
                        <div class="col-md-4">
                            <a href="/?domain={{ $data->domain }}&event_name={{ $item->name }}" class="event-card" style="width: 100%; background: transparent; text-decoration: none;">
                                <div class="card p-3 text-center">
                                    <img src="{{ asset('uploads/'.$item->image) }}" width="298px" height="298px" style="width: 298px; height: 298px;">
                                    <div class="d-flex">
                                        <div class="event-day" style="width: 50%;">{{ \Carbon\Carbon::parse($item->date)->format('l') }}</div>
                                        <div class="event-dates" style="width: 50%;">{{ \Carbon\Carbon::parse($item->date)->format('M') }}<span> <br> {{ \Carbon\Carbon::parse($item->date)->format('d') }}</span></div>
                                    </div>

                                    <div class="event-location">{{ $data->location }}</div>
                                    <div class="event-location">Book Guestlist</div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>


        </section>

        <div class="modal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Modal title</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Modal body text goes here.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>


    </main>
    <footer>
        <p>Powered by <a href="{{ $data->domain }}" style="color: aliceblue;">{{ $data->name }}</a>.</p>
    </footer>
    <script src="scripts/main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <script>

        // Copy package holder info to payment info
        $(document).on('click', '.same-as-info', function() {
            // Text fields
            $("input[name='payment_first_name']").val($("input[name='package_first_name']").val());
            $("input[name='payment_last_name']").val($("input[name='package_last_name']").val());
            $("input[name='payment_phone']").val($("input[name='package_phone']").val());
            $("input[name='payment_email']").val($("input[name='package_email']").val());
            $("input[name='payment_address']").val($("textarea[name='package_note']").val()); // If you want to copy note to address, otherwise remove this line
            // City, state, zip, card fields are not in package holder, so skip
            // DOB selects
            $("#payment-dob-month").val($("#package-dob-month").val());
            $("#payment-dob-day").val($("#package-dob-day").val());
            $("#payment-dob-year").val($("#package-dob-year").val());

            $("#payment-dob-month2").val($("#package-dob-month").val());
            $("#payment-dob-day2").val($("#package-dob-day").val());
            $("#payment-dob-year2").val($("#package-dob-year").val());
            // Country
            $("#country").val(''); // No country in package holder, so clear or set as needed
        });
        // Populate country select
        function populateCountrySelect(selectId) {
            const countries = [
                'United States','Canada','United Kingdom','Australia','Germany','France','Italy','Spain','Netherlands','Brazil','India','China','Japan','South Korea','Mexico','Russia','South Africa','New Zealand','Sweden','Norway','Denmark','Finland','Ireland','Switzerland','Austria','Belgium','Portugal','Poland','Turkey','Argentina','Chile','Colombia','Czech Republic','Greece','Hungary','Iceland','Indonesia','Israel','Malaysia','Philippines','Saudi Arabia','Singapore','Slovakia','Thailand','Ukraine','United Arab Emirates','Vietnam','Egypt','Morocco','Nigeria','Pakistan','Romania','Serbia','Croatia','Slovenia','Bulgaria','Estonia','Latvia','Lithuania','Luxembourg','Malta','Monaco','Montenegro','Qatar','Kuwait','Oman','Bahrain','Jordan','Lebanon','Cyprus','Georgia','Kazakhstan','Uzbekistan','Bangladesh','Sri Lanka','Nepal','Cambodia','Laos','Myanmar','Mongolia','Afghanistan','Albania','Armenia','Azerbaijan','Belarus','Bosnia and Herzegovina','Botswana','Brunei','Burkina Faso','Burundi','Cameroon','Cape Verde','Central African Republic','Chad','Comoros','Congo','Costa Rica','Cuba','Djibouti','Dominica','Dominican Republic','Ecuador','El Salvador','Equatorial Guinea','Eritrea','Eswatini','Ethiopia','Fiji','Gabon','Gambia','Ghana','Grenada','Guatemala','Guinea','Guinea-Bissau','Guyana','Haiti','Honduras','Jamaica','Kenya','Kiribati','Lesotho','Liberia','Libya','Liechtenstein','Madagascar','Malawi','Maldives','Mali','Marshall Islands','Mauritania','Mauritius','Micronesia','Moldova','Mozambique','Namibia','Nauru','Nicaragua','Niger','North Korea','North Macedonia','Palau','Palestine','Panama','Papua New Guinea','Paraguay','Peru','Rwanda','Saint Kitts and Nevis','Saint Lucia','Saint Vincent and the Grenadines','Samoa','San Marino','Sao Tome and Principe','Senegal','Seychelles','Sierra Leone','Solomon Islands','Somalia','South Sudan','Sudan','Suriname','Syria','Tajikistan','Tanzania','Timor-Leste','Togo','Tonga','Trinidad and Tobago','Tunisia','Turkmenistan','Tuvalu','Uganda','Uruguay','Vanuatu','Vatican City','Venezuela','Yemen','Zambia','Zimbabwe'
            ];
            const select = document.getElementById(selectId);
            select.innerHTML = '<option value="">Select Country</option>';
            countries.forEach(function(country) {
                select.innerHTML += `<option value="${country}">${country}</option>`;
            });
        }

        function populateCountrySelect2(selectId) {
            const countries = [
                'United States','Canada','United Kingdom','Australia','Germany','France','Italy','Spain','Netherlands','Brazil','India','China','Japan','South Korea','Mexico','Russia','South Africa','New Zealand','Sweden','Norway','Denmark','Finland','Ireland','Switzerland','Austria','Belgium','Portugal','Poland','Turkey','Argentina','Chile','Colombia','Czech Republic','Greece','Hungary','Iceland','Indonesia','Israel','Malaysia','Philippines','Saudi Arabia','Singapore','Slovakia','Thailand','Ukraine','United Arab Emirates','Vietnam','Egypt','Morocco','Nigeria','Pakistan','Romania','Serbia','Croatia','Slovenia','Bulgaria','Estonia','Latvia','Lithuania','Luxembourg','Malta','Monaco','Montenegro','Qatar','Kuwait','Oman','Bahrain','Jordan','Lebanon','Cyprus','Georgia','Kazakhstan','Uzbekistan','Bangladesh','Sri Lanka','Nepal','Cambodia','Laos','Myanmar','Mongolia','Afghanistan','Albania','Armenia','Azerbaijan','Belarus','Bosnia and Herzegovina','Botswana','Brunei','Burkina Faso','Burundi','Cameroon','Cape Verde','Central African Republic','Chad','Comoros','Congo','Costa Rica','Cuba','Djibouti','Dominica','Dominican Republic','Ecuador','El Salvador','Equatorial Guinea','Eritrea','Eswatini','Ethiopia','Fiji','Gabon','Gambia','Ghana','Grenada','Guatemala','Guinea','Guinea-Bissau','Guyana','Haiti','Honduras','Jamaica','Kenya','Kiribati','Lesotho','Liberia','Libya','Liechtenstein','Madagascar','Malawi','Maldives','Mali','Marshall Islands','Mauritania','Mauritius','Micronesia','Moldova','Mozambique','Namibia','Nauru','Nicaragua','Niger','North Korea','North Macedonia','Palau','Palestine','Panama','Papua New Guinea','Paraguay','Peru','Rwanda','Saint Kitts and Nevis','Saint Lucia','Saint Vincent and the Grenadines','Samoa','San Marino','Sao Tome and Principe','Senegal','Seychelles','Sierra Leone','Solomon Islands','Somalia','South Sudan','Sudan','Suriname','Syria','Tajikistan','Tanzania','Timor-Leste','Togo','Tonga','Trinidad and Tobago','Tunisia','Turkmenistan','Tuvalu','Uganda','Uruguay','Vanuatu','Vatican City','Venezuela','Yemen','Zambia','Zimbabwe'
            ];
            const select = document.getElementById(selectId);
            select.innerHTML = '<option value="">Select Country</option>';
            countries.forEach(function(country) {
                select.innerHTML += `<option value="${country}">${country}</option>`;
            });
        }
        // On DOM ready, also populate country select
        $(function() {
            populateCountrySelect('country');
            populateCountrySelect2('country2');
        });
        // Populate DOB selects for all three sections
        function populateDOBSelects(monthId, dayId, yearId) {
            const monthSelect = document.getElementById(monthId);
            const daySelect = document.getElementById(dayId);
            const yearSelect = document.getElementById(yearId);
            // Months 1-12
            monthSelect.innerHTML = '';
            for (let m = 1; m <= 12; m++) {
                monthSelect.innerHTML += `<option value="${m.toString().padStart(2, '0')}">${m.toString().padStart(2, '0')}</option>`;
            }
            // Days 1-31
            daySelect.innerHTML = '';
            for (let d = 1; d <= 31; d++) {
                daySelect.innerHTML += `<option value="${d.toString().padStart(2, '0')}">${d.toString().padStart(2, '0')}</option>`;
            }
            // Years: current year to (current year - 100)
            const currentYear = new Date().getFullYear();
            yearSelect.innerHTML = '';
            for (let y = currentYear; y >= currentYear - 100; y--) {
                yearSelect.innerHTML += `<option value="${y}">${y}</option>`;
            }
        }
        // On DOM ready
        $(function() {
            populateDOBSelects('dob-month', 'dob-day', 'dob-year');
            populateDOBSelects('package-dob-month', 'package-dob-day', 'package-dob-year');
            populateDOBSelects('payment-dob-month', 'payment-dob-day', 'payment-dob-year');
            populateDOBSelects('payment-dob-month2', 'payment-dob-day2', 'payment-dob-year2');
        });


        $(document).ready(function () {
            $('.vip-btn').on('click', function () {
                $('.vip-btn').not(this).text('Add Package');
                $(this).text('Added');
                price = $(this).data('price');
                gratuity = $(this).data('gratuity');
                refundable = $(this).data('refundable');
                let sales_tax = $('#sales_tax').val();

                gratuited_price = (price / 100) * gratuity;
                refundable_price = (price / 100) * refundable;
                let sales_tax_price = ((gratuited_price + price) / 100) * sales_tax;
                id = $(this).data('id');

                $.ajax({
                    url: "addons/" + id,
                    type: 'GET',
                    dataType: 'json', // added data type
                    success: function (res) {
                        htmls = ``;
                        res.forEach(function (addon) {
                            html = `<div class="addon-item" style="height: 60px;">
                                    <div style="float: left;">
                                        <label for="${addon.id}" data-title="${addon.name}" data-description="${addon.description}" style="float: left; cursor: pointer;">${addon.name} ($${addon.price})</label>
                                        <div onClick="openModal()" style="float: right; margin-left: 10px;">
                                            <svg style="fill: #f5a623; height: 20p x; width: 20px; cursor: pointer;" version="1.1" xmlns="http://www.w3.org/2000/sv g" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" xml:space="preserve">
                                                <g>
                                                    <path d="M256,0C115.39,0,0,115.39,0,256s115.39,256,256,256s256-115.39,256-256S396.61,0,256,0z M286,376
                                                    c0,16.538-13.462,30-30,30c-16.538,0-30-13.462-30-30V226c0-16.538,13.462-30,30-30c16.538,0,30,13.462,30,30V376z M256,166
                                                    c-16.538,0-30-13.462-30-30c0-16.538,13.462-30,30-30c16.538,0,30,13.462,30,30C286,152.538,272.538,166,256,166z"></path>
                                                </g>
                                            </svg>
                                        </div>
                                    </div>
                                    <input style="float: right; margin-top: 3px;" type="checkbox" class="termsConsent" onClick="addToTotal(${addon.price},'${addon.name}',${addon.id})" data-price="${addon.price}" id="${addon.id}" />
                                </div>`;
                            htmls += html;
                        });
                        $('.addons-list').empty(); // Clear previous addons
                        $('.addons-list').html(htmls);

                        $('.addons').show();

                        $('#package_id').val(id); // Set the package ID
                    }
                });

                $('.default-package-price span').text('$' + price.toFixed(2));
                $('.default-gratuity span').text('$' + gratuited_price.toFixed(2));

                $('.default-sales-tax span').text('$' + sales_tax_price.toFixed(2));
                $('.default-refundable span').text('$' + refundable_price.toFixed(2));
                let total = parseFloat(price) + parseFloat(gratuited_price) + parseFloat(sales_tax_price);
                $('.default-total span').text('$' + total.toFixed(2));
                $('.default-deposit span').text('$' + total.toFixed(2));
                $('#subtotal').val(total.toFixed(2));

                $('.dynamic-price').show();
                $('.default-price').hide();
            });
        });
    </script>

    <input type="hidden" id="gratuity" value="{{ $data->gratuity_fee }}">

    <input type="hidden" id="refundable" value="{{ $data->refundable_fee }}">

    <input type="hidden" id="sales_tax" value="{{ $data->sales_tax_fee ?? 10 }}">

    <script>
        function openModal() {
            // Get the description from the clicked addon
            const description = event.target.closest('.addon-item').querySelector('label').getAttribute('data-description');
            const title = event.target.closest('.addon-item').querySelector('label').getAttribute('data-title');

            $('.modal-title').text(title);
            $('.modal-body').html(`<p style="color: #000 !important">${description}</p>`);
            $('.modal').modal('show');

        }

        function openPackageModal() {

            // Get the description from the clicked package
            const description = event.target.closest('.vip-card').querySelector('.items').getAttribute('data-description');
            const title = event.target.closest('.vip-card').querySelector('.items').getAttribute('data-title');

            $('.modal-title').text(title);
            $('.modal-body').html(`<p style="color: #000 !important">${description}</p>`);
            $('.modal').modal('show');

        }

        function addToTotal(price, name, id) {
            // Add the price to the total
            if (event.target.checked) {
                price = parseFloat(price);

                hh = `<div style="font-size: 12px;" class="default-refundabless">${name}: <span>$${price.toFixed(2)}</span></div>`;
                $('.addonns').append(hh);

                $('#addons').val(function (i, val) {
                    return val + (val ? ',' : '') + id; // Append the addon ID
                });
            } else {
                // Remove the price from the total
                $('.addonns .default-refundabless').filter(function () {
                    return $(this).text().trim().startsWith(name + ':');
                }).remove();
                price = -parseFloat(price);

                $('#addons').val(function (i, val) {
                    return val.replace(new RegExp('(^|,)' + id + '(,|$)'), '$1$2'); // Remove the addon ID
                });
            }
            let total = 0;
            // Sum all addon prices currently shown
            $('.addonns .default-refundabless span').each(function() {
                total += parseFloat($(this).text().replace('$', ''));
            });
            // Add package price (if any)
            let packagePrice = parseFloat($('.default-package-price span').text().replace('$', '')) || 0;
            total += packagePrice;
            // Calculate gratuity, sales tax, refundable
            let gratuity = parseFloat($('#gratuity').val()) || 0;
            let sales_tax = parseFloat($('#sales_tax').val()) || 0;
            let refundable = parseFloat($('#refundable').val()) || 0;
            let gratuited_price = (total / 100) * gratuity;
            let sales_tax_price = ((total + gratuited_price) / 100) * sales_tax;
            let refundable_price = (total / 100) * refundable;
            $('.default-gratuity span').text('$' + gratuited_price.toFixed(2));
            $('.default-sales-tax span').text('$' + sales_tax_price.toFixed(2));
            $('.default-refundable span').text('$' + refundable_price.toFixed(2));
            let grandTotal = total + gratuited_price + sales_tax_price + refundable_price;
            $('.default-total span').text('$' + grandTotal.toFixed(2));
            $('#subtotal').val(grandTotal.toFixed(2));
            $('.default-deposit span').text('$' + grandTotal.toFixed(2));
        }
    </script>

    <script>
        $('#package_number_of_guest').on('change', function() {
            var selectedValue = $(this).val();
            $('.package_number_of_guest').val(selectedValue);
        });
    </script>
</body>

</html>
