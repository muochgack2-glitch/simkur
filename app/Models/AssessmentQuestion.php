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
        'image_path',
        'image_position',
        'matching_pairs',
        'max_score',
        'file_accept',
    ];

    protected $casts = [
        'matching_pairs' => 'array',
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

    // ──────────────────────────────────────────────
    // Quiz Helper Methods
    // ──────────────────────────────────────────────

    public function getTypeLabel(): string
    {
        return match($this->question_type) {
            'multiple_choice' => 'Pilihan Ganda',
            'true_false'      => 'Benar / Salah',
            'matching'        => 'Menjodohkan',
            'essay'           => 'Esai',
            'file_upload'     => 'Upload File',
            'scale'           => 'Skala',
            'likert'          => 'Likert',
            default           => ucfirst($this->question_type),
        };
    }

    public function getEffectiveMaxScore(): int
    {
        return $this->max_score ?? $this->weight ?? 10;
    }

    public function isMultipleChoice(): bool
    {
        return $this->question_type === 'multiple_choice';
    }

    public function isTrueFalse(): bool
    {
        return $this->question_type === 'true_false';
    }

    public function isMatching(): bool
    {
        return $this->question_type === 'matching';
    }

    public function isEssay(): bool
    {
        return $this->question_type === 'essay';
    }

    public function isFileUpload(): bool
    {
        return $this->question_type === 'file_upload';
    }

    /** Dinilai otomatis (bukan perlu guru) */
    public function isAutoScored(): bool
    {
        return in_array($this->question_type, ['multiple_choice', 'true_false', 'matching']);
    }

    /** Perlu dinilai manual oleh guru */
    public function isManualScored(): bool
    {
        return in_array($this->question_type, ['essay', 'file_upload']);
    }
}

