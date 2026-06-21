@extends('backend.finance_dashboard')
@section('finance')
<div class="container-fluid">
    <form action="{{ route('finance.bills.store') }}" method="POST">
        @csrf
        <div class="card mb-3">
            <div class="card-header"><h4>Create Invoice</h4></div>
            <div class="card-body row">
                <div class="col-md-4 mb-3">
                    <label>Patient</label>
                    <select name="patient_id" class="form-control" required>
                        <option value="">Select patient</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}">{{ $patient->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Bill Date</label>
                    <input type="date" name="bill_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Discount</label>
                    <input type="number" step="0.01" name="discount" class="form-control" value="0">
                </div>
                <div class="col-12 mb-3">
                    <label>Notes</label>
                    <textarea name="notes" class="form-control" rows="2"></textarea>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between">
                <h5>Line Items</h5>
                <button type="button" class="btn btn-sm btn-primary" id="addLine">Add Line</button>
            </div>
            <div class="card-body" id="lineItems">
                <div class="row line-item mb-2">
                    <div class="col-md-5"><input type="text" name="description[]" class="form-control" placeholder="Description" required></div>
                    <div class="col-md-2"><input type="number" name="quantity[]" class="form-control" value="1" min="1" required></div>
                    <div class="col-md-3"><input type="number" step="0.01" name="unit_price[]" class="form-control" placeholder="Unit price" required></div>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-success">Save Invoice</button>
    </form>
</div>
<script>
document.getElementById('addLine').addEventListener('click', function () {
    const row = document.querySelector('.line-item').cloneNode(true);
    row.querySelectorAll('input').forEach(i => i.value = i.name.includes('quantity') ? '1' : '');
    document.getElementById('lineItems').appendChild(row);
});
</script>
@endsection
