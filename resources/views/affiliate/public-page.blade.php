@php
    $brandPrimary = '#ffcc00';
    $brandSecondary = '#ddb774';
    $brandGradient = 'linear-gradient(135deg, #f7e2b4 0%, #ddb774 52%, #ffcc00 100%)';
    $data->color = $brandPrimary;
    $data->secondary_color = $brandSecondary;
    $data->background_color = '#0b0e1a';
    $data->font_color = '#e8eaf6';
@endphp
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $affiliate->display_name }} - CartVIP</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.7/css/bootstrap.min.css"
            integrity="sha512-fw7f+TcMjTb7bpbLJZlP8g2Y4XcCyFZW8uy8HsRZsH/SwbMw0plKHFHr99DN3l04VsYNwvzicUX/6qurvIxbxw=="
            crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link rel="stylesheet" href="{{ asset('styles/main.css') }}">
        <style>

#Pick-up-time,
input[name="transportation_pickup_time"] {
    background: #ffffff !important;
    color: #111111 !important;
    -webkit-text-fill-color: #111111 !important;
    border-color: #d2d7e3 !important;
}

#Pick-up-time::placeholder,
input[name="transportation_pickup_time"]::placeholder {
    color: #555555 !important;
}

.checkout-steps {
    display: flex !important;
    justify-content: center;
    align-items: flex-start;
    margin: 2rem 0;
    padding: 0;
    list-style: none;
    gap: 0;
    flex-wrap: nowrap !important;
    width: 100%;
}

.step {
    flex: 1 1 0;
    min-width: 0;
    text-align: center;
    position: relative;
    padding: 0 0.5rem;
}

/* Connector line between steps - positioned absolutely on the step element */
.step::after {
    content: '';
    position: absolute;
    top: 20px;
    left: calc(50% + 22px);
    width: calc(100% - 44px);
    height: 2px;
    background: rgba(255,255,255,0.14);
    z-index: 0;
    pointer-events: none;
}
.step:last-child::after { display: none; }
.step.completed::after,
.step.active::after { background: linear-gradient(90deg, #a774ff, #7c3aed); }

.step-number {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(255,255,255,0.06);
    color: rgba(255,255,255,0.7);
    line-height: 1;
    font-weight: bold;
    margin: 0 auto 0.5rem;
    border: 2px solid rgba(255,255,255,0.18);
    position: relative;
    z-index: 1;
    transition: all .2s;
}

.step.active .step-number {
    background: linear-gradient(135deg, #a774ff 0%, #7c3aed 100%);
    border-color: #7c3aed;
    color: #fff;
    box-shadow: 0 0 0 4px rgba(167,116,255,0.18), 0 4px 12px rgba(124,58,237,0.4);
}

.step.completed .step-number {
    background: linear-gradient(135deg, #a774ff 0%, #5b21b6 100%);
    border-color: #7c3aed;
    color: #fff;
}

.step-title {
    font-size: 0.875rem;
    color: rgba(255,255,255,0.55);
    margin: 0;
    line-height: 1.25;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.step.active .step-title,
.step.completed .step-title {
    color: #fff;
    font-weight: bold;
}

@media (max-width: 767px) {
    .checkout-steps { margin: 1.25rem 0; padding: 0 4px; }
    .step { padding: 0 0.2rem; }
    .step-number { width: 32px; height: 32px; font-size: 13px; }
    .step::after { top: 16px; left: calc(50% + 18px); width: calc(100% - 36px); }
    .step-title { font-size: 0.72rem; }
}
@media (max-width: 420px) {
    .step-title { font-size: 0.65rem; }
    .step-number { width: 28px; height: 28px; font-size: 12px; }
    .step::after { top: 14px; left: calc(50% + 16px); width: calc(100% - 32px); }
}

/* cv-dstep responsive - single line on mobile */
.cv-desktop-steps { flex-wrap: nowrap !important; }
@media (max-width: 767px) {
    .cv-desktop-steps { gap: 0; grid-template-columns: repeat(4, minmax(0, 1fr)) !important; }
    .cv-dstep { font-size: 9.5px !important; padding: 0 2px !important; gap: 5px !important; }
    .cv-dstep-num { width: 26px !important; height: 26px !important; font-size: 11px !important; }
    .cv-dstep::before { top: 13px !important; left: calc(50% + 15px) !important; right: calc(-50% + 15px) !important; }
    .cv-dstep > span:last-child { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%; }
}
@media (max-width: 420px) {
    .cv-dstep { font-size: 8.5px !important; }
    .cv-dstep-num { width: 24px !important; height: 24px !important; font-size: 10px !important; }
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
    background: {{ $brandPrimary }};
    color: #000;
    border: none;
    padding: 11px 28px;
    border-radius: 25px;
    font-weight: 700;
    cursor: pointer;
    font-size: 15px;
    transition: opacity .2s, transform .15s;
}

.btn-prev {
    background: #555;
    color: #fff;
    min-width: 140px;
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

/* Red asterisk on required form field labels */
.form-group > label:has(~ input[required])::after,
.form-group > label:has(~ select[required])::after,
.form-group > label:has(~ textarea[required])::after,
.form-group > label:has(~ .form-row input[required])::after,
.form-group > label:has(~ .form-row select[required])::after,
.num-guest > label:has(~ input[required])::after {
    content: " *";
    color: #ef4444;
    font-weight: 700;
}

/* Consistent button styles */
.same-as-info, .same-as-info-transport {
    background: {{ $brandPrimary }} !important;
    color: #000 !important;
    border: none;
    padding: 9px 20px;
    border-radius: 25px;
    font-weight: 700;
    margin-bottom: 16px;
    cursor: pointer;
    transition: opacity .2s, transform .15s;
    display: inline-block;
    width: 280px;
    text-align: center;
    font-size: 13px;
}

.btn-next, .btn-prev, .submit-btn {
    background: {{ $brandPrimary }} !important;
    color: #000 !important;
    border: none;
    padding: 11px 28px;
    border-radius: 25px;
    font-weight: 700;
    cursor: pointer;
    transition: opacity .2s, transform .15s;
    font-size: 15px;
    min-width: 180px;
    text-align: center;
}

.btn-next:hover, .submit-btn:hover {
    opacity: .85;
    transform: translateY(-1px);
}

.btn-prev {
    background: #555 !important;
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
                    {{ $brandPrimary }}
                    !important;
                border-color:
                    {{ $brandPrimary }}
                    !important;
            }

            .card:hover {
                border-color:
                    {{ $brandPrimary }}
                    !important;
            }

            .submit-btn {
                background:
                    {{ $brandPrimary }}
                    !important;
                color: #000 !important;
            }

            .event-filters .active{
                background-color: {{ $brandPrimary }} !important;
                color: #000 !important;
            }

            .event-filter:hover{
                background-color: {{ $brandPrimary }} !important;
                color: #000 !important;
            }

            .submit-btn.active {
                background:
                    {{ $brandPrimary }}
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

/* Hide native date indicator; keep only custom icon */
input[type="date"]::-webkit-calendar-picker-indicator {
    display: none !important;
    opacity: 0 !important;
    width: 0 !important;
    height: 0 !important;
}

/* Ensure date input shows calendar icon in all browsers */
input[type="date"] {
    position: relative;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    background: transparent;
    border: 1px solid #9797a0;
    border-radius: 10px;
    padding: 10px 40px 10px 15px;
    color: #fff;
    -webkit-text-fill-color: #fff;
}

input[type="date"]:disabled,
input[type="date"]:read-only {
    opacity: 1;
    -webkit-text-fill-color: #fff;
    color: #fff;
}

input[type="date"]:focus {
    outline: none;
    border-color: {{ $brandPrimary }};
}

/* Mobile/Safari form field padding fixes */
@media screen and (-webkit-min-device-pixel-ratio: 0) {
    /* Safari and iOS specific styles */
    select.form-select {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        background: url('data:image/svg+xml;charset=UTF-8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white"><path d="M7 10l5 5 5-5z"/></svg>') no-repeat right 10px center;
        background-size: 20px;
        padding: 12px 30px 12px 15px !important;
        border: 1px solid #9797a0;
        border-radius: 10px;
        color: #fff;
        background-color: transparent;
    }
    
    select.form-select:focus {
        outline: none;
        border-color: {{ $brandPrimary }};
    }
    
    /* Fix for guest total select padding */
    .vip-select {
        padding: 12px 40px 12px 15px !important;
        min-height: 45px !important;
    }
    
    /* Date of birth select fields padding */
    select[id*="dob"] {
        padding: 12px 30px 12px 15px !important;
        min-height: 45px !important;
        text-align: center !important;
    }
    
    /* Country and state select padding */
    #country, #st-pv {
        padding: 12px 40px 12px 15px !important;
        min-height: 45px !important;
    }
}

/* General mobile responsive fixes */
@media (max-width: 768px) {
    .form-select {
        padding: 12px 40px 12px 15px !important;
        font-size: 16px !important; /* Prevents zoom on iOS */
        min-height: 45px !important;
    }
    
    input[type="date"] {
        font-size: 16px !important; /* Prevents zoom on iOS */
        padding: 12px 40px 12px 15px !important;
        min-height: 45px !important;
    }
    
    /* Guest total select mobile fix */
    .package_number_of_guestss {
        padding: 12px 40px 12px 15px !important;
        min-height: 45px !important;
        font-size: 16px !important;
    }
    
    /* Date of birth fields mobile spacing */
    .form-row select {
        margin-bottom: 10px !important;
    }
    
    .form-row select[style*="width: 32%"] {
        width: 100% !important;
        margin-right: 0 !important;
        margin-bottom: 10px !important;
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

/* iOS Safari specific fixes */
@supports (-webkit-touch-callout: none) {
    input, select, textarea {
        font-size: 16px !important;
    }
    
    input[type="date"]::-webkit-calendar-picker-indicator {
        display: none !important;
        opacity: 0 !important;
        width: 0 !important;
        height: 0 !important;
    }
    
    /* Force proper rendering of JavaScript-generated select fields */
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

/* Enhanced Safari/iOS fixes for JavaScript-generated select fields */
@media screen and (-webkit-min-device-pixel-ratio: 0) {
    /* JavaScript-generated country selects */
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
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    border-radius: 0;
    flex: 0 0 auto;
    white-space: nowrap;
    font-size: 16px;
    min-width: 120px;
    min-height: 44px;
    line-height: 1.2;
    text-align: center;
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
    background: {{ $brandPrimary }};
    color: #000;
}

nav .tab:hover {
    background: {{ $brandPrimary }};
    color: #000;
}

nav .tab p {
    margin: 0;
    font-weight: 600;
    line-height: 1.2;
}

nav .tab.active p {
    color: #000 !important;
}

.aff-footer {
    margin-top: 0px;
    position: relative;
    background: linear-gradient(180deg, rgba(11,8,22,0.92), rgba(5,3,12,0.98));
    border-top: 1px solid rgba(167,116,255,0.18);
    overflow: hidden;
}
.aff-footer::before {
    content: '';
    position: absolute;
    top: -1px;
    left: 50%;
    transform: translateX(-50%);
    width: 50%;
    height: 2px;
    background: linear-gradient(90deg, transparent, #a774ff, #7c3aed, #a774ff, transparent);
    box-shadow: 0 0 20px rgba(167,116,255,0.5);
}
.aff-footer::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: radial-gradient(ellipse at top center, rgba(167,116,255,0.05), transparent 65%);
    pointer-events: none;
}
.aff-footer .container { position: relative; z-index: 1; }
.cv-footer-inner {
    padding: 28px 0 20px;
    border-bottom: 1px solid rgba(167,116,255,0.12);
    display: grid;
    grid-template-columns: 220px 1fr;
    gap: 40px;
    align-items: start;
    text-align: left;
}
.cv-footer-brand {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
    flex-shrink: 0;
}
.cv-footer-logo { height: 60px; width: auto; max-width: 180px; display: block; object-fit: contain; }
.cv-footer-powered {
    font-size: 10px;
    font-weight: 800;
    color: rgba(167,116,255,0.7);
    letter-spacing: .12em;
    text-transform: uppercase;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.cv-footer-powered::before {
    content: '';
    width: 6px;
    height: 6px;
    background: #a774ff;
    border-radius: 50%;
    box-shadow: 0 0 8px #a774ff;
}
.cv-footer-tagline {
    font-size: 12.5px;
    color: rgba(255,255,255,0.5);
    line-height: 1.5;
    max-width: 220px;
    margin: 2px 0 0;
}
.cv-footer-legal {
    color: rgba(255,255,255,0.5);
    font-size: 11.5px;
    line-height: 1.55;
    display: flex;
    flex-direction: column;
    gap: 8px;
    min-width: 0;
    text-align: left;
}
.cv-footer-legal-title {
    font-size: 10.5px;
    font-weight: 800;
    color: #c4a3ff !important;
    text-transform: uppercase;
    letter-spacing: 0.12em;
    margin: 0 0 4px;
    text-align: left;
}
.cv-footer-legal p { margin: 0; }
.cv-footer-legal a {
    color: #c4a3ff !important;
    font-weight: 600;
    text-decoration: underline;
    text-decoration-color: rgba(167,116,255,0.55);
    text-underline-offset: 3px;
    text-decoration-thickness: 1px;
    transition: all .15s;
}
.cv-footer-legal a:hover {
    color: #fff !important;
    text-decoration-color: #a774ff;
    text-decoration-thickness: 2px;
}
.cv-footer-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    padding: 14px 0 16px;
    font-size: 11.5px;
    color: rgba(255,255,255,0.5);
}
.cv-footer-bar-copy { display: inline-flex; align-items: center; gap: 5px; }
.cv-footer-bar-copy strong { color: rgba(255,255,255,0.85); font-weight: 700; }
.cv-footer-bar-socials { display: inline-flex; gap: 8px; }
.cv-footer-bar-social {
    width: 30px;
    height: 30px;
    border-radius: 8px;
    background: rgba(167,116,255,0.08);
    border: 1px solid rgba(167,116,255,0.22);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: rgba(196,163,255,0.85) !important;
    text-decoration: none !important;
    transition: all .15s;
    font-size: 12px;
}
.cv-footer-bar-social:hover {
    background: linear-gradient(135deg, rgba(167,116,255,0.2), rgba(124,58,237,0.2));
    border-color: #a774ff;
    color: #fff !important;
    transform: translateY(-2px);
}
@media (min-width: 769px) {
    .cv-footer-inner {
        grid-template-columns: minmax(300px, 360px) 1fr;
        gap: 48px;
    }
    .cv-footer-brand {
        position: relative;
        padding: 20px 22px 18px;
        border-radius: 18px;
        background: linear-gradient(160deg, rgba(167,116,255,0.16), rgba(16,11,33,0.86));
        border: 1px solid rgba(167,116,255,0.3);
        box-shadow: 0 14px 34px rgba(0,0,0,0.34), inset 0 1px 0 rgba(255,255,255,0.08);
        overflow: hidden;
    }
    .cv-footer-brand::before {
        content: '';
        position: absolute;
        top: -40px;
        right: -40px;
        width: 130px;
        height: 130px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(167,116,255,0.35), rgba(167,116,255,0));
        pointer-events: none;
    }
    .cv-footer-logo {
        height: 66px;
        filter: drop-shadow(0 4px 10px rgba(0,0,0,0.35));
    }
    .cv-footer-powered {
        color: rgba(231,206,255,0.92);
        letter-spacing: 0.14em;
    }
    .cv-footer-tagline {
        max-width: 290px;
        color: rgba(255,255,255,0.72);
    }
}
@media (max-width: 768px) {
    .cv-footer-inner { grid-template-columns: 1fr; gap: 20px; padding: 24px 0 18px; text-align: center; }
    .cv-footer-brand { align-items: center; text-align: center; }
    .cv-footer-tagline { max-width: 100%; text-align: center; }
    .cv-footer-legal,
    .cv-footer-legal-title { text-align: center; }
    .cv-footer-bar { justify-content: center; text-align: center; flex-direction: column; gap: 10px; padding: 14px 0; }
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

.website-desc h2 {
    font-size: 24px;
    margin-bottom: 15px;
    font-weight: bold;
}

.website-description-content {
    font-size: 16px;
    line-height: 1.6;
}

/* ===================================================
   AFFILIATE PAGE DESIGN SYSTEM
   =================================================== */
:root {
    --accent:    {{ $brandPrimary }};
    --bg:        {{ $data->background_color }};
    --text-main: {{ $data->font_color ?? '#e8eaf6' }};
    --aff-accent: var(--accent);
    --aff-text: var(--text-main);
    --brand-gradient: #ffcc00;
}

/* Glassmorphism package cards */
.vip-card {
    background: rgba(255,255,255,0.03) !important;
    border: 1px solid rgba(255,255,255,0.12) !important;
    border-radius: 14px !important;
    padding: 16px 18px;
    margin-bottom: 12px;
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
    gap: 14px;
    transition: border-color .2s;
}
.vip-card:hover { border-color: rgba(255,255,255,0.28) !important; }

/* Form inputs � frosted glass background */
input[type="text"], input[type="email"], input[type="tel"],
input[type="number"], textarea {
    background: rgba(255,255,255,0.07) !important;
    border: 1px solid #9797a0 !important;
    border-radius: 10px !important;
    color: #fff !important;
    padding: 10px 14px;
    width: 100%;
    font-size: 15px;
}
input::placeholder, textarea::placeholder {
    color: rgba(255,255,255,0.35) !important;
}

/* Checkbox containers - unified toggle switch */
.consent-label {
    display: flex;
    gap: 10px;
    align-items: flex-start;
    cursor: pointer;
    margin-bottom: 10px;
    font-size: 13px;
}
.consent-label span {
    flex: 1;
    line-height: 1.4;
}
.consent-label input {
    -webkit-appearance: none;
    appearance: none;
    width: 46px !important;
    height: 26px;
    border-radius: 999px;
    border: 1px solid rgba(255,255,255,0.28);
    background: rgba(255,255,255,0.16);
    position: relative;
    margin-top: 0 !important;
    padding: 0 !important;
    flex-shrink: 0;
    cursor: pointer;
    transition: background .2s ease, border-color .2s ease;
}
.consent-label input::before {
    content: '';
    position: absolute;
    top: 2px;
    left: 2px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #fff;
    transition: transform .2s ease;
}
.consent-label input:checked {
    background: var(--accent);
    border-color: var(--accent);
}
.consent-label input:checked::before {
    transform: translateX(20px);
}
.consent-label input:focus-visible {
    outline: 2px solid rgba(255,204,0,0.7);
    outline-offset: 2px;
}

/* Payment agreement toggles: exact affiliate parity, locked with stronger selectors */
#payment-consent-group .consent-label {
    display: flex !important;
    gap: 10px !important;
    align-items: flex-start !important;
    cursor: pointer !important;
    margin-bottom: 10px !important;
    font-size: 13px !important;
    font-family: 'Inter', sans-serif !important;
    font-weight: 400 !important;
}
#payment-consent-group .consent-label span {
    flex: 1 !important;
    line-height: 1.4 !important;
    font-size: 13px !important;
    font-family: 'Inter', sans-serif !important;
}
#payment-consent-group .consent-label input[type="checkbox"] {
    -webkit-appearance: none !important;
    appearance: none !important;
    width: 46px !important;
    min-width: 46px !important;
    height: 26px !important;
    border-radius: 999px !important;
    border: 1px solid rgba(255,255,255,0.28) !important;
    background: rgba(255,255,255,0.16) !important;
    position: relative !important;
    margin-top: 0 !important;
    padding: 0 !important;
    flex-shrink: 0 !important;
    cursor: pointer !important;
    transition: background .2s ease, border-color .2s ease !important;
}
#payment-consent-group .consent-label input[type="checkbox"]::before {
    content: '' !important;
    position: absolute !important;
    top: 2px !important;
    left: 2px !important;
    width: 20px !important;
    height: 20px !important;
    border-radius: 50% !important;
    background: #fff !important;
    transition: transform .2s ease !important;
}
#payment-consent-group .consent-label input[type="checkbox"]:checked {
    background: #ffcc00 !important;
    border-color: #ffcc00 !important;
}
#payment-consent-group .consent-label input[type="checkbox"]:checked::before {
    transform: translateX(20px) !important;
}
#payment-consent-group .consent-label input[type="checkbox"]:focus-visible {
    outline: 2px solid rgba(255,204,0,0.7) !important;
    outline-offset: 2px !important;
}
#payment-consent-group .consent-label a {
    font-size: 13px !important;
    font-family: 'Inter', sans-serif !important;
}

.checkbox-container label {
    display: flex;
    gap: 10px;
    align-items: flex-start;
    cursor: pointer;
    margin-bottom: 10px;
    font-size: 13px;
    line-height: 1.4;
}
.checkbox-container label span {
    flex: 1;
}
.checkbox-container input[type="checkbox"] {
    -webkit-appearance: none;
    appearance: none;
    width: 46px !important;
    height: 26px;
    border-radius: 999px;
    border: 1px solid rgba(255,255,255,0.28);
    background: rgba(255,255,255,0.16);
    position: relative;
    margin-top: 0 !important;
    padding: 0 !important;
    flex-shrink: 0;
    cursor: pointer;
    transition: background .2s ease, border-color .2s ease;
}
.checkbox-container input[type="checkbox"]::before {
    content: '';
    position: absolute;
    top: 2px;
    left: 2px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #fff;
    transition: transform .2s ease;
}
.checkbox-container input[type="checkbox"]:checked {
    background: var(--accent);
    border-color: var(--accent);
}
.checkbox-container input[type="checkbox"]:checked::before {
    transform: translateX(20px);
}
.checkbox-container input[type="checkbox"]:focus-visible {
    outline: 2px solid rgba(255,204,0,0.7);
    outline-offset: 2px;
}

/* Cart section card */
#cart-section {
    background:
        linear-gradient(145deg, rgba(255,255,255,0.12), rgba(255,255,255,0.03)),
        radial-gradient(circle at top right, color-mix(in srgb, var(--accent) 22%, transparent), transparent 48%),
        linear-gradient(135deg, rgba(10, 16, 32, 0.96), rgba(7, 11, 22, 0.94)) !important;
    border: 1px solid color-mix(in srgb, var(--accent) 44%, rgba(255,255,255,0.12)) !important;
    border-radius: 16px !important;
    padding: 18px 20px;
    box-shadow: 0 20px 42px rgba(0,0,0,0.24), inset 0 1px 0 rgba(255,255,255,0.14);
    backdrop-filter: blur(10px);
}

#cart-section #cart-list > div {
    border: 1px solid rgba(255,255,255,0.14);
    border-radius: 12px;
    background: rgba(255,255,255,0.03);
    padding: 10px 12px;
    margin-bottom: 8px;
}

#cart-section .cart-heading {
    font-weight: 800;
    font-size: 16px;
    margin-bottom: 12px;
    letter-spacing: .01em;
}

#cart-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

#cart-section #cart-list .cart-line {
    border: 1px solid rgba(255,255,255,0.16);
    border-radius: 12px;
    background: linear-gradient(140deg, rgba(255,255,255,0.06), rgba(255,255,255,0.02));
    padding: 12px;
}

#cart-section .cart-line-main {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
}

#cart-section .cart-item-name {
    font-weight: 700;
    color: #f7f9ff;
    line-height: 1.3;
}

#cart-section .cart-item-price {
    margin-top: 4px;
    color: var(--accent);
    font-size: 13px;
    font-weight: 700;
}

#cart-section .cart-remove-btn {
    border: 1px solid #ef4444;
    background: rgba(239, 68, 68, 0.15);
    color: #ff6b6b;
    border-radius: 999px;
    padding: 4px 11px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
}

#cart-section .cart-remove-btn:hover {
    border-color: #ef4444;
    background: rgba(239, 68, 68, 0.25);
}

#cart-section .cart-addons {
    margin-top: 8px;
    font-size: 12px;
    line-height: 1.5;
    color: rgba(232, 235, 246, 0.8);
}

#cart-total {
    margin-top: 12px;
    padding-top: 10px;
    border-top: 1px solid rgba(255,255,255,0.14);
    font-size: 14px;
    font-weight: 700;
}

#cart-coupon {
    margin-top: 6px;
    font-size: 13px;
    color: #74d49f;
}

/* Step navigation � centered flex row */
.step-navigation {
    display: flex !important;
    justify-content: center;
    gap: 12px;
    flex-wrap: wrap;
    margin: 1.5rem 0;
}

/* Promo apply button */
.vip-btn,
#generateShareLink {
    background: var(--accent) !important;
    color: #000 !important;
    font-weight: 700;
    border: none;
    padding: 8px 20px;
    border-radius: 25px;
    cursor: pointer;
    transition: opacity .2s, transform .15s;
    white-space: nowrap;
    font-size: 14px;
    text-decoration: none;
    display: inline-block;
}

.vip-btn:hover,
#generateShareLink:hover {
    opacity: .85;
    transform: translateY(-1px);
    color: #000 !important;
}

.back-home-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    max-width: min(100%, 360px);
    min-height: 42px;
    padding: 10px 18px;
    border-radius: 14px;
    border: 1px solid rgba(255, 204, 0, 0.45);
    background: linear-gradient(135deg, rgba(255, 204, 0, 0.2), rgba(255, 204, 0, 0.08));
    color: var(--text-main) !important;
    text-decoration: none;
    font-weight: 700;
    letter-spacing: .02em;
    line-height: 1.2;
    white-space: normal;
    overflow-wrap: anywhere;
    word-break: break-word;
    box-shadow: 0 10px 24px rgba(0, 0, 0, 0.25), inset 0 1px 0 rgba(255, 255, 255, 0.14);
    backdrop-filter: blur(6px);
    transition: transform .15s ease, border-color .15s ease, background .2s ease;
}

.back-home-btn i {
    color: var(--accent);
    font-size: 13px;
}

.back-home-btn:hover {
    transform: translateY(-1px);
    border-color: rgba(255, 204, 0, 0.7);
    background: linear-gradient(135deg, rgba(255, 204, 0, 0.26), rgba(255, 204, 0, 0.12));
    color: #fff !important;
}

@media (max-width: 575.98px) {
    .back-home-btn {
        width: 100%;
        max-width: none;
    }
}

.vip-btn-submit, #applyPromoBtn {
    background: var(--accent) !important;
    color: #000 !important;
    font-weight: 700;
    border: none;
    padding: 0 18px;
    cursor: pointer;
    white-space: nowrap;
    font-size: 14px;
    border-top-right-radius: 10px;
    border-bottom-right-radius: 10px;
}

/* Section headings */
.checkout-section h2 {
    font-size: 1.4rem;
    font-weight: 700;
    margin-bottom: 1.2rem;
}

/* Addon selection modal � dark theme */
#addonSelectionModal .modal-content {
    background: #1a1d2e;
    color: #ddd;
}
#addonSelectionModal .modal-title { color: #fff !important; }
#addonModalConfirmBtn {
    background: var(--brand-gradient) !important;
    color: #1f1400 !important;
    font-weight: 700;
}

/* Exact affiliate-page layout surfaces */
body {
    background:
        radial-gradient(circle at top right, rgba(255, 255, 255, 0.06), transparent 34%),
        linear-gradient(180deg, var(--bg) 0%, #0f1526 48%, var(--bg) 100%) !important;
    color: var(--text-main) !important;
    font-family: 'Inter', sans-serif;
    min-height: 100vh;
}

.aff-hero {
    background: linear-gradient(135deg, rgba(255,255,255,0.06) 0%, rgba(255,255,255,0.02) 100%);
    border-bottom: 1px solid rgba(255,255,255,0.1);
    padding: 10px 0 8px;
}

.aff-avatar {
    width: 68px;
    height: 68px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid var(--accent);
    background: rgba(255,255,255,0.08);
}

.aff-initials {
    width: 68px;
    height: 68px;
    border-radius: 50%;
    border: 2px solid var(--accent);
    background: rgba(255,255,255,0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    font-weight: 800;
}

.aff-hero-title {
    font-size: 1.18rem;
    line-height: 1.15;
    font-weight: 800;
}

.aff-hero-copy {
    opacity: .75;
    font-size: 12px;
    line-height: 1.3;
}

.aff-banner {
    position: relative;
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 18px;
    overflow: hidden;
    width: 100%;
    min-height: 520px;
    margin: 0;
    background:
        var(--brand-gradient),
        radial-gradient(circle at top right, rgba(255,255,255,0.08), transparent 35%),
        var(--accent);
}

.event-hero-layout {
    display: grid;
    grid-template-columns: minmax(420px, 1fr) 476px;
    gap: 22px;
    align-items: stretch;
    margin: 8px 0 14px;
}

.event-hero-copy {
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 14px;
    padding: 18px;
    min-height: 700px;
    height: 100%;
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
}

.event-banner-wrap {
    width: 476px;
    min-width: 476px;
    max-width: 476px;
    justify-self: end;
    height: 700px;
}

.event-banner-wrap .aff-banner {
    width: 476px;
    height: 700px;
    min-height: 700px;
}

.aff-kicker {
    font-size: 9px;
    letter-spacing: 1px;
    text-transform: uppercase;
    opacity: .64;
    font-weight: 700;
}

.aff-display-title {
    font-size: clamp(1.05rem, 2vw, 1.55rem);
    line-height: 1.12;
    font-weight: 800;
    max-width: none;
    margin: 2px 0 4px;
    color: #fff !important;
}

.aff-display-copy {
    max-width: none;
    font-size: 11px;
    line-height: 1.25;
    opacity: .82;
    color: #d8def0 !important;
}

.hero-date-card {
    margin-top: 5px;
    width: 100%;
    max-width: 440px;
    padding: 7px 10px;
    border-radius: 14px;
    border: 1px solid rgba(255,255,255,0.08);
    background: rgba(255,255,255,0.04);
}

.back-to-packages-btn {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    margin-top: 10px;
    padding: 8px 11px;
    border-radius: 10px;
    border: 1px solid rgba(255,255,255,0.2);
    background: rgba(255,255,255,0.06);
    color: #fff;
    text-decoration: none;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .04em;
    text-transform: uppercase;
    transition: all .2s ease;
}

.back-to-packages-btn:hover {
    color: #fff;
    background: rgba(255,255,255,0.12);
    transform: translateY(-1px);
}

.hero-date-card label {
    margin-bottom: 3px;
    text-transform: uppercase;
    font-size: 9px;
    letter-spacing: .8px;
    opacity: .7;
}

.reservation-date-error {
    display: block;
    margin-top: 6px;
    font-size: 12px;
    color: #ffb4b4;
    font-weight: 600;
}

.hero-capacity-note {
    margin-top: 6px;
    font-size: 11px;
    font-weight: 600;
    color: #d8def0;
}

.hero-capacity-note.sold-out {
    color: #ffb4b4;
}

.event-cart-capacity-banner {
    margin: 0 0 14px;
    padding: 10px 12px;
    border-radius: 10px;
    border: 1px solid rgba(255,255,255,0.12);
    background: rgba(255,255,255,0.04);
    flex-wrap: wrap;
    font-size: 13px;
    font-weight: 600;
}

.event-cart-capacity-banner.sold-out {
    border-color: rgba(255, 120, 120, 0.45);
    color: #ffb4b4;
}
    min-width: 128px;

.vip-btn[disabled] {
    opacity: .58;
    cursor: not-allowed;
}

.vip-availability-note {
    margin-top: 6px;
    font-size: 11px;
    flex: 1 1 100%;
    min-width: 0;
}

.event-capacity-chip {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-top: 8px;
    padding: 5px 10px;
    border-radius: 999px;
    background: rgba(221, 183, 116, 0.16);
    color: #fff;
    font-size: 12px;
    font-weight: 700;
}

.event-capacity-chip.sold-out {
    background: rgba(255, 96, 96, 0.16);
    color: #ffb4b4;
}

#package_use_date {
    color: #f5f8ff !important;
    -webkit-text-fill-color: #f5f8ff !important;
    font-weight: 600;
    opacity: 1 !important;
    text-shadow: 0 0 0 #f5f8ff;
}

#package_use_date[readonly],
#package_use_date.flatpickr-input[readonly] {
    color: #f5f8ff !important;
    -webkit-text-fill-color: #f5f8ff !important;
    opacity: 1 !important;
    text-shadow: 0 0 0 #f5f8ff;
}

.flatpickr-calendar {
    background: #0f172a !important;
    border: 1px solid rgba(148, 163, 184, 0.35) !important;
    box-shadow: 0 16px 32px rgba(2, 6, 23, 0.45) !important;
}

.flatpickr-calendar .flatpickr-months,
.flatpickr-calendar .flatpickr-weekdays,
.flatpickr-calendar .flatpickr-days {
    background: #0f172a !important;
}

.flatpickr-month,
.flatpickr-current-month .flatpickr-monthDropdown-months,
.flatpickr-current-month input.cur-year,
.flatpickr-weekday,
.flatpickr-day,
.flatpickr-calendar .flatpickr-months .flatpickr-prev-month,
.flatpickr-calendar .flatpickr-months .flatpickr-next-month {
    color: #e2e8f0 !important;
    fill: #e2e8f0 !important;
}

.flatpickr-day:hover {
    background: rgba(221, 183, 116, 0.28) !important;
    border-color: rgba(201, 156, 77, 0.62) !important;
}

.flatpickr-day.today {
    border-color: #ffcc00 !important;
    color: #ffde6b !important;
}

.flatpickr-day.selected,
.flatpickr-day.startRange,
.flatpickr-day.endRange {
    background: #ffcc00 !important;
    border-color: #ffcc00 !important;
    color: #1f1400 !important;
}

.flatpickr-day.prevMonthDay,
.flatpickr-day.nextMonthDay,
.flatpickr-day.notAllowed,
.flatpickr-day.flatpickr-disabled {
    color: rgba(226, 232, 240, 0.35) !important;
}

.flatpickr-calendar .flatpickr-current-month .flatpickr-monthDropdown-months,
.flatpickr-calendar .flatpickr-current-month input.cur-year {
    background: #111d33 !important;
    color: #e2e8f0 !important;
    border: 1px solid rgba(148, 163, 184, 0.45) !important;
    height: 32px;
    line-height: 32px;
    box-sizing: border-box;
}

.flatpickr-calendar .flatpickr-current-month .flatpickr-monthDropdown-months {
    padding: 0 26px 0 10px;
    -webkit-appearance: menulist;
    appearance: menulist;
}

.flatpickr-calendar .flatpickr-current-month input.cur-year {
    padding: 0 8px !important;
}

@media (max-width: 767.98px) {
    .flatpickr-calendar .flatpickr-current-month .flatpickr-monthDropdown-months,
    .flatpickr-calendar .flatpickr-current-month input.cur-year {
        height: 34px;
        line-height: 34px;
    }
}

.flatpickr-calendar .flatpickr-current-month .flatpickr-monthDropdown-months option {
    background: #0f172a !important;
    color: #e2e8f0 !important;
}

/* Final override: keep month/year selectors fully visible on mobile */
.flatpickr-calendar .flatpickr-months,
.flatpickr-calendar .flatpickr-month,
.flatpickr-calendar .flatpickr-current-month {
    overflow: visible !important;
}

.flatpickr-calendar .flatpickr-current-month .flatpickr-monthDropdown-months,
.flatpickr-calendar .flatpickr-current-month input.cur-year,
.flatpickr-calendar .flatpickr-current-month .numInputWrapper {
    height: 34px !important;
    line-height: 34px !important;
}

.flatpickr-calendar .flatpickr-current-month .flatpickr-monthDropdown-months {
    padding-top: 0 !important;
    padding-bottom: 0 !important;
    -webkit-appearance: menulist !important;
    appearance: menulist !important;
}

.flatpickr-calendar .flatpickr-current-month input.cur-year {
    padding-top: 0 !important;
    padding-bottom: 0 !important;
}

.hero-gallery-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 14px;
    margin-bottom: 24px;
}

.hero-gallery-item {
    width: 100%;
    aspect-ratio: 4 / 3;
    padding: 0;
    border-radius: 12px;
    border: 1px solid rgba(239, 190, 111, 0.28);
    overflow: hidden;
    background: rgba(255,255,255,0.04);
    cursor: pointer;
    position: relative;
    transition: transform .24s ease, border-color .24s ease, box-shadow .24s ease, filter .24s ease;
}

.hero-gallery-item::after {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: inherit;
    box-shadow: inset 0 0 0 1px rgba(255,255,255,0.14), inset 0 0 28px rgba(255,255,255,0.08);
    pointer-events: none;
}

.hero-gallery-item:hover,
.hero-gallery-item:focus-visible {
    transform: translateY(-3px);
    border-color: rgba(239, 190, 111, 0.46);
    box-shadow: 0 18px 34px rgba(0,0,0,0.28);
    filter: brightness(1.02);
    outline: none;
}

.hero-gallery-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.checkout-gallery-modal .modal-content {
    background: rgba(9, 13, 24, 0.96);
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 20px;
    overflow: hidden;
}

.checkout-gallery-modal .modal-header {
    border-bottom: 1px solid rgba(255,255,255,0.08);
    padding: 12px 16px;
}

.checkout-gallery-modal .btn-close {
    filter: invert(1) grayscale(1);
    opacity: .9;
}

.checkout-gallery-modal .modal-body {
    padding: 0;
    background: #030712;
}

.checkout-gallery-modal-image {
    width: 100%;
    max-height: min(82vh, 980px);
    object-fit: contain;
    display: block;
    background: #030712;
}

.aff-story,
.guest > form > section,
.guest-count,
.vip-pack,
.package > section,
.checkout-section,
.upcoming-events-card,
.location-card {
    margin: 0 0 24px;
    padding: 18px 20px;
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 16px;
    background: rgba(255,255,255,0.03);
}

.story-copy {
    font-size: 15px;
    opacity: .82;
    line-height: 1.7;
}

.story-copy-block {
    margin-bottom: 0;
}

.story-copy-toggle {
    display: none;
    margin-top: 8px;
    padding: 0;
    border: 0;
    background: transparent;
    color: var(--accent);
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .02em;
}

.story-divider {
    height: 1px;
    background: rgba(255,255,255,0.1);
    margin: 16px 0;
}

nav {
    max-width: 575px;
    margin: 0 auto 24px;
    padding: 4px;
    border-radius: 14px;
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.08);
}

nav .tab {
    border-radius: 10px;
    flex: 1 1 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: transparent;
    color: var(--text-main);
    padding: 10px 20px;
    min-height: 44px;
    line-height: 1.2;
    text-align: center;
}

nav .tab.active,
nav .tab:hover {
    background: var(--accent);
    color: #000 !important;
}

/* Package category tabs - vibrant purple */
.package-category-tiles {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 18px;
}
.package-category-wrap { flex: 1 1 auto; min-width: 0; margin-bottom: 12px; }
.package-category-tile {
    width: 100%;
    background: rgba(167,116,255,0.08) !important;
    color: rgba(255,255,255,0.88) !important;
    border: 1px solid rgba(167,116,255,0.35) !important;
    border-radius: 12px !important;
    padding: 13px 18px !important;
    font-size: 14px !important;
    font-weight: 700 !important;
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    cursor: pointer;
    transition: all .2s;
    text-align: left !important;
}
.package-category-tile:hover {
    background: rgba(167,116,255,0.16) !important;
    border-color: rgba(167,116,255,0.6) !important;
    color: #fff !important;
    transform: translateY(-1px);
}
.package-category-tile.active {
    background: linear-gradient(135deg, #a774ff 0%, #7c3aed 100%) !important;
    color: #fff !important;
    border-color: #7c3aed !important;
    box-shadow: 0 4px 14px rgba(124,58,237,0.4);
}
.package-category-tile .package-category-indicator {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: rgba(255,255,255,0.15);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: 800;
    line-height: 1;
    flex-shrink: 0;
    transition: all .2s;
}
.package-category-tile.active .package-category-indicator {
    background: rgba(255,255,255,0.25);
    transform: rotate(45deg);
}
.package-category-tile-icon {
    font-size: 13px;
    opacity: 0.85;
    margin-right: 7px;
    flex-shrink: 0;
}
/* Category color override when --cat-rgb is set */
.package-category-tile.has-cat-color {
    background: rgba(var(--cat-rgb), 0.95) !important;
    border-color: rgba(var(--cat-rgb), 0.95) !important;
}
.package-category-tile.has-cat-color:hover {
    background: rgba(var(--cat-rgb), 0.16) !important;
    border-color: rgba(var(--cat-rgb), 0.6) !important;
}
.package-category-tile.has-cat-color.active {
    background: linear-gradient(135deg, rgba(var(--cat-rgb), 0.95) 0%, rgba(var(--cat-rgb), 0.75) 100%) !important;
    border-color: rgba(var(--cat-rgb), 1) !important;
    box-shadow: 0 4px 14px rgba(var(--cat-rgb), 0.4) !important;
}
.package-category-group { margin-bottom: 16px; margin-top: 16px;}

@media (max-width: 767px) {
    .package-category-tiles { flex-direction: column; }
    .package-category-wrap { width: 100%; }
}

.vip-card.selected {
    border-color: var(--accent) !important;
    background: rgba(255,255,255,0.06) !important;
}
.vip-card-main {
    flex: 1 1 280px;
    min-width: 0;
}
.vip-title-row { display:flex; align-items:center; gap:8px; }
.vip-card-side {
    flex: 0 0 220px;
    display: grid;
    grid-template-columns: 84px minmax(110px, 1fr);
    gap: 16px;
    align-items: start;
    justify-content: end;
}
.vip-guest-control {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
}
.vip-guest-label {
    font-size: 11px;
    opacity: .6;
    margin-bottom: 4px;
}
.package_number_of_guestss {
    width: 80px !important;
    min-width: 80px;
    padding: 5px 8px !important;
    margin-bottom: 0 !important;
    text-align: center;
}
.vip-price-tag {
    min-width: 110px;
    padding-top: 18px;
    text-align: right;
    white-space: nowrap;
    font-size:18px;
    font-weight:800;
    color:var(--accent);
}
.club-detail-trigger { display:inline-flex; align-items:center; justify-content:center; width:24px; height:24px; border-radius:50%; border:1px solid rgba(255,255,255,0.18); background:rgba(255,255,255,0.07); color:var(--text-main); cursor:pointer; font-size:12px; }
.club-detail-trigger:hover { border-color:var(--accent); color:var(--accent); }
.club-popover { border: 1px solid rgba(255,255,255,0.12) !important; background: #0e1324 !important; }
.club-popover .popover-header { background:#141a2d !important; color:#fff !important; border-bottom:1px solid rgba(255,255,255,0.08) !important; font-weight:700; }
.club-popover .popover-body { background:#0e1324 !important; color:#d8def0 !important; font-size:13px; line-height:1.5; }
.club-popover .popover-header,
.club-popover .popover-body,
.club-popover .popover-body * { color:#d8def0 !important; }
.club-popover .popover-header { color:#fff !important; }

#addonSelectionModal .addon-modal-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    padding: 14px 16px;
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 14px;
    background: linear-gradient(180deg, rgba(255,255,255,0.07), rgba(255,255,255,0.03));
    margin-bottom: 10px;
}

#addonSelectionModal .addon-modal-label {
    display: block;
    color: #f4f6ff !important;
    font-size: 15px;
    font-weight: 700;
    line-height: 1.35;
    flex: 1;
}

#addonSelectionModal .addon-modal-unit {
    color: rgba(247, 226, 180, 0.95);
    font-weight: 600;
    margin-left: 6px;
}

#addonSelectionModal .addon-modal-desc {
    display: block;
    margin-top: 4px;
    color: rgba(232, 234, 246, 0.72);
    font-size: 12px;
    line-height: 1.45;
    font-weight: 500;
}

#addonSelectionModal .addon-line-total {
    display: inline-block;
    margin-top: 5px;
    color: #fff;
    font-size: 12px;
    opacity: 0.88;
}

#addonSelectionModal .addon-qty-stepper {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px;
    border-radius: 999px;
    background: rgba(8, 12, 24, 0.7);
    border: 1px solid rgba(255,255,255,0.15);
    flex-shrink: 0;
}

#addonSelectionModal .addon-qty-btn {
    background: rgba(255,255,255,0.09);
    border: 1px solid rgba(255,255,255,0.26);
    color: #fff;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    font-size: 1.2em;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
    transition: all .15s ease;
}

#addonSelectionModal .addon-qty-btn:hover {
    background: var(--aff-accent, #f7e2b4);
    color: #111;
    border-color: transparent;
}

#addonSelectionModal .addon-qty-val {
    min-width: 30px;
    text-align: center;
    font-weight: 800;
    font-size: 1rem;
    color: #fff;
}

.vip-price {
    font-size: 18px;
    font-weight: 800;
    color: var(--accent) !important;
}

.default-refundable,
.default-due {
    display: block;
    line-height: 1.4;
    margin-top: 6px;
    overflow-wrap: anywhere;
    word-break: break-word;
}

.default-refundable {
    display: flex;
    flex-wrap: wrap;
    align-items: baseline;
    column-gap: 6px;
    row-gap: 2px;
}

.default-due {
    display: flex;
    flex-wrap: wrap;
    align-items: baseline;
    column-gap: 6px;
    row-gap: 2px;
}

.default-refundable .refundable-amount,
.default-due .due-amount {
    white-space: nowrap;
}

.pay-now-tag {
    display: inline-block;
    margin-left: 0;
    padding-left: 0;
    white-space: nowrap;
    font-size: .86em;
    line-height: 1;
    vertical-align: baseline;
    color: inherit !important;
}

.dynamic-price {
    background: rgba(255,255,255,0.04);
    border-radius: 10px;
    padding: 14px 16px;
}

.guest-section {
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.12) !important;
    border-radius: 14px;
    padding: 16px 18px;
    margin-bottom: 12px;
}

.guest-count .guest-gender-row {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 6px;
}

.guest-count .guest-gender-row .guest-section {
    min-width: 0;
}

.guest-count .guest-list > h2 {
    font-size: 1rem;
    margin-bottom: 6px;
}

.guest-count .guest-section {
    padding: 4px 6px;
    margin-bottom: 4px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
}

.guest-count .guest-section .label {
    font-size: 13px;
    font-weight: 700;
    opacity: 0.9;
    margin: 0;
}

.guest-count .counter {
    margin-top: 0;
    display: flex;
    align-items: center;
}

.guest-count .guest-qty-stepper {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px;
    border-radius: 999px;
    background: rgba(8, 12, 24, 0.7);
    border: 1px solid rgba(255,255,255,0.15);
}

.guest-count .guest-qty-btn {
    background: rgba(255,255,255,0.09);
    border: 1px solid rgba(255,255,255,0.26);
    color: #fff;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    font-size: 13px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
    transition: all .15s ease;
}

.guest-count .guest-qty-btn:hover {
    background: var(--accent);
    color: #111;
    border-color: transparent;
}

.guest-count .guest-qty-val {
    min-width: 18px;
    text-align: center;
    font-weight: 800;
    font-size: 12px;
    color: #fff;
}

.section-kicker-lg {
    opacity: .6;
    font-size: .85rem;
    text-transform: uppercase;
    letter-spacing: .8px;
    font-weight: 700;
    margin-bottom: 1rem;
}

.guest-count .container,
.location-card .row,
.upcoming-events-card .event-header,
.upcoming-events-card #events-list {
    padding-left: 0;
    padding-right: 0;
}

.pricing-shell > div {
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 12px;
    padding: 16px;
}

.location-card iframe {
    width: 100%;
    min-height: 280px;
    border: 0;
    border-radius: 14px;
}

.location-card {
    overflow: hidden;
}

.location-shell {
    display: grid;
    grid-template-columns: minmax(240px, 0.95fr) minmax(0, 1.25fr);
    gap: 18px;
    align-items: stretch;
}

.location-shell > * {
    min-width: 0;
}

.location-copy {
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.09);
    border-radius: 14px;
    padding: 18px;
    min-width: 0;
}

.location-kicker {
    display: inline-block;
    font-size: 11px;
    letter-spacing: 0.9px;
    text-transform: uppercase;
    opacity: 0.65;
    margin-bottom: 8px;
}

.location-copy h2 {
    margin: 0 0 10px;
    font-size: 1.5rem;
}

.location-address {
    margin-bottom: 14px;
    opacity: 0.9;
    line-height: 1.6;
}

.location-contact-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.location-contact-chip {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 9px 12px;
    border-radius: 10px;
    border: 1px solid rgba(255,255,255,0.15);
    background: rgba(255,255,255,0.04);
    color: var(--text-main);
    text-decoration: none;
    max-width: 100%;
    min-width: 0;
    overflow-wrap: anywhere;
    word-break: break-word;
}

.location-contact-chip span {
    min-width: 0;
    white-space: normal;
    overflow-wrap: anywhere;
    word-break: break-word;
}

.location-contact-chip:hover {
    border-color: var(--accent);
    color: var(--accent);
}

.location-map-wrap {
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 14px;
    overflow: hidden;
    min-height: 320px;
    background: rgba(255,255,255,0.02);
}

.location-map-wrap iframe {
    width: 100%;
    height: 100%;
    min-height: 320px;
    border: 0;
    display: block;
}

/* Keep a single custom calendar icon in hero date input */
body #package_use_date::-webkit-calendar-picker-indicator {
    display: none !important;
    opacity: 0 !important;
    width: 0 !important;
    height: 0 !important;
}

@media(max-width:768px) {
    .container {
        padding-left: 8px;
        padding-right: 8px;
    }

    .events-section-container.container {
        padding-left: 2px;
        padding-right: 2px;
    }

    #events-list {
        margin-left: -2px;
        margin-right: -2px;
    }

    #events-list .event-card-item {
        padding-left: 2px;
        padding-right: 2px;
    }

    #events-list .event-card {
        padding-left: 0;
        padding-right: 0;
    }

    .aff-hero {
        padding: 10px 0 8px;
    }

    .aff-story,
    .guest > form > section,
    .guest-count,
    .vip-pack,
    .package > section,
    .checkout-section,
    .upcoming-events-card,
    .location-card {
        padding: 14px 12px;
    }

    .guest-count .guest-list {
        display: flex;
        flex-direction: column;
    }

    .guest-count {
        padding: 8px 6px;
    }

    .guest-count .guest-gender-row {
        grid-template-columns: 1fr;
        gap: 8px;
    }

    .guest-count .guest-section {
        padding: 8px 10px;
        gap: 10px;
    }

    .guest-count .counter {
        margin-left: auto;
        flex-shrink: 0;
    }

    .guest-count .guest-section--men {
        order: 1;
    }

    .guest-count .guest-section--women {
        order: 2;
    }

    .guest-count .guest-section--total {
        order: 3;
    }

    .back-home-btn {
        display: inline-flex;
        width: auto;
        max-width: 100%;
        min-height: 36px;
        padding: 7px 12px;
        border-radius: 12px;
        font-size: .78rem;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.16), inset 0 1px 0 rgba(255, 255, 255, 0.1);
        margin-left: auto;
        margin-right: auto;
    }

    .mobile-top-actions {
        display: none !important;
    }

    .mobile-back-home-btn {
        width: 100%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        padding: 9px 12px;
        border: 1px solid rgba(247, 226, 180, 0.28);
        border-radius: 12px;
        background: linear-gradient(145deg, rgba(247, 226, 180, 0.12), rgba(221, 183, 116, 0.1));
        color: rgba(255, 255, 255, 0.94);
        font-size: .82rem;
        font-weight: 600;
        letter-spacing: .01em;
        text-decoration: none;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.14);
    }

    .mobile-back-home-btn:active {
        transform: translateY(1px);
    }

    .mobile-back-home-btn:hover,
    .mobile-back-home-btn:focus-visible {
        color: #fff;
        border-color: rgba(247, 226, 180, 0.4);
    }

    .event-hero-layout { grid-template-columns: 1fr; gap: 14px; }
    .event-banner-wrap { order: 2; width: 100%; min-width: 0; max-width: none; height: auto; justify-self: stretch; }
    .event-banner-wrap .aff-banner { width: 100%; height: auto; min-height: 420px; }
    .event-hero-copy { order: 1; padding: 10px 8px; min-height: 0; }
    .aff-banner { width: 100%; min-height: 420px; }
    .hero-date-card { max-width: 100%; }
    .story-copy-block.is-collapsed .story-copy-collapsible {
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
        overflow: hidden;
    }
    .story-copy-toggle {
        display: inline-flex;
        align-items: center;
    }
    .package-category-tiles,
    .package-category-group,
    .vip-card,
    #cart-section,
    .pricing-shell,
    .pricing-shell > div,
    .dynamic-price {
        width: 100%;
        max-width: 100%;
        margin-left: 0;
        margin-right: 0;
        box-sizing: border-box;
    }
    .pricing-shell {
        --bs-gutter-x: 0;
        --bs-gutter-y: 12px;
    }

    .pricing-shell > div {
        padding: 14px 12px;
    }

    .default-refundable,
    .default-due {
        font-size: 14px !important;
        line-height: 1.45;
    }

    .default-refundable {
        font-size: clamp(11px, 3.2vw, 14px) !important;
        flex-wrap: nowrap;
    }

    .default-refundable .pay-now-tag {
        font-size: clamp(9px, 2.6vw, 11px);
        margin-left: 0;
        flex-shrink: 0;
    }

    .default-due {
        margin-top: 8px;
    }
    .vip-card-side { flex: 1 1 100%; }
    .hero-gallery-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .location-shell { grid-template-columns: 1fr; }
    .location-map-wrap,
    .location-map-wrap iframe { min-height: 260px; }
    .aff-display-title { margin: 2px 0 4px; }
    .aff-display-copy { font-size: 11px; }
}


@media(max-width:576px) {
    .container {
        padding-left: 6px;
        padding-right: 6px;
    }

    .events-section-container.container {
        padding-left: 0;
        padding-right: 0;
    }

    #events-list {
        margin-left: -1px;
        margin-right: -1px;
    }

    #events-list .event-card-item {
        padding-left: 1px;
        padding-right: 1px;
    }

    #events-list .event-card {
        padding-left: 0;
        padding-right: 0;
    }

    .event-hero-copy {
        padding-left: 6px;
        padding-right: 6px;
    }

    .hero-gallery-grid { grid-template-columns: 1fr; }
}

/* ====== CartVIP Redesign UI 2025 ====== */
.cv-top-nav {
    z-index:1000;
    background: linear-gradient(180deg, rgba(8,11,20,0.98) 0%, rgba(5,7,14,0.94) 100%);
    backdrop-filter:blur(14px);
    -webkit-backdrop-filter:blur(14px);
    border-bottom:1px solid rgba(167,116,255,.14);
    display:flex !important;
    align-items:center;
    justify-content: space-between !important;
    padding: 0 clamp(20px, 1vw, 48px);
    height:auto;
    min-height: 72px;
    gap: 12px;
}
.aff-nav-badges {
    display: flex !important;
    gap: 24px !important;
    align-items: center !important;
    margin: 0 auto 0 0;
}
    width: 100%;
    box-sizing: border-box;
}
.cv-top-nav::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: -1px;
    width: 100%;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(167,116,255,0.6) 30%, rgba(124,58,237,0.6) 50%, rgba(167,116,255,0.6) 70%, transparent);
    pointer-events: none;
}
.cv-nav-brand { display:flex; align-items:center; gap:12px; text-decoration:none !important; flex-shrink: 0; }
.cv-nav-logo-img { height:80px; width:auto; max-width: 180px; display:block; object-fit: contain; }
.cv-nav-logo-box {
    width:42px;
    height:42px;
    background: linear-gradient(135deg, #a774ff 0%, #7c3aed 100%);
    border-radius:10px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:900;
    font-size:15px;
    color:#fff !important;
    flex-shrink:0;
    letter-spacing: 0.02em;
    box-shadow: 0 4px 14px rgba(124,58,237,0.4), inset 0 1px 0 rgba(255,255,255,0.2);
    border: 1px solid rgba(167,116,255,0.5);
}
.cv-nav-name { font-weight:800; font-size:22px; color:#fff !important; letter-spacing:-.01em; line-height: 1; }
.cv-nav-name .cv-nav-name-accent {
    background: linear-gradient(135deg, #c4a3ff 0%, #a774ff 100%);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    color: transparent;
    font-weight: 900;
}

/* Center status block - fills the middle of the nav */
.cv-nav-center {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 24px;
    min-width: 0;
}
.cv-nav-status {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 7px 16px;
    border-radius: 999px;
    background: rgba(167,116,255,0.06);
    border: 1px solid rgba(167,116,255,0.22);
    color: rgba(255,255,255,0.85) !important;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.05em;
    text-transform: uppercase;
}
.cv-nav-status .cv-nav-status-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #4ade80;
    box-shadow: 0 0 10px rgba(74,222,128,0.7), 0 0 0 4px rgba(74,222,128,0.18);
    animation: navPulse 2s ease-in-out infinite;
}
@keyframes navPulse {
    0%, 100% { box-shadow: 0 0 10px rgba(74,222,128,0.7), 0 0 0 4px rgba(74,222,128,0.18); }
    50% { box-shadow: 0 0 14px rgba(74,222,128,0.9), 0 0 0 6px rgba(74,222,128,0.08); }
}
.cv-nav-status i { color: #67e8f9 !important; font-size: 11px; }
.cv-nav-divider {
    width: 1px;
    height: 24px;
    background: rgba(255,255,255,0.1);
}
.cv-nav-trust {
    display: inline-flex;
    align-items: center;
    gap: 18px;
    font-size: 11.5px;
    color: rgba(255,255,255,0.55);
    font-weight: 600;
}
.cv-nav-trust > span { display: inline-flex; align-items: center; gap: 6px; }
.cv-nav-trust i { color: rgba(167,116,255,0.85) !important; font-size: 12px; }

.cv-nav-back {
    display:flex;
    align-items:center;
    gap:8px;
    padding:9px 16px;
    border-radius:10px;
    background: rgba(167,116,255,0.08);
    border: 1px solid rgba(167,116,255,0.32);
    color: #c4a3ff !important;
    text-decoration:none !important;
    font-size:13.5px;
    font-weight:700;
    transition:all .15s;
    flex-shrink: 0;
}
.cv-nav-back:hover {
    background: linear-gradient(135deg, rgba(167,116,255,0.18), rgba(124,58,237,0.18));
    border-color: rgba(167,116,255,0.6);
    color: #fff !important;
    transform: translateX(-2px);
}
.cv-nav-back i { font-size: 11px; }

@media (max-width: 991px) {
    .cv-nav-trust { display: none; }
    .cv-nav-divider { display: none; }
}
@media (max-width: 767px) {
    .cv-nav-center { display: none; }
}
.cv-nav-back { display:flex; align-items:center; gap:8px; padding:11px 19px; border-radius:12px; background:rgba(255,255,255,.03); border:1px solid rgba(255,255,255,.15); color:rgba(255,255,255,.88) !important; text-decoration:none !important; font-size:15px; font-weight:600; transition:all .2s; }
.cv-nav-back:hover { background:rgba(255,255,255,.11); color:#fff !important; border-color:rgba(255,255,255,.2); }
.cv-hamburger { display:none; flex-direction:column; gap:5px; background:none; border:none; cursor:pointer; padding:4px; margin-left:auto; }
.cv-hamburger span { display:block; width:22px; height:2px; background:rgba(255,255,255,.85); border-radius:2px; }

.cv-hero-stage {
    position: relative;
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 22px;
    overflow: hidden;
    min-height: 460px;
    background-size: cover;
    background-position: center;
    padding: 28px 34px 18px;
    margin-bottom: 0;
}

.cv-hero-stage::before {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, rgba(7,10,18,0.58) 0%, rgba(7,10,18,0.84) 55%, rgba(7,10,18,0.94) 100%);
    pointer-events: none;
}

.cv-hero-inner {
    position: relative;
    z-index: 1;
    display: flex;
    flex-direction: column;
    gap: 22px;
    height: 100%;
}

@media (min-width: 1200px) {
    .cv-hero-inner {
        display: grid;
        grid-template-columns: 1fr 340px;
        grid-template-rows: auto 1fr;
        gap: 12px 20px;
        height: 100%;
    }
    .cv-hero-head {
        grid-column: 1 / -1;
        grid-row: 1;
    }
    .cv-hero-bottom {
        grid-column: 1;
        grid-row: 2;
        gap: 12px !important;
        margin: 0 !important;
    }
    .aff-hero-gallery-carousel {
        display: flex !important;
        grid-column: 2;
        grid-row: 1 / -1;
    }
}

/* ===== Hero Gallery Carousel ===== */
.aff-hero-gallery-carousel {
    border-radius: 12px;
    border: 1px solid rgba(255,255,255,0.1);
    overflow: hidden;
}
.aff-hero-carousel-item img {
    background: rgba(0,0,0,0.5);
}
.aff-hero-carousel-prev:hover,
.aff-hero-carousel-next:hover {
    background: rgba(0,0,0,0.6) !important;
}

.cv-hero-head { display:flex; justify-content:space-between; align-items:flex-start; gap:16px; }
.cv-hero-venue { display:flex; align-items:center; gap:14px; min-width:0; }
.cv-hero-venue-avatar { width:80px; height:80px; border-radius:12px; object-fit:cover; border:none; flex-shrink:0; }
.cv-hero-venue-initial { width:80px; height:80px; border-radius:12px; display:inline-flex; align-items:center; justify-content:center; font-size:28px; font-weight:900; color:#0b1020 !important; background:var(--accent); flex-shrink:0; }
.cv-hero-venue-title { font-size:24px; font-weight:700; line-height:1.2; color:#fff !important; margin:0; letter-spacing:-0.01em; display: inline-flex; align-items: center; gap: 6px; }
.cv-hero-venue-verified { display:inline-flex; align-items:center; justify-content:center; width:18px; height:18px; border-radius:50%; background:var(--accent); color:#0b1020 !important; font-size:10px; font-weight:900; }
.cv-hero-venue-meta { font-size:13px; color:rgba(255,255,255,0.62) !important; margin:3px 0 0; }
.cv-hero-rating { font-size:13px; color:rgba(255,255,255,0.78) !important; margin-top:4px; display: inline-flex; align-items: center; gap: 6px; }
.cv-hero-rating .stars { color:var(--accent) !important; letter-spacing:-1px; }
.cv-hero-badges { display:flex; gap:24px; align-items: flex-start; flex-wrap: wrap; }
.cv-hero-badge { background:transparent; border:0; border-radius:0; padding:0; display:flex; gap:10px; align-items:flex-start; }
.cv-hero-badge i { color: var(--accent) !important; font-size: 17px; margin-top: 1px; width: 22px; height: 22px; border-radius: 50%; background: rgba(255,204,0,0.1); display: inline-flex; align-items: center; justify-content: center; font-size: 11px; }
.cv-hero-badge-label { display:block; font-size:13px; color:rgba(255,255,255,0.78) !important; font-weight:600; line-height:1.25; }
.cv-hero-badge-sub { display:block; font-size:13px; color:rgba(255,255,255,0.95) !important; margin-top:2px; line-height:1.2; font-weight: 700; }
@media (max-width: 991px) {
    .cv-hero-head { flex-direction: column; gap: 14px; }
    .cv-hero-badges { width: 100%; gap: 18px; padding-top: 4px; border-top: 1px solid rgba(255,255,255,0.08); margin-top: 4px; padding-top: 14px; }
    .cv-hero-badge { flex: 1 1 auto; min-width: 0; }
    .cv-hero-badge-label, .cv-hero-badge-sub { font-size: 12px; }
}
.cv-hero-content { max-width:680px; flex: 1; min-width: 0; }
.aff-kicker { display: inline-block; font-size: 12px; font-weight: 700; letter-spacing: 0.16em; color: var(--accent) !important; text-transform: uppercase; margin-bottom: 12px; }
.cv-hero-title { font-size:clamp(36px, 4vw, 60px); line-height:1.08; letter-spacing:-0.02em; color:#fff !important; font-weight:800; margin:0 0 14px; }
.cv-hero-title-accent { color: var(--accent) !important; }
.cv-hero-subtitle { max-width:560px; font-size:15px; line-height:1.55; color:rgba(255,255,255,0.72) !important; margin-bottom:18px; }
.cv-hero-content .hero-date-card { max-width:420px; margin-top:0; background:rgba(8,11,22,0.55); border:1px solid rgba(255,255,255,0.14); border-radius:12px; padding: 14px 16px; }
.cv-hero-content .hero-date-card label { display: block; font-size: 11px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: var(--accent) !important; margin-bottom: 8px; }

/* Hero bottom row - content + location panel side by side */
.cv-hero-bottom { display: flex; gap: 32px; align-items: center; flex: 1; min-height: 0; }

/* Hero Find Us / map panel - aurora theme (rose + cyan + emerald multi-tone, blends with any bg) */
.cv-hero-location {
    flex: 0 0 460px;
    background: linear-gradient(180deg, rgba(12,8,20,0.72), rgba(6,4,14,0.85)) !important;
    backdrop-filter: blur(20px) saturate(1.6);
    -webkit-backdrop-filter: blur(20px) saturate(1.6);
    border: 1px solid rgba(255,255,255,0.18) !important;
    border-radius: 20px;
    padding: 22px;
    align-self: stretch;
    display: flex;
    flex-direction: column;
    gap: 14px;
    position: relative;
    overflow: hidden;
    box-shadow:
        0 16px 44px rgba(0,0,0,0.5),
        inset 0 1px 0 rgba(255,255,255,0.14),
        inset 0 -1px 0 rgba(251,113,133,0.18);
}
/* Top-right rose glow */
.cv-hero-location::before {
    content: '';
    position: absolute;
    right: -15%;
    top: -15%;
    width: 70%;
    height: 70%;
    background: radial-gradient(ellipse at right top, rgba(251,113,133,0.28), transparent 60%);
    pointer-events: none;
    z-index: 0;
}
/* Bottom-left cyan glow */
.cv-hero-location::after {
    content: '';
    position: absolute;
    left: -10%;
    bottom: -10%;
    width: 70%;
    height: 60%;
    background: radial-gradient(ellipse at bottom left, rgba(34,211,238,0.22), transparent 60%);
    pointer-events: none;
    z-index: 0;
}
.cv-hero-location > * { position: relative; z-index: 1; }

/* Animated gradient accent line at top */
.cv-hero-location-titles {
    flex: 1;
    min-width: 0;
    position: relative;
}

.cv-hero-location-header { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; }
.cv-hero-location-label {
    font-size: 12px;
    font-weight: 800;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    background: linear-gradient(90deg, #fb7185 0%, #f472b6 50%, #22d3ee 100%);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    color: transparent !important;
    margin: 0 0 6px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.cv-hero-location-label::before {
    content: '\f3c5';
    font-family: 'Font Awesome 6 Free';
    font-weight: 900;
    font-size: 11px;
    color: #fb7185;
    -webkit-text-fill-color: #fb7185;
    background: none;
}
.cv-hero-location-name { font-size: 19px; font-weight: 800; color: #fff !important; line-height: 1.25; letter-spacing: -0.01em; }
.cv-hero-location-addr { font-size: 13.5px; color: rgba(255,255,255,0.78) !important; line-height: 1.5; margin-top: 4px; }
.cv-hero-location-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 6px 12px;
    border-radius: 999px;
    background: linear-gradient(135deg, rgba(251,113,133,0.18), rgba(34,211,238,0.18));
    border: 1px solid transparent;
    background-clip: padding-box;
    color: #fff !important;
    font-size: 10.5px;
    font-weight: 800;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    flex-shrink: 0;
    align-self: flex-start;
    box-shadow: 0 0 0 1px rgba(251,113,133,0.45), 0 4px 12px rgba(251,113,133,0.15);
}
.cv-hero-location-badge i { font-size: 9px; color: #fb7185; }

.cv-hero-location-map {
    flex: 1;
    min-height: 230px;
    border-radius: 14px;
    overflow: hidden;
    border: 1px solid rgba(255,255,255,0.12);
    background: rgba(0,0,0,0.4);
    position: relative;
    box-shadow: 0 0 0 1px rgba(251,113,133,0.15), 0 0 0 2px rgba(34,211,238,0.06);
}
.cv-hero-location-map iframe { width: 100%; height: 100%; min-height: 230px; border: 0; display: block; filter: brightness(0.85) contrast(1.08) saturate(0.95); }

.cv-hero-location-contacts { display: flex; flex-direction: column; gap: 8px; }
.cv-hero-location-contact {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    font-size: 13px;
    color: rgba(255,255,255,0.92) !important;
    text-decoration: none !important;
    padding: 10px 14px;
    border-radius: 10px;
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.14);
    transition: all .18s;
    font-weight: 600;
    position: relative;
    overflow: hidden;
}
.cv-hero-location-contact:hover {
    background: linear-gradient(90deg, rgba(251,113,133,0.12), rgba(34,211,238,0.08));
    color: #fff !important;
    border-color: rgba(251,113,133,0.55);
    transform: translateX(3px);
    box-shadow: 0 4px 14px rgba(251,113,133,0.18);
}
.cv-hero-location-contact i {
    color: #fb7185 !important;
    font-size: 13px;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: linear-gradient(135deg, rgba(251,113,133,0.22), rgba(34,211,238,0.12));
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: inset 0 0 0 1px rgba(251,113,133,0.32);
}
.cv-hero-location-contact:nth-child(even) i {
    color: #22d3ee !important;
    background: linear-gradient(135deg, rgba(34,211,238,0.22), rgba(251,113,133,0.12));
    box-shadow: inset 0 0 0 1px rgba(34,211,238,0.32);
}

@media (max-width: 1199px) {
    .cv-hero-location { flex: 0 0 400px; }
    .cv-hero-location-map { min-height: 200px; }
}

@media (max-width: 991px) {
    .cv-hero-bottom { flex-direction: column; align-items: stretch; gap: 18px; }
    .cv-hero-location { flex: 0 0 auto; }
    .cv-hero-location-map { min-height: 200px; }
}

/* Upcoming Events section - dark theme matching the page */
.events-section-container { padding: 48px 0 !important; }
.events-section-container .event-header { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px; margin-bottom: 28px; padding-bottom: 0; border-bottom: none; }
.events-section-container .event-header h2 { font-size: 26px; font-weight: 800; color: #fff !important; margin: 0; letter-spacing: -0.01em; }
.events-section-container .event-filters { display: flex; gap: 8px; flex-wrap: wrap; }
.events-section-container .event-filters .event-filter {
    background: rgba(255,255,255,0.03) !important;
    border: 1px solid rgba(255,255,255,0.14) !important;
    color: rgba(255,255,255,0.78) !important;
    padding: 8px 14px !important;
    border-radius: 999px !important;
    font-size: 12.5px !important;
    font-weight: 600 !important;
    cursor: pointer;
    transition: all .15s;
}
.events-section-container .event-filters .event-filter:hover,
.events-section-container .event-filters .event-filter.active {
    background: rgba(255,204,0,0.08) !important;
    border-color: var(--accent) !important;
    color: var(--accent) !important;
}
.events-section-container .event-card-item { padding: 0; margin-bottom: 20px; }
.events-section-container .event-card {
    display: block;
    background: linear-gradient(180deg, rgba(36,18,58,0.85), rgba(18,10,32,0.95)) !important;
    border: 1px solid rgba(167,116,255,0.28) !important;
    border-radius: 20px !important;
    overflow: hidden;
    transition: all .25s ease;
    text-decoration: none !important;
    width: 100%;
    position: relative;
    box-shadow: 0 10px 32px rgba(0,0,0,0.32);
}
.events-section-container .event-card::before {
    content: '';
    position: absolute;
    right: -10%; top: -20%;
    width: 50%; height: 80%;
    background: radial-gradient(ellipse at right top, rgba(167,116,255,0.18), transparent 70%);
    pointer-events: none;
    z-index: 0;
    opacity: 0.6;
    transition: opacity .25s;
}
.events-section-container .event-card:hover {
    border-color: rgba(167,116,255,0.6) !important;
    transform: translateY(-4px);
    box-shadow: 0 20px 48px rgba(0,0,0,0.5), 0 0 0 1px rgba(167,116,255,0.32), 0 0 36px rgba(124,58,237,0.12);
}
.events-section-container .event-card:hover::before { opacity: 1; }

/* Vertical card layout: image on top, content below */
.events-section-container .event-card .card {
    background: transparent !important;
    border: none !important;
    padding: 0 !important;
    text-align: left !important;
    height: 100%;
    display: flex !important;
    flex-direction: column;
    position: relative;
    z-index: 1;
}
.events-section-container .event-card .card > img {
    width: 100% !important;
    height: 220px !important;
    object-fit: cover;
    display: block;
    border-radius: 0 !important;
    margin: 0;
}
.events-section-container .event-card .card > .d-flex {
    padding: 18px 20px 4px;
    align-items: center;
    gap: 12px;
}
.events-section-container .event-card .event-day {
    font-size: 13px;
    font-weight: 700;
    color: rgba(255,255,255,0.6) !important;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    width: auto !important;
}
.events-section-container .event-card .event-dates {
    font-size: 12px;
    font-weight: 800;
    color: #c4a3ff !important;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    width: auto !important;
    margin-left: auto;
    text-align: center;
    line-height: 1.1;
    background: rgba(167,116,255,0.14);
    border: 1px solid rgba(167,116,255,0.4);
    padding: 8px 14px;
    border-radius: 12px;
    min-width: 64px;
}
.events-section-container .event-card .event-dates span { font-size: 20px; display: block; margin-top: 3px; color: #fff !important; font-weight: 900; }
.events-section-container .event-card .event-dates span br { display: none; }

.events-section-container .event-card .event-location {
    font-size: 13px;
    color: rgba(255,255,255,0.72) !important;
    padding: 2px 20px;
    margin-top: 4px;
    line-height: 1.4;
    display: flex;
    align-items: center;
    gap: 6px;
}
.events-section-container .event-card .event-location:first-of-type {
    color: #fff !important;
    font-size: 19px;
    font-weight: 800;
    line-height: 1.25;
    margin-top: 6px;
    margin-bottom: 4px;
    padding: 0 20px;
    letter-spacing: -0.01em;
}
.events-section-container .event-card .event-location i { color: #c4a3ff !important; margin-right: 4px; width: 14px; }
.events-section-container .event-card .event-location:last-child {
    margin-top: auto;
    padding: 14px 20px 16px;
    color: #fff !important;
    font-weight: 800;
    font-size: 12.5px;
    display: flex;
    align-items: center;
    gap: 10px;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    border-top: 1px solid rgba(167,116,255,0.18);
    margin-left: 0;
    margin-right: 0;
}
.events-section-container .event-card .event-location:last-child::after {
    content: '\f061';
    font-family: 'Font Awesome 6 Free';
    font-weight: 900;
    font-size: 10px;
    color: #fff;
    width: 26px;
    height: 26px;
    border-radius: 50%;
    background: linear-gradient(135deg, #a774ff 0%, #7c3aed 100%);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 14px rgba(124,58,237,0.4);
    margin-left: auto;
}
.events-section-container .event-capacity-chip {
    margin: 12px 20px 16px;
    padding: 7px 12px;
    border-radius: 999px;
    background: rgba(34,197,94,0.14);
    border: 1px solid rgba(34,197,94,0.4);
    color: #4ade80 !important;
    font-size: 11.5px;
    font-weight: 700;
    width: fit-content;
    display: inline-flex;
    align-items: center;
}
.events-section-container .event-capacity-chip.sold-out {
    background: rgba(255,96,96,0.14);
    border-color: rgba(255,96,96,0.4);
    color: #ffb4b4 !important;
}

@media (max-width: 991px) {
    .events-section-container .event-card .card > img { height: 200px !important; }
}
@media (max-width: 767px) {
    .events-section-container .event-header h2 { font-size: 22px; }
    .events-section-container .event-card .card > img { height: 180px !important; }
}

/* ====== ADDON MODAL - vibrant purple package-style ====== */
#addonSelectionModal .modal-content {
    background: linear-gradient(180deg, rgba(36,18,58,0.96), rgba(18,10,32,0.98)) !important;
    border: 1px solid rgba(167,116,255,0.4) !important;
    border-radius: 20px !important;
    box-shadow: 0 20px 60px rgba(0,0,0,0.5), 0 0 0 1px rgba(167,116,255,0.18) !important;
    color: #f4f6ff !important;
    position: relative;
    overflow: hidden;
}
#addonSelectionModal .modal-content::before {
    content: '';
    position: absolute;
    right: -10%; top: -10%;
    width: 60%; height: 50%;
    background: radial-gradient(ellipse at right top, rgba(167,116,255,0.18), transparent 65%);
    pointer-events: none;
    z-index: 0;
}
#addonSelectionModal .modal-content > * { position: relative; z-index: 1; }
#addonSelectionModal .modal-header {
    border-bottom: 1px solid rgba(167,116,255,0.18) !important;
    padding: 20px 24px !important;
}
#addonSelectionModal .modal-title {
    color: #fff !important;
    font-size: 20px !important;
    font-weight: 800 !important;
    letter-spacing: -0.01em;
}
#addonSelectionModal .modal-body { padding: 20px 24px !important; }
#addonSelectionModal .modal-footer {
    border-top: 1px solid rgba(167,116,255,0.18) !important;
    padding: 16px 24px !important;
    gap: 12px;
}
#addonSelectionModal .addon-modal-row {
    background: linear-gradient(180deg, rgba(167,116,255,0.08), rgba(167,116,255,0.02)) !important;
    border: 1px solid rgba(167,116,255,0.22) !important;
    border-radius: 14px !important;
    transition: all .15s;
}
#addonSelectionModal .addon-modal-row:hover {
    border-color: rgba(167,116,255,0.5) !important;
    background: linear-gradient(180deg, rgba(167,116,255,0.12), rgba(167,116,255,0.04)) !important;
}
#addonSelectionModal .addon-modal-unit { color: #c4a3ff !important; }
#addonSelectionModal .addon-qty-stepper {
    background: rgba(0,0,0,0.4) !important;
    border-color: rgba(167,116,255,0.32) !important;
}
#addonSelectionModal .addon-qty-btn:hover {
    background: linear-gradient(135deg, #a774ff 0%, #7c3aed 100%) !important;
    color: #fff !important;
    border-color: transparent !important;
}
#addonModalConfirmBtn {
    background: linear-gradient(135deg, #a774ff 0%, #7c3aed 50%, #5b21b6 100%) !important;
    color: #fff !important;
    font-weight: 800 !important;
    border: none !important;
    border-radius: 12px !important;
    padding: 11px 22px !important;
    font-size: 14px !important;
    box-shadow: 0 4px 16px rgba(124,58,237,0.35) !important;
    transition: all .15s !important;
}
#addonModalConfirmBtn:hover { filter: brightness(1.1); transform: translateY(-1px); }
#addonSelectionModal .btn-secondary {
    background: rgba(255,255,255,0.05) !important;
    border: 1px solid rgba(255,255,255,0.18) !important;
    color: rgba(255,255,255,0.85) !important;
    border-radius: 12px !important;
    font-weight: 600 !important;
    padding: 11px 20px !important;
}
#addonSelectionModal .btn-close-white,
#addonSelectionModal .btn-close { filter: invert(1) brightness(1.5); }

/* ====== Total row strict override (defeat .vip-price gold) ====== */
#cv-order-sidebar .pricing-shell .default-deposit,
#cv-order-sidebar .pricing-shell .default-deposit * {
    color: #fff !important;
    background: transparent !important;
    border-left: none !important;
    border-right: none !important;
    text-decoration: none !important;
    text-shadow: none !important;
}
#cv-order-sidebar .pricing-shell .default-deposit::before,
#cv-order-sidebar .pricing-shell .default-deposit::after { display: none !important; content: none !important; }

/* ====== Share link button - VIBRANT purple gradient ====== */
#cv-order-sidebar #shareLinkContainer,
.cv-main-col #shareLinkContainer { margin-top: 16px; padding-top: 16px; border-top: 1px solid rgba(255,255,255,0.08); }
#cv-order-sidebar #generateShareLink,
.cv-main-col #generateShareLink,
#generateShareLink {
    background: linear-gradient(135deg, #a774ff 0%, #7c3aed 50%, #5b21b6 100%) !important;
    color: #fff !important;
    border: none !important;
    border-radius: 12px !important;
    padding: 12px 18px !important;
    font-size: 13px !important;
    font-weight: 800 !important;
    transition: all .15s !important;
    cursor: pointer;
    width: 100%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    text-transform: none;
    letter-spacing: 0.01em;
    box-shadow: 0 4px 16px rgba(124,58,237,0.35);
    text-decoration: none !important;
}
#cv-order-sidebar #generateShareLink:hover,
.cv-main-col #generateShareLink:hover,
#generateShareLink:hover {
    filter: brightness(1.1) !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 8px 22px rgba(124,58,237,0.5) !important;
    color: #fff !important;
    background: linear-gradient(135deg, #a774ff 0%, #7c3aed 50%, #5b21b6 100%) !important;
}
#generateShareLink::before {
    content: '\f1e0';
    font-family: 'Font Awesome 6 Free';
    font-weight: 900;
    font-size: 13px;
}
#shareableLink {
    background: rgba(255,255,255,0.04) !important;
    border: 1px solid rgba(255,255,255,0.14) !important;
    color: #fff !important;
    border-radius: 10px !important;
    padding: 10px 12px !important;
    font-size: 12px !important;
    margin-top: 8px !important;
}
.checkout-share-btn {
    background: rgba(255,255,255,0.04) !important;
    color: rgba(255,255,255,0.78) !important;
    border: 1px solid rgba(255,255,255,0.14) !important;
    padding: 6px 12px !important;
    border-radius: 999px !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    cursor: pointer;
    transition: all .15s;
}
.checkout-share-btn:hover {
    background: rgba(255,204,0,0.08) !important;
    border-color: rgba(255,204,0,0.4) !important;
    color: var(--accent) !important;
}

/* ====== Payment process: form sections theme (package-card style) ====== */
.checkout-section.holder-info,
.checkout-section.transport,
.checkout-section.payment-section,
.checkout-section[id^="section-"] {
    background: linear-gradient(180deg, rgba(36,18,58,0.85), rgba(18,10,32,0.95)) !important;
    border: 1px solid rgba(167,116,255,0.28) !important;
    border-radius: 20px !important;
    padding: 30px 32px !important;
    margin-top: 24px !important;
    margin-bottom: 8px !important;
    position: relative;
    overflow: hidden;
    box-shadow: 0 12px 38px rgba(0,0,0,0.32);
}
.checkout-section[id^="section-"]::before {
    content: '';
    position: absolute;
    right: -10%; top: -10%;
    width: 50%; height: 60%;
    background: radial-gradient(ellipse at right top, rgba(167,116,255,0.14), transparent 65%);
    pointer-events: none;
    z-index: 0;
}
.checkout-section[id^="section-"] > * { position: relative; z-index: 1; }

.checkout-section[id^="section-"] h2 {
    color: #fff !important;
    font-size: 24px !important;
    font-weight: 800 !important;
    letter-spacing: -0.015em !important;
    margin-bottom: 6px !important;
    line-height: 1.2 !important;
    display: flex;
    align-items: center;
    gap: 14px;
    flex-wrap: wrap;
}
.checkout-section[id^="section-"] h2::before {
    content: '';
    width: 4px;
    height: 28px;
    background: linear-gradient(180deg, #c4a3ff, #7c3aed);
    border-radius: 2px;
    box-shadow: 0 0 12px rgba(124,58,237,0.5);
}
.checkout-section[id^="section-"] h2 span {
    display: block !important;
    color: rgba(255,255,255,0.62) !important;
    font-size: 13px !important;
    font-weight: 500 !important;
    line-height: 1.55 !important;
    margin-top: 10px !important;
    margin-bottom: 10px !important;
    padding-left: 18px;
    border-left: 2px solid rgba(167,116,255,0.3);
    flex-basis: 100%;
}
.checkout-section[id^="section-"] label {
    color: rgba(255,255,255,0.78) !important;
    font-size: 12.5px !important;
    font-weight: 600 !important;
    margin-bottom: 6px !important;
    display: block !important;
    text-transform: none;
    letter-spacing: 0;
}
.checkout-section[id^="section-"] .form-row {
    display: flex !important;
    gap: 14px !important;
    margin-bottom: 14px !important;
}
.checkout-section[id^="section-"] .form-row .form-group {
    flex: 1 1 0 !important;
    min-width: 0 !important;
}
.checkout-section[id^="section-"] input[type="text"],
.checkout-section[id^="section-"] input[type="email"],
.checkout-section[id^="section-"] input[type="tel"],
.checkout-section[id^="section-"] input[type="number"],
.checkout-section[id^="section-"] textarea,
.checkout-section[id^="section-"] select.form-select {
    background: rgba(255,255,255,0.03) !important;
    border: 1px solid rgba(255,255,255,0.14) !important;
    border-radius: 10px !important;
    color: #fff !important;
    padding: 12px 14px !important;
    font-size: 14px !important;
    width: 100% !important;
    min-height: 46px !important;
    transition: border-color .15s, background .15s;
}
.checkout-section[id^="section-"] input:focus,
.checkout-section[id^="section-"] textarea:focus,
.checkout-section[id^="section-"] select:focus {
    outline: none !important;
    border-color: #a774ff !important;
    background: rgba(255,255,255,0.05) !important;
    box-shadow: 0 0 0 3px rgba(167,116,255,0.16) !important;
}
.checkout-section[id^="section-"] input::placeholder,
.checkout-section[id^="section-"] textarea::placeholder {
    color: rgba(255,255,255,0.32) !important;
}
.checkout-section[id^="section-"] textarea {
    min-height: 90px !important;
    resize: vertical;
}

/* DOB / inline form-row inner selects */
.checkout-section[id^="section-"] .form-group .form-row {
    margin-bottom: 0 !important;
    gap: 8px !important;
}
.checkout-section[id^="section-"] .form-group .form-row select {
    flex: 1 !important;
}

/* Step navigation buttons */
.step-navigation {
    margin-top: 24px !important;
    display: flex !important;
    gap: 12px !important;
    flex-wrap: wrap !important;
    align-items: center !important;
    justify-content: flex-end !important;
}
.btn-next, .submit-btn {
    background: linear-gradient(135deg, #a774ff 0%, #7c3aed 50%, #5b21b6 100%) !important;
    color: #fff !important;
    border: none !important;
    border-radius: 12px !important;
    padding: 12px 26px !important;
    font-weight: 800 !important;
    font-size: 14px !important;
    cursor: pointer !important;
    transition: all .15s !important;
    min-width: 180px !important;
    box-shadow: 0 6px 20px rgba(124,58,237,0.4) !important;
    display: inline-flex !important;
    align-items: center;
    justify-content: center;
    gap: 8px;
}
.btn-next:hover, .submit-btn:hover {
    filter: brightness(1.1);
    transform: translateY(-1px);
    box-shadow: 0 10px 26px rgba(124,58,237,0.55) !important;
}
.btn-next:disabled, .submit-btn:disabled {
    opacity: 0.45 !important;
    cursor: not-allowed !important;
    transform: none !important;
    filter: none !important;
    box-shadow: none !important;
}
.btn-prev {
    background: rgba(255,255,255,0.04) !important;
    color: rgba(255,255,255,0.85) !important;
    border: 1px solid rgba(255,255,255,0.16) !important;
    border-radius: 12px !important;
    padding: 12px 22px !important;
    font-weight: 600 !important;
    font-size: 14px !important;
    cursor: pointer !important;
    transition: all .15s !important;
    min-width: 140px !important;
}
.btn-prev:hover {
    background: rgba(255,255,255,0.08) !important;
    color: #fff !important;
    border-color: rgba(255,255,255,0.28) !important;
}

/* "Same as info" helper buttons inside the checkout flow - vibrant purple */
.same-as-info, .same-as-info-transport {
    background: rgba(167,116,255,0.12) !important;
    color: #c4a3ff !important;
    border: 1px solid rgba(167,116,255,0.4) !important;
    border-radius: 10px !important;
    padding: 9px 16px !important;
    font-size: 12.5px !important;
    font-weight: 700 !important;
    cursor: pointer;
    transition: all .15s;
    margin-bottom: 16px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    width: auto !important;
    min-width: 0 !important;
}
.same-as-info:hover, .same-as-info-transport:hover {
    background: rgba(167,116,255,0.22) !important;
    border-color: #a774ff !important;
    color: #fff !important;
    transform: none !important;
}

/* Transportation confirmation checkbox container */
.checkbox-container.transportaiton,
#transport-confirmation {
    background: linear-gradient(180deg, rgba(18,22,42,0.65), rgba(10,12,26,0.78)) !important;
    border: 1px solid rgba(255,255,255,0.1) !important;
    border-radius: 18px !important;
    padding: 24px 28px !important;
    margin-top: 20px !important;
}
.checkbox-container.transportaiton label {
    color: rgba(255,255,255,0.85) !important;
    font-size: 14px !important;
    line-height: 1.55 !important;
    display: flex !important;
    align-items: flex-start !important;
    gap: 12px !important;
}

/* Payment section StripeElement styling fixes */
.checkout-section[id^="section-"] .StripeElement {
    background: rgba(255,255,255,0.03) !important;
    border: 1px solid rgba(255,255,255,0.14) !important;
    border-radius: 10px !important;
    padding: 14px !important;
    min-height: 46px !important;
}
.checkout-section[id^="section-"] .StripeElement--focus,
.checkout-section[id^="section-"] .StripeElement--focused {
    border-color: #a774ff !important;
    background: rgba(255,255,255,0.05) !important;
    box-shadow: 0 0 0 3px rgba(167,116,255,0.16) !important;
}

/* Pick-up time — Flatpickr visual time picker (desktop) */
.checkout-section[id^="section-"] .pickup-time-wrap {
    position: relative;
    width: 100%;
    max-width: 100%;
}
.checkout-section[id^="section-"] .pickup-time-icon {
    position: absolute;
    left: 13px;
    top: 50%;
    transform: translateY(-50%);
    color: rgba(255,255,255,0.45);
    font-size: 14px;
    pointer-events: none;
    z-index: 2;
}
.checkout-section[id^="section-"] #Pick-up-time,
.checkout-section[id^="section-"] input[name="transportation_pickup_time"] {
    background: rgba(255,255,255,0.03) !important;
    border: 1px solid rgba(255,255,255,0.14) !important;
    color: #fff !important;
    -webkit-text-fill-color: #fff !important;
    border-radius: 10px !important;
    padding: 12px 14px 12px 40px !important;
    font-size: 15px !important;
    min-height: 46px !important;
    width: 100% !important;
    max-width: 100% !important;
    height: auto !important;
    cursor: pointer;
    font-family: inherit;
}
.checkout-section[id^="section-"] #Pick-up-time::placeholder,
.checkout-section[id^="section-"] input[name="transportation_pickup_time"]::placeholder { color: rgba(255,255,255,0.35) !important; }
.checkout-section[id^="section-"] input[name="transportation_pickup_time"]:focus {
    outline: none !important;
    border-color: #a774ff !important;
    background: rgba(255,255,255,0.05) !important;
    box-shadow: 0 0 0 3px rgba(167,116,255,0.16) !important;
}
/* Flatpickr time-only popup — desktop theme */
.flatpickr-calendar.hasTime.noCalendar {
    background: #1a1d2e !important;
    border: 1px solid rgba(167,116,255,0.4) !important;
    border-radius: 16px !important;
    box-shadow: 0 24px 64px rgba(0,0,0,0.6), 0 0 0 1px rgba(167,116,255,0.12) !important;
    padding: 22px 20px !important;
    min-width: 288px !important;
    width: auto !important;
    max-width: min(340px, calc(100vw - 32px)) !important;
}
.flatpickr-calendar.hasTime.noCalendar .flatpickr-time {
    border-top: none !important;
    background: transparent !important;
    max-height: none !important;
    height: auto !important;
    line-height: 1 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 8px !important;
}
.flatpickr-time input.numInput,
.flatpickr-time .flatpickr-am-pm {
    color: #fff !important;
    background: rgba(255,255,255,0.07) !important;
    border: 1px solid rgba(255,255,255,0.14) !important;
    border-radius: 10px !important;
    font-size: 18px !important;
    font-weight: 700 !important;
    height: 54px !important;
    min-height: 54px !important;
    line-height: 54px !important;
    padding: 0 8px !important;
    text-align: center !important;
    overflow: visible !important;
    box-sizing: border-box !important;
}
.flatpickr-time .numInputWrapper {
    height: 54px !important;
    display: flex !important;
    align-items: center !important;
}
.flatpickr-time input.numInput {
    min-width: 60px !important;
    width: 100% !important;
    display: block !important;
    appearance: textfield !important;
    -moz-appearance: textfield !important;
    padding: 0 8px 2px !important;
}
.flatpickr-time .flatpickr-am-pm {
    min-width: 72px !important;
    width: 72px !important;
    padding: 0 8px !important;
}
.flatpickr-time input.numInput:focus,
.flatpickr-time .flatpickr-am-pm:focus,
.flatpickr-time .flatpickr-am-pm:hover {
    background: rgba(167,116,255,0.18) !important;
    border-color: #a774ff !important;
    outline: none !important;
    color: #fff !important;
}
.flatpickr-time .numInputWrapper span.arrowUp:after { border-bottom-color: rgba(255,255,255,0.75) !important; }
.flatpickr-time .numInputWrapper span.arrowDown:after { border-top-color: rgba(255,255,255,0.75) !important; }
.flatpickr-time .numInputWrapper span:hover { background: rgba(167,116,255,0.2) !important; }
.flatpickr-time .flatpickr-time-separator { color: rgba(255,255,255,0.6) !important; font-size: 20px !important; font-weight: 700 !important; }
@media (max-width: 767px) {
    .checkout-section[id^="section-"] .pickup-time-wrap,
    .checkout-section[id^="section-"] input[name="transportation_pickup_time"] {
        max-width: 100% !important;
    }
}

/* Consent rows inside the final review section */
.checkout-section[id^="section-"] .checkbox-container .consent-label {
    color: rgba(255,255,255,0.78) !important;
    background: rgba(255,255,255,0.025);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 10px;
    padding: 12px 14px;
    margin-bottom: 8px;
    font-size: 13px !important;
}
.checkout-section[id^="section-"] .checkbox-container .consent-label a { color: #c4a3ff !important; text-decoration: underline !important; }

/* Mobile tweaks for checkout sections */
@media (max-width: 767px) {
    .checkout-section[id^="section-"] { padding: 20px !important; }
    .checkout-section[id^="section-"] .form-row { flex-direction: column !important; gap: 12px !important; }
    .step-navigation { justify-content: stretch !important; }
    .btn-next, .submit-btn, .btn-prev { min-width: 100% !important; flex: 1 1 100% !important; }
    .checkout-section[id^="section-"] .form-row .form-group { width: 100% !important; }
}

/* ====== Guest reservation form - vibrant package-card style ====== */
.guest > form > section:not(.location-card):not(.guest-count) {
    background: linear-gradient(180deg, rgba(36,18,58,0.85), rgba(18,10,32,0.95)) !important;
    border: 1px solid rgba(167,116,255,0.28) !important;
    border-radius: 20px !important;
    padding: 30px 32px !important;
    margin-top: 24px !important;
    margin-bottom: 16px !important;
    position: relative;
    overflow: hidden;
    box-shadow: 0 12px 38px rgba(0,0,0,0.32);
}
.guest > form > section:not(.location-card):not(.guest-count)::before {
    content: '';
    position: absolute;
    right: -10%; top: -10%;
    width: 50%; height: 60%;
    background: radial-gradient(ellipse at right top, rgba(167,116,255,0.14), transparent 65%);
    pointer-events: none;
    z-index: 0;
}
.guest > form > section:not(.location-card):not(.guest-count) > * { position: relative; z-index: 1; }

.guest .section-kicker-lg {
    color: #fff !important;
    font-size: 24px !important;
    font-weight: 800 !important;
    letter-spacing: -0.015em !important;
    margin-bottom: 18px !important;
    line-height: 1.2 !important;
    display: flex;
    align-items: center;
    gap: 14px;
}
.guest .section-kicker-lg::before {
    content: '';
    width: 4px;
    height: 28px;
    background: linear-gradient(180deg, #c4a3ff, #7c3aed);
    border-radius: 2px;
    box-shadow: 0 0 12px rgba(124,58,237,0.5);
}

.guest .form-row {
    display: flex !important;
    gap: 14px !important;
    margin-bottom: 14px !important;
}
.guest .form-row .form-group { flex: 1 1 0 !important; min-width: 0 !important; }

.guest label {
    color: rgba(255,255,255,0.78) !important;
    font-size: 12.5px !important;
    font-weight: 600 !important;
    margin-bottom: 6px !important;
    display: block !important;
}

.guest input[type="text"],
.guest input[type="email"],
.guest input[type="tel"],
.guest input[type="number"],
.guest textarea,
.guest select.form-select {
    background: rgba(255,255,255,0.03) !important;
    border: 1px solid rgba(255,255,255,0.14) !important;
    border-radius: 10px !important;
    color: #fff !important;
    padding: 12px 14px !important;
    font-size: 14px !important;
    width: 100% !important;
    min-height: 46px !important;
    transition: border-color .15s, background .15s;
}
.guest input:focus,
.guest textarea:focus,
.guest select:focus {
    outline: none !important;
    border-color: #a774ff !important;
    background: rgba(255,255,255,0.05) !important;
    box-shadow: 0 0 0 3px rgba(167,116,255,0.16) !important;
}
.guest input::placeholder,
.guest textarea::placeholder {
    color: rgba(255,255,255,0.32) !important;
}
.guest textarea { min-height: 90px !important; resize: vertical; }

/* Guest count card - same vibrant package style */
.guest .guest-count {
    background: linear-gradient(180deg, rgba(36,18,58,0.85), rgba(18,10,32,0.95)) !important;
    border: 1px solid rgba(167,116,255,0.28) !important;
    border-radius: 20px !important;
    padding: 30px 32px !important;
    margin-top: 24px !important;
    margin-bottom: 16px !important;
    position: relative;
    overflow: hidden;
    box-shadow: 0 12px 38px rgba(0,0,0,0.32);
}
.guest .guest-count::before {
    content: '';
    position: absolute;
    right: -10%; top: -10%;
    width: 50%; height: 60%;
    background: radial-gradient(ellipse at right top, rgba(167,116,255,0.14), transparent 65%);
    pointer-events: none;
    z-index: 0;
}
.guest .guest-count > .container { position: relative; z-index: 1; padding: 0 !important; }
.guest .guest-count .guest-list h2 {
    color: #fff !important;
    font-size: 22px !important;
    font-weight: 800 !important;
    letter-spacing: -0.015em !important;
    margin: 0 0 18px !important;
    display: flex;
    align-items: center;
    gap: 14px;
}
.guest .guest-count .guest-list h2::before {
    content: '';
    width: 4px;
    height: 26px;
    background: linear-gradient(180deg, #c4a3ff, #7c3aed);
    border-radius: 2px;
    box-shadow: 0 0 12px rgba(124,58,237,0.5);
}
.guest .guest-gender-row {
    display: grid !important;
    grid-template-columns: repeat(3, 1fr);
    gap: 14px;
    margin-bottom: 18px;
}
.guest .guest-section {
    background: linear-gradient(180deg, rgba(167,116,255,0.08), rgba(167,116,255,0.02)) !important;
    border: 1px solid rgba(167,116,255,0.32) !important;
    border-radius: 14px !important;
    padding: 18px 16px !important;
    text-align: center;
    transition: all .15s;
}
.guest .guest-section .label {
    display: block !important;
    color: rgba(255,255,255,0.7) !important;
    font-size: 12.5px !important;
    font-weight: 700 !important;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    margin-bottom: 12px;
}
.guest .guest-section .counter {
    display: flex;
    justify-content: center;
}
.guest .addon-qty-stepper.guest-qty-stepper {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: rgba(0,0,0,0.4);
    border: 1px solid rgba(167,116,255,0.32);
    border-radius: 999px;
    padding: 4px;
}
.guest .addon-qty-btn.guest-qty-btn {
    background: linear-gradient(135deg, #a774ff 0%, #7c3aed 100%) !important;
    color: #fff !important;
    border: none !important;
    width: 34px !important;
    height: 34px !important;
    border-radius: 50% !important;
    font-size: 1.2em !important;
    font-weight: 700 !important;
    cursor: pointer;
    transition: all .15s;
    box-shadow: 0 2px 8px rgba(124,58,237,0.4);
}
.guest .addon-qty-btn.guest-qty-btn:hover { filter: brightness(1.15); transform: scale(1.05); }
.guest .addon-qty-val.guest-qty-val {
    min-width: 32px;
    text-align: center;
    color: #fff !important;
    font-weight: 800;
    font-size: 18px;
}
.guest .guest-section--total .addon-qty-stepper.guest-qty-stepper { padding: 0; background: transparent; border: none; }

/* Mobile responsive for guest counter */
@media (max-width: 767px) {
    .guest .guest-gender-row {
        grid-template-columns: 1fr !important;
        gap: 10px !important;
    }
    .guest .guest-section {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        padding: 14px 16px !important;
        gap: 12px !important;
        text-align: left !important;
        flex-wrap: nowrap !important;
    }
    .guest .guest-section .label {
        margin-bottom: 0 !important;
        font-size: 12.5px !important;
        flex: 1 1 auto;
        min-width: 0;
    }
    .guest .guest-section .counter {
        flex: 0 0 auto;
        margin-left: auto;
    }
    .guest .addon-qty-stepper.guest-qty-stepper {
        gap: 8px;
        padding: 3px;
    }
    .guest .addon-qty-btn.guest-qty-btn {
        width: 30px !important;
        height: 30px !important;
        font-size: 1.1em !important;
    }
    .guest .addon-qty-val.guest-qty-val {
        min-width: 26px;
        font-size: 16px;
    }
}

/* Guest consent block + submit area */
.guest .checkbox-container {
    background: linear-gradient(180deg, rgba(36,18,58,0.85), rgba(18,10,32,0.95)) !important;
    border: 1px solid rgba(167,116,255,0.28) !important;
    border-radius: 20px !important;
    padding: 24px 28px !important;
    margin-top: 8px !important;
    margin-bottom: 16px !important;
    position: relative;
    overflow: hidden;
}
.guest .checkbox-container .consent-label {
    display: flex !important;
    align-items: flex-start;
    gap: 12px;
    padding: 10px 0;
    color: rgba(255,255,255,0.82) !important;
    font-size: 13px !important;
    line-height: 1.5 !important;
    margin-bottom: 0 !important;
}
.guest .checkbox-container .consent-label a { color: #c4a3ff !important; text-decoration: underline !important; }
.guest .submit-btn {
    margin-top: 20px !important;
    width: 100% !important;
    min-width: 100% !important;
}

.aff-hero.cv-venue-header { padding:16px 0; background:rgba(255,255,255,.025); border-bottom:1px solid rgba(255,255,255,.07); }
.aff-hero-verified { display:inline-flex; align-items:center; justify-content:center; width:17px; height:17px; border-radius:50%; background:var(--accent); color:#000 !important; font-size:9px; font-weight:900; margin-left:5px; vertical-align:middle; flex-shrink:0; }
.aff-hero-stars { display:flex; align-items:center; gap:4px; font-size:12px; color:rgba(255,255,255,.6) !important; margin-top:3px; }
.aff-hero-stars .cv-stars { color:var(--accent) !important; letter-spacing:-1px; }
.aff-hero-badges { display:flex; gap:8px; flex-wrap:wrap; align-items:center; }
.aff-hero-badge { display:inline-flex; align-items:center; gap:5px; padding:5px 11px; border-radius:20px; border:1px solid rgba(255,255,255,.12); background:rgba(255,255,255,.05); font-size:11px; font-weight:600; color:rgba(255,255,255,.8) !important; white-space:nowrap; }
.aff-hero-badge i { color:var(--accent) !important; font-size:11px; }

.cv-checkout-body { display:grid; grid-template-columns:minmax(0,1fr) 440px; gap:28px; align-items:start; margin-top:24px; }
.cv-main-col { min-width:0; }
.cv-sidebar { position:sticky; top:24px; background:rgba(16,18,34,.92); border:1px solid rgba(255,255,255,.14); border-radius:18px; padding:20px; overflow: visible; display:block !important; }
/* Guest tab active: hide sidebar, give full width to .guest content */
.cv-checkout-body.is-guest-mode { grid-template-columns: 1fr !important; }
.cv-checkout-body.is-guest-mode .cv-sidebar { display: none !important; }
.cv-checkout-body.is-guest-mode .cv-main-col { max-width: 100% !important; width: 100%; }
.cv-checkout-body.is-guest-mode ~ * { width: 100%; }
.is-guest-mode {
    width: 100% !important;
    max-width: 100% !important;
}
.is-guest-mode .container {
    width: 100% !important;
    max-width: 100% !important;
    padding-left: 0 !important;
    padding-right: 0 !important;
}
/* Compact spacing to fit without scroll */
.cv-sidebar .cv-sidebar-venue-image { height: 80px; margin-bottom: 10px; padding: 6px; }
@media (max-width: 991px) {
    .cv-sidebar .cv-sidebar-venue-image,
    .cv-sidebar-venue-image { height: 70px; max-height: 70px; padding: 6px; margin-bottom: 8px; }
}
.cv-sidebar .cv-sidebar-venue-row { margin-bottom: 10px !important; }
.cv-sidebar #cv-sidebar-body { font-size: 13px; }
.cv-sidebar .cv-trust-list { gap: 10px; padding: 12px 0 0; margin: 12px 0 0; }
.cv-sidebar .cv-trust-item > i { width: 26px; height: 26px; font-size: 11px; }
.cv-sidebar .cv-trust-item strong { font-size: 12.5px; }
.cv-sidebar .cv-trust-item > div > span { font-size: 11px; }
.cv-sidebar .cv-cta-btn { padding: 13px 20px; margin-top: 12px; }
.cv-sidebar .cv-deposit-box { padding: 12px 14px; margin: 12px 0; }
.cv-sidebar .cv-deposit-main { font-size: 26px; }
.cv-sidebar .cv-deposit-shield { width: 36px; height: 36px; font-size: 15px; }
.cv-sidebar #cv-order-sidebar .pricing-shell .default-deposit > span:last-child,
.cv-sidebar .pricing-shell .default-deposit > span:last-child { font-size: 24px !important; }
.cv-sidebar-header { font-size:16px; font-weight:800; letter-spacing:-.01em; display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; color:rgba(255,255,255,.95) !important; }
.cv-sidebar-edit-btn { font-size:12px; color:var(--accent) !important; background:none; border:none; cursor:pointer; font-weight:600; padding:0; }
.cv-sidebar-venue-row { display:flex; align-items:center; gap:12px; padding-bottom:14px; border-bottom:1px solid rgba(255,255,255,.08); margin-bottom:14px; }
.cv-sidebar-venue-thumb { width:52px; height:52px; border-radius:10px; object-fit:cover; flex-shrink:0; }
.cv-sidebar-venue-placeholder { width:52px; height:52px; border-radius:10px; background:rgba(255,255,255,.08); display:flex; align-items:center; justify-content:center; font-weight:800; font-size:18px; color:var(--accent) !important; flex-shrink:0; }
.cv-sidebar-venue-name { font-size:14px; font-weight:700; line-height:1.3; color:rgba(255,255,255,.9) !important; }
.cv-sidebar-venue-date { font-size:12px; color:rgba(255,255,255,.5) !important; margin-top:3px; }

#cv-order-sidebar #cart-section { border:none !important; background:transparent !important; border-radius:0 !important; padding:0 0 12px !important; margin-bottom:0 !important; }
#cv-order-sidebar #cart-section .cart-heading { font-size:12px !important; text-transform:uppercase; letter-spacing:.08em; font-weight:700; opacity:.55; margin-bottom:8px; }

.cv-deposit-box {
    background: rgba(255,204,0,.05);
    border: 1px solid rgba(255,204,0,.45);
    border-radius: 14px;
    padding: 14px 16px;
    margin: 16px 0;
}
.cv-deposit-content { min-width: 0; }
.cv-deposit-top { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 4px; }
.cv-deposit-label {
    font-size: 13px;
    font-weight: 600;
    color: rgba(255,255,255,0.78) !important;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 0;
    text-transform: none;
    letter-spacing: 0;
}
.cv-deposit-label .cv-info-icon {
    width: 14px;
    height: 14px;
    border-radius: 50%;
    border: 1px solid rgba(255,255,255,0.4);
    color: rgba(255,255,255,0.6);
    font-size: 9px;
    font-style: italic;
    font-family: 'Times New Roman', serif;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
}
.cv-deposit-box .cv-deposit-main {
    font-size: 30px;
    font-weight: 800;
    color: #fff !important;
    line-height: 1.05;
}
.cv-deposit-sub {
    font-size: 12px;
    color: rgba(255,255,255,0.55) !important;
    margin-top: 4px;
}
.cv-deposit-due-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 13px;
    color: rgba(255,255,255,0.72) !important;
    margin-top: 12px;
    padding-top: 10px;
    border-top: 1px solid rgba(255,204,0,.18);
}
.cv-deposit-due-row #cv-due-on-arrival {
    color: #fff !important;
    font-weight: 700;
}
.cv-deposit-shield {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(34,197,94,0.14);
    border: 1px solid rgba(34,197,94,0.4);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #22c55e !important;
    font-size: 17px;
    flex-shrink: 0;
    cursor: help;
    position: relative;
}

/* ===== Custom hover tooltip system (data-tip) ===== */
[data-tip] { position: relative; }
[data-tip]:hover::after,
[data-tip]:focus-visible::after {
    content: attr(data-tip);
    position: absolute;
    top: calc(100% + 10px);
    left: 0;
    background: linear-gradient(180deg, rgba(28,20,52,0.98), rgba(14,8,28,0.99));
    color: #fff;
    padding: 10px 14px;
    border-radius: 10px;
    font-size: 12.5px;
    font-weight: 500;
    line-height: 1.5;
    min-width: 220px;
    max-width: 300px;
    white-space: normal;
    z-index: 1000;
    border: 1px solid rgba(167,116,255,0.35);
    box-shadow: 0 10px 30px rgba(0,0,0,0.55), 0 0 0 1px rgba(167,116,255,0.15);
    pointer-events: none;
    letter-spacing: 0;
    text-transform: none;
    opacity: 0;
    animation: cvTipFadeIn .15s ease-out forwards;
}
[data-tip]:hover::before,
[data-tip]:focus-visible::before {
    content: '';
    position: absolute;
    top: calc(100% + 4px);
    left: 14px;
    width: 0;
    height: 0;
    border-left: 6px solid transparent;
    border-right: 6px solid transparent;
    border-bottom: 6px solid rgba(167,116,255,0.5);
    z-index: 1001;
    pointer-events: none;
}
[data-tip-right]:hover::after { left: auto; right: 0; }
[data-tip-right]:hover::before { left: auto; right: 14px; }
@keyframes cvTipFadeIn {
    from { opacity: 0; transform: translateY(-4px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Hide the redundant breakdown lines the user wants removed */
#cv-order-sidebar #cart-total,
#cv-order-sidebar #cart-section .cart-heading,
#cv-order-sidebar .pricing-shell .default-refundable,
#cv-order-sidebar .pricing-shell .default-due { display: none !important; }

.cv-trust-list { display:flex; flex-direction:column; gap:14px; padding:16px 0 0; background:transparent; border-radius:0; border:none; border-top:1px solid rgba(255,255,255,.08); margin: 16px 0 0; }
.cv-trust-item { display:flex; align-items:center; gap:12px; }
.cv-trust-item > i { width:30px; height:30px; border-radius:50%; background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.1); color:rgba(255,255,255,0.88) !important; font-size:12px; display:inline-flex; align-items:center; justify-content:center; flex-shrink:0; margin-top:0; }
.cv-trust-item strong { display:block; font-size:13.5px; line-height:1.2; color:#fff !important; font-weight:600; }
.cv-trust-item > div > span { display:block; font-size:12px; color:rgba(255,255,255,.5) !important; margin-top:2px; }

.cv-cta-btn { width:100%; display:flex; align-items:center; justify-content:center; gap:10px; padding:16px 20px; border-radius:12px; background: linear-gradient(135deg, #a774ff 0%, #7c3aed 50%, #5b21b6 100%) !important; color:#fff !important; font-size:15px; font-weight:800; border:none; cursor:pointer; transition:all .2s; letter-spacing:-.01em; margin-top: 16px; box-shadow: 0 6px 20px rgba(124,58,237,0.4); }
.cv-cta-btn:hover { filter:brightness(1.1); transform: translateY(-1px); box-shadow: 0 10px 26px rgba(124,58,237,0.55); }
.cv-cta-btn:disabled { opacity:.45; cursor:not-allowed; filter:none; transform: none; box-shadow: none; }
.cv-cta-btn i { font-size:13px; }
.cv-cta-terms { text-align:center; font-size:12px; color:rgba(255,255,255,.5) !important; margin-top:12px; line-height:1.5; }
.cv-cta-terms a { color:rgba(255,255,255,.85) !important; text-decoration:underline !important; }

.cv-mobile-cart-toggle { display:none !important; align-items:center; justify-content:space-between; background:rgba(255,204,0,.08); border:1px solid rgba(255,204,0,.2); border-radius:12px; padding:12px 16px; margin-bottom:16px; cursor:pointer; width:100%; text-align:left; font-size:14px; font-weight:600; color:rgba(255,255,255,.85) !important; }
.cv-mobile-cart-count { background:var(--accent); color:#000 !important; font-size:11px; font-weight:800; border-radius:20px; padding:2px 10px; }

.aff-story { display:none !important; }

.package { display:block; }

.cv-desktop-shell { border: 1px solid rgba(255,255,255,0.09); border-radius: 16px; background: linear-gradient(180deg, rgba(11,14,30,0.84), rgba(8,10,22,0.9)); padding: 20px 18px 16px; margin-bottom: 16px; }

/* Circular 4-step indicator with connecting lines */
.cv-desktop-steps { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 0; margin: 6px 0 18px; position: relative; }
.cv-dstep { display: flex; flex-direction: column; align-items: center; justify-content: flex-start; gap: 8px; color: rgba(255,255,255,0.55) !important; font-size: 12px; font-weight: 600; position: relative; text-align: center; padding: 0 4px; }
.cv-dstep::before { content: ''; position: absolute; top: 16px; left: calc(50% + 18px); right: calc(-50% + 18px); height: 2px; background: rgba(255,255,255,0.14); z-index: 0; }
.cv-dstep:last-child::before { display: none; }
.cv-dstep-num { width: 32px; height: 32px; border-radius: 999px; border: 1.5px solid rgba(255,255,255,0.22); display: inline-flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 800; color: rgba(255,255,255,0.85) !important; background: rgba(255,255,255,0.04); position: relative; z-index: 1; transition: all .2s; }
.cv-dstep.is-active .cv-dstep-num { background: linear-gradient(135deg, #a774ff 0%, #7c3aed 100%) !important; border-color: #7c3aed !important; color: #fff !important; box-shadow: 0 0 0 4px rgba(167,116,255,0.2), 0 4px 12px rgba(124,58,237,0.4); }
.cv-dstep.is-active { color: #c4a3ff !important; }
.cv-dstep.is-complete .cv-dstep-num { background: linear-gradient(135deg, #a774ff 0%, #5b21b6 100%) !important; border-color: #7c3aed !important; color: #fff !important; }
.cv-dstep.is-complete::before { background: linear-gradient(90deg, #a774ff, #7c3aed) !important; }

.cv-access-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; margin-top: 4px; }

/* Access tab hint label */
.cv-access-hint {
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: rgba(167,116,255,0.85);
    margin: 18px 0 10px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.cv-access-hint::before {
    content: '';
    width: 22px;
    height: 1px;
    background: linear-gradient(90deg, transparent, #a774ff);
}
.cv-access-hint .cv-access-hint-dot {
    width: 9px;
    height: 9px;
    border-radius: 50%;
    background: radial-gradient(circle at 30% 30%, #fff4bf 0%, #ffd76a 30%, #ffbf00 72%, #e0a300 100%);
    box-shadow: 0 0 0 2px rgba(255,191,0,0.22), 0 0 14px rgba(255,191,0,0.88), 0 0 22px rgba(255,215,106,0.48);
    margin-left: 5px;
    display: inline-block;
    animation: cvHintPulse 1.2s ease-in-out infinite;
}
@keyframes cvHintPulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.68; transform: scale(0.78); }
}

.cv-access-card {
    border: 1.5px solid rgba(167,116,255,0.18);
    border-radius: 14px;
    padding: 16px 18px 16px 56px;
    background: rgba(167,116,255,0.04);
    display: flex;
    align-items: center;
    gap: 14px;
    cursor: default;
    transition: all .2s;
    text-align: left;
    width: 100%;
    font-family: inherit;
    color: inherit;
    position: relative;
}
/* Radio-style indicator on the left of each card */
.cv-access-card::before {
    content: '';
    position: absolute;
    left: 18px;
    top: 50%;
    transform: translateY(-50%);
    width: 22px;
    height: 22px;
    border-radius: 50%;
    border: 2px solid rgba(167,116,255,0.45);
    background: rgba(0,0,0,0.25);
    transition: all .2s;
    flex-shrink: 0;
}
.cv-access-card::after {
    content: '';
    position: absolute;
    left: 24px;
    top: 50%;
    transform: translateY(-50%) scale(0);
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: linear-gradient(135deg, #a774ff 0%, #7c3aed 100%);
    transition: transform .2s cubic-bezier(.2,.9,.3,1.4);
    box-shadow: 0 0 10px rgba(167,116,255,0.6);
}
.cv-access-card.cv-access-tab { cursor: pointer; }
.cv-access-card.cv-access-tab:hover:not(.is-active) {
    border-color: rgba(167,116,255,0.5);
    background: rgba(167,116,255,0.08);
    transform: translateY(-1px);
}
.cv-access-card.cv-access-tab:hover:not(.is-active)::before { border-color: rgba(167,116,255,0.7); }

.cv-access-card i {
    color: rgba(196,163,255,0.85) !important;
    font-size: 20px;
    flex-shrink: 0;
}
.cv-access-card strong {
    display: block;
    font-size: 15px;
    line-height: 1.2;
    color: #fff !important;
    font-weight: 700;
}
.cv-access-card span {
    display: block;
    font-size: 12px;
    line-height: 1.3;
    color: rgba(255,255,255,0.55) !important;
    margin-top: 3px;
}
.cv-access-card.is-active {
    border-color: #a774ff;
    background: linear-gradient(135deg, rgba(167,116,255,0.18), rgba(124,58,237,0.12));
    box-shadow: 0 0 0 1px rgba(167,116,255,0.4), 0 6px 22px rgba(124,58,237,0.28);
}
.cv-access-card.is-active::before {
    border-color: #a774ff;
    background: rgba(167,116,255,0.18);
}
.cv-access-card.is-active::after {
    transform: translateY(-50%) scale(1);
}
.cv-access-card.is-active i {
    color: #c4a3ff !important;
}

/* Package cards - base + tier theming */
.vip-card.cv-exact-card { display: grid; grid-template-columns: 130px 1fr 200px; gap: 16px; align-items: stretch; border-radius: 16px !important; padding: 12px !important; border: 1px solid rgba(255,255,255,0.12) !important; background: linear-gradient(180deg, rgba(18,22,42,0.76), rgba(10,12,26,0.88)) !important; margin-bottom: 14px; position: relative; overflow: hidden; }
.vip-card.cv-exact-card::before { content: ''; position: absolute; right: 0; top: 0; bottom: 0; width: 45%; background: radial-gradient(ellipse at right center, rgba(255,255,255,0.04), transparent 70%); pointer-events: none; z-index: 0; }
.vip-card.cv-exact-card > * { position: relative; z-index: 1; }
.cv-pkg-media-wrap { position: relative; border-radius: 12px; overflow: hidden; min-height: 130px; background: rgba(255,255,255,0.06); }
.cv-pkg-media { width: 100%; height: 100%; object-fit: cover; display: block; }
.cv-popular-pill {
    position: absolute;
    top: 10px;
    left: 10px;
    font-size: 8px;
    font-weight: 800;
    letter-spacing: 0.06em;
    color: #fff !important;
    background: linear-gradient(135deg, #a774ff 0%, #7c3aed 50%, #5b21b6 100%);
    border: 1px solid rgba(255,255,255,0.18);
    border-radius: 999px;
    padding: 5px 11px;
    text-transform: uppercase;
    box-shadow: 0 4px 14px rgba(124,58,237,0.55), inset 0 1px 0 rgba(255,255,255,0.25);
    display: inline-flex;
    align-items: center;
    gap: 5px;
    z-index: 2;
}
.cv-popular-pill::before {
    content: '\f521';
    font-family: 'Font Awesome 6 Free';
    font-weight: 900;
    font-size: 9px;
    color: #fff;
}
.vip-card.cv-exact-card .vip-card-main { display: flex !important; flex-direction: column !important; justify-content: space-between !important; gap: 6px; min-width: 0; height: 100% !important; padding: 0 !important; }
.cv-pkg-title-row { display: flex; align-items: center; gap: 10px; }
.cv-pkg-title-icon { font-size: 22px; flex-shrink: 0; color: var(--tier-accent, #fff) !important; }
.cv-pkg-title { font-size: 26px; font-weight: 700; line-height: 1.2; color: var(--tier-accent, #fff) !important; letter-spacing: -0.01em; }
.cv-pkg-sub { font-size: 12.5px; color: rgba(255,255,255,0.62) !important; display: inline-flex; align-items: center; gap: 6px; }
.cv-pkg-sub i { font-size: 12px; opacity: .7; }
.cv-pkg-desc { font-size: 13px; color: rgba(255,255,255,0.62) !important; line-height: 1.5; margin: 0; }
.cv-pkg-features { display: flex !important; flex-wrap: wrap; gap: 14px; padding-top: 12px !important; margin-top: 4rem !important; margin-left: auto !important; margin-right: auto !important; border-top: 1px solid rgba(255,255,255,0.08); }
.cv-pkg-feature { font-size: 11.5px; color: rgba(255,255,255,0.65) !important; display: inline-flex; align-items: center; gap: 5px; }
.cv-pkg-feature i { color: var(--tier-accent, rgba(255,255,255,0.76)) !important; font-size: 11px; opacity: .9; }
.vip-card.cv-exact-card .vip-card-side { display: flex; flex-direction: column; justify-content: space-between; align-items: stretch; gap: 6px; grid-template-columns: none !important; flex: initial; text-align: right; }
.vip-card.cv-exact-card .vip-price-tag { font-size: 30px !important; text-align: right; padding-top: 0; min-width: 0; color: #fff !important; font-weight: 700; line-height: 1.1; }
.cv-price-meta { text-align: right; font-size: 12px; color: rgba(255,255,255,0.58) !important; margin-top: 2px; }
.vip-card.cv-exact-card .package_number_of_guestss { width: 100% !important; min-width: 0; margin-top: 8px; }
.vip-card.cv-exact-card .vip-btn { width: 100%; border-radius: 10px; font-weight: 800; background: var(--tier-accent, var(--accent)) !important; color: var(--tier-btn-color, #000) !important; padding: 11px 12px !important; font-size: 14px !important; display: inline-flex !important; align-items: center; justify-content: center; gap: 6px; }
.vip-card.cv-exact-card .vip-btn::after { content: '\f07a'; font-family: 'Font Awesome 6 Free'; font-weight: 900; font-size: 12px; }

/* Tier 1 - Gold (Most Popular) */
.vip-card.cv-exact-card.cv-tier-1 { --tier-accent: #ffcc00; --tier-btn-color: #000; border-color: rgba(255,204,0,0.65) !important; background: linear-gradient(180deg, rgba(40,28,8,0.85), rgba(20,14,6,0.95)) !important; }
.vip-card.cv-exact-card.cv-tier-1::before { background: radial-gradient(ellipse at right center, rgba(255,204,0,0.12), transparent 70%); }

/* Tier 2 - Purple */
.vip-card.cv-exact-card.cv-tier-2 { --tier-accent: #a774ff; --tier-btn-color: #fff; border-color: rgba(167,116,255,0.55) !important; background: linear-gradient(180deg, rgba(36,18,58,0.85), rgba(18,10,32,0.95)) !important; }
.vip-card.cv-exact-card.cv-tier-2::before { background: radial-gradient(ellipse at right center, rgba(167,116,255,0.14), transparent 70%); }

/* Tier 3 - Silver/Diamond (dark) */
.vip-card.cv-exact-card.cv-tier-3 { --tier-accent: #e8e8ea; --tier-btn-color: #000; border-color: rgba(232,232,234,0.35) !important; background: linear-gradient(180deg, rgba(16,18,28,0.92), rgba(8,10,16,0.96)) !important; }
.vip-card.cv-exact-card.cv-tier-3::before { background: radial-gradient(ellipse at right center, rgba(232,232,234,0.06), transparent 70%); }
.vip-card.cv-exact-card.cv-tier-3 .vip-btn { background: rgba(255,255,255,0.94) !important; color: #000 !important; }

/* Tier 4 - Red/High Roller */
.vip-card.cv-exact-card.cv-tier-4 { --tier-accent: #ff5868; --tier-btn-color: #fff; border-color: rgba(255,88,104,0.55) !important; background: linear-gradient(180deg, rgba(56,14,22,0.88), rgba(28,8,14,0.96)) !important; }
.vip-card.cv-exact-card.cv-tier-4::before { background: radial-gradient(ellipse at right center, rgba(255,88,104,0.16), transparent 70%); }

/* Free Ride Included callout */
.cv-freeride-callout { display: flex; align-items: center; gap: 14px; padding: 16px 18px; border-radius: 14px; background: rgba(255,255,255,0.025); border: 1px solid rgba(255,255,255,0.08); margin: 12px 0 0; }
.cv-freeride-callout .cv-freeride-icon { width: 40px; height: 40px; border-radius: 10px; background: rgba(255,204,0,0.1); display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; color: var(--accent) !important; font-size: 17px; }
.cv-freeride-callout strong { display: block; font-size: 14px; color: var(--accent) !important; font-weight: 700; margin-bottom: 2px; }
.cv-freeride-callout span { display: block; font-size: 12px; color: rgba(255,255,255,0.62) !important; line-height: 1.5; }

/* Need Help section */
.cv-need-help { display: flex; align-items: center; justify-content: space-between; padding: 18px 20px; margin-top: 16px; border-top: 1px solid rgba(255,255,255,0.08); gap: 16px; flex-wrap: wrap; }
.cv-need-help-title strong { display: block; font-size: 14px; color: rgba(255,255,255,0.92) !important; font-weight: 700; }
.cv-need-help-title span { display: block; font-size: 12px; color: rgba(255,255,255,0.5) !important; margin-top: 2px; }
.cv-need-help-actions { display: flex; gap: 18px; flex-wrap: wrap; }
.cv-need-help-action { display: flex; align-items: center; gap: 10px; color: rgba(255,255,255,0.85) !important; text-decoration: none !important; font-size: 13px; }
.cv-need-help-action i { color: var(--accent) !important; font-size: 15px; width: 28px; height: 28px; border-radius: 999px; background: rgba(255,204,0,0.1); display: inline-flex; align-items: center; justify-content: center; }
.cv-need-help-action strong { display: block; font-size: 13px; color: #fff !important; font-weight: 600; line-height: 1.1; }
.cv-need-help-action span { display: block; font-size: 11px; color: rgba(255,255,255,0.5) !important; margin-top: 1px; }

/* Order summary sidebar refinements */
.cv-sidebar-venue-image {
    width: 100%;
    height: 100px;
    max-height: 110px;
    border-radius: 12px;
    object-fit: contain;
    object-position: center;
    margin-bottom: 12px;
    display: block;
    background: linear-gradient(180deg, rgba(255,255,255,0.04), rgba(255,255,255,0.01));
    border: 1px solid rgba(255,255,255,0.06);
    padding: 8px;
    box-sizing: border-box;
}
.cv-sidebar-venue-image-placeholder { width: 100%; height: 100px; border-radius: 12px; background: rgba(255,255,255,0.04); display: flex; align-items: center; justify-content: center; color: rgba(255,255,255,0.3); font-size: 13px; margin-bottom: 12px; }
#cv-order-sidebar #cart-section #cart-list .cart-line { border: none !important; background: transparent !important; padding: 8px 0 !important; border-radius: 0 !important; margin: 0 !important; border-bottom: 1px solid rgba(255,255,255,0.07) !important; }
#cv-order-sidebar #cart-section #cart-list .cart-line:last-child { border-bottom: none !important; }
#cv-order-sidebar #cart-section #cart-list .cart-line-main { gap: 10px; }
#cv-order-sidebar #cart-section .cart-item-name { font-size: 14px; font-weight: 700; color: #fff !important; }
#cv-order-sidebar #cart-section .cart-item-price { color: #fff !important; font-size: 14px !important; font-weight: 700; margin-top: 0; }
#cv-order-sidebar #cart-section .cart-line-guests { font-size: 12px; color: rgba(255,255,255,0.55) !important; margin-top: 3px; }
#cv-order-sidebar #cart-section .cart-remove-btn { font-size: 10px; padding: 3px 8px; opacity: .65; }
#cv-order-sidebar .pricing-shell { margin-top: 0 !important; padding-top: 14px; border-top: 1px solid rgba(255,255,255,0.08); }
#cv-order-sidebar .pricing-shell .row.g-3 { margin: 0; }
#cv-order-sidebar .pricing-shell .default-price { display: none !important; }
/* Subtotal/Service Fee/Tax/Gratuity rows - clean flex layout */
#cv-order-sidebar .pricing-shell .default-package-price,
#cv-order-sidebar .pricing-shell .default-service-charge,
#cv-order-sidebar .pricing-shell .default-sales-tax,
#cv-order-sidebar .pricing-shell .default-gratuity,
#cv-order-sidebar .pricing-shell .addonns > div,
#cv-order-sidebar .pricing-shell .sales_tax > div {
    font-size: 14px !important;
    color: rgba(255,255,255,0.75) !important;
    display: flex !important;
    align-items: center;
    gap: 6px;
    padding: 6px 0;
    font-weight: 500;
}
#cv-order-sidebar .pricing-shell .default-package-price > span:last-child,
#cv-order-sidebar .pricing-shell .default-service-charge > span:last-child,
#cv-order-sidebar .pricing-shell .default-sales-tax > span:last-child,
#cv-order-sidebar .pricing-shell .default-gratuity > span:last-child {
    margin-left: auto;
    color: rgba(255,255,255,0.95) !important;
    font-weight: 600;
}
/* Small (i) info icon - rendered as inline element by JS so ::after is free for the tooltip */
#cv-order-sidebar .pricing-shell .default-service-charge,
#cv-order-sidebar .pricing-shell .default-sales-tax,
#cv-order-sidebar .pricing-shell .default-gratuity { cursor: help; }
.cv-row-info-icon {
    font-family: 'Times New Roman', serif;
    font-style: italic;
    font-size: 10px;
    font-weight: 700;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    border: 1px solid rgba(255,255,255,0.35);
    color: rgba(255,255,255,0.55);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
    flex-shrink: 0;
    margin-left: 4px;
    transition: all .15s;
    vertical-align: middle;
}
#cv-order-sidebar .pricing-shell .default-service-charge:hover .cv-row-info-icon,
#cv-order-sidebar .pricing-shell .default-sales-tax:hover .cv-row-info-icon,
#cv-order-sidebar .pricing-shell .default-gratuity:hover .cv-row-info-icon {
    border-color: #a774ff;
    color: #c4a3ff;
    background: rgba(167,116,255,0.12);
}
.cv-deposit-label { cursor: help; }
.cv-deposit-label:hover .cv-info-icon { border-color: #a774ff !important; color: #c4a3ff !important; background: rgba(167,116,255,0.12); }
/* Total row - prominent */
#cv-order-sidebar .pricing-shell .default-deposit {
    font-size: 19px !important;
    font-weight: 700 !important;
    padding: 18px 16px !important;
    border-top: 1px solid rgba(255,255,255,0.16) !important;
    margin: 4px -16px 4px !important;
    display: flex !important;
    justify-content: space-between;
    align-items: center;
    color: #fff !important;
    background: linear-gradient(180deg, rgba(255,255,255,0.04), rgba(255,255,255,0.01)) !important;
    border-radius: 0 !important;
    gap: 12px !important;
}
#cv-order-sidebar .pricing-shell .default-deposit > span:first-child {
    color: rgba(255,255,255,0.88) !important;
    font-size: 16px !important;
    font-weight: 700 !important;
    letter-spacing: -0.005em;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}
#cv-order-sidebar .pricing-shell .default-deposit > span:last-child {
    color: #fff !important;
    font-size: 28px !important;
    font-weight: 800 !important;
    letter-spacing: -0.015em;
    line-height: 1;
    margin-left: auto;
}
#cv-order-sidebar .pricing-shell hr { display: none; }
#cv-order-sidebar .pricing-shell .col-md-6 { width: 100%; max-width: 100%; flex: 0 0 100%; padding: 0; }
/* Promo code section (highest specificity to override .vip-btn-submit, #applyPromoBtn) */
#cv-order-sidebar .dynamic-price.col-md-6 {
    display: block !important;
    margin-top: 16px;
    margin-bottom: 4px;
    padding-top: 0;
    border-top: none;
    width: 100%;
    max-width: 100%;
    flex: 0 0 100%;
}
#cv-order-sidebar .dynamic-price.col-md-6 > label {
    font-size: 13.5px;
    color: rgba(255,255,255,0.7) !important;
    display: block;
    margin-bottom: 8px;
    padding-top: 8px;
    font-weight: 500;
}
/* Force the inner Bootstrap row to a clean flex layout regardless of Bootstrap gutters */
#cv-order-sidebar .dynamic-price.col-md-6 > .row {
    margin: 0 !important;
    display: flex !important;
    flex-wrap: nowrap !important;
    align-items: stretch !important;
    gap: 0;
    --bs-gutter-x: 0;
}
#cv-order-sidebar .dynamic-price.col-md-6 > .row > .col-md-8,
#cv-order-sidebar .dynamic-price.col-md-6 > .row > .col-8 {
    flex: 1 1 auto !important;
    width: auto !important;
    max-width: none !important;
    padding: 0 !important;
    min-width: 0;
}
#cv-order-sidebar .dynamic-price.col-md-6 > .row > .col-md-4,
#cv-order-sidebar .dynamic-price.col-md-6 > .row > .col-4 {
    flex: 0 0 auto !important;
    width: auto !important;
    max-width: none !important;
    padding: 0 !important;
}
#cv-order-sidebar .dynamic-price.col-md-6 #promo_code {
    background: transparent !important;
    border: 1px solid rgba(255,255,255,0.16) !important;
    color: #fff !important;
    padding: 0 14px !important;
    border-radius: 10px 0 0 10px !important;
    font-size: 14px !important;
    height: 46px !important;
    min-height: 46px;
    border-right: none !important;
    width: 100% !important;
    box-sizing: border-box;
    margin: 0 !important;
    outline: none !important;
}
#cv-order-sidebar .dynamic-price.col-md-6 #promo_code::placeholder { color: rgba(255,255,255,0.4) !important; }
#cv-order-sidebar .dynamic-price.col-md-6 #applyPromoBtn,
#cv-order-sidebar .dynamic-price.col-md-6 .vip-btn-submit {
    background: transparent !important;
    color: rgba(255,255,255,0.92) !important;
    border-radius: 0 10px 10px 0 !important;
    border-top-right-radius: 10px !important;
    border-bottom-right-radius: 10px !important;
    border-top-left-radius: 0 !important;
    border-bottom-left-radius: 0 !important;
    font-weight: 700 !important;
    font-size: 14px !important;
    height: 46px !important;
    min-height: 46px;
    border: 1px solid rgba(255,255,255,0.16) !important;
    padding: 0 20px !important;
    width: auto !important;
    margin: 0 !important;
    white-space: nowrap;
    cursor: pointer;
    display: inline-flex !important;
    align-items: center;
    justify-content: center;
    line-height: 1;
}
#cv-order-sidebar .dynamic-price.col-md-6 #applyPromoBtn:hover,
#cv-order-sidebar .dynamic-price.col-md-6 .vip-btn-submit:hover { background: rgba(255,255,255,0.06) !important; }

/* Promo discount line (inserted by JS) */
#cv-order-sidebar .pricing-shell .default-promo-discount {
    font-size: 14px !important;
    display: flex !important;
    align-items: center;
    padding: 6px 0;
    color: #22c55e !important;
    font-weight: 600 !important;
}
#cv-order-sidebar .pricing-shell .default-promo-discount span { margin-left: auto; }

@media (min-width: 992px) {
    .cv-main-col #cart-section,
    .cv-main-col .pricing-shell { display: none !important; }
}

@media (max-width: 1199px) {
    .cv-checkout-body { grid-template-columns: minmax(0,1fr) 400px; gap: 20px; }
}

@media (max-width: 991px) {
    .cv-checkout-body { grid-template-columns: 1fr; }
    /* Mobile: sidebar is moved by JS to sit between package selection and #section-3 (Payment).
       Drop sticky/positioning so it flows inline within the package step. */
    .cv-sidebar { position:static; display:block !important; max-height: none; overflow: visible; margin: 16px 0; }
    .cv-sidebar.cv-sidebar-open { display:block; }
    .cv-mobile-cart-toggle { display:none !important; }
    .vip-card.cv-exact-card { grid-template-columns: 1fr; gap: 14px; padding: 14px !important; }
    .vip-card.cv-exact-card .cv-pkg-media-wrap { min-height: 80px; height: 80px; border-radius: 10px; overflow: hidden; }
    .vip-card.cv-exact-card .cv-pkg-media { width: 100%; height: 100%; object-fit: cover; }
    .vip-card.cv-exact-card .cv-pkg-title-row { margin-top: 0; }
    .vip-card.cv-exact-card .cv-pkg-title { font-size: 22px !important; }
    .vip-card.cv-exact-card .cv-pkg-desc { font-size: 13px !important; line-height: 1.5; }
    .vip-card.cv-exact-card .cv-pkg-features { display: grid; grid-template-columns: repeat(4, 1fr); gap: 6px 10px; margin-top: auto; padding-top: 14px; flex-wrap: wrap; }
    .vip-card.cv-exact-card .cv-pkg-feature { flex: 0 0 auto; font-size: 10.5px !important; flex-direction: column; align-items: center; gap: 3px; text-align: center; }
    .vip-card.cv-exact-card .cv-pkg-feature i { font-size: 15px !important; margin-bottom: 1px; }
    .vip-card.cv-exact-card .vip-card-side {
        display: grid !important;
        grid-template-columns: 1fr auto;
        grid-template-areas:
            "price guests"
            "meta button";
        gap: 8px 12px;
        align-items: center;
        margin-top: 14px;
        padding-top: 14px;
        border-top: 1px solid rgba(255,255,255,0.08);
        text-align: left;
    }
    .vip-card.cv-exact-card .vip-price-tag {
        grid-area: price;
        text-align: left !important;
        font-size: 28px !important;
    }
    .vip-card.cv-exact-card .cv-price-meta {
        grid-area: meta;
        text-align: left !important;
        font-size: 11.5px !important;
        margin-top: -4px !important;
    }
    .vip-card.cv-exact-card .package-guest-input-wrap {
        grid-area: guests;
        width: 100%;
    }
    .vip-card.cv-exact-card .package_number_of_guestss { margin-top: 0 !important; min-height: 38px !important; padding-top: 0px !important; padding-bottom: 0px !important;}
    .vip-card.cv-exact-card .vip-btn {
        grid-area: button;
        margin: 0 !important;
        min-width: 150px;
        padding: 10px 20px !important;
    }
    .vip-card.cv-exact-card .package-guest-error,
    .vip-card.cv-exact-card .package-soldout {
        grid-column: 1 / -1;
        margin-top: 4px;
    }
    .cv-popular-pill { top: 8px; left: 8px; font-size: 9px; padding: 4px 9px; }
    .cv-access-grid { grid-template-columns: 1fr; }
}

@media (max-width: 767px) {
    .cv-top-nav {
        padding: 0 12px !important;
        min-height: 56px !important;
        gap: 8px !important;
    }
    .cv-nav-logo-img { height: 28px; max-width: 100px; }
    .cv-nav-back { display: flex !important; padding: 7px 12px !important; font-size: 12px !important; gap: 6px !important; }
    .cv-nav-back span { display: inline; }
    .cv-hamburger { display: none !important; }
    .mobile-top-actions { display: none !important; }
    .aff-hero.cv-venue-header .aff-hero-badges { order: 3; width: 100%; margin-top: 8px; }
    .aff-nav-badges {
        gap: 10px !important;
        flex-wrap: nowrap !important;
        width: auto !important;
        margin-left: auto !important;
        margin-right: 0 !important;
        overflow: hidden;
    }
    .aff-nav-badge {
        font-size: 9px !important;
        white-space: normal !important;
        flex: 0 1 auto !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 5px !important;
        line-height: 1.1 !important;
    }
    .aff-nav-badge i {
        font-size: 12px !important;
        flex-shrink: 0;
    }
    .aff-nav-badge span {
        display: flex !important;
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 0 !important;
    }
    .aff-nav-badge span span {
        display: block !important;
        font-weight: 400 !important;
        font-size: 8px !important;
    }

    /* Mobile Hero Section - Vibrant CartVIP Design */
    .cv-hero-stage {
        min-height: auto;
        padding: 18px !important;
        background-size: cover;
        background-position: center;
        border-radius: 18px !important;
        border: 1px solid rgba(251,113,133,0.25) !important;
        overflow: hidden;
        background: linear-gradient(135deg, rgba(251,113,133,0.08) 0%, rgba(167,116,255,0.05) 100%), linear-gradient(to bottom, rgba(255,255,255,0.02) 0%, rgba(0,0,0,0.3) 100%) !important;
    }

    .cv-hero-stage::before {
        display: none !important;
    }

    .cv-hero-inner {
        flex-direction: column;
        gap: 0;
        padding: 0;
    }

    /* Profile Photo Section with Vibrant Border */
    .cv-hero-head {
        flex-direction: row;
        gap: 12px;
        align-items: flex-start;
        padding: 0 0 12px 0;
        border-bottom: 1px solid rgba(251,113,133,0.15);
    }

    .cv-hero-venue {
        flex: 0 0 auto;
        gap: 0;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .aff-profile-story-btn {
        width: 110px !important;
        height: 110px !important;
        flex-shrink: 0;
    }

    .cv-hero-venue-avatar,
    .cv-hero-venue-initial {
        width: 110px !important;
        height: 110px !important;
        border-radius: 14px !important;
        flex-shrink: 0;
    }

    /* Name and Subtitle Section */
    .cv-hero-venue > div {
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        gap: 2px;
        flex: 1;
        padding-top: 0;
    }

    .cv-hero-venue-title {
        font-size: 38px !important;
        font-weight: 900;
        line-height: 1;
        color: #fff !important;
        margin: 0 !important;
        letter-spacing: -0.03em;
    }

    .cv-hero-venue-verified {
        display: none;
    }

    .cv-hero-venue-meta {
        font-size: 11px !important;
        margin: 3px 0 0 0 !important;
        letter-spacing: 0.15em;
        text-transform: uppercase;
        font-weight: 800;
        color: var(--accent) !important;
    }

    /* Social Links moved below subtitle */
    .cv-hero-venue > div .aff-social-links {
        margin-top: 6px !important;
    }

    /* Bio Section - Separated and Styled */
    .cv-hero-bottom {
        flex-direction: column;
        gap: 0;
        align-items: stretch;
        padding: 10px 0 0 0;
        border: none;
    }

    .cv-hero-content {
        max-width: 100%;
    }

    .aff-kicker {
        display: none;
    }

    .cv-hero-title {
        display: none;
    }

    .cv-hero-subtitle {
        font-size: 12px;
        line-height: 1.5;
        margin: 0 0 6px 0 !important;
        color: rgba(255,255,255,0.7) !important;
    }

    .aff-display-copy {
        font-size: 12px !important;
        line-height: 1.5;
        margin-bottom: 6px !important;
        color: rgba(255,255,255,0.8) !important;
    }

    /* Social Links (beside profile picture) */
    .cv-hero-venue .aff-social-links {
        margin: 0 !important;
        gap: 8px !important;
        display: flex !important;
    }

    .aff-social-links {
        margin: 0 !important;
        gap: 8px !important;
        display: flex;
    }

    .aff-social-link {
        width: 36px !important;
        height: 36px !important;
        font-size: 15px !important;
        background: rgba(255,255,255,0.08) !important;
        border: 1px solid rgba(251,113,133,0.3) !important;
        border-radius: 8px !important;
        transition: all 0.3s ease;
        padding: 0 !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    .aff-social-link:hover {
        background: linear-gradient(135deg, rgba(251,113,133,0.2), rgba(167,116,255,0.2)) !important;
        border-color: rgba(251,113,133,0.6) !important;
    }

    .cv-hero-location {
        display: none !important;
    }

    .aff-hero-gallery-carousel {
        display: none !important;
    }

    /* Show Features and Tagline on Mobile */
    .aff-hero-features {
        display: grid !important;
    }

    .aff-hero-tagline {
        display: flex !important;
    }

    /* AFFILIATE PACKAGES Section Header */
    .section-kicker-lg {
        font-size: 13px !important;
        font-weight: 800 !important;
        letter-spacing: 0.15em !important;
        text-transform: uppercase !important;
        color: var(--accent) !important;
        position: relative;
        display: inline-block;
        padding-bottom: 8px;
    }

    .section-kicker-lg::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 40px;
        height: 1px;
        background: var(--accent);
    }

    .cv-package-section-header {
        margin: 0 0 16px 0 !important;
        padding: 0 !important;
        display: flex !important;
        flex-direction: column !important;
        gap: 0 !important;
    }

    .cv-package-section-header > div {
        display: flex !important;
        flex-direction: column !important;
        gap: 4px !important;
    }

    .cv-package-section-header p {
        display: none !important;
    }


    /* Featured Package Card Styling */
    .package-category-group {
        display: none !important;
    }

    .package-category-group.is-active {
        display: grid !important;
        grid-template-columns: 1fr !important;
        gap: 12px !important;
    }

    /* First Package Card - Featured Style */
    .vip-card.cv-exact-card {
        background: linear-gradient(135deg, rgba(139,92,246,0.2) 0%, rgba(236,72,153,0.15) 100%) !important;
        border: 1px solid rgba(139,92,246,0.3) !important;
        border-radius: 14px !important;
        padding: 14px !important;
        display: grid !important;
        grid-template-columns: 100px 1fr !important;
        gap: 14px !important;
        align-items: flex-start;
    }

    .vip-card.cv-exact-card:first-of-type {
        background: linear-gradient(135deg, rgba(139,92,246,0.25) 0%, rgba(236,72,153,0.2) 100%) !important;
        border: 1px solid rgba(251,113,133,0.4) !important;
    }

    .vip-card.cv-exact-card .cv-pkg-media-wrap {
        grid-column: 1 !important;
        grid-row: 1 / -1 !important;
        min-height: 100px !important;
        height: 100px !important;
        border-radius: 10px !important;
        overflow: hidden !important;
        border: 1.5px solid rgba(251,113,133,0.5) !important;
        background: rgba(0,0,0,0.3) !important;
    }

    .vip-card.cv-exact-card .vip-card-main {
        grid-column: 2 !important;
        padding: 0 !important;
        display: flex !important;
        flex-direction: column !important;
        gap: 4px !important;
    }

    .vip-card.cv-exact-card .cv-pkg-title-row {
        margin: 0 !important;
        display: flex !important;
        align-items: center !important;
        gap: 6px !important;
    }

    .vip-card.cv-exact-card .cv-pkg-title-icon {
        display: none !important;
    }

    .vip-card.cv-exact-card .cv-pkg-title {
        font-size: 24px !important;
        font-weight: 900 !important;
        color: #fff !important;
        margin: 0 !important;
    }

    .vip-card.cv-exact-card .cv-pkg-sub {
        font-size: 12px !important;
        color: var(--accent) !important;
        font-weight: 700 !important;
    }

    .vip-card.cv-exact-card .cv-pkg-desc {
        font-size: 13px !important;
        color: rgba(255,255,255,0.8) !important;
        margin: 0 !important;
        line-height: 1.4 !important;
    }

    .vip-card.cv-exact-card .cv-pkg-features,
    .vip-card.cv-exact-card .vip-card-side,
    .vip-card.cv-exact-card .cv-club-name-badge {
        display: none !important;
    }

    .vip-card.cv-exact-card .cv-popular-pill {
        display: none !important;
    }

    /* Features Grid Section */
    .aff-hero-features {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
        padding: 12px 0;
        border-bottom: 1px solid rgba(251,113,133,0.15);
    }

    .aff-hero-feature {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        padding: 10px 6px;
        text-align: center;
        background: transparent;
        border: none;
        border-radius: 0;
        transition: all 0.3s ease;
    }

    .aff-hero-feature:hover {
        background: transparent;
        border-color: transparent;
    }

    .aff-hero-feature-icon {
        font-size: 24px;
        color: var(--accent);
        width: auto;
        height: auto;
        display: flex;
        align-items: center;
        justify-content: center;
        background: transparent;
        border-radius: 0;
    }

    .aff-hero-feature-label {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: rgba(255,255,255,0.8);
        line-height: 1.2;
    }

    /* Hide Category Tabs on Mobile */
    .package-category-tiles {
        display: none !important;
    }

    .package-category-wrap {
        display: none !important;
    }

    .aff-location-gated {
        display: block !important;
    }

    /* Final Tagline Box */
    .aff-hero-tagline {
        padding: 10px 12px;
        border: 1.5px solid;
        border-image: linear-gradient(135deg, rgba(251,113,133,0.7) 0%, rgba(167,116,255,0.7) 100%) 1;
        border-radius: 10px;
        background: linear-gradient(135deg, rgba(251,113,133,0.06), rgba(167,116,255,0.06));
        text-align: center;
        margin: 8px 0 0;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .aff-hero-tagline i {
        color: var(--accent);
        font-size: 16px;
        flex-shrink: 0;
    }

    .aff-hero-tagline-text {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 1px;
        text-align: left;
    }

    .aff-hero-tagline-main {
        font-size: 12px;
        font-weight: 800;
        color: #fff;
    }

    .aff-hero-tagline-sub {
        font-size: 10px;
        color: rgba(255,255,255,0.65);
    }
}
@media (max-width: 420px) {
    .cv-nav-back { padding: 6px 10px !important; font-size: 11.5px !important; }
    .cv-nav-back i { font-size: 10px; }
    .cv-nav-logo-img { height: 80px; max-width: 110px; }
}

@media (min-width: 992px) {
    .aff-hero.cv-venue-header { display: none; }
    .aff-mobile-carousel { display: none !important; }
    .aff-desktop-gallery { display: block !important; }
}

/* ===== Mobile Gallery Carousel ===== */
.aff-mobile-carousel { margin-top: 16px; margin-bottom: 16px; position: relative; user-select: none; }
.aff-mc-track { display: flex; overflow-x: auto; scroll-snap-type: x mandatory; -webkit-overflow-scrolling: touch; scrollbar-width: none; border-radius: 14px; overflow: hidden; cursor: grab; transition: scroll-behavior 0.3s ease; }
.aff-mc-track::-webkit-scrollbar { display: none; }
.aff-mc-track.is-dragging { cursor: grabbing; scroll-behavior: auto; }
.aff-mc-slide { flex: 0 0 100%; scroll-snap-align: start; width: 100%; aspect-ratio: 16 / 9; border: none; padding: 0; background: rgba(255,255,255,0.04); cursor: pointer; overflow: hidden; position: relative; transition: transform 0.3s ease; border-radius: 0; }
.aff-mc-slide img { width: 100%; height: 100%; object-fit: cover; display: block; pointer-events: none; transition: transform .35s ease; user-select: none; }
.aff-mc-slide:hover img { transform: scale(1.08); }
.aff-mc-dots { display: flex; justify-content: center; gap: 8px; padding: 12px 0 4px; }
.aff-mc-dot { width: 8px; height: 8px; border-radius: 50%; border: none; background: rgba(255,255,255,0.25); cursor: pointer; transition: background .2s, width .25s, border-radius .25s; padding: 0; }
.aff-mc-dot.is-active { background: #efbe6f; width: 22px; border-radius: 4px; }

/* ===== Desktop Gallery - Modern Masonry with Effects ===== */
.aff-desktop-gallery { display: none; margin-bottom: 28px; perspective: 1000px; }
.aff-dg-grid { display: grid; gap: 8px; border-radius: 20px; overflow: hidden; border: 1px solid rgba(239,190,111,0.2); box-shadow: 0 12px 60px rgba(0,0,0,0.5), 0 0 0 1px rgba(255,255,255,0.06), inset 0 1px 1px rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(20,15,50,0.4) 0%, rgba(15,14,26,0.4) 100%); }
.aff-dg-count-1 { grid-template-columns: 1fr; grid-template-rows: 450px; }
.aff-dg-count-2 { grid-template-columns: repeat(2, 1fr); grid-template-rows: 400px; }
.aff-dg-count-3 { grid-template-columns: 1.6fr 1fr; grid-template-rows: 210px 210px; }
.aff-dg-count-3 .aff-dg-item:first-child { grid-row: span 2; }
.aff-dg-count-4 { grid-template-columns: repeat(2, 1fr); grid-template-rows: 215px 215px; }
.aff-dg-count-5 { grid-template-columns: 2fr 1fr 1fr; grid-template-rows: 210px 210px; }
.aff-dg-count-5 .aff-dg-item:first-child { grid-row: span 2; }
.aff-dg-item { position: relative; overflow: hidden; border: 1px solid rgba(239,190,111,0.15); padding: 0; background: rgba(255,255,255,0.04); cursor: pointer; display: block; border-radius: 14px; transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1), border-color 0.4s ease; will-change: transform, box-shadow; }
.aff-dg-item::before { content: ''; position: absolute; inset: 0; background: radial-gradient(circle at 30% 30%, rgba(255,255,255,0.1) 0%, transparent 70%); pointer-events: none; z-index: 1; transition: opacity 0.3s ease; opacity: 0; }
.aff-dg-item:hover::before { opacity: 1; }
.aff-dg-item img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.5s cubic-bezier(0.34, 1.56, 0.64, 1), filter 0.4s ease, saturate 0.3s ease; filter: saturate(1.05); }
.aff-dg-overlay { position: absolute; inset: 0; background: linear-gradient(135deg, rgba(167,116,255,0.28) 0%, rgba(239,190,111,0.16) 100%); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity .3s ease; z-index: 2; }
.aff-dg-overlay i { color: #fff; font-size: 28px; filter: drop-shadow(0 4px 12px rgba(0,0,0,0.8)); transform: scale(0.8); transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); }
.aff-dg-item:hover { border-color: rgba(239,190,111,0.4); box-shadow: 0 8px 32px rgba(167,116,255,0.2), inset 0 1px 1px rgba(255,255,255,0.1); transform: translateY(-4px); }
.aff-dg-item:hover img { transform: scale(1.08) rotate(0.5deg); filter: brightness(0.85) saturate(1.2); }
.aff-dg-item:hover .aff-dg-overlay { opacity: 1; }
.aff-dg-item:hover .aff-dg-overlay i { transform: scale(1); }
.aff-dg-item:focus-visible { outline: 2px solid rgba(239,190,111,0.8); outline-offset: 2px; border-color: rgba(239,190,111,0.6); }

/* ===== Location-Gated Categories ===== */
.aff-location-gated { display: none; }
.aff-location-gated.is-visible { display: block; }
.package-category-tile.hidden-tab { display: none !important; }
.package-category-tile.visible-tab { display: flex !important; }

/* ===== Mobile Gallery Carousel (Horizontal) ===== */
.aff-mobile-gallery-carousel { margin: 24px 0; width: 100%; overflow: hidden; }
.aff-mgc-track { display: flex; flex-direction: row; overflow-x: auto; scroll-snap-type: x mandatory; -webkit-overflow-scrolling: touch; scrollbar-width: none; height: auto; gap: 0; padding: 0; user-select: none; cursor: grab; scroll-behavior: smooth; }
.aff-mgc-track::-webkit-scrollbar { display: none; }
.aff-mgc-track.is-dragging { cursor: grabbing; scroll-behavior: auto; }
.aff-mgc-slide { flex: 0 0 100%; scroll-snap-align: start; width: 100%; height: auto; min-height: 400px; border: none; padding: 0; background: transparent; cursor: pointer; overflow: hidden; border-radius: 0; position: relative; transition: transform 0.3s ease; display: flex; align-items: center; justify-content: center; }
.aff-mgc-slide img { width: 100%; height: auto; max-height: 500px; object-fit: contain; display: block; pointer-events: none; transition: transform .35s ease, filter .3s ease; }
.aff-mgc-slide:hover img { transform: scale(1.08); filter: brightness(0.9); }
.aff-mgc-dots { display: flex; justify-content: center; gap: 8px; padding: 12px 0; flex-wrap: wrap; }
.aff-mgc-dot { width: 8px; height: 8px; border-radius: 50%; border: none; background: rgba(255,255,255,0.25); cursor: pointer; transition: background .2s, width .25s, border-radius .25s; padding: 0; }
.aff-mgc-dot.is-active { background: #efbe6f; width: 22px; border-radius: 4px; }

/* ===== Desktop Gallery Grid (4 columns, square) ===== */
.aff-desktop-gallery-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin: 24px 0; }
.aff-dgc-item { position: relative; overflow: hidden; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1); background: rgba(0,0,0,0.5); cursor: pointer; aspect-ratio: 1/1; padding: 0; transition: all 0.3s ease; }
.aff-dgc-item img { width: 100%; height: 100%; object-fit: contain; transition: transform 0.3s ease, filter 0.3s ease; }
.aff-dgc-item:hover img { transform: scale(1.08); filter: brightness(0.9); }
.aff-dgc-item > div { position: absolute; inset: 0; background: rgba(0,0,0,0); display: flex; align-items: center; justify-content: center; transition: background 0.3s ease; }
.aff-dgc-item:hover > div { background: rgba(0,0,0,0.3); }
.aff-dgc-item:hover i { opacity: 1; }
.aff-dgc-item i { opacity: 0; transition: opacity 0.3s ease; }

/* ===== Date Input iOS Fix ===== */
.aff-date-input {
    -webkit-appearance: none !important;
    appearance: none !important;
}
.aff-date-input::placeholder {
    color: rgba(255, 255, 255, 0.5) !important;
    opacity: 1 !important;
    -webkit-text-fill-color: rgba(255, 255, 255, 0.5) !important;
}
.aff-date-input::-webkit-input-placeholder {
    color: rgba(255, 255, 255, 0.5) !important;
    opacity: 1 !important;
}
.aff-date-input::-moz-placeholder {
    color: rgba(255, 255, 255, 0.5) !important;
    opacity: 1 !important;
}

/* ===== Profile Story Indicator (Instagram-style) ===== */
@keyframes aff-story-border-glow {
    0% {
        opacity: 0.6;
        box-shadow: 0 0 8px rgba(239, 190, 111, 0.5), 0 0 16px rgba(167, 116, 255, 0.3);
    }
    50% {
        opacity: 1;
        box-shadow: 0 0 16px rgba(239, 190, 111, 0.8), 0 0 24px rgba(167, 116, 255, 0.5);
    }
    100% {
        opacity: 0.6;
        box-shadow: 0 0 8px rgba(239, 190, 111, 0.5), 0 0 16px rgba(167, 116, 255, 0.3);
    }
}

.aff-profile-story-btn {
    border: none;
    background: none;
    padding: 0;
    cursor: pointer;
    position: relative;
    border-radius: 12px;
    transition: transform 0.2s ease;
}
.aff-profile-story-btn::before {
    content: '';
    position: absolute;
    inset: -3px;
    border-radius: 12px;
    background: linear-gradient(135deg, #efbe6f 0%, #a774ff 50%, #efbe6f 100%);
    z-index: 0;
    animation: aff-story-border-glow 2s ease-in-out infinite;
}
.aff-profile-story-btn:hover {
    transform: scale(1.05);
}
.aff-profile-story-btn:hover::before {
    animation: none;
}
.aff-profile-story-btn img,
.aff-profile-story-btn span {
    position: relative;
    z-index: 1;
    border-radius: 10px;
}

/* ===== Gallery Modal ===== */
.aff-gallery-modal-content {
    background: rgba(5, 7, 14, 0.98) !important;
    border: 1px solid rgba(167, 116, 255, 0.2) !important;
}
.aff-gallery-modal-body {
    padding: 20px !important;
    max-height: 80vh;
    overflow-y: auto;
}
.aff-gallery-carousel-container {
    position: relative;
    margin: 0 auto;
}
.aff-gallery-carousel {
    display: flex;
    width: 100%;
    height: auto;
}
.aff-gallery-carousel-item {
    flex: 0 0 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(0, 0, 0, 0.3);
    border-radius: 12px;
    overflow: hidden;
    min-height: auto;
}
.aff-gallery-carousel-item img {
    width: 100%;
    height: auto;
    object-fit: contain;
    max-width: 100%;
}
.aff-gallery-carousel-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(0, 0, 0, 0.4);
    border: none;
    color: #fff;
    font-size: 24px;
    padding: 12px 16px;
    cursor: pointer;
    border-radius: 6px;
    transition: background 0.2s;
    z-index: 10;
}
.aff-gallery-carousel-nav:hover {
    background: rgba(0, 0, 0, 0.6);
}
.aff-gallery-carousel-prev {
    left: 10px;
}
.aff-gallery-carousel-next {
    right: 10px;
}
.aff-gallery-carousel-indicators {
    display: flex;
    justify-content: center;
    gap: 6px;
    margin-top: 16px;
    flex-wrap: wrap;
}
.aff-gallery-carousel-indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    border: none;
    cursor: pointer;
    transition: background 0.2s;
}
.aff-gallery-carousel-indicator.active {
    background: #efbe6f;
}

/* ===== Location Selector Mobile & iOS Fixes ===== */
@media (max-width: 767px) {
    .aff-location-mobile-select {
        padding: 16px 18px !important;
        font-size: 16px !important;
        font-weight: 700 !important;
        min-height: 56px !important;
        letter-spacing: 0.5px;
    }
    .aff-location-selector {
        margin-bottom: 28px !important;
    }
}

/* ===== Gallery Responsive ===== */
@media (max-width: 767px) {
    .aff-mobile-gallery-carousel { display: block !important; }
    .aff-desktop-gallery-grid { display: none !important; }
}
@media (min-width: 768px) {
    .aff-mobile-gallery-carousel { display: none !important; }
    .aff-desktop-gallery-grid { display: grid !important; }
    .aff-desktop-gallery-grid { grid-template-columns: repeat(4, 1fr); }
}
@media (max-width: 1199px) {
    .aff-desktop-gallery-grid { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 992px) {
    .aff-desktop-gallery-grid { grid-template-columns: repeat(2, 1fr); }
}

/* ===== Category tab club name ===== */
.package-category-label-wrap { display: flex; flex-direction: column; gap: 2px; min-width: 0; }
.package-category-club-name { font-size: 10.5px; font-weight: 500; opacity: 0.62; letter-spacing: .2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

/* ===== Mobile search/filter layout ===== */
@media (max-width: 767px) {
    #package-search-wrap {
        grid-template-columns: 1fr auto !important;
        grid-template-rows: auto auto !important;
    }
    #package-search-wrap .package-search-field:first-child {
        grid-column: 1 / -1 !important;
    }
}

        </style>
        @php
            $gaMeasurementId = preg_replace('/[^A-Za-z0-9_-]/', '', (string) ($data->google_analytics_id ?? ''));
        @endphp
        @if(!empty($gaMeasurementId))
            <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaMeasurementId }}"></script>
            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());
                gtag('config', '{{ $gaMeasurementId }}');
            </script>
        @endif
    </head>

    <body>
        <div class="background-glow"></div>

        {{-- New CartVIP Navbar --}}
        <nav class="cv-top-nav">
            <a href="https://cartvip.com" target="_blank" class="cv-nav-brand">
                <img src="{{ asset('images/logo.png') }}" alt="CartVIP" class="cv-nav-logo-img">
            </a>
            <div class="aff-nav-badges" style="display: flex; gap: 24px; align-items: center;">
                <div class="aff-nav-badge" style="display: flex; align-items: center; gap: 8px; font-size: 12px; white-space: nowrap;">
                    <i class="fas fa-star" style="color: #efbe6f; font-size: 16px; flex-shrink: 0;"></i>
                    <span style="font-weight: 700; color: #fff;">{{ $affiliate->hero_badge_1_label ?: 'Featured' }} <span style="color: rgba(255,255,255,0.65); font-weight: 400;">{{ $affiliate->hero_badge_1_sub ?: 'Premium Partner' }}</span></span>
                </div>
                <div class="aff-nav-badge" style="display: flex; align-items: center; gap: 8px; font-size: 12px; white-space: nowrap;">
                    <i class="fas fa-award" style="color: #efbe6f; font-size: 16px; flex-shrink: 0;"></i>
                    <span style="font-weight: 700; color: #fff;">{{ $affiliate->hero_badge_2_label ?: 'Verified' }} <span style="color: rgba(255,255,255,0.65); font-weight: 400;">{{ $affiliate->hero_badge_2_sub ?: 'Trusted Source' }}</span></span>
                </div>
            </div>
            <button class="cv-hamburger" id="cv-hamburger" aria-label="Open menu">
                <span></span><span></span><span></span>
            </button>
        </nav>

        {{-- Duplicate venue header removed - club details are shown in the hero section --}}

        <header style="background: radial-gradient(circle at 18% 60px, rgba(232,190,106,.10), transparent 340px), radial-gradient(circle at 82% 180px, rgba(124,92,255,.10), transparent 360px), linear-gradient(180deg,#050507 0%,#06070a 100%);">
            <div class="container py-1">
                @session('success')
                    <div class="alert alert-success" role="alert">Purchase Successfull!</div>
                @endsession

                @session('error')
                    <div class="alert alert-danger" role="alert">{{ $value }}</div>
                @endsession

                @php
                    $affiliateHeroImage = !empty($affiliate->banner_image) ? asset('uploads/' . $affiliate->banner_image) : asset('images/logo.png');
                @endphp
                <section class="cv-hero-stage" style="background-image:url('{{ $affiliateHeroImage }}');">
                    <div class="cv-hero-inner">
                        <div class="cv-hero-head">
                            <div class="cv-hero-venue">
                                @if (!empty($affiliate->gallery_images))
                                    <button type="button" id="affProfileStoryBtn" class="aff-profile-story-btn" title="Click to view gallery">
                                        @if ($affiliate->profile_image)
                                            <img src="{{ asset('uploads/' . $affiliate->profile_image) }}" alt="{{ $affiliate->display_name }}" class="cv-hero-venue-avatar">
                                        @else
                                            <span class="cv-hero-venue-initial">{{ strtoupper(substr($affiliate->display_name ?: $affiliate->user->name, 0, 1)) }}</span>
                                        @endif
                                    </button>
                                @else
                                    @if ($affiliate->profile_image)
                                        <img src="{{ asset('uploads/' . $affiliate->profile_image) }}" alt="{{ $affiliate->display_name }}" class="cv-hero-venue-avatar">
                                    @else
                                        <span class="cv-hero-venue-initial">{{ strtoupper(substr($affiliate->display_name ?: $affiliate->user->name, 0, 1)) }}</span>
                                    @endif
                                @endif
                                <div>
                                    <p class="cv-hero-venue-title">{{ $affiliate->display_name ?: $affiliate->user->name }}<span class="cv-hero-venue-verified" title="Verified Partner">&check;</span></p>
                                    <p class="cv-hero-venue-meta">Premium Packages</p>
                                    <!-- Social Links beside profile -->
                                    @if($affiliate->facebook_url || $affiliate->instagram_url)
                                        <div class="aff-social-links" style="display: flex; gap: 8px; margin-top: 6px;">
                                            @if($affiliate->facebook_url)
                                                <a href="{{ $affiliate->facebook_url }}" target="_blank" rel="noopener noreferrer" class="aff-social-link" style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); border-radius: 8px; color: #fff; text-decoration: none; transition: all 0.2s ease;" title="Facebook">
                                                    <i class="fab fa-facebook-f" style="font-size: 15px;"></i>
                                                </a>
                                            @endif
                                            @if($affiliate->instagram_url)
                                                <a href="{{ $affiliate->instagram_url }}" target="_blank" rel="noopener noreferrer" class="aff-social-link" style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); border-radius: 8px; color: #fff; text-decoration: none; transition: all 0.2s ease;" title="Instagram">
                                                    <i class="fab fa-instagram" style="font-size: 15px;"></i>
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="cv-hero-bottom">
                            <div class="cv-hero-content">
                                <div class="aff-kicker">Affiliate Packages</div>
                                @php
                                    $heroTitle = $affiliate->hero_title ?: ($affiliate->display_name ?: $affiliate->user->name);
                                    $titleWords = preg_split('/\s+/', trim($heroTitle));
                                    $heroLastWord = '';
                                    if (count($titleWords) > 1) {
                                        $heroLastWord = array_pop($titleWords);
                                        $heroTitleLead = implode(' ', $titleWords);
                                    } else {
                                        $heroTitleLead = $heroTitle;
                                    }
                                @endphp
                                <h1 class="cv-hero-title">{{ $heroTitleLead }}@if($heroLastWord) <span class="cv-hero-title-accent">{{ $heroLastWord }}</span>@endif</h1>
                                <p class="cv-hero-subtitle">{{ $affiliate->hero_subtitle ?: 'Browse our premium collection' }}</p>
                                @if(!empty($affiliate->description))
                                    <div class="aff-display-copy" style="margin-bottom:16px;">
                                        {{ $affiliate->description }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Features Section - Mobile Only -->
                        <div class="aff-hero-features" style="display: none;">
                            <div class="aff-hero-feature">
                                <div class="aff-hero-feature-icon">
                                    <i class="fas fa-crown"></i>
                                </div>
                                <div class="aff-hero-feature-label">Premium<br>Quality</div>
                            </div>
                            <div class="aff-hero-feature">
                                <div class="aff-hero-feature-icon">
                                    <i class="fas fa-bolt"></i>
                                </div>
                                <div class="aff-hero-feature-label">Fast<br>Delivery</div>
                            </div>
                            <div class="aff-hero-feature">
                                <div class="aff-hero-feature-icon">
                                    <i class="fas fa-lock"></i>
                                </div>
                                <div class="aff-hero-feature-label">Secure<br>Payments</div>
                            </div>
                            <div class="aff-hero-feature">
                                <div class="aff-hero-feature-icon">
                                    <i class="fas fa-headset"></i>
                                </div>
                                <div class="aff-hero-feature-label">Dedicated<br>Support</div>
                            </div>
                        </div>

                        <!-- Final Tagline Box - Mobile Only -->
                        <div class="aff-hero-tagline" style="display: none;">
                            <i class="fas fa-shield-alt"></i>
                            <div class="aff-hero-tagline-text">
                                <div class="aff-hero-tagline-main">Trusted. Verified. Premium.</div>
                                <div class="aff-hero-tagline-sub">Partner with confidence.</div>
                            </div>
                        </div>

                        <!-- Desktop Gallery Carousel (Right side) -->
                        @if(!empty($affiliate->gallery_images))
                            <div class="aff-hero-gallery-carousel" style="display: none; flex: 0 0 auto; width: 340px; height: 100%; position: relative;">
                                <div class="aff-hero-carousel-track" id="affHeroCarouselTrack" style="display: flex; flex-direction: column; height: 100%; position: relative; overflow: hidden; border-radius: 12px;">
                                    @foreach($affiliate->gallery_images as $galleryImage)
                                        <div class="aff-hero-carousel-item" style="display: none; width: 100%; height: 100%; flex: 0 0 100%;">
                                            <img src="{{ asset('uploads/' . $galleryImage) }}" alt="Gallery image" style="width: 100%; height: 100%; object-fit: contain; background: rgba(0,0,0,0.5);">
                                        </div>
                                    @endforeach
                                </div>
                                @if(count($affiliate->gallery_images) > 1)
                                    <button class="aff-hero-carousel-prev" id="affHeroCarouselPrev" style="position: absolute; left: 8px; top: 50%; transform: translateY(-50%); background: rgba(0,0,0,0.4); border: none; color: #fff; width: 36px; height: 36px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; z-index: 10; transition: background 0.2s;">
                                        <i class="fas fa-chevron-up" style="font-size: 16px;"></i>
                                    </button>
                                    <button class="aff-hero-carousel-next" id="affHeroCarouselNext" style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: rgba(0,0,0,0.4); border: none; color: #fff; width: 36px; height: 36px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; z-index: 10; transition: background 0.2s;">
                                        <i class="fas fa-chevron-down" style="font-size: 16px;"></i>
                                    </button>
                                @endif
                            </div>
                        @endif
                    </div>
                </section>

                @if(!empty($affiliate->gallery_images))
                    <!-- Mobile Carousel Gallery (Horizontal) - Hidden, shown in modal -->
                    <div class="aff-mobile-gallery-carousel" id="affMobileGalleryCarousel" style="display: none !important; margin: 24px 0;">
                        <div class="aff-mgc-track" id="affMgcTrack" style="display: flex; flex-direction: row; overflow-x: auto; scroll-snap-type: x mandatory; -webkit-overflow-scrolling: touch; scrollbar-width: none; height: auto; gap: 0; padding: 0; scroll-behavior: smooth;">
                            @foreach($affiliate->gallery_images as $galleryImage)
                                <button type="button" class="aff-mgc-slide js-checkout-gallery-trigger"
                                        data-gallery-src="{{ asset('uploads/' . $galleryImage) }}"
                                        data-gallery-alt="Gallery image {{ $loop->iteration }}"
                                        style="flex: 0 0 100%; scroll-snap-align: start; width: 100%; height: auto; min-height: 400px; border: none; padding: 0; background: transparent; cursor: pointer; overflow: hidden; border-radius: 0; position: relative; display: flex; align-items: center; justify-content: center;">
                                    <img src="{{ asset('uploads/' . $galleryImage) }}" alt="Gallery image" style="width: 100%; height: auto; max-height: 500px; object-fit: contain;">
                                </button>
                            @endforeach
                        </div>
                        @if(count($affiliate->gallery_images) > 1)
                            <div class="aff-mgc-dots" id="affMgcDots" style="display: flex; justify-content: center; gap: 8px; padding: 12px 0; flex-wrap: wrap;">
                                @foreach($affiliate->gallery_images as $galleryImage)
                                    <button type="button" class="aff-mgc-dot{{ $loop->first ? ' is-active' : '' }}" data-slide="{{ $loop->index }}" style="width: 8px; height: 8px; border-radius: 50%; border: none; background: rgba(255,255,255,0.25); cursor: pointer; padding: 0; transition: background .2s, width .25s;"></button>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Desktop Grid Gallery (4 columns) - Hidden, shown in modal -->
                    <div class="aff-desktop-gallery-grid" style="display: none !important; grid-template-columns: repeat(4, 1fr); gap: 12px; margin: 24px 0;">
                        @foreach($affiliate->gallery_images as $galleryImage)
                            <button type="button" class="aff-dgc-item js-checkout-gallery-trigger"
                                    data-gallery-src="{{ asset('uploads/' . $galleryImage) }}"
                                    data-gallery-alt="Gallery image {{ $loop->iteration }}"
                                    style="position: relative; overflow: hidden; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1); background: rgba(0,0,0,0.5); cursor: pointer; aspect-ratio: 1/1;">
                                <img src="{{ asset('uploads/' . $galleryImage) }}" alt="Gallery image" style="width: 100%; height: 100%; object-fit: contain;">
                                <div style="position: absolute; inset: 0; background: rgba(0,0,0,0); display: flex; align-items: center; justify-content: center; transition: background 0.3s ease;">
                                    <i class="fas fa-expand-alt" style="color: #fff; font-size: 20px; opacity: 0;"></i>
                                </div>
                            </button>
                        @endforeach
                    </div>
                @endif

                <section class="aff-story">
                    <h2>{{ $data->description_label ?? 'Description' }}</h2>
                    <div class="story-copy-block is-collapsed" data-mobile-collapsible>
                        <div class="story-copy story-copy-collapsible">{{ $affiliate->description }}</div>
                        <button type="button" class="story-copy-toggle" aria-expanded="false">See more</button>
                    </div>
                </section>
            </div>
        </header>
        <main style="background: radial-gradient(circle at 18% 60px, rgba(232,190,106,.10), transparent 340px), radial-gradient(circle at 82% 180px, rgba(124,92,255,.10), transparent 360px), linear-gradient(180deg,#050507 0%,#06070a 100%);">
            <div class="container mt-4">
                <div class="cv-checkout-body" id="cv-checkout-layout">
                <button type="button" class="cv-mobile-cart-toggle" id="cv-mobile-cart-toggle" style="display:none;">
                    <span><i class="fas fa-shopping-cart" style="margin-right:6px;"></i>View Order Summary</span>
                    <span class="cv-mobile-cart-count" id="cv-mobile-cart-count">0 items</span>
                </button>
                <div class="cv-main-col" id="cv-checkout-main">
                    <div class="cv-desktop-shell">
                        <div class="cv-desktop-steps" id="cv-checkout-steps">
                            <div class="cv-dstep is-active" id="cv-dstep-1" data-step="1"><span class="cv-dstep-num">1</span><span>Choose Date</span></div>
                            <div class="cv-dstep" id="cv-dstep-2" data-step="2"><span class="cv-dstep-num">2</span><span>Choose Access</span></div>
                            <div class="cv-dstep" id="cv-dstep-3" data-step="3"><span class="cv-dstep-num">3</span><span>Select Package</span></div>
                            <div class="cv-dstep" id="cv-dstep-4" data-step="4"><span class="cv-dstep-num">4</span><span>Review &amp; Pay</span></div>
                        </div>

                    </div>

                <div class="package">
                    <section class="vip-pack">
                        <div class="">
    
                            <div class="row">
                                <div class="col-md-12">

                                    @php
                                        $mostPopularPackageName = '';
                                        if (isset($packageCategories) && count($packageCategories)) {
                                            $mostPopularPackage = collect($packageCategories)
                                                ->flatMap(function ($category) {
                                                    return is_array($category)
                                                        ? collect($category['packages'] ?? [])
                                                        : collect($category->packages ?? []);
                                                })
                                                ->first(function ($package) {
                                                    return (int) ($package->is_most_popular ?? 0) === 1;
                                                });

                                            $mostPopularPackageName = $mostPopularPackage->name ?? '';
                                        }
                                    @endphp

                                    <!-- Reservation Date Selection -->
                                    <div class="hero-date-card" style="margin-bottom: 24px;">
                                        <label>Choose Your Reservation Date</label>
                                        <div class="date-input-wrapper">
                                            <input type="text" id="package_use_date" class="aff-date-input" style="width: 100%; padding: 12px 14px; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.2); border-radius: 8px; color: #fff; font-size: 16px; font-weight: 500;" required aria-required="true" aria-describedby="package_use_date_error" placeholder="{{ \Carbon\Carbon::now('America/Los_Angeles')->format('M d, Y') }}">
                                        </div>
                                        <small id="package_use_date_error" class="reservation-date-error" style="display:none;">Please select a reservation date.</small>
                                    </div>

                                    <!-- Search & Location Filters for Affiliate -->
                                    <div class="aff-location-selector" style="margin-bottom: 24px;">
                                        <label style="display: block; font-size: 12px; text-transform: uppercase; letter-spacing: .6px; opacity: .68; font-weight: 700; margin: 0 0 8px 0;">Choose Your Location</label>
                                        <select id="package-location-filter-main" class="aff-location-mobile-select" style="width: 100%; min-height: 48px; background: rgba(255,255,255,0.08) !important; border: 1px solid rgba(255,255,255,0.2) !important; border-radius: 10px !important; color: #fff !important; padding: 12px 14px !important; font-size: 14px; font-weight: 600; -webkit-appearance: none; -moz-appearance: none; appearance: none;">
                                            <option value="">-- Select a Location --</option>
                                            @foreach($uniqueClubsForFilter as $clubOption)
                                                <option value="{{ $clubOption->id }}">{{ $clubOption->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div id="package-no-location-message" style="display: block; text-align: center; padding: 40px 20px; color: rgba(255,255,255,0.5); font-size: 14px;">
                                        <p style="margin: 0;">👆 Select a location above to see available packages</p>
                                    </div>

                                    <!-- Select Your Package Header - Show only when location selected -->
                                    <div class="cv-package-section-header aff-package-header-gated" style="display:none; justify-content:space-between; align-items:center; margin: 18px 0 12px; flex-wrap:wrap; gap:10px;">
                                        <div>
                                            <h5 class="section-kicker-lg" style="margin:0 !important;">{{ $data->package_section_title ?: 'Select Your Package' }}</h5>
                                            <p style="margin: 4px 0 0; font-size: 12.5px; color: rgba(255,255,255,0.5);">{{ $data->package_section_subtext ?: 'All packages include free ride, club entry, and priority access.' }}</p>
                                        </div>
                                    </div>


                                    @if(isset($packageCategories) && count($packageCategories))
                                        <div class="aff-location-gated mb-3 package-category-tiles" style="width:100%;">
                                            @foreach ($packageCategories as $category)
                                                @php
                                                    $catRgbStr = null;
                                                    if (!empty($category['color'])) {
                                                        $ch = ltrim($category['color'], '#');
                                                        [$cr, $cg, $cb] = sscanf($ch, '%02x%02x%02x');
                                                        $catRgbStr = "$cr,$cg,$cb";
                                                    }
                                                @endphp
                                                <div class="package-category-wrap">
                                                    @php $catClub = !empty($category['packages']) ? ($category['packages'][0]->website->name ?? '') : ''; @endphp
                                                    <button
                                                        type="button"
                                                        class="package-category-tile{{ $catRgbStr ? ' has-cat-color' : '' }}"
                                                        data-target="#category-group-{{ $category['id'] }}"
                                                        @if($catRgbStr) style="--cat-rgb: {{ $catRgbStr }}" @endif
                                                    >
                                                        @if(!empty($category['icon']))
                                                            <i class="fas {{ $category['icon'] }} package-category-tile-icon"></i>
                                                        @endif
                                                        <span class="package-category-label-wrap">
                                                            <span class="package-category-name">{{ $category['name'] }}</span>
                                                            @if($catClub)
                                                                <span class="package-category-club-name">{{ $catClub }}</span>
                                                            @endif
                                                        </span>
                                                        <span class="package-category-indicator">+</span>
                                                    </button>

                                                    <div id="category-group-{{ $category['id'] }}" class="package-category-group" style="display: none;">
                                                @foreach ($category['packages'] as $item)
                                                    @php
                                                        $pkgGuestCap = max(1, (int) ($item->guests_per_table ?: $item->number_of_guest ?: 1));
                                                        $tableCap = max(2, (int) ($item->guests_per_table ?: $item->number_of_guest ?: 2));
                                                        $pkgIsTicket = ($item->package_type ?? 'table') === 'ticket';
                                                        $pkgTicketMax = max(1, (int) ($item->number_of_guest ?: 1));
                                                        $pkgTableMax  = max(2, (int) ($item->guests_per_table ?: $item->number_of_guest ?: 2));
                                                        $fallbackVisual = $data->logo ? asset('uploads/' . $data->logo) : asset('images/logo.png');
                                                        $packageVisual = !empty($item->image) ? asset('uploads/' . $item->image) : $fallbackVisual;
                                                        $packageMobileVisual = !empty($item->mobile_image) ? asset('uploads/' . $item->mobile_image) : $packageVisual;
                                                        $tierIndex = ($loop->index % 4) + 1;
                                                        $tierIcons = [1 => 'fa-crown', 2 => 'fa-star', 3 => 'fa-gem', 4 => 'fa-fire'];
                                                        $tierIcon = $tierIcons[$tierIndex] ?? 'fa-crown';
                                                    @endphp
                                                    <div class="vip-card cv-exact-card cv-tier-{{ $tierIndex }}" id="pkg-card-{{ $item->id }}" data-package-name="{{ $item->name }}" data-club-name="{{ $item->website->name ?? '' }}" data-location="{{ $item->website->location ?? '' }}" data-club-id="{{ $item->website->id ?? '' }}">
                                                        <div class="cv-pkg-media-wrap">
                                                            <picture>
                                                                <source media="(max-width: 767px)" srcset="{{ $packageMobileVisual }}">
                                                                <img src="{{ $packageVisual }}" alt="{{ $item->name }}" class="cv-pkg-media">
                                                            </picture>
                                                        </div>

                                                        <div class="vip-card-main">
                                                            <div class="cv-pkg-title-row">
                                                                <i class="fas {{ $tierIcon }} cv-pkg-title-icon"></i>
                                                                <div class="cv-pkg-title">{{ $item->name }}</div>
                                                            </div>
                                                            @if($item->website && $item->website->name)
                                                                <div class="cv-club-name-badge" style="font-size: 12px; color: rgba(255,255,255,0.65); margin-bottom: 8px; display: flex; align-items: center; gap: 6px; cursor: help;" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html="true" title="<strong>{{ $item->website->name }}</strong><br><small>{{ $item->website->location ?? 'Location not specified' }}</small>"><i class="fas fa-map-marker-alt" style="opacity: 0.7;"></i>{{ $item->website->name }}</div>
                                                            @endif
                                                            @if($pkgIsTicket)
                                                                <span class="cv-pkg-sub"><i class="fas fa-ticket-alt"></i>1 ticket per person</span>
                                                            @else
                                                                <span class="cv-pkg-sub"><i class="fas fa-user-friends"></i>Up to {{ $pkgTableMax }} guests</span>
                                                            @endif
                                                            @if($item->description)
                                                                <p class="cv-pkg-desc">{{ strip_tags($item->description) }}</p>
                                                            @endif
                                                            @php
                                                                $defaultPackageFeatures = [
                                                                    ['icon' => 'fa-chair', 'text' => 'VIP Table'],
                                                                    ['icon' => 'fa-wine-bottle', 'text' => '1 Premium Bottle'],
                                                                    ['icon' => 'fa-user-shield', 'text' => 'VIP Hosts'],
                                                                    ['icon' => 'fa-shield-alt', 'text' => $item->package_type === 'ticket' ? 'Free Entry' : 'Skip the Line'],
                                                                ];

                                                                $packageFeatures = collect(is_array($item->package_features) ? $item->package_features : [])
                                                                    ->map(function ($feature) {
                                                                        $icon = trim((string) ($feature['icon'] ?? ''));
                                                                        $text = trim((string) ($feature['text'] ?? ''));

                                                                        if ($text === '') {
                                                                            return null;
                                                                        }

                                                                        if (!preg_match('/^fa-[a-z0-9-]+$/i', $icon)) {
                                                                            $icon = 'fa-chair';
                                                                        }

                                                                        return [
                                                                            'icon' => strtolower($icon),
                                                                            'text' => $text,
                                                                        ];
                                                                    })
                                                                    ->filter()
                                                                    ->values();

                                                                if ($packageFeatures->isEmpty()) {
                                                                    $packageFeatures = collect($defaultPackageFeatures);
                                                                }
                                                            @endphp
                                                            <div class="cv-pkg-features">
                                                                @foreach($packageFeatures as $feature)
                                                                    <span class="cv-pkg-feature"><i class="fas {{ $feature['icon'] }}"></i>{{ $feature['text'] }}</span>
                                                                @endforeach
                                                            </div>
                                                        </div>

                                                        <div class="vip-card-side">
                                                            <div class="vip-price-tag price-{{ $item->id }}" data-price="{{ $item->price }}">${{ number_format((float) $item->price, 2) }}</div>
                                                            @if(!$pkgIsTicket)
                                                                <div class="cv-price-meta">Per Package</div>
                                                            @endif
                                                            <div class="package-guest-input-wrap">
                                                                @if ($item->package_type === 'ticket')
                                                                    @php $ticketInitMax = min(15, max(1, (int) ($item->number_of_guest ?? 1))); @endphp
                                                                    <select
                                                                        data-package-type="{{ $item->package_type }}"
                                                                        data-guests-per-table="{{ (int) ($item->guests_per_table ?? 0) }}"
                                                                        data-package-guest-limit="{{ (int) ($item->number_of_guest ?? 1) }}"
                                                                        data-ticket-max="{{ (int) ($item->number_of_guest ?? 1) }}"
                                                                        data-multiple="{{ $item->multiple }}"
                                                                        data-id="{{ $item->id }}"
                                                                        class="form-select package_number_of_guestss ticket-select-lazy"
                                                                        required
                                                                    >
                                                                        <option value=""># of Tickets</option>
                                                                        @for ($i = 1; $i <= $ticketInitMax; $i++)
                                                                            <option value="{{ $i }}">{{ $i }} {{ $i === 1 ? 'ticket' : 'tickets' }}</option>
                                                                        @endfor
                                                                    </select>
                                                                @else
                                                                    <select
                                                                        data-package-type="{{ $item->package_type }}"
                                                                        data-guests-per-table="{{ (int) ($item->guests_per_table ?? 0) }}"
                                                                        data-package-guest-limit="{{ $tableCap }}"
                                                                        data-multiple="{{ $item->multiple }}"
                                                                        data-id="{{ $item->id }}"
                                                                        class="form-select package_number_of_guestss"
                                                                        required
                                                                    >
                                                                        <option value=""># of Guests</option>
                                                                        @for ($i = 1; $i <= $tableCap; $i++)
                                                                            <option value="{{ $i }}">{{ $i }} {{ $i === 1 ? 'guest' : 'guests' }}</option>
                                                                        @endfor
                                                                    </select>
                                                                @endif
                                                            </div>
                                                            <button type="button" class="vip-btn btn-{{ $item->id }} mt-2" style="background-color: {{ $brandPrimary }} !important;"
                                                                data-id="{{ $item->id }}" data-name="{{ $item->name }}" data-price="{{ $item->price }}"
                                                                data-gratuity="{{ $data->gratuity_fee }}"
                                                                data-refundable="{{ $data->refundable_fee }}"
                                                                data-sales_tax="{{ $data->sales_tax_fee ?? 10}}"
                                                                data-transportation="{{ $item->transportation }}"
                                                                data-service_charge="{{ $data->service_charge_fee ?? 10}}"
                                                                data-default-label="Add to Cart">{{ 'Add to Cart' }}</button>
                                                            <small class="package-guest-error" style="display:none;color:#ff6b6b;font-size:11px;line-height:1.35;margin-top:4px;"></small>
                                                            <div class="package-soldout" style="display:none;color:#ff2b2b;font-size:12px;font-weight:700;line-height:1.35;margin-top:4px;">Sold Out!</div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                                    </div>
                                                </div>
                                        @endforeach
                                    @else
                                        <p style="opacity:.6;">No packages are available yet.</p>
                                    @endif

                                    {{-- <div class="cv-freeride-callout">
                                        <div class="cv-freeride-icon"><i class="fas fa-car-side"></i></div>
                                        <div>
                                            <strong>Free Ride Included</strong>
                                            <span>Complimentary pickup &amp; return for you and your guests. We'll contact you after booking to confirm details.</span>
                                        </div>
                                    </div> --}}

                                    <section id="cart-section" class="container py-4" style="display:none; margin-bottom:2rem;">
                                        <div class="cart-heading">Your Cart</div>
                                        <div id="cart-list"></div>
                                        <div id="cart-total" style="font-size:15px;margin-top:8px;font-weight:600;"></div>
                                        <div id="cart-coupon" style="font-size:13px;color:#4caf7d;margin-top:4px;"></div>
                                    </section>
                                    <div class="row pricing-shell g-3">
                                        <div class="text-start mt-3 col-md-6">
                                            <div style="font-size: 16px;" class="default-price">Package: <span>$0.00</span>
                                            </div>
                                            <div class="dynamic-price" style="display: none;">
                                                <input type="hidden" id="old_price">
                                                <div style="font-size: 16px;" class="default-package-price"><span>Subtotal</span>
                                                    <span>$0.00</span></div>
                                                <div class="addonns"></div>

                                                @if ($data->service_charge_name != 0)
                                                    <div style="font-size: 16px;" class="default-service-charge" data-tip="Covers reservation coordination, operational support, and service-related costs.">
                                                        <span>{{ $data->service_charge_name ?? 'Service Fee' }}</span> <span>$0.00</span>
                                                    </div>
                                                @endif
                                                <div class="sales_tax"></div>
                                                @if ($data->sales_tax_name != 0)
                                                    <div style="font-size: 16px;" class="default-sales-tax" data-tip="Government-required sales tax based on local and state regulations.">
                                                        <span>{{ $data->sales_tax_name ?? 'Tax' }}</span> <span>$0.00</span></div>
                                                @endif

                                                @if ($data->gratuity_name != 0)
                                                    <div style="font-size: 16px;" class="default-gratuity" data-tip="Supports venue staff and hospitality service for your experience.">
                                                        <span>{{ $data->gratuity_name ?? 'Gratuity Fee' }}</span> <span>$0.00</span></div>
                                                @else
                                                    <div class="default-gratuity"></div>
                                                @endif

                                                <div style="font-size: 16px; font-weight: bold; display: none"
                                                    class="default-total"><span>Total</span> <span>$0.00</span></div>
                                            </div>



                                            <div class="default-deposit" style="border-top: unset !important; background: transparent !important; padding: 21px 29px !important;"><span>Total</span><span>$0.00</span></div>
                                            @if ($data->refundable_fee > 0)
                                                <div style="font-size: 16px; font-weight: 700; color: {{ $brandSecondary }} !important;"
                                                    class="vip-price default-refundable">
                                                    {{ $data->refundable_name ?? 'Non Refundable Processing Fees' }}:
                                                    <span class="refundable-amount">$0.00</span><span class="pay-now-tag">(Pay Now)</span></div>
                                                <div style="font-size: 16px; font-weight: 700; color: {{ $brandSecondary }} !important;"
                                                    class="vip-price default-due">DUE ON ARRIVAL: <span class="due-amount">$0.00</span></div>
                                            @endif

                                            @if ($data->sales_tax_name == 0)
                                                <div style="font-size: 10px; font-weight: 700; color: {{ $brandSecondary }} !important;"
                                                    class="vip-price"><span>*No sales tax applied. Services sold are not subject to sales tax under Nevada law. Please consult a tax advisor for your local region�if�applicable.</span></div>
                                            @endif
                                        </div>
                                        <div class="col-md-6 dynamic-price" style="display: none;">
                                            <label
                                                style="color: rgba(255,255,255,0.7); font-size: 13.5px;">{{ $data->promo_code_name ?: 'Have a promo code?' }}</label>
                                            <div class="row">
                                                <div class="col-md-8 col-8" style="padding-right: 0%;">
                                                    <input type="text" id="promo_code" style="color: #fff;"
                                                        placeholder="Enter code" />
                                                </div>
                                                <div class="col-md-4 col-4" style="padding-left: 0%;">
                                                    <button type="button" class="vip-btn-submit"
                                                        id="applyPromoBtn">Apply</button>
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

                                    <div style="display:none; margin: 10px 5px 14px; font-size: 12px; line-height: 1.5; color: rgba(255,255,255,0.82);" class="dynamic-price">
                                        This experience is fulfilled by the venue. Entry is subject to venue rules including minimum age requirements (18+ or 21+ depending on venue), valid ID, and dress code.
                                    </div>

                                    <form action="{{ route('checkout.store', ['slug' => $data->slug]) }}" id="payment-form" method="post">
                                        @csrf
                                        


                                        
                                        <!-- Step 1: Package Holder Info -->
                                        <section class="checkout-section holder-info dynamic-price mt-4" id="section-1" style="display: none; width: 100%;">
                                            <div class="">
                                                <div class="row">
    
                                                    <div class="col-md-12">
    
                                                        <h2 style="margin-bottom: 35px;">Personal details <span style="font-size: 1rem;"> Is this package being purchased for someone else? If so enter their legal name here (must present ID upon entry): </span></h2>
    
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
                                                            <div class="form-row">
                                                                <div class="form-group" style="width: 100%;">
                                                                    <label for="Pick-up-time">Pick-up Time</label>
                                                                    <div class="pickup-time-wrap">
                                                                        <i class="fas fa-clock pickup-time-icon"></i>
                                                                        <input name="transportation_pickup_time" type="text" readonly
                                                                            id="Pick-up-time"
                                                                            class="form-control"
                                                                            placeholder="Select pick-up time" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-row" style="margin-top: 14px;">
                                                                <div class="form-group" style="width: 100%;">
                                                                    <label for="address">Pick-up Location</label>
                                                                    <input type="text" name="transportation_address"
                                                                        id="address" placeholder="Enter pick-up address" />
                                                                </div>

                                                            </div>
    
                                                            <div class="form-row">
                                                                <div class="form-group" style="width: 100%;">
                                                                    <label for="phone">Contact Phone Number or WhatsApp</label>
                                                                    <input type="tel" name="transportation_phone" id="phone"
                                                                        placeholder="For driver/dispatch to coordinate�pickup"  />
                                                                </div>
    
                                                            </div>
    
                                                            <div class="form-row">
                                                                <div class="num-guest" style="width: 100%; display: flex;">
                                                                    <label for="">Number of Guest(s)</label>
    
                                                                    <input type="number" class="form-control"
                                                                        name="transportation_guest" value="0" min="1" required
                                                                        style="width: 120px; max-width: 120px; color: #fff;"  />
    
    
    
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

                                        <input type="hidden" name="cart_items" id="cart_items">
    
                                        <input type="hidden" name="package_id" id="package_id">
    
                                        <input type="hidden" name="total" id="subtotal">

                                        <input type="hidden" name="payment_total" class="payment_total">

                                        <input type="hidden" name="commission_base_amount" id="commission_base_amount">

                                        <input type="hidden" name="website_id" value="{{ $data->id }}">

                                        <input type="hidden" name="affiliate_slug" value="{{ $affiliateReferral->slug ?? '' }}">
    
                                        <input type="hidden" name="package_number_of_guest" class="package_number_of_guest" value="2">
    
                                        <!-- Step 3: Payment Information -->
                                        <section class="checkout-section payment-info dynamic-price mt-4" id="section-3" style="display: none;">
                                            <div class="">
                                                <div class="row">
    
                                                    <div class="col-md-12">
                                                        <h2 style="margin-bottom: 35px;">Payment</h2>
    
                                                        <!-- Left: Form Fields -->
                                                        <div class="form-left">
    
                                                            <button type="button" class="same-as-info">Same as package holder information</button>
    
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
    
                                                            
                                                                @php
                                                                    $stockPaymentLogoMap = [
                                                                        'visa' => ['name' => 'Visa', 'logo' => 'https://img.icons8.com/color/48/000000/visa.png'],
                                                                        'mastercard' => ['name' => 'Mastercard', 'logo' => 'https://img.icons8.com/color/48/000000/mastercard-logo.png'],
                                                                        'amex' => ['name' => 'Amex', 'logo' => 'https://img.icons8.com/color/48/000000/amex.png'],
                                                                        'google_pay' => ['name' => 'Google Pay', 'logo' => 'https://img.icons8.com/color/48/000000/google-pay-india.png'],
                                                                        'apple_pay' => ['name' => 'Apple Pay', 'logo' => 'https://img.icons8.com/color/48/000000/apple-pay.png'],
                                                                    ];

                                                                    $paymentLogosToRender = $data->paymentLogos->map(function ($logo) use ($stockPaymentLogoMap) {
                                                                        $logoKey = strtolower(trim((string) $logo->logo));

                                                                        if (isset($stockPaymentLogoMap[$logoKey])) {
                                                                            return [
                                                                                'src' => $stockPaymentLogoMap[$logoKey]['logo'],
                                                                                'name' => $stockPaymentLogoMap[$logoKey]['name'],
                                                                            ];
                                                                        }

                                                                        if ($logoKey === '') {
                                                                            return null;
                                                                        }

                                                                        if (str_starts_with($logoKey, 'http://') || str_starts_with($logoKey, 'https://')) {
                                                                            return [
                                                                                'src' => $logoKey,
                                                                                'name' => $logo->name,
                                                                            ];
                                                                        }

                                                                        return [
                                                                            'src' => asset('uploads/' . $logo->logo),
                                                                            'name' => $logo->name,
                                                                        ];
                                                                    })->filter()->values();

                                                                    if ($paymentLogosToRender->isEmpty()) {
                                                                        $paymentLogosToRender = collect($stockPaymentLogoMap)->map(fn ($method) => [
                                                                            'src' => $method['logo'],
                                                                            'name' => $method['name'],
                                                                        ])->values();
                                                                    }
                                                                @endphp
                                                            @if ($data->payment_method == 'authorize')
                                                                <div class="form-row">
                                                                    <div class="form-group" style="width: 100%;">
                                                                        <!-- Payment method logos start -->
                                                                        <div style="margin-bottom: 10px;">
                                                                            @foreach($paymentLogosToRender as $logo)
                                                                                <img src="{{ $logo['src'] }}" alt="{{ $logo['name'] }}" style="height:32px; margin-right:4px;">
                                                                            @endforeach
                                                                        </div>
                                                                        <label for="card_number">Card Number</label>
                                                                        <input type="tel" name="card_number" id="card_number"
                                                                            placeholder="" inputmode="numeric" autocomplete="cc-number" maxlength="19" required />
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
                                                                        @foreach($paymentLogosToRender as $logo)
                                                                            <img src="{{ $logo['src'] }}" alt="{{ $logo['name'] }}" style="height:32px; margin-right:4px;">
                                                                        @endforeach
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
                                                                <label class="consent-label">
                                                                    <input type="checkbox" id="businessExpenseCheckbox" />
                                                                    <span>This purchase is for business purposes</span>
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
    
                                                            <div class="checkbox-container payment-consent-group" id="payment-consent-group">
                                                                <label class="consent-label">
                                                                    <input type="checkbox" id="smsConsent" required />
                                                                    <span>I agree to receive SMS communications from
                                                                    {{ $data->name }}
                                                                    regarding my upcoming
                                                                    reservation. Message and data rates may apply. Messaging
                                                                    frequency may vary. Reply
                                                                    STOP to opt out at any time.</span>
                                                                </label>
                                                                <label class="consent-label driver-notification-consent-wrap" style="display:none;">
                                                                    <input type="checkbox" id="driverNotificationConsent" class="driver-notification-consent-input" />
                                                                    <span>I agree to receive notifications from the driver regarding my transportation pickup.</span>
                                                                </label>
    
                                                                <label class="consent-label" style="margin-top: 1.4rem;">
                                                                    <input type="checkbox" id="termsConsent" required />
                                                                    <span>I understand that all sales are final. I agree to the <a
                                                                        target="_blank" href="{{ $data->terms }}">Terms of
                                                                        Service</a>, and acknowledge that CartVIP is the merchant of record for this purchase.</span>
                                                                </label>

                                                                <p style="margin: 12px 0 0; font-size: 12px; line-height: 1.5; color: rgba(255,255,255,0.82);">
                                                                    All bookings are processed by CartVIP. By completing this purchase, you agree to our no-refund policy and venue entry requirements.
                                                                </p>
                                                                <p style="margin: 8px 0 0; font-size: 12px; line-height: 1.5; color: rgba(255,255,255,0.72);">
                                                                    By completing this purchase, you confirm you are authorized to use this payment method and agree not to initiate a chargeback without contacting CartVIP first.
                                                                </p>
                                                            </div>

                                                            <input type="hidden" class="package_use_date" name="package_use_date" value="">
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

                </div>{{-- end .package --}}
                </div>{{-- end cv-main-col --}}

                <aside class="cv-sidebar" id="cv-order-sidebar" style="width: 100% !important;">
                    <div class="cv-sidebar-header">
                        <span>ORDER SUMMARY</span>
                        {{-- <button type="button" class="cv-sidebar-edit-btn" id="cv-edit-cart" style="display:none;"><i class="fas fa-pen"></i> Edit Cart</button> --}}
                    </div>

                    @php
                        $sidebarVenueImage = !empty($affiliate->banner_image ?? null) ? asset('uploads/' . $affiliate->banner_image) : ($data->logo ? asset('uploads/' . $data->logo) : null);
                    @endphp
                    @if($sidebarVenueImage)
                        <img src="{{ $sidebarVenueImage }}" class="cv-sidebar-venue-image" alt="{{ $data->name }}">
                    @endif

                    <div class="cv-sidebar-venue-row" style="border-bottom:none; padding-bottom:0; margin-bottom:14px;">
                        <div style="flex:1; min-width:0;">
                            <div class="cv-sidebar-venue-name">{{ $data->name }}</div>
                            <div class="cv-sidebar-venue-date" id="cv-sidebar-date">
                                <i class="fas fa-calendar-alt" style="margin-right:4px;opacity:.6;"></i>Select a date above
                            </div>
                        </div>
                    </div>

                    <div id="cv-sidebar-body"></div>

                    <!-- Generate Shareable Link Section -->
                    <div class="mt-3" id="shareLinkContainer" style="margin: 16px 0; padding: 12px 0; border-top: 1px solid rgba(255,255,255,0.08); border-bottom: 1px solid rgba(255,255,255,0.08);">
                        <button type="button" id="generateShareLink" style="width: 100%; background: linear-gradient(135deg, #a774ff 0%, #7c3aed 50%, #5b21b6 100%); color: #fff; border: none; padding: 10px 14px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; margin-bottom: 8px;">Generate Shareable Link</button>
                        <div style="position: relative;">
                            <input type="text" id="shareableLink" readonly style="width:100%; margin-top:0; display:none; padding:8px 10px; padding-right:40px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 6px; color: #fff; font-size: 12px;" />
                            <div id="copyTooltip" style="position: absolute; top: -35px; right: 0; background: #28a745; color: white; padding: 8px 12px; border-radius: 4px; font-size: 12px; display: none; white-space: nowrap; z-index: 1000;">
                                Link copied!
                            </div>
                        </div>
                        <div id="shareActions" style="display:none; gap:6px; flex-wrap:wrap; margin-top:8px;">
                            <button type="button" class="checkout-share-btn" data-share="email" style="background:rgba(255,255,255,0.08); color:#fff; border:1px solid rgba(255,255,255,0.2); padding:6px 10px; border-radius:6px; font-size:11px; flex: 1; cursor: pointer;">Email</button>
                            <button type="button" class="checkout-share-btn" data-share="whatsapp" style="background:rgba(255,255,255,0.08); color:#fff; border:1px solid rgba(255,255,255,0.2); padding:6px 10px; border-radius:6px; font-size:11px; flex: 1; cursor: pointer;">WhatsApp</button>
                            <button type="button" class="checkout-share-btn" data-share="facebook" style="background:rgba(255,255,255,0.08); color:#fff; border:1px solid rgba(255,255,255,0.2); padding:6px 10px; border-radius:6px; font-size:11px; flex: 1; cursor: pointer;">Facebook</button>
                            <button type="button" class="checkout-share-btn" data-share="copy" style="background:rgba(255,255,255,0.08); color:#fff; border:1px solid rgba(255,255,255,0.2); padding:6px 10px; border-radius:6px; font-size:11px; flex: 1; cursor: pointer;">Copy</button>
                        </div>
                    </div>

                    @php
                        $refundablePct = (int) ($data->refundable_fee ?? 0);
                    @endphp
                    <div class="cv-deposit-box dynamic-price" id="cv-deposit-box" style="display:none;">
                        <div class="cv-deposit-content">
                            <div class="cv-deposit-top">
                                <div class="cv-deposit-label" data-tip="@if($refundablePct > 0){{ $refundablePct }}% of the total is collected today to secure your reservation. The balance is paid on arrival at the venue.@else You're paying the full amount today.@endif">@if($refundablePct > 0)Due Today ({{ $refundablePct }}% Deposit)@else{{ 'Due Today' }}@endif <span class="cv-info-icon">i</span></div>
                                <div class="cv-deposit-shield" data-tip="Secure checkout — your payment is protected by bank-level SSL encryption and never stored on this site." data-tip-right><i class="fas fa-shield-alt"></i></div>
                            </div>
                            <div class="cv-deposit-main" id="cv-deposit-display">$0.00</div>
                            <div class="cv-deposit-sub">Secure your reservation</div>
                            @if($refundablePct > 0)
                                <div class="cv-deposit-due-row">
                                    <span>Due on Arrival</span>
                                    <span id="cv-due-on-arrival">$0.00</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="cv-trust-list">
                        <div class="cv-trust-item"><i class="fas fa-lock"></i><div><strong>Secure Checkout</strong><span>Your payment is encrypted and securely processed</span></div></div>
                        <div class="cv-trust-item"><i class="fas fa-check-circle"></i><div><strong>Instant Confirmation</strong><span>Receive your booking details immediately after checkout</span></div></div>
                        <div class="cv-trust-item"><i class="fas fa-bolt"></i><div><strong>Priority Reservation Access</strong><span>Reservation request and package details submitted instantly</span></div></div>
                        <div class="cv-trust-item"><i class="fas fa-headset"></i><div><strong>Customer Support Available</strong><span>Assistance available before and after your reservation</span></div></div>
                    </div>

                    {{-- <button type="button" class="cv-cta-btn dynamic-price" id="cv-sidebar-cta" style="display:none;" disabled>
                        Continue to Payment <i class="fas fa-lock"></i>
                    </button> --}}
                    <p class="cv-cta-terms">By continuing, you agree to our <a href="{{ $data->terms }}" target="_blank">Terms of Service</a> and <a href="{{ $data->privacy_policy ?? $data->terms }}" target="_blank">Privacy Policy</a></p>
                </aside>

                </div>{{-- end cv-checkout-body --}}

                    {{-- Location info now lives in the hero (.cv-hero-location). Original section removed to avoid duplication. --}}
    
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

                <div class="modal fade" id="addonSelectionModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content" style="background:#1a1d2e;color:#ddd;">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addonSelectionModalTitle" style="color:#fff;">Select Add-ons</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body" id="addonSelectionModalBody"></div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No add-ons</button>
                                <button type="button" class="btn" id="addonModalConfirmBtn" style="background:var(--aff-accent);color:#000;font-weight:700;">Confirm & Add to Cart</button>
                            </div>
                        </div>
                    </div>
                </div>

                @if(isset($checkoutPopup) && $checkoutPopup)
                    <div class="modal fade" id="checkoutPopupModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content" style="background:#111a2e;color:#f4f6ff;border:1px solid rgba(255,255,255,.12);">
                                <div class="modal-header {{ empty($checkoutPopup->title) ? 'justify-content-end' : '' }}" style="border-bottom:1px solid rgba(255,255,255,.1);">
                                    @if(!empty($checkoutPopup->title))
                                        <h5 class="modal-title" style="color:#fff;">{{ $checkoutPopup->title }}</h5>
                                    @endif
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    @if($checkoutPopup->image_path)
                                        <img src="{{ asset('uploads/' . $checkoutPopup->image_path) }}" alt="Popup" style="display:block;max-width:100%;width:auto;max-height:70vh;height:auto;object-fit:contain;border-radius:10px;margin:0 auto 14px;background:#0b1222;">
                                    @endif
                                    @if(!empty($checkoutPopup->message))
                                        <div style="line-height:1.6;white-space:normal;">{!! nl2br(e($checkoutPopup->message)) !!}</div>
                                    @endif
                                </div>
                                <div class="modal-footer" style="border-top:1px solid rgba(255,255,255,.1);">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    @if(!empty($checkoutPopup->button_text) && !empty($checkoutPopup->button_url))
                                        <a href="{{ $checkoutPopup->button_url }}" target="_blank" rel="noopener" class="btn btn-primary">{{ $checkoutPopup->button_text }}</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>


        </main>
        <style>
            #checkout-processing-overlay {
                position: fixed;
                inset: 0;
                background: rgba(8, 12, 22, 0.78);
                backdrop-filter: blur(5px);
                display: none;
                align-items: center;
                justify-content: center;
                z-index: 9999;
            }

            #checkout-processing-overlay.is-visible {
                display: flex;
            }

            .checkout-processing-card {
                width: min(92vw, 420px);
                border-radius: 16px;
                border: 1px solid rgba(255, 255, 255, 0.14);
                background: linear-gradient(150deg, rgba(255,255,255,0.08), rgba(255,255,255,0.02));
                box-shadow: 0 22px 55px rgba(0, 0, 0, 0.35);
                padding: 22px 20px;
                text-align: center;
            }

            .checkout-processing-spinner {
                width: 56px;
                height: 56px;
                margin: 0 auto 12px;
                border-radius: 50%;
                border: 3px solid rgba(255,255,255,0.18);
                border-top-color: var(--accent);
                animation: checkoutSpin .9s linear infinite;
            }

            .checkout-processing-title {
                margin: 0;
                color: #f8fbff;
                font-size: 18px;
                font-weight: 700;
                letter-spacing: .01em;
            }

            .checkout-processing-copy {
                margin: 7px 0 0;
                color: rgba(226, 234, 248, 0.88);
                font-size: 13px;
                line-height: 1.45;
            }

            @keyframes checkoutSpin {
                to { transform: rotate(360deg); }
            }

            /* ===== Cart Toast Notification ===== */
            #cv-cart-toast {
                position: fixed;
                top: 24px;
                left: 50%;
                transform: translateX(-50%) translateY(-140%);
                z-index: 10000;
                background: linear-gradient(135deg, rgba(36,18,58,0.98) 0%, rgba(18,10,32,0.99) 100%);
                color: #fff;
                border: 1px solid rgba(167,116,255,0.55);
                border-radius: 14px;
                padding: 14px 22px 14px 18px;
                font-size: 14.5px;
                font-weight: 700;
                display: flex;
                align-items: center;
                gap: 14px;
                min-width: 280px;
                max-width: calc(100vw - 32px);
                box-shadow: 0 14px 40px rgba(0,0,0,0.5), 0 0 0 1px rgba(167,116,255,0.18), 0 6px 24px rgba(124,58,237,0.32);
                transition: transform .35s cubic-bezier(.2,.9,.3,1.4), opacity .25s;
                opacity: 0;
                pointer-events: none;
            }
            #cv-cart-toast.is-visible {
                transform: translateX(-50%) translateY(0);
                opacity: 1;
                pointer-events: auto;
            }
            #cv-cart-toast .cv-toast-icon {
                width: 36px;
                height: 36px;
                border-radius: 50%;
                background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
                display: inline-flex;
                align-items: center;
                justify-content: center;
                color: #fff;
                font-size: 15px;
                flex-shrink: 0;
                box-shadow: 0 4px 14px rgba(34,197,94,0.4);
            }
            #cv-cart-toast .cv-toast-body { display: flex; flex-direction: column; gap: 2px; min-width: 0; }
            #cv-cart-toast .cv-toast-title { font-size: 14.5px; font-weight: 800; color: #fff; letter-spacing: -0.005em; }
            #cv-cart-toast .cv-toast-sub { font-size: 12px; font-weight: 500; color: rgba(255,255,255,0.65); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 240px; }
            #cv-cart-toast .cv-toast-close {
                margin-left: auto;
                background: rgba(255,255,255,0.08);
                border: 1px solid rgba(255,255,255,0.12);
                color: rgba(255,255,255,0.7);
                width: 26px;
                height: 26px;
                border-radius: 50%;
                font-size: 12px;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                transition: all .15s;
                flex-shrink: 0;
            }
            #cv-cart-toast .cv-toast-close:hover { background: rgba(255,255,255,0.15); color: #fff; }
            @media (max-width: 600px) {
                #cv-cart-toast { top: 14px; padding: 12px 16px 12px 14px; font-size: 13px; min-width: 0; width: calc(100vw - 28px); }
                #cv-cart-toast .cv-toast-icon { width: 32px; height: 32px; font-size: 13px; }
                #cv-cart-toast .cv-toast-title { font-size: 13.5px; }
                #cv-cart-toast .cv-toast-sub { font-size: 11.5px; max-width: 150px; }
            }
        </style>
        <div id="cv-cart-toast" role="status" aria-live="polite" aria-atomic="true">
            <span class="cv-toast-icon"><i class="fas fa-check"></i></span>
            <span class="cv-toast-body">
                <span class="cv-toast-title">Added to cart!</span>
                <span class="cv-toast-sub" id="cv-cart-toast-sub"></span>
            </span>
            <button type="button" class="cv-toast-close" aria-label="Close" onclick="window.hideCartToast && window.hideCartToast();">&times;</button>
        </div>
        <div id="checkout-processing-overlay" aria-hidden="true" role="status" aria-live="polite">
            <div class="checkout-processing-card">
                <div class="checkout-processing-spinner" aria-hidden="true"></div>
                <p class="checkout-processing-title">Processing Your Purchase</p>
                <p class="checkout-processing-copy">Please wait while we securely complete your transaction.</p>
            </div>
        </div>
        <footer class="aff-footer">
            <div class="container">
                <div class="cv-footer-inner">
                    <div class="cv-footer-brand">
                        <img src="{{ asset('images/logo.png') }}" alt="CartVIP" class="cv-footer-logo">
                        <span class="cv-footer-powered">Powered by CartVIP</span>
                        <p class="cv-footer-tagline">Modern commerce infrastructure for products, services, reservations, and affiliate sales.</p>
                    </div>
                    <div class="cv-footer-legal">
                        <div class="cv-footer-legal-title">Legal &amp; Disclosures</div>
                        <p>Secure checkout and booking technology provided by <a href="https://cartvip.com" target="_blank" rel="noopener">CartVIP.com</a>.</p>
                        <p>Experiences, reservations, products, and services displayed on this website are offered and fulfilled by the participating venue or business. Pricing, availability, admission policies, refunds, and fulfillment terms are determined by the venue or merchant.</p>
                        <p>Payments are securely processed through authorized payment providers. CartVIP provides checkout infrastructure and payment support services only.</p>
                        <p>By completing this purchase, you agree to the participating venue or merchant's purchase terms as well as CartVIP's <a href="https://cartvip.com/page/privacy-policy" target="_blank" rel="noopener">Privacy Policy</a>, <a href="https://cartvip.com/page/terms-of-service" target="_blank" rel="noopener">Terms of Service</a>, and <a href="https://cartvip.com/page/merchant-disclosures" target="_blank" rel="noopener">Merchant Disclosures</a>.</p>
                    </div>
                </div>
                <div class="cv-footer-bar">
                    <span class="cv-footer-bar-copy">&copy; {{ date('Y') }} <strong>CartVIP.com</strong> &middot; All rights reserved</span>
                    <div class="cv-footer-bar-socials">
                        <a href="https://cartvip.com" target="_blank" rel="noopener" class="cv-footer-bar-social" aria-label="Website"><i class="fas fa-globe"></i></a>
                        <a href="mailto:hello@cartvip.com" class="cv-footer-bar-social" aria-label="Email"><i class="fas fa-envelope"></i></a>
                    </div>
                </div>
            </div>
        </footer>
        <script src="scripts/main.js"></script>
        <script>
            // Guest counter - robust override to fix double-fire / missed-click bug.
            // main.js's updateDisplay() calls checkEligibility() which is undefined and
            // throws mid-function, leaving state inconsistent. This replaces the global
            // increments/decrements with safe versions and uses a single delegated
            // click handler with a click guard to prevent double firing.
            (function () {
                var guestCounts = { men: 0, women: 0 };
                var lastClickAt = 0;

                function readDom() {
                    var menEl = document.getElementById('menCount');
                    var womenEl = document.getElementById('womenCount');
                    if (menEl) guestCounts.men = parseInt(menEl.textContent, 10) || 0;
                    if (womenEl) guestCounts.women = parseInt(womenEl.textContent, 10) || 0;
                }
                function writeDom() {
                    var menEl = document.getElementById('menCount');
                    var womenEl = document.getElementById('womenCount');
                    var totalEl = document.getElementById('totalCount');
                    var menHidden = document.getElementById('men_count');
                    var womenHidden = document.getElementById('women_count');
                    if (menEl) menEl.textContent = guestCounts.men;
                    if (womenEl) womenEl.textContent = guestCounts.women;
                    if (totalEl) totalEl.textContent = guestCounts.men + guestCounts.women;
                    if (menHidden) menHidden.value = guestCounts.men;
                    if (womenHidden) womenHidden.value = guestCounts.women;
                }
                window.increments = function (type) {
                    if (type !== 'men' && type !== 'women') return;
                    readDom();
                    guestCounts[type] += 1;
                    writeDom();
                };
                window.decrements = function (type) {
                    if (type !== 'men' && type !== 'women') return;
                    readDom();
                    if (guestCounts[type] > 0) guestCounts[type] -= 1;
                    writeDom();
                };

                // Remove inline onclick handlers so the buttons fire exactly once via
                // our delegated handler below (prevents both onclick and a stale
                // listener from firing in the same tap).
                document.addEventListener('DOMContentLoaded', function () {
                    document.querySelectorAll('.guest-qty-btn').forEach(function (btn) {
                        btn.removeAttribute('onclick');
                    });
                    readDom();
                    writeDom();
                });

                // Single delegated click handler with a 200ms guard to coalesce any
                // duplicate fire (e.g. touchend + click on some mobile browsers).
                document.addEventListener('click', function (e) {
                    var btn = e.target.closest('.guest-qty-btn');
                    if (!btn) return;
                    e.preventDefault();
                    e.stopPropagation();
                    var now = Date.now();
                    if (now - lastClickAt < 200) return; // ignore rapid duplicate
                    lastClickAt = now;
                    var type = btn.getAttribute('data-type');
                    var action = btn.getAttribute('data-action');
                    if (!type || !action) return;
                    if (action === 'inc') window.increments(type);
                    else if (action === 'dec') window.decrements(type);
                });
            })();

            // Cart toast: show a notification when an item is added (helpful on mobile
            // where the cart sidebar is below the fold).
            (function () {
                var hideTimer = null;
                window.showToast = function (title, sub, iconClass) {
                    var toast = document.getElementById('cv-cart-toast');
                    if (!toast) return;
                    var titleEl = toast.querySelector('.cv-toast-title');
                    var subEl = document.getElementById('cv-cart-toast-sub');
                    var iconEl = toast.querySelector('.cv-toast-icon i');
                    if (titleEl) titleEl.textContent = title || 'Notice';
                    if (subEl) subEl.textContent = sub || '';
                    if (iconEl) iconEl.className = iconClass || 'fas fa-check';
                    toast.classList.add('is-visible');
                    if (hideTimer) clearTimeout(hideTimer);
                    hideTimer = setTimeout(function () { window.hideCartToast(); }, 4000);
                };
                window.showCartToast = function (packageName, guests) {
                    var qty = parseInt(guests, 10) || 1;
                    var label = qty + (qty === 1 ? ' guest' : ' guests');
                    window.showToast('Added to cart!', packageName ? (packageName + ' · ' + label) : label, 'fas fa-check');
                };
                window.hideCartToast = function () {
                    var toast = document.getElementById('cv-cart-toast');
                    if (!toast) return;
                    toast.classList.remove('is-visible');
                    if (hideTimer) { clearTimeout(hideTimer); hideTimer = null; }
                };
            })();

            // Inject inline info icons into Service Fee / Tax / Gratuity rows so the
            // row's ::after stays free for the custom hover tooltip.
            (function () {
                function inject() {
                    var rows = document.querySelectorAll(
                        '#cv-order-sidebar .pricing-shell .default-service-charge, ' +
                        '#cv-order-sidebar .pricing-shell .default-sales-tax, ' +
                        '#cv-order-sidebar .pricing-shell .default-gratuity'
                    );
                    rows.forEach(function (row) {
                        if (!row.hasAttribute('data-tip')) return;
                        if (row.querySelector('.cv-row-info-icon')) return;
                        var labelSpan = row.querySelector('span');
                        if (!labelSpan) return;
                        var icon = document.createElement('span');
                        icon.className = 'cv-row-info-icon';
                        icon.textContent = 'i';
                        icon.setAttribute('aria-hidden', 'true');
                        labelSpan.appendChild(icon);
                    });
                }
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', inject);
                } else {
                    inject();
                }
                // Re-inject after JS moves pricing-shell into the sidebar.
                setTimeout(inject, 50);
                setTimeout(inject, 500);
            })();
        </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

        <script>
            function showCheckoutProcessingOverlay() {
                var overlay = document.getElementById('checkout-processing-overlay');
                if (!overlay) {
                    return;
                }

                overlay.classList.add('is-visible');
                overlay.setAttribute('aria-hidden', 'false');

                var submitButton = document.getElementById('submitBtn');
                if (submitButton) {
                    if (!submitButton.dataset.defaultText) {
                        submitButton.dataset.defaultText = submitButton.textContent;
                    }
                    submitButton.disabled = true;
                    submitButton.textContent = 'Processing...';
                }
            }

            function hideCheckoutProcessingOverlay() {
                var overlay = document.getElementById('checkout-processing-overlay');
                if (!overlay) {
                    return;
                }

                overlay.classList.remove('is-visible');
                overlay.setAttribute('aria-hidden', 'true');

                var submitButton = document.getElementById('submitBtn');
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.textContent = submitButton.dataset.defaultText || 'Complete Purchase';
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                var form = document.getElementById('payment-form');
                if (!form) {
                    return;
                }

                form.addEventListener('submit', function(event) {
                    window.setTimeout(function() {
                        if (!event.defaultPrevented) {
                            showCheckoutProcessingOverlay();
                        }
                    }, 0);
                });
            });
        </script>

        <script>
            // --- Shareable Link Refinement ---
            function openPackageTab() {
                // If reservation tabs exist, switch to package tab
                var packageTab = $("nav .tab[data-name='package']");
                if(packageTab.length) {
                    packageTab.trigger('click');
                } else {
                    // If no nav, just show .package section
                    $('.guest').hide();
                    $('.package').show();
                }
            }
            // --- Shareable Link Logic ---
            function getCurrentSelections() {
                // Get selected package
                var packageId = $('#package_id').val() || '';
                // Get selected add-ons (comma separated)
                var addons = $('#addons').val() || '';
                // Get guest count
                var guests = $('.package_number_of_guest').val() || '';
                // Get use date
                var useDate = $('.package_use_date').val() || '';
                return { packageId, addons, guests, useDate };
            }

            function setSelectionsFromParams(params) {
                    // Always open package tab if package param exists
                    if(params.package) {
                        openPackageTab();
                    }
                    // Open all packages (simulate click on all .vip-btn)
                    setTimeout(function() {
                        $('.vip-btn').each(function(){
                            if(!$(this).text().toLowerCase().includes('added')) {
                                $(this).trigger('click');
                            }
                        });
                        // Set package selection and guest count
                        if(params.package) {
                            var sel = $('.package_number_of_guestss[data-id="'+params.package+'"]');
                            if(params.guests && sel.length) {
                                sel.val(params.guests).trigger('change');
                            }
                        }
                        // Show all add-ons and check those in params
                        if(params.addons) {
                            var ids = params.addons.split(',');
                            // Show add-ons section
                            $('.addons').show();
                            ids.forEach(function(id) {
                                var cb = $('.addons-list input[type="checkbox"]#'+id);
                                if(cb.length && !cb.prop('checked')) {
                                    cb.prop('checked', true).trigger('click');
                                }
                            });
                        }
                        // Show cost breakdown
                        $('.dynamic-price').show();
                        $('.default-price').hide();
                        $('.default-total').show();
                    }, 700);
                        // Keep selected date synced to hidden checkout field.
                        var desiredDate = params.use_date || '';
                        if ($('#package_use_date option[value="' + desiredDate + '"]').length) {
                            $('#package_use_date').val(desiredDate);
                        } else {
                            $('#package_use_date').val('');
                        }
                        $('.package_use_date').val($('#package_use_date').val());
            }

            function getUrlWithSelections() {
                var sel = getCurrentSelections();
                var url = window.location.origin + window.location.pathname + '?package=' + encodeURIComponent(sel.packageId) + '&addons=' + encodeURIComponent(sel.addons) + '&guests=' + encodeURIComponent(sel.guests) + '&use_date=' + encodeURIComponent(sel.useDate);
                return url;
            }

            // --- End Shareable Link Logic ---

                // --- End Shareable Link Refinement ---
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
            // ======= CART SYSTEM ======= Define immediately in global scope
            window.cart = [];
            window.cartCoupon = window.cartCoupon || null;
            window.eventCapacityState = {
                limit: null,
                remaining: null
            };

            // Ensure cart is always an array
            function ensureCartArray() {
                if (!Array.isArray(window.cart)) {
                    console.warn('window.cart was not an array, resetting');
                    window.cart = [];
                }
            }

            function formatCurrency(value) {
                return '$' + new Intl.NumberFormat('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(Number(value) || 0);
            }

            function syncCheckoutCartFields() {
                var form = document.getElementById('payment-form');
                if (!form || !Array.isArray(window.cart) || !window.cart.length) {
                    return;
                }

                var cartField = form.querySelector('#cart_items');
                var packageField = form.querySelector('#package_id');
                var guestField = form.querySelector('.package_number_of_guest');
                var addonsField = form.querySelector('#addons');
                var firstItem = window.cart[0];
                var totalGuests = window.cart.reduce(function(sum, item) {
                    return sum + (parseInt(item.guests, 10) || 1);
                }, 0);
                var addonNames = window.cart.reduce(function(all, item) {
                    return all.concat(Array.isArray(item.addons) ? item.addons : []);
                }, []).map(function(addon) {
                    return addon.name + ' ($' + addon.price + ')';
                });

                if (cartField) {
                    cartField.value = JSON.stringify(window.cart);
                }
                if (packageField && firstItem) {
                    packageField.value = firstItem.packageId || packageField.value;
                }
                if (guestField) {
                    guestField.value = totalGuests || 1;
                }
                if (addonsField) {
                    addonsField.value = addonNames.join(', ');
                }
            }

            function cartRequiresTransportation() {
                ensureCartArray();
                return window.cart.some(function(pkg) {
                    return pkg.transportation === true || pkg.transportation === 1 || pkg.transportation === '1';
                });
            }

            function syncTransportationStateFromCart() {
                window.requiresTransportation = cartRequiresTransportation();
                const transportationFields = $('#transport-form').find('input, select, textarea');
                const transportationPhoneField = $('input[name="transportation_phone"]');
                const transportationAddressField = $('input[name="transportation_address"]');
                const transportationPickupTimeField = $('input[name="transportation_pickup_time"]');
                const transportationGuestField = $('input[name="transportation_guest"]');
                const pickupDateField = $('input[name="package_use_date"]');
                const driverNotificationConsentWrap = $('.driver-notification-consent-wrap');
                const driverNotificationConsentInputs = $('.driver-notification-consent-input');
                if (window.requiresTransportation) {
                    $('#step-2 .step-title').text('Transportation');
                    $('#next-to-transport').text('Next: Transportation Details');
                    transportationFields.prop('disabled', false);
                    transportationPhoneField.prop('required', true).attr('aria-required', 'true');
                    transportationAddressField.prop('required', true).attr('aria-required', 'true');
                    transportationPickupTimeField.prop('required', true).attr('aria-required', 'true');
                    transportationGuestField.prop('required', true).attr('aria-required', 'true');
                    if (!Number.isFinite(parseInt(transportationGuestField.val(), 10)) || parseInt(transportationGuestField.val(), 10) < 1) {
                        transportationGuestField.val('1');
                    }
                    pickupDateField.prop('required', true).attr('aria-required', 'true');
                    driverNotificationConsentWrap.css('display', 'flex');
                    driverNotificationConsentInputs.prop('required', true).attr('aria-required', 'true');
                } else {
                    $('#step-2 .step-title').text('Confirmation');
                    $('#next-to-transport').text('Next: Transportation Confirmation');
                    transportationFields.prop('disabled', true);
                    transportationPhoneField.prop('required', false).removeClass('required-field').removeAttr('aria-required');
                    transportationAddressField.prop('required', false).removeClass('required-field').removeAttr('aria-required');
                    transportationPickupTimeField.prop('required', false).removeClass('required-field').removeAttr('aria-required');
                    transportationGuestField.prop('required', false).removeClass('required-field').removeAttr('aria-required').val('0');
                    pickupDateField.prop('required', false).removeClass('required-field').removeAttr('aria-required');
                    driverNotificationConsentWrap.hide();
                    driverNotificationConsentInputs.prop('checked', false).prop('required', false).removeAttr('aria-required');
                }
            }

            function parseMultipleFlag(value) {
                return value === true || value === 1 || value === '1' || value === 'true';
            }

            function getPackageMultipleFromDom(packageId) {
                var multipleValue = $('.package_number_of_guestss[data-id="' + packageId + '"]').first().data('multiple');
                return parseMultipleFlag(multipleValue);
            }

            function getBillableGuests(pkg) {
                return parseMultipleFlag(pkg.isMultiple) ? (parseInt(pkg.guests) || 1) : 1;
            }

            function getSelectedUseDate() {
                return String($('#package_use_date').val() || $('.package_use_date').val() || '').trim();
            }

            window.getSelectedUseDate = getSelectedUseDate;

            function showReservationDateError(message) {
                var text = String(message || 'Please select a reservation date.').trim();
                $('#package_use_date').addClass('required-field').attr('aria-invalid', 'true');
                $('#package_use_date_error').text(text).show();
            }

            function clearReservationDateError() {
                $('#package_use_date').removeClass('required-field').removeAttr('aria-invalid');
                $('#package_use_date_error').hide();
            }

            function ensureReservationDateSelected() {
                var selectedDate = window.getSelectedUseDate();
                if (selectedDate) {
                    clearReservationDateError();
                    return true;
                }

                showReservationDateError('Please select a reservation date above before continuing.');
                if (typeof window.showToast === 'function') {
                    window.showToast('Must Choose Date', 'Please select a reservation date to continue.', 'fas fa-calendar-alt');
                }
                var dateCard = document.querySelector('.hero-date-card');
                if (dateCard && typeof dateCard.scrollIntoView === 'function') {
                    dateCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                $('#package_use_date').trigger('focus');
                return false;
            }

            function getCartAttendeeCount(excludedPackageId) {
                ensureCartArray();
                return window.cart.reduce(function(sum, pkg) {
                    if (excludedPackageId !== undefined && excludedPackageId !== null && String(pkg.packageId) === String(excludedPackageId)) {
                        return sum;
                    }

                    return sum + (parseInt(pkg.guests, 10) || 1);
                }, 0);
            }

            function syncUseDateField() {
                var selected = window.getSelectedUseDate();
                if (selected) {
                    $('.package_use_date').val(selected);
                } else {
                    $('.package_use_date').val('');
                }
            }

            window.syncUseDateField = syncUseDateField;

            function clearGuestFieldError($field) {
                var $control = $field.closest('.vip-guest-control');
                $control.find('.package-guest-error').hide().text('');
                $field.removeClass('required-field').removeAttr('aria-invalid');
            }

            function showGuestFieldError($field, message) {
                var $control = $field.closest('.vip-guest-control');
                $control.find('.package-guest-error').text(message || 'The quantity you entered is unavailable for the selected date. Please choose a lower number.').show();
                $field.addClass('required-field').attr('aria-invalid', 'true');
            }

            function updateGuestSelectOptions($field, maxSelectable, soldOutMessage) {
                var currentVal = $field.val();
                var hasPlaceholder = !currentVal || currentVal === '';
                var current = parseInt(currentVal, 10) || 1;
                var safeMax = Math.max(0, parseInt(maxSelectable, 10) || 0);
                var isTicketInput = $field.is('input[type="number"]');
                var isTicketSelect = $field.hasClass('ticket-select-lazy');
                var $control = $field.closest('.vip-guest-control');
                var $inputWrap = $control.find('.package-guest-input-wrap');
                var $soldOut = $control.find('.package-soldout');
                var html = '';

                clearGuestFieldError($field);

                if (safeMax <= 0) {
                    $inputWrap.hide();
                    $soldOut.text(soldOutMessage || 'Sold Out for Selected Date').show();
                    $field.val('1').prop('disabled', true);
                    return;
                }

                $soldOut.hide();
                $inputWrap.show();

                if (isTicketInput) {
                    var safeValue = Math.min(Math.max(current, 1), safeMax);
                    $field.prop('disabled', false);
                    $field.attr('min', '1');
                    $field.attr('step', '1');
                    $field.attr('max', String(safeMax));
                    $field.val(String(safeValue));
                    return;
                }

                if (isTicketSelect) {
                    var showMax = Math.min(15, safeMax);
                    $field.data('ticket-max', safeMax).attr('data-ticket-max', safeMax);
                    var ticketHtml = '<option value=""># of Tickets</option>';
                    for (var i = 1; i <= showMax; i++) {
                        ticketHtml += '<option value="' + i + '">' + i + ' ' + (i === 1 ? 'ticket' : 'tickets') + '</option>';
                    }
                    $field.html(ticketHtml);
                    if (hasPlaceholder) {
                        $field.val('');
                    } else {
                        var safeValue = Math.min(Math.max(current, 1), safeMax);
                        $field.val(String(safeValue));
                    }
                    $field.prop('disabled', false);
                    return;
                }

                html += '<option value=""># of Guests</option>';
                for (var i = 1; i <= safeMax; i++) {
                    html += '<option value="' + i + '">' + i + ' ' + (i === 1 ? 'guest' : 'guests') + '</option>';
                }

                $field.html(html);
                if (hasPlaceholder) {
                    $field.val('');
                } else {
                    $field.val(String(Math.min(current, safeMax)));
                }
                $field.prop('disabled', false);
            }

            window.clearGuestFieldError = clearGuestFieldError;
            window.showGuestFieldError = showGuestFieldError;
            window.updateGuestSelectOptions = updateGuestSelectOptions;
            window.parseMultipleFlag = parseMultipleFlag;

            // Ticket select lazy-load: append next 15 options when scrolled to bottom
            $(document).on('scroll', '.ticket-select-lazy', function () {
                var $sel = $(this);
                var shownMax = $sel.find('option').length;
                var totalMax = parseInt($sel.data('ticket-max'), 10) || shownMax;
                if (shownMax >= totalMax) { return; }
                var el = this;
                if (el.scrollHeight - el.scrollTop - el.clientHeight < 40) {
                    var nextMax = Math.min(shownMax + 15, totalMax);
                    for (var i = shownMax + 1; i <= nextMax; i++) {
                        $sel.append('<option value="' + i + '">' + i + '</option>');
                    }
                }
            });
            $(document).on('keydown', '.ticket-select-lazy', function (e) {
                if (e.key !== 'ArrowDown') { return; }
                var $sel = $(this);
                var shownMax = $sel.find('option').length;
                var totalMax = parseInt($sel.data('ticket-max'), 10) || shownMax;
                if (shownMax >= totalMax) { return; }
                if (parseInt($sel.val(), 10) >= shownMax) {
                    var nextMax = Math.min(shownMax + 15, totalMax);
                    for (var i = shownMax + 1; i <= nextMax; i++) {
                        $sel.append('<option value="' + i + '">' + i + '</option>');
                    }
                }
            });

            function refreshEventPackageSelectionLimits(showAlertWhenReduced) {
                var useDate = window.getSelectedUseDate();
                $('.package_number_of_guestss').each(function() {
                    var $field = $(this);
                    var packageId = $field.data('id');
                    var previous = parseInt($field.val(), 10) || 1;

                    $.get('/{{ $data->slug }}/package/' + packageId + '/capacity', { use_date: useDate })
                        .done(function(response) {
                            var endpointMax = parseInt(response.max_select, 10);
                            if (!Number.isFinite(endpointMax)) {
                                endpointMax = parseInt(response.capacity, 10);
                            }
                            if (!Number.isFinite(endpointMax)) {
                                endpointMax = 1;
                            }

                            var cartRemaining = endpointMax;
                            if (response.event_remaining !== null && response.event_remaining !== undefined) {
                                var eventRemaining = parseInt(response.event_remaining, 10);
                                if (Number.isFinite(eventRemaining)) {
                                    cartRemaining = Math.min(cartRemaining, Math.max(eventRemaining - getCartAttendeeCount(packageId), 0));
                                }
                            }

                            updateGuestSelectOptions($field, cartRemaining, response.message || 'Sold Out for Selected Date');

                            var reducedTo = parseInt($field.val(), 10) || 1;
                            var existingCartPackage = window.cart.find(function(pkg) { return String(pkg.packageId) === String(packageId); });
                            if (existingCartPackage && (parseInt(existingCartPackage.guests, 10) || 1) !== reducedTo) {
                                existingCartPackage.guests = reducedTo;
                                syncCheckoutCartFields();
                                window.renderCart();
                                window.calculateCartTotal();
                            }

                            if (showAlertWhenReduced && previous > reducedTo) {
                                alert('Your guest count was adjusted to match current availability for the selected date.');
                            }

                            var $button = $('.vip-btn[data-id="' + packageId + '"]');
                            setPackageButtonState($button, cartRemaining <= 0, cartRemaining <= 0 ? 'Sold Out' : ($button.data('default-label') || 'Add to Cart'));
                        });
                });
            }

            function setPackageButtonState($button, disabled, label) {
                if (!$button.length) {
                    return;
                }

                if (!$button.data('default-label')) {
                    $button.data('default-label', ($button.attr('data-default-label') || $button.text() || 'Add to Cart').trim());
                }

                $button.prop('disabled', disabled);
                $button.text(label || $button.data('default-label'));
            }

            function syncEventCapacityUi() {
                return;
            }

            function resetCartForDateChange() {
                ensureCartArray();
                if (!window.cart.length) {
                    return;
                }

                window.cart = [];
                window.cartCoupon = null;

                $('#cart-list').html('');
                $('#cart-total').text('');
                $('#cart-coupon').html('');
                $('#cart-section').hide();
                $('#shareLinkContainer').hide();
                $('#shareableLink').val('').hide();
                $('#promo_code').val('');
                $('#applyPromoBtn').prop('disabled', false);
                $('.promo_code').val('');
                $('#package_id').val('');
                $('#addons').val('');
                $('.package_number_of_guest').val('2');
                $('.package_number_of_guestss').val('2');
                $('.vip-card').removeClass('selected');

                syncCheckoutCartFields();
                window.calculateCartTotal();
                syncTransportationStateFromCart();
                syncEventCapacityUi();
            }

            window.addPackageToCart = function(packageId, packageName, packagePrice, guests, addons, transportation, isMultiple) {
                console.log('addPackageToCart called', packageId, packageName);
                ensureCartArray();
                var normalizedGuests = parseInt(guests, 10) || 1;
                var useDate = window.getSelectedUseDate();

                if (!ensureReservationDateSelected()) {
                    return Promise.resolve(false);
                }

                // Check daily limits for this package
                return $.get('/{{ $data->slug }}/package/' + packageId + '/capacity', { use_date: useDate, requested_quantity: normalizedGuests })
                    .then(function(response) {
                        if (!response.available) {
                            alert(response.message || 'This package is currently unavailable for the selected date.');
                            return false;
                        }

                        var effectiveMax = parseInt(response.max_select, 10);
                        if (!Number.isFinite(effectiveMax)) {
                            effectiveMax = parseInt(response.capacity, 10) || 0;
                        }
                        if (response.event_remaining !== null && response.event_remaining !== undefined) {
                            var eventRemaining = parseInt(response.event_remaining, 10);
                            if (Number.isFinite(eventRemaining)) {
                                effectiveMax = Math.min(effectiveMax, Math.max(eventRemaining - getCartAttendeeCount(packageId), 0));
                            }
                        }

                        if (normalizedGuests > effectiveMax) {
                            var $field = $('.package_number_of_guestss[data-id="' + packageId + '"]');
                            updateGuestSelectOptions($field, effectiveMax, response.message || 'Sold Out for Selected Date');
                            showGuestFieldError($field, response.message || ('Only ' + Math.max(effectiveMax, 0) + ' guests can be selected for this package/date.'));
                            refreshEventPackageSelectionLimits(true);
                            return false;
                        }

                        var packageType = ($('.package_number_of_guestss[data-id="' + packageId + '"]').data('package-type') || 'table');
                        var existing = window.cart.find(function(p) { return p.packageId == packageId; });
                        if (!existing) {
                            window.cart.push({
                                packageId: packageId,
                                packageName: packageName,
                                packagePrice: parseFloat(packagePrice),
                                guests: normalizedGuests,
                                isMultiple: parseMultipleFlag(isMultiple),
                                addons: addons || [],
                                transportation: transportation,
                                packageType: packageType
                            });
                        } else {
                            existing.packageName = packageName;
                            existing.packagePrice = parseFloat(packagePrice);
                            existing.guests = normalizedGuests;
                            existing.isMultiple = parseMultipleFlag(isMultiple);
                            existing.addons = addons || [];
                            existing.transportation = transportation;
                            existing.packageType = packageType;
                        }

                        $('#cart-section').show();
                        $('#shareLinkContainer').show();
                        window.renderCart();
                        syncCheckoutCartFields();
                        window.calculateCartTotal();
                        syncTransportationStateFromCart();
                        syncEventCapacityUi();
                        refreshEventPackageSelectionLimits(false);
                        if (typeof window.showCartToast === 'function') {
                            window.showCartToast(packageName, normalizedGuests);
                        }
                        return true;
                    })
                    .catch(function() {
                        alert('Error checking package availability. Please try again.');
                        return false;
                    });
            };

            window.removePackageFromCart = function(packageId) {
                ensureCartArray();
                window.cart = window.cart.filter(p => p.packageId != packageId);
                if (window.cart.length === 0) {
                    $('#cart-section').hide();
                }
                window.renderCart();
                syncCheckoutCartFields();
                window.calculateCartTotal();
                syncTransportationStateFromCart();
                syncEventCapacityUi();
            };

            window.renderCart = function() {
                ensureCartArray();
                if (!window.cart.length) {
                    $('#cart-list').html('');
                    return;
                }
                var html = '';
                window.cart.forEach(function(pkg) {
                    var billableGuests = getBillableGuests(pkg);
                    var unitPrice = parseFloat(pkg.packagePrice) || 0;
                    var lineTotal = unitPrice * billableGuests;
                    var priceLine = parseMultipleFlag(pkg.isMultiple)
                        ? (formatCurrency(unitPrice) + ' &times; ' + (parseInt(pkg.guests, 10) || 1) + ' = ' + formatCurrency(lineTotal))
                        : formatCurrency(lineTotal);
                    var guestQty = parseInt(pkg.guests, 10) || 1;
                    var isTicketPkg = pkg.packageType === 'ticket';
                    var guestLabel = guestQty + (isTicketPkg ? (guestQty === 1 ? ' Ticket' : ' Tickets') : (guestQty === 1 ? ' Guest' : ' Guests'));
                    html += '<div class="cart-line">';
                    html += '<div class="cart-line-main">';
                    html += '<div style="flex:1;min-width:0;"><div class="cart-item-name">' + pkg.packageName + '</div><div class="cart-line-guests">' + guestLabel + '</div></div>';
                    html += '<div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px;"><div class="cart-item-price">' + priceLine + '</div><button onclick="window.removePackageFromCart(' + pkg.packageId + ')" class="cart-remove-btn">Remove</button></div>';
                    html += '</div>';
                    if (pkg.addons.length > 0) {
                        html += '<div class="cart-addons" style="color: #a774ff !important;">Add-ons: ' + pkg.addons.map(function(a) { return a.name + ((parseInt(a.qty, 10) || 1) > 1 ? (' x' + (parseInt(a.qty, 10) || 1)) : '') + ' (' + formatCurrency(a.price) + ')'; }).join(', ') + '</div>';
                    }
                    html += '</div>';
                });
                $('#cart-list').html(html);
                syncCheckoutCartFields();
            };

            window.calculateCartTotal = function() {
                ensureCartArray();
                var subtotal = 0;
                window.cart.forEach(function(pkg) {
                    subtotal += pkg.packagePrice * getBillableGuests(pkg);
                    pkg.addons.forEach(function(addon) {
                        subtotal += addon.price;
                    });
                });

                var service_charge = parseFloat($('#service_charge').val()) || 0;
                var sales_tax = parseFloat($('#sales_tax').val()) || 0;
                var gratuity = parseFloat($('#gratuity').val()) || 0;
                var couponDiscount = 0;

                if (window.cartCoupon) {
                    if (window.cartCoupon.type === 'percentage') {
                        couponDiscount = (subtotal / 100) * window.cartCoupon.discount;
                    } else {
                        couponDiscount = window.cartCoupon.discount;
                    }
                }

                couponDiscount = Math.min(Math.max(couponDiscount, 0), subtotal);

                var discountedSubtotal = subtotal - couponDiscount;
                var service_charge_price = ("{{ $data->service_charge_name }}" != "0") ? (discountedSubtotal / 100) * service_charge : 0;
                var gratuited_price = ("{{ $data->gratuity_name }}" != "0") ? (discountedSubtotal / 100) * gratuity : 0;
                var sales_tax_price = ("{{ $data->sales_tax_name }}" != "0") ? ((discountedSubtotal + service_charge_price + gratuited_price) / 100) * sales_tax : 0;

                var processingFeeBase = discountedSubtotal + service_charge_price;
                var amountAfterCoupon = processingFeeBase + sales_tax_price + gratuited_price;
                var processingFee = parseFloat($('#processing_fee').val()) || 0;
                var processingFeeType = ($('#processing_fee_type').val() || 'percentage').toLowerCase();
                var processingFeeAmount = processingFeeType === 'flat'
                    ? processingFee
                    : (processingFeeBase / 100) * processingFee;
                var grandTotal = amountAfterCoupon + processingFeeAmount;
                var refundableRate = parseFloat($('#refundable').val()) || 0;
                var refundableAmount = (grandTotal / 100) * refundableRate;

                $('.default-package-price > span:last-child').text(formatCurrency(subtotal));
                $('.default-service-charge > span:last-child').text(formatCurrency(service_charge_price));
                $('.default-sales-tax > span:last-child').text(formatCurrency(sales_tax_price));
                $('.default-gratuity > span:last-child').text(formatCurrency(gratuited_price));

                if (window.cartCoupon && couponDiscount > 0) {
                    if ($('.default-promo-discount').length === 0) {
                        $('.default-package-price').after('<div style="font-size: inherit !important; color: #22c55e !important; font-weight: 700 !important;" class="default-promo-discount">Promo Code Discount: <span style="font-size: inherit !important; color: #22c55e !important; font-weight: 700 !important;">$0.00</span></div>');
                    }
                    $('.default-promo-discount span').text('-' + formatCurrency(couponDiscount));
                    $('.default-package-price').after($('.default-promo-discount'));
                } else {
                    $('.default-promo-discount').remove();
                }

                if (processingFeeAmount > 0) {
                    if ($('.default-processing-fee').length === 0) {
                        $('.default-gratuity').after('<div style="font-size: 12px;" class="default-processing-fee" data-tip="Covers secure payment and transaction processing costs.">Processing Fee: <span>$0.00</span></div>');
                    }
                    $('.default-processing-fee span').text(formatCurrency(processingFeeAmount));
                } else {
                    $('.default-processing-fee').remove();
                }

                $('#cart-total').text('');

                if (window.cartCoupon) {
                    $('#cart-coupon').html('Coupon "' + window.cartCoupon.code + '" applied: -' + formatCurrency(couponDiscount));
                } else {
                    $('#cart-coupon').html('');
                }

                $('.payment_total').val(grandTotal.toFixed(2));
                $('#subtotal').val(refundableRate > 0 ? refundableAmount.toFixed(2) : grandTotal.toFixed(2));
                $('#commission_base_amount').val(Math.max(subtotal - couponDiscount, 0).toFixed(2));
                $('.default-refundable .refundable-amount').text(formatCurrency(refundableAmount));
                $('.default-due .due-amount').text(formatCurrency(grandTotal - refundableAmount));
                $('.default-deposit > span:last-child').text(formatCurrency(grandTotal));
                $('.default-total > span:last-child').text(formatCurrency(grandTotal));
                $('.discounted_amount').val(couponDiscount.toFixed(2));

                // Update Due Today (Deposit) box: show deposit amount + Due on Arrival
                if (refundableRate > 0) {
                    $('#cv-deposit-display').text(formatCurrency(refundableAmount));
                    $('#cv-due-on-arrival').text(formatCurrency(Math.max(grandTotal - refundableAmount, 0)));
                } else {
                    $('#cv-deposit-display').text(formatCurrency(grandTotal));
                }
            };
            console.log('Cart functions initialized:', typeof window.addPackageToCart);

            // Shareable link functions
            function getCurrentSelections() {
                var data = {
                    cart: window.cart,
                    coupon: window.cartCoupon ? window.cartCoupon.code : null
                };
                return data;
            }

            function setSelectionsFromParams() {
                var params = new URLSearchParams(window.location.search);
                var cartParam = params.get('cart');
                var couponParam = params.get('coupon');

                if (cartParam) {
                    openPackageTab();
                    try {
                        var decoded = JSON.parse(decodeURIComponent(cartParam));
                        window.cart = decoded.map(function(pkg) {
                            if (typeof pkg.isMultiple === 'undefined') {
                                pkg.isMultiple = getPackageMultipleFromDom(pkg.packageId);
                            }
                            return pkg;
                        });
                        if (window.cart.length > 0) {
                            $('#package_id').val(window.cart[0].packageId);
                            $('.package_number_of_guest').val(window.cart[0].guests);
                            window.cart.forEach(function(pkg) {
                                $('.package_number_of_guestss[data-id="' + pkg.packageId + '"]').val(pkg.guests || 1);
                                $('#pkg-card-' + pkg.packageId).addClass('selected');
                            });
                            $('#cart-section').show();
                            $('#shareLinkContainer').show();
                            window.renderCart();
                            window.calculateCartTotal();
                            syncTransportationStateFromCart();
                            syncEventCapacityUi();
                            $('.dynamic-price').show();
                            $('.default-price').hide();
                            $('#checkout-steps').show();
                            showStep(1);
                        }
                    } catch(e) {
                        console.error('Failed to parse cart param', e);
                    }
                }

                if (couponParam) {
                    $('#promo_code').val(couponParam);
                    $('#applyPromoBtn').trigger('click');
                }
            }

            function getUrlWithSelections() {
                var data = getCurrentSelections();
                var params = new URLSearchParams();
                if (data.cart.length > 0) {
                    params.set('cart', encodeURIComponent(JSON.stringify(data.cart)));
                }
                if (data.coupon) {
                    params.set('coupon', data.coupon);
                }
                return window.location.origin + window.location.pathname + '?' + params.toString();
            }

            $(document).ready(function() {
                function showCopyTooltip() {
                    const tooltip = $('#copyTooltip');
                    tooltip.text('Link copied!').show();
                    setTimeout(function() {
                        tooltip.hide();
                    }, 2000);
                }

                function getShareableUrl() {
                    var existing = String($('#shareableLink').val() || '').trim();
                    return existing || getUrlWithSelections();
                }

                function revealShareActions() {
                    $('#shareActions').css('display', 'flex');
                }

                function copyShareUrl(url) {
                    navigator.clipboard.writeText(url).then(function() {
                        showCopyTooltip();
                        alert('Link copied!');
                    }).catch(function() {
                        $('#shareableLink').val(url).show().trigger('focus').select();
                        revealShareActions();
                        alert('Link ready. Press Ctrl+C to copy.');
                    });
                }

                setSelectionsFromParams();

                $('#generateShareLink').on('click', function() {
                    if (window.cart.length === 0) {
                        alert('Please add at least one package to cart');
                        return;
                    }
                    
                    var selections = getCurrentSelections();
                    
                    $.ajax({
                        url: '/cart/share',
                        type: 'POST',
                        data: {
                            cart: JSON.stringify(selections.cart),
                            website_slug: '{{ $data->slug }}',
                            event_name: @json($affiliate->display_name ?? $affiliate->user->name),
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(res) {
                            if (res.success) {
                                $('#shareableLink').val(res.short_url).show();
                                revealShareActions();
                                navigator.clipboard.writeText(res.short_url).then(function() {
                                    showCopyTooltip();
                                }).catch(function() {
                                    $('#shareableLink').select();
                                });
                            } else {
                                const fallbackUrl = getUrlWithSelections();
                                $('#shareableLink').val(fallbackUrl).show();
                                revealShareActions();
                                $('#shareableLink').select();
                            }
                        },
                        error: function(err) {
                            const fallbackUrl = getUrlWithSelections();
                            $('#shareableLink').val(fallbackUrl).show();
                            revealShareActions();
                            $('#shareableLink').select();
                            console.error(err);
                        }
                    });
                });

                $(document).on('click', '#shareActions .checkout-share-btn', function() {
                    var mode = String($(this).data('share') || '').toLowerCase();
                    var url = getShareableUrl();

                    if (!url) {
                        alert('Please generate a shareable link first.');
                        return;
                    }

                    if (mode === 'email') {
                        window.location.href = 'mailto:?subject=' + encodeURIComponent('Checkout Link') + '&body=' + encodeURIComponent(url);
                        return;
                    }

                    if (mode === 'whatsapp') {
                        window.open('https://wa.me/?text=' + encodeURIComponent(url), '_blank', 'noopener');
                        return;
                    }

                    if (mode === 'facebook') {
                        window.open('https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(url), '_blank', 'noopener');
                        return;
                    }

                    if (mode === 'copy') {
                        copyShareUrl(url);
                    }
                });

                syncEventCapacityUi();

                // Copy to clipboard when clicking the shareable link field
                $('#shareableLink').on('click', function() {
                    const url = $(this).val();
                    navigator.clipboard.writeText(url).then(function() {
                        showCopyTooltip();
                    }).catch(function(err) {
                        console.error('Failed to copy:', err);
                        $('#shareableLink').select();
                    });
                });

                if (String($('#shareableLink').val() || '').trim()) {
                    revealShareActions();
                }

            });

            // ======= END CART SYSTEM =======

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
                if (!select) return;
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

                // Months 1-12 (with "Month" placeholder)
                monthSelect.innerHTML = '<option value="" disabled selected hidden>Month</option>';
                for (let m = 1; m <= 12; m++) {
                    monthSelect.innerHTML += `<option value="${m.toString().padStart(2, '0')}">${m.toString().padStart(2, '0')}</option>`;
                }
                // Days 1-31 (with "Day" placeholder)
                daySelect.innerHTML = '<option value="" disabled selected hidden>Day</option>';
                for (let d = 1; d <= 31; d++) {
                    daySelect.innerHTML += `<option value="${d.toString().padStart(2, '0')}">${d.toString().padStart(2, '0')}</option>`;
                }
                // Years: current year to (current year - 100) (with "Year" placeholder)
                const currentYear = new Date().getFullYear();
                yearSelect.innerHTML = '<option value="" disabled selected hidden>Year</option>';
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


            window.pendingPackageSelection = null;

            function escapeAddonHtml(value) {
                return String(value || '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#39;');
            }

            function openAddonSelectionModal(selection) {
                var addons = selection.addons || [];
                var html = '';

                if (!addons.length) {
                    html = '<p style="margin:0;opacity:.8;">No add-ons available for this package. Click confirm to continue.</p>';
                } else {
                    var existingCartPkg = Array.isArray(window.cart) ? window.cart.find(function(p) { return p.packageId == selection.packageId; }) : null;
                    var existingAddons = existingCartPkg ? (existingCartPkg.addons || []) : [];
                    addons.forEach(function(addon) {
                        var unitPrice = parseFloat(addon.price || 0);
                        var existingAddon = existingAddons.find(function(a) { return String(a.id) === String(addon.id); });
                        var currentQty = existingAddon ? (parseInt(existingAddon.qty, 10) || (existingAddon.price > 0 ? Math.round(existingAddon.price / unitPrice) : 1)) : 0;
                        if (!Number.isFinite(currentQty) || currentQty < 0) {
                            currentQty = 0;
                        }
                        var description = String(addon.description || '').trim();
                        var descriptionHtml = description ? ('<small class="addon-modal-desc">' + escapeAddonHtml(description) + '</small>') : '';
                        var lineTotal = unitPrice * currentQty;
                        html += '<div class="addon-modal-row">'
                            + '<span class="addon-modal-label">' + escapeAddonHtml(addon.name) + '<span class="addon-modal-unit">' + formatCurrency(unitPrice) + '/ea</span>' + descriptionHtml + '<small class="addon-line-total">Line total: <span class="addon-line-total-value" data-id="' + addon.id + '">' + formatCurrency(lineTotal) + '</span></small></span>'
                            + '<span class="addon-qty-stepper">'
                            + '<button type="button" class="addon-qty-btn addon-qty-dec" data-id="' + addon.id + '">&#8722;</button>'
                            + '<span class="addon-qty-val" data-id="' + addon.id + '" data-name="' + escapeAddonHtml(addon.name) + '" data-price="' + unitPrice + '">' + currentQty + '</span>'
                            + '<button type="button" class="addon-qty-btn addon-qty-inc" data-id="' + addon.id + '">+</button>'
                            + '</span>'
                            + '</div>';
                    });
                }

                $('#addonSelectionModalTitle').text('Select Add-ons for ' + (selection.pkgName || selection.packageName));
                $('#addonSelectionModalBody').html(html);
                bootstrap.Modal.getOrCreateInstance(document.getElementById('addonSelectionModal')).show();
            }

            $(document).ready(function () {
                window.lastSelectedUseDate = (typeof window.getSelectedUseDate === 'function')
                    ? window.getSelectedUseDate()
                    : String($('#package_use_date').val() || $('.package_use_date').val() || '').trim();
                if (typeof window.syncUseDateField === 'function') {
                    window.syncUseDateField();
                }
                var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
                popoverTriggerList.forEach(function (popoverTriggerEl) {
                    bootstrap.Popover.getOrCreateInstance(popoverTriggerEl, {
                        trigger: 'focus hover',
                        html: true,
                        sanitize: true,
                        container: 'body'
                    });
                });

                $(document).on('click', '.package-category-tile', function() {
                    var $tile = $(this);
                    var $target = $tile.closest('.package-category-wrap').find('.package-category-group').first();
                    var isOpen = $tile.hasClass('active');

                    if (!$target.length) {
                        var targetSelector = String($tile.data('target') || '');
                        var targetId = targetSelector.replace(/^#/, '');
                        $target = targetId ? $('#' + targetId) : $();
                    }

                    $('.package-category-tile').removeClass('active');
                    $('.package-category-group').stop(true, true).slideUp(180);

                    if (!isOpen && $target.length) {
                        $tile.addClass('active');
                        $target.stop(true, true).slideDown(180);
                    }
                });

                $(document).on('click', '.vip-btn', function () {
                    var $btn = $(this);
                    var packageId = $btn.data('id');
                    var packageName = $btn.data('name');
                    var packagePrice = parseFloat($btn.data('price'));
                    var $guestSelect = $('.package_number_of_guestss[data-id="' + packageId + '"]');
                    var guestValue = $guestSelect.val();
                    var isMultiple = parseMultipleFlag($guestSelect.data('multiple'));

                    if (!ensureReservationDateSelected()) {
                        return;
                    }

                    if (!guestValue) {
                        var fieldLabel = $guestSelect.find('option:first').text();
                        alert('Please select ' + fieldLabel);
                        return;
                    }

                    var guests = parseInt(guestValue) || 1;

                    $('.vip-card').removeClass('selected');
                    $btn.closest('.vip-card').addClass('selected');

                    $.ajax({
                        url: "/{{ $data->slug }}/addons/" + packageId,
                        type: 'GET',
                        dataType: 'json',
                        success: function (res) {
                            window.pendingPackageSelection = {
                                packageId: packageId,
                                packageName: packageName,
                                packagePrice: packagePrice,
                                guests: guests,
                                isMultiple: isMultiple,
                                transportation: ($btn.data('transportation') == 1),
                                addons: Array.isArray(res) ? res : []
                            };

                            openAddonSelectionModal(window.pendingPackageSelection);
                        }
                    });
                });

                $('#addonModalConfirmBtn').on('click', function() {
                    if (!window.pendingPackageSelection) {
                        return;
                    }

                    var selection = window.pendingPackageSelection;
                    var selectedAddons = [];

                    $('#addonSelectionModalBody .addon-qty-val').each(function() {
                        var qty = parseInt($(this).text(), 10) || 0;
                        if (qty > 0) {
                            var unitPrice = parseFloat($(this).data('price'));
                            selectedAddons.push({
                                id: $(this).data('id'),
                                name: $(this).data('name'),
                                unit_price: unitPrice,
                                price: unitPrice * qty,
                                qty: qty
                            });
                        }
                    });

                    window.addPackageToCart(
                        selection.packageId,
                        selection.packageName,
                        selection.packagePrice,
                        selection.guests,
                        selectedAddons,
                        selection.transportation,
                        selection.isMultiple
                    ).then(function(added) {
                        if (!added) {
                            return;
                        }

                        $('#package_id').val(selection.packageId);
                        $('#addons').val(selectedAddons.map(function(addon) { return addon.id; }).join(','));
                        $('.package_number_of_guest').val(selection.guests);
                        $('.dynamic-price').show();
                        $('.default-price').hide();
                        $('#checkout-steps').show();
                        syncTransportationStateFromCart();
                        showStep(1);

                        bootstrap.Modal.getOrCreateInstance(document.getElementById('addonSelectionModal')).hide();
                        window.pendingPackageSelection = null;
                    });
                });

                $(document).on('click', '#addonSelectionModalBody .addon-qty-dec', function() {
                    var id = $(this).data('id');
                    var valEl = $('#addonSelectionModalBody .addon-qty-val[data-id="' + id + '"]');
                    var current = parseInt(valEl.text(), 10) || 0;
                    var next = current > 0 ? current - 1 : 0;
                    valEl.text(next);
                    var unitPrice = parseFloat(valEl.data('price')) || 0;
                    $('#addonSelectionModalBody .addon-line-total-value[data-id="' + id + '"]').text(formatCurrency(unitPrice * next));
                });

                $(document).on('click', '#addonSelectionModalBody .addon-qty-inc', function() {
                    var id = $(this).data('id');
                    var valEl = $('#addonSelectionModalBody .addon-qty-val[data-id="' + id + '"]');
                    var current = parseInt(valEl.text(), 10) || 0;
                    var next = current + 1;
                    valEl.text(next);
                    var unitPrice = parseFloat(valEl.data('price')) || 0;
                    $('#addonSelectionModalBody .addon-line-total-value[data-id="' + id + '"]').text(formatCurrency(unitPrice * next));
                });

                $(document).on('change', '#package_use_date', function() {
                    var previousDate = String(window.lastSelectedUseDate || '').trim();
                    var currentDate = (typeof window.getSelectedUseDate === 'function')
                        ? window.getSelectedUseDate()
                        : String($('#package_use_date').val() || $('.package_use_date').val() || '').trim();

                    if (previousDate && currentDate && previousDate !== currentDate && Array.isArray(window.cart) && window.cart.length) {
                        resetCartForDateChange();
                        alert('Cart was reset because reservation date changed. Please add packages again for the new date.');
                    }

                    window.lastSelectedUseDate = currentDate;
                    clearReservationDateError();
                    if (typeof window.syncUseDateField === 'function') {
                        window.syncUseDateField();
                    }
                    refreshEventPackageSelectionLimits(true);
                });

                setTimeout(function() {
                    refreshEventPackageSelectionLimits(false);
                }, 150);
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
                syncTransportationStateFromCart();
                
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

                // On mobile, scroll to the top of the new step
                if (window.innerWidth < 992) {
                    setTimeout(function() {
                        var el = document.getElementById('section-' + stepNumber);
                        if (el) {
                            el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }
                    }, 50);
                }
            }
            
            function validateStep(stepNumber) {
                let isValid = true;
                const requiredFields = [];
                let firstInvalidField = null;
                let alertMessage = 'Please fill in all required fields.';
                
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
                        '[name="package_use_date"]',
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
                        if (!firstInvalidField) {
                            firstInvalidField = field;
                        }
                    } else {
                        field.removeClass('required-field');
                    }
                });

                if (isValid && stepNumber === 2 && window.requiresTransportation && typeof validateTransportationScheduleClient === 'function') {
                    const scheduleValidation = validateTransportationScheduleClient();
                    if (!scheduleValidation.valid) {
                        isValid = false;
                        firstInvalidField = scheduleValidation.field || firstInvalidField;
                        alertMessage = scheduleValidation.message;
                    }
                }

                if (stepNumber === 2 && window.requiresTransportation) {
                    const transportationGuestField = $('[name="transportation_guest"]');
                    const transportationGuestValue = parseInt(transportationGuestField.val(), 10);
                    if (!Number.isFinite(transportationGuestValue) || transportationGuestValue < 1) {
                        transportationGuestField.addClass('required-field');
                        isValid = false;
                        firstInvalidField = firstInvalidField || transportationGuestField;
                        alertMessage = 'Please enter Number of Guest(s) in Transportation (minimum 1).';
                    }
                }
                
                if (!isValid && stepNumber === 2 && window.requiresTransportation && alertMessage === 'Please fill in all required fields.') {
                    alertMessage = 'Please complete the required transportation details before proceeding.';
                }

                if (!isValid) {
                    alert(alertMessage);
                    if (firstInvalidField && firstInvalidField.length) {
                        firstInvalidField.trigger('focus');
                        firstInvalidField[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
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

        <input type="hidden" id="processing_fee" value="{{ (float) ($data->processing_fee ?? 0) }}">

        <input type="hidden" id="processing_fee_type" value="{{ $data->processing_fee_type ?? 'percentage' }}">

        <script>
            function openModal() {
                // Get the description from the clicked addon
                const description = event.target.closest('.addon-item').querySelector('label').getAttribute('data-description');
                const title = event.target.closest('.addon-item').querySelector('label').getAttribute('data-title');

                $('.modal-title').text(title);
                $('.modal-body').html(`<p style="color: #000 !important;">${description}</p>`);
                $('.modal').modal('show');

            }

            function openPackageModal() {

                // Get the description from the clicked package
                const description = event.target.closest('.vip-card').querySelector('.items').getAttribute('data-description');
                const title = event.target.closest('.vip-card').querySelector('.items').getAttribute('data-title');

                $('.modal-title').text(title);
                $('.modal-body').html(`<p style="color: #000 !important;">${description}</p>`);
                $('.modal').modal('show');

            }

            function addToTotal(price, name, id) {
                // This function is now handled by cart system
                // Keeping for backward compatibility
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
            $('.package_number_of_guestss').on('change', function() {
                var $field = $(this);
                var selectedValue = parseInt($field.val(), 10) || 1;
                var packageId = $field.data('id');
                var useDate = (typeof window.getSelectedUseDate === 'function')
                    ? window.getSelectedUseDate()
                    : String($('#package_use_date').val() || $('.package_use_date').val() || '').trim();

                $.get('/{{ $data->slug }}/package/' + packageId + '/capacity', {
                    use_date: useDate,
                    requested_quantity: selectedValue
                }).done(function(response) {
                    var maxSelectable = parseInt(response.max_select, 10);
                    if (!Number.isFinite(maxSelectable)) {
                        maxSelectable = parseInt(response.capacity, 10) || 1;
                    }

                    if (selectedValue > maxSelectable) {
                        if (typeof window.updateGuestSelectOptions === 'function') {
                            window.updateGuestSelectOptions($field, maxSelectable, response.message || 'Sold Out!');
                        }
                        if (typeof window.showGuestFieldError === 'function') {
                            window.showGuestFieldError($field, response.message || 'The selected quantity is not available for this date.');
                        }
                        return;
                    }

                    if (typeof window.clearGuestFieldError === 'function') {
                        window.clearGuestFieldError($field);
                    }
                    $('.package_number_of_guest').val(String(selectedValue));

                    var pkg = window.cart.find(function(p) { return String(p.packageId) === String(packageId); });
                    if (pkg) {
                        pkg.guests = selectedValue;
                        pkg.isMultiple = (typeof window.parseMultipleFlag === 'function')
                            ? window.parseMultipleFlag($field.data('multiple'))
                            : ($field.data('multiple') === true || $field.data('multiple') === 1 || $field.data('multiple') === '1' || $field.data('multiple') === 'true');
                        window.renderCart();
                        window.calculateCartTotal();
                    }

                    syncEventCapacityUi();
                }).fail(function() {
                    if (typeof window.showGuestFieldError === 'function') {
                        window.showGuestFieldError($field, 'Could not verify availability right now. Please try again.');
                    }
                });
            });

            $(document).on('input', '.package_number_of_guestss[type="number"]', function() {
                var $field = $(this);
                var entered = parseInt($field.val(), 10);
                var maxAllowed = parseInt($field.attr('max'), 10);

                if (!Number.isFinite(entered) || entered < 1) {
                    $field.val('1');
                    return;
                }

                if (Number.isFinite(maxAllowed) && maxAllowed > 0 && entered > maxAllowed) {
                    $field.val(String(maxAllowed));
                }
            });
        </script>



        <script>
            // Coupon logic for cart
            $('#applyPromoBtn').on('click', function() {
                let code = $('#promo_code').val().trim();
                if (!code) return;

                var promoSource = '{{ !empty($affiliateReferral) ? 'affiliate' : 'club' }}';
                var ownerSlug = '{{ !empty($affiliateReferral) ? $affiliateReferral->slug : '' }}';
                var cartItems = Array.isArray(window.cart) ? window.cart : [];
                var packageIds = [];
                var subtotal = 0;
                var totalQty = 0;

                cartItems.forEach(function(pkg) {
                    var pkgId = parseInt(pkg.packageId, 10) || 0;
                    if (pkgId > 0 && packageIds.indexOf(pkgId) === -1) {
                        packageIds.push(pkgId);
                    }

                    var guests = parseInt(pkg.guests, 10) || 1;
                    var billableGuests = (pkg.isMultiple === true || pkg.isMultiple === 1 || pkg.isMultiple === '1') ? guests : 1;
                    subtotal += (parseFloat(pkg.packagePrice) || 0) * billableGuests;
                    subtotal += (pkg.addons || []).reduce(function(sum, addon) { return sum + (parseFloat(addon.price) || 0); }, 0);
                    totalQty += guests;
                });

                $.get('/{{ $data->slug }}/check/' + encodeURIComponent(code), {
                    source: promoSource,
                    owner_slug: ownerSlug,
                    package_ids: packageIds.join(','),
                    subtotal: subtotal.toFixed(2),
                    total_qty: totalQty
                }, function(res) {
                    if (res.valid === false || res.valid === "false") {
                        window.cartCoupon = null;
                        alert(res.message || 'Invalid promo code');
                        window.calculateCartTotal();
                    } else {
                        window.cartCoupon = {
                            code: code,
                            id: res.id,
                            discount: parseFloat(res.discount),
                            type: res.type || 'percentage'
                        };
                        $('#applyPromoBtn').prop('disabled', true);
                        $('.promo_code').val(res.id);
                        window.calculateCartTotal();
                    }
                });
            });
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

        <script>
            // Auto-discount logic: wrap calculateCartTotal to fetch and apply automatic discounts
            (function () {
                var _origCalcCartTotal = window.calculateCartTotal;
                var _autoDiscountTimer = null;

                var promoSource = '{{ !empty($affiliateReferral) ? 'affiliate' : 'club' }}';
                var ownerSlug = '{{ !empty($affiliateReferral) ? $affiliateReferral->slug : '' }}';
                var siteSlug = '{{ $data->slug }}';

                function fetchAutoDiscount() {
                    var cartItems = Array.isArray(window.cart) ? window.cart : [];
                    if (cartItems.length === 0) {
                        if (window.cartCoupon && window.cartCoupon.isAutomatic) {
                            window.cartCoupon = null;
                            _origCalcCartTotal();
                        }
                        return;
                    }
                    var packageIds = [];
                    var subtotal = 0;
                    var totalQty = 0;
                    cartItems.forEach(function (pkg) {
                        var pkgId = parseInt(pkg.packageId, 10) || 0;
                        if (pkgId > 0 && packageIds.indexOf(pkgId) === -1) packageIds.push(pkgId);
                        var guests = parseInt(pkg.guests, 10) || 1;
                        var billable = (pkg.isMultiple === true || pkg.isMultiple === 1 || pkg.isMultiple === '1') ? guests : 1;
                        subtotal += (parseFloat(pkg.packagePrice) || 0) * billable;
                        subtotal += (pkg.addons || []).reduce(function (s, a) { return s + (parseFloat(a.price) || 0); }, 0);
                        totalQty += guests;
                    });
                    $.get('/' + siteSlug + '/auto-discounts', {
                        source: promoSource,
                        owner_slug: ownerSlug,
                        package_ids: packageIds.join(','),
                        subtotal: subtotal.toFixed(2),
                        total_qty: totalQty
                    }, function (res) {
                        if (res.valid) {
                            window.cartCoupon = {
                                code: res.name,
                                id: res.id,
                                discount: parseFloat(res.discount),
                                type: res.type || 'percentage',
                                isAutomatic: true
                            };
                        } else if (window.cartCoupon && window.cartCoupon.isAutomatic) {
                            window.cartCoupon = null;
                        }
                        _origCalcCartTotal();
                    });
                }

                window.calculateCartTotal = function () {
                    _origCalcCartTotal();
                    // Only trigger auto-discount fetch when no manual coupon is active
                    if (!window.cartCoupon || window.cartCoupon.isAutomatic) {
                        clearTimeout(_autoDiscountTimer);
                        _autoDiscountTimer = setTimeout(fetchAutoDiscount, 400);
                    }
                };
            })();
        </script>

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script>
            flatpickr("#package_use_date", {
                dateFormat: "Y-m-d",
                defaultDate: null,
                minDate: "today",
                allowInput: false,
                clickOpens: true,
                onChange: function(selectedDates, dateStr) {
                    document.getElementById('package_use_date').value = dateStr;
                }
            });
        </script>
        <script>
            function prepareCheckoutCartPayload(form) {
                syncCheckoutCartFields();
            }

            (function initRawCardNumberFormatting() {
                function detectCardMeta(digits) {
                    var number = String(digits || '');

                    if (/^3[47]/.test(number)) {
                        return { maxLen: 15, validLens: [15], grouping: [4, 6, 5] }; // Amex
                    }
                    if (/^3(?:0[0-5]|[68])/.test(number)) {
                        return { maxLen: 14, validLens: [14], grouping: [4, 6, 4] }; // Diners
                    }
                    if (/^(5[1-5]|2[2-7])/.test(number)) {
                        return { maxLen: 16, validLens: [16], grouping: [4, 4, 4, 4] }; // Mastercard
                    }
                    if (/^(6011|65|64[4-9])/.test(number)) {
                        return { maxLen: 19, validLens: [16, 19], grouping: [4, 4, 4, 4, 3] }; // Discover
                    }
                    if (/^4/.test(number)) {
                        return { maxLen: 19, validLens: [13, 16, 19], grouping: [4, 4, 4, 4, 3] }; // Visa
                    }
                    if (/^35/.test(number)) {
                        return { maxLen: 19, validLens: [16, 17, 18, 19], grouping: [4, 4, 4, 4, 3] }; // JCB
                    }

                    return { maxLen: 19, validLens: [13, 14, 15, 16, 17, 18, 19], grouping: [4, 4, 4, 4, 3] };
                }

                function formatWithGrouping(digits, grouping) {
                    var cursor = 0;
                    var parts = [];

                    for (var i = 0; i < grouping.length && cursor < digits.length; i++) {
                        var size = grouping[i];
                        var chunk = digits.slice(cursor, cursor + size);
                        if (!chunk) {
                            break;
                        }
                        parts.push(chunk);
                        cursor += size;
                    }

                    if (cursor < digits.length) {
                        parts.push(digits.slice(cursor));
                    }

                    return parts.join(' ');
                }

                function applyMask(input) {
                    if (!input) {
                        return;
                    }

                    var digits = String(input.value || '').replace(/\D/g, '');
                    var meta = detectCardMeta(digits);
                    var maxDigits = Math.min(meta.maxLen, 16);
                    var allowedLengths = meta.validLens.filter(function(len) { return len <= maxDigits; });

                    if (allowedLengths.length === 0) {
                        allowedLengths = [maxDigits];
                    }

                    if (digits.length > maxDigits) {
                        digits = digits.slice(0, maxDigits);
                    }

                    input.value = formatWithGrouping(digits, meta.grouping);
                    input.maxLength = formatWithGrouping(new Array(maxDigits + 1).join('9'), meta.grouping).length;
                    input.setAttribute('inputmode', 'numeric');
                    input.setAttribute('autocomplete', 'cc-number');
                    input.setCustomValidity('');

                    if (digits.length > 0 && allowedLengths.indexOf(digits.length) === -1) {
                        input.setCustomValidity('Please enter a valid card number.');
                    }
                }

                function bindField(input) {
                    if (!input || input.dataset.cardFormatBound === '1') {
                        return;
                    }

                    input.dataset.cardFormatBound = '1';
                    applyMask(input);
                    input.addEventListener('input', function() { applyMask(input); });
                    input.addEventListener('blur', function() { applyMask(input); });
                }

                var cardFields = document.querySelectorAll('input[name="card_number"]');
                cardFields.forEach(function(field) { bindField(field); });

                var form = document.getElementById('payment-form');
                if (form) {
                    form.addEventListener('submit', function(event) {
                        var inputs = form.querySelectorAll('input[name="card_number"]');
                        var hasInvalid = false;

                        inputs.forEach(function(input) {
                            applyMask(input);
                            if (!input.checkValidity()) {
                                hasInvalid = true;
                            }
                            input.value = String(input.value || '').replace(/\D/g, '');
                        });

                        if (hasInvalid) {
                            event.preventDefault();
                            var first = inputs[0];
                            if (first) {
                                first.reportValidity();
                            }
                        }
                    });
                }
            })();

            document.getElementById('payment-form')?.addEventListener('submit', function(e) {
                if (!ensureReservationDateSelected()) {
                    e.preventDefault();
                    return;
                }
                prepareCheckoutCartPayload(this);
            });

            const transportationSchedule = {
                startTime: @json($data->operating_start_time),
                endTime: @json($data->operating_end_time),
            };

            function parseTimeToMinutes(timeValue) {
                if (!timeValue) {
                    return null;
                }

                const trimmedValue = String(timeValue).trim();
                const twelveHourMatch = trimmedValue.match(/^(\d{1,2}):(\d{2})\s*(AM|PM)$/i);
                if (twelveHourMatch) {
                    let hours = parseInt(twelveHourMatch[1], 10) % 12;
                    const minutes = parseInt(twelveHourMatch[2], 10);
                    if (twelveHourMatch[3].toUpperCase() === 'PM') {
                        hours += 12;
                    }

                    return (hours * 60) + minutes;
                }

                const twentyFourHourMatch = trimmedValue.match(/^(\d{1,2}):(\d{2})$/);
                if (twentyFourHourMatch) {
                    return (parseInt(twentyFourHourMatch[1], 10) * 60) + parseInt(twentyFourHourMatch[2], 10);
                }

                return null;
            }

            function isTimeWithinOperatingHours(timeValue) {
                const pickupMinutes = parseTimeToMinutes(timeValue);
                if (pickupMinutes === null) {
                    return false;
                }

                const startMinutes = parseTimeToMinutes(transportationSchedule.startTime);
                const endMinutes = parseTimeToMinutes(transportationSchedule.endTime);

                if (startMinutes === null || endMinutes === null) {
                    return true;
                }

                if (endMinutes < startMinutes) {
                    return pickupMinutes >= startMinutes || pickupMinutes <= endMinutes;
                }

                return pickupMinutes >= startMinutes && pickupMinutes <= endMinutes;
            }

            function validateTransportationScheduleClient() {
                const pickupTimeField = $('[name="transportation_pickup_time"]');
                const pickupTime = pickupTimeField.val().trim();

                if (!pickupTime) {
                    pickupTimeField.addClass('required-field');
                    return {
                        valid: false,
                        field: pickupTimeField,
                        message: 'Please complete the required transportation details before proceeding.'
                    };
                }

                if (!isTimeWithinOperatingHours(pickupTime)) {
                    pickupTimeField.addClass('required-field');
                    return {
                        valid: false,
                        field: pickupTimeField,
                        message: 'Pickup time must be within the club operating hours.'
                    };
                }

                return { valid: true, field: null, message: '' };
            }

            // Flatpickr time picker for pick-up time — visual picker on all devices including iOS.
            // Pick-up time picker: desktop uses Flatpickr, mobile uses the native time control.
            (function () {
                var el = document.querySelector('input[name="transportation_pickup_time"]');
                if (!el) return;
                function to24h(t) {
                    if (!t) return null;
                    var m = String(t).trim().match(/^(\d{1,2}):(\d{2})\s*(AM|PM)?$/i);
                    if (!m) return null;
                    var hh = parseInt(m[1], 10);
                    var mm = parseInt(m[2], 10);
                    if (m[3]) {
                        var mer = m[3].toUpperCase();
                        if (mer === 'PM' && hh < 12) hh += 12;
                        else if (mer === 'AM' && hh === 12) hh = 0;
                    }
                    return String(hh).padStart(2, '0') + ':' + String(mm).padStart(2, '0');
                }
                var minT = to24h(typeof transportationSchedule !== 'undefined' ? transportationSchedule.startTime : null);
                var maxT = to24h(typeof transportationSchedule !== 'undefined' ? transportationSchedule.endTime : null);
                var isMobileDevice = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)
                    || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);

                if (isMobileDevice) {
                    el.type = 'time';
                    el.removeAttribute('readonly');
                    el.step = 900;
                    if (minT) el.min = minT;
                    if (maxT) el.max = maxT;
                    el.addEventListener('input', function () {
                        $(el).removeClass('required-field');
                    });
                    return;
                }

                el.type = 'text';
                el.setAttribute('readonly', 'readonly');
                if (typeof flatpickr === 'undefined') {
                    el.type = 'time';
                    el.removeAttribute('readonly');
                    if (minT) el.min = minT;
                    if (maxT) el.max = maxT;
                    el.step = 900;
                    return;
                }

                flatpickr(el, {
                    enableTime: true,
                    noCalendar: true,
                    time_24hr: false,
                    minuteIncrement: 15,
                    dateFormat: 'H:i',
                    allowInput: false,
                    onChange: function () {
                        $(el).removeClass('required-field');
                    },
                    minTime: minT || undefined,
                    maxTime: maxT || undefined
                });
            })();

            // Keep hidden use-date in sync with actual selected reservation date.
            if (typeof window.syncUseDateField === 'function') {
                window.syncUseDateField();
            } else {
                $('.package_use_date').val(String($('#package_use_date').val() || '').trim());
            }
        </script>

        <script>
            // Keep hidden submit value synced to selected reservation date.
            if (typeof window.syncUseDateField === 'function') {
                window.syncUseDateField();
            } else {
                $('.package_use_date').val(String($('#package_use_date').val() || '').trim());
            }
        </script>

        @if ($data->payment_method == 'stripe')
            <script src="https://js.stripe.com/v3/"></script>

            @php
                $setting = \App\Models\Setting::where('id',1)->first();
            @endphp

            <script>
                    const stripe = Stripe("{{ $setting->stripe_key }}");
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

                        if (!ensureReservationDateSelected()) {
                            hideCheckoutProcessingOverlay();
                            return;
                        }

                        prepareCheckoutCartPayload(form);
                        showCheckoutProcessingOverlay();

                        const {token, error} = await stripe.createToken(cardNumber);

                        if (error) {
                            hideCheckoutProcessingOverlay();
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

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const mobileQuery = window.matchMedia('(max-width: 768px)');
                const collapsibleBlocks = document.querySelectorAll('[data-mobile-collapsible]');

                function refreshCollapsibleBlock(block) {
                    const content = block.querySelector('.story-copy-collapsible');
                    const toggle = block.querySelector('.story-copy-toggle');

                    if (!content || !toggle) {
                        return;
                    }

                    if (!mobileQuery.matches) {
                        block.classList.remove('is-collapsed');
                        block.classList.remove('is-expanded');
                        toggle.style.display = 'none';
                        toggle.textContent = 'See more';
                        toggle.setAttribute('aria-expanded', 'true');
                        return;
                    }

                    if (!block.classList.contains('is-expanded')) {
                        block.classList.add('is-collapsed');
                    }

                    requestAnimationFrame(function() {
                        const isOverflowing = content.scrollHeight > content.clientHeight + 1;

                        if (!isOverflowing && !block.classList.contains('is-expanded')) {
                            block.classList.remove('is-collapsed');
                            toggle.style.display = 'none';
                            return;
                        }

                        toggle.style.display = 'inline-flex';
                        toggle.textContent = block.classList.contains('is-expanded') ? 'See less' : 'See more';
                        toggle.setAttribute('aria-expanded', block.classList.contains('is-expanded') ? 'true' : 'false');
                    });
                }

                collapsibleBlocks.forEach(function(block) {
                    const toggle = block.querySelector('.story-copy-toggle');

                    if (!toggle) {
                        return;
                    }

                    toggle.addEventListener('click', function() {
                        block.classList.toggle('is-expanded');
                        block.classList.toggle('is-collapsed', !block.classList.contains('is-expanded'));
                        refreshCollapsibleBlock(block);
                    });

                    refreshCollapsibleBlock(block);
                });

                if (typeof mobileQuery.addEventListener === 'function') {
                    mobileQuery.addEventListener('change', function() {
                        collapsibleBlocks.forEach(refreshCollapsibleBlock);
                    });
                } else if (typeof mobileQuery.addListener === 'function') {
                    mobileQuery.addListener(function() {
                        collapsibleBlocks.forEach(refreshCollapsibleBlock);
                    });
                }
            });
        </script>

        <script>
            // Mobile: move Order Summary between package selection and the step indicator
            // (Package Details / Transportation / Payment). Desktop: restore to its original parent.
            document.addEventListener('DOMContentLoaded', function() {
                var sidebar = document.getElementById('cv-order-sidebar');
                var stepsAnchor = document.getElementById('checkout-steps');
                if (!sidebar || !stepsAnchor) return;

                var originalParent = sidebar.parentNode;
                var originalNext = sidebar.nextSibling;
                var mq = window.matchMedia('(max-width: 991px)');

                function applySidebarPlacement() {
                    if (mq.matches) {
                        if (sidebar.parentNode !== stepsAnchor.parentNode || sidebar.nextSibling !== stepsAnchor) {
                            stepsAnchor.parentNode.insertBefore(sidebar, stepsAnchor);
                        }
                    } else {
                        if (sidebar.parentNode !== originalParent) {
                            if (originalNext && originalNext.parentNode === originalParent) {
                                originalParent.insertBefore(sidebar, originalNext);
                            } else {
                                originalParent.appendChild(sidebar);
                            }
                        }
                    }
                }

                applySidebarPlacement();
                if (typeof mq.addEventListener === 'function') {
                    mq.addEventListener('change', applySidebarPlacement);
                } else if (typeof mq.addListener === 'function') {
                    mq.addListener(applySidebarPlacement);
                }
            });
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const requestedPackageId = @json($requestedPackageId ?? null);
                if (!requestedPackageId) {
                    return;
                }

                setTimeout(function() {
                    const targetButton = document.querySelector('.vip-btn[data-id="' + requestedPackageId + '"]');
                    if (targetButton) {
                        targetButton.click();
                        const steps = document.getElementById('checkout-steps');
                        if (steps) {
                            steps.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }
                    }
                }, 350);
            });
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const popup = @json(isset($checkoutPopup) && $checkoutPopup ? ['id' => $checkoutPopup->id, 'show_once_per_session' => (bool) $checkoutPopup->show_once_per_session] : null);
                if (!popup) {
                    return;
                }

                const modalElement = document.getElementById('checkoutPopupModal');
                if (!modalElement) {
                    return;
                }

                const seenKey = 'checkout_popup_seen_' + popup.id;
                if (popup.show_once_per_session && sessionStorage.getItem(seenKey) === '1') {
                    return;
                }

                setTimeout(function() {
                    bootstrap.Modal.getOrCreateInstance(modalElement).show();
                    if (popup.show_once_per_session) {
                        sessionStorage.setItem(seenKey, '1');
                    }
                }, 450);
            });
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Gallery Modal Carousel
                function initGalleryCarousel() {
                    var currentSlide = 0;
                    var slides = Array.from(document.querySelectorAll('#affGalleryCarousel .aff-gallery-carousel-item'));
                    var indicators = Array.from(document.querySelectorAll('#affGalleryIndicators .aff-gallery-carousel-indicator'));
                    var prevBtn = document.getElementById('affGalleryPrev');
                    var nextBtn = document.getElementById('affGalleryNext');

                    if (slides.length === 0) return;

                    function showSlide(index) {
                        slides.forEach(slide => slide.style.display = 'none');
                        indicators.forEach(ind => ind.classList.remove('active'));

                        if (index < 0) currentSlide = slides.length - 1;
                        if (index >= slides.length) currentSlide = 0;

                        slides[currentSlide].style.display = 'flex';
                        if (indicators[currentSlide]) {
                            indicators[currentSlide].classList.add('active');
                        }
                    }

                    if (prevBtn) {
                        prevBtn.addEventListener('click', function() {
                            currentSlide--;
                            showSlide(currentSlide);
                        });
                    }

                    if (nextBtn) {
                        nextBtn.addEventListener('click', function() {
                            currentSlide++;
                            showSlide(currentSlide);
                        });
                    }

                    indicators.forEach(function(indicator) {
                        indicator.addEventListener('click', function() {
                            currentSlide = parseInt(this.getAttribute('data-index'));
                            showSlide(currentSlide);
                        });
                    });

                    // Show first slide
                    showSlide(0);

                    // Keyboard navigation
                    document.addEventListener('keydown', function(e) {
                        var modal = document.getElementById('affGalleryModal');
                        if (modal && modal.classList.contains('show')) {
                            if (e.key === 'ArrowLeft') {
                                currentSlide--;
                                showSlide(currentSlide);
                            } else if (e.key === 'ArrowRight') {
                                currentSlide++;
                                showSlide(currentSlide);
                            }
                        }
                    });
                }

                // Hero Gallery Carousel (Desktop only)
                function initHeroCarousel() {
                    var track = document.getElementById('affHeroCarouselTrack');
                    var prevBtn = document.getElementById('affHeroCarouselPrev');
                    var nextBtn = document.getElementById('affHeroCarouselNext');
                    if (!track) return;

                    var items = Array.from(track.querySelectorAll('.aff-hero-carousel-item'));
                    var currentIndex = 0;

                    if (items.length === 0) return;

                    function showItem(index) {
                        items.forEach(item => item.style.display = 'none');
                        items[index].style.display = 'flex';
                    }

                    if (prevBtn) {
                        prevBtn.addEventListener('click', function() {
                            currentIndex = (currentIndex - 1 + items.length) % items.length;
                            showItem(currentIndex);
                        });
                    }

                    if (nextBtn) {
                        nextBtn.addEventListener('click', function() {
                            currentIndex = (currentIndex + 1) % items.length;
                            showItem(currentIndex);
                        });
                    }

                    // Show first item
                    showItem(0);
                }

                initHeroCarousel();

                // Profile picture click to open gallery
                var profileBtn = document.getElementById('affProfileStoryBtn');
                if (profileBtn) {
                    profileBtn.addEventListener('click', function() {
                        var modal = document.getElementById('affGalleryModal');
                        if (modal) {
                            bootstrap.Modal.getOrCreateInstance(modal).show();
                            initGalleryCarousel();
                        }
                    });
                }
            });
        </script>

        <div class="modal fade checkout-gallery-modal" id="checkoutGalleryModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Gallery Image</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <img src="" alt="" id="checkoutGalleryModalImage" class="checkout-gallery-modal-image">
                    </div>
                </div>
            </div>
        </div>

        <!-- Affiliate Gallery Carousel Modal -->
        <div class="modal fade" id="affGalleryModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content aff-gallery-modal-content">
                    <div class="modal-header" style="border-bottom: 1px solid rgba(167,116,255,0.2);">
                        <h5 class="modal-title" style="color: #fff;">Gallery</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body aff-gallery-modal-body">
                        <div class="aff-gallery-carousel-container">
                            <div class="aff-gallery-carousel" id="affGalleryCarousel">
                                @foreach($affiliate->gallery_images as $galleryImage)
                                    <div class="aff-gallery-carousel-item" style="display: none;">
                                        <img src="{{ asset('uploads/' . $galleryImage) }}" alt="Gallery image" data-index="{{ $loop->index }}">
                                    </div>
                                @endforeach
                            </div>
                            @if(count($affiliate->gallery_images) > 1)
                                <button type="button" class="aff-gallery-carousel-nav aff-gallery-carousel-prev" id="affGalleryPrev" aria-label="Previous image">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button type="button" class="aff-gallery-carousel-nav aff-gallery-carousel-next" id="affGalleryNext" aria-label="Next image">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            @endif
                        </div>
                        @if(count($affiliate->gallery_images) > 1)
                            <div class="aff-gallery-carousel-indicators" id="affGalleryIndicators">
                                @foreach($affiliate->gallery_images as $galleryImage)
                                    <button type="button" class="aff-gallery-carousel-indicator{{ $loop->first ? ' active' : '' }}" data-index="{{ $loop->index }}" aria-label="Go to image {{ $loop->iteration }}"></button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <script>
        </script>

        <script>
            document.addEventListener('click', function(event) {
                const trigger = event.target.closest('.js-checkout-gallery-trigger');
                if (!trigger) {
                    return;
                }

                const modalElement = document.getElementById('checkoutGalleryModal');
                const modalImage = document.getElementById('checkoutGalleryModalImage');
                if (!modalElement || !modalImage) {
                    return;
                }

                modalImage.src = trigger.getAttribute('data-gallery-src') || '';
                modalImage.alt = trigger.getAttribute('data-gallery-alt') || 'Gallery image';
                bootstrap.Modal.getOrCreateInstance(modalElement).show();
            });

            document.getElementById('checkoutGalleryModal')?.addEventListener('hidden.bs.modal', function() {
                const modalImage = document.getElementById('checkoutGalleryModalImage');
                if (modalImage) {
                    modalImage.src = '';
                    modalImage.alt = '';
                }
            });
        </script>

        <script>
        (function() {
            function initSidebar() {
                var sidebarBody = document.getElementById('cv-sidebar-body');
                if (!sidebarBody) return;

                var cartSection = document.getElementById('cart-section');
                var pricingShell = document.querySelector('.pricing-shell');
                if (cartSection) sidebarBody.appendChild(cartSection);
                if (pricingShell) sidebarBody.appendChild(pricingShell);

                // Move the promo code section to AFTER the deposit box so it sits below the Due Today box.
                var depositBox = document.getElementById('cv-deposit-box');
                var promoCol = pricingShell ? pricingShell.querySelector('.dynamic-price.col-md-6') : null;
                if (depositBox && promoCol && depositBox.parentNode) {
                    depositBox.parentNode.insertBefore(promoCol, depositBox.nextSibling);
                }
            }

            function initPackageSearch() {
                var locationFilter = document.getElementById('package-location-filter-main');
                var categoriesGate = document.querySelector('.aff-location-gated');
                var noLocationMsg = document.getElementById('package-no-location-message');

                if (!locationFilter) return;

                // Hide all category tabs initially using class
                document.querySelectorAll('.package-category-tile').forEach(function(tab) {
                    tab.classList.add('hidden-tab');
                    tab.classList.remove('visible-tab');
                });

                function filterPackages() {
                    var locationId = String(locationFilter.value || '').trim();
                    console.log('Filter called with locationId:', locationId);
                    var packageHeader = document.querySelector('.aff-package-header-gated');

                    if (!locationId) {
                        // No location selected - hide everything
                        if (categoriesGate) categoriesGate.classList.remove('is-visible');
                        if (noLocationMsg) noLocationMsg.style.display = 'block';
                        if (packageHeader) packageHeader.style.display = 'none';
                        document.querySelectorAll('.package-category-tile').forEach(function(tab) {
                            tab.classList.add('hidden-tab');
                            tab.classList.remove('visible-tab');
                        });
                        return;
                    }

                    // Location selected - show categories gate and package header
                    if (categoriesGate) categoriesGate.classList.add('is-visible');
                    if (noLocationMsg) noLocationMsg.style.display = 'none';
                    if (packageHeader) packageHeader.style.display = 'flex';

                    // Hide all packages first
                    document.querySelectorAll('[id^="pkg-card-"]').forEach(function(card) {
                        card.style.display = 'none';
                    });

                    // Show only packages from selected location
                    document.querySelectorAll('[id^="pkg-card-"]').forEach(function(card) {
                        var clubId = card.getAttribute('data-club-id');
                        if (clubId) clubId = clubId.trim();
                        if (clubId && clubId === locationId) {
                            card.style.display = '';
                        }
                    });

                    // Show only categories that have packages from selected location
                    var firstVisibleTab = null;
                    document.querySelectorAll('.package-category-tile').forEach(function(tab) {
                        var targetId = tab.getAttribute('data-target');
                        var categoryId = null;
                        if (targetId) {
                            var match = targetId.match(/category-group-(.+)/);
                            if (match) {
                                categoryId = match[1];
                            }
                        }

                        if (categoryId) {
                            var groupDiv = document.getElementById('category-group-' + categoryId);
                            var hasPackagesFromLocation = groupDiv ?
                                Array.from(groupDiv.querySelectorAll('[id^="pkg-card-"]')).some(function(card) {
                                    var clubId = card.getAttribute('data-club-id');
                                    if (clubId) clubId = clubId.trim();
                                    return clubId && clubId === locationId;
                                })
                                : false;

                            if (hasPackagesFromLocation) {
                                tab.classList.remove('hidden-tab');
                                tab.classList.add('visible-tab');
                                if (!firstVisibleTab) {
                                    firstVisibleTab = tab;
                                }
                            } else {
                                tab.classList.add('hidden-tab');
                                tab.classList.remove('visible-tab');
                            }
                        }
                    });

                    // Auto-open the first visible category so selected packages are actually shown.
                    document.querySelectorAll('.package-category-group').forEach(function(group) {
                        var groupHasVisibleCard = Array.from(group.querySelectorAll('[id^="pkg-card-"]')).some(function(card) {
                            return card.style.display !== 'none';
                        });
                        group.style.display = groupHasVisibleCard ? '' : 'none';
                    });

                    if (firstVisibleTab) {
                        document.querySelectorAll('.package-category-tile').forEach(function(tab) {
                            tab.classList.toggle('active', tab === firstVisibleTab);
                        });
                    }
                }

                locationFilter.addEventListener('change', filterPackages);
                // Call filterPackages once on init to set initial state
                filterPackages();
            }

            function initMobileGalleryCarousel() {
                var track = document.getElementById('affMgcTrack');
                var dotsWrap = document.getElementById('affMgcDots');
                if (!track) return;

                var dots = dotsWrap ? Array.from(dotsWrap.querySelectorAll('.aff-mgc-dot')) : [];
                var slides = Array.from(track.querySelectorAll('.aff-mgc-slide'));
                var autoScrollInterval = null;
                var currentSlide = 0;
                var isDown = false;
                var startX;
                var scrollLeft;

                function setDot(idx) {
                    dots.forEach(function(d, i) { d.classList.toggle('is-active', i === idx); });
                }

                function startAutoScroll() {
                    clearInterval(autoScrollInterval);
                    autoScrollInterval = setInterval(function() {
                        currentSlide = (currentSlide + 1) % slides.length;
                        var slideWidth = track.offsetWidth; // Use actual track width
                        track.scrollLeft = slideWidth * currentSlide;
                        setDot(currentSlide);
                    }, 5000);
                }

                function stopAutoScroll() {
                    clearInterval(autoScrollInterval);
                }

                // Drag functionality (horizontal)
                track.addEventListener('mousedown', function(e) {
                    isDown = true;
                    startX = e.pageX - track.offsetLeft;
                    scrollLeft = track.scrollLeft;
                    track.classList.add('is-dragging');
                    stopAutoScroll();
                });

                track.addEventListener('mouseleave', function() {
                    isDown = false;
                    track.classList.remove('is-dragging');
                    startAutoScroll();
                });

                track.addEventListener('mouseup', function() {
                    isDown = false;
                    track.classList.remove('is-dragging');
                    startAutoScroll();
                });

                track.addEventListener('mousemove', function(e) {
                    if (!isDown) return;
                    e.preventDefault();
                    var x = e.pageX - track.offsetLeft;
                    var walk = (x - startX) * 0.5;
                    track.scrollLeft = scrollLeft - walk;
                });

                // Touch support (horizontal drag)
                track.addEventListener('touchstart', function(e) {
                    isDown = true;
                    startX = e.touches[0].pageX - track.offsetLeft;
                    scrollLeft = track.scrollLeft;
                    stopAutoScroll();
                }, { passive: true });

                track.addEventListener('touchend', function() {
                    isDown = false;
                    startAutoScroll();
                }, { passive: true });

                track.addEventListener('touchmove', function(e) {
                    if (!isDown) return;
                    var x = e.touches[0].pageX - track.offsetLeft;
                    var walk = (x - startX) * 1.5;
                    track.scrollLeft = scrollLeft - walk;
                }, { passive: true });

                // Scroll snap update
                track.addEventListener('scroll', function() {
                    var scrollPos = track.scrollLeft;
                    var slideWidth = track.offsetWidth; // Use actual track width
                    currentSlide = Math.round(scrollPos / slideWidth);
                    setDot(Math.min(currentSlide, slides.length - 1));
                });

                // Dot navigation
                dots.forEach(function(dot, index) {
                    dot.addEventListener('click', function() {
                        currentSlide = index;
                        var slideWidth = track.offsetWidth; // Use actual track width
                        track.scrollLeft = slideWidth * index;
                        setDot(currentSlide);
                        stopAutoScroll();
                        startAutoScroll();
                    });
                });

                setDot(0);
                if (slides.length > 1) startAutoScroll();
            }

            function initDefaultOpenCategory() {
                var $firstTile = $('.package-category-tile').first();
                if ($firstTile.length) {
                    $firstTile.trigger('click');
                }
            }

            function initSidebarDateSync() {
                var dateInput = document.getElementById('package_use_date');
                var sidebarDate = document.getElementById('cv-sidebar-date');
                if (!dateInput || !sidebarDate) return;

                function updateSidebarDate() {
                    var val = dateInput.value;
                    sidebarDate.innerHTML = '<i class="fas fa-calendar-alt" style="margin-right:4px;opacity:.6;"></i>' + (val || 'Select a date above');
                }

                dateInput.addEventListener('change', updateSidebarDate);
                dateInput.addEventListener('input', updateSidebarDate);
                updateSidebarDate();
            }

            function initReservationDatePicker() {
                var dateInput = document.getElementById('package_use_date');
                if (!dateInput) return;

                // Check if flatpickr is available
                if (typeof flatpickr === 'undefined') {
                    console.warn('Flatpickr not loaded, date picker will use browser default');
                    return;
                }

                // Destroy existing flatpickr instance if it exists
                if (dateInput._flatpickr) {
                    dateInput._flatpickr.destroy();
                }

                flatpickr(dateInput, {
                    mode: 'single',
                    minDate: 'today',
                    dateFormat: 'Y-m-d',
                    enableTime: false,
                    disableMobile: false,
                    onChange: function(selectedDates, dateStr, instance) {
                        // Trigger change event for sidebar sync
                        var event = new Event('change', { bubbles: true });
                        dateInput.dispatchEvent(event);
                    }
                });
            }

            function initClubTooltips() {
                var tooltipElements = document.querySelectorAll('.cv-club-name-badge');
                tooltipElements.forEach(function(element) {
                    new bootstrap.Tooltip(element);
                });
            }

            function initSidebarCta() {
                var ctaBtn = document.getElementById('cv-sidebar-cta');
                var cartList = document.getElementById('cart-list');
                if (!ctaBtn || !cartList || !window.MutationObserver) return;

                new MutationObserver(function() {
                    var hasItems = cartList.children.length > 0;
                    ctaBtn.disabled = !hasItems;
                    ctaBtn.style.display = hasItems ? '' : 'none';

                    var depositBox = document.getElementById('cv-deposit-box');
                    if (depositBox) depositBox.style.display = hasItems ? '' : 'none';

                    var editBtn = document.getElementById('cv-edit-cart');
                    if (editBtn) editBtn.style.display = hasItems ? '' : 'none';

                    var mobileCount = document.getElementById('cv-mobile-cart-count');
                    if (mobileCount) {
                        var count = cartList.querySelectorAll('.cart-line').length;
                        mobileCount.textContent = count + (count === 1 ? ' item' : ' items');
                    }
                }).observe(cartList, { childList: true });

                ctaBtn.addEventListener('click', function() {
                    var nextBtn = document.getElementById('next-to-transport');
                    if (nextBtn && nextBtn.style.display !== 'none') {
                        nextBtn.click();
                    }
                });

                // Deposit display is updated directly in calculateCartTotal — no observer needed.
            }

            function initMobileToggle() {
                var toggleBtn = document.getElementById('cv-mobile-cart-toggle');
                var sidebar = document.getElementById('cv-order-sidebar');
                if (!toggleBtn || !sidebar) return;

                toggleBtn.addEventListener('click', function() {
                    sidebar.classList.toggle('cv-sidebar-open');
                });
            }

            function initHamburger() {
                var hamburger = document.getElementById('cv-hamburger');
                if (!hamburger) return;
                hamburger.addEventListener('click', function() {
                    var mobileActions = document.querySelector('.mobile-top-actions');
                    if (mobileActions) {
                        mobileActions.style.display = mobileActions.style.display === 'none' ? '' : 'none';
                    }
                });
            }

            /* ===== Dynamic checkout step indicator ===== */
            function checkPackageFormFilled() {
                var section = document.getElementById('section-1');
                if (!section) return false;
                var reqInputs = section.querySelectorAll('input[required], select[required]');
                if (reqInputs.length === 0) return false;
                for (var i = 0; i < reqInputs.length; i++) {
                    if (!reqInputs[i].value || reqInputs[i].value.trim() === '') return false;
                }
                return true;
            }

            function updateCheckoutSteps() {
                var stepEls = [
                    document.getElementById('cv-dstep-1'),
                    document.getElementById('cv-dstep-2'),
                    document.getElementById('cv-dstep-3'),
                    document.getElementById('cv-dstep-4')
                ];
                if (!stepEls[0]) return;

                stepEls.forEach(function(s) {
                    if (s) s.classList.remove('is-active', 'is-complete');
                });

                var dateInput = document.getElementById('package_use_date');
                var dateDone = !dateInput || (dateInput.value && dateInput.value.trim() !== '');

                var accessTabs = document.querySelectorAll('.cv-access-tab');
                var accessDone = accessTabs.length === 0 || !!document.querySelector('.cv-access-tab.is-active');

                var cartList = document.getElementById('cart-list');
                var cartDone = !!(cartList && cartList.children.length > 0);

                var formDone = checkPackageFormFilled();

                if (dateDone) stepEls[0].classList.add('is-complete');
                if (dateDone && accessDone) stepEls[1].classList.add('is-complete');
                if (dateDone && accessDone && cartDone) stepEls[2].classList.add('is-complete');
                if (dateDone && accessDone && cartDone && formDone) stepEls[3].classList.add('is-complete');

                if (!dateDone) stepEls[0].classList.add('is-active');
                else if (!accessDone) stepEls[1].classList.add('is-active');
                else if (!cartDone) stepEls[2].classList.add('is-active');
                else stepEls[3].classList.add('is-active');
            }
            window.updateCheckoutSteps = updateCheckoutSteps;

            function initCheckoutSteps() {
                if (!document.getElementById('cv-dstep-1')) return;
                updateCheckoutSteps();

                var dateInput = document.getElementById('package_use_date');
                if (dateInput) {
                    dateInput.addEventListener('change', updateCheckoutSteps);
                    dateInput.addEventListener('input', updateCheckoutSteps);
                }

                var cartList = document.getElementById('cart-list');
                if (cartList && window.MutationObserver) {
                    new MutationObserver(updateCheckoutSteps).observe(cartList, { childList: true });
                }

                document.addEventListener('input', function(e) {
                    if (e.target && e.target.closest && e.target.closest('#section-1')) {
                        updateCheckoutSteps();
                    }
                });
                document.addEventListener('change', function(e) {
                    if (e.target && e.target.closest && e.target.closest('#section-1')) {
                        updateCheckoutSteps();
                    }
                });
            }

            /* ===== Date Selection Notification ===== */
            function initDateNotification() {
                var dateInput = document.getElementById('package_use_date');
                if (!dateInput) return;

                dateInput.addEventListener('change', function() {
                    if (this.value && this.value.trim() !== '') {
                        var toast = document.getElementById('cv-cart-toast');
                        var title = document.querySelector('#cv-cart-toast .cv-toast-title');
                        var sub = document.getElementById('cv-cart-toast-sub');
                        var icon = document.querySelector('#cv-cart-toast .cv-toast-icon i');
                        
                        if (toast && title && sub && icon) {
                            title.textContent = 'Reservation date selected!';
                            sub.textContent = 'Choose your package';
                            icon.className = 'fas fa-calendar-check';
                            toast.classList.add('is-visible');
                            
                            setTimeout(function() {
                                toast.classList.remove('is-visible');
                            }, 3500);
                        }
                    }
                });
            }


            document.addEventListener('DOMContentLoaded', function() {
                initSidebar();
                initPackageSearch();
                initMobileGalleryCarousel();
                initClubTooltips();
                initSidebarDateSync();
                initSidebarCta();
                initHamburger();
                initCheckoutSteps();
                initDateNotification();
            });
        })();
        </script>

    </body>

    </html>