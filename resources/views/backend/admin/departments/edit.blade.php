@extends('backend.admin_dashboard')
@section('admin')
<form action="{{ route('admin.departments.update') }}" method="POST" class="card p-4">
    @csrf
    <input type="hidden" name="id" value="{{ $department->id }}">
    <h4>Edit Department</h4>
    <div class="mb-3"><label>Name</label><input type="text" name="name" class="form-control" value="{{ $department->name }}" required></div>
    <div class="mb-3"><label>Description</label><textarea name="description" class="form-control">{{ $department->description }}</textarea></div>
    <div class="mb-3 form-check"><input type="checkbox" name="is_active" value="1" class="form-check-input" @checked($department->is_active)><label class="form-check-label">Active</label></div>
    <button class="btn btn-success">Update</button>
</form>
@endsection
