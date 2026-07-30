<!DOCTYPE html>
<html lang="en">
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
            background: linear-gradient(to bottom, #f5f5f5, #e8e8e8);
            padding: 20px;
            line-height: 1.4;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        @media (max-width: 1024px) {
            .container {
                grid-template-columns: 1fr;
            }
        }

        .pdf-viewer {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            position: sticky;
            top: 20px;
            max-height: 90vh;
            overflow-y: auto;
        }

        .pdf-viewer h3 {
            margin-bottom: 15px;
            color: #333;
            font-size: 14px;
        }

        .pdf-embed {
            width: 100%;
            height: 700px;
            border: 2px solid #ddd;
            border-radius: 6px;
        }

        .form-section {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }

        .form-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #0066cc;
        }

        .form-header h1 {
            font-size: 24px;
            color: #0066cc;
            margin-bottom: 5px;
        }

        .form-header p {
            color: #666;
            font-size: 14px;
        }

        .section-title {
            background: #f0f4f8;
            padding: 12px 15px;
            margin: 25px 0 15px 0;
            border-left: 4px solid #0066cc;
            font-weight: bold;
            font-size: 13px;
            color: #0066cc;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 15px;
        }

        .form-row.full {
            grid-template-columns: 1fr;
        }

        .form-field {
            display: flex;
            flex-direction: column;
        }

        .field-label {
            font-weight: 600;
            font-size: 12px;
            color: #333;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .field-hint {
            font-size: 11px;
            color: #999;
            margin-bottom: 3px;
            font-style: italic;
        }

        .form-input {
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 13px;
            font-family: Arial, sans-serif;
            transition: border-color 0.3s;
        }

        .form-input:focus {
            outline: none;
            border-color: #0066cc;
            background: #f9f9ff;
            box-shadow: inset 0 0 4px rgba(0, 102, 204, 0.1);
        }

        select.form-input {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23333' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            padding-right: 30px;
        }

        .checkbox-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .checkbox-item {
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }

        .checkbox-item input[type="radio"],
        .checkbox-item input[type="checkbox"] {
            margin-top: 4px;
            cursor: pointer;
            width: 18px;
            height: 18px;
        }

        .checkbox-item label {
            cursor: pointer;
            font-size: 12px;
            line-height: 1.5;
            user-select: none;
        }

        .file-upload-section {
            background: #f0f8ff;
            border: 2px solid #87ceeb;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }

        .file-upload-title {
            font-weight: bold;
            font-size: 13px;
            color: #0066cc;
            margin-bottom: 15px;
        }

        .file-upload-area {
            border: 2px dashed #0066cc;
            border-radius: 6px;
            padding: 25px;
            text-align: center;
            cursor: pointer;
            background: white;
            transition: all 0.3s;
            margin-bottom: 15px;
        }

        .file-upload-area:hover {
            background: #f0f8ff;
            border-color: #003d99;
        }

        .file-upload-area input[type="file"] {
            display: none;
        }

        .upload-icon {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .upload-text {
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }

        .upload-hint {
            font-size: 11px;
            color: #666;
        }

        .preview-img {
            max-width: 150px;
            max-height: 150px;
            border: 1px solid #0066cc;
            border-radius: 4px;
            margin-top: 10px;
        }

        .file-specs {
            font-size: 11px;
            color: #666;
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px solid #ddd;
        }

        .certification-section {
            background: #fff3cd;
            border: 2px solid #ffc107;
            border-radius: 6px;
            padding: 20px;
            margin: 25px 0;
        }

        .certification-title {
            font-weight: bold;
            color: #856404;
            margin-bottom: 15px;
            font-size: 13px;
        }

        .certification-text {
            font-size: 12px;
            color: #666;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .certification-checkbox {
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .certification-checkbox input[type="checkbox"] {
            margin-top: 3px;
            width: 20px;
            height: 20px;
            cursor: pointer;
        }

        .certification-checkbox label {
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            color: #333;
            line-height: 1.5;
        }

        .required {
            color: #dc3545;
            font-weight: bold;
        }

        .button-group {
            display: flex;
            gap: 12px;
            margin-top: 30px;
            justify-content: center;
        }

        .btn {
            padding: 12px 30px;
            font-size: 14px;
            font-weight: bold;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-primary {
            background: #0066cc;
            color: white;
        }

        .btn-primary:hover {
            background: #0052a3;
            box-shadow: 0 4px 8px rgba(0, 102, 204, 0.3);
        }

        .btn-primary:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .success-message {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            display: none;
            text-align: center;
            font-weight: bold;
        }

        .error-message {
            color: #dc3545;
            font-size: 11px;
            margin-top: 4px;
            display: none;
        }

        .loading {
            text-align: center;
            padding: 30px;
            display: none;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #0066cc;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 15px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #0066cc;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .info-box p {
            font-size: 12px;
            color: #0066cc;
            margin: 0;
            line-height: 1.5;
        }

        @media print {
            .pdf-viewer {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- PDF Viewer -->
        <div class="pdf-viewer">
            <h3>📄 Official IRS Form W-9</h3>
            <iframe class="pdf-embed" src="https://www.irs.gov/pub/irs-pdf/fw9.pdf" frameborder="0"></iframe>
        </div>

        <!-- Form -->
        <div class="form-section">
            <div class="form-header">
                <h1>W-9 Form Submission</h1>
                <p>Complete all required fields and submit</p>
            </div>

            <div class="info-box">
                <p>⚠️ <strong>Important:</strong> Fill out this form completely. The data you enter will be used to fill the official IRS Form W-9. Government ID images are required.</p>
            </div>

            <div class="success-message" id="successMsg">
                ✓ Your Form W-9 has been submitted successfully!
            </div>

            <form id="w9Form" method="POST" action="{{ route('w9.store', $token) }}" enctype="multipart/form-data">
                @csrf

                <!-- Part I: Name -->
                <div class="section-title">Part I: Your Information</div>

                <div class="form-row full">
                    <div class="form-field">
                        <label class="field-label">1. Full Name <span class="required">*</span></label>
                        <label class="field-hint">As shown on your income tax return</label>
                        <input type="text" name="full_name" class="form-input" required value="{{ $w9Form?->full_name ?? '' }}" placeholder="First Name, Middle Initial, Last Name">
                        <span class="error-message"></span>
                    </div>
                </div>

                <div class="form-row full">
                    <div class="form-field">
                        <label class="field-label">2. Business Name/DBA (if different)</label>
                        <input type="text" name="business_name" class="form-input" value="{{ $w9Form?->business_name ?? '' }}" placeholder="Leave blank if same as above">
                        <span class="error-message"></span>
                    </div>
                </div>

                <!-- Requester Info -->
                <div class="section-title">Part II: Requester Information</div>

                <div class="form-row">
                    <div class="form-field">
                        <label class="field-label">Requester Name <span class="required">*</span></label>
                        <input type="text" name="requester_name" class="form-input" required value="{{ $w9Form?->requester_name ?? '' }}" placeholder="Name of person/entity requesting form">
                        <span class="error-message"></span>
                    </div>
                    <div class="form-field">
                        <label class="field-label">Requester Phone</label>
                        <input type="tel" name="requester_phone" class="form-input" value="{{ $w9Form?->requester_phone ?? '' }}" placeholder="(XXX) XXX-XXXX">
                        <span class="error-message"></span>
                    </div>
                </div>

                <div class="form-row full">
                    <div class="form-field">
                        <label class="field-label">Requester Email</label>
                        <input type="email" name="requester_email" class="form-input" value="{{ $w9Form?->requester_email ?? '' }}" placeholder="email@example.com">
                        <span class="error-message"></span>
                    </div>
                </div>

                <!-- Tax Classification -->
                <div class="section-title">Part III: Tax Classification</div>

                <div class="form-row full">
                    <div class="form-field">
                        <label class="field-label">Tax Classification <span class="required">*</span></label>
                        <select name="tax_classification" class="form-input" required onchange="toggleOther()">
                            <option value="">-- Select Classification --</option>
                            <option value="individual" {{ $w9Form?->tax_classification === 'individual' ? 'selected' : '' }}>Individual/Sole Proprietor or Single-Member LLC</option>
                            <option value="c_corporation" {{ $w9Form?->tax_classification === 'c_corporation' ? 'selected' : '' }}>C Corporation</option>
                            <option value="s_corporation" {{ $w9Form?->tax_classification === 's_corporation' ? 'selected' : '' }}>S Corporation</option>
                            <option value="partnership" {{ $w9Form?->tax_classification === 'partnership' ? 'selected' : '' }}>Partnership</option>
                            <option value="trust_estate" {{ $w9Form?->tax_classification === 'trust_estate' ? 'selected' : '' }}>Trust/Estate</option>
                            <option value="limited_liability_company_c" {{ $w9Form?->tax_classification === 'limited_liability_company_c' ? 'selected' : '' }}>LLC – Taxed as C Corporation</option>
                            <option value="limited_liability_company_s" {{ $w9Form?->tax_classification === 'limited_liability_company_s' ? 'selected' : '' }}>LLC – Taxed as S Corporation</option>
                            <option value="limited_liability_company_individual" {{ $w9Form?->tax_classification === 'limited_liability_company_individual' ? 'selected' : '' }}>LLC – Individual</option>
                            <option value="other" {{ $w9Form?->tax_classification === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        <span class="error-message"></span>

                        <div id="otherClassification" style="display: none; margin-top: 10px;">
                            <input type="text" name="tax_classification_other" class="form-input" placeholder="Please specify your tax classification">
                        </div>
                    </div>
                </div>

                <!-- Tax ID -->
                <div class="section-title">Part IV: Tax Identification Number</div>

                <div class="form-row">
                    <div class="form-field">
                        <label class="field-label">Tax ID Type <span class="required">*</span></label>
                        <div class="checkbox-group">
                            <div class="checkbox-item">
                                <input type="radio" id="tin1" name="tax_id_type" value="ssn" required {{ $w9Form?->tax_id_type === 'ssn' ? 'checked' : '' }}>
                                <label for="tin1">Social Security Number (SSN)</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="radio" id="tin2" name="tax_id_type" value="ein" {{ $w9Form?->tax_id_type === 'ein' ? 'checked' : '' }}>
                                <label for="tin2">Employer ID Number (EIN)</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-field">
                        <label class="field-label">Tax ID Number <span class="required">*</span></label>
                        <label class="field-hint">SSN: XXX-XX-XXXX | EIN: XX-XXXXXXX</label>
                        <input type="text" name="tax_id_number" class="form-input" required value="{{ $w9Form?->tax_id_number ?? '' }}" placeholder="XXX-XX-XXXX">
                        <span class="error-message"></span>
                    </div>
                </div>

                <div class="form-row full">
                    <div class="form-field">
                        <label class="field-label">Account Numbers (optional)</label>
                        <input type="text" name="account_numbers" class="form-input" value="{{ $w9Form?->account_numbers ?? '' }}" placeholder="For your records">
                        <span class="error-message"></span>
                    </div>
                </div>

                <!-- Optional Fields -->
                <div class="form-row">
                    <div class="form-field">
                        <label class="field-label">Exempt Payee Code</label>
                        <input type="text" name="exempt_payee_code" class="form-input" value="{{ $w9Form?->exempt_payee_code ?? '' }}" placeholder="If applicable">
                    </div>
                    <div class="form-field">
                        <label class="field-label">FATCA Exemption Code</label>
                        <input type="text" name="fatca_exemption_code" class="form-input" value="{{ $w9Form?->fatca_exemption_code ?? '' }}" placeholder="If applicable">
                    </div>
                </div>

                <!-- Address -->
                <div class="section-title">Part V: Address</div>

                <div class="form-row full">
                    <div class="form-field">
                        <label class="field-label">Street Address <span class="required">*</span></label>
                        <input type="text" name="street_address" class="form-input" required value="{{ $w9Form?->street_address ?? '' }}" placeholder="Street address">
                        <span class="error-message"></span>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-field">
                        <label class="field-label">City <span class="required">*</span></label>
                        <input type="text" name="city" class="form-input" required value="{{ $w9Form?->city ?? '' }}" placeholder="City">
                        <span class="error-message"></span>
                    </div>
                    <div class="form-field">
                        <label class="field-label">State/Province <span class="required">*</span></label>
                        <input type="text" name="state" class="form-input" required value="{{ $w9Form?->state ?? '' }}" placeholder="State" maxlength="2" style="text-transform: uppercase;">
                        <span class="error-message"></span>
                    </div>
                </div>

                <div class="form-row full">
                    <div class="form-field">
                        <label class="field-label">ZIP Code <span class="required">*</span></label>
                        <input type="text" name="zip_code" class="form-input" required value="{{ $w9Form?->zip_code ?? '' }}" placeholder="ZIP/Postal Code" maxlength="10">
                        <span class="error-message"></span>
                    </div>
                </div>

                <!-- ID Upload -->
                <div class="section-title">Part VI: Government-Issued ID Verification <span class="required">*</span></div>

                <div class="file-upload-section">
                    <div class="file-upload-title">📸 Upload Clear Images of Your Government-Issued ID</div>

                    <div class="form-row full">
                        <div class="form-field">
                            <label class="field-label">ID Document Type <span class="required">*</span></label>
                            <select name="id_document_type" class="form-input" required>
                                <option value="">-- Select ID Type --</option>
                                <option value="driver_license" {{ $w9Form?->id_document_type === 'driver_license' ? 'selected' : '' }}>Driver's License</option>
                                <option value="passport" {{ $w9Form?->id_document_type === 'passport' ? 'selected' : '' }}>Passport</option>
                                <option value="state_id" {{ $w9Form?->id_document_type === 'state_id' ? 'selected' : '' }}>State ID Card</option>
                                <option value="other" {{ $w9Form?->id_document_type === 'other' ? 'selected' : '' }}>Other Government ID</option>
                            </select>
                            <span class="error-message"></span>
                        </div>
                    </div>

                    <div class="form-row full">
                        <div class="form-field">
                            <label class="field-label">Front of ID <span class="required">*</span></label>
                            <div class="file-upload-area" onclick="document.getElementById('idFront').click();">
                                <div class="upload-icon">📤</div>
                                <div class="upload-text">Click to upload or drag and drop</div>
                                <div class="upload-hint">Front side of your government-issued ID</div>
                            </div>
                            <input type="file" id="idFront" name="id_front_image" accept="image/jpeg,image/png,image/jpg" required onchange="previewImage(this, 'idFrontPreview')">
                            <div id="idFrontPreview"></div>
                            <div class="file-specs">✓ JPG or PNG | ✓ Max 5 MB | ✓ Clear and Legible</div>
                            <span class="error-message"></span>
                        </div>
                    </div>

                    <div class="form-row full">
                        <div class="form-field">
                            <label class="field-label">Back of ID <span class="required">*</span></label>
                            <div class="file-upload-area" onclick="document.getElementById('idBack').click();">
                                <div class="upload-icon">📤</div>
                                <div class="upload-text">Click to upload or drag and drop</div>
                                <div class="upload-hint">Back side of your government-issued ID</div>
                            </div>
                            <input type="file" id="idBack" name="id_back_image" accept="image/jpeg,image/png,image/jpg" required onchange="previewImage(this, 'idBackPreview')">
                            <div id="idBackPreview"></div>
                            <div class="file-specs">✓ JPG or PNG | ✓ Max 5 MB | ✓ Clear and Legible</div>
                            <span class="error-message"></span>
                        </div>
                    </div>
                </div>

                <!-- Certification -->
                <div class="certification-section">
                    <div class="certification-title">⚠️ Certification</div>
                    <div class="certification-text">
                        Under penalties of perjury, I certify that the information provided above is true and complete. The Tax ID number I provided is correct. I understand that providing false information may result in criminal penalties including fines and/or imprisonment.
                    </div>
                    <div class="certification-checkbox">
                        <input type="checkbox" id="certCheck" name="certification_signed" required>
                        <label for="certCheck">I certify this information is accurate and complete</label>
                    </div>
                </div>

                <!-- Submit -->
                <div class="button-group">
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        ✓ Submit Form W-9
                    </button>
                </div>

                <div class="loading" id="loader">
                    <div class="spinner"></div>
                    <p>Processing your Form W-9...</p>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function toggleOther() {
            const selected = document.querySelector('select[name="tax_classification"]').value;
            document.getElementById('otherClassification').style.display = selected === 'other' ? 'block' : 'none';
        }

        function previewImage(input, previewId) {
            const previewDiv = document.getElementById(previewId);
            previewDiv.innerHTML = '';

            if (input.files && input.files[0]) {
                const file = input.files[0];

                if (file.size > 5242880) {
                    previewDiv.innerHTML = '<span class="error-message" style="display: block; margin-top: 10px;">❌ File exceeds 5 MB limit</span>';
                    input.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    previewDiv.innerHTML = '<img src="' + e.target.result + '" class="preview-img" alt="Preview"><div style="font-size: 11px; color: #666; margin-top: 8px;">✓ ' + file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)</div>';
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
                    setTimeout(() => {
                        window.location.href = '/';
                    }, 2000);
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
                    } else {
                        alert('Error submitting form. Please try again.');
                    }
                }
            });
        });

        toggleOther();
    </script>
</body>
</html>
