<?php

namespace App\Http\Resources\Application\Notifications;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return app()->isLocale('ar') ? $this->arabicResource() : $this->englishResource();

    }

    private function arabicResource()
    {
        return [
            'id' => $this->id,
            'title' => $this->title_ar,
            'body' => $this->body_ar,
            'type' => $this->type,
            'is_read' => $this->is_read,
            'notifiable_id' => $this->notifiable_id,
            'notifiable_type' => $this->notifiable_type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    private function englishResource()
    {
        return [
            'id' => $this->id,
            'title' => $this->title_en,
            'body' => $this->body_en,
            'type' => $this->type,
            'is_read' => $this->is_read,
            'notifiable_id' => $this->notifiable_id,
            'notifiable_type' => $this->notifiable_type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
