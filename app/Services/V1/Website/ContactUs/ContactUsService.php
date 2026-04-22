<?php

namespace App\Services\V1\Website\ContactUs;

use App\Events\ContactUs\ContactUsMessagePlaced;
use App\Models\ContactUs;

use function App\Helpers\normalize_saudi_phone_number;

class ContactUsService
{
    public function store(array $data)
    {
        $phone = normalize_saudi_phone_number($data['phone']);
        $contactUs =  ContactUs::create([
            'name' => $data['name'],
            'phone' => $phone,
            'message' => $data['message'],
            'is_contacted' => false
        ]);
        event(new ContactUsMessagePlaced($contactUs));
        return $contactUs;
    }
}
