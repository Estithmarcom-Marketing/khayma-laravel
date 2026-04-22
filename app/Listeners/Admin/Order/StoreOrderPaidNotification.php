<?php

namespace App\Listeners\Admin\Order;

use App\Enums\Enums\Notification\NotificationTypeEnum;
use App\Events\Order\OrderPaid;
use App\Models\AdminNotification;
use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class StoreOrderPaidNotification
{
    public function handle(OrderPaid $event): void
    {
        $order = $event->order;

        try {
            $notification = $this->createNotification($order);

            Log::info("Order #{$order->id} payment received", [
                'order_id' => $order->id,
                'notification_id' => $notification->id,
            ]);
        } catch (\Throwable $th) {
            Log::error('Order Paid Notification Error', [
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
            'type' => NotificationTypeEnum::ORDER_PAID,
            'is_read' => false,
            'title_ar' => 'تم استلام الدفع',
            'title_en' => 'Payment Received',
            'body_ar' => 'تم استلام الدفع لطلبك',
            'body_en' => 'Your payment has been received',
        ]);
    }
}
