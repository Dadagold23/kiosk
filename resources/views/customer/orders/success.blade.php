@extends('layouts.customer')

@section('customer_page_title', 'Order Created')
@section('customer_page_subtitle', 'Your order is recorded and ready for payment completion or tracking in the updated dashboard flow.')

@section('customer_body')
@php($payment = $order->payments->first())

<div class="customer-page-grid">
    <div class="feature-card customer-page-block mx-auto" style="max-width: 920px;">
        <div class="row g-4 align-items-center mb-4">
            <div class="col-lg-7">
                <span class="customer-status-pill is-success mb-3">Order Submitted</span>
                <h1 class="fw-bold mb-3">Your order is now on record</h1>
                <p class="customer-section-copy mb-0">We created your order and linked it to your payment record. If the Paystack flow was interrupted, you can reopen payment from this screen without losing the order.</p>
            </div>
            <div class="col-lg-5">
                <div class="customer-panel-note h-100">
                    <div class="small text-uppercase text-muted mb-2">Order Snapshot</div>
                    <div class="fw-semibold mb-3">{{ $order->order_no }}</div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Order Type</span>
                        <span>{{ ucfirst(str_replace('_', ' ', $order->order_type)) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Payment Status</span>
                        <span>{{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Total</span>
                        <strong class="text-primary">&#8358;{{ number_format($order->total, 2) }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="customer-info-grid mb-4">
            <div class="customer-info-card">
                <span class="label">Payment Reference</span>
                <span class="value">{{ $payment?->reference ?: 'Reference pending' }}</span>
            </div>
            <div class="customer-info-card">
                <span class="label">Gateway</span>
                <span class="value">{{ ucfirst($payment?->gateway ?? 'paystack') }}</span>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-3">
            <a href="{{ route('orders.show', $order->order_no) }}" class="customer-btn-primary btn">Open Order</a>
            @if($order->payment_status !== \App\Models\Payment::STATUS_PAID)
                <form action="{{ route('orders.pay', $order) }}" method="POST">
                    @csrf
                    <button class="btn btn-outline-primary">Retry Paystack Payment</button>
                </form>
            @endif
            <a href="{{ route('shop.index') }}" class="customer-soft-button">Continue Shopping</a>
        </div>
    </div>
</div>
@endsection
