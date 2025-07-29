<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Models\Product;
use App\Mail\LowInventoryAlertMail;

class ShopifyService
{
    protected $shop;
    protected $token;
    protected $apiVersion;

    public function __construct()
    {
        $this->shop = config('services.shopify.domain');
        $this->token = config('services.shopify.access_token');
        $this->apiVersion = config('services.shopify.version');

        if (empty($this->shop)) {
            throw new \Exception('Shopify domain not configured correctly.');
        }
    }

    protected function shopifyGet($endpoint, $params = [])
    {
        $url = "https://{$this->shop}/admin/api/{$this->apiVersion}/{$endpoint}";

        return Http::withHeaders([
            'X-Shopify-Access-Token' => $this->token,
            'Accept' => 'application/json'
        ])->get($url, $params);
    }

    protected function shopifyPost($endpoint, $payload)
    {
        $url = "https://{$this->shop}/admin/api/{$this->apiVersion}/{$endpoint}";

        return Http::withHeaders([
            'X-Shopify-Access-Token' => $this->token,
            'Content-Type' => 'application/json'
        ])->post($url, $payload);
    }

    public function syncProducts()
    {
        $response = $this->shopifyGet('products.json');

        if ($response->failed()) {
            logger()->error('Failed to fetch Shopify products', ['response' => $response->body()]);
            return false;
        }

        $products = $response->json()['products'] ?? [];

        foreach ($products as $product) {
            $variant = $product['variants'][0] ?? null;
            if (!$variant) continue;

            $inventoryItemId = $variant['inventory_item_id'];
            $quantity = $variant['inventory_quantity'];
            $location = 'Default Location';

            $invResponse = $this->shopifyGet('inventory_levels.json', [
                'inventory_item_ids' => $inventoryItemId
            ]);

            if ($invResponse->ok()) {
                $levels = $invResponse->json()['inventory_levels'] ?? [];
                if (!empty($levels)) {
                    $quantity = $levels[0]['available'] ?? $quantity;
                    $locationId = $levels[0]['location_id'] ?? null;

                    if ($locationId) {
                        $locResp = $this->shopifyGet("locations/{$locationId}.json");
                        if ($locResp->ok()) {
                            $location = $locResp->json()['location']['name'] ?? $location;
                        }
                    }
                }
            }

            // 📧 Check and alert on low inventory
            $this->checkInventoryLevel([
                'product_title' => $product['title'],
                'title' => $variant['title'],
                'sku' => $variant['sku'],
                'inventory_quantity' => $quantity
            ]);

            // 🗃️ Store/update locally
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
        $productResponse = $this->shopifyGet("products/{$shopifyProductId}.json");

        if ($productResponse->failed()) return false;

        $product = $productResponse->json()['product'] ?? null;
        $variant = $product['variants'][0] ?? null;

        if (!$variant) return false;

        $inventoryItemId = $variant['inventory_item_id'];

        $invLevelResponse = $this->shopifyGet('inventory_levels.json', [
            'inventory_item_ids' => $inventoryItemId
        ]);

        if ($invLevelResponse->failed()) return false;

        $levels = $invLevelResponse->json()['inventory_levels'] ?? [];
        $locationId = $levels[0]['location_id'] ?? null;

        if (!$locationId) return false;

        $setResponse = $this->shopifyPost('inventory_levels/set.json', [
            'location_id' => $locationId,
            'inventory_item_id' => $inventoryItemId,
            'available' => (int) $newQuantity
        ]);

        return $setResponse->ok();
    }

    /**
     * Send email alert if inventory is low.
     */
    protected function checkInventoryLevel($variant)
    {
        if ($variant['inventory_quantity'] <= 20) {
            Mail::to('support@yourcompany.com') // Change to real internal email
                ->send(new LowInventoryAlertMail($variant));
        }
    }
}
