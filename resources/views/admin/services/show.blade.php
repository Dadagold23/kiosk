@extends('layouts.admin')

@section('meta_title', 'Service Request Details | Kiosk')
@section('page_title', 'Service Request Details')
@section('page_subtitle', 'Track service progress from intake and assignment through delivery and closure')

@section('content')
<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Services</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.services.index') }}">Service Requests</a></li>
                <li class="breadcrumb-item active" aria-current="page">Request Detail #{{ $serviceRequest->id }}</li>
            </ol>
        </nav>
    </div>
    <div class="ms-auto">
        <a href="{{ route('admin.services.index') }}" class="btn btn-outline-secondary radius-30"><i class="bx bx-arrow-back me-1"></i>Back to List</a>
    </div>
</div>
<!--end breadcrumb-->

<!-- Hero status card -->
<div class="card radius-10 border-0 shadow-sm mb-4 bg-primary text-white">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <span class="badge bg-white text-primary mb-2">Field Operations</span>
                <h4 class="mb-1 fw-bold text-white">{{ $serviceRequest->title }}</h4>
                <p class="mb-0 text-white-50 small">Track the request from review and assignment through field execution, payment updates, and closure.</p>
            </div>
            <span class="badge bg-white text-primary px-3 py-2 text-uppercase rounded-pill fw-bold">
                {{ str_replace('_', ' ', $serviceRequest->status) }}
            </span>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column (Snapshot, Timeline, Payments) -->
    <div class="col-xl-7">
        <!-- Snapshot Card -->
        <div class="card radius-10 border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h5 class="mb-4 fw-bold text-dark">Request Snapshot</h5>
                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <div class="p-3 radius-10 bg-light border shadow-none h-100">
                            <small class="text-muted d-block text-uppercase mb-1">Customer</small>
                            <h6 class="mb-0 fw-bold text-dark">{{ $serviceRequest->user?->name ?: 'Unknown customer' }}</h6>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 radius-10 bg-light border shadow-none h-100">
                            <small class="text-muted d-block text-uppercase mb-1">Email</small>
                            <h6 class="mb-0 fw-bold text-dark text-break" style="font-size: 0.9rem;">{{ $serviceRequest->user?->email ?: 'N/A' }}</h6>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 radius-10 bg-light border shadow-none h-100">
                            <small class="text-muted d-block text-uppercase mb-1">Phone</small>
                            <h6 class="mb-0 fw-bold text-dark">{{ $serviceRequest->user?->phone ?: 'N/A' }}</h6>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 radius-10 bg-light border shadow-none h-100">
                            <small class="text-muted d-block text-uppercase mb-1">Category</small>
                            <h6 class="mb-0 fw-bold text-dark text-truncate">{{ $serviceRequest->category?->name ?: 'Not categorised' }}</h6>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 radius-10 bg-light border shadow-none h-100">
                            <small class="text-muted d-block text-uppercase mb-1">Location</small>
                            <h6 class="mb-0 fw-bold text-dark text-truncate" title="{{ $serviceRequest->location }}">{{ $serviceRequest->location ?: 'N/A' }}</h6>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 radius-10 bg-light border shadow-none h-100">
                            <small class="text-muted d-block text-uppercase mb-1">Preferred Date</small>
                            <h6 class="mb-0 fw-bold text-dark">{{ $serviceRequest->preferred_date?->format('d M Y') ?: 'N/A' }}</h6>
                        </div>
                    </div>
                </div>

                <div class="p-3 radius-10 bg-light border shadow-none mb-0">
                    <span class="d-block text-uppercase small text-muted fw-bold mb-2">Description</span>
                    <p class="mb-0 text-dark small" style="white-space: pre-line;">{{ $serviceRequest->description }}</p>
                </div>
            </div>
        </div>

        <!-- Timeline Card -->
        <div class="card radius-10 border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h5 class="mb-1 fw-bold text-dark">Tracking Timeline</h5>
                        <p class="text-muted small mb-0">Expose real progress from review and assignment to on-site work and completion.</p>
                    </div>
                    <span class="badge rounded-pill text-info bg-light-info text-uppercase px-3 py-2">
                        {{ str_replace('_', ' ', $serviceRequest->progress_status ?: 'request_received') }}
                    </span>
                </div>

                <div class="list-group list-group-flush">
                    @forelse($serviceRequest->trackingEvents as $event)
                        <div class="list-group-item px-0 py-3 bg-transparent">
                            <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap mb-2">
                                <div>
                                    <span class="badge bg-light-primary text-primary text-uppercase">{{ str_replace('_', ' ', $event->status) }}</span>
                                    @if($event->location)
                                        <span class="ms-2 fw-semibold text-dark small">@ {{ $event->location }}</span>
                                    @endif
                                </div>
                                <small class="text-muted">{{ $event->event_time?->format('d M Y, h:i A') ?: 'Pending time' }}</small>
                            </div>
                            @if($event->next_step)
                                <div class="small text-secondary mb-1"><strong>Next Step:</strong> {{ $event->next_step }}</div>
                            @endif
                            @if($event->note)
                                <p class="mb-0 text-muted small mt-1">{{ $event->note }}</p>
                            @endif
                        </div>
                    @empty
                        <div class="text-muted text-center py-4">No service tracking updates have been added yet.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Payments Card -->
        <div class="card radius-10 border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h5 class="mb-4 fw-bold text-dark">Payment Records</h5>

                <div class="list-group list-group-flush">
                    @forelse($serviceRequest->payments as $payment)
                        <div class="list-group-item px-0 py-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <strong class="text-dark">{{ $payment->reference }}</strong>
                                <span class="badge rounded-pill text-success bg-light-success p-2 text-uppercase px-3"><i class="bx bxs-circle align-middle me-1"></i>{{ str_replace('_', ' ', $payment->status) }}</span>
                            </div>
                            <div class="small text-muted mb-2">₦{{ number_format($payment->amount, 2) }} · Method: {{ ucfirst(str_replace('_', ' ', $payment->payment_method ?? 'n/a')) }}</div>
                            <a href="{{ route('admin.payments.show', $payment) }}" class="btn btn-sm btn-outline-primary radius-30 px-3 mt-2">Open Payment</a>
                        </div>
                    @empty
                        <div class="text-muted text-center py-4">No payment records found.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column (Assistant, Assignments, Tracking Updates, Reviews) -->
    <div class="col-xl-5">
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

        <!-- Assignments Card -->
        <div class="card radius-10 border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h5 class="mb-1 fw-bold text-dark">Assign and Update</h5>
                <p class="text-muted small mb-4">Keep staffing, team ownership, request state, and payment status in sync.</p>

                <form action="{{ route('admin.services.assign', $serviceRequest) }}" method="POST" class="row g-3">
                    @csrf
                    <div class="col-md-6">
                        <label class="form-label small text-secondary fw-bold text-uppercase">Assign Staff</label>
                        <select name="assigned_to" class="form-select radius-30">
                            <option value="">Select staff</option>
                            @foreach($staff as $member)
                                <option value="{{ $member->id }}" @selected($serviceRequest->assigned_to == $member->id)>{{ $member->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small text-secondary fw-bold text-uppercase">Assigned Team</label>
                        <input type="text" name="assigned_team" value="{{ old('assigned_team', $serviceRequest->assigned_team) }}" class="form-control radius-30" placeholder="Field Ops Team A">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small text-secondary fw-bold text-uppercase">Status</label>
                        <select name="status" class="form-select radius-30">
                            @foreach(['pending','reviewing','approved','in_progress','completed','closed','cancelled'] as $status)
                                <option value="{{ $status }}" @selected($serviceRequest->status === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small text-secondary fw-bold text-uppercase">Payment Status</label>
                        <select name="payment_status" class="form-select radius-30">
                            @foreach(['pending','paid','failed','review'] as $paymentStatus)
                                <option value="{{ $paymentStatus }}" @selected($serviceRequest->payment_status === $paymentStatus)>{{ ucfirst($paymentStatus) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 mt-3 text-end">
                        <button class="btn btn-primary radius-30 w-100">Update Request</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tracking Update Form Card -->
        <div class="card radius-10 border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h5 class="mb-1 fw-bold text-dark">Add Tracking Update</h5>
                <p class="text-muted small mb-4">Record fresh service movement, time windows, and next-step guidance for the team.</p>

                <form action="{{ route('admin.services.track', $serviceRequest) }}" method="POST" class="row g-3">
                    @csrf
                    <div class="col-md-6">
                        <label class="form-label small text-secondary fw-bold text-uppercase">Progress Status</label>
                        <select name="progress_status" class="form-select radius-30">
                            @foreach(config('kiosk.services.tracking_statuses', []) as $status)
                                <option value="{{ $status }}" @selected(($serviceRequest->progress_status ?: 'request_received') === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small text-secondary fw-bold text-uppercase">Assigned Staff</label>
                        <select name="assigned_to" class="form-select radius-30">
                            <option value="">Keep current assignee</option>
                            @foreach($staff as $member)
                                <option value="{{ $member->id }}" @selected($serviceRequest->assigned_to == $member->id)>{{ $member->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small text-secondary fw-bold text-uppercase">Assigned Team</label>
                        <input type="text" name="assigned_team" value="{{ old('assigned_team', $serviceRequest->assigned_team) }}" class="form-control radius-30">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small text-secondary fw-bold text-uppercase">Current Location</label>
                        <input type="text" name="location" class="form-control radius-30" placeholder="Workshop, customer site, transit hub">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small text-secondary fw-bold text-uppercase">Window Start</label>
                        <input type="datetime-local" name="service_window_start" class="form-control radius-30" value="{{ old('service_window_start', optional($serviceRequest->service_window_start)->format('Y-m-d\\TH:i')) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small text-secondary fw-bold text-uppercase">Window End</label>
                        <input type="datetime-local" name="service_window_end" class="form-control radius-30" value="{{ old('service_window_end', optional($serviceRequest->service_window_end)->format('Y-m-d\\TH:i')) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small text-secondary fw-bold text-uppercase">Event Time</label>
                        <input type="datetime-local" name="event_time" class="form-control radius-30">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small text-secondary fw-bold text-uppercase">Next Step</label>
                        <input type="text" name="next_step" class="form-control radius-30" placeholder="Await customer confirmation">
                    </div>

                    <div class="col-12">
                        <label class="form-label small text-secondary fw-bold text-uppercase">Tracking Note</label>
                        <textarea name="tracking_note" rows="3" class="form-control radius-15" placeholder="Describe the latest service progress"></textarea>
                    </div>

                    <div class="col-12 mt-3">
                        <button class="btn btn-outline-primary radius-30 w-100">Add Tracking Update</button>
                    </div>
                </form>
            </div>
        </div>

        @include('partials.reviews.admin-panel', [
            'reviews' => $serviceRequest->reviews,
            'title' => 'Service Reviews',
            'subtitle' => 'Moderate customer experience feedback before it is shown on the services page.',
            'wrapperClass' => 'card radius-10 border-0 shadow-sm mt-4 p-4',
        ])
    </div>
</div>
@endsection
