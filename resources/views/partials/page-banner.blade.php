<section class="page-banner">
    <div class="container">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <span class="section-label">{{ $badge ?? 'Kiosk' }}</span>
                <h1 class="fw-bold mb-2">{{ $title }}</h1>
                @if(!empty($subtitle))
                    <p class="text-muted mb-0 fs-5">{{ $subtitle }}</p>
                @endif
            </div>
            @if(!empty($actionText) && !empty($actionUrl))
                <div class="col-lg-4 text-lg-end">
                    <a href="{{ $actionUrl }}" class="btn btn-primary btn-lg">{{ $actionText }}</a>
                </div>
            @endif
        </div>
    </div>
</section>
