@extends('layouts.customer')

@section('customer_page_title', 'Payment Receipt')
@section('customer_page_subtitle', 'Track the current state of your payment and keep the receipt reference close.')

@section('customer_body')
<div class="customer-page-grid">
    <div class="feature-card customer-page-block mx-auto" style="max-width: 960px;">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
            <div>
                <div class="customer-eyebrow">Receipt</div>
                <h3 class="customer-section-title">Payment receipt</h3>
                <p class="customer-section-copy">Official Kiosk payment acknowledgement with gateway status and related record details.</p>
            </div>
            <button onclick="window.print()" class="customer-soft-button border-0">Print Receipt</button>
        </div>

        <div class="customer-info-grid mb-3">
            <div class="customer-info-card">
                <span class="label">Receipt No</span>
                <span class="value">{{ $payment->receipt_no }}</span>
            </div>
            <div class="customer-info-card">
                <span class="label">Reference</span>
                <span class="value">{{ $payment->reference }}</span>
            </div>
            <div class="customer-info-card">
                <span class="label">Status</span>
                <span class="value">{{ ucfirst(str_replace('_', ' ', $payment->status)) }}</span>
            </div>
            <div class="customer-info-card">
                <span class="label">Paid At</span>
                <span class="value">{{ $payment->paid_at?->format('d M Y, h:i A') ?: 'Pending' }}</span>
            </div>
            <div class="customer-info-card">
                <span class="label">Customer</span>
                <span class="value">{{ $payment->user?->name }}</span>
            </div>
            <div class="customer-info-card">
                <span class="label">Email</span>
                <span class="value">{{ $payment->user?->email }}</span>
            </div>
            <div class="customer-info-card">
                <span class="label">Method</span>
                <span class="value">{{ ucfirst(str_replace('_', ' ', $payment->payment_method ?? 'n/a')) }}</span>
            </div>
            <div class="customer-info-card">
                <span class="label">Gateway</span>
                <span class="value">{{ ucfirst($payment->gateway ?? 'paystack') }}</span>
            </div>
        </div>

        <div class="customer-panel-note mb-3">
            <div class="d-flex justify-content-between align-items-center gap-3">
                <span class="fw-semibold">Amount</span>
                <strong>&#8358;{{ number_format($payment->amount, 2) }}</strong>
            </div>
        </div>

        @if($payment->gateway === 'paystack')
            <div class="customer-panel-note mb-3">
                <div class="fw-semibold mb-2">Gateway Verification</div>
                <div class="small mb-1"><strong>Gateway Transaction ID:</strong> {{ $payment->gateway_transaction_id ?: 'Pending' }}</div>
                <div class="small mb-1"><strong>Gateway Response:</strong> {{ $payment->gateway_response ?: 'Awaiting response' }}</div>
                <div class="small"><strong>Verified At:</strong> {{ $payment->gateway_verified_at?->format('d M Y, h:i A') ?: 'Pending' }}</div>
            </div>
        @endif

        <div class="customer-panel-note mb-4">
            <div class="fw-semibold mb-2">Related Record</div>
            <div class="small mb-1"><strong>Type:</strong> {{ class_basename($payment->payable_type) }}</div>
            <div class="small"><strong>Record ID:</strong> {{ $payment->payable_id }}</div>
        </div>

        <a href="{{ route('receipts.download', $payment) }}" class="customer-btn-primary btn">Download PDF</a>
    </div>
</div>
@endsection
