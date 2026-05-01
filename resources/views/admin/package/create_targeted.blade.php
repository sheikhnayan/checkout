@extends('admin.main')

@section('content')
<link rel="stylesheet" href="{{ asset('user/extra.css') }}">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-xxl-12 mb-6 order-0">
                <div class="app-main__inner">
                    <div class="app-page-title mt-4">
                        <div class="page-title-wrapper">
                            <div class="page-title-heading">
                                <div class="page-title-icon">
                                    <i class="fas fa-id-card icon-gradient bg-arielle-smile"></i>
                                </div>
                                <div>{{ $title }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg">
                            <div class="card-shadow-primary card-border text-white mb-3 card bg-primary">
                                @include('admin.package._targeted_form', [
                                    'formAction' => route('admin.package.store-targeted'),
                                    'submitLabel' => 'Submit',
                                ])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection