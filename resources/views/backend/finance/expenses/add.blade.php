@extends('backend.finance_dashboard')
@section('finance')
<div class="card p-4">
    <h4>Add Expense</h4>
    <form action="{{ route('finance.expenses.store') }}" method="POST">@csrf
        <div class="row">
            <div class="col-md-6 mb-3"><label>Category</label><select name="category" class="form-control" required>
                <option>Salaries</option><option>Rent</option><option>Electricity</option><option>Equipment</option><option>Medicine Purchase</option><option>Other</option>
            </select></div>
            <div class="col-md-6 mb-3"><label>Title</label><input type="text" name="title" class="form-control" required></div>
            <div class="col-md-6 mb-3"><label>Amount</label><input type="number" step="0.01" name="amount" class="form-control" required></div>
            <div class="col-md-6 mb-3"><label>Date</label><input type="date" name="expense_date" class="form-control" value="{{ date('Y-m-d') }}" required></div>
            <div class="col-12 mb-3"><label>Description</label><textarea name="description" class="form-control"></textarea></div>
        </div>
        <button class="btn btn-success">Save Expense</button>
    </form>
</div>
@endsection
