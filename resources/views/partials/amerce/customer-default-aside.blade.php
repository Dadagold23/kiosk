@php
    $currentPageTitle = trim($__env->yieldContent('customer_page_title', 'Customer Dashboard')); $currentPageSubtitle = trim($__env->yieldContent('customer_page_subtitle', ''));

    $defaultAsideLinks = [
        ['label' => 'Profile', 'href' => route('profile.edit')], ['label' => 'Orders', 'href' => route('orders.index')], ['label' => 'Wishlist', 'href' => route('wishlist.index')], ['label' => 'Notifications', 'href' => route('notifications.index')],
    ];
@endphp

<div class="customer-page-grid">
    <section class="customer-card customer-page-block">
        <span class="customer-eyebrow">Page Guide</span>
        <h3 class="customer-section-title">{{ $currentPageTitle }}</h3>
        <p class="customer-section-copy mb-0">
            {{ $currentPageSubtitle ?: 'Keep this part of your account easy to review, update, and return to later.' }}
        </p>
    </section>

    <section class="customer-card customer-page-block">
        <span class="customer-eyebrow">Quick Links</span>
        <h3 class="customer-section-title">Keep moving</h3>
        <p class="customer-section-copy">Jump back to the account pages people usually need next.</p>

        <div class="customer-info-grid mt-3">
            @foreach($defaultAsideLinks as $link)
                <a href="{{ $link['href'] }}" class="customer-info-card text-decoration-none">
                    <span class="label">Open</span>
                    <span class="value">{{ $link['label'] }}</span>
                </a>
            @endforeach
        </div>
    </section>

    <section class="customer-card customer-page-block">
        <span class="customer-eyebrow">Helpful Note</span>
        <h3 class="customer-section-title">Stay on top of updates</h3>
        <div class="customer-panel-note mt-3">
            Check your notifications after any payment, request, booking, or emergency update so nothing important gets missed.
        </div>
    </section>
</div>
