@extends('admin.main')

@section('title', 'Help Center Builder - ' . $page->title)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header Bar -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('admin.help-center.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Back to Dashboard
                </a>
                <span class="badge bg-label-primary">Page Builder</span>
            </div>
            <h4 class="fw-bold mb-1">{{ $page->title }}</h4>
            <p class="text-muted mb-0">Organize sections, forms, and external links for this Help Center page.</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSectionModal">
                <i class="bx bx-plus me-1"></i> Add New Section
            </button>
            <a href="{{ route('help-center.public', $page->slug) }}" target="_blank" class="btn btn-outline-primary">
                <i class="bx bx-show me-1"></i> Public Preview
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bx bx-error me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- SECTIONS & ITEMS LIST -->
    @if($page->sections->count() === 0)
        <div class="card shadow-sm border-0 py-5 text-center">
            <div class="card-body">
                <div class="avatar avatar-xl bg-label-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                    <i class="bx bx-layer fs-1"></i>
                </div>
                <h5 class="fw-bold mb-2">No Sections Added Yet</h5>
                <p class="text-muted max-w-md mx-auto mb-4">Start structuring your Help Center page by creating your first section (e.g. "Customer Registration", "Policy Documents", "Employee Forms").</p>
                <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#addSectionModal">
                    <i class="bx bx-plus me-1"></i> Create First Section
                </button>
            </div>
        </div>
    @else
        <div class="d-flex flex-column gap-4">
            @foreach($page->sections as $section)
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light d-flex align-items-center justify-content-between py-3 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bx bx-dots-vertical-rounded text-muted"></i>
                            <div>
                                <h5 class="fw-bold mb-0 text-dark">{{ $section->title }}</h5>
                                @if($section->description)
                                    <small class="text-muted">{{ $section->description }}</small>
                                @endif
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addItemModal-{{ $section->id }}">
                                <i class="bx bx-plus me-1"></i> Add Link Item
                            </button>
                            <button type="button" class="btn btn-sm btn-icon btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editSectionModal-{{ $section->id }}">
                                <i class="bx bx-pencil"></i>
                            </button>
                            <form action="{{ route('admin.help-center.sections.destroy', $section->id) }}" method="POST" onsubmit="return confirm('Delete this section and all its links?');" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-icon btn-outline-danger">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="card-body py-3">
                        @if($section->items->count() === 0)
                            <div class="p-4 text-center border rounded bg-label-secondary my-2">
                                <p class="text-muted fs-7 mb-2">No links in this section yet.</p>
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addItemModal-{{ $section->id }}">
                                    <i class="bx bx-plus me-1"></i> Add Custom Form or External Link
                                </button>
                            </div>
                        @else
                            <div class="list-group list-group-flush border rounded">
                                @foreach($section->items as $item)
                                    <div class="list-group-item d-flex align-items-center justify-content-between py-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar avatar-sm bg-label-primary rounded-circle d-flex align-items-center justify-content-center">
                                                <i class="bx {{ $item->icon ?: ($item->type === 'form' ? 'bx-file' : 'bx-link-external') }} fs-5"></i>
                                            </div>
                                            <div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <h6 class="mb-0 fw-semibold text-dark">{{ $item->resolved_title }}</h6>
                                                    @if($item->type === 'form')
                                                        <span class="badge bg-label-info fs-8">Form Link</span>
                                                    @else
                                                        <span class="badge bg-label-secondary fs-8">External Link</span>
                                                    @endif
                                                </div>
                                                @if($item->description)
                                                    <small class="text-muted d-block mt-1">{{ $item->description }}</small>
                                                @endif
                                                <a href="{{ $item->resolved_url }}" target="_blank" class="text-primary fs-8 d-inline-block mt-1 text-decoration-none">
                                                    <i class="bx bx-link me-1"></i> {{ Str::limit($item->resolved_url, 70) }}
                                                </a>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <button type="button" class="btn btn-icon btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editItemModal-{{ $item->id }}">
                                                <i class="bx bx-pencil"></i>
                                            </button>
                                            <form action="{{ route('admin.help-center.items.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Delete this link item?');" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-icon btn-sm btn-outline-danger">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- EDIT ITEM MODAL -->
                                    <div class="modal fade" id="editItemModal-{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <form action="{{ route('admin.help-center.items.update', $item->id) }}" method="POST" class="modal-content">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header border-bottom">
                                                    <h5 class="modal-title fw-bold">Edit Link Item</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body py-3">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Item Type</label>
                                                        <select name="type" class="form-select item-type-select" onchange="toggleItemEditFields(this, {{ $item->id }})" required>
                                                            <option value="form" {{ $item->type === 'form' ? 'selected' : '' }}>Custom Form Link</option>
                                                            <option value="external" {{ $item->type === 'external' ? 'selected' : '' }}>External URL Link</option>
                                                        </select>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Link Title <span class="text-danger">*</span></label>
                                                        <input type="text" name="title" class="form-control" value="{{ $item->title }}" required>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Description</label>
                                                        <textarea name="description" class="form-control" rows="2">{{ $item->description }}</textarea>
                                                    </div>

                                                    <div class="mb-3 form-group-form-edit-{{ $item->id }}" style="{{ $item->type === 'form' ? '' : 'display:none;' }}">
                                                        <label class="form-label fw-semibold">Select Form <span class="text-danger">*</span></label>
                                                        <select name="custom_form_id" class="form-select">
                                                            <option value="">-- Choose a Custom Form --</option>
                                                            @foreach($customForms as $cf)
                                                                <option value="{{ $cf->id }}" {{ $item->custom_form_id == $cf->id ? 'selected' : '' }}>{{ $cf->title }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="mb-3 form-group-url-edit-{{ $item->id }}" style="{{ $item->type === 'external' ? '' : 'display:none;' }}">
                                                        <label class="form-label fw-semibold">External URL <span class="text-danger">*</span></label>
                                                        <input type="url" name="url" class="form-control" value="{{ $item->url }}" placeholder="https://example.com/document">
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Icon Class</label>
                                                        <input type="text" name="icon" class="form-control" value="{{ $item->icon ?: 'bx-link' }}" placeholder="bx-link">
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-top">
                                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- EDIT SECTION MODAL -->
                <div class="modal fade" id="editSectionModal-{{ $section->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <form action="{{ route('admin.help-center.sections.update', $section->id) }}" method="POST" class="modal-content">
                            @csrf
                            @method('PUT')
                            <div class="modal-header border-bottom">
                                <h5 class="modal-title fw-bold">Edit Section</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body py-3">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Section Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control" value="{{ $section->title }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Description</label>
                                    <textarea name="description" class="form-control" rows="2">{{ $section->description }}</textarea>
                                </div>
                            </div>
                            <div class="modal-footer border-top">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Update Section</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- ADD ITEM MODAL FOR SECTION -->
                <div class="modal fade" id="addItemModal-{{ $section->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <form action="{{ route('admin.help-center.items.store', $section->id) }}" method="POST" class="modal-content">
                            @csrf
                            <div class="modal-header border-bottom">
                                <h5 class="modal-title fw-bold">Add Link Item to "{{ $section->title }}"</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body py-3">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Link Type <span class="text-danger">*</span></label>
                                    <select name="type" class="form-select" onchange="toggleItemCreateFields(this, {{ $section->id }})" required>
                                        <option value="form" selected>Custom Form Link</option>
                                        <option value="external">External URL Link</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Link Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control" placeholder="e.g., VIP Event Booking Form" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Description</label>
                                    <textarea name="description" class="form-control" rows="2" placeholder="Brief details about what this form or document is for..."></textarea>
                                </div>

                                <div class="mb-3 form-group-form-create-{{ $section->id }}">
                                    <label class="form-label fw-semibold">Select Custom Form <span class="text-danger">*</span></label>
                                    <select name="custom_form_id" class="form-select">
                                        <option value="">-- Select a Form --</option>
                                        @foreach($customForms as $cf)
                                            <option value="{{ $cf->id }}">{{ $cf->title }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3 form-group-url-create-{{ $section->id }}" style="display:none;">
                                    <label class="form-label fw-semibold">External URL <span class="text-danger">*</span></label>
                                    <input type="url" name="url" class="form-control" placeholder="https://example.com/resource">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Icon</label>
                                    <select name="icon" class="form-select">
                                        <option value="bx-file">📄 File / Document (bx-file)</option>
                                        <option value="bx-list-check">📋 Form (bx-list-check)</option>
                                        <option value="bx-link-external">🔗 External Link (bx-link-external)</option>
                                        <option value="bx-book-open">📖 Manual / Guide (bx-book-open)</option>
                                        <option value="bx-download">📥 Download (bx-download)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer border-top">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Add Link Item</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<!-- ADD SECTION MODAL -->
<div class="modal fade" id="addSectionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.help-center.sections.store', $page->id) }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold">Add New Section</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-3">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Section Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" placeholder="e.g., Daily Operations Forms" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Description</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="Optional summary for this section..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Section</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleItemCreateFields(selectEl, sectionId) {
    var val = selectEl.value;
    var formGrp = document.querySelector('.form-group-form-create-' + sectionId);
    var urlGrp = document.querySelector('.form-group-url-create-' + sectionId);
    if (val === 'form') {
        if (formGrp) formGrp.style.display = 'block';
        if (urlGrp) urlGrp.style.display = 'none';
    } else {
        if (formGrp) formGrp.style.display = 'none';
        if (urlGrp) urlGrp.style.display = 'block';
    }
}

function toggleItemEditFields(selectEl, itemId) {
    var val = selectEl.value;
    var formGrp = document.querySelector('.form-group-form-edit-' + itemId);
    var urlGrp = document.querySelector('.form-group-url-edit-' + itemId);
    if (val === 'form') {
        if (formGrp) formGrp.style.display = 'block';
        if (urlGrp) urlGrp.style.display = 'none';
    } else {
        if (formGrp) formGrp.style.display = 'none';
        if (urlGrp) urlGrp.style.display = 'block';
    }
}
</script>
@endsection
