@php
    $selectedWebsiteId = old('website_id', $feedModel->website_id ?? ($websites->first()->id ?? null));
@endphp

<div class="row g-4">
    <div class="col-md-6">
        <label for="website_id" class="form-label">Website</label>
        <select name="website_id" id="website_id" class="form-control" required>
            @foreach($websites as $website)
                <option value="{{ $website->id }}" @selected((string) $selectedWebsiteId === (string) $website->id)>{{ $website->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label for="name" class="form-label">Model Name</label>
        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $feedModel->name ?? '') }}" placeholder="Example: Aria Vale" required>
    </div>

    <div class="col-12">
        <label for="bio" class="form-label">Bio</label>
        <textarea name="bio" id="bio" class="form-control" rows="5" placeholder="Short profile bio for this model">{{ old('bio', $feedModel->bio ?? '') }}</textarea>
    </div>

    <div class="col-md-6">
        <label for="profile_image" class="form-label">Profile Image</label>
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
            <input class="form-check-input" type="checkbox" value="1" id="is_active" name="is_active" @checked(old('is_active', isset($feedModel) ? $feedModel->is_active : true))>
            <label class="form-check-label" for="is_active">
                Active and visible in feed
            </label>
        </div>
    </div>
</div>

<div class="mt-4 d-flex gap-2 flex-wrap">
    <button type="submit" class="btn btn-primary">Save Model</button>
    <a href="{{ route('admin.feed-model.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>