<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PklAssessmentComponent extends Model
{
    protected $fillable = [
        'academic_year_id', 'name', 'category', 'weight', 'max_score', 'sort_order',
    ];

    protected function casts(): array
    {
        return ['weight' => 'decimal:2', 'max_score' => 'integer'];
    }

    public function academicYear(): BelongsTo { return $this->belongsTo(AcademicYear::class); }
    public function assessments(): HasMany { return $this->hasMany(PklFieldAssessment::class, 'component_id'); }

    public function scopeByCategory($q, $cat) { return $q->where('category', $cat); }
    public function scopeOrdered($q) { return $q->orderBy('sort_order')->orderBy('id'); }

    public function getCategoryLabel(): string
    {
        return $this->category === 'company' ? 'DU/DI' : 'Sekolah';
    }
}