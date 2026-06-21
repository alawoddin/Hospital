@extends('backend.finance_dashboard')
@section('finance')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>Invoices</h4>
                <a href="{{ route('finance.bills.add') }}" class="btn btn-primary">Create Invoice</a>
            </div>
            <div class="card-body table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Invoice No</th>
                            <th>Patient</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Due</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bills as $bill)
                        <tr>
                            <td>{{ $bill->invoice_no }}</td>
                            <td>{{ $bill->patient->name ?? 'N/A' }}</td>
                            <td>{{ $bill->bill_date }}</td>
                            <td>${{ number_format($bill->total_amount, 2) }}</td>
                            <td>${{ number_format($bill->due_amount, 2) }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $bill->status)) }}</td>
                            <td><a href="{{ route('finance.bills.show', $bill->id) }}" class="btn btn-sm btn-info">View</a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $bills->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
