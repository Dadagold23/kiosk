<section class="kiosk-testimonials">
    <div class="row g-4">
        @foreach($items as $item)
            @php($initials = collect(explode(' ', $item['name']))->filter()->map(fn ($part) => strtoupper(substr($part, 0, 1)))->take(2)->implode(''))
            <div class="col-lg-6">
                <div class="testimonial-v01 style-1 style-def h-100" style="background:rgba(255,253,249,.84);border:1px solid rgba(176, 143, 121, .16);border-radius:24px;box-shadow:none;">
                    <div class="testimonial-content">
                        <div class="box-author">
                            @if(!empty($item['image']))
                                <img class="avt" src="{{ $item['image'] }}" alt="{{ $item['name'] }}" style="border-radius:50%;height:56px;object-fit:cover;width:56px;">
                            @else
                                <div class="avt" style="align-items:center;background:linear-gradient(135deg,#fff4eb 0%,#f1dfcd 100%);border-radius:50%;color:var(--kiosk-primary-deep);display:flex;font-family:'Space Grotesk',sans-serif;font-size:1rem;font-weight:700;height:56px;justify-content:center;width:56px;">
                                    {{ $initials }}
                                </div>
                            @endif
                            <div class="infor">
                                <h6 class="name">{{ $item['name'] }}</h6>
                                <p class="text-caption-01">{{ $item['role'] }}</p>
                            </div>
                        </div>
                        <p class="text-body-1">&ldquo;{{ $item['quote'] }}&rdquo;</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>
