<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bill extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'bill_date' => 'date',
            'discount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'due_amount' => 'decimal:2',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(BillItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function paidAmount(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    public function refreshPaymentStatus(): void
    {
        $paid = $this->paidAmount();
        $due = max(0, (float) $this->total_amount - $paid);

        $status = match (true) {
            $due <= 0 => 'paid',
            $paid > 0 => 'partially_paid',
            default => 'pending',
        };

        $this->update([
            'due_amount' => $due,
            'status' => $status,
        ]);
    }
}
