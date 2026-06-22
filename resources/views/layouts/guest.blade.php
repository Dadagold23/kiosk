@php
    $routeName = request()->route()?->getName();
    $authImage = asset('admin-assets/images/login-images/login-cover.svg');
    $authAlt = 'Sign In';

    if ($routeName === 'register') {
        $authImage = asset('admin-assets/images/login-images/register-cover.svg');
        $authAlt = 'Register';
    } elseif ($routeName === 'password.request') {
        $authImage = asset('admin-assets/images/login-images/forgot-password-cover.svg');
        $authAlt = 'Forgot Password';
    } elseif ($routeName === 'password.reset') {
        $authImage = asset('admin-assets/images/login-images/reset-password-cover.svg');
        $authAlt = 'Reset Password';
    } elseif ($routeName === 'verification.notice') {
        $authImage = asset('admin-assets/images/login-images/forgot-password-cover.svg');
        $authAlt = 'Verify Email';
    }
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @include('partials.seo-meta', [
            'title' => config('app.name', 'Kiosk') . ' Authentication',
            'description' => 'Secure login, password reset, and account verification for Kiosk users.',
            'keywords' => 'Kiosk login, registration, password reset, email verification',
            'robots' => 'noindex, nofollow',
            'themeColor' => '#111111',
        ])
        @include('partials.assets.ui-head', [
            'includeFonts' => true,
            'includeIcomoon' => true,
            'includeBootstrapIcons' => true,
        ])
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            :root {
                --guest-type-scale: .93;
            }

            html {
                font-size: calc(16px * var(--guest-type-scale));
            }

            body {
                background:
                    radial-gradient(circle at top left, rgba(220, 70, 70, .12), transparent 30%),
                    linear-gradient(135deg, #f7f1ea 0%, #f4efe8 35%, #f8f8f8 100%);
                color: #111111;
                font-family: Figtree, sans-serif;
                margin: 0;
                min-height: 100vh;
            }

            .auth-shell {
                align-items: center;
                display: grid;
                gap: 2rem;
                grid-template-columns: minmax(0, 1.05fr) minmax(360px, 440px);
                margin: 0 auto;
                max-width: 1180px;
                min-height: 100vh;
                padding: 2rem;
            }

            .auth-story {
                padding: 2rem 1rem 2rem 0;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .auth-kicker {
                color: #8f4627;
                display: inline-block;
                font-size: .78rem;
                font-weight: 800;
                letter-spacing: .14em;
                margin-bottom: 1rem;
                text-transform: uppercase;
            }

            .auth-title {
                font-size: clamp(2.6rem, 6vw, 4.8rem);
                font-weight: 700;
                letter-spacing: -.05em;
                line-height: .96;
                margin: 0 0 1rem;
                max-width: 10ch;
            }

            .auth-copy {
                color: #5f5f5f;
                font-size: 1rem;
                line-height: 1.8;
                margin: 0 0 1.5rem;
                max-width: 58ch;
            }

            .auth-points {
                display: grid;
                gap: .85rem;
                margin: 0;
                padding: 0;
            }

            .auth-point {
                align-items: center;
                color: #2b2b2b;
                display: flex;
                gap: .75rem;
            }

            .auth-point-mark {
                align-items: center;
                background: #111111;
                border-radius: 999px;
                color: #ffffff;
                display: inline-flex;
                font-size: .8rem;
                font-weight: 700;
                height: 28px;
                justify-content: center;
                width: 28px;
            }

            .auth-card {
                background: rgba(255, 255, 255, .94);
                border: 1px solid rgba(17, 17, 17, .08);
                border-radius: 28px;
                box-shadow: 0 28px 60px rgba(17, 17, 17, .10);
                padding: 1.75rem;
                position: relative;
            }

            .auth-brand {
                align-items: center;
                color: #111111;
                display: inline-flex;
                font-size: 1.2rem;
                font-weight: 700;
                gap: .75rem;
                letter-spacing: -.03em;
                margin-bottom: 1rem;
                text-decoration: none;
            }

            .auth-brand-mark {
                align-items: center;
                background: #111111;
                border-radius: 16px;
                color: #ffffff;
                display: inline-flex;
                font-size: .9rem;
                font-weight: 800;
                height: 42px;
                justify-content: center;
                width: 42px;
            }

            .icon-close-popup {
                background: url("{{ asset('assets/images/cursor-close.svg') }}") center/24px 24px no-repeat;
                border-radius: 999px;
                display: inline-flex;
                flex-shrink: 0;
                overflow: hidden;
                text-indent: -9999px;
                transform: rotate(0deg);
                transition: opacity .18s ease;
            }

            .icon-close-popup:hover,
            .icon-close-popup:focus-visible {
                opacity: .82;
                outline: none;
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

            .auth-exit {
                display: inline-flex;
                height: 38px;
                position: absolute;
                right: 18px;
                top: 18px;
                width: 38px;
            }

            @media (max-width: 991.98px) {
                .auth-shell {
                    grid-template-columns: 1fr;
                    padding: 1.25rem;
                }

                .auth-story {
                    display: none !important;
                }
            }
        </style>
    </head>
    <body>
        @include('partials.preloader')
        <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:rounded-md focus:bg-black focus:px-4 focus:py-2 focus:text-white">Skip to main content</a>
        <div class="auth-shell">
            <section class="auth-story d-none d-lg-flex align-items-center justify-content-center">
                <img src="{{ $authImage }}" class="img-fluid auth-cover-img" style="max-width: 100%; height: auto; max-height: 500px;" alt="{{ $authAlt }}">
            </section>

            <main id="main-content" class="auth-card">
                <a href="{{ url('/') }}" class="auth-exit icon-close-popup" aria-label="Close authentication and return to site">Close</a>
                <a href="{{ url('/') }}" class="auth-brand">
                    <span class="auth-brand-mark">K</span>
                    <span>{{ config('app.name', 'Kiosk') }}</span>
                </a>
                {{ $slot }}
            </main>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.icon-close-popup').forEach(function (icon) {
                    icon.addEventListener('mouseenter', function () {
                        icon.classList.remove('is-rotating-out');
                        void icon.offsetWidth;
                        icon.classList.add('is-rotating-in');
                    });

                    icon.addEventListener('mouseleave', function () {
                        icon.classList.remove('is-rotating-in');
                        void icon.offsetWidth;
                        icon.classList.add('is-rotating-out');
                    });
                });
            });
        </script>
        @stack('scripts')
        @include('partials.preloader-scripts')
    </body>
</html>
