@extends('admin.main')

@section('content')
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
                                                    <tr>
                                                        <td>{{ $i++ }}</td>
                                                        <td>{{ $manager->name }}</td>
                                                        <td>{{ $manager->email }}</td>
                                                        <td>{{ $manager->websiteRole->name ?? '<span class="text-muted">Unassigned</span>' }}</td>
                                                        <td>
                                                            @if($manager->managedWebsites->isEmpty())
                                                                <span class="text-muted">None</span>
                                                            @else
                                                                <span class="badge bg-info text-dark">{{ $manager->managedWebsites->count() }} club(s)</span>
                                                                <div class="mt-1">
                                                                    @foreach($manager->managedWebsites->take(3) as $w)
                                                                        <small class="d-block text-white-50">{{ $w->name }}</small>
                                                                    @endforeach
                                                                    @if($manager->managedWebsites->count() > 3)
                                                                        <small class="text-white-50">+{{ $manager->managedWebsites->count() - 3 }} more</small>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        </td>
                                                        <td>{{ $manager->created_at->timezone('America/Los_Angeles')->format('M d, Y h:i A') }} PT</td>
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
                                                    <tr>
                                                        <td>{{ $j++ }}</td>
                                                        <td>{{ $manager->name }}</td>
                                                        <td>{{ $manager->email }}</td>
                                                        <td>{{ $manager->websiteRole->name ?? '<span class="text-muted">Unassigned</span>' }}</td>
                                                        <td>
                                                            <span class="badge bg-info text-dark">{{ $manager->managedWebsites->count() }} club(s)</span>
                                                        </td>
                                                        <td>{{ $manager->deleted_at->timezone('America/Los_Angeles')->format('M d, Y h:i A') }} PT</td>
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
@endsection
