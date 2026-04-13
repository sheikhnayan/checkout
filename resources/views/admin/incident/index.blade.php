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
                            <i class="fas fa-file-contract icon-gradient bg-arielle-smile"></i>
                        </div>
                        <div>Incident Reports</div>
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
                                    <th>Club Name</th>
                                    <th>Domain</th>
                                    <th>Incidents</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($websites->isEmpty())
                                    <tr>
                                        <td colspan="5" class="text-center">No websites found.</td>
                                    </tr>
                                @else
                                    @foreach ($websites as $key => $website)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $website->name }}</td>
                                            <td>{{ $website->domain }}</td>
                                            <td>{{ (int) $website->incident_count }}</td>
                                            <td>
                                                <a href="{{ route('admin.incident.show', $website->id) }}" class="btn btn-secondary">Open</a>
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
