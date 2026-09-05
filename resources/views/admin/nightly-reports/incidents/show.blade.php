@extends('admin.nightly-reports.layout')

@section('content')
@php
  $incDate = $incident->incident_calendar_date ?? $incident->incident_date;
  $incTime = $incident->formatted_incident_time ?? $incident->incident_time ?? $incident->time_of_incident;
  $locationName = $incident->website->name ?? $incident->location_dba_name ?? $incident->location_legal_name ?? $incident->location->name ?? 'Venue';
  $reporterName = $incident->reporter_name ?? $incident->submitter_name ?? 'Staff';
  $incType = $incident->incident_type ?? $incident->report_type_field ?? 'Security Incident';
  $witnesses = $incident->witnessReports ?? $incident->witnessStatements ?? collect();
  $involvedPersons = $incident->involved_injured_persons ?? $incident->involved_persons;
  $incidentTz = $incident->website?->resolved_timezone ?? 'America/Los_Angeles';

  $statusClasses = [
      'open' => 'bg-danger',
      'under_review' => 'bg-info text-white',
      'legal_hold' => 'bg-warning text-dark',
      'closed' => 'bg-success',
      'resolved' => 'bg-success',
  ];
@endphp

<div class="container-fluid p-0">
  <!-- Top Header & Action Buttons -->
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
      <a href="{{ route('admin.nightly-reports.incidents.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
        <i class="fas fa-arrow-left me-1"></i> Back to Incident Register
      </a>
      <h4 class="text-white fw-bold mb-0">
        Incident File #{{ $incident->id }}: {{ $incType }} ({{ $locationName }})
      </h4>
      <p class="text-muted small mb-0">
        Date: {{ $incDate ? \Carbon\Carbon::parse($incDate)->format('l, F j, Y') : '—' }} at {{ $incTime }} • Reported by {{ $reporterName }}
      </p>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
      <a href="{{ route('admin.incident.witness.create', $incident->id) }}" class="btn btn-sm btn-primary">
        <i class="fas fa-user-plus me-1"></i> Add Witness
      </a>
      @if(Route::has('admin.incident.export'))
      <a href="{{ route('admin.incident.export', $incident->id) }}" class="btn btn-sm btn-outline-info">
        <i class="fas fa-file-pdf me-1"></i> Export PDF
      </a>
      @endif
    </div>
  </div>

  <!-- Status Bar Card -->
  <div class="card mb-4 p-3" style="background: var(--nr-surface-1); border: 1px solid var(--nr-border);">
    <div class="row g-3 align-items-end">
      <div class="col-md-4">
        <label class="small text-muted text-uppercase fw-bold mb-1">Current Status</label>
        <div>
          <span class="badge {{ $statusClasses[$incident->status] ?? 'bg-secondary' }} fs-6">
            {{ ucwords(str_replace('_', ' ', $incident->status ?? 'Open')) }}
          </span>
        </div>
        <small class="d-block mt-2 text-white-50">
          Last changed: {{ $incident->status_changed_at ? $incident->status_changed_at->timezone($incidentTz)->format('Y-m-d g:i A') : 'N/A' }}
          by {{ optional($incident->statusChangedBy)->name ?: 'System' }}
        </small>
      </div>
      <div class="col-md-8">
        <form method="POST" action="{{ route('admin.nightly-reports.incidents.status', $incident->id) }}" class="row g-2">
          @csrf
          <div class="col-md-4">
            <label class="small text-muted text-uppercase fw-bold mb-1">Update Status</label>
            <select name="status" class="form-select form-select-sm" required>
              <option value="open" {{ in_array($incident->status, ['open', 'pending'], true) ? 'selected' : '' }}>Open</option>
              <option value="under_review" {{ $incident->status === 'under_review' ? 'selected' : '' }}>Under Review</option>
              <option value="legal_hold" {{ $incident->status === 'legal_hold' ? 'selected' : '' }}>Legal Hold</option>
              <option value="closed" {{ in_array($incident->status, ['closed', 'resolved'], true) ? 'selected' : '' }}>Closed / Resolved</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="small text-muted text-uppercase fw-bold mb-1">Status Change Note (optional)</label>
            <input type="text" name="status_note" class="form-control form-control-sm" placeholder="Reason for status change">
          </div>
          <div class="col-md-2 d-grid">
            <label class="small text-muted text-uppercase fw-bold mb-1 d-none d-md-block">&nbsp;</label>
            <button type="submit" class="btn btn-sm btn-gold">Update</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Share Witness Link Alert Banner -->
  @if(!empty($incident->public_witness_token))
  <div class="alert alert-warning d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 mb-4" style="background: rgba(245, 158, 11, 0.15); border-color: rgba(245, 158, 11, 0.3); color: #fbbf24;">
    <div class="flex-grow-1">
      <strong class="text-white me-2"><i class="fas fa-qrcode me-1"></i> Share Witness Form Link:</strong>
      <input class="form-control form-control-sm mt-2 text-white bg-dark border-secondary" readonly value="{{ route('incident.witness.form', $incident->public_witness_token) }}" onclick="this.select();">
    </div>
    <div class="text-nowrap mt-2 mt-md-0">
      <a href="{{ route('incident.witness.form', $incident->public_witness_token) }}" target="_blank" class="btn btn-sm btn-gold">
        <i class="fas fa-external-link-alt me-1"></i> Open Witness Form
      </a>
    </div>
  </div>
  @endif

  <div class="row g-4">
    <!-- Main Content Left Column -->
    <div class="col-lg-8">
      <!-- Chronological Narrative -->
      <div class="card mb-4">
        <div class="card-header"><h5 class="card-title mb-0 text-white">Chronological Narrative</h5></div>
        <div class="card-body">
          <div class="p-3 rounded text-white" style="background: var(--nr-surface-2); border: 1px solid var(--nr-border); line-height: 1.6;">
            {{ $incident->incident_description }}
          </div>
        </div>
      </div>

      <!-- Involved Parties & Witness Statements -->
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0 text-white">Involved Parties & Witness Statements ({{ $witnesses->count() }})</h5>
          <div class="d-flex gap-2">
            <a href="{{ route('admin.incident.witness.create', $incident->id) }}" class="btn btn-sm btn-primary">
              <i class="fas fa-plus me-1"></i> Add Witness
            </a>
            @if(!empty($incident->public_witness_token))
            <a href="{{ route('incident.witness.form', $incident->public_witness_token) }}" target="_blank" class="btn btn-sm btn-outline-warning">
              <i class="fas fa-qrcode me-1"></i> Public Form
            </a>
            @endif
          </div>
        </div>
        <div class="card-body">
          @if($involvedPersons)
          <div class="mb-4">
            <label class="small text-muted text-uppercase fw-bold mb-1">Involved Persons & Descriptions</label>
            <div class="p-3 rounded text-white" style="background: var(--nr-surface-2); border: 1px solid var(--nr-border);">
              {{ $involvedPersons }}
            </div>
          </div>
          @endif

          <label class="small text-muted text-uppercase fw-bold mb-2">Attached Witness Statements</label>
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="background: var(--nr-surface-2); border: 1px solid var(--nr-border);">
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
                @forelse($witnesses as $index => $w)
                <tr>
                  <td>{{ $index + 1 }}</td>
                  <td class="fw-bold text-white">{{ $w->full_name ?? $w->witness_name ?? 'Witness' }}</td>
                  <td><span class="badge bg-secondary">{{ $w->participant_type ?? $w->witness_type ?? 'Witness' }}</span></td>
                  <td>{{ ucwords(str_replace('_', ' ', $w->submitted_via ?? 'admin_panel')) }}</td>
                  <td>{{ optional($w->created_at)->timezone($incidentTz)->format('Y-m-d H:i') }} PT</td>
                  <td>
                    @if(isset($w->attachments) && $w->attachments->isNotEmpty())
                      @foreach($w->attachments as $attachment)
                        <a href="{{ asset('uploads/' . $attachment->file_path) }}" target="_blank" class="text-warning d-block small">{{ $attachment->original_name }}</a>
                      @endforeach
                    @else
                      <span class="text-muted small">N/A</span>
                    @endif
                  </td>
                  <td>
                    <button type="button" class="btn btn-sm btn-outline-light" data-bs-toggle="modal" data-bs-target="#witnessDetailModal{{ $w->id }}">
                      View Full Report
                    </button>
                  </td>
                </tr>
                <tr>
                  <td colspan="7" class="border-bottom text-white-50 small" style="background: rgba(0,0,0,0.2);">
                    <strong>Statement:</strong> {{ $w->detailed_statement ?? $w->statement_text }}<br>
                    <strong>Signature:</strong> {{ $w->digital_signature_name ?? 'N/A' }}
                  </td>
                </tr>

                <!-- Witness Modal -->
                <div class="modal fade" id="witnessDetailModal{{ $w->id }}" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog modal-lg modal-dialog-scrollable">
                    <div class="modal-content" style="background: #121726; color: #e8eaf6; border: 1px solid var(--nr-border);">
                      <div class="modal-header" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                        <h5 class="modal-title text-white">Witness Full Report - {{ $w->full_name }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <div class="row g-3">
                          <div class="col-md-6"><strong>Full Name:</strong> {{ $w->full_name }}</div>
                          <div class="col-md-6"><strong>Participant Type:</strong> {{ $w->participant_type }}</div>
                          <div class="col-md-6"><strong>Phone Number:</strong> {{ $w->phone_number }}</div>
                          <div class="col-md-6"><strong>Address:</strong> {{ $w->address }}</div>
                          <div class="col-md-6"><strong>Legal Name:</strong> {{ $w->location_legal_name }}</div>
                          <div class="col-md-6"><strong>DBA:</strong> {{ $w->location_dba_name }}</div>
                          <div class="col-md-12"><strong>Location Address:</strong> {{ $w->location_address }}</div>
                          <div class="col-md-4"><strong>Incident Date:</strong> {{ optional($w->incident_calendar_date)->format('Y-m-d') }}</div>
                          <div class="col-md-4"><strong>Date Submitted:</strong> {{ optional($w->date_submitted)->format('Y-m-d') }}</div>
                          <div class="col-md-4"><strong>Incident Time:</strong> {{ $w->formatted_incident_time ?? $w->incident_time }}</div>
                          <div class="col-md-6"><strong>Type of Incident:</strong> {{ $w->incident_type }}</div>
                          <div class="col-md-6"><strong>Submitted Via:</strong> {{ ucwords(str_replace('_', ' ', $w->submitted_via ?? 'admin_panel')) }}</div>
                          <div class="col-md-6"><strong>E-sign Accepted:</strong> {{ $w->accepted_esignature ? 'Yes' : 'No' }}</div>
                          <div class="col-md-6"><strong>E-sign Opt-out:</strong> {{ $w->opted_out_esignature ? 'Yes' : 'No' }}</div>
                          <div class="col-md-12"><strong>Digital Signature:</strong> {{ $w->digital_signature_name }}</div>
                          <div class="col-md-12">
                            <strong>Detailed Statement:</strong>
                            <div class="mt-1 p-3 rounded" style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1);">
                              {{ $w->detailed_statement }}
                            </div>
                          </div>
                          <div class="col-md-12">
                            <strong>Attachments:</strong>
                            @if(isset($w->attachments) && $w->attachments->isNotEmpty())
                              @foreach($w->attachments as $attachment)
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
                      <div class="modal-footer" style="border-top: 1px solid rgba(255,255,255,0.1);">
                        @if(Route::has('admin.incident.witness.download'))
                        <a href="{{ route('admin.incident.witness.download', $w->id) }}" class="btn btn-warning">
                          <i class="fas fa-download me-1"></i> Download PDF
                        </a>
                        @endif
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                      </div>
                    </div>
                  </div>
                </div>
                @empty
                <tr>
                  <td colspan="7" class="text-muted text-center py-4">No witness statements linked to this incident file.</td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Incident Attachments Section -->
      <div class="card mb-4">
        <div class="card-header"><h5 class="card-title mb-0 text-white">Incident Attachments</h5></div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead>
                <tr>
                  <th>Type</th>
                  <th>File Name</th>
                  <th>Size (KB)</th>
                </tr>
              </thead>
              <tbody>
                @forelse($incident->attachments as $file)
                <tr>
                  <td>{{ ucwords(str_replace('_', ' ', $file->attachment_type)) }}</td>
                  <td><a href="{{ asset('uploads/' . $file->file_path) }}" target="_blank" class="text-warning fw-bold">{{ $file->original_name }}</a></td>
                  <td>{{ number_format(((int) $file->file_size) / 1024, 2) }}</td>
                </tr>
                @empty
                <tr>
                  <td colspan="3" class="text-muted text-center py-3">No main files attached to this incident.</td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Right Sidebar Metadata Cards -->
    <div class="col-lg-4">
      <!-- Police & Law Enforcement Card -->
      <div class="card mb-4">
        <div class="card-header"><h5 class="card-title mb-0 text-white"><i class="fas fa-building-shield me-2 text-info"></i> Police & Law Enforcement</h5></div>
        <div class="card-body">
          <table class="table table-sm table-borderless mb-0">
            <tr><td class="text-muted">Police Report #:</td><td class="text-end text-white fw-bold">{{ $incident->police_report_number ?: 'None' }}</td></tr>
            <tr><td class="text-muted">Officers / Badges:</td><td class="text-end text-white">{{ $incident->police_officers_badges ?: 'None' }}</td></tr>
            <tr><td class="text-muted">Duty Managers:</td><td class="text-end text-white">{{ $incident->managers_on_duty ?: '—' }}</td></tr>
            <tr><td class="text-muted">Manager Phone:</td><td class="text-end text-white">{{ $incident->manager_phone ?: '—' }}</td></tr>
          </table>
        </div>
      </div>

      <!-- Surveillance Video Logs Card -->
      <div class="card mb-4">
        <div class="card-header"><h5 class="card-title mb-0 text-white"><i class="fas fa-video me-2 text-warning"></i> Surveillance Video Logs</h5></div>
        <div class="card-body">
          <table class="table table-sm table-borderless mb-0">
            <tr><td class="text-muted">Camera Angles:</td><td class="text-end text-white">{{ $incident->camera_angles ?: 'Not specified' }}</td></tr>
            <tr><td class="text-muted">Footage Timestamp:</td><td class="text-end text-warning fw-bold">{{ $incident->camera_timestamp ?: '—' }}</td></tr>
          </table>
        </div>
      </div>

      <!-- Additional Details & Signatures Card -->
      <div class="card mb-4">
        <div class="card-header"><h5 class="card-title mb-0 text-white"><i class="fas fa-file-contract me-2 text-gold"></i> Venue & Signature Info</h5></div>
        <div class="card-body">
          <table class="table table-sm table-borderless mb-0">
            <tr><td class="text-muted">Legal Name:</td><td class="text-end text-white">{{ $incident->location_legal_name ?: '—' }}</td></tr>
            <tr><td class="text-muted">DBA Name:</td><td class="text-end text-white">{{ $incident->location_dba_name ?: '—' }}</td></tr>
            <tr><td class="text-muted">Location Address:</td><td class="text-end text-white small">{{ $incident->location_address ?: '—' }}</td></tr>
            <tr><td class="text-muted">Date Submitted:</td><td class="text-end text-white">{{ optional($incident->date_submitted)->format('Y-m-d') }}</td></tr>
            <tr><td class="text-muted">Reporter:</td><td class="text-end text-white">{{ $incident->reporter_name ?: '—' }}</td></tr>
            <tr><td class="text-muted">Cast Involved:</td><td class="text-end text-white">{{ $incident->cast_members_involved ?: 'None' }}</td></tr>
            <tr><td class="text-muted">E-Sign Accepted:</td><td class="text-end text-white">{{ $incident->accepted_esignature ? 'Yes' : 'No' }}</td></tr>
            <tr><td class="text-muted">E-Sign Opt-Out:</td><td class="text-end text-white">{{ $incident->opted_out_esignature ? 'Yes' : 'No' }}</td></tr>
            <tr><td class="text-muted">Digital Signature:</td><td class="text-end text-gold fw-bold">{{ $incident->digital_signature_name ?: '—' }}</td></tr>
          </table>
          @if($incident->additional_media_notes)
          <div class="mt-3 pt-3 border-top border-secondary">
            <small class="text-muted d-block text-uppercase fw-bold">Additional Media Notes</small>
            <div class="small text-white-50 mt-1">{{ $incident->additional_media_notes }}</div>
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>

  <!-- Immutable Audit Trail -->
  <div class="card mt-2 mb-4">
    <div class="card-header"><h5 class="card-title mb-0 text-white"><i class="fas fa-history me-2 text-info"></i> Immutable Audit Trail ({{ $incident->auditLogs->count() }})</h5></div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead>
            <tr>
              <th>When</th>
              <th>User</th>
              <th>Action</th>
              <th>IP Address</th>
              <th>Details</th>
            </tr>
          </thead>
          <tbody>
            @forelse($incident->auditLogs as $log)
            <tr>
              <td class="text-nowrap">{{ optional($log->created_at)->timezone($incidentTz)->format('Y-m-d H:i:s') }} PT</td>
              <td class="fw-bold text-white">{{ optional($log->user)->name ?: 'Public/Guest' }}</td>
              <td><span class="badge bg-secondary">{{ ucwords(str_replace('_', ' ', $log->action)) }}</span></td>
              <td><small class="text-white-50">{{ $log->ip_address ?: 'N/A' }}</small></td>
              <td>
                @if(!empty($log->change_summary))
                  <pre class="mb-0 text-white-50 small" style="white-space: pre-wrap; font-family: monospace;">{{ json_encode($log->change_summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                @else
                  <span class="text-muted small">N/A</span>
                @endif
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="5" class="text-muted text-center py-4">No audit trail logs recorded yet.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
