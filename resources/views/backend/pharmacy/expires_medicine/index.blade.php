@extends('backend.pharmacy_dashboard')
@section('pharmacy')
<div class="container-fluid">
        <div class="row mb-5">
            <div class="col-11">
                <h2>All Expires Medicines</h2>
            </div>
            {{-- <div class="col-1">
                <a href="{{ route('add.products') }}" class="btn btn-primary btn-sm">Add Product</a>
            </div> --}}
        </div>
    <div class="row">
        <div class="col-xl-12 col-lg-10 col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">All Expires Medicines</h4>
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
                                    {{-- <th>Actions</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($expiredProducts as $key=> $item)
                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->category }}</td>
                                        <td>{{ $item->description }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ $item->price }}</td>
                                        <td>
                                            <span style="background-color: red; font-weight: bold; padding: 5px; color: #fff; border-radius: 5px;">
                                                {{ $item->expiry_date }}
                                            </span>
                                        </td>
                                        <td>{{ $item->supplier_id }}</td>
                                        <td>
                                            {{-- <a href="{{ route('edit.expires.medicine', $item->id) }}" class="btn btn-info">Edit</a>
                                            <a href="{{ route('delete.products', $item->id) }}" class="btn btn-danger">Delete</a> --}}
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
