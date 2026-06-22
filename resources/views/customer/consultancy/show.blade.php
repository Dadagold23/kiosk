@extends('layouts.customer')

@section('customer_page_title', 'Consultancy Details')
@section('customer_page_subtitle', 'Review consultant assignment, payment activity, and the final delivery outcome inside the updated account experience.')

@include('partials.amerce.account-detail-styles')

@section('customer_body')
<div class="customer-page-grid">
    <div class="feature-card p-4 p-lg-5 amerce-detail-hero is-warning">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
            <div>
                <span class="customer-status-pill is-warning mb-3">Kiosk Advisory Desk</span>
                <h1 class="fw-bold mb-2">{{ $consultancyRequest->subject }}</h1>
                <p class="amerce-detail-copy mb-0">Submitted on {{ $consultancyRequest->created_at->format('d M Y, h:i A') }} and managed through review, consultant assignment, and delivery.</p>
            </div>
            <a href="{{ route('customer.consultancy.index') }}" class="customer-soft-button">Back to Consultancy</a>
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <div class="amerce-detail-stat h-100">
                    <div class="label">Request Status</div>
                    <div class="value">{{ ucfirst(str_replace('_', ' ', $consultancyRequest->status)) }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="amerce-detail-stat h-100">
                    <div class="label">Payment Status</div>
                    <div class="value">{{ ucfirst($consultancyRequest->payment_status) }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="amerce-detail-stat h-100">
                    <div class="label">Fee</div>
                    <div class="value">NGN {{ number_format($consultancyRequest->fee, 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="feature-card customer-page-block">
                <div class="mb-3">
                    <div class="customer-eyebrow">Case Snapshot</div>
                    <h3 class="customer-section-title">Consultancy details</h3>
                    <p class="customer-section-copy">Everything attached to the request before the assigned consultant begins delivery.</p>
                </div>

                <div class="customer-info-grid mb-3">
                    <div class="customer-info-card">
                        <span class="label">Category</span>
                        <span class="value">{{ $consultancyRequest->category?->name ?: 'Not specified' }}</span>
                    </div>
                    <div class="customer-info-card">
                        <span class="label">Preferred Date</span>
                        <span class="value">{{ $consultancyRequest->preferred_date?->format('d M Y') ?: 'N/A' }}</span>
                    </div>
                </div>

                <div class="customer-panel-note">
                    <div class="small text-uppercase text-muted mb-2">Description</div>
                    <div>{{ $consultancyRequest->description }}</div>
                </div>
            </div>

            @include('partials.reviews.customer-panel', [
                'reviewType' => 'consultancy',
                'reviewable' => $consultancyRequest,
                'existingReview' => $existingReview,
                'canSubmitReview' => $canSubmitReview,
            ])
        </div>

        <div class="col-xl-4">
            <div class="feature-card customer-page-block">
                <div class="mb-3">
                    <div class="customer-eyebrow">Assignment</div>
                    <h3 class="customer-section-title">Consultant and payment</h3>
                    <p class="customer-section-copy">Track ownership and reopen payment if the original redirect was interrupted.</p>
                </div>

                <div class="customer-info-card mb-3">
                    <span class="label">Assigned Consultant</span>
                    <span class="value">{{ $consultancyRequest->assignedConsultant?->name ?? 'Not assigned yet' }}</span>
                </div>

                @if($consultancyRequest->payments->isNotEmpty())
                    <div class="d-grid gap-3">
                        @foreach($consultancyRequest->payments as $payment)
                            <div class="amerce-payment-card">
                                <div><strong>Ref:</strong> {{ $payment->reference }}</div>
                                <div><strong>Status:</strong> {{ ucfirst($payment->status) }}</div>
                                <div><strong>Method:</strong> {{ ucfirst(str_replace('_', ' ', $payment->payment_method ?? 'n/a')) }}</div>
                                <a href="{{ route('receipts.show', $payment) }}" class="btn btn-sm btn-outline-secondary mt-2">View Receipt</a>
                                @if(in_array($payment->status, ['failed', 'under_review'], true))
                                    <form action="{{ route('customer.consultancy.pay', $consultancyRequest) }}" method="POST" class="mt-2">
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
