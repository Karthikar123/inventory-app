@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-body p-5">
            <h1 class="text-primary fw-bold mb-4">🚀 About Emvigotech Inventory Dashboard</h1>

            <p class="lead text-secondary">
                <strong>Emvigotech Inventory App</strong> is a modern, intuitive dashboard built for Shopify merchants to efficiently track and manage their inventory across multiple locations.
                Whether you're scaling up or streamlining, our app provides the tools you need to stay in control.
            </p>

            <div class="row mt-4">
                <div class="col-md-6">
                    <h4 class="text-success mb-3">✨ Key Features</h4>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">✅ Real-time Inventory Sync with Shopify</li>
                        <li class="list-group-item">📦 Multi-Warehouse Location Support</li>
                        <li class="list-group-item">📊 Dashboard with Inventory Insights</li>
                        <li class="list-group-item">👥 Customer & Order Management</li>
                        <li class="list-group-item">📝 Activity Logs for Every Sync</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h4 class="text-info mb-3">💡 Why Choose Us?</h4>
                    <p>
                        Developed by <strong>Emvigotech</strong>, our Laravel-powered platform is optimized for performance, scalability, and user experience.
                        We blend cutting-edge technology with practical business needs to deliver an inventory solution you can trust.
                    </p>
                    <a href="{{ url('/') }}" class="btn btn-outline-primary mt-3">Return to Dashboard</a>
                </div>
            </div>

            <hr class="my-5">

            <div class="text-center text-muted">
                <small>© {{ now()->year }} Emvigotech. All rights reserved.</small>
            </div>
        </div>
    </div>
</div>
@endsection
