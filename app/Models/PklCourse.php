<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PklCourse extends Model
{
    protected $fillable = [
        'activity_id', 'teacher_id', 'subject_id', 'academic_year_id',
        'title', 'description', 'competency', 'target_classes',
        'order', 'start_date', 'deadline', 'is_published',
    ];

    protected function casts(): array
    {
        return [
            'target_classes' => 'array',
            'start_date' => 'date',
            'deadline' => 'date',
            'is_published' => 'boolean',
        ];
    }

    // Relationships
    public function activity(): BelongsTo { return $this->belongsTo(Activity::class); }
    public function teacher(): BelongsTo { return $this->belongsTo(User::class, 'teacher_id'); }
    public function subject(): BelongsTo { return $this->belongsTo(Subject::class); }
    public function academicYear(): BelongsTo { return $this->belongsTo(AcademicYear::class); }
    public function materials(): HasMany { return $this->hasMany(PklMaterial::class); }
    public function assignments(): HasMany { return $this->hasMany(PklAssignment::class); }
    public function quizzes(): HasMany { return $this->hasMany(PklQuiz::class); }

    // Scopes
    public function scopePublished($q) { return $q->where('is_published', true); }
    public function scopeForTeacher($q, $teacherId) { return $q->where('teacher_id', $teacherId); }
    public function scopeForClass($q, $classId) { return $q->whereJsonContains('target_classes', $classId); }

    // Helpers
    public function isOngoing(): bool
    {
        $today = now()->startOfDay();
        return $this->start_date <= $today && $this->deadline >= $today;
    }

    public function getTargetStudents()
    {
        return User::where('role', 'siswa')
            ->whereIn('class_id', array_map('intval', $this->target_classes ?? []))
            ->where('is_active', true)
            ->get();
    }

    public function getProgressForStudent($studentId): array
    {
        $totalAssignments = $this->assignments()->count();
        $submittedAssignments = PklSubmission::whereIn('pkl_assignment_id', $this->assignments()->pluck('id'))
            ->where('student_id', $studentId)
            ->whereNotNull('submitted_at')
            ->count();

        $totalQuizzes = $this->quizzes()->where('is_published', true)->count();
        $completedQuizzes = PklQuizResponse::whereIn('pkl_quiz_id', $this->quizzes()->pluck('id'))
            ->where('student_id', $studentId)
            ->whereNotNull('submitted_at')
            ->count();

        $total = $totalAssignments + $totalQuizzes;
        $completed = $submittedAssignments + $completedQuizzes;

        return [
            'total' => $total,
            'completed' => $completed,
            'percentage' => $total > 0 ? round(($completed / $total) * 100) : 0,
        ];
    }
}
