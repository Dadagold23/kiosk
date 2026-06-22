@extends('layouts.admin')

@section('meta_title', 'Create Category | Kiosk')
@section('page_title', 'Create Category')
@section('page_subtitle', 'Define a new platform category with cleaner labeling and operational status control')

@section('content')
<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Catalog</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Categories</a></li>
                <li class="breadcrumb-item active" aria-current="page">Create Category</li>
            </ol>
        </nav>
    </div>
    <div class="ms-auto">
        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary radius-30"><i class="bx bx-arrow-back me-1"></i>Back to List</a>
    </div>
</div>
<!--end breadcrumb-->

<div class="row">
    <div class="col-xl-9 mx-auto">
        <div class="card radius-10 border-0 shadow-sm">
            <div class="card-body p-4">
                <h5 class="mb-0 fw-bold">Add a New Category</h5>
                <p class="text-muted small mb-4">Create structured labels for products, managed services, consultancy, or booking flows.</p>
                <hr/>
                <form action="{{ route('admin.categories.store') }}" method="POST" class="mt-4">
                    @csrf
                    @include('admin.categories.partials.form', ['category' => null])
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
