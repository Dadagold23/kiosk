<div class="row g-4">
    @foreach($reviews as $review)
        <div class="col-md-6">
            <article class="feature-card h-100 p-4" style="background:rgba(255,253,249,.84);border:1px solid rgba(176, 143, 121, .16);border-radius:24px;box-shadow:none;">
                <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                    <div>
                        @if(filled($review->title))
                            <h3 class="h5 mb-2">{{ $review->title }}</h3>
                        @endif
                        <p class="text-muted small mb-0">
                            {{ $review->publicReviewerName() }} &middot; {{ $review->moduleLabel() }}
                        </p>
                    </div>
                    <span class="badge text-bg-dark">{{ $review->rating }}/5</span>
                </div>
                <p class="text-muted mb-0">{{ $review->review }}</p>
            </article>
        </div>
    @endforeach
</div>
