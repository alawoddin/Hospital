<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicineStock extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'expiry_date' => 'date',
            'purchase_price' => 'decimal:2',
        ];
    }

    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }
}
