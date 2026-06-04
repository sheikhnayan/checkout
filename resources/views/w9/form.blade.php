<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>W-9 Form - CartVIP</title>
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
            font-family: 'Inter', -apple-system, sans-serif;
            min-height: 100vh;
            padding: 20px;
        }
        .container { max-width: 900px; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 40px; }
        .header h1 { font-size: 28px; font-weight: 700; margin-bottom: 10px; }
        .header p { color: var(--muted); font-size: 16px; }
        .form-card {
            background: var(--dark-panel);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 40px;
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 18px;
            font-weight: 700;
            margin-top: 30px;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid rgba(124,58,237,0.3);
        }
        .form-group { margin-bottom: 20px; }
        .form-label {
            font-weight: 600;
            color: var(--text);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .required { color: #ef4444; }
        .form-control, .form-select {
            background: var(--dark-input);
            border: 1px solid var(--border);
            color: var(--text);
            padding: 10px 14px;
            border-radius: 8px;
        }
        .form-control:focus, .form-select:focus {
            background: var(--dark-input);
            border-color: rgba(124,58,237,0.5);
            color: var(--text);
            box-shadow: 0 0 0 0.2rem rgba(124, 58, 237, 0.25);
        }
        .form-control::placeholder { color: var(--muted); }
        .form-text { color: var(--muted); font-size: 13px; margin-top: 6px; }
        .row-two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        @media (max-width: 768px) { .row-two-col { grid-template-columns: 1fr; } }
        .upload-section {
            background: rgba(124,58,237,0.1);
            border: 2px dashed rgba(124,58,237,0.3);
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            margin-bottom: 15px;
        }
        .upload-section:hover {
            background: rgba(124,58,237,0.15);
            border-color: rgba(124,58,237,0.5);
        }
        .upload-section input[type="file"] { display: none; }
        .file-requirements {
            background: rgba(249,115,22,0.1);
            border-left: 4px solid #f97316;
            padding: 15px;
            border-radius: 6px;
            margin: 15px 0;
            font-size: 13px;
        }
        .file-requirements ul { margin: 10px 0 0 0; padding-left: 20px; }
        .file-requirements li { margin: 5px 0; }
        .preview-img {
            max-width: 200px;
            max-height: 200px;
            border-radius: 8px;
            margin-top: 10px;
            border: 1px solid var(--border);
        }
        .file-name { color: var(--muted); font-size: 13px; margin-top: 8px; }
        .checkbox-group {
            background: rgba(34,197,94,0.1);
            border: 1px solid rgba(34,197,94,0.3);
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .form-check-label {
            color: var(--text);
            font-size: 14px;
            margin-bottom: 0;
            cursor: pointer;
        }
        .btn-submit {
            background: #3b82f6;
            color: #fff;
            padding: 12px 32px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            margin-top: 30px;
        }
        .btn-submit:hover { background: #2563eb; }
        .btn-submit:disabled {
            background: #6b7280;
            cursor: not-allowed;
            opacity: 0.6;
        }
        .info-box {
            background: rgba(59,130,246,0.1);
            border-left: 4px solid #3b82f6;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
            font-size: 14px;
        }
        .error-text { color: #ef4444; font-size: 13px; margin-top: 6px; }
        .success-msg {
            background: rgba(34,197,94,0.1);
            border: 1px solid rgba(34,197,94,0.3);
            color: #86efac;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .loader {
            display: none;
            text-align: center;
            padding: 20px;
        }
        .spinner { display: inline-block; width: 20px; height: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-file-invoice"></i> W-9 Form</h1>
            <p>Request for Taxpayer Identification Number and Certification</p>
            <p style="color: var(--muted); font-size: 13px; margin-top: 10px;">Please complete this form to activate your {{ ucfirst($type) }} account</p>
        </div>

        <div class="form-card">
            <div id="successMsg" class="success-msg" style="display: none;">
                <i class="fas fa-check-circle"></i> Form submitted successfully! Your submission is under review.
            </div>

            <form id="w9Form" method="POST" action="{{ route('w9.store', $token) }}" enctype="multipart/form-data">
                @csrf

                <!-- Personal Information -->
                <div class="section-title">Personal Information</div>

                <div class="form-group">
                    <label class="form-label">
                        Full Name <span class="required">*</span>
                        <small style="font-size: 11px;color: var(--muted);">(As shown on your income tax return)</small>
                    </label>
                    <input type="text" name="full_name" class="form-control" placeholder="First and Last Name" required value="{{ $w9Form?->full_name ?? '' }}">
                    <div class="error-text" style="display: none;"></div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Business Name / DBA <span style="color: var(--muted);">(Optional)</span>
                    </label>
                    <input type="text" name="business_name" class="form-control" placeholder="If applicable, your business or DBA name" value="{{ $w9Form?->business_name ?? '' }}">
                </div>

                <!-- Address Information -->
                <div class="section-title">Mailing Address</div>

                <div class="form-group">
                    <label class="form-label">Street Address <span class="required">*</span></label>
                    <input type="text" name="street_address" class="form-control" placeholder="123 Main Street" required value="{{ $w9Form?->street_address ?? '' }}">
                </div>

                <div class="row-two-col">
                    <div class="form-group">
                        <label class="form-label">City <span class="required">*</span></label>
                        <input type="text" name="city" class="form-control" placeholder="City" required value="{{ $w9Form?->city ?? '' }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">State <span class="required">*</span></label>
                        <input type="text" name="state" class="form-control" placeholder="CA" maxlength="2" required value="{{ $w9Form?->state ?? '' }}" style="text-transform: uppercase;">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">ZIP Code <span class="required">*</span></label>
                    <input type="text" name="zip_code" class="form-control" placeholder="12345" maxlength="10" required value="{{ $w9Form?->zip_code ?? '' }}">
                </div>

                <!-- Tax Identification -->
                <div class="section-title">Tax Classification and Identification</div>

                <div class="form-group">
                    <label class="form-label">Tax Classification <span class="required">*</span></label>
                    <select name="tax_classification" class="form-select" required onchange="toggleOther()">
                        <option value="">-- Select Classification --</option>
                        <option value="individual" {{ $w9Form?->tax_classification === 'individual' ? 'selected' : '' }}>Individual / Sole Proprietor</option>
                        <option value="c_corporation" {{ $w9Form?->tax_classification === 'c_corporation' ? 'selected' : '' }}>C Corporation</option>
                        <option value="s_corporation" {{ $w9Form?->tax_classification === 's_corporation' ? 'selected' : '' }}>S Corporation</option>
                        <option value="partnership" {{ $w9Form?->tax_classification === 'partnership' ? 'selected' : '' }}>Partnership</option>
                        <option value="trust_estate" {{ $w9Form?->tax_classification === 'trust_estate' ? 'selected' : '' }}>Trust / Estate</option>
                        <option value="limited_liability_company_c" {{ $w9Form?->tax_classification === 'limited_liability_company_c' ? 'selected' : '' }}>Limited Liability Company (C)</option>
                        <option value="limited_liability_company_s" {{ $w9Form?->tax_classification === 'limited_liability_company_s' ? 'selected' : '' }}>Limited Liability Company (S)</option>
                        <option value="limited_liability_company_individual" {{ $w9Form?->tax_classification === 'limited_liability_company_individual' ? 'selected' : '' }}>Limited Liability Company (Individual)</option>
                        <option value="other" {{ $w9Form?->tax_classification === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div class="form-group" id="otherClassification" style="display: none;">
                    <label class="form-label">Please Specify <span class="required">*</span></label>
                    <input type="text" name="tax_classification_other" class="form-control" placeholder="Specify your tax classification" value="{{ $w9Form?->tax_classification_other ?? '' }}">
                </div>

                <div class="row-two-col">
                    <div class="form-group">
                        <label class="form-label">Tax ID Type <span class="required">*</span></label>
                        <select name="tax_id_type" class="form-select" required>
                            <option value="">-- Select Type --</option>
                            <option value="ssn" {{ $w9Form?->tax_id_type === 'ssn' ? 'selected' : '' }}>Social Security Number (SSN)</option>
                            <option value="ein" {{ $w9Form?->tax_id_type === 'ein' ? 'selected' : '' }}>Employer Identification Number (EIN)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tax ID Number <span class="required">*</span></label>
                        <input type="text" name="tax_id_number" class="form-control" placeholder="XXX-XX-XXXX or XX-XXXXXXX" required value="{{ $w9Form?->tax_id_number ?? '' }}" pattern="\d{3}-\d{2}-\d{4}|\d{2}-\d{7}">
                        <div class="form-text">Format: XXX-XX-XXXX (SSN) or XX-XXXXXXX (EIN)</div>
                    </div>
                </div>

                <!-- Optional Fields -->
                <div class="section-title">Additional Information <span style="font-size: 13px; color: var(--muted);">(Optional)</span></div>

                <div class="form-group">
                    <label class="form-label">Account Number(s) <span style="color: var(--muted);">(Optional)</span></label>
                    <input type="text" name="account_numbers" class="form-control" placeholder="Your account or reference numbers" value="{{ $w9Form?->account_numbers ?? '' }}">
                    <div class="form-text">For your records if this form is being requested by another party</div>
                </div>

                <div class="row-two-col">
                    <div class="form-group">
                        <label class="form-label">Requester Name <span style="color: var(--muted);">(Optional)</span></label>
                        <input type="text" name="requester_name" class="form-control" placeholder="Name of person requesting form" value="{{ $w9Form?->requester_name ?? '' }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Requester Phone <span style="color: var(--muted);">(Optional)</span></label>
                        <input type="tel" name="requester_phone" class="form-control" placeholder="(XXX) XXX-XXXX" value="{{ $w9Form?->requester_phone ?? '' }}">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Requester Email <span style="color: var(--muted);">(Optional)</span></label>
                    <input type="email" name="requester_email" class="form-control" placeholder="email@example.com" value="{{ $w9Form?->requester_email ?? '' }}">
                </div>

                <div class="row-two-col">
                    <div class="form-group">
                        <label class="form-label">Exempt Payee Code <span style="color: var(--muted);">(Optional)</span></label>
                        <input type="text" name="exempt_payee_code" class="form-control" placeholder="If applicable" value="{{ $w9Form?->exempt_payee_code ?? '' }}">
                        <div class="form-text">Only if you are exempt from backup withholding</div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">FATCA Exemption Code <span style="color: var(--muted);">(Optional)</span></label>
                        <input type="text" name="fatca_exemption_code" class="form-control" placeholder="If applicable" value="{{ $w9Form?->fatca_exemption_code ?? '' }}">
                        <div class="form-text">Exemption from FATCA reporting code</div>
                    </div>
                </div>

                <!-- ID Upload Section -->
                <div class="section-title">Government-Issued ID <span style="font-size: 13px; color: #ef4444;">* Required</span></div>

                <div class="info-box">
                    <strong>📋 ID Requirements:</strong> Please upload clear, legible photos of both the front and back of your government-issued ID for verification purposes.
                </div>

                <div class="form-group">
                    <label class="form-label">ID Document Type <span class="required">*</span></label>
                    <select name="id_document_type" class="form-select" required>
                        <option value="">-- Select ID Type --</option>
                        <option value="driver_license" {{ $w9Form?->id_document_type === 'driver_license' ? 'selected' : '' }}>Driver's License</option>
                        <option value="passport" {{ $w9Form?->id_document_type === 'passport' ? 'selected' : '' }}>Passport</option>
                        <option value="state_id" {{ $w9Form?->id_document_type === 'state_id' ? 'selected' : '' }}>State ID Card</option>
                        <option value="other" {{ $w9Form?->id_document_type === 'other' ? 'selected' : '' }}>Other Government-Issued ID</option>
                    </select>
                </div>

                <!-- ID Front -->
                <div class="form-group">
                    <label class="form-label">ID Front <span class="required">*</span></label>
                    <div class="upload-section" onclick="document.getElementById('idFront').click();">
                        <i class="fas fa-cloud-upload-alt" style="font-size: 32px; color: rgba(124,58,237,0.5); margin-bottom: 10px;"></i>
                        <div style="color: var(--text); font-weight: 600;">Click to upload or drag and drop</div>
                        <div style="color: var(--muted); font-size: 13px;">Front side of your ID</div>
                    </div>
                    <input type="file" id="idFront" name="id_front_image" accept="image/jpeg,image/png,image/jpg" required onchange="previewImage(this, 'idFrontPreview')">
                    <div id="idFrontPreview"></div>
                    <div class="file-requirements">
                        <strong>✓ Accepted Formats:</strong>
                        <ul>
                            <li>JPG / JPEG</li>
                            <li>PNG</li>
                        </ul>
                        <strong>✓ File Size:</strong> Maximum 5 MB per image<br>
                        <strong>✓ Quality:</strong> Clear, legible, with all information visible
                    </div>
                </div>

                <!-- ID Back -->
                <div class="form-group">
                    <label class="form-label">ID Back <span class="required">*</span></label>
                    <div class="upload-section" onclick="document.getElementById('idBack').click();">
                        <i class="fas fa-cloud-upload-alt" style="font-size: 32px; color: rgba(124,58,237,0.5); margin-bottom: 10px;"></i>
                        <div style="color: var(--text); font-weight: 600;">Click to upload or drag and drop</div>
                        <div style="color: var(--muted); font-size: 13px;">Back side of your ID</div>
                    </div>
                    <input type="file" id="idBack" name="id_back_image" accept="image/jpeg,image/png,image/jpg" required onchange="previewImage(this, 'idBackPreview')">
                    <div id="idBackPreview"></div>
                    <div class="file-requirements">
                        <strong>✓ Accepted Formats:</strong>
                        <ul>
                            <li>JPG / JPEG</li>
                            <li>PNG</li>
                        </ul>
                        <strong>✓ File Size:</strong> Maximum 5 MB per image<br>
                        <strong>✓ Quality:</strong> Clear, legible, with all information visible
                    </div>
                </div>

                <!-- Certification -->
                <div class="section-title">Certification</div>

                <div class="info-box">
                    <strong>⚠️ Important:</strong> I declare that I am a U.S. citizen or other U.S. person and that the information provided is true and correct. Providing false information is subject to penalties under U.S. law.
                </div>

                <div class="checkbox-group">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="certCheck" name="certification_signed" required>
                        <label class="form-check-label" for="certCheck">
                            <strong>I certify under penalty of perjury</strong> under the laws of the United States that the information I have provided on this form is true and correct. I understand that providing false information subjects me to criminal penalties including fines and/or imprisonment, and civil penalties including damages and treble damages, under sections 1001, 1341, and 1346 of Title 18 of the United States Code.
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn-submit" id="submitBtn">
                    <i class="fas fa-paper-plane"></i> Submit W-9 Form
                </button>

                <div class="loader" id="loader">
                    <div class="spinner"></div>
                    <p>Submitting your form...</p>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function toggleOther() {
            const classification = document.querySelector('select[name="tax_classification"]').value;
            const otherDiv = document.getElementById('otherClassification');
            if (classification === 'other') {
                otherDiv.style.display = 'block';
                otherDiv.querySelector('input').required = true;
            } else {
                otherDiv.style.display = 'none';
                otherDiv.querySelector('input').required = false;
            }
        }

        function previewImage(input, previewId) {
            const previewDiv = document.getElementById(previewId);
            previewDiv.innerHTML = '';

            if (input.files && input.files[0]) {
                const file = input.files[0];

                // Validate file size (5MB)
                if (file.size > 5242880) {
                    previewDiv.innerHTML = '<div class="error-text"><i class="fas fa-exclamation-circle"></i> File is larger than 5 MB</div>';
                    input.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    previewDiv.innerHTML = `<img src="${e.target.result}" class="preview-img" alt="Preview"><div class="file-name"><i class="fas fa-check-circle" style="color: #22c55e;"></i> ${file.name} (${(file.size / 1024).toFixed(1)} KB)</div>`;
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
                            $(`[name="${field}"]`).closest('.form-group').find('.error-text').text(errorMsg).show();
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
