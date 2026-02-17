<?php

namespace App\Services\V1\Website\City;

use App\Models\City;

class CityService
{
    public function list()
    {
        return City::select(['id', 'name_en', 'name_ar'])->active()->get();
    }

    public function show($id)
    {
        return City::select(['id', 'name_en', 'name_ar'])->active()->find($id);
    }
}
