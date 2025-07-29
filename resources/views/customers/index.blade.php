@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>🧑 Customer Details</h2>
    <p>Here is a list of all customers.</p>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Total Orders</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($customers as $customer)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $customer['name'] ?? '-' }}</td>
                    <td>{{ $customer['email'] ?? '-' }}</td>
                    <td>{{ $customer['phone'] ?? '-' }}</td>
                    <td>{{ $customer['orders_count'] ?? 0 }}</td>
                </tr>
            @empty
                <tr><td colspan="5">No customer data found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
