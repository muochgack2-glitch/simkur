<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TeachingJournal extends Model
{
    protected $fillable = [
        'teacher_id',
        'class_id',
        'subject_id',
        'academic_year_id',
        'date',
        'time_slot',
        'learning_objective',
        'topic',
        'teaching_method',
        'notes',
        'total_students',
        'present_count',
        'sick_count',
        'permission_count',
        'absent_count',
    ];

    protected $casts = [
        'date' => 'date',
        'total_students' => 'integer',
        'present_count' => 'integer',
        'sick_count' => 'integer',
        'permission_count' => 'integer',
        'absent_count' => 'integer',
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

    public function attendances(): HasMany
    {
        return $this->hasMany(StudentAttendance::class);
    }

    // Helper method
    public function updateAttendanceStats(): void
    {
        $this->total_students = $this->attendances()->count();
        $this->present_count = $this->attendances()->where('status', 'hadir')->count();
        $this->sick_count = $this->attendances()->where('status', 'sakit')->count();
        $this->permission_count = $this->attendances()->where('status', 'izin')->count();
        $this->absent_count = $this->attendances()->where('status', 'alpha')->count();
        $this->save();
    }
}
