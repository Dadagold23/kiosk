@extends('layouts.customer')

@section('customer_page_title', 'Notifications')
@section('customer_page_subtitle', 'Updates from your Kiosk account activities and operational workflows.')

@section('customer_body')
@php
    $unreadCount = $notifications->getCollection()->whereNull('read_at')->count();
@endphp

<div class="customer-page-grid">
    <div class="customer-card customer-page-block muara-module-hero muara-module-hero-primary">
        <div class="row g-4 align-items-center">
            <div class="col-lg-8">
                <span class="customer-welcome-chip">Activity Feed</span>
                <h2 class="fw-bold mb-2">Track every account update, payment prompt, and operational notice in one stream.</h2>
                <p class="mb-0 muara-module-copy">Unread items stay actionable here so your orders, services, bookings, and emergency workflows do not go cold.</p>
            </div>
            <div class="col-lg-4">
                <div class="muara-summary-grid">
                    <div class="muara-summary-card"><div class="muara-summary-label">Total alerts</div><div class="muara-summary-value">{{ $notifications->total() }}</div></div>
                    <div class="muara-summary-card"><div class="muara-summary-label">Unread</div><div class="muara-summary-value">{{ $unreadCount }}</div></div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="customer-panel-note">{{ session('success') }}</div>
    @endif

    <div class="customer-card customer-page-block">
        <div class="customer-page-grid">
            @forelse($notifications as $notification)
                <article class="muara-record-card">
                    <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
                        <div class="d-flex gap-3 align-items-start">
                            <span class="customer-status-pill {{ is_null($notification->read_at) ? 'is-primary' : 'is-success' }}">{{ is_null($notification->read_at) ? 'Unread' : 'Read' }}</span>
                            <div>
                                <div class="record-title mb-1">{{ $notification->data['title'] ?? 'Notification' }}</div>
                                <p class="text-muted mb-1">{{ $notification->data['message'] ?? '' }}</p>
                                <div class="record-date">{{ $notification->created_at->format('d M Y, h:i A') }}</div>
                            </div>
                        </div>

                        @if(is_null($notification->read_at))
                            <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                                @csrf
                                <button class="btn customer-btn-primary">Mark as Read</button>
                            </form>
                        @endif
                    </div>
                </article>
            @empty
                <div class="customer-empty">No notifications yet.</div>
            @endforelse
        </div>
    </div>

    <div class="customer-pagination">{{ $notifications->links() }}</div>
</div>
@endsection
