<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Bill;
use App\Models\Department;
use App\Models\Patient;
use App\Models\DoctorConsultation;
use App\Models\FeeType;
use App\Models\Payment;
use App\Services\HospitalBillingService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class AdminController extends Controller
{
    public function AdminDashboard() {
        $totalPatients = Patient::count();
        $totalDoctors = User::where('role', 'doctor')->count();
        $totalRevenue = Payment::sum('amount');
        $totalAppointments = Appointment::count();

         return view('backend.admin.index', compact(
            'totalPatients',
            'totalDoctors',
            'totalRevenue',
            'totalAppointments'
         ));
    }

    //Logout Route
    public function AdminLogout(Request $request) {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
    // End Logout

    // Admin Profile
    public function AdminProfile(){
        $admin = Auth::user();
        return view('backend.admin.profile.admin_profile', compact('admin'));
    }
    // End Admin Profile

    // Update Profile
    public function UpdateAdminProfile(Request $request) {

        $admin = Auth::user();

       if ($request->file('photo')) {
            $image = $request->file('photo');
            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();
            $img = $manager->read($image);
            $img->resize(150,150)->save(public_path('upload/profile/'.$name_gen));
            $save_url = 'upload/profile/'.$name_gen;

            $admin->photo = $save_url;
        }

        $admin->name = $request->name;
        $admin->phone = $request->phone;
        $admin->address = $request->address;
        $admin->role = $request->role;

        $admin->save();
        return redirect()->route('admin.profile');
    }
    // End Update Profile

    public function AllUsers(){
        $users = User::latest()->get();
        return view('backend.admin.users.index', compact('users'));
    }
    // End All Users

    public function AddUsers() {
        return view('backend.admin.users.add');
    }
    // End Add Users

    public function StoreUsers(Request $request) {
       if ($request->file('photo')) {
            $image = $request->file('photo');
            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();
            $img = $manager->read($image);
            $img->resize(150,150)->save(public_path('upload/admin/photo/'.$name_gen));
            $save_url = 'upload/admin/photo/'.$name_gen;

        User::create(array_merge($this->userPayload($request), [
            'password' => Hash::make($request->password),
            'photo' => $save_url,
        ]));
        return redirect()->route('all.users');

        }else{
            User::create(array_merge($this->userPayload($request), [
            'password' => Hash::make($request->password),
            ]));
        }
        return redirect()->route('all.users');
    }
    // End Store Users

    public function EditUsers($id) {
        $user = User::find($id);
        return view('backend.admin.users.edit', compact('user'));
    }
    // End Edit Users

    public function UpdateUsers(Request $request) {
        $user_id = $request->id;
        $user = User::findOrFail($user_id);
        if ($request->file('photo')) {

             if ($user->photo && file_exists(public_path($user->photo))) {
                unlink(public_path($user->photo));
            }

            $image = $request->file('photo');
            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();
            $img = $manager->read($image);
            $img->resize(150,150)->save(public_path('upload/admin/photo/'.$name_gen));
            $save_url = 'upload/admin/photo/'.$name_gen;

        User::find($user_id)->update(array_merge($this->userPayload($request), [
            'password' => Hash::make($request->password),
            'photo' => $save_url,
        ]));
        return redirect()->route('all.users');

        }else{
            User::find($user_id)->update(array_merge($this->userPayload($request), [
            'password' => Hash::make($request->password),
            ]));
        }
        return redirect()->route('all.users');
    }
    // End Update Users

    public function DeleteUsers($id) {
        $user = User::findOrFail($id);
        if ($user->photo && file_exists(public_path($user->photo))) {
            unlink(public_path($user->photo));
        }
        User::findOrFail($id)->delete();
        return redirect()->route('all.users');
    }
    // End Delete User

    // All Doctors
    public function AllDoctors(){
        $doctors = User::where('role', 'doctor')->orderBy('id', 'desc')->get();
        return view('backend.admin.doctors.index', compact('doctors'));
    }
    // End All Doctors

    // All Admin Patients
    public function AllAdminPatients(){
        $patients = Patient::all();
        return view('backend.admin.patients.index', compact('patients'));
    }
    // End All Admin Patients

    // All Admin Pharmacy
    public function AllAdminPharmacy(){
        $pharmacy = User::where('role', 'pharmacy')->orderBy('id', 'desc')->get();
        return view('backend.admin.pharmacy.index', compact('pharmacy'));
    }
    // End All Admin Pharmacy

    // All Admin Finance
    public function AllAdminFinance() {
        $finance = User::where('role', 'finance')->orderBy('id', 'desc')->get();
        return view('backend.admin.finance.index', compact('finance'));
    }
    // End All Admin Finance

    // All Admin Recieption 
    public function AllAdminRecieption(){
        $recieption = User::where('role', 'recieption')->orderBy('id', 'desc')->get();
        return view('backend.admin.recieption.index', compact('recieption'));
    }
    // End Admin Recieption

    // All Admin Appointments
    public function AllAdminAppointments(){
        $appointments = Appointment::all();
        return view('backend.admin.appointment.index', compact('appointments'));
    }
    // End All Admin Appointment

    // Add Admin Appointments
    public function AddAdminAppointments(){
        $patients = Patient::all();
        $doctors = User::where('role', 'doctor')->get();
        return view('backend.admin.appointment.add', compact('patients', 'doctors'));
    }
    // End Add Admin Appointments

    // Store Admin Appointments
    public function StoreAdminAppointments(Request $request){
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
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

        // $doctor = User::find($request->doctor_id);
        // if ($doctor) {
        //     $doctor->notify(new AppointmentCreated($appointment));
        // }

        return redirect()->route('all.admin.appointments')->with('success', 'Appointment created and notification sent successfully!');
    }
    // End Store Admin Appointments

    // All Edit Appointments
    public function EditAdminAppointments($id){
        $appointments = Appointment::findOrFail($id);
        $patients = Patient::all();
        $doctors = User::where('role', 'doctor')->get();
        return view('backend.admin.appointment.edit', compact('appointments','patients', 'doctors'));
    }
    // End All Edit Appointments

    // All Update Appointments
    public function UpdateAdminAppointments(Request $request){
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
    
        // // حذف Notification قبلی برای این نوبت
        // DB::table('notifications')->where('data->appointment_id', $appointments->id)->delete();

        // // ایجاد Notification جدید
        // $doctor = User::find($request->doctor_id);
        // if ($doctor) {
        //     $doctor->notify(new AppointmentCreated($appointments));
        // }

        return redirect()->route('all.admin.appointments');
    }
    // End Update Appointments

    // All Delete Appointments
    public function DeleteAdminAppointments($id){
        Appointment::findOrFail($id)->delete();
        return redirect()->route('all.admin.appointments');
    }

    public function AllDepartments()
    {
        $departments = Department::latest()->get();

        return view('backend.admin.departments.index', compact('departments'));
    }

    public function AddDepartment()
    {
        return view('backend.admin.departments.add');
    }

    public function StoreDepartment(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:departments,name'],
            'description' => ['nullable', 'string'],
        ]);

        Department::create($request->only('name', 'description'));

        return redirect()->route('admin.departments')->with('success', 'Department created.');
    }

    public function EditDepartment($id)
    {
        $department = Department::findOrFail($id);

        return view('backend.admin.departments.edit', compact('department'));
    }

    public function UpdateDepartment(Request $request)
    {
        $department = Department::findOrFail($request->id);

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:departments,name,'.$department->id],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $department->update([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.departments')->with('success', 'Department updated.');
    }

    public function DeleteDepartment($id)
    {
        Department::findOrFail($id)->delete();

        return redirect()->route('admin.departments')->with('success', 'Department deleted.');
    }

    public function AllFeeTypes()
    {
        $feeTypes = FeeType::with('department')->latest()->get();

        return view('backend.admin.fees.index', compact('feeTypes'));
    }

    public function AddFeeType()
    {
        $departments = Department::orderBy('name')->get();

        return view('backend.admin.fees.add', compact('departments'));
    }

    public function StoreFeeType(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:registration,consultation,laboratory'],
            'amount' => ['required', 'numeric', 'min:0'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'description' => ['nullable', 'string'],
        ]);

        FeeType::create([
            'name' => $request->name,
            'category' => $request->category,
            'amount' => $request->amount,
            'department_id' => $request->department_id,
            'description' => $request->description,
            'is_active' => true,
        ]);

        return redirect()->route('admin.fees')->with('success', 'Fee type created.');
    }

    public function EditFeeType($id)
    {
        $feeType = FeeType::findOrFail($id);
        $departments = Department::orderBy('name')->get();

        return view('backend.admin.fees.edit', compact('feeType', 'departments'));
    }

    public function UpdateFeeType(Request $request)
    {
        $feeType = FeeType::findOrFail($request->id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:registration,consultation,laboratory'],
            'amount' => ['required', 'numeric', 'min:0'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'description' => ['nullable', 'string'],
        ]);

        $feeType->update([
            'name' => $request->name,
            'category' => $request->category,
            'amount' => $request->amount,
            'department_id' => $request->department_id,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.fees')->with('success', 'Fee type updated.');
    }

    public function DeleteFeeType($id)
    {
        FeeType::findOrFail($id)->delete();

        return redirect()->route('admin.fees')->with('success', 'Fee type deleted.');
    }

    public function Reports(Request $request, HospitalBillingService $billingService)
    {
        $from = $request->from ?? now()->startOfMonth()->toDateString();
        $to = $request->to ?? now()->endOfMonth()->toDateString();

        $totalPatients = Patient::whereBetween('created_at', [$from.' 00:00:00', $to.' 23:59:59'])->count();
        $registrationFees = Patient::whereBetween('created_at', [$from.' 00:00:00', $to.' 23:59:59'])->sum('registration_fee');
        $totalAppointments = Appointment::whereBetween('appointment_date', [$from, $to])->count();
        $checkedInPatients = DoctorConsultation::whereBetween('visited_at', [$from.' 00:00:00', $to.' 23:59:59'])->count();
        $consultationFees = DoctorConsultation::whereBetween('visited_at', [$from.' 00:00:00', $to.' 23:59:59'])->sum('consultation_fee');
        $totalRevenue = Payment::whereBetween('payment_date', [$from, $to])->sum('amount');
        $pendingBills = Bill::whereIn('status', ['pending', 'partially_paid'])->sum('due_amount');

        $doctors = User::where('role', 'doctor')->get()->map(function ($doctor) use ($billingService, $from, $to) {
            $monthStats = $billingService->doctorMonthlyStats($doctor);
            $periodPatients = DoctorConsultation::where('doctor_id', $doctor->id)
                ->whereBetween('visited_at', [$from.' 00:00:00', $to.' 23:59:59'])
                ->count();
            $periodEarnings = DoctorConsultation::where('doctor_id', $doctor->id)
                ->whereBetween('visited_at', [$from.' 00:00:00', $to.' 23:59:59'])
                ->sum('consultation_fee');

            return [
                'doctor' => $doctor,
                'consultation_fee' => (float) $doctor->consultation_fee,
                'period_patients' => $periodPatients,
                'period_earnings' => (float) $periodEarnings,
                'month_patients' => $monthStats['month_patients'],
                'year_patients' => $monthStats['year_patients'],
                'month_earnings' => $monthStats['month_earnings'],
                'year_earnings' => $monthStats['year_earnings'],
            ];
        });

        return view('backend.admin.reports.index', compact(
            'from', 'to', 'totalPatients', 'registrationFees', 'totalAppointments',
            'checkedInPatients', 'consultationFees', 'totalRevenue', 'pendingBills', 'doctors'
        ));
    }

    private function userPayload(Request $request): array
    {
        return [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'role' => $request->role,
            'consultation_fee' => $request->role === 'doctor' ? (float) ($request->consultation_fee ?? 0) : 0,
        ];
    }
}