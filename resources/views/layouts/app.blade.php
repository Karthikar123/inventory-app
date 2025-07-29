<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory App</title>

    {{-- ✅ Bootstrap 5 CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Optional: Google Fonts or custom styling --}}
    <style>
        body {
            background-color: #f8fafc;
            font-family: 'Segoe UI', sans-serif;
        }
        .sidebar {
            height: 100vh;
            background-color: #ffffff;
            border-right: 1px solid #e0e0e0;
        }
        .sidebar .nav-link {
            color: #333;
        }
        .sidebar .nav-link.active {
            background-color: #e9ecef;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            {{-- Sidebar --}}
            <div class="col-md-3 col-lg-2 sidebar p-3">
                <h5 class="mb-4">📊 Admin Menu</h5>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="{{ route('inventory.index') }}" class="nav-link">
                            📦 Product Inventory
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('inventory.dashboard') }}" class="nav-link">
                            🧮 Inventory Sync Dashboard
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Main Content --}}
            <div class="col-md-9 col-lg-10 py-4">
                @yield('content')
            </div>
        </div>
    </div>

    {{-- ✅ Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
