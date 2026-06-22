@extends('layouts.customer')

@section('customer_page_title', 'My Cart')
@section('customer_page_subtitle', 'Review selected items, adjust quantities, and move into the cleaner checkout flow.')

@section('customer_body')
<div class="customer-page-grid">
    <div class="feature-card customer-page-block">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <div class="customer-eyebrow">Cart Review</div>
                <h3 class="customer-section-title">My cart</h3>
                <p class="customer-section-copy">Your selected products stay here until you proceed to checkout.</p>
            </div>
            <a href="{{ route('shop.index') }}" class="customer-soft-button">Continue Shopping</a>
        </div>
    </div>

    @if($cart->items->isEmpty())
        <div class="feature-card customer-page-block">
            <div class="customer-panel-note">
                Your cart is empty. <a href="{{ route('shop.index') }}">Browse products</a>.
            </div>
        </div>
    @else
        <div class="row g-4">
            <div class="col-xl-8">
                <div class="feature-card customer-page-block">
                    <div class="d-grid gap-3">
                        @foreach($cart->items as $item)
                            <div class="customer-info-card">
                                <div class="row align-items-center g-3">
                                    <div class="col-md-2">
                                        <div class="bg-white rounded-4 d-flex align-items-center justify-content-center overflow-hidden" style="height:90px;">
                                            <img src="{{ $item->image_url }}" alt="{{ $item->item_name }}" class="img-fluid w-100 h-100 object-fit-cover">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="fw-semibold">{{ $item->item_name }}</div>
                                        <div class="small text-muted text-uppercase">{{ $item->source_type }}</div>
                                        @if($item->source_marketplace)
                                            <div class="small text-muted">{{ $item->source_marketplace }}</div>
                                        @endif
                                        @if($item->product?->source_type === 'global')
                                            <div class="small text-muted">Sourced on request. Quantity is confirmed during fulfillment.</div>
                                        @elseif($item->product)
                                            <div class="small text-muted">Available stock: {{ $item->product->quantity }}</div>
                                        @endif
                                    </div>
                                    <div class="col-md-2">
                                        <div class="small text-uppercase text-muted">Unit Price</div>
                                        <div class="fw-semibold">&#8358;{{ number_format($item->unit_price, 2) }}</div>
                                    </div>
                                    <div class="col-md-2">
                                        <form action="{{ route('cart.update', $item) }}" method="POST">
                                            @csrf
                                            <label class="small text-uppercase text-muted d-block mb-2">Qty</label>
                                            <input type="number" name="qty" min="1" max="100" value="{{ $item->qty }}" class="form-control" onchange="this.form.submit()">
                                        </form>
                                    </div>
                                    <div class="col-md-2 text-md-end">
                                        <div class="small text-uppercase text-muted">Subtotal</div>
                                        <div class="fw-bold text-primary mb-2">&#8358;{{ number_format($item->subtotal, 2) }}</div>
                                        <form action="{{ route('cart.destroy', $item) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">Remove</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <form action="{{ route('cart.clear') }}" method="POST" class="mt-3">
                        @csrf
                        <button class="btn btn-outline-danger">Clear Cart</button>
                    </form>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="feature-card customer-page-block">
                    <div class="customer-eyebrow">Summary</div>
                    <h3 class="customer-section-title">Cart summary</h3>

                    <div class="customer-info-grid mb-3">
                        <div class="customer-info-card">
                            <span class="label">Items</span>
                            <span class="value">{{ $cart->items->sum('qty') }}</span>
                        </div>
                        <div class="customer-info-card">
                            <span class="label">Subtotal</span>
                            <span class="value">&#8358;{{ number_format($cart->subtotal, 2) }}</span>
                        </div>
                    </div>

                    <a href="{{ route('checkout.index') }}" class="customer-btn-primary btn w-100">Proceed to Checkout</a>

                    <p class="customer-section-copy mt-3 mb-0">Review delivery details and Paystack checkout on the next screen before your order is created.</p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
