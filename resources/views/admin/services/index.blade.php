@extends('layouts.admin')

@section('meta_title', 'Admin Services | Kiosk')
@section('page_title', 'Service Requests')
@section('page_subtitle', 'Track requests, assignments, and payment state from a unified operations queue.')

@section('content')
@php
    $unassignedCount = $requests->getCollection()->filter(fn ($item) => blank($item->assignedStaff))->count();
    $completedCount = $requests->getCollection()->where('status', 'completed')->count();
@endphp

<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Services</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">Service Requests</li>
            </ol>
        </nav>
    </div>
</div>
<!--end breadcrumb-->

<!-- Stats Summary Row -->
<div class="row row-cols-1 row-cols-md-3 g-3 mb-4">
    <div class="col">
        <div class="card radius-10 border-0 shadow-sm mb-0">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-secondary">Visible Requests</p>
                        <h4 class="my-1 fw-bold text-dark">{{ $requests->total() }}</h4>
                        <p class="mb-0 font-13 text-muted">Currently filtered list</p>
                    </div>
                    <div class="widgets-icons bg-light-primary text-primary ms-auto rounded-3">
                        <i class="bx bx-cog fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card radius-10 border-0 shadow-sm mb-0">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-secondary">Unassigned</p>
                        <h4 class="my-1 fw-bold text-warning">{{ $unassignedCount }}</h4>
                        <p class="mb-0 font-13 text-muted">Awaiting personnel</p>
                    </div>
                    <div class="widgets-icons bg-light-warning text-warning ms-auto rounded-3">
                        <i class="bx bx-user-plus fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card radius-10 border-0 shadow-sm mb-0">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-secondary">Completed</p>
                        <h4 class="my-1 fw-bold text-success">{{ $completedCount }}</h4>
                        <p class="mb-0 font-13 text-muted">Resolved jobs</p>
                    </div>
                    <div class="widgets-icons bg-light-success text-success ms-auto rounded-3">
                        <i class="bx bx-check-circle fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters Card -->
<div class="card mb-4">
    <div class="card-body p-4">
        <div class="d-flex align-items-center mb-3">
            <div>
                <h5 class="mb-1 fw-bold text-dark">Service Request Queue</h5>
                <p class="text-muted small mb-0">Filter service jobs by title, customer, or state, then open individual requests for assignment and progress tracking.</p>
            </div>
        </div>

        <form method="GET" class="row g-3 align-items-center">
            <div class="col-md-7">
                <div class="position-relative">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control ps-5 radius-30" placeholder="Search title, customer, category">
                    <span class="position-absolute top-50 translate-middle-y ms-3"><i class="bx bx-search"></i></span>
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select radius-30">
                    <option value="">All Statuses</option>
                    @foreach(['pending','processing','completed','cancelled','closed'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst(str_replace('_',' ',$status)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary radius-30 w-100">Filter Requests</button>
            </div>
        </form>
    </div>
</div>

<!-- Services Table Card -->
<div class="card">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Request</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Assigned Staff</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $item)
                        @php
                            $statusBadge = in_array($item->status, ['completed', 'closed'], true)
                                ? 'text-success bg-light-success'
                                : (in_array($item->status, ['cancelled'], true) ? 'text-danger bg-light-danger' : 'text-warning bg-light-warning');

                            $paymentBadge = $item->payment_status === 'paid'
                                ? 'text-success bg-light-success'
                                : ($item->payment_status === 'failed' ? 'text-danger bg-light-danger' : 'text-warning bg-light-warning');
                        @endphp
                        <tr>
                            <td>
                                <strong class="text-dark">{{ $item->title }}</strong>
                                <div class="small text-muted">{{ $item->user?->name ?: 'Unknown customer' }}</div>
                            </td>
                            <td><span class="fw-semibold text-secondary">{{ $item->category?->name ?: 'General' }}</span></td>
                            <td>
                                <span class="badge rounded-pill {{ $statusBadge }} p-2 text-uppercase px-3">
                                    <i class='bx bxs-circle align-middle me-1'></i>{{ str_replace('_',' ',$item->status) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge rounded-pill {{ $paymentBadge }} p-2 text-uppercase px-3">
                                    <i class='bx bxs-circle align-middle me-1'></i>{{ str_replace('_',' ',$item->payment_status) }}
                                </span>
                            </td>
                            <td><span class="fw-semibold text-dark">{{ $item->assignedStaff?->name ?? 'Unassigned' }}</span></td>
                            <td class="text-end">
                                <a href="{{ route('admin.services.show', $item) }}" class="btn btn-sm btn-primary radius-30 px-3">Open</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No service requests found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($requests->hasPages())
            <div class="mt-4">
                {{ $requests->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
