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
    protected FcmService $fcm;

    public function __construct(FcmService $fcm)
    {
        $this->fcm = $fcm;
    }

    public function handle(OrderStatusUpdated $event): void
    {
        $order = $event->order;

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

            if (!empty($userTokens)) {
                Log::info("Sending FCM notification to {$order->user->phone}");
                $this->fcm->sendToMany(
                    $userTokens,
                    $notification->title_en,
                    $notification->body_en,
                    [
                        'order_id' => $order->id,
                        'status' => $order->status->value,
                        'type' => NotificationTypeEnum::ORDER_STATUS_UPDATED->value
                    ]
                );
            }else {
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
