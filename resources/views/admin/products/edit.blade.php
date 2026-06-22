@extends('layouts.admin')

@section('meta_title', 'Edit Product | Kiosk')
@section('page_title', 'Edit Product')
@section('page_subtitle', 'Update source, pricing, visibility, and inventory details for this product.')

@section('content')
<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Catalog</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Products</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.products.show', $product) }}">{{ $product->name }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Product</li>
            </ol>
        </nav>
    </div>
    <div class="ms-auto">
        <a href="{{ route('admin.products.show', $product) }}" class="btn btn-outline-secondary radius-30"><i class="bx bx-arrow-back me-1"></i>Back to Details</a>
    </div>
</div>
<!--end breadcrumb-->

<div class="card radius-10 border-0 shadow-sm">
    <div class="card-body p-4">
        <h5 class="mb-1 fw-bold text-dark">Edit Product Form</h5>
        <p class="text-muted small mb-4">Adjust catalog fields without leaving the new softer product workspace.</p>

        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.products.partials.form')
        </form>
    </div>
</div>
@endsection
