<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ActivityType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'category',
        'default_color',
        'is_holiday',
        'is_exam',
        'marks_end_of_period',
        'affects_grades',
        'is_system',
        'description',
        'sort_order',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_holiday' => 'boolean',
            'is_exam' => 'boolean',
            'marks_end_of_period' => 'boolean',
            'affects_grades' => 'array',
            'is_system' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Relationships
     */
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * Scopes
     */
    public function scopeAkademik($query)
    {
        return $query->where('category', 'akademik');
    }

    public function scopeNonAkademik($query)
    {
        return $query->where('category', 'non_akademik');
    }

    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    public function scopeCustom($query)
    {
        return $query->where('is_system', false);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Helpers
     */
    public function isHoliday(): bool
    {
        return $this->is_holiday;
    }

    public function isExam(): bool
    {
        return $this->is_exam;
    }

    public function isSystem(): bool
    {
        return $this->is_system;
    }

    public function canBeDeleted(): bool
    {
        return !$this->is_system && $this->activities()->count() === 0;
    }

    /**
     * Check if this activity type marks end of period
     */
    public function marksEndOfPeriod(): bool
    {
        return $this->marks_end_of_period;
    }

    /**
     * Check if this activity type affects specific grade
     */
    public function affectsGrade(string $grade): bool
    {
        if (!$this->marks_end_of_period) {
            return false;
        }

        if (empty($this->affects_grades)) {
            return false;
        }

        return in_array($grade, $this->affects_grades);
    }

    /**
     * Get affected grades
     */
    public function getAffectedGrades(): array
    {
        return $this->affects_grades ?? [];
    }
}
