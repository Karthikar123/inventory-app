<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'title' => 'Sample Product 1',
            'sku' => 'SKU123',
            'quantity' => 10,
            'location' => 'Chennai'
        ]);

        Product::create([
            'title' => 'Sample Product 2',
            'sku' => 'SKU124',
            'quantity' => 5,
            'location' => 'Mumbai'
        ]);
    }
}
