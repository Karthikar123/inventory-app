<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Product;

class ShopifyWebhookController extends Controller
{
    public function handleProductUpdate(Request $request)
    {
        $data = $request->all();

        // Log raw data for debugging
        Log::info('Product Update Webhook', $data);

        $product = Product::where('shopify_product_id', $data['id'])->first();

        if ($product) {
            $product->title = $data['title'];
            $product->sku = $data['variants'][0]['sku'] ?? null;
            $product->save();
        }

        return response()->json(['success' => true]);
    }

    public function handleInventoryUpdate(Request $request)
    {
        $data = $request->all();

        Log::info('Inventory Update Webhook', $data);

        $inventory_item_id = $data['inventory_item_id'] ?? null;
        $available = $data['available'] ?? null;

        if ($inventory_item_id && $available !== null) {
            $product = Product::where('inventory_item_id', $inventory_item_id)->first();

            if ($product) {
                $product->inventory_quantity = $available;
                $product->save();
            }
        }

        return response()->json(['success' => true]);
    }
}
