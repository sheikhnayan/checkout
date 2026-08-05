@extends('admin.main')

@section('content')
@php
    $websiteOptions = $roles
        ->pluck('website')
        ->filter()
        ->unique('id')
        ->sortBy('name')
        ->values();
@endphp
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Website Roles</h4>
            <a href="{{ route('admin.website-roles.create') }}" class="btn btn-primary">Create Role</a>
        </div>

        @if(auth()->user()->isAdmin())
            <div class="alert alert-info">
                You may see multiple <strong>Website Admin</strong> roles here because each website has its own separate role.
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <div class="row g-3 align-items-end mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Search</label>
                        <input type="search" class="form-control role-table-search" placeholder="Search role, description, website">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Website</label>
                        <select class="form-select role-table-website-filter">
                            <option value="">All websites</option>
                            @foreach($websiteOptions as $website)
                                <option value="{{ $website->id }}">{{ $website->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Type</label>
                        <select class="form-select role-table-type-filter">
                            <option value="">All types</option>
                            <option value="website_admin">Website Admin</option>
                            <option value="system">System</option>
                            <option value="custom">Custom</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Sort by</label>
                        <select class="form-select role-table-sort-by">
                            <option value="0" selected>Role</option>
                            <option value="1">Website</option>
                            <option value="2">Permissions</option>
                            <option value="3">Users</option>
                            <option value="4">Type</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">Order</label>
                        <select class="form-select role-table-sort-order">
                            <option value="asc" selected>A-Z</option>
                            <option value="desc">Z-A</option>
                        </select>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table" id="websiteRolesTable">
                        <thead>
                            <tr>
                                <th>Role</th>
                                <th>Website</th>
                                <th>Permissions</th>
                                <th>Users</th>
                                <th>Type</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($roles as $role)
                                @php
                                    $typeKey = $role->is_website_admin ? 'website_admin' : ($role->is_system ? 'system' : 'custom');
                                    $typeOrder = $role->is_website_admin ? 1 : ($role->is_system ? 2 : 3);
                                    $searchText = strtolower(trim($role->name . ' ' . ($role->description ?? '') . ' ' . optional($role->website)->name));
                                @endphp
                                <tr data-website-id="{{ $role->website_id }}" data-type-key="{{ $typeKey }}" data-search="{{ $searchText }}">
                                    <td>
                                        <strong>{{ $role->name }}</strong>
                                        @if($role->description)
                                            <div class="small text-muted">{{ $role->description }}</div>
                                        @endif
                                    </td>
                                    <td>{{ $role->website->name ?? 'N/A' }}</td>
                                    <td data-order="{{ $role->permissions->count() }}">{{ $role->permissions->count() }}</td>
                                    <td data-order="{{ $role->users->count() }}">{{ $role->users->count() }}</td>
                                    <td data-order="{{ $typeOrder }}">
                                        @if($role->is_website_admin)
                                            <span class="badge bg-info">Website Admin</span>
                                        @elseif($role->is_system)
                                            <span class="badge bg-secondary">System</span>
                                        @else
                                            <span class="badge bg-primary">Custom</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.website-roles.edit', $role->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                        @if(!$role->is_system && !$role->is_website_admin)
                                            <form action="{{ route('admin.website-roles.archive', $role->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this role?')">Delete</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No roles found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const rolesTable = new DataTable('#websiteRolesTable', {
        order: [[0, 'asc']],
        pageLength: 10
    });

    const filterState = {
        search: '',
        websiteId: '',
        typeKey: '',
        sortBy: 0,
        sortOrder: 'asc'
    };

    function matchesRoleFilter(rowData) {
        const searchText = (rowData.searchText || '').toLowerCase();
        const matchesSearch = !filterState.search || searchText.includes(filterState.search);
        const matchesWebsite = !filterState.websiteId || String(rowData.websiteId) === String(filterState.websiteId);
        const matchesType = !filterState.typeKey || String(rowData.typeKey) === String(filterState.typeKey);

        return matchesSearch && matchesWebsite && matchesType;
    }

    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        if (settings.nTable.id !== 'websiteRolesTable') {
            return true;
        }

        const row = settings.aoData[dataIndex].nTr;
        if (!row) {
            return true;
        }

        return matchesRoleFilter({
            websiteId: row.dataset.websiteId || '',
            typeKey: row.dataset.typeKey || '',
            searchText: row.dataset.search || ''
        });
    });

    function applyRoleTableSorting() {
        rolesTable.order([[filterState.sortBy, filterState.sortOrder]]).draw();
    }

    $('.role-table-search').on('input', function () {
        filterState.search = String(this.value || '').trim().toLowerCase();
        rolesTable.draw();
    });

    $('.role-table-website-filter').on('change', function () {
        filterState.websiteId = this.value;
        rolesTable.draw();
    });

    $('.role-table-type-filter').on('change', function () {
        filterState.typeKey = this.value;
        rolesTable.draw();
    });

    $('.role-table-sort-by').on('change', function () {
        filterState.sortBy = parseInt(this.value, 10) || 0;
        applyRoleTableSorting();
    });

    $('.role-table-sort-order').on('change', function () {
        filterState.sortOrder = this.value === 'desc' ? 'desc' : 'asc';
        applyRoleTableSorting();
    });

    applyRoleTableSorting();
});
</script>
@endsection
