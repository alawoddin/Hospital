<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $guarded = [];

    public function payments() {
        return $this->hasMany(Payment::class);
    }

    public function paidAmount() {
        return $this->payments()->sum('amount');
    }
}
