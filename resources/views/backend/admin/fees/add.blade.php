@extends('backend.admin_dashboard')
@section('admin')
<form action="{{ route('admin.fees.store') }}" method="POST" class="card p-4">
    @csrf
    <h4>Add Fee Type</h4>
    <div class="row mt-3">
        <div class="col-md-6 mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" placeholder="e.g. Neurology Consultation" required>
        </div>
        <div class="col-md-6 mb-3">
            <label>Category</label>
            <select name="category" class="form-control" required>
                <option value="consultation">Consultation (Doctor visit)</option>
                <option value="laboratory">Laboratory (Tests)</option>
                <option value="registration">Registration</option>
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label>Amount ($)</label>
            <input type="number" step="0.01" min="0" name="amount" class="form-control" required>
        </div>
        <div class="col-md-6 mb-3">
            <label>Department (optional)</label>
            <select name="department_id" class="form-control">
                <option value="">None</option>
                @foreach($departments as $d)<option value="{{ $d->id }}">{{ $d->name }}</option>@endforeach
            </select>
        </div>
        <div class="col-12 mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control"></textarea>
        </div>
    </div>
    <button class="btn btn-success">Save Fee Type</button>
</form>
@endsection
