<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentQuestionOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_question_id',
        'option_text',
        'score_value',
        'order_number',
    ];

    /**
     * Relationships
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(AssessmentQuestion::class, 'assessment_question_id');
    }
}
