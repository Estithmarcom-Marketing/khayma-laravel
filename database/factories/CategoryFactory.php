<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $nameEn = $this->faker->unique()->words(2, true);
        $nameAr = 'فئة '.$this->faker->unique()->numberBetween(100, 999);

        return [
            'name_en' => $nameEn,
            'name_ar' => $nameAr,
            'slug_en' => Str::slug($nameEn),
            'slug_ar' => Str::slug($nameAr),
            'description_en' => $this->faker->paragraphs(2, true),
            'description_ar' => 'وصف الفئة: '.$this->faker->text(200),
            'parent_id' => null,
        ];
    }
}
