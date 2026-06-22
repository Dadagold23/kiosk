@extends('layouts.frontend')

@section('meta_title', 'Shop | Kiosk')
@section('meta_description', 'Browse local stock and sourced products on Kiosk with elegant discovery, focused categories, and secure checkout.')
@section('meta_keywords', 'Kiosk shop, local products, sourced products, cart, checkout')
@section('og_title', 'Shop on Kiosk')
@section('og_description', 'Explore local stock and sourced products through Kiosk.')

@push('styles')
<style>
    .shop-shell{
        padding:1.35rem 0 3.5rem;
        overflow-x:hidden;
    }
    .shop-hero-clean{
        background:linear-gradient(135deg, rgba(255,250,244,.96) 0%, rgba(247,240,231,.92) 100%);
        border:1px solid rgba(176, 143, 121, .18);
        border-radius:24px;
        padding:1.35rem;
    }
    .shop-hero-clean h1{
        font-size:clamp(1.55rem, 2vw, 2.15rem);
        line-height:1.15;
    }
    .shop-hero-clean p,
    .shop-results-panel p,
    .shop-aside-card p{
        font-size:.92rem;
        line-height:1.6;
    }
    .shop-eyebrow{
        color:var(--kiosk-primary-deep);
        font-size:.72rem;
        font-weight:800;
        letter-spacing:.1em;
        text-transform:uppercase;
    }
    .shop-search-panel{
        background:rgba(255,255,255,.72);
        border:1px solid rgba(176, 143, 121, .16);
        border-radius:20px;
        padding:.9rem;
        backdrop-filter:blur(14px);
    }
    .shop-search-action{
        display:grid;
        align-items:end;
    }
    .shop-search-action .btn{
        width:100%;
    }
    .shop-stat{
        background:rgba(255,255,255,.68);
        border:1px solid rgba(176, 143, 121, .12);
        border-radius:18px;
        padding:.8rem .9rem;
        height:100%;
    }
    .shop-stat span{
        color:var(--kiosk-muted);
        display:block;
        font-size:.82rem;
        margin-bottom:.35rem;
        text-transform:uppercase;
        letter-spacing:.06em;
    }
    .shop-stat strong{
        color:var(--kiosk-ink);
        font-family:"Space Grotesk", sans-serif;
        font-size:1.1rem;
    }
    .shop-category-strip{
        display:flex;
        gap:.85rem;
        overflow:auto;
        padding-bottom:.35rem;
        scroll-behavior:smooth;
        scroll-snap-type:x proximity;
        scrollbar-width:none;
        -webkit-overflow-scrolling:touch;
        overscroll-behavior-x:contain;
    }
    .shop-category-strip::-webkit-scrollbar{
        display:none;
    }
    .shop-category-chip{
        background:rgba(255,253,249,.72);
        border:1px solid rgba(176, 143, 121, .15);
        border-radius:18px;
        color:var(--kiosk-text);
        display:flex;
        flex:0 0 auto;
        gap:.65rem;
        min-width:170px;
        padding:.75rem .85rem;
        scroll-snap-align:start;
        text-decoration:none;
        transition:border-color .2s ease, background-color .2s ease, transform .2s ease;
    }
    .shop-category-chip.active{
        background:#fff;
        border-color:rgba(143, 70, 39, .3);
        color:var(--kiosk-ink);
        transform:translateY(-1px);
    }
    .shop-category-chip strong{
        display:block;
        margin-bottom:.1rem;
    }
    .shop-category-chip small{
        color:var(--kiosk-muted);
    }
    .catalog-head{
        align-items:end;
        display:flex;
        justify-content:space-between;
        gap:1rem;
        margin-bottom:1.1rem;
    }
    .catalog-head h2{
        font-size:clamp(1.3rem, 1.7vw, 1.75rem);
    }
    .catalog-subtle{
        color:var(--kiosk-muted);
        font-size:.88rem;
    }
    .shop-layout{
        align-items:start;
    }
    .shop-results-panel{
        background:rgba(255,253,249,.72);
        border:1px solid rgba(176, 143, 121, .16);
        border-radius:22px;
        padding:1.1rem;
    }
    .shop-results-copy{
        max-width:48ch;
    }
    .shop-card-clean{
        background:rgba(255,253,249,.82);
        border:1px solid rgba(176, 143, 121, .18);
        border-radius:18px;
        height:100%;
        overflow:hidden;
        padding:.7rem;
        transition:border-color .2s ease, background-color .2s ease;
    }
    .shop-card-clean:hover{
        background:rgba(255,253,249,.92);
        border-color:rgba(143, 70, 39, .26);
        transform:none;
    }
    .shop-card-media{
        background:linear-gradient(180deg, #faf4ee 0%, #efe4d8 100%);
        border-radius:14px;
        height:170px;
        overflow:hidden;
    }
    .shop-card-media img{
        height:100%;
        object-fit:cover;
        object-position:center;
        width:100%;
    }
    .shop-card-body{
        display:flex;
        flex-direction:column;
        gap:.65rem;
        padding:.75rem .1rem 0;
    }
    .shop-card-actions{
        display:grid;
        gap:.6rem;
        max-height:0;
        opacity:0;
        overflow:hidden;
        pointer-events:none;
        transform:translateY(8px);
        transition:max-height .25s ease, opacity .2s ease, transform .2s ease;
    }
    .shop-card-clean:hover .shop-card-actions,
    .shop-card-clean:focus-within .shop-card-actions{
        max-height:180px;
        opacity:1;
        pointer-events:auto;
        transform:translateY(0);
    }
    @media (hover: none){
        .shop-card-actions{
            max-height:180px;
            opacity:1;
            overflow:visible;
            pointer-events:auto;
            transform:none;
        }
    }
    .shop-card-title{
        color:var(--kiosk-ink);
        font-size:.9rem;
        line-height:1.35;
        margin:0;
    }
    .shop-card-copy{
        color:var(--kiosk-muted);
        font-size:.84rem;
        margin:0;
    }
    .shop-card-topline{
        align-items:flex-start;
        display:flex;
        justify-content:space-between;
        gap:.75rem;
    }
    .shop-wishlist-btn{
        align-items:center;
        background:#fff;
        border:1px solid rgba(17,17,17,.12);
        border-radius:999px;
        color:var(--kiosk-ink);
        display:inline-flex;
        font-size:.76rem;
        font-weight:700;
        gap:.45rem;
        line-height:1;
        padding:.45rem .62rem;
    }
    .shop-wishlist-btn.is-active{
        background:#111;
        border-color:#111;
        color:#fff;
    }
    .shop-card-meta{
        align-items:center;
        display:flex;
        justify-content:space-between;
        gap:.75rem;
    }
    .shop-card-price{
        color:var(--kiosk-primary-deep);
        font-family:"Space Grotesk", sans-serif;
        font-size:.92rem;
    }
    .shop-inline-tag{
        background:#fff4eb;
        border:1px solid #ffd7bb;
        border-radius:999px;
        color:var(--kiosk-primary-deep);
        display:inline-flex;
        font-size:.68rem;
        font-weight:800;
        padding:.32rem .56rem;
        text-transform:uppercase;
    }
    .shop-secondary-panel{
        display:grid;
        gap:1rem;
        position:sticky;
        top:1.5rem;
    }
    .shop-aside-card{
        background:rgba(255,253,249,.88);
        border:1px solid rgba(176, 143, 121, .16);
        border-radius:18px;
        padding:1rem;
    }
    .shop-aside-card h3{
        color:var(--kiosk-ink);
        font-size:.95rem;
        margin-bottom:.45rem;
    }
    .shop-aside-card p{
        color:var(--kiosk-muted);
        font-size:.94rem;
        line-height:1.7;
        margin-bottom:0;
    }
    .shop-aside-list{
        display:grid;
        gap:.8rem;
        margin:1rem 0 0;
    }
    .shop-aside-row{
        align-items:center;
        display:flex;
        justify-content:space-between;
        gap:1rem;
    }
    .shop-aside-row span{
        color:var(--kiosk-muted);
        font-size:.86rem;
    }
    .shop-aside-row strong{
        color:var(--kiosk-ink);
        font-size:.95rem;
        text-align:right;
    }
    .shop-aside-actions{
        display:grid;
        gap:.75rem;
        margin-top:1rem;
    }
    .shop-focus-grid{
        display:grid;
        gap:.6rem;
        grid-template-columns:repeat(2, minmax(0, 1fr));
        margin-top:.85rem;
    }
    .shop-focus-tile{
        position:relative;
        overflow:hidden;
        min-height:92px;
        border-radius:14px;
        background:rgba(255,255,255,.72);
        border:1px solid rgba(176, 143, 121, .14);
    }
    .shop-focus-tile img{
        width:100%;
        height:100%;
        object-fit:cover;
        display:block;
        transition:transform .35s ease;
    }
    .shop-focus-tile span{
        position:absolute;
        left:10px;
        bottom:10px;
        display:inline-flex;
        align-items:center;
        padding:.38rem .62rem;
        border-radius:999px;
        background:rgba(255,255,255,.92);
        color:var(--kiosk-ink);
        font-size:.68rem;
        font-weight:700;
        box-shadow:0 10px 24px rgba(15, 23, 42, .08);
    }
    .shop-focus-tile:hover img,
    .shop-focus-tile:focus-visible img{
        transform:scale(1.04);
    }
    .shop-source-grid{
        display:grid;
        gap:.55rem;
        grid-template-columns:repeat(2, minmax(0, 1fr));
        margin-top:.85rem;
    }
    .shop-source-tile{
        position:relative;
        overflow:hidden;
        min-height:84px;
        border-radius:14px;
        background:rgba(255,255,255,.74);
        border:1px solid rgba(176, 143, 121, .14);
    }
    .shop-source-tile img{
        width:100%;
        height:100%;
        object-fit:cover;
        display:block;
        transition:transform .35s ease;
    }
    .shop-source-tile span{
        position:absolute;
        left:10px;
        bottom:10px;
        display:inline-flex;
        align-items:center;
        padding:.34rem .58rem;
        border-radius:999px;
        background:rgba(255,255,255,.92);
        color:var(--kiosk-ink);
        font-size:.66rem;
        font-weight:700;
        box-shadow:0 10px 24px rgba(15, 23, 42, .08);
    }
    .shop-source-tile:hover img,
    .shop-source-tile:focus-visible img{
        transform:scale(1.04);
    }
    .shop-empty{
        background:rgba(255,253,249,.82);
        border:1px dashed #d7c2ad;
        border-radius:18px;
        padding:1.35rem;
        text-align:center;
    }
    .shop-pagination{
        margin-top:1.5rem;
    }
    .shop-pagination nav{
        background:rgba(255,253,249,.72);
        border:1px solid rgba(176, 143, 121, .16);
        border-radius:20px;
        display:inline-flex;
        padding:.4rem;
    }
    @media (max-width: 991.98px){
        .shop-hero-clean{
            padding:1.05rem;
        }
        .catalog-head{
            align-items:start;
            flex-direction:column;
        }
        .shop-secondary-panel{
            position:static;
            top:auto;
        }
    }
    @media (max-width: 767.98px){
        .shop-results-panel .row{
            justify-content:center;
        }
        .shop-results-panel .row > [class*="col-"]{
            display:flex;
            justify-content:center;
        }
        .shop-card-clean{
            margin-inline:auto;
            padding:.58rem;
            width:min(100%, 24rem);
        }
        .shop-card-media{
            height:148px;
        }
        .shop-category-chip{
            min-width:150px;
        }
        .shop-focus-grid{
            grid-template-columns:1fr 1fr;
        }
        .shop-focus-tile,
        .shop-source-tile{
            min-height:72px;
        }
        .shop-focus-tile img,
        .shop-source-tile img{
            object-fit:contain;
            padding:.3rem;
        }
        .shop-focus-tile span,
        .shop-source-tile span{
            left:6px;
            bottom:6px;
            padding:.28rem .46rem;
        }
        .shop-results-panel{
            padding:.9rem;
        }
    }
    @media (max-width: 575.98px){
        .shop-results-panel .row > [class*="col-"]{
            width:100%;
        }
        .shop-focus-tile,
        .shop-source-tile{
            min-height:64px;
            border-radius:12px;
        }
        .shop-focus-tile img,
        .shop-source-tile img{
            padding:.24rem;
        }
    }
</style>
@endpush

@section('content')
<section class="shop-shell">
    <div class="container">
        @include('partials.flash')

        <div class="shop-hero-clean mb-4" data-aos="fade-up">
            <div class="row g-4 align-items-center">
                <div class="col-lg-6">
                    <div class="shop-eyebrow mb-2">Shop Kiosk</div>
                    <h1 class="fw-bold mb-3">Browse products without the clutter</h1>
                    <p class="text-muted mb-0">
                        Search by name, filter by source, and get to checkout faster.
                    </p>
                </div>
                <div class="col-lg-6">
                    <form method="GET" action="{{ route('shop.index') }}" class="shop-search-panel">
                        <div class="row g-3">
                            <div class="col-lg-4">
                                <label class="form-label">Search</label>
                                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Product name, SKU, or keyword">
                            </div>
                            <div class="col-lg-3">
                                <label class="form-label">Source</label>
                                <select name="source_type" class="form-select">
                                    <option value="">All sources</option>
                                    <option value="local" @selected(request('source_type') === 'local')>Local</option>
                                    <option value="global" @selected(request('source_type') === 'global')>Global</option>
                                </select>
                            </div>
                            <div class="col-lg-3">
                                <label class="form-label">Category</label>
                                <select name="category" class="form-select">
                                    <option value="">All categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-2 shop-search-action">
                                <button class="btn btn-primary">Go</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row g-3 mt-1">
                <div class="col-6 col-lg-3">
                    <div class="shop-stat">
                        <span>Products</span>
                        <strong>{{ number_format($catalogStats['all']) }}</strong>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="shop-stat">
                        <span>Local Stock</span>
                        <strong>{{ number_format($catalogStats['local']) }}</strong>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="shop-stat">
                        <span>Global Sourcing</span>
                        <strong>{{ number_format($catalogStats['global']) }}</strong>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="shop-stat">
                        <span>Featured</span>
                        <strong>{{ number_format($catalogStats['featured']) }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-5">
            <div class="catalog-head">
                <div>
                    <h2 class="fw-bold mb-1">Browse by category</h2>
                    <p class="catalog-subtle mb-0">Jump into a category without fighting the full catalog first.</p>
                </div>
                @if(request()->hasAny(['search', 'source_type', 'category']))
                    <a href="{{ route('shop.index') }}" class="btn btn-outline-primary">Clear filters</a>
                @endif
            </div>

            <div class="shop-category-strip">
                <a href="{{ route('shop.index') }}" class="shop-category-chip {{ request('category') ? '' : 'active' }}">
                    <div>
                        <strong>All categories</strong>
                        <small>{{ number_format($catalogStats['all']) }} products</small>
                    </div>
                </a>
                @foreach($categories as $category)
                    <a href="{{ route('shop.index', array_filter(['category' => $category->slug, 'source_type' => request('source_type'), 'search' => request('search')])) }}" class="shop-category-chip {{ request('category') === $category->slug ? 'active' : '' }}">
                        <div>
                            <strong>{{ $category->name }}</strong>
                            <small>{{ number_format($category->products_count) }} products</small>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        <div class="row g-4 shop-layout">
            <div class="col-lg-8 col-xl-9">
                <div class="shop-results-panel">
                    <div class="catalog-head">
                        <div>
                            <h2 class="fw-bold mb-1">Catalog</h2>
                            <p class="catalog-subtle mb-0 shop-results-copy">
                                {{ $products->total() }} item{{ $products->total() === 1 ? '' : 's' }}
                                @if(request('search'))
                                    for "{{ request('search') }}"
                                @endif
                            </p>
                        </div>
                        <a href="{{ route('cart.index') }}" class="btn btn-outline-primary">View Cart</a>
                    </div>

                    <div class="row g-4">
                        @forelse($products as $product)
                            <div class="col-sm-6 col-lg-6 col-xl-4" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 4) * 60 }}">
                                <div class="shop-card-clean">
                                    <a href="{{ route('shop.show', $product->slug) }}" class="d-block text-decoration-none">
                                        <div class="shop-card-media">
                                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
                                        </div>
                                    </a>

                                    <div class="shop-card-body">
                                        <div class="shop-card-meta">
                                            <span class="shop-inline-tag">{{ $product->source_type }}</span>
                                            @if($product->category)
                                                <small class="text-muted">{{ $product->category->name }}</small>
                                            @endif
                                        </div>

                                        <div>
                                            <div class="shop-card-topline">
                                                <a href="{{ route('shop.show', $product->slug) }}" class="text-decoration-none">
                                                    <h3 class="shop-card-title">{{ $product->name }}</h3>
                                                </a>
                                                @auth
                                                    @php($isWished = in_array($product->id, $wishlistProductIds, true))
                                                    <form action="{{ $isWished ? route('wishlist.destroy', $product) : route('wishlist.store', $product) }}" method="POST">
                                                        @csrf
                                                        @if($isWished)
                                                            @method('DELETE')
                                                        @endif
                                                        <button type="submit" class="shop-wishlist-btn {{ $isWished ? 'is-active' : '' }}" aria-label="{{ $isWished ? 'Remove from wishlist' : 'Save to wishlist' }}">
                                                            <i class="icon icon-HeartStraight"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <a href="{{ route('login') }}" class="shop-wishlist-btn" aria-label="Login to save wishlist">
                                                        <i class="icon icon-HeartStraight"></i>
                                                    </a>
                                                @endauth
                                            </div>
                                            <p class="shop-card-copy mt-2">{{ \Illuminate\Support\Str::limit(strip_tags($product->description), 92) }}</p>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center gap-3">
                                            <strong class="shop-card-price">&#8358;{{ number_format($product->current_price, 2) }}</strong>
                                            @if($product->source_marketplace)
                                                <small class="text-muted">{{ $product->source_marketplace }}</small>
                                            @endif
                                        </div>

                                        <div class="shop-card-actions">
                                            <a href="{{ route('shop.show', $product->slug) }}" class="btn btn-outline-primary">View Product</a>
                                            @auth
                                                <form action="{{ route('cart.store', $product) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-primary w-100">Add to Cart</button>
                                                </form>
                                            @else
                                                <a href="{{ route('login') }}" class="btn btn-primary">Login to Add to Cart</a>
                                            @endauth
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="shop-empty">
                                    <h3 class="fw-bold mb-2">No matching products found</h3>
                                    <p class="text-muted mb-3">Try broadening your search or clearing one of the filters.</p>
                                    <a href="{{ route('shop.index') }}" class="btn btn-outline-primary">Reset Catalog</a>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <div class="shop-pagination">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-xl-3">
                <aside class="shop-secondary-panel" data-aos="fade-up" data-aos-delay="120">
                    <div class="shop-aside-card">
                        <div class="shop-eyebrow mb-2">Catalog focus</div>
                        <h3 class="mb-1">Keep your next step simple</h3>
                        <p>Use the category strip to narrow the catalog first, then save products or move straight into cart once the shortlist feels right.</p>

                        <div class="shop-aside-list">
                            <div class="shop-aside-row">
                                <span>Search</span>
                                <strong>{{ request('search') ?: 'Open browse' }}</strong>
                            </div>
                            <div class="shop-aside-row">
                                <span>Source</span>
                                <strong>{{ request('source_type') ? ucfirst(request('source_type')) : 'All sources' }}</strong>
                            </div>
                            <div class="shop-aside-row">
                                <span>Category</span>
                                <strong>{{ optional($categories->firstWhere('slug', request('category')))->name ?: 'All categories' }}</strong>
                            </div>
                        </div>

                        <div class="shop-aside-actions">
                            <a href="{{ route('cart.index') }}" class="btn btn-primary">Open Cart</a>
                            @auth
                                <a href="{{ route('wishlist.index') }}" class="btn btn-outline-primary">Saved Items</a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-outline-primary">Login to Save</a>
                            @endauth
                        </div>

                        @if(($catalogFocusImages ?? collect())->isNotEmpty())
                            <div class="shop-focus-grid">
                                @foreach($catalogFocusImages as $image)
                                    <div class="shop-focus-tile">
                                        <img src="{{ $image['url'] }}" alt="{{ $image['label'] }}">
                                        <span>{{ $image['label'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="shop-aside-card">
                        <h3 class="mb-1">What changes by source?</h3>
                        <p>Local products are usually ready faster. Global items may go through sourcing confirmation before dispatch, especially for pricing and delivery timing.</p>
                        @if(($sourceGuideImages ?? collect())->isNotEmpty())
                            <div class="shop-source-grid">
                                @foreach($sourceGuideImages as $image)
                                    <div class="shop-source-tile">
                                        <img src="{{ $image['url'] }}" alt="{{ $image['label'] }}">
                                        <span>{{ $image['label'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </aside>
            </div>
        </div>

        @if($testimonials->isNotEmpty())
            <section class="mt-5 pt-2">
                <div class="catalog-head">
                    <div>
                        <h2 class="fw-bold mb-1">Approved customer feedback</h2>
                        <p class="catalog-subtle mb-0">Approved feedback from customers whose orders were delivered successfully.</p>
                    </div>
                </div>

                @include('partials.reviews.public-grid', ['reviews' => $testimonials])
            </section>
        @endif
    </div>
</section>

@endsection
