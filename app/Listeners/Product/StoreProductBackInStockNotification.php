<?php

namespace App\Listeners\Product;

use App\Enums\Enums\Notification\NotificationTypeEnum;
use App\Events\Product\ProductStockUpdated;
use App\Models\Notification;
use App\Models\Product;
use App\Models\ProductReminder;
use App\Services\V1\Admin\Firebase\FcmService;
use Illuminate\Support\Facades\Log;

class StoreProductBackInStockNotification
{
    public function __construct(protected FcmService $fcm) {}

    public function handle(ProductStockUpdated $event): void
    {
        $product = $event->productVariation->product;

        try {
            $reminders = $this->getPendingReminders($event->productVariation->id);

            foreach ($reminders as $reminder) {
                if (!$reminder->user) {
                    continue;
                }

                $notification = $this->createNotification($reminder->user->id, $product->id);

                $this->sendNotification($reminder->user, $notification, $product);

                $reminder->update(['is_notified' => true]);

                Log::info("Product #{$product->id} back in stock notification sent", [
                    'product_id' => $product->id,
                    'user_id' => $reminder->user->id,
                    'notification_id' => $notification->id,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Product Back In Stock Notification Error', [
                'product_id' => $product->id ?? null,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function getPendingReminders(int $variationId)
    {
        return ProductReminder::with('user')
            ->where('product_variation_id', $variationId)
            ->where('is_notified', false)
            ->get();
    }

    private function createNotification(int $userId, int $productId): Notification
    {
        return Notification::create([
            'user_id' => $userId,
            'type' => NotificationTypeEnum::PRODUCT_BACK_IN_STOCK,
            'notifiable_id' => $productId,
            'notifiable_type' => Product::class,
            'is_read' => false,
            'title_en' => 'Product back in stock',
            'title_ar' => 'المنتج متوفر مرة أخرى',
            'body_ar' => 'المنتج الذي كنت تنتظره عاد للمخزن',
            'body_en' => 'The product you were waiting for is back in stock',
        ]);
    }

    private function sendNotification($user, Notification $notification, Product $product): void
    {
        $tokens = $this->getUserTokens($user);

        if (empty($tokens)) {
            Log::info("No FCM tokens found for {$user->phone}");
            return;
        }

        [$title, $body] = $this->resolveMessage($notification);

        Log::info("Sending Product Back In Stock FCM to {$user->phone}");

        $this->fcm->sendToMany(
            $tokens,
            $title,
            $body,
            [
                'product_id' => $product->id,
                'type' => NotificationTypeEnum::PRODUCT_BACK_IN_STOCK->value
            ]
        );
    }

    private function getUserTokens($user): array
    {
        return $user->fcmTokens()->pluck('token')->toArray();
    }

    private function resolveMessage(Notification $notification): array
    {
        $isAr = app()->getLocale() === 'ar';

        return [
            $isAr ? $notification->title_ar : $notification->title_en,
            $isAr ? $notification->body_ar : $notification->body_en,
        ];
    }
}
