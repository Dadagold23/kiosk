@extends('layouts.frontend')

@section('meta_title', 'Kiosk | Smart Shopping, Services, Consultancy, Booking & Emergency')
@section('meta_description', 'Discover Kiosk for beautifully presented shopping, service requests, consultancy access,
reservations, and emergency support.')
@section('meta_keywords', 'Kiosk Nigeria, online shopping, global sourcing, service booking, consultancy request,
reservation platform, emergency support')
@section('og_title', 'Kiosk | Shopping, Services, Consultancy, Booking & Emergency')
@section('og_description', 'Browse products, request services, access consultancy, manage bookings, and reach emergency
support from one platform.')
@section('twitter_title', 'Kiosk | Smart Commerce and Service Platform')
@section('twitter_description', 'One thoughtfully designed platform for products, bookings, consultancy, and emergency
support.')

@section('structured_data')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "WebSite",
    "name": "Kiosk",
    "url": "{{ url('/') }}",
    "description": "A digital storefront for commerce, services, consultancy, booking, and emergency support.",
    "potentialAction": {
        "@@type": "SearchAction",
        "target": "{{ route('shop.index') }}?search={search_term_string}",
        "query-input": "required name=search_term_string"
    }
}
</script>
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "Organization",
    "name": "Kiosk",
    "url": "{{ url('/') }}",
    "logo": "{{ asset(config('kiosk.assets.meta_image')) }}"
}
</script>
@endsection

@push('styles')
<style>
.home-curated {
    padding: 1.6rem 0 0;
}

.home-curated-shell {
    padding: 0 clamp(1rem, 2vw, 2rem);
}

.landing-intro {
    background: linear-gradient(135deg, rgba(255, 250, 244, .96) 0%, rgba(247, 240, 231, .92) 100%);
    border: 1px solid rgba(176, 143, 121, .18);
    border-radius: 32px;
    display: grid;
    gap: 1.4rem;
    padding: 1.6rem;
}

.landing-top-grid {
    align-items: start;
    display: grid;
    gap: 1.5rem;
    grid-template-columns: minmax(0, .92fr) minmax(0, 1.08fr);
    margin-bottom: 2rem;
}

.landing-kicker {
    color: var(--kiosk-primary-deep);
    font-size: .78rem;
    font-weight: 800;
    letter-spacing: .14em;
    text-transform: uppercase;
}

.landing-title {
    font-size: clamp(2.4rem, 6vw, 4.8rem);
    line-height: .98;
    margin: 0;
    max-width: 12ch;
}

.landing-copy {
    color: var(--kiosk-text);
    font-size: 1.02rem;
    line-height: 1.72;
    margin: 0;
    max-width: 62ch;
}

.landing-route-list {
    display: grid;
    gap: .9rem;
    grid-template-columns: repeat(4, minmax(0, 1fr));
}

.landing-route {
    background: rgba(255, 255, 255, .58);
    border: 1px solid rgba(176, 143, 121, .12);
    border-radius: 20px;
    padding: 1rem;
}

.landing-route strong {
    color: var(--kiosk-ink);
    display: block;
    font-family: "Space Grotesk", sans-serif;
    font-size: 1rem;
    margin-bottom: .2rem;
}

.landing-route p {
    color: var(--kiosk-muted);
    font-size: .92rem;
    line-height: 1.55;
    margin: 0 0 .6rem;
    max-width: 24ch;
}

.landing-hero-slider {
    aspect-ratio: 1.18/1;
    border: 1px solid rgba(176, 143, 121, .18);
    border-radius: 32px;
    min-height: 520px;
    overflow: hidden;
    position: relative;
}

.landing-hero-slide {
    inset: 0;
    opacity: 0;
    position: absolute;
    transform: scale(1.02);
    transition: opacity .45s ease, transform .7s ease;
}

.landing-hero-slide.is-active {
    opacity: 1;
    transform: scale(1);
    z-index: 2;
}

.landing-hero-slide img {
    height: 100%;
    object-fit: cover;
    width: 100%;
}

.landing-hero-overlay {
    background: linear-gradient(180deg, rgba(18, 16, 15, .08) 0%, rgba(18, 16, 15, .72) 100%);
    inset: 0;
    position: absolute;
}

.landing-hero-copy {
    bottom: 0;
    color: #fff;
    left: 0;
    padding: 1.3rem 1.35rem;
    position: absolute;
    width: 100%;
    z-index: 3;
}

.landing-hero-copy h2,
.landing-hero-copy p,
.landing-hero-copy a {
    color: #fff;
}

.landing-hero-copy h2 {
    font-size: clamp(1.5rem, 2vw, 2.2rem);
    margin-bottom: .45rem;
    max-width: 14ch;
}

.landing-hero-copy p {
    font-size: .94rem;
    line-height: 1.55;
    margin-bottom: .85rem;
    max-width: 42ch;
}

.landing-hero-badge {
    background: rgba(255, 255, 255, .12);
    border: 1px solid rgba(255, 255, 255, .16);
    color: #fff;
    display: inline-flex;
    font-size: .74rem;
    font-weight: 800;
    letter-spacing: .08em;
    margin-bottom: .75rem;
    padding: .45rem .7rem;
    text-transform: uppercase;
}

.landing-hero-dots {
    display: flex;
    gap: .45rem;
    margin-top: 1rem;
}

.landing-hero-dot {
    background: rgba(32, 24, 21, .18);
    border: 0;
    border-radius: 999px;
    height: 7px;
    padding: 0;
    transition: width .25s ease, background-color .25s ease;
    width: 26px;
}

.landing-hero-dot.is-active {
    background: var(--kiosk-primary);
    width: 46px;
}

.section-shell {
    padding-top: 0;
    padding-bottom: 2.6rem;
}

.section-shell.is-soft {
    background: rgba(255, 250, 244, .5);
    border: 1px solid rgba(176, 143, 121, .14);
    border-radius: 32px;
    padding: 1.5rem;
}

.section-heading {
    margin-bottom: 1.55rem;
    color: var(--kiosk-muted);
    font-size: .98rem;
    line-height: 1.62;
    margin-bottom: 1.55rem;
    max-width: 58ch;
}

.section-heading.is-centered {
    text-align: center;
}

.section-heading.is-centered {
    margin-inline: auto;
}

.product-item,
.service-entry,
.category-entry,
.benefit-item,
.gallery-entry {
    background: transparent;
    border: 0;
    border-radius: 0;
    overflow: hidden;
}

.action-row {
    align-items: center;
    display: flex;
    flex-wrap: wrap;
    gap: .75rem;
}

.action-row .btn {
    white-space: nowrap;
}

.category-grid {
    display: grid;
    gap: 1rem;
    grid-template-columns: repeat(6, minmax(0, 1fr));
}

.category-entry {
    padding: .2rem 0;
    text-align: left;
}

.category-visual {
    aspect-ratio: 1/1;
    background: #f4ede5;
    border-radius: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
    overflow: hidden;
    position: relative;
}

.category-visual img {
    height: 100%;
    object-fit: cover;
    transition: transform .3s ease;
    width: 100%;
}

.category-entry:hover .category-visual img,
.category-entry:focus-visible .category-visual img {
    transform: scale(1.04);
}

.category-visual-empty {
    align-items: center;
    background: radial-gradient(circle at top, rgba(228, 193, 160, .6) 0%, rgba(244, 237, 229, .96) 45%, rgba(236, 224, 210, .98) 100%);
    display: flex;
    height: 100%;
    justify-content: center;
    width: 100%;
}

.category-visual-empty i {
    color: #7a5b3f;
    font-size: 1.45rem;
}

.category-visual-overlay {
    align-items: center;
    background: linear-gradient(180deg, rgba(20, 16, 14, .08) 0%, rgba(20, 16, 14, .58) 100%);
    color: #fff;
    display: flex;
    gap: .45rem;
    inset: 0;
    justify-content: center;
    opacity: 0;
    position: absolute;
    transition: opacity .25s ease;
}

.category-entry:hover .category-visual-overlay,
.category-entry:focus-visible .category-visual-overlay {
    opacity: 1;
}

.category-visual-overlay i {
    font-size: 1.1rem;
}

.category-visual-overlay span {
    font-size: .82rem;
    font-weight: 800;
    letter-spacing: .08em;
    text-transform: uppercase;
}

.product-grid {
    display: grid;
    gap: 1.25rem;
    grid-template-columns: repeat(4, minmax(0, 1fr));
}

.product-item {
    background: rgba(255, 253, 249, .78);
    border: 1px solid rgba(176, 143, 121, .18);
    border-radius: 22px;
    padding: .8rem;
}

.product-item-media {
    background: linear-gradient(180deg, #faf4ee 0%, #efe4d8 100%);
    aspect-ratio: 3/3.7;
    border-radius: 16px;
    overflow: hidden;
}

.product-item-media img {
    height: 100%;
    object-fit: cover;
    transition: transform .3s ease;
    width: 100%;
}

.product-item:hover .product-item-media img {
    transform: scale(1.04);
}

.product-item-body {
    padding: .85rem .15rem 0;
}

.product-item-actions {
    display: grid;
    gap: .6rem;
    max-height: 0;
    opacity: 0;
    overflow: hidden;
    pointer-events: none;
    transform: translateY(8px);
    transition: max-height .25s ease, opacity .2s ease, transform .2s ease;
}

.product-item:hover .product-item-actions,
.product-item:focus-within .product-item-actions {
    max-height: 180px;
    opacity: 1;
    pointer-events: auto;
    transform: translateY(0);
}

@media (hover: none) {
    .product-item-actions {
        max-height: 180px;
        opacity: 1;
        overflow: visible;
        pointer-events: auto;
        transform: none;
    }
}

.product-item-price {
    color: var(--kiosk-primary-deep);
    font-family: "Space Grotesk", sans-serif;
    font-size: 1.02rem;
}

.product-item-source {
    color: var(--kiosk-muted);
    display: block;
    font-size: .8rem;
    margin-top: .18rem;
}

.benefit-grid {
    display: grid;
    gap: 1rem;
    grid-template-columns: repeat(4, minmax(0, 1fr));
}

.benefit-item {
    background: rgba(255, 253, 249, .84);
    border: 1px solid rgba(176, 143, 121, .16);
    border-radius: 24px;
    padding: 1.25rem;
    position: relative;
}

.benefit-item i {
    color: var(--kiosk-primary);
    font-size: 1.45rem;
}

.benefit-item p {
    font-size: .94rem;
    line-height: 1.6;
}

.service-grid {
    display: grid;
    gap: 1.5rem;
    grid-template-columns: repeat(3, minmax(0, 1fr));
}

.service-entry {
    background: rgba(255, 253, 249, .84);
    border: 1px solid rgba(176, 143, 121, .16);
    border-radius: 24px;
    padding: 1.35rem;
}

.service-entry-media {
    aspect-ratio: 16/11;
    border-radius: 18px;
    margin-bottom: 1rem;
    overflow: hidden;
    position: relative;
}

.service-entry-media img {
    height: 100%;
    object-fit: cover;
    width: 100%;
}

.service-entry-chip {
    background: rgba(255, 255, 255, .92);
    border-radius: 18px;
    bottom: .85rem;
    box-shadow: 0 16px 36px rgba(20, 16, 15, .12);
    padding: .5rem;
    position: absolute;
    right: .85rem;
}

.service-entry-chip img {
    display: block;
    height: 56px;
    object-fit: contain;
    width: 56px;
}

.service-entry .icon-box {
    margin-bottom: 1rem;
}

.service-entry p {
    font-size: .95rem;
    line-height: 1.58;
    max-width: 36ch;
}

.gallery-grid {
    display: grid;
    gap: 1rem;
    grid-template-columns: repeat(5, minmax(0, 1fr));
}

.gallery-entry {
    aspect-ratio: 1/1;
    border-radius: 24px;
    overflow: hidden;
}

.gallery-entry img {
    height: 100%;
    object-fit: cover;
    transition: transform .3s ease;
    width: 100%;
}

.gallery-entry:hover img,
.gallery-entry:focus-visible img {
    transform: scale(1.04);
}

.gallery-empty {
    background: rgba(255, 253, 249, .84);
    border: 1px solid rgba(176, 143, 121, .16);
    border-radius: 24px;
    padding: 2rem;
}

.catalog-lane-grid,
.collection-grid,
.lookbook-grid,
.journal-grid,
.experience-gallery-grid {
    display: grid;
    gap: 1rem;
}

.catalog-lane-grid {
    grid-template-columns: repeat(3, minmax(0, 1fr));
}

.collection-grid {
    grid-template-columns: repeat(3, minmax(0, 1fr));
}

.lookbook-grid {
    grid-template-columns: repeat(4, minmax(0, 1fr));
}

.journal-grid {
    grid-template-columns: repeat(3, minmax(0, 1fr));
}

.experience-gallery-grid {
    grid-template-columns: repeat(6, minmax(0, 1fr));
}

.catalog-lane-card,
.collection-card,
.journal-card {
    background: rgba(255, 253, 249, .86);
    border: 1px solid rgba(176, 143, 121, .16);
    border-radius: 24px;
    overflow: hidden;
}

.catalog-lane-card {
    display: flex;
    gap: 1rem;
    padding: 1.15rem;
}

.catalog-lane-icon {
    align-items: center;
    background: #fff4ea;
    border-radius: 18px;
    display: flex;
    flex: 0 0 72px;
    height: 72px;
    justify-content: center;
    padding: 1rem;
}

.catalog-lane-icon img {
    height: 40px;
    object-fit: contain;
    width: 40px;
}

.collection-card img,
.lookbook-frame img,
.journal-media img,
.experience-gallery-grid img {
    display: block;
    width: 100%;
}

.collection-card img,
.journal-media img {
    height: 240px;
    object-fit: cover;
}

.collection-body,
.journal-body {
    padding: 1.15rem 1.2rem 1.25rem;
}

.lookbook-frame {
    position: relative;
    border-radius: 24px;
    overflow: hidden;
    background: linear-gradient(180deg, #faf4ee 0%, #efe4d8 100%);
}

.lookbook-frame img {
    aspect-ratio: 4/5;
    object-fit: cover;
}

.lookbook-caption {
    background: rgba(255, 255, 255, .92);
    border-radius: 18px;
    bottom: 1rem;
    color: var(--kiosk-ink);
    font-size: .88rem;
    left: 1rem;
    max-width: 80%;
    padding: .65rem .8rem;
    position: absolute;
}

.journal-meta {
    align-items: center;
    display: flex;
    gap: .75rem;
    margin-top: 1rem;
}

.journal-meta img {
    border-radius: 50%;
    height: 44px;
    object-fit: cover;
    width: 44px;
}

.experience-gallery-grid img {
    aspect-ratio: 1/1;
    border-radius: 20px;
    object-fit: cover;
}

.media-spotlight {
    display: grid;
    gap: 1.25rem;
    grid-template-columns: minmax(0, 1.15fr) minmax(280px, .85fr);
}

.media-spotlight-video,
.media-spotlight-copy {
    background: rgba(255, 253, 249, .86);
    border: 1px solid rgba(176, 143, 121, .16);
    border-radius: 28px;
    overflow: hidden;
}

.media-spotlight-video video {
    display: block;
    height: 100%;
    min-height: 340px;
    object-fit: cover;
    width: 100%;
}

.media-spotlight-copy {
    padding: 1.35rem;
}

.media-spotlight-grid {
    display: grid;
    gap: .9rem;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    margin: 0 0 1.2rem;
}

.media-spotlight-tile {
    position: relative;
    overflow: hidden;
    min-height: 140px;
    border-radius: 22px;
    background: rgba(255, 255, 255, .72);
    border: 1px solid rgba(176, 143, 121, .14);
}

.media-spotlight-tile img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform .35s ease;
}

.media-spotlight-tile span {
    position: absolute;
    left: 12px;
    bottom: 12px;
    display: inline-flex;
    align-items: center;
    padding: 6px 10px;
    border-radius: 999px;
    background: rgba(255, 255, 255, .9);
    color: var(--kiosk-ink);
    font-size: .78rem;
    font-weight: 700;
    box-shadow: 0 10px 24px rgba(15, 23, 42, .08);
}

.media-spotlight-tile:hover img,
.media-spotlight-tile:focus-visible img {
    transform: scale(1.04);
}

.eyebrow-light {
    color: var(--kiosk-primary-deep);
    font-size: .78rem;
    font-weight: 800;
    letter-spacing: .12em;
    text-transform: uppercase;
}

@media (max-width: 1199.98px) {
    .landing-top-grid {
        grid-template-columns: 1fr;
    }

    .landing-route-list {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .category-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .product-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .service-grid {
        grid-template-columns: 1fr;
    }

    .gallery-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .catalog-lane-grid,
    .collection-grid,
    .journal-grid {
        grid-template-columns: 1fr 1fr;
    }

    .lookbook-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .experience-gallery-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .media-spotlight {
        grid-template-columns: 1fr;
    }

    .media-spotlight-grid {
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }
}

@media (max-width: 991.98px) {
    .landing-hero-slider {
        min-height: 440px;
    }

    .landing-route-list {
        grid-template-columns: 1fr;
    }

    .benefit-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .section-shell {
        padding-bottom: 2.2rem;
    }

    .catalog-lane-grid,
    .collection-grid,
    .journal-grid,
    .experience-gallery-grid {
        grid-template-columns: 1fr 1fr;
    }

    .media-spotlight-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 767.98px) {
    .home-curated-shell {
        padding: 0 .85rem;
    }

    .landing-route-list,
    .category-grid,
    .product-grid,
    .gallery-grid,
    .benefit-grid,
    .catalog-lane-grid,
    .collection-grid,
    .lookbook-grid,
    .journal-grid,
    .experience-gallery-grid,
    .media-spotlight-grid {
        justify-items: center;
    }

    .landing-intro {
        padding: 1.2rem;
    }

    .landing-hero-slider {
        min-height: 360px;
        margin-inline: auto;
        width: min(100%, 24rem);
    }

    .section-heading {
        font-size: .92rem;
        line-height: 1.55;
        margin-bottom: 1.2rem;
    }

    .category-grid,
    .product-grid,
    .gallery-grid,
    .benefit-grid,
    .catalog-lane-grid,
    .collection-grid,
    .lookbook-grid,
    .journal-grid,
    .experience-gallery-grid {
        grid-template-columns: 1fr 1fr;
    }

    .media-spotlight-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .product-item {
        padding: .65rem;
    }

    .landing-copy {
        font-size: .94rem;
        line-height: 1.65;
    }

    .action-row {
        align-items: stretch;
        flex-direction: column;
    }

    .action-row .btn {
        width: 100%;
    }

    .service-entry p {
        max-width: none;
    }

    .catalog-lane-card {
        align-items: flex-start;
        flex-direction: column;
    }

    .lookbook-frame {
        border-radius: 18px;
    }

    .lookbook-frame img {
        aspect-ratio: 4 / 4.25;
        object-fit: contain;
        padding: .45rem;
    }

    .lookbook-caption {
        bottom: .7rem;
        left: .7rem;
        font-size: .78rem;
        max-width: calc(100% - 1.4rem);
        padding: .5rem .65rem;
    }
}

@media (max-width: 575.98px) {
    .landing-hero-slider {
        border-radius: 24px;
        min-height: 320px;
        width: min(100%, 22.5rem);
    }

    .landing-hero-copy {
        padding: 1rem 1rem .95rem;
    }

    .landing-hero-copy h2 {
        font-size: 1.3rem;
    }

    .landing-hero-copy p {
        font-size: .88rem;
        margin-bottom: .7rem;
    }

    .landing-route,
    .category-entry,
    .product-item,
    .benefit-item,
    .service-entry,
    .catalog-lane-card,
    .collection-card,
    .lookbook-frame,
    .journal-card,
    .gallery-entry,
    .media-spotlight-tile {
        margin-inline: auto;
        width: min(100%, 24rem);
    }

    .experience-gallery-grid img {
        margin-inline: auto;
        width: min(100%, 24rem);
    }

    .category-grid,
    .product-grid,
    .gallery-grid,
    .benefit-grid,
    .catalog-lane-grid,
    .collection-grid,
    .lookbook-grid,
    .journal-grid,
    .experience-gallery-grid {
        grid-template-columns: 1fr;
    }

    .media-spotlight-grid {
        grid-template-columns: 1fr 1fr;
    }

    .lookbook-frame img {
        aspect-ratio: 16 / 13;
        padding: .38rem;
    }

    .lookbook-caption {
        font-size: .74rem;
    }
}
</style>
@endpush

@section('content')
@php
$galleryProducts = $featuredProducts->filter(fn ($product) => filled($product->uploaded_image_url))->take(5);
$spotlightImages = $experienceGallery->take(6);
$bookingTypes = ['Hotels', 'Resorts', 'Lounges', 'Parks', 'Flights'];
$benefits = [
['icon' => 'bi-arrow-counterclockwise', 'title' => 'Clear request tracking', 'copy' => 'Orders, services, consultancy, and bookings stay visible from one account.'], ['icon' => 'bi-truck', 'title' => 'Local and sourced products', 'copy' => 'Switch between in-stock items and assisted sourcing without changing your flow.'], ['icon' => 'bi-headset', 'title' => 'Responsive support desk', 'copy' => 'Every module feeds into one steady customer support process.'], ['icon' => 'bi-shield-check', 'title' => 'Cleaner visual handling', 'copy' => 'Real uploaded media leads the interface while product cards still fail safely when needed.'],
];
@endphp

<section class="home-curated">
    <div class="home-curated-shell">
        <div class="landing-top-grid mb-5" data-aos="fade-up">
            <section class="landing-intro">
                <h1 class="landing-title">Shop, request services, and book what you need in one place.</h1>
                <p class="landing-copy">Browse products, ask for help, make bookings, and follow your updates without
                    jumping between different pages or systems.</p>
                <div class="action-row">
                    <a href="{{ route('shop.index') }}" class="btn btn-primary btn-lg">Shop Now</a>
                    <a href="{{ route('services.index') }}" class="btn btn-outline-primary btn-lg">View Services</a>
                </div>
                <div class="landing-route-list">
                    <div class="landing-route">
                        <strong>Browse products</strong>
                        <p>See featured items, categories, and prices at a glance.</p>
                        <a href="{{ route('shop.index') }}" class="module-link">View Shop</a>
                    </div>
                    <div class="landing-route">
                        <strong>Request services</strong>
                        <p>Find support, advisory help, and everyday services without any guesswork.</p>
                        <a href="{{ route('consultancy.index') }}" class="module-link">View Options</a>
                    </div>
                    <div class="landing-route">
                        <strong>Plan bookings</strong>
                        <p>Handle travel, hospitality, and reservations from the same account.</p>
                        <a href="{{ route('booking.index') }}" class="module-link">View Booking</a>
                    </div>
                    <div class="landing-route">
                        <strong>Reach urgent help</strong>
                        <p>Reach emergency support quickly when something urgent comes up.</p>
                        <a href="{{ route('emergency.index') }}" class="module-link">Emergency Support</a>
                    </div>
                </div>
            </section>

            @if($heroSlides->isNotEmpty())
            <section class="landing-hero-slider" data-landing-slider>
                @foreach($heroSlides as $slide)
                <article class="landing-hero-slide {{ $loop->first ? 'is-active' : '' }}" data-landing-slide>
                    <img src="{{ $slide['image_url'] }}" alt="{{ $slide['title'] }}">
                    <div class="landing-hero-overlay"></div>
                    <div class="landing-hero-copy">
                        <span class="landing-hero-badge">{{ $slide['category'] }}</span>
                        <h2>{{ $slide['title'] }}</h2>
                        <p>{{ $slide['summary'] }}</p>
                        <a href="{{ $slide['href'] }}" class="btn btn-outline-light">Browse Feature</a>
                    </div>
                </article>
                @endforeach
                <div class="landing-hero-dots">
                    @foreach($heroSlides as $slide)
                    <button type="button" class="landing-hero-dot {{ $loop->first ? 'is-active' : '' }}"
                        data-landing-dot aria-label="View slide {{ $loop->iteration }}"></button>
                    @endforeach
                </div>
            </section>
            @endif
        </div>

        @if($productCategories->isNotEmpty())
        <section class="section-shell pt-0">
            <h2 class="mb-2 text-center">CATEGORIES - Browse by category</h2>
            <p class="section-heading is-centered">Pick a category first if you want a faster way to browse the catalog.
            </p>
            <div class="category-grid">
                @foreach($productCategories as $category)
                <a href="{{ route('shop.index', ['category' => $category->slug]) }}" class="category-entry"
                    data-aos="fade-up" data-aos-delay="{{ ($loop->index % 6) * 40 }}">
                    <div class="category-visual">
                        @if($category->category_image_url)
                        <img src="{{ $category->category_image_url }}" alt="{{ $category->name }}">
                        @else
                        <span class="category-visual-empty" aria-hidden="true">
                            <i class="bi bi-grid-1x2-fill"></i>
                        </span>
                        @endif
                        <span class="category-visual-overlay">
                            <i class="bi bi-eye"></i>
                            <span>View</span>
                        </span>
                    </div>
                    <h3 class="h6 mb-1">{{ $category->name }}</h3>
                    <p class="text-muted small mb-0">{{ number_format($category->products_count) }}
                        item{{ $category->products_count === 1 ? '' : 's' }}</p>
                </a>
                @endforeach
            </div>
        </section>
        @endif

        @if($catalogHighlights->isNotEmpty())
        <section class="section-shell pt-0">
            <h2 class="mb-2">Image Library</h2>
            <p class="section-heading">We added these visuals to make browsing feel easier and a little more familiar,
                so each category is quicker to scan at a glance.</p>
            <div class="catalog-lane-grid">
                @foreach($catalogHighlights as $highlight)
                <article class="catalog-lane-card" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 60 }}">
                    <div class="catalog-lane-icon">
                        <img src="{{ $highlight['url'] }}" alt="{{ $highlight['title'] }}">
                    </div>
                    <div>
                        <h3 class="h6 mb-2">{{ $highlight['title'] }}</h3>
                        <p class="text-muted small mb-0">{{ $highlight['copy'] }}</p>
                    </div>
                </article>
                @endforeach
            </div>
        </section>
        @endif

        <section class="section-shell pt-0">
            <div class="d-flex justify-content-between align-items-end gap-3 flex-wrap mb-4">
                <div>
                    <h2 class="mb-2">Curated arrivals</h2>
                    <p class="section-heading mb-0">A few current picks from across the store, from everyday essentials
                        to new arrivals.</p>
                </div>
                <a href="{{ route('shop.index') }}" class="btn btn-outline-primary">See Full Catalog</a>
            </div>

            <div class="product-grid">
                @foreach($featuredGridItems as $item)
                <article class="product-item" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 4) * 60 }}">
                    <div class="product-item-media">
                        <img src="{{ $item['image_url'] }}" alt="{{ $item['title'] }}">
                    </div>
                    <div class="product-item-body">
                        <div class="d-flex justify-content-between align-items-center gap-2 mb-2">
                            <span class="badge badge-soft text-uppercase">{{ $item['badge'] }}</span>
                            <small class="text-muted">{{ $item['category'] }}</small>
                        </div>
                        <h3 class="h5 mb-2">{{ $item['title'] }}</h3>
                        <p class="text-muted small mb-3">{{ $item['summary'] }}</p>
                        <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
                            <div>
                                <strong
                                    class="product-item-price">{{ $item['price_label'] ?? $item['availability'] }}</strong>
                                @if(! empty($item['price_source_name']))
                                <span class="product-item-source">Source: {{ $item['price_source_name'] }}</span>
                                @endif
                            </div>
                            <a href="{{ $item['href'] }}" class="module-link">Details</a>
                        </div>
                        <div class="product-item-actions">
                            <a href="{{ $item['href'] }}" class="btn btn-outline-primary">View Product</a>
                            <a href="{{ route('shop.index') }}" class="btn btn-primary">Browse Catalog</a>
                        </div>
                    </div>
                </article>
                @endforeach
            </div>
        </section>

        @if($collectionShowcase->isNotEmpty())
        <section class="section-shell pt-0">
            <div class="d-flex justify-content-between align-items-end gap-3 flex-wrap mb-4">
                <div>
                    <h2 class="mb-2">COLLECTION - A closer look at what’s in the store</h2>
                    <p class="section-heading mb-0">These collections give you a quick way to explore related products
                        without scrolling through everything at once.</p>
                </div>
                <a href="{{ route('shop.index') }}" class="btn btn-outline-primary">Browse Catalog</a>
            </div>
            <div class="collection-grid">
                @foreach($collectionShowcase as $collection)
                <article class="collection-card" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 60 }}">
                    <img src="{{ $collection['url'] }}" alt="{{ $collection['title'] }}">
                    <div class="collection-body">
                        <h3 class="h5 mb-2">{{ $collection['title'] }}</h3>
                        <p class="text-muted mb-0">{{ $collection['copy'] }}</p>
                    </div>
                </article>
                @endforeach
            </div>
        </section>
        @endif

        <section class="section-shell is-soft pt-0">
            <h2 class="mb-2">Why choose Kiosk - Simple steps from start to finish</h2>
            <p class="section-heading">The goal is to make it easy to browse, place requests, and keep track of what
                happens next.</p>
            <div class="benefit-grid">
                @foreach($benefits as $benefit)
                <div class="benefit-item" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 4) * 50 }}">
                    <i class="bi {{ $benefit['icon'] }}"></i>
                    <h3 class="h6 fw-bold mt-3 mb-2">{{ $benefit['title'] }}</h3>
                    <p class="text-muted mb-0">{{ $benefit['copy'] }}</p>
                </div>
                @endforeach
            </div>
        </section>

        <section class="section-shell is-soft pt-0">
            <h2 class="mb-2">Service paths - Choose how you want help</h2>
            <p class="section-heading">Choose the kind of help you need and go straight to the right section.</p>

            <div class="service-grid">
                <div class="service-entry" data-aos="fade-up">
                    @if(!empty($servicePathVisuals['services']['image_url']))
                    <div class="service-entry-media">
                        <img src="{{ $servicePathVisuals['services']['image_url'] }}" alt="Services">
                        @if(!empty($servicePathVisuals['services']['accent_url']))
                        <div class="service-entry-chip">
                            <img src="{{ $servicePathVisuals['services']['accent_url'] }}" alt="">
                        </div>
                        @endif
                    </div>
                    @endif
                    <h3 class="h4 mb-2">Services</h3>
                    <p class="text-muted mb-3">Ask for technical, artisan, or professional help from one simple request
                        page.</p>
                    <ul class="rail-list mb-4">
                        @foreach($serviceCategories->take(3) as $category)
                        <li class="rail-item"><i class="bi bi-check2"></i>
                            <div>{{ $category->name }}</div>
                        </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('services.index') }}" class="btn btn-outline-primary">View Services</a>
                </div>

                <div class="service-entry" data-aos="fade-up" data-aos-delay="90">
                    @if(!empty($servicePathVisuals['consultancy']['image_url']))
                    <div class="service-entry-media">
                        <img src="{{ $servicePathVisuals['consultancy']['image_url'] }}" alt="Consultancy">
                        @if(!empty($servicePathVisuals['consultancy']['accent_url']))
                        <div class="service-entry-chip">
                            <img src="{{ $servicePathVisuals['consultancy']['accent_url'] }}" alt="">
                        </div>
                        @endif
                    </div>
                    @endif
                    <h3 class="h4 mb-2">Consultancy</h3>
                    <p class="text-muted mb-3">Get legal, business, or education advice with a guided request process.
                    </p>
                    <ul class="rail-list mb-4">
                        @foreach($consultancyCategories->take(3) as $category)
                        <li class="rail-item"><i class="bi bi-check2"></i>
                            <div>{{ $category->name }}</div>
                        </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('consultancy.index') }}" class="btn btn-outline-primary">View Consultancy</a>
                </div>

                <div class="service-entry" data-aos="fade-up" data-aos-delay="180">
                    @if(!empty($servicePathVisuals['booking']['image_url']))
                    <div class="service-entry-media">
                        <img src="{{ $servicePathVisuals['booking']['image_url'] }}" alt="Booking and support">
                        @if(!empty($servicePathVisuals['booking']['accent_url']))
                        <div class="service-entry-chip">
                            <img src="{{ $servicePathVisuals['booking']['accent_url'] }}" alt="">
                        </div>
                        @endif
                    </div>
                    @endif
                    <h3 class="h4 mb-2">Booking & support</h3>
                    <p class="text-muted mb-3">Handle bookings and urgent support from the same public site when you
                        need them.</p>
                    <ul class="rail-list mb-4">
                        @foreach($bookingTypes as $bookingType)
                        @if($loop->index < 3) <li class="rail-item"><i class="bi bi-check2"></i>
                            <div>{{ $bookingType }}</div>
                            </li>
                            @endif
                            @endforeach
                    </ul>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('booking.index') }}" class="btn btn-outline-primary">Booking</a>
                        <a href="{{ route('emergency.index') }}" class="btn btn-outline-danger">Emergency</a>
                    </div>
                </div>
            </div>
        </section>

        @if($lookbookFrames->isNotEmpty())
        <section class="section-shell pt-0">
            <h2 class="mb-2 text-center">Our Lookbook - A visual look at the catalog</h2>
            <p class="section-heading is-centered">These images give you a better feel for the products before you open
                the full listings.</p>
            <div class="lookbook-grid">
                @foreach($lookbookFrames as $frame)
                <article class="lookbook-frame" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 4) * 50 }}">
                    <img src="{{ $frame['url'] }}" alt="{{ $frame['label'] }}">
                    <div class="lookbook-caption">{{ $frame['caption'] }}</div>
                </article>
                @endforeach
            </div>
        </section>
        @endif

        @if($journalEntries->isNotEmpty())
        <section class="section-shell is-soft pt-0">
            <h2 class="mb-2">Kiosk Journal - Store updates and helpful notes</h2>
            <p class="section-heading">A few quick reads to help you shop, place requests, and understand how things
                work here.</p>
            <div class="journal-grid">
                @foreach($journalEntries as $entry)
                <article class="journal-card" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 60 }}">
                    <div class="journal-media">
                        <img src="{{ $entry['url'] }}" alt="{{ $entry['title'] }}">
                    </div>
                    <div class="journal-body">
                        <h3 class="h5 mb-2">{{ $entry['title'] }}</h3>
                        <p class="text-muted mb-0">Helpful notes and updates from the Kiosk team, kept close to the
                            storefront.</p>
                        <div class="journal-meta">
                            @if(!empty($entry['avatar_url']))
                            <img src="{{ $entry['avatar_url'] }}" alt="{{ $entry['author'] }}">
                            @endif
                            <div>
                                <strong class="d-block">{{ $entry['author'] }}</strong>
                                <span class="text-muted small">Kiosk update</span>
                            </div>
                        </div>
                    </div>
                </article>
                @endforeach
            </div>
        </section>
        @endif
    </div>
</section>

<section class="section-shell pt-0">
    <div class="home-curated-shell">
        <h2 class="mb-2 text-center">GALLERY - A quick look at the catalog</h2>
        <p class="section-heading is-centered">Real uploaded media adds warmth and credibility where the storefront
            depends most on visuals.</p>

        @if($galleryProducts->isNotEmpty())
        <div class="gallery-grid">
            @foreach($galleryProducts as $product)
            <a href="{{ route('shop.show', $product->slug) }}" class="gallery-entry">
                <img src="{{ $product->uploaded_image_url }}" alt="{{ $product->name }}">
            </a>
            @endforeach
        </div>
        @else
        <div class="gallery-empty text-center">
            <h3 class="h5 mb-2">The gallery will appear here</h3>
            <p class="text-muted mb-0">This space now waits for real uploaded catalog media instead of showing
                placeholder artwork.</p>
        </div>
        @endif
    </div>
</section>

@if($experienceGallery->isNotEmpty())
<section class="section-shell pt-0">
    <div class="home-curated-shell">
        <h2 class="mb-2 text-center">Image Library</h2>
        <p class="section-heading is-centered">We are making better use of the shared image library across the storefront,
            so the page feels easier to browse and less repetitive.</p>
        <div class="experience-gallery-grid">
            @foreach($experienceGallery as $image)
            <img src="{{ $image['url'] }}" alt="{{ $image['label'] }}">
            @endforeach
        </div>
    </div>
</section>
@endif

@if($videoFeature)
<section class="section-shell pt-0">
    <div class="home-curated-shell">
        <div class="media-spotlight">
            <div class="media-spotlight-video">
                <video autoplay muted loop playsinline @if(!empty($videoFeature['poster_url']))
                    poster="{{ $videoFeature['poster_url'] }}" @endif>
                    <source src="{{ $videoFeature['video_url'] }}" type="video/mp4">
                </video>
            </div>
            <aside class="media-spotlight-copy">
                <h2 class="mb-3">{{ $videoFeature['title'] }}</h2>
                <p class="section-heading mb-4">{{ $videoFeature['copy'] }}</p>
                @if($spotlightImages->isNotEmpty())
                <div class="media-spotlight-grid">
                    @foreach($spotlightImages as $image)
                    <div class="media-spotlight-tile">
                        <img src="{{ $image['url'] }}" alt="{{ $image['label'] }}">
                        <span>{{ $image['label'] }}</span>
                    </div>
                    @endforeach
                </div>
                @endif
                <a href="{{ route('shop.index') }}" class="btn btn-primary">Open Catalog</a>
            </aside>
        </div>
    </div>
</section>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var slider = document.querySelector('[data-landing-slider]');

    if (!slider) {
        return;
    }

    var slides = Array.from(slider.querySelectorAll('[data-landing-slide]'));
    var dots = Array.from(slider.querySelectorAll('[data-landing-dot]'));
    var index = 0;

    if (slides.length < 2) {
        return;
    }

    function activate(nextIndex) {
        index = nextIndex;

        slides.forEach(function(slide, slideIndex) {
            slide.classList.toggle('is-active', slideIndex === index);
        });

        dots.forEach(function(dot, dotIndex) {
            dot.classList.toggle('is-active', dotIndex === index);
        });
    }

    dots.forEach(function(dot, dotIndex) {
        dot.addEventListener('click', function() {
            activate(dotIndex);
        });
    });

    window.setInterval(function() {
        activate((index + 1) % slides.length);
    }, 4200);
});
</script>
@endpush
