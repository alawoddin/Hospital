@extends('backend.radiology_dashboard')
@section('radiology')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-3"><div class="card p-3"><h6>Scans Today</h6><h3>{{ $today }}</h3></div></div>
        <div class="col-md-3"><div class="card p-3"><h6>Scans This Month</h6><h3>{{ $month }}</h3></div></div>
        <div class="col-md-3"><div class="card p-3"><h6>Scans This Year</h6><h3>{{ $year }}</h3></div></div>
        <div class="col-md-3"><div class="card p-3"><h6>Pending</h6><h3>{{ $pending }}</h3></div></div>
    </div>
    <div class="row">
        <div class="col-md-6"><div class="card p-3"><h6>Revenue This Month</h6><h3>${{ number_format($monthRevenue, 2) }}</h3></div></div>
        <div class="col-md-6"><div class="card p-3"><h6>Revenue This Year</h6><h3>${{ number_format($yearRevenue, 2) }}</h3></div></div>
    </div>
</div>
@endsection
