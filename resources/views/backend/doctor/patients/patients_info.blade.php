@extends('backend.doctor_dashboard')
@section('doctor')

<div class="row mt-5">
    <div class="col-xl-10 col-lg-10 col-md-8 mx-auto">
        <div class="card">
            <div class="card-body">
                <div class="text-center">
                    <div class="profile-photo">
                        <img src="{{ asset($patients->photo) }}" width="200" class="img-fluid rounded-circle" alt="">
                    </div>
                    <h3 class="mt-4 mb-1">{{ $patients->name }}</h3>
                    <p class="text-muted">{{ $patients->email }}</p>
                </div>
            </div>
            <div class="card-footer pt-0 pb-0 text-center">
                <div class="row">
                    <div class="col-12 pt-3 pb-3 border-bottom float-left d-flex">
                        <h4>Name: <span style="margin-left: 20px; color:rgb(19, 120, 167);">{{ $patients->name }}</span></h4>
                    </div>
                    <div class="col-12 pt-3 pb-3 border-bottom float-left d-flex">
                        <h4>Father Name: <span style="margin-left: 20px; color:rgb(19, 120, 167);">{{ $patients->father_name }}</span></h4>
                    </div>
                    <div class="col-12 pt-3 pb-3 border-bottom float-left d-flex">
                        <h4>Last Name: <span style="margin-left: 20px; color:rgb(19, 120, 167);">{{ $patients->last_name }}</span></h4>
                    </div>
                    <div class="col-12 pt-3 pb-3 border-bottom float-left d-flex">
                        <h4>Gender: <span style="margin-left: 20px; color:rgb(19, 120, 167);">{{ $patients->gender }}</span></h4>
                    </div>
                    <div class="col-12 pt-3 pb-3 border-bottom float-left d-flex">
                        <h4>Age: <span style="margin-left: 20px; color:rgb(19, 120, 167);">{{ $patients->age }}</span></h4>
                    </div>
                    <div class="col-12 pt-3 pb-3 border-bottom float-left d-flex">
                        <h4>Email: <span style="margin-left: 20px; color:rgb(19, 120, 167);">{{ $patients->email }}</span></h4>
                    </div>
                    <div class="col-12 pt-3 pb-3 border-bottom float-left d-flex">
                        <h4>Phone: <span style="margin-left: 20px; color:rgb(19, 120, 167);">{{ $patients->phone }}</span></h4>
                    </div>
                    <div class="col-12 pt-3 pb-3 border-bottom float-left d-flex">
                        <h4>Address: <span style="margin-left: 20px; color:rgb(19, 120, 167);">{{ $patients->address }}</span></h4>
                    </div>
                    <div class="col-12 pt-3 pb-3 border-bottom float-left d-flex">
                        <h4>National ID: <span style="margin-left: 20px; color:rgb(19, 120, 167);">{{ $patients->national_id }}</span></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-10 col-lg-10 col-md-10 mx-auto">
        <form action="{{ route('doctor.store.prescription') }}" method="POST">
            @csrf
            <div class="card">
                <div class="card-header">
                    <div class="text-start">
                        <h3>Prescription</h3>
                    </div>
                </div>
                <div class="card-body">
                    <div class="col-12 d-flex">
                        <input type="hidden" class="form-control" name="doctor_id" id="doctor" value="{{ auth()->id() }}">
                        <input type="hidden" class="form-control" name="patient_id" value="{{ $patients->id }}" id="patient">
                        <div class="col-4">
                            <label for="pharmacy">Select Pharmacy:</label>
                            <select name="pharmacy_id" id="pharmacy" class="form-control">
                                <option value="">-- Select Pharmacy --</option>
                                @foreach($pharmacies as $pharmacy)
                                    <option value="{{ $pharmacy->id }}">{{ $pharmacy->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-12 d-flex mt-5" style="justify-content: end; align-items: center;">
                        <div class="text-end">
                            <button type="button" class="btn btn-primary" id="addBtn">Add</button>
                            <button type="button" class="btn btn-danger" id="removeBtn">Delete</button>
                        </div>
                    </div>
                    <div id="medicineWrapper">
                        <div class="col-12 d-flex mt-3 medicine-row">
                            <div class="col-4">
                                <label for="medicine">Medicin</label>
                                <input type="text" name="medicine[]" class="form-control">
                            </div>
                            <div class="col-4">
                                <label for="desc">Description</label>
                                <textarea name="desc[]" id="desc" cols="30" rows="1" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <button type="submit" class="btn btn-primary mt-4">Submit</button>
                    </div>
                </div>
            </div>
        </form>
</div>
</div>

<script>
    document.getElementById('addBtn').addEventListener('click', function () {
        let wrapper = document.getElementById('medicineWrapper');
        let newRow = document.querySelector('.medicine-row').cloneNode(true);

        newRow.querySelectorAll('input, textarea').forEach(el => el.value = '');

        wrapper.appendChild(newRow);
    });

    document.getElementById('removeBtn').addEventListener('click', function () {
        let wrapper = document.getElementById('medicineWrapper');
        let rows = wrapper.querySelectorAll('.medicine-row');
        if (rows.length > 1) {
            wrapper.removeChild(rows[rows.length - 1]);
        }
    });
</script>

@endsection