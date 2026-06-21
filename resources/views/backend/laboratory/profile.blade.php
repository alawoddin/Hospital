@extends('backend.laboratory_dashboard')
@section('laboratory')
<div class="card p-4"><h4>Profile</h4>
<form method="POST" action="{{ route('update.laboratory.profile') }}">@csrf
    <input type="text" name="name" class="form-control mb-2" value="{{ $user->name }}">
    <input type="text" name="phone" class="form-control mb-2" value="{{ $user->phone }}">
    <textarea name="address" class="form-control mb-2">{{ $user->address }}</textarea>
    <button class="btn btn-primary">Update</button>
</form></div>
@endsection
