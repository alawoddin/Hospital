@extends('backend.pharmacy_dashboard')
@section('pharmacy')
<form action="{{ route('pharmacy.medicines.store') }}" method="POST" class="card p-4">
    @csrf
    <h4>Add Medicine</h4>
    <div class="row">
        <div class="col-md-6 mb-3"><label>Name</label><input type="text" name="name" class="form-control" required></div>
        <div class="col-md-6 mb-3">
            <label>Category</label>
            <select name="medicine_category_id" class="form-control">
                <option value="">None</option>
                @foreach($categories as $cat)<option value="{{ $cat->id }}">{{ $cat->name }}</option>@endforeach
            </select>
        </div>
        <div class="col-md-6 mb-3"><label>Generic Name</label><input type="text" name="generic_name" class="form-control"></div>
        <div class="col-md-6 mb-3"><label>Unit Price</label><input type="number" step="0.01" name="unit_price" class="form-control" required></div>
        <div class="col-md-4 mb-3"><label>Initial Stock</label><input type="number" name="quantity" class="form-control" min="0"></div>
        <div class="col-md-4 mb-3"><label>Batch No</label><input type="text" name="batch_no" class="form-control"></div>
        <div class="col-md-4 mb-3"><label>Expiry Date</label><input type="date" name="expiry_date" class="form-control"></div>
        <div class="col-12 mb-3"><label>Description</label><textarea name="description" class="form-control"></textarea></div>
    </div>
    <button class="btn btn-success">Save</button>
</form>
@endsection
