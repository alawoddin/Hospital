<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;

class AppointmentPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'doctor', 'recieption', 'finance', 'user'], true);
    }

    public function view(User $user, Appointment $appointment): bool
    {
        if ($user->isAdmin() || $user->isReception() || $user->isFinance()) {
            return true;
        }

        if ($user->isDoctor()) {
            return $appointment->doctor_id === $user->id;
        }

        if ($user->isPatient()) {
            $patient = $user->patientProfile()->first()
                ?? Patient::where('email', $user->email)->first();

            return $patient && $appointment->patient_id === $patient->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isReception();
    }

    public function update(User $user, Appointment $appointment): bool
    {
        return $user->isAdmin() || $user->isReception();
    }

    public function delete(User $user, Appointment $appointment): bool
    {
        return $user->isAdmin() || $user->isReception();
    }

    public function checkIn(User $user, Appointment $appointment): bool
    {
        return $user->isAdmin() || $user->isReception();
    }
}
