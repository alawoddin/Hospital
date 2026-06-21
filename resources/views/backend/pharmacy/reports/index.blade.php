@extends('backend.pharmacy_dashboard')
@section('pharmacy')
<div class="row">
    <div class="col-md-4"><div class="card p-3"><h6>Dispensed Prescriptions</h6><h3>{{ $dispensedCount }}</h3></div></div>
</div>
<div class="card mt-3">
    <div class="card-header"><h5>Low Stock (<= 10)</h5></div>
    <div class="card-body">
        <ul>@foreach($lowStock as $m)<li>{{ $m->name }} - {{ $m->totalStock() }}</li>@endforeach</ul>
    </div>
</div>
<div class="card mt-3">
    <div class="card-header"><h5>Expiring Within 30 Days</h5></div>
    <div class="card-body">
        <ul>@foreach($expiring as $s)<li>{{ $s->medicine->name }} - Batch {{ $s->batch_no }} - {{ $s->expiry_date }}</li>@endforeach</ul>
    </div>
</div>
@endsection
