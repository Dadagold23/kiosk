@extends('layouts.customer')

@section('customer_page_title', 'Booking Details')
@section('customer_page_subtitle', 'Review your booking progress, payment record, confirmation details, and final experience.')

@include('partials.amerce.account-detail-styles')

@section('customer_body')
<div class="customer-page-grid">
    <div class="customer-card customer-page-block muara-detail-hero is-success">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
            <div>
                <span class="customer-status-pill is-success mb-3">Kiosk Booking Desk</span>
                <h1 class="fw-bold mb-2">{{ ucfirst(str_replace('_', ' ', $booking->booking_type)) }} Request</h1>
                <p class="muara-detail-copy mb-0">Submitted on {{ $booking->created_at->format('d M Y, h:i A') }} and tracked across reservation, payment, and confirmation steps.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('customer.bookings.index') }}" class="customer-soft-button">Back to Bookings</a>
                @if($booking->payment_status !== \App\Models\Payment::STATUS_PAID && $booking->payments->isNotEmpty())
                    <form action="{{ route('customer.bookings.pay', $booking) }}" method="POST">
                        @csrf
                        <button class="customer-soft-button border-0">Retry Paystack</button>
                    </form>
                @endif
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <div class="muara-detail-stat h-100">
                    <div class="label">Booking Status</div>
                    <div class="value">{{ ucfirst(str_replace('_', ' ', $booking->status)) }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="muara-detail-stat h-100">
                    <div class="label">Payment Status</div>
                    <div class="value">{{ ucfirst($booking->payment_status) }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="muara-detail-stat h-100">
                    <div class="label">Amount</div>
                    <div class="value">NGN {{ number_format($booking->amount, 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="customer-card customer-page-block">
                <div class="customer-panel-head">
                    <div>
                        <span class="customer-eyebrow">Reservation Snapshot</span>
                        <h3 class="customer-section-title">Booking details</h3>
                        <p class="customer-section-copy">The main destination, timing, and booking preferences tied to this request.</p>
                    </div>
                </div>

                <div class="customer-info-grid mb-3">
                    <div class="customer-info-card">
                        <span class="label">Title or Preference</span>
                        <span class="value">{{ $booking->title ?: 'N/A' }}</span>
                    </div>
                    <div class="customer-info-card">
                        <span class="label">Location</span>
                        <span class="value">{{ $booking->location ?: 'N/A' }}</span>
                    </div>
                    <div class="customer-info-card">
                        <span class="label">Check-in</span>
                        <span class="value">{{ $booking->check_in_date?->format('d M Y') ?: 'N/A' }}</span>
                    </div>
                    <div class="customer-info-card">
                        <span class="label">Check-out</span>
                        <span class="value">{{ $booking->check_out_date?->format('d M Y') ?: 'N/A' }}</span>
                    </div>
                    <div class="customer-info-card">
                        <span class="label">Travel Date</span>
                        <span class="value">{{ $booking->travel_date?->format('d M Y') ?: 'N/A' }}</span>
                    </div>
                    <div class="customer-info-card">
                        <span class="label">Persons</span>
                        <span class="value">{{ $booking->persons }}</span>
                    </div>
                    <div class="customer-info-card" style="grid-column:1/-1;">
                        <span class="label">Confirmation Code</span>
                        <span class="value">{{ $booking->confirmation_code ?: 'Not available yet' }}</span>
                    </div>
                </div>

                <div class="customer-panel-note">
                    <div class="small text-uppercase text-muted mb-2">Request Details</div>
                    <div>{{ $booking->details ?: 'N/A' }}</div>
                </div>
            </div>

            @include('partials.reviews.customer-panel', [
                'reviewType' => 'booking',
                'reviewable' => $booking,
                'existingReview' => $existingReview,
                'canSubmitReview' => $canSubmitReview,
            ])
        </div>

        <div class="col-xl-4">
            <div class="customer-card customer-page-block">
                <div class="customer-panel-head">
                    <div>
                        <span class="customer-eyebrow">Receipts</span>
                        <h3 class="customer-section-title">Payment records</h3>
                        <p class="customer-section-copy">Use the receipt history below to confirm verification and reopen payment if needed.</p>
                    </div>
                </div>

                @if($booking->payments->isNotEmpty())
                    <div class="muara-side-stack">
                        @foreach($booking->payments as $payment)
                            <div class="muara-payment-card">
                                <div><strong>Ref:</strong> {{ $payment->reference }}</div>
                                <div class="small text-muted mt-1"><strong>Status:</strong> {{ ucfirst($payment->status) }}</div>
                                <div class="small text-muted"><strong>Method:</strong> {{ ucfirst(str_replace('_', ' ', $payment->payment_method ?? 'n/a')) }}</div>
                                <a href="{{ route('receipts.show', $payment) }}" class="customer-soft-button mt-3">View Receipt</a>
                                @if(in_array($payment->status, ['failed', 'under_review'], true))
                                    <form action="{{ route('customer.bookings.pay', $booking) }}" method="POST" class="mt-2">
                                        @csrf
                                        <button class="btn btn-sm btn-primary">Retry Paystack</button>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="customer-panel-note">No payment records are attached yet.</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
