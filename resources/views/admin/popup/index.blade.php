@extends('admin.main')

@section('content')
<link rel="stylesheet" href="{{ asset('user/extra.css') }}">

<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="app-main__inner">
            <div class="app-page-title mt-4">
                <div class="page-title-wrapper">
                    <div class="page-title-heading">
                        <div class="page-title-icon">
                            <i class="fas fa-window-maximize icon-gradient bg-arielle-smile"></i>
                        </div>
                        <div>Checkout Popups</div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg">
                    <div class="card-shadow-primary card-border text-white mb-3 card bg-primary p-2">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>SI</th>
                                    <th>Name</th>
                                    <th>Domain</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($data->isEmpty())
                                    <tr>
                                        <td colspan="5" class="text-center">No websites found.</td>
                                    </tr>
                                @else
                                    @foreach ($data as $key => $item)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->domain }}</td>
                                            <td>{{ (int) $item->status === 1 ? 'Active' : 'Inactive' }}</td>
                                            <td>
                                                <a href="{{ route('admin.popup.show', $item->id) }}" class="btn btn-secondary">Show</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
