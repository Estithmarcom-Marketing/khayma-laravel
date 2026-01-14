<?php

namespace App\Services\V1\Admin\CommonQuestion;

use App\Models\CommonQuestion;

class CommonQuestionService
{
    public function list()
    {
        return CommonQuestion::latest()->paginate(10);
    }

    public function store(array $data)
    {
        $question = CommonQuestion::create([
            'question_ar' => $data['question_ar'],
            'question_en' => $data['question_en'],
            'answer_ar' => $data['answer_ar'],
            'answer_en' => $data['answer_en'],
        ]);

        return $question->refresh();
    }

    public function update(CommonQuestion $commonQuestion, array $data)
    {
        $commonQuestion->update([
            'question_ar' => $data['question_ar'] ?? $commonQuestion->question_ar,
            'question_en' => $data['question_en'] ?? $commonQuestion->question_en,
            'answer_ar' => $data['answer_ar'] ?? $commonQuestion->answer_ar,
            'answer_en' => $data['answer_en'] ?? $commonQuestion->answer_en,
        ]);

        return $commonQuestion->refresh();
    }

    public function delete(CommonQuestion $commonQuestion)
    {
        return $commonQuestion->delete();
    }

    public function show(CommonQuestion $commonQuestion)
    {
        return $commonQuestion;
    }
}
