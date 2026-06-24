<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'academic_year_id',
        'semester_id',
        'activity_type_id',
        'name',
        'start_date',
        'end_date',
        'color',
        'description',
        'target_grades',
        'is_active',
        'created_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'target_grades' => 'array',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relationships
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function activityType(): BelongsTo
    {
        return $this->belongsTo(ActivityType::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForGrade($query, string $grade)
    {
        return $query->where(function ($q) use ($grade) {
            $q->whereNull('target_grades')
              ->orWhereJsonContains('target_grades', $grade);
        });
    }

    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('start_date', [$startDate, $endDate])
                ->orWhereBetween('end_date', [$startDate, $endDate])
                ->orWhere(function ($q2) use ($startDate, $endDate) {
                    $q2->where('start_date', '<=', $startDate)
                        ->where('end_date', '>=', $endDate);
                });
        });
    }

    public function scopeUpcoming($query, $days = 7)
    {
        $startDate = now();
        $endDate = now()->addDays($days);
        return $query->inDateRange($startDate, $endDate)->orderBy('start_date');
    }

    public function scopeByAcademicYear($query, $academicYearId)
    {
        return $query->where('academic_year_id', $academicYearId);
    }

    public function scopeBySemester($query, $semesterId)
    {
        return $query->where('semester_id', $semesterId);
    }

    public function scopeByType($query, $activityTypeId)
    {
        return $query->where('activity_type_id', $activityTypeId);
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        // Set default color from activity type if not provided
        static::creating(function ($activity) {
            if (empty($activity->color) && $activity->activityType) {
                $activity->color = $activity->activityType->default_color;
            }
        });
    }

    /**
     * Helpers
     */
    public function getDurationInDays(): int
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    public function isHoliday(): bool
    {
        return $this->activityType->is_holiday;
    }

    public function isExam(): bool
    {
        return $this->activityType->is_exam;
    }

    public function isOngoing(): bool
    {
        $today = now()->startOfDay();
        return $this->start_date <= $today && $this->end_date >= $today;
    }

    public function isPast(): bool
    {
        return $this->end_date < now()->startOfDay();
    }

    public function isFuture(): bool
    {
        return $this->start_date > now()->startOfDay();
    }

    public function hasConflict(): bool
    {
        return static::where('id', '!=', $this->id)
            ->where('semester_id', $this->semester_id)
            ->where(function ($query) {
                $query->whereBetween('start_date', [$this->start_date, $this->end_date])
                    ->orWhereBetween('end_date', [$this->start_date, $this->end_date])
                    ->orWhere(function ($q) {
                        $q->where('start_date', '<=', $this->start_date)
                            ->where('end_date', '>=', $this->end_date);
                    });
            })
            ->exists();
    }

    /**
     * Attributes
     */
    public function getFormattedDateRangeAttribute(): string
    {
        if ($this->start_date->equalTo($this->end_date)) {
            return $this->start_date->format('d/m/Y');
        }
        return $this->start_date->format('d/m/Y') . ' - ' . $this->end_date->format('d/m/Y');
    }

    /**
     * Target Grades Helpers
     */
    public function isForAllGrades(): bool
    {
        return is_null($this->target_grades) || 
               (is_array($this->target_grades) && count($this->target_grades) === 3);
    }

    public function isForGrade(string $grade): bool
    {
        if ($this->isForAllGrades()) {
            return true;
        }
        
        return in_array($grade, $this->target_grades ?? []);
    }

    public function getTargetGradesLabel(): string
    {
        if ($this->isForAllGrades()) {
            return 'Semua Kelas';
        }
        
        if (empty($this->target_grades)) {
            return 'Semua Kelas';
        }
        
        if (count($this->target_grades) === 1) {
            return 'Kelas ' . $this->target_grades[0];
        }
        
        return 'Kelas ' . implode(', ', $this->target_grades);
    }

    public function getTargetGradesBadgeColor(): string
    {
        if ($this->isForAllGrades()) {
            return 'green';
        }
        
        if (count($this->target_grades) === 1) {
            return match($this->target_grades[0]) {
                'X' => 'blue',
                'XI' => 'yellow',
                'XII' => 'purple',
                default => 'gray',
            };
        }
        
        return 'orange'; // Multiple grades
    }
}
