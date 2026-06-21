@extends('backend.admin_dashboard')
@section('admin')
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h4>Fee Types (Admin)</h4>
        <a href="{{ route('admin.fees.add') }}" class="btn btn-primary">Add Fee Type</a>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr><th>Name</th><th>Category</th><th>Amount</th><th>Department</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @foreach($feeTypes as $fee)
                <tr>
                    <td>{{ $fee->name }}</td>
                    <td><span class="badge badge-info">{{ ucfirst($fee->category) }}</span></td>
                    <td>${{ number_format($fee->amount, 2) }}</td>
                    <td>{{ $fee->department->name ?? '—' }}</td>
                    <td>{{ $fee->is_active ? 'Active' : 'Inactive' }}</td>
                    <td>
                        <a href="{{ route('admin.fees.edit', $fee->id) }}" class="btn btn-sm btn-info">Edit</a>
                        <a href="{{ route('admin.fees.delete', $fee->id) }}" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <p class="text-muted">Examples: OPD Consultation $200, Neurology $300, CBC Lab $150</p>
    </div>
</div>
@endsection
