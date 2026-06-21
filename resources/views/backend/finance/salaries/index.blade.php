@extends('backend.finance_dashboard')
@section('finance')
<div class="card">
    <div class="card-header"><h4>Salary Management</h4></div>
    <div class="card-body table-responsive">
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        <table class="table table-bordered">
            <thead><tr><th>Employee</th><th>Month</th><th>Amount</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                @foreach($salaries as $salary)
                <tr>
                    <td>{{ $salary->employee->name ?? 'N/A' }}</td>
                    <td>{{ $salary->month }}</td>
                    <td>${{ number_format($salary->amount, 2) }}</td>
                    <td>{{ ucfirst($salary->status) }}</td>
                    <td>
                        @if($salary->status === 'pending')
                            <form action="{{ route('finance.salaries.approve', $salary->id) }}" method="POST" class="d-inline">@csrf<button class="btn btn-sm btn-info">Approve</button></form>
                        @endif
                        @if($salary->status === 'approved')
                            <form action="{{ route('finance.salaries.pay', $salary->id) }}" method="POST" class="d-inline">@csrf<button class="btn btn-sm btn-success">Pay Salary</button></form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $salaries->links() }}
    </div>
</div>
@endsection
