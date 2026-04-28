<?php

namespace App\Listeners\Admin\ContactUs;

use App\Enums\Enums\Notification\NotificationTypeEnum;
use App\Events\AdminNotification\AdminNotificationCreated;
use App\Events\ContactUs\ContactUsMessagePlaced;
use App\Models\AdminNotification;
use App\Models\ContactUs;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class StoreContactUsNotification
{
    public function handle(ContactUsMessagePlaced $event): void
    {
        $contactUs = $event->contactUs;

        try {
            $notification = $this->createNotification($contactUs);

            Log::info('Contact Us Message Placed', [
                'contact_us_id' => $contactUs->id,
                'notification_id' => $notification->id,
            ]);
            AdminNotificationCreated::dispatch($notification);
        } catch (\Throwable $th) {
            Log::error('Contact Us Notification Error', [
                'contact_us_id' => $contactUs->id,
                'error' => $th->getMessage(),
            ]);
        }
    }
    private function createNotification(ContactUs $contactUs)
    {
        return AdminNotification::create([
            'notifiable_id' => $contactUs->id,
            'notifiable_type' => ContactUs::class,
            'type' => NotificationTypeEnum::CONTACT_US_MESSAGE_PLACED,
            'is_read' => false,
            'title_ar' => 'تم إرسال رسالة جديدة',
            'title_en' => 'New Message',
            'body_ar' => 'تم إرسال رسالة جديدة للمساعدة',
            'body_en' => 'You have a new message',
        ]);
    }
}
