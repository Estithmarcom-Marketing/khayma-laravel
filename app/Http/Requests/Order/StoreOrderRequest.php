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
            'address_id' => ['required', 'exists:addresses,id'],
            'payment_gateway_id' => ['nullable',  'exists:payment_gateways,id'],
            'delivery_method_id' => ['required', 'exists:delivery_methods,id'],
            'promo_code' => ['nullable', 'string', 'max:50'],
            'cart_items' => ['exists:cart_items,id'],
        ];
    }
}
