<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <title>Cash On Hand (COH) Audit — The Nightly Reports</title>
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
    .btn-submit { background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none; color: #fff; font-weight: 700; font-size: 1rem; border-radius: 10px; padding: 0.85rem 2rem; width: 100%; }
    .coh-total-box { background: linear-gradient(145deg, #0d1a2e 0%, #142238 100%); border: 2px solid var(--gold); border-radius: 12px; padding: 1.25rem; text-align: center; }
  </style>
</head>
<body>

<div class="form-container">
  <div class="text-center mb-4">
    <h2 class="text-white fw-bold mb-1" style="font-family: 'Playfair Display', serif;">
      <i class="fas fa-vault text-success me-2"></i> Cash On Hand (COH) Audit Form
    </h2>
    <p class="text-muted small">Daily physical cash reconciliation across all safes, registers, and ATM cassettes.</p>
  </div>

  <form method="POST" action="{{ route('nightly.store.coh') }}" id="cohForm">
    @csrf
    <!-- Shift Info -->
    <div class="card-luxury">
      <div class="section-title"><i class="fas fa-map-marker-alt"></i> 1. Location & Shift</div>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Venue / Club *</label>
          <select name="location_id" class="form-select" required>
            <option value="">Select Venue...</option>
            @foreach($locations as $loc)
              <option value="{{ $loc->id }}" {{ (string)$selectedLocationId === (string)$loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Audit Date *</label>
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

    <!-- Cash Vault Breakdown -->
    <div class="card-luxury">
      <div class="section-title"><i class="fas fa-money-bill-wave"></i> 2. Vault & Drawer Physical Cash Counts</div>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Drop Safe ($)</label>
          <input type="number" step="0.01" name="drop_safe" class="form-control coh-calc" placeholder="0.00" />
        </div>
        <div class="col-md-6">
          <label class="form-label">Main Safe ($)</label>
          <input type="number" step="0.01" name="main_safe" class="form-control coh-calc" placeholder="0.00" />
        </div>
        <div class="col-md-3">
          <label class="form-label">Register 1 ($)</label>
          <input type="number" step="0.01" name="register_1" class="form-control coh-calc" placeholder="0.00" />
        </div>
        <div class="col-md-3">
          <label class="form-label">Register 2 ($)</label>
          <input type="number" step="0.01" name="register_2" class="form-control coh-calc" placeholder="0.00" />
        </div>
        <div class="col-md-3">
          <label class="form-label">Register 3 ($)</label>
          <input type="number" step="0.01" name="register_3" class="form-control coh-calc" placeholder="0.00" />
        </div>
        <div class="col-md-3">
          <label class="form-label">Register 4 ($)</label>
          <input type="number" step="0.01" name="register_4" class="form-control coh-calc" placeholder="0.00" />
        </div>
        <div class="col-md-3">
          <label class="form-label">ATM 1 ($)</label>
          <input type="number" step="0.01" name="atm_1" class="form-control coh-calc" placeholder="0.00" />
        </div>
        <div class="col-md-3">
          <label class="form-label">ATM 2 ($)</label>
          <input type="number" step="0.01" name="atm_2" class="form-control coh-calc" placeholder="0.00" />
        </div>
        <div class="col-md-3">
          <label class="form-label">ATM 3 ($)</label>
          <input type="number" step="0.01" name="atm_3" class="form-control coh-calc" placeholder="0.00" />
        </div>
        <div class="col-md-3">
          <label class="form-label">ATM 4 ($)</label>
          <input type="number" step="0.01" name="atm_4" class="form-control coh-calc" placeholder="0.00" />
        </div>
        <div class="col-md-6">
          <label class="form-label">Other Cash Reserves ($)</label>
          <input type="number" step="0.01" name="other" class="form-control coh-calc" placeholder="0.00" />
        </div>
        <div class="col-md-6">
          <label class="form-label">Paid Outs Total ($)</label>
          <input type="number" step="0.01" name="paid_outs_total" id="paidOutsInput" class="form-control coh-calc" placeholder="0.00" />
        </div>
        <div class="col-12">
          <label class="form-label">Paid Outs Detail Explanation</label>
          <textarea name="paid_outs_explanation" class="form-control" rows="2" placeholder="Required if paid outs > 0..."></textarea>
        </div>
      </div>
    </div>

    <!-- Calculated Total Box -->
    <div class="coh-total-box mb-4">
      <div class="text-muted small text-uppercase fw-bold">Computed Total VU Cash On Hand</div>
      <div class="display-6 fw-bold text-success mt-1" id="vuTotalDisplay">$0.00</div>
    </div>

    <button type="submit" class="btn btn-submit mb-5"><i class="fas fa-check-circle me-2"></i> Submit & Certify COH Audit</button>
  </form>
</div>

<script src="{{ asset('user/assets/vendor/libs/jquery/jquery.js') }}"></script>
<script>
  function calcCOH() {
    var total = 0;
    $('.coh-calc').each(function() {
      var val = parseFloat($(this).val()) || 0;
      if ($(this).attr('id') === 'paidOutsInput') {
        total -= val;
      } else {
        total += val;
      }
    });
    $('#vuTotalDisplay').text('$' + total.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
  }
  $('.coh-calc').on('input change', calcCOH);
</script>
</body>
</html>
