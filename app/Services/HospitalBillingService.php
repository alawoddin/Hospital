<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Bill;
use App\Models\BillItem;
use App\Models\DoctorConsultation;
use App\Models\LabRequest;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class HospitalBillingService
{
    public function laboratoryTests(): array
    {
        return config('hospital.laboratory_tests', []);
    }

    public function radiologyScans(): array
    {
        return config('hospital.radiology_scans', []);
    }

    /** Get or create the single master invoice for a patient visit. */
    public function getMasterBill(Patient $patient, ?Appointment $appointment = null): Bill
    {
        if ($appointment) {
            $bill = Bill::where('appointment_id', $appointment->id)
                ->where('is_master', true)
                ->whereNotIn('status', ['paid', 'canceled'])
                ->first();

            if ($bill) {
                return $bill;
            }

            $openBill = Bill::where('patient_id', $patient->id)
                ->whereNull('appointment_id')
                ->where('is_master', true)
                ->whereNotIn('status', ['paid', 'canceled'])
                ->whereDate('created_at', today())
                ->first();

            if ($openBill) {
                $openBill->update(['appointment_id' => $appointment->id]);

                return $openBill->fresh();
            }

            return Bill::create([
                'invoice_no' => $this->generateInvoiceNo(),
                'patient_id' => $patient->id,
                'appointment_id' => $appointment->id,
                'bill_date' => now()->toDateString(),
                'discount' => 0,
                'total_amount' => 0,
                'due_amount' => 0,
                'status' => 'pending',
                'is_master' => true,
                'notes' => 'Master invoice — visit',
            ]);
        }

        $bill = Bill::where('patient_id', $patient->id)
            ->where('is_master', true)
            ->whereNotIn('status', ['paid', 'canceled'])
            ->whereDate('created_at', today())
            ->first();

        if ($bill) {
            return $bill;
        }

        return Bill::create([
            'invoice_no' => $this->generateInvoiceNo(),
            'patient_id' => $patient->id,
            'bill_date' => now()->toDateString(),
            'discount' => 0,
            'total_amount' => 0,
            'due_amount' => 0,
            'status' => 'pending',
            'is_master' => true,
            'notes' => 'Master invoice',
        ]);
    }

    public function addCharge(
        Bill $bill,
        string $category,
        string $description,
        float $amount,
        int $quantity = 1,
        ?string $referenceType = null,
        ?int $referenceId = null
    ): BillItem {
        if ($referenceType && $referenceId) {
            $exists = BillItem::where('bill_id', $bill->id)
                ->where('reference_type', $referenceType)
                ->where('reference_id', $referenceId)
                ->exists();
            if ($exists) {
                return BillItem::where('bill_id', $bill->id)
                    ->where('reference_type', $referenceType)
                    ->where('reference_id', $referenceId)
                    ->first();
            }
        }

        $total = round($amount * $quantity, 2);

        $item = $bill->items()->create([
            'category' => $category,
            'description' => $description,
            'quantity' => $quantity,
            'unit_price' => $amount,
            'total_price' => $total,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
        ]);

        $this->recalculateBill($bill);

        return $item;
    }

    public function recalculateBill(Bill $bill): void
    {
        $bill->refresh();
        $total = (float) $bill->items()->sum('total_price');
        $paid = $bill->paidAmount();
        $due = max(0, $total - $paid);

        $status = match (true) {
            $due <= 0 && $total > 0 => 'paid',
            $paid > 0 => 'partially_paid',
            default => 'pending',
        };

        $bill->update([
            'total_amount' => $total,
            'due_amount' => $due,
            'status' => $status,
        ]);
    }

    public function financialSummary(Bill $bill): array
    {
        $items = $bill->items()->get();
        $categories = [
            'registration' => ['label' => 'Registration', 'total' => 0, 'items' => []],
            'consultation' => ['label' => 'Doctor', 'total' => 0, 'items' => []],
            'laboratory' => ['label' => 'Laboratory', 'total' => 0, 'items' => []],
            'pharmacy' => ['label' => 'Pharmacy', 'total' => 0, 'items' => []],
            'radiology' => ['label' => 'Radiology', 'total' => 0, 'items' => []],
            'other' => ['label' => 'Other', 'total' => 0, 'items' => []],
        ];

        foreach ($items as $item) {
            $cat = $item->category ?? 'other';
            if (! isset($categories[$cat])) {
                $cat = 'other';
            }
            $categories[$cat]['total'] += (float) $item->total_price;
            $categories[$cat]['items'][] = $item;
        }

        return [
            'bill' => $bill,
            'categories' => $categories,
            'grand_total' => (float) $bill->total_amount,
            'paid' => $bill->paidAmount(),
            'due' => (float) $bill->due_amount,
        ];
    }

    public function createRegistrationBill(int $patientId, float $amount, ?string $notes = null): ?Bill
    {
        if ($amount <= 0) {
            return null;
        }

        $patient = Patient::findOrFail($patientId);
        $bill = $this->getMasterBill($patient);

        $this->addCharge($bill, 'registration', 'Registration Fee', $amount, 1, 'registration', $patientId);

        if ($notes) {
            $bill->update(['notes' => $notes]);
        }

        return $bill->fresh();
    }

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
                $bill = $this->getMasterBill($patient, $appointment);

                return ['consultation' => $existing, 'bill' => $bill, 'lab_requests' => collect(), 'total' => (float) $bill->total_amount];
            }
        }

        $consultationAmount = (float) ($doctor->consultation_fee ?? 0);
        $bill = $this->getMasterBill($patient, $appointment);

        return DB::transaction(function () use ($doctor, $patient, $consultationAmount, $laboratoryTestKeys, $appointment, $recordedBy, $bill) {
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

            if ($consultationAmount > 0) {
                $this->addCharge(
                    $bill,
                    'consultation',
                    'Consultation — Dr. '.$doctor->name,
                    $consultationAmount,
                    1,
                    'consultation',
                    $consultation->id
                );
            }

            $labRequests = $this->createLabRequestsFromKeys($doctor, $patient, $laboratoryTestKeys, $appointment, $bill);

            return [
                'consultation' => $consultation,
                'bill' => $bill->fresh(),
                'lab_requests' => $labRequests,
                'total' => (float) $bill->fresh()->total_amount,
            ];
        });
    }

    public function createLabRequest(
        User $doctor,
        Patient $patient,
        string $testKey,
        ?Appointment $appointment = null,
        ?string $instructions = null
    ): ?LabRequest {
        $catalog = $this->laboratoryTests();
        if (! isset($catalog[$testKey])) {
            return null;
        }

        $test = $catalog[$testKey];
        $bill = $this->getMasterBill($patient, $appointment);

        $labRequest = LabRequest::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_id' => $appointment?->id,
            'test_name' => $test['name'],
            'fee_amount' => $test['fee'],
            'instructions' => $instructions,
            'status' => 'pending',
        ]);

        return $labRequest;
    }

    public function completeLabRequest(LabRequest $labRequest, User $completedBy, ?string $result = null, ?string $reportFile = null): void
    {
        DB::transaction(function () use ($labRequest, $completedBy, $result, $reportFile) {
            $patient = $labRequest->patient;
            $appointment = $labRequest->appointment;
            $bill = $this->getMasterBill($patient, $appointment);

            $fee = (float) ($labRequest->fee_amount ?? 0);
            if ($fee <= 0) {
                $fee = $this->findLabFeeByName($labRequest->test_name);
            }

            $item = $this->addCharge(
                $bill,
                'laboratory',
                'Laboratory: '.$labRequest->test_name,
                $fee,
                1,
                'lab_request',
                $labRequest->id
            );

            $labRequest->update([
                'status' => 'completed',
                'result' => $result,
                'report_file' => $reportFile,
                'completed_at' => now(),
                'completed_by' => $completedBy->id,
                'fee_amount' => $fee,
                'bill_item_id' => $item->id,
            ]);
        });
    }

    public function completeRadiologyRequest($radiologyRequest, User $completedBy, ?string $result = null, ?string $reportFile = null): void
    {
        DB::transaction(function () use ($radiologyRequest, $completedBy, $result, $reportFile) {
            $patient = $radiologyRequest->patient;
            $appointment = $radiologyRequest->appointment;
            $bill = $this->getMasterBill($patient, $appointment);
            $fee = (float) $radiologyRequest->fee_amount;

            $item = $this->addCharge(
                $bill,
                'radiology',
                'Radiology: '.$radiologyRequest->scan_type,
                $fee,
                1,
                'radiology_request',
                $radiologyRequest->id
            );

            $radiologyRequest->update([
                'status' => 'completed',
                'result' => $result,
                'report_file' => $reportFile,
                'completed_at' => now(),
                'completed_by' => $completedBy->id,
                'bill_item_id' => $item->id,
            ]);
        });
    }

    public function addPharmacyCharges($prescription, float $totalAmount): BillItem
    {
        $patient = $prescription->patient;
        $appointment = $prescription->appointment;
        $bill = $this->getMasterBill($patient, $appointment);

        return $this->addCharge(
            $bill,
            'pharmacy',
            'Pharmacy — Prescription #'.$prescription->id,
            $totalAmount,
            1,
            'prescription',
            $prescription->id
        );
    }

    public function markBillPaid(Bill $bill, User $receivedBy, ?string $method = 'cash'): Payment
    {
        return DB::transaction(function () use ($bill, $receivedBy, $method) {
            $due = (float) $bill->due_amount;
            if ($due <= 0) {
                $due = max(0, (float) $bill->total_amount - $bill->paidAmount());
            }

            $payment = Payment::create([
                'bill_id' => $bill->id,
                'amount' => $due,
                'payment_date' => now()->toDateString(),
                'payment_method' => $method,
                'received_by' => $receivedBy->id,
            ]);

            $bill->refreshPaymentStatus();

            return $payment;
        });
    }

    public function recordConsultation(User $doctor, Patient $patient, ?Appointment $appointment = null, ?User $recordedBy = null): DoctorConsultation
    {
        return $this->recordPatientVisit($doctor, $patient, [], $appointment, $recordedBy)['consultation'];
    }

    public function doctorMonthlyStats(User $doctor): array
    {
        $monthStart = now()->copy()->startOfMonth();
        $monthEnd = now()->copy()->endOfMonth();
        $yearStart = now()->copy()->startOfYear();
        $yearEnd = now()->copy()->endOfYear();

        $monthConsultations = DoctorConsultation::where('doctor_id', $doctor->id)
            ->whereBetween('visited_at', [$monthStart, $monthEnd]);

        $yearConsultations = DoctorConsultation::where('doctor_id', $doctor->id)
            ->whereBetween('visited_at', [$yearStart, $yearEnd]);

        $todayConsultations = DoctorConsultation::where('doctor_id', $doctor->id)->whereDate('visited_at', today());

        return [
            'month_patients' => (clone $monthConsultations)->count(),
            'year_patients' => (clone $yearConsultations)->count(),
            'today_patients' => (clone $todayConsultations)->count(),
            'month_earnings' => (float) (clone $monthConsultations)->sum('consultation_fee'),
            'year_earnings' => (float) (clone $yearConsultations)->sum('consultation_fee'),
            'consultation_fee' => (float) ($doctor->consultation_fee ?? 0),
            'month_appointments' => Appointment::where('doctor_id', $doctor->id)
                ->whereBetween('appointment_date', [$monthStart->toDateString(), $monthEnd->toDateString()])->count(),
            'year_appointments' => Appointment::where('doctor_id', $doctor->id)
                ->whereBetween('appointment_date', [$yearStart->toDateString(), $yearEnd->toDateString()])->count(),
            'today_appointments' => Appointment::where('doctor_id', $doctor->id)->whereDate('appointment_date', today())->count(),
            'today_consultations' => (clone $todayConsultations)->count(),
            'pending_patients' => Patient::where('doctor_id', $doctor->id)
                ->whereDoesntHave('appointments', fn ($q) => $q->whereDate('appointment_date', today())->whereNotNull('checked_in_at'))->count(),
            'month_laboratory_tests' => LabRequest::where('doctor_id', $doctor->id)->whereBetween('created_at', [$monthStart, $monthEnd])->count(),
            'total_assigned_patients' => Patient::where(fn ($q) => $q->where('doctor_id', $doctor->id)->orWhere('doctor', $doctor->name))->count(),
        ];
    }

    public function financeStats(string $period = 'today'): array
    {
        [$start, $end] = $this->periodRange($period);

        $payments = Payment::whereBetween('payment_date', [$start, $end]);
        $expenses = \App\Models\Expense::whereBetween('expense_date', [$start, $end])->where('status', 'paid');

        $income = (float) (clone $payments)->sum('amount');
        $expenseTotal = (float) (clone $expenses)->sum('amount');

        $incomeByCategory = BillItem::whereHas('bill.payments', fn ($q) => $q->whereBetween('payment_date', [$start, $end]))
            ->selectRaw('category, SUM(total_price) as total')
            ->groupBy('category')
            ->pluck('total', 'category');

        return [
            'income' => $income,
            'expenses' => $expenseTotal,
            'profit' => $income - $expenseTotal,
            'income_by_category' => $incomeByCategory,
        ];
    }

    public function resolveNotificationRedirect(array $data): string
    {
        return match ($data['type'] ?? '') {
            'patient_assigned', 'patient_checked_in' => isset($data['patient_id'])
                ? route('patients.info', $data['patient_id']) : route('doctor.patients'),
            'appointment_created' => route('all.doctor.appointment'),
            'prescription_sent' => route('pharmacy.patients'),
            'prescription_completed' => route('pharmacy.patients'),
            'lab_requested' => route('laboratory.requests'),
            'lab_completed' => isset($data['patient_id']) ? route('recieption.patient.summary', $data['patient_id']) : route('laboratory.requests'),
            'radiology_requested' => route('radiology.requests'),
            'invoice_generated', 'payment_received' => route('recieption.payments'),
            'salary_paid' => route('finance.salaries'),
            default => route('doctor.dashboard'),
        };
    }

    private function createLabRequestsFromKeys(User $doctor, Patient $patient, array $keys, ?Appointment $appointment, Bill $bill): Collection
    {
        $catalog = $this->laboratoryTests();
        $requests = collect();

        foreach ($keys as $key) {
            if (! isset($catalog[$key])) {
                continue;
            }
            $test = $catalog[$key];
            $requests->push(LabRequest::create([
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'appointment_id' => $appointment?->id,
                'test_name' => $test['name'],
                'fee_amount' => $test['fee'],
                'status' => 'pending',
            ]));
        }

        return $requests;
    }

    private function findLabFeeByName(string $name): float
    {
        foreach ($this->laboratoryTests() as $test) {
            if ($test['name'] === $name) {
                return (float) $test['fee'];
            }
        }

        return 0;
    }

    private function periodRange(string $period): array
    {
        return match ($period) {
            'month' => [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()],
            'year' => [now()->startOfYear()->toDateString(), now()->endOfYear()->toDateString()],
            default => [today()->toDateString(), today()->toDateString()],
        };
    }

    private function generateInvoiceNo(): string
    {
        return 'INV-'.now()->format('Ymd').'-'.str_pad((Bill::max('id') ?? 0) + 1, 5, '0', STR_PAD_LEFT);
    }
}
