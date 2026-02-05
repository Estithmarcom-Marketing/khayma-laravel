<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
            'address_id' => ['required_if:delivery_method_id,!=,null', 'exists:addresses,id'],
            'delivery_method_id' => ['sometimes', 'exists:delivery_methods,id'],
            'payment_method_id' => ['required',  'exists:payment_methods,id'],
            'promo_code' => ['nullable', 'string', 'max:50'],
        ];
    }
}
