<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBillRequest;
use App\Http\Requests\StorePaymentRequest;
use App\Models\Bill;
use App\Models\Patient;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class FinanceController extends Controller
{
    public function FinanceDashboard()
    {
        $totalPayments = Payment::sum('amount');
        $pendingInvoices = Bill::whereIn('status', ['pending', 'partially_paid'])->count();
        $totalDue = Bill::whereIn('status', ['pending', 'partially_paid'])->sum('due_amount');

        return view('backend.finance.index', compact('totalPayments', 'pendingInvoices', 'totalDue'));
    }

    public function FinanceLogout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function FinanceProfile()
    {
        $finance = Auth::user();

        return view('backend.finance.profile.finance_profile', compact('finance'));
    }

    public function UpdateFinanceProfile(Request $request)
    {
        $finance = Auth::user();

        if ($request->file('photo')) {
            if ($finance->photo && file_exists(public_path($finance->photo))) {
                unlink(public_path($finance->photo));
            }

            $image = $request->file('photo');
            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();
            $img = $manager->read($image);
            $img->resize(150, 150)->save(public_path('upload/finance/profile/'.$name_gen));
            $finance->photo = 'upload/finance/profile/'.$name_gen;
        }

        $finance->name = $request->name;
        $finance->phone = $request->phone;
        $finance->address = $request->address;
        $finance->save();

        return redirect()->route('finance.profile');
    }

    public function Income(Request $request)
    {
        $from = $request->from ?? now()->startOfMonth()->toDateString();
        $to = $request->to ?? now()->endOfMonth()->toDateString();

        $totalRevenue = Payment::whereBetween('payment_date', [$from, $to])->sum('amount');
        $totalBills = Bill::whereBetween('bill_date', [$from, $to])->sum('total_amount');
        $paidBills = Bill::whereBetween('bill_date', [$from, $to])->get()->sum(fn ($b) => $b->paidAmount());
        $totalDue = Bill::whereBetween('bill_date', [$from, $to])->sum('due_amount');

        return view('backend.finance.income.dashboard', compact(
            'totalRevenue', 'totalBills', 'paidBills', 'totalDue', 'from', 'to'
        ));
    }

    public function billsIndex()
    {
        $this->authorize('viewAny', Bill::class);
        $bills = Bill::with('patient')->latest()->paginate(20);

        return view('backend.finance.bills.index', compact('bills'));
    }

    public function AddBill()
    {
        $this->authorize('create', Bill::class);
        $patients = Patient::orderBy('name')->get();

        return view('backend.finance.bills.add', compact('patients'));
    }

    public function StoreBill(StoreBillRequest $request)
    {
        $bill = DB::transaction(function () use ($request) {
            $subtotal = 0;
            foreach ($request->description as $index => $description) {
                $qty = (int) $request->quantity[$index];
                $price = (float) $request->unit_price[$index];
                $subtotal += $qty * $price;
            }

            $discount = (float) ($request->discount ?? 0);
            $total = max(0, $subtotal - $discount);

            $bill = Bill::create([
                'invoice_no' => 'INV-'.now()->format('Ymd').'-'.str_pad((Bill::max('id') ?? 0) + 1, 5, '0', STR_PAD_LEFT),
                'patient_id' => $request->patient_id,
                'appointment_id' => $request->appointment_id,
                'bill_date' => $request->bill_date,
                'discount' => $discount,
                'total_amount' => $total,
                'due_amount' => $total,
                'status' => 'pending',
                'notes' => $request->notes,
            ]);

            foreach ($request->description as $index => $description) {
                $qty = (int) $request->quantity[$index];
                $price = (float) $request->unit_price[$index];

                $bill->items()->create([
                    'description' => $description,
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'total_price' => $qty * $price,
                ]);
            }

            return $bill;
        });

        return redirect()->route('finance.bills.show', $bill->id)->with('success', 'Invoice created successfully.');
    }

    public function ShowBill($id)
    {
        $bill = Bill::with(['patient', 'items', 'payments.receiver', 'appointment'])->findOrFail($id);
        $this->authorize('view', $bill);

        return view('backend.finance.bills.show', compact('bill'));
    }

    public function paymentsIndex()
    {
        $payments = Payment::with(['bill.patient', 'receiver'])->latest()->paginate(20);

        return view('backend.finance.payments.index', compact('payments'));
    }

    public function AddPayment()
    {
        $bills = Bill::whereIn('status', ['pending', 'partially_paid'])->with('patient')->get();

        return view('backend.finance.payments.add', compact('bills'));
    }

    public function StorePayment(StorePaymentRequest $request)
    {
        DB::transaction(function () use ($request) {
            $bill = Bill::lockForUpdate()->findOrFail($request->bill_id);

            Payment::create([
                'bill_id' => $bill->id,
                'amount' => $request->amount,
                'payment_date' => $request->payment_date,
                'payment_method' => $request->payment_method,
                'reference_no' => $request->reference_no,
                'received_by' => Auth::id(),
            ]);

            $bill->refreshPaymentStatus();
        });

        return redirect()->route('finance.payments')->with('success', 'Payment recorded successfully.');
    }

    public function expensesIndex()
    {
        return view('backend.finance.expenses.index');
    }
}
