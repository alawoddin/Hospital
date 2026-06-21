@extends('backend.finance_dashboard')
@section('finance')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4"><div class="card p-3"><h6>Total Payments</h6><h3>${{ number_format($totalPayments, 2) }}</h3></div></div>
        <div class="col-md-4"><div class="card p-3"><h6>Pending Invoices</h6><h3>{{ $pendingInvoices }}</h3></div></div>
        <div class="col-md-4"><div class="card p-3"><h6>Total Due</h6><h3>${{ number_format($totalDue, 2) }}</h3></div></div>
    </div>
</div>
@endsection
