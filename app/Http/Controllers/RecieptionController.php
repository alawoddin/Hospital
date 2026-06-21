<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\StorePatientRequest;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use App\Notifications\AppointmentCreated;
use App\Notifications\PatientAssignedToDoctor;
use App\Notifications\PatientCheckedIn;
use App\Services\HospitalBillingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class RecieptionController extends Controller
{
    public function RecieptionDashboard()
    {
        $newRegistrations = Patient::whereDate('created_at', today())->count();
        $todayAppointments = Appointment::whereDate('appointment_date', today())->count();

        return view('backend.recieption.index', compact('newRegistrations', 'todayAppointments'));
    }

    public function RecieptionLogout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function RecieptionProfile()
    {
        $recieption = Auth::user();

        return view('backend.recieption.profile.recieption_profile', compact('recieption'));
    }

    public function UpdateRecieptionProfile(Request $request)
    {
        $recieption = Auth::user();

        if ($request->file('photo')) {
            if ($recieption->photo && file_exists(public_path($recieption->photo))) {
                unlink(public_path($recieption->photo));
            }

            $image = $request->file('photo');
            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();
            $img = $manager->read($image);
            $img->resize(150, 150)->save(public_path('upload/recieption/profile/'.$name_gen));
            $recieption->photo = 'upload/recieption/profile/'.$name_gen;
        }

        $recieption->name = $request->name;
        $recieption->phone = $request->phone;
        $recieption->address = $request->address;
        $recieption->save();

        return redirect()->route('recieption.profile');
    }

    public function AllPatients()
    {
        $patients = Patient::with('assignedDoctor')->latest()->get();

        return view('backend.recieption.patients.index', compact('patients'));
    }

    public function AddPatients()
    {
        $doctors = User::where('role', 'doctor')->get();

        return view('backend.recieption.patients.add', compact('doctors'));
    }

    public function StorePatients(StorePatientRequest $request)
    {
        $data = $this->preparePatientData($request);

        if ($request->file('photo')) {
            $image = $request->file('photo');
            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();
            $img = $manager->read($image);
            $img->resize(150, 150)->save(public_path('upload/recieption/patients/'.$name_gen));
            $data['photo'] = 'upload/recieption/patients/'.$name_gen;
        }

        $patient = Patient::create($data);

        app(HospitalBillingService::class)->createRegistrationBill(
            $patient->id,
            (float) ($request->registration_fee ?? 0),
            'Patient registration'
        );

        if ($patient->doctor_id) {
            $doctor = User::find($patient->doctor_id);
            $doctor?->notify(new PatientAssignedToDoctor($patient));
        }

        return redirect()->route('all.patients')->with('success', 'Patient registered successfully.');
    }

    public function EditPatients($id)
    {
        $patients = Patient::findOrFail($id);
        $doctors = User::where('role', 'doctor')->get();

        return view('backend.recieption.patients.edit', compact('patients', 'doctors'));
    }

    public function UpdatePatients(StorePatientRequest $request)
    {
        $patient = Patient::findOrFail($request->id);
        $data = $this->preparePatientData($request);

        if ($request->file('photo')) {
            if ($patient->photo && file_exists(public_path($patient->photo))) {
                unlink(public_path($patient->photo));
            }

            $image = $request->file('photo');
            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();
            $img = $manager->read($image);
            $img->resize(150, 150)->save(public_path('upload/recieption/patients/'.$name_gen));
            $data['photo'] = 'upload/recieption/patients/'.$name_gen;
        }

        $patient->update($data);

        return redirect()->route('all.patients')->with('success', 'Patient updated successfully.');
    }

    public function DeletePatients($id)
    {
        $patient = Patient::findOrFail($id);

        if ($patient->photo && file_exists(public_path($patient->photo))) {
            unlink(public_path($patient->photo));
        }

        $patient->delete();

        return redirect()->route('all.patients');
    }

    public function AllAppointment()
    {
        $appointments = Appointment::with(['patient', 'doctor'])->latest()->get();

        return view('backend.recieption.appointment.index', compact('appointments'));
    }

    public function AddAppointment()
    {
        $patients = Patient::all();
        $doctors = User::where('role', 'doctor')->get();

        return view('backend.recieption.appointment.add', compact('patients', 'doctors'));
    }

    public function StoreAppointment(StoreAppointmentRequest $request)
    {
        $tokenNumber = $request->token_number ?? $this->nextTokenNumber($request->doctor_id, $request->appointment_date);

        $appointment = Appointment::create([
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'token_number' => $tokenNumber,
            'status' => $request->status ?? 'pending',
            'created_by' => auth()->id(),
            'description' => $request->description,
        ]);

        $doctor = User::find($request->doctor_id);
        $doctor?->notify(new AppointmentCreated($appointment));

        return redirect()->route('all.appointment')->with('success', 'Appointment created and doctor notified.');
    }

    public function EditAppointment($id)
    {
        $appointments = Appointment::findOrFail($id);
        $patients = Patient::all();
        $doctors = User::where('role', 'doctor')->get();

        return view('backend.recieption.appointment.edit', compact('appointments', 'patients', 'doctors'));
    }

    public function UpdateAppointment(StoreAppointmentRequest $request)
    {
        $appointment = Appointment::findOrFail($request->id);

        $appointment->update([
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'token_number' => $request->token_number ?? $appointment->token_number,
            'status' => $request->status ?? $appointment->status,
            'created_by' => auth()->id(),
            'description' => $request->description,
        ]);

        DB::table('notifications')->where('data->appointment_id', $appointment->id)->delete();

        $doctor = User::find($request->doctor_id);
        $doctor?->notify(new AppointmentCreated($appointment));

        return redirect()->route('all.appointment')->with('success', 'Appointment updated.');
    }

    public function DeleteAppointment($id)
    {
        Appointment::findOrFail($id)->delete();

        return redirect()->route('all.appointment');
    }

    public function CheckInForm($id, HospitalBillingService $billingService)
    {
        $appointment = Appointment::with(['patient', 'doctor'])->findOrFail($id);
        $this->authorize('checkIn', $appointment);

        $laboratoryTests = $billingService->laboratoryTests();

        return view('backend.recieption.appointment.checkin', compact(
            'appointment', 'laboratoryTests'
        ));
    }

    public function CheckInAppointment(Request $request, $id, HospitalBillingService $billingService)
    {
        $appointment = Appointment::with(['patient', 'doctor'])->findOrFail($id);
        $this->authorize('checkIn', $appointment);

        $request->validate([
            'laboratory_tests' => ['nullable', 'array'],
            'laboratory_tests.*' => ['string'],
        ]);

        $doctor = $appointment->doctor;
        if (! $doctor) {
            return back()->with('error', 'No doctor assigned to this appointment.');
        }

        $result = $billingService->recordPatientVisit(
            $doctor,
            $appointment->patient,
            $request->laboratory_tests ?? [],
            $appointment,
            Auth::user()
        );

        $doctor->notify(new PatientCheckedIn($appointment->fresh(['patient'])));

        $fee = (float) ($doctor->consultation_fee ?? 0);
        $labCount = $result['lab_requests']->count();
        $msg = 'Patient checked in. Dr. '.$doctor->name.' fee: $'.number_format($fee, 2);
        if ($labCount > 0) {
            $msg .= ' + '.$labCount.' laboratory test(s). Total bill: $'.number_format($result['total'], 2);
        }

        return redirect()->route('all.appointment')->with('success', $msg);
    }

    public function PrintAppointmentSlip($id)
    {
        $appointment = Appointment::with(['patient', 'doctor'])->findOrFail($id);
        $this->authorize('view', $appointment);

        return view('backend.recieption.appointment.slip', compact('appointment'));
    }

    public function DoctorSchedules()
    {
        $doctors = User::where('role', 'doctor')->with(['doctorAppointments' => function ($q) {
            $q->whereDate('appointment_date', '>=', today())->orderBy('appointment_date');
        }])->get();

        return view('backend.recieption.schedules.index', compact('doctors'));
    }

    private function preparePatientData(StorePatientRequest $request): array
    {
        $gender = strtolower($request->gender);
        $doctorId = $request->doctor_id;
        $doctorName = $request->doctor;

        if ($doctorId) {
            $doctorName = User::find($doctorId)?->name ?? $doctorName;
        } elseif ($doctorName) {
            $doctorId = User::where('role', 'doctor')->where('name', $doctorName)->value('id');
        }

        $userId = null;
        if ($request->email) {
            $userId = User::where('email', $request->email)->where('role', 'user')->value('id');
        }

        return [
            'name' => $request->name,
            'father_name' => $request->father_name,
            'last_name' => $request->last_name,
            'age' => $request->age,
            'gender' => $gender,
            'doctor' => $doctorName,
            'doctor_id' => $doctorId,
            'user_id' => $userId,
            'department_id' => $request->department_id,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'national_id' => $request->national_id,
            'registration_fee' => (float) ($request->registration_fee ?? 0),
        ];
    }

    private function nextTokenNumber(int $doctorId, string $date): int
    {
        $lastToken = Appointment::where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $date)
            ->max('token_number');

        return ($lastToken ?? 0) + 1;
    }
}
