<?php

namespace App\Http\Resources\Application\Setting;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'facebook'  => $this->facebook,
            'instagram' => $this->instagram,
            'x'         => $this->x,
            'snapchat'  => $this->snapchat,
            'tiktok'    => $this->tiktok,
            'linkedin'  => $this->linkedin,
            'address'   => $this->address,
            'phone'     => $this->phone,
            'email'     => $this->email,
            'whatsapp'  => $this->whatsapp,
            'telegram'  => $this->telegram
        ];
    }
}
