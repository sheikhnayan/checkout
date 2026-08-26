@extends('admin.nightly-reports.layout')

@section('content')
<div class="container-fluid p-0">

  <!-- Header & Toolbar -->
  <div class="card mb-4">
    <div class="card-body">
      <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <div>
          <h4 class="text-white mb-1 fw-bold"><i class="fas fa-clipboard-list text-warning me-2"></i> Shift Operations Reports</h4>
          <p class="text-muted small mb-0">Master register of all Nightly, Boutique, and COH shift submissions.</p>
        </div>
        <div class="d-flex gap-2 align-items-center">
          <a href="{{ route('admin.nightly-reports.reports.export') }}" class="btn btn-sm btn-success">
            <i class="fas fa-download me-1"></i> Export CSV
          </a>
          <a href="{{ route('nightly.submit.nightly') }}" target="_blank" class="btn btn-sm btn-gold">
            <i class="fas fa-plus me-1"></i> Submit Report
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Alerts -->
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="fas fa-exclamation-triangle me-1"></i> {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="fas fa-exclamation-triangle me-1"></i> Validation Failed: {{ $errors->first() }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <!-- Filters -->
  <div class="card mb-4">
    <div class="card-body">
      <form method="GET" action="{{ route('admin.nightly-reports.reports.index') }}" class="row g-2">
        <div class="col-md-3">
          <select name="location_id" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="">All Locations</option>
            @foreach($locations as $loc)
              <option value="{{ $loc->id }}" {{ (string)$selectedLocationId === (string)$loc->id ? 'selected' : '' }}>
                {{ $loc->name }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}" placeholder="Start Date" />
        </div>
        <div class="col-md-2">
          <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}" placeholder="End Date" />
        </div>
        <div class="col-md-3">
          <input type="text" name="search" class="form-control form-control-sm" value="{{ $search }}" placeholder="Search submitter, notes..." />
        </div>
        <div class="col-md-2 d-flex gap-1">
          <button type="submit" class="btn btn-sm btn-primary flex-grow-1"><i class="fas fa-search me-1"></i> Filter</button>
          <a href="{{ route('admin.nightly-reports.reports.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-redo"></i></a>
        </div>
      </form>
    </div>
  </div>

  <!-- Reports Table -->
  <div class="card">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>Date</th>
            <th>Location</th>
            <th>Net Sales</th>
            <th>Nightly Goal</th>
            <th>Guests</th>
            <th>Avg Spend</th>
            <th>Submitter</th>
            <th>Weather</th>
            <th>Flags</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($reports as $r)
          <tr>
            <td class="fw-bold text-white">{{ $r->business_date->format('M d, Y') }}</td>
            <td>
              <div class="fw-bold text-white">{{ $r->location->name ?? 'Venue' }}</div>
              <div class="small text-muted">{{ $r->location->type ?? '' }}</div>
            </td>
            <td><span class="text-success fw-bold">${{ number_format($r->net_sales, 2) }}</span></td>
            <td><span class="text-muted">${{ number_format($r->nightly_goal, 2) }}</span></td>
            <td>{{ number_format($r->total_guests) }}</td>
            <td><span class="text-warning fw-semibold">${{ number_format($r->guest_average, 2) }}</span></td>
            <td>
              <div class="text-white small">{{ $r->submitter_name }}</div>
              <div class="text-muted small">{{ $r->submitter_email }}</div>
            </td>
            <td><small class="text-muted">{{ $r->weather ?? '—' }}</small></td>
            <td>
              @if($r->incident_flag)
                <span class="badge bg-danger"><i class="fas fa-exclamation-circle me-1"></i> Incident</span>
              @else
                <span class="text-muted small">None</span>
              @endif
            </td>
            <td class="text-end">
              <div class="btn-group btn-group-sm">
                <a href="{{ route('admin.nightly-reports.reports.show', ['type' => 'nightly', 'id' => $r->id]) }}" class="btn btn-outline-light" title="View Full Report">
                  <i class="fas fa-eye"></i>
                </a>
                <a href="{{ route('admin.nightly-reports.reports.edit', ['type' => 'nightly', 'id' => $r->id]) }}" class="btn btn-outline-warning" title="Edit Numbers">
                  <i class="fas fa-edit"></i>
                </a>
                <a href="{{ route('admin.nightly-reports.reports.email-preview', ['type' => 'nightly', 'id' => $r->id]) }}" class="btn btn-outline-info" title="Preview Email Briefing">
                  <i class="fas fa-envelope"></i>
                </a>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="10" class="text-center py-5 text-muted">
              <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
              <div>No shift reports match your search criteria.</div>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($reports->hasPages())
    <div class="card-footer py-2">
      {{ $reports->links() }}
    </div>
    @endif
  </div>

</div>
@endsection
