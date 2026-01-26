<?php

namespace App\Services\V1\Website\City;

use App\Models\City;

class CityService
{
    public function list()
    {
        return City::active()->get();
    }

    public function show($id)
    {
        return City::active()->find($id);
    }
}
