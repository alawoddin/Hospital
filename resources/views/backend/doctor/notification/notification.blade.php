@extends('backend.doctor_dashboard')
@section('doctor')
<div class="p-4">
    <h2>Notifications</h2>

    @forelse($notifications as $notification)
        @php
            $data = $notification->data;
            $type = $data['type'] ?? (isset($data['appointment_id']) ? 'appointment_created' : 'unknown');
        @endphp
        <div class="card p-3 mb-3" id="notification-{{ $notification->id }}">
            @if($type === 'appointment_created' || (isset($data['appointment_id']) && $type !== 'patient_checked_in'))
                <p><strong>New Appointment</strong></p>
                <p>Patient: {{ $data['patient_name'] ?? 'N/A' }}</p>
                <p>Date: {{ $data['appointment_date'] ?? 'N/A' }} | Time: {{ $data['appointment_time'] ?? 'N/A' }}</p>
                <p>Token: {{ $data['token_number'] ?? 'N/A' }}</p>
                <div class="mt-2">
                    @if(isset($data['appointment_id']))
                    <button class="btn btn-success btn-sm" onclick="updateAppointment({{ $data['appointment_id'] }}, 'accept')">Accept</button>
                    <button class="btn btn-danger btn-sm" onclick="updateAppointment({{ $data['appointment_id'] }}, 'ignore')">Ignore</button>
                    @endif
                    <a href="{{ route('doctor.notifications.open', $notification->id) }}" class="btn btn-sm btn-primary">View Appointments</a>
                </div>
            @elseif($type === 'patient_assigned')
                <p><strong>New Patient Assigned to You</strong></p>
                <p>Patient: {{ $data['patient_name'] ?? 'N/A' }}</p>
                <p>Registration Fee: ${{ number_format($data['registration_fee'] ?? 0, 2) }}</p>
                <div class="mt-2">
                    <a href="{{ route('doctor.notifications.open', $notification->id) }}" class="btn btn-sm btn-primary">View Patient</a>
                </div>
            @elseif($type === 'patient_checked_in')
                <p><strong>Patient Checked In</strong></p>
                <p>Patient: {{ $data['patient_name'] ?? 'N/A' }}</p>
                <p>Your Consultation Fee: ${{ number_format($data['consultation_fee'] ?? 0, 2) }}</p>
                <div class="mt-2">
                    <a href="{{ route('doctor.notifications.open', $notification->id) }}" class="btn btn-sm btn-primary">View Patient</a>
                    <a href="{{ route('all.doctor.appointment') }}" class="btn btn-sm btn-secondary">View Appointments</a>
                </div>
            @else
                <p>{{ $data['message'] ?? 'Notification' }}</p>
                <a href="{{ route('doctor.notifications.open', $notification->id) }}" class="btn btn-sm btn-primary mt-2">Open</a>
            @endif
        </div>
    @empty
        <p>No unread notifications.</p>
    @endforelse
</div>

<script>
function updateAppointment(id, action){
    let url = action === 'accept' ?
              "{{ url('/doctor/appointment') }}/"+id+"/accept" :
              "{{ url('/doctor/appointment') }}/"+id+"/ignore";

    $.post(url, {_token: "{{ csrf_token() }}"}, function(response){
        if(response.success){
            if(response.redirect){
                window.location.href = response.redirect;
            } else {
                location.reload();
            }
        }
    });
}
</script>
@endsection
