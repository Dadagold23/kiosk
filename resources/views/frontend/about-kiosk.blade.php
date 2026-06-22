@extends('layouts.frontend')

@section('meta_title', 'About Kiosk')
@section('meta_description', 'Learn how Kiosk connects shopping, services, consultancy, bookings, and emergency support
in one platform.')

@push('styles')
<style>
.kiosk-about-shell {
    padding: 3rem 0 4rem;
}

.kiosk-about-hero {
    margin-bottom: 2rem;
    text-align: center;
}

.kiosk-about-hero h1 {
    margin-bottom: .85rem;
}

.kiosk-about-copy {
    color: var(--kiosk-text);
    font-size: 1rem;
    line-height: 1.8;
    margin: 0 auto;
    max-width: 68ch;
}

.kiosk-about-banner img,
.kiosk-branches-card img {
    border-radius: 28px;
    display: block;
    height: 100%;
    object-fit: cover;
    width: 100%;
}

.kiosk-about-block+.kiosk-about-block {
    margin-top: 3rem;
}

.kiosk-about-lead {
    color: var(--kiosk-text);
    line-height: 1.8;
}

.kiosk-branches-grid {
    display: grid;
    gap: 1.25rem;
    grid-template-columns: repeat(2, minmax(0, 1fr));
}

.kiosk-branches-card {
    background: rgba(255, 255, 255, .9);
    border: 1px solid rgba(17, 17, 17, .08);
    border-radius: 28px;
    overflow: hidden;
}

.kiosk-branches-body {
    padding: 1.2rem 1.25rem 1.35rem;
}

.kiosk-branches-body p {
    color: var(--kiosk-text);
    line-height: 1.7;
    margin-bottom: .65rem;
}

.kiosk-about-surface {
    background: linear-gradient(180deg, #fffdf9 0%, #f3ebe0 100%);
    border: 1px solid rgba(17, 17, 17, .08);
    border-radius: 28px;
    padding: 1.25rem;
}

.kiosk-about-surface.split-layout {
    align-items: stretch;
    display: grid;
    gap: 1.25rem;
    grid-template-columns: minmax(0, 1.1fr) minmax(280px, .9fr);
    min-height: 320px;
}

@media (max-width: 991.98px) {
    .kiosk-branches-grid {
        grid-template-columns: 1fr;
    }

    .kiosk-about-surface.split-layout {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush

@section('content')
<section class="kiosk-about-shell">
    <div class="container">
        <div class="kiosk-about-hero">
            <h1>About Kiosk</h1>
            <p class="kiosk-about-copy">Kiosk brings shopping, service requests, consultancy, reservations, and
                emergency support together so customers can handle everything in one place.</p>
        </div>

        <div class="kiosk-about-banner kiosk-about-block">
            <div class="kiosk-about-surface split-layout">
                <div>
                    <h2 class="mb-2">One Platform - One place for shopping and support</h2>
                    <p class="kiosk-about-copy text-start mx-0 mb-0">Instead of splitting shopping, bookings, and
                        support into separate systems, Kiosk keeps them together so it is easier to follow up when you
                        need to.</p>
                </div>
                @if(!empty($heroImage))
                <div>
                    <img src="{{ $heroImage }}" alt="Kiosk platform overview">
                </div>
                @endif
            </div>
        </div>

        <div class="row align-items-center gy-4 kiosk-about-block">
            <div class="col-md-6">
                <h2 class="text-capitalize">A simpler way to handle products, support, and bookings</h2>
            </div>
            <div class="col-md-6">
                <p class="kiosk-about-lead mb-0">A visitor can browse products, send a service request, make a payment,
                    check a booking, or revisit an older support record without losing track of what they already did.
                </p>
            </div>
        </div>

        <div class="kiosk-about-block">
            @include('partials.amerce.about-stats', ['stats' => $stats])
        </div>

        <div class="kiosk-about-block">
            @include('partials.amerce.about-accordion', ['items' => $accordions])
        </div>

        <div class="kiosk-about-block kiosk-about-surface">
            @if(!empty($bannerImage))
            <div class="mb-4">
                <img src="{{ $bannerImage }}" alt="Kiosk partner network">
            </div>
            @endif
            <div class="d-flex justify-content-between align-items-end gap-3 flex-wrap mb-4">
                <div>
                    <h2 class="mb-2">Trusted look and feel - Partners and support network</h2>
                    <p class="kiosk-about-copy text-start mx-0">These are the kinds of partner groups and support
                        channels that help Kiosk run day to day.</p>
                </div>
            </div>
            @include('partials.amerce.brand-strip', ['brands' => $brands])
        </div>

        <div class="kiosk-about-block">
            <div class="row g-4 align-items-start">
                <div class="col-lg-7">
                    <h2 class="mb-3">Customer perspective - What users value most</h2>
                    @include('partials.amerce.testimonial-cards', ['items' => $testimonials])
                </div>
                <div class="col-lg-5">
                    <h2 class="mb-3">Teams behind the work - Operational roles inside Kiosk</h2>
                    @include('partials.amerce.team-grid', ['members' => $team])
                </div>
            </div>
        </div>
    </div>
</section>
@endsection