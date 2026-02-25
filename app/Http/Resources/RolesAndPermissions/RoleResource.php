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
            'guard_name' => $this->guard_name,
            'users_count' => $this->users_count,
            'permissions_count' => $this->permissions_count,

            'permissions' => $this->whenLoaded('permissions', function () {
                return $this->permissions->pluck('name')->map(function ($permission) {
                    return $this->translatePermission($permission);
                });
            }),
            'created_at' => optional($this->created_at)->toDateTimeString(),
            'updated_at' => optional($this->updated_at)->toDateTimeString(),
        ];
    }

    private function translatePermission(string $permission): string
    {
        return __('permission.' . $permission);
    }
}
