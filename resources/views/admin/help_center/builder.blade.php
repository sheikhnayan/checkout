@extends('admin.main')

@section('title', 'Form Portal Builder - ' . $page->title)

@section('content')
<!-- SortableJS for Drag & Drop Re-ordering -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<style>
    /* High contrast text overrides */
    .hc-white-title,
    .card-title,
    .modal-title,
    .form-label,
    .hc-page-heading {
        color: #ffffff !important;
    }
    .text-dark {
        color: #ffffff !important;
    }
    .text-muted {
        color: #a1a5b7 !important;
    }
    .hc-subtitle {
        color: #cbd5e1 !important;
    }

    /* Modal styling & padding fixes */
    .modal-content {
        background-color: #1b1b29 !important;
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
        border-radius: 14px !important;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6) !important;
    }
    .modal-header {
        border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
        padding-bottom: 1.25rem !important;
    }
    .modal-title {
        color: #ffffff !important;
        font-weight: 700 !important;
        font-size: 1.15rem !important;
    }
    .modal-body {
        padding-top: 1.25rem !important;
        padding-bottom: 1.25rem !important;
    }
    .modal-body .form-label {
        color: #ffffff !important;
        font-weight: 600 !important;
        margin-bottom: 0.5rem !important;
    }
    .modal-body .form-control,
    .modal-body .form-select {
        background-color: rgba(255, 255, 255, 0.07) !important;
        border: 1px solid rgba(255, 255, 255, 0.18) !important;
        color: #ffffff !important;
        border-radius: 8px !important;
    }
    .modal-body .form-control:focus,
    .modal-body .form-select:focus {
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.25) !important;
        color: #ffffff !important;
    }
    .modal-footer {
        border-top: 1px solid rgba(255, 255, 255, 0.1) !important;
        padding-top: 1.25rem !important;
        padding-bottom: 1rem !important;
        margin-top: 0.5rem !important;
        display: flex !important;
        gap: 0.75rem !important;
        align-items: center !important;
        justify-content: flex-end !important;
    }

    /* Standardized action buttons */
    .btn-icon-custom {
        width: 34px !important;
        height: 34px !important;
        padding: 0 !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 8px !important;
    }
    .btn-icon-custom i {
        font-size: 17px !important;
        line-height: 1 !important;
    }

    /* Drag & Drop Visual Handles */
    .drag-section-handle,
    .drag-item-handle {
        cursor: grab !important;
        user-select: none;
        transition: color 0.2s ease;
    }
    .drag-section-handle:hover,
    .drag-item-handle:hover {
        color: #6366f1 !important;
    }
    .drag-section-handle:active,
    .drag-item-handle:active {
        cursor: grabbing !important;
    }
    .sortable-ghost {
        opacity: 0.4 !important;
        background: rgba(99, 102, 241, 0.15) !important;
        border: 2px dashed #6366f1 !important;
    }
</style>

<div class="container-fluid flex-grow-1 container-p-y">
    <!-- Header Bar -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('admin.help-center.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Back to Dashboard
                </a>
                <span class="badge bg-label-primary">Page Builder</span>
            </div>
            <h4 class="fw-bold mb-1 hc-white-title">{{ $page->title }}</h4>
            <p class="hc-subtitle mb-0">Organize sections, forms, uploaded documents, and external links. Drag handles to re-order items or sections.</p>
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

    <!-- SECTIONS & ITEMS LIST CONTAINER -->
    @if($page->sections->count() === 0)
        <div class="card shadow-sm border-0 py-5 text-center">
            <div class="card-body">
                <div class="avatar avatar-xl bg-label-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                    <i class="bx bx-layer fs-1"></i>
                </div>
                <h5 class="fw-bold mb-2 hc-white-title">No Sections Added Yet</h5>
                <p class="hc-subtitle max-w-md mx-auto mb-4">Start structuring your page by creating your first section (e.g. "Customer Registration", "Policy Documents", "Employee Forms").</p>
                <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#addSectionModal">
                    <i class="bx bx-plus me-1"></i> Create First Section
                </button>
            </div>
        </div>
    @else
        <div class="d-flex flex-column gap-4" id="sectionsContainer">
            @foreach($page->sections as $section)
                <div class="card shadow-sm border-0 section-card" data-section-id="{{ $section->id }}">
                    <div class="card-header bg-light d-flex align-items-center justify-content-between py-3 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bx bx-menu drag-section-handle text-muted fs-4" title="Drag to re-order section"></i>
                            <div>
                                <h5 class="fw-bold mb-0 hc-white-title">{{ $section->title }}</h5>
                                @if($section->description)
                                    <small class="hc-subtitle">{{ $section->description }}</small>
                                @endif
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addItemModal-{{ $section->id }}">
                                <i class="bx bx-plus me-1"></i> Add Link Item
                            </button>
                            <button type="button" class="btn btn-icon-custom btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editSectionModal-{{ $section->id }}">
                                <i class="bx bx-pencil"></i>
                            </button>
                            <form action="{{ route('admin.help-center.sections.destroy', $section->id) }}" method="POST" onsubmit="return confirm('Delete this section and all its links?');" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-icon-custom btn-outline-danger">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="card-body py-3">
                        @if($section->items->count() === 0)
                            <div class="p-4 text-center border rounded my-2" style="background: rgba(255,255,255,0.03); border-color: rgba(255,255,255,0.1) !important;">
                                <p class="hc-subtitle fs-7 mb-2">No links or files in this section yet.</p>
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addItemModal-{{ $section->id }}">
                                    <i class="bx bx-plus me-1"></i> Add Form, Document File, or External Link
                                </button>
                            </div>
                        @else
                            <div class="list-group list-group-flush border rounded items-container" data-section-id="{{ $section->id }}">
                                @foreach($section->items as $item)
                                    <div class="list-group-item d-flex align-items-center justify-content-between py-3 item-row" data-item-id="{{ $item->id }}">
                                        <div class="d-flex align-items-center gap-3">
                                            <i class="bx bx-grid-vertical drag-item-handle text-muted fs-4" title="Drag to re-order item"></i>
                                            <div class="avatar avatar-sm bg-label-primary rounded-circle d-flex align-items-center justify-content-center">
                                                <i class="bx {{ $item->icon ?: ($item->type === 'form' ? 'bx-file' : ($item->type === 'file' ? 'bx-cloud-download' : 'bx-link-external')) }} fs-5"></i>
                                            </div>
                                            <div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <h6 class="mb-0 fw-semibold hc-white-title">{{ $item->resolved_title }}</h6>
                                                    @if($item->type === 'form')
                                                        <span class="badge bg-label-info fs-8">Form Link</span>
                                                    @elseif($item->type === 'file')
                                                        <span class="badge bg-label-success fs-8">Uploaded Document</span>
                                                    @else
                                                        <span class="badge bg-label-secondary fs-8">External Link / Email</span>
                                                    @endif
                                                </div>
                                                @if($item->description)
                                                    <small class="hc-subtitle d-block mt-1">{{ $item->description }}</small>
                                                @endif

                                                @if($item->type === 'file' && $item->file_path)
                                                    <a href="{{ $item->resolved_url }}" target="_blank" class="text-success fs-8 d-inline-block mt-1 text-decoration-none">
                                                        <i class="bx bx-download me-1"></i> Download File ({{ basename($item->file_path) }})
                                                    </a>
                                                @else
                                                    <a href="{{ $item->resolved_url }}" target="_blank" class="text-primary fs-8 d-inline-block mt-1 text-decoration-none">
                                                        <i class="bx bx-link me-1"></i> {{ Str::limit($item->resolved_url, 70) }}
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <button type="button" class="btn btn-icon-custom btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editItemModal-{{ $item->id }}">
                                                <i class="bx bx-pencil"></i>
                                            </button>
                                            <form action="{{ route('admin.help-center.items.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Delete this link item?');" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-icon-custom btn-outline-danger">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- EDIT ITEM MODAL -->
                                    <div class="modal fade" id="editItemModal-{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <form action="{{ route('admin.help-center.items.update', $item->id) }}" method="POST" enctype="multipart/form-data" class="modal-content" novalidate>
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Link / Document Item</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Item Type <span class="text-danger">*</span></label>
                                                        <select name="type" class="form-select item-type-select" onchange="toggleItemEditFields(this, {{ $item->id }})" required>
                                                            <option value="form" {{ $item->type === 'form' ? 'selected' : '' }}>Custom Form Link</option>
                                                            <option value="external" {{ $item->type === 'external' ? 'selected' : '' }}>External Link (URL / mailto / #)</option>
                                                            <option value="file" {{ $item->type === 'file' ? 'selected' : '' }}>Upload Document File (PDF, DOCX, XLSX)</option>
                                                        </select>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Link Title <span class="text-danger">*</span></label>
                                                        <input type="text" name="title" class="form-control" value="{{ $item->title }}" required>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Description</label>
                                                        <textarea name="description" class="form-control" rows="2">{{ $item->description }}</textarea>
                                                    </div>

                                                    <div class="mb-3 form-group-form-edit-{{ $item->id }}" style="{{ $item->type === 'form' ? '' : 'display:none;' }}">
                                                        <label class="form-label">Select Form <span class="text-danger">*</span></label>
                                                        <select name="custom_form_id" class="form-select">
                                                            <option value="">-- Choose a Custom Form --</option>
                                                            @foreach($customForms as $cf)
                                                                <option value="{{ $cf->id }}" {{ $item->custom_form_id == $cf->id ? 'selected' : '' }}>{{ $cf->title }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="mb-3 form-group-url-edit-{{ $item->id }}" style="{{ $item->type === 'external' ? '' : 'display:none;' }}">
                                                        <label class="form-label">External Link / URL <span class="text-danger">*</span></label>
                                                        <input type="text" name="url" class="form-control" value="{{ $item->url }}" placeholder="https://example.com, #, or mailto:info@domain.com">
                                                        <small class="hc-subtitle d-block mt-1">Allows URLs (https://), email links (mailto:email@domain.com), anchor hashes (#), or phone links (tel:).</small>
                                                    </div>

                                                    <div class="mb-3 form-group-file-edit-{{ $item->id }}" style="{{ $item->type === 'file' ? '' : 'display:none;' }}">
                                                        <label class="form-label">Upload Document File</label>
                                                        <input type="file" name="file" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip,.rar,.png,.jpg,.jpeg,.webp">
                                                        @if($item->file_path)
                                                            <small class="text-success d-block mt-1"><i class="bx bx-check-circle me-1"></i> Current File: {{ basename($item->file_path) }}</small>
                                                        @endif
                                                        <small class="hc-subtitle d-block mt-1">Supports PDF, DOCX, XLSX, Images & Zip up to 25MB.</small>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Icon</label>
                                                        <select name="icon" class="form-select">
                                                            <option value="bx-file" {{ $item->icon == 'bx-file' || empty($item->icon) ? 'selected' : '' }}>📄 File / Document (bx-file)</option>
                                                            <option value="bx-list-check" {{ $item->icon == 'bx-list-check' ? 'selected' : '' }}>📋 Form (bx-list-check)</option>
                                                            <option value="bx-link-external" {{ $item->icon == 'bx-link-external' ? 'selected' : '' }}>🔗 External Link (bx-link-external)</option>
                                                            <option value="bx-book-open" {{ $item->icon == 'bx-book-open' ? 'selected' : '' }}>📖 Manual / Guide (bx-book-open)</option>
                                                            <option value="bx-download" {{ $item->icon == 'bx-download' ? 'selected' : '' }}>📥 Download (bx-download)</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
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
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Section</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Section Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control" value="{{ $section->title }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control" rows="2">{{ $section->description }}</textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Update Section</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- ADD ITEM MODAL FOR SECTION -->
                <div class="modal fade" id="addItemModal-{{ $section->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <form action="{{ route('admin.help-center.items.store', $section->id) }}" method="POST" enctype="multipart/form-data" class="modal-content">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title">Add Link Item to "{{ $section->title }}"</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Link Type <span class="text-danger">*</span></label>
                                    <select name="type" class="form-select" onchange="toggleItemCreateFields(this, {{ $section->id }})" required>
                                        <option value="form" selected>Custom Form Link</option>
                                        <option value="external">External Link (URL / mailto / #)</option>
                                        <option value="file">Upload Document File (PDF, DOCX, XLSX)</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Link Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control" placeholder="e.g., VIP Event Booking Form" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control" rows="2" placeholder="Brief details about what this form or document is for..."></textarea>
                                </div>

                                <div class="mb-3 form-group-form-create-{{ $section->id }}">
                                    <label class="form-label">Select Custom Form <span class="text-danger">*</span></label>
                                    <select name="custom_form_id" class="form-select">
                                        <option value="">-- Select a Form --</option>
                                        @foreach($customForms as $cf)
                                            <option value="{{ $cf->id }}">{{ $cf->title }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3 form-group-url-create-{{ $section->id }}" style="display:none;">
                                    <label class="form-label">External Link / URL <span class="text-danger">*</span></label>
                                    <input type="text" name="url" class="form-control" placeholder="https://example.com, #, or mailto:info@domain.com">
                                    <small class="hc-subtitle d-block mt-1">Allows URLs (https://), email links (mailto:email@domain.com), anchor hashes (#), or phone links (tel:).</small>
                                </div>

                                <div class="mb-3 form-group-file-create-{{ $section->id }}" style="display:none;">
                                    <label class="form-label">Upload File <span class="text-danger">*</span></label>
                                    <input type="file" name="file" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip,.rar,.png,.jpg,.jpeg,.webp">
                                    <small class="hc-subtitle d-block mt-1">Supports PDF, Word (DOCX), Excel (XLSX), Images & Zip up to 25MB.</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Icon</label>
                                    <select name="icon" class="form-select">
                                        <option value="bx-file">📄 File / Document (bx-file)</option>
                                        <option value="bx-list-check">📋 Form (bx-list-check)</option>
                                        <option value="bx-link-external">🔗 External Link (bx-link-external)</option>
                                        <option value="bx-book-open">📖 Manual / Guide (bx-book-open)</option>
                                        <option value="bx-download">📥 Download (bx-download)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
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
            <div class="modal-header">
                <h5 class="modal-title">Add New Section</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Section Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" placeholder="e.g., Daily Operations Forms" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="Optional summary for this section..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Section</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Initialize Section Drag & Drop
    var sectionsContainer = document.getElementById('sectionsContainer');
    if (sectionsContainer && typeof Sortable !== 'undefined') {
        new Sortable(sectionsContainer, {
            handle: '.drag-section-handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            onEnd: function () {
                var sectionCards = sectionsContainer.querySelectorAll('.section-card');
                var sectionIds = Array.from(sectionCards).map(function(card) {
                    return card.getAttribute('data-section-id');
                });

                fetch("{{ route('admin.help-center.sections.reorder', $page->id) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ orders: sectionIds })
                })
                .then(function(response) { return response.json(); })
                .then(function(data) {
                    if (data.success) {
                        showSortToast('Sections re-ordered successfully!');
                    }
                });
            }
        });
    }

    // 2. Initialize Link Items Drag & Drop inside each Section
    var itemsContainers = document.querySelectorAll('.items-container');
    itemsContainers.forEach(function (container) {
        if (typeof Sortable !== 'undefined') {
            new Sortable(container, {
                group: 'items', // allows dragging between different sections
                handle: '.drag-item-handle',
                animation: 150,
                ghostClass: 'sortable-ghost',
                onEnd: function (evt) {
                    var currentContainer = evt.to;
                    var sectionId = currentContainer.getAttribute('data-section-id');
                    var itemRows = currentContainer.querySelectorAll('.item-row');
                    var itemIds = Array.from(itemRows).map(function(row) {
                        return row.getAttribute('data-item-id');
                    });

                    var url = "{{ route('admin.help-center.items.reorder', ':sectionId') }}".replace(':sectionId', sectionId);

                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ orders: itemIds })
                    })
                    .then(function(response) { return response.json(); })
                    .then(function(data) {
                        if (data.success) {
                            showSortToast('Link items re-ordered successfully!');
                        }
                    });
                }
            });
        }
    });
});

function showSortToast(msg) {
    var toastEl = document.getElementById('sortToast');
    if (!toastEl) {
        toastEl = document.createElement('div');
        toastEl.id = 'sortToast';
        toastEl.style.cssText = 'position: fixed; bottom: 24px; right: 24px; z-index: 9999; background: #4f46e5; color: #ffffff; padding: 12px 24px; border-radius: 8px; font-weight: 600; box-shadow: 0 10px 25px rgba(0,0,0,0.5); display: none; border: 1px solid rgba(255,255,255,0.2);';
        document.body.appendChild(toastEl);
    }
    toastEl.textContent = msg;
    toastEl.style.display = 'block';
    setTimeout(function() {
        toastEl.style.display = 'none';
    }, 2500);
}

function toggleItemCreateFields(selectEl, sectionId) {
    var val = selectEl.value;
    var formGrp = document.querySelector('.form-group-form-create-' + sectionId);
    var urlGrp = document.querySelector('.form-group-url-create-' + sectionId);
    var fileGrp = document.querySelector('.form-group-file-create-' + sectionId);
    if (formGrp) formGrp.style.display = (val === 'form' ? 'block' : 'none');
    if (urlGrp) urlGrp.style.display = (val === 'external' ? 'block' : 'none');
    if (fileGrp) fileGrp.style.display = (val === 'file' ? 'block' : 'none');
}

function toggleItemEditFields(selectEl, itemId) {
    var val = selectEl.value;
    var formGrp = document.querySelector('.form-group-form-edit-' + itemId);
    var urlGrp = document.querySelector('.form-group-url-edit-' + itemId);
    var fileGrp = document.querySelector('.form-group-file-edit-' + itemId);
    if (formGrp) formGrp.style.display = (val === 'form' ? 'block' : 'none');
    if (urlGrp) urlGrp.style.display = (val === 'external' ? 'block' : 'none');
    if (fileGrp) fileGrp.style.display = (val === 'file' ? 'block' : 'none');
}
</script>
@endsection
