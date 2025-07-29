<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Product;

class ShopifyService
{
    public function syncProducts()
    {
        $shop = env('SHOPIFY_STORE_DOMAIN');
        $token = env('SHOPIFY_ACCESS_TOKEN');
        $apiVersion = env('SHOPIFY_API_VERSION', '2023-10');

        $response = Http::withHeaders([
            'X-Shopify-Access-Token' => $token
        ])->get("https://{$shop}/admin/api/{$apiVersion}/products.json");

        if ($response->failed()) {
            return false;
        }

        $products = $response->json()['products'];

        foreach ($products as $product) {
            $variant = $product['variants'][0];
            $inventoryItemId = $variant['inventory_item_id'];
            $quantity = $variant['inventory_quantity'];
            $location = 'Default Location';

            // Get inventory level
            $invResponse = Http::withHeaders([
                'X-Shopify-Access-Token' => $token
            ])->get("https://{$shop}/admin/api/{$apiVersion}/inventory_levels.json", [
                'inventory_item_ids' => $inventoryItemId
            ]);

            if ($invResponse->ok()) {
                $levels = $invResponse->json()['inventory_levels'] ?? [];
                if (!empty($levels)) {
                    $quantity = $levels[0]['available'] ?? $quantity;

                    $locationId = $levels[0]['location_id'] ?? null;
                    if ($locationId) {
                        $locResp = Http::withHeaders([
                            'X-Shopify-Access-Token' => $token
                        ])->get("https://{$shop}/admin/api/{$apiVersion}/locations/{$locationId}.json");

                        if ($locResp->ok()) {
                            $location = $locResp->json()['location']['name'];
                        }
                    }
                }
            }

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

        return true;
    }

   
    public function updateShopifyInventory($shopifyProductId, $newQuantity)
{
    $shop = env('SHOPIFY_STORE_DOMAIN');
    $token = env('SHOPIFY_ACCESS_TOKEN');
    $apiVersion = env('SHOPIFY_API_VERSION', '2023-10');

    // Step 1: Get product details to extract variant ID
    $productResponse = Http::withHeaders([
        'X-Shopify-Access-Token' => $token
    ])->get("https://{$shop}/admin/api/{$apiVersion}/products/{$shopifyProductId}.json");

    if ($productResponse->failed()) {
        return false;
    }

    $product = $productResponse->json()['product'];
    $variant = $product['variants'][0] ?? null;

    if (!$variant) {
        return false;
    }

    $inventoryItemId = $variant['inventory_item_id'];

    // Step 2: Get Inventory Level to find location ID
    $invLevelResponse = Http::withHeaders([
        'X-Shopify-Access-Token' => $token
    ])->get("https://{$shop}/admin/api/{$apiVersion}/inventory_levels.json", [
        'inventory_item_ids' => $inventoryItemId
    ]);

    if ($invLevelResponse->failed()) {
        return false;
    }

    $levels = $invLevelResponse->json()['inventory_levels'] ?? [];
    $locationId = $levels[0]['location_id'] ?? null;

    if (!$locationId) {
        return false;
    }

    // Step 3: Set the new quantity using inventory_levels/set.json
    $setResponse = Http::withHeaders([
        'X-Shopify-Access-Token' => $token,
        'Content-Type' => 'application/json'
    ])->post("https://{$shop}/admin/api/{$apiVersion}/inventory_levels/set.json", [
        'location_id' => $locationId,
        'inventory_item_id' => $inventoryItemId,
        'available' => (int)$newQuantity
    ]);

    return $setResponse->ok();
}



}
