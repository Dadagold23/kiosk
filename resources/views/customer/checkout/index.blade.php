@extends('layouts.customer')

@section('customer_page_title', 'Checkout')
@section('customer_page_subtitle', 'Review your details, confirm the transaction terms, and continue into secure Paystack payment.')

@section('customer_body')
<div class="customer-page-grid">
    <div class="row g-4">
        <div class="col-xl-7">
            <div class="feature-card customer-page-block">
                <div class="mb-4">
                    <div class="customer-eyebrow">Delivery</div>
                    <h3 class="customer-section-title">Delivery information</h3>
                    <p class="customer-section-copy">Confirm where the order should go and add any instructions your delivery team should know.</p>
                </div>

                <form action="{{ route('checkout.store') }}" method="POST">
                    @csrf

                    <div class="customer-field mb-3">
                        <label>Delivery Address</label>
                        <textarea name="delivery_address" rows="4" class="form-control @error('delivery_address') is-invalid @enderror" placeholder="Enter full delivery address">{{ old('delivery_address', $defaultDeliveryAddress) }}</textarea>
                        @error('delivery_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @if(blank($defaultDeliveryAddress))
                        <div class="customer-panel-note mb-3">
                            Add your delivery profile in <a href="{{ route('profile.edit') }}">Profile</a> so checkout can prefill your shipment address and contact details automatically.
                        </div>
                    @endif

                    <div class="customer-field mb-4">
                        <label>Order Notes</label>
                        <textarea name="notes" rows="4" class="form-control @error('notes') is-invalid @enderror" placeholder="Any delivery instructions, landmarks, or special handling?">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="customer-panel-note mb-4">
                        <div class="fw-semibold mb-2">Payment flow</div>
                        <div class="small text-muted">Clicking the button below creates your order and sends you to Paystack for secure payment. After verification, Kiosk will bring you back to the shop so you can continue browsing.</div>
                    </div>

                    <div class="customer-panel-note mb-4">
                        <div class="fw-semibold mb-2">Before you place this order</div>
                        <div class="small text-muted mb-2">By continuing, you confirm that your order details, delivery address, and payment intent are correct.</div>
                        <div class="small text-muted">You are also agreeing to Kiosk's <a href="{{ route('info.privacy') }}">Privacy Notice</a>, <a href="{{ route('info.shipping') }}">Shipping and Service Terms</a>, <a href="{{ route('info.returns') }}">Returns and Support Terms</a>, and <a href="{{ route('info.faqs') }}">Checkout Terms and Customer Agreement</a>.</div>
                    </div>

                    <div class="customer-field mb-4">
                        <label class="d-flex align-items-start gap-2">
                            <input type="checkbox" name="accept_terms" value="1" class="mt-1 @error('accept_terms') is-invalid @enderror" @checked(old('accept_terms'))>
                            <span class="small text-muted">
                                I have read and accept Kiosk's
                                <a href="{{ route('info.privacy') }}">Privacy Notice</a>,
                                <a href="{{ route('info.shipping') }}">Shipping and Service Terms</a>,
                                <a href="{{ route('info.returns') }}">Returns and Support Terms</a>, and
                                <a href="{{ route('info.faqs') }}">Checkout Terms and Customer Agreement</a>.
                            </span>
                        </label>
                        @error('accept_terms')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <button class="customer-btn-primary btn w-100">Place Order and Pay with Paystack</button>
                </form>
            </div>
        </div>

        <div class="col-xl-5">
            <div class="feature-card customer-page-block">
                <div class="mb-4">
                    <div class="customer-eyebrow">Summary</div>
                    <h3 class="customer-section-title">Order summary</h3>
                </div>

                <div class="d-grid gap-3 mb-4">
                    @foreach($cart->items as $item)
                        <div class="customer-info-card">
                            <div class="d-flex justify-content-between gap-3">
                                <div>
                                    <div class="fw-semibold">{{ $item->item_name }}</div>
                                    <div class="small text-muted">Qty: {{ $item->qty }}</div>
                                </div>
                                <div class="fw-semibold">&#8358;{{ number_format($item->subtotal, 2) }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="customer-info-grid mb-3">
                    <div class="customer-info-card">
                        <span class="label">Subtotal</span>
                        <span class="value">&#8358;{{ number_format($breakdown['subtotal'], 2) }}</span>
                    </div>
                    <div class="customer-info-card">
                        <span class="label">Delivery Fee</span>
                        <span class="value">&#8358;{{ number_format($breakdown['delivery_fee'], 2) }}</span>
                    </div>
                    <div class="customer-info-card">
                        <span class="label">Service Charge</span>
                        <span class="value">&#8358;{{ number_format($breakdown['service_charge'], 2) }}</span>
                    </div>
                    <div class="customer-info-card">
                        <span class="label">Total</span>
                        <span class="value text-primary">&#8358;{{ number_format($breakdown['total'], 2) }}</span>
                    </div>
                </div>

                <div class="customer-panel-note">
                    <div class="fw-semibold mb-2">Secure Gateway</div>
                    <div class="small text-muted">Paystack redirect checkout</div>
                    <div class="small text-muted">Currency: {{ config('kiosk.payments.currency', 'NGN') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
