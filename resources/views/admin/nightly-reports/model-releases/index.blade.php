@extends('admin.nightly-reports.layout')

@section('content')
<div class="container-fluid p-0">
  <div class="card mb-4">
    <div class="card-body">
      <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <div>
          <h4 class="text-white mb-1 fw-bold"><i class="fas fa-id-card text-warning me-2"></i> Model Release Legal Vault</h4>
          <p class="text-muted small mb-0">Digital 18+ age verification archives, signed talent release forms, and photo identification scans.</p>
        </div>
      </div>

      <!-- Filters -->
      <form method="GET" action="{{ route('admin.nightly-reports.model-releases.index') }}" class="row g-2">
        <div class="col-md-4">
          <select name="location_id" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="">All Locations</option>
            @foreach($locations as $loc)
              <option value="{{ $loc->id }}" {{ (string)$selectedLocationId === (string)$loc->id ? 'selected' : '' }}>
                {{ $loc->name }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-6">
          <input type="text" name="search" class="form-control form-control-sm" value="{{ $search }}" placeholder="Search performer legal name, stage name, email..." />
        </div>
        <div class="col-md-2">
          <button type="submit" class="btn btn-sm btn-primary w-100"><i class="fas fa-search me-1"></i> Search</button>
        </div>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>Shoot Date</th>
            <th>Performer Legal Name</th>
            <th>Stage Name</th>
            <th>DOB & Age</th>
            <th>Venue</th>
            <th>Contact</th>
            <th>Compliance Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse($releases as $rel)
          <tr>
            <td class="fw-bold text-white">{{ $rel->shoot_date->format('M d, Y') }}</td>
            <td class="fw-bold text-white">{{ $rel->performer_legal_name }}</td>
            <td><span class="badge bg-secondary">{{ $rel->stage_name ?? '—' }}</span></td>
            <td>
              <div class="small text-white">{{ $rel->date_of_birth->format('M d, Y') }}</div>
              <small class="text-success">Verified 18+</small>
            </td>
            <td><small class="text-muted">{{ $rel->location->name ?? 'Corporate / All' }}</small></td>
            <td>
              <div class="small text-white">{{ $rel->phone ?? '—' }}</div>
              <div class="small text-muted">{{ $rel->email ?? '—' }}</div>
            </td>
            <td>
              <span class="badge bg-success"><i class="fas fa-check-shield me-1"></i> Signed Legal Release</span>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7" class="text-center py-5 text-muted">No model release records found in vault.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
