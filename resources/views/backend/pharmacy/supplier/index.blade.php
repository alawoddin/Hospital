@extends('backend.pharmacy_dashboard')
@section('pharmacy')
<div class="container-fluid">
        <div class="row mb-5">
            <div class="col-11">
                <h2>All Suppliers</h2>
            </div>
            <div class="col-1">
                <a href="{{ route('add.supplier') }}" class="btn btn-primary btn-sm">Add Supplier</a>
            </div>
        </div>
    <div class="row">
        <div class="col-xl-12 col-lg-10 col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">All Suppliers</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example" class="display" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Address</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($supplier as $key=> $item)
                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->email }}</td>
                                        <td>{{ $item->phone }}</td>
                                        <td>{{ $item->address }}</td>
                                        <td>
                                            <a href="{{ route('edit.supplier', $item->id) }}" class="btn btn-info">Edit</a>
                                            <a href="{{ route('delete.supplier', $item->id) }}" class="btn btn-danger">Delete</a>
                                        </td>
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
