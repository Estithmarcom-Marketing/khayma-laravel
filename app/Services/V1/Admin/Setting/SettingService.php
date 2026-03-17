<?php

namespace App\Services\V1\Admin\Setting;

use App\Models\Setting;

class SettingService
{
    public function getSettings()
    {
        return Setting::first();
    }

    public function updateOrCreate(array $data)
    {
        $setting = Setting::first();
        if(!$setting){
            $setting = Setting::create($data);
        } else {
            $setting->update($data);
        }
        return $setting->refresh();
    }
}
