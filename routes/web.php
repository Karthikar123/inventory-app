<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProductController;

Route::get('/', function () {
    return view('home');
})->name('home');

// About Us page
Route::get('/about', function () {
    return view('about');
})->name('about');



Route::get('/inventory', [ProductController::class, 'index'])->name('inventory.index');
Route::get('/inventory/sync', [ProductController::class, 'fetchShopifyProducts'])->name('inventory.sync');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
Route::post('/products', [ProductController::class, 'store'])->name('products.store');

Route::post('/inventory/bulk-update', [ProductController::class, 'bulkUpdate']);


Route::resource('products', ProductController::class);
Route::get('/inventory', [ProductController::class, 'index'])->name('inventory.index');

Route::get('/inventory/dashboard', [ProductController::class, 'dashboard'])->name('inventory.dashboard');
Route::get('/inventory/sync', [ProductController::class, 'fetchShopifyProducts'])->name('inventory.sync');


use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;

Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');



Route::get('/inventory/low-stock', [ProductController::class, 'lowStock'])->name('inventory.low_stock');

Route::get('/inventory/out-of-stock', [ProductController::class, 'outOfStock'])->name('inventory.out_of_stock');
Route::get('/inventory/high-stock', [ProductController::class, 'highStock'])->name('inventory.high_stock');

use App\Http\Controllers\InventoryLogController;


Route::get('/inventory/logs', [InventoryLogController::class, 'index'])->name('inventory.logs');




Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');







use Illuminate\Support\Facades\Mail;
use App\Mail\LowInventoryAlertMail;

Route::get('/test-low-inventory-mail', function () {
    $variant = [
        'product_title' => 'Test Product',
        'title' => 'Size M',
        'sku' => 'TEST-SKU',
        'inventory_quantity' => 18
    ];

    Mail::to('karthyka2k24@gmail.com')->send(new \App\Mail\LowInventoryAlertMail($variant));

    return "Low inventory test email sent!";
});
