<?php

namespace App\Services\V1\User\ProfileManagement;

use DB;

class ProfileManagementService
{
    public function updateProfile(array $data)
    {
        $user = auth()->user();

        return DB::transaction(function () use ($user, $data) {
            $user->update([
                'name' => $data['name'] ?? $user->name,
                'email' => $data['email'] ?? $user->email,
            ]);

            return $user->refresh();
        });
    }
}
