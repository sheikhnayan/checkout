@extends('admin.main')

@section('content')
<link rel="stylesheet" href="{{ asset('user/extra.css') }}">

<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="app-main__inner">
            <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3 mt-4">
                <h4 class="mb-0">Incident #{{ $incident->id }} - {{ $incident->website->name }}</h4>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.incident.witness.create', $incident->id) }}" class="btn btn-primary">Add Witness</a>
                    <a href="{{ route('admin.incident.export', $incident->id) }}" class="btn btn-info">Export PDF</a>
                    <a href="{{ route('admin.incident.show', $incident->website_id) }}" class="btn btn-secondary">Back</a>
                </div>
            </div>

            @php
                $statusClasses = [
                    'open' => 'bg-danger',
                    'under_review' => 'bg-warning text-dark',
                    'closed' => 'bg-success',
                ];
            @endphp

            <div class="card bg-primary text-white p-3 mb-3">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Current Status</label>
                        <div>
                            <span class="badge {{ $statusClasses[$incident->status] ?? 'bg-secondary' }} fs-6">{{ ucwords(str_replace('_', ' ', $incident->status)) }}</span>
                        </div>
                        <small class="d-block mt-2 text-white-50">
                            Last changed: {{ $incident->status_changed_at ? $incident->status_changed_at->format('Y-m-d H:i') : 'N/A' }}
                            by {{ optional($incident->statusChangedBy)->name ?: 'System' }}
                        </small>
                    </div>
                    <div class="col-md-8">
                        <form method="POST" action="{{ route('admin.incident.status.update', $incident->id) }}" class="row g-2">
                            @csrf
                            <div class="col-md-4">
                                <label class="form-label">Update Status</label>
                                <select name="status" class="form-select" required>
                                    <option value="open" {{ $incident->status === 'open' ? 'selected' : '' }}>Open</option>
                                    <option value="under_review" {{ $incident->status === 'under_review' ? 'selected' : '' }}>Under Review</option>
                                    <option value="closed" {{ $incident->status === 'closed' ? 'selected' : '' }}>Closed</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status Change Note (optional)</label>
                                <input type="text" name="status_note" class="form-control" placeholder="Reason for status change">
                            </div>
                            <div class="col-md-2 d-grid">
                                <label class="form-label d-none d-md-block">&nbsp;</label>
                                <button type="submit" class="btn btn-warning">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="alert alert-warning">
                <strong>Share Witness Link:</strong>
                <input class="form-control mt-2" readonly value="{{ route('incident.witness.form', $incident->public_witness_token) }}" onclick="this.select();">
            </div>

            <div class="card bg-primary text-white p-3 mb-3">
                <h5 class="mb-3">Incident Information</h5>
                <div class="row">
                    <div class="col-md-6"><strong>Legal Name:</strong> {{ $incident->location_legal_name }}</div>
                    <div class="col-md-6"><strong>DBA:</strong> {{ $incident->location_dba_name }}</div>
                    <div class="col-md-12"><strong>Address:</strong> {{ $incident->location_address }}</div>
                    <div class="col-md-4"><strong>Incident Date:</strong> {{ optional($incident->incident_calendar_date)->format('Y-m-d') }}</div>
                    <div class="col-md-4"><strong>Date Submitted:</strong> {{ optional($incident->date_submitted)->format('Y-m-d') }}</div>
                    <div class="col-md-4"><strong>Incident Time:</strong> {{ $incident->incident_time }}</div>
                    <div class="col-md-6"><strong>Reporter Name:</strong> {{ $incident->reporter_name }}</div>
                    <div class="col-md-6"><strong>Manager(s) on Duty:</strong> {{ $incident->managers_on_duty }}</div>
                    <div class="col-md-6"><strong>Manager Phone:</strong> {{ $incident->manager_phone ?: 'N/A' }}</div>
                    <div class="col-md-6"><strong>Police Report #:</strong> {{ $incident->police_report_number ?: 'N/A' }}</div>
                    <div class="col-md-12 mt-2"><strong>Police Officers/Badges:</strong><br>{{ $incident->police_officers_badges ?: 'N/A' }}</div>
                    <div class="col-md-12 mt-2"><strong>Involved / Injured Persons:</strong><br>{{ $incident->involved_injured_persons }}</div>
                    <div class="col-md-12 mt-2"><strong>Detailed Incident Description:</strong><br>{{ $incident->incident_description }}</div>
                    <div class="col-md-12 mt-2"><strong>Witnesses Statement:</strong><br>{{ $incident->witnesses_statement }}</div>
                    <div class="col-md-8 mt-2"><strong>Camera Angles:</strong><br>{{ $incident->camera_angles }}</div>
                    <div class="col-md-4 mt-2"><strong>Camera Timestamp:</strong><br>{{ $incident->camera_timestamp }}</div>
                    <div class="col-md-12 mt-2"><strong>Cast Members Involved:</strong><br>{{ $incident->cast_members_involved }}</div>
                    <div class="col-md-12 mt-2"><strong>Additional Media Notes:</strong><br>{{ $incident->additional_media_notes ?: 'N/A' }}</div>
                    <div class="col-md-6 mt-2"><strong>E-signature Accepted:</strong> {{ $incident->accepted_esignature ? 'Yes' : 'No' }}</div>
                    <div class="col-md-6 mt-2"><strong>E-signature Opt-out:</strong> {{ $incident->opted_out_esignature ? 'Yes' : 'No' }}</div>
                    <div class="col-md-12 mt-2"><strong>Digital Signature:</strong> {{ $incident->digital_signature_name }}</div>
                </div>
            </div>

            <div class="card bg-primary text-white p-3 mb-3">
                <h5>Incident Attachments</h5>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>File</th>
                            <th>Size (KB)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($incident->attachments as $file)
                            <tr>
                                <td>{{ ucwords(str_replace('_', ' ', $file->attachment_type)) }}</td>
                                <td><a href="{{ asset('uploads/' . $file->file_path) }}" target="_blank" class="text-warning">{{ $file->original_name }}</a></td>
                                <td>{{ number_format(((int) $file->file_size) / 1024, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">No files attached.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <style>
                .modal-admin { --bs-modal-bg: #121726; --bs-modal-border-color: rgba(255,255,255,0.1); }
                .modal-admin .modal-header, .modal-admin .modal-footer { background: rgba(11,14,26,0.8); border-color: rgba(255,255,255,0.1); }
                .modal-admin .modal-header { border-bottom: 1px solid rgba(255,255,255,0.1); }
                .modal-admin .modal-footer { border-top: 1px solid rgba(255,255,255,0.1); }
                .modal-admin .modal-title { color: #e8eaf6; font-weight: 600; }
                .modal-admin .btn-close { filter: brightness(0) invert(1); }
            </style>

            <div class="card bg-primary text-white p-3">
                <h5>Witness Reports ({{ $incident->witnessReports->count() }})</h5>
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Submitted Via</th>
                            <th>Date Submitted</th>
                            <th>Attachment</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($incident->witnessReports as $index => $witness)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $witness->full_name }}</td>
                                <td>{{ $witness->participant_type }}</td>
                                <td>{{ ucwords(str_replace('_', ' ', $witness->submitted_via)) }}</td>
                                <td>{{ optional($witness->created_at)->format('Y-m-d H:i') }}</td>
                                <td>
                                    @if($witness->attachments->isNotEmpty())
                                        @foreach($witness->attachments as $attachment)
                                            <a href="{{ asset('uploads/' . $attachment->file_path) }}" target="_blank" class="text-warning d-block">{{ $attachment->original_name }}</a>
                                        @endforeach
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-light"
                                        data-bs-toggle="modal"
                                        data-bs-target="#witnessDetailModal{{ $witness->id }}">
                                        View Full Report
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="7">
                                    <strong>Statement:</strong> {{ $witness->detailed_statement }}<br>
                                    <strong>Signature:</strong> {{ $witness->digital_signature_name }}
                                </td>
                            </tr>

                            <div class="modal fade modal-admin" id="witnessDetailModal{{ $witness->id }}" tabindex="-1" aria-labelledby="witnessDetailModalLabel{{ $witness->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                    <div class="modal-content" style="background: #121726; color: #e8eaf6;">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="witnessDetailModalLabel{{ $witness->id }}">Witness Full Report - {{ $witness->full_name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row g-2">
                                                <div class="col-md-6"><strong>Full Name:</strong> {{ $witness->full_name }}</div>
                                                <div class="col-md-6"><strong>Participant Type:</strong> {{ $witness->participant_type }}</div>
                                                <div class="col-md-6"><strong>Phone Number:</strong> {{ $witness->phone_number }}</div>
                                                <div class="col-md-6"><strong>Address:</strong> {{ $witness->address }}</div>
                                                <div class="col-md-6"><strong>Legal Name of Location:</strong> {{ $witness->location_legal_name }}</div>
                                                <div class="col-md-6"><strong>DBA of Location:</strong> {{ $witness->location_dba_name }}</div>
                                                <div class="col-md-12"><strong>Location Address:</strong> {{ $witness->location_address }}</div>
                                                <div class="col-md-4"><strong>Incident Date:</strong> {{ optional($witness->incident_calendar_date)->format('Y-m-d') }}</div>
                                                <div class="col-md-4"><strong>Date Submitted:</strong> {{ optional($witness->date_submitted)->format('Y-m-d') }}</div>
                                                <div class="col-md-4"><strong>Incident Time:</strong> {{ $witness->incident_time }}</div>
                                                <div class="col-md-6"><strong>Type of Incident:</strong> {{ $witness->incident_type }}</div>
                                                <div class="col-md-6"><strong>Submitted Via:</strong> {{ ucwords(str_replace('_', ' ', $witness->submitted_via)) }}</div>
                                                <div class="col-md-6"><strong>E-sign Accepted:</strong> {{ $witness->accepted_esignature ? 'Yes' : 'No' }}</div>
                                                <div class="col-md-6"><strong>E-sign Opt-out:</strong> {{ $witness->opted_out_esignature ? 'Yes' : 'No' }}</div>
                                                <div class="col-md-12"><strong>Digital Signature:</strong> {{ $witness->digital_signature_name }}</div>
                                                <div class="col-md-12 mt-2">
                                                    <strong>Detailed Statement:</strong>
                                                    <div class="mt-1 p-2 rounded" style="background: rgba(255,255,255,0.06);">
                                                        {{ $witness->detailed_statement }}
                                                    </div>
                                                </div>
                                                <div class="col-md-12 mt-2">
                                                    <strong>Attachments:</strong>
                                                    @if($witness->attachments->isNotEmpty())
                                                        @foreach($witness->attachments as $attachment)
                                                            <div>
                                                                <a href="{{ asset('uploads/' . $attachment->file_path) }}" target="_blank" class="text-warning">{{ $attachment->original_name }}</a>
                                                                <small class="text-muted">({{ number_format(((int) $attachment->file_size) / 1024, 2) }} KB)</small>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <div>N/A</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <a href="{{ route('admin.incident.witness.download', $witness->id) }}" class="btn btn-warning">
                                                <i class="bx bx-download"></i> Download
                                            </a>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="7">No witness reports submitted yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card bg-primary text-white p-3 mt-3">
                <h5>Immutable Audit Trail ({{ $incident->auditLogs->count() }})</h5>
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
                                <td>
                                    @if(!empty($log->change_summary))
                                        <pre class="mb-0 text-white" style="white-space: pre-wrap;">{{ json_encode($log->change_summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">No audit entries recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
