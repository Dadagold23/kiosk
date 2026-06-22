<div class="banner-why-choose">
    <div class="bn-image">
        <div style="align-items:flex-end;background:linear-gradient(135deg,#fff8f0 0%,#f0e2d1 100%);border-radius:28px;color:var(--kiosk-ink);display:flex;height:100%;min-height:420px;padding:1.5rem;position:relative;">
            <div>
                <h3 class="mb-2">One operational view across commerce and support</h3>
                <p class="text-muted mb-0">The same platform holds customer intent, payment history, fulfillment progress, and support follow-up so operations teams can respond with more continuity.</p>
            </div>
        </div>
    </div>
    <div class="bn-content">
        <h3 class="mb-12">Why Kiosk works across commerce and support</h3>
        <div id="kioskAboutAccordion">
            @foreach($items as $item)
                <div class="accordion-item_v2">
                    <div class="accordion-action {{ $loop->first ? '' : 'collapsed' }} lh-24 fw-medium" data-bs-target="#kiosk-faq-{{ $loop->iteration }}" data-bs-toggle="collapse" aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="kiosk-faq-{{ $loop->iteration }}" role="button">
                        <span>{{ $item['title'] }}</span>
                        <span class="icon ic-accordion-custom cl-2"></span>
                    </div>
                    <div id="kiosk-faq-{{ $loop->iteration }}" class="collapse {{ $loop->first ? 'show' : '' }}" data-bs-parent="#kioskAboutAccordion">
                        <p class="faq-content cl-text-2">{{ $item['body'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
