<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\AssessmentStudentSession;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'assessment_type',
        'academic_year_id',
        'semester_id',
        'target_grades',
        'target_majors',
        'is_active',
        'is_published',
        'start_date',
        'end_date',
        'created_by',
        // Quiz fields
        'start_time',
        'end_time',
        'shuffle_questions',
        'shuffle_options',
        'allow_retry',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'target_grades' => 'array',
            'target_majors' => 'array',
            'is_active' => 'boolean',
            'is_published' => 'boolean',
            'start_date' => 'date',
            'end_date' => 'date',
            'start_time' => 'string',
            'end_time' => 'string',
            'shuffle_questions' => 'boolean',
            'shuffle_options' => 'boolean',
            'allow_retry' => 'boolean',
            'is_published' => 'boolean',
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(AssessmentQuestion::class)->orderBy('order_number');
    }

    public function studentProfiles(): HasMany
    {
        return $this->hasMany(StudentLearningProfile::class);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(StudentAssessmentResponse::class);
    }

    public function studentSessions(): HasMany
    {
        return $this->hasMany(AssessmentStudentSession::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForGrade($query, ?string $grade)
    {
        if (!$grade) {
            return $query;
        }
        
        return $query->where(function ($q) use ($grade) {
            $q->whereNull('target_grades')
              ->orWhereJsonContains('target_grades', $grade);
        });
    }

    public function scopeOngoing($query)
    {
        $today = now()->toDateString();
        return $query->where('start_date', '<=', $today)
                     ->where('end_date', '>=', $today);
    }

    /**
     * Helpers
     */
    /**
     * Status asesmen: upcoming | ongoing | closed
     * Digunakan oleh teacher index badge dan student index.
     */
    public function getStatusAttribute(): string
    {
        $now   = \Carbon\Carbon::now();
        $start = \Carbon\Carbon::parse($this->start_date->toDateString() . ' ' . ($this->start_time ?? '00:00:00'));
        $end   = \Carbon\Carbon::parse($this->end_date->toDateString() . ' ' . ($this->end_time ?? '23:59:59'));

        if ($now->lt($start)) {
            return 'upcoming';
        }
        if ($now->gt($end)) {
            return 'closed';
        }
        return 'ongoing';
    }

    public function isVark(): bool
    {
        return $this->assessment_type === 'vark';
    }

    public function isDiagnostic(): bool
    {
        return $this->assessment_type === 'diagnostic';
    }

    public function getTypeLabel(): string
    {
        return match($this->assessment_type) {
            'vark' => 'VARK (Gaya Belajar)',
            'diagnostic' => 'Diagnostik (Kesiapan Belajar)',
            default => 'Unknown',
        };
    }

    public function isOngoing(): bool
    {
        $today = now();
        return $this->start_date <= $today && $this->end_date >= $today;
    }

    public function hasCompletedByStudent(int $userId): bool
    {
        return $this->studentProfiles()->where('user_id', $userId)->exists();
    }

    public function getTotalQuestionsAttribute(): int
    {
        return $this->questions()->count();
    }

    public function getCompletionPercentageAttribute(): float
    {
        $totalStudents = User::where('role', 'siswa')
            ->when($this->target_grades, function ($query) {
                $query->whereIn('grade', $this->target_grades);
            })
            ->count();

        if ($totalStudents === 0) {
            return 0;
        }

        $completedStudents = $this->studentProfiles()->count();

        return round(($completedStudents / $totalStudents) * 100, 2);
    }

    public function isForAllGrades(): bool
    {
        return is_null($this->target_grades) || 
               (is_array($this->target_grades) && count($this->target_grades) === 3);
    }

    public function isForAllMajors(): bool
    {
        return is_null($this->target_majors) || 
               (is_array($this->target_majors) && count($this->target_majors) === 3);
    }

    public function getTargetGradesLabel(): string
    {
        if ($this->isForAllGrades()) {
            return 'Semua Kelas';
        }
        
        if (empty($this->target_grades)) {
            return 'Semua Kelas';
        }
        
        return 'Kelas ' . implode(', ', $this->target_grades);
    }

    public function getTargetMajorsLabel(): string
    {
        if ($this->isForAllMajors()) {
            return 'Semua Jurusan';
        }
        
        if (empty($this->target_majors)) {
            return 'Semua Jurusan';
        }
        
        return implode(', ', $this->target_majors);
    }

    public function isForStudent(User $student): bool
    {
        // Check grade
        if (!$this->isForAllGrades() && $student->grade) {
            if (!in_array($student->grade, $this->target_grades ?? [])) {
                return false;
            }
        }

        // Check major
        if (!$this->isForAllMajors() && $student->major) {
            if (!in_array($student->major, $this->target_majors ?? [])) {
                return false;
            }
        }

        return true;
    }
}

