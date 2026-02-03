<?php

namespace App\Services\V1\Website\ProfileManagement;

use DB;

class ProfileManagementService
{
    public function updateProfile(array $data)
    {
        $user = auth('sanctum')->user();

        return DB::transaction(function () use ($user, $data) {
            $user->update([
                'name' => $data['name'] ?? $user->name,
                'email' => $data['email'] ?? $user->email,
            ]);
            if (isset($data['image'])) {
                $user->addMediaFromRequest('image')->toMediaCollection('profile');
            }
            return $user->refresh();
        });
    }
}
