@extends('layouts.frontend')

@section('meta_title', 'Bookings | Kiosk')
@section('meta_description', 'Request hotel, resort, lounge, park, and flight bookings through the Kiosk assisted reservation workflow.')
@section('meta_keywords', 'Kiosk booking, hotel reservation, resort booking, flight assistance')

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
                    <span class="section-label">Bookings</span>
                    <h1 class="fw-bold mb-3">Send your reservation request in one place</h1>
                    <p class="text-muted mb-4">
                        From hotels and resorts to flights and lounges, Kiosk keeps your request, payment, and confirmation details together.
                    </p>
                    @auth
                        <a href="{{ route('customer.bookings.create') }}" class="btn btn-primary btn-lg">Start Booking</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary btn-lg">Login to Book</a>
                    @endauth
                </div>
                <div class="col-lg-5">
                    <div class="module-note-card">
                        <h4 class="fw-bold mb-1">Booking flow</h4>
                        <p class="text-muted mb-0">Your reservation request, payment follow-up, and final confirmation stay together so nothing gets missed.</p>
                        <div class="module-note-grid">
                            <div class="module-note-item"><i class="bi bi-check2-circle"></i><div>Choose the booking type and share your preference.</div></div>
                            <div class="module-note-item"><i class="bi bi-check2-circle"></i><div>Add dates, destination details, and party size.</div></div>
                            <div class="module-note-item"><i class="bi bi-check2-circle"></i><div>Track payment, confirmation code, and request status in one view.</div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            @foreach($types as $key => $label)
                <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 70 }}">
                    <div class="module-grid-card">
                        <div class="icon-box">BK</div>
                        <h4 class="fw-bold">{{ $label }}</h4>
                        <p class="text-muted mb-4">Create one reservation request and let Kiosk coordinate the next steps for you.</p>
                        @auth
                            <a href="{{ route('customer.bookings.create') }}" class="btn btn-outline-primary">Book {{ $label }}</a>
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
