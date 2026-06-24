<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EffectiveDay extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'semester_id',
        'total_days',
        'weekend_days',
        'study_days',
        'holiday_days',
        'exam_days',
        'effective_weeks',
        'percentage',
        'calculated_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'total_days' => 'integer',
            'weekend_days' => 'integer',
            'study_days' => 'integer',
            'holiday_days' => 'integer',
            'exam_days' => 'integer',
            'effective_weeks' => 'decimal:2',
            'percentage' => 'decimal:2',
            'calculated_at' => 'datetime',
        ];
    }

    /**
     * Relationships
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * Helpers
     */
    public function getPercentageStudyDaysAttribute(): float
    {
        if ($this->total_days === 0) {
            return 0;
        }
        return round(($this->study_days / $this->total_days) * 100, 2);
    }

    public function getPercentageHolidayDaysAttribute(): float
    {
        if ($this->total_days === 0) {
            return 0;
        }
        return round(($this->holiday_days / $this->total_days) * 100, 2);
    }

    public function getPercentageExamDaysAttribute(): float
    {
        if ($this->total_days === 0) {
            return 0;
        }
        return round(($this->exam_days / $this->total_days) * 100, 2);
    }
}
