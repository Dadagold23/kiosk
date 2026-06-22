@extends('layouts.admin')

@section('meta_title', 'Emergency Desk | Kiosk Admin')
@section('page_title', 'Emergency Desk')
@section('page_subtitle', 'Monitor emergency alerts, assignments, and live field movement from a calmer high-priority queue.')

@section('content')
@php
    $activeCount = $requests->getCollection()->whereNotIn('status', ['resolved', 'closed'])->count();
    $resolvedCount = $requests->getCollection()->where('status', 'resolved')->count();
@endphp

<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Emergency</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">Emergency Desk</li>
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
                        <p class="mb-0 text-secondary">Visible Alerts</p>
                        <h4 class="my-1 fw-bold">{{ $requests->total() }}</h4>
                        <p class="mb-0 font-13 text-muted">Currently active list</p>
                    </div>
                    <div class="widgets-icons bg-light-primary text-primary ms-auto rounded-3">
                        <i class="bx bx-error fs-4"></i>
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
                        <p class="mb-0 text-secondary">Active cases</p>
                        <h4 class="my-1 fw-bold">{{ $activeCount }}</h4>
                        <p class="mb-0 font-13 text-muted">Pending live response</p>
                    </div>
                    <div class="widgets-icons bg-light-danger text-danger ms-auto rounded-3">
                        <i class="bx bx-run fs-4"></i>
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
                        <p class="mb-0 text-secondary">Resolved</p>
                        <h4 class="my-1 fw-bold">{{ $resolvedCount }}</h4>
                        <p class="mb-0 font-13 text-muted">Safely closed events</p>
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
                <div class="col-lg-4 col-md-6 position-relative">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control ps-5 radius-30" placeholder="Search requester, phone, location, unit">
                    <span class="position-absolute top-50 translate-middle-y ms-3"><i class="bx bx-search"></i></span>
                </div>
                <div class="col-lg-2 col-md-6">
                    <select name="status" class="form-select radius-30">
                        <option value="">All Statuses</option>
                        @foreach(config('kiosk.emergency.statuses', []) as $status)
                            <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <select name="emergency_type" class="form-select radius-30">
                        <option value="">All Types</option>
                        @foreach(config('kiosk.emergency.types', []) as $type)
                            <option value="{{ $type }}" @selected(request('emergency_type') === $type)>{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <select name="state_name" class="form-select radius-30">
                        <option value="">All States</option>
                        @foreach($states as $state)
                            <option value="{{ $state }}" @selected(request('state_name') === $state)>{{ $state }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <button class="btn btn-primary radius-30 w-100">Filter</button>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Type</th>
                        <th>Requester</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Assigned Unit</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $item)
                        <tr>
                            <td>
                                <strong>{{ ucfirst(str_replace('_', ' ', $item->emergency_type)) }}</strong>
                                <div class="small text-muted">{{ $item->created_at->format('d M Y, h:i A') }}</div>
                            </td>
                            <td>
                                <div>{{ $item->full_name ?: 'Anonymous/Guest' }}</div>
                                <div class="small text-muted">{{ $item->phone }}</div>
                            </td>
                            <td>
                                <div>{{ $item->state_name ?: 'Unknown state' }}</div>
                                <div class="small text-muted">{{ $item->local_government_area ?: ($item->location_text ?: 'No location') }}</div>
                            </td>
                            <td>
                                @if(in_array($item->status, ['resolved', 'closed'], true))
                                    <span class="badge rounded-pill text-success bg-light-success p-2 text-uppercase px-3"><i class='bx bxs-circle align-middle me-1'></i>{{ ucfirst(str_replace('_', ' ', $item->status)) }}</span>
                                @else
                                    <span class="badge rounded-pill text-danger bg-light-danger p-2 text-uppercase px-3"><i class='bx bxs-circle align-middle me-1'></i>{{ ucfirst(str_replace('_', ' ', $item->status)) }}</span>
                                @endif
                                @if($item->latestTrackingEvent)
                                    <div class="small text-muted mt-1">Latest: {{ ucfirst(str_replace('_', ' ', $item->latestTrackingEvent->status)) }}</div>
                                @endif
                            </td>
                            <td>
                                <div>{{ $item->assigned_unit ?: 'Unassigned' }}</div>
                                <div class="small text-muted">{{ $item->assigned_unit_contact ?: 'No responder contact yet' }}</div>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.emergency.show', $item) }}" class="btn btn-sm btn-primary radius-30 px-3">Open</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No emergency requests found.</td>
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
