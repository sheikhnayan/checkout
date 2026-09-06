<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <title>Security Incident Log — The Nightly Reports</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="{{ asset('user/assets/vendor/css/core.css') }}" />
  <style>
    :root { --bg: #07111f; --surface: #0d1a2e; --surface-2: #142238; --border: rgba(255, 255, 255, 0.08); --gold: #c9a84c; --gold-glow: rgba(201, 168, 76, 0.2); }
    body { background-color: var(--bg); color: #e2e8f0; font-family: 'DM Sans', sans-serif; min-height: 100vh; padding: 1.5rem 0.75rem; }
    .form-container { max-width: 820px; margin: 0 auto; }
    .card-luxury { background: var(--surface); border: 1px solid var(--border); border-radius: 14px; padding: 1.75rem; margin-bottom: 1.5rem; }
    .section-title { font-size: 0.95rem; font-weight: 700; color: #fff; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 8px; }
    .section-title i { color: #f43f5e; }
    .form-label, label, .form-check-label { font-size: 0.82rem; font-weight: 600; color: #ffffff !important; margin-bottom: 0.35rem; opacity: 1 !important; }
    .form-control, .form-select, textarea { background-color: var(--surface-2) !important; border: 1px solid var(--border) !important; color: #fff !important; border-radius: 8px; }
    .form-control:focus, .form-select:focus, textarea:focus { border-color: var(--gold) !important; box-shadow: 0 0 0 0.2rem var(--gold-glow) !important; }
    .btn-submit { background: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%); border: none; color: #fff; font-weight: 700; font-size: 1rem; border-radius: 10px; padding: 0.85rem 2rem; width: 100%; }
  </style>
</head>
<body>

<div class="form-container">
  <div class="text-center mb-4">
    <h2 class="text-white fw-bold mb-1" style="font-family: 'Playfair Display', serif;">
      <i class="fas fa-shield-alt text-danger me-2"></i> Security Incident Intake
    </h2>
    <p class="text-muted small">Confidential internal legal documentation of physical altercations, ejections, medical events, or law enforcement contact.</p>
  </div>

  <form method="POST" action="{{ route('nightly.store.incident') }}" enctype="multipart/form-data">
    @csrf

    <div class="card-luxury">
      <div class="section-title"><i class="fas fa-map-marker-alt"></i> 1. Location & Timing Overview</div>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Venue / Club *</label>
          <select name="location_id" id="pubLocationSelect" class="form-select" required>
            <option value="">Select Venue...</option>
            @foreach($locations as $loc)
              <option value="{{ $loc->id }}" data-legal="{{ $loc->name }}" data-dba="{{ $loc->short_name ?? $loc->name }}" data-address="{{ $loc->address ?? '' }}" {{ (string)$selectedLocationId === (string)$loc->id ? 'selected' : '' }}>
                {{ $loc->name }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Incident Type *</label>
          <select name="report_type_field" class="form-select" required>
            <option value="Physical Altercation">Physical Altercation</option>
            <option value="Patron Ejection">Patron Ejection</option>
            <option value="Police / Law Enforcement Called">Police / Law Enforcement Called</option>
            <option value="Medical Emergency / EMS">Medical Emergency / EMS</option>
            <option value="Property Damage">Property Damage</option>
            <option value="Theft / Lost Property">Theft / Lost Property</option>
            <option value="Other Safety Incident">Other Safety Incident</option>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Legal Name of Location *</label>
          <input type="text" name="location_legal_name" id="pub_legal_name" class="form-control" required placeholder="e.g. Barely Legal New Orleans LLC" />
        </div>
        <div class="col-md-6">
          <label class="form-label">The DBA of Location *</label>
          <input type="text" name="location_dba_name" id="pub_dba_name" class="form-control" required placeholder="e.g. Barely Legal New Orleans" />
        </div>
        <div class="col-md-12">
          <label class="form-label">Address of Location *</label>
          <input type="text" name="location_address" id="pub_address" class="form-control" required placeholder="Street, City, State, ZIP" />
        </div>
        <div class="col-md-4">
          <label class="form-label">Calendar Date of Incident *</label>
          <input type="date" name="incident_date" class="form-control" value="{{ $defaultDate }}" required />
        </div>
        <div class="col-md-4">
          <label class="form-label">Date Submitted *</label>
          <input type="date" name="date_submitted" class="form-control" value="{{ $defaultDate }}" required />
        </div>
        <div class="col-md-4">
          <label class="form-label">Time of Incident *</label>
          <input type="text" name="time_of_incident" class="form-control" placeholder="e.g. 01:45 AM" required />
        </div>
      </div>
    </div>

    <div class="card-luxury">
      <div class="section-title"><i class="fas fa-balance-scale"></i> 2. Police & Law Enforcement Logs</div>
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label">Police Report Number</label>
          <input type="text" name="police_report_number" class="form-control" placeholder="e.g. CAD-2026-00912" />
        </div>
        <div class="col-md-8">
          <label class="form-label">Police Officers & Badge Numbers</label>
          <input type="text" name="police_officers_badges" class="form-control" placeholder="Officer Miller #4412, Officer Davis #1092" />
        </div>
        <div class="col-md-12">
          <label class="form-label">Attach Police Report (max 4MB)</label>
          <input type="file" name="police_report_file" class="form-control" />
        </div>
      </div>
    </div>

    <div class="card-luxury">
      <div class="section-title"><i class="fas fa-user-shield"></i> 3. Submitter & Manager On Duty</div>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Your Name (Submitter) *</label>
          <input type="text" name="submitter_name" class="form-control" required placeholder="Full Legal Name" />
        </div>
        <div class="col-md-3">
          <label class="form-label">Manager(s) on Duty *</label>
          <input type="text" name="managers_on_duty" class="form-control" required placeholder="Manager Names" />
        </div>
        <div class="col-md-3">
          <label class="form-label">Manager's Phone Number</label>
          <input type="text" name="manager_phone" class="form-control" placeholder="(555) 000-0000" />
        </div>
      </div>
    </div>

    <div class="card-luxury">
      <div class="section-title"><i class="fas fa-file-alt"></i> 4. Detailed Narrative & Witness Statements</div>
      <div class="row g-3">
        <div class="col-12">
          <label class="form-label">Involved / Injured Persons *</label>
          <textarea name="involved_persons" class="form-control" rows="3" placeholder="Full names, physical descriptions, injuries sustained..." required></textarea>
        </div>
        <div class="col-12">
          <label class="form-label">Detailed Description of Incident *</label>
          <textarea name="incident_description" class="form-control" rows="5" placeholder="State facts chronologically: what triggered the event, security response, actions taken, and final outcome..." required></textarea>
        </div>
        <div class="col-12">
          <label class="form-label">Witnesses Statement *</label>
          <textarea name="witnesses" class="form-control" rows="4" placeholder="Names & contact info of bystanders or staff testimony..." required></textarea>
        </div>
        <div class="col-12">
          <label class="form-label">Load Witness Report Files (up to 10 files, 4MB each)</label>
          <input type="file" name="witness_report_files[]" class="form-control" multiple />
        </div>
      </div>
    </div>

    <div class="card-luxury">
      <div class="section-title"><i class="fas fa-video"></i> 5. Surveillance Footage & Cast Details</div>
      <div class="row g-3">
        <div class="col-md-8">
          <label class="form-label">Which Cameras (Angles) Cover the Incident? *</label>
          <textarea name="camera_angles" class="form-control" rows="2" placeholder="e.g. CAM-04 (Main Door), CAM-11 (Stage Left)" required></textarea>
        </div>
        <div class="col-md-4">
          <label class="form-label">Camera Timestamp Range *</label>
          <input type="text" name="camera_timestamp" class="form-control" placeholder="e.g. 01:42:00 - 01:51:30" required />
        </div>
        <div class="col-12">
          <label class="form-label">Cast Members Involved or On Duty? *</label>
          <textarea name="cast_members_on_duty" class="form-control" rows="3" placeholder="Floor staff, dancers, bartenders, or door security on duty..." required></textarea>
        </div>
        <div class="col-12">
          <label class="form-label">Additional Photos or Footage Notes</label>
          <textarea name="additional_media_notes" class="form-control" rows="2" placeholder="Notes regarding mobile footage or photos taken..."></textarea>
        </div>
        <div class="col-12">
          <label class="form-label">Upload Mobile Photos or Video Footage (up to 5 files, 4MB each)</label>
          <input type="file" name="additional_media_files[]" class="form-control" multiple />
        </div>
      </div>
    </div>

    <div class="card-luxury">
      <div class="section-title"><i class="fas fa-pen-nib"></i> 6. Verification & Digital Signature</div>
      <div class="row g-3">
        <div class="col-12">
          <label class="form-label d-block mb-2">E-Signature Confirmation *</label>
          <div class="form-check mb-2">
            <input class="form-check-input" type="radio" name="signature_choice" id="pub_sig_accept" value="accept" checked required />
            <label class="form-check-label text-white fw-bold" for="pub_sig_accept">
              <i class="fas fa-check-circle text-success me-1"></i> I Accept & Certify
            </label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="signature_choice" id="pub_sig_opt" value="opt_out" required />
            <label class="form-check-label text-white fw-bold" for="pub_sig_opt">
              <i class="fas fa-times-circle text-warning me-1"></i> I opt out and will physically sign.
            </label>
          </div>
        </div>
        <div class="col-12">
          <label class="form-label">Your Digital Signature (Full Name) *</label>
          <input type="text" name="e_signature" class="form-control" required placeholder="Type full legal name as digital signature" />
        </div>
      </div>
    </div>

    <button type="submit" class="btn btn-submit mb-5"><i class="fas fa-lock me-2"></i> Submit & Archive Incident Report</button>
  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const select = document.getElementById('pubLocationSelect');
  const legalInput = document.getElementById('pub_legal_name');
  const dbaInput = document.getElementById('pub_dba_name');
  const addrInput = document.getElementById('pub_address');

  if (select) {
    select.addEventListener('change', function() {
      const opt = this.options[this.selectedIndex];
      if (opt && opt.value) {
        if (!legalInput.value) legalInput.value = opt.getAttribute('data-legal') || '';
        if (!dbaInput.value) dbaInput.value = opt.getAttribute('data-dba') || '';
        if (!addrInput.value) addrInput.value = opt.getAttribute('data-address') || '';
      }
    });
  }
});
</script>

</body>
</html>
