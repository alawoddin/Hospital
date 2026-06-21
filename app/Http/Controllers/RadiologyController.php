<?php

namespace App\Http\Controllers;

use App\Models\RadiologyRequest;
use App\Models\User;
use App\Notifications\WorkflowNotification;
use App\Services\HospitalBillingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RadiologyController extends Controller
{
    public function RadiologyDashboard()
    {
        $today = RadiologyRequest::whereDate('created_at', today())->count();
        $month = RadiologyRequest::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count();
        $year = RadiologyRequest::whereBetween('created_at', [now()->startOfYear(), now()->endOfYear()])->count();
        $monthRevenue = RadiologyRequest::where('status', 'completed')
            ->whereBetween('completed_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('fee_amount');
        $yearRevenue = RadiologyRequest::where('status', 'completed')
            ->whereBetween('completed_at', [now()->startOfYear(), now()->endOfYear()])
            ->sum('fee_amount');
        $pending = RadiologyRequest::where('status', 'pending')->count();

        return view('backend.radiology.index', compact('today', 'month', 'year', 'monthRevenue', 'yearRevenue', 'pending'));
    }

    public function RadiologyLogout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function RadiologyProfile()
    {
        return view('backend.radiology.profile', ['user' => Auth::user()]);
    }

    public function UpdateRadiologyProfile(Request $request)
    {
        Auth::user()->update($request->only(['name', 'phone', 'address']));

        return redirect()->route('radiology.profile');
    }

    public function RadiologyRequests()
    {
        $requests = RadiologyRequest::with(['patient', 'doctor'])->latest()->paginate(20);

        return view('backend.radiology.requests', compact('requests'));
    }

    public function ProcessRadiologyRequest($id)
    {
        $req = RadiologyRequest::findOrFail($id);
        $req->update(['status' => 'in_progress']);

        return back()->with('success', 'Scan marked in progress.');
    }

    public function CompleteRadiologyRequest(Request $request, $id, HospitalBillingService $billing)
    {
        $radiologyRequest = RadiologyRequest::with(['patient', 'doctor', 'appointment'])->findOrFail($id);

        $request->validate([
            'result' => ['nullable', 'string'],
            'report_file' => ['nullable', 'file', 'max:4096'],
        ]);

        $reportPath = null;
        if ($request->file('report_file')) {
            $reportPath = $request->file('report_file')->store('upload/radiology/reports', 'public');
        }

        $billing->completeRadiologyRequest($radiologyRequest, Auth::user(), $request->result, $reportPath);

        $payload = [
            'type' => 'radiology_completed',
            'patient_id' => $radiologyRequest->patient_id,
            'patient_name' => $radiologyRequest->patient->name,
            'scan_type' => $radiologyRequest->scan_type,
            'message' => 'Radiology completed: '.$radiologyRequest->scan_type,
        ];

        User::whereIn('role', ['finance', 'recieption'])->each(fn ($u) => $u->notify(new WorkflowNotification($payload)));

        return back()->with('success', 'Radiology completed. Bill updated.');
    }
}
