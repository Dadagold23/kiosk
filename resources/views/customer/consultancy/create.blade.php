@extends('layouts.customer')

@section('customer_page_title', 'Request Consultancy')
@section('customer_page_subtitle', 'Submit your issue through the same Muara-style intake flow and route it to the right consultant.')

@include('partials.amerce.account-intake-styles')

@section('customer_body')
<div class="customer-page-grid">
    <div class="feature-card p-4 p-lg-5 amerce-intake-hero is-warning">
        <div class="row g-4 align-items-center">
            <div class="col-lg-8">
                <span class="customer-status-pill is-warning mb-3">Expert Support Intake</span>
                <h1 class="fw-bold mb-2">Describe the advisory issue clearly so it reaches the right consultant.</h1>
                <p class="amerce-intake-copy mb-0">Your case is reviewed after submission, then routed to the appropriate consultant for structured follow-up and delivery.</p>
            </div>
            <div class="col-lg-4">
                <div class="amerce-intake-stat">
                    <div class="label">Access Fee</div>
                    <div class="value">NGN 7,500.00</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 align-items-start">
        <div class="col-xl-8">
            <div class="feature-card customer-page-block">
                <div class="mb-4">
                    <div class="customer-eyebrow">Consultancy Intake</div>
                    <h3 class="customer-section-title">Request consultancy</h3>
                    <p class="customer-section-copy">Tell us what you need help with and we will route the case to the most relevant consultant.</p>
                </div>

                <form action="{{ route('customer.consultancy.store') }}" method="POST">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="amerce-field-block">
                                <label class="form-label">Consultancy Category</label>
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
                                <label class="form-label">Subject</label>
                                <input type="text" name="subject" value="{{ old('subject') }}" class="form-control @error('subject') is-invalid @enderror" placeholder="Enter consultancy subject">
                                @error('subject') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="amerce-field-block">
                                <label class="form-label">Description</label>
                                <textarea name="description" rows="6" class="form-control @error('description') is-invalid @enderror" placeholder="Explain your need in detail...">{{ old('description') }}</textarea>
                                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="amerce-field-block">
                                <label class="form-label">Preferred Date</label>
                                <input type="date" name="preferred_date" value="{{ old('preferred_date') }}" class="form-control @error('preferred_date') is-invalid @enderror">
                                @error('preferred_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <button class="customer-btn-primary btn mt-4">Submit Consultancy Request</button>
                </form>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="feature-card customer-page-block">
                <div class="customer-eyebrow">Before You Submit</div>
                <h3 class="customer-section-title">Access fee</h3>
                <div class="amerce-support-card mb-3">
                    <div class="small text-uppercase text-muted mb-1">Consultancy Fee</div>
                    <div class="fw-semibold">NGN 7,500.00</div>
                </div>
                <p class="customer-section-copy">You will be redirected to Paystack after submission so the request can move into assignment and review.</p>
            </div>
        </div>
    </div>
</div>
@endsection
