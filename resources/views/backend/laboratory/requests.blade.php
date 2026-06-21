@extends('backend.laboratory_dashboard')
@section('laboratory')
<div class="card">
    <div class="card-header"><h4>Laboratory Test Requests</h4></div>
    <div class="card-body table-responsive">
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        <table class="table table-bordered">
            <thead><tr><th>Patient</th><th>Doctor</th><th>Test</th><th>Fee</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                @foreach($requests as $req)
                <tr>
                    <td>{{ $req->patient->name ?? 'N/A' }}</td>
                    <td>{{ $req->doctor->name ?? 'N/A' }}</td>
                    <td>{{ $req->test_name }}</td>
                    <td>${{ number_format($req->fee_amount ?? 0, 2) }}</td>
                    <td>{{ ucfirst($req->status) }}</td>
                    <td>
                        @if($req->status === 'pending')
                            <form action="{{ route('laboratory.requests.process', $req->id) }}" method="POST" class="d-inline">@csrf<button class="btn btn-sm btn-info">Start</button></form>
                        @endif
                        @if(in_array($req->status, ['pending', 'in_progress']))
                            <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#complete{{ $req->id }}">Complete</button>
                            <div class="modal fade" id="complete{{ $req->id }}">
                                <div class="modal-dialog"><div class="modal-content">
                                    <form action="{{ route('laboratory.requests.complete', $req->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="modal-header"><h5>Complete: {{ $req->test_name }}</h5></div>
                                        <div class="modal-body">
                                            <textarea name="result" class="form-control mb-2" placeholder="Test result" required></textarea>
                                            <input type="file" name="report_file" class="form-control">
                                        </div>
                                        <div class="modal-footer"><button class="btn btn-success">Complete Test</button></div>
                                    </form>
                                </div></div>
                            </div>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $requests->links() }}
    </div>
</div>
@endsection
