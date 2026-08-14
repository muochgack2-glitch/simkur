<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PklQuizResponse extends Model
{
    protected $fillable = [
        'pkl_quiz_id', 'student_id', 'answers', 'score',
        'started_at', 'submitted_at', 'is_graded',
    ];

    protected function casts(): array
    {
        return [
            'answers' => 'array',
            'score' => 'decimal:2',
            'started_at' => 'datetime',
            'submitted_at' => 'datetime',
            'is_graded' => 'boolean',
        ];
    }

    public function quiz(): BelongsTo { return $this->belongsTo(PklQuiz::class, 'pkl_quiz_id'); }
    public function student(): BelongsTo { return $this->belongsTo(User::class, 'student_id'); }

    public function isSubmitted(): bool { return $this->submitted_at !== null; }
    public function isGraded(): bool { return $this->is_graded; }
}
