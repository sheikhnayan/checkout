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
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #444;
    color: #fff;
    line-height: 1;
    font-weight: bold;
    margin: 0 auto 0.5rem;
    border: 2px solid #444;
}

.step.active .step-number {
    background: {{ $brandPrimary }};
    border-color: {{ $brandPrimary }};
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
        padding: 12px 40px 12px 15px !important;
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
.checkbox-container label {
    display: flex;
    gap: 10px;
    align-items: flex-start;
    cursor: pointer;
    margin-bottom: 10px;
    font-size: 13px;
    line-height: 1.4;
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
    background: rgba(255,255,255,0.04) !important;
    border: 1px solid rgba(255,255,255,0.1) !important;
    border-radius: 12px !important;
    padding: 16px 18px;
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
    background: var(--accent) !important;
    background: var(--bg) !important;
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

.hero-date-card label {
    margin-bottom: 3px;
    text-transform: uppercase;
    font-size: 9px;
    letter-spacing: .8px;
    opacity: .7;
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
    color: #d8def0;
    font-size: 13px;
    font-weight: 600;
}

.event-cart-capacity-banner.sold-out {
    border-color: rgba(255, 120, 120, 0.45);
    color: #ffb4b4;
}

.vip-btn[disabled] {
    opacity: .58;
    cursor: not-allowed;
}

.vip-availability-note {
    margin-top: 6px;
    font-size: 11px;
    opacity: .72;
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
}

.flatpickr-calendar .flatpickr-current-month .flatpickr-monthDropdown-months option {
    background: #0f172a !important;
    color: #e2e8f0 !important;
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
    object-fit: cover;
    border-radius: 12px;
    border: 1px solid rgba(255,255,255,0.15);
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

.package-category-tile.active {
    background: var(--accent) !important;
    color: #000 !important;
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
    gap: 12px;
    padding: 10px 0;
    border-bottom: 1px solid rgba(255,255,255,0.12);
}

#addonSelectionModal .addon-modal-label {
    display: inline-block;
    color: #e8eaf6 !important;
    font-size: 14px;
}

#addonSelectionModal .addon-switch {
    position: relative;
    display: inline-block;
    width: 46px;
    height: 26px;
    flex-shrink: 0;
}

#addonSelectionModal .addon-modal-switch-input {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}

#addonSelectionModal .addon-switch-slider {
    position: absolute;
    inset: 0;
    cursor: pointer;
    background: rgba(255,255,255,0.18);
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 999px;
    transition: all .2s ease;
}

#addonSelectionModal .addon-switch-slider::before {
    content: '';
    position: absolute;
    width: 20px;
    height: 20px;
    left: 2px;
    top: 2px;
    border-radius: 50%;
    background: #fff;
    transition: transform .2s ease;
}

#addonSelectionModal .addon-modal-switch-input:checked + .addon-switch-slider {
    background: var(--brand-gradient);
    border-color: rgba(247,226,180,0.65);
}

#addonSelectionModal .addon-modal-switch-input:checked + .addon-switch-slider::before {
    transform: translateX(20px);
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

    .guest-count {
        padding: 10px;
    }

    .guest-count h2 {
        font-size: .96rem;
        margin-bottom: 8px;
    }

    .guest-count .guest-section {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 7px 8px;
        margin-bottom: 6px;
        border-radius: 10px;
    }

    .guest-count .label {
        font-size: 13px;
        margin-bottom: 0;
    }

    .guest-count .counter {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .guest-count .count {
        min-width: 28px;
        text-align: center;
        font-size: 14px;
        font-weight: 700;
    }

    .guest-count .btn-gray,
    .guest-count .btn-yellow {
        width: 26px;
        height: 26px;
        min-width: 26px;
        padding: 0;
        line-height: 1;
        border-radius: 8px;
        font-size: 16px;
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
    .event-hero-copy { order: 1; padding: 12px 10px; min-height: 0; }
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

    .hero-gallery-grid { grid-template-columns: 1fr; }
}

        </style>
    </head>

    <body>
        <div class="background-glow"></div>

        <section class="aff-hero">
            <div class="container">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-3">
                        @if ($data->logo)
                            <img src="{{ asset('uploads/' . $data->logo) }}" alt="{{ $data->name }}" class="aff-avatar">
                        @else
                            <div class="aff-initials">{{ strtoupper(substr($data->name, 0, 2)) }}</div>
                        @endif
                        <div>
                            <h2 class="mb-0 aff-hero-title">{{ $data->name }}</h2>
                            @if ($data->location)
                                <p class="mb-0 mt-1 aff-hero-copy">{{ $data->location }}</p>
                            @endif
                        </div>
                    </div>
                    @if ($data->back_link && $data->back_text)
                        <a href="{{ $data->back_link }}" class="back-home-btn"><i class="fas fa-arrow-left"></i><span>{{ $data->back_text }}</span></a>
                    @endif
                </div>
            </div>
        </section>

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

                <section class="event-hero-layout">
                    <div class="event-hero-copy">
                        <div class="aff-kicker">Event Checkout</div>
                        <div class="aff-display-title">{{ $event->hero_title ?: $event->name }}</div>
                        <div class="aff-display-copy">
                            {{ $event->hero_subtitle ?: (\Carbon\Carbon::parse($event->date)->format('l, F d') . ($event->time ? ' � ' . $event->time : '')) }}
                        </div>

                        <div class="hero-date-card">
                            <label>Reservation Date</label>
                            <div class="date-input-wrapper">
                                <input id="package_use_date" type="text" value="{{ \Carbon\Carbon::parse($event->date)->format('l, F d, Y') }}"
                                    readonly style="-webkit-text-fill-color: #fff !important; color: #fff !important; opacity: 1 !important; width: 100%;">
                                <span class="custom-calendar-icon" style="display:none;"></span>
                            </div>
                            @if (!is_null($event->remaining_attendee_capacity))
                                <div id="event-capacity-note" class="hero-capacity-note{{ !empty($event->is_sold_out) ? ' sold-out' : '' }}">
                                    @if (!empty($event->is_sold_out))
                                        Sold out
                                    @else
                                        {{ $event->remaining_attendee_capacity }} spots remaining of {{ $event->attendee_limit }}
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="event-banner-wrap">
                        <section class="aff-banner" style="background:
                            url('{{ asset('uploads/' . $event->image) }}') center/cover no-repeat,
                            #ffcc00;">
                        </section>
                    </div>
                </section>

                @if(!empty($event->gallery_images))
                    <div class="hero-gallery-grid">
                        @foreach((array) $event->gallery_images as $galleryImage)
                            <img src="{{ asset('uploads/' . $galleryImage) }}" alt="Gallery image" class="hero-gallery-item">
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
                            <input type="hidden" name="affiliate_slug" value="{{ $affiliateReferral->slug ?? '' }}">
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
                                            <div class="guest-section" style="border-color: {{ $brandPrimary }} !important;">
                                                <span class="label">Women</span>
                                                <div class="counter">
                                                    <span class="count" id="womenCount">0</span>
                                                    <button class="btn-gray" type="button"
                                                        onclick="decrements('women')">-</button>
                                                    <button class="btn-yellow"
                                                        style="background-color: {{ $brandPrimary }} !important;" type="button"
                                                        onclick="increments('women')">+</button>
                                                </div>
                                            </div>
                                            <div class="guest-section" style="border-color: {{ $brandPrimary }} !important;">
                                                <span class="label">Men</span>
                                                <div class="counter">
                                                    <span class="count" id="menCount">0</span>
                                                    <button class="btn-gray" type="button"
                                                        onclick="decrements('men')">-</button>
                                                    <button class="btn-yellow"
                                                        style="background-color: {{ $brandPrimary }} !important;" type="button"
                                                        onclick="increments('men')">+</button>
                                                </div>
                                            </div>
                                            <div class="guest-section" style="border-color: {{ $brandPrimary }} !important;">
                                                <span class="label">Total Guests</span>
                                                <div class="counter">
                                                    <span class="count" id="totalCount">0</span>
                                                    <button class="btn-gray" type="button" onclick="resets()">-</button>
                                                    <button class="btn-yellow"
                                                        style="background-color: {{ $brandPrimary }} !important;" type="button"
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

                            <section class="location-card" style="width: 100%">
                                <div class="location-shell">
                                    <div class="location-copy">
                                        <span class="location-kicker">Find Us</span>
                                        <h2>Location</h2>
                                        <p class="location-address">{{ $data->location }}</p>
                                        <div class="location-contact-grid">
                                            @if($data->phone)
                                                <a class="location-contact-chip" href="tel:{{ $data->phone }}">
                                                    <i class="fas fa-phone"></i>
                                                    <span>{{ $data->phone }}</span>
                                                </a>
                                            @endif
                                            @if($data->email)
                                                <a class="location-contact-chip" href="mailto:{{ $data->email }}">
                                                    <i class="fas fa-envelope"></i>
                                                    <span>{{ $data->email }}</span>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="location-map-wrap">
                                        <iframe
                                            src="https://www.google.com/maps?q={{ urlencode($data->location) }}&output=embed"
                                            allowfullscreen
                                            loading="lazy"
                                            referrerpolicy="no-referrer-when-downgrade">
                                        </iframe>
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
    
                                    <h5 class="section-kicker-lg">{{ $data->package_button_text ?? 'Packages' }}</h5>
    
                                    @if(isset($packageCategories) && $packageCategories->count())
                                        @php
                                            $sortedPackageCategories = collect($packageCategories)
                                                ->sortBy(function ($category) {
                                                    return strtolower((string) ($category['name'] ?? $category->name ?? ''));
                                                })
                                                ->values();
                                        @endphp
                                        @if (!is_null($event->remaining_attendee_capacity))
                                            <div id="event-cart-capacity-banner" class="event-cart-capacity-banner{{ !empty($event->is_sold_out) ? ' sold-out' : '' }}">
                                                @if (!empty($event->is_sold_out))
                                                    This event is sold out.
                                                @else
                                                    {{ $event->remaining_attendee_capacity }} spots currently available for checkout.
                                                @endif
                                            </div>
                                        @endif
                                        <div class="mb-3 package-category-tiles" style="width:100%;">
                                            @foreach ($sortedPackageCategories as $category)
                                                <button
                                                    type="button"
                                                    class="btn btn-outline-light package-category-tile mb-2 w-100"
                                                    data-target="#category-group-{{ $category['id'] }}"
                                                    style="border-color: {{ $brandPrimary }}; color: {{ $brandPrimary }}; display:flex; justify-content:space-between; align-items:center; text-align:left; padding:14px 16px; border-radius:12px; font-size:15px; font-weight:600;"
                                                >
                                                    {{ $category['name'] }}
                                                    <span style="opacity:.7; font-size:12px;">+</span>
                                                </button>
                                            @endforeach
                                        </div>

                                        @foreach ($sortedPackageCategories as $category)
                                            <div id="category-group-{{ $category['id'] }}" class="package-category-group" style="display: none;">
                                                @foreach ($category['packages'] as $item)
                                                    <div class="vip-card" id="pkg-card-{{ $item->id }}">
                                                        <div class="vip-card-main">
                                                            <div class="vip-title-row">
                                                                <div class="vip-title">{{ $item->name }}</div>
                                                                @if($item->description)
                                                                    <button type="button" class="club-detail-trigger"
                                                                        data-bs-toggle="popover" data-bs-placement="top"
                                                                        data-bs-custom-class="club-popover" data-bs-html="true"
                                                                        data-bs-title="{{ e($item->name) }}"
                                                                        data-bs-content="{{ e(strip_tags($item->description ?? '')) }}"
                                                                    ><i class="fas fa-info"></i></button>
                                                                @endif
                                                            </div>
                                                            <button class="vip-btn btn-{{ $item->id }} mt-2" style="background-color: {{ $brandPrimary }} !important;"
                                                                data-id="{{ $item->id }}" data-name="{{ $item->name }}" data-price="{{ $item->price }}"
                                                                data-gratuity="{{ $data->gratuity_fee }}"
                                                                data-refundable="{{ $data->refundable_fee }}"
                                                                data-sales_tax="{{ $data->sales_tax_fee ?? 10}}"
                                                                data-transportation="{{ $item->transportation }}"
                                                                data-service_charge="{{ $data->service_charge_fee ?? 10}}"
                                                                data-default-label="Add to Cart"
                                                                @disabled(!empty($event->is_sold_out))>{{ !empty($event->is_sold_out) ? 'Sold Out' : 'Add to Cart' }}</button>
                                                        </div>

                                                        <div class="vip-card-side">
                                                            <div class="vip-guest-control">
                                                                <div class="vip-guest-label">Guests</div>
                                                                <select data-multiple="{{ $item->multiple }}" data-id="{{ $item->id }}" class="form-select package_number_of_guestss">
                                                                    @for ($i = 1; $i <= $item->number_of_guest; $i++)
                                                                        <option value="{{ $i }}">{{ $i }}</option>
                                                                    @endfor
                                                                </select>
                                                            </div>
                                                            @if (!is_null($event->remaining_attendee_capacity))
                                                                <div class="vip-availability-note" data-package-availability="{{ $item->id }}">
                                                                    @if (!empty($event->is_sold_out))
                                                                        No spots remaining for this event.
                                                                    @else
                                                                        {{ $event->remaining_attendee_capacity }} spots remain for this event.
                                                                    @endif
                                                                </div>
                                                            @endif
                                                            <div class="vip-price-tag price-{{ $item->id }}" data-price="{{ $item->price }}">${{ number_format((float) $item->price, 2) }}</div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endforeach
                                    @else
                                        <p style="opacity:.6;">No packages are available yet.</p>
                                    @endif
    
                                    <section id="cart-section" class="container py-4" style="display:none; margin-bottom:2rem;">
                                        <div style="font-weight:700;font-size:15px;margin-bottom:10px;">Your Cart</div>
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
                                                style="font-size: 16px; font-weight: 700; color: {{ $brandSecondary }} !important;">
                                                Total: <span>$0.00</span></div>
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
                                                style="color: #808080; font-size: 14px; margin-top: 2rem;">{{ $data->promo_code_name }}</label>
                                            <div class="row">
                                                <div class="col-md-8 col-8" style="padding-right: 0%;">
                                                    <input type="text" id="promo_code" style="color: #fff; border-top-right-radius: 0px !important; border-bottom-right-radius: 0px !important;"
                                                        placeholder="Promo or referral code?" />
                                                </div>
                                                <div class="col-md-4 col-4" style="padding-left: 0%;">
                                                    <button type="button" class="vip-btn-submit"
                                                        style="width: 100%; height: 100%;"
                                                        id="applyPromoBtn">Submit</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
    
                                    <div class="mt-3" id="shareLinkContainer">
                                        <button type="button" id="generateShareLink">Generate Shareable Link</button>
                                        <div style="position: relative;">
                                            <input type="text" id="shareableLink" readonly style="width:100%;margin-top:8px;display:none;padding-right:40px;" />
                                            <div id="copyTooltip" style="position: absolute; top: -35px; right: 0; background: #28a745; color: white; padding: 8px 12px; border-radius: 4px; font-size: 12px; display: none; white-space: nowrap; z-index: 1000;">
                                                URL Copied!
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
    
                                                        <h2 style="margin-bottom: 35px;">Personal details <span style="font-size: 1rem;"> (Gifting? Enter their legal�details�here) </span></h2>
    
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
                                                                        placeholder="For driver/dispatch to coordinate�pickup"  />
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

                                        <input type="hidden" name="affiliate_slug" value="{{ $affiliateReferral->slug ?? '' }}">
    
                                        <input type="hidden" name="package_number_of_guest" class="package_number_of_guest" value="1">
    
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

                                                            <input type="hidden" class="package_use_date" name="package_use_date" value="{{ \Carbon\Carbon::parse($event->date)->format('Y-m-d') }}">
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
    
                    <section class="location-card" style="width: 100%">
                        <div class="location-shell">
                            <div class="location-copy">
                                <span class="location-kicker">Find Us</span>
                                <h2>Location</h2>
                                <p class="location-address">{{ $data->location }}</p>
                                <div class="location-contact-grid">
                                    @if($data->phone)
                                        <a class="location-contact-chip" href="tel:{{ $data->phone }}">
                                            <i class="fas fa-phone"></i>
                                            <span>{{ $data->phone }}</span>
                                        </a>
                                    @endif
                                    @if($data->email)
                                        <a class="location-contact-chip" href="mailto:{{ $data->email }}">
                                            <i class="fas fa-envelope"></i>
                                            <span>{{ $data->email }}</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                            <div class="location-map-wrap">
                                <iframe
                                    src="https://www.google.com/maps?q={{ urlencode($data->location) }}&output=embed"
                                    allowfullscreen
                                    loading="lazy"
                                    referrerpolicy="no-referrer-when-downgrade">
                                </iframe>
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
                                    style="border-color: {{ $brandPrimary }} !important; color: {{ $brandPrimary }}; font-size: 14px; padding: 5px;">This
                                    Week</button>
                                <button type="button" class="btn btn-outline-primary event-filter" data-filter="month"
                                    style="border-color: {{ $brandPrimary }} !important; color: {{ $brandPrimary }}; font-size: 14px; padding: 5px;">This
                                    Month</button>
                                <button type="button" class="btn btn-outline-primary event-filter" data-filter="year"
                                    style="border-color: {{ $brandPrimary }} !important; color: {{ $brandPrimary }}; font-size: 14px; padding: 5px;">This
                                    Year</button>
                            </div>
                        </div>
                        <div class="row g-4" id="events-list">
                            @foreach ($data->events as $item)
                                  @if (!$item->is_archieved && \Carbon\Carbon::parse($item->date)->gt(\Carbon\CArbon::now()))
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
                                                        style="width: 50%; color: {{ $brandPrimary }} !important;">
                                                        {{ \Carbon\Carbon::parse($item->date)->format('M') }}<span> <br>
                                                            {{ \Carbon\Carbon::parse($item->date)->format('d') }}</span></div>
                                                </div>
                                                <div class="event-location">{{ $data->location }}</div>
                                                @if (!is_null($item->remaining_attendee_capacity))
                                                    <div class="event-capacity-chip{{ !empty($item->is_sold_out) ? ' sold-out' : '' }}">
                                                        {{ !empty($item->is_sold_out) ? 'Sold Out' : $item->remaining_attendee_capacity . ' Spots Left' }}
                                                    </div>
                                                @else
                                                    <div class="event-location">Reserve</div>
                                                @endif
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

                <div class="modal fade" id="addonSelectionModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content" style="background:#1a1d2e;color:#ddd;">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addonSelectionModalTitle" style="color:#fff;">Select Add-ons</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body" id="addonSelectionModalBody"></div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn" id="addonModalConfirmBtn" style="background:var(--aff-accent);color:#000;font-weight:700;">Confirm & Add to Cart</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </main>
        <footer>
            <p>{{ $data->footer_text }}</p>
        </footer>
        <script src="scripts/main.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

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
                        // Keep event checkout locked to the event date.
                        $('#package_use_date').val("{{ \Carbon\Carbon::parse($event->date)->format('l, F d, Y') }}");
                        $('.package_use_date').val("{{ \Carbon\Carbon::parse($event->date)->format('Y-m-d') }}");
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

            function cartRequiresTransportation() {
                ensureCartArray();
                return window.cart.some(function(pkg) {
                    return pkg.transportation === true || pkg.transportation === 1 || pkg.transportation === '1';
                });
            }

            function syncTransportationStateFromCart() {
                window.requiresTransportation = cartRequiresTransportation();
                const transportationPhoneField = $('input[name="transportation_phone"]');
                if (window.requiresTransportation) {
                    $('#step-2 .step-title').text('Transportation');
                    $('#next-to-transport').text('Next: Transportation Details');
                    transportationPhoneField.prop('required', true).attr('aria-required', 'true');
                } else {
                    $('#step-2 .step-title').text('Confirmation');
                    $('#next-to-transport').text('Next: Transportation Confirmation');
                    transportationPhoneField.prop('required', false).removeClass('required-field').removeAttr('aria-required');
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

            function hasEventCapacityLimit() {
                return window.eventCapacityState && window.eventCapacityState.remaining !== null;
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

            function getAvailableEventSeats(packageId) {
                if (!hasEventCapacityLimit()) {
                    return null;
                }

                return Math.max((parseInt(window.eventCapacityState.remaining, 10) || 0) - getCartAttendeeCount(packageId), 0);
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
                if (!hasEventCapacityLimit()) {
                    return;
                }

                $('.vip-btn').each(function() {
                    var $button = $(this);
                    var packageId = $button.data('id');
                    var guests = parseInt($('.package_number_of_guestss[data-id="' + packageId + '"]').val(), 10) || 1;
                    var availableSeats = getAvailableEventSeats(packageId);
                    var $note = $('[data-package-availability="' + packageId + '"]');

                    if (availableSeats <= 0) {
                        setPackageButtonState($button, true, 'Sold Out');
                        $note.text('No spots remaining for this event.');
                        return;
                    }

                    if (guests > availableSeats) {
                        setPackageButtonState($button, true, 'Only ' + availableSeats + ' Left');
                        $note.text('Only ' + availableSeats + ' spots remain for this event.');
                        return;
                    }

                    setPackageButtonState($button, false, $button.data('default-label') || 'Add to Cart');
                    $note.text(availableSeats + ' spots remain for this event.');
                });

                var availableForCart = getAvailableEventSeats(null);
                var $banner = $('#event-cart-capacity-banner');
                if ($banner.length) {
                    $banner.text(availableForCart <= 0 ? 'This event is sold out.' : availableForCart + ' spots currently available for checkout.');
                    $banner.toggleClass('sold-out', availableForCart <= 0);
                }
            }

            window.addPackageToCart = function(packageId, packageName, packagePrice, guests, addons, transportation, isMultiple) {
                console.log('addPackageToCart called', packageId, packageName);
                ensureCartArray();
                var normalizedGuests = parseInt(guests, 10) || 1;
                var availableSeats = getAvailableEventSeats(packageId);

                if (availableSeats !== null && normalizedGuests > availableSeats) {
                    alert(availableSeats > 0 ? ('Only ' + availableSeats + ' spots remain for this event.') : 'This event is sold out.');
                    syncEventCapacityUi();
                    return false;
                }

                var existing = window.cart.find(p => p.packageId == packageId);
                if (!existing) {
                    window.cart.push({
                        packageId: packageId,
                        packageName: packageName,
                        packagePrice: parseFloat(packagePrice),
                        guests: normalizedGuests,
                        isMultiple: parseMultipleFlag(isMultiple),
                        addons: addons || [],
                        transportation: transportation
                    });
                } else {
                    existing.packageName = packageName;
                    existing.packagePrice = parseFloat(packagePrice);
                    existing.guests = normalizedGuests;
                    existing.isMultiple = parseMultipleFlag(isMultiple);
                    existing.addons = addons || [];
                    existing.transportation = transportation;
                }
                $('#cart-section').show();
                $('#shareLinkContainer').show();
                window.renderCart();
                window.calculateCartTotal();
                syncTransportationStateFromCart();
                syncEventCapacityUi();
                return true;
            };

            window.removePackageFromCart = function(packageId) {
                ensureCartArray();
                window.cart = window.cart.filter(p => p.packageId != packageId);
                if (window.cart.length === 0) {
                    $('#cart-section').hide();
                }
                window.renderCart();
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
                    html += '<div style="border-bottom:1px solid rgba(255,255,255,0.08);padding:8px 0;">';
                    html += '<strong>' + pkg.packageName + '</strong> ' + (parseMultipleFlag(pkg.isMultiple) ? ('&times;' + pkg.guests) : '(flat)') + ' &mdash; <span style="color:var(--accent)">' + formatCurrency(pkg.packagePrice * billableGuests) + '</span>';
                    html += '<button onclick="window.removePackageFromCart(' + pkg.packageId + ')" style="float:right;background:#c00;color:#fff;border:none;border-radius:5px;padding:3px 9px;cursor:pointer;font-size:12px;">Remove</button>';
                    if (pkg.addons.length > 0) {
                        html += '<div style="margin-left:18px;font-size:12px;opacity:.6;">Add-ons: ' + pkg.addons.map(function(a) { return a.name + ' (' + formatCurrency(a.price) + ')'; }).join(', ') + '</div>';
                    }
                    html += '</div>';
                });
                $('#cart-list').html(html);
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

                var service_charge_price = ("{{ $data->service_charge_name }}" != "0") ? (subtotal / 100) * service_charge : 0;
                var sales_tax_price = ("{{ $data->sales_tax_name }}" != "0") ? (subtotal / 100) * sales_tax : 0;
                var gratuited_price = ("{{ $data->gratuity_name }}" != "0") ? ((subtotal + sales_tax_price + service_charge_price) / 100) * gratuity : 0;

                var totalBeforeCoupon = subtotal + service_charge_price + sales_tax_price + gratuited_price;
                var couponDiscount = 0;

                if (window.cartCoupon) {
                    if (window.cartCoupon.type === 'percentage') {
                        couponDiscount = (totalBeforeCoupon / 100) * window.cartCoupon.discount;
                    } else {
                        couponDiscount = window.cartCoupon.discount;
                    }
                }

                var grandTotal = totalBeforeCoupon - couponDiscount;
                var refundableRate = parseFloat($('#refundable').val()) || 0;
                var refundableAmount = (grandTotal / 100) * refundableRate;

                $('.default-package-price span').text(formatCurrency(subtotal));
                $('.default-service-charge span').text(formatCurrency(service_charge_price));
                $('.default-sales-tax span').text(formatCurrency(sales_tax_price));
                $('.default-gratuity span').text(formatCurrency(gratuited_price));

                if (window.cartCoupon && couponDiscount > 0) {
                    if ($('.default-promo-discount').length === 0) {
                        $('.default-gratuity').after('<div style="font-size: 12px;" class="default-promo-discount">Promo Code Discount: <span>$0.00</span></div>');
                    }
                    $('.default-promo-discount span').text('-' + formatCurrency(couponDiscount));
                } else {
                    $('.default-promo-discount').remove();
                }

                $('#cart-total').html('Subtotal: ' + formatCurrency(subtotal) + '<br>Service Charge: ' + formatCurrency(service_charge_price) + '<br>Sales Tax: ' + formatCurrency(sales_tax_price) + '<br>Gratuity: ' + formatCurrency(gratuited_price) + '<br><strong>Grand Total: ' + formatCurrency(grandTotal) + '</strong>');
                
                if (window.cartCoupon) {
                    $('#cart-coupon').html('Coupon "' + window.cartCoupon.code + '" applied: -' + formatCurrency(couponDiscount));
                } else {
                    $('#cart-coupon').html('');
                }

                $('.payment_total').val(grandTotal.toFixed(2));
                $('#subtotal').val(refundableRate > 0 ? refundableAmount.toFixed(2) : grandTotal.toFixed(2));
                $('.default-refundable .refundable-amount').text(formatCurrency(refundableAmount));
                $('.default-due .due-amount').text(formatCurrency(grandTotal - refundableAmount));
                $('.default-deposit span').text(formatCurrency(grandTotal));
                $('.default-total span').text(formatCurrency(grandTotal));
                $('.discounted_amount').val(couponDiscount.toFixed(2));
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
                            } else {
                                alert('Error: ' + res.message);
                            }
                        },
                        error: function(err) {
                            alert('Error generating share link. Please try again.');
                            console.error(err);
                        }
                    });
                });

                syncEventCapacityUi();

                // Copy to clipboard when clicking the shareable link field
                $('#shareableLink').on('click', function() {
                    const url = $(this).val();
                    navigator.clipboard.writeText(url).then(function() {
                        // Show tooltip
                        const tooltip = $('#copyTooltip');
                        tooltip.show();
                        // Hide tooltip after 2 seconds
                        setTimeout(function() {
                            tooltip.hide();
                        }, 2000);
                    }).catch(function(err) {
                        console.error('Failed to copy:', err);
                        // Fallback: select the text
                        $(this).select();
                    });
                });
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
                    addons.forEach(function(addon) {
                        html += '<label class="addon-modal-row">'
                            + '<span class="addon-modal-label">' + escapeAddonHtml(addon.name) + ' <span style="opacity:.6;">(' + formatCurrency(addon.price || 0) + ')</span></span>'
                            + '<span class="addon-switch">'
                            + '<input type="checkbox" class="addon-modal-switch-input" data-id="' + addon.id + '" data-name="' + escapeAddonHtml(addon.name) + '" data-price="' + parseFloat(addon.price || 0) + '">'
                            + '<span class="addon-switch-slider"></span>'
                            + '</span>'
                            + '</label>';
                    });
                }

                $('#addonSelectionModalTitle').text('Select Add-ons for ' + (selection.pkgName || selection.packageName));
                $('#addonSelectionModalBody').html(html);
                bootstrap.Modal.getOrCreateInstance(document.getElementById('addonSelectionModal')).show();
            }

            $(document).ready(function () {
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
                    var target = $(this).data('target');
                    var isOpen = $(this).hasClass('active');

                    $('.package-category-tile').removeClass('active').css({ backgroundColor: 'transparent', color: '{{ $brandPrimary }}' });
                    $('.package-category-group').hide();

                    if (!isOpen) {
                        $(this).addClass('active').css({ backgroundColor: '{{ $brandPrimary }}', color: '#000' });
                        $(target).show();
                    }
                });

                $(document).on('click', '.vip-btn', function () {
                    var $btn = $(this);
                    var packageId = $btn.data('id');
                    var packageName = $btn.data('name');
                    var packagePrice = parseFloat($btn.data('price'));
                    var $guestSelect = $('.package_number_of_guestss[data-id="' + packageId + '"]');
                    var guests = parseInt($guestSelect.val()) || 1;
                    var isMultiple = parseMultipleFlag($guestSelect.data('multiple'));

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

                    $('#addonSelectionModalBody .addon-modal-switch-input:checked').each(function() {
                        selectedAddons.push({
                            id: $(this).data('id'),
                            name: $(this).data('name'),
                            price: parseFloat($(this).data('price'))
                        });
                    });

                    var added = window.addPackageToCart(
                        selection.packageId,
                        selection.packageName,
                        selection.packagePrice,
                        selection.guests,
                        selectedAddons,
                        selection.transportation,
                        selection.isMultiple
                    );

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
                var selectedValue = $(this).val();
                $('.package_number_of_guest').val(selectedValue);
                var packageId = $(this).data('id');
                var pkg = window.cart.find(p => p.packageId == packageId);
                if (pkg) {
                    pkg.guests = parseInt(selectedValue);
                    pkg.isMultiple = parseMultipleFlag($(this).data('multiple'));
                    window.renderCart();
                    window.calculateCartTotal();
                }

                syncEventCapacityUi();
            });
        </script>



        <script>
            // Coupon logic for cart
            $('#applyPromoBtn').on('click', function() {
                let code = $('#promo_code').val().trim();
                if (!code) return;
                $.get('/{{ $data->slug }}/check/' + encodeURIComponent(code), function(res) {
                    if (res.valid === false || res.valid === "false") {
                        window.cartCoupon = null;
                        alert('Invalid promo code');
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

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script>
            flatpickr(".flatpickr-time", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "h:i K",
                time_24hr: false
            });

            // Event checkout date is fixed to the event date.
            $('#package_use_date').val("{{ \Carbon\Carbon::parse($event->date)->format('l, F d, Y') }}");
            $('.package_use_date').val("{{ \Carbon\Carbon::parse($event->date)->format('Y-m-d') }}");
        </script>

        <script>
            // Keep hidden submit value stable even if scripts rerun.
            $('.package_use_date').val("{{ \Carbon\Carbon::parse($event->date)->format('Y-m-d') }}");
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

    </body>

    </html>