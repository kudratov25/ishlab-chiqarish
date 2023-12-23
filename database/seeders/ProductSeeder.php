<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Product::create([
            'product_name' => 'Product 1',
            'product_code' => 110,
        ]);

        Product::create([
            'product_name' => 'Product 2',
            'product_code' => 51,
        ]);

        // Add more products as needed
    }
}
