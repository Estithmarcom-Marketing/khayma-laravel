<?php

namespace App\Events\AdminNotification;

use App\Models\AdminNotification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AdminNotificationCreated implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    /**
     * Create a new event instance.
     */
    public function __construct(public AdminNotification $notification) {}

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        Log::info('broadcastOn called', [
            'notification_id' => $this->notification->id,
            'channel' => 'admins.notifications',
        ]);

        return [
            new PrivateChannel('admins.notifications'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'notification.created';
    }

    public function broadcastWith(): array
    {
        $data = [
            'id' => $this->notification->id,
            'title_ar' => $this->notification->title_ar,
            'title_en' => $this->notification->title_en,
            'body_ar' => $this->notification->body_ar,
            'body_en' => $this->notification->body_en,
            'type' => $this->notification->type->value,
            'is_read' => (bool) $this->notification->is_read,
            'notifiable_id' => $this->notification->notifiable_id,
            'notifiable_type' => $this->notification->notifiable_type,
            'created_at' => $this->notification->created_at,
        ];
        Log::info('broadcastWith data', $data);

        return $data;
    }
}
