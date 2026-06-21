@extends('backend.pharmacy_dashboard')
@section('pharmacy')
<form action="{{ route('pharmacy.medicines.stock.store', $medicine->id) }}" method="POST" class="card p-4">
    @csrf
    <h4>Add Stock - {{ $medicine->name }}</h4>
    <div class="row">
        <div class="col-md-3 mb-3"><label>Quantity</label><input type="number" name="quantity" class="form-control" min="1" required></div>
        <div class="col-md-3 mb-3"><label>Batch No</label><input type="text" name="batch_no" class="form-control"></div>
        <div class="col-md-3 mb-3"><label>Expiry Date</label><input type="date" name="expiry_date" class="form-control"></div>
        <div class="col-md-3 mb-3"><label>Purchase Price</label><input type="number" step="0.01" name="purchase_price" class="form-control"></div>
    </div>
    <button class="btn btn-success">Add Stock</button>
</form>
@endsection
