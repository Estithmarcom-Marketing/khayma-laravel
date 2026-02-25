<?php

namespace App\Http\Resources\Application\CommonQuestion;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommonQuestionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return app()->isLocale('ar') ? $this->arabicResource() : $this->englishResource();

    }

    private function arabicResource()
    {
        return [
            'id' => $this->id,
            'question' => $this->question_ar,
            'answer' => $this->answer_ar,
        ];
    }

    private function englishResource()
    {
        return [
            'id' => $this->id,
            'question' => $this->question_en,
            'answer' => $this->answer_en,
        ];

    }
}
