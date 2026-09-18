<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssessmentStudentSession extends Model
{
    protected $fillable = [
        'assessment_id',
        'user_id',
        'attempt_number',
        'question_order',
        'option_orders',
        'answers_data',
        'started_at',
        'submitted_at',
        'auto_score',
        'manual_score',
        'total_score',
        'max_possible_score',
    ];

    protected function casts(): array
    {
        return [
            'answers_data'      => 'array',
            'question_order'    => 'array',
            'option_orders'     => 'array',
            'started_at'        => 'datetime',
            'submitted_at'      => 'datetime',
            'auto_score'        => 'decimal:2',
            'manual_score'      => 'decimal:2',
            'total_score'       => 'decimal:2',
            'max_possible_score'=> 'decimal:2',
        ];
    }

    // ── Relationships ────────────────────────────────────────────────────────

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(StudentAssessmentResponse::class, 'assessment_id', 'assessment_id')
            ->where('user_id', $this->user_id)
            ->where('attempt_number', $this->attempt_number);
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    public function isSubmitted(): bool
    {
        return $this->submitted_at !== null;
    }

    public function isInProgress(): bool
    {
        return $this->started_at !== null && $this->submitted_at === null;
    }

    public function needsManualGrading(): bool
    {
        return $this->isSubmitted() && $this->manual_score === null;
    }

    public function getScorePercentage(): float
    {
        if (!$this->max_possible_score || $this->max_possible_score == 0) return 0;
        $total = ($this->auto_score ?? 0) + ($this->manual_score ?? 0);
        return round(($total / $this->max_possible_score) * 100, 1);
    }

    /**
     * Hitung ulang total_score dari auto + manual lalu simpan.
     */
    public function recalculateTotal(): void
    {
        $this->total_score = ($this->auto_score ?? 0) + ($this->manual_score ?? 0);
        $this->save();
    }
}
