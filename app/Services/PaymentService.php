<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Notifications\PaymentStatusUpdatedNotification;

class PaymentService
{
    public function markAsPaid(Payment $payment, array $meta = []): Payment
    {
        $previousStatus = $payment->status;

        $payment->update([
            'status' => 'paid',
            'paid_at' => now(),
            'meta' => array_merge($payment->meta ?? [], $meta),
        ]);

        $this->syncPayableStatus($payment);

        if ($payment->user && $previousStatus !== 'paid') {
            $payment->user->notify(new PaymentStatusUpdatedNotification(
                $payment->reference,
                'paid'
            ));
        }

        return $payment->fresh();
    }

    public function markAsFailed(Payment $payment, array $meta = []): Payment
    {
        $previousStatus = $payment->status;

        $payment->update([
            'status' => 'failed',
            'meta' => array_merge($payment->meta ?? [], $meta),
        ]);

        $this->syncPayableStatus($payment);

        if ($payment->user && $previousStatus !== 'failed') {
            $payment->user->notify(new PaymentStatusUpdatedNotification(
                $payment->reference,
                'failed'
            ));
        }

        return $payment->fresh();
    }

    public function markAsUnderReview(Payment $payment, array $meta = []): Payment
    {
        $previousStatus = $payment->status;

        $payment->update([
            'status' => 'under_review',
            'meta' => array_merge($payment->meta ?? [], $meta),
        ]);

        $this->syncPayableStatus($payment);

        if ($payment->user && $previousStatus !== 'under_review') {
            $payment->user->notify(new PaymentStatusUpdatedNotification(
                $payment->reference,
                'under_review'
            ));
        }

        return $payment->fresh();
    }

    public function markAsCancelled(Payment $payment, array $meta = []): Payment
    {
        $previousStatus = $payment->status;

        $payment->update([
            'status' => 'cancelled',
            'meta' => array_merge($payment->meta ?? [], $meta),
        ]);

        $this->syncPayableStatus($payment);

        if ($payment->user && $previousStatus !== 'cancelled') {
            $payment->user->notify(new PaymentStatusUpdatedNotification(
                $payment->reference,
                'cancelled'
            ));
        }

        return $payment->fresh();
    }

    public function syncVerifiedPaystackPayment(Payment $payment, array $transactionData): Payment
    {
        $previousStatus = $payment->status;
        $meta = array_merge($payment->meta ?? [], [
            'paystack' => $transactionData,
        ]);

        $payment->update([
            'gateway' => 'paystack',
            'status' => Payment::STATUS_PAID,
            'currency' => (string) data_get($transactionData, 'currency', $payment->currency ?: config('kiosk.payments.currency', 'NGN')),
            'gateway_transaction_id' => (string) data_get($transactionData, 'id'),
            'gateway_response' => (string) data_get($transactionData, 'gateway_response', 'Successful'),
            'paid_at' => data_get($transactionData, 'paid_at') ?? data_get($transactionData, 'paidAt') ?? now(),
            'gateway_verified_at' => now(),
            'meta' => $meta,
        ]);

        $this->syncPayableStatus($payment);
        $this->syncOrderStateAfterSuccessfulPayment($payment);

        if ($payment->user && $previousStatus !== Payment::STATUS_PAID) {
            $payment->user->notify(new PaymentStatusUpdatedNotification(
                $payment->reference,
                Payment::STATUS_PAID
            ));
        }

        return $payment->fresh();
    }

    protected function syncPayableStatus(Payment $payment): void
    {
        $payable = $payment->payable;

        if (!$payable) {
            return;
        }

        if (property_exists($payable, 'payment_status') || isset($payable->payment_status)) {
            $payable->payment_status = $payment->status;
            $payable->save();
        }
    }

    protected function syncOrderStateAfterSuccessfulPayment(Payment $payment): void
    {
        if (! $payment->payable instanceof Order) {
            return;
        }

        $nextStatus = $payment->payable->order_type === 'global_shop'
            ? Order::STATUS_REVIEWING
            : Order::STATUS_PROCESSING;

        if (in_array($payment->payable->order_status, [Order::STATUS_PENDING, Order::STATUS_REVIEWING], true)) {
            $payment->payable->update([
                'order_status' => $nextStatus,
            ]);
        }
    }
}
