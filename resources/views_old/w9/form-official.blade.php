<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form W-9 - Request for Taxpayer Identification Number and Certification</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
            line-height: 1.4;
        }

        .page-wrapper {
            max-width: 900px;
            margin: 0 auto;
            background: #fff;
            padding: 40px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .form-header {
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .irs-name {
            font-weight: bold;
            font-size: 12px;
            text-align: center;
            margin-bottom: 5px;
        }

        .form-title {
            font-weight: bold;
            font-size: 18px;
            text-align: center;
            margin: 10px 0;
        }

        .form-subtitle {
            text-align: center;
            font-size: 12px;
            margin-bottom: 15px;
        }

        .form-meta {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            font-size: 11px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ccc;
        }

        .meta-item {
            display: flex;
            justify-content: space-between;
        }

        .meta-label {
            font-weight: bold;
        }

        .instructions-box {
            background: #fffacd;
            border: 1px solid #daa520;
            padding: 15px;
            margin: 20px 0;
            font-size: 12px;
            line-height: 1.6;
        }

        .instructions-box h3 {
            font-size: 13px;
            margin-bottom: 10px;
        }

        .instructions-box ul {
            margin: 10px 0 10px 20px;
        }

        .instructions-box li {
            margin-bottom: 5px;
        }

        .form-section {
            margin: 25px 0;
            padding-bottom: 15px;
            border-bottom: 1px solid #ddd;
        }

        .section-title {
            font-weight: bold;
            font-size: 13px;
            margin-bottom: 15px;
            text-transform: uppercase;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 15px;
        }

        .form-row.full {
            grid-template-columns: 1fr;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }

        .form-field {
            display: flex;
            flex-direction: column;
        }

        .field-label {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 5px;
        }

        .field-sublabel {
            font-size: 10px;
            color: #666;
            margin-bottom: 3px;
            font-style: italic;
        }

        .form-input {
            border: 1px solid #333;
            padding: 8px 5px;
            font-family: Arial, sans-serif;
            font-size: 12px;
            background: #fff;
        }

        .form-input:focus {
            outline: 2px solid #0066cc;
            background: #f0f8ff;
        }

        select.form-input {
            cursor: pointer;
        }

        .checkbox-group {
            display: grid;
            grid-template-columns: 1fr;
            gap: 8px;
        }

        .checkbox-item {
            display: flex;
            align-items: flex-start;
            font-size: 12px;
            gap: 8px;
        }

        .checkbox-item input[type="radio"],
        .checkbox-item input[type="checkbox"] {
            margin-top: 3px;
            cursor: pointer;
        }

        .checkbox-item label {
            cursor: pointer;
            margin: 0;
        }

        .certification-box {
            border: 2px solid #000;
            padding: 15px;
            margin: 20px 0;
            background: #f9f9f9;
        }

        .certification-box h3 {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .certification-box p {
            font-size: 11px;
            margin-bottom: 10px;
            line-height: 1.5;
        }

        .required {
            color: #d32f2f;
            font-weight: bold;
        }

        .address-line {
            font-size: 10px;
            color: #666;
        }

        .signature-line {
            border-top: 1px solid #333;
            display: inline-block;
            width: 150px;
            margin: 20px 0 5px 0;
        }

        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }

        .btn {
            padding: 10px 20px;
            font-size: 13px;
            font-weight: bold;
            border: 1px solid #333;
            background: #fff;
            cursor: pointer;
            border-radius: 3px;
        }

        .btn-primary {
            background: #0066cc;
            color: #fff;
            border-color: #0066cc;
        }

        .btn-primary:hover {
            background: #0052a3;
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .success-message {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
            display: none;
        }

        .error-message {
            color: #d32f2f;
            font-size: 10px;
            margin-top: 3px;
            display: none;
        }

        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }

        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #0066cc;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 0 auto 10px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .footer-text {
            font-size: 10px;
            color: #666;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
        }

        .id-upload-section {
            background: #f0f8ff;
            border: 1px solid #87ceeb;
            padding: 15px;
            border-radius: 4px;
            margin: 15px 0;
        }

        .file-upload-area {
            border: 2px dashed #0066cc;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            background: #f9f9f9;
            border-radius: 4px;
            margin: 10px 0;
        }

        .file-upload-area:hover {
            background: #f0f0f0;
        }

        .file-upload-area input[type="file"] {
            display: none;
        }

        .preview-img {
            max-width: 150px;
            max-height: 150px;
            border: 1px solid #333;
            margin: 10px 0;
        }

        .file-specs {
            font-size: 10px;
            color: #666;
            margin-top: 10px;
        }

        .hide-print {
            display: block;
        }

        @media print {
            .hide-print {
                display: none;
            }
            .page-wrapper {
                box-shadow: none;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="page-wrapper">
        <!-- Header -->
        <div class="form-header">
            <div class="irs-name">Department of the Treasury - Internal Revenue Service</div>
            <div class="form-title">Form W-9</div>
            <div class="form-subtitle">Request for Taxpayer Identification Number and Certification</div>
        </div>

        <!-- Success Message -->
        <div class="success-message" id="successMsg">
            ✓ Your Form W-9 has been submitted successfully. Your submission is now pending review.
        </div>

        <!-- Form Meta -->
        <div class="form-meta hide-print">
            <div class="meta-item">
                <span class="meta-label">OMB No.:</span>
                <span>1545-0047</span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Expires:</span>
                <span>12/31/2025</span>
            </div>
        </div>

        <!-- Instructions -->
        <div class="instructions-box">
            <h3>Instructions</h3>
            <ul>
                <li><strong>General Instruction:</strong> The Information Returns Master File (IRMF) process has been improved and now requires participation by all persons receiving payments reportable under Internal Revenue Code Section 6041, 6041A(b), 6042, 6044, 6047, 6049, or certain payments from federal executive agencies.</li>
                <li><strong>Give Form to Requester:</strong> Provide this completed form to the person or entity requesting it.</li>
                <li><strong>File Complete Form:</strong> Do not leave any required fields blank.</li>
                <li><strong>Penalties:</strong> Failure to provide your correct Tax Identification Number (TIN) may result in penalties and could delay or prevent processing of your account.</li>
            </ul>
        </div>

        <form id="w9Form" method="POST" action="{{ route('w9.store', $token) }}" enctype="multipart/form-data">
            @csrf

            <!-- Part I: Name -->
            <div class="form-section">
                <div class="section-title">Part I: Taxpayer Identification Number (TIN)</div>

                <div class="form-row full">
                    <div class="form-field">
                        <label class="field-label">1. Name as shown on your income tax return <span class="required">*</span></label>
                        <label class="field-sublabel">(Sole proprietors - see instructions)</label>
                        <input type="text" name="full_name" class="form-input" required value="{{ $w9Form?->full_name ?? '' }}" placeholder="First Name, Middle Initial, Last Name">
                        <span class="error-message"></span>
                    </div>
                </div>

                <div class="form-row full">
                    <div class="form-field">
                        <label class="field-label">2. Business name/Disregarded entity name, if different from above</label>
                        <input type="text" name="business_name" class="form-input" value="{{ $w9Form?->business_name ?? '' }}" placeholder="DBA, Trade Name">
                        <span class="error-message"></span>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-field">
                        <label class="field-label">3a. Requester's name and address <span class="required">*</span></label>
                        <input type="text" name="requester_name" class="form-input" value="{{ $w9Form?->requester_name ?? '' }}" placeholder="Requester name">
                        <span class="error-message"></span>
                    </div>
                    <div class="form-field">
                        <label class="field-label">3b. Requester's telephone number</label>
                        <input type="tel" name="requester_phone" class="form-input" value="{{ $w9Form?->requester_phone ?? '' }}" placeholder="(XXX) XXX-XXXX">
                        <span class="error-message"></span>
                    </div>
                </div>

                <div class="form-row full">
                    <div class="form-field">
                        <label class="field-label">3c. Requester's email address</label>
                        <input type="email" name="requester_email" class="form-input" value="{{ $w9Form?->requester_email ?? '' }}" placeholder="email@example.com">
                        <span class="error-message"></span>
                    </div>
                </div>

                <div class="form-row full">
                    <div class="form-field">
                        <label class="field-label">4. Federal income tax classification of the above named item <span class="required">*</span></label>
                        <label class="field-sublabel">Check the appropriate box for federal tax classification of the person whose name is entered on line 1. Check only one of the following seven boxes.</label>

                        <div class="checkbox-group" style="margin-top: 10px;">
                            <div class="checkbox-item">
                                <input type="radio" id="tc1" name="tax_classification" value="individual" required {{ $w9Form?->tax_classification === 'individual' ? 'checked' : '' }} onchange="toggleOther()">
                                <label for="tc1">Individual/Sole proprietor or single-member LLC</label>
                            </div>

                            <div class="checkbox-item">
                                <input type="radio" id="tc2" name="tax_classification" value="c_corporation" {{ $w9Form?->tax_classification === 'c_corporation' ? 'checked' : '' }} onchange="toggleOther()">
                                <label for="tc2">C Corporation</label>
                            </div>

                            <div class="checkbox-item">
                                <input type="radio" id="tc3" name="tax_classification" value="s_corporation" {{ $w9Form?->tax_classification === 's_corporation' ? 'checked' : '' }} onchange="toggleOther()">
                                <label for="tc3">S Corporation</label>
                            </div>

                            <div class="checkbox-item">
                                <input type="radio" id="tc4" name="tax_classification" value="partnership" {{ $w9Form?->tax_classification === 'partnership' ? 'checked' : '' }} onchange="toggleOther()">
                                <label for="tc4">Partnership</label>
                            </div>

                            <div class="checkbox-item">
                                <input type="radio" id="tc5" name="tax_classification" value="trust_estate" {{ $w9Form?->tax_classification === 'trust_estate' ? 'checked' : '' }} onchange="toggleOther()">
                                <label for="tc5">Trust/Estate</label>
                            </div>

                            <div class="checkbox-item">
                                <input type="radio" id="tc6" name="tax_classification" value="limited_liability_company_c" {{ $w9Form?->tax_classification === 'limited_liability_company_c' ? 'checked' : '' }} onchange="toggleOther()">
                                <label for="tc6">Limited Liability Company – Taxed as C Corporation</label>
                            </div>

                            <div class="checkbox-item">
                                <input type="radio" id="tc7" name="tax_classification" value="limited_liability_company_s" {{ $w9Form?->tax_classification === 'limited_liability_company_s' ? 'checked' : '' }} onchange="toggleOther()">
                                <label for="tc7">Limited Liability Company – Taxed as S Corporation</label>
                            </div>

                            <div class="checkbox-item">
                                <input type="radio" id="tc8" name="tax_classification" value="limited_liability_company_individual" {{ $w9Form?->tax_classification === 'limited_liability_company_individual' ? 'checked' : '' }} onchange="toggleOther()">
                                <label for="tc8">Limited Liability Company – Individual</label>
                            </div>

                            <div class="checkbox-item">
                                <input type="radio" id="tc9" name="tax_classification" value="other" {{ $w9Form?->tax_classification === 'other' ? 'checked' : '' }} onchange="toggleOther()">
                                <label for="tc9">Other (see instructions)</label>
                            </div>

                            <div id="otherClassification" style="display: none; margin-top: 10px;">
                                <input type="text" name="tax_classification_other" class="form-input" placeholder="Specify your tax classification">
                            </div>
                        </div>
                        <span class="error-message"></span>
                    </div>
                </div>

                <div class="form-row full">
                    <div class="form-field">
                        <label class="field-label">5. Exemption from FATCA reporting code (if any) <span class="required-indicator" style="opacity:0;">*</span></label>
                        <input type="text" name="fatca_exemption_code" class="form-input" value="{{ $w9Form?->fatca_exemption_code ?? '' }}" placeholder="Optional - if applicable">
                        <label class="field-sublabel">(See instructions if applicable)</label>
                    </div>
                </div>
            </div>

            <!-- Part II: TIN -->
            <div class="form-section">
                <div class="section-title">Part II: Tax Identification Number (TIN)</div>

                <div class="form-row full">
                    <div class="form-field">
                        <label class="field-label">6. Check appropriate box for federal tax classification <span class="required">*</span></label>

                        <div class="checkbox-group" style="margin-top: 10px;">
                            <div class="checkbox-item">
                                <input type="radio" id="tin1" name="tax_id_type" value="ssn" required {{ $w9Form?->tax_id_type === 'ssn' ? 'checked' : '' }}>
                                <label for="tin1">Social Security Number (SSN)</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="radio" id="tin2" name="tax_id_type" value="ein" {{ $w9Form?->tax_id_type === 'ein' ? 'checked' : '' }}>
                                <label for="tin2">Employer Identification Number (EIN)</label>
                            </div>
                        </div>
                        <span class="error-message"></span>
                    </div>
                </div>

                <div class="form-row full">
                    <div class="form-field">
                        <label class="field-label">7. Federal income tax number (SSN or EIN) <span class="required">*</span></label>
                        <label class="field-sublabel">(Format: XXX-XX-XXXX or XX-XXXXXXX)</label>
                        <input type="text" name="tax_id_number" class="form-input" required value="{{ $w9Form?->tax_id_number ?? '' }}" placeholder="XXX-XX-XXXX" pattern="\d{3}-\d{2}-\d{4}|\d{2}-\d{7}">
                        <span class="error-message"></span>
                    </div>
                </div>

                <div class="form-row full">
                    <div class="form-field">
                        <label class="field-label">8. Exempt payee code (if any)</label>
                        <input type="text" name="exempt_payee_code" class="form-input" value="{{ $w9Form?->exempt_payee_code ?? '' }}" placeholder="Optional - if applicable">
                        <label class="field-sublabel">(If applicable, see the instructions above.)</label>
                    </div>
                </div>

                <div class="form-row full">
                    <div class="form-field">
                        <label class="field-label">9. Account number(s) (optional)</label>
                        <input type="text" name="account_numbers" class="form-input" value="{{ $w9Form?->account_numbers ?? '' }}" placeholder="Account reference numbers (if any)">
                    </div>
                </div>
            </div>

            <!-- Part III: Address -->
            <div class="form-section">
                <div class="section-title">Part III: Address</div>

                <div class="form-row full">
                    <div class="form-field">
                        <label class="field-label">10. Street address (optional) <span class="required">*</span></label>
                        <input type="text" name="street_address" class="form-input" required value="{{ $w9Form?->street_address ?? '' }}" placeholder="Street address">
                        <span class="error-message"></span>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-field">
                        <label class="field-label">11. City, state, and ZIP code <span class="required">*</span></label>

                        <input type="text" name="city" class="form-input" required value="{{ $w9Form?->city ?? '' }}" placeholder="City" style="margin-bottom: 5px;">
                        <span class="error-message"></span>

                        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 10px; margin-top: 5px;">
                            <input type="text" name="state" class="form-input" required value="{{ $w9Form?->state ?? '' }}" placeholder="State" maxlength="2" style="text-transform: uppercase;">
                            <input type="text" name="zip_code" class="form-input" required value="{{ $w9Form?->zip_code ?? '' }}" placeholder="ZIP" maxlength="10;">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Part IV: ID Verification -->
            <div class="form-section">
                <div class="section-title">Part IV: Government-Issued ID Verification <span class="required">*</span></div>

                <div class="id-upload-section">
                    <p style="font-size: 12px; margin-bottom: 15px;">
                        <strong>IMPORTANT:</strong> To verify your identity and process this form, you must provide clear images of both the front and back of a government-issued photo ID.
                    </p>

                    <div class="form-row full">
                        <div class="form-field">
                            <label class="field-label">12. Type of ID <span class="required">*</span></label>
                            <select name="id_document_type" class="form-input" required>
                                <option value="">-- Select ID Type --</option>
                                <option value="driver_license" {{ $w9Form?->id_document_type === 'driver_license' ? 'selected' : '' }}>Driver's License</option>
                                <option value="passport" {{ $w9Form?->id_document_type === 'passport' ? 'selected' : '' }}>Passport</option>
                                <option value="state_id" {{ $w9Form?->id_document_type === 'state_id' ? 'selected' : '' }}>State ID Card</option>
                                <option value="other" {{ $w9Form?->id_document_type === 'other' ? 'selected' : '' }}>Other Government-Issued ID</option>
                            </select>
                            <span class="error-message"></span>
                        </div>
                    </div>

                    <div class="form-row full">
                        <div class="form-field">
                            <label class="field-label">13. Front of ID <span class="required">*</span></label>
                            <div class="file-upload-area" onclick="document.getElementById('idFront').click();">
                                <div style="font-weight: bold; margin-bottom: 5px;">📤 Click to upload or drag and drop</div>
                                <div style="font-size: 11px; color: #666;">Front side of your government-issued ID</div>
                            </div>
                            <input type="file" id="idFront" name="id_front_image" accept="image/jpeg,image/png,image/jpg" required onchange="previewImage(this, 'idFrontPreview')">
                            <div id="idFrontPreview"></div>
                            <div class="file-specs">✓ JPG, PNG | ✓ Max 5 MB | ✓ Clear and Legible</div>
                            <span class="error-message"></span>
                        </div>
                    </div>

                    <div class="form-row full">
                        <div class="form-field">
                            <label class="field-label">14. Back of ID <span class="required">*</span></label>
                            <div class="file-upload-area" onclick="document.getElementById('idBack').click();">
                                <div style="font-weight: bold; margin-bottom: 5px;">📤 Click to upload or drag and drop</div>
                                <div style="font-size: 11px; color: #666;">Back side of your government-issued ID</div>
                            </div>
                            <input type="file" id="idBack" name="id_back_image" accept="image/jpeg,image/png,image/jpg" required onchange="previewImage(this, 'idBackPreview')">
                            <div id="idBackPreview"></div>
                            <div class="file-specs">✓ JPG, PNG | ✓ Max 5 MB | ✓ Clear and Legible</div>
                            <span class="error-message"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Part V: Certification -->
            <div class="form-section">
                <div class="section-title">Part V: Certification</div>

                <div class="certification-box">
                    <h3>Certification</h3>
                    <p>
                        <strong>Under penalties of perjury,</strong> I certify that:
                    </p>
                    <p style="margin-left: 20px;">
                        1. The number shown on this form is my correct taxpayer identification number (or I am waiting for a number to be issued to me), and
                    </p>
                    <p style="margin-left: 20px;">
                        2. I am not subject to backup withholding because: (a) I am exempt from U.S. income tax, or (b) I have not been notified by the Internal Revenue Service (IRS) that I am subject to backup withholding as a result of a failure to report all interest or dividends, or (c) the IRS has notified me that I am no longer subject to backup withholding, and
                    </p>
                    <p style="margin-left: 20px;">
                        3. I am a U.S. citizen or other U.S. person (defined in section 7701(a)(30) of the Internal Revenue Code).
                    </p>
                    <p style="margin: 15px 0 10px 0; font-style: italic;">
                        <strong>Certification instructions.</strong> You do not have to sign Form W–9, but you must provide your correct taxpayer identification number. Personal signature is not required. However, the IRS uses the information on your form to ensure that you are reporting the correct amount of tax. Providing fraudulent information may subject you to criminal penalties.
                    </p>
                </div>

                <div style="margin: 20px 0;">
                    <div class="checkbox-item">
                        <input type="checkbox" id="certCheck" name="certification_signed" required>
                        <label for="certCheck">
                            <strong>I certify under penalty of perjury that all information provided above is true, correct, and complete.</strong> I understand that providing false information may result in criminal penalties including fines and/or imprisonment.
                        </label>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="button-group hide-print">
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    ✓ Sign and Submit Form W-9
                </button>
            </div>

            <div class="loading" id="loader">
                <div class="spinner"></div>
                <p>Processing your Form W-9...</p>
            </div>

            <div class="footer-text">
                <strong>Privacy & Security Notice:</strong> CartVIP collects this information to verify your identity and process payments in accordance with IRS requirements. Your information is kept confidential and used only for tax compliance and payment processing purposes. We take your privacy seriously and follow all applicable data protection laws.
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function toggleOther() {
            const checked = document.querySelector('input[name="tax_classification"]:checked');
            const otherDiv = document.getElementById('otherClassification');
            if (checked && checked.value === 'other') {
                otherDiv.style.display = 'block';
            } else {
                otherDiv.style.display = 'none';
            }
        }

        function previewImage(input, previewId) {
            const previewDiv = document.getElementById(previewId);
            previewDiv.innerHTML = '';

            if (input.files && input.files[0]) {
                const file = input.files[0];

                if (file.size > 5242880) {
                    previewDiv.innerHTML = '<span class="error-message" style="display: block;">❌ File exceeds 5 MB limit</span>';
                    input.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    previewDiv.innerHTML = '<img src="' + e.target.result + '" class="preview-img" alt="Preview"><div style="font-size: 10px; color: #666; margin-top: 5px;">✓ ' + file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)</div>';
                };
                reader.readAsDataURL(file);
            }
        }

        $('#w9Form').on('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = document.getElementById('submitBtn');
            const loader = document.getElementById('loader');
            const successMsg = document.getElementById('successMsg');

            submitBtn.style.display = 'none';
            loader.style.display = 'block';

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    loader.style.display = 'none';
                    successMsg.style.display = 'block';
                    document.getElementById('w9Form').style.display = 'none';
                    setTimeout(() => location.reload(), 2000);
                },
                error: function(xhr) {
                    loader.style.display = 'none';
                    submitBtn.style.display = 'block';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        Object.keys(xhr.responseJSON.errors).forEach(field => {
                            const msg = xhr.responseJSON.errors[field][0];
                            const elem = $('[name="' + field + '"]').closest('.form-field').find('.error-message');
                            if (elem.length) {
                                elem.text('❌ ' + msg).show();
                            }
                        });
                    }
                }
            });
        });

        toggleOther();
    </script>
</body>
</html>
