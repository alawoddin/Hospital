@extends('backend.admin_dashboard')
@section('admin')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3"><div class="card p-3"><h6>Total Patients</h6><h3>{{ $totalPatients }}</h3></div></div>
        <div class="col-md-3"><div class="card p-3"><h6>Total Doctors</h6><h3>{{ $totalDoctors }}</h3></div></div>
        <div class="col-md-3"><div class="card p-3"><h6>Total Revenue</h6><h3>${{ number_format($totalRevenue, 2) }}</h3></div></div>
        <div class="col-md-3"><div class="card p-3"><h6>Total Appointments</h6><h3>{{ $totalAppointments }}</h3></div></div>
    </div>
</div>
@endsection
