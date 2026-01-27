<?php

namespace App\Listeners\Product;

use App\Enums\Enums\Notification\NotificationTypeEnum;
use App\Events\Product\ProductStockUpdated;
use App\Models\Notification;
use App\Models\Product;
use App\Models\ProductReminder;
use Illuminate\Support\Facades\Log;

class StoreProductBackInStockNotification
{
    public function handle(ProductStockUpdated $event): void
    {
        try {
            $product = $event->productVariation->product;
            $product_reminders = ProductReminder::with('user')
                ->where('product_variation_id', $event->productVariation->id)
                ->where('is_notified', false)
                ->get();
            foreach ($product_reminders as $reminder) {
                $user = $reminder->user;
                if (! $user) {
                    continue;
                }
                Notification::create([
                    'user_id' => $user->id,
                    'type' => NotificationTypeEnum::PRODUCT_BACK_IN_STOCK,
                    'notifiable_id' => $product->id,
                    'notifiable_type' => Product::class,
                    'is_read' => false,
                    'title' => __('product.notification_title'),
                    'body' => __('product.notification_body'),
                ]);
                $reminder->update(['is_notified' => true]);

                Log::info(__('product.notification_body'), ['product_id' => $product->id]);
            }
        } catch (\Exception $e) {
            Log::error('Product Back In Stock Error', ['error' => $e->getMessage()]);
        }

    }
}
