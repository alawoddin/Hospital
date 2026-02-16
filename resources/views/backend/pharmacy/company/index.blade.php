@extends('backend.pharmacy_dashboard')
@section('pharmacy')
<div class="container-fluid">
        <div class="row mb-5">
            <div class="col-11">
                <h2>All Companies</h2>
            </div>
            <div class="col-1">
                <a href="{{ route('add.company') }}" class="btn btn-primary">Add Company</a>
            </div>
        </div>
    <div class="row">
        <div class="col-xl-12 col-lg-10 col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">All Companies</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example" class="display" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Company Name</th>
                                    <th>Image</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($company as $key=> $item)
                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>
                                            <img src="{{ asset($item->image) }}" alt="" style="width: 50px; height: 50px;">
                                        </td>
                                        <td>
                                            <a href="{{ route('edit.company', $item->id) }}" class="btn btn-info">Edit</a>
                                            <a href="{{ route('delete.company', $item->id) }}" class="btn btn-danger">Delete</a>
                                        </td>
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
