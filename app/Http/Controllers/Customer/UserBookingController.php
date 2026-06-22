<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\ModuleReviewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\PaystackService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserBookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('customer.bookings.index', compact('bookings'));
    }

    public function create()
    {
        $types = [
            'hotel' => 'Hotel Booking',
            'resort' => 'Resort Booking',
            'lounge' => 'Lounge Reservation',
            'park' => 'Park Booking',
            'flight' => 'Flight Booking',
        ];

        return view('customer.bookings.create', compact('types'));
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
            'booking_type' => ['required', 'string', 'max:50'],
            'title' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'check_in_date' => ['nullable', 'date'],
            'check_out_date' => ['nullable', 'date', 'after_or_equal:check_in_date'],
            'travel_date' => ['nullable', 'date'],
            'persons' => ['required', 'integer', 'min:1', 'max:50'],
            'details' => ['nullable', 'string', 'max:4000'],
        ]);

        $fee = $validated['booking_type'] === 'flight' ? 10000 : 5000;

        DB::transaction(function () use ($validated, $fee, &$booking, &$payment) {
            $booking = Booking::create([
                'user_id' => auth()->id(),
                'booking_type' => $validated['booking_type'],
                'title' => $validated['title'] ?? null,
                'location' => $validated['location'] ?? null,
                'check_in_date' => $validated['check_in_date'] ?? null,
                'check_out_date' => $validated['check_out_date'] ?? null,
                'travel_date' => $validated['travel_date'] ?? null,
                'persons' => $validated['persons'],
                'details' => $validated['details'] ?? null,
                'status' => 'pending',
                'payment_status' => 'pending',
                'amount' => $fee,
            ]);

            $payment = $booking->payments()->create([
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
                'reference' => 'BKG-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(6)),
                'status' => 'pending',
                'meta' => [
                    'type' => 'booking_service_fee',
                ],
            ]);
        });

        try {
            $paystackData = $paystackService->initializeTransaction($payment, [
                'booking_id' => $booking->id,
                'booking_type' => $booking->booking_type,
                'customer_name' => auth()->user()->name,
            ]);

            $payment->update([
                'meta' => array_merge($payment->meta ?? [], [
                    'paystack_initialization' => $paystackData,
                ]),
            ]);

            return redirect()->away((string) ($paystackData['authorization_url'] ?? route('customer.bookings.show', $booking)));
        } catch (\Throwable $e) {
            Log::error('Paystack initialization failed for booking request', [
                'booking_id' => $booking->id,
                'reference' => $payment->reference ?? null,
                'message' => $e->getMessage(),
            ]);

            return redirect()->route('customer.bookings.show', $booking)
                ->with('error', 'Your booking request was created, but we could not open Paystack. You can retry payment from this page.');
        }
    }

    public function show(Booking $booking, ModuleReviewService $moduleReviewService)
    {
        abort_unless($booking->user_id === auth()->id(), 403);

        $booking->load('payments');
        $existingReview = $booking->reviews()->where('user_id', auth()->id())->first();
        $canSubmitReview = $moduleReviewService->isEligible($booking);

        return view('customer.bookings.show', compact('booking', 'existingReview', 'canSubmitReview'));
    }

    public function pay(Booking $booking, PaystackService $paystackService)
    {
        abort_unless($booking->user_id === auth()->id(), 403);

        if (! $paystackService->isConfigured()) {
            return back()->with('error', 'Paystack keys are not configured yet. Add your live or test keys before using checkout.');
        }

        if (! $paystackService->acceptsCustomerEmail((string) auth()->user()->email)) {
            return redirect()->route('profile.edit')
                ->with('error', 'Please update your profile with a valid public email address before using Paystack checkout.');
        }

        $payment = DB::transaction(function () use ($booking) {
            return $booking->payments()->create([
                'user_id' => auth()->id(),
                'amount' => $booking->amount,
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
                'reference' => 'BKG-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(6)),
                'status' => 'pending',
                'meta' => [
                    'type' => 'booking_service_fee',
                    'retry' => true,
                ],
            ]);
        });

        try {
            $paystackData = $paystackService->initializeTransaction($payment, [
                'booking_id' => $booking->id,
                'booking_type' => $booking->booking_type,
                'customer_name' => auth()->user()->name,
            ]);

            $payment->update([
                'meta' => array_merge($payment->meta ?? [], [
                    'paystack_initialization' => $paystackData,
                ]),
            ]);

            return redirect()->away((string) ($paystackData['authorization_url'] ?? route('customer.bookings.show', $booking)));
        } catch (\Throwable $e) {
            Log::error('Paystack initialization failed for booking retry', [
                'booking_id' => $booking->id,
                'reference' => $payment->reference ?? null,
                'message' => $e->getMessage(),
            ]);

            return redirect()->route('customer.bookings.show', $booking)
                ->with('error', 'We could not reopen Paystack right now. Please try again shortly.');
        }
    }
}
