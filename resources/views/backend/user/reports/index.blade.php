@extends('backend.user_dashboard')
@section('user')
<div class="card"><div class="card-header d-flex justify-content-between"><h4>Medical Reports</h4><a href="{{ route('user.reports') }}?print=1" class="btn btn-secondary" onclick="window.print();return false;">Download / Print</a></div>
<div class="card-body">
<h5>Diagnoses</h5><ul>@foreach($patient->diagnoses as $d)<li>{{ $d->title }} - {{ $d->description }} ({{ $d->created_at->format('Y-m-d') }})</li>@endforeach</ul>
<h5>Lab Results</h5><ul>@foreach($patient->labRequests as $l)<li>{{ $l->test_name }} - {{ $l->status }} @if($l->result): {{ $l->result }}@endif</li>@endforeach</ul>
<h5>Prescriptions</h5><ul>@foreach($patient->prescriptions as $p)<li>{{ $p->created_at->format('Y-m-d') }} by Dr. {{ $p->doctor->name ?? 'N/A' }}</li>@endforeach</ul>
</div></div>
@endsection
