@extends('backend.user_dashboard')
@section('user')
<div class="card"><div class="card-header"><h4>My Appointments</h4></div>
<div class="card-body table-responsive"><table class="table"><thead><tr><th>Doctor</th><th>Date</th><th>Time</th><th>Status</th></tr></thead>
<tbody>@foreach($appointments as $a)<tr><td>{{ $a->doctor->name ?? 'N/A' }}</td><td>{{ $a->appointment_date }}</td><td>{{ $a->appointment_time }}</td><td>{{ ucfirst($a->status) }}</td></tr>@endforeach</tbody></table></div></div>
@endsection
