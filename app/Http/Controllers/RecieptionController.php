<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\StorePatientRequest;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use App\Notifications\AppointmentCreated;
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

        Patient::create($data);

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

    public function CheckInAppointment($id)
    {
        $appointment = Appointment::findOrFail($id);
        $this->authorize('checkIn', $appointment);

        $appointment->update([
            'checked_in_at' => now(),
            'status' => 'confirmed',
        ]);

        return back()->with('success', 'Patient checked in successfully.');
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
