<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentAssessmentResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_id',
        'user_id',
        'assessment_question_id',
        'selected_option_id',
        'score',
        'answered_at',
    ];

    protected function casts(): array
    {
        return [
            'answered_at' => 'datetime',
        ];
    }

    /**
     * Relationships
     */
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(AssessmentQuestion::class, 'assessment_question_id');
    }

    public function selectedOption(): BelongsTo
    {
        return $this->belongsTo(AssessmentQuestionOption::class, 'selected_option_id');
    }
}
