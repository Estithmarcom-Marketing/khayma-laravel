<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Order\AdminOrderResource;
use App\Http\Resources\RolesAndPermissions\PermissionResource;
use App\Http\Resources\RolesAndPermissions\RoleResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'image' => $this->whenLoaded('media', $this->whenNotNull($this->getFirstMediaUrl('profile'))),
            'orders' => $this->whenLoaded('orders', AdminOrderResource::collection($this->orders)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'orders_count' => $this->orders_count,
            // 'roles' => $this->whenLoaded('roles', RoleResource::collection($this->roles)),
            // 'permissions' => $this->whenLoaded('permissions', PermissionResource::collection($this->permissions)),

        ];
    }
}
