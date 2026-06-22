@extends('layouts.admin')

@section('meta_title', 'Booking Details | Kiosk')
@section('page_title', 'Booking Details')
@section('page_subtitle', 'Review reservation specifics, payment progress, and confirmation state before closure')

@section('content')
<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Reservations</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.bookings.index') }}">Bookings Queue</a></li>
                <li class="breadcrumb-item active" aria-current="page">Booking #{{ $booking->id }}</li>
            </ol>
        </nav>
    </div>
    <div class="ms-auto">
        <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary radius-30"><i class="bx bx-arrow-back me-1"></i>Back to List</a>
    </div>
</div>
<!--end breadcrumb-->

<div class="row g-4">
    <!-- Booking Information -->
    <div class="col-xl-7">
        <div class="card radius-10 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                    <div>
                        <h5 class="mb-0 fw-bold">{{ ucfirst(str_replace('_', ' ', $booking->booking_type)) }}</h5>
                        <p class="mb-0 text-muted small">Submitted on {{ $booking->created_at->format('d M Y, h:i A') }}</p>
                    </div>
                    <div class="ms-auto">
                        @if(in_array($booking->status, ['confirmed', 'completed'], true))
                            <span class="badge rounded-pill text-success bg-light-success p-2 text-uppercase px-3"><i class='bx bxs-circle align-middle me-1'></i>{{ ucfirst($booking->status) }}</span>
                        @elseif($booking->status === 'cancelled')
                            <span class="badge rounded-pill text-danger bg-light-danger p-2 text-uppercase px-3"><i class='bx bxs-circle align-middle me-1'></i>{{ ucfirst($booking->status) }}</span>
                        @else
                            <span class="badge rounded-pill text-warning bg-light-warning p-2 text-uppercase px-3"><i class='bx bxs-circle align-middle me-1'></i>{{ ucfirst($booking->status) }}</span>
                        @endif
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <div class="p-3 radius-10 bg-light">
                            <small class="text-muted d-block text-uppercase mb-1">Customer</small>
                            <h6 class="mb-0 fw-bold">{{ $booking->user?->name ?: 'Unknown customer' }}</h6>
                            <small class="text-muted">{{ $booking->user?->email }}</small>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 radius-10 bg-light">
                            <small class="text-muted d-block text-uppercase mb-1">Location</small>
                            <h6 class="mb-0 fw-bold">{{ $booking->location ?: 'Not provided' }}</h6>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 radius-10 bg-light">
                            <small class="text-muted d-block text-uppercase mb-1">Title / Preference</small>
                            <h6 class="mb-0 fw-bold">{{ $booking->title ?: 'Not provided' }}</h6>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 radius-10 bg-light">
                            <small class="text-muted d-block text-uppercase mb-1">Persons</small>
                            <h6 class="mb-0 fw-bold">{{ $booking->persons }}</h6>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="p-3 radius-10 bg-light">
                            <small class="text-muted d-block text-uppercase mb-1">Check-in</small>
                            <h6 class="mb-0 fw-bold">{{ $booking->check_in_date?->format('d M Y') ?: 'N/A' }}</h6>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="p-3 radius-10 bg-light">
                            <small class="text-muted d-block text-uppercase mb-1">Check-out</small>
                            <h6 class="mb-0 fw-bold">{{ $booking->check_out_date?->format('d M Y') ?: 'N/A' }}</h6>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="p-3 radius-10 bg-light">
                            <small class="text-muted d-block text-uppercase mb-1">Travel Date</small>
                            <h6 class="mb-0 fw-bold">{{ $booking->travel_date?->format('d M Y') ?: 'N/A' }}</h6>
                        </div>
                    </div>
                </div>

                <div class="p-3 radius-10 bg-light-primary text-primary-emphasis mb-0">
                    <span class="d-block text-uppercase small fw-bold mb-2">Additional Details</span>
                    <p class="mb-0">{{ $booking->details ?: 'No additional details provided.' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions & Reviews -->
    <div class="col-xl-5">
        <div class="card radius-10 border-0 shadow-sm mb-4">
            <div class="card-body">
                <h5 class="mb-3 fw-bold">Update Booking</h5>
                <form action="{{ route('admin.bookings.update', $booking) }}" method="POST" class="row g-3">
                    @csrf

                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-uppercase text-secondary">Status</label>
                        <select name="status" class="form-select radius-30">
                            @foreach(['pending','reviewing','confirmed','processing','completed','cancelled'] as $status)
                                <option value="{{ $status }}" @selected($booking->status === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-uppercase text-secondary">Payment Status</label>
                        <select name="payment_status" class="form-select radius-30">
                            @foreach(['pending','paid','failed','review'] as $paymentStatus)
                                <option value="{{ $paymentStatus }}" @selected($booking->payment_status === $paymentStatus)>{{ ucfirst($paymentStatus) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold small text-uppercase text-secondary">Confirmation Code</label>
                        <input type="text" name="confirmation_code" value="{{ old('confirmation_code', $booking->confirmation_code) }}" class="form-control radius-30">
                    </div>

                    <div class="col-12 mt-4 text-end">
                        <button type="submit" class="btn btn-primary radius-30 w-100">Update Booking</button>
                    </div>
                </form>
            </div>
        </div>

        @include('partials.reviews.admin-panel', [
            'reviews' => $booking->reviews,
            'title' => 'Booking Reviews',
            'subtitle' => 'Moderate travel and reservation feedback before it is used as public proof.',
            'wrapperClass' => 'card radius-10 border-0 shadow-sm mt-4 p-4',
        ])
    </div>
</div>
@endsection

