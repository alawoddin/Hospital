@extends('backend.finance_dashboard')
@section('finance')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h4>Invoice {{ $bill->invoice_no }}</h4>
            <a href="{{ route('finance.payments.add', ['bill_id' => $bill->id]) }}" class="btn btn-primary">Record Payment</a>
        </div>
        <div class="card-body">
            <p><strong>Patient:</strong> {{ $bill->patient->name ?? 'N/A' }}</p>
            <p><strong>Date:</strong> {{ $bill->bill_date }}</p>
            <p><strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $bill->status)) }}</p>
            <table class="table mt-3">
                <thead><tr><th>Description</th><th>Qty</th><th>Unit Price</th><th>Total</th></tr></thead>
                <tbody>
                    @foreach($bill->items as $item)
                    <tr>
                        <td>{{ $item->description }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>${{ number_format($item->unit_price, 2) }}</td>
                        <td>${{ number_format($item->total_price, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <p><strong>Total:</strong> ${{ number_format($bill->total_amount, 2) }}</p>
            <p><strong>Paid:</strong> ${{ number_format($bill->paidAmount(), 2) }}</p>
            <p><strong>Due:</strong> ${{ number_format($bill->due_amount, 2) }}</p>
        </div>
    </div>
</div>
@endsection
