<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProductController;


Route::get('/', function () {
    return redirect('/inventory');
});

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


