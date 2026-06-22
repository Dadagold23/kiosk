@php
    $reviewStatus = $existingReview?->status;
    $statusClass = match ($reviewStatus) {
        \App\Models\ModuleReview::STATUS_APPROVED => 'success', \App\Models\ModuleReview::STATUS_REJECTED => 'danger', default => 'warning',
    };
@endphp

<div class="feature-card customer-page-block mt-4">
    <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-3">
        <div>
            <h3 class="customer-section-title">Share Your Experience</h3>
            <p class="customer-section-copy">Verified feedback helps us improve operations and helps other customers trust this module.</p>
        </div>
        @if($existingReview)
            <span class="customer-status-pill {{ $statusClass === 'success' ? 'is-success' : ($statusClass === 'danger' ? 'is-danger' : 'is-warning') }}">
                {{ ucfirst($existingReview->status) }}
            </span>
        @endif
    </div>

    @if($existingReview)
        <div class="customer-panel-note mb-4">
            <div class="fw-semibold mb-1">Current review status: {{ ucfirst($existingReview->status) }}</div>
            <div class="small text-muted">
                @if($existingReview->status === \App\Models\ModuleReview::STATUS_APPROVED)
                    Your feedback is already visible on the public module page.
                @elseif($existingReview->status === \App\Models\ModuleReview::STATUS_REJECTED)
                    Your review needs an update before it can be published.
                @else
                    Your review is waiting for the operations team to approve it.
                @endif
            </div>
            @if($existingReview->moderation_note)
                <div class="small mt-2"><strong>Moderator note:</strong> {{ $existingReview->moderation_note }}</div>
            @endif
        </div>
    @endif

    @if($canSubmitReview || $existingReview)
        <form action="{{ route('reviews.store', ['type' => $reviewType, 'record' => $reviewable->getRouteKey()]) }}" method="POST" class="row g-3">
            @csrf

            <div class="col-md-3">
                <label class="form-label">Rating</label>
                <select name="rating" class="form-select @error('rating') is-invalid @enderror">
                    <option value="">Choose</option>
                    @for($rating = 5; $rating >= 1; $rating--)
                        <option value="{{ $rating }}" @selected(old('rating', $existingReview?->rating) == $rating)>{{ $rating }} Star{{ $rating > 1 ? 's' : '' }}</option>
                    @endfor
                </select>
                @error('rating') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-9">
                <label class="form-label">Headline</label>
                <input type="text" name="title" value="{{ old('title', $existingReview?->title) }}" class="form-control @error('title') is-invalid @enderror" placeholder="Short summary of your experience">
                @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12">
                <label class="form-label">Review</label>
                <textarea name="review" rows="5" class="form-control @error('review') is-invalid @enderror" placeholder="Tell other customers how this experience went, what stood out, and what you would recommend.">{{ old('review', $existingReview?->review) }}</textarea>
                @error('review') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <div class="form-check">
                    <input type="checkbox" name="would_recommend" value="1" class="form-check-input" id="wouldRecommendReview" @checked((bool) old('would_recommend', $existingReview?->would_recommend ?? true))>
                    <label class="form-check-label" for="wouldRecommendReview">I would recommend this to another customer</label>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-check">
                    <input type="checkbox" name="show_identity" value="1" class="form-check-input" id="showIdentityReview" @checked((bool) old('show_identity', $existingReview?->show_identity ?? true))>
                    <label class="form-check-label" for="showIdentityReview">Show my name publicly with this review</label>
                </div>
            </div>

            <div class="col-12">
                <button class="btn btn-primary">{{ $existingReview ? 'Update Review' : 'Submit Review' }}</button>
            </div>
        </form>
    @else
        <div class="customer-panel-note">
            This feedback form unlocks after the request or delivery has been fully completed.
        </div>
    @endif
</div>
