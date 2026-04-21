<?php

namespace App\Services\V1\Admin\Notification;

use App\Models\Admin;
use App\Models\Notification;

class NotificationService
{
    public function getAll(Admin $admin)
    {
        return Notification::query()
            ->where('user_id', $admin->id)
            ->latest()
            ->paginate(6);
    }

    public function getUnread(Admin $admin)
    {
        return Notification::query()
            ->where('user_id', $admin->id)
            ->unread()
            ->latest()
            ->paginate(6);
    }

    public function markAllAsRead(Admin $admin): void
    {
        Notification::query()
            ->where('user_id', $admin->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }
}
