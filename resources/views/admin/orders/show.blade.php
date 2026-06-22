@extends('layouts.admin')

@section('meta_title', 'Order Details | Kiosk')
@section('page_title', 'Order Details')
@section('page_subtitle', 'Review items, payment verification, and fulfillment updates inside the refreshed operations view.')

@section('content')
<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Operations</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Orders Queue</a></li>
                <li class="breadcrumb-item active" aria-current="page">Order Detail #{{ $order->order_no }}</li>
            </ol>
        </nav>
    </div>
    <div class="ms-auto">
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary radius-30"><i class="bx bx-arrow-back me-1"></i>Back to Orders</a>
    </div>
</div>
<!--end breadcrumb-->

<!-- Order Snapshot Card -->
<div class="card radius-10 border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <h5 class="mb-4 fw-bold text-dark">Order Record: {{ $order->order_no }}</h5>
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-md-4">
                <div class="p-3 radius-10 bg-light border shadow-none h-100">
                    <small class="text-muted d-block text-uppercase mb-1">Customer</small>
                    <h6 class="mb-0 fw-bold text-dark">{{ $order->user?->name ?: 'Customer pending' }}</h6>
                </div>
            </div>
            <div class="col-sm-6 col-md-4">
                <div class="p-3 radius-10 bg-light border shadow-none h-100">
                    <small class="text-muted d-block text-uppercase mb-1">Email</small>
                    <h6 class="mb-0 fw-bold text-dark text-break" style="font-size: 0.9rem;">{{ $order->user?->email ?: 'No email' }}</h6>
                </div>
            </div>
            <div class="col-sm-6 col-md-4">
                <div class="p-3 radius-10 bg-light border shadow-none h-100">
                    <small class="text-muted d-block text-uppercase mb-1">Phone</small>
                    <h6 class="mb-0 fw-bold text-dark">{{ $order->user?->phone ?: 'N/A' }}</h6>
                </div>
            </div>
            <div class="col-sm-6 col-md-4">
                <div class="p-3 radius-10 bg-light border shadow-none h-100">
                    <small class="text-muted d-block text-uppercase mb-1">Order Type</small>
                    <h6 class="mb-0 fw-bold text-dark text-uppercase small">{{ str_replace('_', ' ', $order->order_type) }}</h6>
                </div>
            </div>
            <div class="col-sm-6 col-md-4">
                <div class="p-3 radius-10 bg-light border shadow-none h-100">
                    <small class="text-muted d-block text-uppercase mb-1">Payment Reference</small>
                    <h6 class="mb-0 fw-bold text-dark text-break" style="font-size: 0.9rem;">{{ $order->payment_reference ?: 'N/A' }}</h6>
                </div>
            </div>
            <div class="col-sm-6 col-md-4">
                <div class="p-3 radius-10 bg-light border shadow-none h-100">
                    <small class="text-muted d-block text-uppercase mb-1">Created</small>
                    <h6 class="mb-0 fw-bold text-dark">{{ $order->created_at->format('d M Y, h:i A') }}</h6>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <div class="p-3 radius-10 bg-light border shadow-none h-100">
                    <span class="d-block text-uppercase small text-muted fw-bold mb-2">Delivery Address</span>
                    <p class="mb-0 text-dark" style="white-space: pre-line;">{{ $order->delivery_address ?: 'N/A' }}</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-3 radius-10 bg-light border shadow-none h-100">
                    <span class="d-block text-uppercase small text-muted fw-bold mb-2">Notes</span>
                    <p class="mb-0 text-dark" style="white-space: pre-line;">{{ $order->notes ?: 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Items & Tracking Column -->
    <div class="col-lg-7">
        <div class="card radius-10 border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h5 class="mb-4 fw-bold text-dark">Fulfillment Items</h5>

                <div class="list-group list-group-flush">
                    @foreach($order->items as $item)
                        <div class="list-group-item px-0 py-4">
                            <div class="d-flex justify-content-between flex-wrap gap-3 mb-3">
                                <div>
                                    <h6 class="fw-bold text-dark mb-1">{{ $item->product_name }}</h6>
                                    <small class="text-muted">Qty: {{ $item->qty }} · Unit: ₦{{ number_format($item->unit_price, 2) }} · Subtotal: ₦{{ number_format($item->subtotal, 2) }}</small>
                                    <div class="mt-2">
                                        <span class="badge rounded-pill text-warning bg-light-warning p-2 text-uppercase px-3"><i class='bx bxs-circle align-middle me-1'></i>{{ str_replace('_', ' ', $item->fulfillment_status) }}</span>
                                    </div>
                                </div>
                                <div class="small text-muted text-end">
                                    <div>Tracking No: <span class="fw-bold text-dark">{{ $item->tracking_number ?: 'Pending' }}</span></div>
                                    <div>Partner: <span class="fw-bold text-dark">{{ $item->logistics_partner ?: 'Unassigned' }}</span></div>
                                    <div>Updated: <span class="fw-bold text-dark">{{ $item->last_tracked_at?->format('d M Y, h:i A') ?: 'Never' }}</span></div>
                                </div>
                            </div>

                            <form action="{{ route('admin.orders.items.update', [$order, $item]) }}" method="POST" class="bg-light p-3 radius-10 border mt-3">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small text-secondary fw-bold text-uppercase">Item Status</label>
                                        <select name="fulfillment_status" class="form-select radius-30">
                                            @foreach(config('kiosk.orders.tracking_statuses', []) as $status)
                                                <option value="{{ $status }}" @selected($item->fulfillment_status === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small text-secondary fw-bold text-uppercase">Logistics Partner</label>
                                        <input type="text" name="logistics_partner" value="{{ old('logistics_partner', $item->logistics_partner) }}" class="form-control radius-30" placeholder="DHL, GIG, local rider...">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small text-secondary fw-bold text-uppercase">Tracking Number</label>
                                        <input type="text" name="tracking_number" value="{{ old('tracking_number', $item->tracking_number) }}" class="form-control radius-30" placeholder="Tracking number">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small text-secondary fw-bold text-uppercase">Tracking URL</label>
                                        <input type="url" name="tracking_url" value="{{ old('tracking_url', $item->tracking_url) }}" class="form-control radius-30" placeholder="https://tracking.example.com/...">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small text-secondary fw-bold text-uppercase">Location</label>
                                        <input type="text" name="location" class="form-control radius-30" placeholder="Warehouse, transit hub, destination city">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small text-secondary fw-bold text-uppercase">Event Time</label>
                                        <input type="datetime-local" name="event_time" class="form-control radius-30">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small text-secondary fw-bold text-uppercase">Tracking Note</label>
                                        <textarea name="event_note" rows="2" class="form-control radius-15" placeholder="Describe the latest shipment or delivery update"></textarea>
                                    </div>
                                    <div class="col-12 text-end mt-2">
                                        <button class="btn btn-primary radius-30 px-4">Update Item Tracking</button>
                                    </div>
                                </div>
                            </form>

                            @if($item->trackingEvents->isNotEmpty())
                                <div class="mt-3 bg-light p-3 radius-10 border">
                                    <h6 class="fw-bold mb-3 small text-uppercase text-secondary">Recent Tracking Events</h6>
                                    <div class="list-group list-group-flush">
                                        @foreach($item->trackingEvents->take(5) as $event)
                                            <div class="list-group-item px-0 py-2 bg-transparent">
                                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                                    <span class="badge rounded-pill text-info bg-light-info text-uppercase"><i class="bx bxs-circle align-middle me-1"></i>{{ str_replace('_', ' ', $event->status) }}</span>
                                                    <small class="text-muted">{{ $event->event_time?->format('d M Y, h:i A') ?: 'Pending time' }}</small>
                                                </div>
                                                @if($event->location)
                                                    <div class="small text-dark mt-1"><strong>Location:</strong> {{ $event->location }}</div>
                                                @endif
                                                @if($event->note)
                                                    <div class="small text-muted mt-1">{{ $event->note }}</div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Payment Records Card -->
        <div class="card radius-10 border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h5 class="mb-4 fw-bold text-dark">Payment Records</h5>

                <div class="list-group list-group-flush">
                    @forelse($order->payments as $payment)
                        <div class="list-group-item px-0 py-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <strong class="text-dark">{{ $payment->reference }}</strong>
                                <span class="badge rounded-pill text-success bg-light-success p-2 text-uppercase px-3"><i class="bx bxs-circle align-middle me-1"></i>{{ str_replace('_', ' ', $payment->status) }}</span>
                            </div>
                            <div class="small text-muted mb-2">₦{{ number_format($payment->amount, 2) }} · Method: {{ ucfirst(str_replace('_', ' ', $payment->payment_method ?? 'n/a')) }}</div>
                            <div class="small text-muted">Gateway Txn: {{ $payment->gateway_transaction_id ?: 'Pending' }} · Verified: {{ $payment->gateway_verified_at?->format('d M Y, h:i A') ?: 'Pending' }}</div>
                            <a href="{{ route('admin.payments.show', $payment) }}" class="btn btn-sm btn-outline-primary radius-30 px-3 mt-3">Open Payment</a>
                        </div>
                    @empty
                        <div class="text-muted text-center py-4">No payment records found.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar Column -->
    <div class="col-lg-5">
        <!-- Assistant Card -->
        @php
            $riskBadge = match($assistantInsight['risk_level']) {
                'high' => 'text-danger bg-light-danger',
                'medium' => 'text-warning bg-light-warning',
                default => 'text-success bg-light-success',
            };
        @endphp
        <div class="card radius-10 border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h5 class="mb-0 fw-bold text-dark">Admin Assistant</h5>
                    <span class="badge rounded-pill {{ $riskBadge }} p-2 text-uppercase px-3"><i class="bx bxs-circle align-middle me-1"></i>{{ $assistantInsight['risk_level'] }} Risk</span>
                </div>
                <p class="text-muted mb-4">{{ $assistantInsight['summary'] }}</p>

                <div class="p-3 radius-10 bg-light border shadow-none mb-3">
                    <small class="text-muted d-block text-uppercase mb-1">Estimated Completion</small>
                    <h6 class="mb-0 fw-bold text-dark">{{ $assistantInsight['eta_label'] }}</h6>
                </div>

                <div class="p-3 radius-10 bg-light border shadow-none">
                    <small class="text-muted d-block text-uppercase mb-1">Recommended Action</small>
                    <p class="mb-0 fw-bold text-dark" style="font-size: 0.95rem;">{{ $assistantInsight['next_action'] }}</p>
                </div>
            </div>
        </div>

        <!-- Order Controls Card -->
        <div class="card radius-10 border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h5 class="mb-4 fw-bold text-dark">Order Controls</h5>

                <form action="{{ route('admin.orders.update', $order) }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small text-secondary fw-bold text-uppercase">Order Status</label>
                            <select name="order_status" class="form-select radius-30">
                                @foreach(['pending','reviewing','processing','sourced','dispatched','delivered','cancelled'] as $status)
                                    <option value="{{ $status }}" @selected($order->order_status === $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-secondary fw-bold text-uppercase">Payment Status</label>
                            <select name="payment_status" class="form-select radius-30">
                                @foreach(['pending','paid','failed','cancelled','under_review'] as $status)
                                    <option value="{{ $status }}" @selected($order->payment_status === $status)>{{ ucfirst(str_replace('_',' ',$status)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small text-secondary fw-bold text-uppercase">Delivery Address</label>
                            <textarea name="delivery_address" rows="3" class="form-control radius-15">{{ old('delivery_address', $order->delivery_address) }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label small text-secondary fw-bold text-uppercase">Notes</label>
                            <textarea name="notes" rows="3" class="form-control radius-15">{{ old('notes', $order->notes) }}</textarea>
                        </div>
                        <div class="col-12 mt-3">
                            <button class="btn btn-primary radius-30 w-100">Update Order</button>
                        </div>
                    </div>
                </form>

                <div class="p-3 radius-10 bg-light border shadow-none mt-4">
                    <div class="d-flex justify-content-between small text-secondary mb-2"><span>Subtotal</span><strong>₦{{ number_format($order->subtotal, 2) }}</strong></div>
                    <div class="d-flex justify-content-between small text-secondary mb-2"><span>Delivery</span><strong>₦{{ number_format($order->delivery_fee, 2) }}</strong></div>
                    <div class="d-flex justify-content-between small text-secondary mb-2"><span>Service Charge</span><strong>₦{{ number_format($order->service_charge, 2) }}</strong></div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between text-dark fw-bold"><span>Total</span><strong class="text-success fs-5">₦{{ number_format($order->total, 2) }}</strong></div>
                </div>
            </div>
        </div>

        @include('partials.reviews.admin-panel', [
            'reviews' => $order->reviews,
            'title' => 'Order Delivery Reviews',
            'subtitle' => 'Approve or feature customer feedback from completed order deliveries.',
            'wrapperClass' => 'card radius-10 border-0 shadow-sm mt-4 p-4',
        ])
    </div>
</div>
@endsection
