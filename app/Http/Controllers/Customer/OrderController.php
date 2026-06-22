<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Services\ModuleReviewService;
use App\Services\PaystackService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['items', 'payments'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('customer.orders.index', compact('orders'));
    }

    public function show(Order $order, ModuleReviewService $moduleReviewService)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        $order->load('items.product.category', 'items.trackingEvents', 'payments');
        $existingReview = $order->reviews()->where('user_id', auth()->id())->first();
        $canSubmitReview = $moduleReviewService->isEligible($order);

        return view('customer.orders.show', compact('order', 'existingReview', 'canSubmitReview'));
    }

    public function success(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        $order->load('items', 'payments', 'user');

        return view('customer.orders.success', compact('order'));
    }

    public function pay(Order $order, PaystackService $paystackService)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        if (! $paystackService->isConfigured()) {
            return back()->with('error', 'Paystack keys are not configured yet. Add your live or test keys before using checkout.');
        }

        if (! $paystackService->acceptsCustomerEmail((string) auth()->user()->email)) {
            return redirect()->route('profile.edit')
                ->with('error', 'Please update your profile with a valid public email address before retrying Paystack payment.');
        }

        if ($order->payment_status === Payment::STATUS_PAID) {
            return back()->with('success', 'This order has already been paid for.');
        }

        $order->payments()
            ->whereIn('status', [Payment::STATUS_PENDING, Payment::STATUS_FAILED, Payment::STATUS_CANCELLED])
            ->where('gateway', 'paystack')
            ->update([
                'status' => Payment::STATUS_CANCELLED,
            ]);

        $reference = 'PAY-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(6));
        $user = auth()->user();
        $deliveryAddress = $order->delivery_address ?: $user->deliveryAddress();
        $payerName = $user->billing_name ?: $user->name;
        $payerEmail = $user->billing_email ?: $user->email;
        $payerPhone = $user->billing_phone ?: $user->phone ?: $user->delivery_phone;
        $billingAddress = $user->billingAddressForPayment();
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

        $payment = $order->payments()->create([
            'user_id' => auth()->id(),
            'amount' => $order->total,
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
                'channel' => 'paystack_retry_payment',
                'customer_profile_snapshot' => $customerProfileSnapshot,
            ],
        ]);

        $order->update([
            'payment_reference' => $reference,
            'payment_status' => Payment::STATUS_PENDING,
        ]);

        try {
            $paystackData = $paystackService->initializeTransaction($payment, [
                'order_no' => $order->order_no,
                'customer_name' => auth()->user()->name,
                'retry' => true,
            ]);

            $payment->update([
                'meta' => array_merge($payment->meta ?? [], [
                    'paystack_initialization' => $paystackData,
                ]),
            ]);

            return redirect()->away((string) ($paystackData['authorization_url'] ?? route('orders.show', $order->order_no)));
        } catch (\Throwable $e) {
            Log::error('Paystack retry initialization failed', [
                'order_no' => $order->order_no,
                'reference' => $payment->reference,
                'message' => $e->getMessage(),
            ]);

            return back()->with('error', 'We could not reopen Paystack right now. Please try again shortly.');
        }
    }
}
