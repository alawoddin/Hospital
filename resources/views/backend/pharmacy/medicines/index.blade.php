@extends('backend.pharmacy_dashboard')
@section('pharmacy')
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h4>Medicines</h4>
        <a href="{{ route('pharmacy.medicines.add') }}" class="btn btn-primary">Add Medicine</a>
    </div>
    <div class="card-body table-responsive">
        <table class="table">
            <thead><tr><th>Name</th><th>Category</th><th>Unit Price</th><th>Stock</th><th>Actions</th></tr></thead>
            <tbody>
                @foreach($medicines as $medicine)
                <tr>
                    <td>{{ $medicine->name }}</td>
                    <td>{{ $medicine->category->name ?? 'N/A' }}</td>
                    <td>${{ number_format($medicine->unit_price, 2) }}</td>
                    <td>{{ $medicine->totalStock() }}</td>
                    <td>
                        <a href="{{ route('pharmacy.medicines.edit', $medicine->id) }}" class="btn btn-sm btn-info">Edit</a>
                        <a href="{{ route('pharmacy.medicines.stock', $medicine->id) }}" class="btn btn-sm btn-success">Add Stock</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
