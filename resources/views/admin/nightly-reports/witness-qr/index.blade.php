@extends('admin.nightly-reports.layout')

@section('content')
<div class="container-fluid p-0">
  <div class="card mb-4">
    <div class="card-body">
      <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
          <h4 class="text-white mb-1 fw-bold"><i class="fas fa-qrcode text-warning me-2"></i> Door & Venue Witness QR Code Hub</h4>
          <p class="text-muted small mb-0">Generate, download, and email printable door standee QR flyers for instant mobile witness intake.</p>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4">
    @foreach($locations as $loc)
    @php
      $witnessUrl = route('nightly.submit.witness', ['location' => $loc->id]);
      $qrApiUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=' . urlencode($witnessUrl);
    @endphp
    <div class="col-md-6 col-lg-4 col-xl-3">
      <div class="card h-100 text-center">
        <div class="card-header">
          <h6 class="card-title text-white mb-0" style="font-size: 0.92rem;">{{ $loc->name }}</h6>
          <small class="text-muted">{{ $loc->city }}, {{ $loc->state }}</small>
        </div>
        <div class="card-body p-4 d-flex flex-column align-items-center">
          <div class="p-2 bg-white rounded shadow-sm mb-3">
            <img src="{{ $qrApiUrl }}" alt="QR for {{ $loc->name }}" style="width: 160px; height: 160px;" />
          </div>
          <div class="small text-muted mb-3" style="font-size: 0.72rem; word-break: break-all;">
            {{ $witnessUrl }}
          </div>
          <div class="mt-auto d-flex gap-2 w-100">
            <a href="{{ $qrApiUrl }}" download="Witness_QR_{{ $loc->id }}.png" target="_blank" class="btn btn-sm btn-outline-light flex-grow-1">
              <i class="fas fa-download me-1"></i> QR PNG
            </a>
            <form method="POST" action="{{ route('admin.nightly-reports.witness-qr.send') }}" class="flex-grow-1">
              @csrf
              <input type="hidden" name="location_id" value="{{ $loc->id }}" />
              <button type="submit" class="btn btn-sm btn-gold w-100" title="Send to GM Email">
                <i class="fas fa-paper-plane me-1"></i> Email GM
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
    @endforeach
  </div>
</div>
@endsection
