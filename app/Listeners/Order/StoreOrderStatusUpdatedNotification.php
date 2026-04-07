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

        try {
            $notification = $this->createNotification($order);

            $this->sendNotification($order, $notification);

            Log::info("Order #{$order->id} status changed", [
                'order_id'        => $order->id,
                'status'          => $order->status->value,
                'notification_id' => $notification->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Order Status Notification Error', [
                'order_id' => $order->id,
                'status'   => $order->status->value ?? null,
                'error'    => $e->getMessage(),
            ]);
        }
    }

    private function createNotification(Order $order): Notification
    {
        $label = $order->status->label();

        return Notification::create([
            'user_id'        => $order->user_id,
            'notifiable_id'  => $order->id,
            'notifiable_type' => Order::class,
            'type'           => NotificationTypeEnum::ORDER_STATUS_UPDATED,
            'is_read'        => false,
            'title_ar'       => 'تحديث حالة الطلب',
            'title_en'       => 'Order Status Updated',
            'body_ar'        => "طلبك الآن {$label['ar']}",
            'body_en'        => "Your order is now {$label['en']}",
        ]);
    }

    private function sendNotification(Order $order, Notification $notification): void
    {
        $tokens = $this->getUserTokens($order);

        if (empty($tokens)) {
            Log::info("No FCM tokens found for {$order->user->phone}");
            return;
        }

        [$title, $body] = $this->resolveMessage($notification);

        Log::info("Sending Order Status FCM to {$order->user->phone}");

        $this->fcm->sendToMany(
            $tokens,
            $title,
            $body,
            [
                'order_id' => $order->id,
                'status'   => $order->status->value,
                'type'     => NotificationTypeEnum::ORDER_STATUS_UPDATED->value,
            ]
        );
    }

    private function getUserTokens(Order $order): array
    {
        return $order->user->fcmTokens()->pluck('token')->toArray();
    }

    private function resolveMessage(Notification $notification): array
    {
        $isAr = app()->getLocale() === 'ar';

        return [
            $isAr ? $notification->title_ar : $notification->title_en,
            $isAr ? $notification->body_ar  : $notification->body_en,
        ];
    }
}
