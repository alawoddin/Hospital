<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Bill;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class UserController extends Controller
{
    private function resolvePatient()
    {
        $user = Auth::user();

        return Patient::where('user_id', $user->id)
            ->orWhere('email', $user->email)
            ->first();
    }

    public function UserDashboard()
    {
        $patient = $this->resolvePatient();
        $upcomingAppointments = 0;
        $prescriptionCount = 0;
        $pendingBills = 0;

        if ($patient) {
            $upcomingAppointments = Appointment::where('patient_id', $patient->id)
                ->whereDate('appointment_date', '>=', today())
                ->where('status', 'confirmed')
                ->count();
            $prescriptionCount = Prescription::where('patient_id', $patient->id)->count();
            $pendingBills = Bill::where('patient_id', $patient->id)
                ->whereIn('status', ['pending', 'partially_paid'])
                ->count();
        }

        return view('backend.user.index', compact('upcomingAppointments', 'prescriptionCount', 'pendingBills', 'patient'));
    }

    public function UserLogout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function UserProfile()
    {
        $user = Auth::user();
        $patient = $this->resolvePatient();

        return view('backend.user.profile.user_profile', compact('user', 'patient'));
    }

    public function UpdateUserProfile(Request $request)
    {
        $user = Auth::user();

        if ($request->file('photo')) {
            if ($user->photo && file_exists(public_path($user->photo))) {
                unlink(public_path($user->photo));
            }

            $image = $request->file('photo');
            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();
            $img = $manager->read($image);
            $img->resize(150, 150)->save(public_path('upload/user/profile/'.$name_gen));
            $user->photo = 'upload/user/profile/'.$name_gen;
        }

        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->save();

        return redirect()->route('user.profile');
    }

    public function UserAppointments()
    {
        $patient = $this->resolvePatient();
        abort_unless($patient, 404, 'No patient record linked to your account.');

        $appointments = Appointment::with('doctor')
            ->where('patient_id', $patient->id)
            ->latest('appointment_date')
            ->get();

        return view('backend.user.appointments.index', compact('appointments', 'patient'));
    }

    public function UserPrescriptions()
    {
        $patient = $this->resolvePatient();
        abort_unless($patient, 404, 'No patient record linked to your account.');

        $prescriptions = Prescription::with(['doctor', 'items', 'pharmacy'])
            ->where('patient_id', $patient->id)
            ->latest()
            ->get();

        return view('backend.user.prescriptions.index', compact('prescriptions', 'patient'));
    }

    public function UserBills()
    {
        $patient = $this->resolvePatient();
        abort_unless($patient, 404, 'No patient record linked to your account.');

        $bills = Bill::with('payments')
            ->where('patient_id', $patient->id)
            ->latest()
            ->get();

        return view('backend.user.bills.index', compact('bills', 'patient'));
    }

    public function UserPayments()
    {
        $patient = $this->resolvePatient();
        abort_unless($patient, 404, 'No patient record linked to your account.');

        $payments = Payment::whereHas('bill', fn ($q) => $q->where('patient_id', $patient->id))
            ->with('bill')
            ->latest()
            ->get();

        return view('backend.user.payments.index', compact('payments', 'patient'));
    }

    public function UserMedicalReports()
    {
        $patient = $this->resolvePatient();
        abort_unless($patient, 404, 'No patient record linked to your account.');

        $patient->load(['diagnoses.doctor', 'labRequests.doctor', 'prescriptions.doctor']);

        return view('backend.user.reports.index', compact('patient'));
    }
}
