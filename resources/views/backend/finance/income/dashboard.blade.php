@extends('backend.finance_dashboard')
@section('finance')

<div class="row">
  <div class="col-md-3">
    <div class="card p-3">
      <h6>Total Revenue</h6>
      <h3>${{ number_format($totalRevenue,2) }}</h3>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card p-3">
      <h6>Total Bills</h6>
      <h3>${{ number_format($totalBills,2) }}</h3>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card p-3">
      <h6>Paid Amount</h6>
      <h3>${{ number_format($paidBills,2) }}</h3>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card p-3">
      <h6>Total Due</h6>
      <h3>${{ number_format($totalDue,2) }}</h3>
    </div>
  </div>
</div>

@endsection
