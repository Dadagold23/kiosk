<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;

class ReceiptController extends Controller
{
    public function show(Payment $payment)
    {
        abort_unless($payment->user_id === auth()->id(), 403);

        $payment->load('payable', 'user');

        return view('customer.receipts.show', compact('payment'));
    }

    public function download(Payment $payment)
    {
        abort_unless($payment->user_id === auth()->id(), 403);

        $payment->load('payable', 'user');

        $pdf = Pdf::loadView('customer.receipts.pdf', compact('payment'))
            ->setPaper('a4');

        return $pdf->download('receipt-' . $payment->receipt_no . '.pdf');
    }
}
