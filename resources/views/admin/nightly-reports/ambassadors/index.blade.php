@extends('admin.nightly-reports.layout')

@section('nr-content')
<div class="nr-content-header">
    <h2 class="nr-content-title">Manage Ambassadors</h2>
    <div class="nr-content-actions">
        <button class="btn btn-primary" data-toggle="modal" data-target="#addAmbassadorModal">
            <i class="fas fa-plus"></i> Add Ambassador
        </button>
    </div>
</div>

<div class="nr-card">
    <div class="nr-card-body p-0">
        @if(session('success'))
            <div class="alert alert-success m-3">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger m-3">{{ session('error') }}</div>
        @endif

        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Clubs Assigned</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ambassadors as $ambassador)
                <tr>
                    <td>{{ $ambassador->name }}</td>
                    <td>{{ $ambassador->email }}</td>
                    <td>
                        @if($ambassador->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-danger">Disabled</span>
                        @endif
                    </td>
                    <td>
                        {{ $ambassador->clubs->count() }} clubs
                    </td>
                    <td>
                        <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#editAmbassadorModal{{ $ambassador->id }}">
                            <i class="fas fa-edit"></i> Edit Access
                        </button>
                        <a href="{{ route('admin.nightly-reports.ambassadors.impersonate', $ambassador->id) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-user-secret"></i> Login As
                        </a>
                    </td>
                </tr>

                <!-- Edit Modal -->
                <div class="modal fade" id="editAmbassadorModal{{ $ambassador->id }}" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <form action="{{ route('admin.nightly-reports.ambassadors.update', $ambassador->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit {{ $ambassador->name }}'s Access</h5>
                                    <button type="button" class="close" data-dismiss="modal">
                                        <span>&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Status</label>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="statusSwitch{{ $ambassador->id }}" name="is_active" {{ $ambassador->is_active ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="statusSwitch{{ $ambassador->id }}">Active Account</label>
                                        </div>
                                    </div>
                                    <hr>
                                    <h6>Assign Clubs</h6>
                                    <div class="row">
                                        @foreach($websites as $website)
                                        <div class="col-md-6 mb-2">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="clubCheck{{ $ambassador->id }}_{{ $website->id }}" name="clubs[]" value="{{ $website->id }}" {{ $ambassador->clubs->contains($website->id) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="clubCheck{{ $ambassador->id }}_{{ $website->id }}">{{ $website->name }}</label>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">No ambassadors found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addAmbassadorModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="{{ route('admin.nightly-reports.ambassadors.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Ambassador</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">An email will be sent to the ambassador with a link to set their password.</p>
                    
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <hr>
                    <h6>Initial Club Access</h6>
                    <div class="row">
                        @foreach($websites as $website)
                        <div class="col-md-6 mb-2">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="newClubCheck{{ $website->id }}" name="clubs[]" value="{{ $website->id }}">
                                <label class="custom-control-label" for="newClubCheck{{ $website->id }}">{{ $website->name }}</label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add & Send Invite</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
