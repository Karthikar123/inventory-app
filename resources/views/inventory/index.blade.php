<!DOCTYPE html>
<html>
<head>
    <title>Inventory Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f7fa;
        }
        .product-image {
            width: 80px;
            height: auto;
            border-radius: 5px;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .filter-bar {
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
        }
        .btn-outline-secondary:hover {
            background-color: #6c757d;
            color: white;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Inventory Dashboard</h2>
    <div class="d-flex gap-2">
        <a href="{{ route('inventory.dashboard') }}" class="btn btn-outline-secondary">
            ⬅️ Back to Dashboard
        </a>
        <a href="{{ route('inventory.sync') }}" class="btn btn-primary">
            🔄 Sync from Shopify
        </a>
    </div>
</div>


    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('inventory.index') }}" class="filter-bar mb-4">
        <input type="text" name="search" placeholder="Search by Title or SKU"
               value="{{ request('search') }}" class="form-control w-auto">
        <select name="location" class="form-select w-auto">
            <option value="">All Locations</option>
            @foreach($locations as $location)
                <option value="{{ $location }}" {{ request('location') == $location ? 'selected' : '' }}>
                    {{ $location }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-outline-primary">🔍 Filter</button>
    </form>

    {{-- Add Product --}}
    <a href="{{ route('products.create') }}" class="btn btn-primary mb-3">➕ Add Product</a>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Table --}}
    <div class="table-responsive shadow-sm rounded">
        <table class="table table-bordered table-hover bg-white">
            <thead class="table-dark text-center">
                <tr>
                    <th><input type="checkbox" id="select-all-products"></th>
                    <th>Image</th>
                    <th>Title</th>
                    <th>SKU</th>
                    <th>Quantity</th>
                    <th>Location</th>
                    <th>Description</th>
                    <th>Price (₹)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($products as $product)
                <tr>
                    <td class="text-center">
                        <input type="checkbox" class="product-checkbox" value="{{ $product->id }}">
                    </td>
                    <td class="text-center">
                        @if($product->image)
                            <img src="{{ $product->image }}" class="product-image" alt="Product Image">
                        @else
                            <span class="text-muted">No image</span>
                        @endif
                    </td>
                    <td>{{ $product->title }}</td>
                    <td>{{ $product->sku ?? 'N/A' }}</td>
                    <td class="text-center">{{ $product->quantity }}</td>
                    <td>{{ $product->location ?? 'N/A' }}</td>
                    <td>{!! Str::limit(strip_tags($product->description), 80) !!}</td>
                    <td>₹ {{ number_format($product->price, 2) }}</td>
                    <td class="text-center">
                        <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-secondary me-1">Edit</a>
                        <form action="{{ route('products.destroy', $product->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this product?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center text-muted">No products found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- Bulk Update Panel --}}
    <div id="bulk-update-panel" style="display:none;" class="card mt-4 p-3 shadow-sm">
        <h5>🔁 Bulk Inventory Update</h5>
        <div class="row">
            <div class="col-md-3">
                <select id="bulk-action-type" class="form-select">
                    <option value="">Select Action</option>
                    <option value="increase">Increase Quantity</option>
                    <option value="decrease">Decrease Quantity</option>
                    <option value="set">Set Exact Quantity</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="number" id="bulk-quantity" class="form-control" placeholder="Enter quantity">
            </div>
            <div class="col-md-3">
                <button id="apply-bulk-update" class="btn btn-success">Apply to Selected</button>
            </div>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div>
            Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} results
        </div>
        <div>
            {{ $products->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

{{-- Bulk Update Script --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const checkboxes = document.querySelectorAll('.product-checkbox');
        const bulkPanel = document.getElementById('bulk-update-panel');
        const selectAll = document.getElementById('select-all-products');

        function toggleBulkPanel() {
            const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
            bulkPanel.style.display = anyChecked ? 'block' : 'none';
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', toggleBulkPanel);
        });

        selectAll.addEventListener('change', function () {
            checkboxes.forEach(cb => cb.checked = this.checked);
            toggleBulkPanel();
        });

        document.getElementById('apply-bulk-update').addEventListener('click', function () {
            const action = document.getElementById('bulk-action-type').value;
            const quantity = parseInt(document.getElementById('bulk-quantity').value);
            const selectedIds = Array.from(checkboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.value);

            if (!action || isNaN(quantity) || selectedIds.length === 0) {
                alert('Please select an action, enter quantity, and select at least one product.');
                return;
            }

            fetch('/inventory/bulk-update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    product_ids: selectedIds,
                    action,
                    quantity
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Bulk update failed. Try again.');
                }
            });
        });
    });
</script>

</body>
</html>
