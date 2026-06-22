@extends('layouts.customer')

@section('customer_page_title', 'Create Booking')
@section('customer_page_subtitle', 'Share your travel or stay details and let the booking team take it from there.')

@include('partials.amerce.account-intake-styles')

@section('customer_body')
<div class="customer-page-grid">
    <div class="feature-card p-4 p-lg-5 amerce-intake-hero is-success">
        <div class="row g-4 align-items-center">
            <div class="col-lg-8">
                <span class="customer-status-pill is-success mb-3">Managed Reservations</span>
                <h1 class="fw-bold mb-2">Share the trip or stay details once and let the booking desk handle the workflow.</h1>
                <p class="amerce-intake-copy mb-0">After submission, the reservations team sources options, confirms requirements, and guides payment toward final confirmation.</p>
            </div>
            <div class="col-lg-4">
                <div class="amerce-intake-stat">
                    <div class="label">Entry Fee Range</div>
                    <div class="value">NGN 5,000 - 10,000</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 align-items-start">
        <div class="col-xl-8">
            <div class="feature-card customer-page-block">
                <div class="mb-4">
                    <div class="customer-eyebrow">Reservation Intake</div>
                    <h3 class="customer-section-title">Create booking request</h3>
                    <p class="customer-section-copy">Capture your destination, timing, preference, and traveler details so the desk can source options faster.</p>
                </div>

                <form action="{{ route('customer.bookings.store') }}" method="POST">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="amerce-field-block">
                                <label class="form-label">Booking Type</label>
                                <select name="booking_type" class="form-select @error('booking_type') is-invalid @enderror">
                                    <option value="">Select booking type</option>
                                    @foreach($types as $key => $label)
                                        <option value="{{ $key }}" @selected(old('booking_type') === $key)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('booking_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="amerce-field-block">
                                <label class="form-label">Title or Preference</label>
                                <input type="text" name="title" value="{{ old('title') }}" class="form-control @error('title') is-invalid @enderror" placeholder="Preferred hotel, airline, lounge, park">
                                @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="amerce-field-block">
                                <label class="form-label">Location or Destination</label>
                                <input type="text" name="location" value="{{ old('location') }}" class="form-control @error('location') is-invalid @enderror" placeholder="City, route, destination">
                                @error('location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="amerce-field-block">
                                <label class="form-label">Check-in Date</label>
                                <input type="date" name="check_in_date" value="{{ old('check_in_date') }}" class="form-control @error('check_in_date') is-invalid @enderror">
                                @error('check_in_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="amerce-field-block">
                                <label class="form-label">Check-out Date</label>
                                <input type="date" name="check_out_date" value="{{ old('check_out_date') }}" class="form-control @error('check_out_date') is-invalid @enderror">
                                @error('check_out_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="amerce-field-block">
                                <label class="form-label">Travel Date</label>
                                <input type="date" name="travel_date" value="{{ old('travel_date') }}" class="form-control @error('travel_date') is-invalid @enderror">
                                @error('travel_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="amerce-field-block">
                                <label class="form-label">Number of Persons</label>
                                <input type="number" name="persons" min="1" max="50" value="{{ old('persons', 1) }}" class="form-control @error('persons') is-invalid @enderror">
                                @error('persons') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="amerce-field-block">
                                <label class="form-label">Booking Details</label>
                                <textarea name="details" rows="5" class="form-control @error('details') is-invalid @enderror" placeholder="Provide complete booking information...">{{ old('details') }}</textarea>
                                @error('details') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <button class="customer-btn-primary btn mt-4">Submit Booking Request</button>
                </form>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="feature-card customer-page-block">
                <div class="customer-eyebrow">Fee Guide</div>
                <h3 class="customer-section-title">Booking fee guide</h3>
                <div class="amerce-support-card mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Regular bookings</span>
                        <strong>NGN 5,000.00</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Flight bookings</span>
                        <strong>NGN 10,000.00</strong>
                    </div>
                </div>
                <p class="customer-section-copy">After submission, we create the request and redirect you to Paystack to complete payment securely.</p>
            </div>
        </div>
    </div>
</div>
@endsection
