@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-warning">🚨 Low Stock Products (≤ 10)</h2>
        <a href="{{ route('inventory.dashboard') }}" class="btn btn-outline-secondary">⬅️ Back to Dashboard</a>
    </div>

    @if($products->count())
    <div class="table-responsive shadow-sm rounded">
        <table class="table table-bordered table-hover bg-white">
            <thead class="table-warning text-center">
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>SKU</th>
                    <th>Quantity</th>
                    <th>Location</th>
                    <th>Price (₹)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($products as $index => $product)
                <tr>
                    <td>{{ $loop->iteration + ($products->currentPage() - 1) * $products->perPage() }}</td>
                    <td>{{ $product->title }}</td>
                    <td>{{ $product->sku ?? 'N/A' }}</td>
                    <td class="text-center fw-bold text-danger">{{ $product->quantity }}</td>
                    <td>{{ $product->location ?? 'N/A' }}</td>
                    <td>₹ {{ number_format($product->price, 2) }}</td>
                    <td class="text-center">
                        <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <form action="{{ route('products.destroy', $product->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-4">
        <div>
            Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} results
        </div>
        <div>
            {{ $products->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
        </div>
    </div>
    @else
        <div class="alert alert-info">No low stock products found.</div>
    @endif
</div>
@endsection
