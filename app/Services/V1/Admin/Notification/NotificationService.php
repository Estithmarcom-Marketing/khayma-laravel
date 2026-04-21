<?php

namespace App\Services\V1\Admin\Notification;

use App\Models\Admin;
use App\Models\AdminNotification;
use App\Models\Notification;

class NotificationService
{
    public function getAll()
    {
        return AdminNotification::query()
            ->latest()
            ->paginate(6);
    }

    public function getUnread()
    {
        return AdminNotification::query()
            ->where('is_read', false)
            ->latest()
            ->paginate(6);
    }

    public function markAllAsRead()
    {
        return  AdminNotification::query()
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }
    public function markAsRead(AdminNotification $notification)
    {
        return $notification->update(['is_read' => true]);
    }
}
