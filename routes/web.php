<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\PharmacyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecieptionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::get('/dashboard', function () {
    $user = auth()->user();
    return match ($user?->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'doctor' => redirect()->route('doctor.dashboard'),
        'recieption' => redirect()->route('recieption.dashboard'),
        'finance' => redirect()->route('finance.dashboard'),
        'pharmacy' => redirect()->route('pharmacy.dashboard'),
        default => redirect()->route('user.dashboard'),
    };
})->middleware(['auth', 'verified'])->name('dashboard');



Route::get('/', function () {
    return view('welcome');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';


// Admin Controller
Route::middleware('auth', 'role:admin')->group(function (){
    Route::controller(AdminController::class)->group(function () {

        Route::get('/admin/dashboard', 'AdminDashboard')->name('admin.dashboard');
        //Logout Route
        Route::get('/admin/logout', 'AdminLogout')->name('admin.logout');
        // Admin Profile
        Route::get('/admin/profile','AdminProfile')->name('admin.profile');
        Route::post('/update/admin/profile', 'UpdateAdminProfile')->name('update.admin.profile');
       
        // end Admin Profile
        Route::get('/all/users', 'AllUsers')->name('all.users');
        Route::get('/add/users', 'AddUsers')->name('add.users');
        Route::post('/store/users', 'StoreUsers')->name('store.users');
        Route::get('/edit/users/{id}', 'EditUsers')->name('edit.users');
        Route::post('/update/users', 'UpdateUsers')->name('update.users');
        Route::get('/delete/users/{id}', 'DeleteUsers')->name('delete.users');

        // Doctors Management
        Route::get('/all/doctors', 'AllDoctors')->name('all.doctors');

        // Patients Management
        Route::get('/all/admin/patients', 'AllAdminPatients')->name('all.admin.patients');

        // Pharmacy Management
        Route::get('/all/admin/pharmacy', 'AllAdminPharmacy')->name('all.admin.pharmacy');

        // Finance Management
        Route::get('/all/admin/finance', 'AllAdminFinance')->name('all.admin.finance');

        // End Admin Controller
        Route::get('/all/admin/recieption', 'AllAdminRecieption')->name('all.admin.recieption');

        // Admin Appointments
        Route::get('/all/admin/appointments', 'AllAdminAppointments')->name('all.admin.appointments');
        Route::get('/add/admin/appointments', 'AddAdminAppointments')->name('add.admin.appointments');
        Route::post('/store/admin/appointments', 'StoreAdminAppointments')->name('store.admin.appointments');
        Route::get('/edit/admin/appointments/{id}', 'EditAdminAppointments')->name('edit.admin.appointments');
        Route::post('/update/admin/appointments/', 'UpdateAdminAppointments')->name('update.admin.appointments');
        Route::get('/delete/admin/appointments/{id}', 'DeleteAdminAppointments')->name('delete.admin.appointments');

    });
});
// End Controller

// Doctor Controller
Route::middleware('auth', 'role:doctor')->group(function (){
    Route::controller(DoctorController::class)->group(function () {
        Route::get('/doctor/dashboard', 'DoctorDashboard')->name('doctor.dashboard');
        //Logout Route
        Route::get('/doctor/logout', 'DoctorLogout')->name('doctor.logout');
        // Doctor Profile
        Route::get('/doctor/profile','DoctorProfile')->name('doctor.profile');
        Route::post('/update/doctor/profile', 'UpdateDoctorProfile')->name('update.doctor.profile');
        // Doctor Patients
        Route::get('doctor/patients','DoctorPatients')->name('doctor.patients');
        // Patients Info
        Route::get('patients/info/{id}','PatientsInfo')->name('patients.info');
        // Store Patient Prescription
        Route::post('/doctor/store/prescription','StorePrescription')->name('doctor.store.prescription');
        // Appointment Notification
        Route::get('/doctor/notifications','Notifications')->name('doctor.notifications');
        Route::post('/doctor/appointment/{id}/accept','AcceptAppointment')->name('doctor.appointment.accept');
        Route::post('/doctor/appointment/{id}/ignore','IgnoreAppointment')->name('doctor.appointment.ignore');
        Route::get('/doctor/appointment/count', 'appointmentCount')->name('doctor.appointment.count');

        Route::get('/doctor/appointments/data', 'AppointmentsData')->name('doctor.appointments.data');

        // Doctor Appointment
        Route::get('/all/doctor/appointment', 'AllDoctorAppointment')->name('all.doctor.appointment');
        Route::get('/add/doctor/appointment', 'AddDoctorAppointment')->name('add.doctor.appointment');
    });
});

// Recieption Controller
Route::middleware('auth', 'role:recieption')->group(function (){
    Route::controller(RecieptionController::class)->group(function () {
        Route::get('/recieption/dashboard', 'RecieptionDashboard')->name('recieption.dashboard');
        //Logout Route
        Route::get('/recieption/logout', 'RecieptionLogout')->name('recieption.logout');
        // Recieption Profile
        Route::get('/recieption/profile','RecieptionProfile')->name('recieption.profile');
        Route::post('/update/recieption/profile', 'UpdateRecieptionProfile')->name('update.recieption.profile');
        // Patients All Route
        Route::get('/all/patients','AllPatients')->name('all.patients');
        Route::get('/add/patients','AddPatients')->name('add.patients');
        Route::post('/store/patients','StorePatients')->name('store.patients');
        Route::get('/edit/patients/{id}','EditPatients')->name('edit.patients');
        Route::post('/update/patients','UpdatePatients')->name('update.patients');
        Route::get('/delete/patients/{id}','DeletePatients')->name('delete.patients');

        // Appointments All Route
        Route::get('/all/appointment','AllAppointment')->name('all.appointment');
        Route::get('/add/appointment','AddAppointment')->name('add.appointment');
        Route::post('/store/appointment','StoreAppointment')->name('store.appointment');
        Route::get('/edit/appointment/{id}', 'EditAppointment')->name('edit.appointment');
        Route::post('/update/appointment','UpdateAppointment')->name('update.appointment');
        Route::get('/delete/appointment/{id}', 'DeleteAppointment')->name('delete.appointment');
    });
});

// Finance Controller
Route::middleware('auth', 'role:finance')->group(function (){
    Route::controller(FinanceController::class)->group(function () {
        Route::get('/finance/dashboard', 'FinanceDashboard')->name('finance.dashboard');
        //Logout Route
        Route::get('/finance/logout', 'FinanceLogout')->name('finance.logout');
        // Finace Profile
        Route::get('/finance/profile','FinanceProfile')->name('finance.profile');
        Route::post('/update/finance/profile', 'UpdateFinanceProfile')->name('update.finance.profile');

        Route::get('/income','Income')->name('income');
        Route::get('/bills', 'billsIndex')->name('finance.bills');
        Route::get('/payments', 'paymentsIndex')->name('finance.payments');

    });
});

// Pharmacy Controller
Route::middleware('auth', 'role:pharmacy')->group(function (){
    Route::controller(PharmacyController::class)->group(function () {
        Route::get('/pharmacy/dashboard', 'PharmacyDashboard')->name('pharmacy.dashboard');
        //Logout Route
        Route::get('/pharmacy/logout', 'PharmacyLogout')->name('pharmacy.logout');
        // Pharmacy Profile
        Route::get('/pharmacy/profile','PharmacyProfile')->name('pharmacy.profile');
        Route::post('/update/pharmacy/profile', 'UpdatePharmacyProfile')->name('update.pharmacy.profile');
        // Pharmacy Patients
        Route::get('/pharmacy/patients','PharmacyPatients')->name('pharmacy.patients');
        Route::get('/prescription/details/{id}','PrescriptionDetails')->name('prescriptions.details');
        // Company 
        Route::get('/all/company','AllCompany')->name('all.company');
        Route::get('/add/company','AddCompany')->name('add.company');
        Route::post('/store/company','StoreCompany')->name('store.company');
        Route::get('/edit/company/{id}','EditCompany')->name('edit.company');
        Route::post('/update/company','UpdateCompany')->name('update.company');
        Route::get('/delete/company/{id}','DeleteCompany')->name('delete.company');
        // Supplier
        Route::get('/all/supplier','AllSupplier')->name('all.supplier');
        Route::get('/add/supplier','AddSupplier')->name('add.supplier');
        Route::post('/store/supplier','StoreSupplier')->name('store.supplier');
        Route::get('/edit/supplier/{id}','EditSupplier')->name('edit.supplier');
        Route::post('/update/supplier','UpdateSupplier')->name('update.supplier');
        Route::get('/delete/supplier/{id}','DeleteSupplier')->name('delete.supplier');
        // Products
        Route::get('/all/products','AllProducts')->name('all.products');
        Route::get('/add/products','AddProducts')->name('add.products');
        Route::post('/store/products','StoreProducts')->name('store.products');
        Route::get('/edit/products/{id}','EditProducts')->name('edit.products');
        Route::post('/update/products','UpdateProducts')->name('update.products');
        Route::get('/delete/products/{id}','DeleteProducts')->name('delete.products');
        // All Expires Medicine
        Route::get('/all/expires/medicine','AllExpiresMedicine')->name('all.expires.medicine');
        Route::get('/edit/expires/medicine/{id}','EditExpiresMedicine')->name('edit.expires.medicine');
        Route::post('/update/expires/medicine','UpdateExpiresMedicine')->name('update.expires.medicine');
        Route::get('/delete/products/{id}','DeleteProducts')->name('delete.products');
    });
});

// User Controller
Route::middleware('auth', 'role:user')->group(function (){
    Route::controller(UserController::class)->group(function () {
        Route::get('/user/dashboard', 'UserDashboard')->name('user.dashboard');
        //Logout Route
        Route::get('/user/logout', 'UserLogout')->name('user.logout');
        // User Profile
        Route::get('/user/profile','UserProfile')->name('user.profile');
        Route::post('/update/user/profile', 'UpdateUserProfile')->name('update.user.profile');
    });
});