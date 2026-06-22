@extends('layouts.customer')

@section('customer_page_title', 'My Orders')
@section('customer_page_subtitle', 'Track payment, fulfillment, and delivery across your orders.')

@section('customer_body')
@php
    $paidCount = $orders->getCollection()->where('payment_status', 'paid')->count(); $deliveryCount = $orders->getCollection()->where('order_status', 'delivered')->count(); $pendingCount = $orders->getCollection()->whereIn('order_status', ['pending', 'reviewing', 'processing', 'sourced', 'dispatched'])->count();
@endphp

<div class="customer-page-grid">
    <div class="customer-card customer-page-block muara-module-hero muara-module-hero-primary">
        <div class="row g-4 align-items-center">
            <div class="col-lg-8">
                <span class="customer-welcome-chip">Order History</span>
                <h2 class="fw-bold mb-2">Track every checkout from payment through delivery.</h2>
                <p class="mb-0 muara-module-copy">See your recent orders, payment status, and delivery updates in one place.</p>
            </div>
            <div class="col-lg-4">
                <div class="muara-summary-grid">
                    <div class="muara-summary-card"><div class="muara-summary-label">Visible orders</div><div class="muara-summary-value">{{ $orders->total() }}</div></div>
                    <div class="muara-summary-card"><div class="muara-summary-label">Paid</div><div class="muara-summary-value">{{ $paidCount }}</div></div>
                    <div class="muara-summary-card"><div class="muara-summary-label">Delivered</div><div class="muara-summary-value">{{ $deliveryCount }}</div></div>
                    <div class="muara-summary-card"><div class="muara-summary-label">In progress</div><div class="muara-summary-value">{{ $pendingCount }}</div></div>
                </div>
            </div>
        </div>
    </div>

    <div class="customer-panel-head">
        <div>
            <span class="customer-eyebrow">Order Stream</span>
            <h3 class="customer-section-title">My Orders</h3>
            <p class="customer-section-copy">Review payments, delivery status, and order totals without digging through old screens.</p>
        </div>
        <a href="{{ route('shop.index') }}" class="btn customer-btn-primary">Shop Again</a>
    </div>

    <div class="row g-4">
        @forelse($orders as $order)
            @php
                $paymentTone = match($order->payment_status) {
                    'paid' => 'success', 'failed', 'cancelled' => 'danger', default => 'warning',
                };
                $statusTone = match($order->order_status) {
                    'delivered', 'completed' => 'success', 'cancelled', 'canceled', 'closed' => 'danger', default => 'primary',
                };
            @endphp
            <div class="col-md-6 col-xl-4">
                <article class="muara-record-card h-100">
                    <div class="d-flex justify-content-between align-items-start gap-3">
                        <div>
                            <div class="record-kicker">{{ ucfirst(str_replace('_', ' ', $order->order_type)) }}</div>
                            <div class="record-title">{{ $order->order_no }}</div>
                            <div class="record-date">{{ $order->created_at->format('d M Y, h:i A') }}</div>
                        </div>
                        <a href="{{ route('orders.show', $order->order_no) }}" class="customer-soft-button">Open</a>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <span class="customer-status-pill is-{{ $paymentTone }}">{{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}</span>
                        <span class="customer-status-pill is-{{ $statusTone }}">{{ ucfirst(str_replace('_', ' ', $order->order_status)) }}</span>
                    </div>

                    <div class="muara-meta-stack">
                        <div class="muara-meta-row"><span>Total</span><strong>&#8358;{{ number_format($order->total, 2) }}</strong></div>
                        <div class="muara-meta-row"><span>Payment Ref</span><strong>{{ $order->payment_reference ?: 'Pending' }}</strong></div>
                    </div>

                    <p class="text-muted small mb-0">This order remains available for payment review, receipt access, delivery tracking, and post-delivery follow-up.</p>
                </article>
            </div>
        @empty
            <div class="col-12">
                <div class="customer-empty">No orders found.</div>
            </div>
        @endforelse
    </div>

    <div class="customer-pagination">{{ $orders->links() }}</div>
</div>
@endsection
