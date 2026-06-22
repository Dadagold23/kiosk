<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt {{ $payment->receipt_no }}</title>
    <style>
        body{
            color:#222;
            font-family:DejaVu Sans, sans-serif;
            font-size:14px;
        }
        .wrap{
            border:1px solid #ddd;
            border-radius:10px;
            padding:24px;
        }
        .title{
            font-size:24px;
            font-weight:bold;
            margin-bottom:8px;
        }
        .muted{
            color:#666;
        }
        .row{
            margin-bottom:10px;
        }
        .section{
            border-top:1px solid #ddd;
            margin-top:20px;
            padding-top:12px;
        }
        table{
            border-collapse:collapse;
            margin-top:10px;
            width:100%;
        }
        td, th{
            border:1px solid #ddd;
            padding:8px;
            text-align:left;
        }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="title">Kiosk Payment Receipt</div>
        <div class="muted">Official payment acknowledgment</div>

        <div class="section">
            <div class="row"><strong>Receipt No:</strong> {{ $payment->receipt_no }}</div>
            <div class="row"><strong>Reference:</strong> {{ $payment->reference }}</div>
            <div class="row"><strong>Status:</strong> {{ ucfirst($payment->status) }}</div>
            <div class="row"><strong>Paid At:</strong> {{ $payment->paid_at?->format('d M Y, h:i A') ?: 'Pending' }}</div>
        </div>

        <div class="section">
            <div class="row"><strong>Customer:</strong> {{ $payment->user?->name }}</div>
            <div class="row"><strong>Email:</strong> {{ $payment->user?->email }}</div>
            <div class="row"><strong>Phone:</strong> {{ $payment->user?->phone ?: 'N/A' }}</div>
        </div>

        <div class="section">
            <table>
                <tr>
                    <th>Payment Method</th>
                    <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method ?? 'n/a')) }}</td>
                </tr>
                <tr>
                    <th>Gateway</th>
                    <td>{{ ucfirst($payment->gateway ?? 'manual') }}</td>
                </tr>
                <tr>
                    <th>Amount</th>
                    <td>&#8358;{{ number_format($payment->amount, 2) }}</td>
                </tr>
                <tr>
                    <th>Related Record</th>
                    <td>{{ class_basename($payment->payable_type) }} #{{ $payment->payable_id }}</td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
