<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Notifications\AppointmentCreated;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;

class RecieptionController extends Controller
{
    public function RecieptionDashboard() {
        return view('backend.recieption.index');
    }

    //Logout Route
    public function RecieptionLogout(Request $request) {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
    // End Logout

    // Recieption Profile
    public function RecieptionProfile(){
        $recieption = Auth::user();
        return view('backend.recieption.profile.recieption_profile', compact('recieption'));
    }
    // End Recieption Profile

    // Update Profile
    public function UpdateRecieptionProfile(Request $request) {

        $recieption = Auth::user();

       if ($request->file('photo')) {

            if ($recieption->photo && file_exists(public_path($recieption->photo))) {
                unlink(public_path($recieption->photo));
            }

            $image = $request->file('photo');
            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();
            $img = $manager->read($image);
            $img->resize(150,150)->save(public_path('upload/recieption/profile/'.$name_gen));
            $save_url = 'upload/recieption/profile/'.$name_gen;

            $recieption->photo = $save_url;
        }

        $recieption->name = $request->name;
        $recieption->phone = $request->phone;
        $recieption->address = $request->address;
        $recieption->role = $request->role;

        $recieption->save();
        return redirect()->route('recieption.profile');
    }
    // End Update Profile


    // All Patients
    public function AllPatients(){
        $patients = Patient::latest()->get();
        return view('backend.recieption.patients.index', compact('patients'));
    }
    // End All Patients

    // Add Patients 
    public function AddPatients() {
        $doctors = User::where('role', 'doctor')->get();
        return view('backend.recieption.patients.add', compact('doctors'));
    }
    // End Add Patients

    // Store Patients
    public function StorePatients(Request $request) {
       if ($request->file('photo')) {
            $image = $request->file('photo');
            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();
            $img = $manager->read($image);
            $img->resize(150,150)->save(public_path('upload/recieption/patients/'.$name_gen));
            $save_url = 'upload/recieption/patients/'.$name_gen;

        Patient::create([
            'name'=> $request->name,
            'father_name'=> $request->father_name,
            'last_name'=> $request->last_name,
            'age'=> $request->age,
            'gender'=> $request->gender,
            'doctor'=> $request->doctor,
            'phone'=> $request->phone,
            'email'=> $request->email,
            'address'=> $request->address,
            'national_id'=> $request->national_id,
            'photo'=>$save_url,
        ]);
        return redirect()->route('all.patients');

        }else{
            Patient::create([
            'name'=> $request->name,
            'father_name'=> $request->father_name,
            'last_name'=> $request->last_name,
            'age'=> $request->age,
            'gender'=> $request->gender,
            'doctor'=> $request->doctor,
            'phone'=> $request->phone,
            'email'=> $request->email,
            'address'=> $request->address,
            'national_id'=> $request->national_id,
            ]);
        }
        return redirect()->route('all.patients');
    }
    // End Store Patients

    // Edit Patients
    public function EditPatients($id) {
        $patients = Patient::findOrFail($id);
        $doctors = User::where('role', 'doctor')->get();
        return view('backend.recieption.patients.edit', compact('patients','doctors'));
    }
    // End Edit Patients

    // Store Patients
    public function UpdatePatients(Request $request) {
        $patients_id = $request->id;
        $patients = Patient::findOrFail($patients_id);
       if ($request->file('photo')) {

            if ($patients->photo && file_exists(public_path($patients->photo))) {
                unlink(public_path($patients->photo));
            }

            $image = $request->file('photo');
            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();
            $img = $manager->read($image);
            $img->resize(150,150)->save(public_path('upload/recieption/patients/'.$name_gen));
            $save_url = 'upload/recieption/patients/'.$name_gen;

        Patient::find($patients_id)->update([
            'name'=> $request->name,
            'father_name'=> $request->father_name,
            'last_name'=> $request->last_name,
            'age'=> $request->age,
            'gender'=> $request->gender,
            'doctor'=> $request->doctor,
            'phone'=> $request->phone,
            'email'=> $request->email,
            'address'=> $request->address,
            'national_id'=> $request->national_id,
            'photo'=>$save_url,
        ]);
        return redirect()->route('all.patients');

        }else{
            Patient::find($patients_id)->update([
            'name'=> $request->name,
            'father_name'=> $request->father_name,
            'last_name'=> $request->last_name,
            'age'=> $request->age,
            'gender'=> $request->gender,
            'doctor'=> $request->doctor,
            'phone'=> $request->phone,
            'email'=> $request->email,
            'address'=> $request->address,
            'national_id'=> $request->national_id,
            ]);
        }
        return redirect()->route('all.patients');
    }
    // End Store Patients

    public function DeletePatients($id) {
        $patients = Patient::findOrFail($id);
        if ($patients->photo && file_exists(public_path($patients->photo))) {
            unlink(public_path($patients->photo));
        }
        Patient::findOrFail($id)->delete();
        return redirect()->route('all.patients');
    }
    // End Delete User

    // All Appointment
    public function AllAppointment(){
        $appointments = Appointment::with(['patient', 'doctor'])->get();
        return view('backend.recieption.appointment.index', compact('appointments'));
    }
    // End All Appointment

    // Add Appointment
    public function AddAppointment(){
        $patients = Patient::all();
        $doctors = User::where('role', 'doctor')->get();

        return view('backend.recieption.appointment.add', compact('patients', 'doctors'));
    }
    // End Add Appointment

    public function StoreAppointment(Request $request){
        $request->validate([
            'patient_id' => 'required|exists:users,id',
            'doctor_id' => 'required|exists:users,id',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
            'description' => 'nullable|string',
        ]);

        $appointment = Appointment::create([
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'token_number' => $request->token_number,
            'status' => $request->status,
            'created_by' => auth()->id(),
            'description' => $request->description,
        ]);

        $doctor = User::find($request->doctor_id);
        if ($doctor) {
            $doctor->notify(new AppointmentCreated($appointment));
        }

        return redirect()->route('all.appointment')->with('success', 'Appointment created and notification sent successfully!');
    }

    // End Store Appointment

    // Edit Appointment
    public function EditAppointment($id){
        $appointments = Appointment::findOrFail($id);
        $patients = Patient::all();
        $doctors = User::where('role', 'doctor')->get();
        return view('backend.recieption.appointment.edit', compact('appointments','patients', 'doctors'));
    }
    // End Edit Appointment

    // Update Appointment 
    public function UpdateAppointment(Request $request) {
        $app_id = $request->id;
        $appointments = Appointment::findOrFail($app_id);
    
        $request->validate([
            'patient_id' => 'required|exists:patients,id', 
            'doctor_id' => 'required|exists:users,id',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
            'description' => 'nullable|string',
        ]);
    
        $appointments->update([
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'token_number' => $request->token_number,
            'status' => $request->status,
            'created_by' => auth()->id(),
            'description' => $request->description,
        ]);
    
        // حذف Notification قبلی برای این نوبت
        DB::table('notifications')->where('data->appointment_id', $appointments->id)->delete();

        // ایجاد Notification جدید
        $doctor = User::find($request->doctor_id);
        if ($doctor) {
            $doctor->notify(new AppointmentCreated($appointments));
        }

        return redirect()->route('all.appointment');
    }    
    // End Update Appointment

    // Delete Appointment
    public function DeleteAppointment($id){
        Appointment::findOrFail($id)->delete();
        return redirect()->route('all.appointment');
    }

}
