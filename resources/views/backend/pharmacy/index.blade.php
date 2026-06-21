@extends('backend.pharmacy_dashboard')
@section('pharmacy')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4"><div class="card p-3"><h6>Medicine Stock</h6><h3>{{ $medicineStock }}</h3></div></div>
        <div class="col-md-4"><div class="card p-3"><h6>Expiring Medicines (30 days)</h6><h3>{{ $expiringMedicines }}</h3></div></div>
        <div class="col-md-4"><div class="card p-3"><h6>Pending Prescriptions</h6><h3>{{ $pendingPrescriptions }}</h3></div></div>
    </div>
</div>
@endsection
