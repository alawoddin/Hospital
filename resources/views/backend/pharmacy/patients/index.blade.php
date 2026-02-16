@extends('backend.pharmacy_dashboard')
@section('pharmacy')

<div class="container-fluid">
        <div class="row mb-5">
            <div class="col-11">
                <h2>All Patients</h2>
            </div>
        </div>
    <div class="row">
        <div class="col-xl-12 col-lg-10 col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">All Patients</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example" class="display" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Last Name</th>
                                    <th>Age</th>
                                    <th>Gender</th>
                                    <th>Doctor</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Photo</th>
                                    <th>Medicine</th>
                                    <th>Description</th>
                                    <th>Created at</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($prescriptions as $key=> $prescription)
                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td>{{ $prescription->patient->name ?? '' }}</td>
                                        <td>{{ $prescription->patient->last_name ?? '' }}</td>
                                        <td>{{ $prescription->patient->age ?? '' }}</td>
                                        <td>{{ $prescription->patient->gender ?? '' }}</td>
                                        <td>{{ $prescription->doctor->name ?? '' }}</td>
                                        <td>{{ $prescription->patient->phone ?? '' }}</td>
                                        <td>{{ $prescription->patient->email ?? '' }}</td>
                                        <td>
                                            @if($prescription->patient->photo)
                                                <img src="{{ asset($prescription->patient->photo) }}" alt="" style="width: 50px; height: 50px;">
                                            @endif
                                        </td>
                                        <td>
                                            {{ $prescription->items->first()->medicine ?? '' }}...
                                        </td>
                                        <td>
                                            {{ $prescription->items->first()->desc ?? '' }}...
                                        </td>
                                        <td>{{ $prescription->created_at }}</td>
                                        <td><a href="{{ route('prescriptions.details', $prescription->patient->id) }}" class="btn btn-info">Details</a></td>
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
