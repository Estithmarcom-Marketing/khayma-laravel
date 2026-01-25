<?php

namespace App\Services\V1\Website\Notification;

use App\Enums\Enums\Notification\NotificationTypeEnum;
use App\Models\Notification;
use Illuminate\Database\Eloquent\Model;

class NotificationService
{
    public function index()
    {
        return Notification::query()
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(6);
    }

    public function getUnReadNotifications()
    {
        return Notification::query()
            ->where('user_id', auth()->id())
            ->unread()
            ->latest()
            ->paginate(6);
    }

    public function markAllAsRead()
    {
        return Notification::query()
            ->where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    public function send(array $data, NotificationTypeEnum $type, ?Model $notifiable = null)
    {
        return Notification::create([
            'user_id' => $data['user_id'],
            'title' => $data['title'],
            'body' => $data['body'],
            'type' => $type,
            'notifiable_id' => $notifiable?->id,
            'notifiable_type' => $notifiable ? get_class($notifiable) : null,
        ]);
    }
}
