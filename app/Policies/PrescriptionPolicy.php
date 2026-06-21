<?php

namespace App\Policies;

use App\Models\Prescription;
use App\Models\User;

class PrescriptionPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'doctor', 'pharmacy', 'user'], true);
    }

    public function view(User $user, Prescription $prescription): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isDoctor()) {
            return $prescription->doctor_id === $user->id;
        }

        if ($user->isPharmacy()) {
            return $prescription->pharmacy_id === $user->id;
        }

        if ($user->isPatient()) {
            $patientIds = $user->patientProfile()->pluck('id')
                ->merge(
                    \App\Models\Patient::where('email', $user->email)->pluck('id')
                );

            return $patientIds->contains($prescription->patient_id);
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isDoctor();
    }

    public function dispense(User $user, Prescription $prescription): bool
    {
        return $user->isPharmacy() && $prescription->pharmacy_id === $user->id;
    }
}
