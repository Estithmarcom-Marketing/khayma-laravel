<?php

namespace App\Listeners\Order;

use App\Enums\Enums\Notification\NotificationTypeEnum;
use App\Events\Order\OrderStatusUpdated;
use App\Models\Notification;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class StoreOrderStatusUpdatedNotification
{
    public function handle(OrderStatusUpdated $event): void
    {

        try {
            $label = $event->order->status->label();
            $notification = Notification::create([
                'user_id' => $event->order->user_id,
                'notifiable_id' => $event->order->id,
                'notifiable_type' => Order::class,
                'type' => NotificationTypeEnum::ORDER_STATUS_UPDATED,
                'is_read' => false,
                'title_ar' => 'تحديث حالة الطلب',
                'title_en' => 'Order Status Updated',
                'body_ar' => "طلبك الآن {$label['ar']}",
                'body_en' => "Your order is now {$label['en']}",
            ]);
            Log::info("Order #{$event->order->id} status changed", [
                'order_id' => $event->order->id,
                'status' => $event->order->status->value,
                'notification_id' => $notification->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Order Status Notification Error', [
                'order_id' => $event->order->id,
                'status' => $event->order->status->value ?? null,
                'error' => $e->getMessage(),
            ]);
        }

    }
}
