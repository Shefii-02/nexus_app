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
        .amount { font-size: 20px; font-weight: bold; color: #b42318; }
        .badge { display: inline-block; padding: 2px 8px; background: #fef3f2; color: #b42318; border-radius: 4px; font-size: 11px; }
    </style>
</head>
<body>
    <h1>{{ config('app.name') }} - {{ $title }}</h1>
    <span class="badge">PENDING</span>

    <table>
        <tr><th>Name</th><td>{{ $name }}</td></tr>
        @if ($course)
            <tr><th>Course</th><td>{{ $course }}</td></tr>
        @endif
        <tr><th>Period</th><td>{{ $period }}</td></tr>
        @if ($dueDate)
            <tr><th>Due Date</th><td>{{ $dueDate }}</td></tr>
        @endif
        <tr><th>Remarks</th><td>{{ $remarks ?? '—' }}</td></tr>
    </table>

    <p style="margin-top: 30px;">Amount Due</p>
    <p class="amount">&#8377;{{ number_format((float) $amount, 2) }}</p>
</body>
</html>
