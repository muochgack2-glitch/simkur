<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PklFieldAssessment extends Model
{
    protected $table = 'pkl_assessments';

    protected $fillable = [
        'pkl_placement_id', 'student_id', 'component_id',
        'score', 'assessor_id', 'notes',
    ];

    protected function casts(): array
    {
        return ['score' => 'decimal:2'];
    }

    public function placement(): BelongsTo { return $this->belongsTo(PklPlacement::class, 'pkl_placement_id'); }
    public function student(): BelongsTo { return $this->belongsTo(User::class, 'student_id'); }
    public function component(): BelongsTo { return $this->belongsTo(PklAssessmentComponent::class, 'component_id'); }
    public function assessor(): BelongsTo { return $this->belongsTo(User::class, 'assessor_id'); }

    public function getWeightedScore(): float
    {
        if (!$this->score || !$this->component) return 0;
        return ($this->score / $this->component->max_score) * $this->component->weight;
    }
}