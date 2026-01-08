<?php

namespace App\Http\Resources\RolesAndPermissions;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource
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
            'roles' => $this->whenLoaded('roles', RoleResource::collection($this->roles)),
            'created_at' => $this->whenNotNull($this->created_at->toDateTimeString()),
            'updated_at' => $this->whenNotNull($this->updated_at->toDateTimeString()),
        ];
    }
}
