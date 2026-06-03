<h2>Invoice</h2>

<p><strong>Student:</strong> {{ $data->student->user->name }}</p>
<p><strong>Course:</strong> {{ $data->course->name }}</p>
<p><strong>Amount:</strong> ₹{{ $data->amount }}</p>
<p><strong>Date:</strong> {{ $type == 'admission' ? $data->payment_date : $data->renewal_date }}</p>

<hr>

<p>Thank you!</p>
