<?php

namespace App\Listeners\Admin\Order;

use App\Enums\Enums\Notification\NotificationTypeEnum;
use App\Events\AdminNotification\AdminNotificationCreated;
use App\Events\Order\OrderPlacement;
use App\Models\AdminNotification;
use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class StoreOrderPlacedNotification
{
    public function handle(OrderPlacement $event): void
    {
        $order = $event->order;

        try {
            $notification = $this->createNotification($order);
            Log::info("Order #{$order->id} placed", [
                'order_id' => $order->id,
                'notification_id' => $notification->id,
            ]);
            AdminNotificationCreated::dispatch($notification);
        } catch (\Throwable $th) {
            Log::error('Order Placed Notification Error', [
                'order_id' => $order->id,
                'error' => $th->getMessage(),
            ]);
        }
    }
    private function createNotification(Order $order)
    {
        return AdminNotification::create([
            'notifiable_id' => $order->id,
            'notifiable_type' => Order::class,
            'type' => NotificationTypeEnum::ORDER_PLACED,
            'is_read' => false,
            'title_ar' => 'طلب جديد',
            'title_en' => 'New Order',
            'body_ar' => 'لديك طلب جديد',
            'body_en' => 'You have a new order'
        ]);
    }
}
