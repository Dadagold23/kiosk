<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\ConsultancyRequest;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ServiceRequest;
use App\Services\PaymentService;
use App\Services\PaystackService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaystackController extends Controller
{
    public function callback(Request $request, PaystackService $paystackService, PaymentService $paymentService)
    {
        $reference = (string) ($request->string('reference')->value() ?: $request->string('trxref')->value());

        if ($reference === '') {
            return redirect()->route('shop.index')->with('error', 'Payment reference was missing from the Paystack callback.');
        }

        $payment = Payment::with('payable', 'user')->where('reference', $reference)->first();

        if (! $payment) {
            return redirect()->route('shop.index')->with('error', 'We could not match that payment reference in Kiosk.');
        }

        try {
            $transaction = $paystackService->verifyTransaction($reference);

            if (
                data_get($transaction, 'status') !== 'success'
                || (int) data_get($transaction, 'amount') !== (int) round($payment->amount * 100)
            ) {
                $paymentService->markAsUnderReview($payment, [
                    'paystack_verification' => $transaction,
                    'callback_issue' => 'Verification response did not meet success checks.',
                ]);

                return $this->redirectToOrderOrShop(
                    $payment,
                    'error',
                    'Payment is being reviewed. We could not confirm it automatically yet.'
                );
            }

            $paymentService->syncVerifiedPaystackPayment($payment, $transaction);

            if ($payment->payable instanceof Order) {
                return redirect()->route('shop.index')
                    ->with('success', 'Payment verified successfully. Your order is now in processing and you can continue shopping.');
            }

            return $this->redirectToOrderOrShop(
                $payment,
                'success',
                'Payment verified successfully. Your request is now in processing.'
            );
        } catch (\Throwable $e) {
            Log::warning('Paystack callback verification failed', [
                'reference' => $reference,
                'message' => $e->getMessage(),
            ]);

            return $this->redirectToOrderOrShop(
                $payment,
                'error',
                'We could not confirm your payment yet. Please wait a moment or check your order again shortly.'
            );
        }
    }

    public function webhook(Request $request, PaystackService $paystackService, PaymentService $paymentService)
    {
        $payload = $request->getContent();
        $signature = $request->header('x-paystack-signature');

        if (! $paystackService->hasValidSignature($payload, $signature)) {
            return response()->json(['message' => 'Invalid signature'], 401);
        }

        $event = json_decode($payload, true);

        if (! is_array($event)) {
            return response()->json(['message' => 'Invalid payload'], 400);
        }

        if (($event['event'] ?? null) !== 'charge.success') {
            return response()->json(['message' => 'Event acknowledged']);
        }

        $reference = (string) data_get($event, 'data.reference');

        if ($reference === '') {
            return response()->json(['message' => 'Missing reference'], 422);
        }

        $payment = Payment::with('payable')->where('reference', $reference)->first();

        if (! $payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        if ((int) data_get($event, 'data.amount') !== (int) round($payment->amount * 100)) {
            $paymentService->markAsUnderReview($payment, [
                'paystack_webhook' => $event['data'] ?? [],
                'webhook_issue' => 'Webhook amount mismatch.',
            ]);

            return response()->json(['message' => 'Amount mismatch'], 202);
        }

        $paymentService->syncVerifiedPaystackPayment($payment, $event['data'] ?? []);

        return response()->json(['message' => 'Webhook processed']);
    }

    private function redirectToOrderOrShop(Payment $payment, string $flashType, string $message)
    {
        if ($payment->payable instanceof Order) {
            return redirect()->route('orders.show', $payment->payable)->with($flashType, $message);
        }

        if ($payment->payable instanceof ServiceRequest) {
            return redirect()->route('customer.services.show', $payment->payable)->with($flashType, $message);
        }

        if ($payment->payable instanceof ConsultancyRequest) {
            return redirect()->route('customer.consultancy.show', $payment->payable)->with($flashType, $message);
        }

        if ($payment->payable instanceof Booking) {
            return redirect()->route('customer.bookings.show', $payment->payable)->with($flashType, $message);
        }

        if ($payment->payable && method_exists($payment->payable, 'getRouteKey')) {
            return redirect()->route('orders.show', $payment->payable)->with($flashType, $message);
        }

        return redirect()->route('shop.index')->with($flashType, $message);
    }
}
