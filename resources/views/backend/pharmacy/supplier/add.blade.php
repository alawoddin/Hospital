@extends('backend.pharmacy_dashboard')
@section('pharmacy')

<div class="container-fluid">
    <form action="{{ route('store.supplier') }}" method="POST" enctype="multipart/form-data">
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
                        <label for="name">Name: </label>
                        <input type="text" id="name" name="name" class="form-control">
                    </div>
                    <div class="col-xl-6 col-xxl-4 col-lg-4 mx-auto">
                        <label for="email">Email: </label>
                        <input type="text" id="email" name="email" class="form-control">
                    </div>
                </div>
                <div class="row mb-2 mt-3">
                    <div class="col-xl-6 col-xxl-4 col-lg-4 mx-auto">
                        <label for="phone">Phone: </label>
                        <input type="text" id="phone" name="phone" class="form-control">
                    </div>
                    <div class="col-xl-6 col-xxl-4 col-lg-4 mx-auto">
                        <label for="address">Address: </label>
                        <textarea name="address" id="address" cols="3" rows="2" class="form-control"></textarea>
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