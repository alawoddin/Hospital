@extends('backend.recieption_dashboard')
@section('recieption')

<div class="container-fluid">
    
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4>Book Appointment</h4>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('all.appointment') }}">Appointments</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Book Appointment</a></li>
            </ol>
        </div>
    </div>
    
    <div class="row">
        <div class="col-xl-12 col-xxl-12 col-sm-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Book New Appointment</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('store.appointment') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="col-xs-12">

                            <!-- Select Patient -->
                            <div class="form-group">
                                <label for="patient_id">Patient</label>
                                <select class="form-control select2" id="patient_id" name="patient_id" required>
                                    <option value="">Select Patient</option>
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->id }}">{{ $patient->name }} ({{ $patient->email }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Select Doctor -->
                            <div class="form-group">
                                <label for="doctor_id">Doctor</label>
                                <select class="form-control" id="doctor_id" name="doctor_id" required>
                                    <option value="">Select Doctor</option>
                                    @foreach($doctors as $doctor)
                                        <option value="{{ $doctor->id }}">{{ $doctor->name }} ({{ $doctor->email }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="appointment_date">Appointment Date</label>
                                <input type="date" class="form-control" id="appointment_date" name="appointment_date" required>
                            </div>

                            <div class="form-group">
                                <label for="appointment_time">Appointment Time</label>
                                <input type="time" class="form-control" id="appointment_time" name="appointment_time" required>
                            </div>

                            <div class="form-group">
                                <label for="token_number">Token Number</label>
                                <input type="number" class="form-control" id="token_number" name="token_number" placeholder="Queue number for the day">
                            </div>

                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="pending">Pending</option>
                                    <option value="confirmed">Confirmed</option>
                                    <option value="canceled">Canceled</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" id="description" name="description" cols="5"></textarea>
                            </div>

                        </div>
                        <div class="col-xs-12 col-sm-9 col-md-8 mt-3">
                            <button type="submit" class="btn btn-primary">Save</button>
                            <a href="{{ route('all.appointment') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Select Patient",
            allowClear: true
        });
    });
</script>

@endsection
