@extends('layouts.frontend')

@section('meta_title', 'Kiosk Branches')
@section('meta_description', 'Explore Kiosk branch locations, operational hubs, and support points.')

@push('styles')
<style>
.kiosk-branches-shell {
    padding: 3rem 0 4rem;
}

.kiosk-branches-shell .intro {
    color: var(--kiosk-text);
    line-height: 1.8;
    max-width: 68ch;
}
</style>
@endpush

@section('content')
<section class="kiosk-branches-shell">
    <div class="container">
        <h1 class="mb-3">Kiosk Network - Kiosk Branches</h1>
        <p class="intro mb-4">These hubs represent storefront coordination, regional support intake, field follow-up,
            and digital fulfillment visibility across the Kiosk platform.</p>

        <div class="kiosk-branches-grid">
            @foreach($branches as $branch)
            <article class="kiosk-branches-card">
                @if(!empty($branch['image']))
                <div style="height:220px;position:relative;">
                    <img src="{{ $branch['image'] }}" alt="{{ $branch['name'] }}">
                    <div
                        style="align-items:flex-end;background:linear-gradient(180deg, rgba(12,12,12,.05) 0%, rgba(12,12,12,.58) 100%);color:#fff;display:flex;font-family:'Space Grotesk',sans-serif;font-size:2rem;font-weight:700;inset:0;padding:1.25rem;position:absolute;">
                        {{ $branch['code'] }}
                    </div>
                </div>
                @else
                <div
                    style="align-items:flex-end;background:linear-gradient(135deg,#fff8f0 0%,#f0e2d1 100%);color:var(--kiosk-primary-deep);display:flex;font-family:'Space Grotesk',sans-serif;font-size:2rem;font-weight:700;height:220px;padding:1.25rem;">
                    {{ $branch['code'] }}
                </div>
                @endif
                <div class="kiosk-branches-body">
                    <h3 class="h5 mb-2">{{ $branch['name'] }}</h3>
                    <p>{{ $branch['summary'] }}</p>
                    <div class="small text-muted text-uppercase">{{ $branch['address'] }}</div>
                </div>
            </article>
            @endforeach
        </div>
    </div>
</section>
@endsection