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

/* Primary Top Mode Switcher (Fields vs Settings) */
.builder-mode-switcher {
    background: rgba(15, 23, 42, 0.7);
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 12px;
    padding: 4px;
    display: inline-flex;
    gap: 4px;
}
.builder-mode-btn {
    border: none;
    background: transparent;
    color: rgba(255, 255, 255, 0.65);
    font-size: 0.88rem;
    font-weight: 700;
    padding: 8px 20px;
    border-radius: 9px;
    transition: all 0.2s ease;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
}
.builder-mode-btn:hover {
    color: #ffffff;
    background: rgba(255, 255, 255, 0.06);
}
.builder-mode-btn.active {
    background: linear-gradient(135deg, #7c3aed 0%, #6366f1 100%);
    color: #ffffff;
    box-shadow: 0 4px 14px rgba(124, 58, 237, 0.35);
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

/* Settings Secondary Menu List */
.settings-menu-list {
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.settings-menu-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 11px 16px;
    border-radius: 10px;
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.88rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    border: 1px solid transparent;
    text-decoration: none;
}
.settings-menu-item:hover {
    color: #ffffff;
    background: rgba(255, 255, 255, 0.05);
}
.settings-menu-item.active {
    background: #0284c7;
    color: #ffffff;
    border-color: #0369a1;
    box-shadow: 0 4px 12px rgba(2, 132, 199, 0.3);
}

/* Sub-tabs under Field Options (General / Advanced / Smart Logic) */
.inspector-sub-tabs {
    display: flex;
    gap: 16px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    margin-bottom: 16px;
}
.inspector-sub-tab {
    background: transparent;
    border: none;
    color: rgba(255, 255, 255, 0.5);
    font-size: 0.8rem;
    font-weight: 600;
    padding: 6px 0;
    cursor: pointer;
    position: relative;
    transition: all 0.2s ease;
}
.inspector-sub-tab:hover {
    color: rgba(255, 255, 255, 0.85);
}
.inspector-sub-tab.active {
    color: #a78bfa;
}
.inspector-sub-tab.active::after {
    content: '';
    position: absolute;
    bottom: -1px;
    left: 0;
    right: 0;
    height: 2px;
    background: #7c3aed;
    border-radius: 2px;
}

/* Category Headers */
.palette-category-title {
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.06em;
    color: rgba(255, 255, 255, 0.45);
    text-transform: uppercase;
    margin-top: 14px;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

/* Draggable Palette Tiles */
.builder-palette-item {
    cursor: grab;
    transition: all 0.2s ease;
    border: 1px solid rgba(255,255,255,0.08);
    background: rgba(255,255,255,0.04);
    border-radius: 10px;
    padding: 9px;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.8rem;
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
    min-height: 580px;
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
    width: 46px;
    height: 24px;
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
    background-color: rgba(255, 255, 255, 0.15);
    transition: 0.25s ease;
    border-radius: 999px;
    border: 1px solid rgba(255, 255, 255, 0.18);
}
.toggle-switch-slider::before {
    position: absolute;
    content: "";
    height: 16px;
    width: 16px;
    left: 3px;
    bottom: 3px;
    background-color: #ffffff;
    transition: 0.25s ease;
    border-radius: 50%;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}
.toggle-switch-input:checked + .toggle-switch-slider {
    background-color: #0284c7;
    border-color: #0284c7;
}
.toggle-switch-input:checked + .toggle-switch-slider::before {
    transform: translateX(22px);
}

/* Choices Row Manager */
.choice-item-row {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 8px;
}
.btn-choice-icon {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    border: none;
    cursor: pointer;
}
.btn-choice-add { background: #3b82f6; color: #fff; }
.btn-choice-remove { background: #ef4444; color: #fff; }

/* Settings Inner Container */
.settings-section-card {
    background: rgba(15, 23, 42, 0.6);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 14px;
    padding: 24px;
    margin-bottom: 20px;
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

            @php
                $initialSettings = isset($form) && is_array($form->settings) ? $form->settings : [];
            @endphp

            <input type="hidden" name="fields_schema" id="fieldsSchemaInput" value="{{ isset($form) ? json_encode($form->fields_schema) : '[]' }}">
            <input type="hidden" name="settings" id="settingsInput" value="{{ json_encode($initialSettings) }}">

            <!-- Header Toolbar & Mode Switcher -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                <div class="d-flex align-items-center gap-3">
                    <h4 class="mb-0 text-white fw-bold me-2">
                        <i class="bx bx-slider-alt me-2 text-primary"></i>{{ isset($form) ? $form->title : 'Create New Form' }}
                    </h4>

                    <!-- Primary Mode Switcher: Fields vs Settings (Matching Reference Images) -->
                    <div class="builder-mode-switcher">
                        <button type="button" class="builder-mode-btn active" id="modeFieldsBtn">
                            <i class="bx bx-list-ul"></i> Fields
                        </button>
                        <button type="button" class="builder-mode-btn" id="modeSettingsBtn">
                            <i class="bx bx-cog"></i> Settings
                        </button>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 flex-wrap">
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

            <!-- MODE 1: FIELDS VIEW (Canvas & Components Inspector) -->
            <div id="builderModeFieldsView" class="row g-4">
                
                <!-- Left Sidebar Column: Tabbed Inspector & Categorized Palette (4 Cols) -->
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

                        <!-- TAB 1: Add Fields Content (Categorized Palette) -->
                        <div id="tabContentAddFields">
                            
                            <!-- Category: Standard Fields -->
                            <div class="palette-category-title">
                                <span><i class="bx bx-grid-alt me-1.5 text-primary"></i> Standard Fields</span>
                            </div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="builder-palette-item text-white" draggable="true" data-type="text">
                                        <i class="bx bx-text fs-5 text-primary"></i> Short Text
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="builder-palette-item text-white" draggable="true" data-type="textarea">
                                        <i class="bx bx-paragraph fs-5 text-info"></i> Paragraph
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="builder-palette-item text-white" draggable="true" data-type="select">
                                        <i class="bx bx-select-multiple fs-5 text-danger"></i> Dropdown
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="builder-palette-item text-white" draggable="true" data-type="radio">
                                        <i class="bx bx-radio-circle-marked fs-5 text-warning"></i> Multiple Choice
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="builder-palette-item text-white" draggable="true" data-type="checkbox">
                                        <i class="bx bx-checkbox-checked fs-5 text-success"></i> Checkboxes
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="builder-palette-item text-white" draggable="true" data-type="number">
                                        <i class="bx bx-hash fs-5 text-primary"></i> Numbers
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="builder-palette-item text-white" draggable="true" data-type="name">
                                        <i class="bx bx-user fs-5 text-info"></i> Name
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="builder-palette-item text-white" draggable="true" data-type="email">
                                        <i class="bx bx-envelope fs-5 text-warning"></i> Email
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="builder-palette-item text-white" draggable="true" data-type="captcha">
                                        <i class="bx bx-shield-quarter fs-5 text-danger"></i> CAPTCHA
                                    </div>
                                </div>
                            </div>

                            <!-- Category: Fancy Fields -->
                            <div class="palette-category-title">
                                <span><i class="bx bx-star me-1.5 text-warning"></i> Fancy Fields</span>
                            </div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="builder-palette-item text-white" draggable="true" data-type="phone">
                                        <i class="bx bx-phone fs-5 text-success"></i> Phone
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="builder-palette-item text-white" draggable="true" data-type="file">
                                        <i class="bx bx-upload fs-5 text-warning"></i> File Upload
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="builder-palette-item text-white" draggable="true" data-type="date">
                                        <i class="bx bx-calendar fs-5 text-info"></i> Date / Time
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="builder-palette-item text-white" draggable="true" data-type="time">
                                        <i class="bx bx-time-five fs-5 text-primary"></i> Time Picker
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="builder-palette-item text-white" draggable="true" data-type="heading">
                                        <i class="bx bx-heading fs-5 text-light"></i> Heading
                                    </div>
                                </div>
                            </div>

                            <!-- Form Meta Info Section -->
                            <div class="border-top border-secondary border-opacity-10 pt-3 mt-4">
                                <div class="fw-semibold text-muted micro-text text-uppercase mb-3" style="letter-spacing:0.07em">Form Quick Info</div>
                                
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
                                Click any field on the canvas to configure label, format, choices, and rules.
                            </div>

                            <div id="inspectorForm" style="display: none;">
                                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-secondary border-opacity-10">
                                    <h6 class="mb-0 text-white fw-bold d-flex align-items-center gap-2">
                                        <span id="inspectorFieldTypeTitle">Field Options</span>
                                        <span id="inspectorFieldId" class="text-muted font-monospace micro-text"></span>
                                    </h6>
                                </div>

                                <div class="inspector-sub-tabs">
                                    <button type="button" class="inspector-sub-tab active" id="subTabGeneral">General</button>
                                    <button type="button" class="inspector-sub-tab" id="subTabAdvanced">Advanced</button>
                                    <button type="button" class="inspector-sub-tab" id="subTabLogic">Smart Logic</button>
                                </div>

                                <div id="subTabContentGeneral">
                                    <div class="mb-3">
                                        <label class="form-label text-white small fw-semibold">Label <span class="text-danger">*</span></label>
                                        <input type="text" id="propLabel" class="txn-search-input" placeholder="e.g. Full Name">
                                    </div>

                                    <div class="mb-3" id="nameFormatGroup" style="display: none;">
                                        <label class="form-label text-white small fw-semibold">Format</label>
                                        <select id="propNameFormat" class="txn-filter-select w-100">
                                            <option value="first_last">First Last</option>
                                            <option value="first_middle_last">First Middle Last</option>
                                            <option value="simple">Simple / Single Line</option>
                                        </select>
                                    </div>

                                    <div class="mb-3" id="phoneFormatGroup" style="display: none;">
                                        <label class="form-label text-white small fw-semibold">Format</label>
                                        <select id="propPhoneFormat" class="txn-filter-select w-100">
                                            <option value="smart">Smart (International Flag Picker)</option>
                                            <option value="standard">Standard US (###) ###-####</option>
                                            <option value="international">International Raw</option>
                                        </select>
                                    </div>

                                    <div class="mb-3" id="choicesManagerGroup" style="display: none;">
                                        <label class="form-label text-white small fw-semibold d-flex justify-content-between">
                                            <span>Choices</span>
                                            <span class="text-primary micro-text cursor-pointer" id="btnToggleBulkChoices"><i class="bx bx-edit me-1"></i>Bulk Edit</span>
                                        </label>
                                        
                                        <div id="choicesListContainer"></div>

                                        <div id="bulkChoicesContainer" style="display: none;">
                                            <textarea id="propOptionsBulk" class="txn-search-input" rows="5" placeholder="Choice 1&#10;Choice 2&#10;Choice 3"></textarea>
                                        </div>
                                    </div>

                                    <div id="fileUploadPropertiesGroup" style="display: none;" class="p-3 mb-3 rounded-3" style="background: rgba(15,23,42,0.6); border: 1px solid rgba(124,58,237,0.25);">
                                        <div class="fw-semibold text-primary micro-text text-uppercase mb-3" style="letter-spacing:0.05em">
                                            <i class="bx bx-upload me-1"></i> File Upload Rules
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label text-white small fw-semibold d-flex justify-content-between">
                                                <span>Allowed File Extensions</span>
                                                <span class="text-muted micro-text">Comma separated</span>
                                            </label>
                                            <input type="text" id="propAllowedExtensions" class="txn-search-input" placeholder="pdf, doc, docx, png, jpg">
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
                                        <label class="form-label text-white small fw-semibold">Description</label>
                                        <textarea id="propHelpText" class="txn-search-input" rows="3" placeholder="Field help text or instructions..."></textarea>
                                    </div>

                                    <div class="d-flex align-items-center justify-content-between p-3 rounded-3 mt-3" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08);">
                                        <label class="form-label text-white mb-0 fw-semibold small">Required Field</label>
                                        <label class="toggle-switch" for="propRequired">
                                            <input type="checkbox" id="propRequired" class="toggle-switch-input">
                                            <span class="toggle-switch-slider"></span>
                                        </label>
                                    </div>
                                </div>

                                <div id="subTabContentAdvanced" style="display: none;">
                                    <div class="mb-3" id="placeholderGroup">
                                        <label class="form-label text-white small fw-semibold">Placeholder</label>
                                        <input type="text" id="propPlaceholder" class="txn-search-input" placeholder="Enter placeholder...">
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

                                    <div class="mb-3">
                                        <label class="form-label text-white small fw-semibold">Field Key (ID)</label>
                                        <input type="text" id="propName" class="txn-search-input" placeholder="e.g. field_key">
                                    </div>
                                </div>

                                <div id="subTabContentLogic" style="display: none;">
                                    <div class="p-3 rounded-3 text-muted micro-text" style="background: rgba(15,23,42,0.6); border: 1px solid rgba(255,255,255,0.08);">
                                        <i class="bx bx-bulb text-warning fs-5 d-block mb-1"></i>
                                        Smart logic allow you to show/hide this field dynamically based on answers to previous fields.
                                    </div>
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

                        <div class="mt-4 pt-3 border-top border-secondary border-opacity-10 text-start" id="canvasSubmitPreviewRow" style="display: none;">
                            <button type="button" class="btn btn-secondary px-4 py-2" disabled style="border-radius: 8px;">
                                Submit Form
                            </button>
                        </div>
                    </div>
                </div>

            </div>

            <!-- MODE 2: SETTINGS VIEW (Matching Reference Images 1, 2 & 3) -->
            <div id="builderModeSettingsView" class="row g-4" style="display: none;">
                
                <!-- Left Secondary Settings Navigation Menu (3.5 Cols) -->
                <div class="col-lg-3.5 col-xl-3">
                    <div class="forms-card">
                        <div class="fw-bold text-white small text-uppercase mb-3" style="letter-spacing:0.06em;">Form Settings</div>
                        <div class="settings-menu-list">
                            <a class="settings-menu-item active" id="setMenuGeneral" data-target="setSecGeneral">
                                <span><i class="bx bx-cog me-2"></i> General</span>
                                <i class="bx bx-chevron-right micro-text"></i>
                            </a>
                            <a class="settings-menu-item" id="setMenuSpam" data-target="setSecSpam">
                                <span><i class="bx bx-shield-quarter me-2"></i> Spam Protection & Security</span>
                                <i class="bx bx-chevron-right micro-text"></i>
                            </a>
                            <a class="settings-menu-item" id="setMenuConfirmations" data-target="setSecConfirmations">
                                <span><i class="bx bx-check-circle me-2"></i> Confirmations</span>
                                <i class="bx bx-chevron-right micro-text"></i>
                            </a>
                            <a class="settings-menu-item" id="setMenuNotifications" data-target="setSecNotifications">
                                <span><i class="bx bx-bell me-2"></i> Notifications</span>
                                <i class="bx bx-chevron-right micro-text"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Right Workspace Area for Settings (8.5 Cols) -->
                <div class="col-lg-8.5 col-xl-9">
                    
                    <!-- SECTION 1: GENERAL SETTINGS -->
                    <div class="settings-section-card" id="setSecGeneral">
                        <h5 class="text-white fw-bold mb-4 pb-2 border-bottom border-secondary border-opacity-25">
                            <i class="bx bx-slider me-2 text-primary"></i>General Form Settings
                        </h5>
                        
                        <div class="mb-4">
                            <label class="form-label text-white fw-semibold small">Target Clubs / Website Access</label>
                            <select name="website_ids[]" id="settingWebsiteIds" class="txn-filter-select w-100" multiple style="height: 140px;">
                                @foreach($websites as $web)
                                    <option value="{{ $web->id }}" {{ isset($form) && is_array($form->website_ids) && in_array($web->id, $form->website_ids) ? 'selected' : '' }}>
                                        {{ $web->name }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="form-text text-muted micro-text mt-1.5 d-block">Hold Ctrl/Cmd to select target venues. Leave empty to allow access across all clubs.</span>
                        </div>
                    </div>

                    <!-- SECTION 2: SPAM PROTECTION AND SECURITY (Matching Image 2) -->
                    <div class="settings-section-card" id="setSecSpam" style="display: none;">
                        <h5 class="text-white fw-bold mb-2">Spam Protection and Security</h5>
                        <p class="text-muted small mb-4">Behind-the-scenes spam filtering that's invisible to your visitors.</p>

                        <!-- Protection Section -->
                        <div class="mb-4 pb-3 border-bottom border-secondary border-opacity-10">
                            <div class="fw-semibold text-white small mb-3 text-uppercase" style="letter-spacing:0.05em">Protection</div>

                            <div class="d-flex align-items-center justify-content-between mb-3 p-3 rounded-3" style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.07);">
                                <div>
                                    <div class="text-white fw-semibold small">Enable modern anti-spam protection</div>
                                    <div class="text-muted micro-text">Automated invisible bot detection.</div>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="settingEnableModernSpam" class="toggle-switch-input" {{ isset($initialSettings['spam']['enable_modern_spam']) && $initialSettings['spam']['enable_modern_spam'] ? 'checked' : '' }}>
                                    <span class="toggle-switch-slider"></span>
                                </label>
                            </div>

                            <div class="d-flex align-items-center justify-content-between mb-3 p-3 rounded-3" style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.07);">
                                <div>
                                    <div class="text-white fw-semibold small">Enable anti-spam protection</div>
                                    <div class="text-muted micro-text">Honeypot form validation for automated bots.</div>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="settingEnableAntispam" class="toggle-switch-input" {{ !isset($initialSettings['spam']['enable_antispam']) || $initialSettings['spam']['enable_antispam'] ? 'checked' : '' }}>
                                    <span class="toggle-switch-slider"></span>
                                </label>
                            </div>

                            <div class="d-flex align-items-center justify-content-between mb-3 p-3 rounded-3" style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.07);">
                                <div>
                                    <div class="text-white fw-semibold small">Store spam entries in the database</div>
                                    <div class="text-muted micro-text">Keep flag history for audit logs.</div>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="settingStoreSpam" class="toggle-switch-input" {{ isset($initialSettings['spam']['store_spam']) && $initialSettings['spam']['store_spam'] ? 'checked' : '' }}>
                                    <span class="toggle-switch-slider"></span>
                                </label>
                            </div>

                            <div class="p-3 rounded-3 mb-3" style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.07);">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div>
                                        <div class="text-white fw-semibold small">Enable minimum time to submit</div>
                                        <div class="text-muted micro-text">Reject rapid automated form submissions.</div>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox" id="settingMinTime" class="toggle-switch-input" {{ isset($initialSettings['spam']['min_time']) && $initialSettings['spam']['min_time'] ? 'checked' : '' }}>
                                        <span class="toggle-switch-slider"></span>
                                    </label>
                                </div>
                                <div class="mt-2">
                                    <label class="form-label text-white micro-text fw-semibold mb-1">Minimum Submission Time (Seconds)</label>
                                    <input type="number" id="settingMinTimeSeconds" class="txn-search-input" min="1" max="60" placeholder="3" value="{{ $initialSettings['spam']['min_time_seconds'] ?? 3 }}">
                                </div>
                            </div>
                        </div>

                        <!-- Filtering Section -->
                        <div>
                            <div class="fw-semibold text-white small mb-3 text-uppercase" style="letter-spacing:0.05em">Filtering</div>

                            <div class="p-3 rounded-3 mb-3" style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.07);">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div>
                                        <div class="text-white fw-semibold small">Enable country filter</div>
                                        <div class="text-muted micro-text">Restrict entries based on visitor country.</div>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox" id="settingCountryFilter" class="toggle-switch-input" {{ isset($initialSettings['spam']['country_filter']) && $initialSettings['spam']['country_filter'] ? 'checked' : '' }}>
                                        <span class="toggle-switch-slider"></span>
                                    </label>
                                </div>
                                <div class="mt-3">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <label class="form-label text-white micro-text fw-semibold mb-0">Select Restricted / Blocked Countries</label>
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-link text-primary micro-text p-0 text-decoration-none fw-semibold" id="btnSelectAllCountries">Select All</button>
                                            <span class="text-muted micro-text">|</span>
                                            <button type="button" class="btn btn-link text-muted micro-text p-0 text-decoration-none" id="btnClearAllCountries">Clear All</button>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <input type="text" id="countrySearchInput" class="txn-search-input py-1.5 px-3 micro-text" placeholder="Search country name or ISO code...">
                                    </div>

                                    <div class="country-checkbox-grid p-3 rounded-3" style="max-height: 240px; overflow-y: auto; background: rgba(15,23,42,0.8); border: 1px solid rgba(255,255,255,0.1);">
                                        <div class="row g-2" id="countryCheckboxContainer">
                                            <!-- Dynamically rendered country checkboxes with flags -->
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="p-3 rounded-3" style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.07);">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div>
                                        <div class="text-white fw-semibold small">Enable keyword filter</div>
                                        <div class="text-muted micro-text">Block blacklisted words and links.</div>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox" id="settingKeywordFilter" class="toggle-switch-input" {{ isset($initialSettings['spam']['keyword_filter']) && $initialSettings['spam']['keyword_filter'] ? 'checked' : '' }}>
                                        <span class="toggle-switch-slider"></span>
                                    </label>
                                </div>
                                <div class="mt-2">
                                    <label class="form-label text-white micro-text fw-semibold mb-1">Restricted Keywords / Blacklisted Words (comma or line separated)</label>
                                    <textarea id="settingRestrictedKeywords" class="txn-search-input" rows="3" placeholder="e.g. casino, viagra, crypto, http://, https://">{{ $initialSettings['spam']['restricted_keywords'] ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 3: CONFIRMATIONS (Matching Image 1) -->
                    <div class="settings-section-card" id="setSecConfirmations" style="display: none;">
                        <h5 class="text-white fw-bold mb-4 pb-2 border-bottom border-secondary border-opacity-25">Confirmations</h5>
                        
                        <div class="p-4 rounded-3 mb-4" style="background: rgba(15,23,42,0.8); border: 1px solid rgba(255,255,255,0.1);">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fw-bold text-white fs-6">Default Confirmation</span>
                                <i class="bx bx-pencil text-muted cursor-pointer"></i>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-white small fw-semibold">Confirmation Type</label>
                                <select id="settingConfirmationType" class="txn-filter-select w-100">
                                    <option value="message" {{ !isset($initialSettings['confirmation']['type']) || $initialSettings['confirmation']['type'] === 'message' ? 'selected' : '' }}>Message</option>
                                    <option value="redirect" {{ isset($initialSettings['confirmation']['type']) && $initialSettings['confirmation']['type'] === 'redirect' ? 'selected' : '' }}>Redirect to URL</option>
                                </select>
                            </div>

                            <div class="mb-4" id="confirmationMessageGroup">
                                <label class="form-label text-white small fw-semibold">Confirmation Message</label>
                                <textarea id="settingConfirmationMessage" class="txn-search-input" rows="4" placeholder="Thanks for contacting us! We will be in touch with you shortly.">{{ $initialSettings['confirmation']['message'] ?? 'Thanks for contacting us! We will be in touch with you shortly.' }}</textarea>
                            </div>

                            <div class="mb-4" id="confirmationRedirectGroup" style="display: none;">
                                <label class="form-label text-white small fw-semibold">Redirect URL</label>
                                <input type="url" id="settingConfirmationRedirectUrl" class="txn-search-input" placeholder="https://example.com/thank-you" value="{{ $initialSettings['confirmation']['redirect_url'] ?? '' }}">
                            </div>

                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="text-white small fw-medium">Automatically scroll to the confirmation message</span>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="settingConfirmationAutoScroll" class="toggle-switch-input" {{ !isset($initialSettings['confirmation']['auto_scroll']) || $initialSettings['confirmation']['auto_scroll'] ? 'checked' : '' }}>
                                    <span class="toggle-switch-slider"></span>
                                </label>
                            </div>

                            <div class="d-flex align-items-center justify-content-between">
                                <span class="text-white small fw-medium">Show entry preview after confirmation message</span>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="settingConfirmationShowPreview" class="toggle-switch-input" {{ isset($initialSettings['confirmation']['show_preview']) && $initialSettings['confirmation']['show_preview'] ? 'checked' : '' }}>
                                    <span class="toggle-switch-slider"></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 4: NOTIFICATIONS (Matching Image 3) -->
                    <div class="settings-section-card" id="setSecNotifications" style="display: none;">
                        <h5 class="text-white fw-bold mb-2">Notifications</h5>
                        <p class="text-muted small mb-4">Notifications are emails sent when a form is submitted. By default, these emails include entry details.</p>

                        <div class="d-flex align-items-center justify-content-between mb-4 p-3 rounded-3" style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.07);">
                            <span class="text-white fw-semibold small">Enable Notifications</span>
                            <label class="toggle-switch">
                                <input type="checkbox" id="settingNotifyEnabled" class="toggle-switch-input" {{ !isset($initialSettings['notifications']['enabled']) || $initialSettings['notifications']['enabled'] ? 'checked' : '' }}>
                                <span class="toggle-switch-slider"></span>
                            </label>
                        </div>

                        <div class="p-4 rounded-3" style="background: rgba(15,23,42,0.8); border: 1px solid rgba(255,255,255,0.1);">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fw-bold text-white fs-6">Default Notification</span>
                                <i class="bx bx-pencil text-muted cursor-pointer"></i>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-white small fw-semibold">Send To Email Address</label>
                                <input type="text" id="settingNotifySendTo" class="txn-search-input" placeholder="admin@cartvip.com" value="{{ $initialSettings['notifications']['send_to'] ?? 'admin@cartvip.com' }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-white small fw-semibold">Email Subject Line</label>
                                <input type="text" id="settingNotifySubject" class="txn-search-input" placeholder="New Submission: {form_title}" value="{{ $initialSettings['notifications']['subject'] ?? 'New Form Submission' }}">
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label text-white small fw-semibold">From Name</label>
                                    <input type="text" id="settingNotifyFromName" class="txn-search-input" placeholder="CartVIP Forms" value="{{ $initialSettings['notifications']['from_name'] ?? 'CartVIP Forms' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-white small fw-semibold">From Email</label>
                                    <input type="email" id="settingNotifyFromEmail" class="txn-search-input" placeholder="no-reply@cartvip.com" value="{{ $initialSettings['notifications']['from_email'] ?? 'no-reply@cartvip.com' }}">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-white small fw-semibold">Reply-To Email</label>
                                <input type="text" id="settingNotifyReplyTo" class="txn-search-input" placeholder="{field_email}" value="{{ $initialSettings['notifications']['reply_to'] ?? '' }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-white small fw-semibold">Email Message</label>
                                <div class="mb-2">
                                    <span class="badge bg-primary bg-opacity-25 text-primary border border-primary border-opacity-25 px-2.5 py-1 font-monospace">{all_fields}</span>
                                </div>
                                <textarea id="settingNotifyMessage" class="txn-search-input" rows="4" placeholder="{all_fields}">{{ $initialSettings['notifications']['message'] ?? '{all_fields}' }}</textarea>
                                <span class="form-text text-muted micro-text mt-1 d-block">To display all form fields, use the <code>{all_fields}</code> Smart Tag.</span>
                            </div>

                            <div class="d-flex align-items-center justify-content-between pt-3 border-top border-secondary border-opacity-10">
                                <span class="text-white small fw-medium">Enable Conditional Logic</span>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="settingNotifyConditional" class="toggle-switch-input" {{ isset($initialSettings['notifications']['conditional']) && $initialSettings['notifications']['conditional'] ? 'checked' : '' }}>
                                    <span class="toggle-switch-slider"></span>
                                </label>
                            </div>
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
    const canvasSubmitPreviewRow = document.getElementById('canvasSubmitPreviewRow');

    // Primary Builder Mode Switcher (Fields vs Settings View)
    const modeFieldsBtn = document.getElementById('modeFieldsBtn');
    const modeSettingsBtn = document.getElementById('modeSettingsBtn');
    const builderModeFieldsView = document.getElementById('builderModeFieldsView');
    const builderModeSettingsView = document.getElementById('builderModeSettingsView');

    modeFieldsBtn.addEventListener('click', () => {
        modeFieldsBtn.classList.add('active');
        modeSettingsBtn.classList.remove('active');
        builderModeFieldsView.style.display = 'flex';
        builderModeSettingsView.style.display = 'none';
    });

    modeSettingsBtn.addEventListener('click', () => {
        modeSettingsBtn.classList.add('active');
        modeFieldsBtn.classList.remove('active');
        builderModeFieldsView.style.display = 'none';
        builderModeSettingsView.style.display = 'flex';
    });

    // Settings Secondary Sub-Menu Switching (General, Spam, Confirmations, Notifications)
    const settingsMenuItems = document.querySelectorAll('.settings-menu-item');
    settingsMenuItems.forEach(item => {
        item.addEventListener('click', () => {
            settingsMenuItems.forEach(i => i.classList.remove('active'));
            item.classList.add('active');

            const targetId = item.getAttribute('data-target');
            document.querySelectorAll('.settings-section-card').forEach(card => {
                card.style.display = 'none';
            });
            const activeCard = document.getElementById(targetId);
            if (activeCard) activeCard.style.display = 'block';
        });
    });

    // Confirmation Type Toggle (Message vs Redirect URL)
    const settingConfirmationType = document.getElementById('settingConfirmationType');
    const confirmationMessageGroup = document.getElementById('confirmationMessageGroup');
    const confirmationRedirectGroup = document.getElementById('confirmationRedirectGroup');

    if (settingConfirmationType) {
        settingConfirmationType.addEventListener('change', (e) => {
            if (e.target.value === 'redirect') {
                confirmationMessageGroup.style.display = 'none';
                confirmationRedirectGroup.style.display = 'block';
            } else {
                confirmationMessageGroup.style.display = 'block';
                confirmationRedirectGroup.style.display = 'none';
            }
        });
    }

    // Left Panel Tabs inside Fields view (Add Fields vs Field Options)
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

    // Inspector Sub-Tabs (General / Advanced / Smart Logic)
    const subTabGeneral = document.getElementById('subTabGeneral');
    const subTabAdvanced = document.getElementById('subTabAdvanced');
    const subTabLogic = document.getElementById('subTabLogic');
    const subTabContentGeneral = document.getElementById('subTabContentGeneral');
    const subTabContentAdvanced = document.getElementById('subTabContentAdvanced');
    const subTabContentLogic = document.getElementById('subTabContentLogic');

    function switchInspectorSubTab(subTabName) {
        subTabGeneral.classList.remove('active');
        subTabAdvanced.classList.remove('active');
        subTabLogic.classList.remove('active');
        subTabContentGeneral.style.display = 'none';
        subTabContentAdvanced.style.display = 'none';
        subTabContentLogic.style.display = 'none';

        if (subTabName === 'advanced') {
            subTabAdvanced.classList.add('active');
            subTabContentAdvanced.style.display = 'block';
        } else if (subTabName === 'logic') {
            subTabLogic.classList.add('active');
            subTabContentLogic.style.display = 'block';
        } else {
            subTabGeneral.classList.add('active');
            subTabContentGeneral.style.display = 'block';
        }
    }

    subTabGeneral.addEventListener('click', () => switchInspectorSubTab('general'));
    subTabAdvanced.addEventListener('click', () => switchInspectorSubTab('advanced'));
    subTabLogic.addEventListener('click', () => switchInspectorSubTab('logic'));

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
        if (type === 'name') defaultLabel = 'Name';
        if (type === 'phone') defaultLabel = 'Phone';
        if (type === 'heading') defaultLabel = 'Section Heading';
        if (type === 'checkbox') defaultLabel = 'I agree to the terms and conditions';
        if (type === 'file') defaultLabel = 'Resume required for server and/or bartender';
        if (type === 'captcha') defaultLabel = 'Captcha (To prevent spam)';

        const newField = {
            id: id,
            type: type,
            label: defaultLabel,
            name: id,
            placeholder: '',
            help_text: '',
            format: type === 'name' ? 'first_last' : (type === 'phone' ? 'smart' : ''),
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
            canvasSubmitPreviewRow.style.display = 'none';
            return;
        }

        emptyNotice.style.display = 'none';
        fieldCountBadge.innerText = fields.length + ' Fields';
        canvasSubmitPreviewRow.style.display = 'block';

        fields.forEach((f, idx) => {
            const wrapper = document.createElement('div');
            wrapper.className = (f.width_class || 'col-12') + ' field-card-wrapper';
            wrapper.setAttribute('data-idx', idx);

            const card = document.createElement('div');
            card.className = 'field-card' + (selectedFieldIndex === idx ? ' selected' : '');
            card.setAttribute('draggable', 'true');

            let inputPreview = '';
            
            if (f.type === 'name') {
                const format = f.format || 'first_last';
                if (format === 'first_middle_last') {
                    inputPreview = `
                        <div class="row g-2">
                            <div class="col-4">
                                <input type="text" class="txn-search-input" placeholder="First Name" disabled>
                                <div class="text-muted micro-text mt-1">First</div>
                            </div>
                            <div class="col-4">
                                <input type="text" class="txn-search-input" placeholder="Middle Name" disabled>
                                <div class="text-muted micro-text mt-1">Middle</div>
                            </div>
                            <div class="col-4">
                                <input type="text" class="txn-search-input" placeholder="Last Name" disabled>
                                <div class="text-muted micro-text mt-1">Last</div>
                            </div>
                        </div>
                    `;
                } else if (format === 'simple') {
                    inputPreview = `<input type="text" class="txn-search-input" placeholder="Full Name" disabled>`;
                } else {
                    inputPreview = `
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="text" class="txn-search-input" placeholder="" disabled>
                                <div class="text-muted micro-text mt-1">First</div>
                            </div>
                            <div class="col-6">
                                <input type="text" class="txn-search-input" placeholder="" disabled>
                                <div class="text-muted micro-text mt-1">Last</div>
                            </div>
                        </div>
                    `;
                }
            } else if (f.type === 'phone') {
                inputPreview = `
                    <div class="input-group">
                        <button class="btn btn-outline-secondary dropdown-toggle text-white border-secondary bg-dark" type="button" disabled>🇺🇸 ▾</button>
                        <input type="tel" class="txn-search-input" placeholder="Phone Number" disabled>
                    </div>
                `;
            } else if (f.type === 'captcha') {
                inputPreview = `
                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-bold text-white fs-5 font-monospace" style="letter-spacing:2px;">14 + 14 =</span>
                        <input type="text" class="txn-search-input" style="width: 120px;" placeholder="" disabled>
                    </div>
                `;
            } else if (f.type === 'textarea') {
                inputPreview = `<textarea class="txn-search-input" placeholder="${f.placeholder || ''}" rows="3" disabled></textarea>`;
            } else if (f.type === 'select') {
                inputPreview = `<select class="txn-filter-select w-100" disabled><option>${f.options ? f.options[0] : 'Select option...'}</option></select>`;
            } else if (f.type === 'radio') {
                const choices = f.options || ['Choice 1', 'Choice 2'];
                inputPreview = `
                    <div class="mt-1 d-flex flex-wrap gap-3">
                        ${choices.map(c => `
                            <div class="form-check me-2">
                                <input class="form-check-input" type="radio" disabled>
                                <label class="form-check-label text-white small">${c}</label>
                            </div>
                        `).join('')}
                    </div>
                `;
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
                inputPreview = `
                    <div class="input-group d-flex align-items-center">
                        <span class="input-group-text bg-dark text-secondary border-secondary border-end-0"><i class="bx bx-time text-primary fs-5"></i></span>
                        <input type="text" class="txn-search-input" style="border-top-left-radius:0; border-bottom-left-radius:0;" placeholder="Select Time (e.g. 10:30 PM)" disabled>
                    </div>
                `;
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
                    <button type="button" class="field-action-icon-btn duplicate-field-btn" data-idx="${idx}" title="Duplicate Field">
                        <i class="bx bx-copy"></i>
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

        document.querySelectorAll('.duplicate-field-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const idx = parseInt(btn.getAttribute('data-idx'));
                const cloned = JSON.parse(JSON.stringify(fields[idx]));
                cloned.id = 'field_' + Math.random().toString(36).substr(2, 9);
                cloned.name = cloned.id;
                cloned.label = cloned.label + ' (Copy)';
                fields.splice(idx + 1, 0, cloned);
                selectField(idx + 1);
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
        switchInspectorSubTab('general');
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

        document.getElementById('inspectorFieldTypeTitle').innerText = ucfirst(f.type) + ' (ID #' + (selectedFieldIndex + 1) + ')';
        document.getElementById('propLabel').value = f.label || '';
        document.getElementById('propName').value = f.name || '';
        document.getElementById('propPlaceholder').value = f.placeholder || '';
        document.getElementById('propHelpText').value = f.help_text || '';
        document.getElementById('propWidth').value = f.width_class || 'col-12';
        document.getElementById('propRequired').checked = !!f.required;

        const nameFormatGroup = document.getElementById('nameFormatGroup');
        if (f.type === 'name') {
            nameFormatGroup.style.display = 'block';
            document.getElementById('propNameFormat').value = f.format || 'first_last';
        } else {
            nameFormatGroup.style.display = 'none';
        }

        const phoneFormatGroup = document.getElementById('phoneFormatGroup');
        if (f.type === 'phone') {
            phoneFormatGroup.style.display = 'block';
            document.getElementById('propPhoneFormat').value = f.format || 'smart';
        } else {
            phoneFormatGroup.style.display = 'none';
        }

        const choicesManagerGroup = document.getElementById('choicesManagerGroup');
        if (['select', 'radio'].includes(f.type)) {
            choicesManagerGroup.style.display = 'block';
            renderChoicesManager(f.options || ['Option 1', 'Option 2']);
        } else {
            choicesManagerGroup.style.display = 'none';
        }

        const fileUploadPropertiesGroup = document.getElementById('fileUploadPropertiesGroup');
        if (f.type === 'file') {
            fileUploadPropertiesGroup.style.display = 'block';
            document.getElementById('propAllowedExtensions').value = f.allowed_extensions || 'pdf, doc, docx, png, jpg';
            document.getElementById('propMaxFileSize').value = f.max_file_size || 5;
            document.getElementById('propMaxFileUploads').value = f.max_file_uploads || 1;
        } else {
            fileUploadPropertiesGroup.style.display = 'none';
        }

        const placeholderGroup = document.getElementById('placeholderGroup');
        if (['checkbox', 'heading', 'file', 'captcha'].includes(f.type)) {
            placeholderGroup.style.display = 'none';
        } else {
            placeholderGroup.style.display = 'block';
        }
    }

    function renderChoicesManager(optionsArr) {
        const container = document.getElementById('choicesListContainer');
        container.innerHTML = '';
        optionsArr.forEach((opt, oIdx) => {
            const row = document.createElement('div');
            row.className = 'choice-item-row';
            row.innerHTML = `
                <span class="text-muted small me-1">=</span>
                <input type="radio" name="default_choice" ${oIdx === 0 ? 'checked' : ''} class="form-check-input mt-0 me-1">
                <input type="text" class="txn-search-input choice-text-input" value="${opt}" data-oidx="${oIdx}">
                <button type="button" class="btn-choice-icon btn-choice-add add-choice-btn" data-oidx="${oIdx}">+</button>
                <button type="button" class="btn-choice-icon btn-choice-remove remove-choice-btn" data-oidx="${oIdx}" ${optionsArr.length <= 1 ? 'disabled' : ''}>-</button>
            `;
            container.appendChild(row);
        });

        container.querySelectorAll('.choice-text-input').forEach(inp => {
            inp.addEventListener('input', (e) => {
                const oidx = parseInt(inp.getAttribute('data-oidx'));
                if (selectedFieldIndex !== null && fields[selectedFieldIndex]) {
                    fields[selectedFieldIndex].options[oidx] = e.target.value;
                    renderCanvas();
                }
            });
        });

        container.querySelectorAll('.add-choice-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const oidx = parseInt(btn.getAttribute('data-oidx'));
                if (selectedFieldIndex !== null && fields[selectedFieldIndex]) {
                    fields[selectedFieldIndex].options.splice(oidx + 1, 0, 'New Choice');
                    renderCanvas();
                    updateInspector();
                }
            });
        });

        container.querySelectorAll('.remove-choice-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const oidx = parseInt(btn.getAttribute('data-oidx'));
                if (selectedFieldIndex !== null && fields[selectedFieldIndex] && fields[selectedFieldIndex].options.length > 1) {
                    fields[selectedFieldIndex].options.splice(oidx, 1);
                    renderCanvas();
                    updateInspector();
                }
            });
        });
    }

    const btnToggleBulkChoices = document.getElementById('btnToggleBulkChoices');
    const choicesListContainer = document.getElementById('choicesListContainer');
    const bulkChoicesContainer = document.getElementById('bulkChoicesContainer');
    let isBulkChoicesMode = false;

    if (btnToggleBulkChoices) {
        btnToggleBulkChoices.addEventListener('click', () => {
            isBulkChoicesMode = !isBulkChoicesMode;
            if (isBulkChoicesMode) {
                choicesListContainer.style.display = 'none';
                bulkChoicesContainer.style.display = 'block';
                document.getElementById('propOptionsBulk').value = (fields[selectedFieldIndex]?.options || []).join('\n');
                btnToggleBulkChoices.innerText = 'Row View';
            } else {
                bulkChoicesContainer.style.display = 'none';
                choicesListContainer.style.display = 'block';
                btnToggleBulkChoices.innerText = 'Bulk Edit';
            }
        });
    }

    document.getElementById('propOptionsBulk')?.addEventListener('input', (e) => {
        if (selectedFieldIndex !== null && fields[selectedFieldIndex]) {
            fields[selectedFieldIndex].options = e.target.value.split('\n').map(s => s.trim()).filter(Boolean);
            renderCanvas();
        }
    });

    // Inspector Events
    document.getElementById('propLabel').addEventListener('input', (e) => {
        if (selectedFieldIndex !== null) {
            fields[selectedFieldIndex].label = e.target.value;
            renderCanvas();
        }
    });
    document.getElementById('propNameFormat')?.addEventListener('change', (e) => {
        if (selectedFieldIndex !== null) {
            fields[selectedFieldIndex].format = e.target.value;
            renderCanvas();
        }
    });
    document.getElementById('propPhoneFormat')?.addEventListener('change', (e) => {
        if (selectedFieldIndex !== null) {
            fields[selectedFieldIndex].format = e.target.value;
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

    // Interactive Country Selector Checkboxes with Names & Flag Emojis
    const countriesData = [
        { code: 'US', name: 'United States', flag: '🇺🇸' },
        { code: 'CA', name: 'Canada', flag: '🇨🇦' },
        { code: 'GB', name: 'United Kingdom', flag: '🇬🇧' },
        { code: 'AU', name: 'Australia', flag: '🇦🇺' },
        { code: 'DE', name: 'Germany', flag: '🇩🇪' },
        { code: 'FR', name: 'France', flag: '🇫🇷' },
        { code: 'IN', name: 'India', flag: '🇮🇳' },
        { code: 'RU', name: 'Russia', flag: '🇷🇺' },
        { code: 'CN', name: 'China', flag: '🇨🇳' },
        { code: 'BR', name: 'Brazil', flag: '🇧🇷' },
        { code: 'MX', name: 'Mexico', flag: '🇲🇽' },
        { code: 'IT', name: 'Italy', flag: '🇮🇹' },
        { code: 'ES', name: 'Spain', flag: '🇪🇸' },
        { code: 'JP', name: 'Japan', flag: '🇯🇵' },
        { code: 'KR', name: 'South Korea', flag: '🇰🇷' },
        { code: 'NL', name: 'Netherlands', flag: '🇳🇱' },
        { code: 'SE', name: 'Sweden', flag: '🇸🇪' },
        { code: 'NO', name: 'Norway', flag: '🇳🇴' },
        { code: 'CH', name: 'Switzerland', flag: '🇨🇭' },
        { code: 'AT', name: 'Austria', flag: '🇦🇹' },
        { code: 'BE', name: 'Belgium', flag: '🇧🇪' },
        { code: 'DK', name: 'Denmark', flag: '🇩🇰' },
        { code: 'FI', name: 'Finland', flag: '🇫🇮' },
        { code: 'IE', name: 'Ireland', flag: '🇮🇪' },
        { code: 'PL', name: 'Poland', flag: '🇵🇱' },
        { code: 'PT', name: 'Portugal', flag: '🇵🇹' },
        { code: 'UA', name: 'Ukraine', flag: '🇺🇦' },
        { code: 'ZA', name: 'South Africa', flag: '🇿🇦' },
        { code: 'AE', name: 'United Arab Emirates', flag: '🇦🇪' },
        { code: 'SA', name: 'Saudi Arabia', flag: '🇸🇦' },
        { code: 'SG', name: 'Singapore', flag: '🇸🇬' },
        { code: 'NZ', name: 'New Zealand', flag: '🇳🇿' },
        { code: 'IR', name: 'Iran', flag: '🇮🇷' },
        { code: 'KP', name: 'North Korea', flag: '🇰🇵' },
        { code: 'PK', name: 'Pakistan', flag: '🇵🇰' },
        { code: 'NG', name: 'Nigeria', flag: '🇳🇬' },
        { code: 'EG', name: 'Egypt', flag: '🇪🇬' },
        { code: 'VN', name: 'Vietnam', flag: '🇻🇳' },
        { code: 'TH', name: 'Thailand', flag: '🇹🇭' },
        { code: 'ID', name: 'Indonesia', flag: '🇮🇩' },
        { code: 'PH', name: 'Philippines', flag: '🇵🇭' },
        { code: 'MY', name: 'Malaysia', flag: '🇲🇾' },
        { code: 'AR', name: 'Argentina', flag: '🇦🇷' },
        { code: 'CL', name: 'Chile', flag: '🇨🇱' },
        { code: 'CO', name: 'Colombia', flag: '🇨🇴' },
        { code: 'TR', name: 'Turkey', flag: '🇹🇷' },
        { code: 'IL', name: 'Israel', flag: '🇮🇱' }
    ];

    let selectedCountries = [];
    const rawSavedCountries = {!! json_encode($initialSettings['spam']['restricted_countries'] ?? '') !!};
    if (typeof rawSavedCountries === 'string' && rawSavedCountries.trim() !== '') {
        selectedCountries = rawSavedCountries.split(',').map(s => s.trim().toUpperCase());
    } else if (Array.isArray(rawSavedCountries)) {
        selectedCountries = rawSavedCountries.map(s => String(s).trim().toUpperCase());
    }

    function renderCountryCheckboxes(filterText = '') {
        const container = document.getElementById('countryCheckboxContainer');
        if (!container) return;
        container.innerHTML = '';

        const query = filterText.toLowerCase().trim();
        const filtered = countriesData.filter(c => 
            c.name.toLowerCase().includes(query) || c.code.toLowerCase().includes(query)
        );

        if (filtered.length === 0) {
            container.innerHTML = '<div class="text-muted micro-text py-2 text-center col-12">No countries match search.</div>';
            return;
        }

        filtered.forEach(c => {
            const isChecked = selectedCountries.includes(c.code);
            const col = document.createElement('div');
            col.className = 'col-6 col-md-4';
            col.innerHTML = `
                <div class="form-check d-flex align-items-center gap-1.5 py-1 px-2 rounded" style="cursor:pointer; background: rgba(255,255,255,0.03);">
                    <input class="form-check-input country-checkbox mt-0 cursor-pointer" type="checkbox" value="${c.code}" id="country_cb_${c.code}" ${isChecked ? 'checked' : ''}>
                    <label class="form-check-label text-white micro-text cursor-pointer text-truncate mb-0 ms-1" for="country_cb_${c.code}">
                        <span>${c.flag}</span> ${c.name} <span class="text-muted">(${c.code})</span>
                    </label>
                </div>
            `;
            container.appendChild(col);
        });

        container.querySelectorAll('.country-checkbox').forEach(cb => {
            cb.addEventListener('change', () => {
                const val = cb.value;
                if (cb.checked) {
                    if (!selectedCountries.includes(val)) selectedCountries.push(val);
                } else {
                    selectedCountries = selectedCountries.filter(x => x !== val);
                }
            });
        });
    }

    document.getElementById('countrySearchInput')?.addEventListener('input', (e) => {
        renderCountryCheckboxes(e.target.value);
    });

    document.getElementById('btnSelectAllCountries')?.addEventListener('click', () => {
        selectedCountries = countriesData.map(c => c.code);
        renderCountryCheckboxes(document.getElementById('countrySearchInput')?.value || '');
    });

    document.getElementById('btnClearAllCountries')?.addEventListener('click', () => {
        selectedCountries = [];
        renderCountryCheckboxes(document.getElementById('countrySearchInput')?.value || '');
    });

    renderCountryCheckboxes();

    // Serialize Form & Full Settings Object on Save
    document.getElementById('builderForm').addEventListener('submit', function(e) {
        document.getElementById('fieldsSchemaInput').value = JSON.stringify(fields);

        const settingsPayload = {
            general: {
                title: document.getElementById('formTitleInput')?.value || '',
                description: document.getElementById('formDescInput')?.value || '',
                website_ids: Array.from(document.querySelectorAll('#settingWebsiteIds option:checked')).map(o => o.value)
            },
            spam: {
                enable_modern_spam: document.getElementById('settingEnableModernSpam')?.checked ?? false,
                enable_antispam: document.getElementById('settingEnableAntispam')?.checked ?? true,
                store_spam: document.getElementById('settingStoreSpam')?.checked ?? false,
                min_time: document.getElementById('settingMinTime')?.checked ?? false,
                min_time_seconds: document.getElementById('settingMinTimeSeconds')?.value || 3,
                country_filter: document.getElementById('settingCountryFilter')?.checked ?? false,
                restricted_countries: selectedCountries.join(','),
                keyword_filter: document.getElementById('settingKeywordFilter')?.checked ?? false,
                restricted_keywords: document.getElementById('settingRestrictedKeywords')?.value || ''
            },
            confirmation: {
                type: document.getElementById('settingConfirmationType')?.value || 'message',
                message: document.getElementById('settingConfirmationMessage')?.value || 'Thanks for contacting us! We will be in touch with you shortly.',
                redirect_url: document.getElementById('settingConfirmationRedirectUrl')?.value || '',
                auto_scroll: document.getElementById('settingConfirmationAutoScroll')?.checked ?? true,
                show_preview: document.getElementById('settingConfirmationShowPreview')?.checked ?? false
            },
            notifications: {
                enabled: document.getElementById('settingNotifyEnabled')?.checked ?? true,
                send_to: document.getElementById('settingNotifySendTo')?.value || 'admin@cartvip.com',
                subject: document.getElementById('settingNotifySubject')?.value || 'New Form Submission',
                from_name: document.getElementById('settingNotifyFromName')?.value || 'CartVIP Forms',
                from_email: document.getElementById('settingNotifyFromEmail')?.value || 'no-reply@cartvip.com',
                reply_to: document.getElementById('settingNotifyReplyTo')?.value || '',
                message: document.getElementById('settingNotifyMessage')?.value || '{all_fields}',
                conditional: document.getElementById('settingNotifyConditional')?.checked ?? false
            }
        };

        document.getElementById('settingsInput').value = JSON.stringify(settingsPayload);
    });

    function ucfirst(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    // Initial render
    renderCanvas();
});
</script>
@endsection
