<?php

namespace App\Listeners\Admin\ProductReminder;

use App\Enums\Enums\Notification\NotificationTypeEnum;
use App\Events\ProductReminder\ProductReminderPlaced;
use App\Models\AdminNotification;
use App\Models\ContactUs;
use App\Models\ProductReminder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class StoreProductReminderNotification
{
    public function handle(ProductReminderPlaced $event): void
    {
        $productReminder = $event->productReminder;
        try {
            $notification = $this->createNotification($productReminder);

            Log::info('Product Reminder Notification Created', [
                'notification_id' => $notification->id,
                'product_reminder_id' => $productReminder->id,
            ]);
        } catch (\Throwable $th) {
            Log::error('Product Reminder Notification Error', [
                'product_reminder_id' => $productReminder->id,
                'error' => $th->getMessage(),
            ]);
        }
    }
    private function createNotification(ProductReminder $productReminder)
    {
        return AdminNotification::create([
            'notifiable_id' => $productReminder->id,
            'notifiable_type' => ProductReminder::class,
            'type' => NotificationTypeEnum::PRODUCT_REMINDER_PLACED,
            'is_read' => false,
            'title_ar' => ' تذكير جديد لمنتج',
            'title_en' => 'New Product Reminder',
            'body_ar' => 'هناك تذكير جديد لمنتج عند توفره',
            'body_en' => 'There is a new product reminder when the product is available',
        ]);
    }
}
