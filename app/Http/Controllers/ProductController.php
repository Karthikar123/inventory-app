<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Services\ShopifyService;

class ProductController extends Controller
{
    protected $shopifyService;

    public function __construct(ShopifyService $shopifyService)
    {
        $this->shopifyService = $shopifyService;
    }

    // 📦 1. List all products with filters
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('title', 'like', "%$search%")
                  ->orWhere('sku', 'like', "%$search%");
        }

        if ($request->filled('location')) {
            $query->where('location', $request->input('location'));
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

    // 💾 3. Store new product
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:products',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
        ]);

        Product::create([
            'title' => $request->title,
            'sku' => $request->sku,
            'quantity' => $request->quantity,
            'price' => $request->price,
            'shopify_id' => uniqid(), // or use null if nullable
        ]);

        return redirect()->route('inventory.index')->with('success', 'Product created successfully!');
    }

    // ✏️ 4. Show form to edit product
    public function edit(Product $product)
    {
        return view('inventory.edit', compact('product'));
    }

    // 🔄 5. Update product
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'sku' => 'required|string|max:255',
            'quantity' => 'required|integer',
            'price' => 'required|numeric',
        ]);

        $product->update($validated);

        // ✅ Fix: changed to correct route
        return redirect()->route('inventory.index')->with('success', 'Product updated successfully!');
    }

    // ❌ 6. Delete product
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('inventory.index')->with('success', 'Product deleted successfully!');
    }

    // 🔁 7. Sync Shopify products
    public function fetchShopifyProducts(ShopifyService $shopifyService)
    {
        $success = $shopifyService->syncProducts();

        if (!$success) {
            return redirect()->route('inventory.index')->with('error', 'Failed to fetch products from Shopify.');
        }

        return redirect()->route('inventory.index')->with('success', 'Shopify products synced successfully.');
    }

    // 📦 8. Bulk update quantities
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array',
            'action' => 'required|in:increase,decrease,set',
            'quantity' => 'required|integer|min:0',
        ]);

        $products = Product::whereIn('id', $request->product_ids)->get();

        foreach ($products as $product) {
            if ($request->action === 'increase') {
                $product->quantity += $request->quantity;
            } elseif ($request->action === 'decrease') {
                $product->quantity = max(0, $product->quantity - $request->quantity);
            } elseif ($request->action === 'set') {
                $product->quantity = $request->quantity;
            }
            $product->save();

            // ✅ Sync with Shopify after update
            if ($product->shopify_id) {
                $this->shopifyService->updateShopifyInventory($product->shopify_id, $product->quantity);
            }
        }

        return response()->json(['success' => true]);
    }

    public function dashboard()
    {
        $products = Product::latest()->paginate(10);
        $locations = Product::select('location')->distinct()->pluck('location');

        return view('dashboard.inventory-sync', compact('products', 'locations'));
    }
}
