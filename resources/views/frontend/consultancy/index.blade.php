@extends('layouts.frontend')

@section('meta_title', 'Consultancy | Kiosk')
@section('meta_description', 'Explore professional consultancy categories on Kiosk for legal, education, business, and advisory support.')
@section('meta_keywords', 'Kiosk consultancy, legal advisory, education consultancy, business consulting')

@push('styles')
<style>
    .module-shell{
        padding:2.25rem 0 5rem;
    }
    .module-hero-clean{
        background:linear-gradient(135deg, rgba(255,250,244,.96) 0%, rgba(247,240,231,.92) 100%);
        border:1px solid rgba(176, 143, 121, .18);
        border-radius:32px;
        padding:2rem;
    }
    .module-note-card{
        background:rgba(255,255,255,.72);
        border:1px solid rgba(176, 143, 121, .16);
        border-radius:26px;
        padding:1.3rem;
        height:100%;
    }
    .module-note-grid{
        display:grid;
        gap:.85rem;
        margin-top:1rem;
    }
    .module-note-item{
        align-items:flex-start;
        display:flex;
        gap:.75rem;
    }
    .module-note-item i{
        color:var(--kiosk-primary-deep);
        font-size:1rem;
        margin-top:.15rem;
    }
    .module-grid-card{
        background:rgba(255,253,249,.84);
        border:1px solid rgba(176, 143, 121, .18);
        border-radius:24px;
        height:100%;
        padding:1.35rem;
    }
    .module-grid-card .icon-box{
        align-items:center;
        background:#fff4eb;
        border:1px solid #ffd7bb;
        border-radius:18px;
        color:var(--kiosk-primary-deep);
        display:inline-flex;
        font-family:"Space Grotesk", sans-serif;
        font-size:.9rem;
        font-weight:700;
        height:52px;
        justify-content:center;
        margin-bottom:1rem;
        width:52px;
    }
    .module-grid-card p{
        line-height:1.7;
    }
</style>
@endpush

@section('content')
<section class="module-shell">
    <div class="container">
        <div class="module-hero-clean mb-4" data-aos="fade-up">
            <div class="row g-4 align-items-center">
                <div class="col-lg-7">
                    <span class="section-label">Consultancy</span>
                    <h1 class="fw-bold mb-3">Reach the right consultant with less back-and-forth</h1>
                    <p class="text-muted mb-4">
                        Send one structured request for legal, education, business, or specialist advice and follow the engagement from your account.
                    </p>
                    @auth
                        <a href="{{ route('customer.consultancy.create') }}" class="btn btn-primary btn-lg">Request Consultancy</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary btn-lg">Login to Request Consultancy</a>
                    @endauth
                </div>
                <div class="col-lg-5">
                    <div class="module-note-card">
                        <h4 class="fw-bold mb-1">How the request moves</h4>
                        <p class="text-muted mb-0">Kiosk keeps the advisory process structured so assignment, fee status, and follow-up do not drift across channels.</p>
                        <div class="module-note-grid">
                            <div class="module-note-item"><i class="bi bi-check2-circle"></i><div>Pick the category that best fits your subject.</div></div>
                            <div class="module-note-item"><i class="bi bi-check2-circle"></i><div>Share your preferred date and the full problem statement.</div></div>
                            <div class="module-note-item"><i class="bi bi-check2-circle"></i><div>Review assignment, fee status, and follow-up from one place.</div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            @foreach($categories as $category)
                <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 70 }}">
                    <div class="module-grid-card">
                        <div class="icon-box">CS</div>
                        <h4 class="fw-bold">{{ $category->name }}</h4>
                        <p class="text-muted mb-4">{{ $category->description ?: 'Professional advice with a straightforward request and follow-up process.' }}</p>
                        @auth
                            <a href="{{ route('customer.consultancy.create') }}" class="btn btn-outline-primary">Request This Category</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline-primary">Login to Continue</a>
                        @endauth
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

@endsection
