<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'consultation_fee' => 'decimal:2',
        ];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function patientProfile(): HasMany
    {
        return $this->hasMany(Patient::class, 'user_id');
    }

    public function doctorAppointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'doctor_id');
    }

    public function assignedPatients(): HasMany
    {
        return $this->hasMany(Patient::class, 'doctor_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isDoctor(): bool
    {
        return $this->role === 'doctor';
    }

    public function isReception(): bool
    {
        return $this->role === 'recieption';
    }

    public function isFinance(): bool
    {
        return $this->role === 'finance';
    }

    public function isPharmacy(): bool
    {
        return $this->role === 'pharmacy';
    }

    public function isLaboratory(): bool
    {
        return $this->role === 'laboratory';
    }

    public function isRadiology(): bool
    {
        return $this->role === 'radiology';
    }

    public function isPatient(): bool
    {
        return $this->role === 'user';
    }
}
