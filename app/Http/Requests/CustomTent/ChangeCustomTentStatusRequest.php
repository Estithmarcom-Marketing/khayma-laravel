<?php

namespace App\Http\Requests\CustomTent;

use App\Enums\CustomTent\CustomTentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChangeCustomTentStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', Rule::in(CustomTentStatus::all())],
        ];
    }
}
