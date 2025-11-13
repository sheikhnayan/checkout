<!DOCTYPE html>
<r lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Checkout</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.7/css/bootstrap.min.css"
            integrity="sha512-fw7f+TcMjTb7bpbLJZlP8g2Y4XcCyFZW8uy8HsRZsH/SwbMw0plKHFHr99DN3l04VsYNwvzicUX/6qurvIxbxw=="
            crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="{{ asset('styles/main.css') }}">
        <style>
            #Pick-up-time::placeholder {
                color: #fff !important;
            }
            #Pick-up-time::-webkit-input-placeholder {
    color: #fff !important;
}

#Pick-up-time:-ms-input-placeholder {
    color: #fff !important;
}

#Pick-up-time::-moz-placeholder {
    color: #fff !important;
}

#Pick-up-time:-moz-placeholder {
    color: #fff !important;
}

/* Step-by-step checkout styles */
.checkout-steps {
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 2rem 0;
    padding: 0;
    list-style: none;
}

.step {
    flex: 1;
    text-align: center;
    position: relative;
    padding: 0 1rem;
}

.step-number {
    display: inline-block;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #444;
    color: #fff;
    line-height: 40px;
    font-weight: bold;
    margin-bottom: 0.5rem;
    border: 2px solid #444;
}

.step.active .step-number {
    background: {{ $data->color }};
    border-color: {{ $data->color }};
    color: #000;
}

.step.completed .step-number {
    background: #28a745;
    border-color: #28a745;
    color: #fff;
}

.step-title {
    font-size: 0.875rem;
    color: #999;
    margin: 0;
}

.step.active .step-title,
.step.completed .step-title {
    color: #fff;
    font-weight: bold;
}

.step::after {
    content: '';
    position: absolute;
    top: 20px;
    right: -50%;
    width: 100%;
    height: 2px;
    background: #444;
    z-index: -1;
}

.step:last-child::after {
    display: none;
}

.step.completed::after {
    background: #28a745;
}

.checkout-section {
    display: none;
}

.checkout-section.active {
    display: block;
}

.step-navigation {
    text-align: center;
    margin: 2rem 0;
}

.btn-next, .btn-prev {
    background: {{ $data->color }};
    color: #000;
    border: none;
    padding: 12px 30px;
    border-radius: 25px;
    font-weight: bold;
    margin: 0 10px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-next:hover, .btn-prev:hover {
    opacity: 0.8;
    transform: translateY(-2px);
}

.btn-prev {
    background: #666;
    color: #fff;
}

.btn-next:disabled {
    background: #444;
    color: #888;
    cursor: not-allowed;
    transform: none;
}

.required-field {
    border-color: #ff6b6b !important;
}

/* Consistent button styles */
.same-as-info, .same-as-info-transport {
    background: {{ $data->color }} !important;
    color: #000 !important;
    border: none;
    padding: 12px 25px;
    border-radius: 25px;
    font-weight: bold;
    margin-bottom: 20px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-block;
    width: 280px;
    text-align: center;
    font-size: 14px;
}

.same-as-info:hover, .same-as-info-transport:hover {
    opacity: 0.8;
    transform: translateY(-2px);
}

.btn-next, .btn-prev, .submit-btn {
    background: {{ $data->color }} !important;
    color: #000 !important;
    border: none;
    padding: 12px 30px;
    border-radius: 25px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 16px;
    min-width: 200px;
    text-align: center;
}

.btn-next:hover, .btn-prev:hover, .submit-btn:hover {
    opacity: 0.8;
    transform: translateY(-2px);
}

.btn-prev {
    background: #666 !important;
    color: #fff !important;
}

/* Mobile responsive pickup time */
@media (max-width: 768px) {
    .trans-group {
        width: 100% !important;
    }

    .step-title{
        font-size: 0.5rem !important;
    }
    
    #Pick-up-time {
        width: 100% !important;
    }
    
    .flatpickr-time {
        width: 100% !important;
        max-width: 100% !important;
    }
    
    /* Mobile button spacing and layout */
    .same-as-info, .same-as-info-transport {
        width: 100%;
        margin-top: 15px;
        margin-bottom: 25px;
    }
    
    .btn-next, .btn-prev, .submit-btn {
        margin-top: 15px;
        min-width: 180px;
    }
    
    .step-navigation {
        margin-top: 25px;
    }
}
            .checkbox-container input[type="checkbox"]:checked {
                background-color:
                    {{ $data->color }}
                    !important;
                border-color:
                    {{ $data->color }}
                    !important;
            }

            .card:hover {
                border-color:
                    {{ $data->color }}
                    !important;
            }

            .submit-btn {
                background:
                    {{ $data->color }}
                    !important;
                color: #000 !important;
            }

            .event-filters .active{
                background-color: {{ $data->color }} !important;
                color: #000 !important;
            }

            .event-filter:hover{
                background-color: {{ $data->color }} !important;
                color: #000 !important;
            }

            .submit-btn.active {
                background:
                    {{ $data->color }}
                ;
            }
            body{
    background: {{ $data->background_color }} !important;
}
option{
        background: {{ $data->background_color }} !important;
    }
    select option {
      background: {{ $data->background_color }} !important;
    }

            .flatpickr-input[readonly] {
  color: #fff !important;
}

.StripeElement {
            padding: 10px;
            border: 1px solid #9797a0;
            border-radius: 10px;
            margin-bottom: 15px;
            color: #fff !important;
        }
.StripeElement::placeholder{
    color: #fff !important;
}

.holder-info::before {
  content: '';
  display: block;
  width: 100%;
  height: 1px;
  background: #444;
  margin-top: 10px;
  margin-bottom: 20px;
}

/* Safari/iOS fixes for JavaScript-generated select fields */
@media screen and (-webkit-min-device-pixel-ratio: 0) {
    /* Safari and WebKit specific styles */
    select.form-select {
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
        appearance: none !important;
        background: url('data:image/svg+xml;charset=UTF-8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white"><path d="M7 10l5 5 5-5z"/></svg>') no-repeat right 15px center !important;
        background-size: 20px !important;
        background-color: transparent !important;
        padding: 12px 45px 12px 15px !important;
        border: 1px solid #9797a0 !important;
        border-radius: 10px !important;
        color: #fff !important;
        font-size: 16px !important;
        min-height: 45px !important;
        line-height: 1.5 !important;
    }
    
    select.form-select:focus {
        outline: none !important;
        border-color: {{ $data->color }} !important;
        box-shadow: 0 0 0 0.2rem rgba(255,255,255,0.25) !important;
    }
    
    /* Specific fixes for JavaScript-generated country selects */
    #country, #country2 {
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
        appearance: none !important;
        background: url('data:image/svg+xml;charset=UTF-8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white"><path d="M7 10l5 5 5-5z"/></svg>') no-repeat right 15px center !important;
        background-size: 20px !important;
        background-color: transparent !important;
        padding: 12px 45px 12px 15px !important;
        border: 1px solid #9797a0 !important;
        border-radius: 10px !important;
        color: #fff !important;
        font-size: 16px !important;
        min-height: 45px !important;
        line-height: 1.5 !important;
    }
    
    /* State/Province select */
    #st-pv {
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
        appearance: none !important;
        background: url('data:image/svg+xml;charset=UTF-8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white"><path d="M7 10l5 5 5-5z"/></svg>') no-repeat right 15px center !important;
        background-size: 20px !important;
        background-color: transparent !important;
        padding: 12px 45px 12px 15px !important;
        border: 1px solid #9797a0 !important;
        border-radius: 10px !important;
        color: #fff !important;
        font-size: 16px !important;
        min-height: 45px !important;
        line-height: 1.5 !important;
    }
    
    /* JavaScript-generated DOB selects */
    #dob-month, #dob-day, #dob-year,
    #payment-dob-month, #payment-dob-day, #payment-dob-year {
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
        appearance: none !important;
        background: url('data:image/svg+xml;charset=UTF-8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white"><path d="M7 10l5 5 5-5z"/></svg>') no-repeat right 10px center !important;
        background-size: 15px !important;
        background-color: transparent !important;
        padding: 12px 30px 12px 15px !important;
        border: 1px solid #9797a0 !important;
        border-radius: 10px !important;
        color: #fff !important;
        font-size: 16px !important;
        min-height: 45px !important;
        line-height: 1.5 !important;
        text-align: center !important;
    }
}

/* Mobile responsive fixes for all devices */
@media (max-width: 768px) {
    select.form-select {
        font-size: 16px !important; /* Prevents zoom on iOS */
        padding: 12px 45px 12px 15px !important;
        min-height: 45px !important;
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
        appearance: none !important;
    }
    
    /* Mobile fixes for JavaScript-generated selects */
    #country, #country2, #st-pv {
        font-size: 16px !important;
        padding: 12px 45px 12px 15px !important;
        min-height: 45px !important;
        width: 100% !important;
        margin-bottom: 10px !important;
    }
    
    /* DOB selects mobile layout */
    #dob-month, #dob-day, #dob-year,
    #payment-dob-month, #payment-dob-day, #payment-dob-year {
        font-size: 16px !important;
        padding: 12px 30px 12px 15px !important;
        min-height: 45px !important;
        margin-bottom: 10px !important;
    }
    
    /* Make DOB selects stack on mobile */
    .form-row select[style*="width: 32%"] {
        width: 100% !important;
        margin-right: 0 !important;
        margin-bottom: 10px !important;
        display: block !important;
    }

    .ddoobb{
        width: 100% !important;
    }

    .cciittyy{
        width: 100% !important;
    }

    .ffoorrmm{
        display: block !important;
    }
}

/* iOS Safari specific targeting */
@supports (-webkit-touch-callout: none) {
    /* This targets iOS Safari specifically */
    select {
        font-size: 16px !important;
        -webkit-appearance: none !important;
        padding: 12px 45px 12px 15px !important;
        min-height: 45px !important;
    }
    
    /* Force proper rendering of select fields */
    #country, #country2, #st-pv,
    #dob-month, #dob-day, #dob-year,
    #payment-dob-month, #payment-dob-day, #payment-dob-year {
        -webkit-appearance: none !important;
        background-color: transparent !important;
        border: 1px solid #9797a0 !important;
        color: #fff !important;
        padding: 12px 45px 12px 15px !important;
        font-size: 16px !important;
        min-height: 45px !important;
    }
    
    /* Smaller dropdown arrows for DOB fields */
    #dob-month, #dob-day, #dob-year,
    #payment-dob-month, #payment-dob-day, #payment-dob-year {
        padding: 12px 30px 12px 15px !important;
    }
}

/* Force re-styling after JavaScript population */
select[id*="country"], select[id*="dob"], select[id="st-pv"] {
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
    appearance: none !important;
    background-color: transparent !important;
    background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white"><path d="M7 10l5 5 5-5z"/></svg>') !important;
    background-repeat: no-repeat !important;
    background-position: right 15px center !important;
    background-size: 20px !important;
    padding: 12px 45px 12px 15px !important;
    border: 1px solid #9797a0 !important;
    border-radius: 10px !important;
    color: #fff !important;
    font-size: 16px !important;
    min-height: 45px !important;
}

/* Option styling for better visibility */
select option {
    background: {{ $data->background_color }} !important;
    color: #fff !important;
    padding: 10px !important;
}

/* Hide default webkit calendar picker indicator */
input[type="date"]::-webkit-calendar-picker-indicator {
    display: none !important;
    opacity: 0 !important;
    width: 0 !important;
    height: 0 !important;
}

/* Date input container for custom icon */
.date-input-wrapper {
    position: relative !important;
    display: inline-block !important;
    width: 100% !important;
}

/* Date input with custom calendar icon */
input[type="date"] {
    position: relative !important;
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
    appearance: none !important;
    background: transparent !important;
    border: 1px solid #9797a0 !important;
    border-radius: 10px !important;
    padding: 12px 45px 12px 15px !important;
    color: #fff !important;
    font-size: 16px !important;
    min-height: 45px !important;
    width: 100% !important;
    box-sizing: border-box !important;
}

input[type="date"]:focus {
    outline: none !important;
    border-color: {{ $data->color }} !important;
}

/* Custom calendar icon */
.custom-calendar-icon {
    position: absolute !important;
    right: 15px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    width: 16px !important;
    height: 16px !important;
    background: url('data:image/svg+xml;charset=UTF-8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>') no-repeat center !important;
    background-size: contain !important;
    cursor: pointer !important;
    z-index: 2 !important;
    pointer-events: auto !important;
}

/* Specific styling for package_use_date */
#package_use_date {
    position: relative !important;
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
    appearance: none !important;
    background: transparent !important;
    border: 1px solid #9797a0 !important;
    border-radius: 10px !important;
    padding: 12px 45px 12px 15px !important;
    color: #fff !important;
    -webkit-text-fill-color: #fff !important;
    font-size: 16px !important;
    min-height: 45px !important;
    width: 100% !important;
    box-sizing: border-box !important;
}

#package_use_date:disabled,
#package_use_date:read-only {
    opacity: 1 !important;
    -webkit-text-fill-color: #fff !important;
    color: #fff !important;
}

#package_use_date:focus {
    outline: none !important;
    border-color: {{ $data->color }} !important;
}

#package_use_date::-webkit-calendar-picker-indicator {
    display: none !important;
    opacity: 0 !important;
    width: 0 !important;
    height: 0 !important;
}

/* Mobile responsive date input fixes */
@media (max-width: 768px) {
    input[type="date"] {
        font-size: 16px !important; /* Prevents zoom on iOS */
        padding: 12px 40px 12px 15px !important;
        min-height: 45px !important;
    }
    
    #package_use_date {
        font-size: 16px !important;
        padding: 12px 40px 12px 15px !important;
        min-height: 45px !important;
    }
}

/* Additional iOS Safari date input fixes */
@supports (-webkit-touch-callout: none) {
    input[type="date"] {
        font-size: 16px !important;
        -webkit-appearance: none !important;
        -webkit-text-fill-color: #fff !important;
        color: #fff !important;
        padding: 12px 40px 12px 15px !important;
        min-height: 45px !important;
    }
    
    input[type="date"]::-webkit-calendar-picker-indicator {
        opacity: 1 !important;
        display: block !important;
    }
    
    #package_use_date {
        -webkit-appearance: none !important;
        -webkit-text-fill-color: #fff !important;
        color: #fff !important;
        font-size: 16px !important;
        padding: 12px 40px 12px 15px !important;
        min-height: 45px !important;
    }
    
    #package_use_date::-webkit-calendar-picker-indicator {
        opacity: 1 !important;
        display: block !important;
    }
}

/* Navigation CSS for proper width adjustment */
nav {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0;
    width: 100%;
    max-width: fit-content;
    margin: 0 auto;
    padding: 5px 5px;
}

nav .tab {
    background: #333;
    border: none;
    color: #fff;
    padding: 0px 20px;
    cursor: pointer;
    transition: all 0.3s ease;
    border-radius: 0;
    flex: 0 0 auto;
    white-space: nowrap;
    font-size: 16px;
    min-width: 120px;
}

nav .tab:first-child {
    border-top-left-radius: 5px;
    border-bottom-left-radius: 5px;
}

nav .tab:last-child {
    border-top-right-radius: 5px;
    border-bottom-right-radius: 5px;
}

nav .tab:only-child {
    border-radius: 10px;
}

nav .tab.active {
    background: {{ $data->color }};
    color: #000;
}

nav .tab:hover {
    background: {{ $data->color }};
    color: #000;
}

nav .tab p {
    margin: 0;
    font-weight: 600;
}

/* Mobile responsive navigation */
@media (max-width: 768px) {
    nav .tab {
        padding: 0px 20px;
        font-size: 14px;
        min-width: 100px;
    }
}

/* Website custom styling */
body, 
label, 
span, 
p, 
h1, h2, h3, h4, h5, h6, 
.event-day, 
.event-time, 
.event-desc h2, 
.event-desc li, 
.website-desc h2, 
.website-description-content,
.label,
nav p,
a {
    color: {{ $data->font_color ?? '#000000' }} !important;
}

.website-desc {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    padding: 20px;
    margin-top: 20px;
}

        </style>
    </head>

    <body>
        <div class="background-glow"></div>
        <header>
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
                        <div class="col-md-12 text-center mb-4 mt-4">
                            <a href="{{ $data->back_link }}" class="btn tbn-success" style="background: {{ $data->color }}; color: #000">{{ $data->back_text }}</a>
                        </div>
                        <div class="col-md-12">
                            <div class="row justify-content-center">
                                <div class="col-md-5">
                                    <div class="logo-section">
                                        <img src="{{ asset('uploads/' . $data->logo) }}" alt="Peppermint Hippo Logo"
                                            class="logo" @if($data->logo_width || $data->logo_height) style="@if($data->logo_width)width: {{ $data->logo_width }}px;@endif @if($data->logo_height)height: {{ $data->logo_height }}px;@endif" @endif>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="text-align: center;">
                            <div class="row justify-content-center">
                                <div class="col-md-4">
                                    <div class="event-info">
                                        <div class="date">
                                            <label> Choose Your Reservation Date</label>
                                            <div class="event-date" style="border: unset;">
                                                <div class="date-input-wrapper">       
                                                    <input id="package_use_date" type="date" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                                                        onclick="this.showPicker && this.showPicker()">
                                                    <span class="custom-calendar-icon" onclick="document.getElementById('package_use_date').showPicker && document.getElementById('package_use_date').showPicker()"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="event-desc">
                                <h2>{{ $data->description_label ?? 'Description' }}</h2>
                                <ul>
                                    <li class="my-scrollable-div">{{ $data->description }}</li>

                                </ul>
                            </div>
                            
                            @if($data->text_description)
                            <div class="website-desc mt-4">
                                <h2>About</h2>
                                <div class="website-description-content">
                                    {{ $data->text_description }}
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </section>


        </header>
        @if ($data->reservation == 1)
        <nav>
            <button id="button" data-name='guest' class="tab active">
                <p>{{ $data->guest_list_button_text ?? 'Guest List' }}</p>
            </button>
            <button id="button" data-name="package" class="tab">
                <p>{{ $data->package_button_text ?? 'Packages' }}</p>
            </button>
        </nav>
        @endif
        <main>
            <div class="container mt-4">
                @if ($data->reservation == 1)
                    <div class="guest">
                        <form action="{{ route('reservations.store', ['slug' => $data->slug]) }}" method="post">
                            @csrf
                            <input type="hidden" name="website_id" value="{{ $data->id }}">
                            <section style="width: 100%">
                                <div class="">
    
                                    <div class="row">
                                        <div class="col-md-12">
                                            <!-- Left: Form Fields -->
                                            <div class="">
    
                                                <div class="form-row" style="margin-bottom: 1rem;">
                                                    <div class="form-group" style="width: 50%;">
                                                        <label for="firstName">First Name</label>
                                                        <input type="text" name="reservation_first_name" id="firstName"
                                                            placeholder="First Name" required />
                                                    </div>
                                                    <div class="form-group" style="width: 50%;">
                                                        <label for="lastName">Last Name</label>
                                                        <input type="text" name="reservation_last_name" id="lastName"
                                                            placeholder="Last Name" required />
                                                    </div>
                                                </div>
    
                                                <div class="form-row" style="margin-bottom: 1rem;">
                                                    <div class="form-group" style="width: 50%;">
                                                        <label for="phone">Phone Number</label>
                                                        <input type="tel" name="reservation_phone" id="phone"
                                                            placeholder="Phone Number" required />
                                                    </div>
                                                    <div class="form-group" style="width: 50%;">
                                                        <label for="email">Email <span style="font-size: 11px; font-weight: bold;">( You will receive your confirmation here* )</span></label>
                                                        <input type="email" name="reservation_email" id="email"
                                                            placeholder="sample@sample.com" required />
                                                    </div>
                                                </div>
    
                                                <div class="form-row" style="margin-bottom: 1rem;">
                                                    <div class="form-group ddoobb" style="width: 50%;">
                                                        <label for="dob-month">Date of Birth</label>
                                                        <div class="form-row">
                                                            <select id="dob-month" name="reservation_day" class="form-select"
                                                                style="width: 32%; display: inline-block; margin-right: 2%; text-align: center !important; padding-left: 5px !important"
                                                                required></select>
                                                            <select id="dob-day" name="reservation_month" class="form-select"
                                                                style="width: 32%; display: inline-block; margin-right: 2%;"
                                                                required></select>
                                                            <select id="dob-year" name="reservation_year" class="form-select"
                                                                style="width: 32%; display: inline-block;" required></select>
                                                        </div>
                                                    </div>
                                                </div>
    
                                                <div class="form-group" style="margin-bottom: 1rem;">
                                                    <label for="note">Booking Note</label>
                                                    <textarea id="note" name="reservation_description"
                                                        placeholder="Your occasion or special request?"></textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label for="host">Host Name</label>
                                                    <input id="host" name="host_name"
                                                        placeholder="Enter host name">
                                                </div>
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
                                            <div class="guest-section" style="border-color: {{ $data->color }} !important;">
                                                <span class="label">Women</span>
                                                <div class="counter">
                                                    <span class="count" id="womenCount">0</span>
                                                    <button class="btn-gray" type="button"
                                                        onclick="decrements('women')">−</button>
                                                    <button class="btn-yellow"
                                                        style="background-color: {{ $data->color }} !important;" type="button"
                                                        onclick="increments('women')">+</button>
                                                </div>
                                            </div>
                                            <div class="guest-section" style="border-color: {{ $data->color }} !important;">
                                                <span class="label">Men</span>
                                                <div class="counter">
                                                    <span class="count" id="menCount">0</span>
                                                    <button class="btn-gray" type="button"
                                                        onclick="decrements('men')">−</button>
                                                    <button class="btn-yellow"
                                                        style="background-color: {{ $data->color }} !important;" type="button"
                                                        onclick="increments('men')">+</button>
                                                </div>
                                            </div>
                                            <div class="guest-section" style="border-color: {{ $data->color }} !important;">
                                                <span class="label">Total Guests</span>
                                                <div class="counter">
                                                    <span class="count" id="totalCount">0</span>
                                                    <button class="btn-gray" type="button" onclick="resets()">−</button>
                                                    <button class="btn-yellow"
                                                        style="background-color: {{ $data->color }} !important;" type="button"
                                                        onclick="increments('total')">+</button>
                                                </div>
                                            </div>
                                            <input type="hidden" name="men_count" id="men_count" value="0">
                                            <input type="hidden" name="women_count" id="women_count" value="0">
                                        </div>
                                        <div class="col-md-12 mt-4">
                                        </div>
                                        <div class="col-md-12">
                                            <div class="checkbox-container">
                                                <label>
                                                    <input type="checkbox" id="smsConsent_two" required />
                                                    I agree to receive SMS communications from {{ $data->name }} regarding my
                                                    upcoming reservation. Message and data rates may apply. Messaging frequency
                                                    may vary. Reply STOP to opt out at any time. I also agree to receive notifications from the driver.
                                                </label>
                                                <label>
                                                    <input type="checkbox" id="termsConsent_two" required />
                                                    I have read and agreed to the {{ $data->name }} <a target="_blank"
                                                        href="{{ $data->terms }}">Terms of Service</a> and <a
                                                        href="{{ $data->policy }}" target="_blank">Privacy Policy</a>.
                                                </label>
                                            </div>
                                            <button class="submit-btn" type="submit" id="submitBtn_two">Create
                                                Reservation</button>
    
                                        </div>
                                        <div class="col-md-4"></div>
                                    </div>
                                </div>
    
                            </section>
    
                            <section style="width: 100%">
                                <div class="row">
                                    <div class="col-md-12">
                                            <div class="">
                                                <h2 style="margin-top: 4rem;">Location</h2>
                                                <p>{{ $data->location }}</p>
                                                <iframe
                                                    src="https://www.google.com/maps?q={{ urlencode($data->location) }}&output=embed"
                                                    allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                                                </iframe>
                                                <h2 style="margin-top: 2rem;">Contact</h2>
                                                <p><a href="tel:{{ $data->phone }}">{{ $data->phone }}</a></p>
                                                <p><a href="mailto:{{ $data->email }}">{{ $data->email }}</a></p>
                                            </div>
                                        </div>
                                </div>
                            </section>
                            <input type="hidden" name="type" value="guest">
    
                        </form>
                    </div>
                @endif
    
    
                <div class="package" @if ($data->reservation == 1) style="display: none;" @endif>
                    <section class="vip-pack">
                        <div class="">
    
                            <div class="row">
                                <div class="col-md-12">
    
                                    <h2 style="margin-bottom: 35px;">VIP Packages</h2>
    
                                    @php
                                        $packages = \App\Models\Package::where('website_id', $data->id)->where('event_id', null)->get();
                                    @endphp
    
                                    @foreach ($packages as $item)
                                    @if ($item->status == 1)
                                        <div class="vip-card d-flex flex-wrap justify-content-between align-items-center"
                                            style="border-color: {{ $data->color }} !important;">
                                            <div>
                                                <div style="min-width: 210px;">
                                                    <div class="vip-title" style="float: left; width: 125px;">{{ $item->name }} </div>
                                                    <div class="items"
                                                        style="float: right; margin-top: -6px; margin-left: 10px;"
                                                        onClick='openPackageModal()'
                                                        data-description="{!! $item->description !!}"
                                                        data-title="{{ $item->name }}">
                                                        <svg style="fill: {{ $data->secondary_color }}; height: 20px; width: 20px; cursor: pointer;"
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
                                                <br>
                                                <button class="vip-btn btn-{{ $item->id }}" style="background-color: {{ $data->color }} !important;"
                                                    data-id="{{ $item->id }}" data-price="{{ $item->price }}"
                                                    data-gratuity="{{ $data->gratuity_fee }}"
                                                    data-refundable="{{ $data->refundable_fee }}"
                                                    data-sales_tax="{{ $data->sales_tax_fee ?? 10}}"
                                                    data-transportation="{{ $item->transportation }}"
                                                    data-service_charge="{{ $data->service_charge_fee ?? 10}}">Add
                                                    Package</button>
                                            </div>

                                            <div class="d-flex ">
                                                <div class="vip-price me-3 price-{{ $item->id }}" data-price="{{ $item->price }}" style="color: {{ $data->color }} !important">
                                                    ${{ $item->price }}</div>
                                            </div>
                                            <div class="d-flex flex-column align-items-center " style="margin-right: 30px;">
                                                <p>Guests total</p>
                                                <select id="package_number_of_guest" data-multiple="{{ $item->multiple }}" data-id="{{ $item->id }}" style="padding: 5px !important" class="form-select vip-select me-2 gue-1 package_number_of_guestss">
                                                    @for ($i = 1; $i <= $item->number_of_guest; $i++)
                                                        <option value="{{ $i }}">{{ $i }}</option>
                                                    @endfor

                                                </select>

                                            </div>

                                        </div>
                                    @endif
                                    @endforeach
    
                                    <div class="addons" style="display: none;">
                                        <div class="row">
                                            <div class="col-md-6 col-6" style="padding-right: 0px;">
                                                <h5>Add-ons (optional)</h5>
                                            </div>
                                            <div class="col-md-6 col-6" style="text-align: end; padding-left: 0px;">
                                                <h5>Add To Package</h5>
                                            </div>
                                        </div>
                                        <div class="addons-list">
                                            <!-- Addons will be dynamically added here -->
                                        </div>
                                    </div>
    
                                    <div class="row">
                                        <div class="text-start mt-3 col-md-6">
                                            <div style="font-size: 16px;" class="default-price">Package: <span>$0.00</span>
                                            </div>
                                            <div class="dynamic-price" style="display: none;">
                                                <input type="hidden" id="old_price">
                                                <div style="font-size: 16px;" class="default-package-price">Package:
                                                    <span>$0.00</span></div>
                                                <div class="addonns"></div>
    
                                                @if ($data->service_charge_name != 0)
                                                    <div style="font-size: 16px;" class="default-service-charge">
                                                        {{ $data->service_charge_name ?? 'Sales Tax' }}: <span>$0.00</span>
                                                    </div>
                                                @endif
                                                <div class="sales_tax"></div>
                                                @if ($data->sales_tax_name != 0)
                                                    <div style="font-size: 16px;" class="default-sales-tax">
                                                        {{ $data->sales_tax_name ?? 'Sales Tax' }}: <span>$0.00</span></div>
                                                @endif
    
                                                @if ($data->gratuity_name != 0)
                                                    <div style="font-size: 16px;" class="default-gratuity">
                                                        {{ $data->gratuity_name ?? 'Gratuity Fee' }}: <span>$0.00</span></div>
                                                @else
                                                    <div class="default-gratuity"></div>
                                                @endif
    
                                                <div style="font-size: 16px; font-weight: bold; display: none"
                                                    class="default-total">Total: <span>$0.00</span></div>
                                            </div>
    
    
                                            <hr>
                                            <div class="vip-price default-deposit"
                                                style="font-size: 16px; font-weight: 700; color: {{ $data->secondary_color }} !important;">
                                                Total: <span>$0.00</span></div>
                                            @if ($data->refundable_fee > 0)
                                                <div style="font-size: 16px; font-weight: 700; color: {{ $data->secondary_color }} !important;"
                                                    class="vip-price default-refundable">
                                                    {{ $data->refundable_name ?? 'Non Refundable Processing Fees' }}:
                                                    <span>$0.00</span> (Pay Now)</div>
                                                <div style="font-size: 16px; font-weight: 700; color: {{ $data->secondary_color }} !important;"
                                                    class="vip-price default-due">DUE ON ARRIVAL: <span>$0.00</span></div>
                                            @endif
                                            @if ($data->sales_tax_name == 0)
                                                <div style="font-size: 10px; font-weight: 700; color: {{ $data->secondary_color }} !important;"
                                                    class="vip-price"><span>*No sales tax applied. Services sold are not subject to sales tax under Nevada law. Please consult a tax advisor for your local region if applicable.</span></div>
                                            @endif
                                        </div>
                                        <div class="col-md-6 dynamic-price" style="display: none;">
                                            <label
                                                style="color: #808080; font-size: 14px; margin-top: 2rem;">{{ $data->promo_code_name }}</label>
                                            <div class="row">
                                                <div class="col-md-8 col-8" style="padding-right: 0%;">
                                                    <input type="text" id="promo_code" style="color: #fff; border-top-right-radius: 0px !important; border-bottom-right-radius: 0px !important;"
                                                        placeholder="Promo or referral code?" />
                                                </div>
                                                <div class="col-md-4 col-4" style="padding-left: 0%;">
                                                    <button type="button" class="vip-btn-submit"
                                                        style="width: 100%; height: 100%; font-weight: normal; background-color: {{ $data->color }} !important; border-top-right-radius: 10px !important; border-bottom-right-radius: 10px !important;"
                                                        id="applyPromoBtn">Submit</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
    
                                    <!-- Step Progress Indicator -->
                                    <ul class="checkout-steps" id="checkout-steps" style="display: none;">
                                        <li class="step active" id="step-1">
                                            <div class="step-number">1</div>
                                            <p class="step-title">Package Details</p>
                                        </li>
                                        <li class="step" id="step-2">
                                            <div class="step-number">2</div>
                                            <p class="step-title">Transportation</p>
                                        </li>
                                        <li class="step" id="step-3">
                                            <div class="step-number">3</div>
                                            <p class="step-title">Payment</p>
                                        </li>
                                    </ul>

                                    <form action="{{ route('checkout.store', ['slug' => $data->slug]) }}" id="payment-form" method="post">
                                        @csrf
                                        <!-- Step 1: Package Holder Info -->
                                        <section class="checkout-section holder-info dynamic-price mt-4" id="section-1" style="display: none; width: 100%;">
                                            <div class="">
                                                <div class="row">
    
                                                    <div class="col-md-12">
    
                                                        <h2 style="margin-bottom: 35px;">Personal details <span style="font-size: 1rem;"> (Gifting? Enter their legal details here) </span></h2>
    
                                                        <!-- Left: Form Fields -->
                                                        <div class="form-left">
    
                                                            <div class="form-row">
                                                                <div class="form-group" style="width: 50%;">
                                                                    <label for="firstName">First Name</label>
                                                                    <input type="text" id="firstName"
                                                                        name="package_first_name" placeholder="First Name"
                                                                        required />
                                                                </div>
                                                                <div class="form-group" style="width: 50%;">
                                                                    <label for="lastName">Last Name</label>
                                                                    <input type="text" id="lastName"
                                                                        name="package_last_name" placeholder="Last Name"
                                                                        required />
                                                                </div>
                                                            </div>
    
                                                            <div class="form-row">
                                                                <div class="form-group" style="width: 50%;">
                                                                    <label for="phone">Phone Number</label>
                                                                    <input type="tel" id="phone" name="package_phone"
                                                                        placeholder="Phone Number" required />
                                                                </div>
                                                                <div class="form-group" style="width: 50%;">
                                                                    <label for="email">Email</label>
                                                                    <input type="email" id="email" name="package_email"
                                                                        placeholder="sample@sample.com" required />
                                                                </div>
                                                            </div>
    
                                                            <div class="form-row">
                                                                <div class="form-group" style="width: 100%;">
                                                                    <label for="dob-month">Date of Birth</label>
                                                                    <div class="form-row">
                                                                        <select id="package-dob-month" name="package_month"
                                                                            class="form-select"
                                                                            style="width: 32%; display: inline-block; margin-right: 2%;"
                                                                            required></select>
                                                                        <select id="package-dob-day" name="package_day"
                                                                            class="form-select"
                                                                            style="width: 32%; display: inline-block; margin-right: 2%;"
                                                                            required></select>
                                                                        <select id="package-dob-year" name="package_year"
                                                                            class="form-select"
                                                                            style="width: 32%; display: inline-block;"
                                                                            required></select>
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
                                            
                                            <!-- Step Navigation -->
                                            <div class="step-navigation">
                                                <button type="button" class="btn-next" id="next-to-transport">Next: Transportation Details</button>
                                            </div>
                                        </section>
                                        
                                        <!-- Step 2: Transportation -->
                                        <section class="checkout-section transport mt-4" id="section-2" style="display: none; width: 100%;">
                                            
                                            <!-- Transportation confirmation checkbox -->
                                            <div class="checkbox-container transportaiton" id="transport-confirmation" style="display:none">
                                                <label>
                                                    <input type="checkbox" id="transportation_part" />
                                                    {{ $data->transportation_confirmation_text ?? 'I confirm I am not arriving via Uber, Lyft, limo, taxi, ride-sharing or any other paid service. I am arriving in a personal vehicle.' }}
                                                </label>
                                                <div class="step-navigation" style="margin-top: 20px;">
                                                    <button type="button" class="btn-prev" id="prev-to-package">Previous: Package Details</button>
                                                    <button type="button" class="btn-next" id="next-to-payment-from-confirm">Next: Payment Details</button>
                                                </div>
                                            </div>
                                            
                                            <!-- Transportation form -->
                                            <div class="non-transportaiton" id="transport-form" style="display: none;">
                                            <div class="">
                                                <div class="row">
    
                                                    <div class="col-md-12">
    
                                                        <h2 style="margin-bottom: 35px;">Transportation</h2>
    
                                                        <!-- Left: Form Fields -->
                                                        <div class="form-left">
                                                        
                                                            <button type="button" class="same-as-info-transport">Same as package holder information</button>
                                                                
                                                            <div class="from-row ">
                                                                <div class=" trans-group" style="width: 50%; border: none;">
                                                                    <label for="Pick-up-time">Pick-up Time *</label>
                                                                    <br>
                                                                    <br>
                                                                    <input name="transportation_pickup_time" type="text"
                                                                        id="Pick-up-time"
                                                                        class="form-control flatpickr-time"
                                                                        placeholder="Select Time"
                                                                        value="{{ \Carbon\Carbon::now()->format('h:i A') }}"
                                                                        style="width: 100px; height: 30px; color: #fff !important; font-size: 12px;"
                                                                         />
                                                                    <br>
                                                                    <br>
                                                                    <label for="">Pick-up Location</label>
                                                                    <br>
                                                                </div>
                                                            </div>
                                                            <div class="form-row" style="margin-top: 4rem;">
                                                                <div class="form-group" style="width: 100%;">
                                                                    <label for="address">Address</label>
                                                                    <input type="text" name="transportation_address"
                                                                        id="address" placeholder=""  />
                                                                </div>
    
                                                            </div>
    
                                                            <div class="form-row">
                                                                <div class="form-group" style="width: 100%;">
                                                                    <label for="phone">Contact Phone Number or WhatsApp</label>
                                                                    <input type="tel" name="transportation_phone" id="phone"
                                                                        placeholder="For driver/dispatch to coordinate pickup"  />
                                                                </div>
    
                                                            </div>
    
                                                            <div class="form-row">
                                                                <div class="num-guest" style="width: 100%; display: flex;">
                                                                    <label for="">Number of Guest(s)</label>
    
                                                                    <input type="number" class="form-control"
                                                                        name="transportation_guest" value="0"
                                                                        style="width: 50%; color: #fff;"  />
    
    
    
                                                                </div>
                                                            </div>
    
                                                            <div class="form-group">
                                                                <label for="note">Pickup Note</label>
                                                                <textarea name="transportation_note" id="note"
                                                                    placeholder="If any"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Step Navigation -->
                                            <div class="step-navigation">
                                                <button type="button" class="btn-prev" id="prev-to-package-from-form">Previous: Package Details</button>
                                                <button type="button" class="btn-next" id="next-to-payment">Next: Payment Details</button>
                                            </div>
                                            </div>
                                        </section>
    
                                        <input type="hidden" name="addons" id="addons">
    
                                        <input type="hidden" name="package_id" id="package_id">
    
                                        <input type="hidden" name="total" id="subtotal">

                                        <input type="hidden" name="payment_total" class="payment_total">

                                        <input type="hidden" name="website_id" value="{{ $data->id }}">
    
                                        <input type="hidden" name="package_number_of_guest" class="package_number_of_guest" value="1">
    
                                        <!-- Step 3: Payment Information -->
                                        <section class="checkout-section payment-info dynamic-price mt-4" id="section-3" style="display: none;">
                                            <div class="">
                                                <div class="row">
    
                                                    <div class="col-md-12">
                                                        <h2 style="margin-bottom: 35px;">Payment</h2>
    
                                                        <!-- Left: Form Fields -->
                                                        <div class="form-left">
    
                                                            <button type="button" class="same-as-info" style="padding: 1px !important;">Same as package holder information</button>
    
                                                            <div class="form-row">
                                                                <div class="form-group" style="width: 50%;">
                                                                    <label for="firstName">First Name</label>
                                                                    <input name="payment_first_name" type="text"
                                                                        id="firstName" placeholder="" required />
                                                                </div>
                                                                <div class="form-group" style="width: 50%;">
                                                                    <label for="lastName">Last Name</label>
                                                                    <input name="payment_last_name" type="text"
                                                                        id="lastName" placeholder="" required />
                                                                </div>
                                                            </div>
    
                                                            <!-- Hidden fields for phone, email, and DOB - will be auto-populated from package holder info -->
                                                            <input type="hidden" name="payment_phone" id="hidden_payment_phone" />
                                                            <input type="hidden" name="payment_email" id="hidden_payment_email" />
                                                            <input type="hidden" name="payment_month" id="hidden_payment_month" />
                                                            <input type="hidden" name="payment_day" id="hidden_payment_day" />
                                                            <input type="hidden" name="payment_year" id="hidden_payment_year" />
    
                                                            <div class="form-row">
                                                                <div class="form-group" style="width: 100%;">
                                                                    <label for="bill-add">Address</label>
                                                                    <input name="payment_address" type="text" id="bill-add"
                                                                        placeholder="" required />
                                                                </div>
                                                            </div>                                                            <div class="form-row">
                                                                <div class="form-group" style="width: 50%;">
                                                                    <label for="country">Country</label>
                                                                    <select id="country" name="payment_country"
                                                                        class="form-select" required></select>
                                                                </div>
                                                                <div class="form-group" style="width: 50%;">
                                                                    <label for="st-pv">State/ Province</label>
                                                                    <select name="payment_state" id="st-pv"
                                                                        class="form-select" required>
                                                                        <option value="null" selected disabled>Select
                                                                            State/Province</option>
                                                                        <!-- Options will be loaded dynamically -->
                                                                    </select>
                                                                </div>
                                                            </div>
    
                                                            <div class="form-row">
                                                                <div class="form-group" style="width: 50%;">
                                                                    <label for="city">City</label>
                                                                    <input type="text" name="payment_city" id="city"
                                                                        placeholder="" required />
                                                                </div>
                                                                <div class="form-group" style="width: 50%;">
                                                                    <label for="zip">Zip/Postal Code</label>
                                                                    <input type="text" name="payment_zip_code" id="zip"
                                                                        placeholder="" required />
                                                                </div>
                                                            </div>
    
                                                            
                                                            @if ($data->payment_method == 'authorize')
                                                            <div class="form-row">
                                                                <div class="form-group" style="width: 100%;">
                                                                    <!-- Payment method logos start -->
        <div style="margin-bottom: 10px;">
            @forelse($data->paymentLogos as $logo)
                <img src="{{ asset('uploads/' . $logo->logo) }}" alt="{{ $logo->name }}" style="height:32px; margin-right:4px;">
            @empty
                <!-- Default logos if no custom logos are uploaded -->
                <img src="https://img.icons8.com/color/48/000000/visa.png" alt="Visa" style="height:32px; margin-right:4px;">
                <img src="https://img.icons8.com/color/48/000000/mastercard-logo.png" alt="Mastercard" style="height:32px; margin-right:4px;">
                <img src="https://img.icons8.com/color/48/000000/amex.png" alt="Amex" style="height:32px; margin-right:4px;">
                <img src="https://img.icons8.com/color/48/000000/google-pay-india.png" alt="Google Pay" style="height:32px; margin-right:4px;">
                <img src="https://img.icons8.com/color/48/000000/apple-pay.png" alt="Apple Pay" style="height:32px;">
            @endforelse
        </div>
                                                                    <label for="card_number">Card Number</label>
                                                                    <input type="tel" name="card_number" id="card_number"
                                                                        placeholder="" required />
                                                                </div>
    
                                                            </div>
                                                            <div class="form-row">
                                                                    <div class="form-group" style="width: 25%;">
                                                                        <label>Month</label>
                                                                        <input type="tel" maxlength="2" name="card_month"
                                                                            id="city" placeholder="(MM)" required />
                                                                    </div>
                                                                    <div class="form-group" style="width: 25%;">
                                                                        <label>Year</label>
                                                                        <input type="tel" maxlength="2" name="card_year"
                                                                            placeholder="(YY)" required />
                                                                    </div>
                                                                    <div class="form-group" style="width: 25%;">
                                                                        <label>CVV</label>
                                                                        <input type="tel" name="card_cvv" id="cvv"
                                                                            placeholder="CVV" required />
                                                                    </div>
                                                                @else
                                                                <div class="form-row">
                                                                    @forelse($data->paymentLogos as $logo)
                                                                        <img src="{{ asset('uploads/' . $logo->logo) }}" alt="{{ $logo->name }}" style="height:32px; margin-right:4px;">
                                                                    @empty
                                                                        <!-- Default logos if no custom logos are uploaded -->
                                                                        <img src="https://img.icons8.com/color/48/000000/visa.png" alt="Visa" style="height:32px; margin-right:4px;">
                                                                        <img src="https://img.icons8.com/color/48/000000/mastercard-logo.png" alt="Mastercard" style="height:32px; margin-right:4px;">
                                                                        <img src="https://img.icons8.com/color/48/000000/amex.png" alt="Amex" style="height:32px; margin-right:4px;">
                                                                        <img src="https://img.icons8.com/color/48/000000/google-pay-india.png" alt="Google Pay" style="height:32px; margin-right:4px;">
                                                                        <img src="https://img.icons8.com/color/48/000000/apple-pay.png" alt="Apple Pay" style="height:32px;">
                                                                    @endforelse
                                                                </div>
                                                                    <div style="margin-bottom: 10px;">
                                                                    <div class="form-group" style="width: 100%;" id="card_number">
                                                                        <label for="card_number">Card Number</label>
                                                                        {{-- <input type="tel" name="card_number" 
                                                                            placeholder="" required /> --}}
                                                                    </div>
        
                                                                </div>
                                                                <div class="form-row">
                                                                    <div class="form-group" style="width: 50%;" id="expiration_date">
                                                                            <label>Expiry Date</label>
                                                                            {{-- <input type="text"  name="expiration_date"
                                                                                 placeholder="MM/YY" required /> --}}
                                                                    </div>
                                                                    <div class="form-group" style="width: 50%;" id="cvv">
                                                                        <label>CVV</label>
                                                                        {{-- <input type="tel" name="card_cvv" 
                                                                            placeholder="CVV" required /> --}}
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <div class="checkbox-container" style="margin-top: 1.5rem;">
    <label>
        <input type="checkbox" id="businessExpenseCheckbox" />
        This purchase is for business purposes
    </label>
</div>
<div id="businessFields" style="display: none; margin-top: 1rem;">
    <div class="form-row">
        <div class="form-group" style="width: 50%;">
            <label for="business_company">Company Name</label>
            <input type="text" name="business_company" id="business_company" placeholder="Company Name" />
        </div>
        <div class="form-group" style="width: 50%;">
            <label for="business_vat">VAT or Tax ID</label>
            <input type="text" name="business_vat" id="business_vat" placeholder="VAT or Tax ID" />
        </div>
    </div>
    <div class="form-row">
        <div class="form-group" style="width: 100%;">
            <label for="business_address">Business Address</label>
            <input type="text" name="business_address" id="business_address" placeholder="Business Address" />
        </div>
    </div>
    <div class="form-row">
        <div class="form-group" style="width: 100%;">
            <label for="business_purpose">Purpose of Purchase</label>
            <input type="text" name="business_purpose" id="business_purpose" placeholder="e.g. team event, client entertainment" />
        </div>
    </div>
</div>
    
                                                            <div class="checkbox-container">
                                                                <label>
                                                                    <input type="checkbox" id="smsConsent" required />
                                                                    I agree to receive SMS communications from
                                                                    {{ $data->name }}
                                                                    regarding my upcoming
                                                                    reservation. Message and data rates may apply. Messaging
                                                                    frequency may vary. Reply
                                                                    STOP to opt out at any time. I also agree to receive notifications from the driver.
                                                                </label>
    
                                                                <label>
                                                                    <input type="checkbox" id="termsConsent" required />
                                                                    I have read and agreed to the {{ $data->name }} <a
                                                                        target="_blank" href="{{ $data->terms }}">Terms of
                                                                        Service</a> and <a target="_blank"
                                                                        href="{{ $data->privacy }}">Privacy
                                                                        Policy</a>.
                                                                </label>
                                                            </div>

                                                            <input type="hidden" class="package_use_date" name="package_use_date" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                                                            <input type="hidden" class="promo_code" name="promo_code">
                                                            <input type="hidden" class="discounted_amount" name="discounted_amount">
                                                            
                                                            <!-- Step Navigation -->
                                                            <div class="step-navigation">
                                                                <button type="button" class="btn-prev" id="prev-to-transport">Previous: Transportation</button>
                                                                <button class="submit-btn" id="submitBtn" type="submit">Complete Purchase</button>
                                                            </div>
    
                                                        </div>
    
                                                    </div>
    
    
    
    
    
    
                                                </div>
                                            </div>
                                        </section>
                                    </form>
    
                                </div>
                            </div>
                        </div>
    
    
                    </section>
    
                    <input type="hidden" name="type" value="package">
    
                    <section>
                    <div class="row">
                        <div class="col-md-12">
                                    <div class="">
                                        <h2 style="margin-top: 4rem;">Location</h2>
                                        <p>{{ $data->location }}</p>
                                        <iframe
                                            src="https://www.google.com/maps?q={{ urlencode($data->location) }}&output=embed"
                                            allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                                        </iframe>
                                        <h2 style="margin-top: 2rem;">Contact</h2>
                                        <p><a href="">{{ $data->phone }}</a></p>
                                        <p><a href="">{{ $data->email }}</a></p>
                                    </div>
                                </div>
                    </div>
                </section>
                </div>
    
    
    
    
    
                <section>
                    <div class="container py-5">
                        <div class="event-header">
                            <h2>Upcoming Events</h2>
                            <div class="event-filters" style="display: flex; gap: 10px;">
                                <button type="button" class="btn btn-outline-primary event-filter" data-filter="week"
                                    style="border-color: {{ $data->color }} !important; color: {{ $data->color }}; font-size: 14px; padding: 5px;">This
                                    Week</button>
                                <button type="button" class="btn btn-outline-primary event-filter" data-filter="month"
                                    style="border-color: {{ $data->color }} !important; color: {{ $data->color }}; font-size: 14px; padding: 5px;">This
                                    Month</button>
                                <button type="button" class="btn btn-outline-primary event-filter" data-filter="year"
                                    style="border-color: {{ $data->color }} !important; color: {{ $data->color }}; font-size: 14px; padding: 5px;">This
                                    Year</button>
                            </div>
                        </div>
                        <div class="row g-4" id="events-list">
                            @foreach ($data->events as $item)
                                @if (\Carbon\Carbon::parse($item->date)->gt(\Carbon\CArbon::now()))
                                    <div class="col-md-4 event-card-item"
                                        data-date="{{ \Carbon\Carbon::parse($item->date)->format('Y-m-d') }}">
                                        <a href="/{{ $data->slug }}?event_name={{ $item->name }}" class="event-card"
                                            style="width: 100%; background: transparent; text-decoration: none;">
                                            <div class="card p-3 text-center">
                                                <img src="{{ asset('uploads/' . $item->image) }}" width="100%" height="298px"
                                                    style="width: 100%; height: 298px;">
                                                <div class="d-flex">
                                                    <div class="event-day" style="width: 50%;">
                                                        {{ \Carbon\Carbon::parse($item->date)->format('l') }}</div>
                                                    <div class="event-dates"
                                                        style="width: 50%; color: {{ $data->color }} !important;">
                                                        {{ \Carbon\Carbon::parse($item->date)->format('M') }}<span> <br>
                                                            {{ \Carbon\Carbon::parse($item->date)->format('d') }}</span></div>
                                                </div>
                                                <div class="event-location">{{ $data->location }}</div>
                                                <div class="event-location">Book Guestlist</div>
                                            </div>
                                        </a>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
    
    
                </section>
    
                <div class="modal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" style="color: #000 !important;">Modal title</h5>
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
            </div>


        </main>
        <footer>
            <p>{{$data->footer_text}}</p>
        </footer>
        <script src="scripts/main.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

        <script>
            $('#businessExpenseCheckbox').on('change', function () {
                if ($(this).is(':checked')) {
                    $('#businessFields').slideDown();
                } else {
                    $('#businessFields').slideUp();
                }
            });
        </script>

        <script>
            $(function () {
                function isThisWeek(date) {
                    const now = new Date();
                    const input = new Date(date);
                    const first = now.getDate() - now.getDay();
                    const last = first + 6;
                    const weekStart = new Date(now.setDate(first));
                    weekStart.setHours(0, 0, 0, 0);
                    const weekEnd = new Date(now.setDate(last));
                    weekEnd.setHours(23, 59, 59, 999);
                    return input >= weekStart && input <= weekEnd;
                }
                function isThisMonth(date) {
                    const now = new Date();
                    const input = new Date(date);
                    return input.getMonth() === now.getMonth() && input.getFullYear() === now.getFullYear();
                }
                function isThisYear(date) {
                    const now = new Date();
                    const input = new Date(date);
                    return input.getFullYear() === now.getFullYear();
                }
                $('.event-filter').on('click', function () {
                    const filter = $(this).data('filter');
                    $('.event-filter').removeClass('active');
                    $(this).addClass('active');
                    $('#events-list .event-card-item').each(function () {
                        const date = $(this).data('date');
                        let show = false;
                        if (filter === 'week') show = isThisWeek(date);
                        if (filter === 'month') show = isThisMonth(date);
                        if (filter === 'year') show = isThisYear(date);
                        $(this).toggle(show);
                    });
                });
                // Optionally, trigger default filter (e.g., show all or this week)
                $('.event-filter[data-filter="year"]').trigger('click');
            });
        </script>

        <script>

            // Auto-populate hidden payment fields when moving to payment step
            function populatePaymentFields() {
                $('#hidden_payment_phone').val($('input[name="package_phone"]').val());
                $('#hidden_payment_email').val($('input[name="package_email"]').val());
                $('#hidden_payment_month').val($('select[name="package_month"]').val());
                $('#hidden_payment_day').val($('select[name="package_day"]').val());
                $('#hidden_payment_year').val($('select[name="package_year"]').val());
            }
            
            // Copy package holder info to payment info (for visible fields only)
            $(document).on('click', '.same-as-info', function () {
                // Text fields - only copy visible fields now
                $("input[name='payment_first_name']").val($("input[name='package_first_name']").val());
                $("input[name='payment_last_name']").val($("input[name='package_last_name']").val());
                // Hidden fields are auto-populated when moving to payment step
                populatePaymentFields();
            });
            
            // Copy package holder info to transportation info
            $(document).on('click', '.same-as-info-transport', function () {
                $('input[name="transportation_phone"]').val($('input[name="package_phone"]').val());
            });
            // Populate country select
            function populateCountrySelect(selectId) {
                const countries = [
                    'United States', 'Canada', 'United Kingdom', 'Australia', 'Germany', 'France', 'Italy', 'Spain', 'Netherlands', 'Brazil', 'India', 'China', 'Japan', 'South Korea', 'Mexico', 'Russia', 'South Africa', 'New Zealand', 'Sweden', 'Norway', 'Denmark', 'Finland', 'Ireland', 'Switzerland', 'Austria', 'Belgium', 'Portugal', 'Poland', 'Turkey', 'Argentina', 'Chile', 'Colombia', 'Czech Republic', 'Greece', 'Hungary', 'Iceland', 'Indonesia', 'Israel', 'Malaysia', 'Philippines', 'Saudi Arabia', 'Singapore', 'Slovakia', 'Thailand', 'Ukraine', 'United Arab Emirates', 'Vietnam', 'Egypt', 'Morocco', 'Nigeria', 'Pakistan', 'Romania', 'Serbia', 'Croatia', 'Slovenia', 'Bulgaria', 'Estonia', 'Latvia', 'Lithuania', 'Luxembourg', 'Malta', 'Monaco', 'Montenegro', 'Qatar', 'Kuwait', 'Oman', 'Bahrain', 'Jordan', 'Lebanon', 'Cyprus', 'Georgia', 'Kazakhstan', 'Uzbekistan', 'Bangladesh', 'Sri Lanka', 'Nepal', 'Cambodia', 'Laos', 'Myanmar', 'Mongolia', 'Afghanistan', 'Albania', 'Armenia', 'Azerbaijan', 'Belarus', 'Bosnia and Herzegovina', 'Botswana', 'Brunei', 'Burkina Faso', 'Burundi', 'Cameroon', 'Cape Verde', 'Central African Republic', 'Chad', 'Comoros', 'Congo', 'Costa Rica', 'Cuba', 'Djibouti', 'Dominica', 'Dominican Republic', 'Ecuador', 'El Salvador', 'Equatorial Guinea', 'Eritrea', 'Eswatini', 'Ethiopia', 'Fiji', 'Gabon', 'Gambia', 'Ghana', 'Grenada', 'Guatemala', 'Guinea', 'Guinea-Bissau', 'Guyana', 'Haiti', 'Honduras', 'Jamaica', 'Kenya', 'Kiribati', 'Lesotho', 'Liberia', 'Libya', 'Liechtenstein', 'Madagascar', 'Malawi', 'Maldives', 'Mali', 'Marshall Islands', 'Mauritania', 'Mauritius', 'Micronesia', 'Moldova', 'Mozambique', 'Namibia', 'Nauru', 'Nicaragua', 'Niger', 'North Korea', 'North Macedonia', 'Palau', 'Palestine', 'Panama', 'Papua New Guinea', 'Paraguay', 'Peru', 'Rwanda', 'Saint Kitts and Nevis', 'Saint Lucia', 'Saint Vincent and the Grenadines', 'Samoa', 'San Marino', 'Sao Tome and Principe', 'Senegal', 'Seychelles', 'Sierra Leone', 'Solomon Islands', 'Somalia', 'South Sudan', 'Sudan', 'Suriname', 'Syria', 'Tajikistan', 'Tanzania', 'Timor-Leste', 'Togo', 'Tonga', 'Trinidad and Tobago', 'Tunisia', 'Turkmenistan', 'Tuvalu', 'Uganda', 'Uruguay', 'Vanuatu', 'Vatican City', 'Venezuela', 'Yemen', 'Zambia', 'Zimbabwe'
                ];
                const select = document.getElementById(selectId);
                select.innerHTML = '<option value="">Select Country</option>';
                countries.forEach(function (country) {
                    select.innerHTML += `<option value="${country}">${country}</option>`;
                });
            }

            function populateCountrySelect2(selectId) {
                const countries = [
                    'United States', 'Canada', 'United Kingdom', 'Australia', 'Germany', 'France', 'Italy', 'Spain', 'Netherlands', 'Brazil', 'India', 'China', 'Japan', 'South Korea', 'Mexico', 'Russia', 'South Africa', 'New Zealand', 'Sweden', 'Norway', 'Denmark', 'Finland', 'Ireland', 'Switzerland', 'Austria', 'Belgium', 'Portugal', 'Poland', 'Turkey', 'Argentina', 'Chile', 'Colombia', 'Czech Republic', 'Greece', 'Hungary', 'Iceland', 'Indonesia', 'Israel', 'Malaysia', 'Philippines', 'Saudi Arabia', 'Singapore', 'Slovakia', 'Thailand', 'Ukraine', 'United Arab Emirates', 'Vietnam', 'Egypt', 'Morocco', 'Nigeria', 'Pakistan', 'Romania', 'Serbia', 'Croatia', 'Slovenia', 'Bulgaria', 'Estonia', 'Latvia', 'Lithuania', 'Luxembourg', 'Malta', 'Monaco', 'Montenegro', 'Qatar', 'Kuwait', 'Oman', 'Bahrain', 'Jordan', 'Lebanon', 'Cyprus', 'Georgia', 'Kazakhstan', 'Uzbekistan', 'Bangladesh', 'Sri Lanka', 'Nepal', 'Cambodia', 'Laos', 'Myanmar', 'Mongolia', 'Afghanistan', 'Albania', 'Armenia', 'Azerbaijan', 'Belarus', 'Bosnia and Herzegovina', 'Botswana', 'Brunei', 'Burkina Faso', 'Burundi', 'Cameroon', 'Cape Verde', 'Central African Republic', 'Chad', 'Comoros', 'Congo', 'Costa Rica', 'Cuba', 'Djibouti', 'Dominica', 'Dominican Republic', 'Ecuador', 'El Salvador', 'Equatorial Guinea', 'Eritrea', 'Eswatini', 'Ethiopia', 'Fiji', 'Gabon', 'Gambia', 'Ghana', 'Grenada', 'Guatemala', 'Guinea', 'Guinea-Bissau', 'Guyana', 'Haiti', 'Honduras', 'Jamaica', 'Kenya', 'Kiribati', 'Lesotho', 'Liberia', 'Libya', 'Liechtenstein', 'Madagascar', 'Malawi', 'Maldives', 'Mali', 'Marshall Islands', 'Mauritania', 'Mauritius', 'Micronesia', 'Moldova', 'Mozambique', 'Namibia', 'Nauru', 'Nicaragua', 'Niger', 'North Korea', 'North Macedonia', 'Palau', 'Palestine', 'Panama', 'Papua New Guinea', 'Paraguay', 'Peru', 'Rwanda', 'Saint Kitts and Nevis', 'Saint Lucia', 'Saint Vincent and the Grenadines', 'Samoa', 'San Marino', 'Sao Tome and Principe', 'Senegal', 'Seychelles', 'Sierra Leone', 'Solomon Islands', 'Somalia', 'South Sudan', 'Sudan', 'Suriname', 'Syria', 'Tajikistan', 'Tanzania', 'Timor-Leste', 'Togo', 'Tonga', 'Trinidad and Tobago', 'Tunisia', 'Turkmenistan', 'Tuvalu', 'Uganda', 'Uruguay', 'Vanuatu', 'Vatican City', 'Venezuela', 'Yemen', 'Zambia', 'Zimbabwe'
                ];
                const select = document.getElementById(selectId);
                select.innerHTML = '<option value="">Select Country</option>';
                countries.forEach(function (country) {
                    select.innerHTML += `<option value="${country}">${country}</option>`;
                });
            }
            
            // Function to force Safari/iOS select styling after JavaScript population
            function forceSafariSelectStyling() {
                // Target all select fields that are JavaScript-generated
                const selectIds = ['country', 'country2', 'st-pv', 'dob-month', 'dob-day', 'dob-year', 
                                 'package-dob-month', 'package-dob-day', 'package-dob-year',
                                 'payment-dob-month', 'payment-dob-day', 'payment-dob-year',
                                 'payment-dob-month2', 'payment-dob-day2', 'payment-dob-year2'];
                
                selectIds.forEach(function(id) {
                    const element = document.getElementById(id);
                    if (element) {
                        // Force re-apply CSS styles for Safari/iOS
                        element.style.setProperty('-webkit-appearance', 'none', 'important');
                        element.style.setProperty('-moz-appearance', 'none', 'important');
                        element.style.setProperty('appearance', 'none', 'important');
                        element.style.setProperty('background-color', 'transparent', 'important');
                        element.style.setProperty('background-image', 'url("data:image/svg+xml;charset=UTF-8,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'white\'><path d=\'M7 10l5 5 5-5z\'/></svg>")', 'important');
                        element.style.setProperty('background-repeat', 'no-repeat', 'important');
                        element.style.setProperty('background-position', 'right 15px center', 'important');
                        element.style.setProperty('background-size', '20px', 'important');
                        element.style.setProperty('padding', '12px 45px 12px 15px', 'important');
                        element.style.setProperty('border', '1px solid #9797a0', 'important');
                        element.style.setProperty('border-radius', '10px', 'important');
                        element.style.setProperty('color', '#fff', 'important');
                        element.style.setProperty('font-size', '16px', 'important');
                        element.style.setProperty('min-height', '45px', 'important');
                        element.style.setProperty('line-height', '1.5', 'important');
                        
                        // Special handling for DOB fields (smaller arrows)
                        if (id.includes('dob')) {
                            element.style.setProperty('padding', '12px 30px 12px 15px', 'important');
                            element.style.setProperty('background-size', '15px', 'important');
                            element.style.setProperty('background-position', 'right 10px center', 'important');
                            element.style.setProperty('text-align', 'center', 'important');
                        }
                    }
                });
            }
            
            // On DOM ready, also populate country select
            $(function () {
                populateCountrySelect('country');
                populateCountrySelect2('country2');
                
                // Apply styling after population with a slight delay for Safari
                setTimeout(function() {
                    forceSafariSelectStyling();
                }, 100);
            });
            // Populate DOB selects for all three sections
            function populateDOBSelects(monthId, dayId, yearId) {
                const monthSelect = document.getElementById(monthId);
                const daySelect = document.getElementById(dayId);
                const yearSelect = document.getElementById(yearId);
                
                // Check if elements exist before trying to populate them
                if (!monthSelect || !daySelect || !yearSelect) {
                    return; // Elements don't exist, skip population
                }
                
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
            $(function () {
                populateDOBSelects('dob-month', 'dob-day', 'dob-year');
                populateDOBSelects('package-dob-month', 'package-dob-day', 'package-dob-year');
                populateDOBSelects('payment-dob-month', 'payment-dob-day', 'payment-dob-year');
                populateDOBSelects('payment-dob-month2', 'payment-dob-day2', 'payment-dob-year2');
                
                // Apply styling after population with a slight delay for Safari
                setTimeout(function() {
                    forceSafariSelectStyling();
                }, 100);
            });


            $(document).ready(function () {
                $('.vip-btn').on('click', function () {
                    $('.vip-btn').not(this).text('Add Package');
                    $(this).text('Added');
                    let price = parseFloat($(this).data('price'));
                    let gratuity = parseFloat($(this).data('gratuity'));
                    let refundable = parseFloat($(this).data('refundable'));
                    let sales_tax = parseFloat($('#sales_tax').val() || 0);
                    let service_charge = parseFloat($('#service_charge').val() || 0);
                    let id = $(this).data('id');

                    let transportation = $(this).data('transportation');

                    let service_charge_price = 0;
                    let sales_tax_price = 0;
                    let gratuited_price = 0;

                    if ("{{ $data->service_charge }}" != "0") {
                        service_charge_price = (price / 100) * service_charge;
                    } else {
                        service_charge_price = 0;
                    }

                    if ("{{ $data->sales_tax_name }}" != "0") {
                        sales_tax_price = (price / 100) * sales_tax;
                    } else {
                        sales_tax_price = 0;
                    }

                    if ("{{ $data->gratuity_name }}" != "0") {
                        gratuited_price = ((price + sales_tax_price + service_charge_price) / 100) * gratuity;
                    } else {
                        gratuity = 0;
                    }

                    let refundable_price = ((price + sales_tax_price + service_charge_price + gratuited_price) / 100) * refundable;

                    $.ajax({
                        url: "/{{ $data->slug }}/addons/" + id,
                        type: 'GET',
                        dataType: 'json',
                        success: function (res) {
                            let htmls = ``;
                            res.forEach(function (addon) {
                                let html = `<div class=\"addon-item\" style=\"height: 25px;\">\n                                    <div style=\"float: left;\">\n                                        <label for=\"${addon.id}\" data-title=\"${addon.name}\" data-description=\"${addon.description}\" style=\"float: left; cursor: pointer;\">${addon.name} ($${addon.price})</label>\n                                        <div onClick=\"openModal()\" style=\"float: right; margin-left: 10px;\">\n                                            <svg style=\"fill: {{ $data->secondary_color }}; height: 20px; width: 20px; cursor: pointer;\" version=\"1.1\" xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" x=\"0px\" y=\"0px\" viewBox=\"0 0 512 512\" xml:space=\"preserve\">\n                                                <g>\n                                                    <path d=\"M256,0C115.39,0,0,115.39,0,256s115.39,256,256,256s256-115.39,256-256S396.61,0,256,0z M286,376\n                                                    c0,16.538-13.462,30-30,30c-16.538,0-30-13.462-30-30V226c0-16.538,13.462-30,30-30c16.538,0,30,13.462,30,30V376z M256,166\n                                                    c-16.538,0-30-13.462-30-30c0-16.538,13.462-30,30-30c16.538,0,30,13.462,30,30C286,152.538,272.538,166,256,166z\"></path>\n                                                </g>\n                                            </svg>\n                                        </div>\n                                    </div>\n                                    <input style=\"float: right; margin-top: 3px;\" type=\"checkbox\" class=\"termsConsent\" onClick=\"addToTotal(${addon.price},'${addon.name}',${addon.id})\" data-price=\"${addon.price}\" id=\"${addon.id}\" />\n                                </div>`;
                                htmls += html;
                            });
                            $('.addons-list').empty();
                            $('.addons-list').html(htmls);
                            $('.addons').show();
                            $('#package_id').val(id);
                        }
                    });

                    $('.default-package-price span').text('$' + price.toFixed(2));
                    $('.default-gratuity span').text('$' + gratuited_price.toFixed(2));
                    $('.default-refundable span').text('$' + refundable_price.toFixed(2));
                    if ("{{ $data->sales_tax_name }}" != "0") {
                        if ($('.default-sales-tax').length === 0) {
                            $('.sales_tax').after('<div style="font-size: 12px;" class="default-sales-tax">Sales Tax: <span>$0.00</span></div>');
                        }
                    } else {
                        $('.default-sales-tax').remove();

                    }
                    $('.default-sales-tax span').text('$' + sales_tax_price.toFixed(2));
                    $('.default-service-charge span').text('$' + service_charge_price.toFixed(2));

                    let total = price + gratuited_price + sales_tax_price + service_charge_price;
                    $('.default-total span').text('$' + total.toFixed(2));
                    $('.payment_total').val(total.toFixed(2));
                    $('.default-deposit span').text('$' + total.toFixed(2));
                    $('.default-due span').text('$' + (total - refundable_price).toFixed(2));
                    if (refundable_price > 0) {
                        $('#subtotal').val(refundable_price.toFixed(2));
                    } else {
                        $('#subtotal').val(total.toFixed(2));

                    }

                    $('.dynamic-price').show();
                    $('.default-price').hide();
                    
                    // Show step indicators and first step
                    $('#checkout-steps').show();
                    showStep(1);

                    // Store transportation requirement globally
                    window.requiresTransportation = transportation == 1;
                    
                    // Update step 2 based on transportation requirement
                    if (window.requiresTransportation) {
                        $('#step-2 .step-title').text('Transportation');
                        $('#next-to-transport').text('Next: Transportation Details');
                    } else {
                        $('#step-2 .step-title').text('Confirmation');  
                        $('#next-to-transport').text('Next: Transportation Confirmation');
                    }
                });
            });
            
            // Step Management Functions
            let currentStep = 1;
            
            function showStep(stepNumber) {
                // Hide all sections
                $('.checkout-section').removeClass('active').hide();
                
                // Show target section
                $('#section-' + stepNumber).addClass('active').show();
                
                // Update step indicators
                $('.step').removeClass('active completed');
                for (let i = 1; i < stepNumber; i++) {
                    $('#step-' + i).addClass('completed');
                }
                $('#step-' + stepNumber).addClass('active');
                
                currentStep = stepNumber;
                
                // Handle transportation logic for step 2
                if (stepNumber === 2) {
                    if (window.requiresTransportation) {
                        $('#transport-form').show();
                        $('#transport-confirmation').hide();
                    } else {
                        $('#transport-form').hide();
                        $('#transport-confirmation').show();
                    }
                }
            }
            
            function validateStep(stepNumber) {
                let isValid = true;
                const requiredFields = [];
                
                if (stepNumber === 1) {
                    // Validate package holder info
                    requiredFields.push(
                        '[name="package_first_name"]',
                        '[name="package_last_name"]',
                        '[name="package_phone"]',
                        '[name="package_email"]',
                        '[name="package_month"]',
                        '[name="package_day"]',
                        '[name="package_year"]'
                    );
                } else if (stepNumber === 2 && window.requiresTransportation) {
                    // Validate transportation form
                    requiredFields.push(
                        '[name="transportation_pickup_time"]',
                        '[name="transportation_address"]',
                        '[name="transportation_phone"]'
                    );
                } else if (stepNumber === 2 && !window.requiresTransportation) {
                    // Validate transportation confirmation checkbox
                    if (!$('#transportation_part').is(':checked')) {
                        alert('Please confirm your transportation arrangement.');
                        return false;
                    }
                }
                
                // Check required fields
                requiredFields.forEach(function(selector) {
                    const field = $(selector);
                    if (!field.val() || field.val().trim() === '') {
                        field.addClass('required-field');
                        isValid = false;
                    } else {
                        field.removeClass('required-field');
                    }
                });
                
                if (!isValid && stepNumber !== 2) {
                    alert('Please fill in all required fields.');
                }
                
                return isValid;
            }
            
            // Navigation Event Handlers
            $(document).ready(function() {
                
                // Next to Transportation
                $('#next-to-transport').click(function() {
                    if (validateStep(1)) {
                        showStep(2);
                    }
                });
                
                // Previous to Package from Transportation confirmation
                $('#prev-to-package').click(function() {
                    showStep(1);
                });
                
                // Previous to Package from Transportation form  
                $('#prev-to-package-from-form').click(function() {
                    showStep(1);
                });
                
                // Next to Payment from Transportation confirmation
                $('#next-to-payment-from-confirm').click(function() {
                    if (validateStep(2)) {
                        populatePaymentFields();
                        showStep(3);
                    }
                });
                
                // Next to Payment from Transportation form
                $('#next-to-payment').click(function() {
                    if (validateStep(2)) {
                        populatePaymentFields();
                        showStep(3);
                    }
                });
                
                // Previous to Transportation from Payment
                $('#prev-to-transport').click(function() {
                    showStep(2);
                });
                
                // Remove required field styling on input
                $(document).on('input change', 'input, select, textarea', function() {
                    $(this).removeClass('required-field');
                });
            });
            
        </script>

        <input type="hidden" id="gratuity" value="{{ $data->gratuity_fee }}">

        <input type="hidden" id="refundable" value="{{ $data->refundable_fee }}">

        <input type="hidden" id="sales_tax" value="{{ $data->sales_tax_fee ?? 10}}">

        <input type="hidden" id="service_charge" value="{{ $data->service_charge_fee ?? 10}}">

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
                // Gather all selected addon prices
                let addonTotal = 0;
                let selectedAddons = [];
                $('.termsConsent:checked').each(function () {
                    let addonPrice = parseFloat($(this).data('price'));
                    addonTotal += addonPrice;
                    selectedAddons.push($(this).attr('id'));
                });

                // Update addon display
                $('.addonns').empty();
                $('.termsConsent:checked').each(function () {
                    let addonName = $(this).closest('.addon-item').find('label').text().split(' ($')[0];
                    let addonPrice = parseFloat($(this).data('price'));
                    $('.addonns').append(`<div style="font-size: 12px;" class="default-refundabless">${addonName}: <span>$${addonPrice.toFixed(2)}</span></div>`);
                });
                $('#addons').val(selectedAddons.join(','));

                // Get base package price
                let baseTotal = parseFloat($('.default-package-price span').text().replace('$', '')) || 0;
                let total = baseTotal + addonTotal;

                // Recalculate fees
                let gratuity = parseFloat($('#gratuity').val()) || 0;
                let refundable = parseFloat($('#refundable').val()) || 0;
                let sales_tax = parseFloat($('#sales_tax').val()) || 0;
                let service_charge = parseFloat($('#service_charge').val()) || 0;

                let service_charge_price = ("{{ $data->service_charge_name }}" != "0") ? (total / 100) * service_charge : 0;
                let sales_tax_price = ("{{ $data->sales_tax_name }}" != "0") ? (total / 100) * sales_tax : 0;
                let gratuited_price = ("{{ $data->gratuity_name }}" != "0") ? ((total + sales_tax_price + service_charge_price) / 100) * gratuity : 0;
                let refundable_price = ((total + sales_tax_price + service_charge_price + gratuited_price) / 100) * refundable;

                // Update fee displays
                $('.default-gratuity span').text('$' + gratuited_price.toFixed(2));
                $('.default-refundable span').text('$' + refundable_price.toFixed(2));
                if ("{{ $data->sales_tax_name }}" != "0") {
                    if ($('.default-sales-tax').length === 0) {
                        $('.sales_tax').after('<div style="font-size: 12px;" class="default-sales-tax">Sales Tax: <span>$0.00</span></div>');
                    }
                } else {
                    $('.default-sales-tax').remove();
                }
                $('.default-sales-tax span').text('$' + sales_tax_price.toFixed(2));
                $('.default-service-charge span').text('$' + service_charge_price.toFixed(2));

                // Add new fees to total
                let grandTotal = total + gratuited_price + sales_tax_price + service_charge_price;
                $('.default-total span').text('$' + grandTotal.toFixed(2));
                $('.payment_total').val(grandTotal.toFixed(2));
                if (refundable_price > 0) {
                    $('#subtotal').val(refundable_price.toFixed(2));
                } else {
                    $('#subtotal').val(grandTotal.toFixed(2));
                }
                console.log(grandTotal);
                $('.default-deposit span').text('$' + grandTotal.toFixed(2));
                $('.default-due span').text('$' + (grandTotal - refundable_price).toFixed(2));
                recalculateTotals();
            }

            function transportation(){
                console.log('sss');
                if (event.target.checked) {
                    $('.transport').show();
                }else{
                    $('.transport').hide();
                }
            }
        </script>

        <script>
            $('.package_number_of_guestss').on('change', function () {
                var selectedValue = $(this).val();
                $('.package_number_of_guest').val(selectedValue);

                idd = $(this).data('id');
                multiple = $(this).data('multiple');

                // console.log(multiple);

                if (multiple == 1) {
                    price = $('.price-'+idd).data('price');

                    $('#old_price').val(parseFloat($('.default-package-price span').text().replace('$', '')) || 0);

                    price = price * selectedValue;

                    $('.price-'+idd).text('$'+price);

                    $('.btn-'+idd).attr('data-price', price);

                    $('.default-package-price span').text('$'+price);

                    total = price;

                    // total -+ $('#old_price').val();

                    // total +=  parseFloat($('.default-package-price span').text().replace('$', '')) || 0;

                    let service_charge_price = 0;
                    let sales_tax_price = 0;
                    let gratuited_price = 0;

                    let gratuity = parseFloat($('#gratuity').val()) || 0;
                    let sales_tax = parseFloat($('#sales_tax').val()) || 0;
                    let refundable = parseFloat($('#refundable').val()) || 0;
                    let service_charge = parseFloat($('#service_charge').val()) || 0;

                    if ("{{ $data->sales_tax_name }}" != "0") {
                        sales_tax_price = (parseFloat(total) / 100) * sales_tax;
                    } else {
                        sales_tax_price = 0;
                    }

                    if ("{{ $data->service_charge_name }}" != "0") {
                        service_charge_price = (parseFloat(total) / 100) * service_charge;
                    } else {
                        service_charge_price = 0;
                    }

                    if ("{{ $data->gratuity_name }}" != "0") {
                        gratuited_price = ((total + sales_tax_price + service_charge_price) / 100) * gratuity;

                    } else {
                        gratuited_price = 0;
                    }




                    let refundable_price = ((total + sales_tax_price + service_charge_price + gratuited_price) / 100) * refundable;

                // Update fee displays
                $('.default-gratuity span').text('$' + gratuited_price.toFixed(2));
                $('.default-refundable span').text('$' + refundable_price.toFixed(2));
                if ("{{ $data->sales_tax_name }}" != "0") {
                    if ($('.default-sales-tax').length === 0) {
                        $('.sales_tax').after('<div style="font-size: 12px;" class="default-sales-tax">Sales Tax: <span>$0.00</span></div>');
                    }
                } else {
                    $('.default-sales-tax').remove();

                }
                // console.log(service_charge);
                $('.default-sales-tax span').text('$' + sales_tax_price.toFixed(2));
                $('.default-service-charge span').text('$' + service_charge_price.toFixed(2));

                // Add new fees to total
                total += gratuited_price;
                // total += refundable_price;
                total += sales_tax_price;
                total += service_charge_price;

                console.log(total);

                $('.default-total span').text('$' + total.toFixed(2));
                $('.payment_total').val(total.toFixed(2));
                if (refundable_price > 0) {
                    $('#subtotal').val(refundable_price.toFixed(2));
                } else {
                    $('#subtotal').val(total.toFixed(2));

                }
                // $('#subtotal').val(total.toFixed(2));
                $('.default-deposit span').text('$' + total.toFixed(2));
                $('.default-due span').text('$' + (total - refundable_price).toFixed(2));

                }

                recalculateTotals();

            });
        </script>



        <script>
            let promoDiscountPercent = 0;
            let promoDiscountAmount = 0;
            let promoType = 'percent'; // Default to percent discount

            

            function recalculateTotals() {
                let total = 0;
                // Add package price (if any)
                let packagePrice = parseFloat($('.default-package-price span').text().replace('$', '')) || 0;
                let old_price = $('#old_price').val();

                // console.log(old_price);
                // console.log(packagePrice);

                total = $('.default-deposit span').text().replace('$', '');
                // total -= old_price;
                // total += packagePrice;
                // total = parseFloat(total);

                // console.log(old_price);
                // console.log(packagePrice);
                // console.log(total);

                let gratuity = parseFloat($('#gratuity').val()) || 0;
                let sales_tax = parseFloat($('#sales_tax').val()) || 0;
                let refundable = parseFloat($('#refundable').val()) || 0;
                let service_charge = parseFloat($('#service_charge').val()) || 0;

                let service_charge_price = 0;
                let sales_tax_price = 0;
                let gratuited_price = 0;

                
                if ("{{ $data->sales_tax_name }}" != "0") {
                    sales_tax_price = (total / 100) * sales_tax;
                } else {
                    sales_tax_price = 0;
                }
                
                if ("{{ $data->service_charge_name }}" != "0") {
                    service_charge_price = (total / 100) * service_charge;
                } else {
                    service_charge_price = 0;
                }
                
                if ("{{ $data->gratuity_name }}" != "0") {
                    gratuited_price = ((total + sales_tax_price + service_charge_price) / 100) * gratuity;
                } else {
                    gratuited_price = 0;
                }

                // Calculate promo discount
                let promoDiscount = 0;
                if (promoDiscountPercent > 0) {
                    if (promoType == 'percentage') {
                        promoDiscount = (total / 100) * promoDiscountPercent;
                    } else {
                        promoDiscount = promoDiscountPercent; // Fixed amount discount
                    }
                }

                let refundable_base = total + gratuited_price + sales_tax_price + service_charge_price - promoDiscount;
                let refundable_price = (((total / 100) * refundable) / 100) * promoDiscountPercent;

                if (promoDiscountPercent > 0) {
                    if ($('.default-promo-discount').length === 0) {
                        $('.default-gratuity').after('<div style="font-size: 12px;" class="default-promo-discount">Promo Code Discount: <span>$0.00</span></div>');
                    }
                }
                $('.default-promo-discount span').text('-$' + promoDiscount.toFixed(2));

                let grandTotal = total - promoDiscount;

                $('.default-refundable span').text('$' + ((grandTotal / 100) * refundable).toFixed(2));

                $('.default-total span').text('$' + grandTotal.toFixed(2));
                $('.payment_total').val(grandTotal.toFixed(2));
                $('.default-deposit span').text('$' + grandTotal.toFixed(2));
                $('.default-due span').text('$' + (grandTotal - ((grandTotal / 100) * refundable)).toFixed(2));
                $('#subtotal').val(refundable > 0 ? ((grandTotal / 100) * refundable).toFixed(2) : grandTotal.toFixed(2));

                $('.discounted_amount').val(promoDiscount);
            }

            $('#applyPromoBtn').on('click', function () {
                let code = $('#promo_code').val().trim();
                if (!code) return;
                $.get('/{{ $data->slug }}/check/' + encodeURIComponent(code), function (res) {
                    if (res.valid === false || res.valid === "false") {
                        promoDiscountPercent = 0;
                        promoDiscountAmount = 0;
                        $('.default-promo-discount span').text('$0.00');
                        alert('Invalid promo code');
                    } else {
                        promoDiscountPercent = parseFloat(res.discount);
                        promoType = res.type || 'percent';
                        $('#applyPromoBtn').prop('disabled', true);
                        $('.promo_code').val(res.id);
                        recalculateTotals();
                    }
                });
            });

            // Recalculate totals on page load and after any price change
            // $(document).on('change', '.vip-btn', function () {
            //     setTimeout(recalculateTotals, 100); // Wait for DOM updates
            // });
        </script>

        <script>
            // Replace this with your country select's ID
            const countrySelectId = 'country';
            const stateSelectId = 'st-pv';

            // Listen for country change
            $(document).on('change', `#${countrySelectId}`, function () {
                const country = $(this).val();
                const $state = $(`#${stateSelectId}`);
                $state.html('<option value="">Loading...</option>');
                if (!country) {
                    $state.html('<option value="">Select State/Province</option>');
                    return;
                }

                // Example API for US states: https://countriesnow.space/api/v0.1/countries/states
                // You can use another API if you prefer
                $.ajax({
                    url: 'https://countriesnow.space/api/v0.1/countries/states',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({ country: country }),
                    success: function (res) {
                        if (res && res.data && res.data.states && res.data.states.length > 0) {
                            let options = '<option value="null" selected disabled>Select State/Province</option>';
                            res.data.states.forEach(function (state) {
                                options += `<option value="${state.name}">${state.name}</option>`;
                            });
                            $state.html(options);
                        } else {
                            $state.html('<option value="null" selected disabled>No states found</option>');
                        }
                    },
                    error: function () {
                        $state.html('<option value="null" selected disabled>Error loading states</option>');
                    }
                });
            });
        </script>

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script>
            flatpickr(".flatpickr-time", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "h:i K",
                time_24hr: false
            });
        </script>

        <script>
            $('#package_use_date').on('change', function(){
                val = $('#package_use_date').val();

                $('.package_use_date').val(val);

            })
        </script>

        @if ($data->payment_method == 'stripe')
            <script src="https://js.stripe.com/v3/"></script>

            @php
                $setting = \App\Models\Setting::where('id',1)->first();

                if ($data->stripe_app_key != null) {
                    # code...
                    $app = $data->stripe_app_key;
                } else {
                    # code...
                    $app = $setting->stripe_key;
                }
                

            @endphp

            <script>
                    const stripe = Stripe("{{ $app }}");
                    const elements = stripe.elements();

                    const style = {
                        base: {
                            fontSize: '14px',
                            color: '#fff',
                            width: '100%',
                            height: '40px',
                            paddingLeft: '10px',
                            paddingRight: '10px',
                            border: '1px solid #9b9b9b',
                            backgroundColor: 'transparent',
                            borderRadius: '10px',
                        },
                    };

                    const cardNumber = elements.create('cardNumber', {style: style});
                    const cardExpiry = elements.create('cardExpiry', {style: style});
                    const cardCvc = elements.create('cardCvc', {style: style});

                    cardNumber.mount('#card_number');
                    cardExpiry.mount('#expiration_date');
                    cardCvc.mount('#cvv');

                    const form = document.getElementById('payment-form');
                    form.addEventListener('submit', async function(e) {
                        e.preventDefault();

                        const {token, error} = await stripe.createToken(cardNumber);

                        if (error) {
                            document.getElementById('card-errors').textContent = error.message;
                        } else {
                            const hiddenInput = document.createElement('input');
                            hiddenInput.setAttribute('type', 'hidden');
                            hiddenInput.setAttribute('name', 'stripeToken');
                            hiddenInput.setAttribute('value', token.id);
                            form.appendChild(hiddenInput);
                            form.submit();
                        }
                    });
                </script>
        @endif

        <!-- Custom Calendar Icon Handler -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Handle calendar icon clicks
                document.querySelectorAll('.custom-calendar-icon').forEach(function(icon) {
                    icon.addEventListener('click', function() {
                        const dateInput = this.previousElementSibling;
                        if (dateInput && dateInput.type === 'date') {
                            // Try modern showPicker method first
                            if (dateInput.showPicker) {
                                try {
                                    dateInput.showPicker();
                                } catch (e) {
                                    // Fallback: focus and click
                                    dateInput.focus();
                                    dateInput.click();
                                }
                            } else {
                                // Fallback for older browsers
                                dateInput.focus();
                                dateInput.click();
                            }
                        }
                    });
                });
            });
        </script>

    </body>

    </html>