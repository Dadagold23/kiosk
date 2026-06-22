@extends('layouts.admin')

@section('meta_title', 'Admin Orders | Kiosk')
@section('page_title', 'Orders')
@section('page_subtitle', 'Review, filter, and open customer orders from one cleaner operations board.')

@section('content')
@php
    $totalOrders = $orders->total();
    $paidCount = $orders->getCollection()->where('payment_status', 'paid')->count();
    $activeCount = $orders->getCollection()->whereIn('order_status', ['pending', 'reviewing', 'processing', 'sourced', 'dispatched'])->count();
@endphp

<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Operations</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">Orders Queue</li>
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
                        <p class="mb-0 text-secondary">Visible Orders</p>
                        <h4 class="my-1 fw-bold text-dark">{{ $totalOrders }}</h4>
                        <p class="mb-0 font-13 text-muted">Current filtered set</p>
                    </div>
                    <div class="widgets-icons bg-light-primary text-primary ms-auto rounded-3">
                        <i class="bx bx-shopping-bag fs-4"></i>
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
                        <p class="mb-0 text-secondary">Active Fulfillment</p>
                        <h4 class="my-1 fw-bold text-dark">{{ $activeCount }}</h4>
                        <p class="mb-0 font-13 text-muted">Moving through workflow</p>
                    </div>
                    <div class="widgets-icons bg-light-warning text-warning ms-auto rounded-3">
                        <i class="bx bx-refresh fs-4"></i>
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
                        <p class="mb-0 text-secondary">Paid Orders</p>
                        <h4 class="my-1 fw-bold text-dark">{{ $paidCount }}</h4>
                        <p class="mb-0 font-13 text-muted">Awaiting fulfillment or closed</p>
                    </div>
                    <div class="widgets-icons bg-light-success text-success ms-auto rounded-3">
                        <i class="bx bx-wallet fs-4"></i>
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
                <h5 class="mb-1 fw-bold text-dark">Order Queue</h5>
                <p class="text-muted small mb-0">Filter by customer, payment state, or order stage before opening a record.</p>
            </div>
        </div>

        <form method="GET" class="row g-3 align-items-center">
            <div class="col-md-5">
                <div class="position-relative">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control ps-5 radius-30" placeholder="Search order no, name, email">
                    <span class="position-absolute top-50 translate-middle-y ms-3"><i class="bx bx-search"></i></span>
                </div>
            </div>
            <div class="col-md-3">
                <select name="payment_status" class="form-select radius-30">
                    <option value="">All Payment Statuses</option>
                    @foreach(['pending','paid','failed','cancelled','under_review'] as $status)
                        <option value="{{ $status }}" @selected(request('payment_status') === $status)>{{ ucfirst(str_replace('_',' ',$status)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="order_status" class="form-select radius-30">
                    <option value="">All Order Statuses</option>
                    @foreach(['pending','reviewing','processing','sourced','dispatched','delivered','cancelled'] as $status)
                        <option value="{{ $status }}" @selected(request('order_status') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1">
                <button class="btn btn-primary radius-30 w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

<!-- Orders Table Card -->
<div class="card">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Order No</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Payment Status</th>
                        <th>Fulfillment</th>
                        <th>Date</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        @php
                            $paymentBadge = match($order->payment_status) {
                                'paid' => 'text-success bg-light-success',
                                'failed', 'cancelled' => 'text-danger bg-light-danger',
                                'under_review', 'pending' => 'text-warning bg-light-warning',
                                default => 'text-secondary bg-light-secondary',
                            };
                            $orderBadge = match($order->order_status) {
                                'delivered' => 'text-success bg-light-success',
                                'cancelled' => 'text-danger bg-light-danger',
                                'pending', 'reviewing', 'processing', 'sourced', 'dispatched' => 'text-warning bg-light-warning',
                                default => 'text-secondary bg-light-secondary',
                            };
                        @endphp
                        <tr>
                            <td>
                                <strong class="text-dark">{{ $order->order_no }}</strong>
                                <div class="small text-muted text-uppercase">{{ str_replace('_', ' ', $order->order_type) }}</div>
                            </td>
                            <td>
                                <strong class="text-dark">{{ $order->user?->name ?: 'Customer pending' }}</strong>
                                <div class="small text-muted">{{ $order->user?->email }}</div>
                            </td>
                            <td class="fw-bold text-dark">₦{{ number_format($order->total, 2) }}</td>
                            <td>
                                <span class="badge rounded-pill {{ $paymentBadge }} p-2 text-uppercase px-3">
                                    <i class='bx bxs-circle align-middle me-1'></i>{{ str_replace('_',' ',$order->payment_status) }}
                                </span>
                              </td>
                            <td>
                                <span class="badge rounded-pill {{ $orderBadge }} p-2 text-uppercase px-3">
                                    <i class='bx bxs-circle align-middle me-1'></i>{{ str_replace('_',' ',$order->order_status) }}
                                </span>
                            </td>
                            <td>{{ $order->created_at->format('d M Y') }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-primary radius-30 px-3">Open</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No orders found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="mt-4">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
