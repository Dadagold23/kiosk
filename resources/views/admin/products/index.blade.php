@extends('layouts.admin')

@section('meta_title', 'Products | Kiosk Admin')
@section('page_title', 'Products')
@section('page_subtitle', 'Browse and manage the Kiosk product catalog.')

@section('content')
@php
    $totalProducts  = $products->total();
    $activeProducts = $products->getCollection()->where('status', true)->count();
    $localProducts  = $products->getCollection()->where('source_type', 'local')->count();
    $featuredCount  = $products->getCollection()->where('featured', true)->count();
@endphp

{{-- Page Head --}}
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Catalog</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">Products</li>
            </ol>
        </nav>
    </div>
    <div class="ms-auto d-flex gap-2">
        {{-- Grid / List toggle --}}
        <div class="btn-group" role="group" id="viewToggle">
            <button type="button" class="btn btn-outline-secondary radius-30 px-3 active" id="btnGrid" title="Grid view">
                <i class="bx bx-grid-alt"></i>
            </button>
            <button type="button" class="btn btn-outline-secondary radius-30 px-3" id="btnList" title="List view">
                <i class="bx bx-list-ul"></i>
            </button>
        </div>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary radius-30 px-4">
            <i class="bx bx-plus me-1"></i>New Product
        </a>
    </div>
</div>

{{-- Stats --}}
<div class="row row-cols-2 row-cols-md-4 g-3 mb-4">
    <div class="col">
        <div class="card radius-10 border-0 shadow-sm mb-0 h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="widgets-icons bg-light-primary text-primary rounded-3 flex-shrink-0"><i class="bx bx-box fs-4"></i></div>
                <div>
                    <p class="mb-0 small text-muted">Total (page)</p>
                    <h4 class="mb-0 fw-bold text-dark">{{ $totalProducts }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card radius-10 border-0 shadow-sm mb-0 h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="widgets-icons bg-light-success text-success rounded-3 flex-shrink-0"><i class="bx bx-check-circle fs-4"></i></div>
                <div>
                    <p class="mb-0 small text-muted">Active</p>
                    <h4 class="mb-0 fw-bold text-success">{{ $activeProducts }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card radius-10 border-0 shadow-sm mb-0 h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="widgets-icons bg-light-info text-info rounded-3 flex-shrink-0"><i class="bx bx-store fs-4"></i></div>
                <div>
                    <p class="mb-0 small text-muted">Local Source</p>
                    <h4 class="mb-0 fw-bold text-info">{{ $localProducts }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card radius-10 border-0 shadow-sm mb-0 h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="widgets-icons bg-light-warning text-warning rounded-3 flex-shrink-0"><i class="bx bx-star fs-4"></i></div>
                <div>
                    <p class="mb-0 small text-muted">Featured</p>
                    <h4 class="mb-0 fw-bold text-warning">{{ $featuredCount }}</h4>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filter Card --}}
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body p-3">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-md-6 col-lg-5">
                <div class="position-relative">
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="form-control ps-5 radius-30"
                           placeholder="Search product name or SKU…">
                    <span class="position-absolute top-50 translate-middle-y ms-3 text-muted"><i class="bx bx-search"></i></span>
                </div>
            </div>
            <div class="col-md-3 col-lg-2">
                <select name="source_type" class="form-select radius-30">
                    <option value="">All Sources</option>
                    <option value="local"  @selected(request('source_type') === 'local')>Local</option>
                    <option value="global" @selected(request('source_type') === 'global')>Global</option>
                </select>
            </div>
            <div class="col-md-3 col-lg-2">
                <select name="status" class="form-select radius-30">
                    <option value="">All Status</option>
                    <option value="1" @selected(request('status') === '1')>Active</option>
                    <option value="0" @selected(request('status') === '0')>Inactive</option>
                </select>
            </div>
            <div class="col-auto d-flex gap-2">
                <button class="btn btn-primary radius-30 px-4">
                    <i class="bx bx-filter-alt me-1"></i>Filter
                </button>
                @if(request()->hasAny(['search','source_type','status']))
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary radius-30 px-3">
                        <i class="bx bx-x"></i> Clear
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Flash --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
        <i class="bx bx-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- === GRID VIEW === --}}
<div id="gridView">
    @if($products->isEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bx bx-package fs-1 text-muted mb-3 d-block"></i>
                <h5 class="text-muted">No products found</h5>
                <p class="text-muted small mb-3">Try adjusting your filters or add a new product.</p>
                <a href="{{ route('admin.products.create') }}" class="btn btn-primary radius-30 px-4">
                    <i class="bx bx-plus me-1"></i>Add Product
                </a>
            </div>
        </div>
    @else
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 g-4">
            @foreach($products as $product)
            <div class="col">
                <div class="card h-100 border-0 shadow-sm product-card radius-10 overflow-hidden position-relative">

                    {{-- Featured badge --}}
                    @if($product->featured)
                        <span class="position-absolute top-0 start-0 m-2 badge rounded-pill"
                              style="background:linear-gradient(135deg,#f5a623,#d4881e);font-size:.68rem;z-index:2;">
                            <i class="bx bx-star me-1"></i>Featured
                        </span>
                    @endif

                    {{-- Status dot --}}
                    <span class="position-absolute top-0 end-0 m-2 z-2">
                        @if($product->status)
                            <span class="badge bg-success rounded-pill px-2 py-1" style="font-size:.65rem;">Active</span>
                        @else
                            <span class="badge bg-secondary rounded-pill px-2 py-1" style="font-size:.65rem;">Inactive</span>
                        @endif
                    </span>

                    {{-- Product Image --}}
                    <a href="{{ route('admin.products.show', $product) }}" class="d-block product-img-wrap bg-light" style="height:200px;overflow:hidden;">
                        <img src="{{ $product->image_url }}"
                             alt="{{ $product->name }}"
                             loading="lazy"
                             class="w-100 h-100 product-thumb"
                             style="object-fit:cover;transition:transform .35s ease;">
                    </a>

                    {{-- Card Body --}}
                    <div class="card-body p-3 d-flex flex-column">
                        {{-- Category badge --}}
                        <div class="mb-1">
                            <span class="badge rounded-pill text-secondary bg-light border small px-2">
                                {{ $product->category?->name ?: 'Uncategorised' }}
                            </span>
                            <span class="badge rounded-pill text-secondary bg-light border small px-2 ms-1 text-uppercase">
                                {{ $product->source_type }}
                            </span>
                        </div>

                        {{-- Name --}}
                        <h6 class="mb-1 fw-bold text-dark lh-sm" style="font-size:.9rem;min-height:2.6rem;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                            {{ $product->name }}
                        </h6>

                        {{-- SKU --}}
                        <p class="mb-2 text-muted" style="font-size:.75rem;">SKU: {{ $product->sku ?: '—' }}</p>

                        {{-- Pricing --}}
                        <div class="d-flex align-items-baseline gap-2 mb-3 mt-auto">
                            <span class="fw-bold text-dark" style="font-size:1rem;">
                                ₦{{ number_format($product->sale_price ?: $product->price, 2) }}
                            </span>
                            @if($product->sale_price && $product->sale_price < $product->price)
                                <span class="text-muted text-decoration-line-through small">
                                    ₦{{ number_format($product->price, 2) }}
                                </span>
                            @endif
                        </div>

                        {{-- Stock --}}
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="small text-muted">Qty in stock</span>
                            <span class="fw-bold {{ $product->quantity < 5 ? 'text-danger' : 'text-dark' }} small">
                                {{ $product->quantity }}
                                @if($product->quantity < 5)
                                    <i class="bx bx-error-circle ms-1" title="Low stock"></i>
                                @endif
                            </span>
                        </div>

                        {{-- Actions --}}
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.products.show', $product) }}"
                               class="btn btn-sm btn-outline-primary radius-30 flex-fill text-center">
                                <i class="bx bx-show me-1"></i>View
                            </a>
                            <a href="{{ route('admin.products.edit', $product) }}"
                               class="btn btn-sm btn-primary radius-30 flex-fill text-center">
                                <i class="bx bx-edit me-1"></i>Edit
                            </a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                                  onsubmit="return confirm('Delete \'{{ addslashes($product->name) }}\'?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger radius-30 px-2" title="Delete">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>

{{-- === LIST VIEW (hidden by default) === --}}
<div id="listView" class="d-none">
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width:64px;"></th>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Source</th>
                            <th>Price</th>
                            <th>Qty</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                        <tr>
                            <td class="ps-3">
                                <div style="width:48px;height:48px;border-radius:8px;overflow:hidden;background:#f4f4f4;">
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                         class="w-100 h-100" style="object-fit:cover;" loading="lazy">
                                </div>
                            </td>
                            <td>
                                <strong class="text-dark d-block">{{ $product->name }}</strong>
                                <small class="text-muted">{{ $product->sku ?: 'No SKU' }}</small>
                            </td>
                            <td><span class="text-secondary fw-semibold">{{ $product->category?->name ?: '—' }}</span></td>
                            <td><span class="badge bg-light text-secondary border text-uppercase small">{{ $product->source_type }}</span></td>
                            <td class="fw-bold text-dark">₦{{ number_format($product->sale_price ?: $product->price, 2) }}</td>
                            <td class="{{ $product->quantity < 5 ? 'text-danger fw-bold' : '' }}">{{ $product->quantity }}</td>
                            <td>
                                @if($product->status)
                                    <span class="badge rounded-pill bg-success-subtle text-success px-2">Active</span>
                                @else
                                    <span class="badge rounded-pill bg-secondary-subtle text-secondary px-2">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <div class="d-inline-flex gap-2 align-items-center">
                                    <a href="{{ route('admin.products.show', $product) }}" class="btn btn-sm btn-outline-primary radius-30 px-3">View</a>
                                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-primary radius-30 px-3">Edit</a>
                                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                                          onsubmit="return confirm('Delete this product?')" class="mb-0">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger radius-30 px-3">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">No products found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Pagination --}}
@if($products->hasPages())
    <div class="mt-4 d-flex justify-content-center">
        {{ $products->links() }}
    </div>
@endif
@endsection

@section('page_scripts')
<style>
/* Product grid card hover effects */
.product-card {
    transition: transform .25s ease, box-shadow .25s ease;
}
.product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 36px rgba(0,0,0,.10) !important;
}
.product-card:hover .product-thumb {
    transform: scale(1.06);
}
.product-img-wrap { border-bottom: 1px solid rgba(0,0,0,.05); }

/* Toggle button states */
#viewToggle .btn.active {
    background: var(--bs-primary);
    color: #fff;
    border-color: var(--bs-primary);
}
</style>

<script>
(function () {
    var gridView   = document.getElementById('gridView');
    var listView   = document.getElementById('listView');
    var btnGrid    = document.getElementById('btnGrid');
    var btnList    = document.getElementById('btnList');
    var storageKey = 'kiosk_products_view';

    function showGrid() {
        gridView.classList.remove('d-none');
        listView.classList.add('d-none');
        btnGrid.classList.add('active');
        btnList.classList.remove('active');
        localStorage.setItem(storageKey, 'grid');
    }

    function showList() {
        listView.classList.remove('d-none');
        gridView.classList.add('d-none');
        btnList.classList.add('active');
        btnGrid.classList.remove('active');
        localStorage.setItem(storageKey, 'list');
    }

    btnGrid.addEventListener('click', showGrid);
    btnList.addEventListener('click', showList);

    // Restore last preference
    if (localStorage.getItem(storageKey) === 'list') {
        showList();
    }
})();
</script>
@endsection
