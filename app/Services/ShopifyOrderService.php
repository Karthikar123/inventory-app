<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ShopifyOrderService
{
    protected string $baseUrl;
    protected string $token;

    public function __construct()
    {
        $domain = env('SHOPIFY_STORE_DOMAIN');
        $version = env('SHOPIFY_API_VERSION', '2023-10');
        $this->token = env('SHOPIFY_ACCESS_TOKEN');

        if (!$domain || !$this->token) {
            throw new \Exception("Missing Shopify credentials. Please check your .env settings.");
        }

        $this->baseUrl = "https://{$domain}/admin/api/{$version}";
    }

    public function fetchOrders()
    {
        try {
            $response = Http::withHeaders([
                'X-Shopify-Access-Token' => $this->token,
                'Content-Type' => 'application/json',
            ])->get("{$this->baseUrl}/orders.json");

            if ($response->successful()) {
                $orders = $response->json()['orders'] ?? [];

                return collect($orders)->map(function ($order) {
                    return [
                        'order_number'   => $order['order_number'] ?? null,
                        'customer_name'  => trim(
                            ($order['customer']['first_name'] ?? '') . ' ' .
                            ($order['customer']['last_name'] ?? '')
                        ),
                        'customer_email' => $order['customer']['email'] ?? null,
                        'total_amount'   => $order['total_price'] ?? null,
                        'status'         => $order['financial_status'] ?? null,
                        'created_at'     => $order['created_at'] ?? null,
                    ];
                });
            }

            // Log non-success responses
            Log::error('Shopify Order API Error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
        } catch (\Exception $e) {
            // Log exceptions
            Log::error('Shopify Order Fetch Exception', [
                'error' => $e->getMessage(),
            ]);
        }

        // Always return a collection (empty if error)
        return collect();
    }
}
