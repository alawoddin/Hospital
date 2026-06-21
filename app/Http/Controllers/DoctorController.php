<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePrescriptionRequest;
use App\Models\Appointment;
use App\Models\Diagnosis;
use App\Models\LabRequest;
use App\Models\MedicalNote;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\TreatmentPlan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class DoctorController extends Controller
{
    public function DoctorDashboard()
    {
        $doctor = Auth::user();
        $todayAppointments = Appointment::where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', today())
            ->where('status', 'confirmed')
            ->count();
        $assignedPatients = Patient::where('doctor_id', $doctor->id)
            ->orWhere('doctor', $doctor->name)
            ->count();

        return view('backend.doctor.index', compact('todayAppointments', 'assignedPatients'));
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
            ->where('status', 'confirmed')
            ->latest()
            ->get();

        return view('backend.doctor.patients.patients_info', compact('patient', 'pharmacies', 'appointments'));
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
        $notifications = $doctor->unreadNotifications;

        return view('backend.doctor.notification.notification', compact('notifications'));
    }

    public function AcceptAppointment($id)
    {
        $appointment = Appointment::where('doctor_id', Auth::id())->findOrFail($id);
        $appointment->update(['status' => 'confirmed']);
        auth()->user()->unreadNotifications()->where('data->appointment_id', $id)->delete();

        return response()->json(['success' => true]);
    }

    public function IgnoreAppointment($id)
    {
        $appointment = Appointment::where('doctor_id', Auth::id())->findOrFail($id);
        $appointment->update(['status' => 'canceled']);
        auth()->user()->unreadNotifications()->where('data->appointment_id', $id)->delete();

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
