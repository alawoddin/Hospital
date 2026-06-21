<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrescriptionItem extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'dispensed' => 'boolean',
        ];
    }

    public function prescription(): BelongsTo
    {
        return $this->belongsTo(Prescription::class, 'prescription_id');
    }

    public function medicineModel(): BelongsTo
    {
        return $this->belongsTo(Medicine::class, 'medicine_id');
    }
}
