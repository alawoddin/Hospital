@extends('backend.pharmacy_dashboard')
@section('pharmacy')
<div class="row">
    <div class="col-md-5">
        <form action="{{ route('pharmacy.categories.store') }}" method="POST" class="card p-3 mb-3">
            @csrf
            <h5>Add Category</h5>
            <input type="text" name="name" class="form-control mb-2" placeholder="Category name" required>
            <textarea name="description" class="form-control mb-2" placeholder="Description"></textarea>
            <button class="btn btn-primary">Save</button>
        </form>
    </div>
    <div class="col-md-7">
        <div class="card p-3">
            <h5>Categories</h5>
            <table class="table">
                <thead><tr><th>Name</th><th>Medicines</th><th>Description</th></tr></thead>
                <tbody>
                    @foreach($categories as $category)
                    <tr>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->medicines_count }}</td>
                        <td>{{ $category->description }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
