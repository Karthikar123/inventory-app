@extends('layouts.app')

@section('content')
<div class="container">
    <h2>📒 Inventory Logs</h2>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Date</th>
                <th>Product ID</th>
                <th>SKU</th>
                <th>Action</th>
                <th>Details</th>
                <th>Performed By</th>
                <th>Source</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
            <tr>
                <td>{{ $log->created_at->format('d M Y H:i') }}</td>
                <td>{{ $log->product_id }}</td>
                <td>{{ $log->sku }}</td>
                <td>{{ ucfirst($log->action) }}</td>
                <td>{{ $log->details }}</td>
                <td>{{ $log->performed_by }}</td>
                <td>{{ $log->source }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $logs->links() }}
</div>
@endsection
