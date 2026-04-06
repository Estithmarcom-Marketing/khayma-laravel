<?php

namespace App\Listeners\Order;

use App\Enums\Enums\Notification\NotificationTypeEnum;
use App\Events\Order\OrderStatusUpdated;
use App\Models\Notification;
use App\Models\Order;
use App\Services\V1\Admin\Firebase\FcmService;
use Illuminate\Support\Facades\Log;

class StoreOrderStatusUpdatedNotification
{


    public function __construct(protected FcmService $fcm) {}

    public function handle(OrderStatusUpdated $event): void
    {
        $order = $event->order;
        $isAr = app()->getLocale() == 'ar';

        try {
            $label = $order->status->label();
            $notification = Notification::create([
                'user_id' => $order->user_id,
                'notifiable_id' => $order->id,
                'notifiable_type' => Order::class,
                'type' => NotificationTypeEnum::ORDER_STATUS_UPDATED,
                'is_read' => false,
                'title_ar' => 'تحديث حالة الطلب',
                'title_en' => 'Order Status Updated',
                'body_ar' => "طلبك الآن {$label['ar']}",
                'body_en' => "Your order is now {$label['en']}",
            ]);

            $userTokens = $order->user->fcmTokens()->pluck('token')->toArray();
            $title = $isAr ? $notification->title_ar : $notification->title_en;
            $body = $isAr ? $notification->body_ar : $notification->body_en;

            if (!empty($userTokens)) {
                Log::info("Sending FCM notification to {$order->user->phone}");
                $this->fcm->sendToMany(
                    $userTokens,
                    $title,
                    $body,
                    [
                        'order_id' => $order->id,
                        'status' => $order->status->value,
                        'type' => NotificationTypeEnum::ORDER_STATUS_UPDATED->value
                    ]
                );
            } else {
                Log::info("No FCM tokens found for {$order->user->phone}");
            }

            Log::info("Order #{$order->id} status changed", [
                'order_id' => $order->id,
                'status' => $order->status->value,
                'notification_id' => $notification->id,
                'tokens_sent' => count($userTokens)
            ]);
        } catch (\Exception $e) {
            Log::error('Order Status Notification Error', [
                'order_id' => $order->id,
                'status' => $order->status->value ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
