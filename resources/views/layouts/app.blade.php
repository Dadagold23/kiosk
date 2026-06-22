<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @include('partials.seo-meta', [
            'title' => config('app.name', 'Kiosk') . ' Account',
            'description' => 'Manage your account profile and credentials on Kiosk.',
            'keywords' => 'Kiosk account, profile settings, security',
            'robots' => 'noindex, nofollow',
            'themeColor' => '#111827',
        ])
        @include('partials.assets.ui-head', [
            'includeFonts' => true,
            'includeIcomoon' => true,
            'includeBootstrapIcons' => true,
        ])

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            :root {
                --app-type-scale: .93;
            }

            html {
                font-size: calc(16px * var(--app-type-scale));
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        @include('partials.preloader')
        <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:rounded-md focus:bg-indigo-600 focus:px-4 focus:py-2 focus:text-white">Skip to main content</a>
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main id="main-content">
                {{ $slot }}
            </main>
        </div>
        @include('partials.preloader-scripts')
    </body>
</html>
