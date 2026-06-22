@extends('layouts.customer')

@section('customer_page_title', 'Request a Service')
@section('customer_page_subtitle', 'Submit job details and move into the managed service workflow with a clearer intake experience.')

@include('partials.amerce.account-intake-styles')

@section('customer_body')
<div class="customer-page-grid">
    <div class="feature-card p-4 p-lg-5 amerce-intake-hero is-primary">
        <div class="row g-4 align-items-center">
            <div class="col-lg-8">
                <span class="customer-status-pill is-primary mb-3">Managed Service Intake</span>
                <h1 class="fw-bold mb-2">Send the team a clean job brief so review and assignment can start fast.</h1>
                <p class="amerce-intake-copy mb-0">After submission, the service desk validates scope, confirms the request fee, and routes the work to the right field team.</p>
            </div>
            <div class="col-lg-4">
                <div class="amerce-intake-stat">
                    <div class="label">Request Fee</div>
                    <div class="value">NGN 5,000.00</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 align-items-start">
        <div class="col-xl-8">
            <div class="feature-card customer-page-block">
                <div class="mb-4">
                    <div class="customer-eyebrow">Job Intake</div>
                    <h3 class="customer-section-title">Request a service</h3>
                    <p class="customer-section-copy">Share the job title, work details, location, timing, and support images in one place.</p>
                </div>

                <form action="{{ route('customer.services.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="amerce-field-block">
                                <label class="form-label">Service Category</label>
                                <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                                    <option value="">Select category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="amerce-field-block">
                                <label class="form-label">Job Title</label>
                                <input type="text" name="title" value="{{ old('title') }}" class="form-control @error('title') is-invalid @enderror" placeholder="Plumbing repair for leaking pipes">
                                @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="amerce-field-block">
                                <label class="form-label">Description</label>
                                <textarea name="description" rows="5" class="form-control @error('description') is-invalid @enderror" placeholder="Describe the work needed...">{{ old('description') }}</textarea>
                                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="amerce-field-block">
                                <label class="form-label">Location</label>
                                <input type="text" name="location" value="{{ old('location') }}" class="form-control @error('location') is-invalid @enderror" placeholder="Enter service location">
                                @error('location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="amerce-field-block">
                                <label class="form-label">Preferred Date</label>
                                <input type="date" name="preferred_date" value="{{ old('preferred_date') }}" class="form-control @error('preferred_date') is-invalid @enderror">
                                @error('preferred_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="amerce-field-block">
                                <label class="form-label">Budget</label>
                                <input type="number" step="0.01" name="budget" value="{{ old('budget') }}" class="form-control @error('budget') is-invalid @enderror" placeholder="Optional">
                                @error('budget') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="amerce-field-block">
                                <label class="form-label">Upload Images</label>
                                <input type="file" name="images[]" multiple class="form-control @error('images.*') is-invalid @enderror">
                                @error('images.*') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <button class="customer-btn-primary btn mt-4">Submit Service Request</button>
                </form>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="feature-card customer-page-block">
                <div class="customer-eyebrow">Before You Submit</div>
                <h3 class="customer-section-title">Request fee guide</h3>
                <p class="customer-section-copy mb-3">Your payment is collected after submission so the review and assignment workflow can begin without delay.</p>
                <div class="amerce-support-card">
                    <div class="small text-uppercase text-muted mb-1">Service Request Fee</div>
                    <div class="fw-semibold">NGN 5,000.00</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
