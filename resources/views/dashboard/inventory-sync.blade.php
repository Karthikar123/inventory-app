@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">🧮 Inventory Sync Dashboard</h2>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm text-bg-primary">
                <div class="card-body">
                    <h5>Total Products</h5>
                    <h2>{{ $products->total() }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm text-bg-success">
                <div class="card-body">
                    <h5>Total Quantity</h5>
                    <h2>{{ $products->sum('quantity') }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-4 d-grid">
            <a href="{{ route('inventory.sync') }}" class="btn btn-lg btn-warning mt-4">🔄 Sync from Shopify</a>
        </div>
    </div>

    <div class="table-responsive shadow-sm bg-white rounded p-3">
        <h5 class="mb-3">Recent Products</h5>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>SKU</th>
                    <th>Quantity</th>
                    <th>Location</th>
                    <th>Updated At</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($products as $product)
                <tr>
                    <td>{{ $product->title }}</td>
                    <td>{{ $product->sku }}</td>
                    <td>{{ $product->quantity }}</td>
                    <td>{{ $product->location ?? 'N/A' }}</td>
                    <td>{{ $product->updated_at->diffForHumans() }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="mt-3">
            {{ $products->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
