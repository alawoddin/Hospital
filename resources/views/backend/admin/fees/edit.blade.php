@extends('backend.admin_dashboard')
@section('admin')
<form action="{{ route('admin.fees.update') }}" method="POST" class="card p-4">
    @csrf
    <input type="hidden" name="id" value="{{ $feeType->id }}">
    <h4>Edit Fee Type</h4>
    <div class="row mt-3">
        <div class="col-md-6 mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="{{ $feeType->name }}" required>
        </div>
        <div class="col-md-6 mb-3">
            <label>Category</label>
            <select name="category" class="form-control" required>
                <option value="consultation" @selected($feeType->category==='consultation')>Consultation</option>
                <option value="laboratory" @selected($feeType->category==='laboratory')>Laboratory</option>
                <option value="registration" @selected($feeType->category==='registration')>Registration</option>
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label>Amount ($)</label>
            <input type="number" step="0.01" min="0" name="amount" class="form-control" value="{{ $feeType->amount }}" required>
        </div>
        <div class="col-md-6 mb-3 form-check mt-4">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" @checked($feeType->is_active)>
            <label class="form-check-label">Active</label>
        </div>
        <div class="col-12 mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control">{{ $feeType->description }}</textarea>
        </div>
    </div>
    <button class="btn btn-success">Update</button>
</form>
@endsection
