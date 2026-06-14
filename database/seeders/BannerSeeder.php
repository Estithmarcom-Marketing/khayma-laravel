<?php

namespace Database\Seeders;

use App\Enums\BannerPosition\BannerPositionEnum;
use App\Models\Banner;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            [
                'title_en' => 'Custom-Designed Tents Built Just for You',
                'title_ar' => 'اخلق تجربة فريدة بخيمة مصممة خصيصًا لك',
                'position' => BannerPositionEnum::TOP_LEFT,
                'redirect_url' => 'https://alkhimah.com',
                'image' => public_path('Banners/top_left.jpeg'),
            ],

            [
                'title_en' => 'Best Desert Outdoor Seating – Complete Family Trip Essentials',
                'title_ar' => 'أفضل جلسات بر',
                'position' => BannerPositionEnum::TOP_RIGHT,
                'redirect_url' => 'https://alkhimah.com',
                'image' => public_path('Banners/top_right.jpeg'),
            ],
            [
                'title_en' => 'Best Deals & Offers – Shop Now',
                'title_ar' => 'أقوى العروض من الخيمة',
                'position' => BannerPositionEnum::UNDER_CATEGORY,
                'redirect_url' => 'https://alkhimah.com',
                'image' => public_path('Banners/under_category.jpeg'),
            ],
            [
                'title_en' => 'Authentic Pakistani Tents – Dust-Resistant & Built to Last',
                'title_ar' => 'خيام باكستانية أصلية مقاومة للغبار',
                'position' => BannerPositionEnum::MIDDLE_TOP,
                'redirect_url' => 'https://alkhimah.com',
                'image' => public_path('Banners/middle_top.jpeg'),
            ],
            [
                'title_en' => 'Premium Striped Fabric Rolls – Style & Durability',
                'title_ar' => 'أقمشة مخططة عالية الجودة',
                'position' => BannerPositionEnum::MIDDLE_LEFT,
                'redirect_url' => 'https://alkhimah.com',
                'image' => public_path('Banners/middle_left.jpeg'),
            ],
            [
                'title_en' => 'Heavy-Duty Shade & Protection Net Rolls – Garden & Outdoor Use',
                'title_ar' => 'شبك حماية وتظليل للحدائق',
                'position' => BannerPositionEnum::MIDDLE_RIGHT,
                'redirect_url' => 'https://alkhimah.com',
                'image' => public_path('Banners/middle_right.jpeg'),
            ],
            [
                'title_en' => 'Fast Delivery Anywhere in Saudi Arabia – Shop Now',
                'title_ar' => 'توصيل لأي مكان في السعودية',
                'position' => BannerPositionEnum::GIF,
                'redirect_url' => 'https://alkhimah.com',
                'image' => public_path('Banners/gif.jpeg'),
            ],
            [
                'title_en' => 'Fast Delivery Anywhere in Saudi Arabia – Shop Now',
                'title_ar' => 'توصيل لأي مكان في السعودية',
                'position' => BannerPositionEnum::BOTTOM,
                'redirect_url' => 'https://alkhimah.com',
                'image' => public_path('Banners/bottom.jpeg'),
            ],
        ];

        foreach ($banners as $data) {

            $banner = Banner::create([
                'title_en' => $data['title_en'],
                'title_ar' => $data['title_ar'],
                'position' => $data['position'],
                'redirect_url' => $data['redirect_url'],
                'is_active' => true,
            ]);

            if (File::exists($data['image'])) {
                $banner
                    ->copyMedia($data['image'])
                    ->toMediaCollection('banners');
            }
        }
    }
}
