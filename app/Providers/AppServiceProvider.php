<?php

namespace App\Providers;

use App\Models\Appointment;
use App\Models\Bill;
use App\Models\Patient;
use App\Models\Prescription;
use App\Policies\AppointmentPolicy;
use App\Policies\BillPolicy;
use App\Policies\PatientPolicy;
use App\Policies\PrescriptionPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        Patient::class => PatientPolicy::class,
        Appointment::class => AppointmentPolicy::class,
        Bill::class => BillPolicy::class,
        Prescription::class => PrescriptionPolicy::class,
    ];

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }
}
