<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePrescriptionRequest;
use App\Models\Appointment;
use App\Models\DoctorConsultation;
use App\Models\Diagnosis;
use App\Models\LabRequest;
use App\Models\MedicalNote;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\TreatmentPlan;
use App\Services\HospitalBillingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class DoctorController extends Controller
{
    public function DoctorDashboard(HospitalBillingService $billingService)
    {
        $doctor = Auth::user();
        $stats = $billingService->doctorMonthlyStats($doctor);

        $assignedPatients = $stats['total_assigned_patients'];
        $todayAppointments = $stats['today_appointments'];

        $recentConsultations = DoctorConsultation::with('patient')
            ->where('doctor_id', $doctor->id)
            ->latest('visited_at')
            ->take(5)
            ->get();

        $recentNotifications = $doctor->unreadNotifications()->latest()->take(5)->get();
        $notificationCount = $doctor->unreadNotifications()->count();

        return view('backend.doctor.index', compact(
            'assignedPatients',
            'todayAppointments',
            'stats',
            'recentConsultations',
            'recentNotifications',
            'notificationCount'
        ));
    }

    public function DoctorLogout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function DoctorProfile()
    {
        $doctor = Auth::user();

        return view('backend.doctor.profile.doctor_profile', compact('doctor'));
    }

    public function UpdateDoctorProfile(Request $request)
    {
        $doctor = Auth::user();

        if ($request->file('photo')) {
            if ($doctor->photo && file_exists(public_path($doctor->photo))) {
                unlink(public_path($doctor->photo));
            }

            $image = $request->file('photo');
            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();
            $img = $manager->read($image);
            $img->resize(150, 150)->save(public_path('upload/doctor/profile/'.$name_gen));
            $doctor->photo = 'upload/doctor/profile/'.$name_gen;
        }

        $doctor->name = $request->name;
        $doctor->phone = $request->phone;
        $doctor->address = $request->address;
        $doctor->save();

        return redirect()->route('doctor.profile');
    }

    public function DoctorPatients()
    {
        $doctor = Auth::user();
        $patients = Patient::where('doctor_id', $doctor->id)
            ->orWhere('doctor', $doctor->name)
            ->orWhereHas('appointments', fn ($q) => $q->where('doctor_id', $doctor->id))
            ->distinct()
            ->get();

        return view('backend.doctor.patients.index', compact('patients'));
    }

    public function PatientsInfo($id)
    {
        $patient = Patient::with([
            'diagnoses.doctor',
            'medicalNotes.doctor',
            'treatmentPlans',
            'labRequests',
            'prescriptions.items',
            'appointments' => fn ($q) => $q->where('doctor_id', Auth::id())->latest(),
        ])->findOrFail($id);

        $this->authorize('view', $patient);

        $pharmacies = User::where('role', 'pharmacy')->get();
        $appointments = Appointment::where('patient_id', $patient->id)
            ->where('doctor_id', Auth::id())
            ->whereIn('status', ['pending', 'confirmed'])
            ->latest()
            ->get();

        $consultations = DoctorConsultation::where('doctor_id', Auth::id())
            ->where('patient_id', $patient->id)
            ->latest('visited_at')
            ->get();

        $alreadyConsultedToday = DoctorConsultation::where('doctor_id', Auth::id())
            ->where('patient_id', $patient->id)
            ->whereDate('visited_at', today())
            ->exists();

        return view('backend.doctor.patients.patients_info', compact(
            'patient', 'pharmacies', 'appointments', 'consultations', 'alreadyConsultedToday'
        ));
    }

    public function CompleteConsultation(Request $request, $patientId, HospitalBillingService $billingService)
    {
        $patient = Patient::findOrFail($patientId);
        $this->authorize('view', $patient);

        $request->validate([
            'appointment_id' => ['nullable', 'exists:appointments,id'],
        ]);

        $doctor = Auth::user();
        $appointment = null;

        if ($request->appointment_id) {
            $appointment = Appointment::where('doctor_id', $doctor->id)
                ->where('patient_id', $patient->id)
                ->findOrFail($request->appointment_id);
        }

        $billingService->recordPatientVisit($doctor, $patient, [], $appointment, $doctor);

        $fee = (float) ($doctor->consultation_fee ?? 0);

        return redirect()->route('doctor.dashboard')->with(
            'success',
            'Consultation completed. Fee $'.number_format($fee, 2).' added to your dashboard.'
        );
    }

    public function StorePrescription(StorePrescriptionRequest $request)
    {
        $patient = Patient::findOrFail($request->patient_id);
        $this->authorize('view', $patient);

        DB::transaction(function () use ($request) {
            $prescription = Prescription::create([
                'doctor_id' => Auth::id(),
                'patient_id' => $request->patient_id,
                'pharmacy_id' => $request->pharmacy_id,
                'appointment_id' => $request->appointment_id,
                'notes' => $request->notes,
                'status' => 'pending',
            ]);

            foreach ($request->medicine as $index => $medicine) {
                PrescriptionItem::create([
                    'prescription_id' => $prescription->id,
                    'medicine_id' => $request->medicine_id[$index] ?? null,
                    'medicine' => $medicine,
                    'desc' => $request->desc[$index] ?? null,
                    'dosage' => $request->dosage[$index] ?? null,
                    'frequency' => $request->frequency[$index] ?? null,
                    'quantity' => $request->quantity[$index] ?? 1,
                ]);
            }
        });

        return redirect()->route('patients.info', $request->patient_id)->with('success', 'Prescription created.');
    }

    public function StoreDiagnosis(Request $request, $patientId)
    {
        $patient = Patient::findOrFail($patientId);
        $this->authorize('view', $patient);

        $request->validate([
            'appointment_id' => ['nullable', 'exists:appointments,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'severity' => ['nullable', 'string', 'max:50'],
        ]);

        Diagnosis::create([
            'patient_id' => $patient->id,
            'doctor_id' => Auth::id(),
            'appointment_id' => $request->appointment_id,
            'title' => $request->title,
            'description' => $request->description,
            'severity' => $request->severity,
        ]);

        return back()->with('success', 'Diagnosis recorded.');
    }

    public function StoreMedicalNote(Request $request, $patientId)
    {
        $patient = Patient::findOrFail($patientId);
        $this->authorize('view', $patient);

        $request->validate([
            'appointment_id' => ['nullable', 'exists:appointments,id'],
            'note' => ['required', 'string'],
        ]);

        MedicalNote::create([
            'patient_id' => $patient->id,
            'doctor_id' => Auth::id(),
            'appointment_id' => $request->appointment_id,
            'note' => $request->note,
        ]);

        return back()->with('success', 'Medical note added.');
    }

    public function StoreTreatmentPlan(Request $request, $patientId)
    {
        $patient = Patient::findOrFail($patientId);
        $this->authorize('view', $patient);

        $request->validate([
            'appointment_id' => ['nullable', 'exists:appointments,id'],
            'diagnosis_id' => ['nullable', 'exists:diagnoses,id'],
            'title' => ['required', 'string', 'max:255'],
            'plan' => ['required', 'string'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        TreatmentPlan::create([
            'patient_id' => $patient->id,
            'doctor_id' => Auth::id(),
            'appointment_id' => $request->appointment_id,
            'diagnosis_id' => $request->diagnosis_id,
            'title' => $request->title,
            'plan' => $request->plan,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return back()->with('success', 'Treatment plan created.');
    }

    public function StoreLabRequest(Request $request, $patientId)
    {
        $patient = Patient::findOrFail($patientId);
        $this->authorize('view', $patient);

        $request->validate([
            'appointment_id' => ['nullable', 'exists:appointments,id'],
            'test_name' => ['required', 'string', 'max:255'],
            'instructions' => ['nullable', 'string'],
        ]);

        LabRequest::create([
            'patient_id' => $patient->id,
            'doctor_id' => Auth::id(),
            'appointment_id' => $request->appointment_id,
            'test_name' => $request->test_name,
            'instructions' => $request->instructions,
        ]);

        return back()->with('success', 'Laboratory test requested.');
    }

    public function Notifications()
    {
        $doctor = Auth::user();
        $notifications = $doctor->unreadNotifications()->latest()->get();

        return view('backend.doctor.notification.notification', compact('notifications'));
    }

    public function MarkNotificationRead($id, HospitalBillingService $billingService)
    {
        $notification = auth()->user()->unreadNotifications()->where('id', $id)->firstOrFail();
        $data = $notification->data;
        $redirectUrl = $billingService->resolveNotificationRedirect($data);

        $notification->markAsRead();

        $message = match ($data['type'] ?? '') {
            'patient_assigned' => 'Opening assigned patient record.',
            'patient_checked_in' => 'Opening checked-in patient record.',
            'appointment_created' => 'Opening your appointments list.',
            default => 'Notification marked as read.',
        };

        return redirect($redirectUrl)->with('success', $message);
    }

    public function OpenNotification($id, HospitalBillingService $billingService)
    {
        $notification = auth()->user()->notifications()->where('id', $id)->firstOrFail();
        $redirectUrl = $billingService->resolveNotificationRedirect($notification->data);

        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        return redirect($redirectUrl);
    }

    public function AcceptAppointment($id)
    {
        $appointment = Appointment::where('doctor_id', Auth::id())->findOrFail($id);
        $appointment->update(['status' => 'confirmed']);
        auth()->user()->unreadNotifications()
            ->where('data->appointment_id', $id)
            ->update(['read_at' => now()]);

        return response()->json(['success' => true, 'redirect' => route('all.doctor.appointment')]);
    }

    public function IgnoreAppointment($id)
    {
        $appointment = Appointment::where('doctor_id', Auth::id())->findOrFail($id);
        $appointment->update(['status' => 'canceled']);
        auth()->user()->unreadNotifications()
            ->where('data->appointment_id', $id)
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    public function appointmentCount()
    {
        $count = auth()->user()->unreadNotifications()->count();

        return response()->json(['count' => $count]);
    }

    public function AppointmentsData()
    {
        $appointments = Appointment::where('doctor_id', Auth::id())
            ->whereIn('status', ['confirmed', 'canceled'])
            ->with(['patient', 'creator'])
            ->orderBy('appointment_date', 'desc')
            ->get();

        return response()->json($appointments);
    }

    public function AllDoctorAppointment()
    {
        $appointments = Appointment::where('doctor_id', Auth::id())
            ->with(['patient', 'doctor', 'creator'])
            ->orderBy('appointment_date', 'desc')
            ->get();

        return view('backend.doctor.appointment.index', compact('appointments'));
    }

    public function AddDoctorAppointment()
    {
        return view('backend.doctor.appointment.add');
    }
}
