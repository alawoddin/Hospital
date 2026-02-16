@extends('backend.pharmacy_dashboard')
@section('pharmacy')

<div class="container-fluid">
    <form action="{{ route('update.supplier') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row mb-5">
            <div class="col-12">
                <h2>Edit Product</h2>
            </div>
        </div>
        <div class="col-12 mx-auto">
            <div class="card p-5">
                <h3>Edit Product: </h3>
                <div class="row mb-2 mt-3">
                    <div class="col-xl-6 col-xxl-4 col-lg-4 mx-auto">
                        <input type="hidden" name="id"  value="{{ $supplier->id }}">
                        <label for="name">Name: </label>
                        <input type="text" id="name" name="name" class="form-control" value="{{ $supplier->name }}">
                    </div>
                    <div class="col-xl-6 col-xxl-4 col-lg-4 mx-auto">
                        <label for="email">Email: </label>
                        <input type="text" id="email" name="email" class="form-control" value="{{ $supplier->email }}">
                    </div>
                </div>
                <div class="row mb-2 mt-3">
                    <div class="col-xl-6 col-xxl-4 col-lg-4 mx-auto">
                        <label for="phone">Phone: </label>
                        <input type="text" id="phone" name="phone" class="form-control" value="{{ $supplier->phone }}">
                    </div>
                    <div class="col-xl-6 col-xxl-4 col-lg-4 mx-auto">
                        <label for="address">Address: </label>
                        <textarea name="address" id="address" cols="3" rows="2" class="form-control">{{ $supplier->address }}</textarea>
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