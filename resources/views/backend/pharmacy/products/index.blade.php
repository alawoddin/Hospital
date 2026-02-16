@extends('backend.pharmacy_dashboard')
@section('pharmacy')
<div class="container-fluid">
        <div class="row mb-5">
            <div class="col-11">
                <h2>All Product</h2>
            </div>
            <div class="col-1">
                <a href="{{ route('add.products') }}" class="btn btn-primary btn-sm">Add Product</a>
            </div>
        </div>
    <div class="row">
        <div class="col-xl-12 col-lg-10 col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">All Products</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example" class="display" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Description</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Expiry Date</th>
                                    <th>Supplier</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($product as $key=> $item)
                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->category }}</td>
                                        <td>{{ $item->description }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ $item->price }}</td>
                                        <td>{{ $item->expiry_date }}</td>
                                        <td>{{ $item->supplier_id }}</td>
                                        <td>
                                            <a href="{{ route('edit.products', $item->id) }}" class="btn btn-info">Edit</a>
                                            <a href="{{ route('delete.products', $item->id) }}" class="btn btn-danger">Delete</a>
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
