@extends('admin.main')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
            <div>
                <h4 class="mb-1 text-white"><i class="bx bx-form-builder me-2"></i>Custom Drag & Drop Form Builder</h4>
                <p class="text-muted mb-0 small">Create, edit, and manage drag-and-drop web forms with public URLs and audit logs.</p>
            </div>
            <a href="{{ route('admin.forms.create') }}" class="btn btn-primary">
                <i class="bx bx-plus me-1"></i> Create New Form
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Filter Card -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.forms.index') }}" class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label text-muted small">Search Form Title or Slug</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-search"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Search forms..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label text-muted small">Filter by Club / Website</label>
                        <select name="website" class="form-select">
                            <option value="">All Clubs / Websites</option>
                            @foreach($websites as $web)
                                <option value="{{ $web->id }}" {{ (string)request('website') === (string)$web->id ? 'selected' : '' }}>{{ $web->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-secondary w-100"><i class="bx bx-filter-alt me-1"></i> Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Forms Table Card -->
        <div class="card">
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr class="table-dark">
                            <th>Form Title & Public Link</th>
                            <th>Target Clubs</th>
                            <th>Submissions</th>
                            <th>Created / Updated By</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($forms as $form)
                            <tr>
                                <td>
                                    <div class="fw-bold text-white fs-6">{{ $form->title }}</div>
                                    <div class="small text-muted mb-1">{{ $form->description ?: 'No description' }}</div>
                                    <div class="d-flex align-items-center gap-1 mt-1">
                                        <span class="badge bg-label-info font-monospace small"><i class="bx bx-link-external me-1"></i>{{ route('forms.public.show', $form->slug) }}</span>
                                        <button class="btn btn-xs btn-outline-secondary copy-btn" data-clipboard-text="{{ route('forms.public.show', $form->slug) }}" title="Copy Public URL">
                                            <i class="bx bx-copy"></i>
                                        </button>
                                        <a href="{{ route('forms.public.show', $form->slug) }}" target="_blank" class="btn btn-xs btn-outline-primary" title="Open Public Link">
                                            <i class="bx bx-show"></i>
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    @if(empty($form->website_ids))
                                        <span class="badge bg-label-secondary">All Clubs</span>
                                    @else
                                        @foreach($websites->whereIn('id', $form->website_ids) as $w)
                                            <span class="badge bg-label-primary mb-1">{{ $w->name }}</span>
                                        @endforeach
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.forms.submissions', $form->id) }}" class="badge bg-primary fs-6 text-decoration-none">
                                        <i class="bx bx-receipt me-1"></i>{{ $form->submissions_count }} Submissions
                                    </a>
                                </td>
                                <td>
                                    <div class="small">
                                        <i class="bx bx-user me-1 text-primary"></i><strong>Created:</strong> {{ optional($form->creator)->name ?: 'System' }}<br>
                                        <span class="text-muted"><i class="bx bx-time me-1"></i>{{ $form->created_at ? $form->created_at->format('M d, Y h:i A') : '-' }}</span>
                                    </div>
                                    @if($form->updater && $form->updated_by_user_id != $form->created_by_user_id)
                                        <div class="small mt-1 text-info">
                                            <i class="bx bx-edit me-1"></i><strong>Edited by:</strong> {{ $form->updater->name }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('admin.forms.toggle', $form->id) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $form->is_active ? 'btn-success' : 'btn-danger' }}">
                                            <i class="bx {{ $form->is_active ? 'bx-check-circle' : 'bx-x-circle' }} me-1"></i>
                                            {{ $form->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <a href="{{ route('admin.forms.submissions', $form->id) }}" class="btn btn-sm btn-outline-info" title="View Submissions">
                                            <i class="bx bx-table me-1"></i> Data
                                        </a>
                                        <a href="{{ route('admin.forms.edit', $form->id) }}" class="btn btn-sm btn-outline-warning" title="Edit Builder">
                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                        </a>
                                        <form method="POST" action="{{ route('admin.forms.destroy', $form->id) }}" onsubmit="return confirm('Are you sure you want to delete this form? All submission data will be lost.');" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Form">
                                                <i class="bx bx-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bx bx-form-builder fs-1 d-block mb-2"></i>
                                    No custom forms found. Click "Create New Form" above to launch the visual builder.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($forms->hasPages())
                <div class="card-footer">
                    {{ $forms->withQueryString()->links() }}
                </div>
            @endif
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.copy-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const text = this.getAttribute('data-clipboard-text');
            navigator.clipboard.writeText(text).then(function() {
                alert('Public URL copied to clipboard!');
            });
        });
    });
});
</script>
@endsection
