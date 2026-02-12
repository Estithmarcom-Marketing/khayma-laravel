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
}
