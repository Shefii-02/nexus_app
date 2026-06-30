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
    <h1>{{ config('app.name') }} - Salary Receipt</h1>
    <p class="muted">Salary Month: {{ $payment->salary_month }}</p>

    <table>
        <tr><th>Staff</th><td>{{ optional($payment->staff)->name }}</td></tr>
        <tr><th>Salary Amount</th><td>&#8377;{{ number_format((float) $payment->salary_amount, 2) }}</td></tr>
        <tr><th>Bonus</th><td>&#8377;{{ number_format((float) $payment->bonus_amount, 2) }}</td></tr>
        <tr><th>Deduction</th><td>&#8377;{{ number_format((float) $payment->deduction_amount, 2) }} ({{ $payment->deduction_reason ?? '—' }})</td></tr>
        <tr><th>Payment Method</th><td>{{ $payment->payment_method ?? '—' }}</td></tr>
        <tr><th>Transaction No.</th><td>{{ $payment->transaction_no ?? '—' }}</td></tr>
        <tr><th>Released By</th><td>{{ optional($payment->releasedBy)->name ?? '—' }}</td></tr>
    </table>

    <p style="margin-top: 30px;">Net Amount Paid</p>
    <p class="amount">&#8377;{{ number_format((float) $payment->final_amount, 2) }}</p>
</body>
</html>
