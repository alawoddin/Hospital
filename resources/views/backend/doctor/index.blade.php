@extends('backend.doctor_dashboard')
@section('doctor')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6"><div class="card p-3"><h6>Today's Appointments</h6><h3>{{ $todayAppointments }}</h3></div></div>
        <div class="col-md-6"><div class="card p-3"><h6>Assigned Patients</h6><h3>{{ $assignedPatients }}</h3></div></div>
    </div>
</div>
@endsection
