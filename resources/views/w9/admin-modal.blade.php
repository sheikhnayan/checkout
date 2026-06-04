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
            background: #f5f5f5 !important;
            padding: 20px !important;
            font-size: 11px !important;
            line-height: 1.35 !important;
            color: #000 !important;
        }

        body, body * {
            color: #000 !important;
            background-color: inherit !important;
        }

        .page-wrapper, .page-wrapper * {
            color: #000 !important;
        }

        .page-wrapper {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .admin-header {
            background: white;
            padding: 20px;
            margin-bottom: 0;
            border-radius: 4px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .admin-status {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-approved { background: #10b981; color: white; }
        .status-pending { background: #f59e0b; color: #000; }
        .status-rejected { background: #ef4444; color: white; }
        .status-submitted { background: #3b82f6; color: white; }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .form-document {
            background: white !important;
            padding: 40px !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1) !important;
            color: #000 !important;
        }

        .form-document * {
            color: #000 !important;
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
            border: 1px solid #999 !important;
            padding: 8px !important;
            margin-bottom: 12px !important;
            background: #f9f9f9 !important;
            font-size: 10px !important;
            line-height: 1.4 !important;
            color: #000 !important;
        }

        .before-begin * {
            color: #000 !important;
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
            color: #000;
        }

        .line-input:disabled {
            background: white;
            color: #000;
            cursor: not-allowed;
        }

        .line-input:focus {
            outline: none;
            background: #fffacd;
        }

        .line-label {
            font-size: 9px;
            color: #333 !important;
            margin-top: 1px;
        }

        h1, h2, h3, h4, h5, h6, p, label, div, span, strong {
            color: #000 !important;
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

        .checkbox-item input[type="checkbox"]:disabled,
        .checkbox-item input[type="radio"]:disabled {
            cursor: not-allowed;
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
            background: #f9f9f9 !important;
            border: 1px solid #999 !important;
            padding: 8px !important;
            margin: 8px 0 !important;
            font-size: 9px !important;
            line-height: 1.4 !important;
            color: #000 !important;
        }

        .instructions-box * {
            color: #000 !important;
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
            border: 1px solid #999 !important;
            padding: 10px !important;
            margin: 12px 0 !important;
            background: #f9f9f9 !important;
            font-size: 9px !important;
            line-height: 1.4 !important;
            color: #000 !important;
        }

        .certification-box * {
            color: #000 !important;
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

        .sig-input:disabled {
            background: white;
            color: #000;
            cursor: not-allowed;
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

        .btn {
            padding: 11px 16px;
            font-size: 12px;
            font-weight: bold;
            border: none;
            border-radius: 4px;
            cursor: pointer;
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

        .btn-secondary {
            background: #6b7280;
            color: white;
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

        .id-photos-section {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #000;
        }

        .id-photos-section h3 {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .id-photos-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .id-photo-item {
            text-align: center;
        }

        .id-photo-item label {
            display: block;
            font-weight: bold;
            font-size: 10px;
            margin-bottom: 8px;
        }

        .id-photo-item img {
            max-width: 100%;
            max-height: 250px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .admin-info {
            margin-top: 30px !important;
            padding: 15px !important;
            background: #e7f3ff !important;
            border-left: 4px solid #0066cc !important;
            border-radius: 4px !important;
            font-size: 10px !important;
            line-height: 1.5 !important;
            color: #000 !important;
        }

        .admin-info * {
            color: #000 !important;
        }

        .admin-info-row {
            margin-bottom: 8px !important;
            color: #000 !important;
        }

        .admin-info-label {
            font-weight: bold !important;
            color: #0066cc !important;
        }

        @media (max-width: 1024px) {
            .page-wrapper {
                grid-template-columns: 1fr;
            }
        }

        @media print {
            .admin-header { display: none; }
            body { background: white; padding: 0; }
            .form-document { box-shadow: none; }
        }
    </style>
</head>
<body>

<div class="page-wrapper">
    <!-- Admin Header with Status & Actions -->
    <div class="admin-header">
        <div>
            <h2 style="margin: 0 0 8px 0; font-size: 16px;">W-9 Form Submission - Admin View</h2>
            <p style="margin: 0; color: #666; font-size: 12px;">
                @if($w9Form->type === 'affiliate')
                    Affiliate: {{ $w9Form->affiliate?->user->name ?? 'Unknown' }}
                @else
                    Entertainer: {{ $w9Form->entertainer?->user->name ?? 'Unknown' }}
                @endif
            </p>
        </div>
        <div class="admin-status">
            @if($w9Form->status === 'approved')
                <span class="status-badge status-approved">✓ APPROVED</span>
            @elseif($w9Form->status === 'submitted')
                <span class="status-badge status-submitted">⏳ SUBMITTED</span>
            @elseif($w9Form->status === 'rejected')
                <span class="status-badge status-rejected">✗ REJECTED</span>
            @else
                <span class="status-badge status-pending">◯ PENDING</span>
            @endif
            <div class="action-buttons">
                <button onclick="downloadW9PDF({{ $w9Form->id }})" class="btn btn-primary">
                    📥 Download PDF
                </button>
                <button onclick="window.print()" class="btn btn-secondary">
                    🖨️ Print
                </button>
            </div>
        </div>
    </div>

    <!-- Main W-9 Form -->
    <div class="form-document">

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
            <input type="text" class="line-input" disabled value="{{ $w9Form->full_name ?? '' }}">
            <div class="line-label">Name of entity/individual</div>
        </div>
    </div>

    <!-- Line 2: Business Name -->
    <div class="form-line">
        <div class="line-number">2</div>
        <div class="line-content">
            <input type="text" class="line-input" disabled value="{{ $w9Form->business_name ?? '' }}">
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
                    <input type="checkbox" disabled {{ $w9Form->tax_classification === 'individual' ? 'checked' : '' }}>
                    <label>Individual/sole proprietor</label>
                </div>
                <div class="checkbox-item">
                    <input type="checkbox" disabled {{ $w9Form->tax_classification === 'c_corporation' ? 'checked' : '' }}>
                    <label>C corporation</label>
                </div>
                <div class="checkbox-item">
                    <input type="checkbox" disabled {{ $w9Form->tax_classification === 's_corporation' ? 'checked' : '' }}>
                    <label>S corporation</label>
                </div>
            </div>

            <div class="checkbox-group">
                <div class="checkbox-item">
                    <input type="checkbox" disabled {{ $w9Form->tax_classification === 'partnership' ? 'checked' : '' }}>
                    <label>Partnership</label>
                </div>
                <div class="checkbox-item">
                    <input type="checkbox" disabled {{ $w9Form->tax_classification === 'trust_estate' ? 'checked' : '' }}>
                    <label>Trust/estate</label>
                </div>
            </div>

            <div style="margin-top: 6px;">
                <div class="checkbox-item">
                    <input type="checkbox" disabled {{ strpos($w9Form->tax_classification ?? '', 'limited_liability_company') === 0 ? 'checked' : '' }}>
                    <label><strong>LLC.</strong> Enter the tax classification (C = C corporation, S = S corporation, P = Partnership)</label>
                </div>
                @php
                    $llcCode = '';
                    if (strpos($w9Form->tax_classification ?? '', 'limited_liability_company') === 0) {
                        $parts = explode('_', $w9Form->tax_classification);
                        $llcCode = end($parts);
                    }
                @endphp
                <input type="text" disabled value="{{ $llcCode }}" style="border: none; border-bottom: 1px solid #000; width: 35px; padding: 1px; font-size: 10px; margin-left: 17px;" maxlength="1">
            </div>

            <div style="margin-left: 17px; margin-top: 4px; font-size: 8px; line-height: 1.3; color: #333;">
                <strong>Note:</strong> Check the "LLC" box above and enter the appropriate code for the tax classification of the LLC, unless it is a disregarded entity. A disregarded entity should instead check the appropriate box for the tax classification of its owner.
            </div>

            <div class="checkbox-item" style="margin-left: 17px; margin-top: 4px;">
                <input type="checkbox" disabled {{ $w9Form->tax_classification === 'other' ? 'checked' : '' }}>
                <label>Other</label>
            </div>
        </div>
    </div>

    <!-- Line 3b: Foreign Partners -->
    <div class="form-line">
        <div class="line-number">3b</div>
        <div class="line-content">
            <div style="font-size: 9px; line-height: 1.4; margin-bottom: 6px;">If applicable, check this box if you have foreign partners, owners, or beneficiaries.</div>
            <div class="checkbox-item">
                <input type="checkbox" disabled>
                <label></label>
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
                    <input type="text" class="line-input" disabled value="{{ $w9Form->exempt_payee_code ?? '' }}" maxlength="2">
                    <div class="line-label">Exempt payee code (if any)</div>
                </div>
                <div>
                    <input type="text" class="line-input" disabled value="{{ $w9Form->fatca_exemption_code ?? '' }}" maxlength="2">
                    <div class="line-label">Exemption from FATCA reporting code (if any)</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Line 5: Address -->
    <div class="form-line">
        <div class="line-number">5</div>
        <div class="line-content">
            <input type="text" class="line-input" disabled value="{{ $w9Form->street_address ?? '' }}">
            <div class="line-label">Address (number, street, apartment, or suite)</div>
        </div>
    </div>

    <!-- Line 6: City, State, ZIP -->
    <div class="form-line">
        <div class="line-number">6</div>
        <div class="line-content">
            @php
                $cityStateZip = '';
                if ($w9Form->city) $cityStateZip .= $w9Form->city;
                if ($w9Form->state) $cityStateZip .= ($cityStateZip ? ', ' : '') . $w9Form->state;
                if ($w9Form->zip_code) $cityStateZip .= ($cityStateZip ? ' ' : '') . $w9Form->zip_code;
            @endphp
            <input type="text" class="line-input" disabled value="{{ $cityStateZip }}">
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
                <input type="radio" disabled {{ $w9Form->tax_id_type === 'ssn' ? 'checked' : '' }}>
                <label>Social security number</label>
            </div>
            @php
                $tinParts = ['', '', ''];
                if ($w9Form->tax_id_type === 'ssn' && $w9Form->tax_id_number) {
                    $cleaned = preg_replace('/[^0-9]/', '', $w9Form->tax_id_number);
                    if (strlen($cleaned) >= 9) {
                        $tinParts = [
                            substr($cleaned, 0, 3),
                            substr($cleaned, 3, 2),
                            substr($cleaned, 5, 4)
                        ];
                    }
                }
            @endphp
            <div style="display: flex; gap: 8px; align-items: center; margin-top: 6px;">
                <input type="text" disabled value="{{ $tinParts[0] }}" style="width: 60px; padding: 8px; border: 1px solid #ccc; border-radius: 3px; font-size: 12px; text-align: center; background: white; color: #000;">
                <div style="font-weight: bold; font-size: 14px;">–</div>
                <input type="text" disabled value="{{ $tinParts[1] }}" style="width: 50px; padding: 8px; border: 1px solid #ccc; border-radius: 3px; font-size: 12px; text-align: center; background: white; color: #000;">
                <div style="font-weight: bold; font-size: 14px;">–</div>
                <input type="text" disabled value="{{ $tinParts[2] }}" style="width: 70px; padding: 8px; border: 1px solid #ccc; border-radius: 3px; font-size: 12px; text-align: center; background: white; color: #000;">
            </div>
        </div>

        <!-- EIN -->
        <div class="tin-option">
            <div class="checkbox-item">
                <input type="radio" disabled {{ $w9Form->tax_id_type === 'ein' ? 'checked' : '' }}>
                <label>Employer identification number</label>
            </div>
            @php
                $einParts = ['', ''];
                if ($w9Form->tax_id_type === 'ein' && $w9Form->tax_id_number) {
                    $cleaned = preg_replace('/[^0-9]/', '', $w9Form->tax_id_number);
                    if (strlen($cleaned) >= 9) {
                        $einParts = [
                            substr($cleaned, 0, 2),
                            substr($cleaned, 2, 7)
                        ];
                    }
                }
            @endphp
            <div style="display: flex; gap: 8px; align-items: center; margin-top: 6px;">
                <input type="text" disabled value="{{ $einParts[0] }}" style="width: 60px; padding: 8px; border: 1px solid #ccc; border-radius: 3px; font-size: 12px; text-align: center; background: white; color: #000;">
                <div style="font-weight: bold; font-size: 14px;">–</div>
                <input type="text" disabled value="{{ $einParts[1] }}" style="width: 100px; padding: 8px; border: 1px solid #ccc; border-radius: 3px; font-size: 12px; text-align: center; background: white; color: #000;">
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
                <input type="checkbox" disabled {{ $w9Form->certification_signed ? 'checked' : '' }}>
                <label>☐ I acknowledge and agree to the certifications contained in Part II above.</label>
            </div>
            <small style="display: block; margin-top: 6px; color: #666;">If you cannot certify U.S. person status, you may need to complete Form W-8 instead.</small>
        </div>

        <!-- Signature Section -->
        <div style="margin-top: 15px; padding-top: 12px; border-top: 1px solid #999;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 15px;">
                <div>
                    <div class="sig-label">Signature Method</div>
                    <div style="margin-top: 8px;">
                        <div class="checkbox-item">
                            <input type="radio" disabled>
                            <label>Type Legal Name</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="radio" disabled>
                            <label>Draw Signature</label>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="sig-label">Date</div>
                    <input type="text" class="sig-input" disabled value="{{ $w9Form->certification_date ? $w9Form->certification_date->format('m/d/Y') : '' }}" style="margin-top: 6px;">
                </div>
            </div>

            <!-- Signature Display -->
            @php
                $pdfData = $w9Form->pdf_form_data ? json_decode($w9Form->pdf_form_data, true) : [];
                $signatureMethod = $pdfData['signature_type'] ?? 'typed';
                $signatureTyped = $pdfData['signature_typed'] ?? '';
            @endphp

            <div style="margin-bottom: 15px;">
                <div class="sig-label">Signature</div>
                <div class="sig-area">
                    @if($signatureMethod === 'typed' && $signatureTyped)
                        <div style="font-size: 14px; color: #000; padding: 10px; font-style: italic;">{{ $signatureTyped }}</div>
                    @else
                        <div style="font-size: 11px; color: #666; padding: 10px;">
                            [Signature recorded digitally on {{ $w9Form->certification_date?->format('M d, Y') ?? 'N/A' }}]
                        </div>
                    @endif
                </div>
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
            <strong style="display: block; margin-bottom: 8px; color: #0f172a;">Privacy Notice</strong>
            <p style="margin: 0;">Information collected through this Substitute Form W-9 is used solely for tax reporting, contractor onboarding, and compliance purposes. Access to taxpayer information is restricted to authorized personnel only.</p>
        </div>

        <!-- Government ID Photos Section -->
        @if($w9Form->id_front_image || $w9Form->id_back_image)
        <div class="id-photos-section">
            <h3>Government-Issued ID Verification</h3>
            <p style="font-size: 10px; color: #666; margin-bottom: 12px;">
                <strong>ID Type:</strong> {{ ucwords(str_replace('_', ' ', $w9Form->id_document_type ?? 'Not specified')) }}
            </p>

            <div class="id-photos-grid">
                @if($w9Form->id_front_image)
                <div class="id-photo-item">
                    <label>Front of ID</label>
                    <img src="{{ asset('storage/' . $w9Form->id_front_image) }}" alt="ID Front">
                </div>
                @endif

                @if($w9Form->id_back_image)
                <div class="id-photo-item">
                    <label>Back of ID</label>
                    <img src="{{ asset('storage/' . $w9Form->id_back_image) }}" alt="ID Back">
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Admin Information -->
        <div class="admin-info">
            <h4 style="margin: 0 0 12px 0; color: #0066cc;">ADMIN INFORMATION (READ-ONLY)</h4>
            <div class="admin-info-row">
                <span class="admin-info-label">Status:</span>
                {{ ucwords(str_replace('_', ' ', $w9Form->status)) }}
            </div>
            <div class="admin-info-row">
                <span class="admin-info-label">Submitted:</span>
                {{ $w9Form->created_at ? $w9Form->created_at->format('M d, Y h:i A') : 'N/A' }}
            </div>
            <div class="admin-info-row">
                <span class="admin-info-label">IP Address:</span>
                {{ $w9Form->certification_ip ?? 'Not recorded' }}
            </div>
            @if($w9Form->reviewed_by && $w9Form->reviewed_at)
            <div class="admin-info-row">
                <span class="admin-info-label">Reviewed By:</span>
                {{ $w9Form->reviewedBy?->name ?? 'System' }} on {{ $w9Form->reviewed_at->format('M d, Y h:i A') }}
            </div>
            @endif
            @if($w9Form->admin_notes)
            <div class="admin-info-row">
                <span class="admin-info-label">Admin Notes:</span>
                {{ $w9Form->admin_notes }}
            </div>
            @endif
        </div>

    </div>
</div>

<script>
function downloadW9PDF(formId) {
    window.location.href = '/admin/w9/' + formId + '/download-pdf';
}
</script>

</body>
</html>
