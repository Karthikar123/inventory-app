@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">🧮 Inventory Sync Dashboard</h2>

    <div class="row g-4 mt-4">
        <div class="col-md-3">
            <div class="card text-bg-danger shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Out of Stock</h5>
                    <p class="card-text fs-3">{{ $outOfStockCount }}</p>
                    <a href="{{ route('inventory.out_of_stock') }}" class="btn btn-light btn-sm mt-2">View</a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-bg-warning shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Low Stock (&lt;10)</h5>
                    <p class="card-text fs-3">{{ $lowStockCount }}</p>
                    <a href="{{ route('inventory.low_stock') }}" class="btn btn-light btn-sm mt-2">Review</a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-bg-success shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">High Stock (&gt;100)</h5>
                    <p class="card-text fs-3">{{ $highStockCount }}</p>
                    <a href="{{ route('inventory.high_stock') }}" class="btn btn-light btn-sm mt-2">Check</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Inventory Logs Table --}}
    <div class="card mt-5 shadow-sm">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">📜 Recent Inventory Logs</h5>
            <a href="{{ route('inventory.logs') }}" class="btn btn-sm btn-light">View All Logs</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Action</th>
                            <th>SKU</th>
                            <th>Details</th>
                            <th>Performed By</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inventoryLogs as $log)
                            <tr>
                                <td>{{ ucfirst($log->action) }}</td>
                                <td>{{ $log->sku }}</td>
                                <td>{{ $log->details }}</td>
                                <td>{{ $log->performed_by ?? 'System' }}</td>
                                <td>{{ $log->created_at->format('d M Y, h:i A') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No inventory logs found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
