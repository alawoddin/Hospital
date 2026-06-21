<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePrescriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isDoctor();
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'exists:patients,id'],
            'pharmacy_id' => ['required', 'exists:users,id'],
            'appointment_id' => ['nullable', 'exists:appointments,id'],
            'notes' => ['nullable', 'string'],
            'medicine' => ['required', 'array', 'min:1'],
            'medicine.*' => ['required', 'string', 'max:255'],
            'medicine_id' => ['nullable', 'array'],
            'medicine_id.*' => ['nullable', 'exists:medicines,id'],
            'desc' => ['nullable', 'array'],
            'desc.*' => ['nullable', 'string'],
            'dosage' => ['nullable', 'array'],
            'dosage.*' => ['nullable', 'string', 'max:100'],
            'frequency' => ['nullable', 'array'],
            'frequency.*' => ['nullable', 'string', 'max:100'],
            'quantity' => ['nullable', 'array'],
            'quantity.*' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
