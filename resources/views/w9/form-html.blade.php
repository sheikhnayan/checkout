<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Form W-9 - Request for Taxpayer Identification Number and Certification (Rev. March 2024)</title>
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
        <div class="form-header">
            <div class="header-left">
                <div class="header-left-form">Form</div>
                <div class="header-left-number">W-9</div>
                <div class="header-left-sub">
                    (Rev. March 2024)<br>
                    Department of the<br>
                    Treasury Internal<br>
                    Revenue Service
                </div>
            </div>
            <div class="header-center">
                <h1>Request for Taxpayer<br>Identification Number and<br>Certification</h1>
                <p class="header-center-sub">Go to www.irs.gov/FormW9 for instructions and the latest information.</p>
            </div>
            <div class="header-right">
                Give form to the<br>
                requester. Do not<br>
                send to the IRS.
            </div>
        </div>

        <!-- Before You Begin -->
        <div class="before-begin">
            <div class="before-begin-text"><strong>Before you begin.</strong> For guidance related to the purpose of Form W-9, see <em>Purpose of Form</em>, below.</div>
            <div style="margin-top: 4px; font-size: 9px; line-height: 1.4;">
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
                    <label for="tax_other">Other (see instructions)</label>
                </div>
            </div>
        </div>

        <!-- Line 3b: Foreign Partners -->
        <div class="form-line">
            <div class="line-number">3b</div>
            <div class="line-content">
                <div style="font-size: 9px; line-height: 1.4; margin-bottom: 6px;">If you checked "Partnership" above and you are providing this form to a partnership, trust, or estate in which you have an ownership interest, check this box if you have any foreign partners, owners, or beneficiaries. See instructions.</div>
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
                <div style="font-size: 9px; margin-bottom: 6px;">Exemptions (codes apply only to certain entities, not individuals; see instructions on page 3):</div>

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
                <div class="line-label">Address (number, street, and apt. or suite no.). See instructions.</div>
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
            <span class="part-number"><strong>Part I</strong></span> <strong>Taxpayer Identification Number (TIN)</strong>
        </div>

        <div class="instructions-box">
            Enter your TIN in the appropriate box. The TIN provided must match the name given on line 1 to avoid backup withholding. For individuals, this is generally your social security number (SSN). However, for a resident alien, sole proprietor, or disregarded entity, see the instructions for Part I, later. For other entities, it is your employer identification number (EIN). If you do not have a number, see <em>How to get a TIN</em>, later.<br><br>
            <strong>Note:</strong> If the account is in more than one name, see the instructions for line 1. See also <em>What Name and Number To Give the Requester</em> for guidelines on whose number to enter.
        </div>

        <div class="tin-section">
            <!-- SSN -->
            <div class="tin-option">
                <div class="checkbox-item">
                    <input type="radio" id="tin_ssn" name="tinType" value="ssn" checked>
                    <label for="tin_ssn">Social security number</label>
                </div>
                <div class="tin-boxes">
                    <input type="text" id="ssn1" class="tin-box" maxlength="3" inputmode="numeric">
                    <div class="tin-separator">–</div>
                    <input type="text" id="ssn2" class="tin-box" maxlength="2" inputmode="numeric">
                    <div class="tin-separator">–</div>
                    <input type="text" id="ssn3" class="tin-box" maxlength="4" inputmode="numeric">
                </div>
            </div>

            <!-- EIN -->
            <div class="tin-option">
                <div class="checkbox-item">
                    <input type="radio" id="tin_ein" name="tinType" value="ein">
                    <label for="tin_ein">Employer identification number</label>
                </div>
                <div class="tin-boxes">
                    <input type="text" id="ein1" class="tin-box" maxlength="2" inputmode="numeric">
                    <div class="tin-separator">–</div>
                    <input type="text" id="ein2" class="tin-box" maxlength="7" inputmode="numeric">
                </div>
            </div>
        </div>

        <!-- PART II: Certification -->
        <div class="part-header">
            <span class="part-number"><strong>Part II</strong></span> <strong>Certification</strong>
        </div>

        <div class="certification-box">
            <div style="margin-bottom: 8px; font-size: 10px;">Under penalties of perjury, I certify that:</div>

            <div class="cert-item"><strong>1.</strong> The number shown on this form is my correct taxpayer identification number (or I am waiting for a number to be issued to me); and</div>

            <div class="cert-item"><strong>2.</strong> I am not subject to backup withholding because (a) I am exempt from backup withholding, or (b) I have not been notified by the Internal Revenue Service (IRS) that I am subject to backup withholding as a result of a failure to report all interest or dividends, or (c) the IRS has notified me that I am no longer subject to backup withholding; and</div>

            <div class="cert-item"><strong>3.</strong> I am a U.S. citizen or other U.S. person (defined below); and</div>

            <div class="cert-item"><strong>4.</strong> The FATCA code(s) entered on this form (if any) indicating that I am exempt from FATCA reporting is correct.</div>

            <div style="margin-top: 8px; padding-top: 8px; border-top: 1px solid #999; font-size: 8px; line-height: 1.3;">
                <strong>Certification instructions.</strong> You must cross out item 2 above if you have been notified by the IRS that you are currently subject to backup withholding because you have failed to report all interest and dividends on your tax return. For real estate transactions, item 2 does not apply. For mortgage interest paid, acquisition or abandonment of secured property, cancellation of debt, contributions to an individual retirement arrangement (IRA), and generally, payments other than interest and dividends, you are not required to sign the certification, but you must provide your correct TIN. See the instructions for Part II, later.
            </div>

            <!-- Signature Section -->
            <div class="signature-section">
                <div class="sig-item">
                    <div class="sig-label">Sign Here</div>
                    <div class="sig-area"></div>
                    <div style="font-size: 8px;">Signature of U.S. person</div>
                </div>
                <div class="sig-item">
                    <div class="sig-label">Date</div>
                    <input type="text" id="certDate" class="sig-input">
                </div>
            </div>
        </div>

        <!-- ======================== PAGE 2-6: GENERAL INSTRUCTIONS ======================== -->

        <div class="instructions-page">
            <div class="page-title">General Instructions</div>

            <div class="section-heading">What's New</div>
            <div class="instruction-text">
                Line 3a has been modified to clarify how a disregarded entity completes this line. An LLC that is a disregarded entity should check the appropriate box for the tax classification of its owner. Otherwise, it should check the "LLC" box and enter its appropriate tax classification.
            </div>

            <div class="instruction-text">
                New line 3b has been added to this form. A flow-through entity is required to complete this line to indicate that it has direct or indirect foreign partners, owners, or beneficiaries when it provides the Form W-9 to another flow-through entity in which it has an ownership interest. This change is intended to provide a flow-through entity with information regarding the status of its indirect foreign partners, owners, or beneficiaries, so that it can satisfy any applicable reporting requirements. For example, a partnership that has any indirect foreign partners may be required to complete Schedules K-2 and K-3. See the Partnership Instructions for Schedules K-2 and K-3 (Form 1065).
            </div>

            <div class="section-heading">Purpose of Form</div>
            <div class="instruction-text">
                An individual or entity (Form W-9 requester) who is required to file an information return with the IRS is giving you this form. You must provide your correct taxpayer identification number (TIN), which may be your social security number (SSN), individual taxpayer identification number (ITIN), adoption taxpayer identification number (ATIN), or employer identification number (EIN), to the requester. The requester will need your TIN to file an information return with the IRS reporting, for example, income paid to you, real estate transactions, mortgage interest you paid, contributions you made to an IRA, Student Loan Interest, gifts or charitable contributions you made, business transactions of $600 or more, etc.
            </div>

            <div class="instruction-text">
                The requester may use your TIN to prepare Form 1099-NEC (nonemployee compensation), Form 1099-MISC (miscellaneous income), Form 1099-K (Payment Card Network transactions), Form 1099-INT (interest earned or paid), Form 1099-OID (original issue discount), Form 1099-DIV (dividends, including those from stocks or mutual funds), Form 1099-S (proceeds from real estate transactions), Form 1099-B (proceeds from broker and barter exchange transactions), Form 1099-PATR (taxable distributions from partnerships), Form 1099-QA (qualified tuition program distributions), Form 1099-R (distributions from pensions, annuities, retirement or profit-sharing plans, IRAs, insurance contracts, etc.), Form 1099-SA (distributions from an HSA, Archer MSA, or health insurance premium payment assistance), Form 1098-T (qualified tuition and educational-related loan interest and payments), Form 1098 (student loan interest), Form 1098-HC (health coverage with employee health insurance premium tax credit information), Form 1099-HC5 (health insurance credit), Form 1098-MA (mortgage insurance premiums), Form 1098-T (qualified tuition and educational expenses), Form 1098-E (student loan interest paid), Form 1099-LTC (long-term care insurance contracts), and other forms to report that payment to you and, if applicable, to the IRS.
            </div>

            <div class="instruction-text">
                Please note that if you check "Other" on line 3a and do not provide an explanation with your submission, CartVIP may request clarification before processing your registration.
            </div>

            <div class="section-heading">Penalties</div>
            <div class="instruction-text">
                <strong>Failure to furnish TIN.</strong> If you fail to furnish your correct TIN to a requester, you are subject to a penalty of $50 for each failure unless your failure is due to reasonable cause and not to willful neglect.
            </div>

            <div class="instruction-text">
                <strong>Civil penalty for false information with respect to withholding.</strong> If you make a false statement with no reasonable basis that results in no backup withholding, you are subject to a $500 penalty.
            </div>

            <div class="instruction-text">
                <strong>Criminal penalty for falsifying information.</strong> Willfully falsifying certifications or affirmations may subject you to criminal penalties including fines and/or imprisonment.
            </div>

            <div class="instruction-text">
                <strong>Misuse of TINs.</strong> If the requester discloses or uses TINs in violation of federal law, the requester may be subject to civil and criminal penalties.
            </div>

            <div class="section-heading">Specific Instructions</div>

            <div class="instruction-text">
                <strong>Line 1.</strong> You must enter one of the following on this line; do not leave this line blank. The name should match the name on your tax return.
            </div>

            <ul class="bullet-list">
                <li><strong>If this Form W-9 is for a joint account</strong> (other than an account maintained by a foreign financial institution (FFI)), first, circulate the name of the person or entity whose number you entered in Part I of Form W-9. If you are providing Form W-9 to an FFI to document a joint account, each holder of the account that is a U.S. person must provide a Form W-9.</li>
                <li><strong>Individual.</strong> Generally, enter the name shown on your tax return. If you have changed your last name on an amended return filed with the IRS using either your current name or your prior name, enter the current name on line 1. You may be required by IRS regulations to enter your individual name as it was entered on your Form 1040 you filed with your application.</li>
                <li><strong>Sole proprietor or disregarded entity.</strong> Enter your business, trade, or "doing business as" (DBA) name on line 2.</li>
                <li><strong>Partnership, C corporation, S corporation, or LLC, other than a disregarded entity.</strong> Enter the entity's name as shown on the entity's tax return on line 1 and any business, trade, or DBA name on line 2.</li>
                <li><strong>Other entities.</strong> Enter your name as shown on required U.S. federal tax documents on line 1. This name must match the name shown on the charter or other legal document creating the entity. Enter any business, trade, or DBA name on line 2.</li>
                <li><strong>Disregarded entity.</strong> In general, a business entity that has a single owner, including an LLC, and is not a corporation, is disregarded as an entity separate from its owner (a disregarded entity). See Regulations section 301.7701-2(c). A disregarded entity should check the appropriate box for the tax classification of its owner. Enter the owner's name on line 1. The name of the disregarded entity should never be a disregarded entity. The name on line 1 should be the name shown on the income tax return on which the income should be reported. For example, if a foreign LLC that is treated as a disregarded entity for U.S. federal tax purposes has a sole owner, the U.S. owner of the disregarded entity must provide his or her name on line 1. If the direct owner of the disregarded entity is a foreign person, then enter the first owner in the U.S. ownership chain on line 1. If no U.S. person directly owns the disregarded entity, enter the disregarded entity's name on line 2.</li>
            </ul>

            <div class="instruction-text">
                <strong>Line 2.</strong> If you have a business name, trade name, DBA name, or disregarded entity name, enter it on line 2.
            </div>

            <div class="instruction-text">
                <strong>Line 3a.</strong> Check the appropriate box on line 3a for the U.S. federal tax classification of the person whose name is entered on line 1. Check only one box on line 3a.
            </div>

            <div class="instruction-text">
                <strong>Line 3b.</strong> Check this box if you are a partnership (including an LLC classified as a partnership) for U.S. federal tax purposes, trust, or estate that has any foreign partners, owners, or beneficiaries, and you are providing this form to a partnership, trust, or estate in which you have an ownership interest. You must check the box on line 3b if you receive a Form W-8 (or documentary evidence) from any partner, owner, or beneficiary that has checked the box on line 3b.
            </div>

            <div class="instruction-text" style="font-size: 8px; margin-top: 4px;">
                <strong>Note:</strong> A partnership that provides a Form W-9 and checks box 3b may be required to complete Schedules K-2 and K-3 (Form 1065). For more information, see the Partnership Instructions for Schedules K-2 and K-3 (Form 1065). If you are required to complete line 3b but fail to do so, you may not receive the information necessary to file a correct information return with the IRS or furnish a correct payee statement to you.
            </div>

            <div class="instruction-text">
                <strong>Line 4 Exemptions.</strong> If you are exempt from backup withholding and/or FATCA reporting, enter in the appropriate space on line 4 any code(s) that may apply to you.
            </div>

            <div class="instruction-text">
                <strong>Exempt payee code.</strong>
            </div>

            <ul class="bullet-list">
                <li>Generally, individuals (including sole proprietors) are not exempt from backup withholding.</li>
                <li>Except as provided below, corporations are exempt from backup withholding for certain payments, including interest and dividends.</li>
                <li>Corporations are not exempt from backup withholding for payments card or third-party network transactions.</li>
                <li>Corporations are not exempt from backup withholding with respect to attorneys' fees or payments made to attorneys.</li>
            </ul>

            <div class="instruction-text">
                The following codes identify payees that are exempt from backup withholding. Enter the appropriate code in the space on line 4.
            </div>

            <ul class="bullet-list">
                <li>1—An organization exempt from tax under section 501(a), any IRA, or a custodial account under section 403(b)(7) if the account satisfies the requirements of section 401(f)(2).</li>
                <li>2—The United States or any of its agencies or instrumentalities.</li>
                <li>3—A state, the District of Columbia, a U.S. commonwealth or territory, or any of their political subdivisions or instrumentalities.</li>
                <li>4—A foreign government or any of its political subdivisions, agencies, or instrumentalities.</li>
                <li>5—A corporation.</li>
                <li>6—A dealer in securities or commodities required to register in the United States, the District of Columbia, or a U.S. commonwealth or territory.</li>
                <li>7—A futures commission merchant registered with the Commodity Futures Trading Commission.</li>
                <li>8—A real estate investment trust.</li>
                <li>9—An entity registered at all times during the tax year under the Investment Company Act of 1940.</li>
                <li>10—A common trust fund as defined in section 584(a).</li>
                <li>11—A financial institution as defined in section 581.</li>
                <li>12—A middleman known in the investment community as a nominee or custodian.</li>
                <li>13—A trust exempt from tax under section 664 or described in section 4947.</li>
            </ul>

            <div class="instruction-text">
                <strong>Line 5.</strong> Enter your address (number, street, and apartment or suite number). This is where the requester of this Form W-9 will mail your information returns. If this address differs from the one on the requester already has on file, enter "NEW" at the top. If an address is provided, there is still a chance the IRS will use the address in their records.
            </div>

            <div class="instruction-text">
                <strong>Line 6.</strong> Enter your city, state, and ZIP code.
            </div>

            <div class="section-heading">Part I. Taxpayer Identification Number (TIN)</div>

            <div class="instruction-text">
                Enter your TIN in the appropriate box. If you are a resident alien and you do not have, and are not eligible to get, an SSN, your TIN is your IRS individual taxpayer identification number (ITIN). Enter it in the entry space for the Social security number. If you do not have an ITIN, see <em>How to get a TIN</em> below.
            </div>

            <div class="instruction-text">
                If you are a sole proprietor and you have an EIN, you may enter either your SSN or EIN. However, the IRS prefers that you use your SSN. If you have an EIN for a disregarded entity that has a foreign owner, enter the owner's SSN (or EIN, if the owner has one). If the LLC is classified as a corporation or partnership, enter the entity's EIN.
            </div>

            <div class="instruction-text">
                <strong>Note:</strong> See <em>What Name and Number To Give the Requester</em>, later, for further clarification of whose number to enter.
            </div>

            <div class="section-heading">How to get a TIN</div>

            <div class="instruction-text">
                If you do not have a TIN, apply for one immediately. To apply for an SSN, get Form SS-5, <em>Application for a Social Security Card</em>, from your local Social Security Administration office or get this form online at www.SSA.gov. You may also get this form by calling 1-800-772-1213. Use Form W-7, <em>Application for IRS Individual Taxpayer Identification Number</em>, to apply for an ITIN or, if you are an employer, apply for an EIN. You can apply for an EIN online by accessing the IRS website at www.irs.gov/ein. You can also apply by telephone, mail, or fax and obtain EIN. See How to Apply for an EIN on www.irs.gov/businesses to view, download, or print Form SS-4, <em>Application for Employer Identification Number</em>, and its instructions. You can apply for an EIN online by accessing the IRS website at www.irs.gov/ein. You can apply for an EIN online by accessing the IRS website at www.irs.gov/FormW7. You may also download, print Form W-7 and/or Form SS-4 mailed to you within 15 business days.
            </div>

            <div class="instruction-text">
                If you are asked to complete Form W-9 but do not have a TIN, apply for a TIN and give it to the requester before the time you are required to file a return with the IRS. Generally, you will have 60 days to get a TIN and give it to the requester. The 60-day rule does not apply to other types of payments. You will be subject to backup withholding on all such payments until you provide your TIN to the requester.
            </div>

            <div class="instruction-text">
                <strong>Note:</strong> Entering "Applied For" means that you have already applied for a TIN or that you intend to apply for one soon. See also <em>Establishing U.S. status for purposes of chapter 3 and chapter 4 withholding</em>, earlier, for when you may instead be subject to withholding under chapter 3 or 4 of the Code.
            </div>

            <div class="instruction-text">
                <strong>Caution:</strong> A disregarded U.S. entity that has a foreign owner must use the appropriate Form W-8.
            </div>

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
// Auto-format numeric inputs
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

    const errors = [];
    if (!line1) errors.push('✗ Legal Name (Line 1) is required');
    if (!tinValue) errors.push('✗ Taxpayer Identification Number (TIN) is required');
    if (!idType) errors.push('✗ ID Type is required');
    if (!idFront) errors.push('✗ Front of ID is required');
    if (!idBack) errors.push('✗ Back of ID is required');

    if (errors.length > 0) {
        errorBox.innerHTML = '<strong>Please correct these errors:</strong><br>' + errors.join('<br>');
        errorBox.style.display = 'block';
        document.querySelector('.form-document').scrollIntoView({ behavior: 'smooth', block: 'start' });
        return;
    }

    document.getElementById('submitBtn').disabled = true;
    document.getElementById('loader').style.display = 'block';

    const formData = new FormData();
    formData.append('id_type', idType);
    formData.append('id_front_image', idFront);
    formData.append('id_back_image', idBack);

    const w9Data = {
        'line1_name': line1,
        'line2_business': document.getElementById('line2').value.trim(),
        'line3a_tax': Array.from(document.querySelectorAll('input[name="tax_3a"]:checked')).map(el => el.value),
        'line3b': document.getElementById('line3b').checked,
        'line4_exempt': document.getElementById('line4exempt').value.trim(),
        'line4_fatca': document.getElementById('line4fatca').value.trim(),
        'line5_address': document.getElementById('line5').value.trim(),
        'line6_city_state_zip': document.getElementById('line6').value.trim(),
        'line7_account': document.getElementById('line7').value.trim(),
        'tin_type': tinType,
        'tin_number': tinValue,
        'cert_date': document.getElementById('certDate').value,
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