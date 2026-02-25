<?php

namespace App\Http\Resources\CustomTent;

use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomTentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=> $this->id,
            'user_name' =>$this->user_name,
            'phone'=> $this->phone,
            'description'=> $this->description,
            'size'=> $this->size,
            'user'=>UserResource::make($this->whenLoaded('user')),
           'images' => $this->whenLoaded('media', function () {
                return $this->media->map(function ($media) {
                    return [
                        'name' => $media->name,
                        'url' => $media->original_url,
                    ];
                });
            }),
            'created_at'=> $this->created_at,
            'updated_at'=> $this->updated_at
        ];
    }
}
