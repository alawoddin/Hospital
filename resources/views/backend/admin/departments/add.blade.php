@extends('backend.admin_dashboard')
@section('admin')
<form action="{{ route('admin.departments.store') }}" method="POST" class="card p-4">
    @csrf
    <h4>Add Department</h4>
    <div class="mb-3"><label>Name</label><input type="text" name="name" class="form-control" required></div>
    <div class="mb-3"><label>Description</label><textarea name="description" class="form-control"></textarea></div>
    <button class="btn btn-success">Save</button>
</form>
@endsection
