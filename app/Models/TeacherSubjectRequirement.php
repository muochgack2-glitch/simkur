<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherSubjectRequirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'subject_id',
        'academic_year_id',
        'has_cp',
        'has_atp',
        'has_kktp',
        'has_prota',
        'has_prosem',
        'has_modul_ajar',
        'has_modul_projek',
        'completion_percentage',
        'last_upload_at',
        'completed_at',
    ];

    protected $casts = [
        'has_cp' => 'boolean',
        'has_atp' => 'boolean',
        'has_kktp' => 'boolean',
        'has_prota' => 'boolean',
        'has_prosem' => 'boolean',
        'has_modul_ajar' => 'boolean',
        'has_modul_projek' => 'boolean',
        'last_upload_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Relationships
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    // Constants
    public const REQUIRED_DOCUMENTS = [
        'cp' => 'CP (Capaian Pembelajaran)',
        'atp' => 'ATP (Alur Tujuan Pembelajaran)',
        'kktp' => 'KKTP (Kriteria Ketercapaian)',
        'prota' => 'PROTA (Program Tahunan)',
        'prosem' => 'PROSEM (Program Semester)',
        'modul_ajar' => 'Modul Ajar',
        'modul_projek' => 'Modul Projek',
    ];

    // Helper Methods
    public function calculateCompletion(): int
    {
        $total = count(self::REQUIRED_DOCUMENTS);
        $completed = 0;

        if ($this->has_cp) $completed++;
        if ($this->has_atp) $completed++;
        if ($this->has_kktp) $completed++;
        if ($this->has_prota) $completed++;
        if ($this->has_prosem) $completed++;
        if ($this->has_modul_ajar) $completed++;
        if ($this->has_modul_projek) $completed++;

        return round(($completed / $total) * 100);
    }

    public function updateCompletion(): void
    {
        $this->completion_percentage = $this->calculateCompletion();
        
        if ($this->completion_percentage === 100 && !$this->completed_at) {
            $this->completed_at = now();
        } elseif ($this->completion_percentage < 100) {
            $this->completed_at = null;
        }
        
        $this->save();
    }

    public function getMissingDocuments(): array
    {
        $missing = [];

        if (!$this->has_cp) $missing[] = 'cp';
        if (!$this->has_atp) $missing[] = 'atp';
        if (!$this->has_kktp) $missing[] = 'kktp';
        if (!$this->has_prota) $missing[] = 'prota';
        if (!$this->has_prosem) $missing[] = 'prosem';
        if (!$this->has_modul_ajar) $missing[] = 'modul_ajar';
        if (!$this->has_modul_projek) $missing[] = 'modul_projek';

        return $missing;
    }

    public function getCompletedDocuments(): array
    {
        $completed = [];

        if ($this->has_cp) $completed[] = 'cp';
        if ($this->has_atp) $completed[] = 'atp';
        if ($this->has_kktp) $completed[] = 'kktp';
        if ($this->has_prota) $completed[] = 'prota';
        if ($this->has_prosem) $completed[] = 'prosem';
        if ($this->has_modul_ajar) $completed[] = 'modul_ajar';
        if ($this->has_modul_projek) $completed[] = 'modul_projek';

        return $completed;
    }

    public function isComplete(): bool
    {
        return $this->completion_percentage === 100;
    }

    public function getStatusColorAttribute(): string
    {
        if ($this->completion_percentage === 100) return 'green';
        if ($this->completion_percentage >= 70) return 'yellow';
        if ($this->completion_percentage >= 40) return 'orange';
        return 'red';
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->completion_percentage === 100) return 'Lengkap';
        if ($this->completion_percentage >= 70) return 'Hampir Lengkap';
        if ($this->completion_percentage >= 40) return 'Sebagian';
        return 'Belum Lengkap';
    }

    // Scopes
    public function scopeForTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    public function scopeForAcademicYear($query, $academicYearId = null)
    {
        if ($academicYearId === null) {
            $academicYearId = AcademicYear::where('is_active', true)->value('id');
        }
        return $query->where('academic_year_id', $academicYearId);
    }

    public function scopeComplete($query)
    {
        return $query->where('completion_percentage', 100);
    }

    public function scopeIncomplete($query)
    {
        return $query->where('completion_percentage', '<', 100);
    }

    // Static Helper: Sync dari teaching_schedules
    public static function syncFromSchedules($academicYearId = null)
    {
        if ($academicYearId === null) {
            $academicYearId = AcademicYear::where('is_active', true)->value('id');
        }

        if (!$academicYearId) {
            return;
        }

        // Ambil unique teacher + subject dari teaching_schedules
        $assignments = TeachingSchedule::where('academic_year_id', $academicYearId)
            ->where('is_active', true)
            ->select('teacher_id', 'subject_id')
            ->distinct()
            ->get();

        foreach ($assignments as $assignment) {
            self::firstOrCreate([
                'teacher_id' => $assignment->teacher_id,
                'subject_id' => $assignment->subject_id,
                'academic_year_id' => $academicYearId,
            ]);
        }
    }

    // Static Helper: Update status dari TeachingMaterial
    public static function updateFromMaterials($teacherId, $subjectId, $academicYearId)
    {
        $requirement = self::firstOrCreate([
            'teacher_id' => $teacherId,
            'subject_id' => $subjectId,
            'academic_year_id' => $academicYearId,
        ]);

        // Cek keberadaan setiap dokumen
        $materials = TeachingMaterial::where('created_by', $teacherId)
            ->where('subject_id', $subjectId)
            ->where('academic_year_id', $academicYearId)
            ->whereIn('status', ['approved', 'pending_approval'])
            ->get();

        $requirement->has_cp = $materials->where('category', 'cp')->isNotEmpty();
        $requirement->has_atp = $materials->where('category', 'atp')->isNotEmpty();
        $requirement->has_kktp = $materials->where('category', 'kktp')->isNotEmpty();
        $requirement->has_prota = $materials->where('category', 'prota')->isNotEmpty();
        $requirement->has_prosem = $materials->where('category', 'prosem')->isNotEmpty();
        $requirement->has_modul_ajar = $materials->where('category', 'modul_ajar')->isNotEmpty();
        $requirement->has_modul_projek = $materials->where('category', 'modul_projek')->isNotEmpty();

        if ($materials->isNotEmpty()) {
            $requirement->last_upload_at = $materials->max('created_at');
        }

        $requirement->updateCompletion();
    }
}
