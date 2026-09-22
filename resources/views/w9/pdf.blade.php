<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Substitute Form W-9 - Taxpayer Identification & Certification</title>
    <style>
        @page {
            size: letter portrait;
            margin: 10mm 18mm 10mm 18mm;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8pt;
            line-height: 1.25;
            color: #000000;
            background: #ffffff;
        }

        /* Proportional Document Layout matching the Admin Modal */
        .document-wrapper {
            width: 100%;
            margin: 0;
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
            font-size: 14pt;
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
            color: #666666;
            margin-bottom: 2px;
        }

        /* Disclaimer Callout */
        .disclaimer-box {
            background-color: #e7f3ff;
            border-left: 3.5px solid #0066cc;
            padding: 5px 8px;
            margin-bottom: 5px;
            border-radius: 3px;
            font-size: 6.8pt;
            line-height: 1.3;
            color: #1a202c;
        }

        .disclaimer-box strong {
            color: #004080;
        }

        /* Before Begin / Note */
        .note-box {
            background-color: #f9f9f9;
            border: 1px solid #cccccc;
            padding: 4px 6px;
            margin-bottom: 5px;
            font-size: 6.8pt;
            line-height: 1.25;
            color: #222222;
            border-radius: 2px;
        }

        /* Form Lines - Underline styling like the modal */
        .form-line {
            width: 100%;
            margin-bottom: 4px;
            clear: both;
        }

        .line-number {
            float: left;
            width: 20px;
            font-weight: bold;
            font-size: 8.5pt;
            color: #000000;
            padding-top: 1px;
        }

        .line-content {
            margin-left: 22px;
        }

        .clear {
            clear: both;
            height: 0;
            line-height: 0;
            font-size: 0;
        }

        .line-input-underline {
            border: none;
            border-bottom: 1px solid #000000;
            padding: 1px 3px;
            font-size: 8.5pt;
            font-weight: bold;
            color: #000000;
            min-height: 16px;
        }

        .line-label {
            font-size: 6.5pt;
            color: #444444;
            margin-top: 1px;
            line-height: 1.15;
        }

        /* Checkboxes & Radios */
        .cb-box {
            display: inline-block;
            width: 11px;
            height: 11px;
            border: 1.2px solid #000000;
            text-align: center;
            line-height: 10px;
            vertical-align: middle;
            background-color: #ffffff;
            margin-right: 4px;
        }

        .cb-box img {
            width: 8px;
            height: 8px;
            vertical-align: top;
            margin-top: 1px;
            display: inline-block;
        }

        .rb-circle {
            display: inline-block;
            width: 11px;
            height: 11px;
            border: 1.2px solid #000000;
            border-radius: 50%;
            text-align: center;
            line-height: 10px;
            vertical-align: middle;
            background-color: #ffffff;
            margin-right: 4px;
        }

        .rb-circle img {
            width: 7px;
            height: 7px;
            vertical-align: top;
            margin-top: 2px;
            display: inline-block;
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
            border-top: 1.5px solid #111111;
            border-bottom: 1px solid #cccccc;
            padding: 2.5px 6px;
            margin: 4px 0 2px 0;
            font-size: 7.8pt;
            font-weight: bold;
            color: #000000;
            clear: both;
        }

        .section-desc {
            font-size: 6.6pt;
            line-height: 1.2;
            color: #333333;
            margin-bottom: 3px;
        }

        /* Part I TIN Box */
        .tin-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1px;
            margin-bottom: 3px;
        }

        .tin-table td {
            vertical-align: top;
            padding: 1px 6px;
        }

        .tin-digit-cell {
            border: 1px solid #000000;
            background: #ffffff;
            height: 18px;
            text-align: center;
            font-size: 8.5pt;
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
            border: 1px solid #888888;
            padding: 4px 6px;
            background-color: #ffffff;
            margin-top: 2px;
            clear: both;
        }

        .cert-intro {
            font-size: 6.8pt;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .cert-list {
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .cert-list li {
            font-size: 6.3pt;
            line-height: 1.2;
            margin-bottom: 1.5px;
            color: #111111;
        }

        .cert-list li strong {
            display: inline-block;
            width: 11px;
        }

        .cert-divider {
            border-top: 0.8px solid #cccccc;
            margin: 3px 0;
        }

        .cert-ack-box {
            background-color: #f0fdf4;
            border-left: 3px solid #16a34a;
            padding: 3px 6px;
            margin: 2px 0 4px 0;
            font-size: 6.6pt;
            line-height: 1.2;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2px;
        }

        .signature-table td {
            vertical-align: top;
            padding: 1px 4px;
        }

        .sig-box {
            border-bottom: 1px solid #000000;
            background-color: #ffffff;
            min-height: 24px;
            padding: 2px 4px;
        }

        .typed-signature {
            font-family: 'Brush Script MT', 'Apple Chancery', cursive, 'Times New Roman';
            font-size: 14pt;
            font-style: italic;
            color: #000000;
            line-height: 1.1;
        }

        /* Page 2 Elements */
        .page2-header {
            border-bottom: 2px solid #0066cc;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        .page2-header h2 {
            font-size: 11pt;
            color: #0066cc;
            margin: 0;
        }

        .id-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .id-grid td {
            width: 50%;
            vertical-align: top;
            padding: 0 4px;
        }

        .id-card {
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 6px;
            background: #f8fafc;
            text-align: center;
        }

        .id-card-title {
            font-weight: bold;
            font-size: 7.5pt;
            margin-bottom: 4px;
            color: #1e293b;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 2px;
        }

        .id-image-frame {
            background: #ffffff;
            border: 1px dashed #94a3b8;
            padding: 4px;
            min-height: 150px;
            text-align: center;
        }

        .id-image-frame img {
            max-width: 260px;
            max-height: 140px;
            display: inline-block;
        }

        .admin-audit-card {
            background: #f0f7ff;
            border: 1px solid #bfdbfe;
            border-radius: 4px;
            padding: 6px 10px;
            margin-bottom: 10px;
        }

        .admin-audit-title {
            color: #1d4ed8;
            font-weight: bold;
            font-size: 7.5pt;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .audit-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7pt;
        }

        .audit-table td {
            padding: 2px 4px;
        }

        .audit-label {
            font-weight: bold;
            color: #475569;
            width: 110px;
        }

        .badge-pill {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 10px;
            font-weight: bold;
            font-size: 6.8pt;
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
            padding: 6px 10px;
            font-size: 6.8pt;
            line-height: 1.35;
            color: #475569;
        }

        .info-reference-box ul {
            margin-left: 14px;
            margin-top: 2px;
        }
    </style>
</head>
<body>

@php
    // Embedded Base64 Icons (Ensures 100% crisp rendering across all PDF viewers without font dependencies)
    $checkIcon = $checkIcon ?? 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAgUlEQVRYhe2VSRKAIAwEB8v/f1k+YMg6uZA+S7pBS4BhuJ3V5Pkkd0eAKO8IOMrZAaocAB5igAnWCZh2zwowyxkBLnl1gFteGRCSA8BbNSjK33BvRCpaetA6NH1imZ2VvC5tgSQp+1YiAZE5ItpdQL+urYLTSaQiPYvb/xHDMLSwAcVEFRqYBR9tAAAAAElFTkSuQmCC';
    $radioDot = $radioDot ?? 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAbElEQVRYhe2W2w3AIAwDafffmS5AwYmN0gr7H98RiUdrjnN6LmJtV/RlBEbgdG9EAAGH+1GBDBxiIAIMfMm5BeVUVhNQ7H7K+vQElLt/5ZVPwALlAj6Gvop/8RoyErL/QFRky48IEWH6HKcmD6NPDR6ZeggZAAAAAElFTkSuQmCC';

    // Safe extraction of pdf_form_data (handles array, JSON string, or double-encoded string)
    $rawPdf = $w9Form->pdf_form_data;
    $pdfData = $pdfData ?? [];
    if (empty($pdfData)) {
        if (is_array($rawPdf)) {
            $pdfData = $rawPdf;
        } elseif (is_string($rawPdf) && trim($rawPdf) !== '') {
            $dec = json_decode($rawPdf, true);
            if (is_string($dec)) {
                $dec = json_decode($dec, true);
            }
            if (is_array($dec)) {
                $pdfData = $dec;
            }
        }
    }

    // 1. Legal Name
    $fullName = $w9Form->full_name ?: ($pdfData['line1_name'] ?? ($pdfData['manual_full_name'] ?? ''));

    // 2. Business Name
    $businessName = $w9Form->business_name ?: ($pdfData['line2_business'] ?? '');

    // 3a. Tax Classification Resolution
    $taxClassification = $taxClassification ?? $w9Form->tax_classification;
    if (!$taxClassification && !empty($pdfData['line3a_tax'])) {
        $taxClassification = is_array($pdfData['line3a_tax']) ? ($pdfData['line3a_tax'][0] ?? '') : $pdfData['line3a_tax'];
    }

    $taxClassLower = strtolower(trim((string) $taxClassification));
    $rawTax3a = $pdfData['line3a_tax'] ?? [];
    if (!is_array($rawTax3a)) {
        $rawTax3a = [$rawTax3a];
    }
    $tax3aArray = array_map('strtolower', array_map('trim', array_filter($rawTax3a, 'is_string')));

    // Robust checkbox flags for Line 3a
    $isCCorp = in_array('c_corporation', $tax3aArray, true)
        || in_array('c corp', $tax3aArray, true)
        || $taxClassLower === 'c_corporation'
        || $taxClassLower === 'c_corp';

    $isSCorp = in_array('s_corporation', $tax3aArray, true)
        || in_array('s corp', $tax3aArray, true)
        || $taxClassLower === 's_corporation'
        || $taxClassLower === 's_corp';

    $isPartnership = in_array('partnership', $tax3aArray, true)
        || $taxClassLower === 'partnership';

    $isTrust = in_array('trust_estate', $tax3aArray, true)
        || in_array('trust', $tax3aArray, true)
        || str_contains($taxClassLower, 'trust')
        || str_contains($taxClassLower, 'estate');

    $isLlc = in_array('llc', $tax3aArray, true)
        || str_starts_with($taxClassLower, 'limited_liability_company')
        || $taxClassLower === 'llc';

    $isOther = in_array('other', $tax3aArray, true)
        || $taxClassLower === 'other';

    $isIndividual = in_array('individual', $tax3aArray, true)
        || in_array('sole_proprietor', $tax3aArray, true)
        || in_array('individual/sole proprietor', $tax3aArray, true)
        || str_contains($taxClassLower, 'individual')
        || str_contains($taxClassLower, 'sole')
        || (!$isCCorp && !$isSCorp && !$isPartnership && !$isTrust && !$isLlc && !$isOther);

    // LLC code
    $llcCode = '';
    if (strpos($taxClassification ?? '', 'limited_liability_company') === 0) {
        $parts = explode('_', $taxClassification);
        $llcCode = strtoupper(end($parts));
        if ($llcCode === 'INDIVIDUAL') $llcCode = 'P';
    } elseif (!empty($pdfData['llc_code'])) {
        $llcCode = strtoupper($pdfData['llc_code']);
    }

    // 3b. Foreign partners Resolution (Check all variants)
    $line3bRaw = $pdfData['line3b'] ?? ($pdfData['has_foreign_partners'] ?? ($pdfData['foreign_partners'] ?? null));
    $hasForeignPartners = ($line3bRaw === true || $line3bRaw === 1 || $line3bRaw === '1' || $line3bRaw === 'true' || $line3bRaw === 'on' || $line3bRaw === 'yes');

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
            if ($isCCorp || $isSCorp || $isPartnership) {
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
<div class="document-wrapper">
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
        <div class="line-number">1</div>
        <div class="line-content">
            <div class="line-input-underline">{{ $fullName }}</div>
            <div class="line-label">Name of entity/individual</div>
        </div>
        <div class="clear"></div>
    </div>

    <!-- Line 2 -->
    <div class="form-line">
        <div class="line-number">2</div>
        <div class="line-content">
            <div class="line-input-underline">{{ $businessName }}</div>
            <div class="line-label">Business name/disregarded entity name, if different from above.</div>
        </div>
        <div class="clear"></div>
    </div>

    <!-- Line 3a -->
    <div class="form-line">
        <div class="line-number">3a</div>
        <div class="line-content">
            <div style="font-size: 6.8pt; margin-bottom: 2px; line-height: 1.2; color: #111;">
                Check the appropriate box for federal tax classification of the entity/individual whose name is entered on line 1. Check only one of the following seven boxes.
            </div>

            <table class="check-table">
                <tr>
                    <td style="width: 38%;">
                        <span class="cb-box">{!! $isIndividual ? '<img src="' . $checkIcon . '" alt="✓">' : '&nbsp;' !!}</span> Individual/sole proprietor
                    </td>
                    <td style="width: 31%;">
                        <span class="cb-box">{!! $isCCorp ? '<img src="' . $checkIcon . '" alt="✓">' : '&nbsp;' !!}</span> C corporation
                    </td>
                    <td style="width: 31%;">
                        <span class="cb-box">{!! $isSCorp ? '<img src="' . $checkIcon . '" alt="✓">' : '&nbsp;' !!}</span> S corporation
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="cb-box">{!! $isPartnership ? '<img src="' . $checkIcon . '" alt="✓">' : '&nbsp;' !!}</span> Partnership
                    </td>
                    <td colspan="2">
                        <span class="cb-box">{!! $isTrust ? '<img src="' . $checkIcon . '" alt="✓">' : '&nbsp;' !!}</span> Trust/estate
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style="padding-top: 1px;">
                        <span class="cb-box">{!! $isLlc ? '<img src="' . $checkIcon . '" alt="✓">' : '&nbsp;' !!}</span>
                        <strong>LLC.</strong> Enter the tax classification (C = C corporation, S = S corporation, P = Partnership)
                        <span style="border-bottom: 1px solid #111; padding: 0 8px; font-weight: bold; font-size: 7.5pt;">{{ $llcCode ?: ' ' }}</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style="font-size: 6.2pt; color: #475569; padding-left: 15px; line-height: 1.15;">
                        Note: Check the "LLC" box above and enter the appropriate code for the tax classification of the LLC, unless it is a disregarded entity. A disregarded entity should instead check the appropriate box for the tax classification of its owner.
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style="padding-left: 15px; padding-top: 1px;">
                        <span class="cb-box">{!! $isOther ? '<img src="' . $checkIcon . '" alt="✓">' : '&nbsp;' !!}</span> Other
                    </td>
                </tr>
            </table>
        </div>
        <div class="clear"></div>
    </div>

    <!-- Line 3b -->
    <div class="form-line">
        <div class="line-number">3b</div>
        <div class="line-content">
            <div style="font-size: 6.8pt; margin-bottom: 2px; line-height: 1.2;">
                If applicable, check this box if you have foreign partners, owners, or beneficiaries.
            </div>
            <div>
                <span class="cb-box">{!! $hasForeignPartners ? '<img src="' . $checkIcon . '" alt="✓">' : '&nbsp;' !!}</span>
            </div>
        </div>
        <div class="clear"></div>
    </div>

    <!-- Line 4 -->
    <div class="form-line">
        <div class="line-number">4</div>
        <div class="line-content">
            <div style="font-size: 6.8pt; margin-bottom: 2px;">
                <strong>Exemptions:</strong> Exemptions apply only to certain entities. See IRS Form W-9 instructions for details.
            </div>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 50%; padding-right: 8px; vertical-align: top;">
                        <div class="line-input-underline">{{ $exemptPayeeCode }}</div>
                        <div class="line-label">Exempt payee code (if any)</div>
                    </td>
                    <td style="width: 50%; padding-left: 8px; vertical-align: top;">
                        <div class="line-input-underline">{{ $fatcaExemptionCode }}</div>
                        <div class="line-label">Exemption from FATCA reporting code (if any)</div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="clear"></div>
    </div>

    <!-- Line 5 -->
    <div class="form-line">
        <div class="line-number">5</div>
        <div class="line-content">
            <div class="line-input-underline">{{ $streetAddress }}</div>
            <div class="line-label">Address (number, street, apartment, or suite)</div>
        </div>
        <div class="clear"></div>
    </div>

    <!-- Line 6 -->
    <div class="form-line">
        <div class="line-number">6</div>
        <div class="line-content">
            <div class="line-input-underline">{{ $cityStateZip }}</div>
            <div class="line-label">City, state, and ZIP code</div>
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
            <td style="width: 50%; padding-right: 10px; border-right: 1px dashed #cbd5e1;">
                <div style="font-size: 7pt; font-weight: bold; margin-bottom: 2px;">
                    <span class="rb-circle">{!! $taxIdType === 'ssn' ? '<img src="' . $radioDot . '" alt="•">' : '&nbsp;' !!}</span>
                    Social security number
                </div>
                <table style="border-collapse: collapse; margin-top: 1px;">
                    <tr>
                        <td class="tin-digit-cell" style="width: 38px;">{{ $taxIdType === 'ssn' ? ($tinParts[0] ?? '') : '' }}</td>
                        <td class="tin-dash">-</td>
                        <td class="tin-digit-cell" style="width: 28px;">{{ $taxIdType === 'ssn' ? ($tinParts[1] ?? '') : '' }}</td>
                        <td class="tin-dash">-</td>
                        <td class="tin-digit-cell" style="width: 48px;">{{ $taxIdType === 'ssn' ? ($tinParts[2] ?? '') : '' }}</td>
                    </tr>
                </table>
            </td>

            <!-- EIN Column -->
            <td style="width: 50%; padding-left: 10px;">
                <div style="font-size: 7pt; font-weight: bold; margin-bottom: 2px;">
                    <span class="rb-circle">{!! $taxIdType === 'ein' ? '<img src="' . $radioDot . '" alt="•">' : '&nbsp;' !!}</span>
                    Employer identification number
                </div>
                <table style="border-collapse: collapse; margin-top: 1px;">
                    <tr>
                        <td class="tin-digit-cell" style="width: 32px;">{{ $taxIdType === 'ein' ? ($einParts[0] ?? '') : '' }}</td>
                        <td class="tin-dash">-</td>
                        <td class="tin-digit-cell" style="width: 78px;">{{ $taxIdType === 'ein' ? ($einParts[1] ?? '') : '' }}</td>
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

        <div style="font-size: 6.3pt; line-height: 1.2; color: #1e293b;">
            <strong>Electronic Signature Certification:</strong> By typing my legal name or drawing my signature below, I electronically sign this Substitute Form W-9. I understand that my electronic signature has the same legal effect as a handwritten signature.
        </div>

        <div class="cert-ack-box">
            <span class="cb-box">{!! $w9Form->certification_signed ? '<img src="' . $checkIcon . '" alt="✓">' : '&nbsp;' !!}</span>
            <strong>I certify and agree to the statements contained in Part II above.</strong>
            <div style="font-size: 6pt; color: #4b5563; margin-top: 1px; padding-left: 15px;">
                If you cannot certify U.S. person status, you may need to complete Form W-8 instead.
            </div>
        </div>

        <table class="signature-table">
            <tr>
                <td style="width: 50%;">
                    <div style="font-size: 6.8pt; font-weight: bold; margin-bottom: 2px;">Signature Method</div>
                    <div style="font-size: 6.8pt;">
                        <span class="rb-circle">{!! $signatureMethod === 'typed' ? '<img src="' . $radioDot . '" alt="•">' : '&nbsp;' !!}</span> Type Legal Name
                        &nbsp;&nbsp;
                        <span class="rb-circle">{!! $signatureMethod === 'draw' ? '<img src="' . $radioDot . '" alt="•">' : '&nbsp;' !!}</span> Draw Signature
                    </div>
                </td>
                <td style="width: 50%;">
                    <div style="font-size: 6.8pt; font-weight: bold; margin-bottom: 1px;">Date</div>
                    <div class="line-input-underline" style="padding: 1px 2px;">
                        {{ $w9Form->certification_date ? $w9Form->certification_date->format('m/d/Y') : date('m/d/Y') }}
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding-top: 2px;">
                    <div style="font-size: 6.8pt; font-weight: bold; margin-bottom: 1px;">Signature</div>
                    <div class="sig-box">
                        @if($isDrawnSignature && $signatureImage)
                            <img src="{{ $signatureImage }}" alt="Signature" style="max-height: 28px; max-width: 220px; display: block;">
                        @elseif($signatureTyped)
                            <div class="typed-signature">{{ $signatureTyped }}</div>
                        @else
                            <div style="font-size: 6.5pt; color: #94a3b8; padding-top: 4px;">[Electronically Certified via CartVIP Onboarding]</div>
                        @endif
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>

<!-- ======================== PAGE 2: VERIFICATION & AUDIT RECORDS ======================== -->
<div class="document-wrapper page-break">
    <div class="page2-header">
        <h2>Government-Issued ID & Submission Audit Records</h2>
        <div style="font-size: 7.2pt; color: #64748b; margin-top: 2px;">Substitute Form W-9 Verification Attachment</div>
    </div>

    <!-- Government ID Photos Section -->
    <table class="id-grid">
        <tr>
            <td colspan="2" style="width: 100%; padding: 0 0 5px 0;">
                <div style="font-size: 7.5pt; color: #334155; margin-bottom: 3px;">
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
                            <div style="font-size: 7.2pt; color: #94a3b8; padding-top: 55px;">
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
                            <div style="font-size: 7.2pt; color: #94a3b8; padding-top: 55px;">
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
        <div style="font-weight: bold; color: #0f172a; margin-bottom: 2px; font-size: 7.2pt;">Need Help / Official IRS Instructions</div>
        <p style="margin-bottom: 3px;">
            For detailed line-by-line instructions on completing Form W-9, consult official IRS documentation at <strong>IRS.gov/FormW9</strong>. Official resources cover line-by-line requirements, taxpayer identification types (SSN, EIN, ITIN), backup withholding regulations, FATCA reporting codes, and penalties for failure to furnish accurate TIN information.
        </p>

        <div style="border-top: 1px dashed #cbd5e1; margin: 4px 0;"></div>

        <div style="font-weight: bold; color: #0f172a; margin-bottom: 2px; font-size: 7.2pt;">Privacy Notice & Data Protection</div>
        <p style="margin: 0;">
            Information collected through this Substitute Form W-9 onboarding process is utilized exclusively for identity verification, payment processing, fraud prevention, and federal/state tax reporting compliance. Access to taxpayer identification information and government ID records is strictly restricted to authorized compliance personnel.
        </p>
    </div>
</div>

</body>
</html>
