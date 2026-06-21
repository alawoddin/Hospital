@extends('backend.doctor_dashboard')
@section('doctor')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(($stats['consultation_fee'] ?? 0) <= 0)
        <div class="alert alert-warning">Your per-patient fee is not set. Ask admin: Users → Edit Doctor → set fee (e.g. Neurology $300, OPD $200).</div>
    @endif

    <div class="card mb-4 border-primary">
        <div class="card-header bg-primary text-white"><h4 class="mb-0">This Month — {{ now()->format('F Y') }}</h4></div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-4">
                    <h2 class="text-primary mb-0">{{ $stats['month_patients'] }}</h2>
                    <p class="mb-0">Patients you checked this month</p>
                </div>
                <div class="col-md-4">
                    <h2 class="text-success mb-0">${{ number_format($stats['month_earnings'], 2) }}</h2>
                    <p class="mb-0">Your total fee this month</p>
                    @if($stats['month_patients'] > 0)
                        <small class="text-muted">{{ $stats['month_patients'] }} patient(s) checked</small>
                    @endif
                </div>
                <div class="col-md-4">
                    <h2 class="text-info mb-0">{{ $stats['month_appointments'] }}</h2>
                    <p class="mb-0">Total appointments this month</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4 border-success">
        <div class="card-header bg-success text-white"><h4 class="mb-0">This Year — {{ now()->year }}</h4></div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-4">
                    <h2 class="text-primary mb-0">{{ $stats['year_patients'] }}</h2>
                    <p class="mb-0">Total patients you checked this year</p>
                </div>
                <div class="col-md-4">
                    <h2 class="text-success mb-0">${{ number_format($stats['year_earnings'], 2) }}</h2>
                    <p class="mb-0">Your total fee this year</p>
                </div>
                <div class="col-md-4">
                    <h2 class="text-info mb-0">{{ $stats['year_appointments'] }}</h2>
                    <p class="mb-0">Total appointments this year</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3"><div class="card p-3"><h6>Assigned Patients</h6><h3>{{ $assignedPatients }}</h3></div></div>
        <div class="col-md-3"><div class="card p-3"><h6>Today's Appointments</h6><h3>{{ $todayAppointments }}</h3></div></div>
        <div class="col-md-3"><div class="card p-3"><h6>Checked Today</h6><h3>{{ $stats['today_consultations'] }}</h3></div></div>
        <div class="col-md-3"><div class="card p-3"><h6>Pending Patients</h6><h3>{{ $stats['pending_patients'] ?? 0 }}</h3></div></div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header"><h5>Recent Consultations</h5></div>
                <div class="card-body">
                    @forelse($recentConsultations as $c)
                        <div class="border-bottom pb-2 mb-2">
                            <strong>{{ $c->patient->name ?? 'Patient' }}</strong>
                            <span class="text-success">${{ number_format($c->consultation_fee, 2) }}</span>
                            <div class="text-muted small">{{ $c->visited_at->format('M d, Y h:i A') }}</div>
                        </div>
                    @empty
                        <p>No consultations yet. Open a patient and click <strong>Complete Consultation</strong>, or ask reception to check-in.</p>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between">
                    <h5>Notifications</h5>
                    <a href="{{ route('doctor.notifications') }}">View all ({{ $notificationCount }})</a>
                </div>
                <div class="card-body">
                    @forelse($recentNotifications as $notification)
                        @php $data = $notification->data; @endphp
                        <div class="border-bottom pb-2 mb-2 d-flex justify-content-between">
                            <div>
                                <strong>{{ $data['patient_name'] ?? ($data['message'] ?? 'Notification') }}</strong>
                                <div class="text-muted small">{{ $notification->created_at->diffForHumans() }}</div>
                            </div>
                            <a href="{{ route('doctor.notifications.open', $notification->id) }}" class="btn btn-sm btn-primary">Open</a>
                        </div>
                    @empty
                        <p>No new notifications.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
