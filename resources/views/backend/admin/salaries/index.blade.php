@extends('backend.admin_dashboard')
@section('admin')
<div class="card">
    <div class="card-header"><h4>Salaries</h4></div>
    <div class="card-body">
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        <form action="{{ route('admin.salaries.store') }}" method="POST" class="row mb-4">@csrf
            <div class="col-md-3"><select name="employee_id" class="form-control" required><option value="">Employee</option>@foreach($employees as $e)<option value="{{ $e->id }}">{{ $e->name }} ({{ $e->role }})</option>@endforeach</select></div>
            <div class="col-md-2"><input type="text" name="month" class="form-control" placeholder="Month e.g. June 2026" required></div>
            <div class="col-md-2"><input type="number" step="0.01" name="amount" class="form-control" placeholder="Amount" required></div>
            <div class="col-md-3"><input type="text" name="notes" class="form-control" placeholder="Notes"></div>
            <div class="col-md-2"><button class="btn btn-primary">Create Salary</button></div>
        </form>
        <table class="table table-bordered">
            <thead><tr><th>Employee</th><th>Month</th><th>Amount</th><th>Status</th></tr></thead>
            <tbody>@foreach($salaries as $s)<tr><td>{{ $s->employee->name ?? 'N/A' }}</td><td>{{ $s->month }}</td><td>${{ number_format($s->amount, 2) }}</td><td>{{ ucfirst($s->status) }}</td></tr>@endforeach</tbody>
        </table>
    </div>
</div>
@endsection
