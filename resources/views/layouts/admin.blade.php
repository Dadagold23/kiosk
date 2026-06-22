<!DOCTYPE html>
<html lang="en" class="semi-dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('partials.seo-meta', [
    'title' => 'Admin Dashboard | ' . config('app.name', 'Kiosk'),
    'description' => 'Kiosk admin dashboard for orders, payments, bookings, services, consultancy, emergency desk, and reports.',
    'keywords' => 'Kiosk admin, dashboard, payments, reports, bookings, emergency',
    'robots' => 'noindex, nofollow',
    'themeColor' => '#0f1214',
    ])

    <link rel="icon" href="{{ asset(config('kiosk.assets.favicon', 'favicon.ico')) }}">

    {{-- Dashrock template CSS --}}
    <link href="{{ asset('admin-assets/plugins/vectormap/jquery-jvectormap-2.0.2.css') }}" rel="stylesheet"/>
    <link href="{{ asset('admin-assets/plugins/simplebar/css/simplebar.css') }}" rel="stylesheet"/>
    <link href="{{ asset('admin-assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css') }}" rel="stylesheet"/>
    <link href="{{ asset('admin-assets/plugins/metismenu/css/metisMenu.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('admin-assets/css/pace.min.css') }}" rel="stylesheet"/>
    <script src="{{ asset('admin-assets/js/pace.min.js') }}"></script>
    <link href="{{ asset('admin-assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('admin-assets/css/bootstrap-extended.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link href="{{ asset('admin-assets/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('admin-assets/css/icons.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('admin-assets/css/semi-dark.css') }}"/>
    <link rel="stylesheet" href="{{ asset('admin-assets/css/header-colors.css') }}"/>
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @stack('styles')

    @php
    $adminUser = auth()->user();
    $adminName = $adminUser?->name ?: 'Admin Operator';
    $adminInitial = strtoupper(substr($adminName, 0, 1));
    $adminRole = $adminUser?->roles?->pluck('name')->first() ?: 'Operations Desk';
    $adminAvatar = $adminUser?->profilePhotoUrl() ?? asset(config('kiosk.assets.meta_image'));
    $adminNav = [
        ['route' => 'admin.dashboard',          'pattern' => 'admin.dashboard',          'label' => 'Dashboard',       'icon' => 'bx bx-home-alt'],
        ['route' => 'admin.orders.index',        'pattern' => 'admin.orders.*',           'label' => 'Orders',          'icon' => 'bx bx-cart-check'],
        ['route' => 'admin.payments.index',      'pattern' => 'admin.payments.*',         'label' => 'Transactions',    'icon' => 'bx bx-credit-card'],
        ['route' => 'admin.bookings.index',      'pattern' => 'admin.bookings.*',         'label' => 'Bookings',        'icon' => 'bx bx-calendar-check'],
        ['route' => 'admin.services.index',      'pattern' => 'admin.services.*',         'label' => 'Services',        'icon' => 'bx bx-wrench'],
        ['route' => 'admin.consultancy.index',   'pattern' => 'admin.consultancy.*',      'label' => 'Consultancy',     'icon' => 'bx bx-briefcase'],
        ['route' => 'admin.emergency.index',     'pattern' => 'admin.emergency.*',        'label' => 'Emergency Desk',  'icon' => 'bx bx-error-circle'],
        ['route' => 'admin.reports.index',       'pattern' => 'admin.reports.*',          'label' => 'Reports',         'icon' => 'bx bx-line-chart'],
        ['route' => 'admin.users.index',         'pattern' => 'admin.users.*',            'label' => 'KYC & Users',     'icon' => 'bx bx-shield-quarter'],
        ['route' => 'admin.categories.index',    'pattern' => 'admin.categories.*',       'label' => 'Categories',      'icon' => 'bx bx-category'],
        ['route' => 'admin.products.index',      'pattern' => 'admin.products.*',         'label' => 'Products',        'icon' => 'bx bx-box'],
        ['route' => 'admin.marketplaces.index',  'pattern' => 'admin.marketplaces.*',     'label' => 'Marketplaces',    'icon' => 'bx bx-store'],
        ['route' => 'admin.activity-logs.index', 'pattern' => 'admin.activity-logs.*',   'label' => 'Activity Logs',   'icon' => 'bx bx-history'],
    ];
    $todayLabel = now()->format('D, d M Y');
    @endphp
</head>

<body>
    <!--wrapper-->
    <div class="wrapper">

        <!--sidebar wrapper-->
        <div class="sidebar-wrapper" data-simplebar="true">
            <div class="sidebar-header">
                <div>
                    <span class="logo-icon d-flex align-items-center justify-content-center"
                          style="width:36px;height:36px;background:linear-gradient(135deg,#f5a623,#d4881e);border-radius:10px;font-weight:900;color:#1a0e00;font-size:1.05rem;">K</span>
                </div>
                <div>
                    <h4 class="logo-text">Kiosk Admin</h4>
                </div>
                <div class="toggle-icon ms-auto"><i class='bx bx-arrow-back'></i></div>
            </div>

            <!--navigation-->
            <ul class="metismenu" id="menu">
                @foreach($adminNav as $item)
                <li class="{{ request()->routeIs($item['pattern']) ? 'mm-active' : '' }}">
                    <a href="{{ route($item['route']) }}"
                       class="{{ request()->routeIs($item['pattern']) ? 'active' : '' }}">
                        <div class="parent-icon"><i class="{{ $item['icon'] }}"></i></div>
                        <div class="menu-title">{{ $item['label'] }}</div>
                    </a>
                </li>
                @endforeach

                <!-- Applications -->
                <li class="menu-label mt-2">Applications</li>
                <li class="{{ request()->routeIs('admin.orders.*') ? 'mm-active' : '' }}">
                    <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                        <div class="parent-icon"><i class='bx bx-cart-check'></i></div>
                        <div class="menu-title">Orders</div>
                    </a>
                </li>
                <li class="{{ request()->routeIs('admin.payments.*') ? 'mm-active' : '' }}">
                    <a href="{{ route('admin.payments.index') }}" class="{{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                        <div class="parent-icon"><i class='bx bx-credit-card'></i></div>
                        <div class="menu-title">Transactions</div>
                    </a>
                </li>
                <li class="{{ request()->routeIs('admin.bookings.*') ? 'mm-active' : '' }}">
                    <a href="{{ route('admin.bookings.index') }}" class="{{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">
                        <div class="parent-icon"><i class='bx bx-calendar-check'></i></div>
                        <div class="menu-title">Bookings</div>
                    </a>
                </li>
                <li class="{{ request()->routeIs('admin.services.*') ? 'mm-active' : '' }}">
                    <a href="{{ route('admin.services.index') }}" class="{{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
                        <div class="parent-icon"><i class='bx bx-wrench'></i></div>
                        <div class="menu-title">Service Requests</div>
                    </a>
                </li>
                <li class="{{ request()->routeIs('admin.consultancy.*') ? 'mm-active' : '' }}">
                    <a href="{{ route('admin.consultancy.index') }}" class="{{ request()->routeIs('admin.consultancy.*') ? 'active' : '' }}">
                        <div class="parent-icon"><i class='bx bx-briefcase'></i></div>
                        <div class="menu-title">Consultancy</div>
                    </a>
                </li>
                <li class="{{ request()->routeIs('admin.emergency.*') ? 'mm-active' : '' }}">
                    <a href="{{ route('admin.emergency.index') }}" class="{{ request()->routeIs('admin.emergency.*') ? 'active' : '' }}">
                        <div class="parent-icon"><i class='bx bx-error-circle'></i></div>
                        <div class="menu-title">Emergency Desk</div>
                    </a>
                </li>
                <li class="{{ request()->routeIs('admin.reports.*') ? 'mm-active' : '' }}">
                    <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                        <div class="parent-icon"><i class='bx bx-line-chart'></i></div>
                        <div class="menu-title">Reports</div>
                    </a>
                </li>
                <li class="{{ request()->routeIs('admin.users.*') ? 'mm-active' : '' }}">
                    <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <div class="parent-icon"><i class='bx bx-shield-quarter'></i></div>
                        <div class="menu-title">KYC &amp; Users</div>
                    </a>
                </li>
                <li class="{{ request()->routeIs('admin.products.*') ? 'mm-active' : '' }}">
                    <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                        <div class="parent-icon"><i class='bx bx-box'></i></div>
                        <div class="menu-title">Products</div>
                    </a>
                </li>

                <li class="menu-label mt-2">Account</li>
                <li>
                    <a href="{{ route('admin.profile.edit') }}"
                       class="{{ request()->routeIs('admin.profile.*') ? 'active' : '' }}">
                        <div class="parent-icon"><i class='bx bx-user-circle'></i></div>
                        <div class="menu-title">My Profile</div>
                    </a>
                </li>
                <li>
                    <a href="{{ route('home') }}">
                        <div class="parent-icon"><i class='bx bx-link-external'></i></div>
                        <div class="menu-title">View Storefront</div>
                    </a>
                </li>

                <!-- UI Elements -->
                <li class="menu-label">UI Elements</li>
                <li>
                    <a href="{{ route('admin.demo', 'widgets') }}">
                        <div class="parent-icon"><i class='bx bx-cookie'></i></div>
                        <div class="menu-title">Widgets</div>
                    </a>
                </li>
                <li>
                    <a href="javascript:;" class="has-arrow">
                        <div class="parent-icon"><i class='bx bx-cart'></i></div>
                        <div class="menu-title">eCommerce</div>
                    </a>
                    <ul>
                        <li class="{{ request()->routeIs('admin.products.index') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.products.index') }}"
                               class="{{ request()->routeIs('admin.products.index') ? 'active' : '' }}">
                                <i class='bx bx-radio-circle'></i>Products
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.products.show', 'admin.products.edit') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.products.index') }}"
                               class="{{ request()->routeIs('admin.products.show', 'admin.products.edit') ? 'active' : '' }}">
                                <i class='bx bx-radio-circle'></i>Product Details
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.products.create') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.products.create') }}"
                               class="{{ request()->routeIs('admin.products.create') ? 'active' : '' }}">
                                <i class='bx bx-radio-circle'></i>Add New Product
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.categories.index') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.categories.index') }}"
                               class="{{ request()->routeIs('admin.categories.index') ? 'active' : '' }}">
                                <i class='bx bx-radio-circle'></i>Categories
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.orders.*') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.orders.index') }}"
                               class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                                <i class='bx bx-radio-circle'></i>Orders
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.payments.*') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.payments.index') }}"
                               class="{{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                                <i class='bx bx-radio-circle'></i>Transactions
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.marketplaces.*') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.marketplaces.index') }}"
                               class="{{ request()->routeIs('admin.marketplaces.*') ? 'active' : '' }}">
                                <i class='bx bx-radio-circle'></i>Marketplaces
                            </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="javascript:;">
                        <div class="parent-icon"><i class='bx bx-bookmark-heart'></i></div>
                        <div class="menu-title">Components</div>
                    </a>
                    <ul>
                        <li> <a href="{{ route('admin.demo', 'component-alerts') }}"><i class='bx bx-radio-circle'></i>Alerts</a></li>
                        <li> <a href="{{ route('admin.demo', 'component-accordions') }}"><i class='bx bx-radio-circle'></i>Accordions</a></li>
                        <li> <a href="{{ route('admin.demo', 'component-badges') }}"><i class='bx bx-radio-circle'></i>Badges</a></li>
                        <li> <a href="{{ route('admin.demo', 'component-buttons') }}"><i class='bx bx-radio-circle'></i>Buttons</a></li>
                        <li> <a href="{{ route('admin.demo', 'component-cards') }}"><i class='bx bx-radio-circle'></i>Cards</a></li>
                        <li> <a href="{{ route('admin.demo', 'component-carousels') }}"><i class='bx bx-radio-circle'></i>Carousels</a></li>
                        <li> <a href="{{ route('admin.demo', 'component-list-groups') }}"><i class='bx bx-radio-circle'></i>List Groups</a></li>
                        <li> <a href="{{ route('admin.demo', 'component-media-object') }}"><i class='bx bx-radio-circle'></i>Media Objects</a></li>
                        <li> <a href="{{ route('admin.demo', 'component-modals') }}"><i class='bx bx-radio-circle'></i>Modals</a></li>
                        <li> <a href="{{ route('admin.demo', 'component-navs-tabs') }}"><i class='bx bx-radio-circle'></i>Navs & Tabs</a></li>
                        <li> <a href="{{ route('admin.demo', 'component-navbar') }}"><i class='bx bx-radio-circle'></i>Navbar</a></li>
                        <li> <a href="{{ route('admin.demo', 'component-paginations') }}"><i class='bx bx-radio-circle'></i>Pagination</a></li>
                        <li> <a href="{{ route('admin.demo', 'component-popovers-tooltips') }}"><i class='bx bx-radio-circle'></i>Popovers & Tooltips</a></li>
                        <li> <a href="{{ route('admin.demo', 'component-progress-bars') }}"><i class='bx bx-radio-circle'></i>Progress</a></li>
                        <li> <a href="{{ route('admin.demo', 'component-spinners') }}"><i class='bx bx-radio-circle'></i>Spinners</a></li>
                        <li> <a href="{{ route('admin.demo', 'component-notifications') }}"><i class='bx bx-radio-circle'></i>Notifications</a></li>
                        <li> <a href="{{ route('admin.demo', 'component-avtars-chips') }}"><i class='bx bx-radio-circle'></i>Avatars & Chips</a></li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="javascript:;">
                        <div class="parent-icon"><i class="bx bx-repeat"></i></div>
                        <div class="menu-title">Content</div>
                    </a>
                    <ul>
                        <li> <a href="{{ route('admin.demo', 'content-grid-system') }}"><i class='bx bx-radio-circle'></i>Grid System</a></li>
                        <li> <a href="{{ route('admin.demo', 'content-typography') }}"><i class='bx bx-radio-circle'></i>Typography</a></li>
                        <li> <a href="{{ route('admin.demo', 'content-text-utilities') }}"><i class='bx bx-radio-circle'></i>Text Utilities</a></li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="javascript:;">
                        <div class="parent-icon"><i class="bx bx-donate-blood"></i></div>
                        <div class="menu-title">Icons</div>
                    </a>
                    <ul>
                        <li> <a href="{{ route('admin.demo', 'icons-line-icons') }}"><i class='bx bx-radio-circle'></i>Line Icons</a></li>
                        <li> <a href="{{ route('admin.demo', 'icons-boxicons') }}"><i class='bx bx-radio-circle'></i>Boxicons</a></li>
                        <li> <a href="{{ route('admin.demo', 'icons-feather-icons') }}"><i class='bx bx-radio-circle'></i>Feather Icons</a></li>
                    </ul>
                </li>
                <li>
                    <a href="{{ route('admin.demo', 'form-froala-editor') }}">
                        <div class="parent-icon"><i class='bx bx-code-alt'></i></div>
                        <div class="menu-title">Froala Editor</div>
                    </a>
                </li>

                <!-- Format & Tables -->
                <li class="menu-label">Format & Tables</li>
                <li>
                    <a class="has-arrow" href="javascript:;">
                        <div class="parent-icon"><i class='bx bx-message-square-edit'></i></div>
                        <div class="menu-title">Forms</div>
                    </a>
                    <ul>
                        <li> <a href="{{ route('admin.demo', 'form-elements') }}"><i class='bx bx-radio-circle'></i>Form Elements</a></li>
                        <li> <a href="{{ route('admin.demo', 'form-input-group') }}"><i class='bx bx-radio-circle'></i>Input Groups</a></li>
                        <li> <a href="{{ route('admin.demo', 'form-radios-and-checkboxes') }}"><i class='bx bx-radio-circle'></i>Radios & Checkboxes</a></li>
                        <li> <a href="{{ route('admin.demo', 'form-layouts') }}"><i class='bx bx-radio-circle'></i>Forms Layouts</a></li>
                        <li> <a href="{{ route('admin.demo', 'form-validations') }}"><i class='bx bx-radio-circle'></i>Form Validation</a></li>
                        <li> <a href="{{ route('admin.demo', 'form-wizard') }}"><i class='bx bx-radio-circle'></i>Form Wizard</a></li>
                        <li> <a href="{{ route('admin.demo', 'form-text-editor') }}"><i class='bx bx-radio-circle'></i>Text Editor</a></li>
                        <li> <a href="{{ route('admin.demo', 'form-file-upload') }}"><i class='bx bx-radio-circle'></i>File Upload</a></li>
                        <li> <a href="{{ route('admin.demo', 'form-date-time-pickes') }}"><i class='bx bx-radio-circle'></i>Date Pickers</a></li>
                        <li> <a href="{{ route('admin.demo', 'form-select2') }}"><i class='bx bx-radio-circle'></i>Select2</a></li>
                        <li> <a href="{{ route('admin.demo', 'form-repeater') }}"><i class='bx bx-radio-circle'></i>Form Repeater</a></li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="javascript:;">
                        <div class="parent-icon"><i class="bx bx-grid-alt"></i></div>
                        <div class="menu-title">Tables</div>
                    </a>
                    <ul>
                        <li> <a href="{{ route('admin.demo', 'table-basic-table') }}"><i class='bx bx-radio-circle'></i>Basic Table</a></li>
                        <li> <a href="{{ route('admin.demo', 'table-datatable') }}"><i class='bx bx-radio-circle'></i>Data Table</a></li>
                    </ul>
                </li>

                <!-- Pages -->
                <li class="menu-label">Pages</li>
                <li>
                    <a class="has-arrow" href="javascript:;">
                        <div class="parent-icon"><i class="bx bx-lock"></i></div>
                        <div class="menu-title">Authentication</div>
                    </a>
                    <ul>
                        <li>
                            <a href="{{ route('login') }}" target="_blank">
                                <i class='bx bx-radio-circle'></i>Sign In
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('register') }}" target="_blank">
                                <i class='bx bx-radio-circle'></i>Sign Up
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('password.request') }}" target="_blank">
                                <i class='bx bx-radio-circle'></i>Forgot Password
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="{{ request()->routeIs('admin.profile.*') ? 'mm-active' : '' }}">
                    <a href="{{ route('admin.profile.edit') }}"
                       class="{{ request()->routeIs('admin.profile.*') ? 'active' : '' }}">
                        <div class="parent-icon"><i class="bx bx-user-circle"></i></div>
                        <div class="menu-title">My Profile</div>
                    </a>
                </li>
                <li class="{{ request()->routeIs('admin.users.*') ? 'mm-active' : '' }}">
                    <a href="{{ route('admin.users.index') }}"
                       class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <div class="parent-icon"><i class="bx bx-group"></i></div>
                        <div class="menu-title">Users</div>
                    </a>
                </li>
                <li class="{{ request()->routeIs('admin.reports.*') ? 'mm-active' : '' }}">
                    <a href="{{ route('admin.reports.index') }}"
                       class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                        <div class="parent-icon"><i class="bx bx-bar-chart-alt-2"></i></div>
                        <div class="menu-title">Reports</div>
                    </a>
                </li>

                <!-- Charts & Maps -->
                <li class="menu-label">Charts & Maps</li>
                <li>
                    <a class="has-arrow" href="javascript:;">
                        <div class="parent-icon"><i class="bx bx-line-chart"></i></div>
                        <div class="menu-title">Charts</div>
                    </a>
                    <ul>
                        <li> <a href="{{ route('admin.demo', 'charts-apex-chart') }}"><i class='bx bx-radio-circle'></i>Apex</a></li>
                        <li> <a href="{{ route('admin.demo', 'charts-chartjs') }}"><i class='bx bx-radio-circle'></i>Chartjs</a></li>
                        <li> <a href="{{ route('admin.demo', 'charts-highcharts') }}"><i class='bx bx-radio-circle'></i>Highcharts</a></li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="javascript:;">
                        <div class="parent-icon"><i class="bx bx-map-alt"></i></div>
                        <div class="menu-title">Maps</div>
                    </a>
                    <ul>
                        <li> <a href="{{ route('admin.demo', 'map-google-maps') }}"><i class='bx bx-radio-circle'></i>Google Maps</a></li>
                        <li> <a href="{{ route('admin.demo', 'map-vector-maps') }}"><i class='bx bx-radio-circle'></i>Vector Maps</a></li>
                    </ul>
                </li>

                <!-- Others -->
                <li class="menu-label">Others</li>
                <li class="{{ request()->routeIs('admin.bookings.*') ? 'mm-active' : '' }}">
                    <a href="{{ route('admin.bookings.index') }}"
                       class="{{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">
                        <div class="parent-icon"><i class="bx bx-calendar-check"></i></div>
                        <div class="menu-title">Bookings</div>
                    </a>
                </li>
                <li class="{{ request()->routeIs('admin.consultancy.*') ? 'mm-active' : '' }}">
                    <a href="{{ route('admin.consultancy.index') }}"
                       class="{{ request()->routeIs('admin.consultancy.*') ? 'active' : '' }}">
                        <div class="parent-icon"><i class="bx bx-conversation"></i></div>
                        <div class="menu-title">Consultancy</div>
                    </a>
                </li>
                <li class="{{ request()->routeIs('admin.emergency.*') ? 'mm-active' : '' }}">
                    <a href="{{ route('admin.emergency.index') }}"
                       class="{{ request()->routeIs('admin.emergency.*') ? 'active' : '' }}">
                        <div class="parent-icon"><i class="bx bx-alarm-exclamation"></i></div>
                        <div class="menu-title">Emergency</div>
                    </a>
                </li>
                <li class="{{ request()->routeIs('admin.services.*') ? 'mm-active' : '' }}">
                    <a href="{{ route('admin.services.index') }}"
                       class="{{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
                        <div class="parent-icon"><i class="bx bx-wrench"></i></div>
                        <div class="menu-title">Service Requests</div>
                    </a>
                </li>
                <li class="{{ request()->routeIs('admin.activity-logs.*') ? 'mm-active' : '' }}">
                    <a href="{{ route('admin.activity-logs.index') }}"
                       class="{{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}">
                        <div class="parent-icon"><i class="bx bx-list-check"></i></div>
                        <div class="menu-title">Activity Logs</div>
                    </a>
                </li>
            </ul>
            <!--end navigation-->
        </div>
        <!--end sidebar wrapper-->

        <!--start header-->
        <header>
            <div class="topbar d-flex align-items-center">
                <nav class="navbar navbar-expand gap-3">
                    <div class="mobile-toggle-menu"><i class='bx bx-menu'></i></div>

                    {{-- Page title (shown on mobile/collapsed) --}}
                    <div class="d-none d-lg-block">
                        <h6 class="mb-0 fw-bold" style="color:inherit;">@yield('page_title', 'Admin Dashboard')</h6>
                        <small class="text-muted">@yield('page_subtitle', 'Manage Kiosk operations')</small>
                    </div>

                    {{-- Search trigger --}}
                    <div class="search-bar d-lg-block d-none">
                        <form action="{{ route('admin.search') }}" method="GET" class="d-flex align-items-center">
                            <i class='bx bx-search me-2'></i>
                            <input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="Search orders, users, transactions…"
                                   class="border-0 bg-transparent outline-0"
                                   style="min-width:220px;outline:none;">
                        </form>
                    </div>

                    <div class="top-menu ms-auto">
                        <ul class="navbar-nav align-items-center gap-1">

                            {{-- Date pill --}}
                            <li class="nav-item d-none d-md-flex">
                                <span class="nav-link px-2" style="font-size:.83rem;font-weight:600;opacity:.7;">
                                    <i class='bx bx-calendar me-1'></i>{{ $todayLabel }}
                                </span>
                            </li>

                            {{-- Apps grid dropdown --}}
                            <li class="nav-item dropdown dropdown-app">
                                <a class="nav-link dropdown-toggle dropdown-toggle-nocaret"
                                   href="#" data-bs-toggle="dropdown">
                                    <i class='bx bx-grid-alt'></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end p-0">
                                    <div class="app-container p-2 my-2">
                                        <div class="row gx-0 gy-2 row-cols-3 justify-content-center p-2">
                                            <div class="col">
                                                <a href="javascript:;">
                                                    <div class="app-box text-center">
                                                        <div class="app-icon">
                                                            <img src="{{ asset('admin-assets/images/app/slack.png') }}" width="30" alt="">
                                                        </div>
                                                        <div class="app-name">
                                                            <p class="mb-0 mt-1">Slack</p>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="col">
                                                <a href="javascript:;">
                                                    <div class="app-box text-center">
                                                        <div class="app-icon">
                                                            <img src="{{ asset('admin-assets/images/app/behance.png') }}" width="30" alt="">
                                                        </div>
                                                        <div class="app-name">
                                                            <p class="mb-0 mt-1">Behance</p>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="col">
                                                <a href="javascript:;">
                                                    <div class="app-box text-center">
                                                        <div class="app-icon">
                                                            <img src="{{ asset('admin-assets/images/app/google-drive.png') }}" width="30" alt="">
                                                        </div>
                                                        <div class="app-name">
                                                            <p class="mb-0 mt-1">Dribble</p>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="col">
                                                <a href="javascript:;">
                                                    <div class="app-box text-center">
                                                        <div class="app-icon">
                                                            <img src="{{ asset('admin-assets/images/app/outlook.png') }}" width="30" alt="">
                                                        </div>
                                                        <div class="app-name">
                                                            <p class="mb-0 mt-1">Outlook</p>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="col">
                                                <a href="javascript:;">
                                                    <div class="app-box text-center">
                                                        <div class="app-icon">
                                                            <img src="{{ asset('admin-assets/images/app/github.png') }}" width="30" alt="">
                                                        </div>
                                                        <div class="app-name">
                                                            <p class="mb-0 mt-1">GitHub</p>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="col">
                                                <a href="javascript:;">
                                                    <div class="app-box text-center">
                                                        <div class="app-icon">
                                                            <img src="{{ asset('admin-assets/images/app/stack-overflow.png') }}" width="30" alt="">
                                                        </div>
                                                        <div class="app-name">
                                                            <p class="mb-0 mt-1">Stack</p>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="col">
                                                <a href="javascript:;">
                                                    <div class="app-box text-center">
                                                        <div class="app-icon">
                                                            <img src="{{ asset('admin-assets/images/app/figma.png') }}" width="30" alt="">
                                                        </div>
                                                        <div class="app-name">
                                                            <p class="mb-0 mt-1">Stack</p>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="col">
                                                <a href="javascript:;">
                                                    <div class="app-box text-center">
                                                        <div class="app-icon">
                                                            <img src="{{ asset('admin-assets/images/app/twitter.png') }}" width="30" alt="">
                                                        </div>
                                                        <div class="app-name">
                                                            <p class="mb-0 mt-1">Twitter</p>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="col">
                                                <a href="javascript:;">
                                                    <div class="app-box text-center">
                                                        <div class="app-icon">
                                                            <img src="{{ asset('admin-assets/images/app/google-calendar.png') }}" width="30" alt="">
                                                        </div>
                                                        <div class="app-name">
                                                            <p class="mb-0 mt-1">Calendar</p>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="col">
                                                <a href="javascript:;">
                                                    <div class="app-box text-center">
                                                        <div class="app-icon">
                                                            <img src="{{ asset('admin-assets/images/app/spotify.png') }}" width="30" alt="">
                                                        </div>
                                                        <div class="app-name">
                                                            <p class="mb-0 mt-1">Spotify</p>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="col">
                                                <a href="javascript:;">
                                                    <div class="app-box text-center">
                                                        <div class="app-icon">
                                                            <img src="{{ asset('admin-assets/images/app/google-photos.png') }}" width="30" alt="">
                                                        </div>
                                                        <div class="app-name">
                                                            <p class="mb-0 mt-1">Photos</p>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="col">
                                                <a href="javascript:;">
                                                    <div class="app-box text-center">
                                                        <div class="app-icon">
                                                            <img src="{{ asset('admin-assets/images/app/pinterest.png') }}" width="30" alt="">
                                                        </div>
                                                        <div class="app-name">
                                                            <p class="mb-0 mt-1">Photos</p>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="col">
                                                <a href="javascript:;">
                                                    <div class="app-box text-center">
                                                        <div class="app-icon">
                                                            <img src="{{ asset('admin-assets/images/app/linkedin.png') }}" width="30" alt="">
                                                        </div>
                                                        <div class="app-name">
                                                            <p class="mb-0 mt-1">linkedin</p>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="col">
                                                <a href="javascript:;">
                                                    <div class="app-box text-center">
                                                        <div class="app-icon">
                                                            <img src="{{ asset('admin-assets/images/app/dribble.png') }}" width="30" alt="">
                                                        </div>
                                                        <div class="app-name">
                                                            <p class="mb-0 mt-1">Dribble</p>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="col">
                                                <a href="javascript:;">
                                                    <div class="app-box text-center">
                                                        <div class="app-icon">
                                                            <img src="{{ asset('admin-assets/images/app/youtube.png') }}" width="30" alt="">
                                                        </div>
                                                        <div class="app-name">
                                                            <p class="mb-0 mt-1">YouTube</p>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="col">
                                                <a href="javascript:;">
                                                    <div class="app-box text-center">
                                                        <div class="app-icon">
                                                            <img src="{{ asset('admin-assets/images/app/google.png') }}" width="30" alt="">
                                                        </div>
                                                        <div class="app-name">
                                                            <p class="mb-0 mt-1">News</p>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="col">
                                                <a href="javascript:;">
                                                    <div class="app-box text-center">
                                                        <div class="app-icon">
                                                            <img src="{{ asset('admin-assets/images/app/envato.png') }}" width="30" alt="">
                                                        </div>
                                                        <div class="app-name">
                                                            <p class="mb-0 mt-1">Envato</p>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="col">
                                                <a href="javascript:;">
                                                    <div class="app-box text-center">
                                                        <div class="app-icon">
                                                            <img src="{{ asset('admin-assets/images/app/safari.png') }}" width="30" alt="">
                                                        </div>
                                                        <div class="app-name">
                                                            <p class="mb-0 mt-1">Safari</p>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>

                            {{-- Notifications --}}
                            <li class="nav-item dropdown dropdown-large">
                                <a class="nav-link dropdown-toggle dropdown-toggle-nocaret position-relative"
                                   href="#" data-bs-toggle="dropdown">
                                    <i class='bx bx-bell'></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a href="javascript:;">
                                        <div class="msg-header">
                                            <p class="msg-header-title">Notifications</p>
                                            <p class="msg-header-badge">System</p>
                                        </div>
                                    </a>
                                    <div class="header-notifications-list">
                                        <a class="dropdown-item" href="{{ route('admin.orders.index') }}">
                                            <div class="d-flex align-items-center">
                                                <div class="notify bg-light-warning text-warning"><i class='bx bx-cart'></i></div>
                                                <div class="flex-grow-1">
                                                    <h6 class="msg-name">Pending Orders</h6>
                                                    <p class="msg-info">Review orders awaiting fulfillment</p>
                                                </div>
                                            </div>
                                        </a>
                                        <a class="dropdown-item" href="{{ route('admin.emergency.index') }}">
                                            <div class="d-flex align-items-center">
                                                <div class="notify bg-light-danger text-danger"><i class='bx bx-error'></i></div>
                                                <div class="flex-grow-1">
                                                    <h6 class="msg-name">Emergency Desk</h6>
                                                    <p class="msg-info">Check open emergency requests</p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <a href="{{ route('admin.reports.index') }}">
                                        <div class="text-center msg-footer">
                                            <button class="btn btn-primary w-100">View Reports</button>
                                        </div>
                                    </a>
                                </div>
                            </li>

                        </ul>
                    </div>

                    {{-- User dropdown --}}
                    <div class="user-box dropdown px-3">
                        <a class="d-flex align-items-center nav-link dropdown-toggle gap-3 dropdown-toggle-nocaret"
                           href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="{{ $adminAvatar }}" class="user-img" alt="{{ $adminName }}">
                            <div class="user-info">
                                <p class="user-name mb-0">{{ $adminName }}</p>
                                <p class="designattion mb-0">{{ $adminRole }}</p>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.profile.edit') }}">
                                    <i class="bx bx-user fs-5"></i><span>My Profile</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.dashboard') }}">
                                    <i class="bx bx-home-circle fs-5"></i><span>Dashboard</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center" href="{{ route('home') }}">
                                    <i class="bx bx-link-external fs-5"></i><span>Storefront</span>
                                </a>
                            </li>
                            <li><div class="dropdown-divider mb-0"></div></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item d-flex align-items-center w-100 border-0 bg-transparent">
                                        <i class="bx bx-log-out-circle fs-5"></i><span>Logout</span>
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </header>
        <!--end header-->

        <!--start page wrapper-->
        <div class="page-wrapper">
            <div class="page-content">
                <a href="#main-content" class="visually-hidden-focusable">Skip to main content</a>

                @php
                $paystackReady = filled(config('kiosk.payments.paystack.public_key')) &&
                filled(config('kiosk.payments.paystack.secret_key'));
                @endphp
                @unless($paystackReady)
                <div class="alert alert-warning border-0 radius-10 shadow-sm mb-4" role="alert">
                    <strong>Paystack not configured:</strong>
                    Add your Paystack public and secret keys in <code>.env</code> to enable checkout.
                </div>
                @endunless

                @include('partials.flash')

                <main id="main-content">
                    @yield('content')
                </main>
            </div>
        </div>
        <!--end page wrapper-->

        <!--start overlay-->
        <div class="overlay toggle-icon"></div>
        <!--end overlay-->

        <!--Start Back To Top Button-->
        <a href="javascript:;" class="back-to-top">
            <i class='bx bxs-up-arrow-alt'></i>
        </a>
        <!--End Back To Top Button-->

        <!--start footer-->
        <footer class="page-footer">
            <p class="mb-0">© {{ date('Y') }} <strong>Kiosk</strong> — Admin Panel</p>
        </footer>
        <!--end footer-->
    </div>
    <!--end wrapper-->

    {{-- Dashrock JS --}}
    <script src="{{ asset('admin-assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('admin-assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('admin-assets/plugins/simplebar/js/simplebar.min.js') }}"></script>
    <script src="{{ asset('admin-assets/plugins/metismenu/js/metisMenu.min.js') }}"></script>
    <script src="{{ asset('admin-assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('admin-assets/js/app.js') }}"></script>

    @include('partials.idle-logout')
    @include('partials.preloader-scripts')
    @stack('scripts')
</body>

</html>
