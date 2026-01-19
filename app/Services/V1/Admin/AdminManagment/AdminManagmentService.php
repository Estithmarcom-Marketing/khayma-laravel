<?php

namespace App\Services\V1\Admin\AdminManagment;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminManagmentService
{
    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {
            $data['password'] = Hash::make($data['password']);
            $data['is_guest'] = false;
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'phone' => $data['phone'],
                'is_guest' => $data['is_guest'],
            ]);
            if (isset($data['role_id'])) {
                $role = Role::find($data['role_id']);
                $user->assignRole($role);
            }
            return ['admin' => $user];
        });
    }
}
