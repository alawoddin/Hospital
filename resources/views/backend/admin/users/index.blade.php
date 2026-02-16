@extends('backend.admin_dashboard')
@section('admin')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">All Users</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <div>
                            <a href="{{ route('add.users') }}" class="btn btn-primary mb-2" style="float:right;">Add User</a>
                        </div>
                        <table id="example" class="display" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Address</th>
                                    <th>Role</th>
                                    <th>Password</th>
                                    <th>Photo</th>
                                    <th>Edit</th>
                                    <th>Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $item)
                                    <tr>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->email }}</td>
                                        <td>{{ $item->phone }}</td>
                                        <td>{{ $item->address }}</td>
                                        <td>{{ $item->role }}</td>
                                        <td>{{ $item->password }}</td>
                                        <td><img src="{{ asset($item->photo) }}" alt="" style="width: 50px; height: 50px;"></td>
                                        <td><a href="{{ route('edit.users', $item->id) }}" class="btn btn-primary">Edit</a></td>
                                        <td><a href="{{ route('delete.users', $item->id) }}" class="btn btn-danger">Delete</a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
</div>
</div>

@endsection