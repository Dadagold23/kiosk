@extends('layouts.admin')

@section('meta_title', 'Admin Users | Kiosk')
@section('page_title', 'Users')
@section('page_subtitle', 'Manage platform accounts, role coverage, and user onboarding quality from one place')

@section('content')
<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Accounts</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">Users Management</li>
            </ol>
        </nav>
    </div>
</div>
<!--end breadcrumb-->

<!-- Filters Card -->
<div class="card mb-4">
    <div class="card-body p-4">
        <div class="d-flex align-items-center mb-3">
            <div>
                <h5 class="mb-1 fw-bold text-dark">Filter Users</h5>
                <p class="text-muted small mb-0">Search by identity details and narrow by role to review access assignments faster.</p>
            </div>
        </div>

        <form method="GET" class="row g-3 align-items-center">
            <div class="col-md-5">
                <div class="position-relative">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control ps-5 radius-30" placeholder="Search name, email, phone, identity no.">
                    <span class="position-absolute top-50 translate-middle-y ms-3"><i class="bx bx-search"></i></span>
                </div>
            </div>
            <div class="col-md-3">
                <select name="role" class="form-select radius-30">
                    <option value="">All Roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" @selected(request('role') === $role->name)>{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="kyc_status" class="form-select radius-30">
                    <option value="">All KYC States</option>
                    @foreach(['not_submitted' => 'Not Submitted', 'pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'requires_review' => 'Requires Review'] as $value => $label)
                        <option value="{{ $value }}" @selected(request('kyc_status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1">
                <button class="btn btn-primary radius-30 w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

<!-- Users Table Card -->
<div class="card">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Contact</th>
                        <th>KYC Status</th>
                        <th>Role</th>
                        <th>Date</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        @php
                            $kycBadge = match($user->kyc_status) {
                                'approved' => 'text-success bg-light-success',
                                'rejected' => 'text-danger bg-light-danger',
                                'requires_review', 'pending' => 'text-warning bg-light-warning',
                                default => 'text-secondary bg-light-secondary',
                            };
                        @endphp
                        <tr>
                            <td>
                                <strong class="text-dark">{{ $user->name }}</strong>
                            </td>
                            <td>
                                <div class="text-dark">{{ $user->email }}</div>
                                <div class="small text-muted">{{ $user->phone ?: 'No phone' }}</div>
                            </td>
                            <td>
                                <div class="mb-1">
                                    <span class="badge rounded-pill {{ $kycBadge }} p-2 text-uppercase px-3">
                                        <i class="bx bxs-circle align-middle me-1"></i>{{ $user->kyc_status ? ucfirst(str_replace('_', ' ', $user->kyc_status)) : 'Not submitted' }}
                                    </span>
                                </div>
                                <div class="small text-muted">{{ $user->identity_type ? ucfirst(str_replace('_', ' ', $user->identity_type)) : 'No ID type' }}</div>
                            </td>
                            <td>
                                @forelse($user->roles as $role)
                                    <span class="badge rounded-pill text-primary bg-light-primary p-2 text-uppercase px-3"><i class="bx bxs-circle align-middle me-1"></i>{{ $role->name }}</span>
                                @empty
                                    <span class="badge rounded-pill text-secondary bg-light-secondary p-2 text-uppercase px-3"><i class="bx bxs-circle align-middle me-1"></i>No role</span>
                                @endforelse
                            </td>
                            <td>{{ $user->created_at->format('d M Y') }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-primary radius-30 px-3">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="mt-4">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
