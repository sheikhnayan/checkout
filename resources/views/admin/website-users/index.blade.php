@extends('admin.main')

@section('content')
        @php
            $websiteOptions = $users
                ->filter(fn ($user) => $user->website)
                ->pluck('website')
                ->unique('id')
                ->sortBy('name')
                ->values();

            $roleOptions = $users
                ->pluck('websiteRole')
                ->filter()
                ->map(function ($role) {
                    return (object) [
                        'key' => $role->is_website_admin ? 'website_admin' : ('role_' . $role->id),
                        'label' => $role->is_website_admin ? 'Website Admin' : $role->name,
                        'is_website_admin' => (bool) $role->is_website_admin,
                    ];
                })
                ->unique('key')
                ->sortBy('label')
                ->values();
        @endphp
        <!-- Include custom CSS -->
        <link rel="stylesheet" href="{{ asset('public/assets/main.css') }}">
        <link rel="stylesheet" href="{{ asset('public/assets/base.css') }}">
        <link rel="stylesheet" href="{{ asset('public/assets/forms-wizard.css') }}">
        <link rel="stylesheet" href="https://cdn.linearicons.com/free/1.0.0/icon-font.min.css">

        <!-- Content wrapper -->
        <div class="content-wrapper">
            <!-- Content -->
            <div class="container-xxl flex-grow-1 container-p-y">
                <div class="row">
                    <div class="col-xxl-12 mb-6 order-0">
                        <div class="app-main__inner">
                            <div class="app-page-title mt-4" data-step="" data-title="" data-intro="">
                                <div class="page-title-wrapper">
                                    <div class="page-title-heading">

                                        <div class="page-title-icon">
                                            <i class="fas fa-id-card icon-gradient bg-arielle-smile"></i>
                                        </div>

                                        <div>
                                            <span class="text-capitalize">
                                                Website Users
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
                                                Website Users
                                            </li>

                                        </ol>

                                        <div class="btn-group" role="group" aria-label="Basic example" style="float: right">
                                            <a href="{{ route('admin.website-users.create') }}" class="btn btn-primary">Add Website User</a>
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
                                            <input type="search" class="form-control user-table-search" placeholder="Search name, email, website, role">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Website</label>
                                            <select class="form-select user-table-website-filter">
                                                <option value="">All websites</option>
                                                @foreach($websiteOptions as $website)
                                                    <option value="{{ $website->id }}">{{ $website->name }}</option>
                                                @endforeach
                                                <option value="none">No website assigned</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Role</label>
                                            <select class="form-select user-table-role-filter">
                                                <option value="">All roles</option>
                                                @foreach($roleOptions as $role)
                                                    <option value="{{ $role->key }}">{{ $role->label }}</option>
                                                @endforeach
                                                <option value="none">Unassigned</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Sort by</label>
                                            <select class="form-select user-table-sort-by">
                                                <option value="1">Name</option>
                                                <option value="2">Email</option>
                                                <option value="3">Website</option>
                                                <option value="4">Role</option>
                                                <option value="5" selected>Created At</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Order</label>
                                            <select class="form-select user-table-sort-order">
                                                <option value="desc" selected>Newest / Z-A</option>
                                                <option value="asc">Oldest / A-Z</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tabs for Active and Inactive Users -->
                            <ul class="nav nav-tabs mb-3" id="userTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="active-tab" data-bs-toggle="tab" data-bs-target="#activeUsers" type="button" role="tab" aria-controls="activeUsers" aria-selected="true">
                                        Active Users
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="inactive-tab" data-bs-toggle="tab" data-bs-target="#inactiveUsers" type="button" role="tab" aria-controls="inactiveUsers" aria-selected="false">
                                        Inactive Users
                                    </button>
                                </li>
                            </ul>
                            <div class="tab-content" id="userTabsContent">
                                <div class="tab-pane fade show active" id="activeUsers" role="tabpanel" aria-labelledby="active-tab">
                                    <div class="row">
                                        <div class="col-lg">
                                            <div class="card-shadow-primary card-border text-white mb-3 card bg-primary p-2">
                                                <table class="table" id="activeUsersTable">
                                                    <thead>
                                                        <tr>
                                                            <th>SI</th>
                                                            <th>Name</th>
                                                            <th>Email</th>
                                                            <th>Website</th>
                                                            <th>Role</th>
                                                            <th>Created At</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php $activeIndex = 1; @endphp
                                                        @foreach ($users as $user)
                                                            @if (!$user->deleted_at)
                                                            <tr data-website-id="{{ $user->website_id ?: '' }}" data-role-id="{{ $user->website_role_id ?: '' }}" data-role-key="{{ $user->websiteRole?->is_website_admin ? 'website_admin' : ('role_' . $user->website_role_id) }}" data-search="{{ strtolower(trim($user->name . ' ' . $user->email . ' ' . optional($user->website)->name . ' ' . optional($user->website)->domain . ' ' . optional($user->websiteRole)->name)) }}">
                                                                <td>{{ $activeIndex++ }}</td>
                                                                <td>{{ $user->name }}</td>
                                                                <td>{{ $user->email }}</td>
                                                                <td>
                                                                    @if($user->website)
                                                                        {{ $user->website->name }}
                                                                        <small class="text-muted d-block">{{ $user->website->domain }}</small>
                                                                    @else
                                                                        <span class="text-muted">No website assigned</span>
                                                                    @endif
                                                                </td>
                                                                <td>{{ $user->websiteRole->name ?? 'Unassigned' }}</td>
                                                                <td data-order="{{ $user->created_at?->timestamp ?? 0 }}">{{ $user->created_at->timezone('America/Los_Angeles')->format('M d, Y h:i A') }} PT</td>
                                                                <td>
                                                                    <a href="{{ route('admin.website-users.edit', $user->id) }}" class="btn btn-primary">Edit</a>
                                                                    <form action="{{ route('admin.website-users.archive', $user->id) }}" method="POST" style="display:inline;">
                                                                        @csrf
                                                                        <button type="submit" class="btn btn-warning" onclick="return confirm('Are you sure you want to archive this user?');">
                                                                            Archive
                                                                        </button>
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
                                <div class="tab-pane fade" id="inactiveUsers" role="tabpanel" aria-labelledby="inactive-tab">
                                    <div class="row">
                                        <div class="col-lg">
                                            <div class="card-shadow-primary card-border text-white mb-3 card bg-secondary p-2">
                                                <table class="table" id="inactiveUsersTable">
                                                    <thead>
                                                        <tr>
                                                            <th>SI</th>
                                                            <th>Name</th>
                                                            <th>Email</th>
                                                            <th>Website</th>
                                                            <th>Role</th>
                                                            <th>Created At</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php $inactiveIndex = 1; @endphp
                                                        @foreach ($users as $user)
                                                            @if ($user->deleted_at)
                                                            <tr data-website-id="{{ $user->website_id ?: '' }}" data-role-id="{{ $user->website_role_id ?: '' }}" data-role-key="{{ $user->websiteRole?->is_website_admin ? 'website_admin' : ('role_' . $user->website_role_id) }}" data-search="{{ strtolower(trim($user->name . ' ' . $user->email . ' ' . optional($user->website)->name . ' ' . optional($user->website)->domain . ' ' . optional($user->websiteRole)->name)) }}">
                                                                <td>{{ $inactiveIndex++ }}</td>
                                                                <td>{{ $user->name }}</td>
                                                                <td>{{ $user->email }}</td>
                                                                <td>
                                                                    @if($user->website)
                                                                        {{ $user->website->name }}
                                                                        <small class="text-muted d-block">{{ $user->website->domain }}</small>
                                                                    @else
                                                                        <span class="text-muted">No website assigned</span>
                                                                    @endif
                                                                </td>
                                                                <td>{{ $user->websiteRole->name ?? 'Unassigned' }}</td>
                                                                <td data-order="{{ $user->created_at?->timestamp ?? 0 }}">{{ $user->created_at->timezone('America/Los_Angeles')->format('M d, Y h:i A') }} PT</td>
                                                                <td>
                                                                    <form action="{{ route('admin.website-users.archive', $user->id) }}" method="POST" style="display:inline;">
                                                                        @csrf
                                                                        <button type="submit" class="btn btn-success" onclick="return confirm('Unarchive this user?');">
                                                                            Unarchive
                                                                        </button>
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
            <!-- / Content -->

            <!-- Include DataTables and jQuery CDN (jQuery first, then DataTables, then Bootstrap) -->
            <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
            <script>
                $(document).ready(function() {
                    const activeUsersTable = new DataTable('#activeUsersTable', {
                        order: [[5, 'desc']],
                        pageLength: 10
                    });

                    const inactiveUsersTable = new DataTable('#inactiveUsersTable', {
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

                    function matchesFilter(rowData) {
                        const searchText = (rowData.searchText || '').toLowerCase();
                        const matchesSearch = !filterState.search || searchText.includes(filterState.search);
                        const matchesWebsite = !filterState.websiteId
                            || (filterState.websiteId === 'none' ? !rowData.websiteId : String(rowData.websiteId) === String(filterState.websiteId));
                        const matchesRole = !filterState.roleId
                            || (filterState.roleId === 'none' ? !rowData.roleId : String(rowData.roleKey) === String(filterState.roleId));

                        return matchesSearch && matchesWebsite && matchesRole;
                    }

                    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                        if (settings.nTable.id !== 'activeUsersTable' && settings.nTable.id !== 'inactiveUsersTable') {
                            return true;
                        }

                        const row = settings.aoData[dataIndex].nTr;
                        if (!row) {
                            return true;
                        }

                        return matchesFilter({
                            websiteId: row.dataset.websiteId || '',
                            roleId: row.dataset.roleId || '',
                            roleKey: row.dataset.roleKey || '',
                            searchText: row.dataset.search || ''
                        });
                    });

                    function applyUserTableSorting() {
                        activeUsersTable.order([[filterState.sortBy, filterState.sortOrder]]).draw();
                        inactiveUsersTable.order([[filterState.sortBy, filterState.sortOrder]]).draw();
                    }

                    $('.user-table-search').on('input', function() {
                        filterState.search = String(this.value || '').trim().toLowerCase();
                        activeUsersTable.draw();
                        inactiveUsersTable.draw();
                    });

                    $('.user-table-website-filter').on('change', function() {
                        filterState.websiteId = this.value;
                        activeUsersTable.draw();
                        inactiveUsersTable.draw();
                    });

                    $('.user-table-role-filter').on('change', function() {
                        filterState.roleId = this.value;
                        activeUsersTable.draw();
                        inactiveUsersTable.draw();
                    });

                    $('.user-table-sort-by').on('change', function() {
                        filterState.sortBy = parseInt(this.value, 10) || 5;
                        applyUserTableSorting();
                    });

                    $('.user-table-sort-order').on('change', function() {
                        filterState.sortOrder = this.value === 'asc' ? 'asc' : 'desc';
                        applyUserTableSorting();
                    });

                    $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function() {
                        activeUsersTable.columns.adjust();
                        inactiveUsersTable.columns.adjust();
                    });

                    applyUserTableSorting();
                });
            </script>
        @endsection