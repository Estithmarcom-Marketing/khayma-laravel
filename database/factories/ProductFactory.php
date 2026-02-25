<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $nameEn = $this->faker->words(3, true);
        $nameAr = 'منتج ' . $this->faker->numberBetween(1000, 9999);
        
        return [
            'name_en' => $nameEn,
            'name_ar' => $nameAr,
            'slug_en' => Str::slug($nameEn.' '.Str::random(5)),
            'slug_ar' => Str::slug($nameAr.' '.Str::random(5)),
            'description_en' => $this->faker->paragraphs(3, true),
            'description_ar' => 'وصف المنتج: ' . $this->faker->text(200),
            'category_id' => Category::factory(),
            'brand_id' => Brand::factory(),
            'is_published' => $this->faker->boolean(80),
            'meta_title_en' => $this->faker->sentence(6),
            'meta_title_ar' => 'عنوان: ' . $this->faker->words(4, true),
            'meta_description_en' => $this->faker->sentence(12),
            'meta_description_ar' => 'وصف ميتا: ' . $this->faker->text(100),
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
        ]);
    }

    public function unpublished(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => false,
        ]);
    }
}