@extends('backend.admin_dashboard')
@section('admin')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">All Pharmacy</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        {{-- <div>
                            <a href="{{ route('add.doctors') }}" class="btn btn-primary mb-2" style="float:right;">Add Doctor</a>
                        </div> --}}
                        <table id="example" class="display" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th>SI</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Address</th>
                                    <th>Photo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pharmacy as $key => $item)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->email }}</td>
                                        <td>{{ $item->phone }}</td>
                                        <td>{{ $item->address }}</td>
                                        <td><img src="{{ asset($item->photo) }}" alt="" style="width: 50px; height: 50px;"></td>
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