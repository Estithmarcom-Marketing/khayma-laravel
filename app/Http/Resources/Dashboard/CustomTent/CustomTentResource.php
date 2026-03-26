<?php

namespace App\Http\Resources\Dashboard\CustomTent;

use App\Http\Resources\Dashboard\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomTentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'user_name'   => $this->user_name,
            'phone'       => $this->phone,
            'description' => $this->description,
            'size'        => $this->size,
            'status'      => $this->status->value,
            'user'        => UserResource::make($this->whenLoaded('user')),
            'images'      => $this->whenLoaded('media', function () {
                return $this->media->map(fn ($media) => [
                    'name' => $media->name,
                    'url'  => $media->original_url,
                ]);
            }),
            'created_at'  => $this->created_at,
        ];
    }
}
