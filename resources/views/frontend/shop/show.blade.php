@extends('layouts.frontend')

@section('meta_title', $product->name . ' | Kiosk Shop')
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($product->description), 155))
@section('meta_keywords', $product->name . ', Kiosk shop, ' . ($product->category?->name ?? 'product'))
@section('og_title', $product->name . ' | Kiosk')
@section('og_description', \Illuminate\Support\Str::limit(strip_tags($product->description), 155))
@section('og_image', $product->image_url)
@section('twitter_title', $product->name . ' | Kiosk')
@section('twitter_description', \Illuminate\Support\Str::limit(strip_tags($product->description), 155))
@section('twitter_image', $product->image_url)

@push('styles')
<style>
    .product-clean-shell{
        padding:2rem 0 5rem;
    }
    .product-breadcrumb{
        align-items:center;
        color:var(--kiosk-muted);
        display:flex;
        flex-wrap:wrap;
        gap:.55rem;
        margin-bottom:1.5rem;
    }
    .product-breadcrumb a{
        color:var(--kiosk-ink);
        text-decoration:none;
    }
    .product-clean-wrap{
        background:transparent;
        border:0;
        border-radius:0;
        padding:0;
    }
    .product-clean-gallery{
        background:linear-gradient(180deg, #fbfbfb 0%, #f4efe8 100%);
        border-radius:26px;
        min-height:520px;
        overflow:hidden;
        position:sticky;
        top:1.5rem;
    }
    .product-clean-gallery img{
        height:100%;
        object-fit:cover;
        width:100%;
    }
    .product-kicker{
        color:var(--kiosk-primary-deep);
        font-size:.78rem;
        font-weight:800;
        letter-spacing:.12em;
        text-transform:uppercase;
    }
    .product-price-clean{
        color:var(--kiosk-primary-deep);
        font-family:"Space Grotesk", sans-serif;
        font-size:2rem;
        line-height:1.1;
    }
    .product-top-card{
        background:linear-gradient(135deg, rgba(255,250,244,.96) 0%, rgba(247,240,231,.92) 100%);
        border:1px solid rgba(176, 143, 121, .18);
        border-radius:32px;
        padding:1.65rem;
    }
    .product-info-grid{
        display:grid;
        gap:.9rem;
        grid-template-columns:repeat(2, minmax(0, 1fr));
    }
    .product-info-card{
        background:rgba(255,255,255,.72);
        border:1px solid rgba(176, 143, 121, .14);
        border-radius:20px;
        padding:1rem;
    }
    .product-info-card span{
        color:var(--kiosk-muted);
        display:block;
        font-size:.8rem;
        margin-bottom:.2rem;
        text-transform:uppercase;
        letter-spacing:.05em;
    }
    .product-story{
        background:rgba(255,255,255,.72);
        border:1px solid rgba(176, 143, 121, .14);
        border-radius:24px;
        padding:1.1rem 1.15rem;
        margin-top:1rem;
    }
    .product-purchase-card{
        background:rgba(255,253,249,.88);
        border:1px solid rgba(176, 143, 121, .16);
        border-radius:24px;
        margin-top:1rem;
        padding:1.2rem;
    }
    .product-purchase-list{
        display:grid;
        gap:.8rem;
        margin-top:1rem;
    }
    .product-purchase-row{
        align-items:center;
        display:flex;
        justify-content:space-between;
        gap:1rem;
    }
    .product-purchase-row span{
        color:var(--kiosk-muted);
        font-size:.88rem;
    }
    .product-purchase-row strong{
        color:var(--kiosk-ink);
        font-size:.92rem;
        text-align:right;
    }
    .product-actions{
        display:grid;
        gap:.75rem;
    }
    .product-actions form,
    .product-actions a{
        width:100%;
    }
    .product-related-card{
        background:rgba(255,253,249,.82);
        border:1px solid rgba(176, 143, 121, .18);
        border-radius:22px;
        overflow:hidden;
        height:100%;
        padding:.8rem;
    }
    .product-related-card img{
        border-radius:16px;
        height:200px;
        object-fit:cover;
        width:100%;
    }
    .product-wishlist-btn{
        align-items:center;
        background:#fff;
        border:1px solid rgba(17,17,17,.12);
        border-radius:999px;
        color:var(--kiosk-ink);
        display:inline-flex;
        gap:.5rem;
        justify-content:center;
        min-height:48px;
        padding:0 1rem;
    }
    .product-wishlist-btn.is-active{
        background:#111;
        border-color:#111;
        color:#fff;
    }
    .shop-inline-tag{
        background:#fff4eb;
        border:1px solid #ffd7bb;
        border-radius:999px;
        color:var(--kiosk-primary-deep);
        display:inline-flex;
        font-size:.75rem;
        font-weight:800;
        padding:.4rem .7rem;
        text-transform:uppercase;
    }
    @media (max-width: 767.98px){
        .product-info-grid{
            grid-template-columns:1fr;
        }
        .product-clean-gallery{
            min-height:320px;
            position:static;
            top:auto;
        }
    }
</style>
@endpush

@section('content')
<section class="product-clean-shell">
    <div class="container">
        @include('partials.flash')

        <div class="product-breadcrumb">
            <a href="{{ route('shop.index') }}">Shop</a>
            <span>/</span>
            @if($product->category)
                <span>{{ $product->category->name }}</span>
                <span>/</span>
            @endif
            <span>{{ $product->name }}</span>
        </div>

        <div class="product-clean-wrap">
            <div class="row g-4 align-items-start">
                <div class="col-lg-6">
                    <div class="product-clean-gallery d-flex align-items-center justify-content-center">
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="product-top-card">
                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                            <div>
                                <div class="product-kicker mb-2">{{ $product->category?->name ?: 'Catalog product' }}</div>
                                <h1 class="fw-bold mb-0">{{ $product->name }}</h1>
                            </div>
                            <a href="{{ route('shop.index') }}" class="btn btn-outline-primary">Back to Shop</a>
                        </div>

                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <span class="shop-inline-tag">{{ $product->source_type }}</span>
                            @if($product->source_marketplace)
                                <span class="badge text-bg-light border">{{ ucfirst($product->source_marketplace) }}</span>
                            @endif
                        </div>

                        <div class="product-price-clean mb-2">&#8358;{{ number_format($product->current_price, 2) }}</div>

                        @if($product->sale_price)
                            <p class="text-muted mb-3">
                                <span class="text-decoration-line-through">&#8358;{{ number_format($product->price, 2) }}</span>
                            </p>
                        @endif

                        <p class="lead text-muted mb-4">{{ $product->description }}</p>

                        <div class="product-info-grid">
                            <div class="product-info-card">
                                <span>SKU</span>
                                <strong>{{ $product->sku ?: 'N/A' }}</strong>
                            </div>
                            <div class="product-info-card">
                                <span>Availability</span>
                                <strong>{{ $product->quantity > 0 ? 'Ready now' : 'Available on request' }}</strong>
                            </div>
                            <div class="product-info-card">
                                <span>Source</span>
                                <strong>{{ ucfirst($product->source_type) }}</strong>
                            </div>
                            <div class="product-info-card">
                                <span>Category</span>
                                <strong>{{ $product->category?->name ?: 'General' }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="product-purchase-card">
                        <strong class="d-block mb-1">Next step</strong>
                        <p class="text-muted mb-0">Add this to cart now, or save it first and compare it with the rest of your shortlist before checkout.</p>

                        <div class="product-purchase-list">
                            <div class="product-purchase-row">
                                <span>Availability</span>
                                <strong>{{ $product->quantity > 0 ? 'Ready for cart checkout' : 'Handled as request-based fulfillment' }}</strong>
                            </div>
                            <div class="product-purchase-row">
                                <span>Source timing</span>
                                <strong>{{ $product->source_type === 'global' ? 'May need sourcing confirmation' : 'Usually faster local dispatch' }}</strong>
                            </div>
                        </div>

                        <div class="product-actions mt-4">
                            @auth
                                <form action="{{ route('cart.store', $product) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-primary btn-lg w-100">Add to Cart</button>
                                </form>
                                @php($isWished = in_array($product->id, $wishlistProductIds, true))
                                <form action="{{ $isWished ? route('wishlist.destroy', $product) : route('wishlist.store', $product) }}" method="POST">
                                    @csrf
                                    @if($isWished)
                                        @method('DELETE')
                                    @endif
                                    <button class="product-wishlist-btn {{ $isWished ? 'is-active' : '' }} w-100">
                                        <i class="icon icon-HeartStraight"></i>
                                        <span>{{ $isWished ? 'Saved in Wishlist' : 'Save to Wishlist' }}</span>
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-primary btn-lg">Login to Add to Cart</a>
                                <a href="{{ route('login') }}" class="product-wishlist-btn">
                                    <i class="icon icon-HeartStraight"></i>
                                    <span>Login to Save</span>
                                </a>
                            @endauth

                            <a href="{{ route('cart.index') }}" class="btn btn-outline-primary btn-lg">Go to Cart</a>
                        </div>
                    </div>

                    @if($product->source_type === 'global')
                        <div class="product-story">
                            <strong class="d-block mb-2">Sourcing note</strong>
                            <p class="text-muted mb-0">This item moves through the sourcing workflow, so final price or delivery timing may be confirmed before dispatch.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

@if($relatedProducts->count())
<section class="pb-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-4">
            <div>
                <h2 class="fw-bold mb-1">More from the catalog</h2>
                <p class="text-muted mb-0">A few more product items to compare before you decide.</p>
            </div>
            <a href="{{ route('shop.index') }}" class="btn btn-outline-primary">Back to Catalog</a>
        </div>

        <div class="row g-4">
            @foreach($relatedProducts as $related)
                <div class="col-md-6 col-lg-3">
                    <div class="product-related-card">
                        <a href="{{ route('shop.show', $related->slug) }}" class="text-decoration-none text-dark d-block">
                            <img src="{{ $related->image_url }}" alt="{{ $related->name }}">
                            <div class="p-3">
                                <div class="shop-inline-tag mb-2">{{ $related->source_type }}</div>
                                <h3 class="h6 fw-bold mb-2">{{ $related->name }}</h3>
                                <p class="text-primary fw-bold mb-3">&#8358;{{ number_format($related->current_price, 2) }}</p>
                                <span class="btn btn-outline-primary w-100">View Product</span>
                            </div>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
