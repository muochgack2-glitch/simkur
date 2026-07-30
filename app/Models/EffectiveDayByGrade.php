<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EffectiveDayByGrade extends Model
{
    use HasFactory;

    protected $table = 'effective_days_by_grade';

    protected $fillable = [
        'effective_day_id',
        'grade',
        'start_date',
        'end_date',
        'total_days',
        'weekend_days',
        'holiday_days',
        'exam_days',
        'study_days',
        'effective_weeks',
        'percentage',
        'exam_notes',
        'calculated_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_days' => 'integer',
        'weekend_days' => 'integer',
        'holiday_days' => 'integer',
        'exam_days' => 'integer',
        'study_days' => 'integer',
        'effective_weeks' => 'decimal:2',
        'percentage' => 'decimal:2',
        'calculated_at' => 'datetime',
    ];

    // Relationships
    public function effectiveDay(): BelongsTo
    {
        return $this->belongsTo(EffectiveDay::class);
    }

    // Constants
    public const GRADES = ['X', 'XI', 'XII'];

    public const GRADE_LABELS = [
        'X' => 'Kelas X',
        'XI' => 'Kelas XI',
        'XII' => 'Kelas XII',
    ];

    // Helper Methods
    public function getGradeLabelAttribute(): string
    {
        return self::GRADE_LABELS[$this->grade] ?? $this->grade;
    }

    public function getStatusColorAttribute(): string
    {
        if ($this->percentage >= 85) return 'green';
        if ($this->percentage >= 70) return 'yellow';
        if ($this->percentage >= 50) return 'orange';
        return 'red';
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->percentage >= 85) return 'Sangat Baik';
        if ($this->percentage >= 70) return 'Baik';
        if ($this->percentage >= 50) return 'Cukup';
        return 'Kurang';
    }

    public function getDurationInMonths(): float
    {
        if (!$this->start_date || !$this->end_date) return 0;
        
        return $this->start_date->diffInMonths($this->end_date, true);
    }

    public function isGradeXII(): bool
    {
        return $this->grade === 'XII';
    }

    public function hasEarlyEnd(): bool
    {
        if (!$this->effectiveDay) return false;
        
        // Cek apakah end_date lebih awal dari semester end_date
        $semesterEndDate = $this->effectiveDay->semester->end_date;
        return $this->end_date < $semesterEndDate;
    }

    // Scopes
    public function scopeGrade($query, string $grade)
    {
        return $query->where('grade', $grade);
    }

    public function scopeGradeX($query)
    {
        return $query->where('grade', 'X');
    }

    public function scopeGradeXI($query)
    {
        return $query->where('grade', 'XI');
    }

    public function scopeGradeXII($query)
    {
        return $query->where('grade', 'XII');
    }

    public function scopeOrderByGrade($query)
    {
        return $query->orderByRaw("FIELD(grade, 'X', 'XI', 'XII')");
    }
}
