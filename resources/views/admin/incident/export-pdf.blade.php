<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Incident Report #{{ $incident->id }}</title>
    <style>
        @page { margin: 28px 28px 36px 28px; }
        body { font-family: DejaVu Sans, Arial, sans-serif; color: #111; font-size: 12px; line-height: 1.45; }
        .letterhead {
            border: 1px solid #222;
            padding: 12px;
            margin-bottom: 14px;
        }
        .letterhead h1 { margin: 0; font-size: 18px; letter-spacing: 0.5px; }
        .letterhead .meta { margin-top: 8px; font-size: 11px; color: #333; }
        .section { border: 1px solid #c7c7c7; margin-bottom: 12px; }
        .section-title { background: #f2f2f2; font-weight: 700; padding: 8px 10px; border-bottom: 1px solid #c7c7c7; }
        .section-body { padding: 10px; }
        .grid { width: 100%; border-collapse: collapse; }
        .grid td { padding: 4px 6px; vertical-align: top; }
        .label { font-weight: 700; }
        table.table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        table.table th, table.table td { border: 1px solid #b6b6b6; padding: 6px; vertical-align: top; }
        table.table th { background: #f6f6f6; text-align: left; }
        .muted { color: #555; }
        .mono { font-family: DejaVu Sans Mono, monospace; font-size: 10.5px; }
    </style>
</head>
<body>
    <div class="letterhead">
        <h1>CONFIDENTIAL INCIDENT REPORT PACKET</h1>
        <div class="meta">
            Prepared For Legal Review<br>
            Club: {{ $incident->website->name }} ({{ $incident->website->domain ?? 'N/A' }})<br>
            Incident ID: {{ $incident->id }}<br>
            Exported At: {{ now()->format('Y-m-d H:i:s') }}
        </div>
    </div>

    <div class="section">
        <div class="section-title">Incident Summary</div>
        <div class="section-body">
            <table class="grid">
                <tr>
                    <td><span class="label">Legal Name:</span> {{ $incident->location_legal_name }}</td>
                    <td><span class="label">DBA:</span> {{ $incident->location_dba_name }}</td>
                </tr>
                <tr>
                    <td><span class="label">Incident Date:</span> {{ optional($incident->incident_calendar_date)->format('Y-m-d') }}</td>
                    <td><span class="label">Incident Time:</span> {{ $incident->incident_time }}</td>
                </tr>
                <tr>
                    <td><span class="label">Date Submitted:</span> {{ optional($incident->date_submitted)->format('Y-m-d') }}</td>
                    <td><span class="label">Current Status:</span> {{ ucwords(str_replace('_', ' ', $incident->status)) }}</td>
                </tr>
                <tr>
                    <td><span class="label">Status Changed At:</span> {{ $incident->status_changed_at ? $incident->status_changed_at->format('Y-m-d H:i:s') : 'N/A' }}</td>
                    <td><span class="label">Status Changed By:</span> {{ optional($incident->statusChangedBy)->name ?: 'N/A' }}</td>
                </tr>
                <tr>
                    <td colspan="2"><span class="label">Address:</span> {{ $incident->location_address }}</td>
                </tr>
                <tr>
                    <td><span class="label">Reporter Name:</span> {{ $incident->reporter_name }}</td>
                    <td><span class="label">Managers on Duty:</span> {{ $incident->managers_on_duty }}</td>
                </tr>
                <tr>
                    <td><span class="label">Manager Phone:</span> {{ $incident->manager_phone ?: 'N/A' }}</td>
                    <td><span class="label">Police Report #:</span> {{ $incident->police_report_number ?: 'N/A' }}</td>
                </tr>
            </table>

            <p><span class="label">Police Officers and Badge #'s:</span><br>{{ $incident->police_officers_badges ?: 'N/A' }}</p>
            <p><span class="label">Involved/Injured Persons:</span><br>{{ $incident->involved_injured_persons }}</p>
            <p><span class="label">Detailed Description:</span><br>{{ $incident->incident_description }}</p>
            <p><span class="label">Witnesses Statement:</span><br>{{ $incident->witnesses_statement }}</p>
            <p><span class="label">Camera Angles:</span><br>{{ $incident->camera_angles }}</p>
            <p><span class="label">Camera Timestamp:</span> {{ $incident->camera_timestamp }}</p>
            <p><span class="label">Cast Members Involved:</span><br>{{ $incident->cast_members_involved }}</p>
            <p><span class="label">Additional Media Notes:</span><br>{{ $incident->additional_media_notes ?: 'N/A' }}</p>
            <p>
                <span class="label">E-Sign Accepted:</span> {{ $incident->accepted_esignature ? 'Yes' : 'No' }} |
                <span class="label">Opted Out:</span> {{ $incident->opted_out_esignature ? 'Yes' : 'No' }} |
                <span class="label">Digital Signature:</span> {{ $incident->digital_signature_name }}
            </p>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Incident Attachments</div>
        <div class="section-body">
            <table class="table">
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
                            <td class="mono">{{ $attachment->file_path }}</td>
                            <td>{{ number_format(((int) $attachment->file_size) / 1024, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">No incident attachments uploaded.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Witness Reports ({{ $incident->witnessReports->count() }})</div>
        <div class="section-body">
            @forelse($incident->witnessReports as $index => $witness)
                <p><span class="label">Witness #{{ $index + 1 }}</span></p>
                <table class="grid">
                    <tr>
                        <td><span class="label">Name:</span> {{ $witness->full_name }}</td>
                        <td><span class="label">Type:</span> {{ $witness->participant_type }}</td>
                    </tr>
                    <tr>
                        <td><span class="label">Phone:</span> {{ $witness->phone_number }}</td>
                        <td><span class="label">Submitted Via:</span> {{ ucwords(str_replace('_', ' ', $witness->submitted_via)) }}</td>
                    </tr>
                    <tr>
                        <td colspan="2"><span class="label">Address:</span> {{ $witness->address }}</td>
                    </tr>
                </table>
                <p><span class="label">Statement:</span><br>{{ $witness->detailed_statement }}</p>
                <p class="muted">
                    E-Sign Accepted: {{ $witness->accepted_esignature ? 'Yes' : 'No' }},
                    Opted Out: {{ $witness->opted_out_esignature ? 'Yes' : 'No' }},
                    Signature: {{ $witness->digital_signature_name }}
                </p>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Attachment</th>
                            <th>Stored Path</th>
                            <th>Size (KB)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($witness->attachments as $attachment)
                            <tr>
                                <td>{{ $attachment->original_name }}</td>
                                <td class="mono">{{ $attachment->file_path }}</td>
                                <td>{{ number_format(((int) $attachment->file_size) / 1024, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">No witness attachments.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <hr>
            @empty
                <p>No witness reports attached.</p>
            @endforelse
        </div>
    </div>

    <div class="section">
        <div class="section-title">Immutable Audit Trail ({{ $incident->auditLogs->count() }})</div>
        <div class="section-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>When</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>IP</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($incident->auditLogs as $log)
                        <tr>
                            <td>{{ optional($log->created_at)->format('Y-m-d H:i:s') }}</td>
                            <td>{{ optional($log->user)->name ?: 'Public/Guest' }}</td>
                            <td>{{ ucwords(str_replace('_', ' ', $log->action)) }}</td>
                            <td>{{ $log->ip_address ?: 'N/A' }}</td>
                            <td class="mono">{{ $log->change_summary ? json_encode($log->change_summary, JSON_UNESCAPED_SLASHES) : 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">No audit entries recorded.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
