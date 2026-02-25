<?php

namespace App\Listeners\Product;

use App\Events\Product\ProductStockUpdated;
use App\Models\ProductReminder;
use Illuminate\Support\Facades\Log;

class SendProductBackInStockSms
{
    public function handle(ProductStockUpdated $event): void
    {
        try {
            $product = $event->productVariation->product;
            $product_reminders = ProductReminder::with('user')
                ->where('product_variation_id', $event->productVariation->id)
                ->where('is_notified', false)
                ->get();
            if ($product_reminders) {
                foreach ($product_reminders as $reminder) {
                    $user = $reminder->user;
                    if (! $user) {
                        continue;
                    }
                    Log::info('Product back in stock',
                        ['Phone' => $user->phone,
                            'product_variation_id' => $event->productVariation->id,
                            'product_id' => $product->id,
                        ]);
                    // Send SMS message
                }
            }
        } catch (\Exception $e) {
            Log::error('Product Back In Stock Error', ['error' => $e->getMessage()]);
        }
    }
}
