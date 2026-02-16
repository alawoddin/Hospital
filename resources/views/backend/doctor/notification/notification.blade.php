@extends('backend.doctor_dashboard')
@section('doctor')
<div class="p-4">
    <h2>📩 All Appointments</h2>

    @foreach($notifications as $notification)
        @php $data = $notification->data; @endphp
        @if(isset($data['appointment_id']))
            <div class="notification-card" id="notification-{{ $data['appointment_id'] }}">
                <p>Patient: {{ $data['patient_name'] }}</p>
                <p>Date: {{ $data['appointment_date'] }}</p>
                <p>Time: {{ $data['appointment_time'] }}</p>
                <p>Token: {{ $data['token_number'] }}</p>

                <button class="btn btn-success btn-sm" onclick="updateAppointment({{ $data['appointment_id'] }}, 'accept')">Accept</button>
                <button class="btn btn-danger btn-sm" onclick="updateAppointment({{ $data['appointment_id'] }}, 'ignore')">Ignore</button>
            </div>
            <hr>
        @endif
    @endforeach
</div>

<script>
function updateAppointment(id, action){
    let url = action === 'accept' ? 
              "{{ url('/doctor/appointment') }}/"+id+"/accept" :
              "{{ url('/doctor/appointment') }}/"+id+"/ignore";

    $.post(url, {_token: "{{ csrf_token() }}"}, function(response){
        if(response.success){
            // حذف نوتیفیکیشن از صفحه
            $('#notification-'+id).remove();
            // آپدیت جدول نوبت‌ها
            loadAppointments();
            // آپدیت شمارنده نوتیفیکیشن‌ها
            loadAppointmentCount();
        }
    });
}
</script>
@endsection
