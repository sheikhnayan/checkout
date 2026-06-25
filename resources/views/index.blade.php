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
        <title>Checkout</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.7/css/bootstrap.min.css"
            integrity="sha512-fw7f+TcMjTb7bpbLJZlP8g2Y4XcCyFZW8uy8HsRZsH/SwbMw0plKHFHr99DN3l04VsYNwvzicUX/6qurvIxbxw=="
            crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link rel="stylesheet" href="{{ asset('styles/main.css') }}">
        <style>

#Pick-up-time,
input[name="transportation_pickup_time"] {
    background: #ffffff !important;
    color: #111111 !important; required
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
            align-items: center;
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
    align-items: flex-start;
    justify-content: center;
            align-items: center;
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

            /* Red asterisk on required form field labels - currently disabled,  support varies */
            /*
            .form-group > label::after,
            .form-group > label::after,
            .form-group > label::after,
            .form-group > label::after,
            .form-group > label::after,
            .num-guest > label::after {
                content: " *";
                color: #ef4444;
                font-weight: 700;
            }
            */

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
        padding: 8px 30px 8px 15px !important;
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
    align-items: flex-start;
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
    align-items: flex-start;
    justify-content: center;
            align-items: center;
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
    margin-top: 32px;
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
    align-items: flex-start;
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
    align-items: flex-start;
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
    align-items: flex-start;
    justify-content: center;
            align-items: center;
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
    .cv-footer-bar { justify-content: center;
            align-items: center; text-align: center; flex-direction: column; gap: 10px; padding: 14px 0; }
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
   affiliate PAGE DESIGN SYSTEM
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
    align-items: flex-start;
    gap: 14px;
    transition: border-color .2s;
}
.vip-card:hover { border-color: rgba(255,255,255,0.28) !important; }

/* Form inputs ï¿½ frosted glass background */
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
    gap: 12px;
    align-items: flex-start;
    cursor: pointer;
    margin-bottom: 12px;
    font-size: 13px;
    color: rgba(255,255,255,0.9);
    transition: color 0.2s ease;
}
.consent-label:hover {
    color: rgba(255,255,255,1);
}
.consent-label span {
    flex: 1;
    line-height: 1.6;
    padding-top: 2px;
}
.consent-label span a {
    color: #ffcc00;
    text-decoration: none;
    font-weight: 600;
    border-bottom: 1px solid rgba(255,204,0,0.5);
    transition: all 0.2s ease;
}
.consent-label span a:hover {
    color: #fff;
    border-bottom-color: #ffcc00;
    text-decoration: underline;
}
/* TERMS CONSENT - SAME TOGGLE AS OTHER CONSENTS */
/* No special styling - let it be a toggle like the rest */
.consent-label input[type="checkbox"]:focus-visible {
    outline: 2px solid rgba(255,204,0,0.7);
    outline-offset: 2px;
}

/* Payment agreement toggles: exact affiliate parity, locked with stronger selectors */
#payment-consent-group .consent-label,
.payment-consent-group .consent-label {
    display: flex !important;
    gap: 10px !important;
    align-items: flex-start !important;
    cursor: pointer !important;
    margin-bottom: 10px !important;
    font-size: 13px !important;
    font-family: 'Inter', sans-serif !important;
    font-weight: 400 !important;
}

#payment-consent-group .consent-label span,
.payment-consent-group .consent-label span {
    flex: 1 !important;
    line-height: 1.4 !important;
    font-size: 13px !important;
    font-family: 'Inter', sans-serif !important;
}
#payment-consent-group .consent-label input[type="checkbox"],
.payment-consent-group .consent-label input[type="checkbox"] {
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
#payment-consent-group .consent-label input[type="checkbox"]::before,
.payment-consent-group .consent-label input[type="checkbox"]::before {
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
#payment-consent-group .consent-label input[type="checkbox"]:checked,
.payment-consent-group .consent-label input[type="checkbox"]:checked {
    background: #ffcc00 !important;
    border-color: #ffcc00 !important;
}
#payment-consent-group .consent-label input[type="checkbox"]:checked::before,
.payment-consent-group .consent-label input[type="checkbox"]:checked::before {
    background: #fff !important;
    transform: translateX(20px) !important;
    box-shadow: 0 0 0 3px rgba(0,0,0,0.1) !important;
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
}
.checkbox-container label span {
    flex: 1;
    line-height: 1.4;
}

/* TOGGLES: SMS Consent & Driver Notification */
.checkbox-container #smsConsent_two,
.checkbox-container #smsConsent,
.checkbox-container #driverNotificationConsent_two,
.checkbox-container #driverNotificationConsent,
.checkbox-container #termsConsent_two,
.checkbox-container #termsConsent {
    -webkit-appearance: none;
    appearance: none;
    width: 46px !important;
    height: 26px !important;
    border-radius: 999px !important;
    border: 1px solid rgba(255,255,255,0.28) !important;
    background: rgba(255,255,255,0.16) !important;
    position: relative;
    margin: 0 !important;
    margin-right: 12px !important;
    padding: 0 !important;
    flex-shrink: 0;
    cursor: pointer;
    transition: background .2s ease, border-color .2s ease;
}

.checkbox-container #smsConsent_two::before,
.checkbox-container #smsConsent::before,
.checkbox-container #driverNotificationConsent_two::before,
.checkbox-container #driverNotificationConsent::before,
.checkbox-container #termsConsent_two::before,
.checkbox-container #termsConsent::before {
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

.checkbox-container #smsConsent_two:checked,
.checkbox-container #smsConsent:checked,
.checkbox-container #driverNotificationConsent_two:checked,
.checkbox-container #driverNotificationConsent:checked,
.checkbox-container #termsConsent_two:checked,
.checkbox-container #termsConsent:checked {
    background: #ffcc00 !important;
    border-color: #ffcc00 !important;
}

.checkbox-container #smsConsent_two:checked::before,
.checkbox-container #smsConsent:checked::before,
.checkbox-container #driverNotificationConsent_two:checked::before,
.checkbox-container #driverNotificationConsent:checked::before,
.checkbox-container #termsConsent_two:checked::before,
.checkbox-container #termsConsent:checked::before {
    background: #fff;
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

/* Step navigation ï¿½ centered flex row */
.step-navigation {
    display: flex !important;
    justify-content: center;
            align-items: center;
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
    align-items: flex-start;
    justify-content: center;
            align-items: center;
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

/* Addon selection modal ï¿½ dark theme */
#addonSelectionModal .modal-content,
#infoTooltipModal .modal-content {
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
    align-items: flex-start;
    justify-content: center;
            align-items: center;
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
    align-items: flex-start;
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
    align-items: flex-start;
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
    align-items: flex-start;
    justify-content: center;
            align-items: center;
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
    max-width: 420px;
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
    align-items: flex-start;
    justify-content: center;
            align-items: center;
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
    display: flex !important;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 18px;
}
.package-category-tile {
    flex: 1 1 auto;
    min-width: 0;
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
    box-shadow: none !important;
}
.package-category-tile:hover {
    background: rgba(167,116,255,0.16) !important;
    border-color: rgba(167,116,255,0.6) !important;
    color: #fff !important;
    transform: translateY(-1px);
    filter: none !important;
}
.package-category-tile.active {
    background: linear-gradient(135deg, #a774ff 0%, #7c3aed 100%) !important;
    color: #fff !important;
    border-color: #7c3aed !important;
    box-shadow: 0 4px 14px rgba(124,58,237,0.4) !important;
}
.package-category-tile .package-category-indicator {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: rgba(255,255,255,0.15);
    display: inline-flex;
    align-items: flex-start;
    justify-content: center;
            align-items: center;
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
/* Rectangle background behind category icon (dynamic by --cat-rgb) */
.package-category-tile-icon {
    display: inline-flex;
    align-items: flex-start;
    justify-content: center;
            align-items: center;
    min-width: 36px;
    height: 32px;
    padding: 4px 8px;
    border-radius: 8px;
    background: rgba(255,255,255,0.03);
    box-shadow: none;
    transition: all .18s;
}
.package-category-tile.has-cat-color .package-category-tile-icon {
    background: rgba(var(--cat-rgb), 0.12) !important;
    border: 1px solid rgba(var(--cat-rgb), 0.18) !important;
    color: rgba(var(--cat-rgb), 1) !important;
}
.package-category-tile.has-cat-color.active .package-category-tile-icon {
    background: linear-gradient(135deg, rgba(var(--cat-rgb), 0.95) 0%, rgba(var(--cat-rgb), 0.75) 100%) !important;
    color: #fff !important;
    box-shadow: 0 4px 12px rgba(var(--cat-rgb), 0.18) !important;
}
/* Special-case: gold (255,204,0) should use dark icon color for readability */
.package-category-tile[style*="255,204,0"] .package-category-tile-icon,
.package-category-tile.has-cat-color[style*="255,204,0"] .package-category-tile-icon {
    color: #000 !important;
}
/* Category color override when --cat-rgb is set */
.package-category-tile.has-cat-color {
    background: rgba(var(--cat-rgb), 0.08) !important;
    border-color: rgba(var(--cat-rgb), 0.35) !important;
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
.package-category-group { margin-bottom: 16px; }

@media (max-width: 767px) {
    .package-category-tiles { flex-direction: column; }
    .package-category-tile { width: 100%; }
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
    align-items: flex-start;
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
    align-items: flex-start;
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
    align-items: flex-start;
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
    align-items: flex-start;
    justify-content: center;
            align-items: center;
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
    align-items: flex-start;
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
    align-items: flex-start;
}

.guest-count .guest-qty-stepper {
    display: inline-flex;
    align-items: flex-start;
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
    align-items: flex-start;
    justify-content: center;
            align-items: center;
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
    align-items: flex-start;
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
        align-items: flex-start;
        justify-content: center;
            align-items: center;
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
        align-items: flex-start;
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

    /* Mobile form layout: stack fields vertically */
    .form-row {
        flex-direction: column !important;
    }
    .form-row .form-group {
        width: 100% !important;
    }
    .guest .form-row {
        flex-direction: column !important;
        gap: 12px !important;
    }
    .guest .form-row .form-group {
        width: 100% !important;
    }
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
    position: static !important;
    top: auto;
    z-index:1000;
    background: linear-gradient(180deg, rgba(8,11,20,0.98) 0%, rgba(5,7,14,0.94) 100%);
    backdrop-filter:blur(14px);
    -webkit-backdrop-filter:blur(14px);
    border-bottom:1px solid rgba(167,116,255,.14);
    display:flex !important;
    align-items:center;
    justify-content: space-between !important;
    padding: 0 clamp(20px, 1vw, 48px);
    height:72px;
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
    align-items: flex-start;
    justify-content: center;
            align-items: center;
    gap: 24px;
    min-width: 0;
}
.cv-nav-status {
    display: inline-flex;
    align-items: flex-start;
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
    align-items: flex-start;
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
    padding: 28px 34px 32px;
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

.cv-hero-head { display:flex; justify-content:space-between; align-items:flex-start; gap:16px; }
.cv-hero-venue { display:flex; align-items:center; gap:14px; min-width:0; }
.cv-hero-venue-avatar { width:80px; height:80px; border-radius:12px; object-fit:cover; border:none; flex-shrink:0; }
.cv-hero-venue-initial { width:80px; height:80px; border-radius:12px; display:inline-flex; align-items:center; justify-content:center; font-size:28px; font-weight:900; color:#0b1020 !important; background:var(--accent); flex-shrink:0; }
.cv-hero-venue-title { font-size:24px; font-weight:700; line-height:1.2; color:#fff !important; margin:0; letter-spacing:-0.01em; display: inline-flex; align-items: center; gap: 6px; }
.cv-hero-venue-verified { display:inline-flex; align-items:center; justify-content:center; width:18px; height:18px; border-radius:50%; background:var(--accent); color:#0b1020 !important; font-size:10px; font-weight:900; }
.cv-hero-venue-meta { font-size:13px; color:rgba(255,255,255,0.62) !important; margin:3px 0 0; }
.cv-hero-rating { font-size:13px; color:rgba(255,255,255,0.78) !important; margin-top:4px; display: inline-flex; align-items: center; gap: 6px; }
.cv-hero-rating .stars { color:var(--accent) !important; letter-spacing:-1px; }
.cv-hero-badges { display:flex; gap:24px; align-items: center; flex-wrap: wrap; }
.cv-hero-badge { background:transparent; border:0; border-radius:0; padding:0; display:flex; gap:10px; align-items:flex-start; }
.cv-hero-badge i { color: var(--accent) !important; font-size: 17px; margin-top: 1px; width: 22px; height: 22px; border-radius: 50%; background: rgba(255,204,0,0.1); display: inline-flex; align-items: center; justify-content: center;
            align-items: center; font-size: 11px; }
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

.cv-hero-location-header { display: flex; justify-content: space-between; align-items: center; gap: 12px; }
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
    align-items: flex-start;
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
    align-items: flex-start;
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

.cv-hero-location-map-btn { display: none; width: 100%; padding: 16px 20px; margin-bottom: 18px; background: #111827; color: #fff; border: 1px solid rgba(255,255,255,0.12); border-radius: 12px; font-size: 16px; font-weight: 700; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 14px rgba(15, 23, 42, 0.25); }
.cv-hero-location-map-btn:hover { transform: translateY(-2px); background: #0f172a; box-shadow: 0 6px 20px rgba(15, 23, 42, 0.32); }
.cv-hero-location-map-btn i { margin-right: 8px; }

.cv-hero-location-contacts { display: flex; flex-direction: column; gap: 8px; }
.cv-hero-location-contact {
    display: inline-flex;
    align-items: flex-start;
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
    align-items: flex-start;
    justify-content: center;
            align-items: center;
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

@media (max-width: 767px) {
    .cv-hero-location-map { display: none !important; }
    .cv-hero-location-map-btn { display: block; }
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
    align-items: flex-start;
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
    align-items: flex-start;
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
    align-items: flex-start;
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
    align-items: flex-start;
    justify-content: center;
            align-items: center;
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
    align-items: flex-start;
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

/* ====== ADDON + TOOLTIP MODALS - unified package-style background ====== */
#addonSelectionModal .modal-content,
#infoTooltipModal .modal-content {
    background: linear-gradient(180deg, rgba(36,18,58,0.96), rgba(18,10,32,0.98)) !important;
    border: 1px solid rgba(167,116,255,0.4) !important;
    border-radius: 20px !important;
    box-shadow: 0 20px 60px rgba(0,0,0,0.5), 0 0 0 1px rgba(167,116,255,0.18) !important;
    color: #f4f6ff !important;
    position: relative;
    overflow: hidden;
}
#addonSelectionModal .modal-content::before,
#infoTooltipModal .modal-content::before {
    content: '';
    position: absolute;
    right: -10%; top: -10%;
    width: 60%; height: 50%;
    background: radial-gradient(ellipse at right top, rgba(167,116,255,0.18), transparent 65%);
    pointer-events: none;
    z-index: 0;
}
#addonSelectionModal .modal-content > *,
#infoTooltipModal .modal-content > * { position: relative; z-index: 1; }
#addonSelectionModal .modal-header,
#infoTooltipModal .modal-header {
    border-bottom: 1px solid rgba(167,116,255,0.18) !important;
    padding: 20px 24px !important;
}
#addonSelectionModal .modal-title,
#infoTooltipModal .modal-title {
    color: #fff !important;
    font-size: 20px !important;
    font-weight: 800 !important;
    letter-spacing: -0.01em;
}
#addonSelectionModal .modal-body,
#infoTooltipModal .modal-body { padding: 20px 24px !important; }
#addonSelectionModal .modal-footer,
#infoTooltipModal .modal-footer {
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
#addonSelectionModal .btn-secondary,
#infoTooltipModal .btn-secondary {
    background: rgba(255,255,255,0.05) !important;
    border: 1px solid rgba(255,255,255,0.18) !important;
    color: rgba(255,255,255,0.85) !important;
    border-radius: 12px !important;
    font-weight: 600 !important;
    padding: 11px 20px !important;
}
#addonSelectionModal .btn-close-white,
#addonSelectionModal .btn-close,
#infoTooltipModal .btn-close-white,
#infoTooltipModal .btn-close { filter: invert(1) brightness(1.5); }

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
    align-items: flex-start;
    justify-content: center;
            align-items: center;
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
    align-items: flex-start;
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
    align-items: flex-start;
    justify-content: center;
            align-items: center;
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
    padding: 14px 16px !important;
    font-size: 12.5px !important;
    font-weight: 700 !important;
    cursor: pointer;
    transition: all .15s;
    margin-bottom: 16px;
    display: inline-flex;
    align-items: flex-start;
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

/* Pick-up time â€” Flatpickr visual time picker (desktop) */
.checkout-section[id^="section-"] .pickup-time-wrap {
    position: relative;
    max-width: 260px;
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
    max-width: 260px !important;
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
/* Flatpickr time-only popup â€” desktop theme */
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
    align-items: flex-start;
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
    align-items: flex-start;
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
            align-items: center;
}
.guest .addon-qty-stepper.guest-qty-stepper {
    display: inline-flex;
    align-items: flex-start;
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
.guest .guest-section--total .addon-qty-stepper.guest-qty-stepper {
    padding: 4px 14px;
    background: rgba(0,0,0,0.58);
    border: 1px solid rgba(167,116,255,0.32);
    border-radius: 999px;
}
.guest .guest-section--total .label {
    width: 100%;
    text-align: center;
}
.guest .guest-section--total .counter {
    width: 100%;
    justify-content: center !important;
    margin-left: 0 !important;
}
.guest .guest-section--total .addon-qty-val.guest-qty-val {
    display: inline-flex;
    align-items: flex-start;
    justify-content: center;
            align-items: center;
    text-align: center;
    line-height: 1;
}

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
    .guest .guest-section--total {
        justify-content: center !important;
        align-items: center !important;
        text-align: center !important;
        gap: 8px !important;
    }
    .guest .guest-section--total .counter {
        width: 100%;
        justify-content: center !important;
        margin-left: 0 !important;
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
/* Bulletproof desktop layout: explicitly pin the two columns so the Order Summary
   always sits in the right column (top row), regardless of any stray grid item or
   auto-placement quirk. Scoped to desktop and to non-guest mode. */
@media (min-width: 992px) {
    #cv-checkout-layout:not(.is-guest-mode) {
        display: grid !important;
        grid-template-columns: minmax(0, 1fr) 440px !important;
        align-items: start !important;
    }
    #cv-checkout-layout:not(.is-guest-mode) > .cv-main-col {
        grid-column: 1 !important;
        grid-row: 1 !important;
        min-width: 0 !important;
    }
    #cv-checkout-layout:not(.is-guest-mode) > #cv-order-sidebar {
        grid-column: 2 !important;
        grid-row: 1 !important;
    }
}
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
    align-items: flex-start;
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
    align-items: flex-start;
    justify-content: center;
            align-items: center;
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
    align-items: flex-start;
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
    align-items: flex-start;
    justify-content: center;
            align-items: center;
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

/* Modal-only tooltip mode: disable pseudo-tooltip rendering on hover/focus */
[data-tip]:hover::after,
[data-tip]:focus-visible::after,
[data-tip]:hover::before,
[data-tip]:focus-visible::before {
    content: none !important;
    display: none !important;
    animation: none !important;
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
.cv-dstep-num { width: 32px; height: 32px; border-radius: 999px; border: 1.5px solid rgba(255,255,255,0.22); display: inline-flex; align-items: center; justify-content: center;
            align-items: center; font-size: 13px; font-weight: 800; color: rgba(255,255,255,0.85) !important; background: rgba(255,255,255,0.04); position: relative; z-index: 1; transition: all .2s; }
.cv-dstep.is-active .cv-dstep-num { background: linear-gradient(135deg, #a774ff 0%, #7c3aed 100%) !important; border-color: #7c3aed !important; color: #fff !important; box-shadow: 0 0 0 4px rgba(167,116,255,0.2), 0 4px 12px rgba(124,58,237,0.4); }
.cv-dstep.is-active { color: #c4a3ff !important; }
.cv-dstep.is-complete .cv-dstep-num { background: linear-gradient(135deg, #a774ff 0%, #5b21b6 100%) !important; border-color: #7c3aed !important; color: #fff !important; }
.cv-dstep.is-complete::before { background: linear-gradient(90deg, #a774ff, #7c3aed) !important; }

.cv-access-grid { display: flex; gap: 14px; align-items: stretch; margin-top: 4px; }
.cv-access-card[data-name="package"] { flex: 1.95 1 0; }
.cv-access-card[data-name="guest"] { flex: 0.85 1 0; }

/* Access tab selector */
.cv-access-hint {
    font-size: 10.5px;
    font-weight: 800;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: rgba(255,255,255,0.42) !important;
    margin: 18px 0 12px;
    display: flex;
    align-items: flex-start;
    gap: 10px;
}
.cv-access-hint::before,
.cv-access-hint::after {
    content: '';
    flex: 1;
    height: 1px;
    background: rgba(255,255,255,0.1);
}
.cv-access-hint .cv-access-hint-dot { display: none; }

.cv-access-card {
    flex: 1 1 0;
    position: relative;
    border-radius: 18px;
    padding: 18px 16px 18px 50px;
    display: flex;
    align-items: flex-start;
    gap: 14px;
    cursor: default;
    transition: all .35s cubic-bezier(.4,0,.2,1);
    text-align: left;
    font-family: inherit;
    color: inherit;
    overflow: hidden;
    border: 1.5px solid rgba(255,255,255,0.08);
    background: rgba(255,255,255,0.03);
    min-height: 82px;
}
.cv-access-card::before {
    content: '';
    position: absolute;
    left: 17px;
    top: 50%;
    transform: translateY(-50%);
    width: 20px;
    height: 20px;
    border-radius: 50%;
    border: 2px solid rgba(255,255,255,0.22);
    background: rgba(0,0,0,0.28);
    transition: all .3s;
    z-index: 2;
}
.cv-access-card::after {
    content: '';
    position: absolute;
    left: 22px;
    top: 50%;
    transform: translateY(-50%) scale(0);
    width: 10px;
    height: 10px;
    border-radius: 50%;
    transition: transform .25s cubic-bezier(.2,.9,.3,1.4);
    z-index: 3;
}
.cv-access-card[data-name="guest"] { border-color: rgba(16,185,129,0.32); background: rgba(16,185,129,0.10); }
.cv-access-card[data-name="guest"]::before { border-color: rgba(16,185,129,0.56); }
.cv-access-card[data-name="guest"]::after { background: radial-gradient(circle, #34d399 0%, #10b981 100%); box-shadow: 0 0 10px rgba(16,185,129,0.8); }
.cv-access-card[data-name="package"] { border-color: rgba(232,190,106,0.34); background: rgba(232,190,106,0.10); }
.cv-access-card[data-name="package"]::before { border-color: rgba(232,190,106,0.58); }
.cv-access-card[data-name="package"]::after { background: radial-gradient(circle, #fde68a 0%, #e8be6a 100%); box-shadow: 0 0 10px rgba(232,190,106,0.8); }
.cv-access-card.cv-access-tab { cursor: pointer; transition: opacity .28s ease, filter .28s ease; }
.cv-access-card.cv-access-tab:not(.is-active) { opacity: 0.90; }
.cv-access-card.cv-access-tab.is-active { opacity: 1; filter: none; }
@media (hover: hover) and (pointer: fine) {
    .cv-access-grid:hover .cv-access-card.cv-access-tab { opacity: 0.35; filter: brightness(0.65); }
    .cv-access-card.cv-access-tab:hover { opacity: 1 !important; filter: brightness(1.18) !important; }
    .cv-access-grid:hover .cv-access-card[data-name="package"].cv-access-tab { opacity: 1; filter: none; }
    .cv-access-card[data-name="package"].cv-access-tab:hover { opacity: 0.96 !important; filter: brightness(0.94) !important; }
}
.cv-access-card[data-name="package"] { isolation: isolate; --cv-package-rgb: 232,190,106; }
.cv-access-card[data-name="package"] .cv-ac-shimmer {
    position: absolute;
    inset: 0;
    z-index: 1;
    pointer-events: none;
    border-radius: inherit;
    overflow: hidden;
    mix-blend-mode: screen;
    opacity: .55;
}
.cv-access-card[data-name="package"] .cv-ac-shimmer::before {
    content: '';
    position: absolute;
    inset: -2px;
    background: linear-gradient(115deg,
        transparent 0%,
        transparent 34%,
        rgba(255,255,255,.42) 48%,
        rgba(var(--cv-package-rgb),.22) 56%,
        transparent 68%,
        transparent 100%);
    transform: translateX(-130%);
    animation: cvPackageShimmer 4s ease-in-out infinite;
}
@keyframes cvPackageShimmer {
    0%, 16% { transform: translateX(-130%); }
    44%, 100% { transform: translateX(130%); }
}
.cv-ac-ribbon {
    position: absolute;
    top: 10px;
    right: -25px;
    width: 100px;
    text-align: center;
    padding: 5px 0;
    font-size: 9px;
    font-weight: 900;
    letter-spacing: .12em;
    text-transform: uppercase;
    white-space: nowrap;
    overflow: hidden;
    line-height: 1;
    transform: rotate(45deg);
    box-shadow: 0 2px 10px rgba(0,0,0,0.6);
    pointer-events: none;
    z-index: 10;
    background: linear-gradient(90deg, #ffe66a, #ffc928 62%, #ffb300);
    color: #3a2200;
    text-shadow: 0 1px 3px rgba(0,0,0,0.55);
}
.cv-access-card[data-name="package"] .cv-ac-ribbon { filter: saturate(1.25) brightness(1.12); }
.cv-ac-icon-wrap {
    width: 46px;
    height: 46px;
    border-radius: 13px;
    flex-shrink: 0;
    position: relative;
    transition: all .35s cubic-bezier(.4,0,.2,1);
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
}
.cv-access-card[data-name="guest"] .cv-ac-icon-wrap { background: rgba(16,185,129,0.1); border-color: rgba(16,185,129,0.25); }
.cv-access-card[data-name="package"] .cv-ac-icon-wrap { background: rgba(232,190,106,0.1); border-color: rgba(232,190,106,0.25); }
.cv-access-card[data-name="package"] .cv-ac-icon-wrap,
.cv-access-card[data-name="package"] .cv-ac-body { z-index: 2; }
.cv-ac-icon-wrap i { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); transition: color .35s, font-size .35s; line-height: 1; }
.cv-access-card[data-name="guest"] .cv-ac-icon-wrap i { color: rgba(52,211,153,0.65) !important; font-size: 19px; }
.cv-access-card[data-name="package"] .cv-ac-icon-wrap i { color: rgba(232,190,106,0.65) !important; font-size: 19px; }
.cv-ac-body { min-width: 0; }
.cv-access-card strong {
    display: block;
    font-size: 13.5px;
    line-height: 1.2;
    color: rgba(255,255,255,0.65) !important;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .04em;
    transition: color .3s;
}
.cv-access-card span {
    display: block;
    font-size: 11.5px;
    line-height: 1.4;
    color: rgba(255,255,255,0.35) !important;
    margin-top: 4px;
    transition: color .3s;
}
.cv-access-card.is-active {
    flex: 1.42 1 0;
    min-height: 96px;
    padding: 20px 18px 20px 50px;
    gap: 18px;
}
.cv-access-card[data-name="guest"].is-active {
    border-color: #34d399;
    background:
        radial-gradient(ellipse at 94% 50%, rgba(52,211,153,0.2) 0%, transparent 50%),
        linear-gradient(145deg, rgba(16,185,129,0.14), rgba(4,36,20,0.22));
    box-shadow: 0 0 0 1px rgba(52,211,153,0.3), 0 8px 32px rgba(16,185,129,0.22), inset 0 1px 0 rgba(52,211,153,0.12);
}
.cv-access-card[data-name="package"].is-active {
    border-color: #e8be6a;
    background:
        radial-gradient(ellipse at 94% 50%, rgba(232,190,106,0.22) 0%, transparent 50%),
        linear-gradient(145deg, rgba(232,190,106,0.14), rgba(50,35,5,0.22));
    box-shadow: 0 0 0 1px rgba(232,190,106,0.35), 0 8px 32px rgba(232,190,106,0.2), inset 0 1px 0 rgba(232,190,106,0.15);
}
.cv-access-card[data-name="guest"].is-active::before { border-color: #34d399; background: rgba(16,185,129,0.2); transform: translateY(-50%) scale(1.05); }
.cv-access-card[data-name="package"].is-active::before { border-color: #e8be6a; background: rgba(232,190,106,0.2); transform: translateY(-50%) scale(1.05); }
.cv-access-card.is-active::after { transform: translateY(-50%) scale(1); }
.cv-access-card[data-name="guest"].is-active .cv-ac-icon-wrap { background: rgba(16,185,129,0.22); border-color: rgba(52,211,153,0.55); width: 60px; height: 60px; border-radius: 15px; box-shadow: 0 0 22px rgba(16,185,129,0.45); }
.cv-access-card[data-name="package"].is-active .cv-ac-icon-wrap { background: rgba(232,190,106,0.22); border-color: rgba(232,190,106,0.6); width: 60px; height: 60px; border-radius: 15px; box-shadow: 0 0 22px rgba(232,190,106,0.45); }
.cv-access-card[data-name="guest"].is-active .cv-ac-icon-wrap i { color: #34d399 !important; font-size: 24px; }
.cv-access-card[data-name="package"].is-active .cv-ac-icon-wrap i { color: #e8be6a !important; font-size: 24px; }
.cv-access-card.is-active strong { color: #fff !important; font-size: 15px; }
.cv-access-card.is-active span { color: rgba(255,255,255,0.58) !important; }
/* Active card â€” fill space */
.cv-access-card.is-active .cv-ac-body { flex: 1; position: relative; }
.cv-access-card.is-active .cv-ac-body strong,
.cv-access-card.is-active .cv-ac-body > span { position: relative; z-index: 1; }
.cv-access-card[data-name="guest"].is-active .cv-ac-body::before {
    font-family: 'Font Awesome 6 Free'; font-weight: 900; content: '\f0c0';
    position: absolute; right: -6px; top: 50%; transform: translateY(-50%);
    font-size: 66px; color: rgba(52,211,153,0.06); pointer-events: none; line-height: 1; z-index: 0;
}
.cv-access-card[data-name="package"].is-active .cv-ac-body::before {
    font-family: 'Font Awesome 6 Free'; font-weight: 900; content: '\f005';
    position: absolute; right: -6px; top: 50%; transform: translateY(-50%);
    font-size: 66px; color: rgba(232,190,106,0.07); pointer-events: none; line-height: 1; z-index: 0;
}
.cv-access-card[data-name="package"] .cv-ac-body { flex: 1; position: relative; }
.cv-access-card[data-name="package"] .cv-ac-body strong,
.cv-access-card[data-name="package"] .cv-ac-body > span { position: relative; z-index: 1; }
.cv-access-card[data-name="package"] .cv-ac-body::before {
    font-family: 'Font Awesome 6 Free'; font-weight: 900; content: '\f005';
    position: absolute; right: -6px; top: 50%; transform: translateY(-50%);
    font-size: 66px; color: rgba(232,190,106,0.07); pointer-events: none; line-height: 1; z-index: 0;
}
.cv-access-card[data-name="package"] .cv-ac-body::after {
    content: ''; display: block; height: 2px; width: 26px;
    border-radius: 2px; margin-top: 10px; position: relative; z-index: 1;
    background: linear-gradient(90deg, rgba(232,190,106,0.75), rgba(232,190,106,0));
}
.cv-access-card[data-name="package"] {
    filter: brightness(1.12) saturate(1.08);
}
.cv-access-card[data-name="package"]::before { border-color: #ffd54d; background: rgba(255,214,102,0.26); }
.cv-access-card[data-name="package"]::after { background: radial-gradient(circle, #fff2b3 0%, #ffd84d 58%, #ffbf00 100%); box-shadow: 0 0 14px rgba(255,201,40,0.95); }
.cv-access-card[data-name="package"] .cv-ac-icon-wrap { background: rgba(255,214,102,0.28); border-color: rgba(255,214,102,0.68); box-shadow: 0 0 20px rgba(255,201,40,0.45); }
.cv-access-card[data-name="package"] .cv-ac-icon-wrap i { color: #fff5b8 !important; font-size: 20px; }
.cv-access-card[data-name="package"] strong { color: #fff !important; }
.cv-access-card[data-name="package"] span { color: rgba(255,255,255,0.74) !important; }
.cv-access-card[data-name="package"] .cv-ac-body::before { color: rgba(255,214,102,0.16); }
.cv-access-card[data-name="package"] .cv-ac-body::after { background: linear-gradient(90deg, rgba(255,214,102,0.95), rgba(255,214,102,0)); }
.cv-access-card[data-name="package"] .cv-ac-shimmer { opacity: .92; }
.cv-access-card[data-name="package"] .cv-ac-shimmer::before { background: linear-gradient(115deg, transparent 0%, transparent 30%, rgba(255,255,255,.84) 47%, rgba(255,213,77,.42) 56%, transparent 70%, transparent 100%); }
.cv-access-card[data-name="package"] .cv-ac-ribbon { filter: saturate(1.45) brightness(1.28); }
.cv-access-card.is-active .cv-ac-body::after {
    content: ''; display: block; height: 2px; width: 26px;
    border-radius: 2px; margin-top: 10px; position: relative; z-index: 1;
}
.cv-access-card[data-name="guest"].is-active .cv-ac-body::after { background: linear-gradient(90deg, rgba(52,211,153,0.75), rgba(52,211,153,0)); }
.cv-access-card[data-name="package"].is-active .cv-ac-body::after { background: linear-gradient(90deg, rgba(232,190,106,0.75), rgba(232,190,106,0)); }

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
    align-items: flex-start;
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

/* Tier-specific: make badge text dark on gold tier (VIP) */
.vip-card.cv-exact-card.cv-tier-1 .cv-popular-pill {
    color: #000 !important;
    border-color: rgba(0,0,0,0.12) !important;
}
.vip-card.cv-exact-card.cv-tier-1 .cv-popular-pill::before {
    color: #000 !important;
}

/* Use the tier accent color to tint native checkboxes */
input[type="checkbox"],
.package-checkbox input[type="checkbox"],
.vip-card input[type="checkbox"] {
    accent-color: var(--tier-accent, #ffcc00) !important;
}
.vip-card.cv-exact-card .vip-card-main { display: flex; flex-direction: column; justify-content: flex-start; gap: 6px; min-width: 0; }
.cv-pkg-title-row { display: flex; align-items: center; gap: 10px; }
.cv-pkg-title-icon { font-size: 22px; flex-shrink: 0; color: var(--tier-accent, #fff) !important; }
.cv-pkg-tooltip-trigger { width: 20px; height: 20px; border: 1px solid rgba(255,255,255,0.42); border-radius: 999px; background: rgba(255,255,255,0.08); color: #fff; font-size: 12px; font-weight: 700; line-height: 1; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; padding: 0; transition: background-color .15s ease, border-color .15s ease; }
.cv-pkg-tooltip-trigger:hover { background: rgba(255,255,255,0.18); border-color: rgba(255,255,255,0.65); }
.cv-pkg-tooltip-trigger:focus-visible { outline: 2px solid rgba(255,255,255,0.7); outline-offset: 2px; }
.cv-pkg-title { font-size: 26px; font-weight: 700; line-height: 1.2; color: var(--tier-accent, #fff) !important; letter-spacing: -0.01em; }
.cv-pkg-sub { font-size: 12.5px; color: rgba(255,255,255,0.62) !important; display: inline-flex; align-items: center; gap: 6px; }
.cv-pkg-sub i { font-size: 12px; opacity: .7; }
.cv-pkg-desc { font-size: 13px; color: rgba(255,255,255,0.62) !important; line-height: 1.5; margin: 0; }
.cv-pkg-features { display: flex; flex-wrap: wrap; gap: 14px; margin-top: 6px; }
.cv-pkg-feature { font-size: 11.5px; color: rgba(255,255,255,0.65) !important; display: inline-flex; align-items: center; gap: 5px; }
.cv-pkg-feature i { color: var(--tier-accent, rgba(255,255,255,0.76)) !important; font-size: 11px; opacity: .9; }
.vip-card.cv-exact-card .vip-card-side { display: flex; flex-direction: column; justify-content: space-between; align-items: stretch; gap: 6px; grid-template-columns: none !important; flex: initial; text-align: right; }
.vip-card.cv-exact-card .vip-price-tag { font-size: 30px !important; text-align: right; padding-top: 0; min-width: 0; color: #fff !important; font-weight: 700; line-height: 1.1; }
.cv-price-meta { text-align: right; font-size: 12px; color: rgba(255,255,255,0.58) !important; margin-top: 2px; }
.vip-card.cv-exact-card .package_number_of_guestss { width: 100% !important; min-width: 0; margin-top: 8px; }
.vip-card.cv-exact-card .vip-btn { width: 100%; border-radius: 10px; font-weight: 800; background: var(--tier-accent, var(--accent)) !important; color: var(--tier-btn-color, #000) !important; padding: 11px 12px !important; font-size: 14px !important; display: inline-flex !important; align-items: center; justify-content: center;
            align-items: center; gap: 6px; }
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
.cv-freeride-callout .cv-freeride-icon { width: 40px; height: 40px; border-radius: 10px; background: rgba(255,204,0,0.1); display: inline-flex; align-items: center; justify-content: center;
            align-items: center; flex-shrink: 0; color: var(--accent) !important; font-size: 17px; }
.cv-freeride-callout strong { display: block; font-size: 14px; color: var(--accent) !important; font-weight: 700; margin-bottom: 2px; }
.cv-freeride-callout span { display: block; font-size: 12px; color: rgba(255,255,255,0.62) !important; line-height: 1.5; }

/* Need Help section */
.cv-need-help { display: flex; align-items: center; justify-content: space-between; padding: 18px 20px; margin-top: 16px; border-top: 1px solid rgba(255,255,255,0.08); gap: 16px; flex-wrap: wrap; }
.cv-need-help-title strong { display: block; font-size: 14px; color: rgba(255,255,255,0.92) !important; font-weight: 700; }
.cv-need-help-title span { display: block; font-size: 12px; color: rgba(255,255,255,0.5) !important; margin-top: 2px; }
.cv-need-help-actions { display: flex; gap: 18px; flex-wrap: wrap; }
.cv-need-help-action { display: flex; align-items: center; gap: 10px; color: rgba(255,255,255,0.85) !important; text-decoration: none !important; font-size: 13px; }
.cv-need-help-action i { color: var(--accent) !important; font-size: 15px; width: 28px; height: 28px; border-radius: 999px; background: rgba(255,204,0,0.1); display: inline-flex; align-items: center; justify-content: center;
            align-items: center; }
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
.cv-sidebar-venue-image-placeholder { width: 100%; height: 100px; border-radius: 12px; background: rgba(255,255,255,0.04); display: flex; align-items: center; justify-content: center;
            align-items: center; color: rgba(255,255,255,0.3); font-size: 13px; margin-bottom: 12px; }
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
    align-items: flex-start;
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
    align-items: flex-start;
    justify-content: center;
            align-items: center;
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
    align-items: flex-start;
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
    align-items: flex-start;
    justify-content: center;
            align-items: center;
    line-height: 1;
}
#cv-order-sidebar .dynamic-price.col-md-6 #applyPromoBtn:hover,
#cv-order-sidebar .dynamic-price.col-md-6 .vip-btn-submit:hover { background: rgba(255,255,255,0.06) !important; }

/* Promo discount line (inserted by JS) */
#cv-order-sidebar .pricing-shell .default-promo-discount {
    font-size: 14px !important;
    display: flex !important;
    align-items: flex-start;
    padding: 6px 0;
    color: #22c55e !important;
    font-weight: 600 !important;
}
#cv-order-sidebar .pricing-shell .default-promo-discount span { margin-left: auto; }

@media (min-width: 992px) {
    .cv-main-col #cart-section,
    .cv-main-col .pricing-shell,
    .cv-main-col #shareLinkContainer { display: none !important; }
}

/* Shareable link styled for the order-summary sidebar (it's moved there by JS) */
#cv-order-sidebar #shareLinkContainer { margin-top:0; margin-bottom:10px; }
#cv-order-sidebar #generateShareLink { font-size:12px; padding:6px 12px; border-radius:8px; background:rgba(255,255,255,.07); border:1px solid rgba(255,255,255,.14); color:rgba(255,255,255,.7) !important; cursor:pointer; transition:all .15s; }
#cv-order-sidebar #generateShareLink:hover { background:rgba(255,255,255,.11); }

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
    .vip-card.cv-exact-card .vip-card-main { display: flex; flex-direction: column; justify-content: flex-start; }
    .vip-card.cv-exact-card .cv-pkg-title-row { margin-top: 0; }
    .vip-card.cv-exact-card .cv-pkg-title { font-size: 22px !important; }
    .vip-card.cv-exact-card .cv-pkg-desc { font-size: 13px !important; line-height: 1.5; }
    .vip-card.cv-exact-card .cv-pkg-features { display: grid; grid-template-columns: repeat(4, 1fr); gap: 6px 10px; margin-top: 40px !important; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.1); flex-wrap: wrap; }
    .vip-card.cv-exact-card .cv-pkg-feature { flex: 0 0 auto; font-size: 10.5px !important; flex-direction: column; align-items: center; gap: 3px; text-align: center; }
    .vip-card.cv-exact-card .cv-pkg-feature i { font-size: 15px !important; margin-bottom: 1px; }
    .vip-card.cv-exact-card .vip-card-side {
        display: grid !important;
        grid-template-columns: 1fr auto;
        grid-template-areas:
            "price guests"
            "meta button";
        gap: 8px 12px;
        align-items: flex-start;
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
    .cv-access-grid { flex-direction: column; }
    .cv-access-card.is-active { flex: none; }
}

@media (max-width: 767px) {
    .cv-top-nav { padding: 0 14px; height: 60px; }
    .cv-nav-logo-img { height: 32px; max-width: 130px; }
    .cv-nav-back { display: flex !important; padding: 7px 12px !important; font-size: 12px !important; gap: 6px !important; }
    .cv-nav-back span { display: inline; }
    .cv-hamburger { display: none !important; }
    .mobile-top-actions { display: none !important; }
    .aff-hero.cv-venue-header .aff-hero-badges { order: 3; width: 100%; margin-top: 8px; }
}
@media (max-width: 420px) {
    .cv-nav-back { padding: 6px 10px !important; font-size: 11.5px !important; }
    .cv-nav-back i { font-size: 10px; }
    .cv-nav-logo-img { height: 80px; max-width: 110px; }
}

@media (min-width: 992px) {
    .aff-hero.cv-venue-header { display: none; }
    .hero-gallery-grid { display: none !important; }
}

/* Embed-only checkout mode: keep forms and purchase flow, hide page chrome. */
body.embed-checkout-mode .cv-top-nav,
body.embed-checkout-mode .mobile-top-actions,
body.embed-checkout-mode > header,
body.embed-checkout-mode .cv-events-shell,
body.embed-checkout-mode .aff-footer {
    display: none !important;
}
body.embed-checkout-mode main {
    padding-top: 0;
}
body.embed-checkout-mode main .container.mt-4 {
    margin: 0 !important;
    max-width: none !important;
    padding-left: 0 !important;
    padding-right: 0 !important;
    width: 100% !important;
}
body.embed-checkout-mode .cv-checkout-body {
    margin-top: 0;
}
body.embed-checkout-mode .iframe-date-card {
    margin-bottom: 14px;
}
body.embed-checkout-mode .iframe-date-card label {
    color: #fff !important;
}
body.embed-checkout-mode {
    overflow-x: hidden;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
    overscroll-behavior-y: auto;
    touch-action: pan-y;
}
body.embed-checkout-mode main,
body.embed-checkout-mode .cv-checkout-body {
    touch-action: pan-y;
}
body.embed-checkout-mode main .container.mt-4 {
    padding-left: clamp(16px, 6vw, 100px) !important;
    padding-right: clamp(16px, 6vw, 100px) !important;
    box-sizing: border-box;
}
@media (max-width: 991px) {
    body.embed-checkout-mode main {
        padding-top: 0;
    }
    body.embed-checkout-mode main .container.mt-4 {
        padding-left: 16px !important;
        padding-right: 16px !important;
    }
}

        /* Scale down reCAPTCHA badge */
        .grecaptcha-badge {
            z-index: 9999 !important;
            bottom: 10px !important;
            right: 10px !important;
            position: fixed !important;
            transform: scale(0.5) !important;
            transform-origin: bottom right !important;
        }
        .guest .checkbox-container .consent-label {
            align-items: center;
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
        <!-- reCAPTCHA v3 Script -->
        @if(config('services.recaptcha.site_key') && config('services.recaptcha.site_key') !== 'YOUR_RECAPTCHA_SITE_KEY_HERE')
        <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
        <script>
            window.executeRecaptcha = function(action = 'submit') {
                return new Promise((resolve) => {
                    if (!window.grecaptcha) {
                        resolve(null);
                        return;
                    }
                    grecaptcha.ready(function() {
                        grecaptcha.execute('{{ config('services.recaptcha.site_key') }}', {action: action})
                            .then(function(token) {
                                resolve(token);
                            })
                            .catch(function() {
                                resolve(null);
                            });
                    });
                });
            };
        </script>
        @endif
    </head>

    <body class="{{ !empty($isIframeCheckout) ? 'embed-checkout-mode' : '' }}">
        <div class="background-glow"></div>

        {{-- New CartVIP Navbar --}}
        <nav class="cv-top-nav">
            <a href="https://cartvip.com" target="_blank" class="cv-nav-brand">
                <img src="{{ asset('images/logo.png') }}" alt="CartVIP" class="cv-nav-logo-img">
            </a>
            @if ($data->back_link)
            <a href="{{ $data->back_link }}" class="cv-nav-back">
                <i class="fas fa-arrow-left"></i> {{ $data->back_text ?: 'Back to Home' }}
            </a>
            @endif
            <button class="cv-hamburger" id="cv-hamburger" aria-label="Open menu">
                <span></span><span></span><span></span>
            </button>
        </nav>

        {{-- Duplicate venue header removed - club details are shown in the hero section --}}

        @if ($data->back_link)
            <div class="mobile-top-actions d-md-none">
                <a href="{{ $data->back_link }}" class="mobile-back-home-btn">
                    <i class="fas fa-arrow-left"></i>
                    <span>{{ $data->back_text ?: 'Back To Home' }}</span>
                </a>
            </div>
        @endif

        <header>
            <div class="container py-1">
                @session('success')
                    <div class="alert alert-success" role="alert">Purchase Successfull!</div>
                @endsession

                @session('error')
                    <div class="alert alert-danger" role="alert">{{ $value }}</div>
                @endsession

                @php
                    $eventHeroImage = !empty($event->image ?? null) ? asset('uploads/' . $event->image) : ($data->logo ? asset('uploads/' . $data->logo) : asset('images/logo.png'));
                @endphp
                <section class="cv-hero-stage" style="background-image:url('{{ $eventHeroImage }}');">
                    @php
                        $eventStartRaw = $event->start_date ?? $event->date;
                        $eventEndRaw = $event->end_date ?? $eventStartRaw;
                        $eventStart = $eventStartRaw ? \Carbon\Carbon::parse($eventStartRaw) : null;
                        $eventEnd = $eventEndRaw ? \Carbon\Carbon::parse($eventEndRaw) : null;
                        $eventDateShort = $eventStart
                            ? ($eventEnd && !$eventEnd->isSameDay($eventStart)
                                ? $eventStart->format('l, F d') . ' - ' . $eventEnd->format('l, F d')
                                : $eventStart->format('l, F d'))
                            : '';
                        $eventDateLong = $eventStart
                            ? ($eventEnd && !$eventEnd->isSameDay($eventStart)
                                ? $eventStart->format('l, F d, Y') . ' - ' . $eventEnd->format('l, F d, Y')
                                : $eventStart->format('l, F d, Y'))
                            : '';
                        $eventDateOptions = [];
                        $specificEventDates = is_array($event->event_dates ?? null) ? array_values(array_filter($event->event_dates)) : [];
                        if (!empty($specificEventDates)) {
                            // Only the specific dates chosen on the event create/edit page (not the whole range)
                            foreach ($specificEventDates as $specificDate) {
                                try {
                                    $sd = \Carbon\Carbon::parse($specificDate)->startOfDay();
                                } catch (\Throwable $e) {
                                    continue;
                                }
                                $eventDateOptions[] = [
                                    'value' => $sd->format('Y-m-d'),
                                    'label' => $sd->format('l, F d, Y'),
                                ];
                            }
                        } elseif ($eventStart) {
                            // Legacy fallback: events without specific dates use the start..end range
                            $dateCursor = $eventStart->copy()->startOfDay();
                            $dateEnd = ($eventEnd ?: $eventStart)->copy()->startOfDay();
                            while ($dateCursor->lte($dateEnd)) {
                                $eventDateOptions[] = [
                                    'value' => $dateCursor->format('Y-m-d'),
                                    'label' => $dateCursor->format('l, F d, Y'),
                                ];
                                $dateCursor->addDay();
                            }
                        }

                        // Default to the nearest valid event day: start day if upcoming, today if event is in progress.
                        $today = \Carbon\Carbon::now('America/Los_Angeles')->startOfDay();
                        if ($eventStart) {
                            $rangeStart = $eventStart->copy()->startOfDay();
                            $rangeEnd = ($eventEnd ?: $eventStart)->copy()->startOfDay();

                            if ($today->lt($rangeStart)) {
                                $eventCheckoutDateValue = $rangeStart->format('Y-m-d');
                            } elseif ($today->gt($rangeEnd)) {
                                $eventCheckoutDateValue = $rangeEnd->format('Y-m-d');
                            } else {
                                $eventCheckoutDateValue = $today->format('Y-m-d');
                            }
                        } else {
                            $eventCheckoutDateValue = $today->format('Y-m-d');
                        }
                        $packagesPageUrl = route('index', $data->slug);
                        if (request()->filled('aff')) {
                            $packagesPageUrl .= '?aff=' . urlencode((string) request()->input('aff'));
                        }
                        if (!empty($isIframeCheckout)) {
                            $packagesPageUrl .= (strpos($packagesPageUrl, '?') !== false ? '&' : '?') . 'embed=1';
                        }
                    @endphp
                    <div class="cv-hero-inner">
                        <div class="cv-hero-head">
                            <div class="cv-hero-venue">
                                @if ($data->logo)
                                    <img src="{{ asset('uploads/' . $data->logo) }}" alt="{{ $data->name }}" class="cv-hero-venue-avatar">
                                @else
                                    <span class="cv-hero-venue-initial">{{ strtoupper(substr($data->name, 0, 1)) }}</span>
                                @endif
                                <div>
                                    <p class="cv-hero-venue-title">{{ $data->name }}<span class="cv-hero-venue-verified" title="Verified Venue">&check;</span></p>
                                    <p class="cv-hero-venue-meta">{{ $data->location }}</p>
                                </div>
                            </div>
                            <div class="cv-hero-badges">
                                <div class="cv-hero-badge">
                                    <i class="fas fa-clock"></i>
                                    <div>
                                        <span class="cv-hero-badge-label">{{ $data->hero_badge_1_label ?: 'Open Daily' }}</span>
                                        <span class="cv-hero-badge-sub">{{ $data->hero_badge_1_sub ?: '6PM - 7AM' }}</span>
                                    </div>
                                </div>
                                <div class="cv-hero-badge">
                                    <i class="fas fa-award"></i>
                                    <div>
                                        <span class="cv-hero-badge-label">{{ $data->hero_badge_2_label ?: 'Top Rated Club' }}</span>
                                        <span class="cv-hero-badge-sub">{{ $data->hero_badge_2_sub ?: '#1 in Las Vegas' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="cv-hero-bottom">
                            <div class="cv-hero-content">
                                <div class="aff-kicker">Venue Checkout</div>
                                @php
                                    $heroTitle = $event->hero_title ?: $event->name;
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
                                <p class="cv-hero-subtitle">{{ $event->hero_subtitle ?: ($eventDateShort . ($event->time ? ' - ' . $event->time : '')) }}</p>
                                @if(!empty($event->time))
                                    <div class="aff-display-copy" style="margin-bottom:10px;">
                                        <i class="fas fa-clock me-1"></i>{{ $event->time }}
                                    </div>
                                @endif

                                <div class="hero-date-card">
                                    <label>Choose Your Reservation Date</label>
                                    <div class="date-input-wrapper">
                                        <select id="package_use_date" style="width: 100%;" required aria-required="true" aria-describedby="package_use_date_error">
                                            <option value="" selected>Select Date</option>
                                            @foreach($eventDateOptions as $dateOption)
                                                <option value="{{ $dateOption['value'] }}">
                                                    {{ $dateOption['label'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <span class="custom-calendar-icon" style="display:none;"></span>
                                    </div>
                                    <small id="package_use_date_error" class="reservation-date-error" style="display:none;">Please select a reservation date.</small>
                                </div>
                            </div>

                            <aside class="cv-hero-location">
                                <div class="cv-hero-location-header">
                                    <div class="cv-hero-location-titles">
                                        <div class="cv-hero-location-label">Find Us</div>
                                        <div class="cv-hero-location-name">{{ $data->name }}</div>
                                        <div class="cv-hero-location-addr">{{ $data->location }}</div>
                                    </div>
                                    {{-- <span class="cv-hero-location-badge"><i class="fas fa-map-marker-alt"></i>VIP Venue</span> --}}
                                </div>
                                <button type="button" class="cv-hero-location-map-btn" data-location="{{ urlencode($data->location) }}">
                                    <i class="fas fa-map-location-dot"></i>Open in Map
                                </button>
                                <div class="cv-hero-location-map">
                                    <iframe src="https://www.google.com/maps?q={{ urlencode($data->location) }}&output=embed" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                                </div>
                                <div class="cv-hero-location-contacts">
                                    @if($data->phone)
                                        <a href="tel:{{ $data->phone }}" class="cv-hero-location-contact"><i class="fas fa-phone"></i><span>{{ $data->phone }}</span></a>
                                    @endif
                                    @if($data->email)
                                        <a href="mailto:{{ $data->email }}" class="cv-hero-location-contact"><i class="fas fa-envelope"></i><span>{{ $data->email }}</span></a>
                                    @endif
                                </div>
                            </aside>
                        </div>
                    </div>
                </section>

                @if(!empty($event->gallery_images))
                    <div class="hero-gallery-grid">
                        @foreach((array) $event->gallery_images as $galleryImage)
                            <button type="button" class="hero-gallery-item js-checkout-gallery-trigger" data-gallery-src="{{ asset('uploads/' . $galleryImage) }}" data-gallery-alt="Gallery image {{ $loop->iteration }}">
                                <img src="{{ asset('uploads/' . $galleryImage) }}" alt="Gallery image {{ $loop->iteration }}">
                            </button>
                        @endforeach
                    </div>
                @endif

                <section class="aff-story">
                    <h2>{{ $data->description_label ?? 'Description' }}</h2>
                    <div class="story-copy-block is-collapsed" data-mobile-collapsible>
                        <div class="story-copy story-copy-collapsible">{{ $event->description }}</div>
                        <button type="button" class="story-copy-toggle" aria-expanded="false">See more</button>
                    </div>
                    @if ($event->secondary_description)
                        <div class="story-divider"></div>
                        <div class="story-copy-block is-collapsed" data-mobile-collapsible>
                            <div class="story-copy story-copy-collapsible">{{ $event->secondary_description }}</div>
                            <button type="button" class="story-copy-toggle" aria-expanded="false">See more</button>
                        </div>
                    @endif
                </section>
            </div>
        </header>
        <main>
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
                        @if ($data->reservation == 1)
                        <style>
                            #cv-checkout-steps-res { display: none; }
                            .cv-desktop-steps-res { grid-template-columns: repeat(3, minmax(0, 1fr)) !important; }
                            .cv-checkout-body.is-guest-mode #cv-checkout-steps { display: none !important; }
                            .cv-checkout-body.is-guest-mode #cv-checkout-steps-res { display: grid !important; }
                        </style>
                        <div class="cv-desktop-steps cv-desktop-steps-res" id="cv-checkout-steps-res">
                            <div class="cv-dstep is-active" id="cv-rstep-1" data-step="1"><span class="cv-dstep-num">1</span><span>Choose Date</span></div>
                            <div class="cv-dstep" id="cv-rstep-2" data-step="2"><span class="cv-dstep-num">2</span><span>Your Details</span></div>
                            <div class="cv-dstep" id="cv-rstep-3" data-step="3"><span class="cv-dstep-num">3</span><span>Submit</span></div>
                        </div>
                        @endif

                        @if ($data->reservation == 1)
                            <div class="cv-access-hint">Choose one to continue<span class="cv-access-hint-dot"></span></div>
                        @endif
                        @php
                            $cvGuestHex = ltrim($data->guest_tab_color ?? '#34d399', '#');
                            $cvPkgHex   = ltrim($data->package_tab_color ?? '#e8be6a', '#');
                            [$cvGr, $cvGg, $cvGb] = sscanf($cvGuestHex, '%02x%02x%02x');
                            [$cvPr, $cvPg, $cvPb] = sscanf($cvPkgHex, '%02x%02x%02x');
                            $cvGRgb = "$cvGr,$cvGg,$cvGb";
                            $cvPRgb = "$cvPr,$cvPg,$cvPb";
                        @endphp
                        <style>
.cv-access-card[data-name="guest"] { border-color: rgba({{ $cvGRgb }},0.34); background: rgba({{ $cvGRgb }},0.10); }
.cv-access-card[data-name="guest"]::before { border-color: rgba({{ $cvGRgb }},0.58); }
.cv-access-card[data-name="guest"]::after { background: radial-gradient(circle, #{{ $cvGuestHex }} 0%, rgba({{ $cvGRgb }},0.8) 100%); box-shadow: 0 0 10px rgba({{ $cvGRgb }},0.8); }
.cv-access-card[data-name="guest"] .cv-ac-icon-wrap { background: rgba({{ $cvGRgb }},0.18); border-color: rgba({{ $cvGRgb }},0.4); }
.cv-access-card[data-name="guest"] .cv-ac-icon-wrap i { color: rgba({{ $cvGRgb }},0.85) !important; }
.cv-access-card[data-name="guest"].is-active { border-color: #{{ $cvGuestHex }}; background: radial-gradient(ellipse at 94% 50%, rgba({{ $cvGRgb }},0.2) 0%, transparent 50%), linear-gradient(145deg, rgba({{ $cvGRgb }},0.14), rgba(4,36,20,0.22)); box-shadow: 0 0 0 1px rgba({{ $cvGRgb }},0.3), 0 8px 32px rgba({{ $cvGRgb }},0.22), inset 0 1px 0 rgba({{ $cvGRgb }},0.12); }
.cv-access-card[data-name="guest"].is-active::before { border-color: #{{ $cvGuestHex }}; background: rgba({{ $cvGRgb }},0.2); }
.cv-access-card[data-name="guest"].is-active::after { background: radial-gradient(circle, #fff300 0%, rgba({{ $cvGRgb }},0.8) 100%); }
.cv-access-card[data-name="guest"].is-active .cv-ac-icon-wrap { background: rgba({{ $cvGRgb }},0.22); border-color: rgba({{ $cvGRgb }},0.55); box-shadow: 0 0 22px rgba({{ $cvGRgb }},0.45); }
.cv-access-card[data-name="guest"].is-active .cv-ac-icon-wrap i { color: #{{ $cvGuestHex }} !important; }
.cv-access-card[data-name="guest"].is-active .cv-ac-body::before { color: rgba({{ $cvGRgb }},0.06); }
.cv-access-card[data-name="guest"].is-active .cv-ac-body::after { background: linear-gradient(90deg, rgba({{ $cvGRgb }},0.75), rgba({{ $cvGRgb }},0)); }
.cv-access-card[data-name="package"] { border-color: rgba({{ $cvPRgb }},0.36); background: rgba({{ $cvPRgb }},0.11); }
.cv-access-card[data-name="package"]::before { border-color: rgba({{ $cvPRgb }},0.6); }
.cv-access-card[data-name="package"]::after { background: radial-gradient(circle, #{{ $cvPkgHex }} 0%, rgba({{ $cvPRgb }},0.8) 100%); box-shadow: 0 0 10px rgba({{ $cvPRgb }},0.8); }
.cv-access-card[data-name="package"] .cv-ac-icon-wrap { background: rgba({{ $cvPRgb }},0.18); border-color: rgba({{ $cvPRgb }},0.42); }
.cv-access-card[data-name="package"] .cv-ac-icon-wrap i { color: rgba({{ $cvPRgb }},0.88) !important; }
.cv-access-card[data-name="package"].is-active { border-color: #{{ $cvPkgHex }}; background: radial-gradient(ellipse at 94% 50%, rgba({{ $cvPRgb }},0.22) 0%, transparent 50%), linear-gradient(145deg, rgba({{ $cvPRgb }},0.14), rgba(50,35,5,0.22)); box-shadow: 0 0 0 1px rgba({{ $cvPRgb }},0.35), 0 8px 32px rgba({{ $cvPRgb }},0.2), inset 0 1px 0 rgba({{ $cvPRgb }},0.15); }
.cv-access-card[data-name="package"].is-active::before { border-color: #{{ $cvPkgHex }}; background: rgba({{ $cvPRgb }},0.2); }
.cv-access-card[data-name="package"].is-active::after { background: radial-gradient(circle, #fff300 0%, rgba({{ $cvPRgb }},0.8) 100%); transform: translateY(-50%) scale(1); }
.cv-access-card[data-name="package"].is-active .cv-ac-icon-wrap { background: rgba({{ $cvPRgb }},0.22); border-color: rgba({{ $cvPRgb }},0.6); box-shadow: 0 0 22px rgba({{ $cvPRgb }},0.45); }
.cv-access-card[data-name="package"].is-active .cv-ac-icon-wrap i { color: #{{ $cvPkgHex }} !important; }
.cv-access-card[data-name="package"].is-active .cv-ac-body::before { color: rgba({{ $cvPRgb }},0.07); }
.cv-access-card[data-name="package"].is-active .cv-ac-body::after { background: linear-gradient(90deg, rgba({{ $cvPRgb }},0.75), rgba({{ $cvPRgb }},0)); }
.cv-access-card[data-name="guest"].cv-access-tab:hover .cv-ac-icon-wrap { background: rgba({{ $cvGRgb }},0.22); border-color: rgba({{ $cvGRgb }},0.55); box-shadow: 0 0 22px rgba({{ $cvGRgb }},0.45); }
.cv-access-card[data-name="guest"].cv-access-tab:hover .cv-ac-icon-wrap i { color: #{{ $cvGuestHex }} !important; }
.cv-access-card[data-name="package"].cv-access-tab:hover .cv-ac-icon-wrap { background: rgba({{ $cvPRgb }},0.22); border-color: rgba({{ $cvPRgb }},0.6); box-shadow: 0 0 22px rgba({{ $cvPRgb }},0.45); }
.cv-access-card[data-name="package"].cv-access-tab:hover .cv-ac-icon-wrap i { color: #{{ $cvPkgHex }} !important; }
.cv-access-card[data-name="package"] { border-color: rgba({{ $cvPRgb }},0.44); background: radial-gradient(ellipse at 94% 50%, rgba({{ $cvPRgb }},0.15) 0%, transparent 54%), linear-gradient(145deg, rgba({{ $cvPRgb }},0.1), rgba(50,35,5,0.18)); box-shadow: 0 0 0 1px rgba({{ $cvPRgb }},0.24), 0 6px 22px rgba({{ $cvPRgb }},0.14), inset 0 1px 0 rgba({{ $cvPRgb }},0.1); }
.cv-access-card[data-name="package"]::before { border-color: rgba({{ $cvPRgb }},0.62); background: rgba({{ $cvPRgb }},0.14); transform: translateY(-50%) scale(1); }
.cv-access-card[data-name="package"]::after { transform: translateY(-50%) scale(0); }
.cv-access-card[data-name="package"] .cv-ac-icon-wrap { background: rgba({{ $cvPRgb }},0.18); border-color: rgba({{ $cvPRgb }},0.42); box-shadow: 0 0 16px rgba({{ $cvPRgb }},0.3); }
.cv-access-card[data-name="package"] .cv-ac-icon-wrap i { color: rgba({{ $cvPRgb }},0.9) !important; font-size: 20px; }
.cv-access-card[data-name="package"] strong { color: rgba(255,255,255,0.86) !important; font-size: 14px; }
.cv-access-card[data-name="package"] span { color: rgba(255,255,255,0.52) !important; }
.cv-access-card[data-name="package"] .cv-ac-body::before { color: rgba({{ $cvPRgb }},0.05); }
.cv-access-card[data-name="package"] .cv-ac-body::after { background: linear-gradient(90deg, rgba({{ $cvPRgb }},0.55), rgba({{ $cvPRgb }},0)); }
.cv-access-card[data-name="package"] { --cv-package-rgb: {{ $cvPRgb }}; }
.cv-access-card[data-name="package"],
.cv-access-card[data-name="package"].is-active {
    border-color: #{{ $cvPkgHex }};
    background: radial-gradient(ellipse at 94% 50%, rgba({{ $cvPRgb }},0.28) 0%, transparent 50%), linear-gradient(145deg, rgba({{ $cvPRgb }},0.2), rgba(80,52,7,0.28));
    box-shadow: 0 0 0 1px rgba({{ $cvPRgb }},0.46), 0 10px 34px rgba({{ $cvPRgb }},0.3), inset 0 1px 0 rgba({{ $cvPRgb }},0.22);
}
.cv-access-card[data-name="package"]::before,
.cv-access-card[data-name="package"].is-active::before { border-color: #{{ $cvPkgHex }}; background: rgba({{ $cvPRgb }},0.24); }
.cv-access-card[data-name="package"] .cv-ac-icon-wrap,
.cv-access-card[data-name="package"].is-active .cv-ac-icon-wrap { background: rgba({{ $cvPRgb }},0.3); border-color: rgba({{ $cvPRgb }},0.72); box-shadow: 0 0 24px rgba({{ $cvPRgb }},0.52); }
.cv-access-card[data-name="package"] .cv-ac-icon-wrap i,
.cv-access-card[data-name="package"].is-active .cv-ac-icon-wrap i { color: #fff2b3 !important; font-size: 21px; }
.cv-access-card[data-name="package"] strong,
.cv-access-card[data-name="package"].is-active strong { color: #fff !important; font-size: 15px; }
.cv-access-card[data-name="package"] span,
.cv-access-card[data-name="package"].is-active span { color: #000 !important; }
.cv-access-card[data-name="package"] .cv-ac-body::before,
.cv-access-card[data-name="package"].is-active .cv-ac-body::before { color: rgba({{ $cvPRgb }},0.16); }
.cv-access-card[data-name="package"] .cv-ac-body::after,
.cv-access-card[data-name="package"].is-active .cv-ac-body::after { background: linear-gradient(90deg, rgba({{ $cvPRgb }},0.95), rgba({{ $cvPRgb }},0)); }
.cv-access-card[data-name="package"] .cv-ac-shimmer { opacity: .92; }
.cv-access-card[data-name="package"] .cv-ac-shimmer::before { background: linear-gradient(115deg, transparent 0%, transparent 30%, rgba(255,255,255,.84) 47%, rgba(var(--cv-package-rgb),.42) 56%, transparent 70%, transparent 100%); }

                        </style>
                        <div class="cv-access-grid">
                            @if ($data->reservation == 1)
                                <button type="button" class="cv-access-card cv-access-tab is-active" data-name="package">
                                    @if(!empty($data->package_tab_ribbon))
                                        <span class="cv-ac-ribbon">{{ $data->package_tab_ribbon }}</span>
                                    @endif
                                    <span class="cv-ac-shimmer" aria-hidden="true"></span>
                                    <span class="cv-ac-icon-wrap"><i class="fas {{ $data->package_tab_icon ?? 'fa-star' }}"></i></span>
                                    <span class="cv-ac-body">
                                        <strong>{{ $data->package_button_text ?? 'VIP Packages' }}</strong>
                                        <span style="color: #fff !important;">{{ $data->package_tab_subtitle ?? 'VIP table packages &amp; experiences' }}</span>
                                    </span>
                                </button>
                                <button type="button" class="cv-access-card cv-access-tab" data-name="guest">
                                    <span class="cv-ac-icon-wrap"><i class="fas {{ $data->guest_tab_icon ?? 'fa-car-side' }}"></i></span>
                                    <span class="cv-ac-body">
                                        <strong>{{ $data->guest_list_button_text ?? 'Free Ride & Entry' }}</strong>
                                        <span style="color: #fff !important;">{{ $data->guest_tab_subtitle ?? 'Complimentary ride and general entry' }}</span>
                                    </span>
                                </button>
                            @else
                                <div class="cv-access-card is-active" data-name="package">
                                    @if(!empty($data->package_tab_ribbon))
                                        <span class="cv-ac-ribbon">{{ $data->package_tab_ribbon }}</span>
                                    @endif
                                    <span class="cv-ac-shimmer" aria-hidden="true"></span>
                                    <span class="cv-ac-icon-wrap"><i class="fas {{ $data->package_tab_icon ?? 'fa-star' }}"></i></span>
                                    <span class="cv-ac-body">
                                        <strong>{{ $data->package_button_text ?? 'VIP Packages' }}</strong>
                                        <span style="color: #fff !important;">{{ $data->package_tab_subtitle ?? 'VIP table packages &amp; experiences' }}</span>
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>

                @if ($data->reservation == 1)
                    <div class="guest">
                        <form action="{{ route('reservations.store', ['slug' => $data->slug]) }}" method="post">
                            @csrf
                            <input type="hidden" name="website_id" value="{{ $data->id }}">
                            <input type="hidden" name="affiliate_slug" value="{{ $affiliateReferral->slug ?? '' }}">
                            <!-- Reservation date - synced from header dropdown -->
                            <input type="hidden" name="package_use_date" value="">
                            <section style="width: 100%">
                                <h5 class="section-kicker-lg">Guest List Reservation</h5>
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
                                                        <input type="tel" name="reservation_phone" id="reservation_phone"
                                                            placeholder="(555) 123-4567" required />
                                                        <div class="phone-note" style="font-size: 0.75rem; color: rgba(255,255,255,0.6); margin-top: 4px;">Phone formatting may vary by country. International SMS delivery is not guaranteed.</div>
                                                    </div>
                                                    <div class="form-group" style="width: 50%;">
                                                        <label for="email">Email</label>
                                                        <input type="email" name="reservation_email" id="email"
                                                            placeholder="For Confirmation" required />
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
                                        <div class="col-md-12 guest-list">
                                            <h2>Total Guests</h2>
                                            <div class="guest-gender-row">
                                                <div class="guest-section guest-section--men" style="border-color: {{ $brandPrimary }} !important;">
                                                    <span class="label">Men</span>
                                                    <div class="counter">
                                                        <span class="addon-qty-stepper guest-qty-stepper">
                                                            <button class="addon-qty-btn guest-qty-btn" type="button"
                                                                data-type="men" data-action="dec"
                                                                onclick="decrements('men')">-</button>
                                                            <span class="count addon-qty-val guest-qty-val" id="menCount">0</span>
                                                            <button class="addon-qty-btn guest-qty-btn" type="button"
                                                                data-type="men" data-action="inc"
                                                                onclick="increments('men')">+</button>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="guest-section guest-section--women" style="border-color: {{ $brandPrimary }} !important;">
                                                    <span class="label">Women</span>
                                                    <div class="counter">
                                                        <span class="addon-qty-stepper guest-qty-stepper">
                                                            <button class="addon-qty-btn guest-qty-btn" type="button"
                                                                data-type="women" data-action="dec"
                                                                onclick="decrements('women')">-</button>
                                                            <span class="count addon-qty-val guest-qty-val" id="womenCount">0</span>
                                                            <button class="addon-qty-btn guest-qty-btn" type="button"
                                                                data-type="women" data-action="inc"
                                                                onclick="increments('women')">+</button>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="guest-section guest-section--total" style="border-color: {{ $brandPrimary }} !important;">
                                                    <span class="label">Total Guests</span>
                                                    <div class="counter">
                                                        <span class="addon-qty-stepper guest-qty-stepper">
                                                            <span class="count addon-qty-val guest-qty-val" id="totalCount" style="margin-right: 0px !important">0</span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="men_count" id="men_count" value="0">
                                            <input type="hidden" name="women_count" id="women_count" value="0">
                                        </div>
                                        <div class="col-md-12 mt-4">
                                        </div>
                                        <div class="col-md-12">
                                            <div class="checkbox-container">
                                                <label class="consent-label">
                                                    <input type="checkbox" id="smsConsent_two" required />
                                                    <span>I agree to receive SMS communications regarding my reservation, transportation updates, VIP services, and related notifications. Message and data rates may apply. Messaging frequency may vary. Reply STOP to opt out at any time.</span>
                                                </label>
                                                <label class="consent-label">
                                                    <input type="checkbox" id="termsConsent_two" required />
                                                    <span>I have read and agree to the <a target="_blank"
                                                        href="{{ $data->terms }}">Terms of Service</a> / <a target="_blank" href="{{ $data->terms }}">Venue Policies</a></span>
                                                </label>
                                            </div>
                                            <button class="submit-btn" type="submit" id="submitBtn_two">Create
                                                Reservation</button>
    
                                        </div>
                                        <div class="col-md-4"></div>
                                    </div>
                                </div>
    
                            </section>

                            {{-- Location card removed (now lives in the hero .cv-hero-location panel) --}}


                            <input type="hidden" name="type" value="guest">
                            <input type="hidden" name="recaptcha_token" id="recaptcha_token" value="">
                            <input type="hidden" name="form_load_time" id="form_load_time" value="">

                        </form>
                    </div>
                @endif
    
    
                <div class="package">
                    <section class="vip-pack">
                        <div class="">
    
                            <div class="row">
                                <div class="col-md-12">

                                    @php
                                        $mostPopularPackageName = '';
                                        if (isset($packageCategories) && $packageCategories->count()) {
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
                                    <div class="cv-package-section-header" style="display:flex; justify-content:space-between; align-items:center; margin: 18px 0 12px; flex-wrap:wrap; gap:10px;">
                                        <div>
                                            <h5 class="section-kicker-lg" style="margin:0 !important;">{{ $data->package_section_title ?: 'Select Your Package' }}</h5>
                                            <p style="margin: 4px 0 0; font-size: 12.5px; color: rgba(255,255,255,0.5);">{{ $data->package_section_subtext ?: 'All packages include free ride, club entry, and priority access.' }}</p>
                                        </div>
                                        @if($mostPopularPackageName)
                                        <div class="cv-most-popular-tag" style="display:inline-flex; align-items:center; gap:10px; padding: 7px 14px; border-radius: 999px; background: rgba(167,116,255,0.08); border: 1px solid rgba(167,116,255,0.32); font-size: 12.5px; color: rgba(255,255,255,0.9); font-weight: 600;">
                                            <span style="background: linear-gradient(135deg, #a774ff 0%, #7c3aed 50%, #5b21b6 100%); color: #fff; padding: 3px 9px; border-radius: 999px; font-size: 10px; font-weight: 800; letter-spacing: .06em; display: inline-flex; align-items: center; gap: 4px; box-shadow: 0 2px 8px rgba(124,58,237,0.35), inset 0 1px 0 rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.18); text-transform: uppercase;"><i class="fas fa-fire" style="font-size:9px;"></i>MOST POPULAR</span>
                                            <span>{{ $mostPopularPackageName }}</span>
                                        </div>
                                        @endif
                                    </div>
    
                                    @if(!empty($isIframeCheckout))
                                        <div class="hero-date-card iframe-date-card">
                                            <label>Choose Your Reservation Date</label>
                                            <div class="date-input-wrapper">
                                                <select id="package_use_date_iframe" style="width: 100%;" aria-describedby="package_use_date_iframe_error">
                                                    <option value="" selected>Select Date</option>
                                                    @foreach($eventDateOptions as $dateOption)
                                                        <option value="{{ $dateOption['value'] }}">{{ $dateOption['label'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <small id="package_use_date_iframe_error" class="reservation-date-error" style="display:none;">Please select a reservation date.</small>
                                        </div>
                                    @endif

                                    @if(isset($packageCategories) && $packageCategories->count())
                                        <div class="mb-3 package-category-tiles" style="width:100%;">
                                            @foreach ($packageCategories as $category)
                                                @php
                                                    $catRgbStr = null;
                                                    if (!empty($category['color'])) {
                                                        $ch = ltrim($category['color'], '#');
                                                        [$cr, $cg, $cb] = sscanf($ch, '%02x%02x%02x');
                                                        $catRgbStr = "$cr,$cg,$cb";
                                                    }
                                                @endphp
                                                <button
                                                    type="button"
                                                    class="package-category-tile{{ $catRgbStr ? ' has-cat-color' : '' }}"
                                                    @if($catRgbStr) style="--cat-rgb: {{ $catRgbStr }}" @endif
                                                    data-target="#category-group-{{ $category['id'] }}"
                                                >
                                                    @if(!empty($category['icon']))
                                                        <i class="fas {{ $category['icon'] }} package-category-tile-icon"></i>
                                                    @endif
                                                    <span class="package-category-name">{{ $category['name'] }}</span>
                                                    <span class="package-category-indicator">+</span>
                                                </button>
                                            @endforeach
                                        </div>

                                        @foreach ($packageCategories as $category)
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
                                                    <div class="vip-card cv-exact-card cv-tier-{{ $tierIndex }}" id="pkg-card-{{ $item->id }}">
                                                        <div class="cv-pkg-media-wrap">
                                                            <picture>
                                                                <source media="(max-width: 767px)" srcset="{{ $packageMobileVisual }}">
                                                                <img src="{{ $packageVisual }}" alt="{{ $item->name }}" class="cv-pkg-media">
                                                            </picture>
                                                            @if ((int) ($item->is_most_popular ?? 0) === 1)
                                                                <span class="cv-popular-pill">MOST POPULAR</span>
                                                            @endif
                                                        </div>

                                                        <div class="vip-card-main">
                                                            <div class="cv-pkg-title-row">
                                                                <i class="fas {{ $tierIcon }} cv-pkg-title-icon"></i>
                                                                <div class="cv-pkg-title">{{ $item->name }}</div>
                                                                <button type="button" class="cv-pkg-tooltip-trigger" aria-label="View package details">i</button>
                                                            </div>
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
                                                                data-default-label="Add to Cart"
                                                                >Add to Cart</button>
                                                            <small class="package-guest-error" style="display:none;color:#ff6b6b;font-size:11px;line-height:1.35;margin-top:4px;"></small>
                                                            <div class="package-soldout" style="display:none;color:#ff2b2b;font-size:12px;font-weight:700;line-height:1.35;margin-top:4px;">Sold Out!</div>
                                                        </div>
                                                    </div>
                                                @endforeach
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
                                                    <div style="font-size: 16px;" class="default-gratuity" data-tip="Supports venue staff and hospitality service. Calculated based on subtotal.">
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
                                                    class="vip-price"><span>*No sales tax applied. Services sold are not subject to sales tax under Nevada law. Please consult a tax advisor for your local regionï¿½ifï¿½applicable.</span></div>
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
    
                                    <div class="mt-3" id="shareLinkContainer">
                                        <button type="button" id="generateShareLink">Generate Shareable Link</button>
                                        <div style="position: relative;">
                                            <input type="text" id="shareableLink" readonly style="width:100%;margin-top:8px;display:none;padding-right:40px;" />
                                            <div id="copyTooltip" style="position: absolute; top: -35px; right: 0; background: #28a745; color: white; padding: 8px 12px; border-radius: 4px; font-size: 12px; display: none; white-space: nowrap; z-index: 1000;">
                                                Link copied!
                                            </div>
                                        </div>
                                                <div id="shareActions" style="display:none;gap:8px;flex-wrap:wrap;margin-top:8px;">
                                                    <button type="button" class="checkout-share-btn" data-share="email" style="background:#0f172a;color:#fff;border:1px solid #334155;padding:6px 10px;border-radius:8px;font-size:12px;">Email</button>
                                                    <button type="button" class="checkout-share-btn" data-share="whatsapp" style="background:#0f172a;color:#fff;border:1px solid #334155;padding:6px 10px;border-radius:8px;font-size:12px;">WhatsApp</button>
                                                    <button type="button" class="checkout-share-btn" data-share="facebook" style="background:#0f172a;color:#fff;border:1px solid #334155;padding:6px 10px;border-radius:8px;font-size:12px;">Facebook</button>
                                                    <button type="button" class="checkout-share-btn" data-share="copy" style="background:#0f172a;color:#fff;border:1px solid #334155;padding:6px 10px;border-radius:8px;font-size:12px;">Copy</button>
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
                                                                    <input type="tel" id="package_phone" name="package_phone" placeholder="(555) 123-4567" required />
                                                                    <div class="phone-note" style="font-size: 0.75rem; color: rgba(255,255,255,0.6); margin-top: 4px;">Phone formatting may vary by country. International SMS delivery is not guaranteed.</div>
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
                                                            <div class="form-group">
                                                                <label for="host">Host Name</label>
                                                                <input id="host" name="host_name"
                                                                    placeholder="Enter host name (optional)">
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
                                                    <input type="checkbox" id="transportation_part"  required />
                                                    {{ $data->transportation_confirmation_text ?? 'I confirm I am arriving in a personal vehicle or approved venue transportation. I am not arriving via Uber, Lyft, taxi, limousine, ride-share, or any other third-party transportation service.' }}
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

                                                            <div class="form-row">
                                                                <div class="form-group" style="width: 100%;">
                                                                    <label for="Pick-up-time">Pick-up Time</label>
                                                                    <div class="pickup-time-wrap">
                                                                        <i class="fas fa-clock pickup-time-icon"></i>
                                                                        <input name="transportation_pickup_time" type="text" readonly required
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
                                                                        id="address" placeholder="Enter pick-up address" required />
                                                                </div>

                                                            </div>
    
                                                            <div class="form-row">
                                                                <div class="form-group" style="width: 100%;">
                                                                    <label for="phone">Contact Phone Number or WhatsApp</label>
                                                                    <input type="tel" name="transportation_phone" id="phone"
                                                                        placeholder="For driver/dispatch to coordinate pickup"   required />
                                                                </div>
    
                                                            </div>
    
                                                            <div class="form-row">
                                                                <div class="num-guest" style="width: 100%; display: flex;">
                                                                    <label for="">Number of Guest(s)</label>
    
                                                                    <input type="number" class="form-control"
                                                                        name="transportation_guest" value="0" min="0"
                                                                        style="width: 120px; max-width: 120px; color: #fff;" required />
    
    
    
                                                                </div>
                                                            </div>
    
                                                            <div class="form-group">
                                                                <label for="note">Pickup Note</label>
                                                                <textarea name="transportation_note" id="note"
                                                                    placeholder="If any"></textarea>
                                                            </div>

                                                            <!-- TRANSPORTATION RESTRICTION NOTICE -->
                                                            <div style="background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%); border: 2px solid #c92a2a; border-radius: 8px; padding: 14px 16px; margin-top: 20px; box-shadow: 0 4px 12px rgba(255, 107, 107, 0.25);">
                                                                <div style="display: flex; align-items: flex-start; gap: 12px;">
                                                                    <div style="font-size: 20px; color: #fff; flex-shrink: 0;">⚠️</div>
                                                                    <div>
                                                                        <p style="margin: 0; color: #fff; font-weight: 700; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">HOUSE TRANSPORTATION OR SELF DRIVE ONLY</p>
                                                                        <p style="margin: 6px 0 0 0; color: rgba(255,255,255,0.9); font-size: 12px; line-height: 1.4;">Uber, Lyft, taxi, limo & ride-sharing are NOT permitted.</p>
                                                                    </div>
                                                                </div>
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
                                                            <input type="hidden" name="payment_phone" id="hidden_payment_phone" required />
                                                            <input type="hidden" name="payment_email" id="hidden_payment_email" required />
                                                            <input type="hidden" name="payment_month" id="hidden_payment_month" required />
                                                            <input type="hidden" name="payment_day" id="hidden_payment_day" required />
                                                            <input type="hidden" name="payment_year" id="hidden_payment_year" required />
    
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
                                                            <div class="checkbox-container payment-consent-group" style="margin-top: 1.5rem; display: none;">
                                                                <label class="consent-label">
                                                                    <input type="checkbox" id="businessExpenseCheckbox" />
                                                                    <span>This purchase is for business purposes</span>
                                                                </label>
                                                            </div>
<div id="businessFields" style="display: none; margin-top: 1rem;">
    <div class="form-row">
        <div class="form-group" style="width: 50%;">
            <label for="business_company">Company Name</label>
            <input type="text" name="business_company" id="business_company" placeholder="Company Name" required />
        </div>
        <div class="form-group" style="width: 50%;">
            <label for="business_vat">VAT or Tax ID</label>
            <input type="text" name="business_vat" id="business_vat" placeholder="VAT or Tax ID" required />
        </div>
    </div>
    <div class="form-row">
        <div class="form-group" style="width: 100%;">
            <label for="business_address">Business Address</label>
            <input type="text" name="business_address" id="business_address" placeholder="Business Address" required />
        </div>
    </div>
    </div>
</div>
    
                                                            <div class="checkbox-container payment-consent-group" id="payment-consent-group">
                                                                <label class="consent-label">
                                                                    <input type="checkbox" id="smsConsent" required />
                                                                    <span>I agree to receive SMS communications regarding my reservation, transportation updates, VIP services, and related notifications. Message and data rates may apply. Messaging frequency may vary. Reply STOP to opt out at any time.</span>
                                                                </label>

                                                                <label class="consent-label" style="margin-top: 1.4rem;">
                                                                    <input type="checkbox" id="termsConsent" required />
                                                                    <span>I have read and agree to the <a
                                                                        target="_blank" href="{{ $data->terms }}">Terms of Service</a> / <a target="_blank" href="{{ $data->terms }}">Venue Policies</a></span>
                                                                </label>

                                                                {{-- <p style="margin: 12px 0 0; font-size: 12px; line-height: 1.5; color: rgba(255,255,255,0.82);">
                                                                    All bookings are processed through CartVIP. By completing this purchase, you acknowledge that all sales are final and non-refundable, subject to applicable law and the venue's policies, and that you agree to all venue entry requirements. You confirm that you are authorized to use this payment method and that the information provided is accurate. You understand that a valid government-issued photo ID may be required at check-in and may be photographed to verify identity, age, reservation redemption, fraud prevention, venue security, and chargeback dispute purposes. Identification records are securely stored and are never retained on the scanning device.
                                                                </p> --}}
                                                            </div>

                                                            <input type="hidden" class="package_use_date" name="package_use_date" value="">
                                                            <input type="hidden" class="promo_code" name="promo_code">
                                                            <input type="hidden" class="discounted_amount" name="discounted_amount">
                                                            
                                                            <!-- Step Navigation -->
                                                            <div class="step-navigation">
                                                                <button type="button" class="btn-prev" id="prev-to-transport">Previous: Transportation</button>
                                                                <button class="submit-btn" style="margin-top: 0px !important;" id="submitBtn" type="submit">Complete Purchase</button>
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
                        $sidebarVenueImage = !empty($event->image ?? null) ? asset('uploads/' . $event->image) : ($data->logo ? asset('uploads/' . $data->logo) : null);
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

                    @php
                        $refundablePct = (int) ($data->refundable_fee ?? 0);
                    @endphp
                    <div class="cv-deposit-box dynamic-price" id="cv-deposit-box" style="display:none;">
                        <div class="cv-deposit-content">
                            <div class="cv-deposit-top">
                                <div class="cv-deposit-label" data-tip="@if($refundablePct > 0){{ $refundablePct }}% of the total is collected today to secure your reservation. The balance is paid on arrival at the venue.@else You're paying the full amount today.@endif">@if($refundablePct > 0)Due Today ({{ $refundablePct }}% Deposit)@else{{ 'Due Today' }}@endif <span class="cv-info-icon">i</span></div>
                                <div class="cv-deposit-shield" data-tip="Secure checkout â€” your payment is protected by bank-level SSL encryption and never stored on this site." data-tip-right><i class="fas fa-shield-alt"></i></div>
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

                <section class="cv-events-shell">
                    <div class="container py-5 events-section-container">
                        <div class="event-header">
                            <h2>Upcoming Events</h2>
                            <div class="event-filters">
                                <button type="button" class="event-filter" data-filter="week">This Week</button>
                                <button type="button" class="event-filter" data-filter="month">This Month</button>
                                <button type="button" class="event-filter" data-filter="year">This Year</button>
                            </div>
                        </div>
                        <div class="row g-4" id="events-list">
                            @php
                                $todayPacific = \Carbon\Carbon::now('America/Los_Angeles')->toDateString();
                            @endphp
                            @foreach ($data->events as $item)
                                @php
                                    $eventStartDate = $item->start_date ?? $item->date;
                                    $eventEndDate = $item->end_date ?? $eventStartDate;
                                @endphp
                                  @if (!$item->is_archieved && $eventEndDate && \Carbon\Carbon::parse($eventEndDate)->toDateString() >= $todayPacific)
                                    <div class="col-md-4 event-card-item"
                                        data-date="{{ \Carbon\Carbon::parse($eventStartDate)->format('Y-m-d') }}">
                                        <a href="/{{ $data->slug }}?event_name={{ $item->name }}" class="event-card">
                                            <div class="card">
                                                <img src="{{ asset('uploads/' . $item->image) }}" alt="{{ $item->name }}">
                                                <div class="d-flex">
                                                    <div class="event-day">{{ \Carbon\Carbon::parse($eventStartDate)->format('l') }}</div>
                                                    <div class="event-dates">{{ \Carbon\Carbon::parse($eventStartDate)->format('M') }}<span>{{ \Carbon\Carbon::parse($eventStartDate)->format('d') }}</span></div>
                                                </div>
                                                <div class="event-location">{{ $item->name }}</div>
                                                @if($eventEndDate && $eventStartDate !== $eventEndDate)
                                                    <div class="event-location">
                                                        {{ \Carbon\Carbon::parse($eventStartDate)->format('M d') }} - {{ \Carbon\Carbon::parse($eventEndDate)->format('M d') }}
                                                    </div>
                                                @endif
                                                @if(!empty($item->time))
                                                    <div class="event-location"><i class="fas fa-clock"></i>{{ $item->time }}</div>
                                                @endif
                                                <div class="event-location"><i class="fas fa-map-marker-alt"></i>{{ $data->location }}</div>
                                                <div class="event-location">Reserve</div>
                                            </div>
                                        </a>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
    
    
                </section>
    
                <div class="modal fade" id="infoTooltipModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content" style="background:#1a1d2e;color:#ddd;">
                            <div class="modal-header">
                                <h5 class="modal-title" style="color:#fff;">Modal title</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
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
                                <button type="button" class="btn btn-secondary" id="addonModalNoAddonsBtn">No Add-ons</button>
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
            /* ===== Country Code Picker Styles ===== */
            .phone-input-wrapper {
                display: flex;
                gap: 8px;
                align-items: stretch;
            }

            .country-code-input {
                flex: 0 0 120px;
                position: relative;
            }

            .country-code-field {
                width: 100%;
                padding: 10px 12px;
                border: 1px solid rgba(255,255,255,0.2);
                background: rgba(255,255,255,0.05);
                border-radius: 8px;
                color: #fff;
                font-size: 14px;
                transition: border-color 0.3s;
            }

            .country-code-field:focus {
                outline: none;
                border-color: rgba(255,255,255,0.4);
                background: rgba(255,255,255,0.08);
            }

            .country-code-dropdown {
                position: absolute;
                top: 100%;
                left: 0;
                width: 100%;
                max-height: 250px;
                overflow-y: auto;
                background: rgba(20,20,30,0.98);
                border: 1px solid rgba(255,255,255,0.2);
                border-radius: 8px;
                z-index: 1000;
                display: none;
                margin-top: 4px;
                box-shadow: 0 10px 25px rgba(0,0,0,0.5);
            }

            .country-code-dropdown.active {
                display: block;
            }

            .country-option {
                padding: 10px 12px;
                cursor: pointer;
                border-bottom: 1px solid rgba(255,255,255,0.05);
                font-size: 13px;
                color: rgba(255,255,255,0.8);
                transition: background-color 0.2s;
            }

            .country-option:hover {
                background: rgba(255,255,255,0.1);
                color: #fff;
            }

            .country-option.selected {
                background: rgba(124,92,255,0.2);
                color: #fff;
                font-weight: 600;
            }

            .flag-icon {
                display: inline-block;
                width: 20px;
                height: 14px;
                margin-right: 8px;
                border-radius: 2px;
                vertical-align: middle;
                line-height: 14px;
                text-align: center;
                font-size: 12px;
            }

            .phone-number-input {
                flex: 1;
            }

            .phone-validation-message {
                font-size: 12px;
                color: #ff6b6b;
                margin-top: 4px;
                display: none;
            }

            .phone-validation-message.valid {
                color: #51cf66;
                display: block;
            }

            .phone-validation-message.invalid {
                color: #ff6b6b;
                display: block;
            }

            /* ===== Processing Overlay ===== */
            #checkout-processing-overlay {
                position: fixed;
                inset: 0;
                background: rgba(8, 12, 22, 0.78);
                backdrop-filter: blur(5px);
                display: none;
                align-items: flex-start;
                justify-content: center;
            align-items: center;
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
                align-items: flex-start;
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
                align-items: flex-start;
                justify-content: center;
            align-items: center;
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
                align-items: flex-start;
                justify-content: center;
            align-items: center;
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

            // Reservation form validation: prevent submission without date and guests
            (function () {
                const submitBtn = document.getElementById('submitBtn_two');
                if (!submitBtn) return;

                const form = submitBtn.closest('form');
                if (!form) return;

                // Set form load time
                const formLoadTimeField = document.getElementById('form_load_time');
                if (formLoadTimeField) {
                    formLoadTimeField.value = Math.floor(Date.now() / 1000);
                }

                submitBtn.addEventListener('click', function (e) {
                    const reservationDate = document.getElementById('package_use_date');
                    const menCount = parseInt(document.getElementById('menCount')?.textContent || '0', 10);
                    const womenCount = parseInt(document.getElementById('womenCount')?.textContent || '0', 10);
                    const totalGuests = menCount + womenCount;

                    // Sync reservation date to hidden field BEFORE validation
                    if (reservationDate && reservationDate.value) {
                        const hiddenDateField = document.querySelector('input[name="package_use_date"]');
                        if (hiddenDateField) {
                            hiddenDateField.value = reservationDate.value;
                        }
                    }

                    let hasError = false;
                    let errorMessage = '';

                    // Check if reservation date is selected
                    if (!reservationDate || !reservationDate.value || reservationDate.value.trim() === '') {
                        hasError = true;
                        errorMessage = 'Please select a reservation date.';
                        if (reservationDate) {
                            reservationDate.classList.add('required-field');
                            reservationDate.setAttribute('aria-invalid', 'true');
                        }
                        const dateError = document.getElementById('package_use_date_error');
                        if (dateError) {
                            dateError.textContent = errorMessage;
                            dateError.style.display = 'block';
                        }
                    } else {
                        if (reservationDate) {
                            reservationDate.classList.remove('required-field');
                            reservationDate.removeAttribute('aria-invalid');
                        }
                        const dateError = document.getElementById('package_use_date_error');
                        if (dateError) {
                            dateError.style.display = 'none';
                        }
                    }

                    // Check if total guests is greater than 0
                    if (totalGuests === 0) {
                        hasError = true;
                        errorMessage = errorMessage ? 'Please select a reservation date and add at least one guest.' : 'Please add at least one guest (men or women).';
                    }

                    // Check SMS consent checkbox
                    const smsConsent = document.getElementById('smsConsent_two');
                    if (!smsConsent || !smsConsent.checked) {
                        hasError = true;
                        errorMessage = 'Please agree to receive SMS communications regarding your reservation, transportation updates, VIP services, and related notifications.';
                    }

                    // Check terms consent checkbox
                    const termsConsent = document.getElementById('termsConsent_two');
                    if (!termsConsent || !termsConsent.checked) {
                        hasError = true;
                        errorMessage = 'Please accept the Terms of Service.';
                    }

                    // Require a valid country code selection on the reservation phone picker.
                    // The picker's code box is a searchable text input; block submit if the user typed
                    // search text without picking a country (or typed an invalid code).
                    var __ccFields = form.querySelectorAll('.country-code-field');
                    for (var __ci = 0; __ci < __ccFields.length; __ci++) {
                        var __cc = __ccFields[__ci];
                        if (__cc.offsetParent === null) continue;
                        var __ccWrap = __cc.closest('.country-code-input');
                        if (!__ccWrap) continue;
                        var __opts = __ccWrap.querySelectorAll('.country-option');
                        if (!__opts.length) continue; // fail-safe: nothing to validate against
                        var __ccVal = (__cc.value || '').trim();
                        var __ccOk = false;
                        for (var __oi = 0; __oi < __opts.length; __oi++) {
                            if ((__opts[__oi].getAttribute('data-flag') + ' ' + __opts[__oi].getAttribute('data-code')) === __ccVal) { __ccOk = true; break; }
                        }
                        if (!__ccOk) {
                            __cc.style.borderColor = '#ff6b6b';
                            hasError = true;
                            errorMessage = 'Please select a valid country code from the list (search and click your country, or type the full +code in the phone box).';
                            break;
                        }
                        __cc.style.borderColor = '';
                    }

                    if (hasError) {
                        e.preventDefault();
                        e.stopPropagation();
                        // Show error message instead of alert
                        const errorMsg = document.getElementById('validation-error-msg-reservation') || document.createElement('div');
                        if (!errorMsg.id) {
                            errorMsg.id = 'validation-error-msg-reservation';
                            errorMsg.style.cssText = 'color: #ff6b6b; padding: 12px; margin: 10px 0; font-weight: 600; text-align: center; background: rgba(255, 107, 107, 0.1); border-radius: 6px; border-left: 4px solid #ff6b6b;';
                            form.parentElement.insertBefore(errorMsg, form);
                        }
                        errorMsg.textContent = errorMessage;
                        errorMsg.style.display = 'block';
                        errorMsg.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        return;
                    }

                    // Prevent default and handle submission with reCAPTCHA
                    e.preventDefault();

                    // Replace visible phone fields with E.164 values before submission
                    const phoneFieldsToSync = [
                        { visible: 'reservation_phone', e164: 'reservation_phone_e164' }
                    ];

                    phoneFieldsToSync.forEach(pair => {
                        const e164Field = form.querySelector(`input[name="${pair.e164}"]`);
                        const visibleField = form.querySelector(`input[name="${pair.visible}"]`);
                        if (e164Field && visibleField && e164Field.value) {
                            // Use E.164 format for submission
                            visibleField.value = e164Field.value;
                        }
                    });

                    // Get reCAPTCHA token before submitting
                    if (typeof window.executeRecaptcha === 'function') {
                        window.executeRecaptcha('reservation_submit').then(function(token) {
                            if (token) {
                                const tokenField = document.getElementById('recaptcha_token');
                                if (tokenField) {
                                    tokenField.value = token;
                                }
                            }
                            // Submit form after token is set
                            form.submit();
                        }).catch(function(error) {
                            console.warn('reCAPTCHA error:', error);
                            // Still submit if reCAPTCHA fails - server-side validation will handle
                            form.submit();
                        });
                    } else {
                        // reCAPTCHA not available - submit directly
                        console.warn('reCAPTCHA not loaded');
                        form.submit();
                    }
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
                    window.showToast('Added to cart!', packageName ? (packageName + ' Â· ' + label) : label, 'fas fa-check');
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

            // Open order-summary tips in the info modal on click (no hover tooltip).
            (function () {
                function escapeHtml(text) {
                    return String(text || '').replace(/[&<>"']/g, function (char) {
                        return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[char];
                    });
                }

                function resolveTitle(trigger) {
                    if (trigger.classList.contains('default-service-charge')) return 'Service Charge';
                    if (trigger.classList.contains('default-sales-tax')) return 'Sales Tax';
                    if (trigger.classList.contains('default-gratuity')) return 'Gratuity';
                    if (trigger.classList.contains('default-processing-fee')) return 'Processing Fee';
                    if (trigger.classList.contains('cv-deposit-label')) return 'Due Today';
                    if (trigger.classList.contains('cv-deposit-shield')) return 'Secure Checkout';

                    var label = trigger.querySelector('span');
                    if (label) {
                        var text = String(label.textContent || '').trim();
                        if (text) return text.replace(/\bi\s*$/i, '').trim();
                    }
                    return 'Details';
                }

                function openTipModal(trigger) {
                    var tip = String(trigger.getAttribute('data-tip') || '').trim();
                    if (!tip) return;

                    var modal = document.getElementById('infoTooltipModal');
                    if (!modal) return;

                    var modalTitle = modal.querySelector('.modal-title');
                    var modalBody = modal.querySelector('.modal-body');
                    if (modalTitle) modalTitle.textContent = resolveTitle(trigger);
                    if (modalBody) modalBody.innerHTML = '<p style="margin:0;">' + escapeHtml(tip) + '</p>';

                    if (window.bootstrap && window.bootstrap.Modal) {
                        window.bootstrap.Modal.getOrCreateInstance(modal).show();
                        return;
                    }
                    if (window.jQuery && window.jQuery.fn && window.jQuery.fn.modal) {
                        window.jQuery(modal).modal('show');
                    }
                }

                document.addEventListener('click', function (event) {
                    var trigger = event.target.closest('#cv-order-sidebar [data-tip], #cv-deposit-box [data-tip]');
                    if (!trigger) return;
                    event.preventDefault();
                    event.stopPropagation();
                    openTipModal(trigger);
                });
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
            function setBusinessFieldsRequired(on) {
                ['business_company', 'business_vat', 'business_address'].forEach(function (n) {
                    var el = document.querySelector('[name="' + n + '"]');
                    if (el) { if (on) { el.setAttribute('required', 'required'); } else { el.removeAttribute('required'); } }
                });
            }
            // Business fields start hidden, so they must not be required until the box is checked
            // (a required field inside a display:none container blocks form submission).
            setBusinessFieldsRequired($('#businessExpenseCheckbox').is(':checked'));
            $('#businessExpenseCheckbox').on('change', function () {
                if ($(this).is(':checked')) {
                    $('#businessFields').slideDown();
                    setBusinessFieldsRequired(true);
                } else {
                    $('#businessFields').slideUp();
                    setBusinessFieldsRequired(false);
                }
            });

            // Multi-step form safety: the instant "Complete Purchase" is clicked, drop `required`
            // from any field that is currently hidden (an unused transport mode, a collapsed
            // business section, or unpopulated hidden inputs) so native validation can never block
            // submission with "An invalid form control is not focusable". Visible required fields
            // are still validated, and each step is validated as the user advances.
            (function () {
                var purchaseBtn = document.getElementById('submitBtn');
                if (purchaseBtn) {
                    purchaseBtn.addEventListener('click', function () {
                        document.querySelectorAll('#payment-form [required]').forEach(function (el) {
                            if (el.offsetParent === null) { el.removeAttribute('required'); }
                        });
                    }, true);
                }
            })();
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
                limit: @json($event->attendee_limit ?? null),
                remaining: @json(isset($event->remaining_attendee_capacity) ? (int) $event->remaining_attendee_capacity : null)
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
                    if (!Number.isFinite(parseInt(transportationGuestField.val(), 10)) || parseInt(transportationGuestField.val(), 10) < 0) {
                        transportationGuestField.val('0');
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
                return String($('#package_use_date_iframe').val() || $('#package_use_date').val() || $('.package_use_date').val() || '').trim();
            }

            window.getSelectedUseDate = getSelectedUseDate;

            function showReservationDateError(message) {
                var text = String(message || 'Please select a reservation date.').trim();
                $('#package_use_date, #package_use_date_iframe').addClass('required-field').attr('aria-invalid', 'true');
                $('#package_use_date_error').text(text).show();
                $('#package_use_date_iframe_error').text(text).show();
            }

            function clearReservationDateError() {
                $('#package_use_date, #package_use_date_iframe').removeClass('required-field').removeAttr('aria-invalid');
                $('#package_use_date_error').hide();
                $('#package_use_date_iframe_error').hide();
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
                if (document.body.classList.contains('embed-checkout-mode') && document.getElementById('package_use_date_iframe')) {
                    $('#package_use_date_iframe').trigger('focus');
                } else {
                    $('#package_use_date').trigger('focus');
                }
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
                if ($('#package_use_date_iframe').length && $('#package_use_date_iframe').val() !== selected) {
                    $('#package_use_date_iframe').val(selected);
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
                        $sel.append('<option value="' + i + '">' + i + ' ' + (i === 1 ? 'ticket' : 'tickets') + '</option>');
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
                        $sel.append('<option value="' + i + '">' + i + ' ' + (i === 1 ? 'ticket' : 'tickets') + '</option>');
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
                var sales_tax_price = ("{{ $data->sales_tax_name }}" != "0") ? (discountedSubtotal / 100) * sales_tax : 0;

                var processingFeeBase = discountedSubtotal;
                var amountAfterCoupon = discountedSubtotal + service_charge_price + sales_tax_price + gratuited_price;
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
                            event_name: @json($event->name),
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
                // Use E.164 format from hidden field for SMS
                const e164Phone = $('input[name="package_phone_e164"]').val() || $('input[name="package_phone"]').val();
                $('#hidden_payment_phone').val(e164Phone);
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
                // Get references
                const transportPhoneInput = $('input[name="transportation_phone"]')[0];
                const transportCountryCode = $('input[name="transportation_phone_country"]')[0];
                const packagePhoneInput = $('input[name="package_phone"]')[0];
                const packageCountryCode = $('input[name="package_phone_country"]')[0];

                if (!transportPhoneInput || !transportCountryCode || !packageCountryCode) {
                    console.warn('Missing fields for copy operation');
                    return;
                }

                // Copy phone number
                const packagePhoneValue = packagePhoneInput ? packagePhoneInput.value : '';
                transportPhoneInput.value = packagePhoneValue;

                // Copy country code - both the display value and the dataset code
                transportCountryCode.value = packageCountryCode.value;
                transportCountryCode.dataset.code = packageCountryCode.dataset.code;

                // Copy E.164 field
                const packageE164Field = $('input[name="package_phone_e164"]')[0];
                let transportE164Field = $('input[name="transportation_phone_e164"]')[0];

                if (packageE164Field && packageE164Field.value) {
                    if (!transportE164Field) {
                        transportE164Field = document.createElement('input');
                        transportE164Field.type = 'hidden';
                        transportE164Field.name = 'transportation_phone_e164';
                        transportPhoneInput.parentElement.appendChild(transportE164Field);
                    }
                    transportE164Field.value = packageE164Field.value;
                }

                // Trigger change event and validation
                $(transportPhoneInput).trigger('input').trigger('change');
                $(transportCountryCode).trigger('change');
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
                            + '<span class="addon-qty-val" style="padding-top: 3px !important;" data-id="' + addon.id + '" data-name="' + escapeAddonHtml(addon.name) + '" data-price="' + unitPrice + '">' + currentQty + '</span>'
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
                    var targetSelector = String($tile.data('target') || '');
                    var targetId = targetSelector.replace(/^#/, '');
                    var $target = targetId ? $('#' + targetId) : $();
                    var isOpen = $tile.hasClass('active');

                    $('.package-category-tile').removeClass('active');
                    $('.package-category-group').stop(true, true).slideUp(180);

                    if (!isOpen && $target.length) {
                        $tile.addClass('active');
                        $target.stop(true, true).slideDown(180);
                    }
                });

                if (document.body.classList.contains('embed-checkout-mode') && $('.package-category-tile').length && !$('.package-category-tile.active').length) {
                    $('.package-category-tile').first().trigger('click');
                }

                $(document).on('change', '#package_use_date_iframe', function() {
                    $('#package_use_date').val($(this).val()).trigger('change');
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

                            // No add-ons to offer: add the package straight to the cart (skip the modal).
                            if ((window.pendingPackageSelection.addons || []).length === 0) {
                                $('#addonModalNoAddonsBtn').trigger('click');
                            } else {
                                openAddonSelectionModal(window.pendingPackageSelection);
                            }
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

                // No Add-ons button - adds package without any selected add-ons
                $('#addonModalNoAddonsBtn').on('click', function() {
                    if (!window.pendingPackageSelection) {
                        return;
                    }

                    var selection = window.pendingPackageSelection;
                    var selectedAddons = []; // Empty array - no add-ons selected

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
                    if ($('#package_use_date_iframe').length && $('#package_use_date_iframe').val() !== currentDate) {
                        $('#package_use_date_iframe').val(currentDate);
                    }
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

                // Scroll to the top of the new step on all devices
                setTimeout(function() {
                    var el = document.getElementById('section-' + stepNumber);
                    if (el) {
                        var isEmbedMode = document.body.classList.contains('embed-checkout-mode');
                        var isIosDevice = /iPad|iPhone|iPod/.test(navigator.userAgent) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
                        var scrollBlock = (isEmbedMode && stepNumber === 2 && window.innerWidth <= 991)
                            ? 'center'
                            : 'start';
                        var scrollBehavior = (isEmbedMode && isIosDevice) ? 'auto' : 'smooth';
                        el.scrollIntoView({ behavior: scrollBehavior, block: scrollBlock });
                    }
                }, 50);
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
                    // Transportation form validation disabled - allow form to proceed
                    // requiredFields.push(
                    //     '[name="package_use_date"]',
                    //     '[name="transportation_pickup_time"]',
                    //     '[name="transportation_address"]',
                    //     '[name="transportation_phone"]',
                    //     '[name="transportation_guest"]'
                    // );
                } else if (stepNumber === 2 && !window.requiresTransportation) {
                    // Validate transportation confirmation checkbox
                    if (!$('#transportation_part').is(':checked')) {
                        alert('Please confirm your transportation arrangement.');
                        return false;
                    }
                }
                
                // Keep transportation guest default at 0 until user explicitly sets a value > 0.
                if (stepNumber === 2 && window.requiresTransportation) {
                    const guestField = $('[name="transportation_guest"]');
                    if (!guestField.val() || !Number.isFinite(parseInt(guestField.val(), 10))) {
                        guestField.val('0');
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

                // Require a valid country code selection on any visible phone country-code picker.
                // The picker's code box is a searchable text input; if the user typed search text and
                // did not pick a country (or typed an invalid code), block until a valid code is chosen.
                if (isValid) {
                    var __ccFields = document.querySelectorAll('.country-code-field');
                    for (var __ci = 0; __ci < __ccFields.length; __ci++) {
                        var __cc = __ccFields[__ci];
                        if (__cc.offsetParent === null) continue; // not visible in the current step
                        var __ccWrap = __cc.closest('.country-code-input');
                        if (!__ccWrap) continue;
                        var __opts = __ccWrap.querySelectorAll('.country-option');
                        if (!__opts.length) continue; // fail-safe: nothing to validate against
                        var __ccVal = (__cc.value || '').trim();
                        var __ccOk = false;
                        for (var __oi = 0; __oi < __opts.length; __oi++) {
                            if ((__opts[__oi].getAttribute('data-flag') + ' ' + __opts[__oi].getAttribute('data-code')) === __ccVal) { __ccOk = true; break; }
                        }
                        if (!__ccOk) {
                            __cc.style.borderColor = '#ff6b6b';
                            isValid = false;
                            firstInvalidField = $(__cc);
                            alertMessage = 'Please select a valid country code from the list (search and click your country, or type the full +code in the phone box).';
                            break;
                        }
                        __cc.style.borderColor = '';
                    }
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
            function showInfoTooltipModal(title, description) {
                const modalElement = document.getElementById('infoTooltipModal');
                if (!modalElement) {
                    return;
                }

                const titleElement = modalElement.querySelector('.modal-title');
                const bodyElement = modalElement.querySelector('.modal-body');

                if (titleElement) {
                    titleElement.textContent = title || 'Details';
                }

                if (bodyElement) {
                    bodyElement.innerHTML = `<p style="margin:0;">${description || ''}</p>`;
                }

                if (window.bootstrap && window.bootstrap.Modal) {
                    window.bootstrap.Modal.getOrCreateInstance(modalElement).show();
                    return;
                }

                if (window.jQuery && window.jQuery.fn && window.jQuery.fn.modal) {
                    window.jQuery(modalElement).modal('show');
                }
            }

            function openModal() {
                // Get the description from the clicked addon
                const description = event.target.closest('.addon-item').querySelector('label').getAttribute('data-description');
                const title = event.target.closest('.addon-item').querySelector('label').getAttribute('data-title');

                showInfoTooltipModal(title, description);

            }

            function openPackageModal(triggerElement) {
                const trigger = triggerElement || (window.event ? window.event.target : null);
                if (!trigger || !trigger.closest) {
                    return;
                }

                const card = trigger.closest('.vip-card');
                if (!card) {
                    return;
                }

                const legacyMeta = card.querySelector('.items');
                const title = (
                    (legacyMeta && legacyMeta.getAttribute('data-title')) ||
                    (card.querySelector('.cv-pkg-title') && card.querySelector('.cv-pkg-title').textContent) ||
                    'Package Details'
                ).trim();
                const description = (
                    (legacyMeta && legacyMeta.getAttribute('data-description')) ||
                    (card.querySelector('.cv-pkg-desc') && card.querySelector('.cv-pkg-desc').textContent) ||
                    'No additional details available for this package.'
                ).trim();

                showInfoTooltipModal(title, description);
            }

            document.addEventListener('click', function(evt) {
                const tooltipTrigger = evt.target.closest('.cv-pkg-tooltip-trigger');
                if (!tooltipTrigger) {
                    return;
                }

                evt.preventDefault();
                evt.stopPropagation();
                openPackageModal(tooltipTrigger);
            });

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

                // Check SMS consent checkbox for package form
                const smsConsentPackage = document.getElementById('smsConsent');
                if (!smsConsentPackage || !smsConsentPackage.checked) {
                    e.preventDefault();
                    const errorMsg = document.getElementById('validation-error-msg-package') || document.createElement('div');
                    if (!errorMsg.id) {
                        errorMsg.id = 'validation-error-msg-package';
                        errorMsg.style.cssText = 'color: #ff6b6b; padding: 12px; margin: 10px 0; font-weight: 600; text-align: center; background: rgba(255, 107, 107, 0.1); border-radius: 6px; border-left: 4px solid #ff6b6b;';
                        this.parentElement.insertBefore(errorMsg, this);
                    }
                    errorMsg.textContent = 'Please agree to receive SMS communications regarding your reservation, transportation updates, VIP services, and related notifications.';
                    errorMsg.style.display = 'block';
                    errorMsg.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return;
                }

                // Check terms consent checkbox for package form
                const termsConsentPackage = document.getElementById('termsConsent');
                if (!termsConsentPackage || !termsConsentPackage.checked) {
                    e.preventDefault();
                    const errorMsg = document.getElementById('validation-error-msg-package') || document.createElement('div');
                    if (!errorMsg.id) {
                        errorMsg.id = 'validation-error-msg-package';
                        errorMsg.style.cssText = 'color: #ff6b6b; padding: 12px; margin: 10px 0; font-weight: 600; text-align: center; background: rgba(255, 107, 107, 0.1); border-radius: 6px; border-left: 4px solid #ff6b6b;';
                        this.parentElement.insertBefore(errorMsg, this);
                    }
                    errorMsg.textContent = 'Please accept the Terms of Service.';
                    errorMsg.style.display = 'block';
                    errorMsg.scrollIntoView({ behavior: 'smooth', block: 'center' });
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
                const pickupLocationField = $('[name="transportation_address"]');
                const contactPhoneField = $('[name="transportation_phone"]');
                const pickupTime = pickupTimeField.val().trim();
                const pickupLocation = pickupLocationField.val().trim();
                const contactPhone = contactPhoneField.val().trim();

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

                if (!pickupLocation) {
                    pickupLocationField.addClass('required-field');
                    return {
                        valid: false,
                        field: pickupLocationField,
                        message: 'Please enter the pick-up location.'
                    };
                }

                if (!contactPhone) {
                    contactPhoneField.addClass('required-field');
                    return {
                        valid: false,
                        field: contactPhoneField,
                        message: 'Please enter your contact phone number.'
                    };
                }

                return { valid: true, field: null, message: '' };
            }

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
                if (typeof flatpickr === 'undefined') {
                    el.type = 'time';
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
                    dateFormat: 'h:i K',
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

                        // Replace visible phone fields with E.164 values before submission
                        const phoneFieldsToSync = [
                            { visible: 'package_phone', e164: 'package_phone_e164' },
                            { visible: 'reservation_phone', e164: 'reservation_phone_e164' },
                            { visible: 'transportation_phone', e164: 'transportation_phone_e164' }
                        ];

                        phoneFieldsToSync.forEach(pair => {
                            const e164Field = form.querySelector(`input[name="${pair.e164}"]`);
                            const visibleField = form.querySelector(`input[name="${pair.visible}"]`);
                            if (e164Field && visibleField && e164Field.value) {
                                // Use E.164 format for submission
                                visibleField.value = e164Field.value;
                            }
                        });

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
                        // Desktop: the sidebar must live inside the checkout grid so it renders
                        // in the right column. Force it back regardless of the captured parent.
                        var grid = document.getElementById('cv-checkout-layout');
                        if (grid && sidebar.parentNode !== grid) {
                            grid.appendChild(sidebar);
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

            document.addEventListener('click', function(event) {
                const mapBtn = event.target.closest('.cv-hero-location-map-btn');
                if (!mapBtn) return;

                const location = mapBtn.getAttribute('data-location');
                if (location) {
                    window.open('https://www.google.com/maps/search/' + location, '_blank');
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
                var shareContainer = document.getElementById('shareLinkContainer');
                if (cartSection) sidebarBody.appendChild(cartSection);
                if (pricingShell) sidebarBody.appendChild(pricingShell);
                if (shareContainer) sidebarBody.appendChild(shareContainer);

                // Move the promo code section to AFTER the deposit box so it sits below the Due Today box.
                var depositBox = document.getElementById('cv-deposit-box');
                var promoCol = pricingShell ? pricingShell.querySelector('.dynamic-price.col-md-6') : null;
                if (depositBox && promoCol && depositBox.parentNode) {
                    depositBox.parentNode.insertBefore(promoCol, depositBox.nextSibling);
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

                // Deposit display is updated directly in calculateCartTotal â€” no observer needed.
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

                if (typeof updateReservationSteps === 'function') updateReservationSteps();
            }
            window.updateCheckoutSteps = updateCheckoutSteps;

            // Reservation (guest) flow has its own 3-step indicator: Choose Date -> Your Details -> Submit
            function updateReservationSteps() {
                var stepEls = [
                    document.getElementById('cv-rstep-1'),
                    document.getElementById('cv-rstep-2'),
                    document.getElementById('cv-rstep-3')
                ];
                if (!stepEls[0]) return;

                stepEls.forEach(function(s) {
                    if (s) s.classList.remove('is-active', 'is-complete');
                });

                var dateInput = document.getElementById('package_use_date');
                var dateDone = !!(dateInput && dateInput.value && dateInput.value.trim() !== '');

                var form = document.querySelector('.guest form');
                var detailsDone = !!form;
                ['reservation_first_name', 'reservation_last_name', 'reservation_phone', 'reservation_email'].forEach(function(n) {
                    var el = form && form.querySelector('[name="' + n + '"]');
                    if (!el || !el.value || el.value.trim() === '') detailsDone = false;
                });

                if (dateDone) stepEls[0].classList.add('is-complete');
                if (dateDone && detailsDone) stepEls[1].classList.add('is-complete');

                if (!dateDone) stepEls[0].classList.add('is-active');
                else if (!detailsDone) stepEls[1].classList.add('is-active');
                else stepEls[2].classList.add('is-active');
            }
            window.updateReservationSteps = updateReservationSteps;

            function initCheckoutSteps() {
                if (!document.getElementById('cv-dstep-1')) return;
                updateCheckoutSteps();

                var dateInput = document.getElementById('package_use_date');
                if (dateInput) {
                    dateInput.addEventListener('change', updateCheckoutSteps);
                    dateInput.addEventListener('input', updateCheckoutSteps);
                }

                document.querySelectorAll('.cv-access-tab').forEach(function(tab) {
                    tab.addEventListener('click', function() {
                        // Toggle is-guest-mode class based on active tab
                        var layout = document.getElementById('cv-checkout-layout');
                        if (layout) {
                            if (this.getAttribute('data-name') === 'guest') {
                                layout.classList.add('is-guest-mode');
                            } else {
                                layout.classList.remove('is-guest-mode');
                            }
                        }
                        setTimeout(updateCheckoutSteps, 0);
                    });
                });

                // Initialize is-guest-mode based on which tab is active on page load
                var activeTab = document.querySelector('.cv-access-tab.is-active');
                if (activeTab && activeTab.getAttribute('data-name') === 'guest') {
                    var layout = document.getElementById('cv-checkout-layout');
                    if (layout) {
                        layout.classList.add('is-guest-mode');
                    }
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

                var guestForm = document.querySelector('.guest form');
                if (guestForm) {
                    guestForm.addEventListener('input', updateReservationSteps);
                    guestForm.addEventListener('change', updateReservationSteps);
                }
                updateReservationSteps();
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

            // Auto-format phone numbers as user types
            function initPhoneFormatters() {
                // DISABLED: Phone formatters conflict with country code picker
                // The country code picker (validateAndFormatPhone) now handles phone validation and formatting
                // This old formatter only worked for US numbers and was causing issues with international numbers
            }

            document.addEventListener('DOMContentLoaded', function() {
                initSidebar();
                initSidebarDateSync();
                initSidebarCta();
                initHamburger();
                initCheckoutSteps();
                initDateNotification();
                initPhoneFormatters();
            });
        })();
        </script>

        <script>
        // ===== COUNTRY CODE PICKER - COMPREHENSIVE SOLUTION =====
        const COUNTRIES_INDEX = [
            { name: 'United States', code: '+1', flag: '🇺🇸' },
            { name: 'Canada', code: '+1', flag: '🇨🇦' },
            { name: 'Afghanistan', code: '+93', flag: '🇦🇫' },
            { name: 'Albania', code: '+355', flag: '🇦🇱' },
            { name: 'Algeria', code: '+213', flag: '🇩🇿' },
            { name: 'Andorra', code: '+376', flag: '🇦🇩' },
            { name: 'Angola', code: '+244', flag: '🇦🇴' },
            { name: 'Argentina', code: '+54', flag: '🇦🇷' },
            { name: 'Armenia', code: '+374', flag: '🇦🇲' },
            { name: 'Australia', code: '+61', flag: '🇦🇺' },
            { name: 'Austria', code: '+43', flag: '🇦🇹' },
            { name: 'Azerbaijan', code: '+994', flag: '🇦🇿' },
            { name: 'Bahamas', code: '+1-242', flag: '🇧🇸' },
            { name: 'Bahrain', code: '+973', flag: '🇧🇭' },
            { name: 'Bangladesh', code: '+880', flag: '🇧🇩' },
            { name: 'Barbados', code: '+1-246', flag: '🇧🇧' },
            { name: 'Belarus', code: '+375', flag: '🇧🇾' },
            { name: 'Belgium', code: '+32', flag: '🇧🇪' },
            { name: 'Belize', code: '+501', flag: '🇧🇿' },
            { name: 'Benin', code: '+229', flag: '🇧🇯' },
            { name: 'Bhutan', code: '+975', flag: '🇧🇹' },
            { name: 'Bolivia', code: '+591', flag: '🇧🇴' },
            { name: 'Bosnia & Herzegovina', code: '+387', flag: '🇧🇦' },
            { name: 'Botswana', code: '+267', flag: '🇧🇼' },
            { name: 'Brazil', code: '+55', flag: '🇧🇷' },
            { name: 'Brunei', code: '+673', flag: '🇧🇳' },
            { name: 'Bulgaria', code: '+359', flag: '🇧🇬' },
            { name: 'Burkina Faso', code: '+226', flag: '🇧🇫' },
            { name: 'Burundi', code: '+257', flag: '🇧🇮' },
            { name: 'Cambodia', code: '+855', flag: '🇰🇭' },
            { name: 'Cameroon', code: '+237', flag: '🇨🇲' },
            { name: 'Cape Verde', code: '+238', flag: '🇨🇻' },
            { name: 'Central African Republic', code: '+236', flag: '🇨🇫' },
            { name: 'Chad', code: '+235', flag: '🇹🇩' },
            { name: 'Chile', code: '+56', flag: '🇨🇱' },
            { name: 'China', code: '+86', flag: '🇨🇳' },
            { name: 'Colombia', code: '+57', flag: '🇨🇴' },
            { name: 'Comoros', code: '+269', flag: '🇰🇲' },
            { name: 'Congo', code: '+242', flag: '🇨🇬' },
            { name: 'Costa Rica', code: '+506', flag: '🇨🇷' },
            { name: 'Croatia', code: '+385', flag: '🇭🇷' },
            { name: 'Cuba', code: '+53', flag: '🇨🇺' },
            { name: 'Cyprus', code: '+357', flag: '🇨🇾' },
            { name: 'Czech Republic', code: '+420', flag: '🇨🇿' },
            { name: 'Denmark', code: '+45', flag: '🇩🇰' },
            { name: 'Djibouti', code: '+253', flag: '🇩🇯' },
            { name: 'Dominica', code: '+1-767', flag: '🇩🇲' },
            { name: 'Dominican Republic', code: '+1-809', flag: '🇩🇴' },
            { name: 'Ecuador', code: '+593', flag: '🇪🇨' },
            { name: 'Egypt', code: '+20', flag: '🇪🇬' },
            { name: 'El Salvador', code: '+503', flag: '🇸🇻' },
            { name: 'Equatorial Guinea', code: '+240', flag: '🇬🇶' },
            { name: 'Eritrea', code: '+291', flag: '🇪🇷' },
            { name: 'Estonia', code: '+372', flag: '🇪🇪' },
            { name: 'Ethiopia', code: '+251', flag: '🇪🇹' },
            { name: 'Fiji', code: '+679', flag: '🇫🇯' },
            { name: 'Finland', code: '+358', flag: '🇫🇮' },
            { name: 'France', code: '+33', flag: '🇫🇷' },
            { name: 'Gabon', code: '+241', flag: '🇬🇦' },
            { name: 'Gambia', code: '+220', flag: '🇬🇲' },
            { name: 'Georgia', code: '+995', flag: '🇬🇪' },
            { name: 'Germany', code: '+49', flag: '🇩🇪' },
            { name: 'Ghana', code: '+233', flag: '🇬🇭' },
            { name: 'Greece', code: '+30', flag: '🇬🇷' },
            { name: 'Grenada', code: '+1-473', flag: '🇬🇩' },
            { name: 'Guatemala', code: '+502', flag: '🇬🇹' },
            { name: 'Guinea', code: '+224', flag: '🇬🇳' },
            { name: 'Guinea-Bissau', code: '+245', flag: '🇬🇼' },
            { name: 'Guyana', code: '+592', flag: '🇬🇾' },
            { name: 'Haiti', code: '+509', flag: '🇭🇹' },
            { name: 'Honduras', code: '+504', flag: '🇭🇳' },
            { name: 'Hong Kong', code: '+852', flag: '🇭🇰' },
            { name: 'Hungary', code: '+36', flag: '🇭🇺' },
            { name: 'Iceland', code: '+354', flag: '🇮🇸' },
            { name: 'India', code: '+91', flag: '🇮🇳' },
            { name: 'Indonesia', code: '+62', flag: '🇮🇩' },
            { name: 'Iran', code: '+98', flag: '🇮🇷' },
            { name: 'Iraq', code: '+964', flag: '🇮🇶' },
            { name: 'Ireland', code: '+353', flag: '🇮🇪' },
            { name: 'Israel', code: '+972', flag: '🇮🇱' },
            { name: 'Italy', code: '+39', flag: '🇮🇹' },
            { name: 'Jamaica', code: '+1-876', flag: '🇯🇲' },
            { name: 'Japan', code: '+81', flag: '🇯🇵' },
            { name: 'Jordan', code: '+962', flag: '🇯🇴' },
            { name: 'Kazakhstan', code: '+7', flag: '🇰🇿' },
            { name: 'Kenya', code: '+254', flag: '🇰🇪' },
            { name: 'Kiribati', code: '+686', flag: '🇰🇮' },
            { name: 'Kosovo', code: '+383', flag: '🇽🇰' },
            { name: 'Kuwait', code: '+965', flag: '🇰🇼' },
            { name: 'Kyrgyzstan', code: '+996', flag: '🇰🇬' },
            { name: 'Laos', code: '+856', flag: '🇱🇦' },
            { name: 'Latvia', code: '+371', flag: '🇱🇻' },
            { name: 'Lebanon', code: '+961', flag: '🇱🇧' },
            { name: 'Lesotho', code: '+266', flag: '🇱🇸' },
            { name: 'Liberia', code: '+231', flag: '🇱🇷' },
            { name: 'Libya', code: '+218', flag: '🇱🇾' },
            { name: 'Liechtenstein', code: '+423', flag: '🇱🇮' },
            { name: 'Lithuania', code: '+370', flag: '🇱🇹' },
            { name: 'Luxembourg', code: '+352', flag: '🇱🇺' },
            { name: 'Macau', code: '+853', flag: '🇲🇴' },
            { name: 'Madagascar', code: '+261', flag: '🇲🇬' },
            { name: 'Malawi', code: '+265', flag: '🇲🇼' },
            { name: 'Malaysia', code: '+60', flag: '🇲🇾' },
            { name: 'Maldives', code: '+960', flag: '🇲🇻' },
            { name: 'Mali', code: '+223', flag: '🇲🇱' },
            { name: 'Malta', code: '+356', flag: '🇲🇹' },
            { name: 'Marshall Islands', code: '+692', flag: '🇲🇭' },
            { name: 'Mauritania', code: '+222', flag: '🇲🇷' },
            { name: 'Mauritius', code: '+230', flag: '🇲🇺' },
            { name: 'Mexico', code: '+52', flag: '🇲🇽' },
            { name: 'Micronesia', code: '+691', flag: '🇫🇲' },
            { name: 'Moldova', code: '+373', flag: '🇲🇩' },
            { name: 'Monaco', code: '+377', flag: '🇲🇨' },
            { name: 'Mongolia', code: '+976', flag: '🇲🇳' },
            { name: 'Montenegro', code: '+382', flag: '🇲🇪' },
            { name: 'Morocco', code: '+212', flag: '🇲🇦' },
            { name: 'Mozambique', code: '+258', flag: '🇲🇿' },
            { name: 'Myanmar', code: '+95', flag: '🇲🇲' },
            { name: 'Namibia', code: '+264', flag: '🇳🇦' },
            { name: 'Nauru', code: '+674', flag: '🇳🇷' },
            { name: 'Nepal', code: '+977', flag: '🇳🇵' },
            { name: 'Netherlands', code: '+31', flag: '🇳🇱' },
            { name: 'New Zealand', code: '+64', flag: '🇳🇿' },
            { name: 'Nicaragua', code: '+505', flag: '🇳🇮' },
            { name: 'Niger', code: '+227', flag: '🇳🇪' },
            { name: 'Nigeria', code: '+234', flag: '🇳🇬' },
            { name: 'North Korea', code: '+850', flag: '🇰🇵' },
            { name: 'North Macedonia', code: '+389', flag: '🇲🇰' },
            { name: 'Norway', code: '+47', flag: '🇳🇴' },
            { name: 'Oman', code: '+968', flag: '🇴🇲' },
            { name: 'Pakistan', code: '+92', flag: '🇵🇰' },
            { name: 'Palau', code: '+680', flag: '🇵🇼' },
            { name: 'Palestine', code: '+970', flag: '🇵🇸' },
            { name: 'Panama', code: '+507', flag: '🇵🇦' },
            { name: 'Papua New Guinea', code: '+675', flag: '🇵🇬' },
            { name: 'Paraguay', code: '+595', flag: '🇵🇾' },
            { name: 'Peru', code: '+51', flag: '🇵🇪' },
            { name: 'Philippines', code: '+63', flag: '🇵🇭' },
            { name: 'Poland', code: '+48', flag: '🇵🇱' },
            { name: 'Portugal', code: '+351', flag: '🇵🇹' },
            { name: 'Qatar', code: '+974', flag: '🇶🇦' },
            { name: 'Romania', code: '+40', flag: '🇷🇴' },
            { name: 'Russia', code: '+7', flag: '🇷🇺' },
            { name: 'Rwanda', code: '+250', flag: '🇷🇼' },
            { name: 'Saint Kitts & Nevis', code: '+1-869', flag: '🇰🇳' },
            { name: 'Saint Lucia', code: '+1-758', flag: '🇱🇨' },
            { name: 'Saint Vincent & Grenadines', code: '+1-784', flag: '🇻🇨' },
            { name: 'Samoa', code: '+685', flag: '🇼🇸' },
            { name: 'San Marino', code: '+378', flag: '🇸🇲' },
            { name: 'Sao Tome & Principe', code: '+239', flag: '🇸🇹' },
            { name: 'Saudi Arabia', code: '+966', flag: '🇸🇦' },
            { name: 'Senegal', code: '+221', flag: '🇸🇳' },
            { name: 'Serbia', code: '+381', flag: '🇷🇸' },
            { name: 'Seychelles', code: '+248', flag: '🇸🇨' },
            { name: 'Sierra Leone', code: '+232', flag: '🇸🇱' },
            { name: 'Singapore', code: '+65', flag: '🇸🇬' },
            { name: 'Slovakia', code: '+421', flag: '🇸🇰' },
            { name: 'Slovenia', code: '+386', flag: '🇸🇮' },
            { name: 'Solomon Islands', code: '+677', flag: '🇸🇧' },
            { name: 'Somalia', code: '+252', flag: '🇸🇴' },
            { name: 'South Africa', code: '+27', flag: '🇿🇦' },
            { name: 'South Korea', code: '+82', flag: '🇰🇷' },
            { name: 'South Sudan', code: '+211', flag: '🇸🇸' },
            { name: 'Spain', code: '+34', flag: '🇪🇸' },
            { name: 'Sri Lanka', code: '+94', flag: '🇱🇰' },
            { name: 'Sudan', code: '+249', flag: '🇸🇩' },
            { name: 'Suriname', code: '+597', flag: '🇸🇷' },
            { name: 'Sweden', code: '+46', flag: '🇸🇪' },
            { name: 'Switzerland', code: '+41', flag: '🇨🇭' },
            { name: 'Syria', code: '+963', flag: '🇸🇾' },
            { name: 'Taiwan', code: '+886', flag: '🇹🇼' },
            { name: 'Tajikistan', code: '+992', flag: '🇹🇯' },
            { name: 'Tanzania', code: '+255', flag: '🇹🇿' },
            { name: 'Thailand', code: '+66', flag: '🇹🇭' },
            { name: 'Timor-Leste', code: '+670', flag: '🇹🇱' },
            { name: 'Togo', code: '+228', flag: '🇹🇬' },
            { name: 'Tonga', code: '+676', flag: '🇹🇴' },
            { name: 'Trinidad & Tobago', code: '+1-868', flag: '🇹🇹' },
            { name: 'Tunisia', code: '+216', flag: '🇹🇳' },
            { name: 'Turkey', code: '+90', flag: '🇹🇷' },
            { name: 'Turkmenistan', code: '+993', flag: '🇹🇲' },
            { name: 'Tuvalu', code: '+688', flag: '🇹🇻' },
            { name: 'Uganda', code: '+256', flag: '🇺🇬' },
            { name: 'Ukraine', code: '+380', flag: '🇺🇦' },
            { name: 'United Arab Emirates', code: '+971', flag: '🇦🇪' },
            { name: 'United Kingdom', code: '+44', flag: '🇬🇧' },
            { name: 'Uruguay', code: '+598', flag: '🇺🇾' },
            { name: 'Uzbekistan', code: '+998', flag: '🇺🇿' },
            { name: 'Vanuatu', code: '+678', flag: '🇻🇺' },
            { name: 'Vatican City', code: '+379', flag: '🇻🇦' },
            { name: 'Venezuela', code: '+58', flag: '🇻🇪' },
            { name: 'Vietnam', code: '+84', flag: '🇻🇳' },
            { name: 'Yemen', code: '+967', flag: '🇾🇪' },
            { name: 'Zambia', code: '+260', flag: '🇿🇲' },
            { name: 'Zimbabwe', code: '+263', flag: '🇿🇼' }
        ];

        const PHONE_LENGTH_REQUIREMENTS_INDEX = {
            '+1': { min: 10, max: 10 },
            '+880': { min: 10, max: 11 },
            '+44': { min: 9, max: 11 },
            '+33': { min: 9, max: 9 },
            '+49': { min: 9, max: 11 },
            '+39': { min: 9, max: 11 },
            '+34': { min: 9, max: 9 },
            '+31': { min: 9, max: 9 },
            '+41': { min: 9, max: 9 },
            '+43': { min: 9, max: 10 },
            '+46': { min: 9, max: 9 },
            '+47': { min: 8, max: 8 },
            '+45': { min: 8, max: 8 },
            '+358': { min: 9, max: 9 },
            '+353': { min: 9, max: 10 },
            '+32': { min: 9, max: 9 },
            '+86': { min: 11, max: 11 },
            '+81': { min: 10, max: 11 },
            '+82': { min: 10, max: 11 },
            '+91': { min: 10, max: 10 },
            '+62': { min: 10, max: 12 },
            '+60': { min: 9, max: 11 },
            '+66': { min: 9, max: 10 },
            '+65': { min: 8, max: 8 },
            '+61': { min: 9, max: 9 },
            '+64': { min: 9, max: 10 },
            '+27': { min: 9, max: 9 },
            '+55': { min: 10, max: 11 },
            '+52': { min: 10, max: 10 },
            '+54': { min: 10, max: 10 },
            '+56': { min: 9, max: 9 },
            '+57': { min: 10, max: 10 },
            '+51': { min: 9, max: 9 },
            '+84': { min: 9, max: 11 },
            '+855': { min: 8, max: 9 },
            '+663': { min: 9, max: 10 },
            '+95': { min: 9, max: 10 },
            '+970': { min: 9, max: 9 },
            '+972': { min: 9, max: 10 },
            '+966': { min: 9, max: 9 },
            '+971': { min: 9, max: 9 },
            '+973': { min: 8, max: 8 },
            '+974': { min: 8, max: 8 },
            '+965': { min: 8, max: 8 },
        };

        function initCountryCodePickersIndex() {
            const phoneFields = [
                { name: 'package_phone' },
                { name: 'reservation_phone' }
                // Note: transportation_phone is excluded intentionally - it's a simple phone field for driver contact only
            ];

            phoneFields.forEach(field => {
                const input = document.querySelector(`input[name="${field.name}"]`);
                if (input) {
                    setupCountryCodePickerIndex(input, field.name);
                }
            });
        }

        function setupCountryCodePickerIndex(phoneInput, fieldName) {
            if (phoneInput.parentElement.classList.contains('phone-input-wrapper')) {
                return;
            }

            const wrapper = document.createElement('div');
            wrapper.className = 'phone-input-wrapper';

            const countryCodeDiv = document.createElement('div');
            countryCodeDiv.className = 'country-code-input';

            const countryCodeInput = document.createElement('input');
            countryCodeInput.className = 'country-code-field';
            countryCodeInput.type = 'text';
            countryCodeInput.placeholder = '🇺🇸 +1';
            countryCodeInput.name = `${fieldName}_country`;
            countryCodeInput.setAttribute('data-phone-field', fieldName);
            countryCodeInput.setAttribute('autocomplete', 'off');

            const dropdown = document.createElement('div');
            dropdown.className = 'country-code-dropdown';

            COUNTRIES_INDEX.forEach(country => {
                const option = document.createElement('div');
                option.className = 'country-option';
                option.innerHTML = `<span class="flag-icon">${country.flag}</span>${country.code} ${country.name}`;
                option.setAttribute('data-code', country.code);
                option.setAttribute('data-flag', country.flag);
                option.addEventListener('click', () => selectCountryIndex(countryCodeInput, option, country, phoneInput));
                dropdown.appendChild(option);
            });

            countryCodeDiv.appendChild(countryCodeInput);
            countryCodeDiv.appendChild(dropdown);

            const usOption = COUNTRIES_INDEX.find(c => c.code === '+1' && c.name === 'United States');
            if (usOption) {
                countryCodeInput.value = `${usOption.flag} ${usOption.code}`;
                countryCodeInput.dataset.code = usOption.code;
            }

            phoneInput.parentElement.insertBefore(wrapper, phoneInput);
            wrapper.appendChild(countryCodeDiv);
            wrapper.appendChild(phoneInput);

            countryCodeInput.addEventListener('click', () => {
                dropdown.classList.add('active');
                countryCodeInput.select();
            });

            // Close the list whenever focus leaves the field (tab / enter / click away),
            // but not when the blur is caused by clicking an option inside the list.
            dropdown.addEventListener('mousedown', () => { dropdown.dataset.keepOpen = '1'; });
            countryCodeInput.addEventListener('blur', () => {
                if (dropdown.dataset.keepOpen === '1') { dropdown.dataset.keepOpen = ''; return; }
                dropdown.classList.remove('active');
            });

            countryCodeInput.addEventListener('input', (e) => {
                dropdown.classList.add('active');
                const searchValue = e.target.value.toLowerCase();
                const options = dropdown.querySelectorAll('.country-option');
                options.forEach(option => {
                    const text = option.textContent.toLowerCase();
                    option.style.display = text.includes(searchValue) ? 'block' : 'none';
                });
            });

            document.addEventListener('click', (e) => {
                if (!countryCodeDiv.contains(e.target)) {
                    dropdown.classList.remove('active');
                }
            });

            phoneInput.addEventListener('input', () => {
                validateAndFormatPhoneIndex(phoneInput, countryCodeInput);
            });

            phoneInput.addEventListener('blur', () => {
                validateAndFormatPhoneIndex(phoneInput, countryCodeInput);
            });
        }

        function selectCountryIndex(countryCodeInput, optionEl, country, phoneInput) {
            countryCodeInput.value = `${country.flag} ${country.code}`;
            countryCodeInput.dataset.code = country.code;

            const dropdown = countryCodeInput.nextElementSibling;
            dropdown.querySelectorAll('.country-option').forEach(opt => {
                opt.classList.remove('selected');
            });
            optionEl.classList.add('selected');
            dropdown.classList.remove('active');

            validateAndFormatPhoneIndex(phoneInput, countryCodeInput);
        }

        function formatPhoneNumberIndex(digits, countryCode) {
            if (countryCode === '+1' || countryCode === '+7') {
                if (digits.length <= 3) return digits;
                if (digits.length <= 6) return `(${digits.slice(0, 3)}) ${digits.slice(3)}`;
                return `(${digits.slice(0, 3)}) ${digits.slice(3, 6)}-${digits.slice(6)}`;
            } else if (countryCode === '+44') {
                if (digits.length <= 4) return digits;
                if (digits.length <= 7) return `${digits.slice(0, 4)} ${digits.slice(4)}`;
                return `${digits.slice(0, 4)} ${digits.slice(4, 7)} ${digits.slice(7)}`;
            } else if (countryCode === '+880') {
                if (digits.length <= 4) return digits;
                return `${digits.slice(0, 4)} ${digits.slice(4)}`;
            } else {
                if (digits.length <= 4) return digits;
                let formatted = '';
                for (let i = 0; i < digits.length; i += 4) {
                    if (formatted) formatted += ' ';
                    formatted += digits.slice(i, i + 4);
                }
                return formatted;
            }
        }

        // Detect the country whose dial code is the longest prefix of the typed digits.
        function detectCountryFromDigitsIndex(digits) {
            if (!digits) return null;
            let best = null;
            let bestLen = 0;
            COUNTRIES_INDEX.forEach(function (country) {
                const cc = country.code.replace(/\D/g, '');
                if (cc && digits.startsWith(cc) && cc.length > bestLen) {
                    best = country;
                    bestLen = cc.length;
                }
            });
            return best;
        }

        function validateAndFormatPhoneIndex(phoneInput, countryCodeInput) {
            let phoneValue = phoneInput.value.trim();
            let countryCode = countryCodeInput.dataset.code || '+1';

            // If the user typed a leading "+<country code>" directly into the number box,
            // detect the country, sync the flag/dropdown to it, and strip the code from the
            // national number so the flag and the number stay in sync.
            if (phoneValue.startsWith('+')) {
                const typedDigits = phoneValue.replace(/\D/g, '');
                const detected = detectCountryFromDigitsIndex(typedDigits);
                if (detected) {
                    countryCodeInput.value = `${detected.flag} ${detected.code}`;
                    countryCodeInput.dataset.code = detected.code;
                    countryCode = detected.code;
                    const ccDigits = detected.code.replace(/\D/g, '');
                    const nationalDigits = typedDigits.startsWith(ccDigits) ? typedDigits.substring(ccDigits.length) : typedDigits;
                    phoneInput.value = nationalDigits;
                    phoneValue = nationalDigits;
                } else {
                    // Incomplete country code still being typed (e.g. "+3") — leave it so the
                    // user can finish, and don't format/validate yet.
                    phoneInput.style.borderColor = '';
                    phoneInput.classList.remove('is-invalid', 'is-valid');
                    return;
                }
            }

            const requirements = PHONE_LENGTH_REQUIREMENTS_INDEX[countryCode] || { min: 7, max: 15 };
            phoneInput.dataset.maxDigits = requirements.max;

            if (!phoneValue) {
                phoneInput.style.borderColor = '';
                phoneInput.classList.remove('is-invalid', 'is-valid');
                const hiddenField = document.querySelector(`input[name="${phoneInput.name}_e164"]`);
                if (hiddenField) hiddenField.value = '';
                return;
            }

            let digitsOnly = phoneValue.replace(/\D/g, '');
            const maxDigits = parseInt(phoneInput.dataset.maxDigits || requirements.max);
            if (digitsOnly.length > maxDigits) {
                digitsOnly = digitsOnly.substring(0, maxDigits);
            }

            let cleanNumber = digitsOnly;
            if (countryCode === '+1' && digitsOnly.startsWith('1')) {
                cleanNumber = digitsOnly.substring(1);
            }

            phoneInput.value = formatPhoneNumberIndex(cleanNumber, countryCode);

            if (cleanNumber.length < requirements.min || cleanNumber.length > requirements.max) {
                phoneInput.style.borderColor = '#ff6b6b';
                phoneInput.classList.add('is-invalid');
                phoneInput.classList.remove('is-valid');
                return;
            }

            const e164Number = countryCode + cleanNumber;

            if (!/^\+\d{7,15}$/.test(e164Number)) {
                phoneInput.style.borderColor = '#ff6b6b';
                phoneInput.classList.add('is-invalid');
                phoneInput.classList.remove('is-valid');
                return;
            }

            phoneInput.style.borderColor = '#51cf66';
            phoneInput.classList.remove('is-invalid');
            phoneInput.classList.add('is-valid');

            let hiddenField = document.querySelector(`input[name="${phoneInput.name}_e164"]`);
            if (!hiddenField) {
                hiddenField = document.createElement('input');
                hiddenField.type = 'hidden';
                hiddenField.name = `${phoneInput.name}_e164`;
                phoneInput.parentElement.appendChild(hiddenField);
            }
            hiddenField.value = e164Number;
        }

        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                initCountryCodePickersIndex();
            }, 500);
        });
        </script>

    <script>
    (function () {
        // AJAX checkout/reservation submit: keep exact page state on failure and show
        // inline error; keep normal success redirect behavior.
        function isCheckoutForm(form) {
            var a = (form.getAttribute('action') || '');
            return a.indexOf('/checkout/store') !== -1
                || a.indexOf('/reservation/store') !== -1
                || a.indexOf('/reservations/store') !== -1;
        }

        function captureCheckoutState() {
            var activeSection = document.querySelector('.checkout-section.active[id^="section-"]');
            return {
                activeSectionId: activeSection ? activeSection.id : null,
                scrollY: window.pageYOffset || document.documentElement.scrollTop || 0
            };
        }

        function restoreCheckoutState(snapshot) {
            if (!snapshot) return;
            if (snapshot.activeSectionId) {
                var m = /^section-(\d+)$/.exec(snapshot.activeSectionId);
                if (m && typeof window.showStep === 'function') {
                    var step = parseInt(m[1], 10);
                    if (Number.isFinite(step)) {
                        try { window.showStep(step); } catch (e) {}
                    }
                }
            }
            try { window.scrollTo(0, snapshot.scrollY || 0); } catch (e) {}
        }

        function restoreButtons() {
            try { if (typeof hideCheckoutProcessingOverlay === 'function') hideCheckoutProcessingOverlay(); } catch (e) {}
            var overlay = document.getElementById('checkout-processing-overlay');
            if (overlay) { overlay.classList.remove('is-visible'); overlay.setAttribute('aria-hidden', 'true'); }
            ['submitBtn', 'submitBtn_two'].forEach(function (id) {
                var b = document.getElementById(id);
                if (b) {
                    b.disabled = false;
                    if (b.dataset && b.dataset.defaultText) { b.textContent = b.dataset.defaultText; }
                }
            });
        }

        function showCheckoutError(form, message, snapshot) {
            restoreButtons();
            restoreCheckoutState(snapshot);

            var prev = document.getElementById('cv-ajax-error-alert');
            if (prev && prev.parentNode) prev.parentNode.removeChild(prev);

            var alertEl = document.createElement('div');
            alertEl.className = 'alert alert-danger';
            alertEl.setAttribute('role', 'alert');
            alertEl.id = 'cv-ajax-error-alert';
            alertEl.textContent = message || 'Something went wrong. Please try again.';

            var mount = form.closest('.checkout-section.active')
                || form.closest('.checkout-section')
                || form;
            mount.insertBefore(alertEl, mount.firstChild);
        }

        function extractError(json) {
            if (!json) return null;
            if (json.error) return json.error;
            if (json.errors && typeof json.errors === 'object') {
                var keys = Object.keys(json.errors);
                if (keys.length) {
                    var v = json.errors[keys[0]];
                    return Array.isArray(v) ? v[0] : v;
                }
            }
            return json.message || null;
        }

        function submitCheckoutAjax(form) {
            if (form.dataset.ajaxSubmitting === '1') return;
            form.dataset.ajaxSubmitting = '1';

            var snapshot = captureCheckoutState();
            if (typeof showCheckoutProcessingOverlay === 'function') { try { showCheckoutProcessingOverlay(); } catch (e) {} }

            var tokens = form.querySelectorAll('input[name="stripeToken"]');
            for (var i = 0; i < tokens.length - 1; i++) {
                if (tokens[i].parentNode) tokens[i].parentNode.removeChild(tokens[i]);
            }

            fetch(form.getAttribute('action'), {
                method: 'POST',
                body: new FormData(form),
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                credentials: 'same-origin'
            }).then(function (res) {
                return res.text().then(function (t) {
                    var json = null;
                    try { json = JSON.parse(t); } catch (e) {}
                    return { ok: res.ok, json: json };
                });
            }).then(function (result) {
                if (result.json && result.json.success && result.json.redirect) {
                    window.location.href = result.json.redirect;
                    return;
                }
                form.dataset.ajaxSubmitting = '0';
                showCheckoutError(form, extractError(result.json), snapshot);
            }).catch(function () {
                form.dataset.ajaxSubmitting = '0';
                showCheckoutError(form, 'Network error. Please check your connection and try again.', snapshot);
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            Array.prototype.forEach.call(document.querySelectorAll('form'), function (form) {
                if (!isCheckoutForm(form)) return;
                if (form.dataset.ajaxCheckoutBound === '1') return;
                form.dataset.ajaxCheckoutBound = '1';

                form.submit = function () { submitCheckoutAjax(form); };
                form.addEventListener('submit', function (e) {
                    if (e.defaultPrevented) return;
                    e.preventDefault();
                    submitCheckoutAjax(form);
                });
            });
        });
    })();
    </script>

    <script>
    (function () {
        // Checkout UX: Enter advances to the next field instead of submitting the form.
        document.addEventListener('keydown', function (e) {
            if (e.key !== 'Enter' && e.keyCode !== 13) return;
            if (e.defaultPrevented) return; // a field-specific handler already dealt with Enter
            var el = e.target;
            if (!el) return;
            var tag = (el.tagName || '').toLowerCase();
            var type = ((el.getAttribute && el.getAttribute('type')) || '').toLowerCase();
            if (tag === 'textarea' || tag === 'button' || tag === 'a') return; // keep normal behavior
            if (type === 'submit' || type === 'button') return;
            var form = el.form || (el.closest ? el.closest('form') : null);
            if (!form) return; // only intercept fields inside a form

            e.preventDefault(); // stop the implicit form submission

            var fields = Array.prototype.filter.call(
                form.querySelectorAll('input, select, textarea, button'),
                function (node) {
                    if (node.disabled || node.type === 'hidden' || node.tabIndex === -1) return false;
                    return node.offsetParent !== null || node.getClientRects().length > 0;
                }
            );
            var idx = fields.indexOf(el);
            if (idx > -1 && idx < fields.length - 1) {
                var next = fields[idx + 1];
                next.focus();
                if (typeof next.select === 'function') { try { next.select(); } catch (err) {} }
            } else if (typeof el.blur === 'function') {
                el.blur();
            }
        });
    })();
    </script>

    <script>
    (function () {
        if (!document.body.classList.contains('embed-checkout-mode')) return;
        if (window.top === window.self) return;

        function postEmbedHeight() {
            var doc = document.documentElement;
            var body = document.body;
            var height = Math.max(
                doc ? doc.scrollHeight : 0,
                body ? body.scrollHeight : 0,
                doc ? doc.offsetHeight : 0,
                body ? body.offsetHeight : 0,
                doc ? doc.clientHeight : 0
            );
            window.parent.postMessage({ type: 'checkoutEmbedHeight', height: height }, '*');
        }

        var rafId = null;
        function queueEmbedHeightSync() {
            if (rafId) {
                cancelAnimationFrame(rafId);
            }
            rafId = requestAnimationFrame(postEmbedHeight);
        }

        window.addEventListener('load', queueEmbedHeightSync);
        window.addEventListener('resize', queueEmbedHeightSync);
        window.addEventListener('orientationchange', queueEmbedHeightSync);
        document.addEventListener('click', function () { setTimeout(queueEmbedHeightSync, 120); });
        document.addEventListener('input', function () { setTimeout(queueEmbedHeightSync, 120); });

        if ('ResizeObserver' in window) {
            var resizeObserver = new ResizeObserver(queueEmbedHeightSync);
            if (document.documentElement) resizeObserver.observe(document.documentElement);
            if (document.body) resizeObserver.observe(document.body);
        }

        setTimeout(queueEmbedHeightSync, 50);
        setTimeout(queueEmbedHeightSync, 250);
        setTimeout(queueEmbedHeightSync, 900);
    })();
    </script>
    </body>

    </html>




