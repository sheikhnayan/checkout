@extends('admin.main')

@section('content')
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
                <div class="table-responsive">
                    <table class="table">
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
                                <tr>
                                    <td>
                                        <strong>{{ $role->name }}</strong>
                                        @if($role->description)
                                            <div class="small text-muted">{{ $role->description }}</div>
                                        @endif
                                    </td>
                                    <td>{{ $role->website->name ?? 'N/A' }}</td>
                                    <td>{{ $role->permissions->count() }}</td>
                                    <td>{{ $role->users->count() }}</td>
                                    <td>
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
@endsection
