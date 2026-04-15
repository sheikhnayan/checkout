<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Incident Report #{{ $incident->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #111; line-height: 1.4; margin: 24px; }
        h1, h2, h3 { margin: 0 0 8px; }
        .meta { margin-bottom: 16px; }
        .section { border: 1px solid #bbb; padding: 12px; margin-bottom: 12px; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px 16px; }
        .label { font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #aaa; padding: 8px; vertical-align: top; }
        th { background: #f0f0f0; text-align: left; }
        .small { font-size: 12px; color: #444; }
    </style>
</head>
<body>
    @php
        $incidentTz = 'America/Los_Angeles';
    @endphp
    <h1>Incident Report Packet</h1>
    <div class="meta small">
        Incident ID: {{ $incident->id }}<br>
        Club: {{ $incident->website->name }}<br>
        Exported At: {{ now($incidentTz)->format('Y-m-d H:i:s') }} PT
    </div>

    <div class="section">
        <h2>Incident Details</h2>
        <div class="grid">
            <div><span class="label">Legal Name:</span> {{ $incident->location_legal_name }}</div>
            <div><span class="label">DBA:</span> {{ $incident->location_dba_name }}</div>
            <div><span class="label">Address:</span> {{ $incident->location_address }}</div>
            <div><span class="label">Incident Date:</span> {{ optional($incident->incident_calendar_date)->format('Y-m-d') }}</div>
            <div><span class="label">Date Submitted:</span> {{ optional($incident->date_submitted)->format('Y-m-d') }}</div>
            <div><span class="label">Incident Time:</span> {{ $incident->incident_time }}</div>
            <div><span class="label">Reporter:</span> {{ $incident->reporter_name }}</div>
            <div><span class="label">Managers on Duty:</span> {{ $incident->managers_on_duty }}</div>
            <div><span class="label">Manager Phone:</span> {{ $incident->manager_phone ?: 'N/A' }}</div>
            <div><span class="label">Police Report #:</span> {{ $incident->police_report_number ?: 'N/A' }}</div>
        </div>

        <p><span class="label">Police Officers/Badges:</span><br>{{ $incident->police_officers_badges ?: 'N/A' }}</p>
        <p><span class="label">Involved/Injured Persons:</span><br>{{ $incident->involved_injured_persons }}</p>
        <p><span class="label">Detailed Description:</span><br>{{ $incident->incident_description }}</p>
        <p><span class="label">Witnesses Statement:</span><br>{{ $incident->witnesses_statement }}</p>
        <p><span class="label">Camera Angles:</span><br>{{ $incident->camera_angles }}</p>
        <p><span class="label">Camera Timestamp:</span> {{ $incident->camera_timestamp }}</p>
        <p><span class="label">Cast Members Involved:</span><br>{{ $incident->cast_members_involved }}</p>
        <p><span class="label">Additional Media Notes:</span><br>{{ $incident->additional_media_notes ?: 'N/A' }}</p>

        <p><span class="label">E-signature Accepted:</span> {{ $incident->accepted_esignature ? 'Yes' : 'No' }}</p>
        <p><span class="label">E-signature Opt-out:</span> {{ $incident->opted_out_esignature ? 'Yes' : 'No' }}</p>
        <p><span class="label">Digital Signature Name:</span> {{ $incident->digital_signature_name }}</p>
    </div>

    <div class="section">
        <h2>Incident Attachments</h2>
        <table>
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Original File Name</th>
                    <th>Stored Path</th>
                    <th>Size (KB)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($incident->attachments as $attachment)
                    <tr>
                        <td>{{ ucwords(str_replace('_', ' ', $attachment->attachment_type)) }}</td>
                        <td>{{ $attachment->original_name }}</td>
                        <td>{{ $attachment->file_path }}</td>
                        <td>{{ number_format(((int) $attachment->file_size) / 1024, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">No attachments uploaded.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Witness Reports</h2>
        @forelse($incident->witnessReports as $index => $witness)
            <h3>Witness #{{ $index + 1 }} - {{ $witness->full_name }}</h3>
            <div class="grid">
                <div><span class="label">Participant Type:</span> {{ $witness->participant_type }}</div>
                <div><span class="label">Submitted Via:</span> {{ ucwords(str_replace('_', ' ', $witness->submitted_via)) }}</div>
                <div><span class="label">Phone:</span> {{ $witness->phone_number }}</div>
                <div><span class="label">Date Submitted:</span> {{ optional($witness->date_submitted)->format('Y-m-d') }}</div>
            </div>
            <p><span class="label">Address:</span> {{ $witness->address }}</p>
            <p><span class="label">Detailed Statement:</span><br>{{ $witness->detailed_statement }}</p>
            <p><span class="label">Digital Signature:</span> {{ $witness->digital_signature_name }}</p>

            <table>
                <thead>
                    <tr>
                        <th>Attachment Name</th>
                        <th>Stored Path</th>
                        <th>Size (KB)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($witness->attachments as $attachment)
                        <tr>
                            <td>{{ $attachment->original_name }}</td>
                            <td>{{ $attachment->file_path }}</td>
                            <td>{{ number_format(((int) $attachment->file_size) / 1024, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">No witness attachment provided.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <hr>
        @empty
            <p>No witness reports are attached to this incident.</p>
        @endforelse
    </div>
</body>
</html>
