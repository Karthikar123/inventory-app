<?php

namespace App\Http\Controllers;

use App\Services\ShopifyOrderService;

class OrderController extends Controller
{
    public function index(ShopifyOrderService $shopifyOrderService)
    {
        $orders = $shopifyOrderService->fetchOrders();

        return view('orders.index', compact('orders'));
    }
}
