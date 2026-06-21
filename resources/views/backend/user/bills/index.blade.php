@extends('backend.user_dashboard')
@section('user')
<div class="card"><div class="card-header"><h4>My Bills</h4></div>
<div class="card-body table-responsive"><table class="table"><thead><tr><th>Invoice</th><th>Date</th><th>Total</th><th>Due</th><th>Status</th></tr></thead>
<tbody>@foreach($bills as $b)<tr><td>{{ $b->invoice_no }}</td><td>{{ $b->bill_date }}</td><td>${{ number_format($b->total_amount,2) }}</td><td>${{ number_format($b->due_amount,2) }}</td><td>{{ ucfirst(str_replace('_',' ',$b->status)) }}</td></tr>@endforeach</tbody></table></div></div>
@endsection
