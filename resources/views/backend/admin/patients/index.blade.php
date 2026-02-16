@extends('backend.admin_dashboard')
@section('admin')

<div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">All Patients</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        {{-- <div>
                            <a href="{{ route('add.admin.patients') }}" class="btn btn-primary mb-2" style="float:right;">Add Patient</a>
                        </div> --}}
                        <table id="example" class="display" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Last Name</th>
                                    <th>Age</th>
                                    <th>Gender</th>
                                    <th>Doctor</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Photo</th>
                                    <th>Created at</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($patients as $item)
                                    <tr>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->last_name }}</td>
                                        <td>{{ $item->age }}</td>
                                        <td>{{ $item->gender }}</td>
                                        <td>{{ $item->doctor }}</td>
                                        <td>{{ $item->phone }}</td>
                                        <td>{{ $item->email }}</td>
                                        <td><img src="{{ asset($item->photo) }}" alt="" style="width: 50px; height: 50px;"></td>
                                        <td>{{ $item->created_at }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
</div>

@endsection