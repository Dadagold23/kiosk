@extends('layouts.admin')

@section('meta_title', 'Edit Category | Kiosk')
@section('page_title', 'Edit Category')
@section('page_subtitle', 'Update the naming, icon treatment, and active state of an existing category')

@section('content')
<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Catalog</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Categories</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Category</li>
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
                <h5 class="mb-0 fw-bold">Edit Category: {{ $category->name }}</h5>
                <p class="text-muted small mb-4">Refine how this category appears across intake, listing, and admin management screens.</p>
                <hr/>
                <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="mt-4">
                    @csrf
                    @method('PUT')
                    @include('admin.categories.partials.form', ['category' => $category])
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
