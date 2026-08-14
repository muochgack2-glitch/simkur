<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PklQuizQuestion extends Model
{
    protected $fillable = [
        'pkl_quiz_id', 'question_type', 'question',
        'options', 'correct_answer', 'score', 'order',
    ];

    protected function casts(): array
    {
        return ['options' => 'array'];
    }

    public function quiz(): BelongsTo { return $this->belongsTo(PklQuiz::class, 'pkl_quiz_id'); }
}
