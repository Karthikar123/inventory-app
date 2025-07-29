@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">🧮 Inventory Sync Dashboard</h2>

    <div class="row g-4">
        <div class="col-md-3">
            <div class="card text-bg-primary shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Total Products</h5>
                    <p class="card-text fs-3">{{ $totalProducts }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-success shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Total Quantity</h5>
                    <p class="card-text fs-3">{{ $totalQuantity }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-info shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Last Sync</h5>
                    <p class="card-text">{{ $lastSync }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 d-grid">
            <a href="{{ route('inventory.sync') }}" class="btn btn-warning btn-lg mt-4">🔄 Sync Now</a>
        </div>
    </div>
</div>
@endsection
