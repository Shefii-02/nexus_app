{{-- resources/views/receipts/teacher.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<style>
  * { margin:0; padding:0; box-sizing:border-box; }
  body { font-family:'DejaVu Sans',sans-serif; font-size:12px; color:#1a1a1a; }
  .receipt { padding:28px 32px; max-width:540px; margin:0 auto; }
  .header { text-align:center; border-bottom:2px solid #003D30; padding-bottom:14px; margin-bottom:18px; }
  .org { font-size:19px; font-weight:700; color:#003D30; }
  .sub { font-size:11px; color:#888; text-transform:uppercase; letter-spacing:1px; margin-top:3px; }
  .txn { font-size:10px; color:#aaa; margin-top:2px; }
  .box { background:#f0f9f6; border:1.5px solid #003D30; border-radius:8px; padding:12px 18px;
    display:flex; justify-content:space-between; align-items:center; margin:14px 0; }
  .box .lbl { font-size:11px; color:#555; }
  .box .val { font-size:22px; font-weight:700; color:#003D30; }
  table { width:100%; border-collapse:collapse; }
  td { padding:6px 3px; font-size:12px; }
  td:first-child { color:#777; width:45%; }
  td:last-child  { font-weight:600; text-align:right; }
  tr:not(:last-child) td { border-bottom:1px solid #eee; }
  .section-title { font-size:11px; font-weight:700; text-transform:uppercase; color:#003D30;
    letter-spacing:.5px; margin:16px 0 6px; }
  .item-row { display:flex; justify-content:space-between; padding:5px 0;
    border-bottom:1px dashed #ddd; font-size:11px; }
  .item-row:last-child { border:none; }
  .footer { margin-top:20px; text-align:center; font-size:10px; color:#aaa;
    border-top:1px solid #eee; padding-top:12px; }
  .badge { display:inline-block; padding:2px 10px; border-radius:10px; font-size:10px;
    font-weight:700; background:#003D30; color:#fff; margin-top:4px; }
</style>
</head>
<body>
<div class="receipt">
  <div class="header">
    <div class="org">{{ config('app.name') }}</div>
    <div class="sub">Teacher Salary Receipt</div>
    <div class="txn">TXN{{ str_pad($model->id, 8, '0', STR_PAD_LEFT) }}</div>
  </div>

  <div class="box">
    <div><div class="lbl">Net Salary</div><div class="badge">RELEASED</div></div>
    <div class="val">₹{{ number_format($model->amount, 2) }}</div>
  </div>

  <table>
    <tr><td>Teacher</td>         <td>{{ $model->teacher?->full_name ?? '—' }}</td></tr>
    <tr><td>Period</td>          <td>{{ $model->period_start?->format('d M Y') }} – {{ $model->period_end?->format('d M Y') }}</td></tr>
    <tr><td>Total Classes</td>   <td>{{ $model->total_classes }}</td></tr>
    <tr><td>Gross Amount</td>    <td>₹{{ number_format($model->gross_amount, 2) }}</td></tr>
    @if($model->deduction_amount > 0)
    <tr><td>Deduction</td>       <td>- ₹{{ number_format($model->deduction_amount, 2) }}</td></tr>
    @endif
    <tr><td>Payment Method</td>  <td>{{ ucfirst(str_replace('_', ' ', $model->payment_method ?? '—')) }}</td></tr>
    <tr><td>Transaction No.</td> <td>{{ $model->transaction_no ?? '—' }}</td></tr>
    <tr><td>Paid On</td>         <td>{{ $model->paid_at?->format('d M Y, h:i A') ?? '—' }}</td></tr>
    <tr><td>Released By</td>     <td>{{ $model->releasedBy?->name ?? '—' }}</td></tr>
  </table>

  @if($model->items && $model->items->count())
  <div class="section-title">Course Breakdown</div>
  @foreach($model->items as $item)
  <div class="item-row">
    <span>{{ $item->course?->name ?? 'Course' }} · {{ $item->month }}</span>
    <span>₹{{ number_format($item->amount, 2) }}</span>
  </div>
  @endforeach
  @endif

  <div class="footer">{{ config('app.name') }} · {{ now()->format('d M Y') }} · Computer-generated receipt</div>
</div>
</body>
</html>
