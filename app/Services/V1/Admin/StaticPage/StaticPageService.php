<?php

namespace App\Services\V1\Admin\StaticPage;

use App\Models\StaticPage;

use function App\Helpers\make_slug;

class StaticPageService
{
    public function list()
    {
        return StaticPage::latest()->paginate(10);
    }
    public function show(StaticPage $staticPage)
    {
        return $staticPage;
    }
    public function store(array $data)
    {
        $slug_ar = $data['slug_ar'] ?? make_slug($data['title_ar'], 'ar', StaticPage::class, 'slug_ar');
        $slug_en = $data['slug_en'] ?? make_slug($data['title_en'], 'en', StaticPage::class, 'slug_en');
        return StaticPage::create([
            'title_ar' => $data['title_ar'],
            'title_en' => $data['title_en'],
            'slug_ar' => $slug_ar,
            'slug_en' => $slug_en,
            'content_ar' => $data['content_ar'],
            'content_en' => $data['content_en'],
            'meta_title_ar' => $data['meta_title_ar'] ?? null,
            'meta_title_en' => $data['meta_title_en'] ?? null,
            'meta_description_ar' => $data['meta_description_ar'] ?? null,
            'meta_description_en' => $data['meta_description_en'] ?? null,
        ]);
    }
    public function update(StaticPage $staticPage, array $data)
    {
        $staticPage->update([
            'content_ar' => $data['content_ar'] ?? $staticPage->content_ar,
            'content_en' => $data['content_en'] ?? $staticPage->content_en,
            'meta_title_ar' => $data['meta_title_ar'] ?? $staticPage->meta_title_ar,
            'meta_title_en' => $data['meta_title_en'] ?? $staticPage->meta_title_en,
            'meta_description_ar' => $data['meta_description_ar'] ?? $staticPage->meta_description_ar,
            'meta_description_en' => $data['meta_description_en'] ?? $staticPage->meta_description_en,
        ]);
        return $staticPage->refresh();
    }
    public function destroy(StaticPage $staticPage)
    {
        return $staticPage->delete();
    }
}
