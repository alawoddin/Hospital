@extends('backend.recieption_dashboard')
@section('recieption')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6"><div class="card p-3"><h6>New Registrations Today</h6><h3>{{ $newRegistrations }}</h3></div></div>
        <div class="col-md-6"><div class="card p-3"><h6>Today's Appointments</h6><h3>{{ $todayAppointments }}</h3></div></div>
    </div>
</div>
@endsection
