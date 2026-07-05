@extends('admin.main')

@section('content')
@php
    $websiteOptions = $managers
        ->flatMap(fn ($manager) => $manager->managedWebsites)
        ->unique('id')
        ->sortBy('name')
        ->values();

    $roleOptions = $managers
        ->pluck('websiteRole')
        ->filter()
        ->unique('id')
        ->sortBy('name')
        ->values();
@endphp
<link rel="stylesheet" href="{{ asset('public/assets/main.css') }}">
<link rel="stylesheet" href="{{ asset('public/assets/base.css') }}">

<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-xxl-12 mb-6 order-0">
                <div class="app-main__inner">
                    <div class="app-page-title mt-4" data-step="" data-title="" data-intro="">
                        <div class="page-title-wrapper">
                            <div class="page-title-heading">

                                <div class="page-title-icon">
                                    <i class="fas fa-user-shield icon-gradient bg-arielle-smile"></i>
                                </div>

                                <div>
                                    <span class="text-capitalize">
                                        Manager Users
                                    </span>
                                </div>

                            </div>
                            <div class="page-title-actions">
                            </div>
                        </div>
                        <div class="page-title-subheading opacity-10 mt-3"
                            style="white-space: nowrap; overflow-x: auto;">
                            <nav class="" aria-label="breadcrumb">
                                <ol class="breadcrumb" style="float: left">

                                    <li class="breadcrumb-item opacity-10">
                                        <a href="#">
                                            <i class="fas fa-home" role="img" aria-hidden="true"></i>
                                            <span class="visually-hidden">Home</span>
                                        </a>
                                        <i class="fas fa-chevron-right ms-1"></i>
                                    </li>

                                    <li class="breadcrumb-item ">
                                        Setting
                                        <i class="fas fa-chevron-right ms-1"></i>
                                    </li>
                                    <li class="active breadcrumb-item" aria-current="page">
                                        Manager Users
                                    </li>
                                </ol>

                                <div class="btn-group" role="group" aria-label="Basic example" style="float: right">
                                    <a href="{{ route('admin.manager-users.create') }}" class="btn btn-primary">Add Manager User</a>
                                </div>
                            </nav>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label">Search</label>
                                    <input type="search" class="form-control manager-table-search" placeholder="Search name, email, role, club">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Website / Club</label>
                                    <select class="form-select manager-table-website-filter">
                                        <option value="">All clubs</option>
                                        @foreach($websiteOptions as $website)
                                            <option value="{{ $website->id }}">{{ $website->name }}</option>
                                        @endforeach
                                        <option value="none">No clubs assigned</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Role</label>
                                    <select class="form-select manager-table-role-filter">
                                        <option value="">All roles</option>
                                        @foreach($roleOptions as $role)
                                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                                        @endforeach
                                        <option value="none">Unassigned</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Sort by</label>
                                    <select class="form-select manager-table-sort-by">
                                        <option value="1">Name</option>
                                        <option value="2">Email</option>
                                        <option value="3">Role</option>
                                        <option value="4">Clubs Allocated</option>
                                        <option value="5" selected>Date</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Order</label>
                                    <select class="form-select manager-table-sort-order">
                                        <option value="desc" selected>Newest / Z-A</option>
                                        <option value="asc">Oldest / A-Z</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <ul class="nav nav-tabs mb-3" id="managerTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="active-tab" data-bs-toggle="tab" data-bs-target="#activeManagers" type="button" role="tab">Active Managers</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="inactive-tab" data-bs-toggle="tab" data-bs-target="#inactiveManagers" type="button" role="tab">Archived Managers</button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        {{-- ACTIVE --}}
                        <div class="tab-pane fade show active" id="activeManagers" role="tabpanel">
                            <div class="row">
                                <div class="col-lg">
                                    <div class="card-shadow-primary card-border text-white mb-3 card bg-primary p-2">
                                        <table class="table" id="activeManagersTable">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Role</th>
                                                    <th>Clubs Allocated</th>
                                                    <th>Created At</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $i = 1; @endphp
                                                @foreach ($managers as $manager)
                                                    @if (!$manager->deleted_at)
                                                    <tr data-role-id="{{ $manager->website_role_id ?: '' }}" data-website-ids="{{ $manager->managedWebsites->pluck('id')->join(',') }}" data-club-count="{{ $manager->managedWebsites->count() }}" data-search="{{ strtolower(trim($manager->name . ' ' . $manager->email . ' ' . optional($manager->websiteRole)->name . ' ' . $manager->managedWebsites->pluck('name')->implode(' '))) }}">
                                                        <td>{{ $i++ }}</td>
                                                        <td>{{ $manager->name }}</td>
                                                        <td>{{ $manager->email }}</td>
                                                        <td>{{ $manager->websiteRole->name ?? 'Unassigned' }}</td>
                                                        <td data-order="{{ $manager->managedWebsites->count() }}">
                                                            @if($manager->managedWebsites->isEmpty())
                                                                <span class="text-muted">None</span>
                                                            @else
                                                                <span class="badge bg-info text-dark">{{ $manager->managedWebsites->count() }} club(s)</span>
                                                                <button type="button" class="btn btn-link btn-sm p-0 ms-1 text-info"
                                                                    data-bs-toggle="popover"
                                                                    data-bs-trigger="click"
                                                                    data-bs-placement="bottom"
                                                                    data-bs-html="true"
                                                                    data-bs-content="{{ $manager->managedWebsites->pluck('name')->map(fn($n) => '<div>'.e($n).'</div>')->implode('') }}"
                                                                    title="Allocated Clubs">
                                                                    <i class="fas fa-info-circle"></i>
                                                                </button>
                                                            @endif
                                                        </td>
                                                        <td data-order="{{ $manager->created_at?->timestamp ?? 0 }}">{{ $manager->created_at->timezone('America/Los_Angeles')->format('M d, Y h:i A') }} PT</td>
                                                        <td>
                                                            <a href="{{ route('admin.manager-users.edit', $manager->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                                            <form action="{{ route('admin.manager-users.archive', $manager->id) }}" method="POST" style="display:inline;">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-secondary"
                                                                    onclick="return confirm('Archive this manager user?')">Archive</button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ARCHIVED --}}
                        <div class="tab-pane fade" id="inactiveManagers" role="tabpanel">
                            <div class="row">
                                <div class="col-lg">
                                    <div class="card-shadow-primary card-border text-white mb-3 card bg-secondary p-2">
                                        <table class="table" id="inactiveManagersTable">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Role</th>
                                                    <th>Clubs Allocated</th>
                                                    <th>Archived At</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $j = 1; @endphp
                                                @foreach ($managers as $manager)
                                                    @if ($manager->deleted_at)
                                                    <tr data-role-id="{{ $manager->website_role_id ?: '' }}" data-website-ids="{{ $manager->managedWebsites->pluck('id')->join(',') }}" data-club-count="{{ $manager->managedWebsites->count() }}" data-search="{{ strtolower(trim($manager->name . ' ' . $manager->email . ' ' . optional($manager->websiteRole)->name . ' ' . $manager->managedWebsites->pluck('name')->implode(' '))) }}">
                                                        <td>{{ $j++ }}</td>
                                                        <td>{{ $manager->name }}</td>
                                                        <td>{{ $manager->email }}</td>
                                                        <td>{{ $manager->websiteRole->name ?? 'Unassigned' }}</td>
                                                        <td data-order="{{ $manager->managedWebsites->count() }}">
                                                            @if($manager->managedWebsites->isEmpty())
                                                                <span class="text-muted">None</span>
                                                            @else
                                                                <span class="badge bg-info text-dark">{{ $manager->managedWebsites->count() }} club(s)</span>
                                                                <button type="button" class="btn btn-link btn-sm p-0 ms-1 text-info"
                                                                    data-bs-toggle="popover"
                                                                    data-bs-trigger="click"
                                                                    data-bs-placement="bottom"
                                                                    data-bs-html="true"
                                                                    data-bs-content="{{ $manager->managedWebsites->pluck('name')->map(fn($n) => '<div>'.e($n).'</div>')->implode('') }}"
                                                                    title="Allocated Clubs">
                                                                    <i class="fas fa-info-circle"></i>
                                                                </button>
                                                            @endif
                                                        </td>
                                                        <td data-order="{{ $manager->deleted_at?->timestamp ?? 0 }}">{{ $manager->deleted_at->timezone('America/Los_Angeles')->format('M d, Y h:i A') }} PT</td>
                                                        <td>
                                                            <form action="{{ route('admin.manager-users.archive', $manager->id) }}" method="POST" style="display:inline;">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-success"
                                                                    onclick="return confirm('Restore this manager user?')">Restore</button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

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
    const activeManagersTable = new DataTable('#activeManagersTable', {
        order: [[5, 'desc']],
        pageLength: 10
    });

    const inactiveManagersTable = new DataTable('#inactiveManagersTable', {
        order: [[5, 'desc']],
        pageLength: 10
    });

    const filterState = {
        search: '',
        websiteId: '',
        roleId: '',
        sortBy: 5,
        sortOrder: 'desc'
    };

    function matchesManagerFilter(rowData) {
        const searchText = (rowData.searchText || '').toLowerCase();
        const websiteIds = String(rowData.websiteIds || '').split(',').map(function (value) {
            return value.trim();
        }).filter(Boolean);
        const matchesSearch = !filterState.search || searchText.includes(filterState.search);
        const matchesWebsite = !filterState.websiteId
            || (filterState.websiteId === 'none' ? websiteIds.length === 0 : websiteIds.includes(String(filterState.websiteId)));
        const matchesRole = !filterState.roleId
            || (filterState.roleId === 'none' ? !rowData.roleId : String(rowData.roleId) === String(filterState.roleId));

        return matchesSearch && matchesWebsite && matchesRole;
    }

    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        if (settings.nTable.id !== 'activeManagersTable' && settings.nTable.id !== 'inactiveManagersTable') {
            return true;
        }

        const row = settings.aoData[dataIndex].nTr;
        if (!row) {
            return true;
        }

        return matchesManagerFilter({
            websiteIds: row.dataset.websiteIds || '',
            roleId: row.dataset.roleId || '',
            searchText: row.dataset.search || ''
        });
    });

    function applyManagerTableSorting() {
        activeManagersTable.order([[filterState.sortBy, filterState.sortOrder]]).draw();
        inactiveManagersTable.order([[filterState.sortBy, filterState.sortOrder]]).draw();
    }

    $('.manager-table-search').on('input', function () {
        filterState.search = String(this.value || '').trim().toLowerCase();
        activeManagersTable.draw();
        inactiveManagersTable.draw();
    });

    $('.manager-table-website-filter').on('change', function () {
        filterState.websiteId = this.value;
        activeManagersTable.draw();
        inactiveManagersTable.draw();
    });

    $('.manager-table-role-filter').on('change', function () {
        filterState.roleId = this.value;
        activeManagersTable.draw();
        inactiveManagersTable.draw();
    });

    $('.manager-table-sort-by').on('change', function () {
        filterState.sortBy = parseInt(this.value, 10) || 5;
        applyManagerTableSorting();
    });

    $('.manager-table-sort-order').on('change', function () {
        filterState.sortOrder = this.value === 'asc' ? 'asc' : 'desc';
        applyManagerTableSorting();
    });

    $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function () {
        activeManagersTable.columns.adjust();
        inactiveManagersTable.columns.adjust();
    });

    applyManagerTableSorting();

    document.querySelectorAll('[data-bs-toggle="popover"]').forEach(function (el) {
        new bootstrap.Popover(el, { sanitize: false });
    });

    // Close popover when clicking outside
    document.addEventListener('click', function (e) {
        document.querySelectorAll('[data-bs-toggle="popover"]').forEach(function (el) {
            if (!el.contains(e.target)) {
                bootstrap.Popover.getInstance(el)?.hide();
            }
        });
    });
});
</script>
@endsection
