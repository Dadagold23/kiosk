@extends('layouts.customer')

@section('customer_page_title', 'Order Details')
@section('customer_page_subtitle', 'Track payment, logistics, and delivery progress for each item in your order.')

@include('partials.amerce.account-detail-styles')

@section('customer_body')
@php
    $paymentTone = $order->payment_status === \App\Models\Payment::STATUS_PAID ? 'success' : 'warning'; $statusTone = in_array($order->order_status, ['delivered', 'completed'], true) ? 'success' : (in_array($order->order_status, ['cancelled', 'closed'], true) ? 'danger' : 'primary');
@endphp

<div class="customer-page-grid">
    <div class="customer-card customer-page-block muara-detail-hero">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
            <div>
                <span class="customer-status-pill is-primary mb-3">Order Tracking</span>
                <h1 class="fw-bold mb-2">Order {{ $order->order_no }}</h1>
                <p class="muara-detail-copy mb-0">Placed on {{ $order->created_at->format('d M Y, h:i A') }} and available for payment review, logistics follow-up, and receipt access.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('orders.index') }}" class="customer-soft-button">Back to Orders</a>
                @if($order->payment_status !== \App\Models\Payment::STATUS_PAID)
                    <form action="{{ route('orders.pay', $order) }}" method="POST">
                        @csrf
                        <button class="customer-soft-button border-0">Retry Paystack Payment</button>
                    </form>
                @endif
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <div class="muara-detail-stat h-100">
                    <div class="label">Payment Status</div>
                    <div class="value">{{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="muara-detail-stat h-100">
                    <div class="label">Order Status</div>
                    <div class="value">{{ ucfirst(str_replace('_', ' ', $order->order_status)) }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="muara-detail-stat h-100">
                    <div class="label">Grand Total</div>
                    <div class="value">&#8358;{{ number_format($order->total, 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="customer-card customer-page-block">
                <div class="customer-panel-head">
                    <div>
                        <span class="customer-eyebrow">Order Items</span>
                        <h3 class="customer-section-title">Fulfillment line items</h3>
                        <p class="customer-section-copy">Each item keeps its own logistics, tracking, and status trail inside the main order.</p>
                    </div>
                </div>

                <div class="customer-page-grid">
                    @foreach($order->items as $item)
                        <article class="muara-record-card">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                                <div class="d-flex gap-3 align-items-start">
                                    <img src="{{ $item->image_url }}" alt="{{ $item->product_name }}" class="rounded-4 object-fit-cover" style="height:88px; width:88px;">
                                    <div>
                                        <div class="record-title mb-1">{{ $item->product_name }}</div>
                                        <div class="record-date">Qty: {{ $item->qty }} | Unit: &#8358;{{ number_format($item->unit_price, 2) }}</div>
                                        <span class="customer-status-pill is-{{ $paymentTone }}">{{ ucfirst(str_replace('_', ' ', $item->fulfillment_status)) }}</span>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="record-date">Subtotal</div>
                                    <strong>&#8358;{{ number_format($item->subtotal, 2) }}</strong>
                                </div>
                            </div>

                            <div class="customer-info-grid mt-3">
                                <div class="customer-info-card">
                                    <span class="label">Logistics Partner</span>
                                    <span class="value">{{ $item->logistics_partner ?: 'Awaiting assignment' }}</span>
                                </div>
                                <div class="customer-info-card">
                                    <span class="label">Tracking Number</span>
                                    <span class="value">{{ $item->tracking_number ?: 'Pending' }}</span>
                                </div>
                                <div class="customer-info-card">
                                    <span class="label">Latest Update</span>
                                    <span class="value">{{ $item->last_tracked_at?->format('d M Y, h:i A') ?: 'Not updated yet' }}</span>
                                </div>
                                <div class="customer-info-card">
                                    <span class="label">Tracking Link</span>
                                    <span class="value">{{ $item->tracking_url ? 'Available' : 'Pending' }}</span>
                                </div>
                            </div>

                            @if($item->tracking_url)
                                <a href="{{ $item->tracking_url }}" target="_blank" rel="noopener noreferrer" class="customer-soft-button mt-3">Open Tracking Link</a>
                            @endif

                            @if($item->trackingEvents->isNotEmpty())
                                <div class="mt-4">
                                    <h6 class="fw-bold mb-3">Tracking Timeline</h6>
                                    <div class="muara-timeline">
                                        @foreach($item->trackingEvents->take(5) as $event)
                                            <div class="muara-timeline-item">
                                                <div class="content">
                                                    <div class="fw-semibold">{{ ucfirst(str_replace('_', ' ', $event->status)) }}</div>
                                                    <div class="small text-muted">{{ $event->event_time?->format('d M Y, h:i A') ?: 'Update time pending' }}</div>
                                                    @if($event->location)
                                                        <div class="small mt-2"><strong>Location:</strong> {{ $event->location }}</div>
                                                    @endif
                                                    @if($event->note)
                                                        <div class="small mt-1">{{ $event->note }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </article>
                    @endforeach
                </div>
            </div>

            <div class="customer-card customer-page-block">
                <div class="customer-panel-head">
                    <div>
                        <span class="customer-eyebrow">Delivery Address</span>
                        <h3 class="customer-section-title">Where this order is going</h3>
                    </div>
                </div>
                <div class="customer-panel-note">{{ $order->delivery_address }}</div>
            </div>

            @if($order->notes)
                <div class="customer-card customer-page-block">
                    <div class="customer-panel-head">
                        <div>
                            <span class="customer-eyebrow">Notes</span>
                            <h3 class="customer-section-title">Order notes</h3>
                        </div>
                    </div>
                    <div class="customer-panel-note">{{ $order->notes }}</div>
                </div>
            @endif

            @include('partials.reviews.customer-panel', [
                'reviewType' => 'order',
                'reviewable' => $order,
                'existingReview' => $existingReview,
                'canSubmitReview' => $canSubmitReview,
            ])
        </div>

        <div class="col-xl-4">
            <div class="customer-card customer-page-block">
                <div class="customer-panel-head">
                    <div>
                        <span class="customer-eyebrow">Summary</span>
                        <h3 class="customer-section-title">Order totals</h3>
                        <p class="customer-section-copy">A cleaner breakdown of all charges tied to this order.</p>
                    </div>
                </div>

                <div class="customer-info-grid">
                    <div class="customer-info-card">
                        <span class="label">Subtotal</span>
                        <span class="value">&#8358;{{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    <div class="customer-info-card">
                        <span class="label">Delivery Fee</span>
                        <span class="value">&#8358;{{ number_format($order->delivery_fee, 2) }}</span>
                    </div>
                    <div class="customer-info-card">
                        <span class="label">Service Charge</span>
                        <span class="value">&#8358;{{ number_format($order->service_charge, 2) }}</span>
                    </div>
                    <div class="customer-info-card">
                        <span class="label">Total</span>
                        <span class="value">&#8358;{{ number_format($order->total, 2) }}</span>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2 mt-3">
                    <span class="customer-status-pill is-{{ $paymentTone }}">{{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}</span>
                    <span class="customer-status-pill is-{{ $statusTone }}">{{ ucfirst(str_replace('_', ' ', $order->order_status)) }}</span>
                </div>

                <div class="mt-4">
                    <h6 class="fw-bold mb-3">Payment Records</h6>
                    @if($order->payments->isNotEmpty())
                        <div class="muara-side-stack">
                            @foreach($order->payments as $payment)
                                <div class="muara-payment-card">
                                    <div><strong>Ref:</strong> {{ $payment->reference }}</div>
                                    <div class="small text-muted mt-1"><strong>Method:</strong> {{ ucfirst(str_replace('_', ' ', $payment->payment_method ?? 'n/a')) }}</div>
                                    <div class="small text-muted"><strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $payment->status)) }}</div>
                                    <div class="small text-muted"><strong>Amount:</strong> &#8358;{{ number_format($payment->amount, 2) }}</div>
                                    <a href="{{ route('receipts.show', $payment) }}" class="customer-soft-button mt-3">View Receipt</a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="customer-panel-note">No payment records are attached to this order yet.</div>
                    @endif
                </div>
            </div>

            <div class="customer-card customer-page-block">
                <div class="customer-panel-head">
                    <div>
                        <span class="customer-eyebrow">What Next</span>
                        <h3 class="customer-section-title">Follow the flow</h3>
                    </div>
                </div>
                <div class="customer-panel-note">Your order items continue to update as sourcing, dispatch, and delivery progress. Use the tracking links above whenever a logistics provider has been attached to an item.</div>
            </div>
        </div>
    </div>
</div>
@endsection
