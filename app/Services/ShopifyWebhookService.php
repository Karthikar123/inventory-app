<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ShopifyWebhookService
{
    protected $shopDomain;
    protected $accessToken;

    public function __construct($shopDomain, $accessToken)
    {
        $this->shopDomain = $shopDomain;
        $this->accessToken = $accessToken;
    }

    public function registerWebhooks()
    {
        $webhooks = [
            [
                'topic' => 'products/update',
                'address' => 'https://your-laravel-server.com/webhook/products-update',
            ],
            [
                'topic' => 'inventory_levels/update',
                'address' => 'https://your-laravel-server.com/webhook/inventory-update',
            ],
        ];

        foreach ($webhooks as $webhook) {
            $this->registerWebhook($webhook['topic'], $webhook['address']);
        }
    }

    private function registerWebhook($topic, $address)
    {
        $response = Http::withHeaders([
            'X-Shopify-Access-Token' => $this->accessToken,
            'Content-Type' => 'application/json',
        ])->post("https://{$this->shopDomain}/admin/api/2023-10/webhooks.json", [
            'webhook' => [
                'topic' => $topic,
                'address' => $address,
                'format' => 'json',
            ],
        ]);

        if ($response->successful()) {
            Log::info("✅ Webhook registered for {$topic}");
        } else {
            Log::error("❌ Failed to register webhook for {$topic}: " . $response->body());
        }
    }
}
