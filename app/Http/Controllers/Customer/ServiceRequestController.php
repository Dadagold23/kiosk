<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ServiceRequest;
use App\Services\ModuleReviewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\PaystackService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ServiceRequestController extends Controller
{
    public function index()
    {
        $requests = ServiceRequest::with('category', 'assignedStaff')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('customer.services.index', compact('requests'));
    }

    public function create()
    {
        $categories = Category::where('type', 'service')
            ->where('status', true)
            ->orderBy('name')
            ->get();

        return view('customer.services.create', compact('categories'));
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
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:4000'],
            'location' => ['nullable', 'string', 'max:255'],
            'preferred_date' => ['nullable', 'date'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'images.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $fee = 5000;
        $storedImages = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $storedImages[] = $image->store('service-requests', 'public');
            }
        }

        DB::transaction(function () use ($validated, $fee, $storedImages, &$serviceRequest, &$payment) {
            $serviceRequest = ServiceRequest::create([
                'user_id' => auth()->id(),
                'category_id' => $validated['category_id'],
                'title' => $validated['title'],
                'description' => $validated['description'],
                'location' => $validated['location'] ?? null,
                'preferred_date' => $validated['preferred_date'] ?? null,
                'budget' => $validated['budget'] ?? null,
                'images' => $storedImages,
                'status' => 'pending',
                'payment_status' => 'pending',
                'fee' => $fee,
            ]);

            $payment = $serviceRequest->payments()->create([
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
                'reference' => 'SRV-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(6)),
                'status' => 'pending',
                'meta' => [
                    'type' => 'service_request_fee',
                ],
            ]);
        });

        try {
            $paystackData = $paystackService->initializeTransaction($payment, [
                'service_request_id' => $serviceRequest->id,
                'customer_name' => auth()->user()->name,
            ]);

            $payment->update([
                'meta' => array_merge($payment->meta ?? [], [
                    'paystack_initialization' => $paystackData,
                ]),
            ]);

            return redirect()->away((string) ($paystackData['authorization_url'] ?? route('customer.services.show', $serviceRequest)));
        } catch (\Throwable $e) {
            Log::error('Paystack initialization failed for service request', [
                'service_request_id' => $serviceRequest->id,
                'reference' => $payment->reference ?? null,
                'message' => $e->getMessage(),
            ]);

            return redirect()->route('customer.services.show', $serviceRequest)
                ->with('error', 'Your service request was created, but we could not open Paystack. You can retry payment from this page.');
        }
    }

    public function show(ServiceRequest $serviceRequest, ModuleReviewService $moduleReviewService)
    {
        abort_unless($serviceRequest->user_id === auth()->id(), 403);

        $serviceRequest->load('category', 'assignedStaff', 'payments', 'trackingEvents');
        $existingReview = $serviceRequest->reviews()->where('user_id', auth()->id())->first();
        $canSubmitReview = $moduleReviewService->isEligible($serviceRequest);

        return view('customer.services.show', compact('serviceRequest', 'existingReview', 'canSubmitReview'));
    }

    public function pay(ServiceRequest $serviceRequest, PaystackService $paystackService)
    {
        abort_unless($serviceRequest->user_id === auth()->id(), 403);

        if (! $paystackService->isConfigured()) {
            return back()->with('error', 'Paystack keys are not configured yet. Add your live or test keys before using checkout.');
        }

        if (! $paystackService->acceptsCustomerEmail((string) auth()->user()->email)) {
            return redirect()->route('profile.edit')
                ->with('error', 'Please update your profile with a valid public email address before using Paystack checkout.');
        }

        $payment = DB::transaction(function () use ($serviceRequest) {
            return $serviceRequest->payments()->create([
                'user_id' => auth()->id(),
                'amount' => $serviceRequest->fee,
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
                'reference' => 'SRV-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(6)),
                'status' => 'pending',
                'meta' => [
                    'type' => 'service_request_fee',
                    'retry' => true,
                ],
            ]);
        });

        try {
            $paystackData = $paystackService->initializeTransaction($payment, [
                'service_request_id' => $serviceRequest->id,
                'customer_name' => auth()->user()->name,
            ]);

            $payment->update([
                'meta' => array_merge($payment->meta ?? [], [
                    'paystack_initialization' => $paystackData,
                ]),
            ]);

            return redirect()->away((string) ($paystackData['authorization_url'] ?? route('customer.services.show', $serviceRequest)));
        } catch (\Throwable $e) {
            Log::error('Paystack initialization failed for service request retry', [
                'service_request_id' => $serviceRequest->id,
                'reference' => $payment->reference ?? null,
                'message' => $e->getMessage(),
            ]);

            return redirect()->route('customer.services.show', $serviceRequest)
                ->with('error', 'We could not reopen Paystack right now. Please try again shortly.');
        }
    }
}
