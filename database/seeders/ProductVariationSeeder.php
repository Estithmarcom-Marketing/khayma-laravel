<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\Color;
use App\Models\Size;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ProductVariationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $colors = Color::all(); // Make sure you have colors in DB
        $sizes = Size::all();   // Make sure you have sizes in DB

        $products = Product::all();

        foreach ($products as $product) {
            for ($i = 1; $i <= 3; $i++) {
                ProductVariation::create([
                    'product_id' => $product->id,
                    'color_id' => $colors->random()->id, // Random color
                    'size_id' => $sizes->random()->id,   // Random size
                    'stock_quantity' => rand(10, 100),   // Random stock
                    'price' => rand(100, 1000),          // Random price
                    'sku' => 'SKU-' . strtoupper(substr($product->slug_en, 0, 3)) . '-' . $i.rand(1000, 9999),
                    'is_active' => true,
                    'offer' => null,
                    'offer_started_date' => null,
                    'offer_expired_date' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }
}