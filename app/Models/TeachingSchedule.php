<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeachingSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'class_id',
        'subject_id',
        'academic_year_id',
        'day_of_week',
        'time_slot_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function timeSlot(): BelongsTo
    {
        return $this->belongsTo(TimeSlot::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    public function scopeForDay($query, $dayName)
    {
        return $query->where('day_of_week', $dayName);
    }

    public function scopeForAcademicYear($query, $academicYearId = null)
    {
        if ($academicYearId === null) {
            $academicYearId = AcademicYear::where('is_active', true)->value('id');
        }
        return $query->where('academic_year_id', $academicYearId);
    }

    // Helper
    public function getScheduleLabel(): string
    {
        return sprintf(
            '%s - %s - %s (%s)',
            $this->schoolClass->name ?? '-',
            $this->subject->name ?? '-',
            $this->timeSlot->display_name ?? '-',
            $this->getDayLabel()
        );
    }

    public function getDayLabel(): string
    {
        $days = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu',
        ];
        
        return $days[$this->day_of_week] ?? $this->day_of_week;
    }
}
