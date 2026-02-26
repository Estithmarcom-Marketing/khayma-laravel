<?php

namespace App\Http\Requests\Order;

use App\Models\DeliveryMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CalculateTotalRequest extends FormRequest
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
        $deliveryMethodId = $this->input('delivery_method_id');
        $noShipping = $deliveryMethodId
          ? DeliveryMethod::where('id', $deliveryMethodId)
              ->where('has_shipping_cost', false)
              ->exists()
          : false;

        return [
            'address_id' => [$noShipping ? 'nullable' : 'required_with:delivery_method_id', 'exists:addresses,id'],
            'delivery_method_id' => ['sometimes', 'exists:delivery_methods,id'],
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
            'promo_code' => ['nullable', 'string', Rule::exists('promo_codes', 'code')->where('is_active', true)],

        ];
    }

    public function messages(): array
    {
        return [
            'address_id.required_with' => __('orders.address_id_required_with'),
            'address_id.exists' => __('orders.address_id_exists'),
            'delivery_method_id.exists' => __('orders.delivery_method_id_exists'),
            'payment_method_id.required' => __('orders.payment_method_id_required'),
            'payment_method_id.exists' => __('orders.payment_method_id_exists'),
            'promo_code.exists' => __('orders.promo_code_exists'),
        ];
    }
}
