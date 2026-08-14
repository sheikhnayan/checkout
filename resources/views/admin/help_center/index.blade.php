@extends('admin.main')

@section('title', 'Help Center Portal')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-bold py-1 mb-1">
                <span class="text-muted fw-light">Portal /</span> Help Center Management
            </h4>
            <p class="text-muted mb-0">Build your custom Help Center page, organize form links & resources, and collaborate with team members.</p>
        </div>
        @if(!$myPage)
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPageModal">
                <i class="bx bx-plus me-1"></i> Create My Help Center Page
            </button>
        @else
            <div class="d-flex gap-2">
                <a href="{{ route('admin.help-center.builder', $myPage->id) }}" class="btn btn-primary">
                    <i class="bx bx-edit me-1"></i> Open Page Builder
                </a>
                <a href="{{ route('help-center.public', $myPage->slug) }}" target="_blank" class="btn btn-outline-primary">
                    <i class="bx bx-external-link me-1"></i> View Public Page
                </a>
            </div>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bx bx-error me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="bx bx-info-circle me-1"></i> {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Pending Collaborator Invitations Banner -->
    @if($pendingInvitations->count() > 0)
        <div class="card bg-label-warning border-warning mb-4">
            <div class="card-body">
                <h5 class="card-title text-warning fw-bold mb-3">
                    <i class="bx bx-envelope me-1"></i> Pending Collaboration Invitations ({{ $pendingInvitations->count() }})
                </h5>
                <div class="row g-3">
                    @foreach($pendingInvitations as $invite)
                        <div class="col-md-6">
                            <div class="p-3 bg-white rounded border d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="mb-1 text-dark fw-semibold">{{ $invite->page->title }}</h6>
                                    <small class="text-muted">Invited by <strong>{{ $invite->inviter->name ?? 'CartVIP User' }}</strong> ({{ $invite->inviter->email ?? '' }})</small>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.help-center.invitation.accept', $invite->invitation_token) }}" class="btn btn-sm btn-success">
                                        <i class="bx bx-check me-1"></i> Accept
                                    </a>
                                    <a href="{{ route('admin.help-center.invitation.decline', $invite->invitation_token) }}" class="btn btn-sm btn-outline-danger">
                                        Decline
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <div class="row g-4">
        <!-- MY HELP CENTER PAGE CARD -->
        <div class="col-lg-7">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header border-bottom bg-light d-flex align-items-center justify-content-between py-3">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="bx bx-book-bookmark text-primary me-2"></i> My Help Center Page
                    </h5>
                    @if($myPage)
                        <span class="badge bg-success">Active</span>
                    @endif
                </div>
                <div class="card-body py-4">
                    @if(!$myPage)
                        <div class="text-center py-4">
                            <div class="avatar avatar-xl bg-label-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                                <i class="bx bx-help-circle fs-1"></i>
                            </div>
                            <h5 class="fw-bold mb-2">You haven't created a Help Center page yet</h5>
                            <p class="text-muted max-w-md mx-auto mb-4">Create your personalized Help Center portal to organize forms, links, and documents for team members and collaborators.</p>
                            <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#createPageModal">
                                <i class="bx bx-plus me-1"></i> Get Started
                            </button>
                        </div>
                    @else
                        <div class="mb-4">
                            <h4 class="fw-bold mb-1">{{ $myPage->title }}</h4>
                            <p class="text-muted mb-3">{{ $myPage->description ?: 'No description provided.' }}</p>
                            
                            <!-- Shareable Link Input -->
                            <div class="mb-3">
                                <label class="form-label text-uppercase fs-7 fw-bold text-muted">Protected Public Link (Login Required)</label>
                                <div class="input-group">
                                    <input type="text" class="form-readonly form-control" id="shareablePublicUrl" value="{{ route('help-center.public', $myPage->slug) }}" readonly>
                                    <button class="btn btn-outline-primary" type="button" onclick="copyPublicUrl()">
                                        <i class="bx bx-copy me-1"></i> Copy Link
                                    </button>
                                </div>
                                <small class="text-muted mt-1 d-block">
                                    <i class="bx bx-lock-alt me-1"></i> Users must log into CartVIP to access this page. Any authenticated user under CartVIP can view it.
                                </small>
                            </div>

                            <div class="d-flex gap-2 flex-wrap mt-4">
                                <a href="{{ route('admin.help-center.builder', $myPage->id) }}" class="btn btn-primary">
                                    <i class="bx bx-cog me-1"></i> Manage Sections & Links ({{ $myPage->sections->count() }} Sections)
                                </a>
                                <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editPageModal">
                                    <i class="bx bx-pencil me-1"></i> Edit Details
                                </button>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Collaborators Section -->
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold mb-0">
                                    <i class="bx bx-group me-1 text-primary"></i> Page Collaborators ({{ $myPage->collaborators->count() }})
                                </h6>
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#inviteCollaboratorModal">
                                    <i class="bx bx-user-plus me-1"></i> Invite Collaborator
                                </button>
                            </div>

                            @if($myPage->collaborators->count() === 0)
                                <p class="text-muted fs-7 mb-0">No collaborators added yet. Invite team members by email to help customize this page.</p>
                            @else
                                <div class="list-group list-group-flush border rounded">
                                    @foreach($myPage->collaborators as $collab)
                                        <div class="list-group-item d-flex align-items-center justify-content-between py-2">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm bg-label-info me-3 rounded-circle d-flex align-items-center justify-content-center">
                                                    <i class="bx bx-user fs-6"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fs-7 fw-semibold">{{ $collab->user->name ?? $collab->email }}</h6>
                                                    <small class="text-muted fs-8">{{ $collab->email }}</small>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                @if($collab->status === 'accepted')
                                                    <span class="badge bg-success">Accepted</span>
                                                @elseif($collab->status === 'pending')
                                                    <span class="badge bg-warning">Pending Invite</span>
                                                @else
                                                    <span class="badge bg-secondary">Declined</span>
                                                @endif
                                                <form action="{{ route('admin.help-center.collaborators.remove', $collab->id) }}" method="POST" onsubmit="return confirm('Remove this collaborator?');" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-icon btn-sm btn-outline-danger">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- SHARED HELP CENTERS CARD -->
        <div class="col-lg-5">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header border-bottom bg-light py-3">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="bx bx-share-alt text-primary me-2"></i> Shared Help Centers
                    </h5>
                </div>
                <div class="card-body py-4">
                    <p class="text-muted fs-7 mb-3">Help Center pages created by other team members where you have accepted collaboration privileges.</p>
                    
                    @if($sharedCollaborations->count() === 0)
                        <div class="text-center py-4 border rounded bg-light">
                            <i class="bx bx-folder-open fs-2 text-muted mb-2"></i>
                            <p class="text-muted fs-7 mb-0">No shared Help Centers yet.</p>
                        </div>
                    @else
                        <div class="d-flex flex-column gap-3">
                            @foreach($sharedCollaborations as $shared)
                                <div class="card border shadow-none">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <h6 class="fw-bold mb-0 text-dark">{{ $shared->page->title }}</h6>
                                            <span class="badge bg-label-primary fs-8">Collaborator</span>
                                        </div>
                                        <p class="text-muted fs-7 mb-2">{{ Str::limit($shared->page->description, 90) ?: 'No description' }}</p>
                                        <small class="text-muted d-block mb-3">Owner: <strong>{{ $shared->page->owner->name ?? 'CartVIP User' }}</strong></small>
                                        
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('admin.help-center.builder', $shared->page->id) }}" class="btn btn-sm btn-primary">
                                                <i class="bx bx-edit me-1"></i> Edit Sections & Links
                                            </a>
                                            <a href="{{ route('help-center.public', $shared->page->slug) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                                <i class="bx bx-show me-1"></i> View
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CREATE PAGE MODAL -->
<div class="modal fade" id="createPageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.help-center.store-or-update') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold">Create Your Help Center Page</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-3">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Help Center Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" placeholder="e.g., Operations & Client Forms KB" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Description</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Describe the purpose of this Help Center page..."></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Banner Header Theme Color</label>
                    <input type="color" name="banner_color" class="form-control form-control-color w-100" value="#4f46e5">
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Create & Proceed to Builder</button>
            </div>
        </form>
    </div>
</div>

@if($myPage)
<!-- EDIT PAGE MODAL -->
<div class="modal fade" id="editPageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.help-center.store-or-update') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold">Edit Help Center Page Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-3">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Help Center Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" value="{{ $myPage->title }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ $myPage->description }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Banner Header Theme Color</label>
                    <input type="color" name="banner_color" class="form-control form-control-color w-100" value="{{ $myPage->banner_color ?: '#4f46e5' }}">
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- INVITE COLLABORATOR MODAL -->
<div class="modal fade" id="inviteCollaboratorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.help-center.invite-collaborator', $myPage->id) }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold">Invite Collaborator to Page</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-3">
                <p class="text-muted fs-7 mb-3">Invite a CartVIP team member by entering their email address. The recipient must be a registered CartVIP user.</p>
                <div class="mb-3">
                    <label class="form-label fw-semibold">CartVIP User Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" placeholder="colleague@cartvip.com" required>
                </div>
                <div class="alert alert-info py-2 px-3 fs-8 mb-0">
                    <i class="bx bx-info-circle me-1"></i> Invited collaborators can edit and manage sections/links on your page, while still maintaining their own separate Help Center page.
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Send Invitation</button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
function copyPublicUrl() {
    var copyText = document.getElementById("shareablePublicUrl");
    if (!copyText) return;
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(copyText.value);
    alert("Protected Help Center URL copied to clipboard!\nOnly logged-in CartVIP users can view this page.");
}
</script>
@endsection
