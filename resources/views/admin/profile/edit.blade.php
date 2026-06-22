@extends('layouts.admin')

@section('meta_title', 'Admin Profile | Kiosk')
@section('page_title', 'My Profile')
@section('page_subtitle', 'Update your admin account details, password, and profile photo.')

@section('content')
@php($avatarUrl = $user->profilePhotoUrl())

<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Account</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">Profile Settings</li>
            </ol>
        </nav>
    </div>
</div>
<!--end breadcrumb-->

<div class="row g-4">
    <!-- Profile Card Column -->
    <div class="col-xl-4">
        <div class="card radius-10 border-0 shadow-sm h-100 mb-0">
            <div class="card-body p-4 text-center">
                <h5 class="mb-1 fw-bold text-dark">Current Profile</h5>
                <p class="text-muted small mb-4">Your avatar and details are featured across the admin panel.</p>

                <img src="{{ $avatarUrl }}" alt="{{ $user->name }}" class="img-fluid rounded-circle border p-1 mb-3 bg-light shadow-sm" style="width:132px; height:132px; object-fit:cover;">
                <h5 class="fw-bold text-dark mb-1">{{ $user->name }}</h5>
                <p class="text-muted small mb-3">{{ $user->email }}</p>
                <span class="badge rounded-pill text-info bg-light-info text-uppercase px-3 py-2">
                    <i class="bx bxs-circle align-middle me-1"></i>{{ $user->roles->pluck('name')->join(', ') ?: 'Admin User' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Form Column -->
    <div class="col-xl-8">
        <div class="card radius-10 border-0 shadow-sm mb-0">
            <div class="card-body p-4">
                <h5 class="mb-1 fw-bold text-dark">Admin Account Details</h5>
                <p class="text-muted small mb-4">Update your name, contact information, password, and profile picture here.</p>

                <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small text-secondary fw-bold text-uppercase">Full Name</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control radius-30 @error('name') is-invalid @enderror" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small text-secondary fw-bold text-uppercase">Email Address</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control radius-30 @error('email') is-invalid @enderror" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small text-secondary fw-bold text-uppercase">Primary Phone</label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-control radius-30 @error('phone') is-invalid @enderror">
                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small text-secondary fw-bold text-uppercase">Alternate Phone</label>
                            <input type="text" name="alternate_phone" value="{{ old('alternate_phone', $user->alternate_phone) }}" class="form-control radius-30 @error('alternate_phone') is-invalid @enderror">
                            @error('alternate_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label small text-secondary fw-bold text-uppercase">Profile Picture</label>
                            <input type="file" name="profile_photo" accept=".jpg,.jpeg,.png,.webp" class="form-control radius-30 @error('profile_photo') is-invalid @enderror">
                            @error('profile_photo') <div class="invalid-feedback">{{ $message }}</div> @enderror

                            @if($user->profile_photo_path)
                                <div class="form-check mt-3">
                                    <input class="form-check-input" type="checkbox" name="remove_profile_photo" value="1" id="removeProfilePhoto">
                                    <label class="form-check-label text-secondary" for="removeProfilePhoto">
                                        Remove current profile photo
                                    </label>
                                </div>
                            @endif
                        </div>

                        <div class="col-12 pt-3">
                            <hr class="my-3">
                            <h5 class="mb-1 fw-bold text-dark">Change Password</h5>
                            <p class="text-muted small mb-3">Leave the password fields empty if you do not want to change them.</p>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small text-secondary fw-bold text-uppercase">Current Password</label>
                            <input type="password" name="current_password" class="form-control radius-30 @error('current_password') is-invalid @enderror">
                            @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small text-secondary fw-bold text-uppercase">New Password</label>
                            <input type="password" name="new_password" class="form-control radius-30 @error('new_password') is-invalid @enderror">
                            @error('new_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small text-secondary fw-bold text-uppercase">Confirm New Password</label>
                            <input type="password" name="new_password_confirmation" class="form-control radius-30">
                        </div>

                        <div class="col-12 d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary radius-30 px-4">Back to Dashboard</a>
                            <button type="submit" class="btn btn-primary radius-30 px-4">Save Profile</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
