<?php

namespace App\Services\V1\Admin\Setting;

use App\Models\Setting;

use function App\Helpers\normalize_saudi_phone_number;

class SettingService
{
    public function getSettings()
    {
        return Setting::with('media')->firstOrFail();
    }

    public function updateOrCreate(array $data)
    {
        if (isset($data['phone'])) {
            $data['phone'] = normalize_saudi_phone_number($data['phone']);
        }
        $setting = Setting::updateOrCreate([
            'id' => 1
        ], $data);
        if (isset($data['image'])) {
            $setting->clearMediaCollection('settings');
            $setting->addMedia($data['image'])->toMediaCollection('settings');
        }
        return $setting->refresh();
    }
}
