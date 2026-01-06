<?php

namespace App\Services\V1\Admin\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminAuthService
{
    public function store(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        $data['is_guest'] = false;

        $user = User::create($data);
        $token = $user->createToken('token')->plainTextToken;

        return ['admin' => $user, 'token' => $token];
    }

    public function login(array $data)
    {
        $user = User::Where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return false;
        } else {
            $token = $user->createToken('token')->plainTextToken;

            return ['admin' => $user, 'token' => $token];
        }
    }

    public function logout()
    {
        $user = auth()->user();
        if ($user) {
            $user->tokens()->delete();
        }

        return true;
    }
}
