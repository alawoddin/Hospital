@extends('backend.doctor_dashboard')
@section('doctor')

<div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">All Appointments</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        {{-- <div>
                            <a href="{{ route('add.doctor.appointment') }}" class="btn btn-primary mb-2" style="float:right;">Add Appointment</a>
                        </div> --}}
                        <table id="example" class="display" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th>SI</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Token</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($appointments as $key => $appointment)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ $appointment->patient->name ?? 'N/A' }}</td>
                                    <td>{{ $appointment->doctor->name ?? 'N/A' }}</td>
                                    <td>{{ $appointment->appointment_date }}</td>
                                    <td>{{ $appointment->appointment_time }}</td>
                                    <td>{{ $appointment->token_number }}</td>
                                    <td>{{ $appointment->status }}</td>
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