<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Substitute Form W-9 - Taxpayer Identification & Certification</title>
    <style>
        @page {
            size: letter portrait;
            margin: 7mm 10mm 7mm 10mm;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 8.2pt;
            line-height: 1.25;
            color: #111111;
            background: #ffffff;
        }

        .page {
            width: 100%;
            position: relative;
        }

        .page-break {
            page-break-before: always;
        }

        /* Typography & Header */
        .doc-header {
            text-align: center;
            margin-bottom: 6px;
            padding-bottom: 5px;
            border-bottom: 2px solid #0066cc;
        }

        .doc-title {
            font-size: 13.5pt;
            font-weight: bold;
            color: #0066cc;
            margin-bottom: 2px;
        }

        .doc-subtitle {
            font-size: 9.5pt;
            font-weight: bold;
            color: #222222;
            margin-bottom: 2px;
        }

        .doc-org {
            font-size: 7.5pt;
            color: #555555;
            letter-spacing: 0.5px;
        }

        /* Disclaimer Callout */
        .disclaimer-box {
            background-color: #e7f3ff;
            border-left: 3.5px solid #0066cc;
            padding: 5px 8px;
            margin-bottom: 5px;
            border-radius: 2px;
            font-size: 7pt;
            line-height: 1.3;
            color: #1a202c;
        }

        .disclaimer-box strong {
            color: #004080;
        }

        /* Before Begin / Note */
        .note-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 4px 6px;
            margin-bottom: 5px;
            font-size: 6.8pt;
            line-height: 1.25;
            color: #334155;
            border-radius: 2px;
        }

        /* Form Lines Layout using Float */
        .form-line {
            width: 100%;
            margin-bottom: 4px;
            clear: both;
        }

        .col-num {
            float: left;
            width: 22px;
            font-weight: bold;
            font-size: 8.5pt;
            color: #111111;
            padding-top: 2px;
        }

        .col-content {
            margin-left: 24px;
        }

        .clear {
            clear: both;
            height: 0;
            line-height: 0;
            font-size: 0;
        }

        .field-input-box {
            border: 1px solid #777777;
            background-color: #ffffff;
            padding: 3px 6px;
            min-height: 18px;
            font-size: 8.2pt;
            color: #000000;
            font-weight: 500;
            border-radius: 2px;
        }

        .field-label {
            font-size: 6.5pt;
            color: #555555;
            margin-top: 1px;
            line-height: 1.15;
        }

        /* Checkboxes & Radios */
        .cb-box {
            display: inline-block;
            width: 11px;
            height: 11px;
            border: 1.2px solid #222222;
            text-align: center;
            line-height: 9px;
            font-size: 8pt;
            font-weight: bold;
            color: #000000;
            margin-right: 4px;
            vertical-align: middle;
            background-color: #ffffff;
            font-family: 'DejaVu Sans', sans-serif;
        }

        .rb-circle {
            display: inline-block;
            width: 11px;
            height: 11px;
            border: 1.2px solid #222222;
            border-radius: 50%;
            text-align: center;
            line-height: 8px;
            font-size: 10pt;
            color: #000000;
            margin-right: 4px;
            vertical-align: middle;
            background-color: #ffffff;
            font-family: 'DejaVu Sans', sans-serif;
        }

        .check-table {
            width: 100%;
            border-collapse: collapse;
            margin: 2px 0;
        }

        .check-table td {
            padding: 1px 4px 1px 0;
            font-size: 7.2pt;
            line-height: 1.2;
            vertical-align: middle;
        }

        /* Section Bars */
        .section-bar {
            background-color: #f1f5f9;
            border-top: 1.5px solid #1e293b;
            border-bottom: 1px solid #cbd5e1;
            padding: 3px 6px;
            margin: 5px 0 3px 0;
            font-size: 7.8pt;
            font-weight: bold;
            color: #0f172a;
            clear: both;
        }

        .section-desc {
            font-size: 6.8pt;
            line-height: 1.25;
            color: #334155;
            margin-bottom: 4px;
        }

        /* Part I TIN Box */
        .tin-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2px;
        }

        .tin-table td {
            vertical-align: top;
            padding: 2px 6px;
        }

        .tin-digit-cell {
            border: 1px solid #333333;
            background: #ffffff;
            height: 18px;
            text-align: center;
            font-size: 8.2pt;
            font-weight: bold;
            line-height: 18px;
            color: #000000;
        }

        .tin-dash {
            text-align: center;
            font-size: 8.5pt;
            font-weight: bold;
            line-height: 18px;
            width: 10px;
        }

        /* Part II Certification Box */
        .cert-container {
            border: 1px solid #777777;
            padding: 5px 8px;
            background-color: #ffffff;
            margin-top: 3px;
            clear: both;
        }

        .cert-intro {
            font-size: 7pt;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .cert-list {
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .cert-list li {
            font-size: 6.6pt;
            line-height: 1.25;
            margin-bottom: 2px;
            color: #222222;
        }

        .cert-list li strong {
            display: inline-block;
            width: 12px;
        }

        .cert-divider {
            border-top: 0.8px solid #cccccc;
            margin: 4px 0;
        }

        .cert-ack-box {
            background-color: #f0fdf4;
            border-left: 3px solid #16a34a;
            padding: 4px 8px;
            margin: 3px 0 5px 0;
            font-size: 6.8pt;
            line-height: 1.25;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 3px;
        }

        .signature-table td {
            vertical-align: top;
            padding: 2px 4px;
        }

        .sig-box {
            border: 1px solid #777777;
            background-color: #fafafa;
            min-height: 30px;
            padding: 3px 8px;
            border-radius: 2px;
        }

        .typed-signature {
            font-family: 'Brush Script MT', 'Apple Chancery', 'Segoe Script', cursive, 'Times New Roman';
            font-size: 14pt;
            font-style: italic;
            color: #0f2744;
            line-height: 1.2;
        }

        /* Page 2 Elements */
        .page2-header {
            border-bottom: 2px solid #0066cc;
            padding-bottom: 6px;
            margin-bottom: 12px;
        }

        .page2-header h2 {
            font-size: 12pt;
            color: #0066cc;
            margin: 0;
        }

        .id-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        .id-grid td {
            width: 50%;
            vertical-align: top;
            padding: 0 6px;
        }

        .id-card {
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 8px;
            background: #f8fafc;
            text-align: center;
        }

        .id-card-title {
            font-weight: bold;
            font-size: 8pt;
            margin-bottom: 6px;
            color: #1e293b;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 3px;
        }

        .id-image-frame {
            background: #ffffff;
            border: 1px dashed #94a3b8;
            padding: 6px;
            min-height: 160px;
            text-align: center;
        }

        .id-image-frame img {
            max-width: 280px;
            max-height: 150px;
            display: inline-block;
        }

        .admin-audit-card {
            background: #f0f7ff;
            border: 1px solid #bfdbfe;
            border-radius: 4px;
            padding: 8px 12px;
            margin-bottom: 12px;
        }

        .admin-audit-title {
            color: #1d4ed8;
            font-weight: bold;
            font-size: 8pt;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .audit-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5pt;
        }

        .audit-table td {
            padding: 2px 4px;
        }

        .audit-label {
            font-weight: bold;
            color: #475569;
            width: 120px;
        }

        .badge-pill {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-weight: bold;
            font-size: 7pt;
            text-transform: uppercase;
        }

        .badge-approved { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
        .badge-submitted { background: #dbeafe; color: #1e40af; border: 1px solid #93c5fd; }
        .badge-pending { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        .badge-rejected { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }

        .info-reference-box {
            border: 1px solid #e2e8f0;
            background: #fafafa;
            border-radius: 4px;
            padding: 8px 12px;
            font-size: 7pt;
            line-height: 1.4;
            color: #475569;
        }

        .info-reference-box ul {
            margin-left: 14px;
            margin-top: 3px;
        }
    </style>
</head>
<body>

@php
    $pdfData = $w9Form->pdf_form_data ? (is_array($w9Form->pdf_form_data) ? $w9Form->pdf_form_data : json_decode($w9Form->pdf_form_data, true)) : [];
    if (!is_array($pdfData)) {
        $pdfData = [];
    }

    // 1. Legal Name
    $fullName = $w9Form->full_name ?: ($pdfData['line1_name'] ?? ($pdfData['manual_full_name'] ?? ''));

    // 2. Business Name
    $businessName = $w9Form->business_name ?: ($pdfData['line2_business'] ?? '');

    // 3a. Tax Classification
    $taxClassification = $w9Form->tax_classification;
    if (!$taxClassification && !empty($pdfData['line3a_tax'])) {
        $taxClassification = is_array($pdfData['line3a_tax']) ? ($pdfData['line3a_tax'][0] ?? '') : $pdfData['line3a_tax'];
    }

    // LLC code
    $llcCode = '';
    if (strpos($taxClassification ?? '', 'limited_liability_company') === 0) {
        $parts = explode('_', $taxClassification);
        $llcCode = strtoupper(end($parts));
        if ($llcCode === 'INDIVIDUAL') $llcCode = 'P';
    } elseif (!empty($pdfData['llc_code'])) {
        $llcCode = strtoupper($pdfData['llc_code']);
    }

    // 3b. Foreign partners
    $hasForeignPartners = !empty($pdfData['line3b']);

    // 4. Exemptions
    $exemptPayeeCode = $w9Form->exempt_payee_code ?: ($pdfData['line4_exempt'] ?? '');
    $fatcaExemptionCode = $w9Form->fatca_exemption_code ?: ($pdfData['line4_fatca'] ?? '');

    // 5. Street Address
    $streetAddress = $w9Form->street_address ?: ($pdfData['line5_address'] ?? '');

    // 6. City, State, ZIP
    $cityStateZip = '';
    if ($w9Form->city || $w9Form->state || $w9Form->zip_code) {
        if ($w9Form->city) $cityStateZip .= $w9Form->city;
        if ($w9Form->state) $cityStateZip .= ($cityStateZip ? ', ' : '') . $w9Form->state;
        if ($w9Form->zip_code) $cityStateZip .= ($cityStateZip ? ' ' : '') . $w9Form->zip_code;
    }
    if (!$cityStateZip && !empty($pdfData['line6_city_state_zip'])) {
        $cityStateZip = $pdfData['line6_city_state_zip'];
    }

    // Part I. TIN
    $taxIdType = $w9Form->tax_id_type ?: ($pdfData['tin_type'] ?? 'ssn');
    $taxIdNumber = $w9Form->tax_id_number ?: ($pdfData['tin_number'] ?? ($pdfData['manual_tax_id'] ?? ''));

    if (!$taxIdType && $taxIdNumber) {
        $cleanNum = preg_replace('/[^0-9]/', '', $taxIdNumber);
        if (strlen($cleanNum) === 9) {
            if (in_array($taxClassification, ['c_corporation', 's_corporation', 'partnership'])) {
                $taxIdType = 'ein';
            } else {
                $taxIdType = 'ssn';
            }
        }
    }
    if (!$taxIdType) {
        $taxIdType = 'ssn';
    }

    $tinParts = ['', '', ''];
    $einParts = ['', ''];
    $cleanedTin = preg_replace('/[^0-9]/', '', $taxIdNumber);

    if ($taxIdType === 'ssn') {
        if (strlen($cleanedTin) >= 9) {
            $tinParts = [
                substr($cleanedTin, 0, 3),
                substr($cleanedTin, 3, 2),
                substr($cleanedTin, 5, 4)
            ];
        } elseif (strlen($cleanedTin) > 0) {
            $tinParts[0] = $cleanedTin;
        }
    } elseif ($taxIdType === 'ein') {
        if (strlen($cleanedTin) >= 9) {
            $einParts = [
                substr($cleanedTin, 0, 2),
                substr($cleanedTin, 2, 7)
            ];
        } elseif (strlen($cleanedTin) > 0) {
            $einParts[0] = $cleanedTin;
        }
    }

    // Signature details
    $signatureMethod = $pdfData['signature_method'] ?? $pdfData['signature_type'] ?? 'typed';
    $signatureTyped = $pdfData['signature'] ?? $pdfData['signature_typed'] ?? ($w9Form->full_name ?: '');
    $signatureImage = $pdfData['signature_image'] ?? ($pdfData['signature'] ?? '');
    $isDrawnSignature = (!empty($signatureImage) && strpos($signatureImage, 'data:image') === 0) || $signatureMethod === 'draw';
@endphp

<!-- ======================== PAGE 1: SUBSTITUTE FORM W-9 ======================== -->
<div class="page">
    <!-- Header -->
    <div class="doc-header">
        <div class="doc-title">Substitute Form W-9</div>
        <div class="doc-subtitle">Taxpayer Identification & Certification</div>
        <div class="doc-org">CartVIP Onboarding</div>
    </div>

    <!-- Disclaimer -->
    <div class="disclaimer-box">
        <strong>Disclaimer:</strong> This Substitute Form W-9 is used by CartVIP to collect taxpayer identification, certification, and payment information as part of the CartVIP onboarding process. This information may be used for tax reporting, compliance, and payment processing purposes. This is not the official IRS Form W-9. For official IRS instructions and the current Form W-9, visit <strong>IRS.gov/FormW9</strong>.
    </div>

    <!-- Line 1 Note -->
    <div class="note-box">
        <strong>Line 1 — Legal Name:</strong> An entry is required. For a sole proprietor or disregarded entity, enter the owner's name on line 1 and the business/disregarded entity's name on line 2.
    </div>

    <!-- Line 1 -->
    <div class="form-line">
        <div class="col-num">1</div>
        <div class="col-content">
            <div class="field-input-box">{{ $fullName }}</div>
            <div class="field-label">Name of entity/individual</div>
        </div>
        <div class="clear"></div>
    </div>

    <!-- Line 2 -->
    <div class="form-line">
        <div class="col-num">2</div>
        <div class="col-content">
            <div class="field-input-box">{{ $businessName }}</div>
            <div class="field-label">Business name/disregarded entity name, if different from above.</div>
        </div>
        <div class="clear"></div>
    </div>

    <!-- Line 3a -->
    <div class="form-line">
        <div class="col-num">3a</div>
        <div class="col-content">
            <div style="font-size: 7pt; margin-bottom: 2px; line-height: 1.25; color: #111;">
                Check the appropriate box for federal tax classification of the entity/individual whose name is entered on line 1. Check only one of the following seven boxes.
            </div>

            <table class="check-table">
                <tr>
                    <td style="width: 38%;">
                        <span class="cb-box">{!! $taxClassification === 'individual' ? '&#10003;' : '&nbsp;' !!}</span> Individual/sole proprietor
                    </td>
                    <td style="width: 31%;">
                        <span class="cb-box">{!! $taxClassification === 'c_corporation' ? '&#10003;' : '&nbsp;' !!}</span> C corporation
                    </td>
                    <td style="width: 31%;">
                        <span class="cb-box">{!! $taxClassification === 's_corporation' ? '&#10003;' : '&nbsp;' !!}</span> S corporation
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="cb-box">{!! $taxClassification === 'partnership' ? '&#10003;' : '&nbsp;' !!}</span> Partnership
                    </td>
                    <td colspan="2">
                        <span class="cb-box">{!! $taxClassification === 'trust_estate' ? '&#10003;' : '&nbsp;' !!}</span> Trust/estate
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style="padding-top: 1px;">
                        @php
                            $isLlc = (strpos($taxClassification ?? '', 'limited_liability_company') === 0 || $taxClassification === 'llc');
                        @endphp
                        <span class="cb-box">{!! $isLlc ? '&#10003;' : '&nbsp;' !!}</span>
                        <strong>LLC.</strong> Enter the tax classification (C = C corporation, S = S corporation, P = Partnership)
                        <span style="border-bottom: 1px solid #111; padding: 0 8px; font-weight: bold; font-size: 8pt;">{{ $llcCode ?: ' ' }}</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style="font-size: 6.3pt; color: #475569; padding-left: 16px; line-height: 1.15;">
                        Note: Check the "LLC" box above and enter the appropriate code for the tax classification of the LLC, unless it is a disregarded entity. A disregarded entity should instead check the appropriate box for the tax classification of its owner.
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style="padding-left: 16px; padding-top: 1px;">
                        <span class="cb-box">{!! $taxClassification === 'other' ? '&#10003;' : '&nbsp;' !!}</span> Other
                    </td>
                </tr>
            </table>
        </div>
        <div class="clear"></div>
    </div>

    <!-- Line 3b -->
    <div class="form-line">
        <div class="col-num">3b</div>
        <div class="col-content">
            <div style="font-size: 7pt; margin-bottom: 2px; line-height: 1.2;">
                If applicable, check this box if you have foreign partners, owners, or beneficiaries.
            </div>
            <div>
                <span class="cb-box">{!! $hasForeignPartners ? '&#10003;' : '&nbsp;' !!}</span>
            </div>
        </div>
        <div class="clear"></div>
    </div>

    <!-- Line 4 -->
    <div class="form-line">
        <div class="col-num">4</div>
        <div class="col-content">
            <div style="font-size: 7pt; margin-bottom: 2px;">
                <strong>Exemptions:</strong> Exemptions apply only to certain entities. See IRS Form W-9 instructions for details.
            </div>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 50%; padding-right: 8px; vertical-align: top;">
                        <div class="field-input-box">{{ $exemptPayeeCode }}</div>
                        <div class="field-label">Exempt payee code (if any)</div>
                    </td>
                    <td style="width: 50%; padding-left: 8px; vertical-align: top;">
                        <div class="field-input-box">{{ $fatcaExemptionCode }}</div>
                        <div class="field-label">Exemption from FATCA reporting code (if any)</div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="clear"></div>
    </div>

    <!-- Line 5 -->
    <div class="form-line">
        <div class="col-num">5</div>
        <div class="col-content">
            <div class="field-input-box">{{ $streetAddress }}</div>
            <div class="field-label">Address (number, street, apartment, or suite)</div>
        </div>
        <div class="clear"></div>
    </div>

    <!-- Line 6 -->
    <div class="form-line">
        <div class="col-num">6</div>
        <div class="col-content">
            <div class="field-input-box">{{ $cityStateZip }}</div>
            <div class="field-label">City, state, and ZIP code</div>
        </div>
        <div class="clear"></div>
    </div>

    <!-- Part I -->
    <div class="section-bar">
        Part I &mdash; Taxpayer Identification Number (TIN)
    </div>

    <div class="section-desc">
        Enter your TIN in the appropriate box. The TIN provided must match the name given on line 1. For individuals, this is generally your social security number (SSN). For other entities, it is your employer identification number (EIN).
    </div>

    <table class="tin-table">
        <tr>
            <!-- SSN Column -->
            <td style="width: 50%; padding-right: 12px; border-right: 1px dashed #cbd5e1;">
                <div style="font-size: 7.2pt; font-weight: bold; margin-bottom: 3px;">
                    <span class="rb-circle">{!! $taxIdType === 'ssn' ? '&bull;' : '&nbsp;' !!}</span>
                    Social security number
                </div>
                <table style="border-collapse: collapse; margin-top: 1px;">
                    <tr>
                        <td class="tin-digit-cell" style="width: 42px;">{{ $taxIdType === 'ssn' ? ($tinParts[0] ?? '') : '' }}</td>
                        <td class="tin-dash">-</td>
                        <td class="tin-digit-cell" style="width: 32px;">{{ $taxIdType === 'ssn' ? ($tinParts[1] ?? '') : '' }}</td>
                        <td class="tin-dash">-</td>
                        <td class="tin-digit-cell" style="width: 52px;">{{ $taxIdType === 'ssn' ? ($tinParts[2] ?? '') : '' }}</td>
                    </tr>
                </table>
            </td>

            <!-- EIN Column -->
            <td style="width: 50%; padding-left: 12px;">
                <div style="font-size: 7.2pt; font-weight: bold; margin-bottom: 3px;">
                    <span class="rb-circle">{!! $taxIdType === 'ein' ? '&bull;' : '&nbsp;' !!}</span>
                    Employer identification number
                </div>
                <table style="border-collapse: collapse; margin-top: 1px;">
                    <tr>
                        <td class="tin-digit-cell" style="width: 36px;">{{ $taxIdType === 'ein' ? ($einParts[0] ?? '') : '' }}</td>
                        <td class="tin-dash">-</td>
                        <td class="tin-digit-cell" style="width: 86px;">{{ $taxIdType === 'ein' ? ($einParts[1] ?? '') : '' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Part II -->
    <div class="section-bar">
        Part II &mdash; Certification
    </div>

    <div class="cert-container">
        <div class="cert-intro">Under penalties of perjury, I certify that:</div>
        <ul class="cert-list">
            <li><strong>1.</strong> The number shown on this form is my correct taxpayer identification number (or I am waiting for a number to be issued to me); and</li>
            <li><strong>2.</strong> I am not subject to backup withholding because (a) I am exempt from backup withholding, or (b) I have not been notified by the Internal Revenue Service (IRS) that I am subject to backup withholding as a result of a failure to report all interest or dividends, or (c) the IRS has notified me that I am no longer subject to backup withholding; and</li>
            <li><strong>3.</strong> I am a U.S. citizen or other U.S. person; and</li>
            <li><strong>4.</strong> The FATCA code(s) entered on this form (if any) indicating that I am exempt from FATCA reporting is correct.</li>
        </ul>

        <div class="cert-divider"></div>

        <div style="font-size: 6.5pt; line-height: 1.25; color: #1e293b;">
            <strong>Electronic Signature Certification:</strong> By typing my legal name or drawing my signature below, I electronically sign this Substitute Form W-9. I understand that my electronic signature has the same legal effect as a handwritten signature.
        </div>

        <div class="cert-ack-box">
            <span class="cb-box">{!! $w9Form->certification_signed ? '&#10003;' : '&nbsp;' !!}</span>
            <strong>I certify and agree to the statements contained in Part II above.</strong>
            <div style="font-size: 6.2pt; color: #4b5563; margin-top: 1px; padding-left: 15px;">
                If you cannot certify U.S. person status, you may need to complete Form W-8 instead.
            </div>
        </div>

        <table class="signature-table">
            <tr>
                <td style="width: 50%;">
                    <div style="font-size: 7pt; font-weight: bold; margin-bottom: 2px;">Signature Method</div>
                    <div style="font-size: 7pt;">
                        <span class="rb-circle">{!! $signatureMethod === 'typed' ? '&bull;' : '&nbsp;' !!}</span> Type Legal Name
                        &nbsp;&nbsp;
                        <span class="rb-circle">{!! $signatureMethod === 'draw' ? '&bull;' : '&nbsp;' !!}</span> Draw Signature
                    </div>
                </td>
                <td style="width: 50%;">
                    <div style="font-size: 7pt; font-weight: bold; margin-bottom: 2px;">Date</div>
                    <div class="field-input-box" style="padding: 2px 6px;">
                        {{ $w9Form->certification_date ? $w9Form->certification_date->format('m/d/Y') : date('m/d/Y') }}
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding-top: 3px;">
                    <div style="font-size: 7pt; font-weight: bold; margin-bottom: 2px;">Signature</div>
                    <div class="sig-box">
                        @if($isDrawnSignature && $signatureImage)
                            <img src="{{ $signatureImage }}" alt="Signature" style="max-height: 32px; max-width: 240px; display: block;">
                        @elseif($signatureTyped)
                            <div class="typed-signature">{{ $signatureTyped }}</div>
                        @else
                            <div style="font-size: 7pt; color: #94a3b8; padding-top: 5px;">[Electronically Certified via CartVIP Onboarding]</div>
                        @endif
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>

<!-- ======================== PAGE 2: VERIFICATION & AUDIT RECORDS ======================== -->
<div class="page page-break">
    <div class="page2-header">
        <h2>Government-Issued ID & Submission Audit Records</h2>
        <div style="font-size: 7.5pt; color: #64748b; margin-top: 2px;">Substitute Form W-9 Verification Attachment</div>
    </div>

    <!-- Government ID Photos Section -->
    <table class="id-grid">
        <tr>
            <td colspan="2" style="width: 100%; padding: 0 0 6px 0;">
                <div style="font-size: 7.8pt; color: #334155; margin-bottom: 4px;">
                    <strong>ID Document Type:</strong> {{ ucwords(str_replace('_', ' ', $w9Form->id_document_type ?? 'State ID')) }}
                </div>
            </td>
        </tr>
        <tr>
            <!-- Front of ID -->
            <td>
                <div class="id-card">
                    <div class="id-card-title">Front of Government ID</div>
                    <div class="id-image-frame">
                        @if(!empty($idFrontBase64))
                            <img src="{{ $idFrontBase64 }}" alt="ID Front">
                        @else
                            <div style="font-size: 7.5pt; color: #94a3b8; padding-top: 60px;">
                                <em>[No Front ID image uploaded or recorded]</em>
                            </div>
                        @endif
                    </div>
                </div>
            </td>

            <!-- Back of ID -->
            <td>
                <div class="id-card">
                    <div class="id-card-title">Back of Government ID</div>
                    <div class="id-image-frame">
                        @if(!empty($idBackBase64))
                            <img src="{{ $idBackBase64 }}" alt="ID Back">
                        @else
                            <div style="font-size: 7.5pt; color: #94a3b8; padding-top: 60px;">
                                <em>[No Back ID image uploaded or recorded]</em>
                            </div>
                        @endif
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Admin Audit Information -->
    <div class="admin-audit-card">
        <div class="admin-audit-title">Administrative Submission Record</div>
        <table class="audit-table">
            <tr>
                <td class="audit-label">Status:</td>
                <td>
                    @php
                        $st = strtolower($w9Form->status ?? 'submitted');
                        $badgeClass = 'badge-submitted';
                        if ($st === 'approved') $badgeClass = 'badge-approved';
                        elseif ($st === 'rejected') $badgeClass = 'badge-rejected';
                        elseif ($st === 'pending') $badgeClass = 'badge-pending';
                    @endphp
                    <span class="badge-pill {{ $badgeClass }}">{{ ucfirst($w9Form->status ?? 'Submitted') }}</span>
                </td>
                <td class="audit-label">Submission Date:</td>
                <td>{{ $w9Form->created_at ? $w9Form->created_at->format('M d, Y h:i A') : now()->format('M d, Y h:i A') }}</td>
            </tr>
            <tr>
                <td class="audit-label">Certification IP:</td>
                <td><code>{{ $w9Form->certification_ip ?: 'Not recorded' }}</code></td>
                <td class="audit-label">Review Date:</td>
                <td>{{ $w9Form->reviewed_at ? $w9Form->reviewed_at->format('M d, Y h:i A') : 'Pending Review' }}</td>
            </tr>
            @if($w9Form->reviewedBy)
            <tr>
                <td class="audit-label">Reviewed By:</td>
                <td colspan="3">{{ $w9Form->reviewedBy->name }} ({{ $w9Form->reviewedBy->email }})</td>
            </tr>
            @endif
            @if($w9Form->admin_notes)
            <tr>
                <td class="audit-label">Admin Notes:</td>
                <td colspan="3">{{ $w9Form->admin_notes }}</td>
            </tr>
            @endif
        </table>
    </div>

    <!-- Instructions Reference & Privacy Notice -->
    <div class="info-reference-box">
        <div style="font-weight: bold; color: #0f172a; margin-bottom: 3px; font-size: 7.5pt;">Need Help / Official IRS Instructions</div>
        <p style="margin-bottom: 4px;">
            For detailed line-by-line instructions on completing Form W-9, consult official IRS documentation at <strong>IRS.gov/FormW9</strong>. Official resources cover line-by-line requirements, taxpayer identification types (SSN, EIN, ITIN), backup withholding regulations, FATCA reporting codes, and penalties for failure to furnish accurate TIN information.
        </p>

        <div style="border-top: 1px dashed #cbd5e1; margin: 6px 0;"></div>

        <div style="font-weight: bold; color: #0f172a; margin-bottom: 2px; font-size: 7.5pt;">Privacy Notice & Data Protection</div>
        <p style="margin: 0;">
            Information collected through this Substitute Form W-9 onboarding process is utilized exclusively for identity verification, payment processing, fraud prevention, and federal/state tax reporting compliance. Access to taxpayer identification information and government ID records is strictly restricted to authorized compliance personnel.
        </p>
    </div>
</div>

</body>
</html>
