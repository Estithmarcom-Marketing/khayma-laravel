<?php

namespace App\Http\Requests\Order;

use App\Enums\Orders\OrderStatusEnum;
use Illuminate\Foundation\Http\FormRequest;

class FilterOrderRequest extends FormRequest
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
            'status' => 'in:'.implode(',', OrderStatusEnum::all()),
            'sort_by' => 'in:created_at,total',
            'order_by' => 'in:asc,desc',
            'limit' => 'integer|min:1|max:100',
        ];
    }
}
