<?php

namespace App\Http\Requests\Order;

use App\Enums\Orders\OrderStatusEnum;
use App\Enums\Payments\PaymentStatusEnum;
use Illuminate\Foundation\Http\FormRequest;

class GetOrderRequest extends FormRequest
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
        $order_status = implode(',', OrderStatusEnum::all());
        $payment_status = implode(',', PaymentStatusEnum::all());
        return [
            'search' => 'nullable|string',
            'order_status' => "nullable|in:{$order_status}",
            'payment_status' => "nullable|in:{$payment_status}",
            'is_delivered' => 'nullable|boolean',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }
}
