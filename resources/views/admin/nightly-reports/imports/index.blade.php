@extends('admin.nightly-reports.layout')

@section('content')
<div class="container-fluid p-0">
  <div class="card mb-4">
    <div class="card-body">
      <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
          <h4 class="text-white mb-1 fw-bold"><i class="fas fa-file-import text-warning me-2"></i> Bulk Import History & OCR Reconciliation</h4>
          <p class="text-muted small mb-0">Upload bulk spreadsheets, review AI/OCR draft extractions, patch draft line-items, and confirm uploads.</p>
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

  <div class="row g-4">
    <div class="col-lg-5">
      <div class="card">
        <div class="card-header"><h5 class="card-title mb-0">Upload Spreadsheets or Scans</h5></div>
        <div class="card-body">
          <form method="POST" action="{{ route('admin.nightly-reports.imports.upload') }}" enctype="multipart/form-data">
            @csrf
            <div class="p-4 rounded text-center mb-3" style="background: var(--nr-surface-2); border: 2px dashed var(--nr-border-gold);">
              <i class="fas fa-cloud-upload-alt fa-3x text-warning mb-3"></i>
              <div class="text-white fw-bold mb-1">Select Excel (.xlsx) or PDF Files</div>
              <small class="text-muted d-block mb-3">Max file size 20MB</small>
              <input type="file" name="file" class="form-control form-control-sm mb-3" required />
              <button type="submit" class="btn btn-gold w-100"><i class="fas fa-upload me-1"></i> Upload & Process Drafts</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="col-lg-7">
      <div class="card">
        <div class="card-header"><h5 class="card-title mb-0">Recent Import Draft Batches</h5></div>
        <div class="card-body">
          <div class="text-center py-5 text-muted">
            <i class="fas fa-check-double fa-3x mb-3 opacity-50"></i>
            <div>All previous import batches have been reviewed and confirmed.</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
