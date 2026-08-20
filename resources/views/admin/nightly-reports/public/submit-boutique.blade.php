<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <title>Submit Boutique Daily Report — The Nightly Reports</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="{{ asset('user/assets/vendor/css/core.css') }}" />
  <style>
    :root { --bg: #07111f; --surface: #0d1a2e; --surface-2: #142238; --border: rgba(255, 255, 255, 0.08); --gold: #c9a84c; --gold-glow: rgba(201, 168, 76, 0.2); }
    body { background-color: var(--bg); color: #e2e8f0; font-family: 'DM Sans', sans-serif; min-height: 100vh; padding: 1.5rem 0.75rem; }
    .form-container { max-width: 780px; margin: 0 auto; }
    .card-luxury { background: var(--surface); border: 1px solid var(--border); border-radius: 14px; padding: 1.75rem; margin-bottom: 1.5rem; }
    .section-title { font-size: 0.95rem; font-weight: 700; color: #fff; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 8px; }
    .section-title i { color: var(--gold); }
    .form-label { font-size: 0.8rem; font-weight: 600; color: #94a3b8; margin-bottom: 0.35rem; }
    .form-control, .form-select, textarea { background-color: var(--surface-2) !important; border: 1px solid var(--border) !important; color: #fff !important; border-radius: 8px; }
    .form-control:focus, .form-select:focus, textarea:focus { border-color: var(--gold) !important; box-shadow: 0 0 0 0.2rem var(--gold-glow) !important; }
    .btn-submit { background: linear-gradient(135deg, #c9a84c 0%, #b3923d 100%); border: none; color: #07111f; font-weight: 700; font-size: 1rem; border-radius: 10px; padding: 0.85rem 2rem; width: 100%; }
  </style>
</head>
<body>

<div class="form-container">
  <div class="text-center mb-4">
    <h2 class="text-white fw-bold mb-1" style="font-family: 'Playfair Display', serif;">
      <i class="fas fa-store text-info me-2"></i> Boutique Store Daily Report
    </h2>
    <p class="text-muted small">Daily retail merchandise sales, traffic, returns, and POS register intake.</p>
  </div>

  <form method="POST" action="{{ route('nightly.store.boutique') }}">
    @csrf
    <!-- Store & Date -->
    <div class="card-luxury">
      <div class="section-title"><i class="fas fa-map-marker-alt"></i> 1. Store & Shift Details</div>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Boutique Store *</label>
          <select name="location_id" class="form-select" required>
            <option value="">Select Boutique...</option>
            @foreach($locations as $loc)
              <option value="{{ $loc->id }}" {{ (string)$selectedLocationId === (string)$loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Business Date *</label>
          <input type="date" name="business_date" class="form-control" value="{{ $defaultDate }}" required />
        </div>
        <div class="col-md-6">
          <label class="form-label">Submitter Name *</label>
          <input type="text" name="submitter_name" class="form-control" required />
        </div>
        <div class="col-md-6">
          <label class="form-label">Submitter Email *</label>
          <input type="email" name="submitter_email" class="form-control" required />
        </div>
      </div>
    </div>

    <!-- Sales & Traffic -->
    <div class="card-luxury">
      <div class="section-title"><i class="fas fa-cash-register"></i> 2. Sales & Traffic</div>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Gross Daily Sales ($) *</label>
          <input type="number" step="0.01" name="gross_daily_sales" class="form-control" required />
        </div>
        <div class="col-md-6">
          <label class="form-label">Daily Sales Goal ($)</label>
          <input type="number" step="0.01" name="daily_sales_goal" class="form-control" />
        </div>
        <div class="col-md-6">
          <label class="form-label">Total Guest Traffic Count *</label>
          <input type="number" name="total_guest_count" class="form-control" required />
        </div>
        <div class="col-md-6">
          <label class="form-label">Arcade / Theater Headcount</label>
          <input type="number" name="arcade_theater_guest_count" class="form-control" />
        </div>
        <div class="col-md-4">
          <label class="form-label">Total Returns ($)</label>
          <input type="number" step="0.01" name="total_returns" class="form-control" />
        </div>
        <div class="col-md-4">
          <label class="form-label">Total Discounts ($)</label>
          <input type="number" step="0.01" name="total_discount" class="form-control" />
        </div>
        <div class="col-md-4">
          <label class="form-label">Gift Cards Sold ($)</label>
          <input type="number" step="0.01" name="gift_cards_sold" class="form-control" />
        </div>
      </div>
    </div>

    <!-- Safe Balances & Direction -->
    <div class="card-luxury">
      <div class="section-title"><i class="fas fa-vault"></i> 3. Safe Counts & Performance Justification</div>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Said Deposit ($)</label>
          <input type="number" step="0.01" name="said_deposit" class="form-control" />
        </div>
        <div class="col-md-6">
          <label class="form-label">Actual Bank Deposit ($)</label>
          <input type="number" step="0.01" name="actual_deposit" class="form-control" />
        </div>
        <div class="col-md-6">
          <label class="form-label">Sales Direction vs Goal *</label>
          <select name="sales_direction" class="form-select" required>
            <option value="UP">UP (Exceeded Goal)</option>
            <option value="DOWN">DOWN (Below Goal)</option>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Sales Direction Reason *</label>
          <input type="text" name="sales_direction_reason" class="form-control" placeholder="Explanation why sales were up/down..." required />
        </div>
        <div class="col-12">
          <label class="form-label">Incident Flag *</label>
          <select name="incident_flag" class="form-select" required>
            <option value="0">No Store Incidents</option>
            <option value="1">YES — Incident Occurred</option>
          </select>
        </div>
      </div>
    </div>

    <button type="submit" class="btn btn-submit mb-5"><i class="fas fa-check-circle me-2"></i> Submit Boutique Report</button>
  </form>
</div>

</body>
</html>
