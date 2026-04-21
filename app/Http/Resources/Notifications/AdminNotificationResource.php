<?php

namespace App\Http\Resources\Notification;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminNotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title_ar' => $this->title_ar,
            'title_en' => $this->title_en,
            'body_ar' => $this->body_ar,
            'body_en' => $this->body_en,
            'type' => $this->type,
            'is_read' => (bool) $this->is_read,
            'notifiable_id' => $this->notifiable_id,
            'notifiable_type' => $this->notifiable_type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
