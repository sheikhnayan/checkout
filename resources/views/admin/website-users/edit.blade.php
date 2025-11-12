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

    label{
        color: #000 !important;
    }
</style>

<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-xxl-12 mb-6 order-0">
                <div class="app-main__inner">
                    <div class="app-page-title mt-4">
                        <div class="page-title-wrapper">
                            <div class="page-title-heading">
                                <div class="page-title-icon">
                                    <i class="fas fa-user-edit icon-gradient bg-arielle-smile"></i>
                                </div>
                                <div>
                                    <span class="text-capitalize">Website User</span>
                                </div>
                            </div>
                            <div class="page-title-actions">
                            </div>
                        </div>

                        <div class="page-title-subheading opacity-10 mt-3" style="white-space: nowrap; overflow-x: auto;">
                            <nav class="" aria-label="breadcrumb">
                                <ol class="breadcrumb" style="float: left">
                                    <li class="breadcrumb-item opacity-10">
                                        <a href="/admins">
                                            <i class="fas fa-home" role="img" aria-hidden="true"></i>
                                            <span class="visually-hidden">Home</span>
                                        </a>
                                        <i class="fas fa-chevron-right ms-1"></i>
                                    </li>
                                    <li class="breadcrumb-item">
                                        Website Users
                                        <i class="fas fa-chevron-right ms-1"></i>
                                    </li>
                                    <li class="active breadcrumb-item" aria-current="page">
                                        Edit
                                    </li>
                                </ol>
                            </nav>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg">
                            <div class="card-shadow-primary card-border text-white mb-3 card bg-primary" style="background: #fff !important;">
                                <form action="{{ route('admin.website-users.update', $user->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label for="name" class="form-label">User Name</label>
                                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                                           id="name" value="{{ old('name', $user->name) }}" placeholder="Enter User Name" required>
                                                    @error('name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label for="email" class="form-label">Email Address</label>
                                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                                           id="email" value="{{ old('email', $user->email) }}" placeholder="Enter Email Address" required>
                                                    @error('email')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="password" class="form-label">New Password</label>
                                                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                                                           id="password" placeholder="Leave blank to keep current password">
                                                    @error('password')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                                                    <input type="password" name="password_confirmation" class="form-control" 
                                                           id="password_confirmation" placeholder="Confirm New Password">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label for="website_id" class="form-label">Assign Website</label>
                                                    <select name="website_id" id="website_id" class="form-control @error('website_id') is-invalid @enderror" required>
                                                        <option value="">Select a website</option>
                                                        @foreach($websites as $website)
                                                            <option value="{{ $website->id }}" {{ old('website_id', $user->website_id) == $website->id ? 'selected' : '' }}>
                                                                {{ $website->name }} ({{ $website->domain }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('website_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <button type="submit" class="btn btn-success">
                                                    <i class="fas fa-save"></i> Update Website User
                                                </button>
                                                <a href="{{ route('admin.website-users.index') }}" class="btn btn-secondary ms-2">
                                                    <i class="fas fa-times"></i> Cancel
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection