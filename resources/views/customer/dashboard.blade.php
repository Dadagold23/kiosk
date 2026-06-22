@extends('layouts.customer')

@section('meta_title', 'Dashboard | Kiosk')
@section('meta_description', 'Manage your orders, payments, services, consultancy, bookings, and emergency activity from your Kiosk dashboard.')
@section('customer_page_title', 'Dashboard')
@section('customer_page_subtitle', 'Welcome back, ' . auth()->user()->name . '. Here is your account flow at a glance.')

@push('styles')
<style>
    .customer-dashboard{
        display:grid;
        gap:1.25rem;
    }

    .customer-welcome-card{
        background:
            linear-gradient(115deg, rgba(27, 79, 73, .96) 0%, rgba(47, 111, 104, .94) 54%, rgba(207, 131, 89, .86) 100%),
            radial-gradient(circle at top right, rgba(255,255,255,.18), transparent 32%);
        color:#fff;
        padding:1.4rem;
        position:relative;
    }

    .customer-welcome-card::after{
        border:1px solid rgba(255,255,255,.16);
        border-radius:22px;
        content:"";
        inset:1rem;
        pointer-events:none;
        position:absolute;
    }

    .customer-welcome-card > *{
        position:relative;
        z-index:1;
    }

    .customer-welcome-chip{
        align-items:center;
        background:rgba(255,255,255,.14);
        border:1px solid rgba(255,255,255,.12);
        border-radius:999px;
        color:#fff;
        display:inline-flex;
        font-size:.76rem;
        font-weight:800;
        gap:.42rem;
        letter-spacing:.06em;
        margin-bottom:.85rem;
        padding:.44rem .75rem;
        text-transform:uppercase;
    }

    .customer-welcome-title{
        font-size:clamp(1.9rem, 3.2vw, 2.65rem);
        font-weight:800;
        letter-spacing:-.05em;
        line-height:1.02;
        margin:0 0 .75rem;
        max-width:12ch;
    }

    .customer-welcome-copy{
        color:rgba(255,255,255,.82);
        line-height:1.7;
        margin:0 0 1rem;
        max-width:52ch;
        overflow-wrap:anywhere;
    }

    .customer-welcome-actions{
        display:flex;
        flex-wrap:wrap;
        gap:.75rem;
    }

    .customer-welcome-actions a{
        align-items:center;
        border-radius:999px;
        display:inline-flex;
        font-weight:700;
        min-height:42px;
        padding:.62rem 1rem;
        text-decoration:none;
    }

    .customer-welcome-actions .is-light{
        background:#fff;
        color:var(--customer-primary-deep);
    }

    .customer-welcome-actions .is-outline{
        background:rgba(255,255,255,.08);
        border:1px solid rgba(255,255,255,.24);
        color:#fff;
    }

    .customer-kpi-strip{
        display:grid;
        gap:.85rem;
        grid-template-columns:repeat(4, minmax(0, 1fr));
        margin-top:1.1rem;
    }

    .customer-kpi-pill{
        background:rgba(255,255,255,.12);
        border:1px solid rgba(255,255,255,.12);
        border-radius:20px;
        padding:.9rem;
    }

    .customer-kpi-pill .label{
        color:rgba(255,255,255,.68);
        display:block;
        font-size:.72rem;
        font-weight:700;
        letter-spacing:.08em;
        margin-bottom:.45rem;
        overflow-wrap:anywhere;
        text-transform:uppercase;
    }

    .customer-kpi-pill .value{
        display:block;
        font-size:1.35rem;
        font-weight:800;
        letter-spacing:-.04em;
        margin-bottom:.18rem;
    }

    .customer-kpi-pill .meta{
        color:rgba(255,255,255,.76);
        font-size:.82rem;
        line-height:1.45;
        margin:0;
        overflow-wrap:anywhere;
    }

    .customer-panel{
        padding:1.15rem;
    }

    .customer-panel-head{
        align-items:flex-start;
        display:flex;
        flex-wrap:wrap;
        gap:.85rem;
        justify-content:space-between;
        margin-bottom:1rem;
    }

    .customer-module-grid{
        display:grid;
        gap:1rem;
        grid-template-columns:repeat(auto-fit, minmax(190px, 1fr));
    }

    .customer-module-card{
        background:linear-gradient(180deg, #fffdf9 0%, var(--customer-surface-soft) 100%);
        border:1px solid var(--customer-border);
        border-radius:22px;
        padding:1rem;
    }

    .customer-module-thumb{
        align-items:center;
        background:var(--customer-accent);
        border-radius:18px;
        color:var(--customer-primary-deep);
        display:inline-flex;
        font-size:1.05rem;
        height:44px;
        justify-content:center;
        margin-bottom:.85rem;
        width:44px;
    }

    .customer-module-card h3{
        font-size:1rem;
        font-weight:800;
        letter-spacing:-.02em;
        margin:0 0 .4rem;
        overflow-wrap:anywhere;
    }

    .customer-module-value{
        color:var(--customer-primary-deep);
        display:block;
        font-size:1.6rem;
        font-weight:800;
        letter-spacing:-.05em;
        margin-bottom:.25rem;
    }

    .customer-module-card p{
        color:var(--customer-muted);
        font-size:.87rem;
        line-height:1.58;
        margin:0 0 .8rem;
        overflow-wrap:anywhere;
    }

    .customer-module-card a{
        color:var(--customer-primary-deep);
        font-size:.84rem;
        font-weight:700;
        text-decoration:none;
    }

    .customer-readiness-grid{
        display:grid;
        gap:.8rem;
        grid-template-columns:repeat(3, minmax(0, 1fr));
        margin-bottom:1rem;
    }

    .customer-readiness-item{
        background:var(--customer-surface-soft);
        border:1px solid var(--customer-border);
        border-radius:18px;
        padding:.9rem;
    }

    .customer-readiness-item .label{
        color:var(--customer-muted);
        display:block;
        font-size:.72rem;
        font-weight:800;
        letter-spacing:.08em;
        margin-bottom:.4rem;
        text-transform:uppercase;
    }

    .customer-readiness-item .state{
        display:block;
        font-size:.94rem;
        font-weight:800;
        overflow-wrap:anywhere;
    }

    .customer-progress{
        background:var(--customer-accent);
        border-radius:999px;
        height:12px;
        margin-bottom:1rem;
        overflow:hidden;
    }

    .customer-progress-bar{
        background:linear-gradient(90deg, var(--customer-warm) 0%, var(--customer-primary) 100%);
        border-radius:999px;
        height:100%;
    }

    .customer-recommendation{
        background:var(--customer-surface-soft);
        border:1px solid var(--customer-border);
        border-radius:20px;
        padding:1rem;
    }

    .customer-recommendation .label{
        color:var(--customer-muted);
        display:block;
        font-size:.72rem;
        font-weight:800;
        letter-spacing:.08em;
        margin-bottom:.35rem;
        text-transform:uppercase;
    }

    .customer-activity-list{
        display:grid;
        gap:.8rem;
    }

    .customer-activity-item{
        align-items:center;
        background:var(--customer-surface-soft);
        border:1px solid var(--customer-border);
        border-radius:18px;
        display:grid;
        gap:.75rem;
        grid-template-columns:minmax(0, 1fr) auto;
        padding:.9rem 1rem;
    }

    .customer-activity-item .title{
        color:var(--customer-ink);
        display:block;
        font-size:.95rem;
        font-weight:800;
        margin-bottom:.15rem;
        overflow-wrap:anywhere;
    }

    .customer-activity-item .meta{
        color:var(--customer-muted);
        display:block;
        font-size:.84rem;
        line-height:1.45;
        overflow-wrap:anywhere;
    }

    .customer-activity-item .value{
        color:var(--customer-primary-deep);
        display:block;
        font-size:.95rem;
        font-weight:800;
        overflow-wrap:anywhere;
        text-align:right;
    }

    .customer-mini-stack{
        display:grid;
        gap:.7rem;
    }

    .customer-mini-card{
        background:var(--customer-surface-soft);
        border:1px solid var(--customer-border);
        border-radius:20px;
        padding:.95rem;
    }

    .customer-mini-card .label{
        color:var(--customer-muted);
        display:block;
        font-size:.72rem;
        font-weight:800;
        letter-spacing:.08em;
        margin-bottom:.35rem;
        text-transform:uppercase;
    }

    .customer-mini-card .value{
        display:block;
        font-size:1.35rem;
        font-weight:800;
        letter-spacing:-.04em;
        margin-bottom:.2rem;
    }

    .customer-mini-card .value.is-compact{
        font-size:1rem;
        margin-bottom:.45rem;
    }

    .customer-mini-card p{
        color:var(--customer-muted);
        font-size:.86rem;
        line-height:1.55;
        margin:0;
        overflow-wrap:anywhere;
    }

    .customer-cart-item .title{
        color:var(--customer-ink);
        display:block;
        font-size:.95rem;
        font-weight:800;
        margin-bottom:.2rem;
        overflow-wrap:anywhere;
    }

    .customer-aside-stack{
        display:grid;
        gap:.9rem;
    }

    .customer-aside-card{
        background:linear-gradient(180deg, #fff 0%, var(--customer-surface-soft) 100%);
        border:1px solid var(--customer-border);
        border-radius:22px;
        padding:1rem;
    }

    .customer-aside-card:first-child{
        background:
            linear-gradient(145deg, rgba(255,255,255,.98) 0%, rgba(247,251,255,.98) 100%),
            radial-gradient(circle at top right, rgba(28,95,168,.08), transparent 38%);
        box-shadow:0 18px 34px rgba(32, 77, 122, .10);
        padding:1.1rem;
    }

    .customer-aside-card h3{
        font-size:1rem;
        font-weight:800;
        margin:0 0 .25rem;
    }

    .customer-aside-card p{
        color:var(--customer-muted);
        font-size:.86rem;
        line-height:1.55;
        margin:0 0 .85rem;
        overflow-wrap:anywhere;
    }

    .customer-cart-list{
        display:grid;
        gap:.75rem;
    }

    .customer-cart-item{
        background:#fff;
        border:1px solid var(--customer-border);
        border-radius:16px;
        padding:.8rem .85rem;
    }

    .customer-cart-item .meta{
        color:var(--customer-muted);
        font-size:.82rem;
        line-height:1.45;
        overflow-wrap:anywhere;
    }

    .customer-cart-item .amount{
        color:var(--customer-primary-deep);
        display:block;
        font-size:.92rem;
        font-weight:800;
        margin-top:.35rem;
        overflow-wrap:anywhere;
    }

    .customer-aside-actions{
        display:grid;
        gap:.55rem;
    }

    .customer-aside-actions .btn{
        justify-content:center;
    }

    .customer-aside-feed{
        display:grid;
        gap:.75rem;
    }

    .customer-aside-feed-item{
        background:#fff;
        border:1px solid var(--customer-border);
        border-radius:16px;
        padding:.8rem .85rem;
    }

    .customer-aside-feed-item .label{
        color:var(--customer-muted);
        display:block;
        font-size:.72rem;
        font-weight:800;
        letter-spacing:.08em;
        margin-bottom:.25rem;
        text-transform:uppercase;
    }

    .customer-aside-feed-item .title{
        color:var(--customer-ink);
        display:block;
        font-size:.92rem;
        font-weight:800;
        line-height:1.45;
        margin-bottom:.15rem;
        overflow-wrap:anywhere;
    }

    .customer-aside-feed-item .meta{
        color:var(--customer-muted);
        display:block;
        font-size:.82rem;
        line-height:1.45;
        overflow-wrap:anywhere;
    }

    .customer-empty{
        background:var(--customer-surface-soft);
        border:1px dashed var(--customer-border);
        border-radius:18px;
        color:var(--customer-muted);
        padding:1rem;
    }

    @media (max-width: 1399.98px){
        .customer-kpi-strip{
            grid-template-columns:repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 767.98px){
        .customer-readiness-grid,
        .customer-module-grid,
        .customer-kpi-strip{
            grid-template-columns:1fr;
        }

        .customer-activity-item{
            grid-template-columns:1fr;
        }

        .customer-activity-item .value{
            text-align:left;
        }
    }
</style>
@endpush

@section('customer_body')
@php
    $moduleCards = [
        [
            'title' => 'Orders', 'value' => $stats['orders'], 'copy' => 'Track purchases, deliveries, and order movement from one place.', 'link' => route('orders.index'), 'link_label' => 'Open Orders', 'icon' => 'bi-bag',
        ],
        [
            'title' => 'Payments', 'value' => $stats['payments'], 'copy' => 'Review receipts, successful payments, and pending checkouts.', 'link' => route('orders.index'), 'link_label' => 'Review Payments', 'icon' => 'bi-credit-card-2-front',
        ],
        [
            'title' => 'Services', 'value' => $stats['services'], 'copy' => 'Follow service requests and open support work from your account.', 'link' => route('customer.services.index'), 'link_label' => 'Open Services', 'icon' => 'bi-tools',
        ],
        [
            'title' => 'Bookings', 'value' => $stats['bookings'], 'copy' => 'Keep reservations and travel-related account activity in one view.', 'link' => route('customer.bookings.index'), 'link_label' => 'Open Bookings', 'icon' => 'bi-calendar-check',
        ],
        [
            'title' => 'Consultancy', 'value' => $stats['consultancy'], 'copy' => 'Review advisory requests and consultant follow-up from your account.', 'link' => route('customer.consultancy.index'), 'link_label' => 'Open Consultancy', 'icon' => 'bi-briefcase',
        ],
        [
            'title' => 'Emergency', 'value' => $stats['emergencies'], 'copy' => 'Keep emergency alerts and response activity close at hand.', 'link' => route('customer.emergency.index'), 'link_label' => 'Open Emergency', 'icon' => 'bi-shield-exclamation',
        ],
        [
            'title' => 'Wishlist', 'value' => $stats['wishlist'], 'copy' => 'See what you have saved for later and return to it quickly.', 'link' => route('wishlist.index'), 'link_label' => 'Open Wishlist', 'icon' => 'bi-heart',
        ],
        [
            'title' => 'Notifications', 'value' => $stats['notifications'], 'copy' => 'Catch up on account updates, payment prompts, and new alerts.', 'link' => route('notifications.index'), 'link_label' => 'View Alerts', 'icon' => 'bi-bell',
        ],
    ];

    $utilityCards = [
        [
            'label' => 'Profile Readiness', 'value' => $profileCompletion . '%', 'copy' => 'How complete your payment and delivery profile currently is.',
        ],
        [
            'label' => 'Paid Total', 'value' => 'NGN ' . number_format($stats['paid_total'], 2), 'copy' => 'Recorded successful payments across your account history.',
        ],
        [
            'label' => 'Pending Orders', 'value' => number_format($stats['pending_orders']), 'copy' => 'Orders still in processing, sourcing, or delivery states.',
        ],
    ];

    $cartLikeItems = collect($recentOrders)->take(3);
    $asideUnreadNotifications = collect($recentNotifications)->whereNull('read_at')->count();
@endphp

<div class="customer-dashboard">
    <section class="customer-card customer-welcome-card">
        <span class="customer-welcome-chip"><i class="bi bi-stars"></i><span>Welcome Back</span></span>
        <h1 class="customer-welcome-title">Keep your shopping and account activity moving.</h1>
        <p class="customer-welcome-copy">Use this dashboard to check orders, payments, bookings, and support requests without hunting through different pages.</p>
        <div class="customer-welcome-actions">
            <a href="{{ route('shop.index') }}" class="is-light">Continue Shopping</a>
            <a href="{{ route('orders.index') }}" class="is-outline">Review Orders</a>
        </div>

        <div class="customer-kpi-strip">
            <div class="customer-kpi-pill">
                <span class="label">Revenue Paid</span>
                <span class="value">&#8358;{{ number_format($stats['paid_total'], 2) }}</span>
                <p class="meta">Successful payments already captured on your account.</p>
            </div>
            <div class="customer-kpi-pill">
                <span class="label">Pending Payments</span>
                <span class="value">{{ $stats['pending_payments'] }}</span>
                <p class="meta">Transactions waiting for completion or confirmation.</p>
            </div>
            <div class="customer-kpi-pill">
                <span class="label">Delivered Orders</span>
                <span class="value">{{ $stats['delivered_orders'] }}</span>
                <p class="meta">Orders that have completed the delivery flow.</p>
            </div>
            <div class="customer-kpi-pill">
                <span class="label">Emergency Cases</span>
                <span class="value">{{ $stats['emergencies'] }}</span>
                <p class="meta">Alerts and response records tied to your profile.</p>
            </div>
        </div>
    </section>

    <section class="customer-card customer-panel">
        <div class="customer-panel-head">
            <div>
                <span class="customer-eyebrow">Browse Modules</span>
                <h2 class="customer-section-title">Your account dashboard</h2>
                <p class="customer-section-copy">A quick summary of the sections you use most.</p>
            </div>
        </div>

        <div class="customer-module-grid">
            @foreach($moduleCards as $card)
                <article class="customer-module-card">
                    <span class="customer-module-thumb"><i class="bi {{ $card['icon'] }}"></i></span>
                    <h3>{{ $card['title'] }}</h3>
                    <span class="customer-module-value">{{ $card['value'] }}</span>
                    <p>{{ $card['copy'] }}</p>
                    <a href="{{ $card['link'] }}">{{ $card['link_label'] }}</a>
                </article>
            @endforeach
        </div>
    </section>

    <div class="row g-4">
        <div class="col-12">
            <section class="customer-card customer-panel h-100">
                <div class="customer-panel-head">
                    <div>
                        <span class="customer-eyebrow">Account Readiness</span>
                        <h2 class="customer-section-title">Payment and delivery setup</h2>
                        <p class="customer-section-copy">A quick check on the details that matter for checkout, delivery, and support.</p>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="customer-soft-button">Update Profile</a>
                </div>

                <div class="customer-progress" role="progressbar" aria-label="Profile completion" aria-valuenow="{{ $profileCompletion }}" aria-valuemin="0" aria-valuemax="100">
                    <div class="customer-progress-bar" style="width: {{ $profileCompletion }}%"></div>
                </div>

                <div class="customer-readiness-grid">
                    <div class="customer-readiness-item">
                        <span class="label">Payment Email</span>
                        <div class="state {{ $profileChecks['email'] ? 'text-success' : 'text-danger' }}">{{ $profileChecks['email'] ? 'Ready for Paystack' : 'Needs attention' }}</div>
                    </div>
                    <div class="customer-readiness-item">
                        <span class="label">Phone Number</span>
                        <div class="state {{ $profileChecks['phone'] ? 'text-success' : 'text-danger' }}">{{ $profileChecks['phone'] ? 'Saved' : 'Missing' }}</div>
                    </div>
                    <div class="customer-readiness-item">
                        <span class="label">Delivery Address</span>
                        <div class="state {{ $profileChecks['address'] ? 'text-success' : 'text-danger' }}">{{ $profileChecks['address'] ? 'Saved' : 'Missing' }}</div>
                    </div>
                </div>

                <div class="customer-recommendation">
                    <span class="label">Recommended Next Step</span>
                    <strong>{{ $nextStep }}</strong>
                </div>
            </section>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <section class="customer-card customer-panel h-100">
                <div class="customer-panel-head">
                    <div>
                        <span class="customer-eyebrow">Recent Orders</span>
                        <h2 class="customer-section-title">Order activity</h2>
                        <p class="customer-section-copy">Your latest orders, with the status shown clearly.</p>
                    </div>
                    <a href="{{ route('orders.index') }}" class="customer-soft-button">View All</a>
                </div>

                <div class="customer-activity-list">
                    @forelse($recentOrders as $order)
                        <article class="customer-activity-item">
                            <div>
                                <span class="title">{{ $order->order_no }}</span>
                                <span class="meta">{{ ucfirst(str_replace('_', ' ', $order->order_status)) }} | Payment: {{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}</span>
                            </div>
                            <span class="value">&#8358;{{ number_format($order->total, 2) }}</span>
                        </article>
                    @empty
                        <div class="customer-empty">No orders yet.</div>
                    @endforelse
                </div>
            </section>
        </div>

        <div class="col-lg-6">
            <section class="customer-card customer-panel h-100">
                <div class="customer-panel-head">
                    <div>
                        <span class="customer-eyebrow">Recent Payments</span>
                        <h2 class="customer-section-title">Payment activity</h2>
                        <p class="customer-section-copy">Recent payments and their current status.</p>
                    </div>
                    <a href="{{ route('orders.index') }}" class="customer-soft-button">Orders</a>
                </div>

                <div class="customer-activity-list">
                    @forelse($recentPayments as $payment)
                        <article class="customer-activity-item">
                            <div>
                                <span class="title">{{ $payment->reference }}</span>
                                <span class="meta">{{ ucfirst(str_replace('_', ' ', $payment->status)) }} | {{ ucfirst($payment->gateway ?? 'manual') }}</span>
                            </div>
                            <span class="value">&#8358;{{ number_format($payment->amount, 2) }}</span>
                        </article>
                    @empty
                        <div class="customer-empty">No payments recorded yet.</div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <section class="customer-card customer-panel h-100">
                <div class="customer-panel-head">
                    <div>
                        <span class="customer-eyebrow">Services</span>
                        <h2 class="customer-section-title">Recent Services</h2>
                    </div>
                    <a href="{{ route('customer.services.index') }}" class="customer-soft-button">View All</a>
                </div>
                <div class="customer-activity-list">
                    @forelse($recentServices as $item)
                        <article class="customer-activity-item">
                            <div>
                                <span class="title">{{ $item->title }}</span>
                                <span class="meta">{{ ucfirst(str_replace('_', ' ', $item->status)) }} | Payment: {{ ucfirst(str_replace('_', ' ', $item->payment_status)) }}</span>
                            </div>
                            <span class="value">{{ strtoupper(substr($item->status, 0, 3)) }}</span>
                        </article>
                    @empty
                        <div class="customer-empty">No service requests yet.</div>
                    @endforelse
                </div>
            </section>
        </div>

        <div class="col-lg-4">
            <section class="customer-card customer-panel h-100">
                <div class="customer-panel-head">
                    <div>
                        <span class="customer-eyebrow">Consultancy</span>
                        <h2 class="customer-section-title">Recent Consultancy</h2>
                    </div>
                    <a href="{{ route('customer.consultancy.index') }}" class="customer-soft-button">View All</a>
                </div>
                <div class="customer-activity-list">
                    @forelse($recentConsultancy as $item)
                        <article class="customer-activity-item">
                            <div>
                                <span class="title">{{ $item->subject }}</span>
                                <span class="meta">{{ ucfirst(str_replace('_', ' ', $item->status)) }} | Payment: {{ ucfirst(str_replace('_', ' ', $item->payment_status)) }}</span>
                            </div>
                            <span class="value">{{ strtoupper(substr($item->status, 0, 3)) }}</span>
                        </article>
                    @empty
                        <div class="customer-empty">No consultancy requests yet.</div>
                    @endforelse
                </div>
            </section>
        </div>

        <div class="col-lg-4">
            <section class="customer-card customer-panel h-100">
                <div class="customer-panel-head">
                    <div>
                        <span class="customer-eyebrow">Bookings</span>
                        <h2 class="customer-section-title">Recent Bookings</h2>
                    </div>
                    <a href="{{ route('customer.bookings.index') }}" class="customer-soft-button">View All</a>
                </div>
                <div class="customer-activity-list">
                    @forelse($recentBookings as $booking)
                        <article class="customer-activity-item">
                            <div>
                                <span class="title">{{ ucfirst(str_replace('_', ' ', $booking->booking_type)) }}</span>
                                <span class="meta">{{ ucfirst(str_replace('_', ' ', $booking->status)) }} | Payment: {{ ucfirst(str_replace('_', ' ', $booking->payment_status)) }}</span>
                            </div>
                            <span class="value">{{ strtoupper(substr($booking->status, 0, 3)) }}</span>
                        </article>
                    @empty
                        <div class="customer-empty">No bookings yet.</div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>

    <section class="customer-card customer-panel">
        <div class="customer-panel-head">
            <div>
                <span class="customer-eyebrow">Emergency Desk</span>
                <h2 class="customer-section-title">Recent Emergency Activity</h2>
                <p class="customer-section-copy">Your recent emergency requests and their latest updates.</p>
            </div>
            <a href="{{ route('customer.emergency.index') }}" class="customer-soft-button">Emergency Desk</a>
        </div>

        <div class="row g-3">
            @forelse($recentEmergencies as $item)
                <div class="col-md-6 col-xl-3">
                    <article class="customer-mini-card h-100">
                        <span class="label">{{ ucfirst(str_replace('_', ' ', $item->emergency_type)) }}</span>
                        <span class="value is-compact">{{ $item->phone }}</span>
                        <p>{{ $item->location_text ?: 'No location provided' }}</p>
                        <span class="customer-status-pill {{ $item->status === 'resolved' ? 'is-success' : ($item->status === 'closed' ? 'is-warning' : 'is-danger') }}">
                            {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                        </span>
                    </article>
                </div>
            @empty
                <div class="col-12">
                    <div class="customer-empty">No emergency activity recorded on your account.</div>
                </div>
            @endforelse
        </div>
    </section>
</div>
@endsection

@section('customer_aside')
<div class="customer-aside-stack">
    <div class="customer-aside-card">
        <span class="customer-eyebrow">Account Summary</span>
        <h3>Quick profile view</h3>
        <p>A quick view of the details tied to your account.</p>

        <div class="customer-mini-stack">
            @foreach($utilityCards as $card)
                <div class="customer-mini-card">
                    <span class="label">{{ $card['label'] }}</span>
                    <div class="value">{{ $card['value'] }}</div>
                    <p>{{ $card['copy'] }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <div class="customer-aside-card">
        <span class="customer-eyebrow">Quick Actions</span>
        <h3>Move faster</h3>
        <p>Shortcuts for the pages you are most likely to open next.</p>
        <div class="customer-aside-actions">
            <a href="{{ route('shop.index') }}" class="btn customer-btn-primary">Continue Shopping</a>
            <a href="{{ route('orders.index') }}" class="btn customer-btn-secondary">Review Orders</a>
            <a href="{{ route('customer.services.index') }}" class="btn customer-btn-secondary">Create Service Request</a>
            <a href="{{ route('customer.consultancy.index') }}" class="btn customer-btn-secondary">Open Consultancy Desk</a>
            <a href="{{ route('customer.bookings.index') }}" class="btn customer-btn-secondary">Manage Bookings</a>
            <a href="{{ route('customer.emergency.index') }}" class="btn customer-btn-secondary">Emergency Response Desk</a>
        </div>
    </div>

    <div class="customer-aside-card">
        <span class="customer-eyebrow">Latest Orders</span>
        <h3>Recent orders</h3>
        <p>A short list of your latest orders so you can pick up where you left off.</p>

        <div class="customer-cart-list">
            @forelse($cartLikeItems as $order)
                <div class="customer-cart-item">
                    <span class="title">{{ $order->order_no }}</span>
                    <span class="meta">{{ $order->items->count() }} item(s) | {{ ucfirst(str_replace('_', ' ', $order->order_status)) }}</span>
                    <span class="amount">&#8358;{{ number_format($order->total, 2) }}</span>
                </div>
            @empty
                <div class="customer-empty">No recent orders available yet.</div>
            @endforelse
        </div>
    </div>

    <div class="customer-aside-card">
        <span class="customer-eyebrow">Next Actions</span>
        <h3>Keep things moving</h3>
        <p>Use these links when you want to get back to the most common tasks quickly.</p>
        <div class="customer-aside-actions">
            <a href="{{ route('wishlist.index') }}" class="btn customer-btn-secondary">Open Wishlist</a>
            <a href="{{ route('notifications.index') }}" class="btn customer-btn-secondary">View Notifications</a>
            <a href="{{ route('profile.edit') }}" class="btn customer-btn-primary">Edit Profile</a>
        </div>
    </div>

    <div class="customer-aside-card">
        <span class="customer-eyebrow">Saved Items And Alerts</span>
        <h3>Keep an eye on what matters</h3>
        <p>Your latest saved products and account notifications are right here when you need a quick check.</p>

        <div class="customer-mini-stack mb-3">
            <div class="customer-mini-card">
                <span class="label">Wishlist Items</span>
                <span class="value">{{ $recentWishlistItems->count() }}</span>
                <p>Saved products ready for a second look or a move into your cart.</p>
            </div>
            <div class="customer-mini-card">
                <span class="label">Unread Alerts</span>
                <span class="value">{{ $asideUnreadNotifications }}</span>
                <p>Recent updates that still need your attention.</p>
            </div>
        </div>

        <div class="customer-aside-feed">
            @forelse($recentNotifications as $notification)
                <div class="customer-aside-feed-item">
                    <span class="label">{{ is_null($notification->read_at) ? 'Unread' : 'Read' }}</span>
                    <span class="title">{{ $notification->data['title'] ?? 'Notification' }}</span>
                    <span class="meta">{{ $notification->created_at->format('d M Y, h:i A') }}</span>
                </div>
            @empty
                @forelse($recentWishlistItems as $item)
                    <div class="customer-aside-feed-item">
                        <span class="label">Wishlist</span>
                        <span class="title">{{ $item->product?->name ?: 'Saved product' }}</span>
                        <span class="meta">{{ $item->product?->category?->name ?: 'Catalog item' }}</span>
                    </div>
                @empty
                    <div class="customer-empty">No recent wishlist items or notifications yet.</div>
                @endforelse
            @endforelse
        </div>
    </div>
</div>
@endsection
