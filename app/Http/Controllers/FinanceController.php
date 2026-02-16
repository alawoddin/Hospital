<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Payment;
use App\Models\Bill;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class FinanceController extends Controller
{
    public function FinanceDashboard() {
        return view('backend.finance.index');
    }

    //Logout Route
    public function FinanceLogout(Request $request) {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
    // End Logout

    // Finance Profile
    public function FinanceProfile(){
        $finance = Auth::user();
        return view('backend.finance.profile.finance_profile', compact('finance'));
    }
    // End Finance Profile

    // Update Profile
    public function UpdateFinanceProfile(Request $request) {

        $finance = Auth::user();

       if ($request->file('photo')) {

            if ($finance->photo && file_exists(public_path($finance->photo))) {
                unlink(public_path($finance->photo));
            }

            $image = $request->file('photo');
            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();
            $img = $manager->read($image);
            $img->resize(150,150)->save(public_path('upload/finance/profile/'.$name_gen));
            $save_url = 'upload/finance/profile/'.$name_gen;

            $finance->photo = $save_url;
        }

        $finance->name = $request->name;
        $finance->phone = $request->phone;
        $finance->address = $request->address;
        $finance->role = $request->role;

        $finance->save();
        return redirect()->route('finance.profile');
    }
    // End Update Profile

    // داشبورد مالی
    public function Income(Request $request)
    {
        $from = $request->from ?? now()->startOfMonth()->toDateString();
        $to = $request->to ?? now()->endOfMonth()->toDateString();

        $totalRevenue = Payment::whereBetween('payment_date', [$from, $to])->sum('amount');

        $totalBills = Bill::whereBetween('bill_date', [$from, $to])->sum('total_amount');
        $paidBills = Bill::whereBetween('bill_date', [$from, $to])->get()->sum(fn($b) => $b->paidAmount());
        $totalDue = Bill::whereBetween('bill_date', [$from, $to])->sum('due_amount');

        return view('backend.finance.income.dashboard', compact(
            'totalRevenue', 'totalBills', 'paidBills', 'totalDue', 'from', 'to'
        ));
    }

    // لیست فاکتورها
    public function billsIndex()
    {
        $bills = Bill::latest()->paginate(20);
        return view('backend.finance.bills.index', compact('bills'));
    }

    // لیست پرداخت‌ها
    public function paymentsIndex()
    {
        $payments = Payment::latest()->paginate(20);
        return view('backend.finance.payments.index', compact('payments'));
    }

}
