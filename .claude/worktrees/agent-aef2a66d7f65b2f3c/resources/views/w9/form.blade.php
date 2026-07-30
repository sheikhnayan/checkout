<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form W-9 - CartVIP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --dark-base: #06090f;
            --dark-panel: #0c1120;
            --dark-input: #161e2e;
            --border: rgba(255,255,255,0.08);
            --text: #e8edf8;
            --muted: #8892a4;
        }
        body {
            background: var(--dark-base);
            color: var(--text);
            font-family: 'Times New Roman', serif;
            min-height: 100vh;
            padding: 20px;
        }
        .container-main { max-width: 900px; margin: 0 auto; }
        .form-header {
            background: #fff;
            color: #333;
            padding: 40px;
            border-radius: 0;
            margin-bottom: 0;
            text-align: center;
            border-bottom: 3px solid #000;
        }
        .form-header h1 {
            font-size: 36px;
            font-weight: bold;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .form-header .form-number {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
            font-family: Arial, sans-serif;
        }
        .form-header .irs-title {
            font-size: 12px;
            color: #000;
            margin-top: 10px;
            font-weight: bold;
            font-family: Arial, sans-serif;
        }
        .form-body {
            background: #fff;
            color: #333;
            padding: 30px;
            border-radius: 0;
        }
        .form-section {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #ccc;
        }
        .form-section:last-child {
            border-bottom: none;
        }
        .section-title {
            font-weight: bold;
            font-size: 13px;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-family: Arial, sans-serif;
        }
        .form-group-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 15px;
        }
        .form-group-row.full {
            grid-template-columns: 1fr;
        }
        @media (max-width: 768px) {
            .form-group-row {
                grid-template-columns: 1fr;
            }
        }
        .form-group-item {
            display: flex;
            flex-direction: column;
        }
        .form-label {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 5px;
            font-family: Arial, sans-serif;
        }
        .form-input {
            border: 1px solid #333;
            padding: 8px;
            font-size: 13px;
            background: #fff;
            color: #333;
            font-family: Arial, sans-serif;
        }
        .form-input::placeholder {
            color: #999;
        }
        .form-input:focus {
            outline: none;
            border: 2px solid #0066cc;
            box-shadow: none;
        }
        .checkbox-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 10px;
            font-size: 12px;
            line-height: 1.6;
        }
        .checkbox-item input[type="radio"],
        .checkbox-item input[type="checkbox"] {
            margin-right: 8px;
            margin-top: 2px;
            cursor: pointer;
        }
        .checkbox-item label {
            margin-bottom: 0;
            cursor: pointer;
            font-size: 12px;
        }
        .info-box {
            background: #f5f5f5;
            border-left: 3px solid #0066cc;
            padding: 12px;
            margin: 15px 0;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
        }
        .instructions-box {
            background: #ffffcc;
            border: 1px solid #ffcc00;
            padding: 15px;
            margin: 20px 0;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
        }
        .instructions-box h4 {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 10px;
            font-family: Arial, sans-serif;
        }
        .instructions-box ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        .instructions-box li {
            margin-bottom: 8px;
        }
        .required-indicator {
            color: #d32f2f;
            font-weight: bold;
        }
        .address-group {
            margin-bottom: 15px;
        }
        .address-line {
            font-size: 11px;
            color: #666;
            margin-top: 2px;
        }
        .file-upload-area {
            border: 2px dashed #0066cc;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            background: #f9f9f9;
            margin: 15px 0;
            border-radius: 4px;
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
            margin-top: 10px;
        }
        .file-name {
            font-size: 11px;
            color: #666;
            margin-top: 8px;
        }
        .btn-submit {
            background: #0066cc;
            color: #fff;
            padding: 10px 30px;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
            font-size: 13px;
            font-family: Arial, sans-serif;
            margin-top: 20px;
        }
        .btn-submit:hover {
            background: #0052a3;
        }
        .btn-submit:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        .certification-box {
            background: #fff;
            border: 2px solid #333;
            padding: 15px;
            margin: 20px 0;
            page-break-inside: avoid;
        }
        .certification-box h4 {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 12px;
            font-family: Arial, sans-serif;
        }
        .certification-box p {
            font-size: 12px;
            line-height: 1.6;
            margin-bottom: 12px;
        }
        .loader {
            display: none;
            text-align: center;
            padding: 20px;
        }
        .success-message {
            background: #dff0d8;
            border: 1px solid #d6e9c6;
            color: #3c763d;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            display: none;
        }
        .error-text {
            color: #d32f2f;
            font-size: 11px;
            margin-top: 4px;
            display: none;
        }
        .two-col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        @media (max-width: 768px) {
            .two-col {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container-main">
        <div class="form-header">
            <div class="irs-title">Internal Revenue Service</div>
            <h1>Form W-9</h1>
            <div class="form-number">Request for Taxpayer Identification Number and Certification</div>
            <div style="font-size: 11px; margin-top: 10px; color: #666;">CartVIP Verification Form</div>
        </div>

        <div class="form-body">
            <div class="success-message" id="successMsg">
                <i class="fas fa-check-circle"></i> <strong>Success!</strong> Your W-9 form has been submitted successfully and is pending review.
            </div>

            <div class="instructions-box">
                <h4><i class="fas fa-info-circle"></i> Important Information</h4>
                <ul>
                    <li><strong>File below by:</strong> Complete all sections with accurate information. Incomplete forms will be returned.</li>
                    <li><strong>Penalties:</strong> Failure to complete this form may delay account activation and restrict payment processing.</li>
                    <li><strong>Legal Certification:</strong> You declare under penalty of perjury that the information is true and correct.</li>
                </ul>
            </div>

            <form id="w9Form" method="POST" action="{{ route('w9.store', $token) }}" enctype="multipart/form-data">
                @csrf

                <!-- Part I: Taxpayer Identification Number (TIN) -->
                <div class="form-section">
                    <div class="section-title">Part I: Taxpayer Identification Number (TIN)</div>

                    <div class="form-group-row full">
                        <div class="form-group-item">
                            <label class="form-label">Full Legal Name <span class="required-indicator">*</span></label>
                            <label class="address-line">(Enter the name exactly as shown on your income tax return)</label>
                            <input type="text" name="full_name" class="form-input" required value="{{ $w9Form?->full_name ?? '' }}" placeholder="First Name, Middle Initial, Last Name">
                            <div class="error-text"></div>
                        </div>
                    </div>

                    <div class="form-group-row full">
                        <div class="form-group-item">
                            <label class="form-label">Business Name / DBA (if different from above)</label>
                            <label class="address-line">(Doing Business As)</label>
                            <input type="text" name="business_name" class="form-input" value="{{ $w9Form?->business_name ?? '' }}" placeholder="Leave blank if same as legal name">
                            <div class="error-text"></div>
                        </div>
                    </div>

                    <div class="info-box">
                        <strong>Select Tax Classification:</strong> Check the box that best describes your tax classification.
                    </div>

                    <div class="form-group-row full">
                        <div class="form-group-item">
                            <label class="form-label">Federal Income Tax Classification <span class="required-indicator">*</span></label>

                            <div class="checkbox-item">
                                <input type="radio" id="tc1" name="tax_classification" value="individual" required {{ $w9Form?->tax_classification === 'individual' ? 'checked' : '' }} onchange="toggleOther()">
                                <label for="tc1">☐ Individual / Sole Proprietor</label>
                            </div>

                            <div class="checkbox-item">
                                <input type="radio" id="tc2" name="tax_classification" value="c_corporation" {{ $w9Form?->tax_classification === 'c_corporation' ? 'checked' : '' }} onchange="toggleOther()">
                                <label for="tc2">☐ C Corporation</label>
                            </div>

                            <div class="checkbox-item">
                                <input type="radio" id="tc3" name="tax_classification" value="s_corporation" {{ $w9Form?->tax_classification === 's_corporation' ? 'checked' : '' }} onchange="toggleOther()">
                                <label for="tc3">☐ S Corporation</label>
                            </div>

                            <div class="checkbox-item">
                                <input type="radio" id="tc4" name="tax_classification" value="partnership" {{ $w9Form?->tax_classification === 'partnership' ? 'checked' : '' }} onchange="toggleOther()">
                                <label for="tc4">☐ Partnership</label>
                            </div>

                            <div class="checkbox-item">
                                <input type="radio" id="tc5" name="tax_classification" value="trust_estate" {{ $w9Form?->tax_classification === 'trust_estate' ? 'checked' : '' }} onchange="toggleOther()">
                                <label for="tc5">☐ Trust / Estate</label>
                            </div>

                            <div class="checkbox-item">
                                <input type="radio" id="tc6" name="tax_classification" value="limited_liability_company_c" {{ $w9Form?->tax_classification === 'limited_liability_company_c' ? 'checked' : '' }} onchange="toggleOther()">
                                <label for="tc6">☐ Limited Liability Company - Taxed as C Corporation</label>
                            </div>

                            <div class="checkbox-item">
                                <input type="radio" id="tc7" name="tax_classification" value="limited_liability_company_s" {{ $w9Form?->tax_classification === 'limited_liability_company_s' ? 'checked' : '' }} onchange="toggleOther()">
                                <label for="tc7">☐ Limited Liability Company - Taxed as S Corporation</label>
                            </div>

                            <div class="checkbox-item">
                                <input type="radio" id="tc8" name="tax_classification" value="limited_liability_company_individual" {{ $w9Form?->tax_classification === 'limited_liability_company_individual' ? 'checked' : '' }} onchange="toggleOther()">
                                <label for="tc8">☐ Limited Liability Company - Individual</label>
                            </div>

                            <div class="checkbox-item">
                                <input type="radio" id="tc9" name="tax_classification" value="other" {{ $w9Form?->tax_classification === 'other' ? 'checked' : '' }} onchange="toggleOther()">
                                <label for="tc9">☐ Other</label>
                            </div>

                            <div id="otherClassification" style="display: none; margin-top: 10px;">
                                <input type="text" name="tax_classification_other" class="form-input" placeholder="Please specify your tax classification">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Part II: Tax Identification Number (TIN) -->
                <div class="form-section">
                    <div class="section-title">Part II: Tax Identification Number (TIN)</div>

                    <div class="info-box">
                        <strong>Important:</strong> Provide your correct TIN. If you do not provide the correct TIN, you may be subject to penalties, and payments may be delayed.
                    </div>

                    <div class="form-group-row">
                        <div class="form-group-item">
                            <label class="form-label">TIN Type <span class="required-indicator">*</span></label>
                            <select name="tax_id_type" class="form-input" required>
                                <option value="">-- Select Type --</option>
                                <option value="ssn" {{ $w9Form?->tax_id_type === 'ssn' ? 'selected' : '' }}>Social Security Number (SSN)</option>
                                <option value="ein" {{ $w9Form?->tax_id_type === 'ein' ? 'selected' : '' }}>Employer Identification Number (EIN)</option>
                            </select>
                            <div class="error-text"></div>
                        </div>

                        <div class="form-group-item">
                            <label class="form-label">TIN <span class="required-indicator">*</span></label>
                            <label class="address-line">(Format: XXX-XX-XXXX for SSN, XX-XXXXXXX for EIN)</label>
                            <input type="text" name="tax_id_number" class="form-input" required value="{{ $w9Form?->tax_id_number ?? '' }}" placeholder="XXX-XX-XXXX" pattern="\d{3}-\d{2}-\d{4}|\d{2}-\d{7}">
                            <div class="error-text"></div>
                        </div>
                    </div>
                </div>

                <!-- Part III: Address -->
                <div class="form-section">
                    <div class="section-title">Part III: Address</div>

                    <div class="form-group-row full">
                        <div class="form-group-item">
                            <label class="form-label">Street Address <span class="required-indicator">*</span></label>
                            <input type="text" name="street_address" class="form-input" required value="{{ $w9Form?->street_address ?? '' }}" placeholder="Street address (P.O. Box if applicable)">
                            <div class="error-text"></div>
                        </div>
                    </div>

                    <div class="two-col">
                        <div class="form-group-item">
                            <label class="form-label">City <span class="required-indicator">*</span></label>
                            <input type="text" name="city" class="form-input" required value="{{ $w9Form?->city ?? '' }}" placeholder="City">
                            <div class="error-text"></div>
                        </div>

                        <div class="form-group-item">
                            <label class="form-label">State <span class="required-indicator">*</span></label>
                            <input type="text" name="state" class="form-input" required value="{{ $w9Form?->state ?? '' }}" placeholder="State" maxlength="2" style="text-transform: uppercase;">
                            <div class="error-text"></div>
                        </div>
                    </div>

                    <div class="form-group-row full">
                        <div class="form-group-item">
                            <label class="form-label">ZIP Code <span class="required-indicator">*</span></label>
                            <input type="text" name="zip_code" class="form-input" required value="{{ $w9Form?->zip_code ?? '' }}" placeholder="ZIP code" maxlength="10">
                            <div class="error-text"></div>
                        </div>
                    </div>
                </div>

                <!-- Part IV: Optional Information -->
                <div class="form-section">
                    <div class="section-title">Part IV: Optional Information</div>

                    <div class="form-group-row full">
                        <div class="form-group-item">
                            <label class="form-label">Account Numbers (for your records)</label>
                            <input type="text" name="account_numbers" class="form-input" value="{{ $w9Form?->account_numbers ?? '' }}" placeholder="If this form is for an account, enter account number(s)">
                        </div>
                    </div>

                    <div class="two-col">
                        <div class="form-group-item">
                            <label class="form-label">Exempt Payee Code (if applicable)</label>
                            <input type="text" name="exempt_payee_code" class="form-input" value="{{ $w9Form?->exempt_payee_code ?? '' }}" placeholder="Enter code if applicable">
                            <label class="address-line">Only if you are exempt from backup withholding</label>
                        </div>

                        <div class="form-group-item">
                            <label class="form-label">FATCA Exemption Code (if applicable)</label>
                            <input type="text" name="fatca_exemption_code" class="form-input" value="{{ $w9Form?->fatca_exemption_code ?? '' }}" placeholder="Enter code if applicable">
                            <label class="address-line">Exemption from FATCA reporting code</label>
                        </div>
                    </div>

                    <div class="form-group-row">
                        <div class="form-group-item">
                            <label class="form-label">Requester Name</label>
                            <input type="text" name="requester_name" class="form-input" value="{{ $w9Form?->requester_name ?? '' }}" placeholder="Name of person or organization requesting this form">
                        </div>

                        <div class="form-group-item">
                            <label class="form-label">Requester Phone</label>
                            <input type="tel" name="requester_phone" class="form-input" value="{{ $w9Form?->requester_phone ?? '' }}" placeholder="(XXX) XXX-XXXX">
                        </div>
                    </div>

                    <div class="form-group-row full">
                        <div class="form-group-item">
                            <label class="form-label">Requester Email</label>
                            <input type="email" name="requester_email" class="form-input" value="{{ $w9Form?->requester_email ?? '' }}" placeholder="email@example.com">
                        </div>
                    </div>
                </div>

                <!-- Part V: Government-Issued ID Verification -->
                <div class="form-section">
                    <div class="section-title">Part V: Government-Issued ID Verification <span class="required-indicator">*</span></div>

                    <div class="info-box">
                        <strong>Required for Verification:</strong> CartVIP requires government-issued ID verification to process this W-9 form. Please provide clear, legible photos of both the front and back of your ID.
                    </div>

                    <div class="form-group-row full">
                        <div class="form-group-item">
                            <label class="form-label">Type of Government-Issued ID <span class="required-indicator">*</span></label>
                            <select name="id_document_type" class="form-input" required>
                                <option value="">-- Select ID Type --</option>
                                <option value="driver_license" {{ $w9Form?->id_document_type === 'driver_license' ? 'selected' : '' }}>Driver's License</option>
                                <option value="passport" {{ $w9Form?->id_document_type === 'passport' ? 'selected' : '' }}>Passport</option>
                                <option value="state_id" {{ $w9Form?->id_document_type === 'state_id' ? 'selected' : '' }}>State ID Card</option>
                                <option value="other" {{ $w9Form?->id_document_type === 'other' ? 'selected' : '' }}>Other Government-Issued ID</option>
                            </select>
                            <div class="error-text"></div>
                        </div>
                    </div>

                    <!-- ID Front Upload -->
                    <div class="form-group-row full">
                        <div class="form-group-item">
                            <label class="form-label">Front of ID <span class="required-indicator">*</span></label>
                            <div class="file-upload-area" onclick="document.getElementById('idFront').click();">
                                <i class="fas fa-cloud-upload-alt" style="font-size: 32px; color: #0066cc; margin-bottom: 10px;"></i>
                                <div style="font-weight: bold;">Click to upload or drag and drop</div>
                                <div style="font-size: 11px; color: #666;">Front side of your ID</div>
                            </div>
                            <input type="file" id="idFront" name="id_front_image" accept="image/jpeg,image/png,image/jpg" required onchange="previewImage(this, 'idFrontPreview')">
                            <div id="idFrontPreview"></div>
                            <div style="font-size: 11px; color: #666; margin-top: 10px;">
                                <strong>Accepted Formats:</strong> JPG, JPEG, PNG | <strong>Max Size:</strong> 5 MB | <strong>Quality:</strong> Clear and legible
                            </div>
                            <div class="error-text"></div>
                        </div>
                    </div>

                    <!-- ID Back Upload -->
                    <div class="form-group-row full">
                        <div class="form-group-item">
                            <label class="form-label">Back of ID <span class="required-indicator">*</span></label>
                            <div class="file-upload-area" onclick="document.getElementById('idBack').click();">
                                <i class="fas fa-cloud-upload-alt" style="font-size: 32px; color: #0066cc; margin-bottom: 10px;"></i>
                                <div style="font-weight: bold;">Click to upload or drag and drop</div>
                                <div style="font-size: 11px; color: #666;">Back side of your ID</div>
                            </div>
                            <input type="file" id="idBack" name="id_back_image" accept="image/jpeg,image/png,image/jpg" required onchange="previewImage(this, 'idBackPreview')">
                            <div id="idBackPreview"></div>
                            <div style="font-size: 11px; color: #666; margin-top: 10px;">
                                <strong>Accepted Formats:</strong> JPG, JPEG, PNG | <strong>Max Size:</strong> 5 MB | <strong>Quality:</strong> Clear and legible
                            </div>
                            <div class="error-text"></div>
                        </div>
                    </div>
                </div>

                <!-- Part VI: Certification -->
                <div class="form-section">
                    <div class="section-title">Part VI: Certification</div>

                    <div class="certification-box">
                        <h4>Under Penalties of Perjury</h4>
                        <p>
                            I declare that I am a U.S. citizen or other U.S. person (as defined in section 7701(a)(30)) and that the information I have provided on this form is true and correct.
                        </p>
                        <p style="font-weight: bold;">
                            Willfully providing false information on this form subjects you to penalties. Under Section 1621 of the Internal Revenue Code, making a false statement or providing false information on this form may result in criminal penalties including fines and/or imprisonment.
                        </p>
                        <p style="font-size: 11px; margin-bottom: 0;">
                            I understand that false information can result in civil penalties including damages and treble damages, under sections 1001, 1341, and 1346 of Title 18 of the United States Code.
                        </p>
                    </div>

                    <div class="checkbox-item" style="margin: 20px 0;">
                        <input type="checkbox" id="certCheck" name="certification_signed" required>
                        <label for="certCheck">
                            I certify under penalty of perjury that I am a U.S. person and that the information provided above is true, correct, and complete. I understand that false information subjects me to criminal and civil penalties.
                        </label>
                    </div>
                </div>

                <!-- Sign and Submit -->
                <div class="form-section">
                    <button type="submit" class="btn-submit" id="submitBtn">
                        <i class="fas fa-paper-plane"></i> Sign and Submit Form W-9
                    </button>
                    <div class="loader" id="loader">
                        <div style="display: inline-block; width: 20px; height: 20px; border: 2px solid #0066cc; border-top: 2px solid transparent; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                        <p style="margin-top: 10px; font-size: 12px;">Submitting your W-9 form...</p>
                    </div>
                </div>

                <div style="font-size: 11px; color: #666; margin-top: 20px; padding-top: 20px; border-top: 1px solid #ccc;">
                    <strong>Privacy Notice:</strong> CartVIP uses the information collected on this form to verify your identity and process payments. Your information will be kept confidential and only used for tax compliance and payment processing purposes.
                </div>
            </form>
        </div>
    </div>

    <style>
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function toggleOther() {
            const classification = document.querySelector('input[name="tax_classification"]:checked').value;
            const otherDiv = document.getElementById('otherClassification');
            if (classification === 'other') {
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

                // Validate file size (5MB)
                if (file.size > 5242880) {
                    previewDiv.innerHTML = '<div style="color: #d32f2f; font-size: 12px;"><i class="fas fa-exclamation-circle"></i> File is larger than 5 MB</div>';
                    input.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    previewDiv.innerHTML = `<img src="${e.target.result}" class="preview-img" alt="Preview"><div class="file-name"><i class="fas fa-check-circle" style="color: #28a745;"></i> ${file.name} (${(file.size / 1024).toFixed(1)} KB)</div>`;
                };
                reader.readAsDataURL(file);
            }
        }

        $('#w9Form').on('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = document.getElementById('submitBtn');
            const loader = document.getElementById('loader');

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
                    document.getElementById('successMsg').style.display = 'block';
                    document.getElementById('w9Form').style.display = 'none';
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                },
                error: function(xhr) {
                    loader.style.display = 'none';
                    submitBtn.style.display = 'block';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        Object.keys(xhr.responseJSON.errors).forEach(field => {
                            const errorMsg = xhr.responseJSON.errors[field][0];
                            $(`[name="${field}"]`).closest('.form-group-item').find('.error-text').text(errorMsg).show();
                        });
                    }
                }
            });
        });

        // Initialize
        toggleOther();
    </script>
</body>
</html>
