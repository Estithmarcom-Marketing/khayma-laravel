<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name_ar'               => 'required|string|max:255',
            'name_en'               => 'required|string|max:255',
            'description_ar'        => 'nullable|string|max:500',
            'description_en'        => 'nullable|string|max:500',

            'slug_ar' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('products', 'slug_ar')->whereNull('deleted_at'),
            ],
            'slug_en' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('products', 'slug_en')->whereNull('deleted_at'),
            ],

            'category_id'           => 'required|exists:categories,id',
            'brand_id'              => 'required|exists:brands,id',
            'is_published'          => 'required|boolean',

            'meta_title_ar'         => 'nullable|string|max:255',
            'meta_title_en'         => 'nullable|string|max:255',
            'meta_description_ar'   => 'nullable|string|max:500',
            'meta_description_en'   => 'nullable|string|max:500',

            'images'                => 'nullable|array|max:10',
            'images.*'              => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:10240',

            'variations'            => 'required|array|min:1',
            'variations.*.color_id' => 'required|exists:colors,id',
            'variations.*.size_id'  => 'required|exists:sizes,id',


            'variations.*.sku' => [
                'required',
                'string',
                'max:255',
                'distinct',
                Rule::unique('product_variations', 'sku'),
            ],

            'variations.*.price'          => 'required|numeric|min:0',
            'variations.*.stock_quantity' => 'required|integer|min:0',
            'variations.*.is_active'      => 'required|boolean',

            'variations.*.offer'              => 'nullable|numeric|min:0',
            'variations.*.offer_expired_date' => 'nullable|date|after:today',
            'variations.*.offer_started_date' => 'nullable|date',
        ];
    }


    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $variations = $this->input('variations', []);

            foreach ($variations as $index => $variation) {
                $offer       = $variation['offer'] ?? null;
                $startedDate = $variation['offer_started_date'] ?? null;
                $expiredDate = $variation['offer_expired_date'] ?? null;

                if (!is_null($offer)) {
                    if (empty($expiredDate)) {
                        $validator->errors()->add(
                            "variations.$index.offer_expired_date",
                            __('product.offer_expired_date_required')
                        );
                    }
                    if (empty($startedDate)) {
                        $validator->errors()->add(
                            "variations.$index.offer_started_date",
                            __('product.offer_started_date_required')
                        );
                    }
                }

                if ($startedDate && $expiredDate) {
                    if (strtotime($startedDate) >= strtotime($expiredDate)) {
                        $validator->errors()->add(
                            "variations.$index.offer_started_date",
                            __('product.offer_started_date_before_expired')
                        );
                    }
                }

                if ((!is_null($startedDate) || !is_null($expiredDate)) && is_null($offer)) {
                    $validator->errors()->add(
                        "variations.$index.offer",
                        __('product.offer_required_with_dates')
                    );
                }
            }
        });
    }
}
