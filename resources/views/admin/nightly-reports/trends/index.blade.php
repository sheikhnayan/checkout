@extends('admin.nightly-reports.layout')

@section('content')
<div class="container-fluid p-0">
  <div class="card mb-4">
    <div class="card-body">
      <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <div>
          <h4 class="text-white mb-1 fw-bold"><i class="fas fa-chart-line text-warning me-2"></i> Performance Trends & YoY Analytics</h4>
          <p class="text-muted small mb-0">Multi-period revenue velocity and year-over-year pacing comparisons.</p>
        </div>
      </div>

      <form method="GET" action="{{ route('admin.nightly-reports.trends.index') }}" class="row g-2">
        <div class="col-md-4">
          <select name="location_id" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="">All Accessible Locations (Aggregated)</option>
            @foreach($locations as $loc)
              <option value="{{ $loc->id }}" {{ (string)$selectedLocationId === (string)$loc->id ? 'selected' : '' }}>
                {{ $loc->name }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
            @for($y = \Carbon\Carbon::now()->year; $y >= \Carbon\Carbon::now()->year - 4; $y--)
              <option value="{{ $y }}" {{ $year === $y ? 'selected' : '' }}>{{ $y }} Calendar Year</option>
            @endfor
          </select>
        </div>
      </form>
    </div>
  </div>

  <div class="row g-4">
    <!-- 30-Day Daily Velocity Chart -->
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
          <h5 class="card-title mb-0">30-Day Daily Net Sales vs Prior Year</h5>
          <span class="badge badge-gold">Daily Pacing</span>
        </div>
        <div class="card-body">
          <div id="dailyPacingChart" style="height: 320px;"></div>
        </div>
      </div>
    </div>

    <!-- Monthly YoY Chart -->
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
          <h5 class="card-title mb-0">Monthly Revenue Comparison ({{ $year }} vs {{ $year - 1 }})</h5>
          <span class="badge bg-primary">Monthly Total</span>
        </div>
        <div class="card-body">
          <div id="monthlyYoYChart" style="height: 340px;"></div>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
  // Monthly Chart
  var monthlyOptions = {
    series: [
      { name: '{{ $year }} Net Sales ($)', data: @json($monthlyCurrent) },
      { name: '{{ $year - 1 }} Net Sales ($)', data: @json($monthlyPrior) }
    ],
    chart: { type: 'bar', height: 320, background: 'transparent', toolbar: { show: false } },
    colors: ['#c9a84c', '#64748b'],
    plotOptions: { bar: { horizontal: false, columnWidth: '55%', borderRadius: 4 } },
    dataLabels: { enabled: false },
    stroke: { show: true, width: 2, colors: ['transparent'] },
    xaxis: {
      categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
      labels: { style: { colors: '#94a3b8' } }
    },
    yaxis: {
      labels: {
        style: { colors: '#94a3b8' },
        formatter: function(val) { return '$' + (val / 1000).toFixed(0) + 'k'; }
      }
    },
    theme: { mode: 'dark' },
    grid: { borderColor: 'rgba(255,255,255,0.06)' },
    tooltip: { y: { formatter: function (val) { return "$" + Number(val).toLocaleString(); } } }
  };
  var monthlyChart = new ApexCharts(document.querySelector("#monthlyYoYChart"), monthlyOptions);
  monthlyChart.render();

  // Daily Chart
  var dailyOptions = {
    series: [
      { name: 'Current Period ($)', data: @json($dailyCurrent) },
      { name: 'Prior Year ($)', data: @json($dailyLastYear) }
    ],
    chart: { type: 'area', height: 300, background: 'transparent', toolbar: { show: false } },
    colors: ['#34d399', '#64748b'],
    dataLabels: { enabled: false },
    stroke: { curve: 'smooth', width: 2 },
    xaxis: {
      categories: @json($dailyLabels),
      labels: { style: { colors: '#94a3b8' } }
    },
    yaxis: {
      labels: {
        style: { colors: '#94a3b8' },
        formatter: function(val) { return '$' + (val / 1000).toFixed(0) + 'k'; }
      }
    },
    theme: { mode: 'dark' },
    grid: { borderColor: 'rgba(255,255,255,0.06)' },
    tooltip: { y: { formatter: function (val) { return "$" + Number(val).toLocaleString(); } } }
  };
  var dailyChart = new ApexCharts(document.querySelector("#dailyPacingChart"), dailyOptions);
  dailyChart.render();
</script>
@endpush
@endsection
