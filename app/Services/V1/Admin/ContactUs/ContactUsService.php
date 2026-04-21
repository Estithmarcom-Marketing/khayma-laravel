<?php

namespace App\Services\V1\Admin\ContactUs;

use App\Models\ContactUs;

class ContactUsService
{
    public function list(array $data)
    {
        $per_page = $data['per_page'] ?? 10;

        return ContactUs::query()
            ->when($data['contacted'] ?? null, fn($q, $v) => $q->contacted($v))
            ->when($data['search'] ?? null, fn($q, $v) => $q->search($v))
            ->latest()
            ->paginate($per_page);
    }

    public function show(ContactUs $contactUs)
    {
        return $contactUs;
    }

    public function updateStatus(ContactUs $contactUs, array $data)
    {
        $contactUs->update([
            'contacted' => $data['contacted'],
        ]);

        return $contactUs->refresh();
    }

    public function destroy(ContactUs $contactUs)
    {
        return $contactUs->delete();
    }
}
