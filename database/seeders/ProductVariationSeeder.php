<?php

namespace Database\Seeders;

use App\Models\Color;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\Size;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ProductVariationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $colors = Color::all();
        $sizes = Size::all();

        $products = Product::all();

        foreach ($products as $product) {
            $max = rand(1, 5); // Each product can have between 1 to 5 variations
            for ($i = 1; $i <= $max; $i++) {
                ProductVariation::create([
                    'product_id' => $product->id,
                    'color_id' => $colors->random()->id,
                    'size_id' => $sizes->random()->id,
                    'stock_quantity' => rand(10, 100),
                    'price' => rand(100, 1000),
                    'sku' => 'SKU-'.strtoupper(substr($product->slug_en, 0, 3)).'-'.$i.rand(1, 99999999),
                    'is_active' => true,
                    'offer' => rand(0, 60),
                    'offer_started_date' => Carbon::now()->subDays(rand(1, 30)),
                    'offer_expired_date' => Carbon::now()->addDays(rand(7, 30)),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }
}
