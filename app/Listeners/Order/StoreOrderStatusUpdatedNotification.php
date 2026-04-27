<?php

namespace App\Listeners\Order;

use App\Enums\Enums\Notification\NotificationTypeEnum;
use App\Events\Order\OrderStatusUpdated;
use App\Models\Notification;
use App\Models\Order;
use App\Services\V1\Admin\Firebase\FcmService;
use App\Services\V1\Website\TqnyatSms\TqnyatSmsService;
use Illuminate\Support\Facades\Log;

class StoreOrderStatusUpdatedNotification
{
    public function __construct(
        protected FcmService $fcm,
        protected TqnyatSmsService $tqnyat
    ) {}

    public function handle(OrderStatusUpdated $event): void
    {
        $order = $event->order;

        try {
            $notification = $this->createNotification($order);

            $this->dispatchChannels($order, $notification);

            Log::info("Order status updated", [
                'order_id' => $order->id,
                'status'   => $order->status->value,
            ]);
        } catch (\Throwable $e) {
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
            'user_id'         => $order->user_id,
            'notifiable_id'   => $order->id,
            'notifiable_type' => Order::class,
            'type'            => NotificationTypeEnum::ORDER_STATUS_UPDATED,
            'is_read'         => false,
            'title_ar'        => 'تحديث حالة الطلب',
            'title_en'        => 'Order Status Updated',
            'body_ar'         => "طلبك الآن {$label['ar']}",
            'body_en'         => "Your order is now {$label['en']}",
        ]);
    }

    private function dispatchChannels(Order $order, Notification $notification): void
    {
        [$title, $body] = $this->resolveMessage($notification);

        $this->sendFcm($order, $title, $body);
        $this->sendSms($order, $this->formatSmsMessage($order));
    }

    private function sendFcm(Order $order, string $title, string $body): void
    {
        $tokens = $order->user->fcmTokens()
            ->pluck('token')
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (!$tokens) {
            Log::info("No FCM tokens found", [
                'user_id' => $order->user_id,
            ]);
            return;
        }

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

    private function sendSms(Order $order, string $message): void
    {
        try {
            $phone = $order->user->phone;

            if (!$phone) {
                return;
            }

            $this->tqnyat->send($phone, $message);
        } catch (\Throwable $e) {
            Log::error('Order Status SMS Error', [
                'order_id' => $order->id,
                'phone'    => $order->user->phone,
                'error'    => $e->getMessage(),
            ]);
        }
    }

    private function resolveMessage(Notification $notification): array
    {
        return app()->getLocale() === 'ar'
            ? [$notification->title_ar, $notification->body_ar]
            : [$notification->title_en, $notification->body_en];
    }
    private function formatSmsMessage(Order $order): string
     {
         $label = $order->status->label();

         return app()->getLocale() === 'ar'
             ? "الخيمة | Alkhimah: تم تحديث حالة طلبك رقم #{$order->id} إلى {$label['ar']}."
             : "Alkhimah: Your order #{$order->id} status has been updated to {$label['en']}.";
     }
}
