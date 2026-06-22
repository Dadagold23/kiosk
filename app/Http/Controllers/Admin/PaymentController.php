<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use App\Services\ActivityLogService;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with('user', 'payable');

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                ->orWhere('receipt_no', 'like', "%{$search}%")
                ->orWhereHas('user', function ($u) use ($search) {
                    $u->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payments = $query->latest()->paginate(20)->withQueryString();

        return view('admin.payments.index', compact('payments'));
    }


    public function show(Payment $payment)
    {
        $payment->load('user', 'payable');

        return view('admin.payments.show', compact('payment'));
    }

    public function update(Request $request, Payment $payment, PaymentService $paymentService, ActivityLogService $activityLogService)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,paid,failed,cancelled,under_review'],
        ]);

        match ($validated['status']) {
            'paid' => $paymentService->markAsPaid($payment),
            'failed' => $paymentService->markAsFailed($payment),
            'under_review' => $paymentService->markAsUnderReview($payment),
            default => $payment->update(['status' => $validated['status']]),
        };

        $activityLogService->log(
            auth()->id(),
            'payment_updated',
            Payment::class,
            $payment->id,
            'Updated payment: ' . $payment->reference,
            ['status' => $validated['status']]
        );

        return back()->with('success', 'Payment updated successfully.');
    }

}
