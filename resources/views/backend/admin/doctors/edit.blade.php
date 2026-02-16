@extends('backend.admin_dashboard')
@section('admin')
<div class="col-xl-8 col-xxl-8 col-lg-8 mx-auto">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Add Doctor</h4>
        </div>
        <div class="card-body py-5">
            <div class="basic-form">
                <form method="POST" action="{{ route('update.doctors')}}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-row">
                        <input type="hidden" name="id" value="{{ $doctor->id}}">
                        <div class="col-sm-6">
                            <label for="name">Name: </label>
                            <input type="text" id="name" name="name" class="form-control" value="{{ $doctor->name}}">
                        </div>
                        <div class="col-sm-6 mt-2 mt-sm-0">
                            <label for="email">Email: </label>
                            <input type="email" id="email" name="email" class="form-control" value="{{ $doctor->email}}">
                        </div>
                    </div>
                    <div class="form-row mt-4">
                        <div class="col-sm-6">
                            <label for="pwd">Password: </label>
                            <input type="password" id="pwd" name="password" class="form-control" value="{{ $doctor->password}}">
                        </div>
                        <div class="col-sm-6 mt-2 mt-sm-0">
                            <label>Role: </label>
                            <select class="form-control" id="sel1" name="role">
                                <option value="doctor" {{ $doctor->role == 'doctor' ? 'selected' : '' }}>Doctor</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row mt-4">
                        <div class="col-sm-6">
                            <label for="phone">Phone: </label>
                            <input type="text" id="phone" name="phone" class="form-control" value="{{ $doctor->phone}}">
                        </div>
                        <div class="col-sm-6 mt-2 mt-sm-0">
                            <label for="address">Address:</label>
                            <textarea class="form-control" name="address">{{ $doctor->address}}</textarea>
                        </div>
                        <div class="form-row mt-4">
                        <div class="col-sm-12">
                            <div class="custom-file">
                                <label for="formFile" class="form-label">Upload file</label>
                                <input class="form-control" type="file" id="formFile" name="photo">
                            </div>
                        </div>
                         <div class="col-sm-12">
                            <div class="custom-file">
                                <button type="submit" class="btn btn-primary mt-4">Save</button>
                            </div>
                        </div>
                    </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection