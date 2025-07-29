<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\InventoryLog;
use App\Services\ShopifyService;
use Illuminate\Support\Facades\Mail;


class ProductController extends Controller
{
    protected $shopifyService;

    public function __construct(ShopifyService $shopifyService)
    {
        $this->shopifyService = $shopifyService;
    }

    // 📦 1. List all products with optional filters (search, location)
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('title', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
        }

        if ($request->filled('location')) {
            $query->where('location', $request->input('location'));
        }

        if ($request->ajax()) {
            return response()->json($query->get());
        }

        $products = $query->paginate(10);
        $locations = Product::select('location')->distinct()->pluck('location');

        return view('inventory.index', compact('products', 'locations'));
    }

    // 🆕 2. Show form to create a new product
    public function create()
    {
        return view('products.create');
    }

    // 💾 3. Store a new product and log the creation
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:products',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
        ]);

        $product = Product::create([
            'title' => $request->title,
            'sku' => $request->sku,
            'quantity' => $request->quantity,
            'price' => $request->price,
            'shopify_id' => uniqid(),
        ]);

        InventoryLog::create([
            'product_id' => $product->id,
            'sku' => $product->sku,
            'action' => 'create',
            'details' => 'Product created with quantity: ' . $product->quantity,
            'performed_by' => auth()->user()->name ?? 'system',
            'source' => 'Manual Create'
        ]);

        return redirect()->route('inventory.index')->with('success', 'Product created successfully!');
    }

    // ✏️ 4. Show form to edit a product
    public function edit(Product $product)
    {
        return view('inventory.edit', compact('product'));
    }

    // 🔄 5. Update an existing product and log the update
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'sku' => 'required|string|max:255',
            'quantity' => 'required|integer',
            'price' => 'required|numeric',
        ]);

        $oldQty = $product->quantity;
        $product->update($validated);
        $newQty = $product->quantity;

        InventoryLog::create([
            'product_id' => $product->id,
            'sku' => $product->sku,
            'action' => 'update',
            'details' => 'Quantity updated from ' . $oldQty . ' to ' . $newQty,
            'performed_by' => auth()->user()->name ?? 'system',
            'source' => 'Manual Update'
        ]);

        return redirect()->route('inventory.index')->with('success', 'Product updated successfully!');
    }

    // ❌ 6. Delete a product and log the deletion
    public function destroy(Product $product)
    {
        InventoryLog::create([
            'product_id' => $product->id,
            'sku' => $product->sku,
            'action' => 'delete',
            'details' => 'Product deleted',
            'performed_by' => auth()->user()->name ?? 'system',
            'source' => 'Manual Delete'
        ]);

        $product->delete();

        return redirect()->route('inventory.index')->with('success', 'Product deleted successfully!');
    }

   // 🔁 7. Sync products from Shopify into local DB and send low inventory alerts
public function fetchShopifyProducts(ShopifyService $shopifyService)
{
    $products = $shopifyService->getProducts(); // You need this to return product data

    foreach ($products as $productData) {
        foreach ($productData['variants'] as $variant) {
            $existingProduct = Product::updateOrCreate(
                ['shopify_id' => $variant['id']],
                [
                    'title' => $productData['title'] . ' - ' . $variant['title'],
                    'sku' => $variant['sku'],
                    'quantity' => $variant['inventory_quantity'],
                    'price' => $variant['price'],
                    'shopify_id' => $variant['id'],
                    'location' => 'Shopify'
                ]
            );

            // Send low inventory email if below threshold
            if ($variant['inventory_quantity'] < 20) {
                \Mail::to('your-team@example.com')->send(
                    new \App\Mail\LowInventoryAlertMail([
                        'product_title' => $productData['title'],
                        'title' => $variant['title'],
                        'sku' => $variant['sku'],
                        'inventory_quantity' => $variant['inventory_quantity'],
                    ])
                );
            }
        }
    }

    return redirect()->route('inventory.index')->with('success', 'Shopify products synced and alerts sent successfully.');
}


    // 🔄 8. Bulk update product quantities (increase, decrease, set)
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array',
            'action' => 'required|in:increase,decrease,set',
            'quantity' => 'required|integer|min:0',
        ]);

        $products = Product::whereIn('id', $request->product_ids)->get();

        foreach ($products as $product) {
            $oldQty = $product->quantity;

            if ($request->action === 'increase') {
                $product->quantity += $request->quantity;
            } elseif ($request->action === 'decrease') {
                $product->quantity = max(0, $product->quantity - $request->quantity);
            } elseif ($request->action === 'set') {
                $product->quantity = $request->quantity;
            }

            $product->save();

            InventoryLog::create([
                'product_id' => $product->id,
                'sku' => $product->sku,
                'action' => 'bulk_update',
                'details' => "Quantity {$request->action}d from $oldQty to {$product->quantity}",
                'performed_by' => auth()->user()->name ?? 'system',
                'source' => 'Bulk Update'
            ]);

            if ($product->shopify_id) {
                $this->shopifyService->updateShopifyInventory($product->shopify_id, $product->quantity);
            }
        }

        return response()->json(['success' => true]);
    }

    // 📊 9. Inventory dashboard (stock stats + recent syncs)
    public function dashboard()
{
    $totalProducts = Product::count();
    $totalQuantity = Product::sum('quantity');
    $lastSync = now()->format('d M Y, h:i A');

    $lowStockCount = Product::where('quantity', '<', 10)->count();
    $outOfStockCount = Product::where('quantity', 0)->count();
    $highStockCount = Product::where('quantity', '>', 100)->count();
    $recentlySynced = Product::latest()->take(10)->get();

    $inventoryLogs = \App\Models\InventoryLog::latest()->take(5)->get();

    return view('inventory.dashboard', compact(
        'totalProducts',
        'totalQuantity',
        'lastSync',
        'lowStockCount',
        'outOfStockCount',
        'highStockCount',
        'recentlySynced',
        'inventoryLogs'
    ));
}


    // ⚠️ 10. List products with low stock
    public function lowStock()
    {
        $lowStockThreshold = 10;
        $products = Product::where('quantity', '<', $lowStockThreshold)->paginate(10);
        return view('inventory.low_stock', compact('products'));
    }

    // 🛑 11. List products that are out of stock
    public function outOfStock()
    {
        $products = Product::where('quantity', 0)->paginate(10);
        return view('inventory.out_of_stock', compact('products'));
    }

    // 📈 12. List products with high stock
    public function highStock()
    {
        $products = Product::where('quantity', '>', 100)->paginate(10);
        return view('inventory.high_stock', compact('products'));
    }
}
