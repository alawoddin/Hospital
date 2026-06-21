@extends('backend.user_dashboard')
@section('user')
<div class="card"><div class="card-header"><h4>My Prescriptions</h4></div>
<div class="card-body">@foreach($prescriptions as $p)
<div class="border p-3 mb-3"><strong>Dr. {{ $p->doctor->name ?? 'N/A' }}</strong> - {{ $p->created_at->format('Y-m-d') }} ({{ $p->status }})
<ul>@foreach($p->items as $item)<li>{{ $item->medicine }} - {{ $item->desc }}</li>@endforeach</ul></div>
@endforeach</div></div>
@endsection
