<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'nip_nuptk',
        'nisn',
        'nis',
        'email',
        'no_hp',
        'parent_name',
        'parent_phone',
        'password',
        'role',
        'grade',
        'major',
        'class_id',
        'beban_mengajar',
        'taught_majors',
        'is_pkl',
        'is_teaching_factory',
        'avatar',
        'is_active',
        'is_alumni',
        'graduation_year',
        'alumni_notes',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'is_pkl' => 'boolean',
            'is_teaching_factory' => 'boolean',
            'is_alumni' => 'boolean',
            'taught_majors' => 'array',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Relationships
     */
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class, 'created_by');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function importLogs(): HasMany
    {
        return $this->hasMany(ImportLog::class);
    }

    public function assessmentResponses(): HasMany
    {
        return $this->hasMany(StudentAssessmentResponse::class);
    }

    public function learningProfiles(): HasMany
    {
        return $this->hasMany(StudentLearningProfile::class);
    }

    /**
     * Get the class for a student
     */
    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /**
     * Get classes where this teacher is homeroom teacher (wali kelas)
     */
    public function isWaliKelas(): bool
    {
        return $this->homeroomClasses()->exists();
    }

    public function homeroomClasses()
    {
        return $this->hasMany(SchoolClass::class, 'homeroom_teacher_id');
    }

    /**
     * Get subjects taught by this teacher (many-to-many)
     */
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'teacher_subjects', 'user_id', 'subject_id');
    }

    /**
     * Get teacher subject requirements (monitoring kelengkapan perangkat ajar)
     */
    public function teacherRequirements()
    {
        return $this->hasMany(TeacherSubjectRequirement::class, 'teacher_id');
    }

    /**
     * Get teaching materials created by this user
     */
    public function teachingMaterials()
    {
        return $this->hasMany(TeachingMaterial::class, 'created_by');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function scopeAlumni($query)
    {
        return $query->where('is_alumni', true);
    }

    public function scopeActiveStudents($query)
    {
        return $query->where('role', 'siswa')
            ->where('is_active', true)
            ->where('is_alumni', false);
    }

    /**
     * Helpers
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isWakaKurikulum(): bool
    {
        return $this->role === 'waka_kurikulum';
    }

    public function isKepalaSekolah(): bool
    {
        return $this->role === 'kepala_sekolah';
    }

    public function isGuru(): bool
    {
        return $this->role === 'guru';
    }

    public function isSiswa(): bool
    {
        return $this->role === 'siswa';
    }

    public function isAlumni(): bool
    {
        return $this->is_alumni === true;
    }

    public function canManageActivities(): bool
    {
        return in_array($this->role, ['admin', 'waka_kurikulum', 'kepala_sekolah']);
    }

    public function canManageUsers(): bool
    {
        return in_array($this->role, ['admin', 'kepala_sekolah']);
    }

    public function canManageAssessments(): bool
    {
        return in_array($this->role, ['admin', 'waka_kurikulum']);
    }

    public function canViewAllStudentProfiles(): bool
    {
        return in_array($this->role, ['admin', 'waka_kurikulum', 'kepala_sekolah', 'guru']);
    }

    public function getMajorLabel(): string
    {
        return match($this->major) {
            'MPLB' => 'Manajemen Perkantoran dan Layanan Bisnis',
            'AKL' => 'Akuntansi dan Keuangan Lembaga',
            'BUSANA' => 'Tata Busana',
            default => '-',
        };
    }

    public function getFullClassLabel(): string
    {
        if ($this->schoolClass) {
            return $this->schoolClass->name;
        }
        if ($this->grade && $this->major) {
            return "{$this->grade} {$this->major}";
        }
        return $this->grade ?? '-';
    }

    /**
     * Get taught majors as comma-separated string
     */
    public function getTaughtMajorsLabel(): string
    {
        if (!$this->taught_majors || empty($this->taught_majors)) {
            return '-';
        }
        return implode(', ', $this->taught_majors);
    }

    /**
     * Get PKL status label
     */
    public function isInPklClass()
    {
        if (!$this->class_id) return false;
        $academicYear = \App\Models\AcademicYear::where('is_active', true)->first();
        if (!$academicYear) return false;
        return \App\Models\TeachingSchedule::where('academic_year_id', $academicYear->id)
            ->where('class_id', $this->class_id)
            ->where('is_active', false)
            ->exists();
    }

    public function getPklStatusLabel(): string
    {
        return $this->is_pkl ? 'Ya' : 'Tidak';
    }

    /**
     * Get Teaching Factory status label
     */
    public function getTeachingFactoryStatusLabel(): string
    {
        return $this->is_teaching_factory ? 'Ya' : 'Tidak';
    }

    /**
     * Check if teacher teaches in a specific major
     */
    public function teachesInMajor(string $major): bool
    {
        if (!$this->taught_majors) {
            return false;
        }
        return in_array($major, $this->taught_majors);
    }

    /**
     * Check if user can access a teaching material
     */
    public function canAccessMaterial($material): bool
    {
        // Admin & Waka can access all materials
        if ($this->canManageUsers() || $this->isWakaKurikulum()) {
            return true;
        }

        // Owner can access own materials
        if ($material->created_by === $this->id) {
            return true;
        }

        // Public approved materials can be accessed by all authenticated users
        if ($material->is_public && $material->status === 'approved') {
            return true;
        }

        // Otherwise, no access
        return false;
    }
}
