@extends('admin.main')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="mb-3">Edit Website Role</h4>

        <div class="card">
            <div class="card-body">
                @if($role->is_website_admin)
                    <div class="alert alert-info">Website Admin role is system-managed and cannot be edited.</div>
                @endif

                <form method="POST" action="{{ route('admin.website-roles.update', $role->id) }}">
                    @csrf
                    @method('PUT')

                    @if(auth()->user()->isAdmin())
                        <div class="mb-3">
                            <label class="form-label">Website</label>
                            <select class="form-control" disabled>
                                @foreach($websites as $website)
                                    <option value="{{ $website->id }}" @selected((int) $selectedWebsiteId === (int) $website->id)>
                                        {{ $website->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">Role Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $role->name) }}" @disabled($role->is_website_admin) required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2" @disabled($role->is_website_admin)>{{ old('description', $role->description) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Permissions</label>
                        @php $selectedPermissionIds = old('permission_ids', $role->permissions->pluck('id')->all()); @endphp
                        @foreach($permissionsByModule as $module => $permissions)
                            <div class="border rounded p-2 mb-2 permission-module" data-module="{{ $module }}">
                                <div class="d-flex justify-content-between align-items-center gap-2">
                                    <strong>{{ ucfirst($module) }}</strong>
                                    <label class="form-check-label mb-0">
                                        <input type="checkbox" class="form-check-input module-select-all" data-module="{{ $module }}" @disabled($role->is_website_admin)>
                                        Select All in {{ ucfirst($module) }}
                                    </label>
                                </div>
                                <div class="row mt-2">
                                    @foreach($permissions as $permission)
                                        <div class="col-md-4 mb-2">
                                            <label class="form-check-label">
                                                <input type="checkbox" class="form-check-input module-permission" data-module="{{ $module }}" name="permission_ids[]" value="{{ $permission->id }}" @checked(in_array($permission->id, $selectedPermissionIds)) @disabled($role->is_website_admin)>
                                                {{ $permission->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <button class="btn btn-primary" type="submit" @disabled($role->is_website_admin)>Update Role</button>
                    <a href="{{ route('admin.website-roles.index') }}" class="btn btn-secondary">Back</a>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const moduleToggles = document.querySelectorAll('.module-select-all');

    function syncModuleToggle(moduleName) {
        const moduleCheckboxes = Array.from(document.querySelectorAll('.module-permission[data-module="' + moduleName + '"]'));
        const toggle = document.querySelector('.module-select-all[data-module="' + moduleName + '"]');

        if (!toggle || moduleCheckboxes.length === 0) {
            return;
        }

        const enabledCheckboxes = moduleCheckboxes.filter(cb => !cb.disabled);
        if (enabledCheckboxes.length === 0) {
            toggle.checked = false;
            toggle.indeterminate = false;
            return;
        }

        toggle.checked = enabledCheckboxes.every(cb => cb.checked);
        toggle.indeterminate = !toggle.checked && enabledCheckboxes.some(cb => cb.checked);
    }

    moduleToggles.forEach(function (toggle) {
        const moduleName = toggle.getAttribute('data-module');

        toggle.addEventListener('change', function () {
            document.querySelectorAll('.module-permission[data-module="' + moduleName + '"]').forEach(function (cb) {
                if (!cb.disabled) {
                    cb.checked = toggle.checked;
                }
            });
            syncModuleToggle(moduleName);
        });

        document.querySelectorAll('.module-permission[data-module="' + moduleName + '"]').forEach(function (cb) {
            cb.addEventListener('change', function () {
                syncModuleToggle(moduleName);
            });
        });

        syncModuleToggle(moduleName);
    });
});
</script>
@endsection
