@extends('admin.main')

@section('content')
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
                                                Website User
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
                                                <a href="/admins">
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
                                                Website User
                                            </li>

                                        </ol>

                                        <div class="btn-group" role="group" aria-label="Basic example" style="float: right">
                                            <a href="{{ route('admin.website-users.create') }}" class="btn btn-primary">Add Website User</a>
                                    </nav>
                                </div>
                            </div>

                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

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
                                            <div class="card-shadow-primary card-border text-white mb-3 card bg-primary p-2" style="background: #fff !important;">
                                                <table class="table" id="activeUsersTable">
                                                    <thead>
                                                        <tr>
                                                            <th>SI</th>
                                                            <th>Name</th>
                                                            <th>Email</th>
                                                            <th>Website</th>
                                                            <th>Created At</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php $activeIndex = 1; @endphp
                                                        @foreach ($users as $user)
                                                            @if (!$user->deleted_at)
                                                            <tr>
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
                                                                <td>{{ $user->created_at->format('M d, Y H:i') }}</td>
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
                                            <div class="card-shadow-primary card-border text-white mb-3 card bg-secondary p-2" style="background: #f8f9fa !important;">
                                                <table class="table" id="inactiveUsersTable">
                                                    <thead>
                                                        <tr>
                                                            <th>SI</th>
                                                            <th>Name</th>
                                                            <th>Email</th>
                                                            <th>Website</th>
                                                            <th>Created At</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php $inactiveIndex = 1; @endphp
                                                        @foreach ($users as $user)
                                                            @if ($user->deleted_at)
                                                            <tr>
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
                                                                <td>{{ $user->created_at->format('M d, Y H:i') }}</td>
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
                    // Only initialize each table once
                    let table = new DataTable('#activeUsersTable');
                    let table2 = new DataTable('#inactiveUsersTable');
                });
            </script>
        @endsection