<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 13px; color: #222; }
        h1 { font-size: 18px; margin-bottom: 0; }
        .muted { color: #777; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        td, th { padding: 8px; border-bottom: 1px solid #eee; text-align: left; }
        .amount { font-size: 20px; font-weight: bold; color: #1a7f37; }
    </style>
</head>
<body>
    <h1>{{ config('app.name') }} - Payment Receipt</h1>
    <p class="muted">Receipt #{{ $payment->id }} &middot; {{ optional($payment->paid_at)->format('d M Y, h:i A') }}</p>

    <table>
        <tr><th>Student</th><td>{{ optional($payment->student)->name }}</td></tr>
        <tr><th>Course</th><td>{{ optional($payment->course)->name }}</td></tr>
        <tr><th>Payment Method</th><td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td></tr>
        <tr><th>Transaction No.</th><td>{{ $payment->transaction_no ?? '—' }}</td></tr>
        <tr><th>Remarks</th><td>{{ $payment->remarks ?? '—' }}</td></tr>
        <tr><th>Received By</th><td>{{ $payment->received_by ?? '—' }}</td></tr>
    </table>

    <p style="margin-top: 30px;">Amount Paid</p>
    <p class="amount">&#8377;{{ number_format((float) $payment->amount, 2) }}</p>
</body>
</html>
