@extends('backend.finance_dashboard')
@section('finance')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>Payments</h4>
                <a href="{{ route('finance.payments.add') }}" class="btn btn-primary">Record Payment</a>
            </div>
            <div class="card-body table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Patient</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Method</th>
                            <th>Reference</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                        <tr>
                            <td>{{ $payment->bill->invoice_no ?? 'N/A' }}</td>
                            <td>{{ $payment->bill->patient->name ?? 'N/A' }}</td>
                            <td>${{ number_format($payment->amount, 2) }}</td>
                            <td>{{ $payment->payment_date }}</td>
                            <td>{{ ucfirst($payment->payment_method) }}</td>
                            <td>{{ $payment->reference_no }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $payments->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
