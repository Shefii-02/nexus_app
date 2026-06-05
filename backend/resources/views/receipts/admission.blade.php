<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #1a1a1a; background: #fff; }
  .receipt { padding: 28px 32px; max-width: 540px; margin: 0 auto; }
  .header { text-align: center; border-bottom: 2px solid #005C4B; padding-bottom: 16px; margin-bottom: 20px; }
  .org-name { font-size: 20px; font-weight: 700; color: #005C4B; }
  .receipt-title { font-size: 13px; color: #555; margin-top: 4px; text-transform: uppercase; letter-spacing: 1px; }
  .receipt-no { font-size: 11px; color: #888; margin-top: 3px; }
  .amount-box { background: #f0f9f6; border: 1.5px solid #005C4B; border-radius: 8px;
    padding: 14px 20px; text-align: center; margin: 18px 0; }
  .amount-box .label { font-size: 11px; color: #555; text-transform: uppercase; letter-spacing: .5px; }
  .amount-box .value { font-size: 28px; font-weight: 700; color: #005C4B; margin-top: 4px; }
  table { width: 100%; border-collapse: collapse; margin-top: 12px; }
  td { padding: 7px 4px; font-size: 12px; }
  td:first-child { color: #666; width: 45%; }
  td:last-child  { font-weight: 600; text-align: right; }
  tr:not(:last-child) td { border-bottom: 1px solid #eee; }
  .footer { margin-top: 24px; text-align: center; font-size: 11px; color: #999; border-top: 1px solid #eee; padding-top: 14px; }
  .badge { display: inline-block; background: #005C4B; color: #fff; border-radius: 12px;
    padding: 3px 12px; font-size: 11px; font-weight: 600; margin-top: 8px; }
</style>
</head>
<body>
<div class="receipt">
  <div class="header">
    <div class="org-name">{{ config('app.name') }}</div>
    <div class="receipt-title">Admission Payment Receipt</div>
    <div class="receipt-no">TXN{{ str_pad($model->id, 8, '0', STR_PAD_LEFT) }}</div>
  </div>

  <div class="amount-box">
    <div class="label">Amount Paid</div>
    <div class="value">₹{{ number_format($model->amount, 2) }}</div>
    <div class="badge">PAID</div>
  </div>

  <table>
    <tr><td>Student</td>         <td>{{ $model->student?->full_name ?? '—' }}</td></tr>
    <tr><td>Course</td>          <td>{{ $model->course?->name ?? '—' }}</td></tr>
    <tr><td>Payment Method</td>  <td>{{ ucfirst($model->payment_method ?? '—') }}</td></tr>
    <tr><td>Transaction No.</td> <td>{{ $model->transaction_no ?? '—' }}</td></tr>
    <tr><td>Paid On</td>         <td>{{ $model->paid_at?->format('d M Y, h:i A') ?? '—' }}</td></tr>
    <tr><td>Received By</td>     <td>{{ $model->receivedBy?->name ?? '—' }}</td></tr>
    @if($model->remarks)
    <tr><td>Remarks</td>         <td>{{ $model->remarks }}</td></tr>
    @endif
  </table>

  <div class="footer">
    {{ config('app.name') }} · Generated on {{ now()->format('d M Y') }}<br>
    This is a computer-generated receipt.
  </div>
</div>
</body>
</html>
