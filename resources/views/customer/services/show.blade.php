@extends('layouts.customer')

@section('customer_page_title', 'Service Request Details')
@section('customer_page_subtitle', 'Track your request from review and assignment through on-site work and completion.')

@include('partials.amerce.account-detail-styles')

@section('customer_body')
<div class="customer-page-grid">
    <div class="customer-card customer-page-block muara-detail-hero">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
            <div>
                <span class="customer-status-pill is-primary mb-3">Kiosk Service Desk</span>
                <h1 class="fw-bold mb-2">{{ $serviceRequest->title }}</h1>
                <p class="muara-detail-copy mb-0">Submitted on {{ $serviceRequest->created_at->format('d M Y, h:i A') }} and tracked through review, assignment, field work, and closeout.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('customer.services.index') }}" class="customer-soft-button">Back to Services</a>
                @if($serviceRequest->payment_status !== \App\Models\Payment::STATUS_PAID && $serviceRequest->payments->isNotEmpty())
                    <form action="{{ route('customer.services.pay', $serviceRequest) }}" method="POST">
                        @csrf
                        <button class="customer-soft-button border-0">Retry Paystack</button>
                    </form>
                @endif
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <div class="muara-detail-stat h-100">
                    <div class="label">Request Status</div>
                    <div class="value">{{ ucfirst(str_replace('_', ' ', $serviceRequest->status)) }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="muara-detail-stat h-100">
                    <div class="label">Progress Stage</div>
                    <div class="value">{{ ucfirst(str_replace('_', ' ', $serviceRequest->progress_status ?: 'request_received')) }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="muara-detail-stat h-100">
                    <div class="label">Fee</div>
                    <div class="value">NGN {{ number_format($serviceRequest->fee, 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="customer-card customer-page-block">
                <div class="customer-panel-head">
                    <div>
                        <span class="customer-eyebrow">Request Snapshot</span>
                        <h3 class="customer-section-title">Service details</h3>
                        <p class="customer-section-copy">The original brief, timing, and delivery context tied to this request.</p>
                    </div>
                </div>

                <div class="customer-info-grid mb-3">
                    <div class="customer-info-card">
                        <span class="label">Category</span>
                        <span class="value">{{ $serviceRequest->category?->name ?: 'Not specified' }}</span>
                    </div>
                    <div class="customer-info-card">
                        <span class="label">Location</span>
                        <span class="value">{{ $serviceRequest->location ?: 'N/A' }}</span>
                    </div>
                    <div class="customer-info-card">
                        <span class="label">Preferred Date</span>
                        <span class="value">{{ $serviceRequest->preferred_date?->format('d M Y') ?: 'N/A' }}</span>
                    </div>
                    <div class="customer-info-card">
                        <span class="label">Budget</span>
                        <span class="value">{{ $serviceRequest->budget ? 'NGN ' . number_format($serviceRequest->budget, 2) : 'N/A' }}</span>
                    </div>
                </div>

                <div class="customer-panel-note">
                    <div class="small text-uppercase text-muted mb-2">Description</div>
                    <div>{{ $serviceRequest->description }}</div>
                </div>
            </div>

            <div class="customer-card customer-page-block">
                <div class="customer-panel-head">
                    <div>
                        <span class="customer-eyebrow">Live Progress</span>
                        <h3 class="customer-section-title">Tracking timeline</h3>
                        <p class="customer-section-copy">Follow each update from review to assignment, field work, and closeout.</p>
                    </div>
                    <span class="customer-status-pill is-primary">{{ ucfirst(str_replace('_', ' ', $serviceRequest->progress_status ?: 'request_received')) }}</span>
                </div>

                <div class="muara-timeline">
                    @forelse($serviceRequest->trackingEvents as $event)
                        <div class="muara-timeline-item">
                            <div class="content">
                                <div class="fw-semibold">{{ ucfirst(str_replace('_', ' ', $event->status)) }}</div>
                                <div class="small text-muted">{{ $event->event_time?->format('d M Y, h:i A') ?: 'Pending time' }}</div>
                                @if($event->location)
                                    <div class="small mt-2"><strong>Location:</strong> {{ $event->location }}</div>
                                @endif
                                @if($event->next_step)
                                    <div class="small"><strong>Next step:</strong> {{ $event->next_step }}</div>
                                @endif
                                @if($event->note)
                                    <div class="small mt-1">{{ $event->note }}</div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="customer-panel-note">No service tracking updates have been added yet.</div>
                    @endforelse
                </div>
            </div>

            @if(!empty($serviceRequest->image_urls))
                <div class="customer-card customer-page-block">
                    <div class="customer-panel-head">
                        <div>
                            <span class="customer-eyebrow">Attachments</span>
                            <h3 class="customer-section-title">Uploaded images</h3>
                        </div>
                    </div>

                    <div class="row g-3">
                        @foreach($serviceRequest->image_urls as $imageUrl)
                            <div class="col-md-4">
                                <img src="{{ $imageUrl }}" class="img-fluid rounded-4 border" alt="Service request image">
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @include('partials.reviews.customer-panel', [
                'reviewType' => 'service',
                'reviewable' => $serviceRequest,
                'existingReview' => $existingReview,
                'canSubmitReview' => $canSubmitReview,
            ])
        </div>

        <div class="col-xl-4">
            <div class="customer-card customer-page-block">
                <div class="customer-panel-head">
                    <div>
                        <span class="customer-eyebrow">Assignment</span>
                        <h3 class="customer-section-title">Payment and field team</h3>
                        <p class="customer-section-copy">Everything tied to payment verification and the people handling the job.</p>
                    </div>
                </div>

                <div class="muara-side-stack">
                    <div class="muara-panel-card">
                        <div class="label">Payment Status</div>
                        <div class="value">{{ ucfirst($serviceRequest->payment_status) }}</div>
                    </div>
                    <div class="muara-panel-card">
                        <div class="label">Assigned Staff</div>
                        <div class="value">{{ $serviceRequest->assignedStaff?->name ?? 'Not assigned yet' }}</div>
                    </div>
                    <div class="muara-panel-card">
                        <div class="label">Assigned Team</div>
                        <div class="value">{{ $serviceRequest->assigned_team ?: 'Pending team assignment' }}</div>
                    </div>
                    <div class="muara-panel-card">
                        <div class="label">Service Window</div>
                        <div class="value">
                            {{ $serviceRequest->service_window_start?->format('d M Y, h:i A') ?: 'Pending' }}
                            to
                            {{ $serviceRequest->service_window_end?->format('d M Y, h:i A') ?: 'Pending' }}
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <h6 class="fw-bold mb-3">Payment Records</h6>
                    @if($serviceRequest->payments->isNotEmpty())
                        <div class="muara-side-stack">
                            @foreach($serviceRequest->payments as $payment)
                                <div class="muara-payment-card">
                                    <div><strong>Ref:</strong> {{ $payment->reference }}</div>
                                    <div class="small text-muted mt-1"><strong>Status:</strong> {{ ucfirst($payment->status) }}</div>
                                    <div class="small text-muted"><strong>Method:</strong> {{ ucfirst(str_replace('_', ' ', $payment->payment_method ?? 'n/a')) }}</div>
                                    <a href="{{ route('receipts.show', $payment) }}" class="customer-soft-button mt-3">View Receipt</a>
                                    @if(in_array($payment->status, ['failed', 'under_review'], true))
                                        <form action="{{ route('customer.services.pay', $serviceRequest) }}" method="POST" class="mt-2">
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
</div>
@endsection
