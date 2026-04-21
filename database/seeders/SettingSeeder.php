<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::updateOrCreate(
            [
                'id' => 1
            ],
            [
                'facebook' => 'https://www.facebook.com',
                'instagram' => 'https://www.instagram.com',
                'x' => 'https://www.x.com',
                'snapchat' => 'https://www.snapchat.com',
                'tiktok' => 'https://www.tiktok.com',
                'linkedin' => 'https://www.linkedin.com/company',
                'address' => '123 Main Street, City, Country',
                'phone' => '966123456789',
                'telegram' => 'https://t.me/',
                'whatsapp' => 'https://wa.me/966123456789',
                'email' => 'info@alkhimah.com',
            ]
        );
    }
}
