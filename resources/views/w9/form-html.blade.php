<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Substitute Form W-9 - Taxpayer Identification & Certification</title>
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
            font-size: 11px;
            line-height: 1.35;
            color: #000;
        }

        .page-wrapper {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .form-document {
            background: white;
            padding: 40px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        /* Header */
        .form-header {
            display: grid;
            grid-template-columns: 80px 1fr 120px;
            gap: 20px;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            align-items: start;
            font-size: 10px;
            line-height: 1.2;
        }

        .header-left {
            text-align: left;
        }

        .header-left-form {
            font-weight: bold;
            font-size: 10px;
        }

        .header-left-number {
            font-size: 16px;
            font-weight: bold;
            margin: 2px 0;
        }

        .header-left-sub {
            font-size: 8px;
            line-height: 1.3;
        }

        .header-center {
            text-align: center;
        }

        .header-center h1 {
            font-size: 16px;
            font-weight: bold;
            line-height: 1.2;
            margin-bottom: 5px;
        }

        .header-center-sub {
            font-size: 9px;
            color: #333;
            margin: 3px 0;
        }

        .header-right {
            text-align: center;
            font-weight: bold;
            font-size: 9px;
            line-height: 1.3;
        }

        /* Form Sections */
        .before-begin {
            border: 1px solid #999;
            padding: 8px;
            margin-bottom: 12px;
            background: #f9f9f9;
            font-size: 10px;
            line-height: 1.4;
        }

        .before-begin-text {
            margin-bottom: 4px;
        }

        .form-line {
            display: grid;
            grid-template-columns: 20px 1fr;
            gap: 10px;
            margin-bottom: 12px;
            align-items: start;
        }

        .line-number {
            font-weight: bold;
            font-size: 11px;
            padding-top: 1px;
        }

        .line-content {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .line-input {
            border: none;
            border-bottom: 1px solid #000;
            padding: 2px 3px;
            font-family: Arial, sans-serif;
            font-size: 11px;
            background: white;
            min-height: 18px;
        }

        .line-input:focus {
            outline: none;
            background: #fffacd;
        }

        .line-label {
            font-size: 9px;
            color: #333;
            margin-top: 1px;
        }

        /* Checkboxes */
        .checkbox-group {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin: 6px 0;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 10px;
        }

        .checkbox-item input[type="checkbox"],
        .checkbox-item input[type="radio"] {
            width: 12px;
            height: 12px;
            cursor: pointer;
            margin: 0;
        }

        .checkbox-item label {
            cursor: pointer;
            margin: 0;
            font-size: 10px;
        }

        /* Part Headers */
        .part-header {
            font-weight: bold;
            font-size: 11px;
            margin: 15px 0 8px 0;
            padding: 6px 0;
            border-top: 2px solid #000;
            border-bottom: 1px solid #000;
        }

        .part-number {
            display: inline-block;
            min-width: 50px;
        }

        /* Instructions Box */
        .instructions-box {
            background: #f9f9f9;
            border: 1px solid #999;
            padding: 8px;
            margin: 8px 0;
            font-size: 9px;
            line-height: 1.4;
        }

        /* TIN Section */
        .tin-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin: 10px 0;
        }

        .tin-option {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .tin-boxes {
            display: flex;
            gap: 5px;
            align-items: center;
        }

        .tin-box {
            border: 1px solid #000;
            width: 22px;
            height: 17px;
            text-align: center;
            padding: 1px;
            font-weight: bold;
            font-size: 10px;
        }

        .tin-separator {
            font-weight: bold;
            font-size: 11px;
        }

        /* Certification Section */
        .certification-box {
            border: 1px solid #999;
            padding: 10px;
            margin: 12px 0;
            background: #f9f9f9;
            font-size: 9px;
            line-height: 1.4;
        }

        .cert-item {
            margin-bottom: 5px;
            padding-left: 12px;
        }

        .signature-section {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #999;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .sig-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .sig-label {
            font-weight: bold;
            font-size: 9px;
        }

        .sig-area {
            border-bottom: 1px solid #000;
            min-height: 20px;
        }

        .sig-input {
            border: none;
            border-bottom: 1px solid #000;
            padding: 2px;
            font-size: 10px;
            width: 100%;
        }

        /* General Instructions Pages */
        .instructions-page {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px dashed #ccc;
        }

        .page-title {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 12px;
            text-align: center;
        }

        .section-heading {
            font-weight: bold;
            font-size: 11px;
            margin-top: 12px;
            margin-bottom: 6px;
        }

        .instruction-text {
            font-size: 9px;
            line-height: 1.4;
            margin-bottom: 6px;
            text-align: justify;
        }

        .bullet-list {
            margin-left: 15px;
            margin-bottom: 8px;
        }

        .bullet-list li {
            font-size: 9px;
            margin-bottom: 3px;
            line-height: 1.4;
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
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .sidebar-title {
            font-weight: bold;
            font-size: 13px;
            color: #0066cc;
            margin-bottom: 15px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            font-size: 11px;
            color: #333;
            margin-bottom: 6px;
            text-transform: uppercase;
        }

        .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 11px;
        }

        .required {
            color: #dc3545;
        }

        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #0066cc;
            padding: 10px;
            border-radius: 4px;
            font-size: 10px;
            color: #0066cc;
            margin-bottom: 12px;
            line-height: 1.4;
        }

        .error-box {
            background: #ffe7e7;
            border-left: 4px solid #dc3545;
            padding: 10px;
            border-radius: 4px;
            font-size: 11px;
            color: #dc3545;
            margin-bottom: 12px;
            display: none;
            line-height: 1.4;
        }

        .file-upload {
            margin-bottom: 15px;
        }

        .file-upload-label {
            font-weight: 600;
            font-size: 11px;
            color: #333;
            text-transform: uppercase;
            display: block;
            margin-bottom: 8px;
        }

        .file-upload-area {
            border: 2px dashed #0066cc;
            border-radius: 4px;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            background: #f9f9ff;
            font-size: 11px;
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
            margin-top: 8px;
            font-size: 10px;
        }

        .file-preview img {
            max-width: 100%;
            max-height: 80px;
            border-radius: 4px;
            margin-top: 5px;
        }

        .btn {
            padding: 11px 16px;
            font-size: 12px;
            font-weight: bold;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s ease;
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
            margin-top: 10px;
        }

        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #0066cc;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            animation: spin 1s linear infinite;
            margin: 0 auto 8px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 1024px) {
            .page-wrapper {
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
    <!-- Main W-9 Form -->
    <form id="w9Form" class="form-document">

        <!-- ======================== PAGE 1: FORM ======================== -->

        <!-- Header -->
        <div style="text-align: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #0066cc;">
            <h1 style="font-size: 18px; font-weight: bold; color: #0066cc; margin-bottom: 5px;">Substitute Form W-9</h1>
            <p style="font-size: 13px; font-weight: bold; color: #333; margin-bottom: 3px;">Taxpayer Identification & Certification</p>
            <p style="font-size: 10px; color: #666; margin: 8px 0;">CartVIP Contractor/Vendor Onboarding</p>
        </div>

        <!-- Disclaimer -->
        <div style="background: #e7f3ff; border-left: 4px solid #0066cc; padding: 12px; margin-bottom: 18px; border-radius: 4px; font-size: 10px; line-height: 1.5;">
            <strong>Disclaimer:</strong> This substitute Form W-9 is used by CartVIP to collect taxpayer identification and certification information for contractor/vendor onboarding. This is not the official IRS Form W-9. For official IRS instructions and the current Form W-9 (Rev. March 2024), visit <strong><a href="https://www.irs.gov/FormW9" target="_blank" style="color: #0066cc; text-decoration: underline;">irs.gov/FormW9</a></strong>.
        </div>

        <!-- Line 1 Notes -->
        <div class="before-begin">
            <div style="font-size: 9px; line-height: 1.4;">
                <strong>Line 1 — Legal Name:</strong> An entry is required. For a sole proprietor or disregarded entity, enter the owner's name on line 1 and the business/disregarded entity's name on line 2.
            </div>
        </div>

        <!-- Line 1: Name -->
        <div class="form-line">
            <div class="line-number">1</div>
            <div class="line-content">
                <input type="text" id="line1" class="line-input" required>
                <div class="line-label">Name of entity/individual</div>
            </div>
        </div>

        <!-- Line 2: Business Name -->
        <div class="form-line">
            <div class="line-number">2</div>
            <div class="line-content">
                <input type="text" id="line2" class="line-input">
                <div class="line-label">Business name/disregarded entity name, if different from above.</div>
            </div>
        </div>

        <!-- Line 3a: Tax Classification -->
        <div class="form-line">
            <div class="line-number">3a</div>
            <div class="line-content">
                <div style="font-size: 9px; margin-bottom: 6px; line-height: 1.4;">Check the appropriate box for federal tax classification of the entity/individual whose name is entered on line 1. Check only one of the following seven boxes.</div>

                <div class="checkbox-group">
                    <div class="checkbox-item">
                        <input type="checkbox" id="tax_individual" name="tax_3a" value="individual">
                        <label for="tax_individual">Individual/sole proprietor</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="tax_ccorp" name="tax_3a" value="c_corporation">
                        <label for="tax_ccorp">C corporation</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="tax_scorp" name="tax_3a" value="s_corporation">
                        <label for="tax_scorp">S corporation</label>
                    </div>
                </div>

                <div class="checkbox-group">
                    <div class="checkbox-item">
                        <input type="checkbox" id="tax_partnership" name="tax_3a" value="partnership">
                        <label for="tax_partnership">Partnership</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="tax_trust" name="tax_3a" value="trust_estate">
                        <label for="tax_trust">Trust/estate</label>
                    </div>
                </div>

                <div style="margin-top: 6px;">
                    <div class="checkbox-item">
                        <input type="checkbox" id="tax_llc" name="tax_3a" value="llc">
                        <label for="tax_llc"><strong>LLC.</strong> Enter the tax classification (C = C corporation, S = S corporation, P = Partnership)</label>
                    </div>
                    <input type="text" id="llc_code" style="border: none; border-bottom: 1px solid #000; width: 35px; padding: 1px; font-size: 10px; margin-left: 17px;" maxlength="1">
                </div>

                <div style="margin-left: 17px; margin-top: 4px; font-size: 8px; line-height: 1.3; color: #333;">
                    <strong>Note:</strong> Check the "LLC" box above and enter the appropriate code for the tax classification of the LLC, unless it is a disregarded entity. A disregarded entity should instead check the appropriate box for the tax classification of its owner.
                </div>

                <div class="checkbox-item" style="margin-left: 17px; margin-top: 4px;">
                    <input type="checkbox" id="tax_other" name="tax_3a" value="other">
                    <label for="tax_other">Other</label>
                </div>
            </div>
        </div>

        <!-- Line 3b: Foreign Partners -->
        <div class="form-line">
            <div class="line-number">3b</div>
            <div class="line-content">
                <div style="font-size: 9px; line-height: 1.4; margin-bottom: 6px;">If applicable, check this box if you have foreign partners, owners, or beneficiaries.</div>
                <div class="checkbox-item">
                    <input type="checkbox" id="line3b">
                    <label for="line3b"></label>
                </div>
            </div>
        </div>

        <!-- Line 4: Exemptions -->
        <div class="form-line">
            <div class="line-number">4</div>
            <div class="line-content">
                <div style="font-size: 9px; margin-bottom: 6px;"><strong>Exemptions:</strong> Exemptions apply only to certain entities. See IRS Form W-9 instructions for details.</div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div>
                        <input type="text" id="line4exempt" class="line-input" maxlength="2">
                        <div class="line-label">Exempt payee code (if any)</div>
                    </div>
                    <div>
                        <input type="text" id="line4fatca" class="line-input" maxlength="2">
                        <div class="line-label">Exemption from Foreign Account Tax Compliance Act (FATCA) reporting code (if any)</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Line 5: Address -->
        <div class="form-line">
            <div class="line-number">5</div>
            <div class="line-content">
                <input type="text" id="line5" class="line-input">
                <div class="line-label">Address (number, street, apartment, or suite)</div>
            </div>
        </div>

        <!-- Line 6: City, State, ZIP -->
        <div class="form-line">
            <div class="line-number">6</div>
            <div class="line-content">
                <input type="text" id="line6" class="line-input">
                <div class="line-label">City, state, and ZIP code</div>
            </div>
        </div>

        <!-- PART I: Taxpayer Identification Number (TIN) -->
        <div class="part-header">
            <span class="part-number"><strong>Part I</strong></span> <strong>Taxpayer Identification Number (TIN)</strong>
        </div>

        <div class="instructions-box">
            <strong>Part I — Taxpayer Identification Number (TIN):</strong> Enter your TIN in the appropriate box. The TIN provided must match the name given on line 1. For individuals, this is generally your social security number (SSN). For other entities, it is your employer identification number (EIN). If you are unsure whether to provide an SSN, EIN, or ITIN, consult the IRS Form W-9 instructions.
        </div>

        <div class="tin-section">
            <!-- SSN -->
            <div class="tin-option">
                <div class="checkbox-item">
                    <input type="radio" id="tin_ssn" name="tinType" value="ssn" checked>
                    <label for="tin_ssn">Social security number</label>
                </div>
                <div style="display: flex; gap: 8px; align-items: center; margin-top: 6px;">
                    <input type="text" id="ssn1" placeholder="000" maxlength="3" inputmode="numeric" style="width: 60px; padding: 8px; border: 1px solid #ccc; border-radius: 3px; font-size: 12px; text-align: center;">
                    <div style="font-weight: bold; font-size: 14px;">–</div>
                    <input type="text" id="ssn2" placeholder="00" maxlength="2" inputmode="numeric" style="width: 50px; padding: 8px; border: 1px solid #ccc; border-radius: 3px; font-size: 12px; text-align: center;">
                    <div style="font-weight: bold; font-size: 14px;">–</div>
                    <input type="text" id="ssn3" placeholder="0000" maxlength="4" inputmode="numeric" style="width: 70px; padding: 8px; border: 1px solid #ccc; border-radius: 3px; font-size: 12px; text-align: center;">
                </div>
            </div>

            <!-- EIN -->
            <div class="tin-option">
                <div class="checkbox-item">
                    <input type="radio" id="tin_ein" name="tinType" value="ein">
                    <label for="tin_ein">Employer identification number</label>
                </div>
                <div style="display: flex; gap: 8px; align-items: center; margin-top: 6px;">
                    <input type="text" id="ein1" placeholder="00" maxlength="2" inputmode="numeric" style="width: 60px; padding: 8px; border: 1px solid #ccc; border-radius: 3px; font-size: 12px; text-align: center;">
                    <div style="font-weight: bold; font-size: 14px;">–</div>
                    <input type="text" id="ein2" placeholder="0000000" maxlength="7" inputmode="numeric" style="width: 100px; padding: 8px; border: 1px solid #ccc; border-radius: 3px; font-size: 12px; text-align: center;">
                </div>
            </div>
        </div>

        <!-- PART II: Certification -->
        <div class="part-header">
            <span class="part-number"><strong>Part II</strong></span> <strong>Certification</strong>
        </div>

        <div class="certification-box">
            <div style="margin-bottom: 10px; font-size: 10px; line-height: 1.5;">
                <strong>Under penalties of perjury, I certify that:</strong>
            </div>

            <div class="cert-item" style="margin-bottom: 10px; font-size: 10px; line-height: 1.5;">
                <strong>1.</strong> The number shown on this form is my correct taxpayer identification number (or I am waiting for a number to be issued to me); and
            </div>

            <div class="cert-item" style="margin-bottom: 10px; font-size: 10px; line-height: 1.5;">
                <strong>2.</strong> I am not subject to backup withholding because (a) I am exempt from backup withholding, or (b) I have not been notified by the Internal Revenue Service (IRS) that I am subject to backup withholding as a result of a failure to report all interest or dividends, or (c) the IRS has notified me that I am no longer subject to backup withholding; and
            </div>

            <div class="cert-item" style="margin-bottom: 10px; font-size: 10px; line-height: 1.5;">
                <strong>3.</strong> I am a U.S. citizen or other U.S. person; and
            </div>

            <div class="cert-item" style="margin-bottom: 12px; font-size: 10px; line-height: 1.5;">
                <strong>4.</strong> The FATCA code(s) entered on this form (if any) indicating that I am exempt from FATCA reporting is correct.
            </div>

            <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #999; font-size: 10px; line-height: 1.5;">
                <strong>Electronic Signature Certification:</strong> By typing my legal name or drawing my signature below, I electronically sign this Substitute Form W-9. I understand that my electronic signature has the same legal effect as a handwritten signature.
            </div>

            <div style="margin-top: 12px; padding: 12px; background: #f0fdf4; border-left: 3px solid #22c55e; border-radius: 4px; font-size: 10px; line-height: 1.4;">
                <div class="checkbox-item">
                    <input type="checkbox" id="certificationAck" required>
                    <label for="certificationAck">☐ I acknowledge and agree to the certifications contained in Part II above.</label>
                </div>
                <small style="display: block; margin-top: 6px; color: #666;">If you cannot certify U.S. person status, you may need to complete Form W-8 instead.</small>
            </div>

            <!-- Signature Section -->
            <div style="margin-top: 15px; padding-top: 12px; border-top: 1px solid #999;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 15px;">
                    <div>
                        <div class="sig-label">Signature Method <span class="required">*</span></div>
                        <div style="margin-top: 8px;">
                            <div class="checkbox-item">
                                <input type="radio" id="sigMethod_typed" name="signatureMethod" value="typed" checked>
                                <label for="sigMethod_typed">Type Legal Name</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="radio" id="sigMethod_draw" name="signatureMethod" value="draw">
                                <label for="sigMethod_draw">Draw Signature</label>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="sig-label">Date <span class="required">*</span></div>
                        <input type="text" id="certDate" class="sig-input" placeholder="MM/DD/YYYY">
                    </div>
                </div>

                <!-- Typed Signature -->
                <div id="typedSigSection" style="margin-bottom: 15px;">
                    <div class="sig-label">Sign by Typing Your Full Legal Name <span class="required">*</span></div>
                    <input type="text" id="typedSignature" class="sig-input" placeholder="Enter your full legal name exactly as shown on line 1" style="margin-top: 6px;">
                    <div style="font-size: 8px; color: #666; margin-top: 4px;">Your typed name serves as your digital signature and is legally binding when you certify below.</div>
                </div>

                <!-- Canvas Signature -->
                <div id="canvasSigSection" style="display: none; margin-bottom: 15px;">
                    <div class="sig-label">Draw Your Signature <span class="required">*</span></div>
                    <canvas id="signatureCanvas" style="border: 2px solid #ccc; border-radius: 4px; background: white; display: block; cursor: crosshair; margin-top: 6px; width: 100%; height: 120px;"></canvas>
                    <button type="button" onclick="clearSignature()" style="margin-top: 6px; padding: 6px 12px; font-size: 10px; background: #f0f0f0; border: 1px solid #ccc; border-radius: 3px; cursor: pointer;">Clear Signature</button>
                    <div style="font-size: 8px; color: #666; margin-top: 4px;">Draw your signature in the box above. This will be captured as your digital signature.</div>
                </div>
            </div>
        </div>

        <!-- ======================== INSTRUCTIONS REFERENCE ======================== -->

        <div class="instructions-page">
            <div class="page-title" style="font-size: 12px; color: #0066cc;">Need Help?</div>

            <div class="instruction-text" style="font-size: 11px; line-height: 1.6;">
                <strong>For detailed instructions on how to complete this form, visit the official IRS Form W-9 page:</strong>
            </div>

            <div style="text-align: center; margin: 15px 0;">
                <a href="https://www.irs.gov/FormW9" target="_blank" style="display: inline-block; padding: 10px 20px; background: #0066cc; color: white; text-decoration: none; border-radius: 4px; font-weight: bold;">Visit IRS.gov/FormW9 for Official Instructions</a>
            </div>

            <div class="instruction-text" style="font-size: 10px; line-height: 1.5; margin-top: 15px;">
                The official IRS Form W-9 page includes:
                <ul style="margin-left: 20px; margin-top: 8px;">
                    <li>Complete line-by-line instructions</li>
                    <li>Information about TIN types (SSN, EIN, ITIN)</li>
                    <li>Tax classification guidance</li>
                    <li>How to get a TIN if you don't have one</li>
                    <li>Penalty information</li>
                    <li>The current official Form W-9</li>
                </ul>
            </div>

        </div>

        <!-- Privacy Notice -->
        <div style="margin-top: 30px; padding: 15px; background: #f0f4f8; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 10px; line-height: 1.5;">
            <strong style="display: block; margin-bottom: 8px; color: #0f172a;">🔒 Privacy Notice</strong>
            <p style="margin: 0;">Information collected through this Substitute Form W-9 is used solely for tax reporting, contractor onboarding, and compliance purposes. Access to taxpayer information is restricted to authorized personnel only.</p>
        </div>

    </form>

    <!-- Sidebar: ID Verification & Submit -->
    <div class="sidebar">
        <div class="sidebar-card">
            <div class="error-box" id="errorBox"></div>

            <div class="sidebar-title">📸 Government ID Verification</div>

            <div class="info-box">
                Upload clear images of BOTH sides of your government-issued ID
            </div>

            <div class="form-group">
                <label>Type of ID <span class="required">*</span></label>
                <select id="idType" required>
                    <option value="">-- Select ID Type --</option>
                    <option value="driver_license">Driver's License</option>
                    <option value="passport">Passport</option>
                    <option value="state_id">State ID Card</option>
                    <option value="other">Other Government ID</option>
                </select>
            </div>

            <div class="file-upload">
                <div class="file-upload-label">Front of ID <span class="required">*</span></div>
                <div class="file-upload-area" onclick="document.getElementById('idFront').click();">
                    <div>📤 Click to upload or drag & drop</div>
                </div>
                <input type="file" id="idFront" accept="image/jpeg,image/png,image/jpg" required>
                <div id="idFrontPreview" class="file-preview"></div>
            </div>

            <div class="file-upload">
                <div class="file-upload-label">Back of ID <span class="required">*</span></div>
                <div class="file-upload-area" onclick="document.getElementById('idBack').click();">
                    <div>📤 Click to upload or drag & drop</div>
                </div>
                <input type="file" id="idBack" accept="image/jpeg,image/png,image/jpg" required>
                <div id="idBackPreview" class="file-preview"></div>
            </div>

            <button type="button" id="submitBtn" onclick="submitForm()" class="btn btn-primary" style="margin-top: 20px;">
                ✓ Submit W-9 Form
            </button>

            <div class="loading" id="loader">
                <div class="spinner"></div>
                <p style="font-size: 11px; color: #666;">Processing your submission...</p>
            </div>
        </div>
    </div>
</div>

<script>
// ========== SIGNATURE FUNCTIONALITY ==========
let isDrawing = false;
let canvas, ctx;

function initSignatureCanvas() {
    canvas = document.getElementById('signatureCanvas');
    ctx = canvas.getContext('2d');

    // Set proper canvas resolution
    const rect = canvas.getBoundingClientRect();
    canvas.width = rect.width * window.devicePixelRatio;
    canvas.height = rect.height * window.devicePixelRatio;
    ctx.scale(window.devicePixelRatio, window.devicePixelRatio);

    // Set drawing properties
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';
    ctx.lineWidth = 2;
    ctx.strokeStyle = '#000';

    canvas.addEventListener('mousedown', startDrawing);
    canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('mouseup', stopDrawing);
    canvas.addEventListener('mouseout', stopDrawing);

    // Touch support
    canvas.addEventListener('touchstart', handleTouch);
    canvas.addEventListener('touchmove', handleTouch);
    canvas.addEventListener('touchend', stopDrawing);
}

function handleTouch(e) {
    e.preventDefault();
    const touch = e.touches[0];
    const mouseEvent = new MouseEvent(e.type === 'touchstart' ? 'mousedown' : 'mousemove', {
        clientX: touch.clientX,
        clientY: touch.clientY
    });
    canvas.dispatchEvent(mouseEvent);
}

function startDrawing(e) {
    isDrawing = true;
    const rect = canvas.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;
    ctx.beginPath();
    ctx.moveTo(x, y);
}

function draw(e) {
    if (!isDrawing) return;
    const rect = canvas.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;
    ctx.lineTo(x, y);
    ctx.stroke();
}

function stopDrawing() {
    isDrawing = false;
    ctx.closePath();
}

function clearSignature() {
    const rect = canvas.getBoundingClientRect();
    ctx.clearRect(0, 0, rect.width, rect.height);
}

// Handle signature method toggle
document.querySelectorAll('input[name="signatureMethod"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const typedSection = document.getElementById('typedSigSection');
        const canvasSection = document.getElementById('canvasSigSection');

        if (this.value === 'typed') {
            typedSection.style.display = 'block';
            canvasSection.style.display = 'none';
            document.getElementById('typedSignature').focus();
        } else {
            typedSection.style.display = 'none';
            canvasSection.style.display = 'block';
            // Initialize canvas when shown
            setTimeout(initSignatureCanvas, 100);
        }
    });
});

// ========== AUTO-FORMAT NUMERIC INPUTS ==========
function autoFormatNumeric(e, maxLen) {
    e.target.value = e.target.value.replace(/[^0-9]/g, '').slice(0, maxLen);
}

document.getElementById('ssn1').addEventListener('input', (e) => autoFormatNumeric(e, 3));
document.getElementById('ssn2').addEventListener('input', (e) => autoFormatNumeric(e, 2));
document.getElementById('ssn3').addEventListener('input', (e) => autoFormatNumeric(e, 4));
document.getElementById('ein1').addEventListener('input', (e) => autoFormatNumeric(e, 2));
document.getElementById('ein2').addEventListener('input', (e) => autoFormatNumeric(e, 7));

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
            preview.innerHTML = '<div style="color: red; font-size: 11px;">❌ File exceeds 5 MB limit</div>';
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
            name.style.fontSize = '10px';
            name.style.color = '#0066cc';
            name.style.marginTop = '5px';
            preview.appendChild(name);
        };
        reader.readAsDataURL(file);
    }
}

async function submitForm() {
    const errorBox = document.getElementById('errorBox');
    errorBox.style.display = 'none';
    errorBox.innerHTML = '';

    const line1 = document.getElementById('line1').value.trim();
    const idType = document.getElementById('idType').value;
    const idFront = document.getElementById('idFront').files[0];
    const idBack = document.getElementById('idBack').files[0];
    const certDate = document.getElementById('certDate').value.trim();
    const certifyCheckbox = document.getElementById('certificationAck').checked;
    const sigMethod = document.querySelector('input[name="signatureMethod"]:checked').value;

    let tinValue = '';
    const tinType = document.querySelector('input[name="tinType"]:checked').value;

    if (tinType === 'ssn') {
        const ssn1 = document.getElementById('ssn1').value.trim();
        const ssn2 = document.getElementById('ssn2').value.trim();
        const ssn3 = document.getElementById('ssn3').value.trim();
        if (ssn1 && ssn2 && ssn3) tinValue = ssn1 + '-' + ssn2 + '-' + ssn3;
    } else {
        const ein1 = document.getElementById('ein1').value.trim();
        const ein2 = document.getElementById('ein2').value.trim();
        if (ein1 && ein2) tinValue = ein1 + '-' + ein2;
    }

    // Get signature
    let signature = '';
    let signatureImage = null;
    if (sigMethod === 'typed') {
        signature = document.getElementById('typedSignature').value.trim();
    } else {
        signatureImage = document.getElementById('signatureCanvas').toDataURL('image/png');
    }

    const errors = [];
    if (!line1) errors.push('✗ Legal Name (Line 1) is required');
    if (!tinValue) errors.push('✗ Taxpayer Identification Number (TIN) is required');
    if (!idType) errors.push('✗ ID Type is required');
    if (!idFront) errors.push('✗ Front of ID is required');
    if (!idBack) errors.push('✗ Back of ID is required');
    if (!certDate) errors.push('✗ Certification Date is required');
    if (sigMethod === 'typed' && !signature) errors.push('✗ Signature (typed name) is required');
    if (sigMethod === 'draw' && !signatureImage) errors.push('✗ Signature (drawn) is required');
    if (!certifyCheckbox) errors.push('✗ You must certify the information is accurate');

    if (errors.length > 0) {
        errorBox.innerHTML = '<strong>Please correct these errors:</strong><br>' + errors.join('<br>');
        errorBox.style.display = 'block';
        document.querySelector('.form-document').scrollIntoView({ behavior: 'smooth', block: 'start' });
        return;
    }

    document.getElementById('submitBtn').disabled = true;
    document.getElementById('loader').style.display = 'block';

    const formData = new FormData();
    formData.append('id_document_type', idType);
    formData.append('id_front_image', idFront);
    formData.append('id_back_image', idBack);
    formData.append('certification_signed', 'on');
    formData.append('signature_method', sigMethod);
    if (signatureImage) {
        formData.append('signature_image', signatureImage);
    }

    const w9Data = {
        'line1_name': line1,
        'line2_business': document.getElementById('line2').value.trim(),
        'line3a_tax': Array.from(document.querySelectorAll('input[name="tax_3a"]:checked')).map(el => el.value),
        'line3b': document.getElementById('line3b').checked,
        'line4_exempt': document.getElementById('line4exempt').value.trim(),
        'line4_fatca': document.getElementById('line4fatca').value.trim(),
        'line5_address': document.getElementById('line5').value.trim(),
        'line6_city_state_zip': document.getElementById('line6').value.trim(),
        'tin_type': tinType,
        'tin_number': tinValue,
        'signature_type': sigMethod,
        'signature_typed': signature,
        'certification_date': certDate,
        'certified': true,
    };

    formData.append('pdf_form_data', JSON.stringify(w9Data));

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
            errorBox.innerHTML = '<strong>Error:</strong><br>' + (data.message || 'An error occurred.');
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