@extends('backend.recieption_dashboard')
@section('recieption')

<div class="container-fluid">
    <form action="{{ route('store.patients') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row mb-5">
            <div class="col-12">
                <h2>Add a Patient</h2>
            </div>
        </div>

        <div class="row mb-5">
            
            <div class="col-xl-4 col-xxl-4 col-lg-4 mx-auto">
                
                <label for="name">Name: </label>
                <input type="text" id="name" name="name" class="form-control">
            </div>
            <div class="col-xl-4 col-xxl-4 col-lg-4 mx-auto">
                <label for="father_name">Father Name: </label>
                <input type="text" id="father_name" name="father_name" class="form-control">
            </div>
            <div class="col-xl-4 col-xxl-4 col-lg-4 mx-auto">
                <label for="last_name">Last Name: </label>
                <input type="text" id="last_name" name="last_name" class="form-control">
            </div>
        </div>
        <div class="row mb-5">
            <div class="col-xl-4 col-xxl-4 col-lg-4 mx-auto">
                <label for="age">Age: </label>
                <input type="text" id="age" name="age" class="form-control">
            </div>
            <div class="col-xl-4 col-xxl-4 col-lg-4 mx-auto">
                <label>Gender: </label>
                <select class="form-control" id="gender" name="gender">
                        <option>Male</option>
                        <option>Female</option>
                        <option>Other</option>
                </select>   
            </div>
            <div class="col-xl-4 col-xxl-4 col-lg-4 mx-auto">
                <label>Doctor: </label>
                <select class="form-control" id="doctor" name="doctor">
                    @foreach ($doctors as $doctor)
                        <option value="{{ $doctor->name }}">{{ $doctor->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row mb-5">
            <div class="col-xl-4 col-xxl-4 col-lg-4 mx-auto">
                <label for="phone">Phone: </label>
                <input type="text" id="phone" name="phone" class="form-control">
            </div>
            <div class="col-xl-4 col-xxl-4 col-lg-4 mx-auto">
                <label for="email">email: </label>
                <input type="text" id="email" name="email" class="form-control">
            </div>
            <div class="col-xl-4 col-xxl-4 col-lg-4 mx-auto">
                <label for="address">Address:</label>
                <textarea class="form-control" name="address"></textarea>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-xl-4 col-xxl-4 col-lg-4">
                <label for="national_id">National ID: </label>
                <input type="text" id="national_id" name="national_id" class="form-control">
            </div>
            <div class="col-xl-4 col-xxl-4 col-lg-4">
                <label for="photo">Photo: </label>
                <input type="file" id="photo" name="photo" class="form-control">
            </div>
        </div>
        <div class="row">
            <div class="col-xl-4 col-xxl-4 col-lg-4">
                <button type="submit" class="btn btn-primary mt-4">Submit</button>
            </div>
        </div>
    </form>
</div>

@endsection