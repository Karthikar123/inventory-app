<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        // Fetch customer data from DB or Shopify
        $customers = []; // Placeholder for now
        return view('customers.index', compact('customers'));
    }
}
