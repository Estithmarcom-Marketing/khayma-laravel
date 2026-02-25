<?php

namespace Database\Factories;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BrandFactory extends Factory
{
    protected $model = Brand::class;

    public function definition(): array
    {
        $nameEn = $this->faker->company();
        $nameAr = 'علامة '.$this->faker->numberBetween(100, 999);

        return [
            'name_en' => $nameEn,
            'name_ar' => $nameAr,
            'slug_en' => Str::slug($nameEn.' '.Str::random(5)),
            'slug_ar' => Str::slug($nameAr.' '.Str::random(5)),
            'description_en' => $this->faker->paragraphs(2, true),
            'description_ar' => 'وصف العلامة: '.$this->faker->text(200),
        ];
    }
}
