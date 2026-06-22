@extends('layouts.customer')

@section('customer_page_title', 'Wishlist')
@section('customer_page_subtitle', 'Saved products you want to revisit quickly.')

@section('customer_body')
<div class="customer-page-grid">
    <div class="customer-card customer-page-block muara-module-hero muara-module-hero-primary">
        <div class="row g-4 align-items-center">
            <div class="col-lg-8">
                <span class="customer-welcome-chip">Saved Picks</span>
                <h2 class="fw-bold mb-2">Keep products you want to compare or purchase later in one shortlist.</h2>
                <p class="mb-0 muara-module-copy">Save items here when you want to come back, compare options, or move them into your cart later.</p>
            </div>
            <div class="col-lg-4">
                <div class="muara-summary-grid">
                    <div class="muara-summary-card">
                        <div class="muara-summary-label">Saved items</div>
                        <div class="muara-summary-value">{{ $items->total() }}</div>
                    </div>
                    <div class="muara-summary-card">
                        <div class="muara-summary-label">Ready to cart</div>
                        <div class="muara-summary-value">{{ $items->getCollection()->filter(fn ($item) => filled($item->product))->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="customer-panel-head">
        <div>
            <span class="customer-eyebrow">Product Shortlist</span>
            <h3 class="customer-section-title">My Wishlist</h3>
            <p class="customer-section-copy">Keep products you want to compare, revisit, or buy later in one easy list.</p>
        </div>
        <a href="{{ route('shop.index') }}" class="btn customer-btn-primary">Continue Shopping</a>
    </div>

    <div class="row g-4">
        @forelse($items as $item)
            @php($product = $item->product)
            @if($product)
                <div class="col-md-6 col-xl-4">
                    <article class="muara-record-card h-100">
                        <a href="{{ route('shop.show', $product->slug) }}" class="d-block mb-3">
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-100 rounded-4" style="height:240px; object-fit:cover;">
                        </a>

                        <div class="d-flex justify-content-between gap-3 align-items-start mb-2">
                            <div>
                                <div class="record-kicker">{{ $product->category?->name ?: 'Catalog' }}</div>
                                <div class="record-title">{{ $product->name }}</div>
                            </div>
                            <form action="{{ route('wishlist.destroy', $product) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="customer-soft-button border-0">Remove</button>
                            </form>
                        </div>

                        <p class="text-muted small">{{ \Illuminate\Support\Str::limit(strip_tags($product->description), 110) }}</p>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <strong class="text-primary">&#8358;{{ number_format($product->current_price, 2) }}</strong>
                            <div class="d-flex gap-2">
                                <a href="{{ route('shop.show', $product->slug) }}" class="customer-soft-button">View</a>
                                <form action="{{ route('cart.store', $product) }}" method="POST">
                                    @csrf
                                    <button class="btn customer-btn-primary">Add to Cart</button>
                                </form>
                            </div>
                        </div>
                    </article>
                </div>
            @endif
        @empty
            <div class="col-12">
                <div class="customer-empty">No wishlist items yet. Save products from the shop to build your shortlist.</div>
            </div>
        @endforelse
    </div>

    <div class="customer-pagination">{{ $items->links() }}</div>
</div>
@endsection
