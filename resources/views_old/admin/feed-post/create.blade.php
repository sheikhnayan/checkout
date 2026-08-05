@extends('admin.main')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card mt-4">
            <div class="card-header">
                <h4 class="mb-1">Create Feed Post</h4>
                <p class="text-muted mb-0">Publish a new image post to the public feed.</p>
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

                <form action="{{ route('admin.feed-post.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @include('admin.feed-post._form', ['feedPost' => null])
                </form>
            </div>
        </div>
    </div>
</div>
@endsection