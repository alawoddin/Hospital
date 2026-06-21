@extends('backend.pharmacy_dashboard')
@section('pharmacy')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-3"><div class="card p-3"><h6>Medicines Sold Today</h6><h3>{{ $soldToday }}</h3></div></div>
        <div class="col-md-3"><div class="card p-3"><h6>Sold This Month</h6><h3>{{ $soldMonth }}</h3></div></div>
        <div class="col-md-3"><div class="card p-3"><h6>Sold This Year</h6><h3>{{ $soldYear }}</h3></div></div>
        <div class="col-md-3"><div class="card p-3"><h6>Pending Prescriptions</h6><h3>{{ $pendingPrescriptions }}</h3></div></div>
    </div>
    <div class="row">
        <div class="col-md-4"><div class="card p-3"><h6>Medicine Stock</h6><h3>{{ $medicineStock }}</h3></div></div>
        <div class="col-md-4"><div class="card p-3"><h6>Low Stock Medicines</h6><h3>{{ $lowStock }}</h3></div></div>
        <div class="col-md-4"><div class="card p-3"><h6>Expired / Expiring (30d)</h6><h3>{{ $expiringMedicines }}</h3></div></div>
    </div>
</div>
@endsection
