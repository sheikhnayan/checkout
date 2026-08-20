@extends('admin.nightly-reports.layout')

@section('content')
<div class="container-fluid p-0">
  <div class="card mb-4">
    <div class="card-body">
      <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
          <h4 class="text-white mb-1 fw-bold"><i class="fas fa-calendar-alt text-warning me-2"></i> 4-Week Rolling Revenue Velocity</h4>
          <p class="text-muted small mb-0">28-day rolling moving average analysis and week-over-week growth trajectory across venues.</p>
        </div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>Venue / Club</th>
            <th>Week 1 (Oldest)</th>
            <th>Week 2</th>
            <th>Week 3</th>
            <th>Week 4 (Recent)</th>
            <th>4-Week Average</th>
            <th>Trend Direction</th>
          </tr>
        </thead>
        <tbody>
          @foreach($fourWeekGrid as $row)
          <tr>
            <td>
              <div class="fw-bold text-white">{{ $row['location_name'] }}</div>
              <div class="small text-muted">{{ $row['location_type'] }}</div>
            </td>
            <td>${{ number_format($row['week_1'], 0) }}</td>
            <td>${{ number_format($row['week_2'], 0) }}</td>
            <td>${{ number_format($row['week_3'], 0) }}</td>
            <td><span class="fw-bold text-white">${{ number_format($row['week_4'], 0) }}</span></td>
            <td><span class="text-warning fw-bold">${{ number_format($row['average'], 0) }}</span></td>
            <td>
              @if($row['trend'] >= 0)
                <span class="badge bg-success"><i class="fas fa-arrow-up me-1"></i> +{{ number_format($row['trend'], 1) }}%</span>
              @else
                <span class="badge bg-danger"><i class="fas fa-arrow-down me-1"></i> {{ number_format($row['trend'], 1) }}%</span>
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
