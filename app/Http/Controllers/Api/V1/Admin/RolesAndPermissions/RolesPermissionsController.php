<?php

namespace App\Http\Controllers\Api\V1\Admin\RolesAndPermissions;

use App\Http\Controllers\Controller;
use App\Http\Resources\RolesAndPermissions\PermissionResource;
use App\Http\Resources\RolesAndPermissions\RoleResource;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use App\Services\V1\Admin\RolesAndPermissions\RolePermissionService;
use App\Traits\Response\ApiResponse;
use Illuminate\Http\Request;
use Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Response;

class RolesPermissionsController extends Controller
{
    public function __construct(protected RolePermissionService $service) {}

    public function storeRole(Request $request)
    {

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:roles,name',
            ]);

            $role = $this->service->storeRole($validated);

            return ApiResponse::successResponse(['role' => RoleResource::make($role)], 'Role created successfully', Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error('Failed to create role', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to create role', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function storePermission(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:permissions,name',
            ]);

            $permission = $this->service->storePermission($validated);

            return ApiResponse::successResponse(['permission' => PermissionResource::make($permission)], 'Permission created successfully', Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error('Failed to create permission', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to create permission', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function assignPermissionToRole(Role $role, Permission $permission)
    {
        try {

            $role = $this->service->assignPermissionToRole($role, $permission);

            return ApiResponse::successResponse(['role' => RoleResource::make($role)], 'Permission assigned to role successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to assign permission to role', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to assign permission to role', Response::HTTP_INTERNAL_SERVER_ERROR);

        }
    }

    public function revokePermissionFromRole(Role $role, Permission $permission)
    {
        try {

            $role = $this->service->removePermissionFromRole($permission, $role);

            return ApiResponse::successResponse(['role' => RoleResource::make($role)], 'Permission revoked from role successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to revoke permission from role', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to revoke permission from role', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function assignRoleToUser(User $user, Role $role)
    {
        try {
            $user = $this->service->assignRoleToUser($user, $role);

            return ApiResponse::successResponse(['user' => UserResource::make($user)], 'Role assigned to user successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to assign role to user', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'role_id' => $role->id,
                'method' => __METHOD__,
            ]);

            return ApiResponse::errorResponse('Failed to assign role to user',
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function revokeRoleFromUser(User $user, Role $role)
    {
        try {

            $user = $this->service->removeRoleFromUser($user, $role);

            return ApiResponse::successResponse(['user' => UserResource::make($user)], 'Role revoked from user successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to revoke role from user', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to revoke role from user', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getAllRoles()
    {
        try {
            $roles = $this->service->getAllRoles();
            $roles = RoleResource::collection($roles)->response()->getData(true);

            return ApiResponse::successResponse(['roles' => $roles['data'],
                'meta' => $roles['meta'],
                'links' => $roles['links']], 'Roles fetched successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to fetch roles', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to fetch roles', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getAllPermissions()
    {
        try {
            $permissions = $this->service->getAllPermissions();
            $permissions = PermissionResource::collection($permissions)->response()->getData(true);

            return ApiResponse::successResponse(['permissions' => $permissions['data'],
                'meta' => $permissions['meta'],
                'links' => $permissions['links']], 'Permissions fetched successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to fetch permissions', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to fetch permissions', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    

    public function getRolesByUser(User $user)
    {
        try {

            $roles = $this->service->getUserRoles($user);

            return ApiResponse::successResponse(['roles' => RoleResource::collection($roles)], 'Roles fetched successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to fetch roles by user', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to fetch roles by user', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getPermissionsByRole(Role $role)
    {
        try {
            $permissions = $this->service->getRolePermissions($role);

            return ApiResponse::successResponse(['permissions' => PermissionResource::collection($permissions)], 'Permissions fetched successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to fetch permissions by role', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to fetch permissions by role', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getUsersByRole(Role $role)
    {
        try {

            $users = $this->service->getRoleUsers($role);

            return ApiResponse::successResponse(['users' => UserResource::collection($users)], 'Users fetched successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to fetch users by role', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to fetch users by role', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getRolesByPermission(Permission $permission)
    {
        try {

            $roles = $this->service->getPermissionRoles($permission);

            return ApiResponse::successResponse(['roles' => RoleResource::collection($roles)], 'Roles fetched successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to fetch roles by permission', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to fetch roles by permission', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    
}
