@extends('backend.finance_dashboard')
@section('finance')
<div class="container-fluid">
    <h4 class="mb-4">Finance Dashboard</h4>
    <div class="row mb-4">
        <div class="col-md-4"><div class="card p-3 border-success"><h6>Today Income</h6><h3>${{ number_format($today['income'], 2) }}</h3><small>Expense: ${{ number_format($today['expenses'], 2) }} | Profit: ${{ number_format($today['profit'], 2) }}</small></div></div>
        <div class="col-md-4"><div class="card p-3 border-primary"><h6>Monthly Income</h6><h3>${{ number_format($month['income'], 2) }}</h3><small>Expense: ${{ number_format($month['expenses'], 2) }} | Profit: ${{ number_format($month['profit'], 2) }}</small></div></div>
        <div class="col-md-4"><div class="card p-3 border-info"><h6>Yearly Income</h6><h3>${{ number_format($year['income'], 2) }}</h3><small>Expense: ${{ number_format($year['expenses'], 2) }} | Profit: ${{ number_format($year['profit'], 2) }}</small></div></div>
    </div>
    <div class="row">
        <div class="col-md-6"><div class="card p-3"><h6>Pending Invoices</h6><h3>{{ $pendingInvoices }}</h3></div></div>
        <div class="col-md-6"><div class="card p-3"><h6>Total Due</h6><h3>${{ number_format($totalDue, 2) }}</h3></div></div>
    </div>
</div>
@endsection
