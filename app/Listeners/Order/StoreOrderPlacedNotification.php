<?php

namespace App\Listeners\Order;

use App\Enums\Enums\Notification\NotificationTypeEnum;
use App\Events\Order\OrderPlacement;
use App\Models\Notification;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class StoreOrderPlacedNotification
{
    public function handle(OrderPlacement $event): void
    {
        try {
            $notification = Notification::create([
                'user_id' => $event->order->user_id,
                'notifiable_id' => $event->order->id,
                'notifiable_type' => Order::class,
                'type' => NotificationTypeEnum::ORDER_PLACED,
                'is_read' => false,
                'title' => __('orders.notification_title'),
                'body' => __('orders.notification_body'),
            ]);
            Log::info(__('orders.notification_body'), ['order_id' => $event->order->id, 'notification_id' => $notification->id]);
        } catch (\Exception $e) {
            Log::error('Order Placed Error', ['error' => $e->getMessage()]);
        }
    }
}
