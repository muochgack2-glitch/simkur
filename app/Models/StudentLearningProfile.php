<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentLearningProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'assessment_id',
        'academic_year_id',
        'semester_id',
        'dominant_style',
        'visual_score',
        'auditory_score',
        'kinesthetic_score',
        'reading_writing_score',
        'total_score',
        'recommendations',
        'aspect_scores',
        'needs_support_in',
        'diagnostic_recommendations',
        'diagnostic_category',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'recommendations' => 'array',
            'aspect_scores' => 'array',
            'needs_support_in' => 'array',
            'diagnostic_recommendations' => 'array',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * Relationships
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * Helpers
     */
    public function getDominantStyleLabel(): string
    {
        return match($this->dominant_style) {
            'visual' => 'Visual Learner',
            'auditory' => 'Auditory Learner',
            'kinesthetic' => 'Kinesthetic Learner',
            'reading_writing' => 'Reading/Writing Learner',
            default => 'Unknown',
        };
    }

    public function getDominantStyleColor(): string
    {
        return match($this->dominant_style) {
            'visual' => 'blue',
            'auditory' => 'green',
            'kinesthetic' => 'orange',
            'reading_writing' => 'purple',
            default => 'gray',
        };
    }

    public function getDominantStyleIcon(): string
    {
        return match($this->dominant_style) {
            'visual' => '👁️',
            'auditory' => '👂',
            'kinesthetic' => '🤸',
            'reading_writing' => '📖',
            default => '❓',
        };
    }

    public function getScoresArray(): array
    {
        return [
            'visual' => $this->visual_score,
            'auditory' => $this->auditory_score,
            'kinesthetic' => $this->kinesthetic_score,
            'reading_writing' => $this->reading_writing_score,
        ];
    }

    public function getScoresPercentage(): array
    {
        $total = $this->total_score > 0 ? $this->total_score : 1;
        
        return [
            'visual' => round(($this->visual_score / $total) * 100, 2),
            'auditory' => round(($this->auditory_score / $total) * 100, 2),
            'kinesthetic' => round(($this->kinesthetic_score / $total) * 100, 2),
            'reading_writing' => round(($this->reading_writing_score / $total) * 100, 2),
        ];
    }

    /**
     * Diagnostic-specific helpers
     */
    public function getDiagnosticCategoryLabel(): string
    {
        return match($this->diagnostic_category) {
            'sangat_baik' => 'Sangat Baik',
            'baik' => 'Baik',
            'cukup' => 'Cukup',
            'perlu_pendampingan' => 'Perlu Pendampingan',
            default => 'Unknown',
        };
    }

    public function getDiagnosticCategoryColor(): string
    {
        return match($this->diagnostic_category) {
            'sangat_baik' => 'green',
            'baik' => 'blue',
            'cukup' => 'yellow',
            'perlu_pendampingan' => 'red',
            default => 'gray',
        };
    }

    public function needsSupport(): bool
    {
        return $this->diagnostic_category === 'perlu_pendampingan';
    }

    /**
     * Get interpreted diagnostic results
     */
    public function getInterpretation(): array
    {
        if (!$this->aspect_scores) {
            return [];
        }

        return \App\Services\DiagnosticAssessmentInterpreter::interpret(
            $this->total_score,
            $this->aspect_scores
        );
    }
}
