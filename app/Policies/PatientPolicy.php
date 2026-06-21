<?php

namespace App\Policies;

use App\Models\Patient;
use App\Models\User;

class PatientPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'doctor', 'recieption', 'pharmacy', 'finance'], true);
    }

    public function view(User $user, Patient $patient): bool
    {
        if ($user->isAdmin() || $user->isReception() || $user->isFinance() || $user->isPharmacy()) {
            return true;
        }

        if ($user->isDoctor()) {
            return $patient->doctor_id === $user->id
                || $patient->doctor === $user->name
                || $patient->appointments()->where('doctor_id', $user->id)->exists();
        }

        if ($user->isPatient()) {
            return $patient->user_id === $user->id
                || ($patient->email && $patient->email === $user->email);
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isReception();
    }

    public function update(User $user, Patient $patient): bool
    {
        return $user->isAdmin() || $user->isReception();
    }

    public function delete(User $user, Patient $patient): bool
    {
        return $user->isAdmin() || $user->isReception();
    }
}
