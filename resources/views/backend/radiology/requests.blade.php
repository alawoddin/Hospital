@extends('backend.radiology_dashboard')
@section('radiology')
<div class="card">
    <div class="card-header"><h4>Radiology Scan Requests</h4></div>
    <div class="card-body table-responsive">
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        <table class="table table-bordered">
            <thead><tr><th>Patient</th><th>Doctor</th><th>Scan</th><th>Fee</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                @foreach($requests as $req)
                <tr>
                    <td>{{ $req->patient->name ?? 'N/A' }}</td>
                    <td>{{ $req->doctor->name ?? 'N/A' }}</td>
                    <td>{{ $req->scan_type }}</td>
                    <td>${{ number_format($req->fee_amount, 2) }}</td>
                    <td>{{ ucfirst($req->status) }}</td>
                    <td>
                        @if($req->status === 'pending')
                            <form action="{{ route('radiology.requests.process', $req->id) }}" method="POST" class="d-inline">@csrf<button class="btn btn-sm btn-info">Start</button></form>
                        @endif
                        @if(in_array($req->status, ['pending', 'in_progress']))
                            <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#rad{{ $req->id }}">Complete</button>
                            <div class="modal fade" id="rad{{ $req->id }}">
                                <div class="modal-dialog"><div class="modal-content">
                                    <form action="{{ route('radiology.requests.complete', $req->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="modal-body">
                                            <textarea name="result" class="form-control mb-2" placeholder="Report notes"></textarea>
                                            <input type="file" name="report_file" class="form-control">
                                        </div>
                                        <div class="modal-footer"><button class="btn btn-success">Complete Scan</button></div>
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
