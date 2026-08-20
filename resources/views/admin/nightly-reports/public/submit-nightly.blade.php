<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <title>Submit Nightly Operations Report — The Nightly Reports</title>

  <!-- Google Fonts: DM Sans & Playfair Display -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="{{ asset('user/assets/vendor/css/core.css') }}" />

  <style>
    :root {
      --bg: #07111f;
      --surface: #0d1a2e;
      --surface-2: #142238;
      --border: rgba(255, 255, 255, 0.08);
      --gold: #c9a84c;
      --gold-glow: rgba(201, 168, 76, 0.2);
    }
    body {
      background-color: var(--bg);
      color: #e2e8f0;
      font-family: 'DM Sans', sans-serif;
      min-height: 100vh;
      padding: 1.5rem 0.75rem;
    }
    .form-container {
      max-width: 780px;
      margin: 0 auto;
    }
    .card-luxury {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 14px;
      padding: 1.75rem;
      margin-bottom: 1.5rem;
      box-shadow: 0 10px 30px rgba(0,0,0,0.35);
    }
    .section-title {
      font-size: 0.95rem;
      font-weight: 700;
      color: #fff;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      margin-bottom: 1rem;
      padding-bottom: 0.5rem;
      border-bottom: 1px solid var(--border);
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .section-title i { color: var(--gold); }
    .form-label {
      font-size: 0.8rem;
      font-weight: 600;
      color: #94a3b8;
      margin-bottom: 0.35rem;
    }
    .form-control, .form-select, textarea {
      background-color: var(--surface-2) !important;
      border: 1px solid var(--border) !important;
      color: #fff !important;
      border-radius: 8px;
      padding: 0.65rem 0.85rem;
      font-size: 0.92rem;
    }
    .form-control:focus, .form-select:focus, textarea:focus {
      border-color: var(--gold) !important;
      box-shadow: 0 0 0 0.2rem var(--gold-glow) !important;
    }
    .btn-submit {
      background: linear-gradient(135deg, #c9a84c 0%, #b3923d 100%);
      border: none;
      color: #07111f;
      font-weight: 700;
      font-size: 1rem;
      border-radius: 10px;
      padding: 0.85rem 2rem;
      width: 100%;
      transition: all 0.2s;
    }
    .btn-submit:hover {
      box-shadow: 0 6px 20px var(--gold-glow);
      color: #000;
    }
    .calc-pill {
      background: rgba(201, 168, 76, 0.12);
      border: 1px solid rgba(201, 168, 76, 0.3);
      color: #e8be6a;
      border-radius: 8px;
      padding: 0.5rem 0.75rem;
      font-size: 0.82rem;
      font-weight: 600;
    }
  </style>
</head>
<body>

<div class="form-container">
  <!-- Header -->
  <div class="text-center mb-4">
    <h2 class="text-white fw-bold mb-1" style="font-family: 'Playfair Display', serif;">
      <i class="fas fa-moon text-warning me-2"></i> Nightly Operations Report
    </h2>
    <p class="text-muted small">Daily managerial financial, attendance, and shift operations intake.</p>
  </div>

  <form method="POST" action="{{ route('nightly.store.nightly') }}" id="nightlyReportForm">
    @csrf

    <!-- Section 1: Venue & Shift Details -->
    <div class="card-luxury">
      <div class="section-title"><i class="fas fa-map-marker-alt"></i> 1. Location & Shift Info</div>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Venue / Club *</label>
          <select name="location_id" class="form-select" required>
            <option value="">Select Club...</option>
            @foreach($locations as $loc)
              <option value="{{ $loc->id }}" {{ (string)$selectedLocationId === (string)$loc->id ? 'selected' : '' }}>
                {{ $loc->name }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Business Date *</label>
          <input type="date" name="business_date" class="form-control" value="{{ $defaultDate }}" required />
        </div>
        <div class="col-md-6">
          <label class="form-label">Submitter Full Name *</label>
          <input type="text" name="submitter_name" class="form-control" placeholder="e.g. John Smith (GM)" required />
        </div>
        <div class="col-md-6">
          <label class="form-label">Submitter Email *</label>
          <input type="email" name="submitter_email" class="form-control" placeholder="gm@venue.com" required />
        </div>
      </div>
    </div>

    <!-- Section 2: Financial Sales Breakdown -->
    <div class="card-luxury">
      <div class="section-title"><i class="fas fa-dollar-sign"></i> 2. Financial Revenue Breakdown</div>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Total Net Sales ($) *</label>
          <input type="number" step="0.01" name="net_sales" id="netSalesInput" class="form-control" placeholder="0.00" required />
        </div>
        <div class="col-md-6">
          <label class="form-label">Nightly Sales Goal ($)</label>
          <input type="number" step="0.01" name="nightly_goal" class="form-control" placeholder="0.00" />
        </div>
        <div class="col-md-4">
          <label class="form-label">Last Year Net Sales ($)</label>
          <input type="number" step="0.01" name="last_year_net_sales" class="form-control" placeholder="0.00" />
        </div>
        <div class="col-md-4">
          <label class="form-label">Running Weekly Sales ($)</label>
          <input type="number" step="0.01" name="weekly_running_net_sales" class="form-control" placeholder="0.00" />
        </div>
        <div class="col-md-4">
          <label class="form-label">Day Shift Net Sales ($)</label>
          <input type="number" step="0.01" name="day_shift_net_sales" class="form-control" placeholder="0.00" />
        </div>
        <div class="col-md-6">
          <label class="form-label">POS Voids ($)</label>
          <input type="number" step="0.01" name="voids" class="form-control" placeholder="0.00" />
        </div>
        <div class="col-md-6">
          <label class="form-label">Manager Comps ($)</label>
          <input type="number" step="0.01" name="comps" class="form-control" placeholder="0.00" />
        </div>
        <div class="col-md-4">
          <label class="form-label">Dance Dollars Sold ($)</label>
          <input type="number" step="0.01" name="dance_dollars_sold" id="danceDollarsInput" class="form-control" placeholder="0.00" />
        </div>
        <div class="col-md-4">
          <label class="form-label">Dance Dollars Redeemed ($)</label>
          <input type="number" step="0.01" name="dance_dollars_redeemed" class="form-control" placeholder="0.00" />
        </div>
        <div class="col-md-4">
          <label class="form-label">VIP Rooms Sold (#)</label>
          <input type="number" name="vip_rooms_sold" class="form-control" placeholder="0" />
        </div>
      </div>
    </div>

    <!-- Section 3: Attendance & Headcount -->
    <div class="card-luxury">
      <div class="section-title"><i class="fas fa-users"></i> 3. Guest Attendance & Spend Analytics</div>
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label">Total Guests (Clicker Count) *</label>
          <input type="number" name="total_guests" id="totalGuestsInput" class="form-control" placeholder="0" required />
        </div>
        <div class="col-md-4">
          <label class="form-label">Paid Admissions</label>
          <input type="number" name="paid_guests" class="form-control" placeholder="0" />
        </div>
        <div class="col-md-4">
          <label class="form-label">Free / Discount Guests</label>
          <input type="number" name="free_discount_guests" class="form-control" placeholder="0" />
        </div>
        <div class="col-md-6">
          <label class="form-label">Passes Redeemed</label>
          <input type="number" name="passes_redeemed" class="form-control" placeholder="0" />
        </div>
        <div class="col-md-6">
          <label class="form-label">IPEs on Shift (#)</label>
          <input type="number" name="ipes" id="ipesInput" class="form-control" placeholder="0" />
        </div>
        <div class="col-12 d-flex gap-3 mt-2">
          <div class="calc-pill flex-grow-1 text-center">
            Estimated Guest Avg Spend: <span id="guestAvgDisplay" class="fw-bold text-white">$0.00</span>
          </div>
          <div class="calc-pill flex-grow-1 text-center">
            Dance Spend / Entertainer: <span id="danceAvgDisplay" class="fw-bold text-white">$0.00</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Section 4: Cash Flow & Payouts -->
    <div class="card-luxury">
      <div class="section-title"><i class="fas fa-vault"></i> 4. Cash Flow, Payouts & Vault</div>
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label">Taxi / Rideshare Payout ($)</label>
          <input type="number" step="0.01" name="taxi_payout" id="taxiInput" class="form-control" placeholder="0.00" />
        </div>
        <div class="col-md-4">
          <label class="form-label">ATM Payout ($)</label>
          <input type="number" step="0.01" name="atm_payout" id="atmInput" class="form-control" placeholder="0.00" />
        </div>
        <div class="col-md-4">
          <label class="form-label">Other Payouts ($)</label>
          <input type="number" step="0.01" name="other_payouts" id="otherInput" class="form-control" placeholder="0.00" />
        </div>
        <div class="col-md-6">
          <label class="form-label">Actual Bank Deposit ($)</label>
          <input type="number" step="0.01" name="deposit" class="form-control" placeholder="0.00" />
        </div>
        <div class="col-md-6">
          <label class="form-label">Ending Safe Balance ($)</label>
          <input type="number" step="0.01" name="safe_balance" class="form-control" placeholder="0.00" />
        </div>
      </div>
    </div>

    <!-- Section 5: Operations & Incident Alert -->
    <div class="card-luxury">
      <div class="section-title"><i class="fas fa-shield-alt"></i> 5. Operations & Incident Alert</div>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Weather Conditions</label>
          <input type="text" name="weather" class="form-control" placeholder="e.g. Clear 78°, Heavy Rain" />
        </div>
        <div class="col-md-6">
          <label class="form-label">Were there any incidents tonight? *</label>
          <select name="incident_flag" class="form-select" required>
            <option value="0">No Incidents</option>
            <option value="1">YES — Incidents Occurred</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Section 6: Shift Narrative Notes -->
    <div class="card-luxury">
      <div class="section-title"><i class="fas fa-sticky-note"></i> 6. Shift Narrative Notes</div>
      <div class="row g-3">
        <div class="col-12">
          <label class="form-label">Executive Night Summary</label>
          <textarea name="night_summary" class="form-control" rows="3" placeholder="Overview of the night's flow, VIP tables, crowd vibe..."></textarea>
        </div>
        <div class="col-md-6">
          <label class="form-label">Team Member Notes</label>
          <textarea name="team_member_notes" class="form-control" rows="2" placeholder="Bartenders, servers, floor staff performance..."></textarea>
        </div>
        <div class="col-md-6">
          <label class="form-label">IPE / Entertainer Notes</label>
          <textarea name="ipe_notes" class="form-control" rows="2" placeholder="Entertainer headcount and VIP room pace..."></textarea>
        </div>
        <div class="col-md-6">
          <label class="form-label">Superstar Shift MVP Nomination</label>
          <input type="text" name="super_star_nomination" class="form-control" placeholder="Nominated staff member / entertainer" />
        </div>
        <div class="col-md-6">
          <label class="form-label">Inventory & Ordering Requests</label>
          <input type="text" name="ordering_notes" class="form-control" placeholder="Liquor, beer, or supply restock requests" />
        </div>
        <div class="col-12">
          <label class="form-label">Additional GM Comments</label>
          <textarea name="shift_comments" class="form-control" rows="2" placeholder="Any closing observations..."></textarea>
        </div>
      </div>
    </div>

    <!-- Submit Button -->
    <div class="mb-5">
      <button type="submit" class="btn btn-submit">
        <i class="fas fa-check-circle me-2"></i> Submit Nightly Report
      </button>
    </div>
  </form>
</div>

<script src="{{ asset('user/assets/vendor/libs/jquery/jquery.js') }}"></script>
<script>
  function recalculate() {
    var netSales = parseFloat($('#netSalesInput').val()) || 0;
    var totalGuests = parseInt($('#totalGuestsInput').val()) || 0;
    var danceSold = parseFloat($('#danceDollarsInput').val()) || 0;
    var ipes = parseInt($('#ipesInput').val()) || 0;

    var guestAvg = totalGuests > 0 ? (netSales / totalGuests) : 0;
    var danceAvg = ipes > 0 ? (danceSold / ipes) : 0;

    $('#guestAvgDisplay').text('$' + guestAvg.toFixed(2));
    $('#danceAvgDisplay').text('$' + danceAvg.toFixed(2));
  }

  $('#netSalesInput, #totalGuestsInput, #danceDollarsInput, #ipesInput').on('input change', recalculate);
</script>
</body>
</html>
