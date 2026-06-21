@extends('backend.admin_dashboard')
@section('admin')
<div class="col-xl-8 col-xxl-8 col-lg-8 mx-auto">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Add User</h4>
        </div>
        <div class="card-body py-5">
            <div class="basic-form">
                <form method="POST" action="{{ route('store.users')}}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-row">
                        <div class="col-sm-6">
                            <label for="name">Name: </label>
                            <input type="text" id="name" name="name" class="form-control">
                        </div>
                        <div class="col-sm-6 mt-2 mt-sm-0">
                            <label for="email">Email: </label>
                            <input type="email" id="email" name="email" class="form-control">
                        </div>
                    </div>
                    <div class="form-row mt-4">
                        <div class="col-sm-6">
                            <label for="pwd">Password: </label>
                            <input type="password" id="pwd" name="password" class="form-control">
                        </div>
                        <div class="col-sm-6 mt-2 mt-sm-0">
                            <label>Role: </label>
                            <select class="form-control" id="sel1" name="role" required>
                                    <option value="admin">Admin</option>
                                    <option value="doctor">Doctor</option>
                                    <option value="recieption">Reception</option>
                                    <option value="finance">Finance</option>
                                    <option value="pharmacy">Pharmacy</option>
                                    <option value="user">Patient (User)</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row mt-4">
                        <div class="col-sm-6">
                            <label for="phone">Phone: </label>
                            <input type="text" id="phone" name="phone" class="form-control">
                        </div>
                        <div class="col-sm-6 mt-2 mt-sm-0">
                            <label for="address">Address:</label>
                            <textarea class="form-control" name="address"></textarea>
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
                                <button type="submit" class="btn btn-primary mt-4">Submit</button>
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