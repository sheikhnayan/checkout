@extends('admin.main')

@section('content')
<link rel="stylesheet" href="{{ asset('user/extra.css') }}">
<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.linearicons.com/free/1.0.0/icon-font.min.css">

<style>
    .forms-wizard li.done em::before, .lnr-checkmark-circle::before {
  content: "\e87f";
}

.forms-wizard li.done em::before {
  display: block;
  font-size: 1.2rem;
  height: 42px;
  line-height: 40px;
  text-align: center;
  width: 42px;
}

.forms-wizard li.done em {
  font-family: Linearicons-Free;
}

.cat-icon-preview {
    width: 38px;
    height: 34px;
    border: 1px solid #d7dce4;
    border-radius: 8px;
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
    flex-shrink: 0;
}
.cat-icon-preview i { font-size: 15px; }

.drag-handle {
    cursor: grab;
    color: #6b7280;
    font-size: 16px;
}

.drag-handle:active {
    cursor: grabbing;
}

.sortable-ghost {
    opacity: .4;
}

.category-open-btn {
    min-width: 90px;
}

.category-open-btn,
.uncategorized-open-btn {
    background: #111827 !important;
    border-color: #111827 !important;
    color: #fff !important;
    font-weight: 700;
}

.category-open-btn:hover,
.uncategorized-open-btn:hover {
    background: #1f2937 !important;
    border-color: #1f2937 !important;
    color: #fff !important;
}

.category-open-btn.is-selected,
.uncategorized-open-btn.is-selected {
    background: #2563eb !important;
    border-color: #2563eb !important;
    box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2);
}
</style>
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
                                            Packages
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
                                            Packages
                                        </li>

                                    </ol>

                                    <div class="btn-group" role="group" aria-label="Basic example" style="float: right">
                                        <a href="/admins/package/create/{{ $website_id }}" class="btn btn-primary">Add Package</a>
                                        @if(auth()->user()->isAdmin())
                                            <a href="{{ route('admin.package.create-targeted', ['audience' => 'affiliate', 'website_id' => $website_id]) }}" class="btn btn-dark">Add affiliate Package</a>
                                        @endif
                                        <a href="{{ route('admin.package.create-targeted', ['audience' => 'entertainer', 'website_id' => $website_id]) }}" class="btn btn-info">Add Entertainer Package</a>
                                    </div>
                                </nav>
                            </div>
                        </div>

                        @php
                            $activeTab = request('tab', 'categories');
                        @endphp

                        <!-- Tabs for Active / Archived Packages + Categories -->
                        <ul class="nav nav-tabs mb-3" id="packageTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $activeTab === 'active' ? 'active' : '' }}" id="active-tab" data-bs-toggle="tab" data-bs-target="#activePackages" type="button" role="tab" aria-controls="activePackages" aria-selected="{{ $activeTab === 'active' ? 'true' : 'false' }}">
                                    Active Packages
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $activeTab === 'archived' ? 'active' : '' }}" id="archived-tab" data-bs-toggle="tab" data-bs-target="#archivedPackages" type="button" role="tab" aria-controls="archivedPackages" aria-selected="{{ $activeTab === 'archived' ? 'true' : 'false' }}">
                                    Archived Packages
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $activeTab === 'categories' ? 'active' : '' }}" id="categories-tab" data-bs-toggle="tab" data-bs-target="#categoriesPanel" type="button" role="tab" aria-controls="categoriesPanel" aria-selected="{{ $activeTab === 'categories' ? 'true' : 'false' }}">
                                    Categories
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $activeTab === 'targeted' ? 'active' : '' }}" id="targeted-tab" data-bs-toggle="tab" data-bs-target="#targetedPackages" type="button" role="tab" aria-controls="targetedPackages" aria-selected="{{ $activeTab === 'targeted' ? 'true' : 'false' }}">
                                    Targeted Packages
                                </button>
                            </li>
                        </ul>
                        <div class="tab-content" id="packageTabsContent">
                            <div class="tab-pane fade {{ $activeTab === 'active' ? 'show active' : '' }}" id="activePackages" role="tabpanel" aria-labelledby="active-tab">
                                <div class="row">
                                    <div class="col-lg">
                                        <div class="card-shadow-primary card-border text-white mb-3 card bg-primary p-2">
                                            <table class="table" id="activePackagesTable">
                                                <thead>
                                                    <tr>
                                                        <th>SI</th>
                                                        <th>Name</th>
                                                        <th>Category</th>
                                                        <th>Price</th>
                                                        <th>Most Popular</th>
                                                        <th>Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php $activeIndex = 1; @endphp
                                                    @foreach ($data as $item)
                                                        @if (empty($item->is_archieved) || $item->is_archieved == 0)
                                                        <tr>
                                                            <td>{{ $activeIndex++ }}</td>
                                                            <td>{{ $item->name }}</td>
                                                            <td>{{ optional($item->category)->name ?: 'Uncategorized' }}</td>
                                                            <td>{{ $item->price }}</td>
                                                            <td>
                                                                @if((int) ($item->is_most_popular ?? 0) === 1)
                                                                    <span class="badge bg-warning text-dark">Yes</span>
                                                                @else
                                                                    <span class="badge bg-secondary">No</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <form action="/admins/package/toggle-status/{{ $item->id }}" method="POST" style="display:inline;">
                                                                    @csrf
                                                                    @if ($item->status == 1)
                                                                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Deactivate this package?');">Active</button>
                                                                    @else
                                                                        <button type="submit" class="btn btn-sm btn-secondary" onclick="return confirm('Activate this package?');">Inactive</button>
                                                                    @endif
                                                                </form>
                                                            </td>
                                                            <td>
                                                                <a href="/admins/package/edit/{{ $item->id }}" class="btn btn-primary">Edit</a>
                                                                <form action="/admins/package/archive/{{ $item->id }}" method="POST" style="display:inline;">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-warning" onclick="return confirm('Are you sure you want to archive this package?');">
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
                            <div class="tab-pane fade {{ $activeTab === 'archived' ? 'show active' : '' }}" id="archivedPackages" role="tabpanel" aria-labelledby="archived-tab">
                                <div class="row">
                                    <div class="col-lg">
                                        <div class="card-shadow-primary card-border text-white mb-3 card bg-secondary p-2">
                                            <table class="table" id="archivedPackagesTable">
                                                <thead>
                                                    <tr>
                                                        <th>SI</th>
                                                        <th>Name</th>
                                                        <th>Category</th>
                                                        <th>Price</th>
                                                        <th>Most Popular</th>
                                                        <th>Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php $archivedIndex = 1; @endphp
                                                    @foreach ($data as $item)
                                                        @if (!empty($item->is_archieved) && $item->is_archieved == 1)
                                                        <tr>
                                                            <td>{{ $archivedIndex++ }}</td>
                                                            <td>{{ $item->name }}</td>
                                                            <td>{{ optional($item->category)->name ?: 'Uncategorized' }}</td>
                                                            <td>{{ $item->price }}</td>
                                                            <td>
                                                                @if((int) ($item->is_most_popular ?? 0) === 1)
                                                                    <span class="badge bg-warning text-dark">Yes</span>
                                                                @else
                                                                    <span class="badge bg-secondary">No</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <form action="/admins/package/toggle-status/{{ $item->id }}" method="POST" style="display:inline;">
                                                                    @csrf
                                                                    @if ($item->status == 1)
                                                                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Deactivate this package?');">Active</button>
                                                                    @else
                                                                        <button type="submit" class="btn btn-sm btn-secondary" onclick="return confirm('Activate this package?');">Inactive</button>
                                                                    @endif
                                                                </form>
                                                            </td>
                                                            <td>
                                                                <form action="/admins/package/unarchive/{{ $item->id }}" method="POST" style="display:inline;">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-success" onclick="return confirm('Unarchive this package?');">
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
                            {{-- ===== CATEGORIES TAB ===== --}}
                            <div class="tab-pane fade {{ $activeTab === 'categories' ? 'show active' : '' }}" id="categoriesPanel" role="tabpanel" aria-labelledby="categories-tab">
                                @php
                                    $categoryIconOptions = [
                                        ''                => '— No Icon —',
                                        'fa-star'         => 'Star',
                                        'fa-crown'        => 'Crown',
                                        'fa-gem'          => 'Gem',
                                        'fa-fire'         => 'Fire',
                                        'fa-bolt'         => 'Bolt',
                                        'fa-wine-bottle'  => 'Bottle',
                                        'fa-chair'        => 'Table',
                                        'fa-user-shield'  => 'VIP',
                                        'fa-shield-alt'   => 'Entry',
                                        'fa-music'        => 'Music',
                                        'fa-cocktail'     => 'Cocktail',
                                        'fa-ticket-alt'   => 'Ticket',
                                        'fa-users'        => 'Group',
                                        'fa-calendar-alt' => 'Calendar',
                                        'fa-glass-cheers' => 'Cheers',
                                    ];
                                @endphp
                                @if(session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif

                                {{-- Add new category --}}
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h6 class="card-title fw-bold">Add New Category</h6>
                                        <form method="POST" action="{{ route('admin.package-category.store', $website_id) }}" class="d-flex gap-2 align-items-center flex-wrap">
                                            @csrf
                                            <div class="package-feature-icon-preview cat-icon-preview" id="add-cat-icon-preview"><i class="fas fa-star"></i></div>
                                            <select name="icon" class="form-control cat-icon-select" style="max-width:170px;" id="add-cat-icon-select">
                                                @foreach($categoryIconOptions as $iconClass => $iconLabel)
                                                    <option value="{{ $iconClass }}" {{ $iconClass === 'fa-star' ? 'selected' : '' }}>{{ $iconLabel }}</option>
                                                @endforeach
                                            </select>
                                            <input type="text" name="name" class="form-control" placeholder="Category name" required style="max-width:280px;">
                                            <div class="d-flex align-items-center gap-1">
                                                <input type="color" name="color" value="#a774ff" title="Category color" style="width:38px;height:34px;padding:2px;border-radius:6px;border:1px solid #d7dce4;cursor:pointer;">
                                                <small class="text-muted" style="white-space:nowrap;">Color</small>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Add</button>
                                        </form>
                                    </div>
                                </div>

                                {{-- Existing categories --}}
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title fw-bold">Existing Categories</h6>
                                        @if($categories->isEmpty())
                                            <p class="text-muted">No categories yet for this website.</p>
                                        @else
                                            <table class="table" id="categoriesTable">
                                                <thead>
                                                    <tr>
                                                        <th style="width:40px;"></th>
                                                        <th>#</th>
                                                        <th>Icon &amp; Name</th>
                                                        <th>State</th>
                                                        <th>Packages</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="categorySortableBody">
                                                    @foreach($categories as $i => $cat)
                                                    <tr data-id="{{ $cat->id }}">
                                                        <td class="text-center align-middle"><i class="fas fa-grip-vertical drag-handle" title="Drag to reorder"></i></td>
                                                        <td>{{ $i + 1 }}</td>
                                                        <td>
                                                            <form method="POST" action="{{ route('admin.package-category.update', $cat->id) }}" class="d-flex gap-2 align-items-center flex-wrap">
                                                                @csrf
                                                                <div class="package-feature-icon-preview cat-icon-preview"><i class="fas {{ $cat->icon ?: 'fa-star' }}"></i></div>
                                                                <select name="icon" class="form-control form-control-sm cat-icon-select" style="max-width:155px;">
                                                                    @foreach($categoryIconOptions as $iconClass => $iconLabel)
                                                                        <option value="{{ $iconClass }}" {{ ($cat->icon ?? '') === $iconClass ? 'selected' : '' }}>{{ $iconLabel }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <input type="text" name="name" value="{{ $cat->name }}" class="form-control form-control-sm" style="max-width:200px;" required>
                                                                <input type="color" name="color" value="{{ $cat->color ?? '#a774ff' }}" title="Category color" style="width:34px;height:31px;padding:2px;border-radius:6px;border:1px solid #d7dce4;cursor:pointer;flex-shrink:0;">
                                                                <button type="submit" class="btn btn-sm btn-outline-primary">Save</button>
                                                            </form>
                                                        </td>
                                                        <td>
                                                            @if((int) ($cat->is_archieved ?? 0) === 1)
                                                                <span class="badge bg-warning text-dark">Archived</span>
                                                            @else
                                                                <span class="badge bg-success">Active</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $cat->packages()->count() }}</td>
                                                        <td>
                                                            <div class="d-flex gap-2 align-items-center flex-wrap">
                                                                <a href="{{ route('admin.package.show', ['id' => $website_id, 'tab' => 'categories', 'category_id' => $cat->id]) }}" class="btn btn-sm category-open-btn {{ (string) ($selectedCategoryKey ?? '') === (string) $cat->id ? 'is-selected' : '' }}">Open</a>
                                                                @if((int) ($cat->is_archieved ?? 0) === 1)
                                                                    <form method="POST" action="{{ route('admin.package-category.unarchive', $cat->id) }}" onsubmit="return confirm('Unarchive this category?')">
                                                                        @csrf
                                                                        <button type="submit" class="btn btn-sm btn-success">Unarchive</button>
                                                                    </form>
                                                                @else
                                                                    <form method="POST" action="{{ route('admin.package-category.archive', $cat->id) }}" onsubmit="return confirm('Archive this category? It will be hidden on front-end checkout pages.')">
                                                                        @csrf
                                                                        <button type="submit" class="btn btn-sm btn-warning">Archive</button>
                                                                    </form>
                                                                @endif
                                                                <form method="POST" action="{{ route('admin.package-category.destroy', $cat->id) }}" onsubmit="return confirm('Delete this category? Packages will become Uncategorized.')">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>

                                            <div class="mt-2">
                                                <a href="{{ route('admin.package.show', ['id' => $website_id, 'tab' => 'categories', 'category_id' => 'uncategorized']) }}" class="btn btn-sm uncategorized-open-btn {{ ($selectedCategoryKey ?? '') === 'uncategorized' ? 'is-selected' : '' }}">Open Uncategorized</a>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                @if(!empty($selectedCategoryKey))
                                    <div class="card mt-3">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="card-title fw-bold mb-0">
                                                    {{ $selectedCategoryKey === 'uncategorized' ? 'Uncategorized Packages' : 'Packages in ' . ($selectedCategory->name ?? 'Category') }}
                                                </h6>
                                                <small class="text-muted">Drag rows to reorder packages for this category.</small>
                                            </div>

                                            @if($categoryPackages->isEmpty())
                                                <p class="text-muted mb-0">No packages found in this category.</p>
                                            @else
                                                <table class="table" id="categoryPackagesTable">
                                                    <thead>
                                                        <tr>
                                                            <th style="width:40px;"></th>
                                                            <th>#</th>
                                                            <th>Name</th>
                                                            <th>Status</th>
                                                            <th>Archive</th>
                                                            <th>Price</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="packageSortableBody">
                                                        @foreach($categoryPackages as $idx => $pkg)
                                                            <tr data-id="{{ $pkg->id }}">
                                                                <td class="text-center align-middle"><i class="fas fa-grip-vertical drag-handle" title="Drag to reorder"></i></td>
                                                                <td>{{ $idx + 1 }}</td>
                                                                <td>{{ $pkg->name }}</td>
                                                                <td>
                                                                    @if((int) ($pkg->status ?? 0) === 1)
                                                                        <span class="badge bg-success">Active</span>
                                                                    @else
                                                                        <span class="badge bg-secondary">Inactive</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if((int) ($pkg->is_archieved ?? 0) === 1)
                                                                        <span class="badge bg-warning text-dark">Archived</span>
                                                                    @else
                                                                        <span class="badge bg-primary">Live</span>
                                                                    @endif
                                                                </td>
                                                                <td>{{ $pkg->price }}</td>
                                                                <td>
                                                                    <a href="{{ route('admin.package.edit', $pkg->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="tab-pane fade {{ $activeTab === 'targeted' ? 'show active' : '' }}" id="targetedPackages" role="tabpanel" aria-labelledby="targeted-tab">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title fw-bold">affiliate and Entertainer Packages</h6>
                                        @if($targetedPackages->isEmpty())
                                            <p class="text-muted mb-0">No targeted packages created for this club yet.</p>
                                        @else
                                            <table class="table" id="targetedPackagesTable">
                                                <thead>
                                                    <tr>
                                                        <th>SI</th>
                                                        <th>Name</th>
                                                        <th>Sort</th>
                                                        <th>Audience</th>
                                                        <th>Assigned To</th>
                                                        <th>Category</th>
                                                        <th>Price</th>
                                                        <th>Most Popular</th>
                                                        <th>Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($targetedPackages as $index => $item)
                                                        @php
                                                            $owner = $item->audience === 'affiliate'
                                                                ? (optional($item->affiliate)->display_name ?: optional(optional($item->affiliate)->user)->name ?: 'All affiliates')
                                                                : (optional($item->entertainer)->display_name ?: optional(optional($item->entertainer)->user)->name ?: 'All Entertainers');
                                                        @endphp
                                                        <tr>
                                                            <td>{{ $index + 1 }}</td>
                                                            <td>{{ $item->name }}</td>
                                                            <td>{{ (int) ($item->sort_order ?? 0) }}</td>
                                                            <td>{{ ucfirst($item->audience) }}</td>
                                                            <td>{{ $owner ?: 'Unknown' }}</td>
                                                            <td>{{ optional($item->category)->name ?: 'Uncategorized' }}</td>
                                                            <td>{{ $item->price }}</td>
                                                            <td>
                                                                @if((int) ($item->is_most_popular ?? 0) === 1)
                                                                    <span class="badge bg-warning text-dark">Yes</span>
                                                                @else
                                                                    <span class="badge bg-secondary">No</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <form action="/admins/package/toggle-status/{{ $item->id }}" method="POST" style="display:inline;">
                                                                    @csrf
                                                                    @if ($item->status == 1)
                                                                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Deactivate this package?');">Active</button>
                                                                    @else
                                                                        <button type="submit" class="btn btn-sm btn-secondary" onclick="return confirm('Activate this package?');">Inactive</button>
                                                                    @endif
                                                                </form>
                                                            </td>
                                                            <td>
                                                                <a href="{{ route('admin.package.edit-targeted', $item->id) }}" class="btn btn-primary btn-sm">Edit</a>
                                                                @if (empty($item->is_archieved) || $item->is_archieved == 0)
                                                                    <form action="/admins/package/archive/{{ $item->id }}" method="POST" style="display:inline;">
                                                                        @csrf
                                                                        <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('Are you sure you want to archive this package?');">
                                                                            Archive
                                                                        </button>
                                                                    </form>
                                                                @else
                                                                    <form action="/admins/package/unarchive/{{ $item->id }}" method="POST" style="display:inline;">
                                                                        @csrf
                                                                        <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Unarchive this package?');">
                                                                            Unarchive
                                                                        </button>
                                                                    </form>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- / Content -->
        </div>
    </div>

    <div class="position-fixed top-0 end-0 p-3" style="z-index: 1080;">
        <div id="reorderToast" class="toast text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="reorderToastBody">Order updated successfully.</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

            <!-- Include DataTables and jQuery CDN (jQuery first, then DataTables, then Bootstrap) -->
            <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
            <script>
                $(document).ready(function() {
                    let table1 = new DataTable('#activePackagesTable');
                    let table2 = new DataTable('#archivedPackagesTable');
                    if ($('#targetedPackagesTable').length) {
                        let table3 = new DataTable('#targetedPackagesTable');
                    }

                    // Live icon preview for category icon pickers
                    $(document).on('change', '.cat-icon-select', function() {
                        var val = $(this).val();
                        var $preview = $(this).siblings('.cat-icon-preview').find('i');
                        if (val) {
                            $preview.attr('class', 'fas ' + val);
                        } else {
                            $preview.attr('class', 'fas fa-tag');
                        }
                    });

                    const csrfToken = '{{ csrf_token() }}';

                    function showReorderToast(message, isError = false) {
                        const toastEl = document.getElementById('reorderToast');
                        const bodyEl = document.getElementById('reorderToastBody');

                        if (!toastEl || !bodyEl || typeof bootstrap === 'undefined') {
                            return;
                        }

                        bodyEl.textContent = message;
                        toastEl.classList.remove('text-bg-success', 'text-bg-danger');
                        toastEl.classList.add(isError ? 'text-bg-danger' : 'text-bg-success');

                        const toast = bootstrap.Toast.getOrCreateInstance(toastEl, { delay: 2200 });
                        toast.show();
                    }

                    function postReorder(url, payload) {
                        return fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(payload)
                        }).then(function(response) {
                            if (!response.ok) {
                                throw new Error('Request failed');
                            }

                            return response;
                        });
                    }

                    const categoryBody = document.getElementById('categorySortableBody');
                    if (categoryBody) {
                        Sortable.create(categoryBody, {
                            handle: '.drag-handle',
                            animation: 150,
                            ghostClass: 'sortable-ghost',
                            onEnd: function() {
                                const orderedIds = Array.from(categoryBody.querySelectorAll('tr[data-id]')).map(function(row) {
                                    return parseInt(row.getAttribute('data-id'), 10);
                                }).filter(Number.isInteger);

                                if (!orderedIds.length) {
                                    return;
                                }

                                postReorder('{{ route('admin.package-category.reorder', $website_id) }}', { ordered_ids: orderedIds })
                                    .then(function() {
                                        showReorderToast('Category order saved.');
                                    })
                                    .catch(function() {
                                        showReorderToast('Could not save category order. Please retry.', true);
                                    });
                            }
                        });
                    }

                    const packageBody = document.getElementById('packageSortableBody');
                    if (packageBody) {
                        Sortable.create(packageBody, {
                            handle: '.drag-handle',
                            animation: 150,
                            ghostClass: 'sortable-ghost',
                            onEnd: function() {
                                const orderedIds = Array.from(packageBody.querySelectorAll('tr[data-id]')).map(function(row) {
                                    return parseInt(row.getAttribute('data-id'), 10);
                                }).filter(Number.isInteger);

                                if (!orderedIds.length) {
                                    return;
                                }

                                postReorder('{{ route('admin.package.reorder', $website_id) }}', {
                                    ordered_ids: orderedIds,
                                    category_id: '{{ $selectedCategoryKey ?? '' }}'
                                })
                                    .then(function() {
                                        showReorderToast('Package order saved.');
                                    })
                                    .catch(function() {
                                        showReorderToast('Could not save package order. Please retry.', true);
                                    });
                            }
                        });
                    }
                });
            </script>
        @endsection
