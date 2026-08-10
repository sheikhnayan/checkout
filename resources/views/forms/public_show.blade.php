<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $form->title }} | CartVIP Official Document</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5, Boxicons & Intl Tel Input -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/css/intlTelInput.css">

    <style>
        .iti {
            width: 100%;
            display: block;
        }
        .iti__country-list {
            z-index: 1050;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            border: 1px solid #cbd5e1;
            color: #0f172a;
        }
        .iti__selected-flag {
            border-radius: 10px 0 0 10px;
            padding: 0 12px;
            background-color: #f8fafc;
            border-right: 1px solid #cbd5e1;
        }

        :root {
            --doc-bg: #f8fafc;
            --doc-surface: #ffffff;
            --doc-border: #e2e8f0;
            --doc-border-strong: #cbd5e1;
            --doc-primary: #4f46e5;
            --doc-primary-hover: #4338ca;
            --doc-text-main: #0f172a;
            --doc-text-muted: #475569;
            --doc-text-subtle: #64748b;
        }

        body {
            background-color: var(--doc-bg);
            color: var(--doc-text-main);
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
            padding: 40px 15px;
            -webkit-font-smoothing: antialiased;
        }

        /* Wider Standard Form Container */
        .document-wrapper {
            max-width: 1040px;
            margin: 0 auto;
        }

        /* Top CartVIP Branding Bar */
        .branding-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
            padding: 16px 24px;
            background: #ffffff;
            border: 1px solid var(--doc-border);
            border-radius: 16px;
            margin-bottom: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.03);
        }
        .brand-logo-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .brand-logo-icon {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 1.4rem;
            box-shadow: 0 4px 12px rgba(124, 58, 237, 0.25);
        }
        .brand-title {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            font-size: 1.15rem;
            color: #0f172a;
            letter-spacing: -0.02em;
            line-height: 1.1;
        }
        .brand-subtitle {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--doc-text-subtle);
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        /* Document Paper Card */
        .document-card {
            background: var(--doc-surface);
            border: 1px solid var(--doc-border);
            border-radius: 20px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
            padding: 44px 48px;
            position: relative;
            overflow: hidden;
        }

        @media (max-width: 768px) {
            .document-card {
                padding: 24px 20px;
            }
        }

        /* Top Accent Bar */
        .document-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 5px;
            background: linear-gradient(90deg, #7c3aed 0%, #4f46e5 50%, #06b6d4 100%);
        }

        /* Club Header Banner */
        .club-banner {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }
        .club-info-title {
            font-size: 0.88rem;
            font-weight: 700;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Form Titles & Headers */
        .doc-form-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1.85rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.03em;
            margin-bottom: 8px;
        }
        .doc-form-desc {
            font-size: 0.95rem;
            color: var(--doc-text-muted);
            line-height: 1.6;
            margin-bottom: 0;
        }

        /* Form Inputs & Labels */
        .form-label-doc {
            font-size: 0.88rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 6px;
            display: block;
        }
        /* Input Group Fixing for Inline Time & Phone Icons */
        .input-group {
            display: flex !important;
            flex-wrap: nowrap !important;
            align-items: stretch !important;
            width: 100% !important;
        }
        .input-group > .input-group-text {
            border-top-right-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
            border-right: none !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 0 14px !important;
            background-color: #f8fafc !important;
            border: 1px solid var(--doc-border-strong);
            color: #475569;
        }
        .input-group > .form-control-doc {
            border-top-left-radius: 0 !important;
            border-bottom-left-radius: 0 !important;
            flex: 1 1 auto !important;
            width: 1% !important;
        }

        .form-control-doc, .form-select-doc {
            background-color: #ffffff;
            border: 1px solid var(--doc-border-strong);
            color: #0f172a !important;
            padding: 11px 16px;
            border-radius: 10px;
            font-size: 0.92rem;
            font-weight: 500;
            transition: all 0.2s ease;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
            width: 100%;
        }
        .form-control-doc::placeholder {
            color: #94a3b8;
            font-weight: 400;
        }
        .form-control-doc:focus, .form-select-doc:focus {
            background-color: #ffffff;
            border-color: var(--doc-primary);
            color: #0f172a !important;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.12);
            outline: none;
        }

        /* Sub-labels for Name & Phone */
        .sub-label-text {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--doc-text-subtle);
            margin-top: 4px;
        }

        /* File Upload Box (Bright Legal Drop Zone) */
        .file-dropzone-doc {
            background: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 14px;
            padding: 32px 20px;
            text-align: center;
            transition: all 0.2s ease;
            cursor: pointer;
            position: relative;
        }
        .file-dropzone-doc:hover {
            border-color: var(--doc-primary);
            background: #f1f5f9;
        }
        .file-dropzone-icon {
            font-size: 2.8rem;
            color: var(--doc-primary);
            margin-bottom: 10px;
        }
        .file-dropzone-text {
            font-size: 0.95rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 4px;
        }

        /* Section Headings inside Document */
        .doc-section-heading {
            font-size: 1.15rem;
            font-weight: 800;
            color: #0f172a;
            padding-bottom: 10px;
            border-bottom: 2px solid #e2e8f0;
            margin-top: 24px;
            margin-bottom: 16px;
            letter-spacing: -0.01em;
        }

        /* Checkbox & Radio Styling */
        .form-check-input-doc {
            width: 1.25em;
            height: 1.25em;
            border: 1.5px solid #94a3b8;
            border-radius: 4px;
            cursor: pointer;
        }
        .form-check-input-doc:checked {
            background-color: var(--doc-primary);
            border-color: var(--doc-primary);
        }

        /* Submit Button & Security Footer */
        .btn-submit-doc {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            border: none;
            color: #ffffff !important;
            padding: 14px 40px;
            font-size: 1.02rem;
            font-weight: 700;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(79, 70, 229, 0.35);
            transition: all 0.2s ease;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-submit-doc:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(79, 70, 229, 0.45);
            background: linear-gradient(135deg, #4338ca 0%, #6d28d9 100%);
        }

        .security-footer-note {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 0.8rem;
            color: var(--doc-text-subtle);
            margin-top: 24px;
            font-weight: 500;
        }

        .thank-you-card {
            background: #ffffff;
            border: 2px solid #22c55e;
            border-radius: 20px;
            padding: 60px 30px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="document-wrapper">
    
    <!-- Top CartVIP Branding Bar -->
    <div class="branding-header">
        <div class="brand-logo-wrap">
            <div class="brand-logo-icon">
                <i class="bx bx-check-shield"></i>
            </div>
            <div>
                <div class="brand-title">CARTVIP</div>
                <div class="brand-subtitle">Secure Document Portal</div>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-1.5 rounded-pill font-monospace small">
                <i class="bx bx-lock-alt me-1"></i>DOCUMENT ID: #CV-{{ strtoupper(substr($form->slug, 0, 12)) }}
            </span>
        </div>
    </div>

    <!-- Main Document Card (Wider Canvas & Light Legal Theme) -->
    <div class="document-card">
        
        @if(session('form_success'))
            <!-- Thank You Screen -->
            <div class="thank-you-card">
                <div class="mb-3">
                    <i class="bx bx-check-circle text-success" style="font-size: 5.5rem;"></i>
                </div>
                <h2 class="text-dark fw-bold mb-3">Submission Confirmed!</h2>
                <p class="fs-5 text-secondary mb-4" style="max-width: 620px; margin: 0 auto;">
                    {{ session('form_success') }}
                </p>
                <a href="{{ url()->current() }}" class="btn btn-outline-dark px-4 py-2 rounded-pill fw-semibold">
                    <i class="bx bx-refresh me-1"></i> Submit Another Response
                </a>
            </div>
        @else

            <!-- Club Details Header (If Form Belongs to Clubs) -->
            @if(!empty($targetWebsites) && count($targetWebsites) > 0)
                <div class="club-banner">
                    <div class="club-info-title">
                        <i class="bx bx-building-house text-primary fs-5"></i>
                        <span>Associated Venue / Club: 
                            <strong class="text-dark">{{ $targetWebsites->pluck('name')->implode(', ') }}</strong>
                        </span>
                    </div>
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1.5 rounded-pill">
                        <i class="bx bx-badge-check me-1"></i>Verified Official Club Document
                    </span>
                </div>
            @endif

            <!-- Form Header Title & Description -->
            <div class="border-bottom pb-4 mb-4">
                <h1 class="doc-form-title">{{ $form->title }}</h1>
                @if($form->description)
                    <p class="doc-form-desc">{{ $form->description }}</p>
                @endif
            </div>

            @if($errors->any())
                <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger mb-4 rounded-3">
                    <div class="fw-bold mb-1"><i class="bx bx-error-circle me-1"></i> Please correct the following errors:</div>
                    <ul class="mb-0 ps-3 small">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form Fields Render Grid -->
            <form method="POST" action="{{ route('forms.public.submit', $form->slug) }}" enctype="multipart/form-data">
                @csrf
                
                <div class="row g-4">
                    @foreach(($form->fields_schema ?: []) as $field)
                        @php
                            $type = $field['type'] ?? 'text';
                            $key = $field['name'] ?? $field['id'] ?? 'field_' . $loop->index;
                            $label = $field['label'] ?? ucfirst($type);
                            $placeholder = $field['placeholder'] ?? '';
                            $helpText = $field['help_text'] ?? '';
                            $widthClass = $field['width_class'] ?? 'col-12';
                            $required = !empty($field['required']);
                            $options = $field['options'] ?? [];
                        @endphp

                        <div class="{{ $widthClass }}">
                            @if($type === 'heading')
                                <h4 class="doc-section-heading">{{ $label }}</h4>
                                @if($helpText)
                                    <div class="text-muted small mb-2">{{ $helpText }}</div>
                                @endif

                            @elseif($type === 'checkbox')
                                <!-- Single Agreement Checkbox -->
                                <div class="form-check mt-2">
                                    <input class="form-check-input form-check-input-doc me-2" type="checkbox" name="{{ $key }}" id="{{ $key }}" value="1" {{ old($key) ? 'checked' : '' }} {{ $required ? 'required' : '' }}>
                                    <label class="form-check-label fw-semibold text-dark" for="{{ $key }}">
                                        {{ $label }}
                                        @if($required)<span class="text-danger">*</span>@endif
                                    </label>
                                </div>
                                @if($helpText)
                                    <div class="form-text text-muted small mt-1 ms-4">{{ $helpText }}</div>
                                @endif

                            @else
                                <label class="form-label-doc">
                                    {{ $label }}
                                    @if($required)<span class="text-danger">*</span>@endif
                                </label>

                                @if($type === 'name')
                                    @php $format = $field['format'] ?? 'first_last'; @endphp
                                    @if($format === 'first_middle_last')
                                        <div class="row g-2">
                                            <div class="col-4">
                                                <input type="text" name="{{ $key }}[first]" class="form-control-doc" placeholder="First Name" {{ $required ? 'required' : '' }}>
                                                <div class="sub-label-text">First</div>
                                            </div>
                                            <div class="col-4">
                                                <input type="text" name="{{ $key }}[middle]" class="form-control-doc" placeholder="Middle Name">
                                                <div class="sub-label-text">Middle</div>
                                            </div>
                                            <div class="col-4">
                                                <input type="text" name="{{ $key }}[last]" class="form-control-doc" placeholder="Last Name" {{ $required ? 'required' : '' }}>
                                                <div class="sub-label-text">Last</div>
                                            </div>
                                        </div>
                                    @elseif($format === 'simple')
                                        <input type="text" name="{{ $key }}" class="form-control-doc" placeholder="{{ $placeholder ?: 'Full Name' }}" {{ $required ? 'required' : '' }}>
                                    @else
                                        <!-- Dual Input: First & Last Name (Matching Reference Images) -->
                                        <div class="row g-3">
                                            <div class="col-6">
                                                <input type="text" name="{{ $key }}[first]" class="form-control-doc" placeholder="" {{ $required ? 'required' : '' }}>
                                                <div class="sub-label-text">First</div>
                                            </div>
                                            <div class="col-6">
                                                <input type="text" name="{{ $key }}[last]" class="form-control-doc" placeholder="" {{ $required ? 'required' : '' }}>
                                                <div class="sub-label-text">Last</div>
                                            </div>
                                        </div>
                                    @endif

                                @elseif($type === 'phone')
                                    <div class="phone-input-container">
                                        <input type="tel" id="{{ $key }}" name="{{ $key }}" class="form-control-doc phone-intl-input" placeholder="{{ $placeholder ?: 'Phone Number' }}" {{ $required ? 'required' : '' }}>
                                    </div>

                                @elseif($type === 'captcha')
                                    <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:#f1f5f9; border: 1px solid #cbd5e1;">
                                        <span class="fw-bold text-dark fs-5 font-monospace" style="letter-spacing:2px;">{{ $captchaQuestion ?? '8 + 5' }} =</span>
                                        <input type="number" name="{{ $key }}" class="form-control-doc" style="width: 130px;" placeholder="Answer" required>
                                    </div>

                                @elseif($type === 'textarea')
                                    <textarea name="{{ $key }}" class="form-control-doc" rows="4" placeholder="{{ $placeholder }}" {{ $required ? 'required' : '' }}>{{ old($key) }}</textarea>
                                
                                @elseif($type === 'select')
                                    <select name="{{ $key }}" class="form-select-doc" {{ $required ? 'required' : '' }}>
                                        <option value="">-- Select Option --</option>
                                        @foreach($options as $opt)
                                            <option value="{{ $opt }}" {{ old($key) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                        @endforeach
                                    </select>

                                @elseif($type === 'radio')
                                    <div class="mt-1 d-flex flex-wrap gap-4">
                                        @foreach($options as $opt)
                                            <div class="form-check">
                                                <input class="form-check-input form-check-input-doc" type="radio" name="{{ $key }}" id="{{ $key }}_{{ $loop->index }}" value="{{ $opt }}" {{ old($key) === $opt ? 'checked' : '' }} {{ $required ? 'required' : '' }}>
                                                <label class="form-check-label fw-semibold text-dark ms-1" for="{{ $key }}_{{ $loop->index }}">{{ $opt }}</label>
                                            </div>
                                        @endforeach
                                    </div>

                                @elseif($type === 'time')
                                    <div class="input-group d-flex align-items-center">
                                        <span class="input-group-text bg-light text-secondary border-secondary-subtle time-icon-trigger" style="cursor: pointer;"><i class="bx bx-time fs-5"></i></span>
                                        <input type="text" name="{{ $key }}" class="form-control-doc flatpickr-time-input" placeholder="{{ $placeholder ?: 'Select Time (e.g. 10:30 PM)' }}" value="{{ old($key) }}" {{ $required ? 'required' : '' }}>
                                    </div>

                                @elseif($type === 'file')
                                    @php
                                        $exts = !empty($field['allowed_extensions']) ? implode(', ', array_filter(array_map('trim', explode(',', $field['allowed_extensions'])))) : 'pdf, doc, docx, png, jpg';
                                        $maxMb = !empty($field['max_file_size']) ? $field['max_file_size'] : 5;
                                        $maxCount = !empty($field['max_file_uploads']) ? $field['max_file_uploads'] : 1;
                                        $acceptArr = !empty($field['allowed_extensions']) ? array_filter(array_map('trim', explode(',', $field['allowed_extensions']))) : ['pdf', 'doc', 'docx', 'png', 'jpg'];
                                        $acceptAttr = '.' . implode(',.', $acceptArr);
                                    @endphp
                                    <div class="file-dropzone-doc">
                                        <i class="bx bx-cloud-upload file-dropzone-icon"></i>
                                        <div class="file-dropzone-text">Drag & Drop File or <span class="text-primary text-decoration-underline">Choose File to Upload</span></div>
                                        <div class="text-muted micro-text mt-1">
                                            Supported Formats: <strong>{{ $exts }}</strong> | Max Size: <strong>{{ $maxMb }}MB</strong> @if($maxCount > 1)| Max Files: <strong>{{ $maxCount }}</strong>@endif
                                        </div>
                                        <input type="file" name="{{ $key }}{{ $maxCount > 1 ? '[]' : '' }}" class="form-control-doc mt-3" accept="{{ $acceptAttr }}" {{ $maxCount > 1 ? 'multiple' : '' }} {{ $required ? 'required' : '' }}>
                                    </div>

                                @else
                                    <input type="{{ $type === 'phone' ? 'tel' : $type }}" name="{{ $key }}" class="form-control-doc" placeholder="{{ $placeholder }}" value="{{ old($key) }}" {{ $required ? 'required' : '' }}>
                                @endif

                                @if($helpText)
                                    <div class="form-text text-muted small mt-1.5"><i class="bx bx-info-circle me-1"></i>{{ $helpText }}</div>
                                @endif
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Submit Action Row & Security Footnote -->
                <div class="mt-5 pt-3 border-top d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <button type="submit" class="btn-submit-doc">
                        <i class="bx bx-paper-plane"></i> Submit Form
                    </button>

                    <div class="security-footer-note">
                        <i class="bx bx-lock-alt text-primary fs-5"></i>
                        <span>Protected by <strong>CartVIP 256-Bit SSL Encryption</strong> • Official & Confidential</span>
                    </div>
                </div>
            </form>

        @endif

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/intlTelInput.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Time Picker with Icon Click Listener
    document.querySelectorAll('.flatpickr-time-input').forEach(function(input) {
        const fp = flatpickr(input, {
            enableTime: true,
            noCalendar: true,
            dateFormat: "h:i K",
            time_24hr: false,
            minuteIncrement: 5
        });

        const group = input.closest('.input-group');
        if (group) {
            const iconBtn = group.querySelector('.time-icon-trigger');
            if (iconBtn) {
                iconBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    fp.open();
                });
            }
        }
    });

    // 2. Intl Tel Input with Dynamic Country Auto-Formatter
    const itiMap = new Map();
    document.querySelectorAll('.phone-intl-input').forEach(function(input) {
        if (window.intlTelInput) {
            const iti = window.intlTelInput(input, {
                initialCountry: "us",
                preferredCountries: ["us", "ca", "gb", "au", "de", "fr", "in"],
                separateDialCode: true,
                autoPlaceholder: "aggressive",
                formatOnDisplay: true,
                nationalMode: true,
                utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js"
            });
            itiMap.set(input, iti);

            // Auto-format placeholder & current value on country change
            input.addEventListener('countrychange', function() {
                const countryData = iti.getSelectedCountryData();
                if (window.intlTelInputUtils && countryData && countryData.iso2) {
                    const example = window.intlTelInputUtils.getExampleNumber(
                        countryData.iso2,
                        true,
                        window.intlTelInputUtils.numberType.MOBILE
                    );
                    if (example) input.placeholder = example;

                    if (input.value.trim().length > 0) {
                        const formatted = window.intlTelInputUtils.formatNumber(
                            input.value,
                            countryData.iso2,
                            window.intlTelInputUtils.numberFormat.NATIONAL
                        );
                        if (formatted) input.value = formatted;
                    }
                }
            });

            // Live auto-format on input typing without blocking extra digits
            input.addEventListener('input', function() {
                if (window.intlTelInputUtils && input.value.trim().length > 0) {
                    const countryData = iti.getSelectedCountryData();
                    if (countryData && countryData.iso2) {
                        const rawDigits = input.value.replace(/\D/g, '');
                        if (rawDigits.length >= 3) {
                            const formatted = window.intlTelInputUtils.formatNumber(
                                input.value,
                                countryData.iso2,
                                window.intlTelInputUtils.numberFormat.NATIONAL
                            );
                            if (formatted && formatted !== input.value && !input.value.endsWith(' ')) {
                                input.value = formatted;
                            }
                        }
                    }
                }
            });
        }
    });

    // 3. Form Submit Validation for Phone & Country Match
    const mainForm = document.querySelector('form');
    if (mainForm) {
        mainForm.addEventListener('submit', function(e) {
            let isValid = true;
            document.querySelectorAll('.phone-intl-input').forEach(function(input) {
                const iti = itiMap.get(input);
                if (iti) {
                    const rawVal = input.value.trim();
                    const container = input.closest('.phone-input-container');
                    let errDiv = container ? container.querySelector('.phone-error-msg') : null;
                    if (rawVal.length > 0) {
                        if (!iti.isValidNumber()) {
                            isValid = false;
                            input.classList.add('is-invalid');
                            if (!errDiv && container) {
                                errDiv = document.createElement('div');
                                errDiv.className = 'text-danger small mt-1 phone-error-msg fw-semibold';
                                container.appendChild(errDiv);
                            }
                            const countryName = iti.getSelectedCountryData().name || 'selected country';
                            if (errDiv) errDiv.innerHTML = '<i class="bx bx-error-circle me-1"></i>Invalid phone number format for ' + countryName + '.';
                        } else {
                            input.classList.remove('is-invalid');
                            if (errDiv) errDiv.remove();
                            // Standardize to full E.164 phone number including country dial code
                            input.value = iti.getNumber();
                        }
                    } else if (input.hasAttribute('required')) {
                        isValid = false;
                        input.classList.add('is-invalid');
                    }
                }
            });

            if (!isValid) {
                e.preventDefault();
                e.stopPropagation();
            }
        });
    }
});
</script>
</body>
</html>
