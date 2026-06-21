@extends('backend.recieption_dashboard')
@section('recieption')
<div class="card">
    <div class="card-header"><h4>Pending Patient Payments</h4></div>
    <div class="card-body table-responsive">
        <table class="table table-bordered">
            <thead><tr><th>Invoice</th><th>Patient</th><th>Total</th><th>Due</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                @foreach($bills as $bill)
                <tr>
                    <td>{{ $bill->invoice_no }}</td>
                    <td>{{ $bill->patient->name ?? 'N/A' }}</td>
                    <td>${{ number_format($bill->total_amount, 2) }}</td>
                    <td>${{ number_format($bill->due_amount, 2) }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $bill->status)) }}</td>
                    <td>
                        <a href="{{ route('recieption.patient.summary', $bill->patient_id) }}" class="btn btn-sm btn-info">Summary</a>
                        @if($bill->due_amount > 0)
                        <form action="{{ route('recieption.bill.pay', $bill->id) }}" method="POST" class="d-inline">@csrf<button class="btn btn-sm btn-success" onclick="return confirm('Mark as paid?')">Mark As Paid</button></form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $bills->links() }}
    </div>
</div>
@endsection
