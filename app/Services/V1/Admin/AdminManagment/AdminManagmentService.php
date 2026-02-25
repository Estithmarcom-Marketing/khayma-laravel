<?php

namespace App\Services\V1\Admin\AdminManagment;

use App\Models\Admin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminManagmentService
{
    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {
            $data['password'] = Hash::make($data['password']);
            $admin = Admin::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'is_active' => $data['is_active'] ?? true,
            ]);
            if (isset($data['role_id'])) {
                $role = Role::find($data['role_id']);
                $admin->assignRole($role);
            }
            return ['admin' => $admin];
        });
    }

    public function list($limit = 10)
    {
        return Admin::with('roles')->paginate($limit);
    }

    public function show($id) 
    { 
        return Admin::with('roles')->findOrFail($id); 
    }

    public function update(Admin $admin, array $data) 
    { 
        return DB::transaction(function () use ($admin, $data) { 
            if (isset($data['password'])) { 
                $data['password'] = Hash::make($data['password']); 
            } 
            $admin->update($data); 
            if (isset($data['role_id'])) { 
                $role = Role::find($data['role_id']); 
                $admin->syncRoles($role); 
            } 
            return $admin->refresh();
        }); 
    } 
    
    public function delete(Admin $admin) 
    { 
        if ($admin->id == 1) {
            throw new \Exception(__('admin.cannot_delete_super_admin'));
        }
        return $admin->delete(); 
    }
}
