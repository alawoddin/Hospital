<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $guarded = [];

    public function creator() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function patient() {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function doctor() {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    protected static function boot() {
        parent::boot();

        static::deleting(function ($appointment) {
            // پاک کردن تمام اعلان‌های مرتبط با این نوبت
            \DB::table('notifications')->where('data->appointment_id', $appointment->id)->delete();
        });
    }

}
