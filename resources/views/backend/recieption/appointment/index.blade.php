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
                            <a href="{{ route('add.appointment') }}" class="btn btn-primary mb-2" style="float:right;">Add Appointment</a>
                        </div>
                        <table id="example" class="display" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th>SI</th>
                                    <th>Patient Name</th>
                                    <th>Doctor Name</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Token</th>
                                    <th>Status</th>
                                    <th>Description</th>
                                    <th>Created By</th>
                                    <th>Edit</th>
                                    <th>Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($appointments as $key=> $item)
                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td>{{ $item->patient->name ?? 'N/A' }}</td>
                                        <td>{{ $item->doctor->name ?? 'N/A' }}</td>
                                        <td>{{ $item->appointment_date }}</td>
                                        <td>{{ $item->appointment_time }}</td>
                                        <td>{{ $item->token_number }}</td>
                                        <td>
                                            @if($item->status === 'pending')
                                                <span class="badge bg-primary">{{ ucfirst($item->status) }}</span>
                                            @elseif($item->status === 'confirmed')
                                                <span class="badge bg-success">{{ ucfirst($item->status) }}</span>
                                            @elseif($item->status === 'canceled')
                                                <span class="badge bg-danger">{{ ucfirst($item->status) }}</span>
                                            @else
                                                <span class="badge bg-secondary">Unknown</span>
                                            @endif
                                        </td>
                                        
                                        <td>{{ $item->description }}</td>
                                        <td>{{ $item->creator->name ?? 'N/A' }}</td>
                                        <td><a href="{{ route('edit.appointment', $item->id) }}" class="btn btn-info">Edit</a></td>
                                        <td><a href="{{ route('delete.appointment', $item->id) }}" class="btn btn-danger">Delete</a></td>
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