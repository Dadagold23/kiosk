@extends('layouts.admin')

@section('meta_title', 'Admin Bookings | Kiosk')
@section('page_title', 'Booking Requests')
@section('page_subtitle', 'Track reservation workflows, payment state, and travel or stay details from one board.')

@section('content')
@php
    $confirmedCount = $bookings->getCollection()->where('status', 'confirmed')->count();
    $pendingPaymentCount = $bookings->getCollection()->where('payment_status', 'pending')->count();
@endphp

<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Reservations</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">Bookings Queue</li>
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
                        <p class="mb-0 text-secondary">Visible Bookings</p>
                        <h4 class="my-1 fw-bold">{{ $bookings->total() }}</h4>
                        <p class="mb-0 font-13 text-muted">Currently filtered list</p>
                    </div>
                    <div class="widgets-icons bg-light-primary text-primary ms-auto rounded-3">
                        <i class="bx bx-calendar fs-4"></i>
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
                        <p class="mb-0 text-secondary">Confirmed</p>
                        <h4 class="my-1 fw-bold">{{ $confirmedCount }}</h4>
                        <p class="mb-0 font-13 text-muted">Ready for processing</p>
                    </div>
                    <div class="widgets-icons bg-light-success text-success ms-auto rounded-3">
                        <i class="bx bx-check-circle fs-4"></i>
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
                        <p class="mb-0 text-secondary">Pending Payment</p>
                        <h4 class="my-1 fw-bold">{{ $pendingPaymentCount }}</h4>
                        <p class="mb-0 font-13 text-muted">Awaiting confirmation</p>
                    </div>
                    <div class="widgets-icons bg-light-warning text-warning ms-auto rounded-3">
                        <i class="bx bx-time fs-4"></i>
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
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control ps-5 radius-30" placeholder="Search customer, location, booking type">
                    <span class="position-absolute top-50 translate-middle-y ms-3"><i class="bx bx-search"></i></span>
                </div>
                <div class="col-md-4">
                    <select name="status" class="form-select radius-30">
                        <option value="">All Statuses</option>
                        @foreach(['pending','confirmed','completed','cancelled'] as $status)
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
                        <th>Booking Type</th>
                        <th>Customer</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Amount</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $item)
                        <tr>
                            <td><strong>{{ ucfirst(str_replace('_', ' ', $item->booking_type)) }}</strong></td>
                            <td>{{ $item->user?->name ?: 'Unknown customer' }}</td>
                            <td>{{ $item->location ?: 'N/A' }}</td>
                            <td>
                                @if(in_array($item->status, ['confirmed', 'completed'], true))
                                    <div class="badge rounded-pill text-success bg-light-success p-2 text-uppercase px-3"><i class='bx bxs-circle align-middle me-1'></i>{{ ucfirst($item->status) }}</div>
                                @elseif($item->status === 'cancelled')
                                    <div class="badge rounded-pill text-danger bg-light-danger p-2 text-uppercase px-3"><i class='bx bxs-circle align-middle me-1'></i>{{ ucfirst($item->status) }}</div>
                                @else
                                    <div class="badge rounded-pill text-warning bg-light-warning p-2 text-uppercase px-3"><i class='bx bxs-circle align-middle me-1'></i>{{ ucfirst($item->status) }}</div>
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
                            <td class="fw-bold">₦{{ number_format($item->amount, 2) }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.bookings.show', $item) }}" class="btn btn-sm btn-primary radius-30 px-3">Open</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No booking requests found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bookings->hasPages())
        <div class="mt-3">
            {{ $bookings->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
