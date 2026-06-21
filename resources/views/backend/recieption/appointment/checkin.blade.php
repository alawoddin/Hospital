@extends('backend.recieption_dashboard')
@section('recieption')
<div class="container-fluid">
    <div class="card p-4">
        <h4>Check-in Patient</h4>
        <p>
            <strong>Patient:</strong> {{ $appointment->patient->name ?? 'N/A' }} |
            <strong>Doctor:</strong> {{ $appointment->doctor->name ?? 'N/A' }} |
            <strong>Date:</strong> {{ $appointment->appointment_date }}
        </p>

        <div class="alert alert-info">
            <strong>Doctor consultation fee:</strong>
            ${{ number_format($appointment->doctor->consultation_fee ?? 0, 2) }}
            <small class="d-block text-muted">This fee is set by admin when the doctor was added (e.g. Neurology $300, OPD $200).</small>
        </div>

        <form action="{{ route('appointment.checkin', $appointment->id) }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="font-weight-bold">Laboratory Tests (optional — same patient)</label>
                <div class="row">
                    @forelse($laboratoryTests as $key => $lab)
                        <div class="col-md-4 mb-2">
                            <div class="form-check border p-2 rounded">
                                <input class="form-check-input" type="checkbox" name="laboratory_tests[]" value="{{ $key }}" id="lab{{ $key }}">
                                <label class="form-check-label" for="lab{{ $key }}">
                                    {{ $lab['name'] }} — <strong>${{ number_format($lab['fee'], 2) }}</strong>
                                </label>
                            </div>
                        </div>
                    @empty
                        <div class="col-12"><p class="text-muted">No laboratory tests configured.</p></div>
                    @endforelse
                </div>
            </div>

            <button type="submit" class="btn btn-success btn-lg">Check-in &amp; Create Invoice</button>
            <a href="{{ route('all.appointment') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
