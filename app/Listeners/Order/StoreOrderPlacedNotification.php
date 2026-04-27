<?php

namespace App\Listeners\Order;

use App\Enums\Enums\Notification\NotificationTypeEnum;
use App\Events\Order\OrderPlacement;
use App\Models\Notification;
use App\Models\Order;
use App\Services\V1\Admin\Firebase\FcmService;
use App\Services\V1\Website\TqnyatSms\TqnyatSmsService;
use Illuminate\Support\Facades\Log;

class StoreOrderPlacedNotification
{
    public function __construct(
        protected FcmService $fcm,
        protected TqnyatSmsService $tqnyat
    ) {}

    public function handle(OrderPlacement $event): void
    {
        $order = $event->order;

        try {
            $notification = $this->createNotification($order);

            $this->dispatchChannels($order, $notification);

            Log::info("Order placed", [
                'order_id' => $order->id,
            ]);
        } catch (\Throwable $e) {
            Log::error('Order Placed Notification Error', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
            ]);
        }
    }

    private function createNotification(Order $order): Notification
    {
        return Notification::create([
            'user_id'         => $order->user_id,
            'notifiable_id'   => $order->id,
            'notifiable_type' => Order::class,
            'type'            => NotificationTypeEnum::ORDER_PLACED,
            'is_read'         => false,
            'title_ar'        => 'طلب جديد',
            'title_en'        => 'New Order',
            'body_ar'         => 'لديك طلب جديد',
            'body_en'         => 'You have a new order',
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
                'type'     => NotificationTypeEnum::ORDER_PLACED->value,
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
            Log::error('Order Placed SMS Error', [
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
        return app()->getLocale() === 'ar'
            ? 'الخيمة | Alkhimah: تم إنشاء طلبك بنجاح. سنقوم بمعالجته قريبًا. شكرًا لتسوقك معنا.'
            : 'Alkhimah: Your order has been placed successfully. We will process it shortly. Thank you for shopping with us.';
    }
}
