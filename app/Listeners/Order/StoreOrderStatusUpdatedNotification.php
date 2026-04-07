<?php

namespace App\Listeners\Order;

use App\Enums\Enums\Notification\NotificationTypeEnum;
use App\Events\Order\OrderPaid;
use App\Models\Notification;
use App\Models\Order;
use App\Services\V1\Admin\Firebase\FcmService;
use Illuminate\Support\Facades\Log;

class StoreOrderPaidNotification
{
    public function __construct(protected FcmService $fcm) {}

    /**
     * Handle the event.
     */
    public function handle(OrderPaid $event): void
    {
        $order = $event->order;

        try {
            $notification = $this->createNotification($order);

            $this->sendNotification($order, $notification);

            Log::info("Order #{$order->id} payment received", [
                'order_id' => $order->id,
                'notification_id' => $notification->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Order Paid Notification Error', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function createNotification(Order $order): Notification
    {
        return Notification::create([
            'user_id' => $order->user_id,
            'notifiable_id' => $order->id,
            'notifiable_type' => Order::class,
            'type' => NotificationTypeEnum::ORDER_PAID,
            'is_read' => false,
            'title_ar' => 'تم استلام الدفع',
            'title_en' => 'Payment Received',
            'body_ar' => 'تم استلام الدفع لطلبك',
            'body_en' => 'Your payment has been received',
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

        Log::info("Sending Payment Received FCM to {$order->user->phone}");

        $this->fcm->sendToMany(
            $tokens,
            $title,
            $body,
            [
                'order_id' => $order->id,
                'type' => NotificationTypeEnum::ORDER_PAID->value
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
            $isAr ? $notification->body_ar : $notification->body_en,
        ];
    }
}
