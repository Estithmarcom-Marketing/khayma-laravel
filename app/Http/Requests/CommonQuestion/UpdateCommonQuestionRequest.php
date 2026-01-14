<?php

namespace App\Http\Requests\CommonQuestion;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCommonQuestionRequest extends FormRequest
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
            'question_ar' => 'sometimes|string|max:255',
            'question_en' => 'sometimes|string|max:255',
            'answer_ar' => 'sometimes|string|max:1000',
            'answer_en' => 'sometimes|string|max:1000',
        ];
    }
}
