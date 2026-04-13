<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Witness Statement #{{ $witness->id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; color: #000; background: #fff; padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; }
        .header { border-bottom: 3px solid #333; padding-bottom: 12px; margin-bottom: 20px; }
        .header h1 { font-size: 22px; margin-bottom: 4px; }
        .header p { font-size: 12px; color: #666; }
        .section { margin-bottom: 18px; }
        .section-title { font-size: 14px; font-weight: 700; background: #f0f0f0; padding: 8px 10px; border-left: 4px solid #333; margin-bottom: 10px; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px; }
        .field { }
        .field-label { font-weight: 700; font-size: 12px; color: #333; }
        .field-value { font-size: 12px; color: #555; margin-top: 2px; word-break: break-word; }
        .full-width { grid-column: 1 / -1; }
        .statement-box { border: 1px solid #ccc; padding: 10px; background: #fafafa; min-height: 40px; font-size: 12px; line-height: 1.4; white-space: pre-wrap; word-break: break-word; }
        .footer { margin-top: 30px; padding-top: 12px; border-top: 1px solid #ddd; font-size: 11px; color: #666; text-align: center; }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Witness Statement Report</h1>
            <p>Incident #{{ $incident->id }} | {{ $incident->website->name }}</p>
        </div>

        <div class="section">
            <div class="section-title">Location Information</div>
            <div class="grid">
                <div class="field">
                    <div class="field-label">Legal Name of Location</div>
                    <div class="field-value">{{ $witness->location_legal_name }}</div>
                </div>
                <div class="field">
                    <div class="field-label">DBA of Location</div>
                    <div class="field-value">{{ $witness->location_dba_name }}</div>
                </div>
                <div class="field full-width">
                    <div class="field-label">Address of Location</div>
                    <div class="field-value">{{ $witness->location_address }}</div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Incident Details</div>
            <div class="grid">
                <div class="field">
                    <div class="field-label">Calendar Date of Incident</div>
                    <div class="field-value">{{ optional($witness->incident_calendar_date)->format('Y-m-d') }}</div>
                </div>
                <div class="field">
                    <div class="field-label">Date Submitted</div>
                    <div class="field-value">{{ optional($witness->date_submitted)->format('Y-m-d') }}</div>
                </div>
                <div class="field">
                    <div class="field-label">Time of Incident</div>
                    <div class="field-value">{{ $witness->incident_time }}</div>
                </div>
                <div class="field">
                    <div class="field-label">Type of Incident</div>
                    <div class="field-value">{{ $witness->incident_type }}</div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Witness Information</div>
            <div class="grid">
                <div class="field">
                    <div class="field-label">Full Name (Legal Name)</div>
                    <div class="field-value">{{ $witness->full_name }}</div>
                </div>
                <div class="field">
                    <div class="field-label">Participant Type</div>
                    <div class="field-value">{{ $witness->participant_type }}</div>
                </div>
                <div class="field">
                    <div class="field-label">Address</div>
                    <div class="field-value">{{ $witness->address }}</div>
                </div>
                <div class="field">
                    <div class="field-label">Phone Number</div>
                    <div class="field-value">{{ $witness->phone_number }}</div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Statement</div>
            <div class="field full-width">
                <div class="statement-box">{{ $witness->detailed_statement }}</div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Signature & Attestation</div>
            <div class="grid">
                <div class="field">
                    <div class="field-label">E-Signature Accepted</div>
                    <div class="field-value">{{ $witness->accepted_esignature ? 'Yes' : 'No' }}</div>
                </div>
                <div class="field">
                    <div class="field-label">Opted Out of E-Signature</div>
                    <div class="field-value">{{ $witness->opted_out_esignature ? 'Yes' : 'No' }}</div>
                </div>
                <div class="field full-width">
                    <div class="field-label">Digital Signature Name</div>
                    <div class="field-value">{{ $witness->digital_signature_name }}</div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Submission Details</div>
            <div class="grid">
                <div class="field">
                    <div class="field-label">Submitted Via</div>
                    <div class="field-value">{{ ucwords(str_replace('_', ' ', $witness->submitted_via)) }}</div>
                </div>
                <div class="field">
                    <div class="field-label">Submitted At</div>
                    <div class="field-value">{{ optional($witness->created_at)->format('Y-m-d H:i:s') }}</div>
                </div>
            </div>
        </div>

        @if($witness->attachments->isNotEmpty())
            <div class="section">
                <div class="section-title">Attachments</div>
                <div class="field full-width">
                    @foreach($witness->attachments as $attachment)
                        <div style="font-size: 12px; margin-bottom: 4px;">
                            • {{ $attachment->original_name }} ({{ number_format(((int) $attachment->file_size) / 1024, 2) }} KB)
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="footer">
            <p>Generated: {{ now()->format('Y-m-d H:i:s') }}</p>
        </div>
    </div>
</body>
</html>
