@extends('backend.pharmacy_dashboard')
@section('pharmacy')

<div class="row mt-5">
    <div class="col-xl-12 col-lg-10 col-md-8 mx-auto">
        <div class="card">
            <div class="card-body">
                <div class="text-center">
                    <div class="profile-photo">
                        <img src="{{ asset($prescription->patient->photo) }}" width="200" class="img-fluid rounded-circle" alt="">
                    </div>
                    <h3 class="mt-4 mb-1">{{ $prescription->patient->name ?? '' }}</h3>
                    <p class="text-muted">{{ $prescription->patient->email ?? '' }}</p>
                </div>
            </div>
            <div class="card-footer pt-0 pb-0 text-center">
                <table class="table table-hovered table-stripped mt-3">
                    <thead>
                        <tr>
                            <th><h5>Name</h5></th>
                            <th><h5>Father Name</h5></th>
                            <th><h5>Last Name</h5></th>
                            <th><h5>Gender</h5></th>
                            <th><h5>Age</h5></th>
                            <th><h5>Email</h5></th>
                            <th><h5>Phone</h5></th>
                            <th><h5>Doctor</h5></th>
                            <th><h5>Address</h5></th>
                            <th><h5>National ID</h5></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><h6 style="color:rgb(19, 120, 167);">{{ $prescription->patient->name ?? '' }}</h6></td>
                            <td><h6 style="color:rgb(19, 120, 167);">{{ $prescription->patient->father_name ?? '' }}</h6></td>
                            <td><h6 style="color:rgb(19, 120, 167);">{{ $prescription->patient->last_name ?? '' }}</h6></td>
                            <td><h6 style="color:rgb(19, 120, 167);">{{ $prescription->patient->gender ?? '' }}</h6></td>
                            <td><h6 style="color:rgb(19, 120, 167);">{{ $prescription->patient->age ?? '' }}</h6></td>
                            <td><h6 style="color:rgb(19, 120, 167);">{{ $prescription->patient->email ?? '' }}</h6></td>
                            <td><h6 style="color:rgb(19, 120, 167);">{{ $prescription->patient->phone ?? '' }}</h6></td>
                            <td><h6 style="color:rgb(19, 120, 167);">{{ $prescription->doctor->name ?? '' }}</h6></td>
                            <td><h6 style="color:rgb(19, 120, 167);">{{ $prescription->patient->address ?? '' }}</h6></td>
                            <td><h6 style="color:rgb(19, 120, 167);">{{ $prescription->patient->national_id ?? '' }}</h6></td>
                        </tr>
                    </tbody>
                </table>
            </div>
                {{--
            <div class="card-footer pt-0 pb-0 text-center">
                <div class="row">
                    <div class="col-6 pt-3 pb-3 border-bottom float-left d-flex">
                        <h4>Name: <span style="margin-left: 20px; color:rgb(19, 120, 167);">{{ $prescription->patient->name ?? '' }}</span></h4>
                    </div>
                    <div class="col-6 pt-3 pb-3 border-bottom float-left d-flex">
                        <h4>Father Name: <span style="margin-left: 20px; color:rgb(19, 120, 167);">{{ $prescription->patient->father_name ?? '' }}</span></h4>
                    </div>
                    <div class="col-6 pt-3 pb-3 border-bottom float-left d-flex">
                        <h4>Last Name: <span style="margin-left: 20px; color:rgb(19, 120, 167);">{{ $prescription->patient->last_name ?? '' }}</span></h4>
                    </div>
                    <div class="col-6 pt-3 pb-3 border-bottom float-left d-flex">
                        <h4>Gender: <span style="margin-left: 20px; color:rgb(19, 120, 167);">{{ $prescription->patient->gender ?? '' }}</span></h4>
                    </div>
                    <div class="col-6 pt-3 pb-3 border-bottom float-left d-flex">
                        <h4>Age: <span style="margin-left: 20px; color:rgb(19, 120, 167);">{{ $prescription->patient->age ?? '' }}</span></h4>
                    </div>
                    <div class="col-6 pt-3 pb-3 border-bottom float-left d-flex">
                        <h4>Email: <span style="margin-left: 20px; color:rgb(19, 120, 167);">{{ $prescription->patient->email ?? '' }}</span></h4>
                    </div>
                    <div class="col-6 pt-3 pb-3 border-bottom float-left d-flex">
                        <h4>Phone: <span style="margin-left: 20px; color:rgb(19, 120, 167);">{{ $prescription->patient->phone ?? '' }}</span></h4>
                    </div>
                    <div class="col-6 pt-3 pb-3 border-bottom float-left d-flex">
                        <h4>Doctor: <span style="margin-left: 20px; color:rgb(19, 120, 167);">{{ $prescription->doctor->name ?? '' }}</span></h4>
                    </div>
                </div>
            </div> --}}
        </div>
    </div>
</div>
<div class="row mt-5">
    <div class="col-12 pt-3 pb-3 border-bottom mx-auto">
        <div class="card p-3">
            <h2 class="m-2">Prescription:</h2>
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>Medicine</th>
                    <th>Quantity</th>
                    <th>Duration</th>
                    <th>Instructions</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($prescription->items as $item)
                    <tr>
                        <td>{{ $item->medicine }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->frequency }}</td>
                        <td>{{ $item->desc }}</td>
                        <td>{{ $item->dispensed ? 'Dispensed' : 'Pending' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
        @if($prescription->status !== 'dispensed')
        <form action="{{ route('pharmacy.prescription.dispense', $prescription->id) }}" method="POST" class="mt-3">
            @csrf
            <button type="submit" class="btn btn-success btn-lg" onclick="return confirm('Dispense medicines? Doctor will review before reception is notified.')">Dispense Medicines</button>
        </form>
        @elseif(!$prescription->doctor_confirmed_at)
        <p class="text-info mt-3">Dispensed — waiting for doctor to confirm, then reception will be notified.</p>
        @else
        <p class="text-success mt-3">Dispensed and confirmed by doctor. Sent to reception.</p>
        @endif
    </div>
</div>

@endsection