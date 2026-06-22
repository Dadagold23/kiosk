@extends('layouts.admin')

@section('meta_title', 'Product Details | Kiosk')
@section('page_title', 'Product Details')
@section('page_subtitle', 'Review catalog source, pricing, inventory, and media in the updated product detail view.')

@section('content')
<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Catalog</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Products</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
            </ol>
        </nav>
    </div>
    <div class="ms-auto">
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary radius-30 px-3"><i class="bx bx-edit me-1"></i>Edit Product</a>
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary radius-30 px-3"><i class="bx bx-arrow-back me-1"></i>Back</a>
        </div>
    </div>
</div>
<!--end breadcrumb-->

<div class="row g-4">
    <!-- Image Card Column -->
    <div class="col-lg-4">
        <div class="card radius-10 border-0 shadow-sm h-100 mb-0">
            <div class="card-body p-4 d-flex align-items-center justify-content-center">
                <div class="border rounded-4 overflow-hidden bg-light d-flex align-items-center justify-content-center w-100" style="min-height: 320px;">
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="img-fluid object-fit-cover w-100" style="max-height: 360px;">
                </div>
            </div>
        </div>
    </div>

    <!-- Details Column -->
    <div class="col-lg-8">
        <div class="card radius-10 border-0 shadow-sm h-100 mb-0">
            <div class="card-body p-4">
                <h5 class="mb-1 fw-bold text-dark">Catalog Details</h5>
                <p class="text-muted small mb-4">This product record sits inside the same premium details grid as the rest of the workspace.</p>

                <div class="row g-3 mb-4">
                    <div class="col-sm-6 col-md-4">
                        <div class="p-3 radius-10 bg-light border shadow-none h-100">
                            <small class="text-muted d-block text-uppercase mb-1">Category</small>
                            <h6 class="mb-0 fw-bold text-dark">{{ $product->category?->name ?: 'Unassigned' }}</h6>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="p-3 radius-10 bg-light border shadow-none h-100">
                            <small class="text-muted d-block text-uppercase mb-1">Source</small>
                            <h6 class="mb-0 fw-bold text-dark text-uppercase small">{{ $product->source_type }}</h6>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="p-3 radius-10 bg-light border shadow-none h-100">
                            <small class="text-muted d-block text-uppercase mb-1">Marketplace</small>
                            <h6 class="mb-0 fw-bold text-dark text-truncate">{{ $product->source_marketplace ?: 'N/A' }}</h6>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="p-3 radius-10 bg-light border shadow-none h-100">
                            <small class="text-muted d-block text-uppercase mb-1">SKU</small>
                            <h6 class="mb-0 fw-bold text-dark text-truncate">{{ $product->sku ?: 'N/A' }}</h6>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="p-3 radius-10 bg-light border shadow-none h-100">
                            <small class="text-muted d-block text-uppercase mb-1">Price</small>
                            <h6 class="mb-0 fw-bold text-dark text-nowrap">₦{{ number_format($product->price, 2) }}</h6>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="p-3 radius-10 bg-light border shadow-none h-100">
                            <small class="text-muted d-block text-uppercase mb-1">Sale Price</small>
                            <h6 class="mb-0 fw-bold text-dark text-nowrap">
                                {{ $product->sale_price ? '₦' . number_format($product->sale_price, 2) : 'N/A' }}
                            </h6>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="p-3 radius-10 bg-light border shadow-none h-100">
                            <small class="text-muted d-block text-uppercase mb-1">Quantity</small>
                            <h6 class="mb-0 fw-bold text-dark">{{ $product->quantity }}</h6>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="p-3 radius-10 bg-light border shadow-none h-100">
                            <small class="text-muted d-block text-uppercase mb-1">Visibility</small>
                            <h6 class="mb-0 fw-bold text-dark">{{ $product->featured ? 'Featured' : 'Standard' }}</h6>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="p-3 radius-10 bg-light border shadow-none h-100">
                            <small class="text-muted d-block text-uppercase mb-1">Status</small>
                            <h6 class="mb-0 fw-bold text-dark text-uppercase small">
                                {{ $product->status ? 'Active' : 'Inactive' }}
                            </h6>
                        </div>
                    </div>
                </div>

                <div class="p-3 radius-10 bg-light border shadow-none mb-0">
                    <span class="d-block text-uppercase small text-muted fw-bold mb-2">Description</span>
                    <p class="mb-0 text-dark small" style="white-space: pre-line;">{{ $product->description ?: 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
