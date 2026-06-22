<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('partials.seo-meta', [
    'title' => config('app.name', 'Kiosk') . ' | Smart Shopping, Services, Consultancy, Booking & Emergency',
    'description' => 'Kiosk brings shopping, service requests, consultancy access, reservations, and emergency support together in one place.',
    'keywords' => 'Kiosk, ecommerce, services, consultancy, booking, emergency support, Nigeria, global sourcing',
    'themeColor' => '#111111',
    ])

    @php
    $favicon = asset(config('kiosk.assets.favicon', 'favicon.ico'));
    $frontendUser = auth()->user();
    $homePath = $frontendUser?->homePath();

    if ($frontendUser) {
    $frontendUser->loadCount(['unreadNotifications', 'wishlistItems']);
    $frontendUser->loadMissing(['cart.items.product']);
    }

    $notificationCount = $frontendUser->unread_notifications_count ?? 0;
    $wishlistCount = $frontendUser->wishlist_items_count ?? 0;
    $headerWishlistItems = $frontendUser
    ? $frontendUser->wishlistItems()->with('product.category')->latest()->take(3)->get()
    : collect();
    $headerCart = $frontendUser?->cart;
    $cartCount = $headerCart?->items->sum('qty') ?? 0;
    $cartSubtotal = $headerCart?->items->sum('subtotal') ?? 0;
    $sitePreferenceUser = $frontendUser;
    @endphp

    <link rel="icon" href="{{ $favicon }}">
    <link rel="apple-touch-icon" href="{{ $favicon }}">
    @include('partials.assets.ui-head', [
    'includeFonts' => true,
    'includeIcomoon' => true,
    'includeBootstrap' => true,
    'includeBootstrapIcons' => true,
    'stylesheets' => [
    'assets/css/bootstrap-select.min.css',
    'assets/css/swiper-bundle.min.css',
    'assets/css/animate.css',
    'assets/css/styles.css',
    ],
    ])
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">

    <style>
    :root {
        --kiosk-type-scale: .93;
        --kiosk-ink: #111111;
        --kiosk-text: #5f5f5f;
        --kiosk-muted: #8b8b8b;
        --kiosk-border: #e8e8e8;
        --kiosk-surface: #ffffff;
        --kiosk-surface-soft: #f8f8f8;
        --kiosk-primary: #111111;
        --kiosk-primary-deep: #000000;
        --kiosk-accent: #dc4646;
        --kiosk-radius: 18px;
    }

    html {
        font-size: calc(16px * var(--kiosk-type-scale));
        scroll-behavior: smooth;
    }

    body {
        background: #fff;
        color: var(--kiosk-text);
        font-family: var(--font-1, "Arial", sans-serif);
        margin: 0;
        overflow-x: hidden;
    }

    .site-shell {
        min-height: 100vh;
        overflow-x: hidden;
    }

    .site-content {
        min-height: 40vh;
    }

    .skip-link {
        left: -9999px;
        position: absolute;
        top: auto;
        z-index: 1300;
    }

    .skip-link:focus {
        left: 1rem;
        top: 1rem;
    }

    .container-full {
        margin: 0 auto;
        max-width: 1800px;
        padding-left: 16px;
        padding-right: 16px;
        width: 100%;
    }

    .tf-topbar .tf-list {
        align-items: center;
        display: flex;
        flex-wrap: wrap;
        gap: 24px;
    }

    .topbar-meta {
        align-items: center;
        display: flex;
        gap: 20px;
        justify-content: flex-end;
    }

    .topbar-meta-item {
        align-items: center;
        color: #fff;
        display: inline-flex;
        font-size: 16px;
        gap: 8px;
    }

    .topbar-meta-item i {
        font-size: 12px;
    }

    .header-inner_wrap {
        border-bottom: 1px solid rgba(17, 17, 17, .08);
    }

    .header-bottom_wrap {
        border-bottom: 1px solid rgba(17, 17, 17, .08);
    }

    .logo-site.logo-text {
        color: #111;
        font-family: var(--font-2, "Arial", sans-serif);
        font-size: 24px;
        font-weight: 700;
        letter-spacing: -.04em;
    }

    .logo-site.logo-text img {
        height: auto;
        max-width: 112px;
    }

    .form-search-nav.style-2 input[name="search"] {
        min-width: 0;
    }

    .nav-icon-item .label-lite {
        display: none;
    }

    .nav-icon-item.has-text .label-lite {
        display: inline;
    }

    .mini-kiosk-note {
        color: #696e73;
        font-size: 12px;
        line-height: 1.6;
    }

    .header-hover-group {
        position: relative;
    }

    .header-hover-panel {
        background: #fff;
        border: 1px solid rgba(17, 17, 17, .08);
        box-shadow: 0 22px 44px rgba(17, 17, 17, .12);
        opacity: 0;
        padding: 20px;
        pointer-events: none;
        position: absolute;
        right: 0;
        top: calc(100% + 14px);
        transform: translateY(10px);
        transition: opacity .2s ease, transform .2s ease, visibility .2s ease;
        visibility: hidden;
        width: min(360px, calc(100vw - 32px));
        z-index: 1200;
    }

    .header-hover-panel.has-close {
        padding-top: 56px;
    }

    .header-hover-panel.is-open,
    .header-hover-group:focus-within .header-hover-panel {
        opacity: 1;
        pointer-events: auto;
        transform: translateY(0);
        visibility: visible;
    }

    .header-hover-panel::before {
        content: "";
        height: 14px;
        left: 0;
        position: absolute;
        right: 0;
        top: -14px;
    }

    .header-hover-heading {
        color: #111;
        font-size: 15px;
        font-weight: 700;
        margin-bottom: 6px;
    }

    .header-hover-copy {
        color: #696e73;
        font-size: 13px;
        line-height: 1.65;
        margin-bottom: 16px;
    }

    .header-hover-actions {
        display: grid;
        gap: 10px;
    }

    .auth-hover-tabs {
        display: grid;
        gap: 8px;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        margin-bottom: 14px;
    }

    .auth-hover-tab {
        background: #f4f4f4;
        border: 0;
        border-radius: 999px;
        color: #111;
        cursor: pointer;
        font-size: 13px;
        font-weight: 700;
        padding: 10px 14px;
    }

    .auth-hover-tab.is-active {
        background: #111;
        color: #fff;
    }

    .auth-hover-form {
        display: none;
    }

    .auth-hover-form.is-active {
        display: block;
    }

    .auth-hover-field+.auth-hover-field {
        margin-top: 10px;
    }

    .auth-hover-field input {
        background: #fff;
        border: 1px solid rgba(17, 17, 17, .14);
        border-radius: 14px;
        color: #111;
        min-height: 46px;
        padding: 0 14px;
        width: 100%;
    }

    .auth-hover-field input:focus {
        border-color: #111;
        outline: none;
    }

    .auth-hover-row {
        align-items: center;
        display: flex;
        gap: 10px;
        justify-content: space-between;
        margin: 12px 0 14px;
    }

    .auth-hover-check {
        align-items: center;
        color: #696e73;
        display: inline-flex;
        font-size: 12px;
        gap: 8px;
    }

    .auth-hover-check input {
        accent-color: #111;
    }

    .auth-hover-linkline {
        color: #696e73;
        font-size: 12px;
        line-height: 1.5;
        margin-top: 12px;
        text-align: center;
    }

    .auth-hover-linkline a,
    .auth-hover-forgot {
        color: #111;
        font-weight: 600;
        text-decoration: underline;
        text-underline-offset: 3px;
    }

    .auth-hover-forgot {
        font-size: 12px;
    }

    .auth-hover-visibility {
        align-items: center;
        color: #696e73;
        display: inline-flex;
        font-size: 12px;
        gap: 8px;
        margin: 10px 0 0;
    }

    .auth-hover-visibility input {
        accent-color: #111;
    }

    .header-hover-links {
        display: grid;
        gap: 10px;
        margin-top: 12px;
    }

    .header-hover-link {
        align-items: center;
        color: #111;
        display: flex;
        font-size: 14px;
        font-weight: 600;
        justify-content: space-between;
    }

    .header-hover-close {
        position: absolute;
        right: 16px;
        top: 16px;
        z-index: 2;
    }

    .header-hover-link span:last-child {
        color: #8b8b8b;
        font-size: 12px;
        font-weight: 500;
    }

    .icon-close-popup {
        align-items: center;
        background: url("{{ asset('assets/images/cursor-close.svg') }}") center/22px 22px no-repeat;
        border-radius: 999px;
        cursor: pointer;
        display: inline-flex;
        height: 36px;
        justify-content: center;
        transform: rotate(0deg);
        transition: opacity .18s ease;
        width: 36px;
    }

    .icon-close-popup i {
        opacity: 0;
    }

    .icon-close-popup.is-rotating-in {
        animation: kiosk-close-spin-in .28s ease forwards;
    }

    .icon-close-popup.is-rotating-out {
        animation: kiosk-close-spin-out .28s ease forwards;
    }

    @keyframes kiosk-close-spin-in {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(90deg);
        }
    }

    @keyframes kiosk-close-spin-out {
        from {
            transform: rotate(90deg);
        }

        to {
            transform: rotate(0deg);
        }
    }

    .tf-dropdown-static {
        align-items: center;
        display: inline-flex;
        gap: 8px;
        position: relative;
    }

    .tf-dropdown-static img {
        border-radius: 999px;
        height: 16px;
        object-fit: cover;
        width: 16px;
    }

    .topbar-dropdown {
        align-items: center;
        cursor: pointer;
        display: inline-flex;
        gap: 8px;
        position: relative;
    }

    .topbar-dropdown-menu {
        background: #fff;
        border: 1px solid rgba(17, 17, 17, .08);
        box-shadow: 0 16px 32px rgba(17, 17, 17, .12);
        color: #111;
        display: grid;
        gap: 8px;
        min-width: 220px;
        max-height: min(60vh, 420px);
        opacity: 0;
        overflow-x: hidden;
        overflow-y: auto;
        padding: 12px;
        pointer-events: none;
        position: absolute;
        right: 0;
        top: calc(100% + 12px);
        transform: translateY(8px);
        transition: opacity .2s ease, transform .2s ease, visibility .2s ease;
        visibility: hidden;
        z-index: 1200;
    }

    .topbar-dropdown:hover .topbar-dropdown-menu,
    .topbar-dropdown:focus-within .topbar-dropdown-menu {
        opacity: 1;
        pointer-events: auto;
        transform: translateY(0);
        visibility: visible;
    }

    .topbar-dropdown-option {
        align-items: center;
        color: #111;
        display: flex;
        gap: 10px;
        line-height: 1.4;
    }

    .topbar-dropdown-menu::-webkit-scrollbar {
        width: 8px;
    }

    .topbar-dropdown-menu::-webkit-scrollbar-thumb {
        background: rgba(17, 17, 17, .22);
        border-radius: 999px;
    }

    .topbar-dropdown-menu::-webkit-scrollbar-track {
        background: rgba(17, 17, 17, .06);
        border-radius: 999px;
    }

    .topbar-dropdown-current {
        align-items: center;
        display: inline-flex;
        gap: 10px;
    }

    .topbar-dropdown-select {
        background: transparent;
        border: 0;
        color: #111;
        cursor: pointer;
        padding: 0;
        text-align: left;
        width: 100%;
    }

    .topbar-dropdown-select.is-active {
        background: #f7f7f7;
        border-radius: 12px;
        padding: 8px;
    }

    .topbar-dropdown-option img {
        border-radius: 999px;
        flex-shrink: 0;
        height: 18px;
        object-fit: cover;
        width: 18px;
    }

    .topbar-flag-badge {
        align-items: center;
        background: #f3f3f3;
        border-radius: 999px;
        display: inline-flex;
        flex-shrink: 0;
        font-size: 12px;
        font-weight: 700;
        height: 18px;
        justify-content: center;
        width: 18px;
    }

    .topbar-dropdown-option small {
        color: #696e73;
        display: block;
        font-size: 11px;
    }

    .site-module-shell {
        padding-bottom: 4rem;
    }

    .site-module-shell>*:first-child.page-banner {
        margin-top: 0;
    }

    .section-label {
        color: #111;
        display: inline-block;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .12em;
        margin-bottom: 10px;
        text-transform: uppercase;
    }

    .module-link {
        color: #111;
        font-weight: 600;
        text-decoration: underline;
        text-underline-offset: 3px;
    }

    .btn {
        border-radius: 999px;
        font-weight: 600;
    }

    .btn-primary {
        background: #111;
        border-color: #111;
        color: #fff;
    }

    .btn-primary:hover,
    .btn-primary:focus {
        background: #2b2b2b;
        border-color: #2b2b2b;
        color: #fff;
    }

    .btn-outline-primary {
        border-color: #111;
        color: #111;
    }

    .btn-outline-primary:hover,
    .btn-outline-primary:focus {
        background: #111;
        border-color: #111;
        color: #fff;
    }

    .form-control {
        border: 1px solid #d9d9d9;
        border-radius: 16px;
        box-shadow: none;
    }

    .form-control:focus {
        border-color: #111;
        box-shadow: none;
    }

    .footer-amere {
        background: #111;
        color: #bfbfbf;
        margin-top: 0;
        overflow: hidden;
        padding-top: 72px;
    }

    .footer-amere h6,
    .footer-amere .footer-title {
        color: #fff;
    }

    .footer-amere .footer-link-list {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .footer-amere .footer-link-list li+li {
        margin-top: 12px;
    }

    .footer-amere .footer-link-list a {
        color: #bfbfbf;
    }

    .footer-amere .footer-link-list a:hover {
        color: #fff;
    }

    .footer-subscribe {
        border-right: 1px solid rgba(255, 255, 255, .08);
        padding-right: 42px;
    }

    .footer-subscribe-field {
        align-items: center;
        background: #fff;
        border-radius: 999px;
        display: flex;
        margin: 28px 0 18px;
        overflow: hidden;
    }

    .footer-subscribe-field input {
        background: transparent;
        border: 0;
        color: #111;
        flex: 1;
        min-height: 52px;
        padding: 0 22px;
    }

    .footer-subscribe-field button {
        align-items: center;
        background: #111;
        border: 0;
        border-left: 1px solid rgba(17, 17, 17, .08);
        color: #fff;
        display: inline-flex;
        height: 52px;
        justify-content: center;
        width: 52px;
    }

    .footer-subscribe-field button i {
        font-size: 18px;
    }

    .footer-subscribe-status {
        border-radius: 16px;
        font-size: 13px;
        line-height: 1.6;
        margin-top: 14px;
        padding: 12px 14px;
    }

    .footer-subscribe-status.is-success {
        background: rgba(40, 167, 69, .10);
        color: #155724;
    }

    .footer-subscribe-status.is-error {
        background: rgba(220, 53, 69, .10);
        color: #842029;
    }

    .footer-fine {
        color: #9d9d9d;
        font-size: 14px;
    }

    .footer-marquee {
        align-items: center;
        border-top: 1px solid rgba(255, 255, 255, .08);
        border-bottom: 1px solid rgba(255, 255, 255, .08);
        display: flex;
        justify-content: center;
        margin-top: 52px;
        min-height: 132px;
        overflow: hidden;
        padding: 16px 0;
        white-space: nowrap;
    }

    .footer-marquee-track {
        animation: kiosk-marquee 28s linear infinite;
        color: #fff;
        display: inline-block;
        font-family: var(--font-2, "Arial", sans-serif);
        font-size: clamp(52px, 9vw, 128px);
        font-weight: 600;
        line-height: 1;
        letter-spacing: -.04em;
        opacity: .95;
        padding-right: 48px;
        text-align: center;
        transform: translateY(.04em);
        white-space: nowrap;
    }

    @keyframes kiosk-marquee {
        0% {
            transform: translateX(0);
        }

        100% {
            transform: translateX(-50%);
        }
    }

    .footer-bottom-amere {
        padding: 22px 0 28px;
    }

    .footer-bottom-inner {
        align-items: center;
        display: flex;
        gap: 24px;
        justify-content: space-between;
    }

    .footer-bottom-left {
        align-items: center;
        display: flex;
        flex-wrap: wrap;
        gap: 26px;
    }

    .footer-bottom-right {
        align-items: center;
        display: flex;
        flex-wrap: wrap;
        gap: 18px;
        justify-content: flex-end;
    }

    .footer-locale-meta {
        flex-wrap: wrap;
        gap: 12px 20px;
        justify-content: flex-start;
    }

    .footer-locale-meta .topbar-meta-item {
        position: relative;
    }

    .footer-locale-meta .topbar-dropdown {
        background: rgba(255, 255, 255, .08);
        border: 1px solid rgba(255, 255, 255, .12);
        border-radius: 999px;
        min-height: 44px;
        padding: 8px 14px;
        transition: background-color .2s ease, border-color .2s ease;
    }

    .footer-locale-meta .topbar-dropdown:hover,
    .footer-locale-meta .topbar-dropdown:focus-within {
        background: rgba(255, 255, 255, .12);
        border-color: rgba(255, 255, 255, .18);
    }

    .footer-locale-meta .topbar-dropdown i {
        color: rgba(255, 255, 255, .74);
    }

    .footer-locale-meta .topbar-dropdown-menu {
        background: #111111;
        border: 1px solid rgba(255, 255, 255, .1);
        bottom: calc(100% + 12px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, .32);
        left: 0;
        max-height: min(55vh, 380px);
        min-width: 240px;
        right: auto;
        top: auto;
    }

    .footer-locale-meta .topbar-dropdown-current,
    .footer-locale-meta [data-locale-display="language-label"] {
        align-items: center;
        color: #ffffff;
        display: inline-flex;
        min-height: 40px;
    }

    .footer-locale-meta [data-locale-display="country-label"] small,
    .footer-locale-meta .topbar-dropdown-option small {
        color: rgba(255, 255, 255, .62);
    }

    .footer-locale-meta .topbar-dropdown-select,
    .footer-locale-meta .topbar-dropdown-option {
        color: #ffffff;
    }

    .footer-locale-meta .topbar-dropdown-select.is-active {
        background: rgba(255, 255, 255, .08);
    }

    .footer-locale-meta .topbar-dropdown-menu img,
    .footer-locale-meta .topbar-dropdown-current img {
        border: 1px solid rgba(255, 255, 255, .12);
    }

    .footer-bottom-copy {
        color: #8b8b8b;
        margin: 0;
    }

    .footer-social-list {
        align-items: center;
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .footer-social-link {
        align-items: center;
        border: 1px solid rgba(255, 255, 255, .18);
        border-radius: 50%;
        color: #ffffff;
        display: inline-flex;
        height: 38px;
        justify-content: center;
        transition: background-color .2s ease, border-color .2s ease, color .2s ease, transform .2s ease;
        width: 38px;
    }

    .footer-social-link:hover,
    .footer-social-link:focus {
        background: #ffffff;
        border-color: #ffffff;
        color: #111111;
        transform: translateY(-2px);
    }

    .footer-social-link .icon {
        font-size: 18px;
    }

    .payment-list {
        align-items: center;
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .payment-list img {
        display: block;
        height: 24px;
        width: auto;
    }

    .quick-auth-modal .modal-content {
        border-radius: 0;
        padding: 26px 22px 22px;
    }

    .quick-auth-modal .modal-heading {
        margin-bottom: 18px;
    }

    .quick-auth-modal .modal-heading h3 {
        margin-bottom: 8px;
    }

    .mini-cart-offcanvas {
        width: min(420px, 100vw);
    }

    .mini-cart-offcanvas .offcanvas-header {
        border-bottom: 1px solid rgba(17, 17, 17, .08);
        padding: 24px 24px 18px;
    }

    .mini-cart-offcanvas .offcanvas-body {
        display: flex;
        flex-direction: column;
        padding: 0;
    }

    .mini-cart-list {
        flex: 1;
        overflow: auto;
        padding: 18px 24px;
    }

    .mini-cart-item {
        align-items: center;
        display: grid;
        gap: 14px;
        grid-template-columns: 78px minmax(0, 1fr);
    }

    .mini-cart-item+.mini-cart-item {
        border-top: 1px solid rgba(17, 17, 17, .08);
        margin-top: 16px;
        padding-top: 16px;
    }

    .mini-cart-thumb {
        background: #f7f7f7;
        height: 92px;
        overflow: hidden;
        width: 78px;
    }

    .mini-cart-thumb img {
        height: 100%;
        object-fit: cover;
        width: 100%;
    }

    .mini-cart-title {
        color: #111;
        display: block;
        font-size: 15px;
        font-weight: 600;
        line-height: 1.45;
        margin-bottom: 6px;
    }

    .mini-cart-meta {
        color: #696e73;
        font-size: 13px;
        margin-bottom: 6px;
    }

    .mini-cart-price {
        color: #111;
        font-weight: 700;
    }

    .mini-cart-footer {
        border-top: 1px solid rgba(17, 17, 17, .08);
        padding: 20px 24px 24px;
    }

    .mini-cart-total {
        align-items: center;
        color: #111;
        display: flex;
        font-weight: 700;
        justify-content: space-between;
        margin-bottom: 18px;
    }

    .mini-cart-actions {
        display: grid;
        gap: 12px;
    }

    .mini-cart-empty {
        color: #696e73;
        padding: 28px 24px 8px;
    }

    .mini-cart-empty.is-compact {
        padding: 0;
    }

    .mini-cart-preview {
        display: grid;
        gap: 14px;
        margin-bottom: 16px;
        max-height: 300px;
        overflow: auto;
        padding-right: 4px;
    }

    .mini-cart-preview .mini-cart-item+.mini-cart-item {
        margin-top: 0;
        padding-top: 14px;
    }

    .mini-cart-preview-footer {
        border-top: 1px solid rgba(17, 17, 17, .08);
        display: grid;
        gap: 14px;
        padding-top: 16px;
    }

    .mobile-utility-links {
        display: grid;
        gap: 14px;
        margin-top: 18px;
    }

    .mobile-utility-links a {
        color: #111;
        font-weight: 600;
    }

    .mobile-auth-links {
        display: grid;
        gap: 12px;
        margin-top: 22px;
    }

    .need-help-wrap {
        position: relative;
        z-index: 2;
    }

    .need-help-wrap a,
    .need-help-wrap .tf-btn,
    .need-help-wrap .link {
        pointer-events: auto;
        position: relative;
        z-index: 2;
    }

    .cookie-banner {
        background: rgba(17, 17, 17, .96);
        border-top: 1px solid rgba(255, 255, 255, .08);
        bottom: 0;
        color: #f4f4f4;
        left: 0;
        opacity: 0;
        pointer-events: none;
        position: fixed;
        right: 0;
        transform: translateY(100%);
        transition: transform .25s ease, opacity .25s ease;
        z-index: 1400;
    }

    .cookie-banner.is-visible {
        opacity: 1;
        pointer-events: auto;
        transform: translateY(0);
    }

    .cookie-banner-inner {
        align-items: center;
        display: grid;
        gap: 16px;
        grid-template-columns: minmax(0, 1fr) auto;
        margin: 0 auto;
        max-width: 1280px;
        padding: 18px 20px;
    }

    .cookie-banner-copy {
        font-size: 14px;
        line-height: 1.7;
    }

    .cookie-banner-copy strong {
        color: #fff;
        display: block;
        font-size: 15px;
        margin-bottom: 4px;
    }

    .cookie-banner-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        justify-content: flex-end;
    }

    .cookie-banner .btn {
        min-width: 120px;
    }

    @media (max-width: 1199.98px) {
        .header-inner {
            grid-template-columns: auto 1fr auto;
        }

        .header-hover-panel {
            display: none;
        }
    }

    @media (max-width: 991.98px) {
        body {
            padding-bottom: 0;
        }

        .site-module-shell {
            padding-bottom: 2.5rem;
        }

        .footer-subscribe {
            border-right: 0;
            padding-right: 0;
        }

        .footer-bottom-inner {
            align-items: flex-start;
            flex-direction: column;
        }

        .footer-bottom-left {
            gap: 18px;
        }

        .footer-bottom-right {
            justify-content: flex-start;
        }

        .footer-locale-meta {
            width: 100%;
        }

        .cookie-banner-inner {
            grid-template-columns: 1fr;
        }

        .cookie-banner-actions {
            justify-content: flex-start;
        }
    }

    @media (max-width: 767.98px) {
        .tf-topbar {
            display: none;
        }

        .site-module-shell {
            padding-bottom: 2rem;
        }

        .logo-site.logo-text img {
            max-width: 106px;
        }

        .canvas-mb .mb-body {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .mobile-utility-links {
            padding-left: .15rem;
            padding-right: .15rem;
        }

        .mobile-utility-links a {
            padding-left: .25rem;
            padding-right: .25rem;
        }

        .nav-icon-list {
            gap: 14px;
        }

        .nav-icon-list .nav-icon-item .icon {
            font-size: 20px;
        }

        .footer-amere {
            padding-top: 56px;
        }

        .footer-marquee-track {
            font-size: 48px;
        }

        .footer-marquee {
            min-height: 104px;
        }
    }

    @media (max-width: 575.98px) {
        .site-module-shell {
            padding-bottom: 1.5rem;
        }

        .canvas-mb .mb-body {
            padding-left: .85rem;
            padding-right: .85rem;
        }
    }
    </style>

    @yield('structured_data')
    @stack('styles')
</head>

<body>
    @include('partials.preloader')
    <div class="site-shell">
        <a href="#main-content" class="skip-link btn btn-primary">Skip to main content</a>

        <main id="wrapper">
            <div class="tf-topbar topbar-s2 d-none d-md-flex bg-dark">
                <div class="container">
                    <div class="row">
                        <div class="col-6 col-xxl-4">
                            <div class="tf-list">
                                <a href="tel:+2347035627734" class="text-white link">(+234) 703 562 7734</a>
                                <a href="{{ route('shop.index') }}"
                                    class="text-decoration-underline text-white link">Our Store</a>
                                <a href="mailto:info@mirrorageconcepts.com" class="text-white link">Contact</a>
                            </div>
                        </div>
                        <div class="col-4 d-none d-xxl-block">
                            <div class="text-center">
                                <p class="text-white mb-0">Shop, request services, and manage bookings in one place.</p>
                            </div>
                        </div>
                        <div class="col-6 col-xxl-4">
                            <div class="topbar-meta">
                                <span class="topbar-meta-item">
                                    <span class="tf-dropdown-static topbar-dropdown" tabindex="0" role="button"
                                        aria-haspopup="true" data-locale-dropdown="country">
                                        <span class="topbar-dropdown-current">
                                            <span class="topbar-flag-badge" aria-hidden="true">NG</span>
                                            <span data-locale-display="country-label">
                                                Nigeria
                                                <small data-locale-display="country-meta">Detecting your
                                                    location...</small>
                                            </span>
                                        </span>
                                        <i class="icon icon-CaretDown"></i>
                                        <span class="topbar-dropdown-menu" data-country-menu>
                                            <span class="topbar-dropdown-option">
                                                <span class="topbar-flag-badge" aria-hidden="true">...</span>
                                                <span>Loading countries<small>Preparing storefront
                                                        regions</small></span>
                                            </span>
                                        </span>
                                    </span>
                                </span>
                                <span class="topbar-meta-item">
                                    <span class="tf-dropdown-static topbar-dropdown" tabindex="0" role="button"
                                        aria-haspopup="true" data-locale-dropdown="language">
                                        <span data-locale-display="language-label">Yoruba</span>
                                        <i class="icon icon-CaretDown"></i>
                                        <span class="topbar-dropdown-menu">
                                            <button type="button"
                                                class="topbar-dropdown-select topbar-dropdown-option is-active"
                                                data-locale-option="language" data-value="yoruba" data-label="Yoruba"
                                                data-country="ng">
                                                <span>
                                                    Yoruba
                                                    <small>South West Nigeria</small>
                                                </span>
                                            </button>
                                            <button type="button" class="topbar-dropdown-select topbar-dropdown-option"
                                                data-locale-option="language" data-value="igbo" data-label="Igbo"
                                                data-country="ng">
                                                <span>
                                                    Igbo
                                                    <small>South East Nigeria</small>
                                                </span>
                                            </button>
                                            <button type="button" class="topbar-dropdown-select topbar-dropdown-option"
                                                data-locale-option="language" data-value="hausa" data-label="Hausa"
                                                data-country="ng">
                                                <span>
                                                    Hausa
                                                    <small>North and West Africa</small>
                                                </span>
                                            </button>
                                            <button type="button" class="topbar-dropdown-select topbar-dropdown-option"
                                                data-locale-option="language" data-value="english" data-label="English"
                                                data-country="nigeria">
                                                <span>
                                                    English
                                                    <small>National and support default</small>
                                                </span>
                                            </button>
                                        </span>
                                    </span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <header class="tf-header header-s4 scr-box-shadow">
                <div class="header-inner_wrap">
                    <div class="container">
                        <div class="header-inner">
                            <div class="box-open-menu-mobile d-xl-none">
                                <a href="#mobileMenu" data-bs-toggle="offcanvas" class="btn-open-menu">
                                    <i class="icon icon-List"></i>
                                </a>
                            </div>
                            <div class="header-left d-none d-xl-block">
                                <form action="{{ route('shop.index') }}" class="form-search-nav style-2">
                                    <fieldset>
                                        <input type="text" name="search" value="{{ request('search') }}"
                                            placeholder="Search Products" aria-label="Search products">
                                    </fieldset>
                                    <button type="submit" class="btn-action">
                                        <i class="icon icon-MagnifyingGlass"></i>
                                    </button>
                                </form>
                            </div>
                            <div class="header-center">
                                <a href="{{ route('home') }}" class="logo-site logo-text">
                                    <img loading="lazy" width="112" height="22"
                                        src="{{ asset('assets/images/logo/logo.svg') }}" alt="Kiosk">
                                </a>
                            </div>
                            <div class="header-right">
                                <ul class="nav-icon-list">
                                    <li class="d-none d-sm-block d-xl-none">
                                        <a href="{{ route('shop.index') }}" class="nav-icon-item link">
                                            <i class="icon icon-MagnifyingGlass"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="header-hover-group" data-hover-panel>
                                            @guest
                                            <a href="#sign" data-bs-toggle="modal" class="nav-icon-item link"
                                                aria-label="Login or register" data-hover-trigger>
                                                <i class="icon icon-User"></i>
                                            </a>
                                            <div class="header-hover-panel has-close" data-hover-content>
                                                <button type="button" class="header-hover-close icon-close-popup"
                                                    data-hover-close aria-label="Close account access panel">
                                                    <i class="icon icon-X2"></i>
                                                </button>
                                                <div class="header-hover-heading">Account access</div>
                                                <p class="header-hover-copy">Open your cart, bookings, consultancy
                                                    requests, and order history without leaving this screen.</p>
                                                <div class="auth-hover-tabs">
                                                    <button type="button" class="auth-hover-tab is-active"
                                                        data-auth-tab="login">Login</button>
                                                    <button type="button" class="auth-hover-tab"
                                                        data-auth-tab="register">Sign up</button>
                                                </div>

                                                <form method="POST" action="{{ route('login') }}"
                                                    class="auth-hover-form is-active" data-auth-form="login">
                                                    @csrf
                                                    <div class="auth-hover-field">
                                                        <input type="email" name="email" value="{{ old('email') }}"
                                                            placeholder="Email address" required
                                                            autocomplete="username">
                                                    </div>
                                                    <div class="auth-hover-field">
                                                        <input type="password" name="password" placeholder="Password"
                                                            data-password-field required
                                                            autocomplete="current-password">
                                                    </div>
                                                    <label class="auth-hover-visibility">
                                                        <input type="checkbox" data-password-toggle>
                                                        <span>Show password</span>
                                                    </label>
                                                    <div class="auth-hover-row">
                                                        <label class="auth-hover-check">
                                                            <input type="checkbox" name="remember">
                                                            <span>Remember me</span>
                                                        </label>
                                                        @if (Route::has('password.request'))
                                                        <a href="{{ route('password.request') }}"
                                                            class="auth-hover-forgot">Forgot password?</a>
                                                        @endif
                                                    </div>
                                                    <div class="header-hover-actions">
                                                        <button type="submit" class="tf-btn animate-btn w-100">Log
                                                            in</button>
                                                    </div>
                                                </form>

                                                <form method="POST" action="{{ route('register') }}"
                                                    class="auth-hover-form" data-auth-form="register">
                                                    @csrf
                                                    <div class="auth-hover-field">
                                                        <input type="text" name="name" placeholder="Full name" required
                                                            autocomplete="name">
                                                    </div>
                                                    <div class="auth-hover-field">
                                                        <input type="email" name="email" placeholder="Email address"
                                                            required autocomplete="username">
                                                    </div>
                                                    <div class="auth-hover-field">
                                                        <input type="password" name="password" data-password-field
                                                            placeholder="Create password" required
                                                            autocomplete="new-password">
                                                    </div>
                                                    <div class="auth-hover-field">
                                                        <input type="password" name="password_confirmation"
                                                            data-password-field placeholder="Confirm password" required
                                                            autocomplete="new-password">
                                                    </div>
                                                    <label class="auth-hover-visibility">
                                                        <input type="checkbox" data-password-toggle>
                                                        <span>Show passwords</span>
                                                    </label>
                                                    <div class="header-hover-actions" style="margin-top: 14px;">
                                                        <button type="submit" class="tf-btn animate-btn w-100">Create
                                                            account</button>
                                                    </div>
                                                    <p class="auth-hover-linkline">By continuing, you can manage
                                                        shopping, services, reservations, and emergency support from one
                                                        account.</p>
                                                </form>
                                            </div>
                                            @else
                                            <a href="{{ $homePath }}" class="nav-icon-item link" aria-label="My account"
                                                data-hover-trigger>
                                                <i class="icon icon-User"></i>
                                            </a>
                                            <div class="header-hover-panel" data-hover-content>
                                                <div class="header-hover-heading">{{ auth()->user()->name }}</div>
                                                <p class="header-hover-copy">Move quickly between your dashboard,
                                                    profile, orders, and service activity.</p>
                                                <div class="header-hover-links">
                                                    <a href="{{ $homePath }}" class="header-hover-link">
                                                        <span>My account</span>
                                                        <span>Dashboard</span>
                                                    </a>
                                                    <a href="{{ route('profile.edit') }}" class="header-hover-link">
                                                        <span>Profile</span>
                                                        <span>Update details</span>
                                                    </a>
                                                    <a href="{{ route('orders.index') }}" class="header-hover-link">
                                                        <span>Orders</span>
                                                        <span>Track purchases</span>
                                                    </a>
                                                    <a href="{{ route('wishlist.index') }}" class="header-hover-link">
                                                        <span>Wishlist</span>
                                                        <span>{{ $wishlistCount }} saved</span>
                                                    </a>
                                                </div>
                                            </div>
                                            @endguest
                                        </div>
                                    </li>
                                    <li>
                                        <div class="header-hover-group" data-hover-panel>
                                            <a href="{{ auth()->check() ? route('wishlist.index') : route('register') }}"
                                                class="nav-icon-item link position-relative" data-hover-trigger
                                                aria-label="Wishlist">
                                                <i class="icon icon-HeartStraight"></i>
                                                @if($wishlistCount > 0)
                                                <span class="count">{{ $wishlistCount }}</span>
                                                @endif
                                            </a>
                                            <div class="header-hover-panel" data-hover-content>
                                                <div class="header-hover-heading">Wishlist</div>
                                                <p class="header-hover-copy">A quick look at the items you have saved so
                                                    you can jump back in without opening the full page.</p>
                                                @guest
                                                <div class="mini-cart-empty is-compact">
                                                    <p class="mb-3">Login to save products and keep your shortlist
                                                        ready.</p>
                                                    <div class="header-hover-actions">
                                                        <a href="{{ route('login') }}"
                                                            class="tf-btn animate-btn w-100">Login</a>
                                                        <a href="{{ route('register') }}"
                                                            class="tf-btn btn-white w-100">Register</a>
                                                    </div>
                                                </div>
                                                @else
                                                @if($headerWishlistItems->isEmpty())
                                                <div class="mini-cart-empty is-compact">
                                                    <p class="mb-3">Your wishlist is empty for now.</p>
                                                    <a href="{{ route('shop.index') }}"
                                                        class="tf-btn animate-btn w-100">Browse Products</a>
                                                </div>
                                                @else
                                                <div class="mini-cart-preview">
                                                    @foreach($headerWishlistItems as $wishlistItem)
                                                    @php
                                                        $wishlistProduct = $wishlistItem->product;
                                                    @endphp
                                                    @if($wishlistProduct)
                                                    <div class="mini-cart-item">
                                                        <a href="{{ route('shop.show', $wishlistProduct->slug) }}"
                                                            class="mini-cart-thumb">
                                                            <img src="{{ $wishlistProduct->image_url }}"
                                                                alt="{{ $wishlistProduct->name }}">
                                                        </a>
                                                        <div>
                                                            <a href="{{ route('shop.show', $wishlistProduct->slug) }}"
                                                                class="mini-cart-title">
                                                                {{ $wishlistProduct->name }}
                                                            </a>
                                                            <div class="mini-cart-meta">
                                                                {{ $wishlistProduct->category?->name ?: 'Saved item' }}
                                                            </div>
                                                            <div class="mini-cart-price">
                                                                &#8358;{{ number_format($wishlistProduct->current_price, 2) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif
                                                    @endforeach
                                                </div>
                                                <div class="mini-cart-preview-footer">
                                                    <div class="mini-cart-total mb-0">
                                                        <span>Saved items</span>
                                                        <span>{{ $wishlistCount }}</span>
                                                    </div>
                                                    <div class="header-hover-actions">
                                                        <a href="{{ route('wishlist.index') }}"
                                                            class="tf-btn btn-white w-100">Open Wishlist</a>
                                                        <a href="{{ route('shop.index') }}"
                                                            class="tf-btn animate-btn w-100">Keep Shopping</a>
                                                    </div>
                                                </div>
                                                @endif
                                                @endguest
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="header-hover-group" data-hover-panel>
                                            <a href="#shoppingCart" data-bs-toggle="offcanvas"
                                                class="nav-icon-item link shop-cart" data-hover-trigger
                                                aria-label="Shopping cart">
                                                <i class="icon icon-Handbag"></i>
                                                <span class="count">{{ $cartCount }}</span>
                                            </a>
                                            <div class="header-hover-panel has-close" data-hover-content>
                                                <button type="button" class="header-hover-close icon-close-popup"
                                                    data-hover-close aria-label="Close shopping cart panel">
                                                    <i class="icon icon-X2"></i>
                                                </button>
                                                <div class="header-hover-heading">Shopping cart</div>
                                                <p class="header-hover-copy">Kiosk-style quick access to your current
                                                    cart without leaving the page.</p>
                                                @guest
                                                <div class="mini-cart-empty is-compact">
                                                    <p class="mb-3">Login to keep products in your cart and continue to
                                                        checkout.</p>
                                                    <div class="header-hover-actions">
                                                        <a href="{{ route('login') }}"
                                                            class="tf-btn animate-btn w-100">Login</a>
                                                        <a href="{{ route('register') }}"
                                                            class="tf-btn btn-white w-100">Register</a>
                                                    </div>
                                                </div>
                                                @else
                                                @if(!$headerCart || $headerCart->items->isEmpty())
                                                <div class="mini-cart-empty is-compact">
                                                    <p class="mb-3">Your cart is empty for now.</p>
                                                    <a href="{{ route('shop.index') }}"
                                                        class="tf-btn animate-btn w-100">Continue Shopping</a>
                                                </div>
                                                @else
                                                <div class="mini-cart-preview">
                                                    @foreach($headerCart->items->take(3) as $item)
                                                    <div class="mini-cart-item">
                                                        <a href="{{ $item->product ? route('shop.show', $item->product->slug) : route('cart.index') }}"
                                                            class="mini-cart-thumb">
                                                            <img src="{{ $item->image_url }}"
                                                                alt="{{ $item->item_name }}">
                                                        </a>
                                                        <div>
                                                            <a href="{{ $item->product ? route('shop.show', $item->product->slug) : route('cart.index') }}"
                                                                class="mini-cart-title">
                                                                {{ $item->item_name }}
                                                            </a>
                                                            <div class="mini-cart-meta">{{ $item->qty }} x
                                                                ₦{{ number_format($item->unit_price, 2) }}</div>
                                                            <div class="mini-cart-price">
                                                                ₦{{ number_format($item->subtotal, 2) }}</div>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                </div>
                                                <div class="mini-cart-preview-footer">
                                                    <div class="mini-cart-total mb-0">
                                                        <span>Subtotal</span>
                                                        <span>₦{{ number_format($cartSubtotal, 2) }}</span>
                                                    </div>
                                                    <div class="header-hover-actions">
                                                        <a href="{{ route('cart.index') }}"
                                                            class="tf-btn btn-white w-100">View Cart</a>
                                                        <a href="{{ route('checkout.index') }}"
                                                            class="tf-btn animate-btn w-100">Check Out</a>
                                                    </div>
                                                </div>
                                                @endif
                                                @endguest
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="header-bottom_wrap d-none d-xl-block">
                    <div class="container">
                        <div class="header-bottom">
                            <nav class="box-navigation">
                                <ul class="box-nav-menu justify-content-center">
                                    <li class="menu-item">
                                        <a href="{{ route('home') }}" class="item-link">
                                            <span class="text cus-text">Home</span>
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a href="{{ route('shop.index') }}" class="item-link">
                                            <span class="text cus-text">Shop</span>
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a href="{{ route('services.index') }}" class="item-link">
                                            <span class="text cus-text">Services</span>
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a href="{{ route('consultancy.index') }}" class="item-link">
                                            <span class="text cus-text">Advisory</span>
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a href="{{ route('booking.index') }}" class="item-link">
                                            <span class="text cus-text">Reservations</span>
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a href="{{ route('emergency.index') }}" class="item-link">
                                            <span class="text cus-text">Emergency</span>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </header>

            <div class="offcanvas offcanvas-start canvas-mb" id="mobileMenu">
                <div class="mb-canvas-content">
                    <div class="mb-body">
                        <div class="mb-content-top">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <a href="{{ route('home') }}" class="logo-site logo-text">
                                    <img loading="lazy" width="112" height="22"
                                        src="{{ asset('assets/images/logo/logo.svg') }}" alt="Kiosk">
                                </a>
                                <span class="icon-close-popup" data-bs-dismiss="offcanvas">
                                    <i class="icon icon-X2"></i>
                                </span>
                            </div>

                            <form action="{{ route('shop.index') }}" class="form-search-nav style-2">
                                <fieldset>
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        placeholder="Search Products" aria-label="Search products">
                                </fieldset>
                                <button type="submit" class="btn-action">
                                    <i class="icon icon-MagnifyingGlass"></i>
                                </button>
                            </form>

                            <div class="mobile-utility-links">
                                <a href="{{ route('home') }}">Home</a>
                                <a href="{{ route('shop.index') }}">Shop</a>
                                <a href="{{ route('services.index') }}">Services</a>
                                <a href="{{ route('consultancy.index') }}">Advisory</a>
                                <a href="{{ route('booking.index') }}">Reservations</a>
                                <a href="{{ route('emergency.index') }}">Emergency</a>
                                <a href="{{ route('info.contact') }}">Contact Kiosk</a>
                            </div>

                            <div class="mobile-auth-links">
                                @guest
                                <a href="{{ route('login') }}" class="tf-btn animate-btn w-100">Login</a>
                                <a href="{{ route('register') }}" class="tf-btn btn-white w-100">Register</a>
                                @else
                                <a href="{{ $homePath }}" class="tf-btn animate-btn w-100">My Account</a>
                                <a href="{{ route('cart.index') }}" class="tf-btn btn-white w-100">View Cart</a>
                                @endguest
                            </div>

                            <div class="need-help-wrap">
                                <a href="{{ route('info.contact') }}"
                                    class="nd-title h6 fw-medium mb-16 d-inline-block">Need Help?</a>
                                <p class="lh-26 cl-text-2 mb-4">
                                    Reach the right support flow quickly, whether you need contact help, order guidance,
                                    or urgent assistance.
                                </p>
                                <div class="mobile-help-links d-grid gap-2 mb-4">
                                    <a href="{{ route('info.contact') }}" class="tf-btn animate-btn w-100">Contact
                                        Kiosk</a>
                                    <a href="{{ route('info.faqs') }}" class="tf-btn btn-white w-100">Orders and
                                        FAQs</a>
                                    <a href="{{ route('emergency.index') }}" class="tf-btn btn-white w-100">Emergency
                                        Help</a>
                                </div>
                                <a href="mailto:info@mirrorageconcepts.com" class="cl-text-2 link mb-8">
                                    info@mirrorageconcepts.com
                                </a>
                                <a href="tel:+2347035627734" class="cl-text-2 link">
                                    +234 703 562 7734
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="site-module-shell" id="main-content">
                @yield('content')
            </div>

            <div class="modal modalCentered fade quick-auth-modal" id="sign" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <span class="icon-close-popup" data-bs-dismiss="modal">
                            <i class="icon-X2"></i>
                        </span>
                        <div class="modal-heading text-center">
                            <h3 class="title-pop mb-8">Account Access</h3>
                            <p class="desc-pop cl-text-2">Use the desktop hover panel for quick login or signup. This
                                modal remains available as a small-screen fallback.</p>
                        </div>
                        <div class="modal-main">
                            <div class="d-grid gap-3">
                                <a href="{{ route('login') }}" class="tf-btn animate-btn w-100">Login</a>
                                <a href="{{ route('register') }}" class="tf-btn btn-white w-100">Register</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="offcanvas offcanvas-end popup-shopping-cart mini-cart-offcanvas" id="shoppingCart">
                <div class="offcanvas-header">
                    <div>
                        <h5 class="title mb-1">Shopping Cart</h5>
                        <p class="mb-0 text-muted small">Quick cart access for your current Kiosk session.</p>
                    </div>
                    <span class="icon-close-popup" data-bs-dismiss="offcanvas">
                        <i class="icon icon-X2"></i>
                    </span>
                </div>
                <div class="offcanvas-body">
                    @guest
                    <div class="mini-cart-empty">
                        <p class="mb-3">Login to keep products in your cart and continue to checkout.</p>
                        <div class="mini-cart-actions">
                            <a href="{{ route('login') }}" class="tf-btn animate-btn w-100">Login</a>
                            <a href="{{ route('register') }}" class="tf-btn btn-white w-100">Register</a>
                        </div>
                    </div>
                    @else
                    @if(!$headerCart || $headerCart->items->isEmpty())
                    <div class="mini-cart-empty">
                        <p class="mb-3">Your cart is empty for now.</p>
                        <a href="{{ route('shop.index') }}" class="tf-btn animate-btn">Continue Shopping</a>
                    </div>
                    @else
                    <div class="mini-cart-list">
                        @foreach($headerCart->items as $item)
                        <div class="mini-cart-item">
                            <a href="{{ $item->product ? route('shop.show', $item->product->slug) : route('cart.index') }}"
                                class="mini-cart-thumb">
                                <img src="{{ $item->image_url }}" alt="{{ $item->item_name }}">
                            </a>
                            <div>
                                <a href="{{ $item->product ? route('shop.show', $item->product->slug) : route('cart.index') }}"
                                    class="mini-cart-title">
                                    {{ $item->item_name }}
                                </a>
                                <div class="mini-cart-meta">{{ $item->qty }} x
                                    ₦{{ number_format($item->unit_price, 2) }}</div>
                                <div class="mini-cart-price">₦{{ number_format($item->subtotal, 2) }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="mini-cart-footer">
                        <div class="mini-cart-total">
                            <span>Subtotal</span>
                            <span>₦{{ number_format($cartSubtotal, 2) }}</span>
                        </div>
                        <div class="mini-cart-actions">
                            <a href="{{ route('cart.index') }}" class="tf-btn btn-white w-100">View Cart</a>
                            <a href="{{ route('checkout.index') }}" class="tf-btn animate-btn w-100">Check Out</a>
                        </div>
                    </div>
                    @endif
                    @endguest
                </div>
            </div>

            <footer class="footer-amere footer style-default bg-dark">
                <div class="container">
                    <div class="row g-5">
                        <div class="col-lg-5">
                            <div class="footer-subscribe">
                                <p class="footer-title h6 fw-medium text-uppercase mb-16">Newsletter</p>
                                <p class="mb-0">Subscribe for store updates and discounts.</p>
                                <form class="footer-subscribe-field" action="{{ route('newsletter.subscribe') }}"
                                    method="post">
                                    @csrf
                                    <input type="email" name="email"
                                        value="{{ old('email', auth()->user()?->email ?? '') }}"
                                        placeholder="Enter your e-mail" aria-label="Enter your e-mail" required>
                                    <button type="submit" aria-label="Subscribe">
                                        <i class="icon icon-ArrowUpRight"></i>
                                    </button>
                                </form>
                                @if(session('newsletter_status'))
                                <div class="footer-subscribe-status is-success">{{ session('newsletter_status') }}</div>
                                @endif
                                @if($errors->has('email'))
                                <div class="footer-subscribe-status is-error">{{ $errors->first('email') }}</div>
                                @endif
                                <p class="footer-fine mb-0">
                                    By subscribing, you agree to Kiosk's<br>
                                    <span class="text-white">customer privacy and communication terms.</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-2">
                            <h6 class="mb-24 text-uppercase">Kiosk</h6>
                            <ul class="footer-link-list">
                                <li><a href="{{ route('info.about') }}">About Kiosk</a></li>
                                <li><a href="{{ route('info.branches') }}">Kiosk Branches</a></li>
                                <li><a href="{{ route('info.contact') }}">Contact Kiosk</a></li>
                                <li><a href="{{ auth()->check() ? $homePath : route('login') }}">Customer Account</a>
                                </li>
                            </ul>
                        </div>
                        <div class="col-sm-6 col-lg-2">
                            <h6 class="mb-24 text-uppercase">Modules</h6>
                            <ul class="footer-link-list">
                                <li><a href="{{ route('info.shipping') }}">Shipping and Service Terms</a></li>
                                <li><a href="{{ route('info.returns') }}">Returns and Support Terms</a></li>
                                <li><a href="{{ route('info.privacy') }}">Customer Privacy Notice</a></li>
                                <li><a href="{{ route('info.faqs') }}">Checkout Terms and Agreement</a></li>
                            </ul>
                        </div>
                        <div class="col-lg-3">
                            <h6 class="mb-24 text-uppercase">Our Store</h6>
                            <p class="cl-text-3 mb-4">24/7 Support Center:</p>
                            <a href="tel:+2347035627734" class="text-white link h4 fw-medium mb-12 d-inline-block">
                                (+234) 703 562 7734
                            </a>
                            <p class="mb-4">Global sourcing, curated storefront access, and customer request support.
                            </p>
                            <a href="mailto:info@mirrorageconcepts.com" class="cl-text-3 link">
                                info@mirrorageconcepts.com
                            </a>
                        </div>
                    </div>
                </div>

                <div class="footer-marquee">
                    <div class="footer-marquee-track">
                        KIOSK COMMERCE CUSTOMER FLOW KIOSK COMMERCE CUSTOMER FLOW KIOSK COMMERCE CUSTOMER FLOW KIOSK
                        COMMERCE CUSTOMER FLOW KIOSK COMMERCE CUSTOMER FLOW KIOSK COMMERCE CUSTOMER FLOW KIOSK
                        COMMERCE CUSTOMER FLOW
                    </div>
                </div>

                <div class="footer-bottom-amere">
                    <div class="container-full">
                        <div class="footer-bottom-inner">
                            <div class="footer-bottom-left">
                                <div class="topbar-meta footer-locale-meta">
                                    <span class="topbar-meta-item">
                                        <span class="tf-dropdown-static topbar-dropdown" tabindex="0" role="button"
                                            aria-haspopup="true" data-locale-dropdown="country">
                                            <span class="topbar-dropdown-current">
                                                <span class="topbar-flag-badge" aria-hidden="true">NG</span>
                                                <span data-locale-display="country-label">
                                                    Nigeria
                                                    <small data-locale-display="country-meta">Detecting your
                                                        location...</small>
                                                </span>
                                            </span>
                                            <i class="icon icon-CaretDown"></i>
                                            <span class="topbar-dropdown-menu" data-country-menu>
                                                <span class="topbar-dropdown-option">
                                                    <span class="topbar-flag-badge" aria-hidden="true">...</span>
                                                    <span>Loading countries<small>Preparing storefront
                                                            regions</small></span>
                                                </span>
                                            </span>
                                        </span>
                                    </span>
                                    <span class="topbar-meta-item">
                                        <span class="tf-dropdown-static topbar-dropdown" tabindex="0" role="button"
                                            aria-haspopup="true" data-locale-dropdown="language">
                                            <span data-locale-display="language-label">Yoruba</span>
                                            <i class="icon icon-CaretDown"></i>
                                            <span class="topbar-dropdown-menu">
                                                <button type="button"
                                                    class="topbar-dropdown-select topbar-dropdown-option is-active"
                                                    data-locale-option="language" data-value="yoruba"
                                                    data-label="Yoruba" data-country="ng">
                                                    <span>
                                                        Yoruba
                                                        <small>South West Nigeria</small>
                                                    </span>
                                                </button>
                                                <button type="button"
                                                    class="topbar-dropdown-select topbar-dropdown-option"
                                                    data-locale-option="language" data-value="igbo" data-label="Igbo"
                                                    data-country="ng">
                                                    <span>
                                                        Igbo
                                                        <small>South East Nigeria</small>
                                                    </span>
                                                </button>
                                                <button type="button"
                                                    class="topbar-dropdown-select topbar-dropdown-option"
                                                    data-locale-option="language" data-value="hausa" data-label="Hausa"
                                                    data-country="ng">
                                                    <span>
                                                        Hausa
                                                        <small>North and West Africa</small>
                                                    </span>
                                                </button>
                                                <button type="button"
                                                    class="topbar-dropdown-select topbar-dropdown-option"
                                                    data-locale-option="language" data-value="english"
                                                    data-label="English" data-country="nigeria">
                                                    <span>
                                                        English
                                                        <small>National and support default</small>
                                                    </span>
                                                </button>
                                            </span>
                                        </span>
                                    </span>
                                </div>
                                <p class="footer-bottom-copy">&copy; {{ date('Y') }} Kiosk. All Rights Reserved.</p>
                            </div>
                            @php
                            $footerSocialLinks = [
                            ['label' => 'Facebook', 'url' => config('kiosk.social.facebook'), 'icon' => 'icon-FacebookLogo'],
                            ['label' => 'Instagram', 'url' => config('kiosk.social.instagram'), 'icon' => 'icon-InstagramLogo'],
                            ['label' => 'X', 'url' => config('kiosk.social.x'), 'icon' => 'icon-XLogo'],
                            ['label' => 'TikTok', 'url' => config('kiosk.social.tiktok'), 'icon' => 'icon-TiktokLogo'],
                            ['label' => 'LinkedIn', 'url' => config('kiosk.social.linkedin'), 'icon' => 'bi bi-linkedin'],
                            ['label' => 'YouTube', 'url' => config('kiosk.social.youtube'), 'icon' => 'bi bi-youtube'],
                            ['label' => 'WhatsApp', 'url' => config('kiosk.social.whatsapp'), 'icon' => 'bi bi-whatsapp'],
                            ['label' => 'Pinterest', 'url' => config('kiosk.social.pinterest'), 'icon' => 'bi bi-pinterest'],
                            ];
                            @endphp
                            <div class="footer-bottom-right">
                                <ul class="footer-social-list" aria-label="Social media accounts">
                                    @foreach ($footerSocialLinks as $socialLink)
                                    @if (filled($socialLink['url']))
                                    <li>
                                        <a class="footer-social-link" href="{{ $socialLink['url'] }}" target="_blank"
                                            rel="noopener noreferrer" aria-label="Kiosk on {{ $socialLink['label'] }}">
                                            <i class="icon {{ $socialLink['icon'] }}" aria-hidden="true"></i>
                                        </a>
                                    </li>
                                    @endif
                                    @endforeach
                                </ul>
                                <ul class="payment-list" aria-label="Accepted payment cards">
                                    <li><img loading="lazy" width="38" height="24"
                                            src="{{ asset('assets/images/payment/visa.svg') }}" alt="Visa">
                                    </li>
                                    <li><img loading="lazy" width="38" height="24"
                                            src="{{ asset('assets/images/payment/master-card.svg') }}" alt="Mastercard">
                                    </li>
                                    <li><img loading="lazy" width="38" height="24"
                                            src="{{ asset('assets/images/payment/amex.svg') }}" alt="Amex">
                                    </li>
                                    <li><img loading="lazy" width="38" height="24"
                                            src="{{ asset('assets/images/payment/paypal.svg') }}" alt="PayPal">
                                    </li>
                                    <li><img loading="lazy" width="38" height="24"
                                            src="{{ asset('assets/images/payment/water.svg') }}" alt="Debit card"></li>
                                    <li><img loading="lazy" width="38" height="24"
                                            src="{{ asset('assets/images/payment/discover.svg') }}" alt="Discover"></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </main>
    </div>

    <div class="cookie-banner" id="siteCookieBanner" aria-live="polite">
        <div class="cookie-banner-inner">
            <div class="cookie-banner-copy">
                <strong>Site cookies</strong>
                Kiosk uses essential cookies for secure login, cart continuity, language preferences, and site
                performance. You can accept all cookies, keep only essential cookies, or save a balanced preference.
            </div>
            <div class="cookie-banner-actions">
                <button type="button" class="btn btn-outline-light" data-cookie-action="essential">Essential
                    only</button>
                <button type="button" class="btn btn-outline-light" data-cookie-action="balanced">Save
                    preferences</button>
                <button type="button" class="btn btn-primary" data-cookie-action="accept">Accept all</button>
            </div>
        </div>
    </div>

    @include('partials.assets.ui-scripts', [
    'scripts' => [
    'assets/js/plugin/jquery.min.js',
    'assets/js/plugin/bootstrap.min.js',
    'assets/js/plugin/swiper-bundle.min.js',
    'assets/js/plugin/bootstrap-select.min.js',
    'assets/js/plugin/count-down.js',
    'assets/js/plugin/infinityslide.js',
    'assets/js/plugin/wow.min.js',
    'assets/js/carousel.js',
    'assets/js/main.js',
    ],
    ])
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var geoOptionsUrl = @json(route('profile.geo-options'));
        var detectCountryUrl = @json(route('profile.detect-country'));
        var sitePreferencesUrl = @json(route('site.preferences.store'));
        var shouldPersistSitePreferences = @json(auth()->check());
        var persistedCountryCode = @json($sitePreferenceUser?->preferred_country_code);
        var persistedLanguage = @json($sitePreferenceUser?->preferred_language);
        var persistedCookieConsent = @json($sitePreferenceUser?->cookie_consent_preferences);
        var persistedCookieConsentMode = @json($sitePreferenceUser?->cookie_consent_mode);

        if (typeof AOS !== 'undefined') {
            var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            AOS.init({
                disable: prefersReducedMotion,
                duration: 700,
                easing: 'ease-out-quart',
                once: true,
                offset: 40
            });
        }

        if (window.jQuery && typeof window.jQuery.fn.selectpicker === 'function') {
            window.jQuery('.selectpicker').selectpicker();
        }

        function fetchJson(url, options) {
            return fetch(url, options || {}).then(function(response) {
                if (!response.ok) {
                    throw new Error('Request failed.');
                }

                return response.json();
            });
        }

        function persistSitePreferences(payload) {
            if (!shouldPersistSitePreferences) {
                return Promise.resolve();
            }

            return fetchJson(sitePreferencesUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(payload)
            }).catch(function() {
                return null;
            });
        }

        function normalizeCountryValue(value) {
            return String(value || '').trim().toLowerCase();
        }

        function countryLabel(item) {
            var code = String(item.code || '').trim().toUpperCase();

            return code ? item.name + ' (' + code + ')' : item.name;
        }

        function countryMeta(item, source) {
            if (normalizeCountryValue(item.code) === 'ng') {
                return source === 'gps' ? 'Auto-detected from your location' : 'Kiosk operating region';
            }

            return source === 'gps' ? 'Auto-detected from your location' : 'Global storefront access';
        }

        function countryValue(item) {
            return normalizeCountryValue(item.code || item.name);
        }

        function findCountryOption(value) {
            var normalized = normalizeCountryValue(value);

            return Array.from(document.querySelectorAll('[data-locale-option="country"]')).find(function(
                option) {
                return normalizeCountryValue(option.dataset.value) === normalized;
            });
        }

        function populateCountryMenus(countries) {
            document.querySelectorAll('[data-country-menu]').forEach(function(menu) {
                menu.innerHTML = '';

                countries.forEach(function(item) {
                    var option = document.createElement('button');
                    option.type = 'button';
                    option.className = 'topbar-dropdown-select topbar-dropdown-option';
                    option.dataset.localeOption = 'country';
                    option.dataset.value = countryValue(item);
                    option.dataset.label = countryLabel(item);
                    option.dataset.meta = countryMeta(item, 'manual');
                    option.dataset.badge = String(item.code || '').trim().toUpperCase() || item
                        .name.slice(0, 2).toUpperCase();
                    option.innerHTML =
                        '<span class="topbar-flag-badge" aria-hidden="true">' + option.dataset
                        .badge + '</span>' +
                        '<span>' + option.dataset.label + '<small>' + option.dataset.meta +
                        '</small></span>';
                    menu.appendChild(option);
                });
            });
        }

        function detectCountryFromLocale() {
            var localeCandidates = [];

            if (Array.isArray(navigator.languages)) {
                localeCandidates = localeCandidates.concat(navigator.languages);
            }

            if (navigator.language) {
                localeCandidates.push(navigator.language);
            }

            for (var index = 0; index < localeCandidates.length; index += 1) {
                var locale = localeCandidates[index];
                var region = '';

                if (typeof Intl !== 'undefined' && typeof Intl.Locale === 'function') {
                    try {
                        region = new Intl.Locale(locale).region || '';
                    } catch (error) {
                        region = '';
                    }
                }

                if (!region && locale.indexOf('-') !== -1) {
                    region = locale.split('-').pop() || '';
                }

                if (!region && locale.indexOf('_') !== -1) {
                    region = locale.split('_').pop() || '';
                }

                var match = findCountryOption(region);

                if (match) {
                    return match.dataset.value;
                }
            }

            return 'ng';
        }

        function browserLanguageMeta() {
            return 'Detected from your browser';
        }

        function browserLanguageLabel() {
            var locale = (Array.isArray(navigator.languages) && navigator.languages[0]) || navigator.language ||
                'en';
            var languageCode = String(locale).split('-')[0].split('_')[0].toLowerCase();

            if (typeof Intl !== 'undefined' && typeof Intl.DisplayNames === 'function') {
                try {
                    var displayNames = new Intl.DisplayNames([locale], {
                        type: 'language'
                    });
                    var label = displayNames.of(languageCode);

                    if (label) {
                        return label.charAt(0).toUpperCase() + label.slice(1);
                    }
                } catch (error) {
                    return languageCode === 'en' ? 'English' : languageCode.toUpperCase();
                }
            }

            return languageCode === 'en' ? 'English' : languageCode.toUpperCase();
        }

        function ensureAutoLanguageOption() {
            var value = ((Array.isArray(navigator.languages) && navigator.languages[0]) || navigator.language ||
                    'en')
                .split('-')[0]
                .split('_')[0]
                .toLowerCase();
            var label = browserLanguageLabel();

            document.querySelectorAll('[data-locale-dropdown="language"] .topbar-dropdown-menu').forEach(
                function(menu) {
                    var option = menu.querySelector(
                        '[data-locale-option="language"][data-auto-language="true"]');

                    if (!option) {
                        option = document.createElement('button');
                        option.type = 'button';
                        option.className = 'topbar-dropdown-select topbar-dropdown-option';
                        option.dataset.localeOption = 'language';
                        option.dataset.autoLanguage = 'true';
                        menu.insertBefore(option, menu.firstChild);
                    }

                    option.dataset.value = value;
                    option.dataset.label = label;
                    option.dataset.country = '';
                    option.innerHTML = '<span>' + label + '<small>' + browserLanguageMeta() +
                        '</small></span>';
                });

            return value;
        }

        function detectCountryByGps() {
            return new Promise(function(resolve) {
                if (!navigator.geolocation) {
                    resolve(detectCountryFromLocale());
                    return;
                }

                navigator.geolocation.getCurrentPosition(function(position) {
                    fetchJson(detectCountryUrl, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').getAttribute('content'),
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                latitude: position.coords.latitude,
                                longitude: position.coords.longitude
                            })
                        })
                        .then(function(payload) {
                            var option = findCountryOption(payload.code || payload
                                .country || '');

                            if (option) {
                                option.dataset.meta = countryMeta({
                                    code: payload.code || option.dataset.value,
                                    name: payload.country || option.dataset.label
                                }, 'gps');
                                option.querySelector('small').textContent = option.dataset
                                    .meta;
                                resolve(option.dataset.value);
                                return;
                            }

                            resolve(detectCountryFromLocale());
                        })
                        .catch(function() {
                            resolve(detectCountryFromLocale());
                        });
                }, function() {
                    resolve(detectCountryFromLocale());
                }, {
                    enableHighAccuracy: false,
                    timeout: 8000,
                    maximumAge: 300000
                });
            });
        }

        function renderCountry(dropdown, option) {
            var label = dropdown.querySelector('[data-locale-display="country-label"]');
            var meta = dropdown.querySelector('[data-locale-display="country-meta"]');
            var current = dropdown.querySelector('.topbar-dropdown-current');

            if (!label || !meta || !current || !option) {
                return;
            }

            label.childNodes[0].nodeValue = option.dataset.label + ' ';
            meta.textContent = option.dataset.meta || '';

            var badge = option.dataset.badge ?
                '<span class="topbar-flag-badge" aria-hidden="true">' + option.dataset.badge + '</span>' :
                '<span class="topbar-flag-badge" aria-hidden="true">' + option.dataset.label.slice(0, 2)
                .toUpperCase() + '</span>';

            current.innerHTML = badge + '<span data-locale-display="country-label">' + option.dataset.label +
                '<small data-locale-display="country-meta">' + (option.dataset.meta || '') + '</small></span>';
        }

        function renderLanguage(dropdown, option) {
            var label = dropdown.querySelector('[data-locale-display="language-label"]');

            if (label && option) {
                label.textContent = option.dataset.label;
            }
        }

        function applyLanguageAvailability(countryValue) {
            document.querySelectorAll('[data-locale-option="language"]').forEach(function(option) {
                var allowedCountries = (option.dataset.country || '').split(' ').filter(Boolean);
                var isAllowed = !allowedCountries.length || allowedCountries.indexOf(countryValue) !== -
                    1;

                option.hidden = !isAllowed;
                option.disabled = !isAllowed;
            });
        }

        function firstAvailableLanguage(countryValue) {
            return Array.from(document.querySelectorAll('[data-locale-option="language"]')).find(function(
                option) {
                var allowedCountries = (option.dataset.country || '').split(' ').filter(Boolean);
                return !option.hidden && !option.disabled && (!allowedCountries.length ||
                    allowedCountries.indexOf(countryValue) !== -1);
            });
        }

        function syncLocale(type, value) {
            var options = document.querySelectorAll('[data-locale-option="' + type + '"]');
            var activeOption = null;

            options.forEach(function(option) {
                if (type === 'language' && (option.hidden || option.disabled)) {
                    option.classList.remove('is-active');
                    return;
                }

                var isActive = option.dataset.value === value;
                option.classList.toggle('is-active', isActive);

                if (isActive) {
                    activeOption = option;
                    var dropdown = option.closest('[data-locale-dropdown="' + type + '"]');

                    if (type === 'country') {
                        renderCountry(dropdown, option);
                    } else {
                        renderLanguage(dropdown, option);
                    }
                }
            });

            window.localStorage.setItem('kiosk-' + type, value);

            if (type === 'country') {
                persistSitePreferences({
                    preferred_country_code: String(value || '').toUpperCase()
                });
            }

            if (type === 'language') {
                persistSitePreferences({
                    preferred_language: value
                });
            }

            if (type === 'country' && activeOption) {
                applyLanguageAvailability(value);

                var savedLanguage = window.localStorage.getItem('kiosk-language') || 'yoruba';
                var matchingLanguage = Array.from(document.querySelectorAll('[data-locale-option="language"]'))
                    .find(function(option) {
                        return option.dataset.value === savedLanguage && !option.hidden && !option.disabled;
                    });
                var fallbackLanguage = matchingLanguage || firstAvailableLanguage(value);

                if (fallbackLanguage) {
                    syncLocale('language', fallbackLanguage.dataset.value);
                }
            }
        }

        document.addEventListener('click', function(event) {
            var option = event.target.closest('[data-locale-option]');

            if (!option) {
                return;
            }

            event.preventDefault();
            syncLocale(option.dataset.localeOption, option.dataset.value);
            option.closest('.topbar-dropdown').blur();
        });

        var savedCountry = window.localStorage.getItem('kiosk-country') || persistedCountryCode || '';
        var savedLanguage = window.localStorage.getItem('kiosk-language') || persistedLanguage || 'yoruba';

        fetchJson(geoOptionsUrl + '?country=', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(function(payload) {
                var countries = Array.isArray(payload.countries) ? payload.countries : [];

                if (!countries.length) {
                    throw new Error('No countries available.');
                }

                populateCountryMenus(countries);
                var autoLanguageValue = ensureAutoLanguageOption();

                var hasSavedCountry = !!findCountryOption(savedCountry);
                var initialCountryPromise = hasSavedCountry ?
                    Promise.resolve(savedCountry) :
                    detectCountryByGps();

                return initialCountryPromise.then(function(initialCountry) {
                    var fallbackCountry = findCountryOption(initialCountry) ? initialCountry : 'ng';
                    syncLocale('country', fallbackCountry);

                    var initialLanguage = Array.from(document.querySelectorAll(
                        '[data-locale-option="language"]')).find(
                        function(option) {
                            return option.dataset.value === savedLanguage && !option.hidden && !
                                option.disabled;
                        });

                    if (initialLanguage) {
                        syncLocale('language', savedLanguage);
                        return;
                    }

                    var autoLanguage = Array.from(document.querySelectorAll(
                        '[data-locale-option="language"]')).find(
                        function(option) {
                            return option.dataset.value === autoLanguageValue && !option
                                .hidden && !option.disabled;
                        });

                    if (autoLanguage) {
                        syncLocale('language', autoLanguageValue);
                        return;
                    }

                    var fallbackInitialLanguage = firstAvailableLanguage(fallbackCountry);

                    if (fallbackInitialLanguage) {
                        syncLocale('language', fallbackInitialLanguage.dataset.value);
                    }
                });
            })
            .catch(function() {
                syncLocale('country', 'ng');
                syncLocale('language', 'yoruba');
            });

        document.querySelectorAll('[data-auth-tab]').forEach(function(tab) {
            tab.addEventListener('click', function() {
                var nextTab = tab.dataset.authTab;
                var panel = tab.closest('[data-hover-content]');

                if (!panel) {
                    return;
                }

                panel.querySelectorAll('[data-auth-tab]').forEach(function(button) {
                    button.classList.toggle('is-active', button.dataset.authTab ===
                        nextTab);
                });

                panel.querySelectorAll('[data-auth-form]').forEach(function(form) {
                    form.classList.toggle('is-active', form.dataset.authForm ===
                        nextTab);
                });
            });
        });

        document.querySelectorAll('[data-auth-form]').forEach(function(form) {
            var toggle = form.querySelector('[data-password-toggle]');
            var fields = form.querySelectorAll('[data-password-field]');

            if (!toggle || !fields.length) {
                return;
            }

            toggle.addEventListener('change', function() {
                var type = toggle.checked ? 'text' : 'password';
                fields.forEach(function(field) {
                    field.type = type;
                });
            });
        });

        document.querySelectorAll('.icon-close-popup').forEach(function(icon) {
            icon.addEventListener('mouseenter', function() {
                icon.classList.remove('is-rotating-out');
                void icon.offsetWidth;
                icon.classList.add('is-rotating-in');
            });

            icon.addEventListener('mouseleave', function() {
                icon.classList.remove('is-rotating-in');
                void icon.offsetWidth;
                icon.classList.add('is-rotating-out');
            });
        });

        var cookieBanner = document.getElementById('siteCookieBanner');
        var csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
        var currentCsrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : '';

        function setSiteCookie(name, value, days) {
            var expires = '';

            if (days) {
                var date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = '; expires=' + date.toUTCString();
            }

            document.cookie = name + '=' + encodeURIComponent(value) + expires + '; path=/; SameSite=Lax';
        }

        function getSiteCookie(name) {
            var prefix = name + '=';
            return document.cookie.split(';').map(function(item) {
                return item.trim();
            }).reduce(function(found, item) {
                if (found || item.indexOf(prefix) !== 0) {
                    return found;
                }

                return decodeURIComponent(item.substring(prefix.length));
            }, '');
        }

        function getCookieConsentPayload() {
            var raw = getSiteCookie('kiosk_cookie_consent');

            if (!raw) {
                return persistedCookieConsent ? {
                    mode: persistedCookieConsentMode,
                    essential: persistedCookieConsent.essential,
                    analytics: persistedCookieConsent.analytics,
                    marketing: persistedCookieConsent.marketing,
                    saved_at: persistedCookieConsent.saved_at || null,
                    csrf_token: currentCsrfToken
                } : null;
            }

            try {
                return JSON.parse(raw);
            } catch (error) {
                return null;
            }
        }

        function shouldShowCookieBanner() {
            var payload = getCookieConsentPayload();

            if (!payload) {
                return true;
            }

            return !payload.csrf_token || payload.csrf_token !== currentCsrfToken;
        }

        function saveCookieConsent(mode) {
            var payload = {
                mode: mode,
                essential: true,
                analytics: mode === 'accept' || mode === 'balanced',
                marketing: mode === 'accept',
                saved_at: new Date().toISOString(),
                csrf_token: currentCsrfToken
            };

            setSiteCookie('kiosk_cookie_consent', JSON.stringify(payload));
            persistSitePreferences({
                cookie_consent_mode: mode,
                cookie_consent_preferences: {
                    essential: payload.essential,
                    analytics: payload.analytics,
                    marketing: payload.marketing,
                    saved_at: payload.saved_at
                }
            });

            if (cookieBanner) {
                cookieBanner.classList.remove('is-visible');
            }
        }

        if (cookieBanner && shouldShowCookieBanner()) {
            window.setTimeout(function() {
                cookieBanner.classList.add('is-visible');
            }, 500);
        }

        document.querySelectorAll('[data-cookie-action]').forEach(function(button) {
            button.addEventListener('click', function() {
                saveCookieConsent(button.dataset.cookieAction);
            });
        });

        if (window.matchMedia('(min-width: 1200px)').matches) {
            document.querySelectorAll('[data-hover-panel]').forEach(function(group) {
                var trigger = group.querySelector('[data-hover-trigger]');
                var panel = group.querySelector('[data-hover-content]');
                var closeTimer = null;

                if (!trigger || !panel) {
                    return;
                }

                function openPanel() {
                    window.clearTimeout(closeTimer);
                    panel.classList.add('is-open');
                    trigger.setAttribute('aria-expanded', 'true');
                }

                function closePanel() {
                    closeTimer = window.setTimeout(function() {
                        panel.classList.remove('is-open');
                        trigger.setAttribute('aria-expanded', 'false');
                    }, 120);
                }

                trigger.removeAttribute('data-bs-toggle');
                trigger.setAttribute('aria-haspopup', 'true');
                trigger.setAttribute('aria-expanded', 'false');

                trigger.addEventListener('click', function(event) {
                    if ((trigger.getAttribute('href') || '').charAt(0) === '#') {
                        event.preventDefault();
                        openPanel();
                    }
                });

                group.addEventListener('mouseenter', openPanel);
                group.addEventListener('mouseleave', closePanel);
                trigger.addEventListener('focus', openPanel);
                panel.addEventListener('mouseenter', openPanel);
                panel.addEventListener('mouseleave', closePanel);

                panel.querySelectorAll('[data-hover-close]').forEach(function(closeButton) {
                    closeButton.addEventListener('click', function(event) {
                        event.preventDefault();
                        event.stopPropagation();
                        window.clearTimeout(closeTimer);
                        panel.classList.remove('is-open');
                        trigger.setAttribute('aria-expanded', 'false');
                    });
                });
            });
        }
    });
    </script>
    @include('partials.idle-logout')
    @include('partials.preloader-scripts')
    @stack('scripts')
</body>

</html>