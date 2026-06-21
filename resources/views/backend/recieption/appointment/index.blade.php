@extends('backend.recieption_dashboard')
@section('recieption')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>Appointments</h4>
                <a href="{{ route('add.appointment') }}" class="btn btn-primary">Add Appointment</a>
            </div>
            <div class="card-body table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th><th>Patient</th><th>Doctor</th><th>Date</th><th>Time</th><th>Token</th><th>Status</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($appointments as $key => $item)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $item->patient->name ?? 'N/A' }}</td>
                            <td>{{ $item->doctor->name ?? 'N/A' }}</td>
                            <td>{{ $item->appointment_date }}</td>
                            <td>{{ $item->appointment_time }}</td>
                            <td>{{ $item->token_number }}</td>
                            <td>{{ ucfirst($item->status) }}</td>
                            <td>
                                <a href="{{ route('edit.appointment', $item->id) }}" class="btn btn-sm btn-info">Edit</a>
                                <a href="{{ route('appointment.slip', $item->id) }}" class="btn btn-sm btn-secondary" target="_blank">Print</a>
                                <form action="{{ route('appointment.checkin', $item->id) }}" method="POST" class="d-inline">@csrf<button class="btn btn-sm btn-success">Check-in</button></form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
