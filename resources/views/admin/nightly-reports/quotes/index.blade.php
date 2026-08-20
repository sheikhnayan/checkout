@extends('admin.nightly-reports.layout')

@section('content')
<div class="container-fluid p-0">
  <div class="card mb-4">
    <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-3">
      <div>
        <h4 class="text-white mb-1 fw-bold"><i class="fas fa-quote-left text-warning me-2"></i> Daily Leadership Quotes Manager</h4>
        <p class="text-muted small mb-0">Manage inspirational and operational leadership quotes displayed on executive dashboards.</p>
      </div>
      <button class="btn btn-sm btn-gold" data-bs-toggle="modal" data-bs-target="#addQuoteModal">
        <i class="fas fa-plus me-1"></i> Add Quote
      </button>
    </div>
  </div>

  <div class="card">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>Quote Text</th>
            <th>Author</th>
            <th>Category</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($quotes as $q)
          <tr>
            <td class="text-white fw-medium">"{{ $q->quote_text }}"</td>
            <td><span class="text-warning small">{{ $q->author }}</span></td>
            <td><span class="badge bg-secondary">{{ $q->category ?? 'General' }}</span></td>
            <td class="text-end">
              <form method="POST" action="{{ route('admin.nightly-reports.quotes.destroy', $q->id) }}" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Remove quote?')">
                  <i class="fas fa-trash"></i>
                </button>
              </form>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  <!-- Add Quote Modal -->
  <div class="modal fade" id="addQuoteModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content" style="background: var(--nr-surface-2); border-color: var(--nr-border);">
        <form method="POST" action="{{ route('admin.nightly-reports.quotes.store') }}">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title text-white">Add New Leadership Quote</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label text-muted small fw-bold">Quote Text</label>
              <textarea name="quote_text" class="form-control" rows="3" required placeholder="e.g. Excellence is not an act but a habit..."></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label text-muted small fw-bold">Author Name</label>
              <input type="text" name="author" class="form-control" required placeholder="e.g. Operations / Reports" />
            </div>
            <div class="mb-3">
              <label class="form-label text-muted small fw-bold">Category</label>
              <input type="text" name="category" class="form-control" placeholder="e.g. Leadership, Hospitality" />
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-gold">Add Quote</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
