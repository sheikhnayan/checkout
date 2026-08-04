@extends('admin.main')

@section('content')
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
    transition: border-color 0.2s ease;
}
.field-card:hover, .field-card.selected {
    border-color: #696cff;
    box-shadow: 0 0 10px rgba(105, 108, 255, 0.3);
}
.field-actions {
    position: absolute;
    top: 10px;
    right: 10px;
}
.field-ghost {
    opacity: 0.4;
    background: #32344d;
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
                    <p class="text-muted mb-0 small">Drag fields from the left panel onto the canvas. Reorder, set grid widths, and configure field properties.</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#settingsModal">
                        <i class="bx bx-cog me-1"></i> Form Settings & Clubs
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
                                        <i class="bx bx-checkbox-checked fs-4 d-block mb-1 text-success"></i> Checkbox
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
                                <div class="mb-3">
                                    <label class="form-label text-white">Placeholder</label>
                                    <input type="text" id="propPlaceholder" class="form-control" placeholder="Enter placeholder...">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-white">Help Tip / Subtitle</label>
                                    <input type="text" id="propHelpText" class="form-control" placeholder="e.g. Provide primary contact phone">
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
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="propRequired">
                                    <label class="form-check-label text-white" for="propRequired">Required Field</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Settings Modal -->
            <div class="modal fade" id="settingsModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-white"><i class="bx bx-cog me-2"></i>Form Settings & Club Access</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-content-body p-3">
                            <div class="mb-3">
                                <label class="form-label text-white">Target Clubs / Websites</label>
                                <select name="website_ids[]" class="form-select" multiple style="height: 120px;">
                                    @foreach($websites as $web)
                                        <option value="{{ $web->id }}" {{ isset($form) && is_array($form->website_ids) && in_array($web->id, $form->website_ids) ? 'selected' : '' }}>
                                            {{ $web->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="form-text small">Leave empty to make form accessible across all clubs. Hold Ctrl/Cmd to select multiple.</span>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-white">Success Message on Submission</label>
                                <textarea id="successMessageInput" class="form-control" rows="3" placeholder="Thank you! Your form submission has been received."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Done</button>
                        </div>
                    </div>
                </div>
            </div>

        </form>

        <!-- Audit Log Drawer (Offcanvas) -->
        @if(isset($form) && $form->activityLogs->count() > 0)
            <div class="offcanvas offcanvas-end bg-dark text-white" tabindex="-1" id="auditLogDrawer" style="width: 400px;">
                <div class="offcanvas-header border-bottom">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    let fields = [];
    try {
        fields = JSON.parse(document.getElementById('fieldsSchemaInput').value || '[]');
    } catch(e) {
        fields = [];
    }

    let selectedFieldIndex = null;
    const canvas = document.getElementById('canvasContainer');
    const emptyNotice = document.getElementById('emptyCanvasNotice');
    const inspectorForm = document.getElementById('inspectorForm');
    const noSelectionNotice = document.getElementById('noSelectionNotice');
    const fieldCountBadge = document.getElementById('fieldCountBadge');

    // Drag from palette
    document.querySelectorAll('.builder-palette-item').forEach(item => {
        item.addEventListener('dragstart', (e) => {
            e.dataTransfer.setData('field-type', item.getAttribute('data-type'));
        });
    });

    canvas.addEventListener('dragover', (e) => e.preventDefault());
    canvas.addEventListener('drop', (e) => {
        e.preventDefault();
        const type = e.dataTransfer.getData('field-type');
        if (type) {
            addField(type);
        }
    });

    function addField(type) {
        const id = 'field_' + Math.random().toString(36).substr(2, 9);
        let defaultLabel = ucfirst(type) + ' Field';
        if (type === 'heading') defaultLabel = 'Section Heading';

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
        renderCanvas();
        selectField(fields.length - 1);
    }

    function renderCanvas() {
        // Clear canvas
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

            const card = document.createElement('div');
            card.className = 'field-card' + (selectedFieldIndex === idx ? ' selected' : '');

            let inputPreview = '';
            if (f.type === 'textarea') {
                inputPreview = `<textarea class="form-control form-control-sm" placeholder="${f.placeholder || ''}" disabled></textarea>`;
            } else if (f.type === 'select') {
                inputPreview = `<select class="form-select form-select-sm" disabled><option>${f.options ? f.options[0] : 'Select...'}</option></select>`;
            } else if (f.type === 'heading') {
                inputPreview = `<h5 class="text-white mb-0">${f.label}</h5>`;
            } else {
                inputPreview = `<input type="${f.type === 'phone' ? 'tel' : (f.type === 'heading' ? 'text' : f.type)}" class="form-control form-control-sm" placeholder="${f.placeholder || ''}" disabled>`;
            }

            card.innerHTML = `
                <div class="field-actions">
                    <button type="button" class="btn btn-xs btn-outline-danger delete-field-btn" data-idx="${idx}"><i class="bx bx-trash"></i></button>
                </div>
                ${f.type !== 'heading' ? `<label class="form-label text-white mb-1 small">${f.label} ${f.required ? '<span class="text-danger">*</span>' : ''}</label>` : ''}
                ${inputPreview}
                ${f.help_text ? `<div class="form-text small mt-1">${f.help_text}</div>` : ''}
            `;

            card.addEventListener('click', (e) => {
                if (e.target.closest('.delete-field-btn')) return;
                selectField(idx);
            });

            wrapper.appendChild(card);
            canvas.appendChild(wrapper);
        });

        // Add delete event handlers
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
        if (['select', 'radio', 'checkbox'].includes(f.type)) {
            optionsGroup.style.display = 'block';
            document.getElementById('propOptions').value = (f.options || []).join('\n');
        } else {
            optionsGroup.style.display = 'none';
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
