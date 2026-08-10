@extends('admin.main')

@section('content')
<style>
/* ─── Forms Dashboard (Matching Transactions Aesthetics) ─────────────── */
.forms-card {
    background: linear-gradient(145deg, rgba(255,255,255,0.05), rgba(255,255,255,0.02));
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 16px;
    padding: 24px;
}

/* Header & Create Button */
.btn-create-form {
    background: linear-gradient(135deg, #7c3aed 0%, #6366f1 100%);
    border: 1px solid rgba(255, 255, 255, 0.15);
    color: #ffffff !important;
    font-weight: 600;
    font-size: 0.85rem;
    padding: 9px 20px;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(124, 58, 237, 0.35);
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
}
.btn-create-form:hover {
    background: linear-gradient(135deg, #6d28d9 0%, #4f46e5 100%);
    color: #ffffff !important;
    box-shadow: 0 6px 20px rgba(124, 58, 237, 0.5);
    transform: translateY(-1px);
}

/* Search & Filter Controls */
.txn-search-wrap {
    position: relative;
    width: 280px;
}
.txn-search-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: rgba(255, 255, 255, 0.35);
    font-size: 0.9rem;
    pointer-events: none;
}
.txn-search-input {
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    color: #fff;
    font-size: 0.85rem;
    padding: 8px 14px 8px 38px;
    outline: none;
    width: 100%;
    transition: all 0.2s ease;
}
.txn-search-input:focus {
    border-color: rgba(124, 58, 237, 0.6);
    background: rgba(255, 255, 255, 0.08);
    box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.2);
}
.txn-search-input::placeholder {
    color: rgba(255, 255, 255, 0.35);
}

.txn-filter-select {
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    color: #fff;
    font-size: 0.85rem;
    padding: 8px 14px;
    outline: none;
    transition: all 0.2s ease;
    min-width: 200px;
}
.txn-filter-select:focus {
    border-color: rgba(124, 58, 237, 0.6);
    box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.2);
}
.txn-filter-select option {
    background: #1e293b;
    color: #ffffff;
}

.txn-reset-btn {
    background: rgba(255, 255, 255, 0.07);
    border: 1px solid rgba(255, 255, 255, 0.12);
    color: rgba(255, 255, 255, 0.75) !important;
    border-radius: 10px;
    font-size: 0.85rem;
    font-weight: 600;
    width: 36px;
    height: 36px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    text-decoration: none;
}
.txn-reset-btn:hover {
    background: rgba(255, 255, 255, 0.14);
    color: #ffffff !important;
}

/* Public URL Box (High Contrast) */
.form-url-badge {
    background: rgba(15, 23, 42, 0.85);
    border: 1px solid rgba(124, 58, 237, 0.35);
    color: #93c5fd;
    font-family: 'SFMono-Regular', Consolas, Monaco, monospace;
    font-size: 0.75rem;
    font-weight: 500;
    padding: 5px 10px;
    border-radius: 8px;
    max-width: 320px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    box-shadow: inset 0 1px 2px rgba(0,0,0,0.4);
}
.form-url-icon {
    color: #a78bfa;
    font-size: 0.85rem;
    flex-shrink: 0;
}
.form-url-text {
    color: #93c5fd;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.form-url-btn {
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.12);
    color: rgba(255, 255, 255, 0.75);
    width: 30px;
    height: 30px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    transition: all 0.2s ease;
    cursor: pointer;
    text-decoration: none;
}
.form-url-btn:hover {
    background: rgba(124, 58, 237, 0.3);
    color: #ffffff;
    border-color: rgba(124, 58, 237, 0.6);
    transform: translateY(-1px);
}

/* Table Styling & Padding */
.forms-table-wrapper {
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 12px;
    overflow: hidden;
    background: rgba(0, 0, 0, 0.12);
}
.forms-table {
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
}
.forms-table thead th {
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.07em;
    color: rgba(255, 255, 255, 0.45);
    text-transform: uppercase;
    padding: 14px 18px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    background: rgba(0, 0, 0, 0.25);
    white-space: nowrap;
}
.forms-table tbody tr {
    border-bottom: 1px solid rgba(255, 255, 255, 0.04);
    transition: background 0.15s ease;
}
.forms-table tbody tr:last-child {
    border-bottom: none;
}
.forms-table tbody tr:hover {
    background: rgba(255, 255, 255, 0.04) !important;
}
.forms-table tbody td {
    padding: 16px 18px;
    vertical-align: middle;
}

/* Club Tags */
.txn-club-tag {
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.12);
    color: rgba(255, 255, 255, 0.85);
    font-size: 0.75rem;
    font-weight: 500;
    padding: 3px 9px;
    border-radius: 6px;
    display: inline-block;
}

/* Submissions Badge */
.txn-submissions-badge {
    background: rgba(124, 58, 237, 0.15);
    border: 1px solid rgba(124, 58, 237, 0.3);
    color: #c4b5fd;
    font-size: 0.78rem;
    font-weight: 600;
    padding: 5px 12px;
    border-radius: 20px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
    transition: all 0.2s ease;
}
.txn-submissions-badge:hover {
    background: rgba(124, 58, 237, 0.3);
    color: #ffffff;
    border-color: rgba(124, 58, 237, 0.5);
    transform: translateY(-1px);
}

/* Action Buttons (Matching Transactions Page) */
.txn-action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    border-radius: 8px;
    font-size: 0.78rem;
    font-weight: 600;
    padding: 6px 12px;
    transition: all 0.2s ease;
    cursor: pointer;
    text-decoration: none;
    border: 1px solid transparent;
}
.txn-action-btn.action-data {
    background: rgba(99, 102, 241, 0.12);
    border-color: rgba(99, 102, 241, 0.25);
    color: #818cf8;
}
.txn-action-btn.action-data:hover {
    background: rgba(99, 102, 241, 0.28);
    border-color: rgba(99, 102, 241, 0.5);
    color: #a5b4fc;
    transform: translateY(-1px);
}
.txn-action-btn.action-edit {
    background: rgba(245, 158, 11, 0.12);
    border-color: rgba(245, 158, 11, 0.25);
    color: #fbbf24;
}
.txn-action-btn.action-edit:hover {
    background: rgba(245, 158, 11, 0.28);
    border-color: rgba(245, 158, 11, 0.5);
    color: #fef08a;
    transform: translateY(-1px);
}
.txn-action-btn.action-delete {
    background: rgba(239, 68, 68, 0.12);
    border-color: rgba(239, 68, 68, 0.25);
    color: #f87171;
    padding: 6px 10px;
}
.txn-action-btn.action-delete:hover {
    background: rgba(239, 68, 68, 0.28);
    border-color: rgba(239, 68, 68, 0.5);
    color: #fca5a5;
    transform: translateY(-1px);
}

/* Status Pill Button */
.txn-status-badge {
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.03em;
    padding: 5px 12px;
    border-radius: 20px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: all 0.2s ease;
    cursor: pointer;
    border: 1px solid transparent;
}
.txn-status-badge.status-active {
    background: rgba(16, 185, 129, 0.15);
    color: #34d399;
    border-color: rgba(16, 185, 129, 0.3);
}
.txn-status-badge.status-active:hover {
    background: rgba(16, 185, 129, 0.25);
    color: #6ee7b7;
    transform: translateY(-1px);
}
.txn-status-badge.status-inactive {
    background: rgba(239, 68, 68, 0.15);
    color: #f87171;
    border-color: rgba(239, 68, 68, 0.3);
}
.txn-status-badge.status-inactive:hover {
    background: rgba(239, 68, 68, 0.25);
    color: #fca5a5;
    transform: translateY(-1px);
}
</style>

<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        
        <!-- Header Row -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h4 class="mb-1 text-white fw-bold"><i class="bx bx-list-check me-2 text-primary"></i>Custom Drag & Drop Form Builder</h4>
                <p class="text-muted mb-0 small">Create, edit, and manage drag-and-drop web forms with public URLs and audit logs.</p>
            </div>
            <a href="{{ route('admin.forms.create') }}" class="btn-create-form">
                <i class="bx bx-plus"></i> Create New Form
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 bg-success bg-opacity-25 text-white mb-4" role="alert">
                <i class="bx bx-check-circle me-2 fs-5"></i>{{ session('success') }}
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Main Forms Table Card -->
        <div class="forms-card">
            <!-- Integrated Search & Filter Controls inside Table Card Header -->
            <form method="GET" action="{{ route('admin.forms.index') }}" class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <h5 class="mb-0 text-white fw-bold fs-6"><i class="bx bx-table me-2 text-primary"></i>All Custom Forms</h5>
                    
                    <!-- Club / Website Filter Select -->
                    <select name="website" class="txn-filter-select" onchange="this.form.submit()">
                        <option value="">All Clubs / Websites</option>
                        @foreach($websites as $web)
                            <option value="{{ $web->id }}" {{ (string)request('website') === (string)$web->id ? 'selected' : '' }}>{{ $web->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="d-flex align-items-center gap-2 flex-wrap ms-auto">
                    <!-- Search Input inside Table Header -->
                    <div class="txn-search-wrap">
                        <i class="bx bx-search txn-search-icon"></i>
                        <input type="text" name="search" class="txn-search-input" placeholder="Search title or slug..." value="{{ request('search') }}">
                    </div>

                    @if(request('search') || request('website'))
                        <a href="{{ route('admin.forms.index') }}" class="btn txn-reset-btn" title="Clear Filters">
                            <i class="bx bx-x fs-5"></i>
                        </a>
                    @endif
                </div>
            </form>

            <!-- Table Container with Internal Padding & Border Radius -->
            <div class="table-responsive forms-table-wrapper text-nowrap p-4">
                <table class="forms-table">
                    <thead>
                        <tr>
                            <th>Form Title & Public Link</th>
                            <th>Target Clubs</th>
                            <th>Submissions</th>
                            <th>Created / Updated By</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($forms as $form)
                            <tr>
                                <td>
                                    <div class="fw-bold text-white fs-6 mb-1">{{ $form->title }}</div>
                                    <div class="small text-muted mb-2">{{ $form->description ?: 'No description' }}</div>
                                    <div class="d-inline-flex align-items-center gap-1.5">
                                        <div class="form-url-badge d-flex align-items-center gap-1.5" title="{{ route('forms.public.show', $form->slug) }}">
                                            <i class="bx bx-link-external form-url-icon"></i>
                                            <span class="form-url-text">{{ route('forms.public.show', $form->slug) }}</span>
                                        </div>
                                        <button type="button" class="form-url-btn copy-btn" data-clipboard-text="{{ route('forms.public.show', $form->slug) }}" title="Copy Public URL">
                                            <i class="bx bx-copy"></i>
                                        </button>
                                        <a href="{{ route('forms.public.show', $form->slug) }}" target="_blank" class="form-url-btn" title="Open Public Link">
                                            <i class="bx bx-show"></i>
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    @if(empty($form->website_ids))
                                        <span class="txn-club-tag">All Clubs</span>
                                    @else
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($websites->whereIn('id', $form->website_ids) as $w)
                                                <span class="txn-club-tag">{{ $w->name }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.forms.submissions', $form->id) }}" class="txn-submissions-badge">
                                        <i class="bx bx-receipt"></i>
                                        <span>{{ $form->submissions_count }} {{ Str::plural('Submission', $form->submissions_count) }}</span>
                                    </a>
                                </td>
                                <td>
                                    <div class="small">
                                        <i class="bx bx-user me-1 text-primary"></i><strong class="text-white">Created:</strong> {{ optional($form->creator)->name ?: 'System' }}<br>
                                        <span class="text-muted"><i class="bx bx-time me-1"></i>{{ $form->created_at ? $form->created_at->format('M d, Y h:i A') : '-' }}</span>
                                    </div>
                                    @if($form->updater && $form->updated_by_user_id != $form->created_by_user_id)
                                        <div class="small mt-1 text-info">
                                            <i class="bx bx-edit me-1"></i><strong>Edited:</strong> {{ $form->updater->name }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('admin.forms.toggle', $form->id) }}">
                                        @csrf
                                        <button type="submit" class="txn-status-badge {{ $form->is_active ? 'status-active' : 'status-inactive' }}" title="Click to toggle status">
                                            <i class="bx {{ $form->is_active ? 'bx-check-circle' : 'bx-x-circle' }}"></i>
                                            <span>{{ $form->is_active ? 'Active' : 'Inactive' }}</span>
                                        </button>
                                    </form>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex align-items-center justify-content-end gap-1.5">
                                        <a href="{{ route('admin.forms.submissions', $form->id) }}" class="txn-action-btn action-data" title="View Submissions Data">
                                            <i class="bx bx-table"></i>
                                            <span>Data</span>
                                        </a>
                                        <a href="{{ route('admin.forms.edit', $form->id) }}" class="txn-action-btn action-edit" title="Edit Builder">
                                            <i class="bx bx-edit-alt"></i>
                                            <span>Edit</span>
                                        </a>
                                        <form method="POST" action="{{ route('admin.forms.destroy', $form->id) }}" onsubmit="return confirm('Are you sure you want to delete this form? All submission data will be lost.');" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="txn-action-btn action-delete" title="Delete Form">
                                                <i class="bx bx-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bx bx-list-check fs-1 d-block mb-2 text-primary"></i>
                                    No custom forms found. Click "Create New Form" above to launch the visual builder.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($forms->hasPages())
                <div class="pt-3">
                    {{ $forms->withQueryString()->links() }}
                </div>
            @endif
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function showToast(message) {
        let toast = document.getElementById('forms-toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'forms-toast';
            toast.style.cssText = 'position:fixed;bottom:24px;right:24px;background:#1e293b;border:1px solid #7c3aed;color:#fff;padding:12px 20px;border-radius:10px;box-shadow:0 10px 30px rgba(0,0,0,0.5);z-index:9999;font-size:0.85rem;display:flex;align-items:center;gap:8px;transition:all 0.3s ease;opacity:0;transform:translateY(10px);';
            document.body.appendChild(toast);
        }
        toast.innerHTML = '<i class="bx bx-check-circle text-success fs-5"></i> ' + message;
        toast.style.opacity = '1';
        toast.style.transform = 'translateY(0)';
        setTimeout(function() {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(10px)';
        }, 3000);
    }

    document.querySelectorAll('.copy-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const text = this.getAttribute('data-clipboard-text');
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(function() {
                    showToast('Public URL copied to clipboard!');
                }).catch(function() {
                    fallbackCopy(text);
                });
            } else {
                fallbackCopy(text);
            }
        });
    });

    function fallbackCopy(text) {
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.select();
        try {
            document.execCommand('copy');
            showToast('Public URL copied to clipboard!');
        } catch (err) {
            console.error('Failed to copy: ', err);
        }
        document.body.removeChild(textarea);
    }
});
</script>
@endsection


