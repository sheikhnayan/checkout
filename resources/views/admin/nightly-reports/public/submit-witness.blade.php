<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <title>Witness Statement Intake — The Nightly Reports</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="{{ asset('user/assets/vendor/css/core.css') }}" />
  <style>
    :root { --bg: #07111f; --surface: #0d1a2e; --surface-2: #142238; --border: rgba(255, 255, 255, 0.08); --gold: #c9a84c; --gold-glow: rgba(201, 168, 76, 0.2); }
    body { background-color: var(--bg); color: #e2e8f0; font-family: 'DM Sans', sans-serif; min-height: 100vh; padding: 1.5rem 0.75rem; }
    .form-container { max-width: 700px; margin: 0 auto; }
    .card-luxury { background: var(--surface); border: 1px solid var(--border); border-radius: 14px; padding: 1.75rem; margin-bottom: 1.5rem; }
    .section-title { font-size: 0.95rem; font-weight: 700; color: #fff; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 8px; }
    .section-title i { color: #38bdf8; }
    .form-label, label, .form-check-label { font-size: 0.82rem; font-weight: 600; color: #ffffff !important; margin-bottom: 0.35rem; opacity: 1 !important; }
    .form-control, .form-select, textarea { background-color: var(--surface-2) !important; border: 1px solid var(--border) !important; color: #fff !important; border-radius: 8px; }
    .form-control:focus, .form-select:focus, textarea:focus { border-color: var(--gold) !important; box-shadow: 0 0 0 0.2rem var(--gold-glow) !important; }
    .btn-submit { background: linear-gradient(135deg, #38bdf8 0%, #0284c7 100%); border: none; color: #fff; font-weight: 700; font-size: 1rem; border-radius: 10px; padding: 0.85rem 2rem; width: 100%; }
  </style>
</head>
<body>

<div class="form-container">
  <div class="text-center mb-4">
    <h2 class="text-white fw-bold mb-1" style="font-family: 'Playfair Display', serif;">
      <i class="fas fa-file-signature text-info me-2"></i> Witness Statement Intake
    </h2>
    <p class="text-muted small">Eyewitness testimony form for patrons, floor staff, performers, or bystanders.</p>
  </div>

  <form method="POST" action="{{ route('nightly.store.witness') }}">
    @csrf
    <div class="card-luxury">
      <div class="section-title"><i class="fas fa-user"></i> 1. Witness Contact Details</div>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Venue / Location *</label>
          <select name="location_id" class="form-select" required>
            <option value="">Select Venue...</option>
            @foreach($locations as $loc)
              <option value="{{ $loc->id }}" {{ (string)$selectedLocationId === (string)$loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Date of Incident *</label>
          <input type="date" name="incident_date" class="form-control" value="{{ $defaultDate }}" required />
        </div>
        <div class="col-md-6">
          <label class="form-label">Witness Full Legal Name *</label>
          <input type="text" name="witness_name" class="form-control" required />
        </div>
        <div class="col-md-6">
          <label class="form-label">Witness Role / Type *</label>
          <select name="witness_type" class="form-select" required>
            <option value="Employee / Staff">Employee / Staff</option>
            <option value="Customer / Patron">Customer / Patron</option>
            <option value="Performer / Model">Performer / Model</option>
            <option value="Security Personnel">Security Personnel</option>
            <option value="Third-Party Vendor">Third-Party Vendor</option>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Phone Number *</label>
          <input type="tel" name="witness_phone" class="form-control" placeholder="(555) 000-0000" required />
        </div>
        <div class="col-md-6">
          <label class="form-label">Email Address</label>
          <input type="email" name="witness_email" class="form-control" placeholder="witness@email.com" />
        </div>
      </div>
    </div>

    <div class="card-luxury">
      <div class="section-title"><i class="fas fa-feather-alt"></i> 2. Statement Testimony</div>
      <div class="row g-3">
        <div class="col-12">
          <label class="form-label">What did you observe? (In your own words) *</label>
          <textarea name="statement_text" class="form-control" rows="6" placeholder="Describe where you were standing, what you heard and saw, who was involved, and what happened..." required></textarea>
        </div>
        <div class="col-12">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="affirmCert" required />
            <label class="form-check-label text-muted small" for="affirmCert">
              I affirm that this statement represents an accurate and truthful account of what I personally witnessed to the best of my recollection.
            </label>
          </div>
        </div>
      </div>
    </div>

    <button type="submit" class="btn btn-submit mb-5"><i class="fas fa-check-circle me-2"></i> Submit Witness Testimony</button>
  </form>
</div>

</body>
</html>
