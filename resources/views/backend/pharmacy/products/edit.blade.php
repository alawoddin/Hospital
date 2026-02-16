@extends('backend.pharmacy_dashboard')
@section('pharmacy')

<div class="container-fluid">
    <form action="{{ route('update.products') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row mb-5">
            <div class="col-12">
                <h2>Add a Product</h2>
            </div>
        </div>
        <div class="col-12 mx-auto">
            <div class="card p-5">
                <h3>Add Product: </h3>
                <div class="row mb-2 mt-3">
                    <div class="col-xl-6 col-xxl-4 col-lg-4 mx-auto">
                        <input type="hidden" name="id" value="{{ $product->id }}">
                        <label for="name">Name: </label>
                        <input type="text" id="name" name="name" class="form-control" value="{{ $product->name }}">
                    </div>
                    <div class="col-xl-6 col-xxl-4 col-lg-4 mx-auto">
                        <label for="category">Category: </label>
                        <input type="text" id="category" name="category" class="form-control" value="{{ $product->category }}">
                    </div>
                </div>
                <div class="row mb-2 mt-3">
                    <div class="col-xl-6 col-xxl-4 col-lg-4 mx-auto">
                        <label for="description">Description: </label>
                        <textarea name="description" id="description" cols="3" rows="1" class="form-control">{{ $product->description }}</textarea>
                    </div>
                    <div class="col-xl-6 col-xxl-4 col-lg-4 mx-auto">
                        <label for="quantity">Quantity: </label>
                        <input type="number" id="quantity" name="quantity" class="form-control" value="{{ $product->quantity }}">
                    </div>
                </div>
                <div class="row mb-2 mt-3">
                    <div class="col-xl-6 col-xxl-4 col-lg-4 mx-auto">
                        <label for="Price">Price: </label>
                        <input type="text" id="Price" name="price" class="form-control" value="{{ $product->price }}">
                    </div>
                    <div class="col-xl-6 col-xxl-4 col-lg-4 mx-auto">
                        <label for="expiry_date">Expiry Date: </label>
                        <input type="date" id="expiry_date" name="expiry_date" class="form-control" value="{{ $product->expiry_date }}">
                    </div>
                </div>
                <div class="row mb-2 mt-3">
                    <div class="col-xl-6 col-xxl-4 col-lg-4">
                        <label for="Supplier">Supplier: </label>
                        <input type="text" id="Supplier" name="supplier_id" class="form-control" value="{{ $product->supplier_id }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-4 col-xxl-4 col-lg-4">
                        <button type="submit" class="btn btn-primary mt-4">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection