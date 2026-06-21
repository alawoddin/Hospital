<!DOCTYPE html>
<html>
<head><title>Appointment Slip</title><style>body{font-family:Arial;padding:30px} .box{border:1px solid #333;padding:20px;max-width:500px}</style></head>
<body onload="window.print()">
<div class="box">
    <h2>Appointment Slip</h2>
    <p><strong>Patient:</strong> {{ $appointment->patient->name }}</p>
    <p><strong>Doctor:</strong> {{ $appointment->doctor->name }}</p>
    <p><strong>Date:</strong> {{ $appointment->appointment_date }}</p>
    <p><strong>Time:</strong> {{ $appointment->appointment_time }}</p>
    <p><strong>Token:</strong> {{ $appointment->token_number }}</p>
    <p><strong>Status:</strong> {{ ucfirst($appointment->status) }}</p>
</div>
</body>
</html>
