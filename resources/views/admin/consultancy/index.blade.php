@extends('layouts.admin')

@section('meta_title', 'Admin Consultancy | Kiosk')
@section('page_title', 'Consultancy Requests')
@section('page_subtitle', 'Manage advisory cases, consultant assignment, and payment readiness in one place.')

@section('content')
@php
    $unassignedCount = $requests->getCollection()->filter(fn ($item) => blank($item->assignedConsultant))->count();
    $completedCount = $requests->getCollection()->where('status', 'completed')->count();
@endphp

<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Advisory</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">Consultancy Requests</li>
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
                        <p class="mb-0 text-secondary">Visible Cases</p>
                        <h4 class="my-1 fw-bold">{{ $requests->total() }}</h4>
                        <p class="mb-0 font-13 text-muted">Currently active list</p>
                    </div>
                    <div class="widgets-icons bg-light-primary text-primary ms-auto rounded-3">
                        <i class="bx bx-briefcase fs-4"></i>
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
                        <h4 class="my-1 fw-bold">{{ $unassignedCount }}</h4>
                        <p class="mb-0 font-13 text-muted">Needs expert matching</p>
                    </div>
                    <div class="widgets-icons bg-light-danger text-danger ms-auto rounded-3">
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
                        <h4 class="my-1 fw-bold">{{ $completedCount }}</h4>
                        <p class="mb-0 font-13 text-muted">Closed and archived</p>
                    </div>
                    <div class="widgets-icons bg-light-success text-success ms-auto rounded-3">
                        <i class="bx bx-check-circle fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="d-lg-flex align-items-center mb-4 gap-3">
            <form method="GET" class="d-flex align-items-center flex-grow-1 row g-2">
                <div class="col-md-6 position-relative">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control ps-5 radius-30" placeholder="Search subject, customer, category">
                    <span class="position-absolute top-50 translate-middle-y ms-3"><i class="bx bx-search"></i></span>
                </div>
                <div class="col-md-4">
                    <select name="status" class="form-select radius-30">
                        <option value="">All Statuses</option>
                        @foreach(['pending','processing','completed','cancelled','closed'] as $status)
                            <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst(str_replace('_',' ',$status)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary radius-30 w-100">Filter</button>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Subject</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Assigned Consultant</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->subject }}</strong>
                                <div class="small text-muted">{{ $item->user?->name ?: 'Unknown customer' }}</div>
                            </td>
                            <td>{{ $item->category?->name ?: 'General' }}</td>
                            <td>
                                @if(in_array($item->status, ['completed', 'closed'], true))
                                    <div class="badge rounded-pill text-success bg-light-success p-2 text-uppercase px-3"><i class='bx bxs-circle align-middle me-1'></i>{{ ucfirst(str_replace('_',' ',$item->status)) }}</div>
                                @elseif($item->status === 'cancelled')
                                    <div class="badge rounded-pill text-danger bg-light-danger p-2 text-uppercase px-3"><i class='bx bxs-circle align-middle me-1'></i>{{ ucfirst(str_replace('_',' ',$item->status)) }}</div>
                                @else
                                    <div class="badge rounded-pill text-warning bg-light-warning p-2 text-uppercase px-3"><i class='bx bxs-circle align-middle me-1'></i>{{ ucfirst(str_replace('_',' ',$item->status)) }}</div>
                                @endif
                            </td>
                            <td>
                                @if($item->payment_status === 'paid')
                                    <div class="badge rounded-pill text-success bg-light-success p-2 text-uppercase px-3"><i class='bx bxs-circle align-middle me-1'></i>Paid</div>
                                @elseif($item->payment_status === 'failed')
                                    <div class="badge rounded-pill text-danger bg-light-danger p-2 text-uppercase px-3"><i class='bx bxs-circle align-middle me-1'></i>Failed</div>
                                @else
                                    <div class="badge rounded-pill text-warning bg-light-warning p-2 text-uppercase px-3"><i class='bx bxs-circle align-middle me-1'></i>{{ ucfirst($item->payment_status) }}</div>
                                @endif
                            </td>
                            <td>{{ $item->assignedConsultant?->name ?? 'Unassigned' }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.consultancy.show', $item) }}" class="btn btn-sm btn-primary radius-30 px-3">Open</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No consultancy requests found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($requests->hasPages())
        <div class="mt-3">
            {{ $requests->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
