<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PatientCheckedIn extends Notification
{
    use Queueable;

    public function __construct(protected Appointment $appointment)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'patient_checked_in',
            'appointment_id' => $this->appointment->id,
            'patient_id' => $this->appointment->patient_id,
            'patient_name' => $this->appointment->patient->name ?? 'Unknown',
            'consultation_fee' => $this->appointment->consultation_fee,
            'checked_in_at' => $this->appointment->checked_in_at?->toDateTimeString(),
            'message' => 'Patient checked in: '.($this->appointment->patient->name ?? 'Unknown'),
        ];
    }
}
