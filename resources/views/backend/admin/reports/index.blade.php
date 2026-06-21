@extends('backend.admin_dashboard')
@section('admin')
<div class="container-fluid">
    <form method="GET" class="card p-3 mb-4">
        <div class="row align-items-end">
            <div class="col-md-3"><label>From</label><input type="date" name="from" class="form-control" value="{{ $from }}"></div>
            <div class="col-md-3"><label>To</label><input type="date" name="to" class="form-control" value="{{ $to }}"></div>
            <div class="col-md-3"><button class="btn btn-primary">Filter Report</button></div>
        </div>
    </form>

    <div class="row mb-4">
        <div class="col-md-3"><div class="card p-3"><h6>New Patients</h6><h3>{{ $totalPatients }}</h3></div></div>
        <div class="col-md-3"><div class="card p-3"><h6>Registration Fees</h6><h3>${{ number_format($registrationFees, 2) }}</h3></div></div>
        <div class="col-md-3"><div class="card p-3"><h6>Checked-in Patients</h6><h3>{{ $checkedInPatients }}</h3></div></div>
        <div class="col-md-3"><div class="card p-3"><h6>Consultation Fees</h6><h3>${{ number_format($consultationFees, 2) }}</h3></div></div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4"><div class="card p-3"><h6>Total Appointments</h6><h3>{{ $totalAppointments }}</h3></div></div>
        <div class="col-md-4"><div class="card p-3"><h6>Payments Received</h6><h3>${{ number_format($totalRevenue, 2) }}</h3></div></div>
        <div class="col-md-4"><div class="card p-3"><h6>Pending Bills</h6><h3>${{ number_format($pendingBills, 2) }}</h3></div></div>
    </div>

    <div class="card">
        <div class="card-header"><h4>Doctor Performance Report</h4></div>
        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Doctor</th>
                        <th>Fee/Patient</th>
                        <th>Checked-in (Period)</th>
                        <th>Earnings (Period)</th>
                        <th>This Month</th>
                        <th>Month Earnings</th>
                        <th>This Year</th>
                        <th>Year Earnings</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($doctors as $row)
                    <tr>
                        <td>{{ $row['doctor']->name }}</td>
                        <td>${{ number_format($row['consultation_fee'], 2) }}</td>
                        <td>{{ $row['period_patients'] }}</td>
                        <td>${{ number_format($row['period_earnings'], 2) }}</td>
                        <td>{{ $row['month_patients'] }}</td>
                        <td>${{ number_format($row['month_earnings'], 2) }}</td>
                        <td>{{ $row['year_patients'] }}</td>
                        <td>${{ number_format($row['year_earnings'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <p class="text-muted mt-2">Example: 100 patients × $250 fee = $25,000 monthly earnings</p>
        </div>
    </div>
</div>
@endsection
