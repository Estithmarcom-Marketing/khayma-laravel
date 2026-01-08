<?php

namespace App\Http\Resources\RolesAndPermissions;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
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
            'permissions' => $this->whenLoaded('permissions', PermissionResource::collection($this->permissions)),
            'created_at' => $this->whenNotNull($this->created_at->toDateTimeString()),
            'updated_at' => $this->whenNotNull($this->updated_at->toDateTimeString()),
        ];
    }
}
