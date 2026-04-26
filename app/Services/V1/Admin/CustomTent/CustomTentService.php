<?php

namespace App\Services\V1\Admin\CustomTent;

use App\Models\CustomTent;

class CustomTentService
{
    public function list(array $data)
    {
        $per_page = $data['per_page'] ?? 10;
        return CustomTent::when($data['status'] ?? null, fn($q, $status) => $q->status($status))
            ->with(['user', 'media'])
            ->latest()
            ->paginate($per_page);
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
