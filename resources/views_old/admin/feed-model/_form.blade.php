@php
    $selectedWebsiteId = old('website_id', $feedModel->website_id ?? ($websites->first()->id ?? null));
    $performanceDates = collect(old('performance_dates', $feedModel?->performanceDates?->pluck('performance_date')->map(fn ($date) => optional($date)->format('Y-m-d'))->all() ?? []))
        ->filter(fn ($date) => !empty($date))
        ->values()
        ->all();

    if (empty($performanceDates)) {
        $performanceDates = [''];
    }
@endphp

<div class="row g-4">
    <div class="col-md-6">
        <label for="website_id" class="form-label">Website <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The club or venue this entertainer profile is associated with."></i></label>
        <select name="website_id" id="website_id" class="form-control" required>
            @foreach($websites as $website)
                <option value="{{ $website->id }}" @selected((string) $selectedWebsiteId === (string) $website->id)>{{ $website->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label for="name" class="form-label">Entertainer Name <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The performer's display name shown on their public feed profile."></i></label>
        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $feedModel->name ?? '') }}" placeholder="Example: Aria Vale" required>
    </div>

    <div class="col-12">
        <label class="form-label">Bio <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="A short biography or description of the entertainer displayed on their profile."></i></label>
        <div id="bio-editor"></div>
        <textarea name="bio" id="bio" style="display:none">{{ old('bio', $feedModel->bio ?? '') }}</textarea>
    </div>

    <div class="col-md-6">
        <label for="profile_image" class="form-label">Profile Image <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Profile photo displayed on the entertainer's feed card."></i></label>
        <input type="file" name="profile_image" id="profile_image" class="form-control" accept="image/*">
        @if(!empty($feedModel?->profile_image))
            <div class="mt-3 d-flex align-items-center gap-3">
                <img src="{{ asset('uploads/' . $feedModel->profile_image) }}" alt="{{ $feedModel->name }}" style="width:72px;height:72px;object-fit:cover;border-radius:50%;">
                <small class="text-muted">Current profile image</small>
            </div>
        @endif
    </div>

    <div class="col-md-6 d-flex align-items-center">
        <div class="form-check mt-4">
            <input type="hidden" name="is_active" value="0">
            <input class="form-check-input" type="checkbox" value="1" id="is_active" name="is_active" @checked(old('is_active', isset($feedModel) ? $feedModel->is_active : true))>
            <label class="form-check-label" for="is_active">
                Active and visible in feed <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="When enabled, this entertainer profile is publicly visible in the feed."></i>
            </label>
        </div>
    </div>

    <div class="col-12">
        <label class="form-label mb-2">Performance Dates <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Dates when this entertainer is scheduled to perform at the venue. Past dates are hidden automatically from their public profile."></i></label>
        <p class="text-muted mb-2">Build a custom roster by adding as many dates as needed. Past dates will be hidden from the public profile automatically.</p>

        <div class="border rounded p-3" id="performanceDateRoster">
            <div id="performanceDateRows" class="d-grid" style="gap:10px;">
                @foreach($performanceDates as $date)
                    <div class="performance-date-row d-flex align-items-center gap-2">
                        <input type="date" name="performance_dates[]" class="form-control" value="{{ $date }}">
                        <button type="button" class="btn btn-outline-danger remove-performance-date" title="Remove date">Remove</button>
                    </div>
                @endforeach
            </div>
            <button type="button" id="addPerformanceDate" class="btn btn-outline-primary btn-sm mt-3">Add Another Date</button>
        </div>
    </div>
</div>

<div class="mt-4 d-flex gap-2 flex-wrap">
    <button type="submit" class="btn btn-primary">Save Entertainer</button>
    <a href="{{ route('admin.feed-model.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const addDateButton = document.getElementById('addPerformanceDate');
        const dateRows = document.getElementById('performanceDateRows');
        const removeLabel = 'Remove';

        const bindRemoveActions = () => {
            dateRows.querySelectorAll('.remove-performance-date').forEach((button) => {
                button.onclick = function () {
                    const rows = dateRows.querySelectorAll('.performance-date-row');
                    if (rows.length === 1) {
                        rows[0].querySelector('input').value = '';
                        return;
                    }
                    this.closest('.performance-date-row').remove();
                };
            });
        };

        addDateButton.addEventListener('click', function () {
            const row = document.createElement('div');
            row.className = 'performance-date-row d-flex align-items-center gap-2';
            row.innerHTML = '<input type="date" name="performance_dates[]" class="form-control"><button type="button" class="btn btn-outline-danger remove-performance-date" title="Remove date">' + removeLabel + '</button>';
            dateRows.appendChild(row);
            bindRemoveActions();
        });

        bindRemoveActions();
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
    var bioTA = document.getElementById('bio');
    var quillBio = new Quill('#bio-editor', {
        theme: 'snow',
        placeholder: 'Short profile bio for this entertainer...',
        modules: { toolbar: [['bold','italic','underline'],[{'list':'ordered'},{'list':'bullet'}],['link','clean']] }
    });
    if (bioTA && bioTA.value) quillBio.root.innerHTML = bioTA.value;
    var bioForm = bioTA ? bioTA.closest('form') : null;
    if (bioForm) bioForm.addEventListener('submit', function() {
        bioTA.value = quillBio.root.innerHTML === '<p><br></p>' ? '' : quillBio.root.innerHTML;
    });
});
</script>
@endpush