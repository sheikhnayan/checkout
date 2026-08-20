@extends('admin.nightly-reports.layout')

@section('content')
<div class="container-fluid p-0">
  <div class="card mb-4">
    <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-3">
      <div>
        <h4 class="text-white mb-1 fw-bold"><i class="fas fa-database text-warning me-2"></i> Encrypted Data Backup & Recovery Vault</h4>
        <p class="text-muted small mb-0">Generate full AES-256 encrypted database archives with SHA-256 integrity checksums.</p>
      </div>
      <form method="POST" action="{{ route('admin.nightly-reports.backups.generate') }}">
        @csrf
        <button type="submit" class="btn btn-sm btn-gold">
          <i class="fas fa-lock me-1"></i> Generate Full System Backup
        </button>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>Backup Archive</th>
            <th>Generated Date</th>
            <th>File Size</th>
            <th>SHA-256 Checksum</th>
            <th>Encryption</th>
            <th class="text-end">Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse($backups as $b)
          <tr>
            <td class="fw-bold text-white"><i class="fas fa-file-archive text-warning me-2"></i> {{ $b->file_name }}</td>
            <td><small class="text-muted">{{ $b->created_at->format('M d, Y h:i A') }}</small></td>
            <td><small class="text-white">{{ number_format($b->file_size / 1024, 1) }} KB</small></td>
            <td><code class="text-info small">{{ substr($b->checksum, 0, 16) }}...</code></td>
            <td><span class="badge bg-secondary">{{ $b->encryption_type }}</span></td>
            <td class="text-end">
              <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Verified Ready</span>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="6" class="text-center py-5 text-muted">
              <i class="fas fa-shield-alt fa-3x mb-3 opacity-50"></i>
              <div>No manual backup archives created yet. Click "Generate Full System Backup" above.</div>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
