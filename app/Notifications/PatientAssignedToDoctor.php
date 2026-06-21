<?php

namespace App\Notifications;

use App\Models\Patient;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PatientAssignedToDoctor extends Notification
{
    use Queueable;

    public function __construct(protected Patient $patient)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'patient_assigned',
            'patient_id' => $this->patient->id,
            'patient_name' => $this->patient->name,
            'registration_fee' => $this->patient->registration_fee,
            'message' => 'New patient assigned: '.$this->patient->name,
        ];
    }
}
