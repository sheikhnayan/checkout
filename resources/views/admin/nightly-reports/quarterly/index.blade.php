@extends('admin.nightly-reports.layout')

@section('content')
<div class="container-fluid p-0">
  <div class="card mb-4">
    <div class="card-body">
      <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <div>
          <h4 class="text-white mb-1 fw-bold"><i class="fas fa-layer-group text-warning me-2"></i> Quarterly Executive Rollup</h4>
          <p class="text-muted small mb-0">Consolidated Q1, Q2, Q3, and Q4 revenue performance across venues.</p>
        </div>
      </div>

      <form method="GET" action="{{ route('admin.nightly-reports.quarterly.index') }}" class="row g-2">
        <div class="col-md-3">
          <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
            @for($y = \Carbon\Carbon::now()->year; $y >= \Carbon\Carbon::now()->year - 4; $y--)
              <option value="{{ $y }}" {{ $year === $y ? 'selected' : '' }}>{{ $y }} Financial Year</option>
            @endfor
          </select>
        </div>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>Venue / Club</th>
            <th>Q1 (Jan–Mar)</th>
            <th>Q2 (Apr–Jun)</th>
            <th>Q3 (Jul–Sep)</th>
            <th>Q4 (Oct–Dec)</th>
            <th>Annual Net Total</th>
          </tr>
        </thead>
        <tbody>
          @foreach($quarterGrid as $row)
          <tr>
            <td>
              <div class="fw-bold text-white">{{ $row['location_name'] }}</div>
            </td>
            <td>${{ number_format($row['q1'], 0) }}</td>
            <td>${{ number_format($row['q2'], 0) }}</td>
            <td>${{ number_format($row['q3'], 0) }}</td>
            <td>${{ number_format($row['q4'], 0) }}</td>
            <td><span class="text-success fw-bold">${{ number_format($row['total'], 0) }}</span></td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
