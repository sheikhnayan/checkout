<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Form W-9 - {{ $w9Form->full_name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Times New Roman', serif;
            font-size: 11px;
            line-height: 1.4;
            color: #000;
        }
        .page {
            page-break-after: always;
            padding: 0.75in 0.5in;
        }
        .page:last-child {
            page-break-after: avoid;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }
        .irs-name {
            font-weight: bold;
            font-size: 10px;
            margin-bottom: 3px;
        }
        .form-title {
            font-weight: bold;
            font-size: 16px;
            margin: 5px 0;
        }
        .form-subtitle {
            font-size: 10px;
            margin-bottom: 5px;
        }
        .meta-info {
            display: table;
            width: 100%;
            font-size: 9px;
            margin-bottom: 15px;
            border-bottom: 1px solid #999;
            padding-bottom: 8px;
        }
        .meta-cell {
            display: table-cell;
            width: 25%;
        }
        .section {
            margin: 18px 0;
        }
        .section-title {
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
            border-bottom: 1px solid #000;
            padding-bottom: 4px;
            margin-bottom: 10px;
        }
        .field-row {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        .field-cell {
            display: table-cell;
            width: 50%;
            padding-right: 10px;
            vertical-align: top;
        }
        .field-cell.full {
            width: 100%;
        }
        .field-group {
            margin-bottom: 8px;
        }
        .field-label {
            font-weight: bold;
            font-size: 10px;
            margin-bottom: 2px;
        }
        .field-value {
            font-size: 11px;
            padding: 3px 4px;
            border-bottom: 1px solid #000;
            min-height: 14px;
        }
        .checkbox-status {
            font-size: 11px;
            margin-bottom: 4px;
        }
        .certification-box {
            border: 1px solid #000;
            padding: 10px;
            margin: 15px 0;
            background: #fafafa;
        }
        .certification-box p {
            font-size: 10px;
            margin-bottom: 6px;
            line-height: 1.5;
        }
        .id-section {
            margin: 20px 0;
            border: 1px solid #ccc;
            padding: 10px;
            background: #f9f9f9;
        }
        .id-label {
            font-weight: bold;
            font-size: 10px;
            margin-bottom: 8px;
        }
        .id-image {
            max-width: 48%;
            border: 1px solid #000;
            margin-bottom: 8px;
            display: inline-block;
            margin-right: 2%;
        }
        .signature-box {
            margin-top: 20px;
            border-top: 1px solid #000;
            padding-top: 10px;
        }
        .signature-line {
            border-top: 1px solid #000;
            width: 40%;
            display: inline-block;
            margin-bottom: 2px;
        }
        .signature-label {
            font-size: 9px;
            margin-top: 2px;
            display: inline-block;
        }
        .footer {
            text-align: center;
            font-size: 8px;
            color: #666;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #999;
        }
        .badge {
            display: inline-block;
            background: #007bff;
            color: white;
            padding: 2px 6px;
            font-size: 9px;
            border-radius: 3px;
            margin-right: 5px;
        }
        .badge-success {
            background: #28a745;
        }
        .badge-warning {
            background: #ffc107;
            color: #000;
        }
        .badge-danger {
            background: #dc3545;
        }
        .badge-secondary {
            background: #6c757d;
        }
    </style>
</head>
<body>
    <div class="page">
        <!-- Header -->
        <div class="header">
            <div class="irs-name">Department of the Treasury - Internal Revenue Service</div>
            <div class="form-title">Form W-9</div>
            <div class="form-subtitle">Request for Taxpayer Identification Number and Certification</div>
        </div>

        <!-- Meta Info -->
        <div class="meta-info">
            <div class="meta-cell"><strong>OMB No.:</strong> 1545-0047</div>
            <div class="meta-cell"><strong>Expires:</strong> 12/31/2025</div>
            <div class="meta-cell"><strong>Submitted:</strong> {{ $w9Form->created_at->format('M d, Y') }}</div>
            <div class="meta-cell"><strong>Status:</strong>
                @if($w9Form->status === 'approved')
                    <span class="badge badge-success">✓ Approved</span>
                @elseif($w9Form->status === 'submitted')
                    <span class="badge badge-warning">⏳ Pending</span>
                @elseif($w9Form->status === 'rejected')
                    <span class="badge badge-danger">✗ Rejected</span>
                @else
                    <span class="badge badge-secondary">Pending</span>
                @endif
            </div>
        </div>

        <!-- Part I: Taxpayer Identification -->
        <div class="section">
            <div class="section-title">Part I: Taxpayer Identification Number (TIN)</div>

            <div class="field-row">
                <div class="field-cell full">
                    <div class="field-group">
                        <div class="field-label">1. Name as shown on your income tax return</div>
                        <div class="field-value">{{ $w9Form->full_name }}</div>
                    </div>
                </div>
            </div>

            <div class="field-row">
                <div class="field-cell full">
                    <div class="field-group">
                        <div class="field-label">2. Business name/Disregarded entity name, if different from above</div>
                        <div class="field-value">{{ $w9Form->business_name ?: '(Not provided)' }}</div>
                    </div>
                </div>
            </div>

            <div class="field-row">
                <div class="field-cell">
                    <div class="field-group">
                        <div class="field-label">3a. Requester's name and address</div>
                        <div class="field-value">{{ $w9Form->requester_name ?: '(Not provided)' }}</div>
                    </div>
                </div>
                <div class="field-cell">
                    <div class="field-group">
                        <div class="field-label">3b. Requester's telephone number</div>
                        <div class="field-value">{{ $w9Form->requester_phone ?: '(Not provided)' }}</div>
                    </div>
                </div>
            </div>

            <div class="field-row">
                <div class="field-cell full">
                    <div class="field-group">
                        <div class="field-label">3c. Requester's email address</div>
                        <div class="field-value">{{ $w9Form->requester_email ?: '(Not provided)' }}</div>
                    </div>
                </div>
            </div>

            <div class="field-row">
                <div class="field-cell full">
                    <div class="field-group">
                        <div class="field-label">4. Federal income tax classification</div>
                        <div class="field-value">{{ ucwords(str_replace('_', ' ', $w9Form->tax_classification)) }}{{ $w9Form->tax_classification_other ? ' - ' . $w9Form->tax_classification_other : '' }}</div>
                    </div>
                </div>
            </div>

            <div class="field-row">
                <div class="field-cell full">
                    <div class="field-group">
                        <div class="field-label">5. Exemption from FATCA reporting code (if any)</div>
                        <div class="field-value">{{ $w9Form->fatca_exemption_code ?: '(Not provided)' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Part II: Tax Identification Number -->
        <div class="section">
            <div class="section-title">Part II: Tax Identification Number (TIN)</div>

            <div class="field-row">
                <div class="field-cell">
                    <div class="field-group">
                        <div class="field-label">6. Tax ID Type</div>
                        <div class="field-value">{{ strtoupper($w9Form->tax_id_type) }}</div>
                    </div>
                </div>
                <div class="field-cell">
                    <div class="field-group">
                        <div class="field-label">7. Federal income tax number</div>
                        <div class="field-value">{{ $w9Form->tax_id_number }}</div>
                    </div>
                </div>
            </div>

            <div class="field-row">
                <div class="field-cell">
                    <div class="field-group">
                        <div class="field-label">8. Exempt payee code (if any)</div>
                        <div class="field-value">{{ $w9Form->exempt_payee_code ?: '(Not provided)' }}</div>
                    </div>
                </div>
                <div class="field-cell">
                    <div class="field-group">
                        <div class="field-label">9. Account number(s)</div>
                        <div class="field-value">{{ $w9Form->account_numbers ?: '(Not provided)' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Part III: Address -->
        <div class="section">
            <div class="section-title">Part III: Address</div>

            <div class="field-row">
                <div class="field-cell full">
                    <div class="field-group">
                        <div class="field-label">10. Street address</div>
                        <div class="field-value">{{ $w9Form->street_address }}</div>
                    </div>
                </div>
            </div>

            <div class="field-row">
                <div class="field-cell full">
                    <div class="field-group">
                        <div class="field-label">11. City, state, and ZIP code</div>
                        <div class="field-value">{{ $w9Form->city }}, {{ strtoupper($w9Form->state) }} {{ $w9Form->zip_code }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Part IV: ID Verification -->
        <div class="section">
            <div class="section-title">Part IV: Government-Issued ID Verification</div>

            <div class="field-group">
                <div class="field-label">ID Document Type: {{ ucwords(str_replace('_', ' ', $w9Form->id_document_type)) }}</div>
            </div>

            @if($w9Form->id_front_image || $w9Form->id_back_image)
            <div class="id-section">
                <div class="id-label">Submitted Government-Issued ID Images</div>
                @if($w9Form->id_front_image)
                    <div style="display: inline-block; width: 48%; margin-right: 2%;">
                        <img src="{{ asset('storage/' . $w9Form->id_front_image) }}" alt="ID Front" class="id-image" style="width: 100%;">
                        <div style="font-size: 9px; margin-top: 3px;">ID Front</div>
                    </div>
                @endif
                @if($w9Form->id_back_image)
                    <div style="display: inline-block; width: 48%;">
                        <img src="{{ asset('storage/' . $w9Form->id_back_image) }}" alt="ID Back" class="id-image" style="width: 100%;">
                        <div style="font-size: 9px; margin-top: 3px;">ID Back</div>
                    </div>
                @endif
            </div>
            @endif
        </div>

        <!-- Part V: Certification -->
        <div class="section">
            <div class="section-title">Part V: Certification</div>

            <div class="certification-box">
                <p><strong>Certification Status:</strong>
                    @if($w9Form->certification_signed)
                        ✓ Certified on {{ $w9Form->certification_date?->format('M d, Y h:i A') ?? 'Unknown date' }}
                    @else
                        Not yet certified
                    @endif
                </p>
                <p style="margin-top: 10px;">
                    <strong>Under penalties of perjury,</strong> I certify that:
                    <br>1. The number shown on this form is my correct taxpayer identification number (or I am waiting for a number to be issued to me), and
                    <br>2. I am not subject to backup withholding because: (a) I am exempt from U.S. income tax, or (b) I have not been notified by the Internal Revenue Service (IRS) that I am subject to backup withholding as a result of a failure to report all interest or dividends, or (c) the IRS has notified me that I am no longer subject to backup withholding, and
                    <br>3. I am a U.S. citizen or other U.S. person (defined in section 7701(a)(30) of the Internal Revenue Code).
                </p>
            </div>

            @if($w9Form->reviewed_by && $w9Form->reviewed_at)
            <div style="margin-top: 12px;">
                <div class="field-group">
                    <div class="field-label">Reviewed by Admin:</div>
                    <div class="field-value">{{ $w9Form->reviewedBy?->name ?? 'System' }} on {{ $w9Form->reviewed_at->format('M d, Y h:i A') }}</div>
                </div>
            </div>
            @endif

            @if($w9Form->admin_notes)
            <div style="margin-top: 12px;">
                <div class="field-group">
                    <div class="field-label">Admin Notes:</div>
                    <div class="field-value" style="border-bottom: none; white-space: pre-wrap;">{{ $w9Form->admin_notes }}</div>
                </div>
            </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="margin: 0 0 4px 0;">
                This Form W-9 was completed and submitted through CartVIP on {{ $w9Form->created_at->format('M d, Y') }}.
            </p>
            <p style="margin: 0; color: #999; font-size: 8px;">
                For official IRS requirements, visit www.irs.gov/pub/irs-pdf/fw9.pdf
            </p>
        </div>
    </div>
</body>
</html>
