<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <title>Nightly Report — Adult Club</title>

  <!-- Google Fonts: DM Sans & Playfair Display -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Playfair+Display:ital,wght@0,600;0,700;1,400&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="{{ asset('user/assets/vendor/css/core.css') }}" />

  <style>
    :root {
      --nr-bg: #060913;
      --nr-card-bg: #0d1525;
      --nr-input-bg: #121c2e;
      --nr-border: rgba(255, 255, 255, 0.09);
      --nr-border-gold: rgba(245, 158, 11, 0.4);
      --nr-gold: #f59e0b;
      --nr-gold-bright: #fbbf24;
      --nr-gold-glow: rgba(245, 158, 11, 0.25);
      --nr-text: #ffffff;
      --nr-text-muted: #94a3b8;
      --nr-hint: #64748b;
    }

    * {
      box-sizing: border-box;
    }

    body {
      background: radial-gradient(circle at 10% 20%, #15102a 0%, #0a0e1a 50%, #060913 100%);
      background-attachment: fixed;
      color: #e2e8f0;
      font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      min-height: 100vh;
      margin: 0;
      padding: 0;
      overflow-x: hidden;
    }

    /* ── TOP NAV BAR ── */
    .nr-topbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 1rem 2rem;
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
      background: rgba(6, 9, 19, 0.7);
      backdrop-filter: blur(10px);
    }

    .nr-brand-logo {
      font-family: 'Playfair Display', serif;
      font-weight: 700;
      font-size: 1.35rem;
      color: var(--nr-gold);
      text-decoration: none;
      letter-spacing: 0.02em;
    }

    .nr-brand-logo:hover {
      color: var(--nr-gold-bright);
    }

    .nr-topbar-nav {
      display: flex;
      align-items: center;
      gap: 1.25rem;
    }

    .btn-return-admin {
      border: 1px solid var(--nr-gold);
      color: var(--nr-gold);
      background: transparent;
      border-radius: 9999px;
      padding: 0.35rem 0.95rem;
      font-size: 0.75rem;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.2s ease-in-out;
    }

    .btn-return-admin:hover {
      background: rgba(245, 158, 11, 0.15);
      color: var(--nr-gold-bright);
      border-color: var(--nr-gold-bright);
    }

    .nr-nav-link {
      color: var(--nr-text-muted);
      font-size: 0.8rem;
      font-weight: 500;
      text-decoration: none;
      transition: color 0.2s;
      display: inline-flex;
      align-items: center;
      gap: 5px;
    }

    .nr-nav-link:hover {
      color: #ffffff;
    }

    /* ── HERO HEADER ── */
    .nr-hero {
      text-align: center;
      padding: 2.25rem 1rem 1.5rem;
    }

    .badge-pill {
      background: rgba(245, 158, 11, 0.08);
      border: 1px solid rgba(245, 158, 11, 0.35);
      color: var(--nr-gold-bright);
      border-radius: 9999px;
      font-size: 0.65rem;
      font-weight: 700;
      letter-spacing: 0.14em;
      padding: 0.28rem 0.85rem;
      display: inline-block;
      margin-bottom: 0.65rem;
      text-transform: uppercase;
    }

    .page-title {
      font-family: 'Playfair Display', serif;
      font-size: 2.35rem;
      font-weight: 700;
      color: #ffffff;
      margin: 0 0 0.4rem 0;
      letter-spacing: 0.01em;
    }

    .page-subtitle {
      color: var(--nr-text-muted);
      font-size: 0.85rem;
      margin: 0;
    }

    .text-gold-highlight {
      color: var(--nr-gold-bright);
      font-weight: 600;
    }

    /* ── MAIN CARD CONTAINER ── */
    .form-wrapper {
      max-width: 780px;
      margin: 0 auto 4rem auto;
      padding: 0 1rem;
    }

    .nr-card-main {
      background: var(--nr-card-bg);
      border: 1px solid var(--nr-border);
      border-radius: 16px;
      padding: 2.25rem 2.5rem;
      box-shadow: 0 20px 50px rgba(0, 0, 0, 0.55);
    }

    @media (max-width: 576px) {
      .nr-card-main {
        padding: 1.5rem 1.25rem;
      }
      .nr-topbar {
        padding: 0.75rem 1rem;
        flex-wrap: wrap;
        gap: 0.75rem;
      }
      .page-title {
        font-size: 1.85rem;
      }
    }

    /* ── SECTION HEADERS ── */
    .section-header {
      font-size: 0.72rem;
      font-weight: 700;
      letter-spacing: 0.12em;
      color: var(--nr-gold);
      text-transform: uppercase;
      margin-top: 2rem;
      margin-bottom: 0.85rem;
      display: block;
    }

    .section-header:first-of-type {
      margin-top: 0;
    }

    /* ── LABELS & CONTROLS ── */
    .form-label, label {
      font-size: 0.78rem;
      font-weight: 600;
      color: #ffffff !important;
      margin-bottom: 0.35rem;
      display: block;
      opacity: 1 !important;
    }

    .form-control, .form-select, textarea {
      background-color: var(--nr-input-bg) !important;
      border: 1px solid var(--nr-border) !important;
      color: #ffffff !important;
      border-radius: 8px !important;
      padding: 0.55rem 0.85rem !important;
      font-size: 0.88rem !important;
      line-height: 1.4 !important;
      transition: border-color 0.2s, box-shadow 0.2s;
      width: 100%;
    }

    .form-control:focus, .form-select:focus, textarea:focus {
      border-color: var(--nr-gold) !important;
      box-shadow: 0 0 0 2px var(--nr-gold-glow) !important;
      outline: none !important;
    }

    .form-control::placeholder, textarea::placeholder {
      color: #556987 !important;
      opacity: 0.85;
    }

    select.form-select {
      background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%2394a3b8' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") !important;
      background-repeat: no-repeat !important;
      background-position: right 0.75rem center !important;
      background-size: 14px 10px !important;
      color-scheme: dark;
    }

    input[type="date"] {
      color-scheme: dark;
    }

    textarea.form-control {
      min-height: 68px;
      resize: vertical;
    }

    .field-hint {
      font-size: 0.7rem;
      color: var(--nr-hint);
      margin-top: 0.25rem;
      font-weight: 400;
    }

    /* ── REMOVE SPINNERS & ARROWS FROM ALL NUMBER INPUTS ── */
    input[type="number"]::-webkit-outer-spin-button,
    input[type="number"]::-webkit-inner-spin-button {
      -webkit-appearance: none !important;
      margin: 0 !important;
    }

    input[type="number"] {
      -moz-appearance: textfield !important;
      appearance: textfield !important;
    }

    /* ── BOTTOM ACTIONS ── */
    .nr-form-footer {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-top: 2.25rem;
      padding-top: 1.5rem;
      border-top: 1px solid rgba(255, 255, 255, 0.07);
      gap: 1rem;
    }

    .btn-draft {
      background: transparent !important;
      border: 1px solid rgba(255, 255, 255, 0.15) !important;
      color: var(--nr-text-muted) !important;
      font-size: 0.85rem !important;
      border-radius: 8px !important;
      padding: 0.6rem 1.25rem !important;
      font-weight: 500 !important;
      transition: all 0.2s ease-in-out !important;
      display: inline-flex !important;
      align-items: center !important;
      gap: 7px !important;
      cursor: pointer;
    }

    .btn-draft:hover {
      background: rgba(255, 255, 255, 0.06) !important;
      border-color: rgba(255, 255, 255, 0.25) !important;
      color: #ffffff !important;
    }

    .btn-review-submit {
      background: linear-gradient(135deg, #c9a84c 0%, #b3923d 100%) !important;
      background-color: #c9a84c !important;
      border: 1px solid #c9a84c !important;
      color: #07111f !important;
      font-weight: 700 !important;
      font-size: 0.92rem !important;
      border-radius: 8px !important;
      padding: 0.65rem 1.75rem !important;
      transition: all 0.2s ease-in-out !important;
      box-shadow: 0 4px 14px rgba(0, 0, 0, 0.3) !important;
      display: inline-flex !important;
      align-items: center !important;
      gap: 7px !important;
      cursor: pointer;
    }

    .btn-review-submit:hover {
      background: linear-gradient(135deg, #dfbc5e 0%, #c9a84c 100%) !important;
      background-color: #dfbc5e !important;
      border-color: #dfbc5e !important;
      box-shadow: 0 6px 20px var(--nr-gold-glow) !important;
      color: #000000 !important;
      transform: translateY(-1px);
    }

    .btn-review-submit * {
      color: #07111f !important;
    }

    .btn-review-submit:hover * {
      color: #000000 !important;
    }

    /* ── TOAST NOTIFICATION ── */
    #draftToast {
      position: fixed;
      bottom: 24px;
      right: 24px;
      background: #142238;
      border: 1px solid var(--nr-border-gold);
      color: #ffffff;
      padding: 0.75rem 1.25rem;
      border-radius: 8px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.5);
      display: none;
      z-index: 9999;
      font-size: 0.85rem;
      align-items: center;
      gap: 8px;
    }
  </style>
</head>
<body>

<!-- TOP NAVIGATION -->
<div class="nr-topbar">
  <a href="{{ route('admin.nightly-reports.dashboard') }}" class="nr-brand-logo">Reports</a>
  <div class="nr-topbar-nav">
    <a href="{{ route('admin.nightly-reports.dashboard') }}" class="btn-return-admin">Return to Admin</a>
    <form action="{{ route('ambassador.logout') }}" method="POST" class="d-inline m-0">
      @csrf
      <button type="submit" class="nr-nav-link bg-transparent border-0 p-0" style="cursor: pointer;">
        <i class="fas fa-sign-out-alt"></i> Sign out
      </button>
    </form>
  </div>
</div>

<!-- HERO TITLE -->
<div class="nr-hero">
  <div class="badge-pill">ADULT CLUB - NIGHTLY REPORT</div>
  <h1 class="page-title">Nightly Report</h1>
  <p class="page-subtitle">
    Date defaults to <span class="text-gold-highlight">yesterday ({{ $yesterdayFormatted ?? \Carbon\Carbon::yesterday()->format('D, M j, Y') }}).</span>
  </p>
</div>

<!-- MAIN FORM WRAPPER -->
<div class="form-wrapper">
  <div class="nr-card-main">
    <form method="POST" action="{{ route('nightly.store.nightly') }}" id="nightlyReportForm">
      @csrf

      <!-- 1. TEAM INFORMATION -->
      <div class="section-header">TEAM INFORMATION</div>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Venue *</label>
          <select name="location_id" class="form-select" required>
            <option value="">Choose a venue</option>
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
          <label class="form-label">Submitter Name</label>
          <input type="text" name="submitter_name" class="form-control" placeholder="First Last" required />
        </div>
        <div class="col-md-6">
          <label class="form-label">Additional Contributor</label>
          <input type="text" name="additional_contributor" class="form-control" placeholder="Name" />
        </div>
        <div class="col-12">
          <label class="form-label">Submitter Email</label>
          <input type="email" name="submitter_email" class="form-control" placeholder="you@example.com" required />
        </div>
      </div>

      <!-- 2. SALES & REVENUE -->
      <div class="section-header">SALES & REVENUE</div>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Nightly Goal</label>
          <input type="number" step="0.01" min="0" name="nightly_goal" class="form-control num-field" placeholder="0.00" />
        </div>
        <div class="col-md-6">
          <label class="form-label">Net Sales</label>
          <input type="number" step="0.01" min="0" name="net_sales" id="netSalesInput" class="form-control num-field" placeholder="0.00" required />
        </div>
        <div class="col-md-6">
          <label class="form-label">Last Year Net Sales</label>
          <input type="number" step="0.01" min="0" name="last_year_net_sales" class="form-control num-field" placeholder="0.00" />
        </div>
        <div class="col-md-6">
          <label class="form-label">Weekly Running Net Sales</label>
          <input type="number" step="0.01" min="0" name="weekly_running_net_sales" class="form-control num-field" placeholder="0.00" />
        </div>
        <div class="col-md-6">
          <label class="form-label">Day Shift Net Sales</label>
          <input type="number" step="0.01" min="0" name="day_shift_net_sales" class="form-control num-field" placeholder="0.00" />
        </div>
        <div class="col-md-6">
          <label class="form-label">Voids</label>
          <input type="number" step="0.01" min="0" name="voids" class="form-control num-field" placeholder="0.00" />
        </div>
        <div class="col-md-6">
          <label class="form-label">Comps</label>
          <input type="number" step="0.01" min="0" name="comps" class="form-control num-field" placeholder="0.00" />
        </div>
      </div>

      <!-- 3. DANCE DOLLARS -->
      <div class="section-header">DANCE DOLLARS</div>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Dance Dollars Sold</label>
          <input type="number" step="0.01" min="0" name="dance_dollars_sold" id="danceDollarsInput" class="form-control num-field" placeholder="0.00" />
          <div class="field-hint">Adult venues only</div>
        </div>
        <div class="col-md-6">
          <label class="form-label">Dance Dollars Redeemed</label>
          <input type="number" step="0.01" min="0" name="dance_dollars_redeemed" class="form-control num-field" placeholder="0.00" />
          <div class="field-hint">Adult venues only</div>
        </div>
      </div>

      <!-- 4. VIP / CHAMPAGNE ROOMS -->
      <div class="section-header">VIP / CHAMPAGNE ROOMS</div>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">VIP Rooms Sold</label>
          <input type="number" step="1" min="0" name="vip_rooms_sold" class="form-control num-int-field" placeholder="0" />
          <div class="field-hint">Adult venues only</div>
        </div>
      </div>

      <!-- 5. GUEST COUNT & ADMISSIONS -->
      <div class="section-header">GUEST COUNT & ADMISSIONS</div>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Total Guests</label>
          <input type="number" step="1" min="0" name="total_guests" id="totalGuestsInput" class="form-control num-int-field" placeholder="0" required />
        </div>
        <div class="col-md-6">
          <label class="form-label">Paid Guests</label>
          <input type="number" step="1" min="0" name="paid_guests" class="form-control num-int-field" placeholder="0" />
        </div>
        <div class="col-md-6">
          <label class="form-label">Free / Discount Guests</label>
          <input type="number" step="1" min="0" name="free_discount_guests" class="form-control num-int-field" placeholder="0" />
        </div>
        <div class="col-md-6">
          <label class="form-label">Passes Redeemed</label>
          <input type="number" step="1" min="0" name="passes_redeemed" class="form-control num-int-field" placeholder="0" />
        </div>
        <div class="col-md-6">
          <label class="form-label">Guest Average</label>
          <input type="number" step="0.01" min="0" name="guest_average" id="guestAverageInput" class="form-control num-field" placeholder="0.00" />
        </div>
        <div class="col-md-6">
          <label class="form-label">Dance Average</label>
          <input type="number" step="0.01" min="0" name="dance_average" id="danceAverageInput" class="form-control num-field" placeholder="0.00" />
          <div class="field-hint">Adult venues only</div>
        </div>
      </div>

      <!-- 6. IPR'S -->
      <div class="section-header">IPR'S</div>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">IPRs</label>
          <input type="number" step="1" min="0" name="ipes" id="ipesInput" class="form-control num-int-field" placeholder="0" />
          <div class="field-hint">Adult venues only</div>
        </div>
        <div class="col-12">
          <label class="form-label">IPR Notes</label>
          <textarea name="ipe_notes" class="form-control" rows="2" placeholder="Details about IPR activity..."></textarea>
          <div class="field-hint">Adult venues only</div>
        </div>
      </div>

      <!-- 7. PAYOUTS -->
      <div class="section-header">PAYOUTS</div>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Total Payouts</label>
          <input type="number" step="0.01" min="0" name="total_payouts" id="totalPayoutsInput" class="form-control num-field" placeholder="0.00" />
        </div>
        <div class="col-md-6">
          <label class="form-label">Taxi Payout</label>
          <input type="number" step="0.01" min="0" name="taxi_payout" id="taxiPayoutInput" class="form-control num-field" placeholder="0.00" />
        </div>
        <div class="col-md-6">
          <label class="form-label">ATM Payout</label>
          <input type="number" step="0.01" min="0" name="atm_payout" id="atmPayoutInput" class="form-control num-field" placeholder="0.00" />
        </div>
        <div class="col-md-6">
          <label class="form-label">Other Payouts</label>
          <input type="number" step="0.01" min="0" name="other_payouts" id="otherPayoutsInput" class="form-control num-field" placeholder="0.00" />
        </div>
      </div>

      <!-- 8. DEPOSIT & SAFE -->
      <div class="section-header">DEPOSIT & SAFE</div>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Deposit</label>
          <input type="number" step="0.01" min="0" name="deposit" class="form-control num-field" placeholder="0.00" />
        </div>
        <div class="col-md-6">
          <label class="form-label">Safe Balance</label>
          <input type="number" step="0.01" min="0" name="safe_balance" class="form-control num-field" placeholder="0.00" />
        </div>
      </div>

      <!-- 9. WEATHER & INCIDENTS -->
      <div class="section-header">WEATHER & INCIDENTS</div>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Weather</label>
          <select name="weather" class="form-select">
            <option value="Clear" selected>Clear</option>
            <option value="Partly Cloudy">Partly Cloudy</option>
            <option value="Overcast">Overcast</option>
            <option value="Rain">Rain</option>
            <option value="Heavy Rain / Storm">Heavy Rain / Storm</option>
            <option value="Snow / Ice">Snow / Ice</option>
            <option value="Extreme Heat">Extreme Heat</option>
            <option value="Extreme Cold">Extreme Cold</option>
            <option value="Windy">Windy</option>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Incident Flag</label>
          <select name="incident_flag" class="form-select">
            <option value="0" selected>No</option>
            <option value="1">Yes</option>
          </select>
        </div>
      </div>

      <!-- 10. NOTES -->
      <div class="section-header">NOTES</div>
      <div class="row g-3">
        <div class="col-12">
          <label class="form-label">Team Member Notes</label>
          <textarea name="team_member_notes" class="form-control" rows="2" placeholder="Shout-outs, concerns, team updates..."></textarea>
        </div>
        <div class="col-12">
          <label class="form-label">Social Media Content</label>
          <textarea name="social_media_content" class="form-control" rows="2" placeholder="What was posted or should be posted..."></textarea>
        </div>
        <div class="col-12">
          <label class="form-label">Ordering Notes</label>
          <textarea name="ordering_notes" class="form-control" rows="2" placeholder="Bar/supply ordering needs..."></textarea>
        </div>
      </div>

      <!-- 11. PASSES & PROMOTIONS -->
      <div class="section-header">PASSES & PROMOTIONS</div>
      <div class="row g-3">
        <div class="col-12">
          <label class="form-label">Pass Distribution Locations</label>
          <textarea name="pass_distribution_locations" class="form-control" rows="2" placeholder="Where were passes distributed tonight?"></textarea>
        </div>
      </div>

      <!-- 12. NIGHT SUMMARY -->
      <div class="section-header">NIGHT SUMMARY</div>
      <div class="row g-3">
        <div class="col-12">
          <label class="form-label">Night Summary</label>
          <textarea name="night_summary" class="form-control" rows="2" placeholder="Overall summary of the night..."></textarea>
        </div>
        <div class="col-12">
          <label class="form-label">Superstar Nomination</label>
          <textarea name="super_star_nomination" class="form-control" rows="2" placeholder="Nominate a team member who went above and beyond..."></textarea>
        </div>
        <div class="col-12">
          <label class="form-label">Shift Comments</label>
          <textarea name="shift_comments" class="form-control" rows="2" placeholder="Any final comments about the shift..."></textarea>
        </div>
      </div>

      <!-- 13. ADDITIONAL RECIPIENT -->
      <div class="section-header">ADDITIONAL RECIPIENT</div>
      <div class="row g-3">
        <div class="col-12">
          <label class="form-label">Additional Email Recipient</label>
          <input type="email" name="additional_recipient" class="form-control" placeholder="someone@example.com" />
        </div>
      </div>

      <!-- BOTTOM ACTIONS -->
      <div class="nr-form-footer">
        <button type="button" class="btn btn-draft" id="btnSaveDraft">
          <i class="fas fa-save"></i> Save Draft
        </button>
        <button type="submit" class="btn btn-review-submit">
          <i class="fas fa-check-circle"></i> Review & Submit
        </button>
      </div>
    </form>
  </div>
</div>

<!-- TOAST -->
<div id="draftToast">
  <i class="fas fa-check-circle text-warning"></i>
  <span id="draftToastMsg">Draft saved locally!</span>
</div>

<script src="{{ asset('user/assets/vendor/libs/jquery/jquery.js') }}"></script>
<script>
  $(function() {
    // ── 1. NUMERIC ONLY INPUT VALIDATION (NO TEXT, NO EXPONENTS, MAX 2 DECIMALS) ──
    $('input[type="number"]').on('keydown', function(e) {
      // Allow navigation and action keys: backspace, delete, tab, escape, enter
      if ([46, 8, 9, 27, 13].indexOf(e.keyCode) !== -1 ||
          // Allow: Ctrl/Cmd+A, Ctrl/Cmd+C, Ctrl/Cmd+V, Ctrl/Cmd+X, Ctrl/Cmd+Z
          ((e.keyCode === 65 || e.keyCode === 67 || e.keyCode === 86 || e.keyCode === 88 || e.keyCode === 90) && (e.ctrlKey === true || e.metaKey === true)) ||
          // Allow: home, end, left, right, down, up
          (e.keyCode >= 35 && e.keyCode <= 40)) {
        return;
      }

      var isDecimalField = $(this).hasClass('num-field');
      var val = $(this).val() || '';

      // Decimal point (.) key: 190 (main) or 110 (numpad)
      if (e.keyCode === 190 || e.keyCode === 110) {
        if (!isDecimalField || val.indexOf('.') !== -1) {
          e.preventDefault(); // Block if integer field or dot already present
        }
        return;
      }

      // Check if pressed key is a digit (0-9)
      var isDigit = (!e.shiftKey && (e.keyCode >= 48 && e.keyCode <= 57)) || (e.keyCode >= 96 && e.keyCode <= 105);
      if (!isDigit) {
        e.preventDefault();
        return;
      }

      // Prevent typing more than two decimal places after the decimal point
      if (isDecimalField && val.indexOf('.') !== -1) {
        var dotIndex = val.indexOf('.');
        var selStart = this.selectionStart;
        var selEnd = this.selectionEnd;
        // If cursor is after the dot and no text selection will be overwritten
        if (selStart !== null && selStart > dotIndex && selStart === selEnd) {
          var decimals = val.substring(dotIndex + 1);
          if (decimals.length >= 2) {
            e.preventDefault();
            return;
          }
        }
      }
    });

    // Strip any illegal non-numeric characters and enforce max 2 decimals on input/paste
    $('input[type="number"]').on('input paste', function() {
      var $this = $(this);
      setTimeout(function() {
        var val = $this.val();
        if (!val) return;

        if ($this.hasClass('num-int-field')) {
          // Integer fields only allow whole digits
          var cleanInt = val.replace(/[^0-9]/g, '');
          if ($this.val() !== cleanInt) {
            $this.val(cleanInt);
          }
        } else if ($this.hasClass('num-field')) {
          // Decimal fields allow digits and at most 2 digits after the decimal point
          var cleaned = val.replace(/[^0-9.]/g, '');
          var parts = cleaned.split('.');
          if (parts.length > 2) {
            cleaned = parts[0] + '.' + parts.slice(1).join('');
          }
          if (cleaned.indexOf('.') !== -1) {
            var splitParts = cleaned.split('.');
            if (splitParts[1].length > 2) {
              cleaned = splitParts[0] + '.' + splitParts[1].substring(0, 2);
            }
          }
          if ($this.val() !== cleaned) {
            $this.val(cleaned);
          }
        }
      }, 5);
    });

    // ── 2. AUTO-CALCULATION OF METRICS ──
    function recalculate() {
      var netSales = parseFloat($('#netSalesInput').val()) || 0;
      var totalGuests = parseInt($('#totalGuestsInput').val()) || 0;
      var danceSold = parseFloat($('#danceDollarsInput').val()) || 0;
      var ipes = parseInt($('#ipesInput').val()) || 0;

      // Guest Average: Net Sales / Total Guests
      if (totalGuests > 0 && netSales > 0) {
        var guestAvg = (netSales / totalGuests).toFixed(2);
        if (!$('#guestAverageInput').is(':focus')) {
          $('#guestAverageInput').val(guestAvg);
        }
      }

      // Dance Average: Dance Dollars Sold / IPRs
      if (ipes > 0 && danceSold > 0) {
        var danceAvg = (danceSold / ipes).toFixed(2);
        if (!$('#danceAverageInput').is(':focus')) {
          $('#danceAverageInput').val(danceAvg);
        }
      }

      // Total Payouts: Taxi + ATM + Other
      var taxi = parseFloat($('#taxiPayoutInput').val()) || 0;
      var atm = parseFloat($('#atmPayoutInput').val()) || 0;
      var other = parseFloat($('#otherPayoutInput').val()) || 0;
      var sumPayouts = (taxi + atm + other).toFixed(2);

      if (!$('#totalPayoutsInput').is(':focus') && (taxi > 0 || atm > 0 || other > 0)) {
        $('#totalPayoutsInput').val(sumPayouts);
      }
    }

    $('#netSalesInput, #totalGuestsInput, #danceDollarsInput, #ipesInput, #taxiPayoutInput, #atmPayoutInput, #otherPayoutInput')
      .on('input change', recalculate);

    // ── 3. SAVE & RESTORE DRAFT VIA LOCALSTORAGE ──
    var draftKey = 'nightly_report_draft_data';

    $('#btnSaveDraft').on('click', function() {
      var formArray = $('#nightlyReportForm').serializeArray();
      var draftData = {};
      $.each(formArray, function(i, field) {
        if (field.name !== '_token') {
          draftData[field.name] = field.value;
        }
      });
      localStorage.setItem(draftKey, JSON.stringify(draftData));
      showToast('Draft saved successfully!');
    });

    function showToast(msg) {
      $('#draftToastMsg').text(msg);
      $('#draftToast').fadeIn(250).delay(2500).fadeOut(250);
    }

    // Auto-restore draft if available
    try {
      var savedDraft = localStorage.getItem(draftKey);
      if (savedDraft) {
        var parsed = JSON.parse(savedDraft);
        $.each(parsed, function(name, val) {
          if (val) {
            var $el = $('[name="' + name + '"]');
            if ($el.length && !$el.val()) {
              $el.val(val);
            }
          }
        });
      }
    } catch(e) {}

    // Clear draft upon successful form submission
    $('#nightlyReportForm').on('submit', function() {
      localStorage.removeItem(draftKey);
    });
  });
</script>
</body>
</html>
