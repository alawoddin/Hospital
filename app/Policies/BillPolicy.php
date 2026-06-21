<?php

namespace App\Policies;

use App\Models\Bill;
use App\Models\User;

class BillPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'finance', 'user'], true);
    }

    public function view(User $user, Bill $bill): bool
    {
        if ($user->isAdmin() || $user->isFinance()) {
            return true;
        }

        if ($user->isPatient()) {
            $patientIds = $user->patientProfile()->pluck('id')
                ->merge(
                    \App\Models\Patient::where('email', $user->email)->pluck('id')
                );

            return $patientIds->contains($bill->patient_id);
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isFinance();
    }

    public function update(User $user, Bill $bill): bool
    {
        return $user->isAdmin() || $user->isFinance();
    }

    public function delete(User $user, Bill $bill): bool
    {
        return $user->isAdmin() || $user->isFinance();
    }
}
