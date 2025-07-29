<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Product;

class SyncShopifyInventory extends Command
{
    protected $signature = 'shopify:sync-inventory';
    protected $description = 'Sync inventory from Shopify API to products table';

    public function handle()
    {
        $this->info('🔄 Syncing products from Shopify...');

        $shop = env('SHOPIFY_SHOP');
        $token = env('SHOPIFY_ACCESS_TOKEN');

        // Step 1: Get all products
        $response = Http::withHeaders([
            'X-Shopify-Access-Token' => $token
        ])->get("https://{$shop}/admin/api/2023-10/products.json");

        if ($response->failed()) {
            $this->error('❌ Failed to fetch products from Shopify.');
            return;
        }

        $products = $response->json()['products'];

        foreach ($products as $product) {
            $variant = $product['variants'][0];
            $inventoryItemId = $variant['inventory_item_id'];

            // Step 2: Get inventory levels
            $inventoryResponse = Http::withHeaders([
                'X-Shopify-Access-Token' => $token
            ])->get("https://{$shop}/admin/api/2023-10/inventory_levels.json", [
                'inventory_item_ids' => $inventoryItemId
            ]);

            $quantity = $variant['inventory_quantity'];
            $location = 'Default Location';

            if ($inventoryResponse->ok()) {
                $inventoryLevels = $inventoryResponse->json()['inventory_levels'];
                if (!empty($inventoryLevels)) {
                    $quantity = $inventoryLevels[0]['available'] ?? $quantity;

                    // Optional: Fetch location name if you want actual name
                    $locationId = $inventoryLevels[0]['location_id'] ?? null;

                    if ($locationId) {
                        $locationResp = Http::withHeaders([
                            'X-Shopify-Access-Token' => $token
                        ])->get("https://{$shop}/admin/api/2023-10/locations/{$locationId}.json");

                        if ($locationResp->ok()) {
                            $location = $locationResp->json()['location']['name'];
                        }
                    }
                }
            }

            // Step 3: Update or create product in DB
            Product::updateOrCreate(
                ['shopify_id' => $product['id']],
                [
                    'title' => $product['title'],
                    'sku' => $variant['sku'],
                    'quantity' => $quantity,
                    'location' => $location,
                    'description' => $product['body_html'],
                    'price' => $variant['price'],
                    'image' => $product['image']['src'] ?? null,
                ]
            );
        }

        $this->info('✅ Inventory synced successfully!');
    }
}
