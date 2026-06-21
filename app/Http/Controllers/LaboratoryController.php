<?php

namespace App\Http\Controllers;

use App\Models\LabRequest;
use App\Models\User;
use App\Notifications\WorkflowNotification;
use App\Services\HospitalBillingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaboratoryController extends Controller
{
    public function LaboratoryDashboard()
    {
        $today = LabRequest::whereDate('created_at', today())->count();
        $month = LabRequest::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count();
        $year = LabRequest::whereBetween('created_at', [now()->startOfYear(), now()->endOfYear()])->count();
        $monthRevenue = LabRequest::where('status', 'completed')
            ->whereBetween('completed_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('fee_amount');
        $yearRevenue = LabRequest::where('status', 'completed')
            ->whereBetween('completed_at', [now()->startOfYear(), now()->endOfYear()])
            ->sum('fee_amount');
        $pending = LabRequest::where('status', 'pending')->count();

        return view('backend.laboratory.index', compact('today', 'month', 'year', 'monthRevenue', 'yearRevenue', 'pending'));
    }

    public function LaboratoryLogout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function LaboratoryProfile()
    {
        return view('backend.laboratory.profile', ['user' => Auth::user()]);
    }

    public function UpdateLaboratoryProfile(Request $request)
    {
        $user = Auth::user();
        $user->update($request->only(['name', 'phone', 'address']));

        return redirect()->route('laboratory.profile');
    }

    public function LabRequests()
    {
        $requests = LabRequest::with(['patient', 'doctor'])->latest()->paginate(20);

        return view('backend.laboratory.requests', compact('requests'));
    }

    public function ProcessLabRequest($id)
    {
        $request = LabRequest::with(['patient', 'doctor'])->findOrFail($id);
        $request->update(['status' => 'in_progress']);

        return back()->with('success', 'Test marked in progress.');
    }

    public function CompleteLabRequest(Request $request, $id, HospitalBillingService $billing)
    {
        $labRequest = LabRequest::with(['patient', 'doctor', 'appointment'])->findOrFail($id);

        $request->validate([
            'result' => ['required', 'string'],
            'report_file' => ['nullable', 'file', 'max:4096'],
        ]);

        $reportPath = null;
        if ($request->file('report_file')) {
            $reportPath = $request->file('report_file')->store('upload/lab/reports', 'public');
        }

        $billing->completeLabRequest($labRequest, Auth::user(), $request->result, $reportPath);

        $labRequest->patient->bills()->latest()->first()?->patient;
        $financeUsers = User::where('role', 'finance')->get();
        $receptionUsers = User::where('role', 'recieption')->get();

        $payload = [
            'type' => 'lab_completed',
            'patient_id' => $labRequest->patient_id,
            'patient_name' => $labRequest->patient->name,
            'test_name' => $labRequest->test_name,
            'message' => 'Lab test completed: '.$labRequest->test_name.' for '.$labRequest->patient->name,
        ];

        foreach ($financeUsers->merge($receptionUsers) as $user) {
            $user->notify(new WorkflowNotification($payload));
        }

        $labRequest->doctor?->notify(new WorkflowNotification($payload));

        return back()->with('success', 'Test completed. Bill updated automatically.');
    }
}
