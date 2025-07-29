@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">🧾 Customer Orders</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($orders->isEmpty())
        <div class="alert alert-info">No orders found.</div>
    @else
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Email</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
    @foreach ($orders as $order)
        <tr>
            <td>#{{ $order['order_number'] }}</td>
            <td>{{ $order['customer_name'] }}</td>
            <td>{{ $order['customer_email'] }}</td>
            <td>₹{{ number_format($order['total_amount'], 2) }}</td>
            <td>
                <span class="badge 
                    {{ $order['status'] === 'completed' ? 'bg-success' : 'bg-warning text-dark' }}">
                    {{ ucfirst($order['status']) }}
                </span>
            </td>
            <td>{{ \Carbon\Carbon::parse($order['created_at'])->format('d M Y, h:i A') }}</td>
            <td>
                <!-- action buttons or links here -->
            </td>
        </tr>
    @endforeach
</tbody>

            </table>
        </div>
    @endif
</div>
@endsection
