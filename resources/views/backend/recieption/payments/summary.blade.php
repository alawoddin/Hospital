@extends('backend.recieption_dashboard')
@section('recieption')
<div class="card p-4">
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <h3>Patient Financial Summary — {{ $patient->name }}</h3>
    <p class="text-muted">Invoice: {{ $summary['bill']->invoice_no }} | Status: {{ ucfirst($summary['bill']->status) }}</p>

    <table class="table table-bordered mt-4">
        <thead><tr><th>Category</th><th>Amount</th></tr></thead>
        <tbody>
            @foreach($summary['categories'] as $key => $cat)
                @if($cat['total'] > 0)
                <tr>
                    <td>{{ $cat['label'] }}</td>
                    <td>${{ number_format($cat['total'], 2) }}</td>
                </tr>
                @endif
            @endforeach
            <tr class="font-weight-bold bg-light">
                <td>Grand Total</td>
                <td>${{ number_format($summary['grand_total'], 2) }}</td>
            </tr>
            <tr><td>Paid</td><td>${{ number_format($summary['paid'], 2) }}</td></tr>
            <tr><td>Due</td><td>${{ number_format($summary['due'], 2) }}</td></tr>
        </tbody>
    </table>

    @if($summary['due'] > 0)
    <form action="{{ route('recieption.bill.pay', $summary['bill']->id) }}" method="POST">@csrf
        <button class="btn btn-success btn-lg">Mark As Paid — Collect ${{ number_format($summary['due'], 2) }}</button>
    </form>
    @else
    <p class="text-success">This invoice is fully paid.</p>
    @endif
</div>
@endsection
