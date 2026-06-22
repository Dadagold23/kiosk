@extends('layouts.admin')

@section('meta_title', 'Admin Search | Kiosk')
@section('page_title', 'Global Search')
@section('page_subtitle', $search !== '' ? 'Results for "' . $search . '"' : 'Search across orders, users, payments, products, and operations records.')

@section('content')
<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Search</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">Global Search</li>
            </ol>
        </nav>
    </div>
</div>
<!--end breadcrumb-->

<div class="row g-4">
    <!-- Search Banner Card -->
    <div class="col-12">
        <div class="card radius-10 border-0 shadow-sm">
            <div class="card-body p-4">
                <h5 class="mb-1 fw-bold text-dark">Admin-Wide Search</h5>
                <p class="text-muted small mb-0">
                    @if($search !== '')
                        Found {{ $totalMatches }} quick result{{ $totalMatches === 1 ? '' : 's' }} across the main admin modules for "{{ $search }}".
                    @else
                        Use the top search bar to look across orders, payments, users, products, bookings, services, emergencies, and logs.
                    @endif
                </p>
            </div>
        </div>
    </div>

    @if($search !== '')
        @foreach($sections as $section)
            <div class="col-xl-6 col-xxl-6">
                <div class="card radius-10 border-0 shadow-sm h-100 mb-0">
                    <div class="card-body p-4">
                        <h5 class="mb-3 fw-bold text-dark">{{ $section['title'] }}</h5>

                        <div class="list-group list-group-flush">
                            @forelse($results[$section['key']] as $item)
                                <div class="list-group-item px-0 py-3 bg-transparent">
                                    @if($section['key'] === 'orders')
                                        <h6 class="mb-1"><a href="{{ route('admin.orders.show', $item) }}" class="fw-bold text-primary text-decoration-none">{{ $item->order_no }}</a></h6>
                                        <small class="text-muted">{{ $item->user?->name ?: 'Customer pending' }} · Status: <span class="text-uppercase small fw-bold text-secondary">{{ str_replace('_', ' ', $item->order_status) }}</span></small>
                                    @elseif($section['key'] === 'payments')
                                        <h6 class="mb-1"><a href="{{ route('admin.payments.show', $item) }}" class="fw-bold text-primary text-decoration-none">{{ $item->reference }}</a></h6>
                                        <small class="text-muted">{{ $item->user?->name ?: 'Customer pending' }} · Status: <span class="text-uppercase small fw-bold text-secondary">{{ str_replace('_', ' ', $item->status) }}</span></small>
                                    @elseif($section['key'] === 'users')
                                        <h6 class="mb-1"><a href="{{ route('admin.users.edit', $item) }}" class="fw-bold text-primary text-decoration-none">{{ $item->name }}</a></h6>
                                        <small class="text-muted">{{ $item->email }}{{ $item->phone ? ' · Phone: ' . $item->phone : '' }}</small>
                                    @elseif($section['key'] === 'products')
                                        <h6 class="mb-1"><a href="{{ route('admin.products.edit', $item) }}" class="fw-bold text-primary text-decoration-none">{{ $item->name }}</a></h6>
                                        <small class="text-muted">SKU: {{ $item->sku ?: 'No SKU' }} · Category: {{ $item->category?->name ?: 'Uncategorized' }}</small>
                                    @elseif($section['key'] === 'services')
                                        <h6 class="mb-1"><a href="{{ route('admin.services.show', $item) }}" class="fw-bold text-primary text-decoration-none">{{ $item->title }}</a></h6>
                                        <small class="text-muted">{{ $item->user?->name ?: 'Customer pending' }} · Status: <span class="text-uppercase small fw-bold text-secondary">{{ str_replace('_', ' ', $item->status) }}</span></small>
                                    @elseif($section['key'] === 'consultancies')
                                        <h6 class="mb-1"><a href="{{ route('admin.consultancy.show', $item) }}" class="fw-bold text-primary text-decoration-none">{{ $item->subject }}</a></h6>
                                        <small class="text-muted">{{ $item->user?->name ?: 'Customer pending' }} · Status: <span class="text-uppercase small fw-bold text-secondary">{{ str_replace('_', ' ', $item->status) }}</span></small>
                                    @elseif($section['key'] === 'bookings')
                                        <h6 class="mb-1"><a href="{{ route('admin.bookings.show', $item) }}" class="fw-bold text-primary text-decoration-none">{{ $item->title }}</a></h6>
                                        <small class="text-muted">{{ $item->user?->name ?: 'Customer pending' }} · Status: <span class="text-uppercase small fw-bold text-secondary">{{ str_replace('_', ' ', $item->status) }}</span></small>
                                    @elseif($section['key'] === 'emergencies')
                                        <h6 class="mb-1"><a href="{{ route('admin.emergency.show', $item) }}" class="fw-bold text-primary text-decoration-none">{{ ucfirst(str_replace('_', ' ', $item->emergency_type)) }}</a></h6>
                                        <small class="text-muted">{{ $item->full_name }} · Phone: {{ $item->phone }}</small>
                                    @else
                                        <h6 class="mb-1"><a href="{{ route('admin.activity-logs.index', ['search' => $search]) }}" class="fw-bold text-primary text-decoration-none">{{ str($item->action)->replace('_', ' ')->title() }}</a></h6>
                                        <small class="text-muted">{{ $item->user?->name ?: 'System' }} · {{ \Illuminate\Support\Str::limit($item->description, 80) }}</small>
                                    @endif
                                </div>
                            @empty
                                <div class="text-muted text-center py-4">{{ $section['empty'] }}</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>
@endsection
