@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>📦 EmvigoTech Inventory Dashboard</h1>
        <a href="{{ route('inventory.index') }}" class="btn btn-primary shadow-sm">
            Go to Inventory
        </a>
    </div>

    <!-- Summary Cards -->
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 bg-light">
                <div class="card-body">
                    <h5 class="card-title">🛒 Total Products</h5>
                    <h3 class="fw-bold">{{ \App\Models\Product::count() }}</h3>
                    <p class="text-muted">All active items</p>
                    <a href="{{ route('inventory.index') }}" class="btn btn-outline-primary btn-sm">View Products</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 bg-light">
                <div class="card-body">
                    <h5 class="card-title">📦 Total Quantity</h5>
                    <h3 class="fw-bold">{{ \App\Models\Product::sum('quantity') }}</h3>
                    <p class="text-muted">Combined stock count</p>
                    <a href="{{ route('inventory.index') }}" class="btn btn-outline-success btn-sm">Manage Stock</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 bg-light">
                <div class="card-body">
                    <h5 class="card-title">⚠️ Low Stock Items</h5>
                    <h3 class="fw-bold">{{ \App\Models\Product::where('quantity', '<=', 5)->count() }}</h3>
                    <p class="text-muted">Items ≤ 5 units</p>
<a href="{{ route('inventory.low_stock') }}" class="btn btn-outline-danger btn-sm">Review Now</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
