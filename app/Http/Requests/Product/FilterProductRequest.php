<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class FilterProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:1'],

            'categories' => ['nullable', 'array'],
            'categories.*' => ['integer', 'exists:categories,id'],

            'subcategories' => ['nullable', 'array'],
            'subcategories.*' => ['integer', 'exists:categories,id'],

            'brands' => ['nullable', 'array'],
            'brands.*' => ['integer', 'exists:brands,id'],

            'colors' => ['nullable', 'array'],
            'colors.*' => ['integer', 'exists:colors,id'],

            'rating' => ['nullable', 'integer', 'in:2,3,4,5'],
            'best_sellers' => ['nullable', 'boolean'],

            'has_offer' => ['nullable', 'boolean'],
            'search' => ['nullable', 'string', 'max:255'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            'sort_by' => ['sometimes', 'string', 'in:min_price,created_at'],

            'order_by' => ['sometimes', 'string', 'in:asc,desc'],
        ];
    }
}
