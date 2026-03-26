<?php

namespace App\Services\V1\Admin\CustomTent;

use App\Models\CustomTent;

class CustomTentService
{
    public function list()
    {
        return CustomTent::with(['user', 'media'])->latest()->paginate(15);
    }

    public function show(CustomTent $customTent): CustomTent
    {
        return $customTent->load(['user', 'media']);
    }

    public function changeStatus(CustomTent $customTent, array $data): CustomTent
    {
        $customTent->update(['status' => $data['status']]);

        return $customTent->refresh();
    }

    public function delete(CustomTent $customTent): void
    {
        $customTent->clearMediaCollection('custom-tents');
        $customTent->delete();
    }
}
