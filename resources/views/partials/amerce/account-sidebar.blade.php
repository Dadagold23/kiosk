@php
    $accountUser = $user ?? auth()->user();
    $sidebarVariant = $variant ?? 'desktop';
    $navLinks = [
        ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'bi-grid-1x2-fill'], ['route' => 'orders.index', 'label' => 'Orders', 'icon' => 'bi-bag'], ['route' => 'customer.services.index', 'label' => 'Services', 'icon' => 'bi-tools'], ['route' => 'customer.consultancy.index', 'label' => 'Consultancy', 'icon' => 'bi-briefcase'], ['route' => 'customer.bookings.index', 'label' => 'Bookings', 'icon' => 'bi-calendar-check'], ['route' => 'wishlist.index', 'label' => 'Wishlist', 'icon' => 'bi-heart'], ['route' => 'notifications.index', 'label' => 'Notifications', 'icon' => 'bi-bell'], ['route' => 'profile.edit', 'label' => 'Profile', 'icon' => 'bi-person'], ['route' => 'customer.emergency.index', 'label' => 'Emergency Desk', 'icon' => 'bi-life-preserver', 'danger' => true], ['route' => 'shop.index', 'label' => 'Continue Shopping', 'icon' => 'bi-shop'],
    ];
    $accountStats = [
        ['value' => $accountUser->orders_count ?? $accountUser->orders()->count(), 'label' => 'Orders'], ['value' => $accountUser->wishlist_items_count ?? $accountUser->wishlistItems()->count(), 'label' => 'Saved'], ['value' => $accountUser->bookings_count ?? $accountUser->bookings()->count(), 'label' => 'Trips'],
    ];
    $accountPhotoCard = $accountUser->profilePhotoUrl();
@endphp

<style>
    .customer-account-nav{
        display:grid;
        gap:1rem;
    }

    .customer-account-card{
        background:transparent;
        border:0;
        border-bottom:1px solid var(--customer-border);
        border-radius:0;
        padding:0 0 1rem;
    }

    .customer-account-greeting{
        color:var(--customer-muted);
        font-size:.74rem;
        font-weight:800;
        letter-spacing:.08em;
        margin-bottom:.75rem;
        text-transform:uppercase;
    }

    .customer-account-head{
        align-items:center;
        display:flex;
        gap:.8rem;
        margin-bottom:1rem;
    }

    .customer-account-avatar{
        background:
            linear-gradient(180deg, rgba(17, 61, 115, .12) 0%, rgba(17, 61, 115, .02) 100%),
            #ffffff;
        border:1px solid var(--customer-border);
        border-radius:20px;
        box-shadow:var(--customer-shadow-soft);
        display:block;
        flex-shrink:0;
        height:64px;
        overflow:hidden;
        padding:.28rem;
        width:64px;
    }

    .customer-account-avatar img{
        border-radius:16px;
        display:block;
        height:100%;
        object-fit:cover;
        width:100%;
    }

    .customer-account-name{
        color:var(--customer-ink);
        display:block;
        font-size:1rem;
        font-weight:800;
        line-height:1.25;
        margin-bottom:.15rem;
        overflow-wrap:anywhere;
    }

    .customer-account-meta{
        color:var(--customer-muted);
        display:block;
        font-size:.84rem;
        line-height:1.4;
        word-break:break-word;
    }

    .customer-account-badges{
        display:grid;
        gap:.65rem;
        grid-template-columns:repeat(3, minmax(0, 1fr));
        margin-bottom:.9rem;
    }

    .customer-account-badge{
        background:rgba(255,255,255,.72);
        border:1px solid var(--customer-border);
        border-radius:18px;
        padding:.85rem .55rem;
        text-align:center;
    }

    .customer-account-badge strong{
        color:var(--customer-primary-deep);
        display:block;
        font-size:1rem;
        font-weight:800;
    }

    .customer-account-badge span{
        color:var(--customer-muted);
        display:block;
        font-size:.72rem;
        font-weight:700;
        letter-spacing:.05em;
        margin-top:.2rem;
        text-transform:uppercase;
    }

    .customer-account-status{
        align-items:center;
        color:var(--customer-muted);
        display:flex;
        font-size:.84rem;
        gap:.55rem;
        overflow-wrap:anywhere;
    }

    .customer-account-status-block{
        display:grid;
        gap:.7rem;
    }

    .customer-account-status-copy{
        color:var(--customer-muted);
        font-size:.8rem;
        line-height:1.5;
        margin:0;
        overflow-wrap:anywhere;
    }

    .customer-account-status-action{
        align-items:center;
        background:rgba(255,255,255,.78);
        border:1px solid var(--customer-border);
        border-radius:999px;
        color:var(--customer-primary-deep);
        display:inline-flex;
        font-size:.78rem;
        font-weight:700;
        justify-content:center;
        min-height:36px;
        padding:.42rem .8rem;
        text-decoration:none;
    }

    .customer-account-status-action:hover{
        background:var(--customer-accent);
        color:var(--customer-primary-deep);
    }

    .customer-status-dot{
        border-radius:999px;
        display:inline-block;
        height:10px;
        width:10px;
    }

    .customer-status-dot.is-ready{
        background:#22c55e;
    }

    .customer-status-dot.is-pending{
        background:var(--customer-warm);
    }

    .customer-account-menu-label{
        color:var(--customer-muted);
        font-size:.74rem;
        font-weight:800;
        letter-spacing:.08em;
        margin:0 0 .4rem;
        padding:.1rem 0 0;
        text-transform:uppercase;
    }

    .customer-account-links{
        border-top:1px solid var(--customer-border);
        display:grid;
        gap:.18rem;
        padding-top:.75rem;
    }

    .customer-account-links a{
        align-items:center;
        border-radius:18px;
        color:var(--customer-muted);
        display:flex;
        font-size:.92rem;
        font-weight:700;
        gap:.75rem;
        min-width:0;
        padding:.78rem .82rem;
        text-decoration:none;
        transition:background-color .2s ease, color .2s ease, transform .2s ease;
    }

    .customer-account-links a span{
        overflow-wrap:anywhere;
    }

    .customer-account-links a i{
        color:var(--customer-primary-deep);
        font-size:1rem;
        opacity:.76;
    }

    .customer-account-links a:hover,
    .customer-account-links a.active{
        background:var(--customer-accent);
        color:var(--customer-primary-deep);
        transform:translateY(-1px);
    }

    .customer-account-links a.danger-link:hover,
    .customer-account-links a.danger-link.active{
        background:rgba(217, 83, 83, .12);
        color:var(--customer-danger);
    }

    .customer-account-links a.danger-link:hover i,
    .customer-account-links a.danger-link.active i{
        color:var(--customer-danger);
    }

    .customer-account-note{
        border-top:1px solid var(--customer-border);
        color:var(--customer-muted);
        font-size:.86rem;
        line-height:1.58;
        overflow-wrap:anywhere;
        padding:.85rem 0 0;
    }

    .customer-account-nav.is-mobile{
        gap:.85rem;
    }

    .customer-account-nav.is-mobile .customer-account-head{
        align-items:flex-start;
    }

    .customer-account-nav.is-mobile .customer-account-badges{
        margin-bottom:.8rem;
    }

    @media (max-width: 575.98px){
        .customer-account-card{
            padding:0 0 .9rem;
        }

        .customer-account-head{
            align-items:flex-start;
            gap:.7rem;
        }

        .customer-account-avatar{
            border-radius:18px;
            height:58px;
            width:58px;
        }

        .customer-account-avatar img{
            border-radius:14px;
        }

        .customer-account-name{
            font-size:.96rem;
        }

        .customer-account-meta{
            font-size:.8rem;
            line-height:1.45;
        }

        .customer-account-badges{
            gap:.55rem;
        }

        .customer-account-badge{
            border-radius:16px;
            padding:.75rem .4rem;
        }

        .customer-account-badge strong{
            font-size:.94rem;
        }

        .customer-account-badge span{
            font-size:.68rem;
        }

        .customer-account-status-copy{
            font-size:.78rem;
        }

        .customer-account-status-action{
            width:100%;
        }
    }
</style>

<div class="customer-account-nav {{ $sidebarVariant === 'mobile' ? 'is-mobile' : 'is-desktop' }}">
    <div class="customer-account-card">
        <div class="customer-account-greeting">Welcome Back</div>
        <div class="customer-account-head">
            <div class="customer-account-avatar">
                <img src="{{ $accountPhotoCard }}" alt="{{ $accountUser->name }}">
            </div>
            <div>
                <span class="customer-account-name">{{ $accountUser->name }}</span>
                <span class="customer-account-meta">{{ $accountUser->email }}</span>
            </div>
        </div>

        <div class="customer-account-badges">
            @foreach($accountStats as $stat)
                <div class="customer-account-badge">
                    <strong>{{ $stat['value'] }}</strong>
                    <span>{{ $stat['label'] }}</span>
                </div>
            @endforeach
        </div>

        <div class="customer-account-status-block">
            <div class="customer-account-status">
                <span class="customer-status-dot {{ $accountUser->email_verified_at ? 'is-ready' : 'is-pending' }}"></span>
                {{ $accountUser->email_verified_at ? 'Verified account' : 'Verify your email' }}
            </div>
            @unless($accountUser->email_verified_at)
                <p class="customer-account-status-copy">Confirm your email address so account alerts, receipts, and recovery links keep reaching you correctly.</p>
                <a href="{{ route('verification.notice') }}" class="customer-account-status-action">Complete Verification</a>
            @endunless
        </div>
    </div>

    <div>
        <div class="customer-account-menu-label">Main Menu</div>
        <nav class="customer-account-links" aria-label="Customer account navigation">
            @foreach($navLinks as $link)
                <a
                    href="{{ route($link['route']) }}"
                    class="{{ request()->routeIs(str_replace('.index', '.*', $link['route'])) || request()->routeIs($link['route']) ? 'active' : '' }} {{ !empty($link['danger']) ? 'danger-link' : '' }}"
                >
                    <i class="bi {{ $link['icon'] }}"></i>
                    <span>{{ $link['label'] }}</span>
                </a>
            @endforeach
        </nav>
    </div>

    <div class="customer-account-note">
        Keep your delivery, billing, and identity details current so orders, support, and approvals move without delays.
    </div>
</div>
