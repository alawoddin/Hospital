<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Bill;
use App\Models\DoctorConsultation;
use App\Models\LabRequest;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class HospitalBillingService
{
    public function createRegistrationBill(int $patientId, float $amount, ?string $notes = null): ?Bill
    {
        if ($amount <= 0) {
            return null;
        }

        return DB::transaction(function () use ($patientId, $amount, $notes) {
            $bill = Bill::create([
                'invoice_no' => $this->generateInvoiceNo(),
                'patient_id' => $patientId,
                'bill_date' => now()->toDateString(),
                'discount' => 0,
                'total_amount' => $amount,
                'due_amount' => $amount,
                'status' => 'pending',
                'notes' => $notes ?? 'Patient registration',
            ]);

            $bill->items()->create([
                'description' => 'Registration Fee',
                'quantity' => 1,
                'unit_price' => $amount,
                'total_price' => $amount,
            ]);

            return $bill;
        });
    }

    /** @return array<string, array{name: string, fee: float|int}> */
    public function laboratoryTests(): array
    {
        return config('hospital.laboratory_tests', []);
    }

    /**
     * Record consultation (doctor fee) + optional laboratory for one patient visit.
     *
     * @param  array<int, string>  $laboratoryTestKeys  Keys from config hospital.laboratory_tests
     */
    public function recordPatientVisit(
        User $doctor,
        Patient $patient,
        array $laboratoryTestKeys = [],
        ?Appointment $appointment = null,
        ?User $recordedBy = null
    ): array {
        if ($appointment) {
            $existing = DoctorConsultation::where('appointment_id', $appointment->id)
                ->where('doctor_id', $doctor->id)
                ->first();
            if ($existing) {
                return ['consultation' => $existing, 'bill' => null, 'lab_requests' => collect(), 'total' => 0];
            }
        }

        $consultationAmount = (float) ($doctor->consultation_fee ?? 0);
        $labCatalog = $this->laboratoryTests();
        $selectedLabs = collect($laboratoryTestKeys)
            ->filter(fn ($key) => isset($labCatalog[$key]))
            ->map(fn ($key) => array_merge(['key' => $key], $labCatalog[$key]));

        return DB::transaction(function () use ($doctor, $patient, $consultationAmount, $selectedLabs, $appointment, $recordedBy) {
            $consultation = DoctorConsultation::create([
                'doctor_id' => $doctor->id,
                'patient_id' => $patient->id,
                'appointment_id' => $appointment?->id,
                'consultation_fee' => $consultationAmount,
                'visited_at' => now(),
                'recorded_by' => $recordedBy?->id,
            ]);

            if ($appointment) {
                $appointment->update([
                    'checked_in_at' => $appointment->checked_in_at ?? now(),
                    'checked_in_by' => $recordedBy?->id,
                    'consultation_fee' => $consultationAmount,
                    'status' => 'confirmed',
                ]);
            }

            $lineItems = [[
                'description' => 'Consultation — Dr. '.$doctor->name.' ($'.number_format($consultationAmount, 2).')',
                'quantity' => 1,
                'unit_price' => $consultationAmount,
                'total_price' => $consultationAmount,
            ]];

            $labRequests = collect();

            foreach ($selectedLabs as $lab) {
                $fee = (float) $lab['fee'];

                $labRequest = LabRequest::create([
                    'patient_id' => $patient->id,
                    'doctor_id' => $doctor->id,
                    'appointment_id' => $appointment?->id,
                    'fee_amount' => $fee,
                    'test_name' => $lab['name'],
                    'status' => 'pending',
                ]);

                $labRequests->push($labRequest);

                $lineItems[] = [
                    'description' => 'Laboratory: '.$lab['name'],
                    'quantity' => 1,
                    'unit_price' => $fee,
                    'total_price' => $fee,
                ];
            }

            $total = collect($lineItems)->sum('total_price');

            $bill = null;
            if ($total > 0) {
                $bill = Bill::create([
                    'invoice_no' => $this->generateInvoiceNo(),
                    'patient_id' => $patient->id,
                    'appointment_id' => $appointment?->id,
                    'bill_date' => now()->toDateString(),
                    'discount' => 0,
                    'total_amount' => $total,
                    'due_amount' => $total,
                    'status' => 'pending',
                    'notes' => 'Visit charges — Dr. '.$doctor->name,
                ]);

                foreach ($lineItems as $item) {
                    $bill->items()->create($item);
                }
            }

            return [
                'consultation' => $consultation,
                'bill' => $bill,
                'lab_requests' => $labRequests,
                'total' => $total,
            ];
        });
    }

    public function recordConsultation(
        User $doctor,
        Patient $patient,
        ?Appointment $appointment = null,
        ?User $recordedBy = null
    ): DoctorConsultation {
        $result = $this->recordPatientVisit($doctor, $patient, [], $appointment, $recordedBy);

        return $result['consultation'];
    }

    public function doctorMonthlyStats(User $doctor): array
    {
        $doctor->refresh();

        $monthStart = now()->copy()->startOfMonth();
        $monthEnd = now()->copy()->endOfMonth();
        $yearStart = now()->copy()->startOfYear();
        $yearEnd = now()->copy()->endOfYear();

        $monthConsultations = DoctorConsultation::where('doctor_id', $doctor->id)
            ->whereBetween('visited_at', [$monthStart, $monthEnd]);

        $yearConsultations = DoctorConsultation::where('doctor_id', $doctor->id)
            ->whereBetween('visited_at', [$yearStart, $yearEnd]);

        $monthPatients = (clone $monthConsultations)->count();
        $yearPatients = (clone $yearConsultations)->count();
        $monthEarnings = (float) (clone $monthConsultations)->sum('consultation_fee');
        $yearEarnings = (float) (clone $yearConsultations)->sum('consultation_fee');

        $monthAppointments = Appointment::where('doctor_id', $doctor->id)
            ->whereBetween('appointment_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->count();

        $yearAppointments = Appointment::where('doctor_id', $doctor->id)
            ->whereBetween('appointment_date', [$yearStart->toDateString(), $yearEnd->toDateString()])
            ->count();

        $todayAppointments = Appointment::where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', today())
            ->count();

        $todayConsultations = DoctorConsultation::where('doctor_id', $doctor->id)
            ->whereDate('visited_at', today())
            ->count();

        $monthLabs = LabRequest::where('doctor_id', $doctor->id)
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->count();

        $totalAssignedPatients = Patient::where(function ($query) use ($doctor) {
            $query->where('doctor_id', $doctor->id)->orWhere('doctor', $doctor->name);
        })->count();

        return [
            'month_patients' => $monthPatients,
            'year_patients' => $yearPatients,
            'month_earnings' => $monthEarnings,
            'year_earnings' => $yearEarnings,
            'consultation_fee' => (float) ($doctor->consultation_fee ?? 0),
            'month_appointments' => $monthAppointments,
            'year_appointments' => $yearAppointments,
            'today_appointments' => $todayAppointments,
            'today_consultations' => $todayConsultations,
            'month_laboratory_tests' => $monthLabs,
            'total_assigned_patients' => $totalAssignedPatients,
        ];
    }

    public function resolveNotificationRedirect(array $data): string
    {
        $type = $data['type'] ?? (isset($data['appointment_id']) ? 'appointment_created' : 'unknown');

        return match ($type) {
            'patient_assigned' => isset($data['patient_id'])
                ? route('patients.info', $data['patient_id'])
                : route('doctor.patients'),
            'patient_checked_in' => isset($data['patient_id'])
                ? route('patients.info', $data['patient_id'])
                : route('all.doctor.appointment'),
            'appointment_created' => route('all.doctor.appointment'),
            default => route('doctor.dashboard'),
        };
    }

    private function generateInvoiceNo(): string
    {
        return 'INV-'.now()->format('Ymd').'-'.str_pad((Bill::max('id') ?? 0) + 1, 5, '0', STR_PAD_LEFT);
    }
}
