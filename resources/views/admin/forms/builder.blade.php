@extends('admin.main')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">

<style>
/* ─── Forms Builder Dashboard (Transactions Aesthetics) ──────────────── */
.forms-card {
    background: linear-gradient(145deg, rgba(255,255,255,0.05), rgba(255,255,255,0.02));
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 16px;
    padding: 22px;
}

/* Header Action Buttons */
.btn-create-form {
    background: linear-gradient(135deg, #7c3aed 0%, #6366f1 100%);
    border: 1px solid rgba(255, 255, 255, 0.15);
    color: #ffffff !important;
    font-weight: 600;
    font-size: 0.85rem;
    padding: 9px 20px;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(124, 58, 237, 0.35);
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
}
.btn-create-form:hover {
    background: linear-gradient(135deg, #6d28d9 0%, #4f46e5 100%);
    color: #ffffff !important;
    box-shadow: 0 6px 20px rgba(124, 58, 237, 0.5);
    transform: translateY(-1px);
}

.txn-header-btn {
    background: rgba(255, 255, 255, 0.07);
    border: 1px solid rgba(255, 255, 255, 0.12);
    color: rgba(255, 255, 255, 0.85) !important;
    border-radius: 10px;
    font-size: 0.85rem;
    font-weight: 600;
    padding: 9px 16px;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
}
.txn-header-btn:hover {
    background: rgba(255, 255, 255, 0.14);
    color: #ffffff !important;
    transform: translateY(-1px);
}

/* Left Sidebar Tab Navigation */
.builder-nav-tabs {
    background: rgba(15, 23, 42, 0.7);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    padding: 4px;
    display: flex;
    gap: 4px;
}
.builder-nav-tab {
    flex: 1;
    border: none;
    background: transparent;
    color: rgba(255, 255, 255, 0.6);
    font-size: 0.82rem;
    font-weight: 600;
    padding: 8px 12px;
    border-radius: 8px;
    transition: all 0.2s ease;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}
.builder-nav-tab:hover {
    color: #ffffff;
    background: rgba(255, 255, 255, 0.05);
}
.builder-nav-tab.active {
    background: rgba(124, 58, 237, 0.25);
    border: 1px solid rgba(124, 58, 237, 0.5);
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(124, 58, 237, 0.25);
}

/* Draggable Palette Tiles */
.builder-palette-item {
    cursor: grab;
    transition: all 0.2s ease;
    border: 1px solid rgba(255,255,255,0.08);
    background: rgba(255,255,255,0.04);
    border-radius: 10px;
    padding: 10px;
}
.builder-palette-item:hover {
    background: rgba(124, 58, 237, 0.18);
    border-color: rgba(124, 58, 237, 0.45);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
}

/* Form Controls Inputs inside Inspector */
.txn-search-input {
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    color: #fff;
    font-size: 0.85rem;
    padding: 9px 14px;
    outline: none;
    width: 100%;
    transition: all 0.2s ease;
}
.txn-search-input:focus {
    border-color: rgba(124, 58, 237, 0.6);
    background: rgba(255, 255, 255, 0.08);
    box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.2);
}
.txn-search-input::placeholder {
    color: rgba(255, 255, 255, 0.35);
}

.txn-filter-select {
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    color: #fff;
    font-size: 0.85rem;
    padding: 9px 14px;
    outline: none;
    transition: all 0.2s ease;
}
.txn-filter-select:focus {
    border-color: rgba(124, 58, 237, 0.6);
    box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.2);
}
.txn-filter-select option {
    background: #1e293b;
    color: #ffffff;
}

/* Canvas Area (Wider Layout) */
.builder-canvas {
    min-height: 540px;
    background: rgba(15, 23, 42, 0.5);
    border: 2px dashed rgba(124, 58, 237, 0.3);
    border-radius: 14px;
    padding: 24px;
}

/* Field Cards on Canvas */
.field-card {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 12px;
    padding: 18px;
    margin-bottom: 14px;
    cursor: pointer;
    position: relative;
    transition: all 0.2s ease;
}
.field-card:hover {
    border-color: rgba(124, 58, 237, 0.4);
    background: rgba(255, 255, 255, 0.05);
}
.field-card.selected {
    border-color: #7c3aed;
    background: rgba(124, 58, 237, 0.08);
    box-shadow: 0 0 15px rgba(124, 58, 237, 0.25);
}
.field-drag-handle {
    cursor: move;
    color: #a78bfa;
    font-size: 1.1rem;
    margin-right: 8px;
}
.field-actions {
    position: absolute;
    top: 14px;
    right: 14px;
    display: flex;
    gap: 4px;
}
.field-action-icon-btn {
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.12);
    color: rgba(255, 255, 255, 0.7);
    width: 28px;
    height: 28px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    transition: all 0.2s ease;
    cursor: pointer;
}
.field-action-icon-btn:hover {
    background: rgba(124, 58, 237, 0.3);
    color: #ffffff;
    border-color: rgba(124, 58, 237, 0.6);
}
.field-action-icon-btn.btn-delete:hover {
    background: rgba(239, 68, 68, 0.3);
    color: #fca5a5;
    border-color: rgba(239, 68, 68, 0.6);
}

/* File Upload Preview Drop Zone (Reference Image Style) */
.file-upload-preview-box {
    background: rgba(15, 23, 42, 0.6);
    border: 2px dashed rgba(124, 58, 237, 0.35);
    border-radius: 10px;
    padding: 24px 16px;
    text-align: center;
    color: #94a3b8;
    transition: all 0.2s ease;
}
.file-upload-preview-box:hover {
    border-color: rgba(124, 58, 237, 0.6);
    background: rgba(15, 23, 42, 0.8);
}

/* Custom Toggle Switch */
.toggle-switch {
    position: relative;
    display: inline-block;
    width: 48px;
    height: 26px;
    margin-bottom: 0;
}
.toggle-switch-input {
    opacity: 0;
    width: 0;
    height: 0;
}
.toggle-switch-slider {
    position: absolute;
    cursor: pointer;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: rgba(255, 255, 255, 0.12);
    transition: 0.25s ease;
    border-radius: 999px;
    border: 1px solid rgba(255, 255, 255, 0.15);
}
.toggle-switch-slider::before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: #ffffff;
    transition: 0.25s ease;
    border-radius: 50%;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}
.toggle-switch-input:checked + .toggle-switch-slider {
    background-color: #7c3aed;
    border-color: #7c3aed;
}
.toggle-switch-input:checked + .toggle-switch-slider::before {
    transform: translateX(22px);
}

.audit-timeline {
    border-left: 2px solid rgba(255,255,255,0.1);
    padding-left: 16px;
    margin-left: 8px;
}
.audit-item {
    position: relative;
    margin-bottom: 16px;
}
.audit-item::before {
    content: '';
    position: absolute;
    left: -21px;
    top: 4px;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #7c3aed;
}
</style>

<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        
        <form id="builderForm" method="POST" action="{{ isset($form) ? route('admin.forms.update', $form->id) : route('admin.forms.store') }}">
            @csrf
            @if(isset($form))
                @method('PUT')
            @endif

            <input type="hidden" name="fields_schema" id="fieldsSchemaInput" value="{{ isset($form) ? json_encode($form->fields_schema) : '[]' }}">
            <input type="hidden" name="settings" id="settingsInput" value="{{ isset($form) ? json_encode($form->settings) : '{}' }}">

            <!-- Header Toolbar -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                <div>
                    <h4 class="mb-1 text-white fw-bold">
                        <i class="bx bx-slider-alt me-2 text-primary"></i>{{ isset($form) ? 'Edit Form: ' . $form->title : 'Create New Drag & Drop Form' }}
                    </h4>
                    <p class="text-muted mb-0 small">Drag components onto the wide layout canvas or reorder fields directly.</p>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <button type="button" class="txn-header-btn" data-bs-toggle="modal" data-bs-target="#settingsModal">
                        <i class="bx bx-cog"></i> Settings & Clubs
                    </button>
                    @if(isset($form) && $form->activityLogs->count() > 0)
                        <button type="button" class="txn-header-btn" data-bs-toggle="offcanvas" data-bs-target="#auditLogDrawer">
                            <i class="bx bx-history"></i> Audit Logs ({{ $form->activityLogs->count() }})
                        </button>
                    @endif
                    <a href="{{ route('admin.forms.index') }}" class="txn-header-btn">Cancel</a>
                    <button type="submit" class="btn-create-form border-0" id="saveFormBtn">
                        <i class="bx bx-save"></i> Save Form
                    </button>
                </div>
            </div>

            <!-- Main Builder Grid (2 Columns: Left Sidebar with Tabs + Wide Right Canvas) -->
            <div class="row g-4">
                
                <!-- Left Sidebar Column: Tabbed Inspector & Components Palette (4 Cols) -->
                <div class="col-lg-4 col-xl-3.5">
                    <div class="forms-card h-100">
                        
                        <!-- Top Navigation Tabs (Add Fields vs Field Options) -->
                        <div class="builder-nav-tabs mb-4">
                            <button type="button" class="builder-nav-tab active" id="tabAddFieldsBtn">
                                <i class="bx bx-plus-circle me-1.5"></i> Add Fields
                            </button>
                            <button type="button" class="builder-nav-tab" id="tabFieldOptionsBtn">
                                <i class="bx bx-slider-alt me-1.5"></i> Field Options
                                <span id="selectedFieldBadge" class="badge bg-primary ms-1.5 d-none">#1</span>
                            </button>
                        </div>

                        <!-- TAB 1: Add Fields Content -->
                        <div id="tabContentAddFields">
                            <div class="fw-semibold text-muted micro-text text-uppercase mb-2" style="letter-spacing:0.07em">Drag Field Components</div>
                            <div class="row g-2 mb-4">
                                <div class="col-6">
                                    <div class="builder-palette-item text-center small text-white" draggable="true" data-type="text">
                                        <i class="bx bx-text fs-4 d-block mb-1 text-primary"></i> Short Text
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="builder-palette-item text-center small text-white" draggable="true" data-type="textarea">
                                        <i class="bx bx-paragraph fs-4 d-block mb-1 text-info"></i> Long Text
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="builder-palette-item text-center small text-white" draggable="true" data-type="file">
                                        <i class="bx bx-upload fs-4 d-block mb-1 text-warning"></i> File Upload
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="builder-palette-item text-center small text-white" draggable="true" data-type="email">
                                        <i class="bx bx-envelope fs-4 d-block mb-1 text-success"></i> Email
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="builder-palette-item text-center small text-white" draggable="true" data-type="phone">
                                        <i class="bx bx-phone fs-4 d-block mb-1 text-info"></i> Phone
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="builder-palette-item text-center small text-white" draggable="true" data-type="select">
                                        <i class="bx bx-select-multiple fs-4 d-block mb-1 text-danger"></i> Dropdown
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="builder-palette-item text-center small text-white" draggable="true" data-type="radio">
                                        <i class="bx bx-radio-circle-marked fs-4 d-block mb-1 text-primary"></i> Radio List
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="builder-palette-item text-center small text-white" draggable="true" data-type="checkbox">
                                        <i class="bx bx-checkbox-checked fs-4 d-block mb-1 text-success"></i> Single Checkbox
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="builder-palette-item text-center small text-white" draggable="true" data-type="date">
                                        <i class="bx bx-calendar fs-4 d-block mb-1 text-info"></i> Date Picker
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="builder-palette-item text-center small text-white" draggable="true" data-type="time">
                                        <i class="bx bx-time-five fs-4 d-block mb-1 text-warning"></i> Time Picker
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="builder-palette-item text-center small text-white" draggable="true" data-type="number">
                                        <i class="bx bx-hash fs-4 d-block mb-1 text-primary"></i> Number
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="builder-palette-item text-center small text-white" draggable="true" data-type="heading">
                                        <i class="bx bx-heading fs-4 d-block mb-1 text-light"></i> Heading
                                    </div>
                                </div>
                            </div>

                            <div class="border-top border-secondary border-opacity-10 pt-3 mt-3">
                                <div class="fw-semibold text-muted micro-text text-uppercase mb-3" style="letter-spacing:0.07em">Form Information</div>
                                
                                <div class="mb-3">
                                    <label class="form-label text-white small fw-semibold">Form Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" id="formTitleInput" class="txn-search-input" placeholder="e.g. Employment Application" value="{{ isset($form) ? $form->title : '' }}" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label text-white small fw-semibold">Form Description</label>
                                    <textarea name="description" id="formDescInput" class="txn-search-input" rows="3" placeholder="Brief explanation for visitors...">{{ isset($form) ? $form->description : '' }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 2: Field Options Inspector Content -->
                        <div id="tabContentFieldOptions" style="display: none;">
                            <div class="text-center text-muted py-5" id="noSelectionNotice">
                                <i class="bx bx-pointer fs-1 d-block mb-2 text-primary"></i>
                                Click any field on the canvas to configure label, rules, and file parameters.
                            </div>

                            <div id="inspectorForm" style="display: none;">
                                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-secondary border-opacity-10">
                                    <h6 class="mb-0 text-white fw-bold d-flex align-items-center gap-2">
                                        <span id="inspectorFieldTypeTitle">Field Options</span>
                                        <span id="inspectorFieldId" class="text-muted font-monospace micro-text"></span>
                                    </h6>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label text-white small fw-semibold">Label <span class="text-danger">*</span></label>
                                    <input type="text" id="propLabel" class="txn-search-input" placeholder="e.g. Resume required for server">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label text-white small fw-semibold">Description / Help Text</label>
                                    <textarea id="propHelpText" class="txn-search-input" rows="2" placeholder="Subtext or instructions for users..."></textarea>
                                </div>

                                <div class="mb-3" id="placeholderGroup">
                                    <label class="form-label text-white small fw-semibold">Placeholder</label>
                                    <input type="text" id="propPlaceholder" class="txn-search-input" placeholder="Enter placeholder...">
                                </div>

                                <!-- Detailed File Upload Options (Matching Client Reference Image) -->
                                <div id="fileUploadPropertiesGroup" style="display: none;" class="p-3 mb-3 rounded-3" style="background: rgba(15,23,42,0.6); border: 1px solid rgba(124,58,237,0.25);">
                                    <div class="fw-semibold text-primary micro-text text-uppercase mb-3" style="letter-spacing:0.05em">
                                        <i class="bx bx-upload me-1"></i> File Upload Configuration
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label text-white small fw-semibold d-flex justify-content-between">
                                            <span>Allowed File Extensions</span>
                                            <span class="text-muted micro-text">Comma separated</span>
                                        </label>
                                        <input type="text" id="propAllowedExtensions" class="txn-search-input" placeholder="pdf, doc, docx, png, jpg">
                                        <span class="form-text text-muted micro-text">e.g. pdf, doc, docx, png, jpg</span>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label text-white small fw-semibold d-flex justify-content-between">
                                            <span>Max File Size (MB)</span>
                                            <span class="text-muted micro-text">Size in MB</span>
                                        </label>
                                        <input type="number" id="propMaxFileSize" class="txn-search-input" min="1" max="100" placeholder="5">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label text-white small fw-semibold d-flex justify-content-between">
                                            <span>Max File Uploads</span>
                                            <span class="text-muted micro-text">Max limit</span>
                                        </label>
                                        <input type="number" id="propMaxFileUploads" class="txn-search-input" min="1" max="20" placeholder="1">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label text-white small fw-semibold">Column Width</label>
                                    <select id="propWidth" class="txn-filter-select w-100">
                                        <option value="col-12">Full Width (100%)</option>
                                        <option value="col-md-6">Half Width (50%)</option>
                                        <option value="col-md-4">One Third (33%)</option>
                                        <option value="col-md-8">Two Thirds (66%)</option>
                                    </select>
                                </div>

                                <div class="mb-3" id="optionsGroup" style="display: none;">
                                    <label class="form-label text-white small fw-semibold">Options (One per line)</label>
                                    <textarea id="propOptions" class="txn-search-input" rows="4" placeholder="Option 1&#10;Option 2&#10;Option 3"></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label text-white small fw-semibold">Field Key (ID)</label>
                                    <input type="text" id="propName" class="txn-search-input" placeholder="e.g. resume_file">
                                </div>

                                <div class="d-flex align-items-center justify-content-between p-3 rounded-3 mt-3" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08);">
                                    <label class="form-label text-white mb-0 fw-semibold small">Required Field</label>
                                    <label class="toggle-switch" for="propRequired">
                                        <input type="checkbox" id="propRequired" class="toggle-switch-input">
                                        <span class="toggle-switch-slider"></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Right Canvas Column: Wide Layout Canvas (8 Cols) -->
                <div class="col-lg-8 col-xl-8.5">
                    <div class="forms-card">
                        <div class="d-flex justify-content-between align-items-center border-bottom border-secondary border-opacity-10 pb-3 mb-4">
                            <h5 class="mb-0 text-white fw-bold fs-6">
                                <i class="bx bx-layout me-2 text-primary"></i>Form Layout Canvas
                            </h5>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-primary bg-opacity-25 text-primary border border-primary border-opacity-25 px-3 py-1.5 rounded-pill" id="fieldCountBadge">0 Fields</span>
                            </div>
                        </div>
                        
                        <div class="builder-canvas row g-3" id="canvasContainer">
                            <div class="text-center text-muted py-5" id="emptyCanvasNotice">
                                <i class="bx bx-mouse fs-1 d-block mb-2 text-primary"></i>
                                Drag components from the left panel onto this wide layout canvas to construct your form.
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Settings Modal -->
            <div class="modal fade" id="settingsModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content bg-dark text-white border-secondary">
                        <div class="modal-header border-bottom border-secondary">
                            <h5 class="modal-title text-white"><i class="bx bx-cog me-2 text-primary"></i>Form Settings & Club Access</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label text-white small fw-semibold">Target Clubs / Websites</label>
                                <select name="website_ids[]" class="txn-filter-select w-100" multiple style="height: 120px;">
                                    @foreach($websites as $web)
                                        <option value="{{ $web->id }}" {{ isset($form) && is_array($form->website_ids) && in_array($web->id, $form->website_ids) ? 'selected' : '' }}>
                                            {{ $web->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="form-text text-muted small">Leave empty to make form accessible across all clubs. Hold Ctrl/Cmd to select multiple.</span>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-white small fw-semibold">Success Message on Submission</label>
                                <textarea id="successMessageInput" class="txn-search-input" rows="3" placeholder="Thank you! Your form submission has been received."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-top border-secondary">
                            <button type="button" class="btn-create-form border-0" data-bs-dismiss="modal">Done</button>
                        </div>
                    </div>
                </div>
            </div>

        </form>

        <!-- Audit Log Drawer (Offcanvas) -->
        @if(isset($form) && $form->activityLogs->count() > 0)
            <div class="offcanvas offcanvas-end bg-dark text-white border-secondary" tabindex="-1" id="auditLogDrawer" style="width: 400px;">
                <div class="offcanvas-header border-bottom border-secondary">
                    <h5 class="offcanvas-title text-white"><i class="bx bx-history me-2 text-primary"></i>Audit Activity Log</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
                </div>
                <div class="offcanvas-body">
                    <div class="audit-timeline">
                        @foreach($form->activityLogs as $log)
                            <div class="audit-item">
                                <div class="fw-bold text-white small">
                                    {{ ucfirst($log->action) }} by {{ optional($log->user)->name ?: 'System' }}
                                </div>
                                <div class="text-muted small mb-1">{{ $log->changes_summary }}</div>
                                <div class="text-secondary micro-text font-monospace">
                                    {{ $log->created_at ? $log->created_at->format('M d, Y h:i A') : '' }} | IP: {{ $log->ip_address ?: '127.0.0.1' }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let fields = [];
    try {
        fields = JSON.parse(document.getElementById('fieldsSchemaInput').value || '[]');
    } catch(e) {
        fields = [];
    }

    let selectedFieldIndex = null;
    let draggedCanvasIdx = null;

    const canvas = document.getElementById('canvasContainer');
    const emptyNotice = document.getElementById('emptyCanvasNotice');
    const inspectorForm = document.getElementById('inspectorForm');
    const noSelectionNotice = document.getElementById('noSelectionNotice');
    const fieldCountBadge = document.getElementById('fieldCountBadge');

    // Tab switching elements
    const tabAddFieldsBtn = document.getElementById('tabAddFieldsBtn');
    const tabFieldOptionsBtn = document.getElementById('tabFieldOptionsBtn');
    const tabContentAddFields = document.getElementById('tabContentAddFields');
    const tabContentFieldOptions = document.getElementById('tabContentFieldOptions');
    const selectedFieldBadge = document.getElementById('selectedFieldBadge');

    function switchLeftTab(tabName) {
        if (tabName === 'options') {
            tabAddFieldsBtn.classList.remove('active');
            tabFieldOptionsBtn.classList.add('active');
            tabContentAddFields.style.display = 'none';
            tabContentFieldOptions.style.display = 'block';
        } else {
            tabFieldOptionsBtn.classList.remove('active');
            tabAddFieldsBtn.classList.add('active');
            tabContentFieldOptions.style.display = 'none';
            tabContentAddFields.style.display = 'block';
        }
    }

    tabAddFieldsBtn.addEventListener('click', () => switchLeftTab('add'));
    tabFieldOptionsBtn.addEventListener('click', () => switchLeftTab('options'));

    // Drag from palette
    document.querySelectorAll('.builder-palette-item').forEach(item => {
        item.addEventListener('dragstart', (e) => {
            e.dataTransfer.setData('source', 'palette');
            e.dataTransfer.setData('field-type', item.getAttribute('data-type'));
        });
    });

    canvas.addEventListener('dragover', (e) => e.preventDefault());
    canvas.addEventListener('drop', (e) => {
        e.preventDefault();
        const source = e.dataTransfer.getData('source');
        if (source === 'palette') {
            const type = e.dataTransfer.getData('field-type');
            if (type) addField(type);
        } else if (source === 'canvas') {
            const targetIdxStr = e.target.closest('.field-card-wrapper')?.getAttribute('data-idx');
            if (targetIdxStr !== null && targetIdxStr !== undefined) {
                const targetIdx = parseInt(targetIdxStr);
                if (draggedCanvasIdx !== null && draggedCanvasIdx !== targetIdx) {
                    const movedItem = fields.splice(draggedCanvasIdx, 1)[0];
                    fields.splice(targetIdx, 0, movedItem);
                    selectField(targetIdx);
                }
            }
        }
    });

    function addField(type) {
        const id = 'field_' + Math.random().toString(36).substr(2, 9);
        let defaultLabel = ucfirst(type) + ' Field';
        if (type === 'heading') defaultLabel = 'Section Heading';
        if (type === 'checkbox') defaultLabel = 'I agree to the terms and conditions';
        if (type === 'file') defaultLabel = 'Resume required for server and/or bartender';

        const newField = {
            id: id,
            type: type,
            label: defaultLabel,
            name: id,
            placeholder: '',
            help_text: '',
            allowed_extensions: type === 'file' ? 'pdf, doc, docx, png, jpg' : '',
            max_file_size: type === 'file' ? 5 : null,
            max_file_uploads: type === 'file' ? 1 : null,
            width_class: 'col-12',
            required: false,
            options: ['Option 1', 'Option 2', 'Option 3']
        };

        fields.push(newField);
        selectField(fields.length - 1);
    }

    function renderCanvas() {
        canvas.innerHTML = '';
        if (fields.length === 0) {
            canvas.appendChild(emptyNotice);
            emptyNotice.style.display = 'block';
            fieldCountBadge.innerText = '0 Fields';
            return;
        }

        emptyNotice.style.display = 'none';
        fieldCountBadge.innerText = fields.length + ' Fields';

        fields.forEach((f, idx) => {
            const wrapper = document.createElement('div');
            wrapper.className = (f.width_class || 'col-12') + ' field-card-wrapper';
            wrapper.setAttribute('data-idx', idx);

            const card = document.createElement('div');
            card.className = 'field-card' + (selectedFieldIndex === idx ? ' selected' : '');
            card.setAttribute('draggable', 'true');

            let inputPreview = '';
            if (f.type === 'textarea') {
                inputPreview = `<textarea class="txn-search-input" placeholder="${f.placeholder || ''}" rows="3" disabled></textarea>`;
            } else if (f.type === 'select') {
                inputPreview = `<select class="txn-filter-select w-100" disabled><option>${f.options ? f.options[0] : 'Select option...'}</option></select>`;
            } else if (f.type === 'checkbox') {
                inputPreview = `
                    <div class="form-check mt-1">
                        <input class="form-check-input" type="checkbox" disabled>
                        <label class="form-check-label text-white small">${f.label}</label>
                    </div>`;
            } else if (f.type === 'file') {
                const exts = f.allowed_extensions || 'pdf, doc, docx, png, jpg';
                const maxSize = f.max_file_size || 5;
                const maxCount = f.max_file_uploads || 1;
                inputPreview = `
                    <div class="file-upload-preview-box mt-2">
                        <i class="bx bx-cloud-upload text-primary" style="font-size: 2.2rem;"></i>
                        <div class="text-white fw-semibold small mt-1">Drag & Drop File or <span class="text-primary text-decoration-underline">Choose File to Upload</span></div>
                        <div class="text-muted micro-text mt-1">Supported: <strong>${exts}</strong> | Max Size: <strong>${maxSize}MB</strong> | Limit: <strong>${maxCount} file(s)</strong></div>
                    </div>
                `;
            } else if (f.type === 'time') {
                inputPreview = `<input type="text" class="txn-search-input" placeholder="Select Time (e.g. 10:30 PM)" disabled>`;
            } else if (f.type === 'heading') {
                inputPreview = `<h5 class="text-white fw-bold mb-0 border-bottom border-secondary border-opacity-25 pb-2">${f.label}</h5>`;
            } else {
                inputPreview = `<input type="${f.type === 'phone' ? 'tel' : f.type}" class="txn-search-input" placeholder="${f.placeholder || ''}" disabled>`;
            }

            card.innerHTML = `
                <div class="field-actions">
                    <button type="button" class="field-action-icon-btn move-up-btn" data-idx="${idx}" title="Move Up" ${idx === 0 ? 'disabled' : ''}>
                        <i class="bx bx-chevron-up"></i>
                    </button>
                    <button type="button" class="field-action-icon-btn move-down-btn" data-idx="${idx}" title="Move Down" ${idx === fields.length - 1 ? 'disabled' : ''}>
                        <i class="bx bx-chevron-down"></i>
                    </button>
                    <button type="button" class="field-action-icon-btn btn-delete delete-field-btn" data-idx="${idx}" title="Delete Field">
                        <i class="bx bx-trash"></i>
                    </button>
                </div>
                <div class="d-flex align-items-center mb-1">
                    <i class="bx bx-move field-drag-handle" title="Drag to relocate field"></i>
                    ${f.type !== 'heading' && f.type !== 'checkbox' ? `<label class="form-label text-white mb-0 fw-semibold small me-2">${f.label} ${f.required ? '<span class="text-danger">*</span>' : ''}</label>` : ''}
                </div>
                ${inputPreview}
                ${f.help_text ? `<div class="text-muted micro-text mt-1.5"><i class="bx bx-info-circle me-1 text-primary"></i>${f.help_text}</div>` : ''}
            `;

            // Field Card Drag & Drop Events for Relocating
            card.addEventListener('dragstart', (e) => {
                draggedCanvasIdx = idx;
                e.dataTransfer.setData('source', 'canvas');
                card.style.opacity = '0.5';
            });
            card.addEventListener('dragend', () => {
                card.style.opacity = '1';
                draggedCanvasIdx = null;
            });

            card.addEventListener('click', (e) => {
                if (e.target.closest('.field-actions')) return;
                selectField(idx);
            });

            wrapper.appendChild(card);
            canvas.appendChild(wrapper);
        });

        // Add Move Up / Move Down / Delete action handlers
        document.querySelectorAll('.move-up-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const idx = parseInt(btn.getAttribute('data-idx'));
                if (idx > 0) {
                    const temp = fields[idx];
                    fields[idx] = fields[idx - 1];
                    fields[idx - 1] = temp;
                    selectField(idx - 1);
                }
            });
        });

        document.querySelectorAll('.move-down-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const idx = parseInt(btn.getAttribute('data-idx'));
                if (idx < fields.length - 1) {
                    const temp = fields[idx];
                    fields[idx] = fields[idx + 1];
                    fields[idx + 1] = temp;
                    selectField(idx + 1);
                }
            });
        });

        document.querySelectorAll('.delete-field-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const idx = parseInt(btn.getAttribute('data-idx'));
                fields.splice(idx, 1);
                if (selectedFieldIndex === idx) selectedFieldIndex = null;
                else if (selectedFieldIndex > idx) selectedFieldIndex--;
                renderCanvas();
                updateInspector();
            });
        });
    }

    function selectField(idx) {
        selectedFieldIndex = idx;
        renderCanvas();
        updateInspector();
        switchLeftTab('options');
    }

    function updateInspector() {
        if (selectedFieldIndex === null || !fields[selectedFieldIndex]) {
            inspectorForm.style.display = 'none';
            noSelectionNotice.style.display = 'block';
            selectedFieldBadge.classList.add('d-none');
            return;
        }

        const f = fields[selectedFieldIndex];
        noSelectionNotice.style.display = 'none';
        inspectorForm.style.display = 'block';
        selectedFieldBadge.innerText = '#' + (selectedFieldIndex + 1);
        selectedFieldBadge.classList.remove('d-none');

        document.getElementById('inspectorFieldTypeTitle').innerText = ucfirst(f.type) + ' Options';
        document.getElementById('inspectorFieldId').innerText = '(ID #' + (selectedFieldIndex + 1) + ')';
        document.getElementById('propLabel').value = f.label || '';
        document.getElementById('propName').value = f.name || '';
        document.getElementById('propPlaceholder').value = f.placeholder || '';
        document.getElementById('propHelpText').value = f.help_text || '';
        document.getElementById('propWidth').value = f.width_class || 'col-12';
        document.getElementById('propRequired').checked = !!f.required;

        const optionsGroup = document.getElementById('optionsGroup');
        const placeholderGroup = document.getElementById('placeholderGroup');
        const fileUploadPropertiesGroup = document.getElementById('fileUploadPropertiesGroup');

        // Toggle File Upload specific properties
        if (f.type === 'file') {
            fileUploadPropertiesGroup.style.display = 'block';
            document.getElementById('propAllowedExtensions').value = f.allowed_extensions || 'pdf, doc, docx, png, jpg';
            document.getElementById('propMaxFileSize').value = f.max_file_size || 5;
            document.getElementById('propMaxFileUploads').value = f.max_file_uploads || 1;
        } else {
            fileUploadPropertiesGroup.style.display = 'none';
        }

        if (['select', 'radio'].includes(f.type)) {
            optionsGroup.style.display = 'block';
            document.getElementById('propOptions').value = (f.options || []).join('\n');
        } else {
            optionsGroup.style.display = 'none';
        }

        if (['checkbox', 'heading', 'file'].includes(f.type)) {
            placeholderGroup.style.display = 'none';
        } else {
            placeholderGroup.style.display = 'block';
        }
    }

    // Bind inspector inputs
    document.getElementById('propLabel').addEventListener('input', (e) => {
        if (selectedFieldIndex !== null) {
            fields[selectedFieldIndex].label = e.target.value;
            renderCanvas();
        }
    });
    document.getElementById('propName').addEventListener('input', (e) => {
        if (selectedFieldIndex !== null) {
            fields[selectedFieldIndex].name = e.target.value;
        }
    });
    document.getElementById('propPlaceholder').addEventListener('input', (e) => {
        if (selectedFieldIndex !== null) {
            fields[selectedFieldIndex].placeholder = e.target.value;
            renderCanvas();
        }
    });
    document.getElementById('propHelpText').addEventListener('input', (e) => {
        if (selectedFieldIndex !== null) {
            fields[selectedFieldIndex].help_text = e.target.value;
            renderCanvas();
        }
    });
    document.getElementById('propAllowedExtensions').addEventListener('input', (e) => {
        if (selectedFieldIndex !== null) {
            fields[selectedFieldIndex].allowed_extensions = e.target.value;
            renderCanvas();
        }
    });
    document.getElementById('propMaxFileSize').addEventListener('input', (e) => {
        if (selectedFieldIndex !== null) {
            fields[selectedFieldIndex].max_file_size = e.target.value ? parseInt(e.target.value) : 5;
            renderCanvas();
        }
    });
    document.getElementById('propMaxFileUploads').addEventListener('input', (e) => {
        if (selectedFieldIndex !== null) {
            fields[selectedFieldIndex].max_file_uploads = e.target.value ? parseInt(e.target.value) : 1;
            renderCanvas();
        }
    });
    document.getElementById('propWidth').addEventListener('change', (e) => {
        if (selectedFieldIndex !== null) {
            fields[selectedFieldIndex].width_class = e.target.value;
            renderCanvas();
        }
    });
    document.getElementById('propRequired').addEventListener('change', (e) => {
        if (selectedFieldIndex !== null) {
            fields[selectedFieldIndex].required = e.target.checked;
            renderCanvas();
        }
    });
    document.getElementById('propOptions').addEventListener('input', (e) => {
        if (selectedFieldIndex !== null) {
            fields[selectedFieldIndex].options = e.target.value.split('\n').map(s => s.trim()).filter(Boolean);
            renderCanvas();
        }
    });

    // Save form submit handler
    document.getElementById('builderForm').addEventListener('submit', function(e) {
        document.getElementById('fieldsSchemaInput').value = JSON.stringify(fields);

        const settings = {
            success_message: document.getElementById('successMessageInput').value || 'Thank you! Your form submission has been received.'
        };
        document.getElementById('settingsInput').value = JSON.stringify(settings);
    });

    function ucfirst(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    // Initial render
    renderCanvas();
});
</script>
@endsection
