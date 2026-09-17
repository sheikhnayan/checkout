@extends('admin.nightly-reports.layout')

@section('content')
<style>
  /* ── PORTAL MODAL LUXURY THEME (MATCHES ACTUAL PORTAL) ── */
  .modal-portal .modal-content {
    background: #0d1726 !important;
    border: 1px solid #1e2f47 !important;
    border-radius: 18px !important;
    box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.85) !important;
    color: #f1f5f9;
  }
  .modal-portal .modal-header {
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    padding: 22px 26px 14px 26px;
  }
  .modal-portal .modal-title {
    font-family: 'Playfair Display', Georgia, serif;
    color: #d9a05b;
    font-size: 1.45rem;
    font-weight: 600;
    letter-spacing: 0.01em;
  }
  .modal-portal .btn-close-portal {
    background: transparent;
    border: none;
    color: #64748b;
    font-size: 1.4rem;
    line-height: 1;
    padding: 4px 8px;
    cursor: pointer;
    transition: color 0.15s ease;
  }
  .modal-portal .btn-close-portal:hover {
    color: #f1f5f9;
  }
  .modal-portal .modal-body {
    padding: 20px 26px 24px 26px;
  }
  .modal-portal .modal-footer {
    border-top: 1px solid rgba(255, 255, 255, 0.05);
    padding: 14px 26px 22px 26px;
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 12px;
  }

  /* Portal Field Group & Inputs */
  .portal-field-group {
    margin-bottom: 16px;
  }
  .portal-label {
    display: block;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.07em;
    text-transform: uppercase;
    color: #8da2be;
    margin-bottom: 7px;
  }
  .portal-input, .portal-select {
    background-color: #121e2f !important;
    border: 1px solid #1f314a !important;
    color: #f1f5f9 !important;
    border-radius: 10px !important;
    padding: 11px 15px !important;
    font-size: 0.92rem !important;
    width: 100% !important;
    box-sizing: border-box;
    transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
  }
  .portal-input::placeholder {
    color: #43546b !important;
  }
  .portal-input:focus, .portal-select:focus {
    border-color: #d9a05b !important;
    box-shadow: 0 0 0 2px rgba(217, 160, 91, 0.25) !important;
    background-color: #152438 !important;
    outline: none !important;
  }

  /* ── MODERN DAY-WISE GOALS SECTION (SPACIOUS & NOT CLUSTERED) ── */
  .goals-section-container {
    background: linear-gradient(180deg, rgba(16, 27, 43, 0.7) 0%, rgba(11, 19, 31, 0.9) 100%);
    border: 1px solid #1c2e47;
    border-radius: 14px;
    padding: 18px 18px;
    margin-top: 22px;
    margin-bottom: 4px;
  }
  .goals-section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 14px;
  }
  .goal-card-item {
    background: #0f1a2a;
    border: 1px solid #1c2d44;
    border-radius: 10px;
    padding: 8px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    transition: all 0.2s ease;
  }
  .goal-card-item:hover, .goal-card-item:focus-within {
    border-color: #d9a05b;
    background: #142236;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.35);
  }
  .goal-day-info {
    display: flex;
    align-items: center;
    gap: 9px;
  }
  .goal-day-pill {
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.05em;
    padding: 3px 7px;
    border-radius: 6px;
    background: #19273c;
    color: #94a3b8;
  }
  .goal-day-pill.weekend {
    background: rgba(217, 160, 91, 0.2);
    color: #e8b878;
    border: 1px solid rgba(217, 160, 91, 0.4);
  }
  .goal-day-name {
    font-size: 0.84rem;
    font-weight: 600;
    color: #e2e8f0;
  }
  .goal-input-box {
    display: flex;
    align-items: center;
    background: #121e2f;
    border: 1px solid #20334d;
    border-radius: 8px;
    padding: 0 10px;
    width: 125px;
    transition: border-color 0.2s ease;
  }
  .goal-card-item:focus-within .goal-input-box {
    border-color: #d9a05b;
  }
  .goal-dollar {
    color: #d9a05b;
    font-weight: 600;
    font-size: 0.85rem;
    margin-right: 4px;
    user-select: none;
  }
  .goal-amount-input {
    background: transparent !important;
    border: none !important;
    color: #f8fafc !important;
    font-size: 0.88rem !important;
    font-weight: 600 !important;
    width: 100% !important;
    padding: 7px 0 !important;
    outline: none !important;
    text-align: right !important;
  }
  .goal-amount-input:focus {
    box-shadow: none !important;
    outline: none !important;
  }

  /* Remove number input increment/decrement spinner arrows */
  .goal-amount-input::-webkit-outer-spin-button,
  .goal-amount-input::-webkit-inner-spin-button,
  .modal-portal input[type="number"]::-webkit-outer-spin-button,
  .modal-portal input[type="number"]::-webkit-inner-spin-button {
    -webkit-appearance: none !important;
    margin: 0 !important;
  }
  .goal-amount-input,
  .modal-portal input[type="number"] {
    -moz-appearance: textfield !important;
    appearance: textfield !important;
  }

  /* Portal Modal Action Buttons */
  .btn-portal-cancel {
    background: transparent;
    border: 1px solid #2b3e58;
    color: #cbd5e1;
    border-radius: 9999px;
    padding: 9px 24px;
    font-size: 0.88rem;
    font-weight: 500;
    transition: all 0.2s ease;
  }
  .btn-portal-cancel:hover {
    background: #16253b;
    color: #fff;
    border-color: #405777;
  }
  .btn-portal-submit {
    background: #d9a05b;
    border: none;
    color: #0b1320;
    border-radius: 9999px;
    padding: 9px 28px;
    font-size: 0.88rem;
    font-weight: 600;
    transition: all 0.2s ease;
    box-shadow: 0 2px 10px rgba(217, 160, 91, 0.25);
  }
  .btn-portal-submit:hover {
    background: #e6b16e;
    color: #000;
    box-shadow: 0 4px 18px rgba(217, 160, 91, 0.45);
  }
</style>

<div class="container-fluid p-0">
  <div class="card mb-4">
    <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-3">
      <div>
        <h4 class="text-white mb-1 fw-bold"><i class="fas fa-map-marker-alt text-warning me-2"></i> Locations & Venues Directory</h4>
        <p class="text-muted small mb-0">Manage corporate venues, legal entities, day-wise sales targets, and notification inboxes.</p>
      </div>
      <button class="btn btn-sm btn-gold" data-bs-toggle="modal" data-bs-target="#addLocationModal">
        <i class="fas fa-plus me-1"></i> Add Location
      </button>
    </div>
  </div>

  <div class="card">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>Club / DBA Name</th>
            <th>Type</th>
            <th>Club Inbox</th>
            <th>GM Email</th>
            <th>Nightly Goals</th>
            <th>Status</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($locations as $loc)
          <tr>
            <td>
              <div class="fw-bold text-white">{{ $loc->name }}</div>
              @if(!empty($loc->legal_name))
                <div class="small text-muted" style="font-size: 0.76rem;"><i class="fas fa-building me-1 opacity-50"></i>{{ $loc->legal_name }}</div>
              @endif
              @if($loc->website)
                <div class="small text-warning" style="font-size: 0.72rem;"><i class="fas fa-link me-1"></i> Mapped: {{ $loc->website->name }}</div>
              @endif
            </td>
            <td><span class="badge bg-secondary">{{ $loc->type }}</span></td>
            <td>
              <div class="small text-white">{{ $loc->club_inbox_email ?? '—' }}</div>
            </td>
            <td>
              <div class="small text-muted">{{ $loc->gm_email ?? '—' }}</div>
            </td>
            <td>
              <span class="text-warning fw-semibold">${{ number_format($loc->nightly_goal ?? 0, 0) }}</span>
              @if(!empty($loc->nightly_goals) && is_array($loc->nightly_goals))
                <div class="mt-1">
                  <span class="badge bg-dark text-warning border border-warning-subtle" style="font-size: 0.65rem;" title="Mon: ${{ number_format($loc->nightly_goals['monday'] ?? 0) }} | Tue: ${{ number_format($loc->nightly_goals['tuesday'] ?? 0) }} | Wed: ${{ number_format($loc->nightly_goals['wednesday'] ?? 0) }} | Thu: ${{ number_format($loc->nightly_goals['thursday'] ?? 0) }} | Fri: ${{ number_format($loc->nightly_goals['friday'] ?? 0) }} | Sat: ${{ number_format($loc->nightly_goals['saturday'] ?? 0) }} | Sun: ${{ number_format($loc->nightly_goals['sunday'] ?? 0) }}">
                    <i class="fas fa-calendar-alt me-1"></i>7-Day Goals Set
                  </span>
                </div>
              @endif
            </td>
            <td>
              @if($loc->active)
                <span class="badge bg-success">Active</span>
              @else
                <span class="badge bg-secondary">Inactive</span>
              @endif
            </td>
            <td class="text-end">
              <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $loc->id }}" title="Edit Details">
                <i class="fas fa-edit"></i>
              </button>
              <form method="POST" action="{{ route('admin.nightly-reports.locations.toggle-active', $loc->id) }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm {{ $loc->active ? 'btn-outline-danger' : 'btn-outline-success' }}" title="Toggle Status">
                  <i class="fas {{ $loc->active ? 'fa-ban' : 'fa-check' }}"></i>
                </button>
              </form>
            </td>
          </tr>

          <!-- Edit Location Modal (Matches Actual Portal Layout) -->
          <div class="modal fade modal-portal" id="editModal{{ $loc->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 580px;">
              <div class="modal-content">
                <form method="POST" action="{{ route('admin.nightly-reports.locations.update', $loc->id) }}">
                  @csrf
                  @method('PUT')
                  <div class="modal-header d-flex align-items-center justify-content-between">
                    <h5 class="modal-title">Edit Location</h5>
                    <button type="button" class="btn-close-portal" data-bs-dismiss="modal" aria-label="Close">&times;</button>
                  </div>
                  <div class="modal-body">
                    <div class="portal-field-group">
                      <label class="portal-label">LEGAL LLC NAME</label>
                      <input type="text" name="legal_name" class="portal-input" value="{{ $loc->legal_name }}" placeholder="Acme Enterprises, LLC" />
                    </div>

                    <div class="portal-field-group">
                      <label class="portal-label">CLUB / DBA NAME</label>
                      <input type="text" name="name" class="portal-input" value="{{ $loc->name }}" placeholder="Club XYZ" required />
                    </div>

                    <div class="portal-field-group">
                      <label class="portal-label">LOCATION TYPE</label>
                      <select name="type" class="portal-select" required>
                        <option value="Adult with Liquor" {{ $loc->type === 'Adult with Liquor' ? 'selected' : '' }}>Adult with Liquor</option>
                        <option value="Adult Juice Bar" {{ $loc->type === 'Adult Juice Bar' ? 'selected' : '' }}>Adult Juice Bar</option>
                        <option value="Adult Alcohol Free" {{ $loc->type === 'Adult Alcohol Free' ? 'selected' : '' }}>Adult Alcohol Free</option>
                        <option value="Bar/Night Club" {{ $loc->type === 'Bar/Night Club' ? 'selected' : '' }}>Bar/Night Club</option>
                        <option value="Boutique" {{ $loc->type === 'Boutique' ? 'selected' : '' }}>Boutique</option>
                      </select>
                    </div>

                    <div class="portal-field-group">
                      <label class="portal-label">GM EMAIL (OPTIONAL)</label>
                      <input type="email" name="gm_email" class="portal-input" value="{{ $loc->gm_email }}" placeholder="gm@venue.com" />
                    </div>

                    <div class="portal-field-group">
                      <label class="portal-label">CLUB INBOX EMAIL</label>
                      <input type="email" name="club_inbox_email" class="portal-input" value="{{ $loc->club_inbox_email }}" placeholder="club@venue.com" />
                    </div>

                    <!-- Modern Day-Wise Nightly Goals Grid -->
                    <div class="goals-section-container">
                      <div class="goals-section-header">
                        <div>
                          <span class="portal-label text-warning mb-0" style="letter-spacing: 0.08em;">NIGHTLY REVENUE GOALS</span>
                          <div class="text-muted small" style="font-size: 0.72rem;">Set target for each day of the week (auto-populates report)</div>
                        </div>
                      </div>

                      <div class="row g-2">
                        @php
                          $dayItems = [
                            'monday' => ['name' => 'Monday', 'tag' => 'MON', 'weekend' => false],
                            'tuesday' => ['name' => 'Tuesday', 'tag' => 'TUE', 'weekend' => false],
                            'wednesday' => ['name' => 'Wednesday', 'tag' => 'WED', 'weekend' => false],
                            'thursday' => ['name' => 'Thursday', 'tag' => 'THU', 'weekend' => false],
                            'friday' => ['name' => 'Friday', 'tag' => 'FRI', 'weekend' => false],
                            'saturday' => ['name' => 'Saturday', 'tag' => 'SAT', 'weekend' => true],
                            'sunday' => ['name' => 'Sunday', 'tag' => 'SUN', 'weekend' => true],
                          ];
                        @endphp

                        @foreach($dayItems as $dKey => $info)
                          <div class="col-12 col-sm-6">
                            <div class="goal-card-item">
                              <div class="goal-day-info">
                                <span class="goal-day-pill {{ $info['weekend'] ? 'weekend' : '' }}">{{ $info['tag'] }}</span>
                                <span class="goal-day-name">{{ $info['name'] }}</span>
                              </div>
                              <div class="goal-input-box">
                                <span class="goal-dollar">$</span>
                                <input type="number" step="0.01" min="0" name="nightly_goals[{{ $dKey }}]" class="goal-amount-input" value="{{ $loc->nightly_goals[$dKey] ?? '' }}" placeholder="0.00" onwheel="this.blur()" />
                              </div>
                            </div>
                          </div>
                        @endforeach

                        <!-- Baseline Fallback Target -->
                        <div class="col-12 col-sm-6">
                          <div class="goal-card-item" style="border-style: dashed; border-color: rgba(217, 160, 91, 0.35);">
                            <div class="goal-day-info">
                              <span class="goal-day-pill" style="background: rgba(255, 255, 255, 0.06); color: #cbd5e1;">BASE</span>
                              <span class="goal-day-name text-muted">Baseline Goal</span>
                            </div>
                            <div class="goal-input-box">
                              <span class="goal-dollar">$</span>
                              <input type="number" step="0.01" min="0" name="nightly_goal" class="goal-amount-input" value="{{ $loc->nightly_goal }}" placeholder="0.00" />
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-portal-cancel" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-portal-submit">Save Changes</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  <!-- Add Location Modal (Exact Match to Actual Nightly Reports Portal) -->
  <div class="modal fade modal-portal" id="addLocationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 580px;">
      <div class="modal-content">
        <form method="POST" action="{{ route('admin.nightly-reports.locations.store') }}">
          @csrf
          <div class="modal-header d-flex align-items-center justify-content-between">
            <h5 class="modal-title">New Location</h5>
            <button type="button" class="btn-close-portal" data-bs-dismiss="modal" aria-label="Close">&times;</button>
          </div>
          <div class="modal-body">
            <div class="portal-field-group">
              <label class="portal-label">LEGAL LLC NAME</label>
              <input type="text" name="legal_name" class="portal-input" placeholder="Acme Enterprises, LLC" />
            </div>

            <div class="portal-field-group">
              <label class="portal-label">CLUB / DBA NAME</label>
              <input type="text" name="name" class="portal-input" placeholder="Club XYZ" required />
            </div>

            <div class="portal-field-group">
              <label class="portal-label">LOCATION TYPE</label>
              <select name="type" class="portal-select" required>
                <option value="Adult with Liquor">Adult with Liquor</option>
                <option value="Adult Juice Bar">Adult Juice Bar</option>
                <option value="Adult Alcohol Free">Adult Alcohol Free</option>
                <option value="Bar/Night Club">Bar/Night Club</option>
                <option value="Boutique">Boutique</option>
              </select>
            </div>

            <div class="portal-field-group">
              <label class="portal-label">GM EMAIL (OPTIONAL)</label>
              <input type="email" name="gm_email" class="portal-input" placeholder="gm@venue.com" />
            </div>

            <div class="portal-field-group">
              <label class="portal-label">CLUB INBOX EMAIL</label>
              <input type="email" name="club_inbox_email" class="portal-input" placeholder="club@venue.com" />
            </div>

            <!-- Modern Day-Wise Nightly Goals Grid -->
            <div class="goals-section-container">
              <div class="goals-section-header">
                <div>
                  <span class="portal-label text-warning mb-0" style="letter-spacing: 0.08em;">NIGHTLY REVENUE GOALS</span>
                  <div class="text-muted small" style="font-size: 0.72rem;">Set target for each day of the week (auto-populates report)</div>
                </div>
              </div>

              <div class="row g-2">
                @php
                  $dayItems = [
                    'monday' => ['name' => 'Monday', 'tag' => 'MON', 'weekend' => false],
                    'tuesday' => ['name' => 'Tuesday', 'tag' => 'TUE', 'weekend' => false],
                    'wednesday' => ['name' => 'Wednesday', 'tag' => 'WED', 'weekend' => false],
                    'thursday' => ['name' => 'Thursday', 'tag' => 'THU', 'weekend' => false],
                    'friday' => ['name' => 'Friday', 'tag' => 'FRI', 'weekend' => false],
                    'saturday' => ['name' => 'Saturday', 'tag' => 'SAT', 'weekend' => true],
                    'sunday' => ['name' => 'Sunday', 'tag' => 'SUN', 'weekend' => true],
                  ];
                @endphp

                @foreach($dayItems as $dKey => $info)
                  <div class="col-12 col-sm-6">
                    <div class="goal-card-item">
                      <div class="goal-day-info">
                        <span class="goal-day-pill {{ $info['weekend'] ? 'weekend' : '' }}">{{ $info['tag'] }}</span>
                        <span class="goal-day-name">{{ $info['name'] }}</span>
                      </div>
                      <div class="goal-input-box">
                        <span class="goal-dollar">$</span>
                        <input type="number" step="0.01" min="0" name="nightly_goals[{{ $dKey }}]" class="goal-amount-input" placeholder="0.00" onwheel="this.blur()" />
                      </div>
                    </div>
                  </div>
                @endforeach

                <!-- Baseline Fallback Target -->
                <div class="col-12 col-sm-6">
                  <div class="goal-card-item" style="border-style: dashed; border-color: rgba(217, 160, 91, 0.35);">
                    <div class="goal-day-info">
                      <span class="goal-day-pill" style="background: rgba(255, 255, 255, 0.06); color: #cbd5e1;">BASE</span>
                      <span class="goal-day-name text-muted">Baseline Goal</span>
                    </div>
                    <div class="goal-input-box">
                      <span class="goal-dollar">$</span>
                      <input type="number" step="0.01" min="0" name="nightly_goal" class="goal-amount-input" placeholder="0.00" onwheel="this.blur()" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-portal-cancel" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-portal-submit">Create Location</button>
          </div>
        </form>
      </div>
    </div>
  </div>

</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Prevent mouse wheel increment/decrement on all number inputs in modals
    document.querySelectorAll('.modal-portal input[type="number"]').forEach(function(input) {
      input.addEventListener('wheel', function(e) {
        e.preventDefault();
        input.blur();
      }, { passive: false });

      // Enforce 2 decimal places max
      input.addEventListener('input', function() {
        var val = this.value;
        if (val.indexOf('.') !== -1) {
          var parts = val.split('.');
          if (parts[1].length > 2) {
            this.value = parts[0] + '.' + parts[1].substring(0, 2);
          }
        }
      });
    });
  });
</script>
@endsection
