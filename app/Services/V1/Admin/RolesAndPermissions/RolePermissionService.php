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
            'guard_name' => 'sanctum',
        ]);

    }

    public function storePermission(array $data)
    {
        return Permission::create([
            'name' => $data['name'],
            'guard_name' => 'sanctum',
        ]);
    }

    public function assignPermissionToRole(array $data, Role $role)
    {

        if (! $role->hasPermissionTo($data['permission_name'])) {
            $role->givePermissionTo($data['permission_name']);
        }

        return $role->refresh();
    }

    public function removePermissionFromRole(Permission $permission, Role $role)
    {
        if ($role->hasPermissionTo($permission->name)) {
            $role->revokePermissionTo($permission->name);
        }

        return $role->refresh();
    }

    public function assignRoleToUser(array $data, User $user)
    {

        $user->assignRole($data['role_name']);

        return $user->refresh();
    }

    public function removeRoleFromUser(User $user, Role $role)
    {

        if ($user->hasRole($role->name)) {
            $user->removeRole($role->name);
        }

        return $user->refresh();

    }

    public function getAllRoles()
    {
        return Role::query()
            ->select('id', 'name', 'created_at', 'updated_at')
            ->latest()
            ->paginate(10);
    }

    public function getAllPermissions()
    {
        return Permission::query()
            ->select('id', 'name', 'created_at', 'updated_at')
            ->latest()
            ->paginate(10);
    }

    public function getUserPermissions(User $user)
    {

        return $user->getAllPermissions();
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

    public function getPermissionUsers(Permission $permission)
    {

        return $permission->users;
    }
}
