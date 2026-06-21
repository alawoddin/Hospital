@extends('backend.recieption_dashboard')
@section('recieption')

<div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Basic Datatable</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <div>
                            <a href="{{ route('add.patients') }}" class="btn btn-primary mb-2" style="float:right;">Add Patient</a>
                        </div>
                        <table id="example" class="display" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th>id</th>
                                    <th>Name</th>
                                    <th>Father Name</th>
                                    <th>Last Name</th>
                                    <th>Age</th>
                                    <th>Gender</th>
                                    <th>Doctor</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Address</th>
                                    <th>National ID</th>
                                    <th>Photo</th>
                                    <th>Created at</th>
                                    <th>Edit</th>
                                    <th>Bill</th>
                                    <th>Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($patients as $key=> $item)
                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->father_name }}</td>
                                        <td>{{ $item->last_name }}</td>
                                        <td>{{ $item->age }}</td>
                                        <td>{{ $item->gender }}</td>
                                        <td>{{ $item->doctor }}</td>
                                        <td>{{ $item->phone }}</td>
                                        <td>{{ $item->email }}</td>
                                        <td>{{ $item->address }}</td>
                                        <td>{{ $item->national_id }}</td>
                                        {{-- <td><img src="{{ $item->gender }}" alt=""></td> --}}
                                        <td><img src="{{ asset($item->photo) }}" alt="" style="width: 50px; height: 50px;"></td>
                                        <td>{{ $item->created_at }}</td>
                                        <td><a href="{{ route('edit.patients', $item->id) }}" class="btn btn-info">Edit</a></td>
                                        <td><a href="{{ route('recieption.patient.summary', $item->id) }}" class="btn btn-success btn-sm">Bill</a></td>
                                        <td><a href="{{ route('delete.patients', $item->id) }}" class="btn btn-danger">Delete</a></td>
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