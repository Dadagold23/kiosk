<div class="{{ $wrapperClass ?? 'admin-card admin-page-block mt-4' }}">
    <div class="admin-panel-head mb-3">
        <div>
            <h3 class="admin-section-title">{{ $title ?? 'Customer Reviews' }}</h3>
            <p class="admin-section-copy">{{ $subtitle ?? 'Approve, reject, and feature published testimonials for this module.' }}</p>
        </div>
    </div>

    @forelse($reviews as $review)
        @php
            $statusClass = match ($review->status) {
                \App\Models\ModuleReview::STATUS_APPROVED => 'success', \App\Models\ModuleReview::STATUS_REJECTED => 'danger', default => 'warning',
            };
        @endphp
        <div class="admin-stacked-item {{ !$loop->last ? 'mb-3' : '' }}">
            <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-2">
                <div>
                    <div class="fw-semibold">{{ $review->user?->name ?? $review->publicReviewerName() }}</div>
                    <div class="small text-muted">{{ $review->created_at?->format('d M Y, h:i A') }}</div>
                </div>
                <div class="text-end">
                    <div class="text-warning small">
                        @for($index = 0; $index < $review->rating; $index++)
                            <span>&#9733;</span>
                        @endfor
                    </div>
                    <span class="admin-status-badge {{ $statusClass === 'success' ? 'is-success' : ($statusClass === 'danger' ? 'is-danger' : 'is-warning') }}">{{ ucfirst($review->status) }}</span>
                </div>
            </div>

            @if($review->title)
                <div class="fw-semibold mb-1">{{ $review->title }}</div>
            @endif

            <p class="small mb-2">{{ $review->review }}</p>

            <div class="small text-muted mb-3">
                Public name: {{ $review->publicReviewerName() }} |
                Recommends: {{ $review->would_recommend ? 'Yes' : 'No' }} |
                Featured: {{ $review->is_featured ? 'Yes' : 'No' }}
            </div>

            <form action="{{ route('admin.reviews.moderate', $review) }}" method="POST" class="admin-form-grid mt-3">
                @csrf

                <div class="admin-field admin-col-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        @foreach([\App\Models\ModuleReview::STATUS_PENDING, \App\Models\ModuleReview::STATUS_APPROVED, \App\Models\ModuleReview::STATUS_REJECTED] as $status)
                            <option value="{{ $status }}" @selected($review->status === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="admin-field admin-col-6 d-flex align-items-end">
                    <div class="form-check">
                        <input type="checkbox" name="is_featured" value="1" class="form-check-input" id="featuredReview{{ $review->id }}" @checked($review->is_featured)>
                        <label class="form-check-label" for="featuredReview{{ $review->id }}">Feature this review on the public page</label>
                    </div>
                </div>

                <div class="admin-field">
                    <label class="form-label">Moderation Note</label>
                    <textarea name="moderation_note" rows="3" class="form-control" placeholder="Optional note for internal review or customer guidance">{{ $review->moderation_note }}</textarea>
                </div>

                <div>
                    <button class="btn btn-outline-primary">Save Moderation</button>
                </div>
            </form>
        </div>
    @empty
        <div class="admin-note-box">No customer reviews have been submitted for this record yet.</div>
    @endforelse
</div>
