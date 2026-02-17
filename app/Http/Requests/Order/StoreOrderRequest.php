<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'address_id' => ['required_with:delivery_method_id', 'exists:addresses,id'],
            'delivery_method_id' => ['sometimes', 'exists:delivery_methods,id'],
            'payment_method_id' => ['required',  'exists:payment_methods,id'],
            'promo_code' => ['nullable', 'string', Rule::exists('promo_codes', 'code')->where('is_active', true)],
            'phone' => ['nullable', 'string', 'phone:SA'],
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email'],
        ];
    }

    public function messages(): array
    {
        $locale = app()->getLocale();
        if ($locale === 'ar') {
            return [
                'address_id.required_with' => 'حقل العنوان مطلوب عندما يتم اختيار طريقة توصيل.',
                'address_id.exists' => 'العنوان المحدد غير صالح.',
                'delivery_method_id.exists' => 'طريقة التوصيل المحددة غير صالحة.',
                'payment_method_id.required' => 'حقل طريقة الدفع مطلوب.',
                'payment_method_id.exists' => 'طريقة الدفع المحددة غير صالحة.',
                'promo_code.exists' => 'كود الخصم المدخل غير صالح أو منتهي الصلاحية.',
                'phone.phone' => 'رقم الهاتف يجب أن يكون رقم سعودي صالح.',
                'name.string' => 'الاسم يجب أن يكون نصًا.',
                'name.max' => 'الاسم لا يجوز أن يكون أكثر من 255 حرفًا.',
                'email.string' => 'البريد الإلكتروني يجب أن يكون نصًا.',
                'email.email' => 'البريد الإلكتروني يجب أن يكون عنوان بريد إلكتروني صالح.',
            ];
        }

        return [
            'address_id.required_with' => 'The address field is required when a delivery method is selected.',
            'address_id.exists' => 'The selected address is invalid.',
            'delivery_method_id.exists' => 'The selected delivery method is invalid.',
            'payment_method_id.required' => 'The payment method field is required.',
            'payment_method_id.exists' => 'The selected payment method is invalid.',
            'promo_code.exists' => 'The entered promo code is invalid or expired.',
            'phone.phone' => 'The phone number must be a valid Saudi Arabian phone number.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'email.string' => 'The email must be a string.',
            'email.email' => 'The email must be a valid email address.',
        ];
    }
}
