<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() || $this->user()?->isFinance();
    }

    public function rules(): array
    {
        return [
            'bill_id' => ['required', 'exists:bills,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_date' => ['required', 'date'],
            'payment_method' => ['required', 'in:cash,card,bank_transfer,other'],
            'reference_no' => ['nullable', 'string', 'max:100'],
        ];
    }
}
