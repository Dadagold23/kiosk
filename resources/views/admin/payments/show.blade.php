@extends('layouts.admin')

@section('meta_title', 'Admin Payment Details | Kiosk')
@section('page_title', 'Payment Details')
@section('page_subtitle', 'Inspect payment verification, gateway feedback, and finance status from a cleaner detail page.')

@section('content')
<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Finance</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.payments.index') }}">Payments Desk</a></li>
                <li class="breadcrumb-item active" aria-current="page">Payment Detail #{{ $payment->reference }}</li>
            </ol>
        </nav>
    </div>
    <div class="ms-auto">
        <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary radius-30"><i class="bx bx-arrow-back me-1"></i>Back to Payments</a>
    </div>
</div>
<!--end breadcrumb-->

<div class="row g-4">
    <!-- Payment Info Column -->
    <div class="col-lg-7">
        <div class="card radius-10 border-0 shadow-sm h-100 mb-0">
            <div class="card-body p-4">
                <h5 class="mb-4 fw-bold text-dark">Payment Record: {{ $payment->reference }}</h5>
                <div class="row g-3 mb-4">
                    <div class="col-sm-6 col-md-4">
                        <div class="p-3 radius-10 bg-light border shadow-none h-100">
                            <small class="text-muted d-block text-uppercase mb-1">Receipt No</small>
                            <h6 class="mb-0 fw-bold text-dark">{{ $payment->receipt_no ?: 'Pending' }}</h6>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="p-3 radius-10 bg-light border shadow-none h-100">
                            <small class="text-muted d-block text-uppercase mb-1">User</small>
                            <h6 class="mb-0 fw-bold text-dark text-truncate">{{ $payment->user?->name ?: 'Unknown user' }}</h6>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="p-3 radius-10 bg-light border shadow-none h-100">
                            <small class="text-muted d-block text-uppercase mb-1">Amount</small>
                            <h6 class="mb-0 fw-bold text-dark text-nowrap">₦{{ number_format($payment->amount, 2) }}</h6>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="p-3 radius-10 bg-light border shadow-none h-100">
                            <small class="text-muted d-block text-uppercase mb-1">Status</small>
                            <h6 class="mb-0 fw-bold text-dark text-uppercase small text-truncate">{{ str_replace('_', ' ', $payment->status) }}</h6>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="p-3 radius-10 bg-light border shadow-none h-100">
                            <small class="text-muted d-block text-uppercase mb-1">Method</small>
                            <h6 class="mb-0 fw-bold text-dark text-uppercase small text-truncate">{{ str_replace('_', ' ', $payment->payment_method ?? 'n/a') }}</h6>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="p-3 radius-10 bg-light border shadow-none h-100">
                            <small class="text-muted d-block text-uppercase mb-1">Gateway</small>
                            <h6 class="mb-0 fw-bold text-dark text-uppercase small text-truncate">{{ $payment->gateway ?? 'manual' }}</h6>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="p-3 radius-10 bg-light border shadow-none h-100">
                            <small class="text-muted d-block text-uppercase mb-1">Gateway Txn ID</small>
                            <h6 class="mb-0 fw-bold text-dark text-break small">{{ $payment->gateway_transaction_id ?: 'Pending' }}</h6>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="p-3 radius-10 bg-light border shadow-none h-100">
                            <small class="text-muted d-block text-uppercase mb-1">Verified At</small>
                            <h6 class="mb-0 fw-bold text-dark small">{{ $payment->gateway_verified_at?->format('d M Y, h:i A') ?: 'Pending' }}</h6>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="p-3 radius-10 bg-light border shadow-none h-100">
                            <small class="text-muted d-block text-uppercase mb-1">Paid At</small>
                            <h6 class="mb-0 fw-bold text-dark small">{{ $payment->paid_at?->format('d M Y, h:i A') ?: 'N/A' }}</h6>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="p-3 radius-10 bg-light border shadow-none h-100">
                            <small class="text-muted d-block text-uppercase mb-1">Related Record</small>
                            <h6 class="mb-0 fw-bold text-dark">{{ class_basename($payment->payable_type) }} #{{ $payment->payable_id }}</h6>
                        </div>
                    </div>
                </div>

                <div class="p-3 radius-10 bg-light border shadow-none">
                    <span class="d-block text-uppercase small text-muted fw-bold mb-2">Gateway Response</span>
                    <p class="mb-0 text-dark small text-break">{{ $payment->gateway_response ?: 'Awaiting response' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Controls Column -->
    <div class="col-lg-5">
        <div class="card radius-10 border-0 shadow-sm">
            <div class="card-body p-4">
                <h5 class="mb-4 fw-bold text-dark">Payment Controls</h5>

                <form action="{{ route('admin.payments.update', $payment) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small text-secondary fw-bold text-uppercase">Payment Status</label>
                        <select name="status" class="form-select radius-30">
                            @foreach(['pending','paid','failed','cancelled','under_review'] as $status)
                                <option value="{{ $status }}" @selected($payment->status === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button class="btn btn-primary radius-30 w-100 mt-2">Update Payment</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
