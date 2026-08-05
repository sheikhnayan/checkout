@extends('admin.main')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">

<style>
.builder-palette-item {
    cursor: grab;
    transition: all 0.2s ease;
    border: 1px solid rgba(255,255,255,0.1);
    background: #2b2c40;
}
.builder-palette-item:hover {
    background: #3a3b54;
    border-color: #696cff;
    transform: translateY(-2px);
}
.builder-canvas {
    min-height: 480px;
    background: #232333;
    border: 2px dashed #444564;
    border-radius: 8px;
    padding: 20px;
}
.field-card {
    background: #2b2c40;
    border: 1px solid #444564;
    border-radius: 6px;
    padding: 14px;
    margin-bottom: 12px;
    cursor: pointer;
    position: relative;
    transition: all 0.2s ease;
}
.field-card:hover, .field-card.selected {
    border-color: #696cff;
    box-shadow: 0 0 10px rgba(105, 108, 255, 0.3);
}
.field-drag-handle {
    cursor: move;
    color: #696cff;
    font-size: 1.2rem;
    margin-right: 6px;
}
.field-actions {
    position: absolute;
    top: 10px;
    right: 10px;
    display: flex;
    gap: 4px;
}

/* Custom Website Toggle Switch */
.toggle-switch {
    position: relative;
    display: inline-block;
    width: 52px;
    height: 28px;
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
    background-color: #3b4056;
    transition: 0.25s ease;
    border-radius: 999px;
    border: 1px solid rgba(255, 255, 255, 0.15);
}
.toggle-switch-slider::before {
    position: absolute;
    content: "";
    height: 20px;
    width: 20px;
    left: 3px;
    bottom: 3px;
    background-color: #ffffff;
    transition: 0.25s ease;
    border-radius: 50%;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.25);
}
.toggle-switch-input:checked + .toggle-switch-slider {
    background-color: #696cff;
    border-color: #696cff;
}
.toggle-switch-input:checked + .toggle-switch-slider::before {
    transform: translateX(24px);
}
.audit-timeline {
    border-left: 2px solid #444564;
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
    background: #696cff;
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
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                <div>
                    <h4 class="mb-1 text-white">
                        <i class="bx bx-slider-alt me-2"></i>{{ isset($form) ? 'Edit Form: ' . $form->title : 'Create New Drag & Drop Form' }}
                    </h4>
                    <p class="text-muted mb-0 small">Drag components onto the canvas or drag existing fields up/down to relocate them anywhere.</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#settingsModal">
                        <i class="bx bx-cog me-1"></i> Settings & Clubs
                    </button>
                    @if(isset($form) && $form->activityLogs->count() > 0)
                        <button type="button" class="btn btn-outline-warning" data-bs-toggle="offcanvas" data-bs-target="#auditLogDrawer">
                            <i class="bx bx-history me-1"></i> Audit Logs ({{ $form->activityLogs->count() }})
                        </button>
                    @endif
                    <a href="{{ route('admin.forms.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary" id="saveFormBtn">
                        <i class="bx bx-save me-1"></i> Save Form
                    </button>
                </div>
            </div>

            <!-- Main Builder Grid -->
            <div class="row g-3">
                
                <!-- Left Column: Draggable Component Palette -->
                <div class="col-lg-3">
                    <div class="card mb-3">
                        <div class="card-header border-bottom py-3">
                            <h6 class="mb-0 text-white"><i class="bx bx-grid-alt me-2"></i>Field Components</h6>
                        </div>
                        <div class="card-body p-2">
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="builder-palette-item p-2 rounded text-center small text-white" draggable="true" data-type="text">
                                        <i class="bx bx-text fs-4 d-block mb-1 text-primary"></i> Short Text
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="builder-palette-item p-2 rounded text-center small text-white" draggable="true" data-type="textarea">
                                        <i class="bx bx-paragraph fs-4 d-block mb-1 text-info"></i> Long Text
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="builder-palette-item p-2 rounded text-center small text-white" draggable="true" data-type="email">
                                        <i class="bx bx-envelope fs-4 d-block mb-1 text-warning"></i> Email
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="builder-palette-item p-2 rounded text-center small text-white" draggable="true" data-type="phone">
                                        <i class="bx bx-phone fs-4 d-block mb-1 text-success"></i> Phone
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="builder-palette-item p-2 rounded text-center small text-white" draggable="true" data-type="select">
                                        <i class="bx bx-select-multiple fs-4 d-block mb-1 text-danger"></i> Dropdown
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="builder-palette-item p-2 rounded text-center small text-white" draggable="true" data-type="radio">
                                        <i class="bx bx-radio-circle-marked fs-4 d-block mb-1 text-primary"></i> Radio List
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="builder-palette-item p-2 rounded text-center small text-white" draggable="true" data-type="checkbox">
                                        <i class="bx bx-checkbox-checked fs-4 d-block mb-1 text-success"></i> Single Checkbox
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="builder-palette-item p-2 rounded text-center small text-white" draggable="true" data-type="date">
                                        <i class="bx bx-calendar fs-4 d-block mb-1 text-info"></i> Date Picker
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="builder-palette-item p-2 rounded text-center small text-white" draggable="true" data-type="time">
                                        <i class="bx bx-time-five fs-4 d-block mb-1 text-warning"></i> Time Picker
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="builder-palette-item p-2 rounded text-center small text-white" draggable="true" data-type="file">
                                        <i class="bx bx-upload fs-4 d-block mb-1 text-secondary"></i> File Upload
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="builder-palette-item p-2 rounded text-center small text-white" draggable="true" data-type="number">
                                        <i class="bx bx-hash fs-4 d-block mb-1 text-primary"></i> Number
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="builder-palette-item p-2 rounded text-center small text-white" draggable="true" data-type="heading">
                                        <i class="bx bx-heading fs-4 d-block mb-1 text-light"></i> Heading
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Meta Information Card -->
                    <div class="card">
                        <div class="card-body">
                            <label class="form-label text-white">Form Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="formTitleInput" class="form-control mb-3" placeholder="e.g. VIP Table Inquiry Form" value="{{ isset($form) ? $form->title : '' }}" required>

                            <label class="form-label text-white">Form Description</label>
                            <textarea name="description" id="formDescInput" class="form-control" rows="3" placeholder="Brief explanation for visitors...">{{ isset($form) ? $form->description : '' }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Middle Column: Live Visual Canvas -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 text-white"><i class="bx bx-layout me-2"></i>Form Layout Canvas</h6>
                            <span class="badge bg-label-primary" id="fieldCountBadge">0 Fields</span>
                        </div>
                        <div class="card-body p-3">
                            <div class="builder-canvas row g-3" id="canvasContainer">
                                <div class="text-center text-muted py-5" id="emptyCanvasNotice">
                                    <i class="bx bx-mouse fs-1 d-block mb-2 text-primary"></i>
                                    Drag components from the left panel onto this canvas to construct your form.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Selected Field Inspector -->
                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-header border-bottom py-3">
                            <h6 class="mb-0 text-white"><i class="bx bx-slider me-2"></i>Field Inspector</h6>
                        </div>
                        <div class="card-body" id="inspectorContainer">
                            <div class="text-center text-muted py-5" id="noSelectionNotice">
                                <i class="bx bx-pointer fs-1 d-block mb-2"></i>
                                Click any field on the canvas to configure label, placeholder, required rules, and options.
                            </div>

                            <div id="inspectorForm" style="display: none;">
                                <div class="mb-3">
                                    <label class="form-label text-white">Field Label</label>
                                    <input type="text" id="propLabel" class="form-control" placeholder="e.g. Full Name">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-white">Field ID / Key</label>
                                    <input type="text" id="propName" class="form-control" placeholder="e.g. full_name">
                                </div>
                                <div class="mb-3" id="placeholderGroup">
                                    <label class="form-label text-white">Placeholder</label>
                                    <input type="text" id="propPlaceholder" class="form-control" placeholder="Enter placeholder...">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-white">Subtitle / Help Tip</label>
                                    <input type="text" id="propHelpText" class="form-control" placeholder="e.g. I agree to the terms and privacy policy">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-white">Column Width</label>
                                    <select id="propWidth" class="form-select">
                                        <option value="col-12">Full Width (100%)</option>
                                        <option value="col-md-6">Half Width (50%)</option>
                                        <option value="col-md-4">One Third (33%)</option>
                                        <option value="col-md-8">Two Thirds (66%)</option>
                                    </select>
                                </div>
                                <div class="mb-3" id="optionsGroup" style="display: none;">
                                    <label class="form-label text-white">Options (One per line)</label>
                                    <textarea id="propOptions" class="form-control" rows="4" placeholder="Option 1&#10;Option 2&#10;Option 3"></textarea>
                                </div>
                                
                                <!-- Exact Website Toggle Switch for Required Field -->
                                <div class="d-flex align-items-center justify-content-between mb-3 p-2 rounded" style="background: #252636; border: 1px solid #3b4056;">
                                    <label class="form-label text-white mb-0 fw-medium">Required Field</label>
                                    <label class="toggle-switch" for="propRequired">
                                        <input type="checkbox" id="propRequired" class="toggle-switch-input">
                                        <span class="toggle-switch-slider"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Settings Modal -->
            <div class="modal fade" id="settingsModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content bg-dark text-white">
                        <div class="modal-header border-bottom border-secondary">
                            <h5 class="modal-title text-white"><i class="bx bx-cog me-2"></i>Form Settings & Club Access</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label text-white">Target Clubs / Websites</label>
                                <select name="website_ids[]" class="form-select bg-dark text-white" multiple style="height: 120px;">
                                    @foreach($websites as $web)
                                        <option value="{{ $web->id }}" {{ isset($form) && is_array($form->website_ids) && in_array($web->id, $form->website_ids) ? 'selected' : '' }}>
                                            {{ $web->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="form-text text-muted small">Leave empty to make form accessible across all clubs. Hold Ctrl/Cmd to select multiple.</span>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-white">Success Message on Submission</label>
                                <textarea id="successMessageInput" class="form-control bg-dark text-white" rows="3" placeholder="Thank you! Your form submission has been received."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-top border-secondary">
                            <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Done</button>
                        </div>
                    </div>
                </div>
            </div>

        </form>

        <!-- Audit Log Drawer (Offcanvas) -->
        @if(isset($form) && $form->activityLogs->count() > 0)
            <div class="offcanvas offcanvas-end bg-dark text-white" tabindex="-1" id="auditLogDrawer" style="width: 400px;">
                <div class="offcanvas-header border-bottom border-secondary">
                    <h5 class="offcanvas-title text-white"><i class="bx bx-history me-2"></i>Audit Activity Log</h5>
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
                    selectedFieldIndex = targetIdx;
                    renderCanvas();
                    updateInspector();
                }
            }
        }
    });

    function addField(type) {
        const id = 'field_' + Math.random().toString(36).substr(2, 9);
        let defaultLabel = ucfirst(type) + ' Field';
        if (type === 'heading') defaultLabel = 'Section Heading';
        if (type === 'checkbox') defaultLabel = 'I agree to the terms and conditions';

        const newField = {
            id: id,
            type: type,
            label: defaultLabel,
            name: id,
            placeholder: '',
            help_text: '',
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
                inputPreview = `<textarea class="form-control form-control-sm" placeholder="${f.placeholder || ''}" disabled></textarea>`;
            } else if (f.type === 'select') {
                inputPreview = `<select class="form-select form-select-sm" disabled><option>${f.options ? f.options[0] : 'Select...'}</option></select>`;
            } else if (f.type === 'checkbox') {
                inputPreview = `
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" disabled>
                        <label class="form-check-label text-white small">${f.label}</label>
                    </div>`;
            } else if (f.type === 'time') {
                inputPreview = `<input type="text" class="form-control form-control-sm time-picker-preview" placeholder="Select Time (e.g. 10:30 PM)" disabled>`;
            } else if (f.type === 'heading') {
                inputPreview = `<h5 class="text-white mb-0">${f.label}</h5>`;
            } else {
                inputPreview = `<input type="${f.type === 'phone' ? 'tel' : f.type}" class="form-control form-control-sm" placeholder="${f.placeholder || ''}" disabled>`;
            }

            card.innerHTML = `
                <div class="field-actions">
                    <button type="button" class="btn btn-xs btn-outline-secondary move-up-btn" data-idx="${idx}" title="Move Up" ${idx === 0 ? 'disabled' : ''}>
                        <i class="bx bx-chevron-up"></i>
                    </button>
                    <button type="button" class="btn btn-xs btn-outline-secondary move-down-btn" data-idx="${idx}" title="Move Down" ${idx === fields.length - 1 ? 'disabled' : ''}>
                        <i class="bx bx-chevron-down"></i>
                    </button>
                    <button type="button" class="btn btn-xs btn-outline-danger delete-field-btn" data-idx="${idx}" title="Delete Field">
                        <i class="bx bx-trash"></i>
                    </button>
                </div>
                <div class="d-flex align-items-center mb-1">
                    <i class="bx bx-move field-drag-handle" title="Drag to relocate field"></i>
                    ${f.type !== 'heading' && f.type !== 'checkbox' ? `<label class="form-label text-white mb-0 small me-2">${f.label} ${f.required ? '<span class="text-danger">*</span>' : ''}</label>` : ''}
                </div>
                ${inputPreview}
                ${f.help_text ? `<div class="text-info micro-text mt-1"><i class="bx bx-info-circle me-1"></i>${f.help_text}</div>` : ''}
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
    }

    function updateInspector() {
        if (selectedFieldIndex === null || !fields[selectedFieldIndex]) {
            inspectorForm.style.display = 'none';
            noSelectionNotice.style.display = 'block';
            return;
        }

        const f = fields[selectedFieldIndex];
        noSelectionNotice.style.display = 'none';
        inspectorForm.style.display = 'block';

        document.getElementById('propLabel').value = f.label || '';
        document.getElementById('propName').value = f.name || '';
        document.getElementById('propPlaceholder').value = f.placeholder || '';
        document.getElementById('propHelpText').value = f.help_text || '';
        document.getElementById('propWidth').value = f.width_class || 'col-12';
        document.getElementById('propRequired').checked = !!f.required;

        const optionsGroup = document.getElementById('optionsGroup');
        const placeholderGroup = document.getElementById('placeholderGroup');

        if (['select', 'radio'].includes(f.type)) {
            optionsGroup.style.display = 'block';
            document.getElementById('propOptions').value = (f.options || []).join('\n');
        } else {
            optionsGroup.style.display = 'none';
        }

        if (['checkbox', 'heading'].includes(f.type)) {
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
