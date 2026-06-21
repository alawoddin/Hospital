@extends('backend.doctor_dashboard')
@section('doctor')
<div class="row mt-4">
    <div class="col-xl-10 mx-auto">
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        <div class="card mb-4">
            <div class="card-body">
                <h3>{{ $patient->name }}</h3>
                <p>Phone: {{ $patient->phone }} | Email: {{ $patient->email }} | Age: {{ $patient->age }}</p>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><h5>Add Diagnosis</h5></div>
            <div class="card-body">
                <form action="{{ route('doctor.store.diagnosis', $patient->id) }}" method="POST" class="row">
                    @csrf
                    <div class="col-md-4 mb-2">
                        <select name="appointment_id" class="form-control">
                            <option value="">Appointment (optional)</option>
                            @foreach($appointments as $appt)
                                <option value="{{ $appt->id }}">{{ $appt->appointment_date }} - #{{ $appt->token_number }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-2"><input type="text" name="title" class="form-control" placeholder="Diagnosis title" required></div>
                    <div class="col-md-4 mb-2"><input type="text" name="severity" class="form-control" placeholder="Severity"></div>
                    <div class="col-12 mb-2"><textarea name="description" class="form-control" placeholder="Description"></textarea></div>
                    <div class="col-12"><button class="btn btn-primary">Save Diagnosis</button></div>
                </form>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><h5>Medical Note</h5></div>
            <div class="card-body">
                <form action="{{ route('doctor.store.medical_note', $patient->id) }}" method="POST">
                    @csrf
                    <textarea name="note" class="form-control mb-2" required></textarea>
                    <button class="btn btn-primary">Add Note</button>
                </form>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><h5>Treatment Plan</h5></div>
            <div class="card-body">
                <form action="{{ route('doctor.store.treatment_plan', $patient->id) }}" method="POST" class="row">
                    @csrf
                    <div class="col-md-6 mb-2"><input type="text" name="title" class="form-control" placeholder="Plan title" required></div>
                    <div class="col-md-3 mb-2"><input type="date" name="start_date" class="form-control"></div>
                    <div class="col-md-3 mb-2"><input type="date" name="end_date" class="form-control"></div>
                    <div class="col-12 mb-2"><textarea name="plan" class="form-control" placeholder="Treatment plan" required></textarea></div>
                    <div class="col-12"><button class="btn btn-primary">Save Plan</button></div>
                </form>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><h5>Lab Request</h5></div>
            <div class="card-body">
                <form action="{{ route('doctor.store.lab_request', $patient->id) }}" method="POST" class="row">
                    @csrf
                    <div class="col-md-6 mb-2"><input type="text" name="test_name" class="form-control" placeholder="Test name" required></div>
                    <div class="col-12 mb-2"><textarea name="instructions" class="form-control" placeholder="Instructions"></textarea></div>
                    <div class="col-12"><button class="btn btn-primary">Request Test</button></div>
                </form>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><h5>Prescription</h5></div>
            <div class="card-body">
                <form action="{{ route('doctor.store.prescription') }}" method="POST">
                    @csrf
                    <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label>Pharmacy</label>
                            <select name="pharmacy_id" class="form-control" required>
                                <option value="">Select pharmacy</option>
                                @foreach($pharmacies as $pharmacy)
                                    <option value="{{ $pharmacy->id }}">{{ $pharmacy->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Appointment</label>
                            <select name="appointment_id" class="form-control">
                                <option value="">Optional</option>
                                @foreach($appointments as $appt)
                                    <option value="{{ $appt->id }}">{{ $appt->appointment_date }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div id="medicineWrapper">
                        <div class="row medicine-row mb-2">
                            <div class="col-md-3"><input type="text" name="medicine[]" class="form-control" placeholder="Medicine" required></div>
                            <div class="col-md-2"><input type="text" name="dosage[]" class="form-control" placeholder="Dosage"></div>
                            <div class="col-md-2"><input type="text" name="frequency[]" class="form-control" placeholder="Frequency"></div>
                            <div class="col-md-2"><input type="number" name="quantity[]" class="form-control" value="1" min="1"></div>
                            <div class="col-md-3"><textarea name="desc[]" class="form-control" placeholder="Notes"></textarea></div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-secondary" id="addBtn">Add Medicine</button>
                    <button type="submit" class="btn btn-success mt-3">Submit Prescription</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h5>Patient History</h5></div>
            <div class="card-body">
                <h6>Diagnoses</h6>
                <ul>@foreach($patient->diagnoses as $d)<li>{{ $d->title }} - {{ $d->created_at->format('Y-m-d') }}</li>@endforeach</ul>
                <h6>Medical Notes</h6>
                <ul>@foreach($patient->medicalNotes as $n)<li>{{ $n->note }}</li>@endforeach</ul>
                <h6>Lab Requests</h6>
                <ul>@foreach($patient->labRequests as $l)<li>{{ $l->test_name }} ({{ $l->status }})</li>@endforeach</ul>
            </div>
        </div>
    </div>
</div>
<script>
document.getElementById('addBtn').addEventListener('click', function () {
    const wrapper = document.getElementById('medicineWrapper');
    const row = wrapper.querySelector('.medicine-row').cloneNode(true);
    row.querySelectorAll('input, textarea').forEach(el => el.value = el.name.includes('quantity') ? '1' : '');
    wrapper.appendChild(row);
});
</script>
@endsection
