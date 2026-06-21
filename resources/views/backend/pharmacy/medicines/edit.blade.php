@extends('backend.pharmacy_dashboard')
@section('pharmacy')
<form action="{{ route('pharmacy.medicines.update') }}" method="POST" class="card p-4">
    @csrf
    <input type="hidden" name="id" value="{{ $medicine->id }}">
    <h4>Edit Medicine</h4>
    <div class="row">
        <div class="col-md-6 mb-3"><label>Name</label><input type="text" name="name" class="form-control" value="{{ $medicine->name }}" required></div>
        <div class="col-md-6 mb-3">
            <label>Category</label>
            <select name="medicine_category_id" class="form-control">
                <option value="">None</option>
                @foreach($categories as $cat)<option value="{{ $cat->id }}" @selected($medicine->medicine_category_id == $cat->id)>{{ $cat->name }}</option>@endforeach
            </select>
        </div>
        <div class="col-md-6 mb-3"><label>Unit Price</label><input type="number" step="0.01" name="unit_price" class="form-control" value="{{ $medicine->unit_price }}" required></div>
        <div class="col-md-6 mb-3 form-check mt-4"><input type="checkbox" name="is_active" value="1" class="form-check-input" @checked($medicine->is_active)><label class="form-check-label">Active</label></div>
    </div>
    <button class="btn btn-success">Update</button>
</form>
@endsection
