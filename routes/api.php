<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShopifyWebhookController;

Route::post('/shopify/webhook/products-update', [ShopifyWebhookController::class, 'handleProductUpdate']);
Route::post('/shopify/webhook/inventory-update', [ShopifyWebhookController::class, 'handleInventoryUpdate']);
