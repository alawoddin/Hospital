<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Appointment;

class AppointmentCreated extends Notification
{
    use Queueable;

    protected $appointment;

    /**
     * Create a new notification instance.
     */
    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable)
    {
        return ['database']; // فقط ذخیره در دیتابیس
    }

    /**
     * ذخیره اطلاعات در جدول notifications
     */
    public function toDatabase($notifiable)
    {
        return [
            'appointment_id'   => $this->appointment->id,
            'type'             => 'appointment_created',
            'patient_name'     => $this->appointment->patient->name ?? 'Unknown',
            'appointment_date' => $this->appointment->appointment_date ?? 'N/A',
            'appointment_time' => $this->appointment->appointment_time ?? 'N/A',
            'token_number'     => $this->appointment->token_number ?? 'N/A',
        ];
    }
}
