<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ConsultancyRequest;
use App\Services\ModuleReviewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\PaystackService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ConsultancyRequestController extends Controller
{
    public function index()
    {
        $requests = ConsultancyRequest::with('category', 'assignedConsultant')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('customer.consultancy.index', compact('requests'));
    }

    public function create()
    {
        $categories = Category::where('type', 'consultancy')
            ->where('status', true)
            ->orderBy('name')
            ->get();

        return view('customer.consultancy.create', compact('categories'));
    }

    public function store(Request $request, PaystackService $paystackService)
    {
        if (! $paystackService->isConfigured()) {
            return back()->with('error', 'Paystack keys are not configured yet. Add your live or test keys before using checkout.');
        }

        if (! $paystackService->acceptsCustomerEmail((string) auth()->user()->email)) {
            return redirect()->route('profile.edit')
                ->with('error', 'Please update your profile with a valid public email address before using Paystack checkout.');
        }

        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:4000'],
            'preferred_date' => ['nullable', 'date'],
        ]);

        $fee = 7500;

        DB::transaction(function () use ($validated, $fee, &$consultancyRequest, &$payment) {
            $consultancyRequest = ConsultancyRequest::create([
                'user_id' => auth()->id(),
                'category_id' => $validated['category_id'],
                'subject' => $validated['subject'],
                'description' => $validated['description'],
                'preferred_date' => $validated['preferred_date'] ?? null,
                'status' => 'pending',
                'payment_status' => 'pending',
                'fee' => $fee,
            ]);

            $payment = $consultancyRequest->payments()->create([
                'user_id' => auth()->id(),
                'amount' => $fee,
                'currency' => config('kiosk.payments.currency', 'NGN'),
                'payment_method' => 'paystack',
                'payer_name' => auth()->user()->billing_name ?: auth()->user()->name,
                'payer_email' => auth()->user()->billing_email ?: auth()->user()->email,
                'payer_phone' => auth()->user()->billing_phone ?: auth()->user()->phone,
                'billing_address' => auth()->user()->billingAddressForPayment(),
                'delivery_address_snapshot' => auth()->user()->deliveryAddress(),
                'customer_profile_snapshot' => [
                    'name' => auth()->user()->name,
                    'email' => auth()->user()->email,
                    'phone' => auth()->user()->phone,
                    'kyc_status' => auth()->user()->kyc_status,
                    'identity_type' => auth()->user()->identity_type,
                ],
                'gateway' => 'paystack',
                'reference' => 'CON-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(6)),
                'status' => 'pending',
                'meta' => [
                    'type' => 'consultancy_fee',
                ],
            ]);
        });

        try {
            $paystackData = $paystackService->initializeTransaction($payment, [
                'consultancy_request_id' => $consultancyRequest->id,
                'customer_name' => auth()->user()->name,
            ]);

            $payment->update([
                'meta' => array_merge($payment->meta ?? [], [
                    'paystack_initialization' => $paystackData,
                ]),
            ]);

            return redirect()->away((string) ($paystackData['authorization_url'] ?? route('customer.consultancy.show', $consultancyRequest)));
        } catch (\Throwable $e) {
            Log::error('Paystack initialization failed for consultancy request', [
                'consultancy_request_id' => $consultancyRequest->id,
                'reference' => $payment->reference ?? null,
                'message' => $e->getMessage(),
            ]);

            return redirect()->route('customer.consultancy.show', $consultancyRequest)
                ->with('error', 'Your consultancy request was created, but we could not open Paystack. You can retry payment from this page.');
        }
    }

    public function show(ConsultancyRequest $consultancyRequest, ModuleReviewService $moduleReviewService)
    {
        abort_unless($consultancyRequest->user_id === auth()->id(), 403);

        $consultancyRequest->load('category', 'assignedConsultant', 'payments');
        $existingReview = $consultancyRequest->reviews()->where('user_id', auth()->id())->first();
        $canSubmitReview = $moduleReviewService->isEligible($consultancyRequest);

        return view('customer.consultancy.show', compact('consultancyRequest', 'existingReview', 'canSubmitReview'));
    }

    public function pay(ConsultancyRequest $consultancyRequest, PaystackService $paystackService)
    {
        abort_unless($consultancyRequest->user_id === auth()->id(), 403);

        if (! $paystackService->isConfigured()) {
            return back()->with('error', 'Paystack keys are not configured yet. Add your live or test keys before using checkout.');
        }

        if (! $paystackService->acceptsCustomerEmail((string) auth()->user()->email)) {
            return redirect()->route('profile.edit')
                ->with('error', 'Please update your profile with a valid public email address before using Paystack checkout.');
        }

        $payment = DB::transaction(function () use ($consultancyRequest) {
            return $consultancyRequest->payments()->create([
                'user_id' => auth()->id(),
                'amount' => $consultancyRequest->fee,
                'currency' => config('kiosk.payments.currency', 'NGN'),
                'payment_method' => 'paystack',
                'payer_name' => auth()->user()->billing_name ?: auth()->user()->name,
                'payer_email' => auth()->user()->billing_email ?: auth()->user()->email,
                'payer_phone' => auth()->user()->billing_phone ?: auth()->user()->phone,
                'billing_address' => auth()->user()->billingAddressForPayment(),
                'delivery_address_snapshot' => auth()->user()->deliveryAddress(),
                'customer_profile_snapshot' => [
                    'name' => auth()->user()->name,
                    'email' => auth()->user()->email,
                    'phone' => auth()->user()->phone,
                    'kyc_status' => auth()->user()->kyc_status,
                    'identity_type' => auth()->user()->identity_type,
                ],
                'gateway' => 'paystack',
                'reference' => 'CON-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(6)),
                'status' => 'pending',
                'meta' => [
                    'type' => 'consultancy_fee',
                    'retry' => true,
                ],
            ]);
        });

        try {
            $paystackData = $paystackService->initializeTransaction($payment, [
                'consultancy_request_id' => $consultancyRequest->id,
                'customer_name' => auth()->user()->name,
            ]);

            $payment->update([
                'meta' => array_merge($payment->meta ?? [], [
                    'paystack_initialization' => $paystackData,
                ]),
            ]);

            return redirect()->away((string) ($paystackData['authorization_url'] ?? route('customer.consultancy.show', $consultancyRequest)));
        } catch (\Throwable $e) {
            Log::error('Paystack initialization failed for consultancy request retry', [
                'consultancy_request_id' => $consultancyRequest->id,
                'reference' => $payment->reference ?? null,
                'message' => $e->getMessage(),
            ]);

            return redirect()->route('customer.consultancy.show', $consultancyRequest)
                ->with('error', 'We could not reopen Paystack right now. Please try again shortly.');
        }
    }
}
