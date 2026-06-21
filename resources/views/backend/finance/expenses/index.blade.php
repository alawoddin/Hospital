@extends('backend.finance_dashboard')
@section('finance')
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h4>Expenses</h4>
        <a href="{{ route('finance.expenses.add') }}" class="btn btn-primary">Add Expense</a>
    </div>
    <div class="card-body table-responsive">
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        <table class="table table-bordered">
            <thead><tr><th>Date</th><th>Category</th><th>Title</th><th>Amount</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                @foreach($expenses as $expense)
                <tr>
                    <td>{{ $expense->expense_date }}</td>
                    <td>{{ $expense->category }}</td>
                    <td>{{ $expense->title }}</td>
                    <td>${{ number_format($expense->amount, 2) }}</td>
                    <td>{{ ucfirst($expense->status) }}</td>
                    <td>
                        @if($expense->status === 'pending')
                            <form action="{{ route('finance.expenses.approve', $expense->id) }}" method="POST" class="d-inline">@csrf<button class="btn btn-sm btn-info">Approve</button></form>
                        @endif
                        @if($expense->status === 'approved')
                            <form action="{{ route('finance.expenses.pay', $expense->id) }}" method="POST" class="d-inline">@csrf<button class="btn btn-sm btn-success">Pay</button></form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $expenses->links() }}
    </div>
</div>
@endsection
