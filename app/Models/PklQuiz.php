<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PklQuiz extends Model
{
    protected $fillable = [
        'pkl_course_id', 'title', 'description', 'duration_minutes',
        'max_score', 'deadline', 'is_published', 'shuffle_questions', 'order',
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'datetime',
            'is_published' => 'boolean',
            'shuffle_questions' => 'boolean',
        ];
    }

    public function course(): BelongsTo { return $this->belongsTo(PklCourse::class, 'pkl_course_id'); }
    public function questions(): HasMany { return $this->hasMany(PklQuizQuestion::class)->orderBy('order'); }
    public function responses(): HasMany { return $this->hasMany(PklQuizResponse::class); }

    public function getResponseForStudent($studentId): ?PklQuizResponse
    {
        return $this->responses()->where('student_id', $studentId)->first();
    }

    public function getTotalScore(): int
    {
        return $this->questions()->sum('score');
    }

    public function autoGrade(PklQuizResponse $response): float
    {
        $totalScore = 0;
        $answers = $response->answers ?? [];

        foreach ($this->questions as $question) {
            if ($question->question_type === 'essay') continue;
            $studentAnswer = $answers[$question->id] ?? null;
            if ($studentAnswer !== null && strtolower(trim($studentAnswer)) === strtolower(trim($question->correct_answer))) {
                $totalScore += $question->score;
            }
        }

        return $totalScore;
    }
}
