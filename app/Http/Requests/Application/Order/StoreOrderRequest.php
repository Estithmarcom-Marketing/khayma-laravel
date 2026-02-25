<?php

namespace App\Http\Requests\Application\Order;

use App\Enums\Payments\PaymentGatewayEnum;
use App\Models\DeliveryMethod;
use App\Models\PaymentGateway;
use App\Models\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $deliveryMethodId = $this->input('delivery_method_id');
        $paymentMethodId = $this->input('payment_method_id');

        $noShipping = $deliveryMethodId
            ? DeliveryMethod::where('id', $deliveryMethodId)
                ->where('has_shipping_cost', false)
                ->exists()
            : false;
        

        $isCash = $paymentMethodId
            ? PaymentMethod::find($paymentMethodId)?->type->value === 'cash'
            : false;
          

        return [
            'address_id' => [$noShipping ? 'nullable' : 'required_with:delivery_method_id', 'exists:addresses,id'],
            'delivery_method_id' => ['sometimes', 'exists:delivery_methods,id'],
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
            'gateway' => [$isCash ? 'nullable' : 'required', Rule::in(PaymentGatewayEnum::values())],
            'promo_code' => ['nullable', 'string', Rule::exists('promo_codes', 'code')->where('is_active', true)],
            'phone' => ['nullable', 'string', 'phone:SA'],
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email:rfc,dns'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            if (! $this->filled(['payment_method_id', 'gateway'])) {
                return;
            }

            $gatewayExists = PaymentGateway::query()
                ->where('payment_method_id', $this->input('payment_method_id'))
                ->where('gateway', $this->input('gateway'))
                ->where('is_active', true)
                ->exists();

            if (! $gatewayExists) {
                $validator->errors()->add(
                    'gateway',
                    app()->isLocale('ar')
                        ? 'بوابة الدفع غير متاحة لطريقة الدفع المختارة.'
                        : 'The selected gateway is not available for this payment method.'
                );
            }
        });
    }

    public function messages(): array
    {
        return app()->isLocale('ar') ? $this->arabicMessages() : $this->englishMessages();
    }

    private function arabicMessages(): array
    {
        return [
            'address_id.required_with' => 'حقل العنوان مطلوب عندما لا تكون طريقة التوصيل "استلام في المتجر".',
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
            'gateway.required' => 'حقل بوابة الدفع مطلوب عندما لا تكون طريقة الدفع نقدًا.',
            'gateway.in' => 'بوابة الدفع المحددة غير صالحة.',
        ];
    }

    private function englishMessages(): array
    {
        return [
            'address_id.required_with' => 'The address field is required when a delivery method is not In-Store Pickup.',
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
            'gateway.required' => 'The payment gateway field is required when the payment method is not cash.',
            'gateway.in' => 'The selected payment gateway is invalid.',
        ];
    }
}
