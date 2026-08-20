@extends('admin.nightly-reports.layout')

@section('content')
<div class="container-fluid p-0">
  <div class="d-flex align-items-center justify-content-between mb-4">
    <div>
      <a href="{{ route('admin.nightly-reports.boutique.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
        <i class="fas fa-arrow-left me-1"></i> Back to Boutique Summary
      </a>
      <h4 class="text-white fw-bold mb-0">Boutique Retail Batch Import</h4>
      <p class="text-muted small mb-0">Upload daily Excel or POS settlement spreadsheets for retail store locations.</p>
    </div>
  </div>

  <div class="card mx-auto" style="max-width: 650px;">
    <div class="card-body p-4 text-center">
      <div class="p-4 rounded mb-4" style="background: var(--nr-surface-2); border: 2px dashed var(--nr-border-gold);">
        <i class="fas fa-file-excel fa-3x text-success mb-3"></i>
        <h5 class="text-white fw-bold">Drag & Drop Boutique POS Files Here</h5>
        <p class="text-muted small">Supports .xlsx, .xls, .csv end-of-day register exports.</p>
        <form method="POST" action="{{ route('admin.nightly-reports.imports.upload') }}" enctype="multipart/form-data">
          @csrf
          <input type="file" name="file" class="form-control mb-3" required />
          <button type="submit" class="btn btn-gold px-4"><i class="fas fa-upload me-1"></i> Parse & Process Spreadsheet</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
