@extends('admin.nightly-reports.layout')

@section('content')
<div class="container-fluid p-0">
  <div class="card mb-4">
    <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-3">
      <div>
        <h4 class="text-white mb-1 fw-bold"><i class="fas fa-bullseye text-warning me-2"></i> Venue Benchmarks & Target Records</h4>
        <p class="text-muted small mb-0">Set Historical Best Sales records and monthly Break-Even pacing targets per venue.</p>
      </div>
      <form method="POST" action="{{ route('admin.nightly-reports.benchmarks.send-email') }}" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-sm btn-gold">
          <i class="fas fa-paper-plane me-1"></i> Dispatch Executive Email Briefing
        </button>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>Venue / Club</th>
            <th>Historical Best Sales ($)</th>
            <th>Monthly Break-Even ($)</th>
            <th class="text-end">Update Targets</th>
          </tr>
        </thead>
        <tbody>
          @foreach($locations as $loc)
          <tr>
            <td class="fw-bold text-white">{{ $loc->name }}</td>
            <td><span class="text-success fw-bold">${{ number_format($loc->historical_best ?? 0, 2) }}</span></td>
            <td><span class="text-info fw-bold">${{ number_format($loc->break_even ?? 0, 2) }}</span></td>
            <td class="text-end">
              <form method="POST" action="{{ route('admin.nightly-reports.benchmarks.upsert') }}" class="d-inline-flex gap-2 justify-content-end align-items-center">
                @csrf
                <input type="hidden" name="location_id" value="{{ $loc->id }}" />
                <input type="number" step="0.01" name="historical_best" class="form-control form-control-sm" style="width: 130px;" value="{{ $loc->historical_best }}" placeholder="Hist Best" />
                <input type="number" step="0.01" name="break_even" class="form-control form-control-sm" style="width: 130px;" value="{{ $loc->break_even }}" placeholder="Break-Even" />
                <button type="submit" class="btn btn-sm btn-outline-warning"><i class="fas fa-save"></i></button>
              </form>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
