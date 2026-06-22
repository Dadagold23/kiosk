<section class="kiosk-team-grid">
    <div class="row g-4">
        @foreach($members as $member)
            @php($initials = collect(explode(' ', $member['name']))->filter()->map(fn ($part) => strtoupper(substr($part, 0, 1)))->take(2)->implode(''))
            <div class="col-md-6 col-xl-3">
                <div class="card-member-v01 hover-img h-100" style="background:rgba(255,253,249,.84);border:1px solid rgba(176, 143, 121, .16);border-radius:24px;box-shadow:none;overflow:hidden;">
                    @if(!empty($member['image']))
                        <img class="member-image" src="{{ $member['image'] }}" alt="{{ $member['name'] }}" style="display:block;height:180px;object-fit:cover;width:100%;">
                    @else
                        <div class="member-image" style="align-items:center;background:linear-gradient(135deg,#fff4eb 0%,#f1dfcd 100%);color:var(--kiosk-primary-deep);display:flex;font-family:'Space Grotesk',sans-serif;font-size:1.25rem;font-weight:700;height:180px;justify-content:center;">
                            {{ $initials }}
                        </div>
                    @endif
                    <div class="member-info">
                        <h6 class="name mb-1">{{ $member['name'] }}</h6>
                        <p class="text-caption-01 mb-0">{{ $member['role'] }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>
