<?php

namespace App\Services\V1\Admin\RolesAndPermissions;

use App\Models\User;
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

   
}
