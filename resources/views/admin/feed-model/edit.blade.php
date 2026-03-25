@extends('admin.main')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card mt-4">
            <div class="card-header">
                <h4 class="mb-1">Edit Feed Model</h4>
                <p class="text-muted mb-0">Update this model’s public profile and visibility.</p>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.feed-model.update', $feedModel) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @include('admin.feed-model._form', ['feedModel' => $feedModel])
                </form>
            </div>
        </div>
    </div>
</div>
@endsection