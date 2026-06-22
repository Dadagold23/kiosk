<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('partials.seo-meta', [
    'title' => 'Customer Dashboard | ' . config('app.name', 'Kiosk'),
    'description' => 'Kiosk customer dashboard for orders, payments, services, consultancy, bookings, and emergency support.',
    'keywords' => 'Kiosk customer, dashboard, orders, payments, bookings, emergency',
    'robots' => 'noindex, nofollow',
    'themeColor' => '#f6efe4',
    ])

    <link rel="icon" href="{{ asset(config('kiosk.assets.favicon', 'favicon.ico')) }}">
    @include('partials.assets.ui-head', [
    'includeFonts' => true,
    'includeIcomoon' => true,
    'includeBootstrap' => true,
    'includeBootstrapIcons' => true,
    ])

    @php
    $customerUser = auth()->user();
    if ($customerUser) {
        $customerUser->loadCount(['orders', 'wishlistItems', 'bookings']);
    }
    $customerName = $customerUser?->name ?: 'Customer';
    $customerInitial = strtoupper(substr($customerName, 0, 1));
    $customerGreeting = now()->hour < 12 ? 'Good morning' : (now()->hour < 17 ? 'Good afternoon' : 'Good evening' ); $hasCustomerAside = trim($__env->yieldContent('customer_aside')) !== ''; $customerAsideAligned = trim($__env->yieldContent('customer_aside_align', 'topbar')) === 'content';
            @endphp <style>
            :root{
            --customer-type-scale:.93;
            --customer-bg:#f6efe4;
            --customer-surface:#fffdf9;
            --customer-surface-soft:#fbf5ec;
            --customer-ink:#2a3c39;
            --customer-muted:#7c7468;
            --customer-border:rgba(89, 91, 68, .14);
            --customer-primary:#2f6f68;
            --customer-primary-deep:#1b4f49;
            --customer-accent:#e5efe8;
            --customer-accent-strong:#255e58;
            --customer-warm:#cf8359;
            --customer-danger:#bf5a50;
            --customer-shadow:0 26px 62px rgba(60, 47, 32, .10);
            --customer-shadow-soft:0 14px 36px rgba(60, 47, 32, .07);
            }

            html{
            font-size:calc(16px * var(--customer-type-scale));
            }

            body{
            background:
            radial-gradient(circle at top right, rgba(255,255,255,.6), transparent 24%),
            radial-gradient(circle at top left, rgba(207,131,89,.16), transparent 22%),
            linear-gradient(180deg, #e8dcc8 0, #e8dcc8 132px, var(--customer-bg) 132px);
            color:var(--customer-ink);
            font-family:"Space Grotesk", "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            margin:0;
            min-height:100vh;
            }

            .skip-link{
            left:-9999px;
            position:absolute;
            top:auto;
            }

            .skip-link:focus{
            left:1rem;
            top:1rem;
            z-index:1200;
            }

            .customer-shell{
            padding:1.35rem 1rem 1rem;
            position:relative;
            z-index:1;
            }

            .customer-layout-row{
            align-items:start;
            min-height:calc(100vh - 2rem);
            }

            .customer-sidebar-column,
            .customer-utility-column{
            display:flex;
            }

            .customer-sidebar,
            .customer-utility{
            position:sticky;
            top:1.35rem;
            width:100%;
            }

            .customer-sidebar{
            background:var(--customer-surface);
            border:1px solid var(--customer-border);
            border-radius:28px;
            box-shadow:var(--customer-shadow);
            min-height:calc(100vh - 2.7rem);
            padding:1rem;
            }

            .customer-brand{
            align-items:center;
            color:var(--customer-primary-deep);
            display:flex;
            font-size:1.35rem;
            font-weight:800;
            gap:.7rem;
            letter-spacing:-.04em;
            margin-bottom:1rem;
            text-decoration:none;
            }

            .customer-brand-mark{
            align-items:center;
            background:linear-gradient(135deg, #cf8359 0%, #9e6143 100%);
            border-radius:16px;
            color:#fff;
            display:inline-flex;
            font-size:.95rem;
            font-weight:800;
            height:42px;
            justify-content:center;
            width:42px;
            }

            .customer-utility{
            background:transparent;
            border:0;
            border-radius:0;
            box-shadow:none;
            min-height:auto;
            padding:0;
            }

            .customer-utility.is-aligned{
            margin-top:5.8rem;
            }

            .customer-main{
            min-height:100vh;
            }

            .customer-topbar{
            align-items:center;
            background:rgba(255,253,249,.9);
            backdrop-filter:blur(12px);
            border:1px solid rgba(255,255,255,.5);
            border-radius:28px;
            box-shadow:var(--customer-shadow);
            display:flex;
            flex-wrap:wrap;
            gap:1rem;
            justify-content:space-between;
            padding:1rem 1.15rem;
            }

            .customer-topbar-title h5{
            font-size:1.22rem;
            font-weight:800;
            letter-spacing:-.03em;
            margin:0;
            }

            .customer-topbar-title small{
            color:var(--customer-muted);
            display:block;
            margin-top:.12rem;
            }

            .customer-topbar-search{
            align-items:center;
            background:rgba(251,245,236,.96);
            border:1px solid var(--customer-border);
            border-radius:999px;
            display:flex;
            gap:.65rem;
            min-height:46px;
            min-width:min(100%, 330px);
            padding:0 .95rem;
            }

            .customer-topbar-search i{
            color:var(--customer-muted);
            }

            .customer-topbar-search input{
            background:transparent;
            border:0;
            color:var(--customer-ink);
            flex:1;
            font-size:.93rem;
            outline:none;
            padding:0;
            }

            .customer-topbar-actions{
            align-items:center;
            display:flex;
            flex-wrap:wrap;
            gap:.7rem;
            }

            .customer-pill{
            align-items:center;
            background:rgba(251,245,236,.96);
            border:1px solid var(--customer-border);
            border-radius:999px;
            color:var(--customer-primary-deep);
            display:inline-flex;
            font-size:.84rem;
            font-weight:700;
            gap:.48rem;
            min-height:40px;
            padding:.42rem .82rem;
            text-decoration:none;
            }

            .customer-btn-primary,
            .customer-btn-secondary,
            .mobile-customer-toggle{
            border-radius:999px;
            font-weight:700;
            min-height:40px;
            padding:.56rem .95rem;
            }

            .customer-btn-primary{
            background:linear-gradient(180deg, var(--customer-primary) 0%, var(--customer-primary-deep) 100%);
            border:0;
            color:#fff;
            }

            .customer-btn-secondary,
            .mobile-customer-toggle{
            background:rgba(255,253,249,.96);
            border:1px solid var(--customer-border);
            color:var(--customer-primary-deep);
            }

            .customer-card,
            .feature-card,
            .dashboard-card{
            background:var(--customer-surface);
            border:1px solid var(--customer-border);
            border-radius:26px;
            box-shadow:var(--customer-shadow-soft);
            overflow:hidden;
            }

            .customer-section-title{
            color:var(--customer-ink);
            font-size:1.12rem;
            font-weight:800;
            letter-spacing:-.03em;
            margin:0 0 .25rem;
            }

            .customer-section-copy{
            color:var(--customer-muted);
            margin:0;
            }

            .customer-eyebrow{
            color:var(--customer-primary-deep);
            display:inline-block;
            font-size:.75rem;
            font-weight:800;
            letter-spacing:.12em;
            margin-bottom:.55rem;
            text-transform:uppercase;
            }

            .customer-soft-button{
            background:rgba(251,245,236,.96);
            border:1px solid var(--customer-border);
            border-radius:999px;
            color:var(--customer-primary-deep);
            display:inline-flex;
            font-size:.84rem;
            font-weight:700;
            min-height:38px;
            padding:.5rem .85rem;
            text-decoration:none;
            }

            .customer-sidebar-note{
            color:var(--customer-muted);
            font-size:.88rem;
            line-height:1.6;
            }

            .offcanvas-customer{
            background:var(--customer-surface);
            color:var(--customer-ink);
            }

            .offcanvas-customer .offcanvas-header{
            border-bottom:1px solid var(--customer-border);
            }

            .customer-page-grid{
            display:grid;
            gap:1.25rem;
            }

            .customer-page-block{
            padding:1.15rem;
            }

            .customer-page-block .form-control,
            .customer-page-block .form-select,
            .customer-page-block textarea.form-control{
            background:rgba(251,245,236,.96);
            border:1px solid var(--customer-border);
            border-radius:16px;
            box-shadow:none;
            color:var(--customer-ink);
            min-height:46px;
            padding:.72rem .9rem;
            }

            .customer-page-block .form-control:focus,
            .customer-page-block .form-select:focus,
            .customer-page-block textarea.form-control:focus{
            background:#fff;
            border-color:var(--customer-primary);
            box-shadow:0 0 0 .2rem color-mix(in srgb, var(--customer-primary) 16%, transparent);
            }

            .customer-page-block textarea.form-control{
            min-height:140px;
            }

            .customer-field label{
            color:var(--customer-muted);
            display:block;
            font-size:.74rem;
            font-weight:800;
            letter-spacing:.08em;
            margin-bottom:.42rem;
            text-transform:uppercase;
            }

            .customer-status-pill{
            border-radius:999px;
            display:inline-flex;
            font-size:.72rem;
            font-weight:800;
            padding:.34rem .62rem;
            text-transform:uppercase;
            }

            .customer-status-pill.is-success{
            background:rgba(34, 197, 94, .12);
            color:#1b8d48;
            }

            .customer-status-pill.is-warning{
            background:rgba(207, 131, 89, .14);
            color:#9e6143;
            }

            .customer-status-pill.is-danger{
            background:rgba(217, 83, 83, .12);
            color:var(--customer-danger);
            }

            .customer-status-pill.is-primary{
            background:rgba(47, 111, 104, .12);
            color:var(--customer-primary-deep);
            }

            .customer-info-grid{
            display:grid;
            gap:.85rem;
            grid-template-columns:repeat(2, minmax(0, 1fr));
            }

            .customer-info-card{
            background:rgba(251,245,236,.96);
            border:1px solid var(--customer-border);
            border-radius:18px;
            padding:.95rem;
            }

            .customer-info-card .label{
            color:var(--customer-muted);
            display:block;
            font-size:.72rem;
            font-weight:800;
            letter-spacing:.08em;
            margin-bottom:.35rem;
            text-transform:uppercase;
            }

            .customer-info-card .value{
            color:var(--customer-ink);
            font-size:.94rem;
            font-weight:700;
            line-height:1.5;
            }

            .customer-panel-note{
            background:rgba(251,245,236,.96);
            border:1px solid var(--customer-border);
            border-radius:20px;
            padding:1rem;
            }

            .customer-pagination .pagination{
            gap:.35rem;
            }

            .customer-pagination .page-link{
            background:var(--customer-surface);
            border:1px solid var(--customer-border);
            border-radius:12px;
            color:var(--customer-primary-deep);
            }

            .customer-pagination .active > .page-link{
            background:var(--customer-primary);
            border-color:var(--customer-primary);
            color:#fff;
            }

            @media (max-width: 1199.98px){
            .customer-shell{
            padding:1rem .85rem;
            }

            .customer-utility.is-aligned{
            margin-top:0;
            }
            }

            @media (max-width: 991.98px){
            body{
            background:linear-gradient(180deg, #cfe7fb 0, #cfe7fb 104px, var(--customer-bg) 104px);
            }

            .customer-main{
            min-height:auto;
            }

            .customer-topbar-search{
            min-width:100%;
            order:3;
            }
            }

            @media (max-width: 767.98px){
            .customer-topbar{
            border-radius:24px;
            }

            .customer-info-grid{
            grid-template-columns:1fr;
            }
            }
            </style>

            @stack('styles')
</head>

<body>
    @include('partials.preloader')
    <a href="#main-content" class="skip-link btn btn-light">Skip to main content</a>

    <div class="container-fluid customer-shell">
        <div class="row g-4 customer-layout-row">
            <aside class="col-xl-2 col-lg-3 d-none d-lg-block customer-sidebar-column">
                <div class="customer-sidebar">
                    <a href="{{ route('dashboard') }}" class="customer-brand">
                        <span>Kiosk</span>
                    </a>
                    @include('partials.amerce.account-sidebar', ['user' => $customerUser])
                </div>
            </aside>

            <main id="main-content" class="col-xl-7 col-lg-9 customer-main">
                <div class="customer-topbar mb-4">
                    <div class="d-flex align-items-center gap-2">
                        <button class="btn mobile-customer-toggle d-lg-none" type="button" data-bs-toggle="offcanvas"
                            data-bs-target="#customerSidebar">
                            <i class="bi bi-list"></i>
                            <span>Menu</span>
                        </button>
                        <div class="customer-topbar-title">
                            <h5>@yield('customer_page_title', 'Customer Dashboard')</h5>
                            <small>@yield('customer_page_subtitle', $customerGreeting . ', ' . $customerName .
                                '.')</small>
                        </div>
                    </div>

                    <form action="{{ route('customer.search') }}" method="GET" class="customer-topbar-search" aria-label="Customer dashboard search">
                        <i class="bi bi-search"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products, orders, or support"
                            aria-label="Search customer dashboard">
                    </form>

                    <div class="customer-topbar-actions">
                        <span class="customer-pill">
                            <i class="bi bi-person-circle"></i>
                            <span>{{ $customerGreeting }}</span>
                        </span>

                        <a href="{{ route('shop.index') }}" class="btn customer-btn-secondary">Shop</a>
                        <a href="{{ route('home') }}" class="btn customer-btn-secondary">Visit Site</a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="btn customer-btn-primary">Logout</button>
                        </form>
                    </div>
                </div>

                @include('partials.flash')

                @yield('customer_body')
            </main>

            <aside class="col-xl-3 d-none d-xl-block customer-utility-column">
                <div class="customer-utility {{ $customerAsideAligned ? 'is-aligned' : '' }}">
                    @if($hasCustomerAside)
                        @yield('customer_aside')
                    @else
                        @include('partials.amerce.customer-default-aside')
                    @endif
                </div>
            </aside>
        </div>
    </div>

    <div class="offcanvas offcanvas-start offcanvas-customer" tabindex="-1" id="customerSidebar">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title fw-bold mb-0">Kiosk Account</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            @include('partials.amerce.account-sidebar', ['variant' => 'mobile', 'user' => $customerUser])
        </div>
    </div>

    @include('partials.assets.ui-scripts', [
    'scripts' => [
    'assets/js/plugin/bootstrap.min.js',
    ],
    ])
    @include('partials.idle-logout')
    @include('partials.preloader-scripts')
    @stack('scripts')
</body>

</html>
