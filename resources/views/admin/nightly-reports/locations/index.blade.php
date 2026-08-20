@extends('admin.nightly-reports.layout')

@section('content')
<div class="container-fluid p-0">
  <div class="card mb-4">
    <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-3">
      <div>
        <h4 class="text-white mb-1 fw-bold"><i class="fas fa-map-marker-alt text-warning me-2"></i> Locations & Venues Directory</h4>
        <p class="text-muted small mb-0">Manage corporate venues, nightly sales targets, break-even targets, and GM contact routing.</p>
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
            <th>Location / Club</th>
            <th>Type</th>
            <th>City / State</th>
            <th>Nightly Goal</th>
            <th>Break-Even</th>
            <th>Historical Best</th>
            <th>GM Contact</th>
            <th>Status</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($locations as $loc)
          <tr>
            <td>
              <div class="fw-bold text-white">{{ $loc->name }}</div>
              @if($loc->website)
                <div class="small text-warning"><i class="fas fa-link me-1"></i> Mapped: {{ $loc->website->website_name }}</div>
              @endif
            </td>
            <td><span class="badge bg-secondary">{{ $loc->type }}</span></td>
            <td><small class="text-white">{{ $loc->city }}, {{ $loc->state }}</small></td>
            <td><span class="text-warning fw-semibold">${{ number_format($loc->nightly_goal, 0) }}</span></td>
            <td><span class="text-info fw-semibold">${{ number_format($loc->break_even, 0) }}</span></td>
            <td><span class="text-success fw-semibold">${{ number_format($loc->historical_best, 0) }}</span></td>
            <td>
              <div class="small text-white">{{ $loc->gm_name ?? '—' }}</div>
              <div class="small text-muted">{{ $loc->gm_email ?? '—' }}</div>
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

          <!-- Edit Modal -->
          <div class="modal fade" id="editModal{{ $loc->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
              <div class="modal-content" style="background: var(--nr-surface-2); border-color: var(--nr-border);">
                <form method="POST" action="{{ route('admin.nightly-reports.locations.update', $loc->id) }}">
                  @csrf
                  @method('PUT')
                  <div class="modal-header" style="border-bottom: 1px solid var(--nr-border);">
                    <h5 class="modal-title text-white fw-bold">Edit Location: {{ $loc->name }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                    <div class="row g-3">
                      <div class="col-md-8">
                        <label class="form-label text-muted small fw-bold">Location Name</label>
                        <input type="text" name="name" class="form-control" value="{{ $loc->name }}" required />
                      </div>
                      <div class="col-md-4">
                        <label class="form-label text-muted small fw-bold">Venue Type</label>
                        <select name="type" class="form-select" required>
                          <option value="Adult with Liquor" {{ $loc->type === 'Adult with Liquor' ? 'selected' : '' }}>Adult with Liquor</option>
                          <option value="Adult Alcohol Free" {{ $loc->type === 'Adult Alcohol Free' ? 'selected' : '' }}>Adult Alcohol Free</option>
                          <option value="Bar/Night Club" {{ $loc->type === 'Bar/Night Club' ? 'selected' : '' }}>Bar/Night Club</option>
                          <option value="Boutique" {{ $loc->type === 'Boutique' ? 'selected' : '' }}>Boutique</option>
                        </select>
                      </div>
                      <div class="col-md-6">
                        <label class="form-label text-muted small fw-bold">Map to CartVIP Website (Optional)</label>
                        <select name="website_id" class="form-select">
                          <option value="">-- Standalone Location --</option>
                          @foreach($websites as $web)
                            <option value="{{ $web->id }}" {{ (string)$loc->website_id === (string)$web->id ? 'selected' : '' }}>
                              {{ $web->website_name }} ({{ $web->domain ?? $web->slug }})
                            </option>
                          @endforeach
                        </select>
                      </div>
                      <div class="col-md-3">
                        <label class="form-label text-muted small fw-bold">City</label>
                        <input type="text" name="city" class="form-control" value="{{ $loc->city }}" />
                      </div>
                      <div class="col-md-3">
                        <label class="form-label text-muted small fw-bold">State</label>
                        <input type="text" name="state" class="form-control" value="{{ $loc->state }}" />
                      </div>
                      <div class="col-md-4">
                        <label class="form-label text-muted small fw-bold">Nightly Sales Goal ($)</label>
                        <input type="number" step="0.01" name="nightly_goal" class="form-control" value="{{ $loc->nightly_goal }}" />
                      </div>
                      <div class="col-md-4">
                        <label class="form-label text-muted small fw-bold">Monthly Break-Even ($)</label>
                        <input type="number" step="0.01" name="break_even" class="form-control" value="{{ $loc->break_even }}" />
                      </div>
                      <div class="col-md-4">
                        <label class="form-label text-muted small fw-bold">Historical Best Sales ($)</label>
                        <input type="number" step="0.01" name="historical_best" class="form-control" value="{{ $loc->historical_best }}" />
                      </div>
                      <div class="col-md-6">
                        <label class="form-label text-muted small fw-bold">General Manager Name</label>
                        <input type="text" name="gm_name" class="form-control" value="{{ $loc->gm_name }}" />
                      </div>
                      <div class="col-md-6">
                        <label class="form-label text-muted small fw-bold">GM Email Address</label>
                        <input type="email" name="gm_email" class="form-control" value="{{ $loc->gm_email }}" />
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer" style="border-top: 1px solid var(--nr-border);">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-gold">Save Changes</button>
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

  <!-- Add Location Modal -->
  <div class="modal fade" id="addLocationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content" style="background: var(--nr-surface-2); border-color: var(--nr-border);">
        <form method="POST" action="{{ route('admin.nightly-reports.locations.store') }}">
          @csrf
          <div class="modal-header" style="border-bottom: 1px solid var(--nr-border);">
            <h5 class="modal-title text-white fw-bold">Add New Location</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="row g-3">
              <div class="col-md-8">
                <label class="form-label text-muted small fw-bold">Location Name</label>
                <input type="text" name="name" class="form-control" required placeholder="e.g. Larry Flynt's Hustler Club Miami" />
              </div>
              <div class="col-md-4">
                <label class="form-label text-muted small fw-bold">Venue Type</label>
                <select name="type" class="form-select" required>
                  <option value="Adult with Liquor">Adult with Liquor</option>
                  <option value="Adult Alcohol Free">Adult Alcohol Free</option>
                  <option value="Bar/Night Club">Bar/Night Club</option>
                  <option value="Boutique">Boutique</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label text-muted small fw-bold">Map to CartVIP Website (Optional)</label>
                <select name="website_id" class="form-select">
                  <option value="">-- Standalone Location --</option>
                  @foreach($websites as $web)
                    <option value="{{ $web->id }}">{{ $web->website_name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label text-muted small fw-bold">City</label>
                <input type="text" name="city" class="form-control" />
              </div>
              <div class="col-md-3">
                <label class="form-label text-muted small fw-bold">State</label>
                <input type="text" name="state" class="form-control" />
              </div>
              <div class="col-md-4">
                <label class="form-label text-muted small fw-bold">Nightly Sales Goal ($)</label>
                <input type="number" step="0.01" name="nightly_goal" class="form-control" placeholder="15000" />
              </div>
              <div class="col-md-4">
                <label class="form-label text-muted small fw-bold">Monthly Break-Even ($)</label>
                <input type="number" step="0.01" name="break_even" class="form-control" placeholder="250000" />
              </div>
              <div class="col-md-4">
                <label class="form-label text-muted small fw-bold">Historical Best Sales ($)</label>
                <input type="number" step="0.01" name="historical_best" class="form-control" placeholder="45000" />
              </div>
              <div class="col-md-6">
                <label class="form-label text-muted small fw-bold">General Manager Name</label>
                <input type="text" name="gm_name" class="form-control" />
              </div>
              <div class="col-md-6">
                <label class="form-label text-muted small fw-bold">GM Email Address</label>
                <input type="email" name="gm_email" class="form-control" />
              </div>
            </div>
          </div>
          <div class="modal-footer" style="border-top: 1px solid var(--nr-border);">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-gold">Create Location</button>
          </div>
        </form>
      </div>
    </div>
  </div>

</div>
@endsection
