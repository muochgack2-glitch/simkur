<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'name',
        'grade',
        'major',
        'rombel',
        'academic_year_id',
        'homeroom_teacher_id',
        'capacity',
        'room',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the academic year for this class
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get the homeroom teacher (wali kelas)
     */
    public function homeroomTeacher()
    {
        return $this->belongsTo(User::class, 'homeroom_teacher_id');
    }

    /**
     * Get students in this class
     */
    public function students()
    {
        return $this->hasMany(User::class, 'class_id')
            ->where('role', 'siswa')
            ->orderBy('name');
    }

    /**
     * Get teaching journals for this class
     */
    public function teachingJournals()
    {
        return $this->hasMany(TeachingJournal::class, 'class_id');
    }

    /**
     * Scope for active classes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope by grade
     */
    public function scopeByGrade($query, $grade)
    {
        return $query->where('grade', $grade);
    }

    /**
     * Scope by major
     */
    public function scopeByMajor($query, $major)
    {
        return $query->where('major', $major);
    }

    /**
     * Get full class name with academic year
     */
    public function getFullName(): string
    {
        return "{$this->name} ({$this->academicYear->name})";
    }

    /**
     * Get major label
     */
    public function getMajorLabel(): string
    {
        return match($this->major) {
            'MPLB' => 'Manajemen Perkantoran dan Layanan Bisnis',
            'AKL' => 'Akuntansi dan Keuangan Lembaga',
            'BUSANA' => 'Tata Busana',
            default => $this->major,
        };
    }

    /**
     * Get student count
     */
    public function getStudentCount(): int
    {
        return $this->students()->count();
    }

    /**
     * Check if class is full
     */
    public function isFull(): bool
    {
        return $this->getStudentCount() >= $this->capacity;
    }

    /**
     * Get available slots
     */
    public function getAvailableSlots(): int
    {
        return max(0, $this->capacity - $this->getStudentCount());
    }

    /**
     * Helper method to generate standard class names
     * Rules:
     * - If only 1 rombel: "X MPLB" (no number)
     * - If 2+ rombel: "X MPLB 1", "X MPLB 2" (with number)
     */
    public static function generateClassName(string $grade, string $major, ?int $rombel = null): string
    {
        if ($rombel === null || $rombel === 0) {
            return "{$grade} {$major}";
        }
        return "{$grade} {$major} {$rombel}";
    }

    /**
     * Auto-generate 9 standard classes for a given academic year
     * Now supports configuring number of rombel per major for grade X
     * 
     * @param int $academicYearId
     * @param array $gradeXRombelConfig ['MPLB' => 2, 'AKL' => 1, 'BUSANA' => 3]
     */
    public static function autoGenerateClasses(int $academicYearId, array $gradeXRombelConfig = []): void
    {
        $grades = ['X', 'XI', 'XII'];
        $majors = ['MPLB', 'AKL', 'BUSANA'];

        foreach ($grades as $grade) {
            foreach ($majors as $major) {
                // Special handling for grade X with multiple rombel configuration
                if ($grade === 'X' && isset($gradeXRombelConfig[$major])) {
                    $rombelCount = (int) $gradeXRombelConfig[$major];
                    
                    if ($rombelCount === 1) {
                        // Single rombel: "X MPLB" (no number)
                        self::firstOrCreate(
                            [
                                'academic_year_id' => $academicYearId,
                                'grade' => $grade,
                                'major' => $major,
                                'rombel' => null,
                            ],
                            [
                                'name' => self::generateClassName($grade, $major),
                                'capacity' => 36,
                                'is_active' => true,
                            ]
                        );
                    } else {
                        // Multiple rombel: "X MPLB 1", "X MPLB 2", etc
                        for ($i = 1; $i <= $rombelCount; $i++) {
                            self::firstOrCreate(
                                [
                                    'academic_year_id' => $academicYearId,
                                    'grade' => $grade,
                                    'major' => $major,
                                    'rombel' => $i,
                                ],
                                [
                                    'name' => self::generateClassName($grade, $major, $i),
                                    'capacity' => 36,
                                    'is_active' => true,
                                ]
                            );
                        }
                    }
                } else {
                    // Default: single rombel for XI and XII
                    self::firstOrCreate(
                        [
                            'academic_year_id' => $academicYearId,
                            'grade' => $grade,
                            'major' => $major,
                            'rombel' => null,
                        ],
                        [
                            'name' => self::generateClassName($grade, $major),
                            'capacity' => 36,
                            'is_active' => true,
                        ]
                    );
                }
            }
        }
    }
}
