<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PklAssignment extends Model
{
    protected $fillable = [
        'pkl_course_id', 'title', 'description', 'deadline',
        'max_score', 'allow_late', 'allow_file_upload', 'order',
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'datetime',
            'allow_late' => 'boolean',
            'allow_file_upload' => 'boolean',
        ];
    }

    public function course(): BelongsTo { return $this->belongsTo(PklCourse::class, 'pkl_course_id'); }
    public function submissions(): HasMany { return $this->hasMany(PklSubmission::class); }

    public function isOverdue(): bool { return $this->deadline->isPast(); }

    public function getSubmissionForStudent($studentId): ?PklSubmission
    {
        return $this->submissions()->where('student_id', $studentId)->first();
    }

    public function getSubmissionStats(): array
    {
        $total = $this->course->getTargetStudents()->count();
        $submitted = $this->submissions()->whereNotNull('submitted_at')->count();
        $graded = $this->submissions()->whereNotNull('graded_at')->count();
        return compact('total', 'submitted', 'graded');
    }
}
