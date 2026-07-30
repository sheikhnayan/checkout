@extends('admin.main')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Checkout Popups - {{ $website->name }}</h4>
            <a href="{{ route('admin.popup.create', $website_id) }}" class="btn btn-primary">Create Popup</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card p-3">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>SI</th>
                            <th>Title</th>
                            <th>Schedule</th>
                            <th>Flags</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $key => $popup)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $popup->title ?: 'Untitled popup' }}</td>
                                <td>
                                    {{ $popup->starts_at ? $popup->starts_at->format('Y-m-d H:i') : 'Immediate' }}
                                    -
                                    {{ $popup->ends_at ? $popup->ends_at->format('Y-m-d H:i') : 'No End' }}
                                </td>
                                <td>
                                    <span class="badge bg-{{ $popup->show_once_per_session ? 'info' : 'secondary' }}">
                                        {{ $popup->show_once_per_session ? 'Show Once/Session' : 'Show Every Visit' }}
                                    </span>
                                    <span class="badge bg-{{ $popup->is_active ? 'success' : 'warning' }}">
                                        {{ $popup->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>{{ $popup->is_archieved ? 'Archived' : 'Live' }}</td>
                                <td>
                                    <a href="{{ route('admin.popup.edit', $popup->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                    @if(!$popup->is_archieved)
                                        <form action="{{ route('admin.popup.archive', $popup->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-warning" onclick="return confirm('Archive this popup?');">Archive</button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.popup.unarchive', $popup->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Unarchive this popup?');">Unarchive</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No popups created yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
