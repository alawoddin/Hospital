<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\Appointment;
use App\Models\User;
use Faker\Core\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;


class DoctorController extends Controller
{
    public function DoctorDashboard() {
        $totalPatients = Patient::count();
        return view('backend.doctor.index', compact('totalPatients'));
    }

    //Logout Route
    public function DoctorLogout(Request $request) {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
    // End Logout
    // Doctor Profile
    public function DoctorProfile(){
        $doctor = Auth::user();
        return view('backend.doctor.profile.doctor_profile', compact('doctor'));
    }
    // End Doctor Profile

    // Update Profile
    public function UpdateDoctorProfile(Request $request) {

        $doctor = Auth::user();

       if ($request->file('photo')) {

            if ($doctor->photo && file_exists(public_path($doctor->photo))) {
                unlink(public_path($doctor->photo));
            }

            $image = $request->file('photo');
            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();
            $img = $manager->read($image);
            $img->resize(150,150)->save(public_path('upload/doctor/profile/'.$name_gen));
            $save_url = 'upload/doctor/profile/'.$name_gen;

            $doctor->photo = $save_url;
        }

        $doctor->name = $request->name;
        $doctor->phone = $request->phone;
        $doctor->address = $request->address;
        $doctor->role = $request->role;

        $doctor->save();
        return redirect()->route('doctor.profile');
    }
    // End Update Profile

    // Doctor Patients
    public function DoctorPatients(){
        $doctorName = Auth::user()->name;
        $patients = Patient::where('doctor', $doctorName)->get();
        return view('backend.doctor.patients.index', compact('patients'));
    }
    // End Doctor Patients

    // Patients Info 
    public function PatientsInfo($id) {
        $patients = Patient::findOrFail($id);
        $pharmacies = User::where('role', 'pharmacy')->get();
        return view('backend.doctor.patients.patients_info', compact('patients','pharmacies'));
    }
    // Store Patients Prescription
    public function StorePrescription(Request $request)
{
    $request->validate([
        'doctor_id'=> 'required|exists:users,id',
        'patient_id'=> 'required|exists:patients,id',
        'pharmacy_id'=> 'required|exists:users,id',
        'medicine.*'=> 'required|string',
        'desc.*'=> 'nullable|string',
    ]);

    DB::transaction(function () use ($request) {
        $prescription = Prescription::create([
            'doctor_id'=> $request->doctor_id,
            'patient_id'=> $request->patient_id,
            'pharmacy_id'=> $request->pharmacy_id,
        ]);

        foreach ($request->medicine as $index => $medicine) {
            PrescriptionItem::create([
                'prescription_id'=> $prescription->id,
                'medicine'=> $medicine,
                'desc'=> $request->desc[$index] ?? null,
            ]);
        }
    });

    return redirect()->route('doctor.patients');
}
    // End Store Prescription
    
    // // Appointment Notification
    // public function Notifications(){
    //     $doctor = Auth::user();
    //     $notifications = $doctor->notifications;
    //     return view('backend.doctor.notification.notification', compact('notifications'));
    // }
    // // End Appointment Notification

    // // Accept Appointment
    // public function AcceptAppointment($id){
    //     $appointment = Appointment::findOrFail($id);
    //     $appointment->update(['status' => 'confirmed']);

    //     // Optional: mark notification as read
    //     auth()->user()->unreadNotifications()->where('data->appointment_id', $id)->update(['read_at' => now()]);

    //     return redirect()->back()->with('success', 'Appointment confirmed!');
    // }
    // // End Accept Appointment

    // // Ignore Appointment
    // public function IgnoreAppointment($id){
    //     $appointment = Appointment::findOrFail($id);
    //     $appointment->update(['status' => 'canceled']);

    //     // Optional: mark notification as read
    //     auth()->user()->unreadNotifications()->where('data->appointment_id', $id)->update(['read_at' => now()]);

    //     return redirect()->back()->with('success', 'Appointment ignored!');
    // }
    // // End Ignore Appointment

    // public function appointmentCount(){
    //     $doctor = Auth::user();
    //     $count = $doctor->unreadNotifications()->count();
    //     return response()->json(['count' => $count]);
    // }

    // Appointment Notification
    public function Notifications(){
        $doctor = Auth::user();
        $notifications = $doctor->unreadNotifications;
        return view('backend.doctor.notification.notification', compact('notifications'));
    }
    // End Appointment Notification

    // Accept Appointment
    public function AcceptAppointment($id){
        $appointment = Appointment::findOrFail($id);
        $appointment->update(['status' => 'confirmed']);

        auth()->user()->unreadNotifications()->where('data->appointment_id', $id)->delete();

        return response()->json(['success' => true]);
    }
    // End Accept Appointment

    //  Ignore Appointment
    public function IgnoreAppointment($id){
        $appointment = Appointment::findOrFail($id);
        $appointment->update(['status' => 'canceled']);

        auth()->user()->unreadNotifications()->where('data->appointment_id', $id)->delete();

        return response()->json(['success' => true]);
    }
    // End Ignore Appointments

    // Number of Appointments
    public function appointmentCount(){
        $doctor = Auth::user();
        $count = $doctor->unreadNotifications()->count();
        return response()->json(['count' => $count]);
    }
    // End Number of Appointments

    // Appointments Data
    public function AppointmentsData(){
        $doctor = Auth::user();
        $appointments = Appointment::where('doctor_id', $doctor->id)
                                ->whereIn('status', ['confirmed', 'canceled'])
                                ->with(['patient', 'creator'])
                                ->orderBy('appointment_date', 'desc')
                                ->get();
        return response()->json($appointments);
    }
    // End Appointments Data

    // All Doctor Appointment
    public function AllDoctorAppointment(){
        $appointments = Appointment::where('status', 'confirmed') // یا هر شرطی که می‌خوای
        ->with(['patient', 'doctor', 'creator'])
        ->orderBy('appointment_date', 'desc')
        ->get();

        return view('backend.doctor.appointment.index',  compact('appointments'));
    }
    // End All Doctor Appointment

    // Add Doctor Appointment
    public function AddDoctorAppointment() {
        return view('backend.doctor.appointment.add');
    }
}
