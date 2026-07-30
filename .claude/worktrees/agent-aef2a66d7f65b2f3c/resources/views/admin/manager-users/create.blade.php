@extends('admin.main')

@section('content')
<link rel="stylesheet" href="{{ asset('user/extra.css') }}">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<style>
    label { color: #000 !important; }

    .club-checkbox-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 0.5rem;
    }

    .club-checkbox-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(255,255,255,0.08);
        border-radius: 6px;
        padding: 0.45rem 0.75rem;
        cursor: pointer;
        transition: background 0.15s;
    }

    .club-checkbox-item:hover {
        background: rgba(255,255,255,0.18);
    }

    .club-checkbox-item input[type="checkbox"] {
        width: 16px;
        height: 16px;
        flex-shrink: 0;
    }
</style>

<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-xxl-12 mb-6 order-0">
                <div class="app-main__inner">

                    <div class="app-page-title mt-4">
                        <div class="page-title-wrapper">
                            <div class="page-title-heading">
                                <div class="page-title-icon">
                                    <i class="fas fa-user-plus icon-gradient bg-arielle-smile"></i>
                                </div>
                                <div><span class="text-capitalize">Create Manager User</span></div>
                            </div>
                        </div>
                        <div class="page-title-subheading opacity-10 mt-3">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb" style="float:left">
                                    <li class="breadcrumb-item opacity-10">
                                        <a href="#"><i class="fas fa-home"></i></a>
                                        <i class="fas fa-chevron-right ms-1"></i>
                                    </li>
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.manager-users.index') }}">Manager Users</a>
                                        <i class="fas fa-chevron-right ms-1"></i>
                                    </li>
                                    <li class="active breadcrumb-item" aria-current="page">Create</li>
                                </ol>
                            </nav>
                        </div>
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-lg">
                            <div class="card-shadow-primary card-border text-white mb-3 card bg-primary">
                                <form action="{{ route('admin.manager-users.store') }}" method="POST">
                                    @csrf

                                    <div class="card-body">

                                        {{-- ── SECTION: Account Details ── --}}
                                        <h6 class="section-title-highlight mb-3">
                                            <i class="fas fa-user me-2"></i>Account Details
                                        </h6>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="name" class="form-label">Full Name <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The manager's full name used for identification in the admin panel."></i></label>
                                                <input type="text" name="name" id="name"
                                                    class="form-control @error('name') is-invalid @enderror"
                                                    value="{{ old('name') }}" placeholder="Enter full name" required>
                                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="email" class="form-label">Email Address <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Login email address for this manager's admin account."></i></label>
                                                <input type="email" name="email" id="email"
                                                    class="form-control @error('email') is-invalid @enderror"
                                                    value="{{ $errors->has('email') ? '' : old('email') }}"
                                                    placeholder="Enter email address" required>
                                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="password" class="form-label">Password <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Account login password. Minimum 8 characters."></i></label>
                                                <input type="password" name="password" id="password"
                                                    class="form-control @error('password') is-invalid @enderror"
                                                    placeholder="Enter password" required>
                                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="password_confirmation" class="form-label">Confirm Password <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="Re-enter the password to confirm it matches."></i></label>
                                                <input type="password" name="password_confirmation" id="password_confirmation"
                                                    class="form-control" placeholder="Confirm password" required>
                                            </div>
                                        </div>

                                        {{-- ── SECTION: Portal Role ── --}}
                                        <h6 class="section-title-highlight mb-3 mt-4">
                                            <i class="fas fa-shield-alt me-2"></i>Portal Role &amp; Permissions
                                        </h6>

                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label for="website_role_id" class="form-label">Assign Role <i class="fas fa-circle-info ms-1 field-tip" data-bs-toggle="tooltip" data-bs-placement="top" title="The admin role that determines this manager's access and permissions in the panel."></i></label>
                                                <select name="website_role_id" id="website_role_id"
                                                    class="form-control @error('website_role_id') is-invalid @enderror" required>
                                                    <option value="">— Select a role —</option>
                                                    @foreach($roles as $role)
                                                        <option value="{{ $role->id }}"
                                                            {{ old('website_role_id') == $role->id ? 'selected' : '' }}>
                                                            {{ $role->name }}
                                                            @if($role->website) ({{ $role->website->name }}) @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('website_role_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>

                                        {{-- ── SECTION: Club Allocation ── --}}
                                        <h6 class="section-title-highlight mb-3 mt-4">
                                            <i class="fas fa-building me-2"></i>Allocate Clubs
                                        </h6>

                                        @error('website_ids')
                                            <div class="alert alert-danger py-2">{{ $message }}</div>
                                        @enderror

                                        <div class="mb-3">
                                            <div class="d-flex align-items-center gap-3 mb-3">
                                                <button type="button" id="selectAllClubs" class="btn btn-sm btn-outline-light">
                                                    <i class="fas fa-check-square me-1"></i> Select All
                                                </button>
                                                <button type="button" id="deselectAllClubs" class="btn btn-sm btn-outline-secondary">
                                                    <i class="fas fa-square me-1"></i> Deselect All
                                                </button>
                                                <span class="text-white-50 small" id="selectedCount">0 selected</span>
                                            </div>

                                            <div class="club-checkbox-grid">
                                                @foreach($websites as $website)
                                                    <label class="club-checkbox-item">
                                                        <input type="checkbox"
                                                            name="website_ids[]"
                                                            value="{{ $website->id }}"
                                                            class="club-checkbox"
                                                            {{ in_array($website->id, old('website_ids', [])) ? 'checked' : '' }}>
                                                        <span>
                                                            {{ $website->name }}
                                                            @if($website->domain)
                                                                <small class="d-block text-white-50">{{ $website->domain }}</small>
                                                            @endif
                                                        </span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>

                                        {{-- ── Submit ── --}}
                                        <div class="mt-4">
                                            <button type="submit" class="btn btn-success">
                                                <i class="fas fa-save"></i> Create Manager User
                                            </button>
                                            <a href="{{ route('admin.manager-users.index') }}" class="btn btn-secondary ms-2">
                                                <i class="fas fa-times"></i> Cancel
                                            </a>
                                        </div>

                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var checkboxes = document.querySelectorAll('.club-checkbox');
    var countEl    = document.getElementById('selectedCount');

    function updateCount() {
        var checked = document.querySelectorAll('.club-checkbox:checked').length;
        countEl.textContent = checked + ' selected';
    }

    checkboxes.forEach(function (cb) {
        cb.addEventListener('change', updateCount);
    });

    document.getElementById('selectAllClubs').addEventListener('click', function () {
        checkboxes.forEach(function (cb) { cb.checked = true; });
        updateCount();
    });

    document.getElementById('deselectAllClubs').addEventListener('click', function () {
        checkboxes.forEach(function (cb) { cb.checked = false; });
        updateCount();
    });

    updateCount();
})();
</script>
@endsection
