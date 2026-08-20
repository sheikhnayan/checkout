@extends('admin.nightly-reports.layout')

@section('content')
<div class="container-fluid p-0">
  <div class="card mb-4">
    <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-3">
      <div>
        <h4 class="text-white mb-1 fw-bold"><i class="fas fa-balance-scale text-warning me-2"></i> External Legal Access Tokens</h4>
        <p class="text-muted small mb-0">Generate secure, time-limited external access portals for attorneys, insurance adjusters, and police investigators.</p>
      </div>
      <button class="btn btn-sm btn-gold" data-bs-toggle="modal" data-bs-target="#createTokenModal">
        <i class="fas fa-plus me-1"></i> Generate Legal Token
      </button>
    </div>
  </div>

  <div class="card">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>Token ID</th>
            <th>Attorney / Firm Name</th>
            <th>Case Reference #</th>
            <th>Expires At</th>
            <th>Status</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($tokens as $tok)
          <tr>
            <td>
              <code class="text-warning small">{{ substr($tok->token, 0, 16) }}...</code>
            </td>
            <td>
              <div class="fw-bold text-white">{{ $tok->attorney_name }}</div>
              <small class="text-muted">{{ $tok->firm_name ?? 'Independent Counsel' }}</small>
            </td>
            <td><small class="text-white">{{ $tok->case_reference ?? '—' }}</small></td>
            <td><small class="text-muted">{{ $tok->expires_at ? $tok->expires_at->format('M d, Y') : 'Never' }}</small></td>
            <td>
              @if($tok->isValid())
                <span class="badge bg-success">Active Valid</span>
              @else
                <span class="badge bg-danger">Revoked / Expired</span>
              @endif
            </td>
            <td class="text-end">
              @if($tok->isValid())
                <form method="POST" action="{{ route('admin.nightly-reports.legal.revoke-token', $tok->id) }}" class="d-inline">
                  @csrf
                  <button type="submit" class="btn btn-sm btn-outline-danger" title="Revoke Access Now">
                    <i class="fas fa-ban me-1"></i> Revoke
                  </button>
                </form>
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="6" class="text-center py-5 text-muted">No external legal tokens generated yet.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <!-- Generate Token Modal -->
  <div class="modal fade" id="createTokenModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content" style="background: var(--nr-surface-2); border-color: var(--nr-border);">
        <form method="POST" action="{{ route('admin.nightly-reports.legal.create-token') }}">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title text-white">Generate Secure Legal Access Token</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label text-muted small fw-bold">Attorney / Investigator Name</label>
              <input type="text" name="attorney_name" class="form-control" required placeholder="e.g. Robert Shapiro, Esq." />
            </div>
            <div class="mb-3">
              <label class="form-label text-muted small fw-bold">Law Firm / Agency</label>
              <input type="text" name="firm_name" class="form-control" placeholder="e.g. Shafer & Associates" />
            </div>
            <div class="mb-3">
              <label class="form-label text-muted small fw-bold">Case Reference #</label>
              <input type="text" name="case_reference" class="form-control" placeholder="e.g. CL-2026-8812" />
            </div>
            <div class="mb-3">
              <label class="form-label text-muted small fw-bold">Days Valid</label>
              <select name="days_valid" class="form-select">
                <option value="7">7 Days</option>
                <option value="14" selected>14 Days</option>
                <option value="30">30 Days</option>
                <option value="60">60 Days</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-gold">Generate Token</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
