@extends('backend.user_dashboard')
@section('user')
<div class="container-fluid">
    @if(!$patient)
        <div class="alert alert-warning">No patient record is linked to your account yet. Contact reception.</div>
    @else
    <div class="row">
        <div class="col-md-4"><div class="card p-3"><h6>Upcoming Appointments</h6><h3>{{ $upcomingAppointments }}</h3></div></div>
        <div class="col-md-4"><div class="card p-3"><h6>Prescriptions</h6><h3>{{ $prescriptionCount }}</h3></div></div>
        <div class="col-md-4"><div class="card p-3"><h6>Pending Bills</h6><h3>{{ $pendingBills }}</h3></div></div>
    </div>
    @endif
</div>
@endsection
