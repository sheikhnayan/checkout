<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tax Information & Certification Form</title>
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
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }

        .page-wrapper {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 20px;
            max-width: 1100px;
            margin: 0 auto;
        }

        .form-document {
            background: white;
            padding: 40px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: 4px;
        }

        /* Header */
        .form-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #0066cc;
        }

        .form-header h1 {
            font-size: 28px;
            font-weight: bold;
            color: #0066cc;
            margin-bottom: 5px;
        }

        .form-header p {
            font-size: 14px;
            color: #666;
            margin: 5px 0;
        }

        .disclaimer-box {
            background: #e7f3ff;
            border-left: 4px solid #0066cc;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            font-size: 11px;
            line-height: 1.5;
        }

        .disclaimer-title {
            font-weight: bold;
            color: #0066cc;
            margin-bottom: 8px;
        }

        /* Form Sections */
        .form-section {
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #000;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #ddd;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-row.full {
            grid-template-columns: 1fr;
        }

        .form-row.three-col {
            grid-template-columns: 2fr 1fr 1.2fr;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-weight: 600;
            font-size: 12px;
            color: #333;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-group label .required {
            color: #dc3545;
            font-weight: bold;
            margin-left: 3px;
        }

        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group select {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 12px;
            font-family: Arial, sans-serif;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #0066cc;
            background-color: #f9fbff;
            box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.1);
        }

        .form-group input[type="text"]:disabled {
            background-color: #f5f5f5;
            color: #999;
        }

        .help-text {
            font-size: 10px;
            color: #666;
            margin-top: 4px;
            font-style: italic;
        }

        /* Checkboxes and Radio */
        .checkbox-group {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 10px;
        }

        .checkbox-item,
        .radio-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 12px;
        }

        .checkbox-item input[type="checkbox"],
        .radio-item input[type="radio"] {
            width: 16px;
            height: 16px;
            cursor: pointer;
            margin: 0;
            accent-color: #0066cc;
        }

        .checkbox-item label,
        .radio-item label {
            cursor: pointer;
            margin: 0;
            font-size: 12px;
        }

        .checkbox-with-input {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 8px;
        }

        .checkbox-with-input input[type="text"] {
            flex: 0 0 60px;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 12px;
            text-transform: uppercase;
            max-width: 60px;
        }

        /* TIN Entry */
        .tin-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-top: 15px;
        }

        .tin-option {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .tin-boxes {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .tin-box {
            width: 28px;
            height: 28px;
            border: 1px solid #ccc;
            border-radius: 4px;
            text-align: center;
            padding: 4px;
            font-weight: bold;
            font-size: 12px;
        }

        .tin-separator {
            font-weight: bold;
            font-size: 14px;
            color: #333;
        }

        /* Certification Section */
        .certification-section {
            background: #f9f9f9;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 4px;
            margin-top: 20px;
        }

        .certification-title {
            font-weight: bold;
            font-size: 13px;
            margin-bottom: 12px;
            color: #000;
        }

        .cert-item {
            margin-bottom: 10px;
            padding-left: 20px;
            font-size: 12px;
            line-height: 1.5;
        }

        .cert-item-number {
            font-weight: bold;
            color: #333;
        }

        .signature-area {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-top: 20px;
        }

        .signature-field {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .signature-label {
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
            color: #333;
        }

        .signature-line {
            border-bottom: 2px solid #333;
            min-height: 40px;
            display: flex;
            align-items: flex-end;
            padding-bottom: 4px;
        }

        .signature-input {
            padding: 8px;
            border: none;
            border-bottom: 2px solid #333;
            font-family: 'Lucida Handwriting', cursive;
            font-size: 14px;
            width: 100%;
        }

        .date-input {
            padding: 8px;
            border: none;
            border-bottom: 2px solid #333;
            width: 100%;
            font-size: 12px;
        }

        /* Sidebar */
        .sidebar {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .sidebar-card {
            background: white;
            border-radius: 4px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .sidebar-title {
            font-weight: bold;
            font-size: 14px;
            color: #0066cc;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #0066cc;
            padding: 12px;
            border-radius: 4px;
            font-size: 11px;
            color: #0066cc;
            margin-bottom: 15px;
            line-height: 1.5;
        }

        .error-box {
            background: #ffe7e7;
            border-left: 4px solid #dc3545;
            padding: 12px;
            border-radius: 4px;
            font-size: 12px;
            color: #dc3545;
            margin-bottom: 15px;
            display: none;
            line-height: 1.5;
        }

        .file-upload {
            margin-bottom: 18px;
        }

        .file-upload-label {
            font-weight: 600;
            font-size: 11px;
            color: #333;
            text-transform: uppercase;
            display: block;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }

        .file-upload-area {
            border: 2px dashed #0066cc;
            border-radius: 4px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            background: #f9fbff;
            font-size: 12px;
            transition: all 0.3s ease;
        }

        .file-upload-area:hover {
            background: #f0f5ff;
            border-color: #0052a3;
        }

        .file-upload input[type="file"] {
            display: none;
        }

        .file-preview {
            margin-top: 10px;
            font-size: 11px;
        }

        .file-preview img {
            max-width: 100%;
            max-height: 90px;
            border-radius: 4px;
            margin-top: 8px;
            border: 1px solid #ddd;
        }

        .file-info {
            font-size: 10px;
            color: #0066cc;
            margin-top: 6px;
        }

        .file-specs {
            font-size: 9px;
            color: #999;
            margin-top: 8px;
        }

        .btn {
            padding: 12px 20px;
            font-size: 13px;
            font-weight: bold;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-primary {
            background: #0066cc;
            color: white;
        }

        .btn-primary:hover:not(:disabled) {
            background: #0052a3;
        }

        .btn-primary:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .loading {
            display: none;
            text-align: center;
            margin-top: 12px;
        }

        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #0066cc;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
            margin: 0 auto 10px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .note-box {
            background: #fff9e6;
            border-left: 3px solid #ff9800;
            padding: 12px;
            margin: 15px 0;
            border-radius: 4px;
            font-size: 11px;
            line-height: 1.5;
        }

        .note-label {
            font-weight: bold;
            color: #ff9800;
            margin-bottom: 6px;
        }

        @media (max-width: 1024px) {
            .page-wrapper {
                grid-template-columns: 1fr;
            }

            .form-row {
                grid-template-columns: 1fr;
            }
        }

        @media print {
            .sidebar { display: none; }
            body { background: white; padding: 0; }
            .form-document { box-shadow: none; }
        }
    </style>
</head>
<body>

<div class="page-wrapper">
    <!-- Main Form -->
    <form id="taxCertForm" class="form-document">

        <!-- Header -->
        <div class="form-header">
            <h1>Tax Information & Certification Form</h1>
            <p>Contractor/Affiliate Registration</p>
            <p style="font-size: 10px; color: #999; margin-top: 10px;">CartVIP Contractor Management System</p>
        </div>

        <!-- Important Disclaimer -->
        <div class="disclaimer-box">
            <div class="disclaimer-title">⚠️ Important Information</div>
            <p><strong>This is a custom form created by CartVIP for contractor information collection.</strong> This is NOT the official IRS Form W-9. Depending on your engagement, you may be required to provide the official Form W-9 (Request for Taxpayer Identification Number and Certification) to CartVIP separately. This form collects essential tax and certification information for contractor management and payment processing purposes.</p>
        </div>

        <!-- Section 1: Personal/Business Information -->
        <div class="form-section">
            <div class="section-title">1. Legal Name & Business Information</div>

            <div class="form-row full">
                <div class="form-group">
                    <label>Legal Name <span class="required">*</span></label>
                    <input type="text" id="legalName" required placeholder="Enter full legal name as it appears on tax return">
                    <div class="help-text">If you have changed your legal name, enter the name that matches your current tax return.</div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Business/Trade Name (if different)</label>
                    <input type="text" id="businessName" placeholder="DBA, sole proprietorship, or entity name">
                    <div class="help-text">Leave blank if you operate as an individual.</div>
                </div>
                <div class="form-group">
                    <label>Business Type <span class="required">*</span></label>
                    <select id="businessType" required>
                        <option value="">-- Select Business Type --</option>
                        <option value="individual">Individual/Sole Proprietor</option>
                        <option value="sole_proprietorship">Sole Proprietorship</option>
                        <option value="partnership">Partnership (General or Limited)</option>
                        <option value="llc">Limited Liability Company (LLC)</option>
                        <option value="c_corporation">C Corporation</option>
                        <option value="s_corporation">S Corporation</option>
                        <option value="trust">Trust or Estate</option>
                        <option value="other">Other Entity Type</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Section 2: Address -->
        <div class="form-section">
            <div class="section-title">2. Mailing Address</div>

            <div class="form-row full">
                <div class="form-group">
                    <label>Street Address <span class="required">*</span></label>
                    <input type="text" id="address" required placeholder="Number, street, and apt/suite no.">
                </div>
            </div>

            <div class="form-row three-col">
                <div class="form-group">
                    <label>City <span class="required">*</span></label>
                    <input type="text" id="city" required>
                </div>
                <div class="form-group">
                    <label>State <span class="required">*</span></label>
                    <input type="text" id="state" maxlength="2" required style="text-transform: uppercase;" placeholder="CA">
                </div>
                <div class="form-group">
                    <label>ZIP Code <span class="required">*</span></label>
                    <input type="text" id="zip" required placeholder="00000">
                </div>
            </div>
        </div>

        <!-- Section 3: Tax Classification -->
        <div class="form-section">
            <div class="section-title">3. Federal Tax Classification</div>

            <div class="form-row full">
                <div class="form-group">
                    <label>Select Your Tax Classification <span class="required">*</span></label>
                    <div class="checkbox-group">
                        <div class="radio-item">
                            <input type="radio" id="tax_individual" name="taxClassification" value="individual" required>
                            <label for="tax_individual">Individual/Sole Proprietor</label>
                        </div>
                        <div class="radio-item">
                            <input type="radio" id="tax_ccorp" name="taxClassification" value="c_corporation">
                            <label for="tax_ccorp">C Corporation</label>
                        </div>
                        <div class="radio-item">
                            <input type="radio" id="tax_scorp" name="taxClassification" value="s_corporation">
                            <label for="tax_scorp">S Corporation</label>
                        </div>
                        <div class="radio-item">
                            <input type="radio" id="tax_partnership" name="taxClassification" value="partnership">
                            <label for="tax_partnership">Partnership</label>
                        </div>
                        <div class="radio-item">
                            <input type="radio" id="tax_trust" name="taxClassification" value="trust_estate">
                            <label for="tax_trust">Trust or Estate</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-row full">
                <div class="form-group">
                    <div class="checkbox-with-input">
                        <input type="checkbox" id="tax_llc" name="taxClassification">
                        <label for="tax_llc"><strong>LLC</strong> - Enter classification code:</label>
                        <input type="text" id="llcCode" maxlength="1" placeholder="C, S, or P">
                    </div>
                    <div class="help-text">C = C Corporation, S = S Corporation, P = Partnership</div>
                </div>
            </div>
        </div>

        <!-- Section 4: Taxpayer Identification Number -->
        <div class="form-section">
            <div class="section-title">4. Taxpayer Identification Number (TIN) <span class="required">*</span></div>

            <div class="note-box">
                <div class="note-label">Note:</div>
                The TIN you provide must match the name on Line 1 above. The IRS will use this information to verify your tax records.
            </div>

            <div class="tin-section">
                <!-- SSN -->
                <div class="tin-option">
                    <div class="radio-item">
                        <input type="radio" id="tin_ssn" name="tinType" value="ssn" checked>
                        <label for="tin_ssn"><strong>Social Security Number (SSN)</strong></label>
                    </div>
                    <div class="tin-boxes">
                        <input type="text" id="ssn1" class="tin-box" maxlength="3" inputmode="numeric" placeholder="000">
                        <div class="tin-separator">–</div>
                        <input type="text" id="ssn2" class="tin-box" maxlength="2" inputmode="numeric" placeholder="00">
                        <div class="tin-separator">–</div>
                        <input type="text" id="ssn3" class="tin-box" maxlength="4" inputmode="numeric" placeholder="0000">
                    </div>
                </div>

                <!-- EIN -->
                <div class="tin-option">
                    <div class="radio-item">
                        <input type="radio" id="tin_ein" name="tinType" value="ein">
                        <label for="tin_ein"><strong>Employer Identification Number (EIN)</strong></label>
                    </div>
                    <div class="tin-boxes">
                        <input type="text" id="ein1" class="tin-box" maxlength="2" inputmode="numeric" placeholder="00">
                        <div class="tin-separator">–</div>
                        <input type="text" id="ein2" class="tin-box" maxlength="7" inputmode="numeric" placeholder="0000000">
                    </div>
                </div>
            </div>

            <div class="help-text" style="margin-top: 15px;">
                If you are an individual, enter your Social Security Number. If you are a business entity, enter your Employer Identification Number (EIN).
            </div>
        </div>

        <!-- Section 5: Certification -->
        <div class="form-section">
            <div class="certification-section">
                <div class="certification-title">5. Certification & Agreement</div>

                <div class="cert-item">
                    <span class="cert-item-number">I certify that:</span>
                </div>

                <div class="cert-item">
                    <strong>1.</strong> The taxpayer identification number provided above is correct and matches my legal name;
                </div>

                <div class="cert-item">
                    <strong>2.</strong> I am authorized to represent this business entity and provide this information on its behalf;
                </div>

                <div class="cert-item">
                    <strong>3.</strong> I am a U.S. citizen, U.S. permanent resident, or other U.S. person;
                </div>

                <div class="cert-item">
                    <strong>4.</strong> I understand that CartVIP will use this information for payment processing, tax reporting, and contractor management;
                </div>

                <div class="cert-item">
                    <strong>5.</strong> I acknowledge that I may be required to provide the official IRS Form W-9 as a separate document.
                </div>

                <div class="signature-area">
                    <div class="signature-field">
                        <div class="signature-label">Authorized Signature</div>
                        <div class="signature-line"></div>
                        <div style="font-size: 9px; color: #666; margin-top: 4px;">Sign above (or print name below)</div>
                    </div>
                    <div class="signature-field">
                        <div class="signature-label">Date</div>
                        <input type="text" id="signatureDate" class="date-input" placeholder="MM/DD/YYYY">
                    </div>
                </div>

                <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd;">
                    <div class="checkbox-item">
                        <input type="checkbox" id="certifyCheckbox" required>
                        <label for="certifyCheckbox">I certify under penalties of perjury that the information provided above is true, correct, and complete to the best of my knowledge.</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 6: Additional Information -->
        <div class="form-section">
            <div class="section-title">6. Account Reference (Optional)</div>

            <div class="form-row full">
                <div class="form-group">
                    <label>Account/Reference Number</label>
                    <input type="text" id="accountNumber" placeholder="Enter if you have a CartVIP or other account number">
                    <div class="help-text">This helps us match the form with your existing accounts.</div>
                </div>
            </div>
        </div>

    </form>

    <!-- Sidebar: ID Verification & Submit -->
    <div class="sidebar">

        <!-- ID Verification Card -->
        <div class="sidebar-card">
            <div class="error-box" id="errorBox"></div>

            <div class="sidebar-title">
                <span>📸</span> ID Verification
            </div>

            <div class="info-box">
                Upload clear photos of BOTH sides of your government-issued ID (driver's license, passport, or state ID).
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label>ID Type <span class="required">*</span></label>
                <select id="idType" required>
                    <option value="">-- Select ID Type --</option>
                    <option value="driver_license">Driver's License</option>
                    <option value="passport">Passport</option>
                    <option value="state_id">State ID Card</option>
                    <option value="other">Other Government ID</option>
                </select>
            </div>

            <!-- Front of ID -->
            <div class="file-upload">
                <div class="file-upload-label">Front of ID <span class="required">*</span></div>
                <div class="file-upload-area" onclick="document.getElementById('idFront').click();" title="Click to select file or drag and drop">
                    <div>📤 Click to upload or drag & drop</div>
                </div>
                <input type="file" id="idFront" accept="image/jpeg,image/png,image/jpg" required>
                <div id="idFrontPreview" class="file-preview"></div>
                <div class="file-specs">✓ JPG or PNG | ✓ Max 5 MB | ✓ Must be clear and readable</div>
            </div>

            <!-- Back of ID -->
            <div class="file-upload">
                <div class="file-upload-label">Back of ID <span class="required">*</span></div>
                <div class="file-upload-area" onclick="document.getElementById('idBack').click();" title="Click to select file or drag and drop">
                    <div>📤 Click to upload or drag & drop</div>
                </div>
                <input type="file" id="idBack" accept="image/jpeg,image/png,image/jpg" required>
                <div id="idBackPreview" class="file-preview"></div>
                <div class="file-specs">✓ JPG or PNG | ✓ Max 5 MB | ✓ Must be clear and readable</div>
            </div>

            <button type="button" id="submitBtn" onclick="submitForm()" class="btn btn-primary" style="margin-top: 25px;">
                ✓ Submit Form & ID
            </button>

            <div class="loading" id="loader">
                <div class="spinner"></div>
                <p style="font-size: 11px; color: #666; margin-top: 8px;">Processing your submission...</p>
            </div>
        </div>

        <!-- Information Card -->
        <div class="sidebar-card">
            <div class="sidebar-title">
                <span>ℹ️</span> What Happens Next
            </div>

            <div style="font-size: 11px; line-height: 1.6; color: #666;">
                <p style="margin-bottom: 12px;">
                    <strong>After you submit:</strong>
                </p>
                <ul style="margin-left: 16px; margin-bottom: 12px;">
                    <li style="margin-bottom: 8px;">✓ Your information will be securely stored</li>
                    <li style="margin-bottom: 8px;">✓ ID verification will be reviewed by our team</li>
                    <li style="margin-bottom: 8px;">✓ Your status will be updated in your account</li>
                    <li>✓ You may receive a follow-up if we need the official IRS Form W-9</li>
                </ul>
                <p style="margin-bottom: 12px;">
                    <strong>Data Security:</strong> Your tax ID and personal information are encrypted and stored securely. Access is restricted to authorized CartVIP staff only.
                </p>
                <p style="font-size: 10px; color: #999;">
                    For questions, contact: support@cartvip.com
                </p>
            </div>
        </div>

    </div>
</div>

<script>
// Auto-format numeric inputs
function autoFormatNumeric(e, maxLen) {
    e.target.value = e.target.value.replace(/[^0-9]/g, '').slice(0, maxLen);
}

document.getElementById('ssn1').addEventListener('input', (e) => autoFormatNumeric(e, 3));
document.getElementById('ssn2').addEventListener('input', (e) => autoFormatNumeric(e, 2));
document.getElementById('ssn3').addEventListener('input', (e) => autoFormatNumeric(e, 4));
document.getElementById('ein1').addEventListener('input', (e) => autoFormatNumeric(e, 2));
document.getElementById('ein2').addEventListener('input', (e) => autoFormatNumeric(e, 7));
document.getElementById('state').addEventListener('input', (e) => e.target.value = e.target.value.toUpperCase());

// File preview handlers
document.getElementById('idFront').addEventListener('change', function(e) {
    previewFile(e.target, 'idFrontPreview');
});

document.getElementById('idBack').addEventListener('change', function(e) {
    previewFile(e.target, 'idBackPreview');
});

// Drag and drop
['idFront', 'idBack'].forEach(id => {
    const input = document.getElementById(id);
    const uploadArea = input.previousElementSibling;

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    uploadArea.addEventListener('drop', function(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        input.files = files;
        const event = new Event('change', { bubbles: true });
        input.dispatchEvent(event);
    });
});

function previewFile(input, previewId) {
    const file = input.files[0];
    const preview = document.getElementById(previewId);
    preview.innerHTML = '';

    if (file) {
        if (file.size > 5242880) {
            preview.innerHTML = '<div style="color: #dc3545; font-size: 11px;">❌ File exceeds 5 MB limit</div>';
            input.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            preview.appendChild(img);

            const name = document.createElement('div');
            name.textContent = '✓ ' + file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
            name.className = 'file-info';
            preview.appendChild(name);
        };
        reader.readAsDataURL(file);
    }
}

async function submitForm() {
    const errorBox = document.getElementById('errorBox');
    errorBox.style.display = 'none';
    errorBox.innerHTML = '';

    // Validate form
    const errors = [];

    const legalName = document.getElementById('legalName').value.trim();
    const address = document.getElementById('address').value.trim();
    const city = document.getElementById('city').value.trim();
    const state = document.getElementById('state').value.trim();
    const zip = document.getElementById('zip').value.trim();
    const businessType = document.getElementById('businessType').value;
    const idType = document.getElementById('idType').value;
    const idFront = document.getElementById('idFront').files[0];
    const idBack = document.getElementById('idBack').files[0];
    const certifyCheckbox = document.getElementById('certifyCheckbox').checked;

    // TIN validation
    let tinValue = '';
    const tinType = document.querySelector('input[name="tinType"]:checked').value;

    if (tinType === 'ssn') {
        const ssn1 = document.getElementById('ssn1').value.trim();
        const ssn2 = document.getElementById('ssn2').value.trim();
        const ssn3 = document.getElementById('ssn3').value.trim();

        if (ssn1 && ssn2 && ssn3) {
            tinValue = ssn1 + '-' + ssn2 + '-' + ssn3;
        }
    } else {
        const ein1 = document.getElementById('ein1').value.trim();
        const ein2 = document.getElementById('ein2').value.trim();

        if (ein1 && ein2) {
            tinValue = ein1 + '-' + ein2;
        }
    }

    // Check all required fields
    if (!legalName) errors.push('✗ Legal Name is required');
    if (!address) errors.push('✗ Street Address is required');
    if (!city) errors.push('✗ City is required');
    if (!state) errors.push('✗ State is required');
    if (!zip) errors.push('✗ ZIP Code is required');
    if (!businessType) errors.push('✗ Business Type is required');
    if (!tinValue) errors.push('✗ Taxpayer Identification Number (TIN) is required');
    if (!idType) errors.push('✗ ID Type is required');
    if (!idFront) errors.push('✗ Front of ID is required');
    if (!idBack) errors.push('✗ Back of ID is required');
    if (!certifyCheckbox) errors.push('✗ You must certify the information is accurate');

    if (errors.length > 0) {
        errorBox.innerHTML = '<strong>Please correct these errors:</strong><br>' + errors.join('<br>');
        errorBox.style.display = 'block';
        document.querySelector('.form-document').scrollIntoView({ behavior: 'smooth', block: 'start' });
        return;
    }

    // Show loading
    document.getElementById('submitBtn').disabled = true;
    document.getElementById('loader').style.display = 'block';

    // Prepare form data
    const formData = new FormData();
    formData.append('id_type', idType);
    formData.append('id_front_image', idFront);
    formData.append('id_back_image', idBack);

    // Collect all tax info
    const taxData = {
        'legal_name': legalName,
        'business_name': document.getElementById('businessName').value.trim(),
        'business_type': businessType,
        'address': address,
        'city': city,
        'state': state,
        'zip': zip,
        'tax_classification': document.querySelector('input[name="taxClassification"]:checked')?.value || '',
        'llc_code': document.getElementById('llcCode').value.trim(),
        'tin_type': tinType,
        'tin_number': tinValue,
        'account_number': document.getElementById('accountNumber').value.trim(),
        'signature_date': document.getElementById('signatureDate').value,
        'certified': true,
    };

    formData.append('tax_data', JSON.stringify(taxData));

    try {
        const response = await fetch('{{ route("w9.store", $token) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData
        });

        const data = await response.json();

        if (response.ok) {
            window.location.href = data.redirect_url || document.referrer;
        } else {
            const errorMsg = data.message || 'An error occurred while submitting the form.';
            errorBox.innerHTML = '<strong>Error:</strong><br>' + errorMsg;
            errorBox.style.display = 'block';
            document.querySelector('.form-document').scrollIntoView({ behavior: 'smooth' });
        }
    } catch (error) {
        errorBox.innerHTML = '<strong>Error:</strong><br>Failed to submit form. Please try again.';
        errorBox.style.display = 'block';
        console.error('Submit error:', error);
    } finally {
        document.getElementById('submitBtn').disabled = false;
        document.getElementById('loader').style.display = 'none';
    }
}
</script>

</body>
</html>