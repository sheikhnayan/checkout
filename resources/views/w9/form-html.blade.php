<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Form W-9 - Request for Taxpayer Identification Number and Certification (Rev. 3-2024)</title>
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

        .page-break {
            page-break-after: always;
            margin: 40px 0;
            padding: 30px 0;
            border-bottom: 1px dashed #ccc;
        }

        /* Form Header */
        .form-header {
            display: grid;
            grid-template-columns: 70px 1fr 110px;
            gap: 20px;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 12px;
            align-items: start;
        }

        .header-left {
            text-align: left;
            line-height: 1.1;
        }

        .header-left-text {
            font-weight: bold;
            font-size: 10px;
            margin-bottom: 2px;
        }

        .header-left-form {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .header-left-sub {
            font-size: 9px;
            line-height: 1.3;
        }

        .header-center {
            text-align: center;
        }

        .header-center h1 {
            font-size: 17px;
            font-weight: bold;
            line-height: 1.2;
            margin-bottom: 8px;
        }

        .header-center-sub {
            font-size: 9px;
            color: #666;
            margin: 4px 0;
        }

        .header-right {
            text-align: center;
            font-weight: bold;
            font-size: 9px;
            line-height: 1.4;
        }

        /* Form Sections */
        .before-begin {
            border: 1px solid #999;
            padding: 10px;
            margin-bottom: 15px;
            background: #f9f9f9;
            font-size: 10px;
            line-height: 1.5;
        }

        .before-begin-title {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .form-line {
            display: grid;
            grid-template-columns: 30px 1fr;
            gap: 12px;
            margin-bottom: 15px;
            align-items: start;
        }

        .line-number {
            font-weight: bold;
            font-size: 11px;
            padding-top: 2px;
        }

        .line-content {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .line-input {
            border: none;
            border-bottom: 1px solid #000;
            padding: 2px 4px;
            font-family: Arial, sans-serif;
            font-size: 11px;
            background: white;
            min-height: 20px;
        }

        .line-input:focus {
            outline: none;
            background: #fffacd;
        }

        .line-label {
            font-size: 10px;
            color: #333;
            margin-top: 2px;
        }

        .line-description {
            font-size: 10px;
            color: #666;
            margin-bottom: 5px;
            line-height: 1.4;
        }

        /* Checkboxes */
        .checkbox-group {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin: 8px 0;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 10px;
        }

        .checkbox-item input[type="checkbox"],
        .checkbox-item input[type="radio"] {
            width: 13px;
            height: 13px;
            cursor: pointer;
            margin: 0;
        }

        .checkbox-item label {
            cursor: pointer;
            margin: 0;
        }

        /* Part Headers */
        .part-header {
            font-weight: bold;
            font-size: 12px;
            margin: 20px 0 12px 0;
            padding: 8px 0;
            border-top: 2px solid #000;
            border-bottom: 1px solid #000;
            display: grid;
            grid-template-columns: 80px 1fr;
            gap: 15px;
        }

        /* Instructions Box */
        .instructions-box {
            background: #f9f9f9;
            border: 1px solid #999;
            padding: 10px;
            margin: 10px 0;
            font-size: 10px;
            line-height: 1.5;
        }

        .instructions-title {
            font-weight: bold;
            display: block;
            margin-bottom: 6px;
            font-size: 10px;
        }

        /* TIN Section */
        .tin-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin: 12px 0;
        }

        .tin-option {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .tin-boxes {
            display: flex;
            gap: 6px;
            align-items: center;
        }

        .tin-box {
            border: 1px solid #000;
            width: 23px;
            height: 18px;
            text-align: center;
            padding: 2px;
            font-weight: bold;
            font-size: 11px;
        }

        .tin-separator {
            font-weight: bold;
            font-size: 12px;
        }

        /* Certification Section */
        .certification-box {
            border: 1px solid #999;
            padding: 12px;
            margin: 15px 0;
            background: #f9f9f9;
            font-size: 10px;
            line-height: 1.5;
        }

        .certification-title {
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 8px;
        }

        .cert-item {
            margin-bottom: 6px;
            padding-left: 15px;
        }

        .signature-section {
            margin-top: 15px;
            padding-top: 12px;
            border-top: 1px solid #999;
        }

        .signature-line {
            display: grid;
            grid-template-columns: 200px 1fr 150px 1fr;
            gap: 15px;
            align-items: end;
            margin-top: 12px;
        }

        .sig-label {
            font-weight: bold;
            font-size: 10px;
        }

        .sig-area {
            border-bottom: 1px solid #000;
            height: 24px;
        }

        .date-input {
            border: none;
            border-bottom: 1px solid #000;
            width: 100%;
            padding: 2px;
            font-size: 11px;
        }

        /* Instructions Pages */
        .instructions-section {
            margin-top: 30px;
        }

        .instructions-title-page {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            text-align: center;
        }

        .section-heading {
            font-weight: bold;
            font-size: 11px;
            margin-top: 15px;
            margin-bottom: 8px;
            border-bottom: 1px solid #999;
        }

        .instruction-text {
            font-size: 10px;
            line-height: 1.5;
            margin-bottom: 8px;
        }

        .instruction-list {
            margin-left: 20px;
            margin-bottom: 10px;
        }

        .instruction-list li {
            margin-bottom: 6px;
            font-size: 10px;
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

        .note-box {
            background: #fff9e6;
            border-left: 3px solid #ff9800;
            padding: 8px;
            margin: 8px 0;
            font-size: 9px;
            line-height: 1.4;
        }

        .note-label {
            font-weight: bold;
            font-size: 10px;
        }

        .grid-2col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .grid-3col {
            display: grid;
            grid-template-columns: 2fr 80px 100px;
            gap: 8px;
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
        <div class="form-header">
            <div class="header-left">
                <div class="header-left-text">Form</div>
                <div class="header-left-form">W-9</div>
                <div class="header-left-sub">
                    (Rev. March 2024)<br>
                    Department of the Treasury<br>
                    Internal Revenue Service
                </div>
            </div>
            <div class="header-center">
                <h1>Request for Taxpayer<br>Identification Number and<br>Certification</h1>
                <p class="header-center-sub">Go to <strong>www.irs.gov/FormW9</strong> for instructions and the latest information.</p>
            </div>
            <div class="header-right">
                Give form to the<br>
                requester. Do not<br>
                send to the IRS.
            </div>
        </div>

        <!-- Before You Begin -->
        <div class="before-begin">
            <div class="before-begin-title">Before you begin.</div>
            For guidance related to the purpose of Form W-9, see <strong>Purpose of Form</strong> on page 2. For guidance on line 3b and the definition of "foreign partners, owners, or beneficiaries," see <strong>Definitions</strong> on page 3. For additional information, see the <strong>General Instructions for Forms W-9, W-8BEN, W-8BEN-E, and W-8ECI</strong> on page 3.
            <div style="margin-top: 6px;">
                <strong>1</strong> Name of entity/individual. An entry is required. (For a sole proprietor or disregarded entity, enter the owner's name on line 1, and enter the business/disregarded entity's name on line 2.)
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
                <div class="line-label">Business name/disregarded entity name, if different from above</div>
            </div>
        </div>

        <!-- Line 3a: Tax Classification -->
        <div class="form-line">
            <div class="line-number">3a</div>
            <div class="line-content">
                <div class="line-description">Check the appropriate box for your federal tax classification of the entity whose name is entered on line 1. Check only one box on line 3a.</div>

                <div class="checkbox-group">
                    <div class="checkbox-item">
                        <input type="radio" id="tax_individual" name="tax_classification" value="individual">
                        <label for="tax_individual">Individual/sole proprietor</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="radio" id="tax_ccorp" name="tax_classification" value="c_corporation">
                        <label for="tax_ccorp">C corporation</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="radio" id="tax_scorp" name="tax_classification" value="s_corporation">
                        <label for="tax_scorp">S corporation</label>
                    </div>
                </div>

                <div class="checkbox-group">
                    <div class="checkbox-item">
                        <input type="radio" id="tax_partnership" name="tax_classification" value="partnership">
                        <label for="tax_partnership">Partnership</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="radio" id="tax_trust" name="tax_classification" value="trust_estate">
                        <label for="tax_trust">Trust/estate</label>
                    </div>
                </div>

                <div style="margin: 8px 0 8px 20px; display: flex; align-items: center; gap: 8px;">
                    <div class="checkbox-item" style="margin: 0;">
                        <input type="checkbox" id="tax_llc">
                        <label for="tax_llc"><strong>LLC.</strong> Enter the tax classification (C = C corporation, S = S corporation, P = Partnership)</label>
                    </div>
                    <input type="text" id="llc_code" style="border: none; border-bottom: 1px solid #000; width: 40px; padding: 2px; font-size: 11px;" maxlength="1">
                </div>

                <div class="note-box" style="margin-left: 20px; margin-bottom: 8px;">
                    <div class="note-label">Note:</div>
                    Check the "LLC" box above and enter the appropriate code for the tax classification of the LLC, unless it is a disregarded entity. A disregarded entity should instead check the appropriate box for the tax classification of its owner.
                </div>

                <div class="checkbox-item" style="margin-left: 20px;">
                    <input type="checkbox" id="tax_other">
                    <label for="tax_other">Other (see instructions)</label>
                </div>
            </div>
        </div>

        <!-- Line 3b: Foreign Partners -->
        <div class="form-line">
            <div class="line-number">3b</div>
            <div class="line-content">
                <div class="line-description">Check this box if you are a partnership (including an LLC classified as a partnership), trust, or estate that has any foreign partners, owners, or beneficiaries, and you are providing this form to a partnership, trust, or estate in which you have an ownership interest, check this box if you have any foreign partners, owners, or beneficiaries. See instructions.</div>
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
                <div class="line-description">Exemptions (codes apply only to certain entities, not individuals; see instructions on page 3):</div>

                <div class="grid-2col">
                    <div>
                        <input type="text" id="line4exempt" class="line-input" maxlength="2">
                        <div class="line-label">Exempt payee code (if any)</div>
                    </div>
                    <div>
                        <input type="text" id="line4fatca" class="line-input" maxlength="2">
                        <div class="line-label">Exemption from FATCA reporting code (if any)</div>
                    </div>
                </div>
                <div class="note-box">
                    (Applies to accounts maintained outside the United States.)
                </div>
            </div>
        </div>

        <!-- Line 5: Address -->
        <div class="form-line">
            <div class="line-number">5</div>
            <div class="line-content">
                <input type="text" id="line5" class="line-input">
                <div class="line-label">Address (number, street, and apt. or suite no.). See instructions.</div>
            </div>
        </div>

        <!-- Line 6: City, State, ZIP -->
        <div class="form-line">
            <div class="line-number">6</div>
            <div class="line-content">
                <div class="grid-3col">
                    <input type="text" id="line6city" class="line-input">
                    <input type="text" id="line6state" class="line-input" maxlength="2" style="text-transform: uppercase;">
                    <input type="text" id="line6zip" class="line-input" maxlength="10">
                </div>
                <div class="line-label">City, state, and ZIP code</div>
            </div>
        </div>

        <!-- Line 7: Account Numbers -->
        <div class="form-line">
            <div class="line-number">7</div>
            <div class="line-content">
                <input type="text" id="line7" class="line-input">
                <div class="line-label">List account number(s) here (optional)</div>
            </div>
        </div>

        <!-- PART I: Taxpayer Identification Number (TIN) -->
        <div class="part-header">
            <div><strong>Part I</strong></div>
            <div><strong>Taxpayer Identification Number (TIN)</strong></div>
        </div>

        <div class="instructions-box">
            <span class="instructions-title">Enter your TIN in the appropriate box. The TIN provided must match the name given on line 1 to avoid backup withholding. For individuals, this is your social security number (SSN). However, for a resident alien, sole proprietor, or disregarded entity, see the instructions for Part I on page 4. For other entities, it is your employer identification number (EIN). If you do not have a number, see How to get a TIN on page 5.</span>
            <div style="margin-top: 6px; font-style: italic;">
                <strong>Note:</strong> If the account is in more than one name, see the instructions for line 1. See also What Name and Number To Give the Requester on page 4 for guidelines on whose number to enter.
            </div>
        </div>

        <div class="tin-section">
            <!-- SSN -->
            <div class="tin-option">
                <div class="checkbox-item">
                    <input type="radio" id="tin_ssn" name="tin_type" value="ssn" checked>
                    <label for="tin_ssn">Social security number</label>
                </div>
                <div class="tin-boxes">
                    <input type="text" id="ssn1" class="tin-box" maxlength="3" inputmode="numeric">
                    <div class="tin-separator">−</div>
                    <input type="text" id="ssn2" class="tin-box" maxlength="2" inputmode="numeric">
                    <div class="tin-separator">−</div>
                    <input type="text" id="ssn3" class="tin-box" maxlength="4" inputmode="numeric">
                </div>
            </div>

            <!-- EIN -->
            <div class="tin-option">
                <div class="checkbox-item">
                    <input type="radio" id="tin_ein" name="tin_type" value="ein">
                    <label for="tin_ein">Employer identification number</label>
                </div>
                <div class="tin-boxes">
                    <input type="text" id="ein1" class="tin-box" maxlength="2" inputmode="numeric">
                    <div class="tin-separator">−</div>
                    <input type="text" id="ein2" class="tin-box" maxlength="7" inputmode="numeric">
                </div>
            </div>
        </div>

        <!-- PART II: Certification -->
        <div class="part-header">
            <div><strong>Part II</strong></div>
            <div><strong>Certification</strong></div>
        </div>

        <div class="certification-box">
            <div class="certification-title">Under penalties of perjury, I certify that:</div>

            <div class="cert-item">1. The number shown on this form is my correct taxpayer identification number (or I am waiting for a number to be issued to me); and</div>

            <div class="cert-item">2. I am not subject to backup withholding because: (a) I have not been notified by the Internal Revenue Service (IRS) that I am subject to backup withholding as a result of a failure to report all interest or dividends, or (b) the IRS has notified me that I am no longer subject to backup withholding; and</div>

            <div class="cert-item">3. I am a U.S. citizen or other U.S. person (defined below); and</div>

            <div class="cert-item">4. The FATCA code(s) entered on this form (if any) indicating that I am exempt from FATCA reporting is correct.</div>

            <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #999; font-size: 9px; line-height: 1.5;">
                <strong>Certification instructions.</strong> You must cross out item 2 above if you have been notified by the IRS that you are currently subject to backup withholding because you have failed to report all interest and dividends on your tax return. For real estate transactions, item 2 does not apply. For mortgage interest paid, acquisition or abandonment of secured property, cancellation of debt, contributions to an individual retirement arrangement (IRA), and generally, payments other than interest and dividends, you are not required to sign the certification, but you must provide your correct TIN. See the instructions for Part II on page 5.
            </div>

            <!-- Signature Section -->
            <div class="signature-section">
                <div class="signature-line">
                    <div>
                        <div class="sig-label">Sign Here</div>
                        <div class="sig-area"></div>
                    </div>
                    <div>
                        <div class="sig-label">Signature of<br>U.S. person</div>
                    </div>
                    <div>
                        <div class="sig-label">Date</div>
                        <input type="text" id="certification_date" class="date-input" placeholder="">
                    </div>
                    <div></div>
                </div>
            </div>
        </div>

        <!-- ======================== PAGE 2: GENERAL INSTRUCTIONS ======================== -->

        <div class="page-break"></div>

        <div class="instructions-section">
            <div class="instructions-title-page">General Instructions</div>

            <div class="section-heading">What's New</div>
            <div class="instruction-text">
                The W-9 form has been updated for 2024. See the top of page 1 for the revision date and go to <strong>www.irs.gov/FormW9</strong> for information about any recent updates to this form and its instructions.
            </div>

            <div class="section-heading">Purpose of Form</div>
            <div class="instruction-text">
                Use Form W-9 to provide your correct Taxpayer Identification Number (TIN) to the person who is required to file an information return with the IRS to report, for example:
            </div>
            <ul class="instruction-list">
                <li>Income you received from their business;</li>
                <li>Mortgage interest you paid;</li>
                <li>Dividends and broker proceeds paid to you;</li>
                <li>Agricultural payments made to you;</li>
                <li>Health care and dental insurance information;</li>
                <li>Payments to you from a federal or state government;</li>
                <li>Certain payments from a partnership, S corporation, trust, commodity broker, or barter exchange; or</li>
                <li>Payment card network transactions (submitted by third party settlement organizations, payment processors, etc.).</li>
            </ul>

            <div class="instruction-text">
                In addition, you may need to provide a Form W-9 if you are:
            </div>
            <ul class="instruction-list">
                <li>A noncitizen alien who has not been issued a Social Security Number (SSN) by the Social Security Administration (SSA) (see Nonresident Alien Individuals on page 4 of these instructions);</li>
                <li>A nonresident alien entity;</li>
                <li>An individual who has changed their name; or</li>
                <li>An entity that has changed its name or tax classification.</li>
            </ul>

            <div class="section-heading">Who Must File</div>
            <div class="instruction-text">
                You must file a Form W-9 if you are a U.S. person and the following conditions apply:
            </div>
            <ul class="instruction-list">
                <li>You do not have a Social Security Number (SSN) but are eligible to obtain one;</li>
                <li>You changed your name because of marriage or divorce;</li>
                <li>You changed your tax classification (such as from an S corporation to a C corporation or LLC); or</li>
                <li>You have been informed that the IRS has an incorrect TIN for you.</li>
            </ul>

            <div class="instruction-text">
                If you do not have a TIN, apply for one immediately. For information on how to apply, see How to get a TIN on page 5.
            </div>

            <div class="section-heading">Penalties</div>
            <div class="instruction-text">
                Failure to furnish a TIN may result in a penalty of $50 per Form W-9 not furnished to the IRS. Additionally, if you are found to be in violation of the anti-money-laundering (AML) regulations, civil penalties of up to $100,000 per violation may apply.
            </div>

            <div class="section-heading">Backup Withholding</div>
            <div class="instruction-text">
                Certain payees who do not furnish their TIN to a requester are subject to backup withholding. Backup withholding is the withholding of a percentage of reportable payments. If you are subject to backup withholding, you must check the appropriate box on line 2 of your Form W-9. Backup withholding does not apply to real estate transactions. You will not be subject to backup withholding if you provide the correct TIN and the requester does not have notice that you are subject to backup withholding.
            </div>

            <!-- Continue on next page -->
        </div>

        <!-- ======================== PAGE 3: INSTRUCTIONS (CONTINUED) ======================== -->

        <div class="page-break"></div>

        <div class="instructions-section">

            <div class="section-heading">Specific Instructions</div>

            <div class="instruction-text">
                <strong>Line 1 — Name</strong>
            </div>
            <div class="instruction-text">
                If you are an individual, enter the name shown on your income tax return. However, if you have changed your name on an amended return filed with the IRS, use the new name. If the entity applying for an EIN is a disregarded entity, the name of the entity owner or sole proprietor should be entered on line 1. For a sole proprietorship, you may enter your business name or your individual name, but the IRS prefers your individual name. An entry is required.
            </div>

            <div class="instruction-text">
                <strong>Line 2 — Business Name/Disregarded Entity Name</strong>
            </div>
            <div class="instruction-text">
                Enter your business name. This name should match the name shown on your federal income tax return. Do not enter a "doing business as" (DBA) name or a trade name unless it is shown on your federal income tax return. If you have a disregarded entity, enter the name of the disregarded entity on line 2. A sole proprietor or disregarded entity should enter the business/disregarded entity name on line 2, and the owner's name on line 1.
            </div>

            <div class="instruction-text">
                <strong>Line 3a — Tax Classification</strong>
            </div>
            <div class="instruction-text">
                Check the box that corresponds to the tax classification of the entity on line 1. Check only one box. Refer to the instructions for the type of entity and its tax classification.
            </div>

            <div class="instruction-text">
                <strong>Individual/Sole Proprietor</strong> — You are a self-employed individual if you carry on a business as a sole proprietor and you are not otherwise classified as a partnership, corporation, S corporation, or trust/estate. You should also enter your SSN on the appropriate line in Part I.
            </div>

            <div class="instruction-text">
                <strong>C Corporation</strong> — A C corporation is any corporation other than an S corporation. This includes a publicly traded corporation. You should also enter your EIN on the appropriate line in Part I.
            </div>

            <div class="instruction-text">
                <strong>S Corporation</strong> — An S corporation is a business entity that has made a valid election to be taxed as an S corporation. You should also enter your EIN on the appropriate line in Part I.
            </div>

            <div class="instruction-text">
                <strong>Partnership</strong> — A partnership includes a general partnership, limited partnership (LP), limited liability partnership (LLP), and limited liability company (LLC) that has elected to be taxed as a partnership. You should also enter your EIN on the appropriate line in Part I.
            </div>

            <div class="instruction-text">
                <strong>Trust/Estate</strong> — A trust or estate includes a grantor retained annuity trust (GRAT), charitable remainder trust, or other trust. You should also enter your EIN on the appropriate line in Part I, unless the trust is a grantor trust and the grantor is a U.S. person or a resident alien. In that case, enter the grantor's SSN.
            </div>

            <div class="instruction-text">
                <strong>LLC</strong> — If you check the "LLC" box on line 3a, you must enter the appropriate tax classification code in the space provided:
            </div>
            <ul class="instruction-list">
                <li>If the LLC is taxed as a corporation, enter "C" for a C corporation or "S" for an S corporation;</li>
                <li>If the LLC is taxed as a partnership, enter "P";</li>
                <li>If the LLC is a disregarded entity, see the instructions for a disregarded entity on the back of the form.</li>
            </ul>

            <div class="instruction-text">
                <strong>Line 3b — Foreign Partners, Owners, or Beneficiaries</strong>
            </div>
            <div class="instruction-text">
                Check this box if you are a partnership, trust, or estate that has foreign partners, owners, or beneficiaries, or if you are providing this form to a partnership, trust, or estate in which you have an ownership interest and you have any foreign partners, owners, or beneficiaries. Do not check this box if you are an individual, a corporation, or an S corporation.
            </div>

        </div>

        <!-- ======================== PAGE 4: INSTRUCTIONS (CONTINUED) ======================== -->

        <div class="page-break"></div>

        <div class="instructions-section">

            <div class="instruction-text">
                <strong>Line 4 — Exemptions</strong>
            </div>
            <div class="instruction-text">
                The codes that apply in this line are those for exempt payee and exempt from FATCA reporting. These codes apply only to certain entities, not to individuals. Refer to the appropriate Internal Revenue Code section and Treasury Regulations for the codes that apply to your situation. For more information about exemption codes, see Pub. 1544, General Rules and Specifications for Substitute Forms W-9.
            </div>

            <div class="instruction-text">
                <strong>Lines 5 and 6 — Address</strong>
            </div>
            <div class="instruction-text">
                Provide your mailing address. You do not need to provide your street address if you have a P.O. box. Include your apartment number or suite number, if applicable.
            </div>

            <div class="instruction-text">
                <strong>Line 7 — Account Numbers</strong>
            </div>
            <div class="instruction-text">
                If the requester has assigned you an account number, enter that number. This line is optional and is used to help match the form with your account. If you do not have an account number, you may leave this line blank.
            </div>

            <div class="section-heading">Part I — Taxpayer Identification Number (TIN)</div>

            <div class="instruction-text">
                <strong>Social Security Number</strong>
            </div>
            <div class="instruction-text">
                Enter your Social Security Number (SSN) if you are an individual. A resident alien who does not have a Social Security Number (SSN) should enter his or her Individual Taxpayer Identification Number (ITIN). Do not include hyphens or spaces. Enter the number in the format XXX-XX-XXXX.
            </div>

            <div class="instruction-text">
                <strong>Employer Identification Number</strong>
            </div>
            <div class="instruction-text">
                If you are a business entity, you generally must have an Employer Identification Number (EIN). An EIN is a nine-digit number in the format XX-XXXXXXX. You may apply for an EIN online by going to <strong>www.irs.gov/ein</strong>, or by telephone, mail, or fax. See How to get a TIN, below.
            </div>

            <div class="section-heading">What Name and Number To Give the Requester</div>
            <div class="instruction-text">
                The name and number on the Form W-9 must match the name and number on file with the IRS. Report any changes in your name or number to the IRS before giving a new Form W-9 to the requester. Otherwise, the IRS may issue you a notice.
            </div>

            <div class="instruction-text">
                For a self-employed individual or sole proprietor, the name should be the individual's name, not the business name. However, if you have an EIN for a sole proprietorship, you can use either the individual's name or the business name. Generally, if the business is a corporation, partnership, or LLC, the name should match the business name.
            </div>

            <div class="instruction-text">
                If you are a disregarded entity, the name and number should match the owner's name and number. For trusts, estates, and other entities, the name and number should match the name and number of the entity as filed with the IRS.
            </div>

            <div class="section-heading">Nonresident Alien Individuals</div>
            <div class="instruction-text">
                If you are a nonresident alien individual, you cannot use Form W-9. Instead, use Form W-8BEN, <strong>Certificate of Foreign Status and Allocation of Residency by a Nonresident Alien Individual</strong>. A nonresident alien individual includes a citizen of a foreign country, a foreign corporation, a foreign partnership, a foreign trust, or a foreign estate, unless such person has a U.S. tax home and is otherwise a U.S. person for tax purposes. However, certain expatriates and former citizens may need to use Form W-9 or Form W-8BEN, depending on their status.
            </div>

            <div class="section-heading">Definitions</div>

            <div class="instruction-text">
                <strong>U.S. Person</strong> — A U.S. person includes:
            </div>
            <ul class="instruction-list">
                <li>A U.S. citizen;</li>
                <li>A resident alien for federal income tax purposes;</li>
                <li>A domestic corporation;</li>
                <li>A domestic partnership;</li>
                <li>A domestic trust; or</li>
                <li>A domestic estate.</li>
            </ul>

        </div>

        <!-- ======================== PAGE 5: INSTRUCTIONS (CONTINUED) ======================== -->

        <div class="page-break"></div>

        <div class="instructions-section">

            <div class="instruction-text">
                <strong>Disregarded Entity</strong> — A disregarded entity is a business entity (other than a corporation, partnership, or trust) that is not recognized as separate from its owner. A disregarded entity is treated as a sole proprietorship for federal income tax purposes. The owner of a disregarded entity should provide the owner's SSN, not the disregarded entity's EIN.
            </div>

            <div class="instruction-text">
                <strong>Foreign Partners, Owners, or Beneficiaries</strong> — Foreign partners, owners, or beneficiaries include any partner, member, owner, or beneficiary of a partnership, trust, or estate who is not a U.S. person. If you check the "Foreign Partners, Owners, or Beneficiaries" box on line 3b, you are certifying that you are a partnership, trust, or estate with at least one foreign partner, owner, or beneficiary.
            </div>

            <div class="section-heading">Part II — Certification</div>
            <div class="instruction-text">
                You must sign Form W-9. If you are a nonresident alien individual, do not sign this form. Instead, use Form W-8BEN. If you are a representative authorized to sign on behalf of an entity, you can sign the form on behalf of that entity.
            </div>

            <div class="instruction-text">
                The signature on this form certifies that the TIN provided is correct and that you are not subject to backup withholding, unless you have been notified otherwise. You are also certifying that you are a U.S. person, and that you have not been notified that the IRS has an incorrect TIN for you.
            </div>

            <div class="section-heading">How to Get a TIN</div>
            <div class="instruction-text">
                <strong>Social Security Number (SSN)</strong>
            </div>
            <div class="instruction-text">
                If you do not have a Social Security Number, you can apply for one by completing Form SS-5, <strong>Application for a Social Security Card</strong>. You can get this form from your local Social Security Administration office or by calling 1-800-772-1213. You can also download the form from the Social Security Administration's website at <strong>www.ssa.gov</strong>.
            </div>

            <div class="instruction-text">
                <strong>Individual Taxpayer Identification Number (ITIN)</strong>
            </div>
            <div class="instruction-text">
                If you are a resident alien who does not have a Social Security Number, you can apply for an Individual Taxpayer Identification Number (ITIN) by completing Form W-7, <strong>Application for an Individual Taxpayer Identification Number</strong>. You can get this form from the IRS website at <strong>www.irs.gov</strong>, or by calling 1-800-829-3676.
            </div>

            <div class="instruction-text">
                <strong>Employer Identification Number (EIN)</strong>
            </div>
            <div class="instruction-text">
                If you need an Employer Identification Number (EIN), you can apply for one online by going to <strong>www.irs.gov/ein</strong>. You can also apply by telephone at 1-800-829-4933, or by mail using Form SS-4, <strong>Application for Employer Identification Number</strong>. You can get Form SS-4 from the IRS website or by calling 1-800-829-3676.
            </div>

            <div class="section-heading">Backup Withholding</div>
            <div class="instruction-text">
                Backup withholding is the withholding of a percentage of a payee's reportable payment. The current backup withholding rate is 24%. You are subject to backup withholding if:
            </div>
            <ul class="instruction-list">
                <li>You have not furnished a taxpayer identification number (TIN) to the requester;</li>
                <li>The IRS has notified the requester that you furnished an incorrect TIN;</li>
                <li>The IRS has notified the requester that you are subject to backup withholding because you failed to report interest or dividend income on your tax return; or</li>
                <li>You have not certified to the requester that you are not subject to backup withholding.</li>
            </ul>

            <div class="instruction-text">
                If you are subject to backup withholding, the requester must withhold 24% of the payments made to you and remit the amount withheld to the IRS. However, certain payments are exempt from backup withholding, such as payments for real estate transactions, payments for certain gaming activities, and payments for certain fishing boat crew shares. For more information about backup withholding, see <strong>Publication 515, Withholding of Tax on Nonresident Aliens and Foreign Entities</strong>.
            </div>

            <div class="section-heading">FATCA Reporting Requirement</div>
            <div class="instruction-text">
                The Foreign Account Tax Compliance Act (FATCA) requires certain U.S. entities to report information about foreign financial assets and certain other items on their federal income tax return. FATCA exemption codes allow certain entities to claim exemption from FATCA reporting. If you have a FATCA exemption code, enter the code on line 4 of this form.
            </div>

        </div>

        <!-- ======================== PAGE 6: INSTRUCTIONS (FINAL) ======================== -->

        <div class="page-break"></div>

        <div class="instructions-section">

            <div class="section-heading">Privacy Act Notice</div>
            <div class="instruction-text">
                Section 6109 of the Internal Revenue Code requires you to provide your taxpayer identification number (TIN) on certain forms, statements, or other documents for tax-related matters. To ensure compliance with applicable federal laws imposing civil and criminal penalties, Form W-9 collects the TIN and other information about the taxpayer. The principal purpose of collecting the information is to ensure the proper identification of the taxpayer for federal tax purposes. The information collected on this form may be disclosed to the Department of Justice for civil and criminal litigation, and to cities, states, the District of Columbia, and U.S. possessions for tax administration purposes. The information may also be disclosed to other agencies as authorized by law.
            </div>

            <div class="section-heading">Penalties</div>
            <div class="instruction-text">
                <strong>Civil Penalty</strong>
            </div>
            <div class="instruction-text">
                If you knowingly or recklessly disregard the rules relating to Form W-9, you may be subject to penalties. Civil penalties for failure to furnish a TIN, providing an incorrect TIN, or failure to provide a Form W-9 when required may be up to $50 for each failure.
            </div>

            <div class="instruction-text">
                <strong>Criminal Penalty</strong>
            </div>
            <div class="instruction-text">
                Willful failure to furnish a TIN or providing a false or fraudulent TIN may result in criminal penalties, including fines of up to $250,000 and imprisonment for up to 3 years.
            </div>

            <div class="section-heading">Specific Instructions for Entities</div>

            <div class="instruction-text">
                <strong>Partnerships</strong>
            </div>
            <div class="instruction-text">
                A partnership should enter its Employer Identification Number (EIN) on line 3. Enter the partnership's name on line 1. If the partnership is classified as a disregarded entity, enter the owner's name on line 1 and the owner's Social Security Number on line 3.
            </div>

            <div class="instruction-text">
                <strong>S Corporations</strong>
            </div>
            <div class="instruction-text">
                An S corporation should enter its Employer Identification Number (EIN) on line 3. Enter the S corporation's name on line 1.
            </div>

            <div class="instruction-text">
                <strong>Trusts and Estates</strong>
            </div>
            <div class="instruction-text">
                A trust or estate should enter its Employer Identification Number (EIN) on line 3, unless the trust is a grantor trust and the grantor is a U.S. person or resident alien. In that case, enter the grantor's Social Security Number on line 3. Enter the trust or estate name on line 1.
            </div>

            <div class="instruction-text">
                <strong>Foreign Entities</strong>
            </div>
            <div class="instruction-text">
                If you are a foreign entity, you should not complete Form W-9. Instead, you should complete Form W-8BEN-E, <strong>Certificate of Status of Beneficial Owner for U.S. Tax Withholding and Reporting (Entities)</strong>, or another appropriate Form W-8 series form based on your situation and status.
            </div>

            <div class="instruction-text">
                For additional information and forms, visit the IRS website at <strong>www.irs.gov/FormW9</strong> or call 1-800-TAX-FORM (1-800-829-3676).
            </div>

        </div>

    </form>

    <!-- Sidebar: ID Verification & Submit -->
    <div class="sidebar">
        <div class="sidebar-card">
            <div class="error-box" id="errorBox"></div>

            <div class="sidebar-title">📸 Government ID Verification</div>

            <div class="form-group">
                <label>Type of ID <span class="required">*</span></label>
                <select id="idDocumentType" required>
                    <option value="">-- Select ID Type --</option>
                    <option value="driver_license">Driver's License</option>
                    <option value="passport">Passport</option>
                    <option value="state_id">State ID Card</option>
                    <option value="other">Other Government ID</option>
                </select>
            </div>

            <div class="info-box">
                ⚠️ Upload clear images of BOTH sides of your government-issued ID
            </div>

            <div class="file-upload">
                <label style="font-weight: 600; font-size: 11px; color: #333; text-transform: uppercase; display: block; margin-bottom: 8px;">Front of ID <span class="required">*</span></label>
                <div class="file-upload-area" onclick="document.getElementById('idFront').click();">
                    <div>📤 Click to upload or drag & drop</div>
                </div>
                <input type="file" id="idFront" accept="image/jpeg,image/png,image/jpg" required>
                <div id="idFrontPreview" class="file-preview"></div>
                <div style="font-size: 9px; margin-top: 5px; color: #999;">✓ JPG or PNG | ✓ Max 5 MB</div>
            </div>

            <div class="file-upload">
                <label style="font-weight: 600; font-size: 11px; color: #333; text-transform: uppercase; display: block; margin-bottom: 8px;">Back of ID <span class="required">*</span></label>
                <div class="file-upload-area" onclick="document.getElementById('idBack').click();">
                    <div>📤 Click to upload or drag & drop</div>
                </div>
                <input type="file" id="idBack" accept="image/jpeg,image/png,image/jpg" required>
                <div id="idBackPreview" class="file-preview"></div>
                <div style="font-size: 9px; margin-top: 5px; color: #999;">✓ JPG or PNG | ✓ Max 5 MB</div>
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
// Auto-format numeric inputs
function autoFormatNumeric(e, maxLen) {
    e.target.value = e.target.value.replace(/[^0-9]/g, '').slice(0, maxLen);
}

document.getElementById('ssn1').addEventListener('input', (e) => autoFormatNumeric(e, 3));
document.getElementById('ssn2').addEventListener('input', (e) => autoFormatNumeric(e, 2));
document.getElementById('ssn3').addEventListener('input', (e) => autoFormatNumeric(e, 4));
document.getElementById('ein1').addEventListener('input', (e) => autoFormatNumeric(e, 2));
document.getElementById('ein2').addEventListener('input', (e) => autoFormatNumeric(e, 7));
document.getElementById('line6state').addEventListener('input', (e) => e.target.value = e.target.value.toUpperCase());

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

    // Get form values
    const line1 = document.getElementById('line1').value.trim();
    const idDocumentType = document.getElementById('idDocumentType').value;
    const idFront = document.getElementById('idFront').files[0];
    const idBack = document.getElementById('idBack').files[0];

    let tinValue = '';
    const tinType = document.querySelector('input[name="tin_type"]:checked').value;

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

    // Validate
    const errors = [];
    if (!line1) errors.push('✗ Full Name (Line 1) is required');
    if (!tinValue) errors.push('✗ Taxpayer Identification Number (TIN) is required');
    if (!idDocumentType) errors.push('✗ ID Type is required');
    if (!idFront) errors.push('✗ Front of ID is required');
    if (!idBack) errors.push('✗ Back of ID is required');

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
    formData.append('id_document_type', idDocumentType);
    formData.append('id_front_image', idFront);
    formData.append('id_back_image', idBack);
    formData.append('certification_signed', true);

    // Collect all W-9 form data
    const w9Data = {
        'line1_name': line1,
        'line2_business': document.getElementById('line2').value.trim(),
        'line3a_tax_classification': document.querySelector('input[name="tax_classification"]:checked')?.value || '',
        'line3b_foreign': document.getElementById('line3b').checked,
        'line4_exempt_code': document.getElementById('line4exempt').value.trim(),
        'line4_fatca_code': document.getElementById('line4fatca').value.trim(),
        'line5_address': document.getElementById('line5').value.trim(),
        'line6_city': document.getElementById('line6city').value.trim(),
        'line6_state': document.getElementById('line6state').value.trim(),
        'line6_zip': document.getElementById('line6zip').value.trim(),
        'line7_account': document.getElementById('line7').value.trim(),
        'tin_type': tinType,
        'tin_number': tinValue,
        'certification_date': document.getElementById('certification_date').value,
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