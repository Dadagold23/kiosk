@php
    $includeFonts = $includeFonts ?? true;
    $includeIcomoon = $includeIcomoon ?? false;
    $includeBootstrap = $includeBootstrap ?? false;
    $includeBootstrapIcons = $includeBootstrapIcons ?? false;
    $stylesheets = $stylesheets ?? [];
@endphp

@if($includeFonts)
    <link rel="stylesheet" href="{{ asset('assets/fonts/fonts.css') }}">
@endif

@if($includeIcomoon)
    <link rel="stylesheet" href="{{ asset('assets/icon/icomoon/style.css') }}">
@endif

@if($includeBootstrap)
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
@endif

@if($includeBootstrapIcons)
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
@endif

@foreach($stylesheets as $stylesheet)
    <link rel="stylesheet" href="{{ asset($stylesheet) }}">
@endforeach
