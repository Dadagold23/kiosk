@extends('layouts.frontend')

@section('meta_title', $title . ' | Kiosk')
@section('meta_description', $intro)

@push('styles')
<style>
    .info-page-shell {
        padding: 3rem 0 4rem;
    }

    .info-page-hero {
        display: grid;
        gap: 1.5rem;
        grid-template-columns: minmax(0, 1.15fr) minmax(280px, .85fr);
        margin-bottom: 2.5rem;
    }

    .info-page-kicker {
        color: var(--kiosk-primary-deep);
        font-size: .78rem;
        font-weight: 800;
        letter-spacing: .12em;
        margin-bottom: .9rem;
        text-transform: uppercase;
    }

    .info-page-title {
        color: var(--kiosk-ink);
        font-size: clamp(2.2rem, 5vw, 4rem);
        line-height: .98;
        margin: 0 0 1rem;
        max-width: 12ch;
    }

    .info-page-intro {
        color: var(--kiosk-text);
        font-size: 1rem;
        line-height: 1.8;
        margin: 0;
        max-width: 64ch;
    }

    .info-page-card {
        background: linear-gradient(180deg, #fffdf9 0%, #f3ebe0 100%);
        border: 1px solid rgba(17, 17, 17, .08);
        border-radius: 28px;
        padding: 1.5rem;
    }

    .info-page-cta {
        margin-top: 1.25rem;
    }

    .info-page-card h3 {
        margin-bottom: 1rem;
    }

    .info-page-list {
        display: grid;
        gap: .9rem;
        margin: 0;
        padding: 0;
    }

    .info-page-list li {
        color: var(--kiosk-text);
        list-style: none;
        line-height: 1.65;
        padding-left: 1.4rem;
        position: relative;
    }

    .info-page-list li::before {
        content: "";
        background: var(--kiosk-primary);
        border-radius: 999px;
        height: 8px;
        left: 0;
        position: absolute;
        top: .55rem;
        width: 8px;
    }

    .info-page-grid {
        display: grid;
        gap: 1.2rem;
    }

    .info-page-grid.columns-2 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .info-page-section {
        background: rgba(255, 255, 255, .88);
        border: 1px solid rgba(17, 17, 17, .08);
        border-radius: 24px;
        padding: 1.4rem 1.5rem;
    }

    .info-page-section h2 {
        font-size: 1.2rem;
        margin-bottom: .7rem;
    }

    .info-page-section p {
        color: var(--kiosk-text);
        line-height: 1.8;
        margin: 0;
    }

    .info-page-section + .info-page-section {
        margin-top: 0;
    }

    .info-page-visual {
        border-radius: 22px;
        display: block;
        margin-bottom: 1rem;
        object-fit: cover;
        width: 100%;
    }

    .info-page-spotlights {
        display: grid;
        gap: .85rem;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        margin-top: 1rem;
    }

    .info-page-spotlights img {
        border-radius: 18px;
        display: block;
        height: 170px;
        object-fit: cover;
        width: 100%;
    }

    @media (max-width: 991.98px) {
        .info-page-hero {
            grid-template-columns: 1fr;
        }

        .info-page-grid.columns-2 {
            grid-template-columns: 1fr;
        }

        .info-page-title {
            max-width: none;
        }
    }
</style>
@endpush

@section('content')
<section class="info-page-shell">
    <div class="container">
        <div class="info-page-hero">
            <div>
                <div class="info-page-kicker">{{ $meta['eyebrow'] ?? 'Kiosk information' }}</div>
                <h1 class="info-page-title">{{ $title }}</h1>
                <p class="info-page-intro">{{ $intro }}</p>
            </div>
            <aside class="info-page-card">
                @if(!empty($meta['hero_image']))
                    <img src="{{ $meta['hero_image'] }}" alt="{{ $title }}" class="info-page-visual">
                @endif
                <h3 class="h5">Quick notes</h3>
                <ul class="info-page-list">
                    @foreach($highlights as $highlight)
                        <li>{{ $highlight }}</li>
                    @endforeach
                </ul>
                @if(!empty($meta['cta_label']) && (!empty($meta['cta_route']) || !empty($meta['cta_url'])))
                    <div class="info-page-cta">
                        <a href="{{ !empty($meta['cta_route']) ? route($meta['cta_route']) : $meta['cta_url'] }}" class="btn btn-primary">
                            {{ $meta['cta_label'] }}
                        </a>
                    </div>
                @endif
                @if(!empty($meta['spotlight_images']))
                    <div class="info-page-spotlights">
                        @foreach($meta['spotlight_images'] as $spotlightImage)
                            @if($spotlightImage)
                                <img src="{{ $spotlightImage }}" alt="{{ $title }} spotlight">
                            @endif
                        @endforeach
                    </div>
                @endif
            </aside>
        </div>

        <div class="info-page-grid {{ count($sections) > 3 ? 'columns-2' : '' }}">
            @foreach($sections as $section)
                <article class="info-page-section">
                    <h2>{{ $section['heading'] }}</h2>
                    <p>{{ $section['body'] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endsection
