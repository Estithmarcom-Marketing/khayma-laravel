<?php

namespace App\Services\V1\Admin\RolesAndPermissions;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionService
{
    public function storeRole(array $data)
    {
        return Role::create([
            'name' => $data['name'],
            'guard_name' => 'api',
        ]);

    }

    public function storePermission(array $data)
    {
        return Permission::create([
            'name' => $data['name'],
            'guard_name' => 'api',
        ]);
    }

    public function assignPermissionToRole(Role $role, Permission $permission)
    {

        if (! $role->hasPermissionTo($permission)) {
            $role->givePermissionTo($permission);
        }

        return $role->refresh()->load('permissions');
    }

    public function removePermissionFromRole(Permission $permission, Role $role)
    {
        if ($role->hasPermissionTo($permission)) {
            $role->revokePermissionTo($permission);
        }

        return $role->refresh()->load('permissions');
    }

    public function assignRoleToUser(User $user, Role $role)
    {
        if ($user->hasRole($role->name)) {
            return $user;
        }
        $user->assignRole($role);

        return $user->refresh()->load('roles');
    }

    public function removeRoleFromUser(User $user, Role $role)
    {

        if ($user->hasRole($role)) {
            $user->removeRole($role);
        }

        return $user->refresh()->load('roles');

    }

    public function getAllRoles()
    {
        return Role::query()
            ->select('id', 'name', 'guard_name', 'created_at', 'updated_at')
            ->latest()
            ->paginate(10);
    }

    public function getAllPermissions()
    {
        return Permission::query()
            ->select('id', 'name', 'guard_name', 'created_at', 'updated_at')
            ->latest()
            ->paginate(10);
    }

    public function getUserRoles(User $user)
    {

        return $user->roles;
    }

    public function getRolePermissions(Role $role)
    {

        return $role->permissions;

    }

    public function getPermissionRoles(Permission $permission)
    {

        return $permission->roles;

    }

    public function getRoleUsers(Role $role)
    {

        return $role->users;

    }

    public function storeRoleWithPermissions(array $data)
    {

        return DB::transaction(function () use ($data) {
            $role = Role::create([
                'name' => $data['name'],
                'guard_name' => 'api',
            ]);
            if (isset($data['permissions'])) {
                $permissionIds = collect($data['permissions'])->pluck('id');
                $permissions = Permission::whereIn('id', $permissionIds)->get();
                $role->syncPermissions($permissions);
            }

            return $role->refresh()->load('permissions');
        });
    }

    public function updateRoleWithPermissions(Role $role, array $data)
    {
        return DB::transaction(function () use ($role, $data) {
            $role->update([
                'name' => $data['name'] ?? $role->name,
            ]);
            if (isset($data['permissions'])) {
                $permissionIds = collect($data['permissions'])->pluck('id');
                $permissions = Permission::whereIn('id', $permissionIds)->get();
                $role->syncPermissions($permissions);
            }

            return $role->refresh()->load('permissions');
        });
    }
}
