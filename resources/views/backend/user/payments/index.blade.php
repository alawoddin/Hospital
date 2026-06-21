@extends('backend.user_dashboard')
@section('user')
<div class="card"><div class="card-header"><h4>Payment History</h4></div>
<div class="card-body table-responsive"><table class="table"><thead><tr><th>Invoice</th><th>Amount</th><th>Date</th><th>Method</th></tr></thead>
<tbody>@foreach($payments as $p)<tr><td>{{ $p->bill->invoice_no ?? 'N/A' }}</td><td>${{ number_format($p->amount,2) }}</td><td>{{ $p->payment_date }}</td><td>{{ ucfirst($p->payment_method) }}</td></tr>@endforeach</tbody></table></div></div>
@endsection
