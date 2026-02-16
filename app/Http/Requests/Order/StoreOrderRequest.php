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
            'promo_code' => ['nullable', 'string', 'exists:promo_codes,code'],
            'phone' => ['nullable', 'string', 'max:20'],
        ];
    }

    public function messages(): array
    {
        $locale = app()->getLocale();
        if ($locale === 'ar') {
            return [
                'address_id.required_if' => 'حقل العنوان مطلوب عندما يتم اختيار طريقة توصيل.',
                'address_id.exists' => 'العنوان المحدد غير صالح.',
                'delivery_method_id.exists' => 'طريقة التوصيل المحددة غير صالحة.',
                'payment_method_id.required' => 'حقل طريقة الدفع مطلوب.',
                'payment_method_id.exists' => 'طريقة الدفع المحددة غير صالحة.',
                'promo_code.exists' => 'كود الخصم المدخل غير صالح أو منتهي الصلاحية.',
            ];
        }

        return [
            'address_id.required_if' => 'The address field is required when a delivery method is selected.',
            'address_id.exists' => 'The selected address is invalid.',
            'delivery_method_id.exists' => 'The selected delivery method is invalid.',
            'payment_method_id.required' => 'The payment method field is required.',
            'payment_method_id.exists' => 'The selected payment method is invalid.',
            'promo_code.exists' => 'The entered promo code is invalid or expired.',
        ];
    }
}
