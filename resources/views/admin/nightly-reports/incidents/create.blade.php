@extends('admin.nightly-reports.layout')

@section('content')
<div class="container-fluid p-0">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h4 class="text-white mb-1 fw-bold"><i class="fas fa-shield-alt text-danger me-2"></i> Log Security Incident</h4>
      <p class="text-muted small mb-0">Legal risk management, police reports, surveillance footage timestamps, and witness logs.</p>
    </div>
    <a href="{{ route('admin.nightly-reports.incidents.index') }}" class="btn btn-sm btn-outline-secondary">
      <i class="fas fa-arrow-left me-1"></i> Back to Incident Reports
    </a>
  </div>

  @if($errors->any())
    <div class="alert alert-danger mb-4">
      <ul class="mb-0">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('admin.nightly-reports.incidents.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="mb-4 pb-3 border-bottom border-secondary border-opacity-25">
          <h6 class="text-gold text-uppercase fw-bold mb-3"><i class="fas fa-map-marker-alt me-2"></i> 1. Location & Timing Overview</h6>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label text-white">Select Location / Venue *</label>
              <select name="website_id" id="nrLocationSelect" class="form-select" required>
                <option value="">Select Location...</option>
                @foreach($websites as $web)
                  <option value="{{ $web->id }}" data-legal="{{ $web->name }}" data-dba="{{ $web->name }}" {{ old('website_id', $selectedWebsiteId ?? '') == $web->id ? 'selected' : '' }}>
                    {{ $web->name }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label text-white">Incident Type *</label>
              <select name="incident_type" class="form-select" required>
                <option value="Physical Altercation" {{ old('incident_type') === 'Physical Altercation' ? 'selected' : '' }}>Physical Altercation</option>
                <option value="Patron Ejection" {{ old('incident_type') === 'Patron Ejection' ? 'selected' : '' }}>Patron Ejection</option>
                <option value="Police / Law Enforcement Called" {{ old('incident_type') === 'Police / Law Enforcement Called' ? 'selected' : '' }}>Police / Law Enforcement Called</option>
                <option value="Medical Emergency / EMS" {{ old('incident_type') === 'Medical Emergency / EMS' ? 'selected' : '' }}>Medical Emergency / EMS</option>
                <option value="Property Damage" {{ old('incident_type') === 'Property Damage' ? 'selected' : '' }}>Property Damage</option>
                <option value="Theft / Lost Property" {{ old('incident_type') === 'Theft / Lost Property' ? 'selected' : '' }}>Theft / Lost Property</option>
                <option value="Other Safety Incident" {{ old('incident_type') === 'Other Safety Incident' ? 'selected' : '' }}>Other Safety Incident</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label text-white">Legal Name of the Location * <i class="fas fa-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="The officially registered business name of the venue."></i></label>
              <input type="text" name="location_legal_name" id="location_legal_name" class="form-control" value="{{ old('location_legal_name') }}" required placeholder="e.g. Barely Legal New Orleans LLC" />
            </div>
            <div class="col-md-6">
              <label class="form-label text-white">The DBA of the Location * <i class="fas fa-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="The 'Doing Business As' trade name if different from the legal name."></i></label>
              <input type="text" name="location_dba_name" id="location_dba_name" class="form-control" value="{{ old('location_dba_name') }}" required placeholder="e.g. Barely Legal New Orleans" />
            </div>
            <div class="col-md-12">
              <label class="form-label text-white">Address of Location * <i class="fas fa-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="Full street address where the incident occurred."></i></label>
              <input type="text" name="location_address" class="form-control" value="{{ old('location_address') }}" required placeholder="Street, City, State, ZIP" />
            </div>
            <div class="col-md-4">
              <label class="form-label text-white">Calendar Date of incident * <i class="fas fa-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="The exact date the incident took place."></i></label>
              <input type="date" name="incident_calendar_date" class="form-control" value="{{ old('incident_calendar_date', date('Y-m-d')) }}" required />
            </div>
            <div class="col-md-4">
              <label class="form-label text-white">Date Submitted * <i class="fas fa-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="The date this incident report is being filed."></i></label>
              <input type="date" name="date_submitted" class="form-control" value="{{ old('date_submitted', date('Y-m-d')) }}" required />
            </div>
            <div class="col-md-4">
              <label class="form-label text-white">Time of incident * <i class="fas fa-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="The time the incident occurred."></i></label>
              <input type="time" name="incident_time" class="form-control" value="{{ old('incident_time') }}" required />
            </div>
          </div>
        </div>

        <div class="mb-4 pb-3 border-bottom border-secondary border-opacity-25">
          <h6 class="text-gold text-uppercase fw-bold mb-3"><i class="fas fa-balance-scale me-2"></i> 2. Police & Law Enforcement Details</h6>
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label text-white">Police Report Number <i class="fas fa-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="Reference number of any associated police report."></i></label>
              <input type="text" name="police_report_number" class="form-control" value="{{ old('police_report_number') }}" placeholder="e.g. CAD-2026-00912" />
            </div>
            <div class="col-md-8">
              <label class="form-label text-white">Police Officers and Badge #'s <i class="fas fa-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="Names and badge numbers of responding police officers."></i></label>
              <input type="text" name="police_officers_badges" class="form-control" value="{{ old('police_officers_badges') }}" placeholder="e.g. Officer Miller #4412, Officer Davis #1092" />
            </div>
            <div class="col-md-12">
              <label class="form-label text-white">Attach Police Report (max 4MB) <i class="fas fa-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="Upload a scanned copy of the police report."></i></label>
              <input type="file" name="police_report_file" class="form-control" />
            </div>
          </div>
        </div>

        <div class="mb-4 pb-3 border-bottom border-secondary border-opacity-25">
          <h6 class="text-gold text-uppercase fw-bold mb-3"><i class="fas fa-user-shield me-2"></i> 3. Reporter & Management Information</h6>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label text-white">Your Name * <i class="fas fa-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="The name of the person filing this incident report."></i></label>
              <input type="text" name="reporter_name" class="form-control" value="{{ old('reporter_name', auth()->user()?->name) }}" required placeholder="Full Name" />
            </div>
            <div class="col-md-3">
              <label class="form-label text-white">Manager(s) on Duty * <i class="fas fa-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="Names of all managers on duty at the time of the incident."></i></label>
              <input type="text" name="managers_on_duty" class="form-control" value="{{ old('managers_on_duty') }}" required placeholder="Manager Names" />
            </div>
            <div class="col-md-3">
              <label class="form-label text-white">Manager's phone number <i class="fas fa-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="Contact phone number for the manager on duty."></i></label>
              <input type="text" name="manager_phone" class="form-control" value="{{ old('manager_phone') }}" placeholder="(555) 000-0000" />
            </div>
          </div>
        </div>

        <div class="mb-4 pb-3 border-bottom border-secondary border-opacity-25">
          <h6 class="text-gold text-uppercase fw-bold mb-3"><i class="fas fa-file-alt me-2"></i> 4. Detailed Narrative & Witnesses</h6>
          <div class="row g-3">
            <div class="col-md-12">
              <label class="form-label text-white">Involved / Injured Persons * <i class="fas fa-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="Names and details of all persons involved or injured."></i></label>
              <textarea name="involved_injured_persons" class="form-control" rows="3" required placeholder="Full names, physical descriptions, injuries sustained...">{{ old('involved_injured_persons') }}</textarea>
            </div>
            <div class="col-md-12">
              <label class="form-label text-white">Detailed description of the incident * <i class="fas fa-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="A thorough written account of exactly what happened."></i></label>
              <textarea name="incident_description" class="form-control" rows="5" required placeholder="State facts chronologically: what triggered the event, security response, actions taken, and final outcome...">{{ old('incident_description') }}</textarea>
            </div>
            <div class="col-md-12">
              <label class="form-label text-white">Witnesses statement * <i class="fas fa-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="Written statements from any witnesses present."></i></label>
              <textarea name="witnesses_statement" class="form-control" rows="4" required placeholder="Eyewitness testimony, staff/bystander statements...">{{ old('witnesses_statement') }}</textarea>
            </div>
            <div class="col-md-12">
              <label class="form-label text-white">Load Witness Report Here (up to 10 files, 4MB each) <i class="fas fa-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="Upload scanned witness statement documents."></i></label>
              <input type="file" name="witness_report_files[]" class="form-control" multiple />
            </div>
          </div>
        </div>

        <div class="mb-4 pb-3 border-bottom border-secondary border-opacity-25">
          <h6 class="text-gold text-uppercase fw-bold mb-3"><i class="fas fa-video me-2"></i> 5. Surveillance & Additional Media</h6>
          <div class="row g-3">
            <div class="col-md-8">
              <label class="form-label text-white">Which cameras (angles) will show the incident? * <i class="fas fa-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="Specify which camera views and angles cover the incident area."></i></label>
              <textarea name="camera_angles" class="form-control" rows="2" required placeholder="e.g. CAM-04 (Main Entry), CAM-11 (Stage Area)">{{ old('camera_angles') }}</textarea>
            </div>
            <div class="col-md-4">
              <label class="form-label text-white">Timestamp of the camera * <i class="fas fa-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="The time code on the camera footage corresponding to when the incident occurred."></i></label>
              <input type="text" name="camera_timestamp" class="form-control" value="{{ old('camera_timestamp') }}" required placeholder="e.g. 01:42:00 - 01:51:30" />
            </div>
            <div class="col-md-12">
              <label class="form-label text-white">Cast member involved or on duty? * <i class="fas fa-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="Names of any venue staff directly involved or on duty."></i></label>
              <textarea name="cast_members_involved" class="form-control" rows="3" required placeholder="Names of floor staff, dancers, bartenders, or door security on duty...">{{ old('cast_members_involved') }}</textarea>
            </div>
            <div class="col-md-12">
              <label class="form-label text-white">Any additional photos or recordings notes <i class="fas fa-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="Any extra photos, footage notes, or relevant context."></i></label>
              <textarea name="additional_media_notes" class="form-control" rows="3" placeholder="Additional observations, phone recording notes...">{{ old('additional_media_notes') }}</textarea>
            </div>
            <div class="col-md-12">
              <label class="form-label text-white">Additional Cell Phone Photos or Footage (up to 5 files, 4MB each) <i class="fas fa-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="Upload supporting photos or video files from mobile devices."></i></label>
              <input type="file" name="additional_media_files[]" class="form-control" multiple />
            </div>
          </div>
        </div>

        <div class="mb-4">
          <h6 class="text-gold text-uppercase fw-bold mb-3"><i class="fas fa-pen-nib me-2"></i> 6. Verification & E-Signature</h6>
          <div class="row g-3">
            <div class="col-md-12">
              <label class="form-label d-block text-white mb-2">E-signature confirmation * <i class="fas fa-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="Confirm that all information provided in this report is accurate and true."></i></label>
              <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="signature_choice" id="sig_accept" value="accept" {{ old('signature_choice', 'accept') === 'accept' ? 'checked' : '' }} required />
                <label class="form-check-label text-white fw-bold" for="sig_accept">
                  <i class="fas fa-check-circle text-success me-1"></i> I Accept
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="signature_choice" id="sig_opt_out" value="opt_out" {{ old('signature_choice') === 'opt_out' ? 'checked' : '' }} required />
                <label class="form-check-label text-white fw-bold" for="sig_opt_out">
                  <i class="fas fa-times-circle text-warning me-1"></i> I opt out and will physically sign.
                </label>
              </div>
            </div>
            <div class="col-md-12">
              <label class="form-label text-white">Your Digital Signature (full name) * <i class="fas fa-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="Type your full legal name as your digital signature."></i></label>
              <input type="text" name="digital_signature_name" class="form-control" value="{{ old('digital_signature_name', auth()->user()?->name) }}" required placeholder="Type full name to sign" />
            </div>
          </div>
        </div>

        <div class="d-flex justify-content-end gap-2 pt-3">
          <a href="{{ route('admin.nightly-reports.incidents.index') }}" class="btn btn-outline-secondary">Cancel</a>
          <button type="submit" class="btn btn-gold fw-bold px-4">
            <i class="fas fa-paper-plane me-1"></i> Submit Incident Report
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const select = document.getElementById('nrLocationSelect');
  const legalInput = document.getElementById('location_legal_name');
  const dbaInput = document.getElementById('location_dba_name');

  if (select) {
    select.addEventListener('change', function() {
      const opt = this.options[this.selectedIndex];
      if (opt && opt.value) {
        if (!legalInput.value) legalInput.value = opt.getAttribute('data-legal') || '';
        if (!dbaInput.value) dbaInput.value = opt.getAttribute('data-dba') || '';
      }
    });
  }
});
</script>
@endsection
