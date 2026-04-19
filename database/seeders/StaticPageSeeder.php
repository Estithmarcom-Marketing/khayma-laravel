<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StaticPage;

class StaticPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StaticPage::insert([
            [
                'slug_en' => 'about-us',
                'slug_ar' => 'من-نحن',
                'title_en' => 'About Us',
                'title_ar' => 'من نحن',
                'content_en' => 'This is the About Us page content. You can describe your company, mission, and values here.',
                'content_ar' => 'هذه صفحة من نحن. يمكنك وصف شركتك ورسالتك وقيمك هنا.',
                'meta_title_en' => 'About Us',
                'meta_title_ar' => 'من نحن',
                'meta_description_en' => 'Learn more about our company and what we do.',
                'meta_description_ar' => 'تعرف أكثر على شركتنا وما نقدمه.',
            ],

            [
                'slug_en' => 'privacy-policy',
                'slug_ar' => 'سياسة-الخصوصية',
                'title_en' => 'Privacy Policy',
                'title_ar' => 'سياسة الخصوصية',
                'content_en' => 'This is the Privacy Policy page content. Explain how you collect and use user data.',
                'content_ar' => 'هذه صفحة سياسة الخصوصية. وضّح كيف يتم جمع واستخدام بيانات المستخدم.',
                'meta_title_en' => 'Privacy Policy',
                'meta_title_ar' => 'سياسة الخصوصية',
                'meta_description_en' => 'Read our privacy policy.',
                'meta_description_ar' => 'اقرأ سياسة الخصوصية الخاصة بنا.',
            ],

            [
                'slug_en' => 'refund-policy',
                'slug_ar' => 'سياسة-الاسترجاع',
                'title_en' => 'Refund Policy',
                'title_ar' => 'سياسة الاسترجاع',
                'content_en' => 'This is the Refund Policy page content. Explain refund conditions and timelines.',
                'content_ar' => 'هذه صفحة سياسة الاسترجاع. وضّح شروط الاسترجاع والمدة الزمنية.',
                'meta_title_en' => 'Refund Policy',
                'meta_title_ar' => 'سياسة الاسترجاع',
                'meta_description_en' => 'Read our refund policy.',
                'meta_description_ar' => 'اقرأ سياسة الاسترجاع الخاصة بنا.',
            ],
        ]);
    }
}
