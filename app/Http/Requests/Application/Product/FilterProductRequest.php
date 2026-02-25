<?php

namespace App\Http\Requests\Application\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Validator;

class FilterProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0'],

            'categories' => ['nullable', 'array'],
            'categories.*' => ['integer'],

            'subcategories' => ['nullable', 'array'],
            'subcategories.*' => ['integer'],

            'brands' => ['nullable', 'array'],
            'brands.*' => ['integer'],

            'colors' => ['nullable', 'array'],
            'colors.*' => ['integer'],

            'rating' => ['nullable', 'integer', 'in:1,2,3,4,5'],
            'best_sellers' => ['nullable', 'boolean'],
            'has_offer' => ['nullable', 'boolean'],

            'search' => ['nullable', 'string', 'max:255'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            'sort_by' => ['nullable', 'string', 'in:price,created_at'],
            'order_by' => ['nullable', 'string', 'in:asc,desc'],
        ];
    }

    /**
     * Bulk validates array IDs — 1 query per field
     * instead of 1 query per ID.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $this->validateBulkExists($validator, 'categories', 'categories', 'id');
            $this->validateBulkExists($validator, 'subcategories', 'categories', 'id');
            $this->validateBulkExists($validator, 'brands', 'brands', 'id');
            $this->validateBulkExists($validator, 'colors', 'colors', 'id');
        });
    }

    private function validateBulkExists(
        Validator $validator,
        string $field,
        string $table,
        string $column
    ): void {
        $ids = $this->input($field);

        if (empty($ids) || ! is_array($ids)) {
            return;
        }

        $existingIds = DB::table($table)
            ->whereIn($column, $ids)
            ->pluck($column)
            ->map(fn ($id) => (string) $id)
            ->toArray();

        $missing = array_diff(array_map('strval', $ids), $existingIds);
        $locale = app()->getLocale();
        if (! empty($missing)) {
            foreach ($missing as $id) {
                if ($locale == 'ar') {
                    $validator->errors()->add(
                        $field,
                        "ال{$field} المحدد غير موجود."
                    );
                }
                $validator->errors()->add(
                    $field,
                    "The selected {$field} does not exist."
                );
            }
        }
    }
}
