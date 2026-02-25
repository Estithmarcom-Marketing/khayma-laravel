<?php

namespace App\Listeners\Order;

use App\Enums\Enums\Notification\NotificationTypeEnum;
use App\Events\Order\OrderPaid;
use App\Models\Notification;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class StoreOrderPaidNotification
{
    /**
     * Handle the event.
     */
    public function handle(OrderPaid $event): void
    {

        try {
            $notification = Notification::create([
                'user_id' => $event->order->user_id,
                'notifiable_id' => $event->order->id,
                'notifiable_type' => Order::class,
                'type' => NotificationTypeEnum::ORDER_PAID,
                'is_read' => false,
                'title_ar' => 'تم استلام الدفع',
                'title_en' => 'Payment Received',

                'body_ar' => 'تم استلام الدفع لطلبك',
                'body_en' => 'Your payment has been received',
            ]);
            Log::info('Payment Received', ['order_id' => $event->order->id, 'notification_id' => $notification->id]);
        } catch (\Exception $e) {
            Log::error('Payment Received Error', ['error' => $e->getMessage()]);
        }

    }
}
