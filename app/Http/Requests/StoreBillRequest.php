<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBillRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() || $this->user()?->isFinance();
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'exists:patients,id'],
            'appointment_id' => ['nullable', 'exists:appointments,id'],
            'bill_date' => ['required', 'date'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'description' => ['required', 'array', 'min:1'],
            'description.*' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'array', 'min:1'],
            'quantity.*' => ['required', 'integer', 'min:1'],
            'unit_price' => ['required', 'array', 'min:1'],
            'unit_price.*' => ['required', 'numeric', 'min:0'],
        ];
    }
}
