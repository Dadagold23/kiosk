@extends('layouts.admin')

@section('meta_title', 'Consultancy Details | Kiosk')
@section('page_title', 'Consultancy Details')
@section('page_subtitle', 'Review the request brief, consultant assignment, payment state, and final delivery assets')

@section('content')
<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Advisory</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.consultancy.index') }}">Consultancy Requests</a></li>
                <li class="breadcrumb-item active" aria-current="page">Case #{{ $consultancyRequest->id }}</li>
            </ol>
        </nav>
    </div>
    <div class="ms-auto">
        <a href="{{ route('admin.consultancy.index') }}" class="btn btn-outline-secondary radius-30"><i class="bx bx-arrow-back me-1"></i>Back to List</a>
    </div>
</div>
<!--end breadcrumb-->

<div class="row g-4">
    <!-- Snapshot & Details -->
    <div class="col-xl-7">
        <div class="card radius-10 border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                    <div>
                        <h5 class="mb-0 fw-bold">{{ $consultancyRequest->subject }}</h5>
                        <p class="mb-0 text-muted small">Submitted on {{ $consultancyRequest->created_at->format('d M Y, h:i A') }}</p>
                    </div>
                    <div class="ms-auto">
                        @if(in_array($consultancyRequest->status, ['completed', 'closed'], true))
                            <span class="badge rounded-pill text-success bg-light-success p-2 text-uppercase px-3"><i class='bx bxs-circle align-middle me-1'></i>{{ ucfirst($consultancyRequest->status) }}</span>
                        @else
                            <span class="badge rounded-pill text-warning bg-light-warning p-2 text-uppercase px-3"><i class='bx bxs-circle align-middle me-1'></i>{{ ucfirst($consultancyRequest->status) }}</span>
                        @endif
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <div class="p-3 radius-10 bg-light">
                            <small class="text-muted d-block text-uppercase mb-1">Customer</small>
                            <h6 class="mb-0 fw-bold">{{ $consultancyRequest->user?->name ?: 'Unknown customer' }}</h6>
                            <small class="text-muted">{{ $consultancyRequest->user?->email }}</small>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 radius-10 bg-light">
                            <small class="text-muted d-block text-uppercase mb-1">Category</small>
                            <h6 class="mb-0 fw-bold">{{ $consultancyRequest->category?->name ?: 'Not specified' }}</h6>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 radius-10 bg-light">
                            <small class="text-muted d-block text-uppercase mb-1">Preferred Date</small>
                            <h6 class="mb-0 fw-bold">{{ $consultancyRequest->preferred_date?->format('d M Y') ?: 'No preferred date' }}</h6>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 radius-10 bg-light">
                            <small class="text-muted d-block text-uppercase mb-1">Payment Status</small>
                            <h6 class="mb-0 fw-bold">
                                @if($consultancyRequest->payment_status === 'paid')
                                    <span class="text-success"><i class="bx bx-check-circle me-1"></i>Paid</span>
                                @elseif($consultancyRequest->payment_status === 'failed')
                                    <span class="text-danger"><i class="bx bx-x-circle me-1"></i>Failed</span>
                                @else
                                    <span class="text-warning"><i class="bx bx-time-five me-1"></i>{{ ucfirst($consultancyRequest->payment_status ?: 'pending') }}</span>
                                @endif
                            </h6>
                        </div>
                    </div>
                </div>

                <div class="p-3 radius-10 bg-light">
                    <span class="d-block text-uppercase small text-muted fw-bold mb-2">Description Brief</span>
                    <p class="mb-0">{{ $consultancyRequest->description }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions & Report Upload -->
    <div class="col-xl-5">
        <div class="card radius-10 border-0 shadow-sm mb-4">
            <div class="card-body">
                <h5 class="mb-3 fw-bold">Assign and Update</h5>
                <form action="{{ route('admin.consultancy.assign', $consultancyRequest) }}" method="POST" enctype="multipart/form-data" class="row g-3">
                    @csrf

                    <div class="col-12">
                        <label class="form-label fw-bold small text-uppercase text-secondary">Assign Consultant</label>
                        <select name="assigned_consultant_id" class="form-select radius-30">
                            <option value="">Select consultant</option>
                            @foreach($consultants as $consultant)
                                <option value="{{ $consultant->id }}" @selected($consultancyRequest->assigned_consultant_id == $consultant->id)>{{ $consultant->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-uppercase text-secondary">Status</label>
                        <select name="status" class="form-select radius-30">
                            @foreach(['pending','assigned','in_progress','completed','closed'] as $status)
                                <option value="{{ $status }}" @selected($consultancyRequest->status === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-uppercase text-secondary">Payment Status</label>
                        <select name="payment_status" class="form-select radius-30">
                            @foreach(['pending','paid','failed','review'] as $paymentStatus)
                                <option value="{{ $paymentStatus }}" @selected($consultancyRequest->payment_status === $paymentStatus)>{{ ucfirst($paymentStatus) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold small text-uppercase text-secondary">Upload Report</label>
                        <input type="file" name="report_file" class="form-control radius-30">
                        @if($consultancyRequest->report_file)
                            <div class="mt-2 text-end">
                                <a href="{{ asset('storage/' . $consultancyRequest->report_file) }}" target="_blank" class="btn btn-sm btn-outline-secondary radius-30"><i class="bx bx-file me-1"></i>View Current Report</a>
                            </div>
                        @endif
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold small text-uppercase text-secondary">Admin Note</label>
                        <textarea name="admin_note" rows="5" class="form-control radius-30">{{ old('admin_note', $consultancyRequest->admin_note) }}</textarea>
                    </div>

                    <div class="col-12 mt-4 text-end">
                        <button type="submit" class="btn btn-primary radius-30 w-100">Update Advisory Case</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delivery Assets -->
        <div class="card radius-10 border-0 shadow-sm mb-4">
            <div class="card-body">
                <h5 class="mb-3 fw-bold">Delivery Assets</h5>
                <div class="list-group list-group-flush">
                    <div class="list-group-item px-0">
                        <small class="text-muted text-uppercase d-block mb-1">Recorded Admin Note</small>
                        <p class="mb-0">{{ $consultancyRequest->admin_note ?: 'No admin note recorded yet.' }}</p>
                    </div>
                    <div class="list-group-item px-0">
                        <small class="text-muted text-uppercase d-block mb-2">Consultancy Report Output</small>
                        @if($consultancyRequest->report_file)
                            <div>
                                <a href="{{ asset('storage/' . $consultancyRequest->report_file) }}" target="_blank" class="btn btn-sm btn-outline-primary radius-30"><i class="bx bx-download me-1"></i>Download Report</a>
                            </div>
                        @else
                            <p class="mb-0 text-warning"><i class="bx bx-info-circle me-1"></i>No report uploaded yet.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @include('partials.reviews.admin-panel', [
            'reviews' => $consultancyRequest->reviews,
            'title' => 'Consultancy Reviews',
            'subtitle' => 'Moderate approved consultancy outcomes before they appear publicly.',
            'wrapperClass' => 'card radius-10 border-0 shadow-sm mt-4 p-4',
        ])
    </div>
</div>
@endsection

