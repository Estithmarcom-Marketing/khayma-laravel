<?php

namespace App\Http\Requests\Product;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductVaritionsRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'color_id' => 'sometimes|exists:colors,id',
            'size_id' => 'sometimes|exists:sizes,id',
            'sku' => 'sometimes|string|max:255| unique:product_variations,sku,'.$this->route('productVariation')->id,
            'price' => 'sometimes|numeric|min:0',
            'stock_quantity' => 'sometimes|numeric|min:0',
            'is_active' => 'sometimes|boolean',
            'offer' => 'sometimes|numeric|min:0',
            'offer_expired_date' => 'sometimes|date|after:today',
            'offer_started_date' => 'sometimes|date|before:offer_expired_date',
        ];
    }
}
