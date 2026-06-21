@extends('backend.recieption_dashboard')
@section('recieption')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3"><div class="card p-3"><h6>New Patients Today</h6><h3>{{ $newRegistrations }}</h3></div></div>
        <div class="col-md-3"><div class="card p-3"><h6>Appointments Today</h6><h3>{{ $todayAppointments }}</h3></div></div>
        <div class="col-md-3"><div class="card p-3"><h6>Pending Payments</h6><h3>{{ $pendingPayments }}</h3></div></div>
        <div class="col-md-3"><div class="card p-3"><h6>Collected Today</h6><h3>${{ number_format($collectedToday, 2) }}</h3></div></div>
    </div>
</div>
@endsection
