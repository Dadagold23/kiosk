<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCheckoutRequest;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Payment;
use App\Notifications\NewOrderNotification;
use App\Services\PaystackService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $cart = Cart::with('items.product')
            ->firstOrCreate(['user_id' => auth()->id()]);

        if ($cart->items->isEmpty()) {
            return redirect()->route('shop.index')->with('error', 'Your cart is empty.');
        }

        if ($message = $this->validateCartItems($cart)) {
            return redirect()->route('cart.index')->with('error', $message);
        }

        $breakdown = $this->checkoutBreakdown($cart);
        $defaultDeliveryAddress = $user->deliveryAddress();

        return view('customer.checkout.index', compact('cart', 'breakdown', 'defaultDeliveryAddress'));
    }

    public function store(StoreCheckoutRequest $request, PaystackService $paystackService)
    {
        if (! $paystackService->isConfigured()) {
            return back()->with('error', 'Paystack keys are not configured yet. Add your live or test keys before using checkout.');
        }

        if (! $paystackService->acceptsCustomerEmail((string) auth()->user()->email)) {
            return redirect()->route('profile.edit')
                ->with('error', 'Please update your profile with a valid public email address before using Paystack checkout.');
        }

        $validated = $request->validated();
        $cart = Cart::with('items.product')->where('user_id', auth()->id())->first();

        if (! $cart || $cart->items->isEmpty()) {
            return redirect()->route('shop.index')->with('error', 'Your cart is empty.');
        }

        if ($message = $this->validateCartItems($cart)) {
            return redirect()->route('cart.index')->with('error', $message);
        }

        $breakdown = $this->checkoutBreakdown($cart);
        $reference = 'PAY-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(6));

        $user = auth()->user();
        $payerName = $user->billing_name ?: $user->name;
        $payerEmail = $user->billing_email ?: $user->email;
        $payerPhone = $user->billing_phone ?: $user->phone ?: $user->delivery_phone;
        $billingAddress = $user->billingAddressForPayment();
        $deliveryAddress = $validated['delivery_address'];
        $customerProfileSnapshot = [
            'customer_name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'alternate_phone' => $user->alternate_phone,
            'delivery_contact_name' => $user->delivery_contact_name ?: $user->name,
            'delivery_phone' => $user->delivery_phone ?: $user->phone,
            'delivery_address' => $deliveryAddress,
            'preferred_payment_method' => $user->preferred_payment_method,
            'billing_name' => $payerName,
            'billing_email' => $payerEmail,
            'billing_phone' => $payerPhone,
            'billing_address' => $billingAddress,
            'kyc_status' => $user->kyc_status,
            'identity_type' => $user->identity_type,
            'identity_country' => $user->identity_country,
        ];
        $termsAcceptance = [
            'accepted' => true,
            'accepted_at' => now()->toIso8601String(),
            'accepted_from_ip' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
        ];

        $order = DB::transaction(function () use ($cart, $validated, $breakdown, $reference, $payerName, $payerEmail, $payerPhone, $billingAddress, $deliveryAddress, $customerProfileSnapshot, $termsAcceptance) {
            $hasGlobal = $cart->items->contains(fn ($item) => $item->source_type === 'global');

            $order = Order::create([
                'user_id' => auth()->id(),
                'order_no' => 'KSK-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(5)),
                'order_type' => $hasGlobal ? 'global_shop' : 'local_shop',
                'subtotal' => $breakdown['subtotal'],
                'delivery_fee' => $breakdown['delivery_fee'],
                'service_charge' => $breakdown['service_charge'],
                'total' => $breakdown['total'],
                'payment_status' => Payment::STATUS_PENDING,
                'order_status' => Order::STATUS_PENDING,
                'payment_reference' => $reference,
                'delivery_address' => $deliveryAddress,
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($cart->items as $cartItem) {
                $order->items()->create([
                    'product_id' => $cartItem->product_id,
                    'product_name' => $cartItem->item_name,
                    'qty' => $cartItem->qty,
                    'unit_price' => $cartItem->unit_price,
                    'subtotal' => $cartItem->subtotal,
                    'fulfillment_status' => 'pending',
                    'meta' => [
                        'source_type' => $cartItem->source_type,
                        'source_marketplace' => $cartItem->source_marketplace,
                        'cart_meta' => $cartItem->meta,
                    ],
                ]);
            }

            $payment = $order->payments()->create([
                'user_id' => auth()->id(),
                'amount' => $breakdown['total'],
                'currency' => config('kiosk.payments.currency', 'NGN'),
                'payment_method' => 'paystack',
                'payer_name' => $payerName,
                'payer_email' => $payerEmail,
                'payer_phone' => $payerPhone,
                'billing_address' => $billingAddress,
                'delivery_address_snapshot' => $deliveryAddress,
                'customer_profile_snapshot' => $customerProfileSnapshot,
                'gateway' => 'paystack',
                'reference' => $reference,
                'status' => Payment::STATUS_PENDING,
                'meta' => [
                    'channel' => 'paystack_redirect_checkout',
                    'checkout_snapshot' => $breakdown,
                    'customer_profile_snapshot' => $customerProfileSnapshot,
                    'terms_acceptance' => $termsAcceptance,
                ],
            ]);

            return $order->setRelation('payments', collect([$payment]));
        });

        $payment = $order->payments->first();

        try {
            $paystackData = $paystackService->initializeTransaction($payment, [
                'order_no' => $order->order_no,
                'customer_name' => auth()->user()->name,
            ]);

            $payment->update([
                'meta' => array_merge($payment->meta ?? [], [
                    'paystack_initialization' => $paystackData,
                ]),
            ]);

            $cart->items()->delete();
            auth()->user()->notify(new NewOrderNotification($order));

            return redirect()->away((string) ($paystackData['authorization_url'] ?? route('orders.show', $order->order_no)));
        } catch (\Throwable $e) {
            Log::error('Paystack initialization failed', [
                'order_no' => $order->order_no,
                'reference' => $payment->reference,
                'message' => $e->getMessage(),
            ]);

            return redirect()->route('orders.show', $order->order_no)
                ->with('error', 'Your order was created, but we could not open Paystack. You can retry payment from this order page.');
        }
    }

    private function checkoutBreakdown(Cart $cart): array
    {
        $subtotal = (float) $cart->items->sum('subtotal');
        $deliveryFee = $subtotal > 0 ? 2500.0 : 0.0;
        $serviceCharge = $subtotal > 0 ? 500.0 : 0.0;

        return [
            'subtotal' => $subtotal,
            'delivery_fee' => $deliveryFee,
            'service_charge' => $serviceCharge,
            'total' => $subtotal + $deliveryFee + $serviceCharge,
        ];
    }

    private function validateCartItems(Cart $cart): ?string
    {
        foreach ($cart->items as $item) {
            if (! $item->product) {
                return 'One or more products in your cart are no longer available. Please review your cart.';
            }

            if (! $item->product->status) {
                return 'A product in your cart is no longer available for checkout. Please update your cart and try again.';
            }

            if ($this->requiresInventoryStockCheck($item->product) && $item->product->quantity < $item->qty) {
                return 'Some items in your cart exceed the available stock. Please update your quantities and try again.';
            }
        }

        return null;
    }

    private function requiresInventoryStockCheck($product): bool
    {
        return $product->source_type !== 'global';
    }
}
