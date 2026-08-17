@php
    $selectedFont = $form->settings['font_family'] ?? 'Plus Jakarta Sans';
    $fontMap = [
        'Plus Jakarta Sans' => 'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap',
        'Inter'             => 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap',
        'Roboto'            => 'https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap',
        'Outfit'            => 'https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap',
        'Poppins'           => 'https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap',
        'Open Sans'         => 'https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap',
        'Space Grotesk'     => 'https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&display=swap',
        'Playfair Display'  => 'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700;800&display=swap',
        'Lora'              => 'https://fonts.googleapis.com/css2?family=Lora:wght@500;600;700&display=swap',
    ];
    $activeFontUrl = $fontMap[$selectedFont] ?? $fontMap['Plus Jakarta Sans'];
    $isSerifFont = in_array($selectedFont, ['Playfair Display', 'Lora']);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $form->title }} | CartVIP Official Document</title>
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('user/assets/img/favicon/favicon.svg') }}?v={{ time() }}" />
    <link rel="mask-icon" href="{{ asset('user/assets/img/favicon/safari-mask.svg') }}?v={{ time() }}" color="#ffcc00" />
    <link rel="shortcut icon" href="{{ asset('user/assets/img/favicon/favicon.ico') }}?v={{ time() }}" />
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    <link href="{{ $activeFontUrl }}" rel="stylesheet">

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

        body, input, select, textarea, button, label, .doc-form-title, .doc-form-desc {
            background-color: var(--doc-bg);
            color: var(--doc-text-main);
            font-family: '{{ $selectedFont }}', {{ $isSerifFont ? 'serif' : 'sans-serif' }} !important;
            -webkit-font-smoothing: antialiased;
        }
        body {
            min-height: 100vh;
            padding: 40px 15px;
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

        /* Validation Error Styling & Smooth Mobile Scroll Highlighting */
        .form-field-wrapper.has-validation-error {
            animation: fieldPulseErr 0.35s ease;
        }
        .form-field-wrapper.has-validation-error .form-control-doc,
        .form-field-wrapper.has-validation-error .form-select-doc,
        .form-field-wrapper.has-validation-error .multiselect-search-container,
        .form-field-wrapper.has-validation-error .phone-input-container,
        .form-field-wrapper.has-validation-error .file-dropzone-doc {
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.18) !important;
        }
        .form-field-wrapper.has-validation-error .form-check-input-doc {
            border-color: #ef4444 !important;
            outline: 2px solid rgba(239, 68, 68, 0.3);
        }
        .field-validation-error-msg {
            color: #dc2626;
            font-size: 0.82rem;
            font-weight: 600;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        @keyframes fieldPulseErr {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-4px); }
            75% { transform: translateX(4px); }
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
                    <i class="bx bx-refresh me-1"></i> Back to Forms Portal
                </a>
            </div>
        @else



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
            <form method="POST" action="{{ route('forms.public.submit', $form->slug) }}" enctype="multipart/form-data" novalidate>
                @csrf
                <!-- Honeypot Anti-Spam & Submission Timestamp -->
                <div style="display:none !important; visibility:hidden !important; opacity:0 !important; position:absolute !important; left:-9999px !important;">
                    <input type="text" name="_hp_security_check" value="" tabindex="-1" autocomplete="off">
                    <input type="hidden" name="_form_render_timestamp" value="{{ time() }}">
                </div>
                
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

                        <div class="{{ $widthClass }} form-field-wrapper" id="wrapper_{{ $key }}" data-field-key="{{ $key }}" data-field-id="{{ $field['id'] ?? $key }}" data-originally-required="{{ $required ? 'true' : 'false' }}">
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
                                        <span class="input-group-text bg-light text-secondary border-secondary-subtle time-icon-trigger" style="cursor: pointer; border-top-right-radius: 0; border-bottom-right-radius: 0;"><i class="bx bx-time fs-5"></i></span>
                                        <input type="text" name="{{ $key }}" class="form-control-doc flatpickr-time-input flex-grow-1" style="border-top-left-radius: 0; border-bottom-left-radius: 0; width: auto !important;" placeholder="{{ $placeholder ?: 'Select Time (e.g. 10:30 PM)' }}" value="{{ old($key) }}" {{ $required ? 'required' : '' }}>
                                    </div>

                                @elseif($type === 'multiselect_search')
                                    <div class="multiselect-search-container p-3 rounded-3" style="background: #f8fafc; border: 1px solid #cbd5e1;" data-field-key="{{ $key }}">
                                        <div class="input-group mb-2">
                                            <span class="input-group-text bg-white text-secondary border-secondary-subtle"><i class="bx bx-search fs-5"></i></span>
                                            <input type="text" class="form-control-doc multiselect-search-filter-input" placeholder="{{ $placeholder ?: 'Type to search options...' }}" style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mb-2 micro-text text-muted" style="font-size: 0.8rem;">
                                            <span class="text-secondary fw-medium"><i class="bx bx-check-double me-1 text-primary"></i>Multi-Select Searchable Choices</span>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-link btn-sm p-0 text-primary fw-semibold multiselect-btn-select-all" style="font-size: 0.78rem; text-decoration: none;">Select All</button>
                                                <span class="text-muted opacity-50">|</span>
                                                <button type="button" class="btn btn-link btn-sm p-0 text-secondary fw-semibold multiselect-btn-clear-all" style="font-size: 0.78rem; text-decoration: none;">Clear All</button>
                                            </div>
                                        </div>
                                        <div class="multiselect-options-wrapper p-4 rounded-2 bg-white border border-secondary-subtle" style="max-height: 220px; overflow-y: auto;">
                                            @foreach($options as $opt)
                                                @php
                                                    $oldValues = old($key, []);
                                                    if (!is_array($oldValues)) $oldValues = array_map('trim', explode(',', (string)$oldValues));
                                                    $isChecked = in_array($opt, $oldValues, true);
                                                @endphp
                                                <div class="form-check multiselect-option-item py-1.5 px-2 rounded mb-1 d-flex align-items-center" style="cursor: pointer; transition: background 0.15s ease;">
                                                    <input class="form-check-input multiselect-checkbox mt-0 cursor-pointer" type="checkbox" name="{{ $key }}[]" id="{{ $key }}_{{ $loop->index }}" value="{{ $opt }}" {{ $isChecked ? 'checked' : '' }}>
                                                    <label class="form-check-label text-dark fw-medium small cursor-pointer ms-2 w-100 mb-0" for="{{ $key }}_{{ $loop->index }}">
                                                        {{ $opt }}
                                                    </label>
                                                </div>
                                            @endforeach
                                            <div class="multiselect-no-results text-muted text-center py-2 micro-text d-none">
                                                <i class="bx bx-search-alt me-1"></i>No matching options found.
                                            </div>
                                        </div>
                                        <div class="multiselect-selected-pills mt-2.5 d-flex flex-wrap gap-1.5">
                                            <!-- Selected badge pills rendered via JS -->
                                        </div>
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

                @if(!empty($targetWebsites) && count($targetWebsites) > 0)
                    <div class="mt-4 pt-3 border-top border-secondary border-opacity-10 text-center text-muted d-flex align-items-center justify-content-center flex-wrap gap-2" style="font-size: 0.78rem; color: #64748b;">
                        <span><i class="bx bx-building-house me-1 text-primary"></i>Official Form for: <strong class="text-secondary">{{ $targetWebsites->pluck('name')->implode(', ') }}</strong></span>
                        <span class="text-secondary opacity-50">•</span>
                        <span class="text-success"><i class="bx bx-badge-check me-1"></i>Verified Venue Document</span>
                    </div>
                @endif
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

    // 2. Intl Tel Input with Strict Country Digit Limits & Auto-Formatter
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
                strictMode: true,
                utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js"
            });
            itiMap.set(input, iti);

            function enforceCountryLengthLimit() {
                const countryData = iti.getSelectedCountryData();
                if (window.intlTelInputUtils && countryData && countryData.iso2) {
                    const example = window.intlTelInputUtils.getExampleNumber(
                        countryData.iso2,
                        true,
                        window.intlTelInputUtils.numberType.MOBILE
                    );
                    if (example) {
                        input.placeholder = example;
                        input.setAttribute('maxlength', example.length);
                    }
                }
            }

            // Set initial limit
            setTimeout(enforceCountryLengthLimit, 300);

            // Update limit dynamically on country change
            input.addEventListener('countrychange', function() {
                enforceCountryLengthLimit();
                const countryData = iti.getSelectedCountryData();
                if (input.value.trim().length > 0 && window.intlTelInputUtils && countryData) {
                    const formatted = window.intlTelInputUtils.formatNumber(
                        input.value,
                        countryData.iso2,
                        window.intlTelInputUtils.numberFormat.NATIONAL
                    );
                    if (formatted) {
                        const example = window.intlTelInputUtils.getExampleNumber(countryData.iso2, true, window.intlTelInputUtils.numberType.MOBILE);
                        input.value = example ? formatted.slice(0, example.length) : formatted;
                    }
                }
            });

            // Live auto-format & strict length capping on input typing
            input.addEventListener('input', function() {
                const countryData = iti.getSelectedCountryData();
                if (window.intlTelInputUtils && countryData && countryData.iso2 && input.value.trim().length > 0) {
                    const example = window.intlTelInputUtils.getExampleNumber(
                        countryData.iso2,
                        true,
                        window.intlTelInputUtils.numberType.MOBILE
                    );
                    
                    const formatted = window.intlTelInputUtils.formatNumber(
                        input.value,
                        countryData.iso2,
                        window.intlTelInputUtils.numberFormat.NATIONAL
                    );

                    if (formatted) {
                        let finalVal = formatted;
                        if (example && finalVal.length > example.length) {
                            finalVal = finalVal.slice(0, example.length);
                        }
                        input.value = finalVal;
                    }
                }
            });
        }
    });

    // 3. Robust Mobile-Friendly Submit Validation & Smooth Auto-Scroll Engine
    const mainForm = document.querySelector('form');
    if (mainForm) {
        // Enforce novalidate so browser native validation doesn't silently block submit listener
        mainForm.setAttribute('novalidate', 'novalidate');

        // Prevent browser's silent native invalid event behavior on iOS Safari
        mainForm.addEventListener('invalid', function(e) {
            e.preventDefault();
        }, true);

        function clearWrapperError(wrapper) {
            if (!wrapper) return;
            wrapper.classList.remove('has-validation-error');
            const errBadge = wrapper.querySelector('.field-validation-error-msg');
            if (errBadge) errBadge.remove();
        }

        function setWrapperError(wrapper, message, targetInput) {
            if (!wrapper) return;
            wrapper.classList.add('has-validation-error');
            let errBadge = wrapper.querySelector('.field-validation-error-msg');
            if (!errBadge) {
                errBadge = document.createElement('div');
                errBadge.className = 'field-validation-error-msg';
                wrapper.appendChild(errBadge);
            }
            errBadge.innerHTML = '<i class="bx bx-error-circle fs-6"></i> ' + message;
            if (targetInput && typeof targetInput.classList !== 'undefined') {
                targetInput.classList.add('is-invalid');
            }
        }

        // Live clear errors on user interaction
        mainForm.addEventListener('input', function(e) {
            const wrapper = e.target.closest('.form-field-wrapper');
            if (wrapper) clearWrapperError(wrapper);
        });
        mainForm.addEventListener('change', function(e) {
            const wrapper = e.target.closest('.form-field-wrapper');
            if (wrapper) clearWrapperError(wrapper);
        });

        mainForm.addEventListener('submit', function(e) {
            let isValid = true;
            let firstInvalidWrapper = null;
            let firstInvalidInput = null;

            // Clear all previous validation error highlights
            document.querySelectorAll('.form-field-wrapper').forEach(clearWrapperError);

            // Iterate over all visible field wrappers
            const wrappers = document.querySelectorAll('.form-field-wrapper');
            wrappers.forEach(function(wrapper) {
                // Ignore conditionally hidden wrappers
                if (wrapper.offsetWidth === 0 && wrapper.offsetHeight === 0) return;
                if (window.getComputedStyle(wrapper).display === 'none') return;

                const isRequired = wrapper.getAttribute('data-originally-required') === 'true' || wrapper.querySelector('[required]') !== null;
                const fieldKey = wrapper.getAttribute('data-field-key');

                // A. Check Radio Group Fields
                const radioInputs = wrapper.querySelectorAll('input[type="radio"]');
                if (radioInputs.length > 0 && isRequired) {
                    const checked = wrapper.querySelector('input[type="radio"]:checked');
                    if (!checked) {
                        isValid = false;
                        setWrapperError(wrapper, 'Please select an option to proceed.', radioInputs[0]);
                        if (!firstInvalidWrapper) {
                            firstInvalidWrapper = wrapper;
                            firstInvalidInput = radioInputs[0];
                        }
                    }
                }

                // B. Check Searchable Multi-Select Fields
                const multiselectCheckboxes = wrapper.querySelectorAll('.multiselect-checkbox');
                if (multiselectCheckboxes.length > 0 && isRequired) {
                    const checkedCount = wrapper.querySelectorAll('.multiselect-checkbox:checked').length;
                    if (checkedCount === 0) {
                        isValid = false;
                        setWrapperError(wrapper, 'Please select at least one option.', multiselectCheckboxes[0]);
                        if (!firstInvalidWrapper) {
                            firstInvalidWrapper = wrapper;
                            firstInvalidInput = multiselectCheckboxes[0];
                        }
                    }
                }

                // C. Check Phone Number Inputs
                const phoneInput = wrapper.querySelector('.phone-intl-input');
                if (phoneInput) {
                    const iti = itiMap.get(phoneInput);
                    const rawVal = phoneInput.value.trim();
                    if (rawVal.length > 0) {
                        if (iti && !iti.isValidNumber()) {
                            isValid = false;
                            const countryData = iti.getSelectedCountryData();
                            const countryName = (countryData && countryData.name) ? countryData.name : 'selected country';
                            setWrapperError(wrapper, 'Invalid phone number format for ' + countryName + '.', phoneInput);
                            if (!firstInvalidWrapper) {
                                firstInvalidWrapper = wrapper;
                                firstInvalidInput = phoneInput;
                            }
                        } else if (iti) {
                            phoneInput.value = iti.getNumber();
                        }
                    } else if (isRequired) {
                        isValid = false;
                        setWrapperError(wrapper, 'Phone number is required.', phoneInput);
                        if (!firstInvalidWrapper) {
                            firstInvalidWrapper = wrapper;
                            firstInvalidInput = phoneInput;
                        }
                    }
                }

                // D. Check General Inputs (Text, Number, Email, Select, Textarea)
                const standardInputs = wrapper.querySelectorAll('input:not([type="radio"]):not([type="checkbox"]):not(.multiselect-search-filter-input):not(.phone-intl-input), select, textarea');
                standardInputs.forEach(function(input) {
                    if (input.hasAttribute('required') || isRequired) {
                        const val = input.value ? input.value.trim() : '';
                        if (val === '') {
                            isValid = false;
                            const labelEl = wrapper.querySelector('.form-label-doc');
                            const fieldLabel = labelEl ? labelEl.textContent.replace('*', '').trim() : 'This field';
                            setWrapperError(wrapper, fieldLabel + ' is required.', input);
                            if (!firstInvalidWrapper) {
                                firstInvalidWrapper = wrapper;
                                firstInvalidInput = input;
                            }
                        }
                    }
                });
            });

            if (!isValid) {
                e.preventDefault();
                e.stopPropagation();

                // Smoothly auto-scroll to the very first invalid field with offset for mobile viewports
                if (firstInvalidWrapper) {
                    const rect = firstInvalidWrapper.getBoundingClientRect();
                    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                    const targetY = rect.top + scrollTop - 90;

                    window.scrollTo({
                        top: Math.max(0, targetY),
                        behavior: 'smooth'
                    });

                    try {
                        firstInvalidWrapper.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    } catch (err) {}

                    if (firstInvalidInput && typeof firstInvalidInput.focus === 'function') {
                        setTimeout(function() {
                            try { firstInvalidInput.focus({ preventScroll: true }); } catch (err) {}
                        }, 300);
                    }
                }
            }
        });
    }

    // 4. Interactive Searchable Multi-Select Component Engine
    document.querySelectorAll('.multiselect-search-container').forEach(function(container) {
        const filterInput = container.querySelector('.multiselect-search-filter-input');
        const items = container.querySelectorAll('.multiselect-option-item');
        const noResults = container.querySelector('.multiselect-no-results');
        const selectAllBtn = container.querySelector('.multiselect-btn-select-all');
        const clearAllBtn = container.querySelector('.multiselect-btn-clear-all');
        const pillsContainer = container.querySelector('.multiselect-selected-pills');

        function updateSelectedPills() {
            if (!pillsContainer) return;
            pillsContainer.innerHTML = '';
            const checkedBoxes = container.querySelectorAll('.multiselect-checkbox:checked');
            
            if (checkedBoxes.length === 0) {
                pillsContainer.innerHTML = '<span class="text-muted micro-text fst-italic me-1" style="font-size:0.75rem;">No options selected yet</span>';
                return;
            }

            checkedBoxes.forEach(function(cb) {
                const val = cb.value;
                const pill = document.createElement('span');
                pill.className = 'badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2.5 py-1 rounded-pill d-inline-flex align-items-center gap-1 small fw-semibold';
                pill.innerHTML = `<span>${val}</span> <i class="bx bx-x remove-pill-btn cursor-pointer" style="font-size:1.1rem; line-height:1;" title="Remove option"></i>`;
                
                pill.querySelector('.remove-pill-btn').addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    cb.checked = false;
                    cb.dispatchEvent(new Event('change', { bubbles: true }));
                    updateSelectedPills();
                });
                pillsContainer.appendChild(pill);
            });
        }

        if (filterInput) {
            filterInput.addEventListener('input', function(e) {
                const query = e.target.value.toLowerCase().trim();
                let matchCount = 0;

                items.forEach(function(item) {
                    const text = item.textContent.toLowerCase();
                    if (text.includes(query)) {
                        item.style.setProperty('display', 'flex', 'important');
                        matchCount++;
                    } else {
                        item.style.setProperty('display', 'none', 'important');
                    }
                });

                if (noResults) {
                    noResults.classList.toggle('d-none', matchCount > 0);
                }
            });
        }

        if (selectAllBtn) {
            selectAllBtn.addEventListener('click', function() {
                items.forEach(function(item) {
                    if (item.style.display !== 'none') {
                        const cb = item.querySelector('.multiselect-checkbox');
                        if (cb) cb.checked = true;
                    }
                });
                updateSelectedPills();
                const firstCb = container.querySelector('.multiselect-checkbox');
                if (firstCb) firstCb.dispatchEvent(new Event('change', { bubbles: true }));
            });
        }

        if (clearAllBtn) {
            clearAllBtn.addEventListener('click', function() {
                container.querySelectorAll('.multiselect-checkbox').forEach(function(cb) {
                    cb.checked = false;
                });
                updateSelectedPills();
                const firstCb = container.querySelector('.multiselect-checkbox');
                if (firstCb) firstCb.dispatchEvent(new Event('change', { bubbles: true }));
            });
        }

        container.querySelectorAll('.multiselect-checkbox').forEach(function(cb) {
            cb.addEventListener('change', function() {
                updateSelectedPills();
                if (typeof evaluateAllConditionalRules === 'function') {
                    evaluateAllConditionalRules();
                }
            });
        });

        // Initialize pills on page load
        updateSelectedPills();
    });

    // 5. Smart Conditional Logic Engine
    const formFieldsSchema = {!! json_encode($form->fields_schema ?: []) !!};

    function getFieldValue(fieldKey) {
        if (!fieldKey) return '';
        // 1. Radio buttons
        const checkedRadio = document.querySelector(`input[name="${fieldKey}"]:checked, input[name="${fieldKey}[first]"]:checked`);
        if (checkedRadio) return checkedRadio.value || '';

        // 2. Checkboxes
        const checkboxes = document.querySelectorAll(`input[name="${fieldKey}"][type="checkbox"]:checked, input[name="${fieldKey}[]"][type="checkbox"]:checked`);
        if (checkboxes.length > 0) {
            return Array.from(checkboxes).map(cb => cb.value).join(', ');
        }

        // 3. Inputs, selects, textareas by name or id
        const el = document.querySelector(`[name="${fieldKey}"], [name="${fieldKey}[]"], [name="${fieldKey}[first]"], #${fieldKey}`);
        if (el) {
            return el.value || '';
        }

        // Fallback: search by data-field-id
        const wrapper = document.querySelector(`[data-field-id="${fieldKey}"]`);
        if (wrapper) {
            const innerInp = wrapper.querySelector('input, select, textarea');
            if (innerInp) return innerInp.value || '';
        }

        return '';
    }

    function evaluateSingleRule(rule) {
        if (!rule || !rule.field) return true;
        const val = getFieldValue(rule.field).toString().trim().toLowerCase();
        const targetVal = (rule.value || '').toString().trim().toLowerCase();
        const op = rule.operator || 'is';

        switch (op) {
            case 'is':
                return val === targetVal;
            case 'is_not':
                return val !== targetVal;
            case 'contains':
                return val.includes(targetVal);
            case 'not_contains':
                return !val.includes(targetVal);
            case 'is_empty':
                return val === '';
            case 'is_not_empty':
                return val !== '';
            default:
                return val === targetVal;
        }
    }

    function evaluateConditionalLogic() {
        if (!Array.isArray(formFieldsSchema)) return;

        formFieldsSchema.forEach(field => {
            const cl = field.conditional_logic;
            if (!cl || !cl.enabled || !cl.rules || !Array.isArray(cl.rules) || cl.rules.length === 0) return;

            const fieldKey = field.name || field.id;
            const fieldId = field.id;
            
            let wrapper = document.getElementById('wrapper_' + fieldKey);
            if (!wrapper && fieldId) wrapper = document.getElementById('wrapper_' + fieldId);
            if (!wrapper) wrapper = document.querySelector(`[data-field-key="${fieldKey}"]`) || document.querySelector(`[data-field-id="${fieldId}"]`);

            if (!wrapper) return;

            const rules = cl.rules;
            const gate = cl.logic_gate || 'all';
            let match = false;

            if (gate === 'all') {
                match = rules.every(r => evaluateSingleRule(r));
            } else {
                match = rules.some(r => evaluateSingleRule(r));
            }

            const action = cl.action || 'show';
            let shouldShow = (action === 'show') ? match : !match;

            if (shouldShow) {
                wrapper.style.display = '';
                wrapper.querySelectorAll('input, select, textarea').forEach(inp => {
                    if (wrapper.getAttribute('data-originally-required') === 'true') {
                        inp.setAttribute('required', 'required');
                    }
                });
            } else {
                wrapper.style.display = 'none';
                wrapper.querySelectorAll('input, select, textarea').forEach(inp => {
                    inp.removeAttribute('required');
                });
            }
        });
    }

    if (mainForm) {
        mainForm.addEventListener('change', evaluateConditionalLogic);
        mainForm.addEventListener('input', evaluateConditionalLogic);
    }

    // Run initial check
    evaluateConditionalLogic();
});
</script>
</body>
</html>
