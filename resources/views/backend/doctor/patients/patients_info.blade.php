@extends('backend.doctor_dashboard')
@section('doctor')
<style>
.workflow-card { border-radius: 8px; margin-bottom: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
.workflow-card .card-header { font-weight: 600; }
.review-box { background: #fff8e1; border: 1px solid #ffc107; border-radius: 6px; padding: 1rem; margin-bottom: 1rem; }
.medicine-row { background: #f8f9fa; padding: 12px; border-radius: 6px; margin-bottom: 10px; }
</style>

<div class="container-fluid py-3">
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

    {{-- Patient header --}}
    <div class="card workflow-card">
        <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h3 class="mb-1">{{ $patient->name }}</h3>
                <p class="mb-0 text-muted">Phone: {{ $patient->phone }} | Age: {{ $patient->age }}</p>
            </div>
            <span class="badge badge-success p-2">Your fee: ${{ number_format(auth()->user()->consultation_fee ?? 0, 2) }}</span>
        </div>
    </div>

    {{-- DOCTOR REVIEW: Lab results from laboratory --}}
    @if($pendingLabReviews->count())
    <div class="card workflow-card border-warning">
        <div class="card-header bg-warning text-dark"><h5 class="mb-0">Laboratory Results — Review &amp; Confirm</h5></div>
        <div class="card-body">
            @foreach($pendingLabReviews as $lab)
            <div class="review-box">
                <strong>{{ $lab->test_name }}</strong>
                <span class="badge badge-success ml-2">Completed</span>
                <p class="mt-2 mb-1"><strong>Result:</strong> {{ $lab->result }}</p>
                @if($lab->report_file)
                    <p class="mb-2"><a href="{{ asset('storage/'.$lab->report_file) }}" target="_blank">View report file</a></p>
                @endif
                <form action="{{ route('doctor.lab.confirm', $lab->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button class="btn btn-success btn-sm">Confirm &amp; Send To Reception</button>
                </form>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- DOCTOR REVIEW: Pharmacy dispensed --}}
    @if($pendingPrescriptionReviews->count())
    <div class="card workflow-card border-info">
        <div class="card-header bg-info text-white"><h5 class="mb-0">Pharmacy Completed — Review &amp; Confirm</h5></div>
        <div class="card-body">
            @foreach($pendingPrescriptionReviews as $rx)
            <div class="review-box" style="background:#e3f2fd;border-color:#17a2b8;">
                <strong>Prescription #{{ $rx->id }}</strong> — dispensed {{ $rx->dispensed_at?->format('M d, Y h:i A') }}
                <ul class="mt-2 mb-2">
                    @foreach($rx->items as $item)
                        <li>{{ $item->medicine }} × {{ $item->quantity }} — {{ $item->frequency }} — {{ $item->desc }}</li>
                    @endforeach
                </ul>
                <form action="{{ route('doctor.prescription.confirm', $rx->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button class="btn btn-primary btn-sm">Confirm &amp; Send To Reception</button>
                </form>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Request lab --}}
    <div class="card workflow-card border-primary" id="lab">
        <div class="card-header bg-primary text-white"><h5 class="mb-0">Request Laboratory Test (CBC, etc.)</h5></div>
        <div class="card-body">
            <form action="{{ route('doctor.store.lab_request', $patient->id) }}" method="POST" class="row">
                @csrf
                <div class="col-md-4 form-group">
                    <label>Test</label>
                    <select name="test_key" class="form-control" required>
                        <option value="">Select test...</option>
                        @foreach($laboratoryTests as $key => $test)
                            <option value="{{ $key }}" @selected($key === 'cbc')>{{ $test['name'] }} — ${{ number_format($test['fee'], 2) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 form-group">
                    <label>Appointment</label>
                    <select name="appointment_id" class="form-control">
                        <option value="">Optional</option>
                        @foreach($appointments as $appt)
                            <option value="{{ $appt->id }}">{{ $appt->appointment_date }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 form-group">
                    <label>Instructions</label>
                    <textarea name="instructions" class="form-control" rows="2"></textarea>
                </div>
                <div class="col-12">
                    <button class="btn btn-primary">Send To Laboratory</button>
                </div>
            </form>
            @if($patient->labRequests->count())
                <hr><h6>Lab status</h6>
                <ul class="mb-0">
                    @foreach($patient->labRequests as $l)
                        <li>{{ $l->test_name }} — {{ ucfirst($l->status) }}
                            @if($l->doctor_confirmed_at) <span class="text-success">(Confirmed → Reception)</span>@endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    {{-- Prescription - doctor writes own medicine --}}
    <div class="card workflow-card border-success" id="pharmacy">
        <div class="card-header bg-success text-white"><h5 class="mb-0">Write Prescription &amp; Send To Pharmacy</h5></div>
        <div class="card-body">
            <p class="text-muted">Write medicine name, quantity, duration and instructions yourself. Click Save &amp; Send.</p>
            <form action="{{ route('doctor.store.prescription') }}" method="POST">
                @csrf
                <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                <div class="row mb-3">
                    <div class="col-md-6 form-group">
                        <label>Pharmacy</label>
                        <select name="pharmacy_id" class="form-control" required>
                            <option value="">Select pharmacy</option>
                            @foreach($pharmacies as $pharmacy)
                                <option value="{{ $pharmacy->id }}">{{ $pharmacy->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Appointment (optional)</label>
                        <select name="appointment_id" class="form-control">
                            <option value="">None</option>
                            @foreach($appointments as $appt)
                                <option value="{{ $appt->id }}">{{ $appt->appointment_date }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div id="medicineWrapper">
                    <div class="row medicine-row align-items-end">
                        <div class="col-md-3 form-group mb-0">
                            <label>Medicine Name</label>
                            <input type="text" name="medicine[]" class="form-control" placeholder="e.g. Paracetamol 500mg" required>
                        </div>
                        <div class="col-md-2 form-group mb-0">
                            <label>Quantity</label>
                            <input type="number" name="quantity[]" class="form-control" value="1" min="1" required>
                        </div>
                        <div class="col-md-2 form-group mb-0">
                            <label>Duration</label>
                            <input type="text" name="frequency[]" class="form-control" placeholder="e.g. 7 days" required>
                        </div>
                        <div class="col-md-5 form-group mb-0">
                            <label>Instructions</label>
                            <input type="text" name="desc[]" class="form-control" placeholder="e.g. 1 tablet after meals">
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-outline-secondary btn-sm mt-2" id="addBtn">+ Add Another Medicine</button>
                <div class="mt-3">
                    <button type="submit" class="btn btn-success btn-lg">Save &amp; Send To Pharmacy</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Diagnosis --}}
    <div class="card workflow-card" id="diagnosis">
        <div class="card-header"><h5 class="mb-0">Add Diagnosis</h5></div>
        <div class="card-body">
            <form action="{{ route('doctor.store.diagnosis', $patient->id) }}" method="POST" class="row">
                @csrf
                <div class="col-md-4 mb-2"><input type="text" name="title" class="form-control" placeholder="Diagnosis" required></div>
                <div class="col-md-4 mb-2"><input type="text" name="severity" class="form-control" placeholder="Severity"></div>
                <div class="col-12 mb-2"><textarea name="description" class="form-control" placeholder="Notes"></textarea></div>
                <div class="col-12"><button class="btn btn-primary">Save Diagnosis</button></div>
            </form>
        </div>
    </div>

    {{-- Complete visit --}}
    <div class="card workflow-card border-success" id="consultation">
        <div class="card-header bg-success text-white"><h5 class="mb-0">Complete Consultation</h5></div>
        <div class="card-body">
            @if($alreadyConsultedToday)
                <p class="text-success mb-0">Patient already checked today.</p>
            @else
                <form action="{{ route('doctor.complete.consultation', $patient->id) }}" method="POST" class="row">
                    @csrf
                    <div class="col-md-6 form-group">
                        <select name="appointment_id" class="form-control">
                            <option value="">Link appointment (optional)</option>
                            @foreach($appointments as $appt)
                                <option value="{{ $appt->id }}">{{ $appt->appointment_date }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-success btn-lg">Complete &amp; Record Fee (${{ number_format(auth()->user()->consultation_fee ?? 0, 2) }})</button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>

<script>
document.getElementById('addBtn')?.addEventListener('click', function () {
    const wrapper = document.getElementById('medicineWrapper');
    const row = wrapper.querySelector('.medicine-row').cloneNode(true);
    row.querySelectorAll('input').forEach(el => {
        el.value = el.name.includes('quantity') ? '1' : '';
    });
    wrapper.appendChild(row);
});
</script>
@endsection
