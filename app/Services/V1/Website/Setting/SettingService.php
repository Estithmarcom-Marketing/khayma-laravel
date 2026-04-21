<?php

namespace App\Services\V1\Website\Setting;

use App\Models\Setting;

class SettingService
{
    public function getSettings()
    {
        return Setting::firstOrFail();
    }
}
