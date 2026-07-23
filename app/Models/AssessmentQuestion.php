<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssessmentQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_id',
        'question_text',
        'question_type',
        'order_number',
        'learning_style_indicator',
        'aspect',
        'aspect_weight',
        'major',
        'weight',
    ];

    /**
     * Relationships
     */
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(AssessmentQuestionOption::class)->orderBy('order_number');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(StudentAssessmentResponse::class);
    }

    /**
     * Helpers
     */
    public function getLearningStyleLabel(): string
    {
        return match($this->learning_style_indicator) {
            'visual' => 'Visual',
            'auditory' => 'Auditory',
            'kinesthetic' => 'Kinesthetic',
            'reading_writing' => 'Reading/Writing',
            default => 'Unknown',
        };
    }

    public function getAspectLabel(): string
    {
        return match($this->aspect) {
            'kesiapan' => 'Kesiapan Belajar',
            'motivasi' => 'Motivasi Belajar',
            'kemandirian' => 'Kemandirian Belajar',
            'kolaborasi' => 'Kolaborasi & Komunikasi',
            'dunia_kerja' => 'Kesiapan Dunia Kerja',
            'preferensi' => 'Preferensi Belajar',
            default => 'Unknown',
        };
    }

    public function getAspectWeight(): int
    {
        return $this->aspect_weight ?? 0;
    }

    public function getLearningStyleColor(): string
    {
        return match($this->learning_style_indicator) {
            'visual' => 'blue',
            'auditory' => 'green',
            'kinesthetic' => 'orange',
            'reading_writing' => 'purple',
            default => 'gray',
        };
    }
}
