@php
    $selectedWebsiteId = old('website_id', $feedPost->website_id ?? ($websites->first()->id ?? null));
    $selectedModelId = old('feed_model_id', $feedPost->feed_model_id ?? null);
    $selectedAuthorMode = old('author_mode', $feedPost->author_mode ?? 'model');
    $existingMediaItems = $feedPost ? ($feedPost->resolved_media_items ?? []) : [];
    $oldExternalLinks = old('external_media_links', []);
    $oldExternalTypes = old('external_media_types', []);
    $isEntertainerUser = auth()->check() && auth()->user()->isEntertainer() && auth()->user()->entertainer;
    $entertainerProfileId = $isEntertainerUser ? auth()->user()->entertainer->feed_model_id : null;
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
</style>

<div class="row g-4">
    @if($isEntertainerUser)
        <input type="hidden" name="website_id" id="website_id" value="{{ $selectedWebsiteId }}">
        <input type="hidden" name="author_mode" id="author_mode" value="model">
        <input type="hidden" name="feed_model_id" id="feed_model_id" value="{{ $entertainerProfileId }}">
    @else
        <div class="col-md-6">
            <label for="website_id" class="form-label">Website / Club</label>
            <select name="website_id" id="website_id" class="form-control" required>
                @foreach($websites as $website)
                    <option value="{{ $website->id }}" @selected((string) $selectedWebsiteId === (string) $website->id)>{{ $website->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label for="author_mode" class="form-label">Post As</label>
            <select name="author_mode" id="author_mode" class="form-control" required>
                <option value="model" @selected($selectedAuthorMode === 'model')>Entertainer Profile</option>
                <option value="club" @selected($selectedAuthorMode === 'club')>Club Itself</option>
            </select>
        </div>
    @endif

    @if(!$isEntertainerUser)
    <div class="col-md-6" id="feed-model-select-wrap">
        <label for="feed_model_id" class="form-label">Entertainer Profile</label>
        <select name="feed_model_id" id="feed_model_id" class="form-control">
            <option value="">Select entertainer</option>
            @foreach($feedModels as $model)
                <option value="{{ $model->id }}" data-website="{{ $model->website_id }}" @selected((string) $selectedModelId === (string) $model->id)>{{ $model->name }} ({{ $model->website->name ?? 'No website' }})</option>
            @endforeach
        </select>
        <small class="text-muted">Visible only when posting as an entertainer.</small>
    </div>
    @endif

    <div class="col-md-6">
        <label for="posted_at" class="form-label">Post Date</label>
        <input type="datetime-local" name="posted_at" id="posted_at" class="form-control" value="{{ old('posted_at', optional($feedPost->posted_at ?? now())->format('Y-m-d\TH:i')) }}">
    </div>

    <div class="col-md-6 d-flex align-items-center">
        <div class="form-check mt-4">
            <input class="form-check-input" type="checkbox" value="1" id="is_active" name="is_active" @checked(old('is_active', isset($feedPost) ? $feedPost->is_active : true))>
            <label class="form-check-label" for="is_active">Visible on public feed</label>
        </div>
    </div>

    <div class="col-12">
        <label for="caption" class="form-label">Caption</label>
        <textarea name="caption" id="caption" class="form-control" rows="5" placeholder="Write the post caption here">{{ old('caption', $feedPost->caption ?? '') }}</textarea>
    </div>

    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
            <div>
                <label class="form-label mb-0">Upload Media</label>
                <div><small class="text-muted">Accepted: JPG, PNG, WEBP, GIF, MP4, MOV, WEBM &mdash; max 20 MB each.</small></div>
                <div><small class="text-muted">Recommended dimensions: <strong>1080 &times; 1080 px</strong> (square) or <strong>1080 &times; 1350 px</strong> (portrait). Actual file dimensions shown after selecting.</small></div>
            </div>
            <button type="button" class="btn btn-outline-primary btn-sm" id="add-upload-row">Upload Media</button>
        </div>
        <div id="upload-rows" class="feed-dynamic-list"></div>
    </div>

    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
            <div>
                <label class="form-label mb-0">External Media Links</label>
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
            <label class="form-label">Existing Media</label>
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
    const selectedModelInput = @json((string) $selectedModelId);
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

    if (uploadRows && !uploadRows.children.length) {
        addUploadRow();
    }

    syncModelOptions();
    syncAuthorMode();
});
</script>