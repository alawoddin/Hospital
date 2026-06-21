<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() || $this->user()?->isReception();
    }

    public function rules(): array
    {
        return [
            'id' => ['nullable', 'exists:appointments,id'],
            'patient_id' => ['required', 'exists:patients,id'],
            'doctor_id' => ['required', 'exists:users,id'],
            'appointment_date' => ['required', 'date'],
            'appointment_time' => ['required'],
            'token_number' => ['nullable', 'integer', 'min:1'],
            'status' => ['nullable', 'in:pending,confirmed,canceled'],
            'description' => ['nullable', 'string'],
        ];
    }
}
