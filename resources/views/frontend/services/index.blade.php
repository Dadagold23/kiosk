@extends('layouts.frontend')

@section('meta_title', 'Services | Kiosk')
@section('meta_description', 'Browse skilled service categories on Kiosk and submit managed requests for technical, artisan, and professional support.')
@section('meta_keywords', 'Kiosk services, artisan booking, technical services, managed service request')

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
                    <span class="section-label">Services</span>
                    <h1 class="fw-bold mb-3">Request skilled support without the back-and-forth</h1>
                    <p class="text-muted mb-4">
                        Share the job once, confirm the request fee, and follow assignment and progress from one account.
                    </p>
                    @auth
                        <a href="{{ route('customer.services.create') }}" class="btn btn-primary btn-lg">Request a Service</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary btn-lg">Login to Request Service</a>
                    @endauth
                </div>
                <div class="col-lg-5">
                    <div class="module-note-card">
                        <h4 class="fw-bold mb-1">Request flow</h4>
                        <p class="text-muted mb-0">Your request, updates, and payment status stay together so it is easier to follow from start to finish.</p>
                        <div class="module-note-grid">
                            <div class="module-note-item"><i class="bi bi-check2-circle"></i><div>Choose the service area that best matches the job.</div></div>
                            <div class="module-note-item"><i class="bi bi-check2-circle"></i><div>Add location, preferred date, and any useful photos.</div></div>
                            <div class="module-note-item"><i class="bi bi-check2-circle"></i><div>Follow assignment, payment, and completion updates in your dashboard.</div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            @foreach($categories as $category)
                <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 70 }}">
                    <div class="module-grid-card">
                        <div class="icon-box">SV</div>
                        <h4 class="fw-bold">{{ $category->name }}</h4>
                        <p class="text-muted mb-4">{{ $category->description ?: 'Professional support with a simple request process and clear follow-up.' }}</p>
                        @auth
                            <a href="{{ route('customer.services.create') }}" class="btn btn-outline-primary">Request This Service</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline-primary">Login to Continue</a>
                        @endauth
                    </div>
                </div>
            @endforeach
        </div>

        @if($testimonials->isNotEmpty())
            <section class="mt-5 pt-3">
                <div class="d-flex justify-content-between align-items-end gap-3 flex-wrap mb-4">
                    <div>
                        <span class="section-label">Approved reviews</span>
                        <h2 class="mb-2">What completed service customers said</h2>
                        <p class="text-muted mb-0">Only approved reviews appear here so the public page reflects moderated customer feedback.</p>
                    </div>
                </div>

                @include('partials.reviews.public-grid', ['reviews' => $testimonials])
            </section>
        @endif
    </div>
</section>

@endsection
