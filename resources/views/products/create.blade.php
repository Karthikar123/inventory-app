@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="d-flex align-items-center mb-4">
                <h2 class="fw-bold mb-0 me-2">➕ Add New Product</h2>
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary ms-auto">
                    ← Back to List
                </a>
            </div>

            @if($errors->any())
                <div class="alert alert-danger shadow-sm">
                    <h6 class="mb-2">There were some issues with your input:</h6>
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li class="small">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card shadow-sm rounded-4 border-0">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('products.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold">📝 Title</label>
                            <input type="text" name="title" value="{{ old('title') }}" class="form-control" required placeholder="Enter product title">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">🔢 SKU</label>
                            <input type="text" name="sku" value="{{ old('sku') }}" class="form-control" required placeholder="Enter product SKU">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">📦 Quantity</label>
                            <input type="number" name="quantity" value="{{ old('quantity') }}" class="form-control" required min="0" placeholder="Enter available stock">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">💰 Price (₹)</label>
                            <input type="text" name="price" value="{{ old('price') }}" class="form-control" required placeholder="Enter price in INR">
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-success rounded-pill px-4">
                                ✅ Create
                            </button>
                            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary rounded-pill ms-2 px-4">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
