@extends('layouts.admin')

@section('meta_title', 'Create Product | Kiosk')
@section('page_title', 'Create Product')
@section('page_subtitle', 'Add a new local or global product inside the refreshed catalog workspace.')

@section('content')
<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Catalog</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Products</a></li>
                <li class="breadcrumb-item active" aria-current="page">New Product</li>
            </ol>
        </nav>
    </div>
    <div class="ms-auto">
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary radius-30"><i class="bx bx-arrow-back me-1"></i>Back to Products</a>
    </div>
</div>
<!--end breadcrumb-->

<div class="card radius-10 border-0 shadow-sm">
    <div class="card-body p-4">
        <h5 class="mb-1 fw-bold text-dark">New Product Form</h5>
        <p class="text-muted small mb-4">Add product identity, source, pricing, stock, and media using the updated catalog form system.</p>

        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('admin.products.partials.form')
        </form>
    </div>
</div>
@endsection
