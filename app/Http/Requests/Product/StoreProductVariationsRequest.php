<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductVariationsRequest extends FormRequest
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
            'color_id' => 'nullable|exists:colors,id',
            'size_id' => 'nullable|exists:sizes,id',
            'sku' => 'required|string|max:255|unique:product_variations,sku',
            'price' => 'required|numeric|min:0|decimal:0,2',
            'tax' => 'required|numeric|min:0|decimal:0,2',
            'stock_quantity' => 'required|integer|min:0',
            'is_active' => 'required|boolean',
            'offer' => 'nullable|numeric|min:0|decimal:0,2',
            'offer_expired_date' => 'nullable|date|after:today',
            'offer_started_date' => 'nullable|date|before:offer_expired_date',
        ];
    }
}
