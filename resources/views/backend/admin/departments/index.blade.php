@extends('backend.admin_dashboard')
@section('admin')
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h4>Departments</h4>
        <a href="{{ route('admin.departments.add') }}" class="btn btn-primary">Add Department</a>
    </div>
    <div class="card-body table-responsive">
        <table class="table">
            <thead><tr><th>Name</th><th>Description</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                @foreach($departments as $department)
                <tr>
                    <td>{{ $department->name }}</td>
                    <td>{{ $department->description }}</td>
                    <td>{{ $department->is_active ? 'Active' : 'Inactive' }}</td>
                    <td>
                        <a href="{{ route('admin.departments.edit', $department->id) }}" class="btn btn-sm btn-info">Edit</a>
                        <a href="{{ route('admin.departments.delete', $department->id) }}" class="btn btn-sm btn-danger" onclick="return confirm('Delete department?')">Delete</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
