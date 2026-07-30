@php
    $selectedWebsiteId = old('website_id', $feedPost->website_id ?? ($websites->first()->id ?? null));
    $selectedModelId = old('feed_model_id', $feedPost->feed_model_id ?? null);
    $selectedAuthorMode = old('author_mode', $feedPost->author_mode ?? 'model');
    $existingMediaItems = $feedPost ? ($feedPost->resolved_media_items ?? []) : [];
    $oldExternalLinks = old('external_media_links', []);
    $oldExternalTypes = old('external_media_types', []);
    $isEntertainerUser = auth()->check() && auth()->user()->isEntertainer() && auth()->user()->entertainer;
    $entertainerProfileId = $isEntertainerUser ? auth()->user()->entertainer->feed_model_id : null;
    $showOnRollCall = old('show_on_roll_call', isset($feedPost) ? $feedPost->show_on_roll_call : false);
    $canUseRollCallInitial = !$isEntertainerUser && in_array($selectedAuthorMode, ['model', 'club'], true);
    $showOnRollCall = $canUseRollCallInitial ? (bool) $showOnRollCall : false;
    $rollCallStartDateSource = optional($feedPost)->roll_call_start_date
        ?? optional($feedPost)->roll_call_date
        ?? optional($feedPost)->posted_at
        ?? now();
    $rollCallEndDateSource = optional($feedPost)->roll_call_end_date
        ?? optional($feedPost)->roll_call_start_date
        ?? optional($feedPost)->roll_call_date;
    $rollCallStartDateValue = old('roll_call_start_date', optional($rollCallStartDateSource)->format('Y-m-d'));
    $rollCallEndDateValue = old('roll_call_end_date', optional($rollCallEndDateSource)->format('Y-m-d'));
    $postedAtValue = old('posted_at', optional($feedPost->posted_at ?? now('America/Los_Angeles'))?->timezone('America/Los_Angeles')->format('Y-m-d H:i'));
@endphp

<style>
    .feed-media-card {
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 12px;
        background: #fff;
        height: 100%;
    }

    .feed-media-preview {
        width: 100%;
        height: 180px;
        border-radius: 12px;
        object-fit: cover;
        background: #111827;
    }

    .feed-media-preview-wrap {
        position: relative;
    }

    .feed-media-pill {
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(17, 24, 39, 0.78);
        color: #fff;
        border-radius: 999px;
        padding: 6px 10px;
        font-size: 11px;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .feed-dynamic-list {
        display: grid;
        gap: 12px;
    }

    .feed-dynamic-item {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 10px;
        align-items: end;
        padding: 14px;
        border-radius: 14px;
        border: 1px solid #e5e7eb;
        background: #f8fafc;
    }

    .feed-dynamic-fields {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 140px;
        gap: 10px;
    }

    @media (max-width: 767.98px) {
        .feed-dynamic-item,
        .feed-dynamic-fields {
            grid-template-columns: 1fr;
        }
    }

    .feed-calendar-input {
        min-height: 42px;
    }
</style>

<div class="row g-4">
    @if($isEntertainerUser)
        <div class="col-12">
            <div class="alert alert-info mb-0">
                Your post will be submitted for approval. A club admin or super admin must approve it before it appears on the public feed.
            </div>
        </div>

        <input type="hidden" name="website_id" id="website_id" value="{{ $selectedWebsiteId }}">
        <input type="hidden" name="author_mode" id="author_mode" value="model">
        <input type="hidden" name="feed_model_id" id="feed_model_id" value="{{ $entertainerProfileId }}">
    @else
        <div class="col-md-6">
            <label for="website_id" class="form-label">Website / Club <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The club or venue this post is associated with."></i></label>
            <select name="website_id" id="website_id" class="form-control" required>
                @foreach($websites as $website)
                    <option value="{{ $website->id }}" @selected((string) $selectedWebsiteId === (string) $website->id)>{{ $website->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label for="author_mode" class="form-label">Post As <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Choose whether to post as the club brand or as a specific entertainer."></i></label>
            <select name="author_mode" id="author_mode" class="form-control" required>
                <option value="model" @selected($selectedAuthorMode === 'model')>Entertainer Profile</option>
                <option value="club" @selected($selectedAuthorMode === 'club')>Club Itself</option>
            </select>
        </div>
    @endif

    @if(!$isEntertainerUser)
    <div class="col-md-6" id="feed-model-select-wrap">
        <label for="feed_model_id" class="form-label">Entertainer Profile <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The entertainer profile this post will be attributed to."></i></label>
        <select name="feed_model_id" id="feed_model_id" class="form-control">
            <option value="">Select entertainer</option>
            @foreach($feedModels as $model)
                    <option value="{{ $model->id }}" data-website="{{ $model->website_id }}" @selected((string) $selectedModelId === (string) $model->id)>{{ $model->name }} - {{ $model->is_real_profile ? 'Verified' : 'Managed' }} ({{ $model->website->name ?? 'No website' }})</option>
            @endforeach
        </select>
        <small class="text-muted">Visible only when posting as an entertainer.</small>
    </div>
    @endif

    <div class="col-md-6">
        <label for="posted_at" class="form-label">Post Date <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The date this post is published or scheduled."></i></label>
        <input type="text" name="posted_at" id="posted_at" class="form-control feed-calendar-input" value="{{ $postedAtValue }}" autocomplete="off" placeholder="YYYY-MM-DD HH:MM">
    </div>

    <div class="col-md-6 d-flex align-items-center">
        <div class="form-check mt-4">
            <input class="form-check-input" type="checkbox" value="1" id="is_active" name="is_active" @checked(old('is_active', isset($feedPost) ? $feedPost->is_active : true))>
            <label class="form-check-label" for="is_active">Visible on public feed <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="When enabled, this post is visible to the public on the feed."></i></label>
        </div>
    </div>

    <div class="col-md-6 align-items-center" id="roll-call-toggle-wrap" style="display: {{ $canUseRollCallInitial ? 'flex' : 'none' }};">
        <div class="form-check mt-4">
            <input class="form-check-input" type="checkbox" value="1" id="show_on_roll_call" name="show_on_roll_call" @checked($showOnRollCall)>
            <label class="form-check-label" for="show_on_roll_call">Do you want to post to Roll Call in feed post? <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Whether this post also appears in the Roll Call section of the feed."></i></label>
        </div>
    </div>

    <div class="col-md-6" id="roll-call-date-wrap" style="display: {{ $canUseRollCallInitial && $showOnRollCall ? 'block' : 'none' }};">
        <label for="roll_call_start_date" class="form-label">Show in Roll Call from <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Start date for this post to appear in the Roll Call section of the feed."></i></label>
        <input type="text" name="roll_call_start_date" id="roll_call_start_date" class="form-control feed-calendar-input" value="{{ $rollCallStartDateValue }}" autocomplete="off" placeholder="YYYY-MM-DD">
    </div>

    <div class="col-md-6" id="roll-call-end-date-wrap" style="display: {{ $canUseRollCallInitial && $showOnRollCall ? 'block' : 'none' }};">
        <label for="roll_call_end_date" class="form-label">Until <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="End date for this post's Roll Call appearance. Leave blank for a single day."></i></label>
        <input type="text" name="roll_call_end_date" id="roll_call_end_date" class="form-control feed-calendar-input" value="{{ $rollCallEndDateValue }}" autocomplete="off" placeholder="YYYY-MM-DD">
        <small class="text-muted">This post appears in Roll Call for every date in the range. Leave end date empty for one day.</small>
    </div>

    <div class="col-12">
        <label class="form-label">Caption <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The text content of this feed post."></i></label>
        <div id="caption-editor"></div>
        <textarea name="caption" id="caption" style="display:none">{{ old('caption', $feedPost->caption ?? '') }}</textarea>
    </div>

    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
            <div>
                <label class="form-label mb-0">Upload Media <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Upload images or videos to attach to this post. Max 4MB each."></i></label>
                <div><small class="text-muted">Accepted: JPG, PNG, WEBP, GIF, MP4, MOV, WEBM &mdash; max 4 MB each.</small></div>
                <div><small class="text-muted">Recommended dimensions: <strong>1080 &times; 1080 px</strong> (square) or <strong>1080 &times; 1350 px</strong> (portrait). Actual file dimensions shown after selecting.</small></div>
            </div>
            <button type="button" class="btn btn-outline-primary btn-sm" id="add-upload-row">Upload Media</button>
        </div>
        <div id="upload-rows" class="feed-dynamic-list"></div>
    </div>

    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
            <div>
                <label class="form-label mb-0">External Media Links <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="External video or image URLs to embed in this post. Enter one URL per line."></i></label>
                <div><small class="text-muted">Use direct image/video links. Videos can also be hosted externally.</small></div>
            </div>
            <button type="button" class="btn btn-outline-primary btn-sm" id="add-external-row">Add External Media</button>
        </div>
        <div id="external-rows" class="feed-dynamic-list">
            @foreach($oldExternalLinks as $index => $link)
                <div class="feed-dynamic-item external-row">
                    <div class="feed-dynamic-fields">
                        <input type="url" class="form-control" name="external_media_links[]" value="{{ $link }}" placeholder="https://example.com/media.mp4">
                        <select name="external_media_types[]" class="form-control">
                            <option value="image" @selected(($oldExternalTypes[$index] ?? 'image') === 'image')>Image</option>
                            <option value="video" @selected(($oldExternalTypes[$index] ?? '') === 'video')>Video</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-outline-danger remove-dynamic-row">Remove</button>
                </div>
            @endforeach
        </div>
    </div>

    @if(!empty($existingMediaItems))
        <div class="col-12">
            <label class="form-label">Existing Media <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Media files already attached to this post. Remove any you want to delete before saving."></i></label>
            <div class="row g-3">
                @foreach($existingMediaItems as $index => $item)
                    @php
                        $mediaUrl = ($item['source'] ?? 'upload') === 'upload' ? asset('uploads/' . $item['url']) : $item['url'];
                    @endphp
                    <div class="col-md-4 col-sm-6">
                        <label class="feed-media-card">
                            <div class="feed-media-preview-wrap">
                                @if(($item['type'] ?? 'image') === 'video')
                                    <video class="feed-media-preview" src="{{ $mediaUrl }}" controls></video>
                                @else
                                    <img class="feed-media-preview" src="{{ $mediaUrl }}" alt="Existing media">
                                @endif
                                <span class="feed-media-pill">{{ strtoupper($item['type'] ?? 'image') }}</span>
                            </div>
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" name="existing_media_keys[]" value="{{ $index }}" checked>
                                <span class="form-check-label">Keep this media item</span>
                            </div>
                            <small class="text-muted d-block mt-2">{{ ucfirst($item['source'] ?? 'upload') }}</small>
                        </label>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<div class="mt-4 d-flex gap-2 flex-wrap">
    <button type="submit" class="btn btn-primary">Save Post</button>
    <a href="{{ route('admin.feed-post.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>



<script>
document.addEventListener('DOMContentLoaded', function () {
    const websiteSelect = document.getElementById('website_id');
    const authorModeSelect = document.getElementById('author_mode');
    const modelSelect = document.getElementById('feed_model_id');
    const modelWrap = document.getElementById('feed-model-select-wrap');
    const uploadRows = document.getElementById('upload-rows');
    const externalRows = document.getElementById('external-rows');
    const addUploadRowBtn = document.getElementById('add-upload-row');
    const addExternalRowBtn = document.getElementById('add-external-row');
    const rollCallToggleWrap = document.getElementById('roll-call-toggle-wrap');
    const postedAtInput = document.getElementById('posted_at');
    const showOnRollCallCheckbox = document.getElementById('show_on_roll_call');
    const rollCallDateWrap = document.getElementById('roll-call-date-wrap');
    const rollCallEndDateWrap = document.getElementById('roll-call-end-date-wrap');
    const rollCallStartDateInput = document.getElementById('roll_call_start_date');
    const rollCallEndDateInput = document.getElementById('roll_call_end_date');
    let rollCallStartPicker = null;
    let rollCallEndPicker = null;
    const selectedModelInput = @json((string) $selectedModelId);
    const isEntertainerUser = @json((bool) $isEntertainerUser);
    const hasModelSelect = modelSelect && modelSelect.tagName === 'SELECT';
    const allModelOptions = hasModelSelect
        ? Array.from(modelSelect.options)
            .filter(function (option) { return !!option.value; })
            .map(function (option) {
                return {
                    value: option.value,
                    text: option.text,
                    website: option.dataset.website || ''
                };
            })
        : [];

    function canUseRollCall() {
        if (!authorModeSelect || !showOnRollCallCheckbox) {
            return false;
        }

        if (isEntertainerUser) {
            return false;
        }

        return authorModeSelect && (authorModeSelect.value === 'model' || authorModeSelect.value === 'club');
    }

    function initCalendars() {
        if (typeof flatpickr !== 'function') {
            return;
        }

        if (postedAtInput) {
            flatpickr(postedAtInput, {
                enableTime: true,
                time_24hr: false,
                dateFormat: 'Y-m-d H:i',
                allowInput: true,
                disableMobile: true,
                defaultDate: postedAtInput.value || null,
            });
        }

        if (rollCallStartDateInput) {
            rollCallStartPicker = flatpickr(rollCallStartDateInput, {
                dateFormat: 'Y-m-d',
                allowInput: true,
                disableMobile: true,
                defaultDate: rollCallStartDateInput.value || null,
                monthSelectorType: 'static',
                locale: {
                    firstDayOfWeek: 0,
                },
            });
        }

        if (rollCallEndDateInput) {
            rollCallEndPicker = flatpickr(rollCallEndDateInput, {
                dateFormat: 'Y-m-d',
                allowInput: true,
                disableMobile: true,
                defaultDate: rollCallEndDateInput.value || null,
                monthSelectorType: 'static',
                locale: {
                    firstDayOfWeek: 0,
                },
            });
        }

        if (rollCallStartDateInput && rollCallEndPicker && rollCallStartDateInput.value) {
            rollCallEndPicker.set('minDate', rollCallStartDateInput.value);
        }
    }

    function syncModelOptions() {
        if (!websiteSelect || !hasModelSelect) {
            return;
        }

        const selectedWebsite = websiteSelect.value;
        const currentValue = modelSelect.value || selectedModelInput;

        while (modelSelect.options.length) {
            modelSelect.remove(0);
        }

        const placeholder = document.createElement('option');
        placeholder.value = '';
        placeholder.textContent = 'Select entertainer';
        modelSelect.appendChild(placeholder);

        const filteredModels = allModelOptions.filter(function (model) {
            return model.website === selectedWebsite;
        });

        filteredModels.forEach(function (model) {
            const option = document.createElement('option');
            option.value = model.value;
            option.textContent = model.text;
            option.dataset.website = model.website;

            if (String(model.value) === String(currentValue)) {
                option.selected = true;
            }

            modelSelect.appendChild(option);
        });

        if (modelSelect.selectedIndex <= 0 && filteredModels.length > 0) {
            modelSelect.selectedIndex = 1;
        }
    }

    function syncAuthorMode() {
        if (!authorModeSelect || !modelWrap || !modelSelect) {
            return;
        }

        const isClub = authorModeSelect.value === 'club';
        modelWrap.style.display = isClub ? 'none' : '';
        modelSelect.required = !isClub;
    }

    function syncRollCallDateField() {
        if (!showOnRollCallCheckbox || !rollCallDateWrap || !rollCallStartDateInput || !rollCallEndDateWrap || !rollCallEndDateInput || !rollCallToggleWrap) {
            return;
        }

        const allowed = canUseRollCall();
        rollCallToggleWrap.style.display = allowed ? 'flex' : 'none';

        if (!allowed) {
            showOnRollCallCheckbox.checked = false;
            rollCallDateWrap.style.display = 'none';
            rollCallEndDateWrap.style.display = 'none';
            rollCallStartDateInput.required = false;
            rollCallEndDateInput.required = false;
            return;
        }

        const enabled = showOnRollCallCheckbox.checked;
        rollCallDateWrap.style.display = enabled ? 'block' : 'none';
        rollCallEndDateWrap.style.display = enabled ? 'block' : 'none';
        rollCallStartDateInput.required = enabled;
        rollCallEndDateInput.required = false;

        if (enabled && !rollCallStartDateInput.value) {
            rollCallStartDateInput.value = new Date().toISOString().slice(0, 10);
        }

        if (enabled && rollCallStartDateInput.value && !rollCallEndDateInput.value) {
            rollCallEndDateInput.value = rollCallStartDateInput.value;
        }
    }

    function addUploadRow() {
        var row = document.createElement('div');
        row.className = 'feed-dynamic-item upload-row';
        row.innerHTML =
            '<div>' +
            '<input type="file" name="media_uploads[]" class="form-control" accept="image/*,video/*">' +
            '<small class="text-muted d-block mt-1 upload-dimensions"></small>' +
            '</div>' +
            '<button type="button" class="btn btn-outline-danger remove-dynamic-row">Remove</button>';
        uploadRows.appendChild(row);
    }

    function setRowDimensionsText(fileInput, message) {
        const row = fileInput.closest('.upload-row');
        if (!row) {
            return;
        }

        const info = row.querySelector('.upload-dimensions');
        if (info) {
            info.textContent = message;
        }
    }

    function detectMediaDimensions(fileInput) {
        const file = fileInput.files && fileInput.files[0];
        if (!file) {
            setRowDimensionsText(fileInput, 'Dimensions: -');
            return;
        }

        const objectUrl = URL.createObjectURL(file);
        setRowDimensionsText(fileInput, 'Dimensions: Reading...');

        if ((file.type || '').startsWith('image/')) {
            const img = new Image();
            img.onload = function () {
                setRowDimensionsText(fileInput, 'Dimensions: ' + img.naturalWidth + ' x ' + img.naturalHeight + ' px (image)');
                URL.revokeObjectURL(objectUrl);
            };
            img.onerror = function () {
                setRowDimensionsText(fileInput, 'Dimensions: Unable to read image dimensions');
                URL.revokeObjectURL(objectUrl);
            };
            img.src = objectUrl;
            return;
        }

        if ((file.type || '').startsWith('video/')) {
            const video = document.createElement('video');
            video.preload = 'metadata';
            video.onloadedmetadata = function () {
                setRowDimensionsText(fileInput, 'Dimensions: ' + video.videoWidth + ' x ' + video.videoHeight + ' px (video)');
                URL.revokeObjectURL(objectUrl);
            };
            video.onerror = function () {
                setRowDimensionsText(fileInput, 'Dimensions: Unable to read video dimensions');
                URL.revokeObjectURL(objectUrl);
            };
            video.src = objectUrl;
            return;
        }

        setRowDimensionsText(fileInput, 'Dimensions: Unsupported media type');
        URL.revokeObjectURL(objectUrl);
    }

    function addExternalRow() {
        var row = document.createElement('div');
        row.className = 'feed-dynamic-item external-row';
        row.innerHTML =
            '<div class="feed-dynamic-fields">' +
            '<input type="url" class="form-control" name="external_media_links[]" placeholder="https://example.com/media.mp4">' +
            '<select name="external_media_types[]" class="form-control">' +
            '<option value="image">Image</option>' +
            '<option value="video">Video</option>' +
            '</select>' +
            '</div>' +
            '<button type="button" class="btn btn-outline-danger remove-dynamic-row">Remove</button>';
        externalRows.appendChild(row);
    }

    if (addUploadRowBtn && uploadRows) {
        addUploadRowBtn.addEventListener('click', addUploadRow);
    }
    if (addExternalRowBtn && externalRows) {
        addExternalRowBtn.addEventListener('click', addExternalRow);
    }

    document.addEventListener('click', function (event) {
        if (event.target.classList.contains('remove-dynamic-row')) {
            const row = event.target.closest('.feed-dynamic-item');
            if (row) {
                row.remove();
            }
        }
    });

    document.addEventListener('change', function (event) {
        const target = event.target;
        if (target && target.matches('input[name="media_uploads[]"]')) {
            detectMediaDimensions(target);
        }
    });

    if (websiteSelect) {
        websiteSelect.addEventListener('change', syncModelOptions);
    }
    if (authorModeSelect) {
        authorModeSelect.addEventListener('change', syncAuthorMode);
    }
    if (showOnRollCallCheckbox) {
        showOnRollCallCheckbox.addEventListener('change', syncRollCallDateField);
    }

    if (rollCallStartDateInput && rollCallEndDateInput) {
        rollCallStartDateInput.addEventListener('change', function () {
            if (rollCallStartDateInput.value) {
                rollCallEndDateInput.min = rollCallStartDateInput.value;
                if (rollCallEndPicker) {
                    rollCallEndPicker.set('minDate', rollCallStartDateInput.value);
                }
                if (!rollCallEndDateInput.value || rollCallEndDateInput.value < rollCallStartDateInput.value) {
                    rollCallEndDateInput.value = rollCallStartDateInput.value;
                }
            } else {
                rollCallEndDateInput.removeAttribute('min');
                if (rollCallEndPicker) {
                    rollCallEndPicker.set('minDate', null);
                }
            }
        });
    }

    if (uploadRows && !uploadRows.children.length) {
        addUploadRow();
    }

    syncModelOptions();
    syncAuthorMode();
    initCalendars();
    syncRollCallDateField();

    if (rollCallStartDateInput && rollCallEndDateInput && rollCallStartDateInput.value) {
        rollCallEndDateInput.min = rollCallStartDateInput.value;
    }
});
</script>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css" rel="stylesheet">
<style>
.ql-toolbar.ql-snow{background:#1e2230;border:1px solid rgba(255,255,255,.12)!important;border-bottom:1px solid rgba(255,255,255,.07)!important;border-radius:6px 6px 0 0;padding:8px}
.ql-container.ql-snow{background:#161b2e;border:1px solid rgba(255,255,255,.12)!important;border-top:none!important;border-radius:0 0 6px 6px;font-size:14px}
.ql-editor{min-height:140px;color:#d8def0;line-height:1.7}
.ql-editor.ql-blank::before{color:rgba(216,222,240,.3);font-style:normal}
.ql-snow .ql-stroke{stroke:rgba(216,222,240,.6)}
.ql-snow .ql-fill,.ql-snow .ql-stroke.ql-fill{fill:rgba(216,222,240,.6)}
.ql-snow .ql-picker{color:rgba(216,222,240,.6)}
.ql-snow .ql-picker-options{background:#1e2230;border-color:rgba(255,255,255,.12)}
.ql-snow .ql-toolbar button.ql-active .ql-stroke,.ql-snow .ql-toolbar button:hover .ql-stroke{stroke:#ffcc00}
.ql-snow .ql-toolbar button.ql-active .ql-fill,.ql-snow .ql-toolbar button:hover .ql-fill{fill:#ffcc00}
.ql-snow .ql-toolbar button.ql-active,.ql-snow .ql-toolbar button:hover{color:#ffcc00}
.ql-snow a{color:#ffcc00}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var captionTA = document.getElementById('caption');
    var quillCaption = new Quill('#caption-editor', {
        theme: 'snow',
        placeholder: 'Write the post caption here...',
        modules: { toolbar: [['bold','italic','underline'],[{'list':'ordered'},{'list':'bullet'}],['link','clean']] }
    });
    if (captionTA && captionTA.value) quillCaption.root.innerHTML = captionTA.value;
    var captionForm = captionTA ? captionTA.closest('form') : null;
    if (captionForm) captionForm.addEventListener('submit', function() {
        captionTA.value = quillCaption.root.innerHTML === '<p><br></p>' ? '' : quillCaption.root.innerHTML;
    });
});
</script>
@endpush