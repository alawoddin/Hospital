@extends('backend.recieption_dashboard')
@section('recieption')
<div class="card">
    <div class="card-header"><h4>Doctor Schedules</h4></div>
    <div class="card-body">
        @foreach($doctors as $doctor)
            <h5>{{ $doctor->name }}</h5>
            <table class="table table-sm mb-4">
                <thead><tr><th>Date</th><th>Time</th><th>Patient</th><th>Token</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse($doctor->doctorAppointments as $appt)
                    <tr>
                        <td>{{ $appt->appointment_date }}</td>
                        <td>{{ $appt->appointment_time }}</td>
                        <td>{{ $appt->patient->name ?? 'N/A' }}</td>
                        <td>{{ $appt->token_number }}</td>
                        <td>{{ ucfirst($appt->status) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5">No upcoming appointments</td></tr>
                    @endforelse
                </tbody>
            </table>
        @endforeach
    </div>
</div>
@endsection
