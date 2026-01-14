<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommonQuestion extends Model
{
    protected $fillable = [
        'question_ar',
        'question_en',
        'answer_ar',
        'answer_en',
    ];
}
