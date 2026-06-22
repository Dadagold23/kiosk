@extends('layouts.admin')

@section('meta_title', 'Admin Payments | Kiosk')
@section('page_title', 'Payments')
@section('page_subtitle', 'Track references, users, and payment statuses from one refined finance board.')

@section('content')
@php
    $totalPayments = $payments->total();
    $paidCount = $payments->getCollection()->where('status', 'paid')->count();
    $reviewCount = $payments->getCollection()->whereIn('status', ['pending', 'under_review'])->count();
@endphp

<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Finance</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">Payments Desk</li>
            </ol>
        </nav>
    </div>
</div>
<!--end breadcrumb-->

<!-- Stats Summary Row -->
<div class="row row-cols-1 row-cols-md-3 g-3 mb-4">
    <div class="col">
        <div class="card radius-10 border-0 shadow-sm mb-0">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-secondary">Visible Payments</p>
                        <h4 class="my-1 fw-bold text-dark">{{ $totalPayments }}</h4>
                        <p class="mb-0 font-13 text-muted">Currently filtered list</p>
                    </div>
                    <div class="widgets-icons bg-light-primary text-primary ms-auto rounded-3">
                        <i class="bx bx-receipt fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card radius-10 border-0 shadow-sm mb-0">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-secondary">Paid</p>
                        <h4 class="my-1 fw-bold text-success">{{ $paidCount }}</h4>
                        <p class="mb-0 font-13 text-muted">Settled successfully</p>
                    </div>
                    <div class="widgets-icons bg-light-success text-success ms-auto rounded-3">
                        <i class="bx bx-check-circle fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card radius-10 border-0 shadow-sm mb-0">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-secondary">Needs Review</p>
                        <h4 class="my-1 fw-bold text-warning">{{ $reviewCount }}</h4>
                        <p class="mb-0 font-13 text-muted">Pending verification</p>
                    </div>
                    <div class="widgets-icons bg-light-warning text-warning ms-auto rounded-3">
                        <i class="bx bx-time fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters Card -->
<div class="card mb-4">
    <div class="card-body p-4">
        <div class="d-flex align-items-center mb-3">
            <div>
                <h5 class="mb-1 fw-bold text-dark">Payment Records</h5>
                <p class="text-muted small mb-0">Search by reference, receipt, or user and refine the list by payment state.</p>
            </div>
        </div>

        <form method="GET" class="row g-3 align-items-center">
            <div class="col-md-7">
                <div class="position-relative">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control ps-5 radius-30" placeholder="Search reference, receipt, user">
                    <span class="position-absolute top-50 translate-middle-y ms-3"><i class="bx bx-search"></i></span>
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select radius-30">
                    <option value="">All Statuses</option>
                    @foreach(['pending','paid','failed','cancelled','under_review'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst(str_replace('_',' ',$status)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary radius-30 w-100">Filter Payments</button>
            </div>
        </form>
    </div>
</div>

<!-- Payments Table Card -->
<div class="card">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Reference</th>
                        <th>User</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Record Type</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        @php
                            $badgeClass = match($payment->status) {
                                'paid' => 'text-success bg-light-success',
                                'failed', 'cancelled' => 'text-danger bg-light-danger',
                                'pending', 'under_review' => 'text-warning bg-light-warning',
                                default => 'text-secondary bg-light-secondary',
                            };
                        @endphp
                        <tr>
                            <td>
                                <strong class="text-dark">{{ $payment->reference }}</strong>
                                <div class="small text-muted">{{ $payment->receipt_no ?: 'Receipt pending' }}</div>
                            </td>
                            <td>
                                <strong class="text-dark">{{ $payment->user?->name ?: 'Unknown user' }}</strong>
                                <div class="small text-muted">{{ $payment->user?->email }}</div>
                            </td>
                            <td class="fw-bold text-dark">₦{{ number_format($payment->amount, 2) }}</td>
                            <td>
                                <span class="badge rounded-pill {{ $badgeClass }} p-2 text-uppercase px-3">
                                    <i class='bx bxs-circle align-middle me-1'></i>{{ str_replace('_', ' ', $payment->status) }}
                                </span>
                            </td>
                            <td><span class="text-uppercase small fw-bold text-secondary">{{ class_basename($payment->payable_type) }}</span></td>
                            <td class="text-end">
                                <a href="{{ route('admin.payments.show', $payment) }}" class="btn btn-sm btn-primary radius-30 px-3">Open</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No payments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
            <div class="mt-4">
                {{ $payments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
