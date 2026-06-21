@extends('backend.finance_dashboard')
@section('finance')
<div class="container-fluid">
    <form action="{{ route('finance.payments.store') }}" method="POST" class="card p-4">
        @csrf
        <h4>Record Payment</h4>
        <div class="row mt-3">
            <div class="col-md-6 mb-3">
                <label>Invoice</label>
                <select name="bill_id" class="form-control" required>
                    <option value="">Select invoice</option>
                    @foreach($bills as $bill)
                        <option value="{{ $bill->id }}" @selected(request('bill_id') == $bill->id)>
                            {{ $bill->invoice_no }} - {{ $bill->patient->name ?? 'N/A' }} (Due: ${{ number_format($bill->due_amount, 2) }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label>Amount</label>
                <input type="number" step="0.01" name="amount" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>Payment Date</label>
                <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>Payment Method</label>
                <select name="payment_method" class="form-control" required>
                    <option value="cash">Cash</option>
                    <option value="card">Card</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label>Reference No</label>
                <input type="text" name="reference_no" class="form-control">
            </div>
        </div>
        <button type="submit" class="btn btn-success">Save Payment</button>
    </form>
</div>
@endsection
