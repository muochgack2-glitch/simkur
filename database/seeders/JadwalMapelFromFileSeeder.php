<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\Subject;
use App\Models\TeachingSchedule;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JadwalMapelFromFileSeeder extends Seeder
{
    private $createdCount = 0;
    private $skippedCount = 0;
    private $errorCount = 0;
    private $errors = [];
    
    /**
     * Name mapping for teachers with different formats in TXT vs Database
     */
    private $teacherNameMapping = [
        'Debby Furi Wijayanti, S. Pd.' => 'Debby Furi Wijayanti, S.Pd',
        'Rinawati, S. Pd.' => 'Rinawati, S.Pd',
        'Tri Mulyaniningsih, S.E.' => 'Tri Mulyaningsih, S.E',
        'Yully Setyo. A., S.Pd.' => 'Yully Setyo A., S.Pd',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting Jadwal Mapel Seeder from TXT file...');
        $this->command->info('');

        // Read and parse file
        $filePath = base_path('Jadwal_Guru_Terintegrasi_FIX.txt');
        
        if (!file_exists($filePath)) {
            $this->command->error("❌ File not found: {$filePath}");
            return;
        }

        $content = file_get_contents($filePath);
        $schedules = $this->parseScheduleFile($content);

        $teacherCount = count($schedules);
        $this->command->info("📄 Parsed {$teacherCount} teachers from file");
        $this->command->info('');

        // Get active academic year and semester
        $academicYear = AcademicYear::where('is_active', true)->first();
        
        if (!$academicYear) {
            // Fallback: get latest academic year
            $academicYear = AcademicYear::orderBy('start_date', 'desc')->first();
        }
        
        $semester = Semester::where('academic_year_id', $academicYear->id)
            ->orderBy('start_date', 'desc')
            ->first();

        if (!$academicYear || !$semester) {
            $this->command->error('❌ No academic year or semester found!');
            return;
        }

        $this->command->info("📅 Using: {$academicYear->year} - Semester {$semester->type}");
        $this->command->info('');

        // Process schedules
        DB::beginTransaction();
        
        try {
            foreach ($schedules as $index => $schedule) {
                $this->command->info(sprintf(
                    '[%d/%d] Processing: %s',
                    $index + 1,
                    count($schedules),
                    $schedule['teacher_name']
                ));

                $this->processTeacherSchedule($schedule, $semester);
            }

            DB::commit();
            
            $this->command->info('');
            $this->command->info('✅ SEEDING COMPLETED!');
            $this->command->info("   Created: {$this->createdCount} schedules");
            $this->command->info("   Skipped: {$this->skippedCount} schedules (duplicates)");
            $this->command->info("   Errors: {$this->errorCount} schedules");
            
            if ($this->errorCount > 0) {
                $this->command->warn('');
                $this->command->warn('⚠️  ERRORS ENCOUNTERED:');
                foreach ($this->errors as $error) {
                    $this->command->error("   - {$error}");
                }
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('');
            $this->command->error('❌ SEEDING FAILED!');
            $this->command->error("   Error: {$e->getMessage()}");
            $this->command->error("   File: {$e->getFile()}");
            $this->command->error("   Line: {$e->getLine()}");
            
            Log::error('Jadwal Seeder Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Parse schedule file content
     */
    private function parseScheduleFile(string $content): array
    {
        $schedules = [];
        $lines = explode("\n", $content);
        
        $currentTeacher = null;
        $currentDay = null;
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            if (empty($line) || str_starts_with($line, '=') || str_starts_with($line, 'JADWAL') || str_starts_with($line, 'Sumber')) {
                continue;
            }
            
            // Teacher line (starts with number)
            if (preg_match('/^(\d+)\.\s+(.+)$/', $line, $matches)) {
                if ($currentTeacher) {
                    $schedules[] = $currentTeacher;
                }
                
                $currentTeacher = [
                    'teacher_name' => trim($matches[2]),
                    'days' => [],
                ];
                $currentDay = null;
                continue;
            }
            
            // Day line (ends with colon)
            if (preg_match('/^(Senin|Selasa|Rabu|Kamis|Jumat|Sabtu):$/', $line, $matches)) {
                $currentDay = $matches[1];
                $currentTeacher['days'][$currentDay] = [];
                continue;
            }
            
            // Schedule line (starts with dash and "Jam ke-")
            if (preg_match('/^-\s+Jam ke-(\d+)(?:\s+s\/d\s+(\d+))?:\s+(.+?)\s+-\s+(.+)$/', $line, $matches)) {
                if ($currentDay && $currentTeacher) {
                    $slotStart = (int)$matches[1];
                    $slotEnd = $matches[2] ? (int)$matches[2] : $slotStart;
                    $className = trim($matches[3]);
                    $subjectName = trim($matches[4]);
                    
                    $currentTeacher['days'][$currentDay][] = [
                        'slot_start' => $slotStart,
                        'slot_end' => $slotEnd,
                        'class' => $className,
                        'subject' => $subjectName,
                    ];
                }
            }
        }
        
        // Add last teacher
        if ($currentTeacher) {
            $schedules[] = $currentTeacher;
        }
        
        return $schedules;
    }

    /**
     * Process single teacher schedule
     */
    private function processTeacherSchedule(array $schedule, Semester $semester): void
    {
        // Find or create teacher
        $teacher = $this->findOrCreateTeacher($schedule['teacher_name']);
        
        if (!$teacher) {
            $this->errors[] = "Teacher not found/created: {$schedule['teacher_name']}";
            $this->errorCount++;
            return;
        }

        // Process each day
        foreach ($schedule['days'] as $day => $sessions) {
            foreach ($sessions as $session) {
                try {
                    $this->createTeachingSchedule(
                        $teacher,
                        $semester,
                        $day,
                        $session
                    );
                } catch (\Exception $e) {
                    $this->errors[] = sprintf(
                        "%s - %s - %s: %s",
                        $schedule['teacher_name'],
                        $day,
                        $session['class'],
                        $e->getMessage()
                    );
                    $this->errorCount++;
                }
            }
        }
    }

    /**
     * Find or create teacher
     */
    private function findOrCreateTeacher(string $teacherName): ?User
    {
        // Check if there's a mapping for this name
        $mappedName = $this->teacherNameMapping[$teacherName] ?? null;
        
        // Clean name
        $cleanName = $this->cleanTeacherName($teacherName);
        
        // Try exact match first (with original name)
        $teacher = User::where('name', $teacherName)
            ->where(function($q) {
                $q->where('role', 'Guru')
                  ->orWhere('role', 'guru')
                  ->orWhereNull('role');
            })
            ->first();
        
        if ($teacher) {
            return $teacher;
        }
        
        // Try with mapped name
        if ($mappedName) {
            $teacher = User::where('name', $mappedName)
                ->where(function($q) {
                    $q->where('role', 'Guru')
                      ->orWhere('role', 'guru')
                      ->orWhereNull('role');
                })
                ->first();
            
            if ($teacher) {
                return $teacher;
            }
        }
        
        // Try fuzzy match with LIKE (clean name)
        $teacher = User::where(function($q) use ($cleanName) {
                $q->where('name', 'LIKE', "%{$cleanName}%")
                  ->orWhere('name', 'LIKE', "%" . str_replace(' ', '%', $cleanName) . "%");
            })
            ->where(function($q) {
                $q->where('role', 'Guru')
                  ->orWhere('role', 'guru')
                  ->orWhereNull('role');
            })
            ->first();
        
        if ($teacher) {
            return $teacher;
        }
        
        // Try without title suffixes for more flexible matching
        $nameWithoutSuffix = preg_replace('/(,?\s*S\.\s*Pd\.?.*|,?\s*S\.\s*E\.?.*|,?\s*A\.\s*Md\.?.*)$/i', '', $teacherName);
        $nameWithoutSuffix = trim($nameWithoutSuffix);
        
        if ($nameWithoutSuffix !== $teacherName) {
            $teacher = User::where('name', 'LIKE', "{$nameWithoutSuffix}%")
                ->where(function($q) {
                    $q->where('role', 'Guru')
                      ->orWhere('role', 'guru')
                      ->orWhereNull('role');
                })
                ->first();
            
            if ($teacher) {
                return $teacher;
            }
        }

        // OPTION: Auto-create teacher if still not found
        // Uncomment block below to enable auto-creation of missing teachers
        /*
        $this->command->warn("   ⚠️  Teacher not found, creating: {$teacherName}");
        
        // Generate username from name
        $username = strtolower(str_replace([' ', '.', ',', '(', ')'], '', $cleanName));
        $username = substr($username, 0, 20);
        
        // Check if username exists
        $counter = 1;
        $originalUsername = $username;
        while (User::where('username', $username)->exists()) {
            $username = $originalUsername . $counter;
            $counter++;
        }

        // Generate email
        $email = $username . '@smkpgriblora.sch.id';
        
        // Create user
        $teacher = User::create([
            'name' => $teacherName,
            'username' => $username,
            'email' => $email,
            'password' => bcrypt('password'), // Default password
            'role' => 'Guru',
            'is_active' => true,
        ]);

        return $teacher;
        */
        
        // Return null if teacher not found (skip this schedule)
        return null;
    }

    /**
     * Clean teacher name (remove certain titles, preserve others)
     */
    private function cleanTeacherName(string $name): string
    {
        // Only remove title prefixes (Drs., Dr.), keep suffix titles
        $name = preg_replace('/^(Drs\.|Dr\.)\s+/', '', $name);
        
        // Normalize whitespace
        $name = trim($name);
        $name = preg_replace('/\s+/', ' ', $name);
        
        return $name;
    }

    /**
     * Create teaching schedule
     */
    private function createTeachingSchedule(
        User $teacher,
        Semester $semester,
        string $day,
        array $session
    ): void {
        // Find school class
        $schoolClass = SchoolClass::where('name', $session['class'])
            ->where('academic_year_id', $semester->academic_year_id)
            ->first();

        if (!$schoolClass) {
            throw new \Exception("Class not found: {$session['class']}");
        }

        // Find or create subject
        $subject = Subject::firstOrCreate(
            ['name' => $session['subject']],
            [
                'code' => $this->generateSubjectCode($session['subject']),
                'description' => $session['subject'],
                'is_active' => true,
            ]
        );

        // Get time slots - PRODUCTION SCHEMA
        // Structure: id, name, start_time, end_time, order, day_of_week, is_active
        $slots = [];
        for ($slot = $session['slot_start']; $slot <= $session['slot_end']; $slot++) {
            $timeSlot = TimeSlot::where('name', 'Jam ke-' . $slot)
                ->where('day_of_week', $day)
                ->where('is_active', 1)
                ->first();
                
            if ($timeSlot) {
                $slots[] = $timeSlot->id;
            } else {
                // Log missing time slot for debugging
                Log::warning("Time slot not found", [
                    'slot' => "Jam ke-{$slot}",
                    'day_of_week' => $day,
                ]);
            }
        }

        if (empty($slots)) {
            throw new \Exception("No time slots found for Jam ke-{$session['slot_start']}-{$session['slot_end']} on {$day}");
        }

        // Check if schedule already exists
        $exists = TeachingSchedule::where('semester_id', $semester->id)
            ->where('teacher_id', $teacher->id)
            ->where('school_class_id', $schoolClass->id)
            ->where('subject_id', $subject->id)
            ->where('day_of_week', $day)
            ->where('time_slot_id', json_encode($slots))
            ->exists();

        if ($exists) {
            $this->skippedCount++;
            return;
        }

        // Create schedule
        TeachingSchedule::create([
            'semester_id' => $semester->id,
            'teacher_id' => $teacher->id,
            'school_class_id' => $schoolClass->id,
            'subject_id' => $subject->id,
            'day_of_week' => $day,
            'time_slot_id' => $slots, // JSON array
            'room' => null,
            'notes' => "Imported from Jadwal_Guru_Terintegrasi_FIX.txt",
        ]);

        $this->createdCount++;
    }

    /**
     * Generate subject code from name
     */
    private function generateSubjectCode(string $subjectName): string
    {
        // Remove common words
        $name = str_replace(['dan', 'atau', 'untuk'], '', strtolower($subjectName));
        
        // Take first letters of each word
        $words = explode(' ', $name);
        $code = '';
        
        foreach ($words as $word) {
            if (strlen($word) > 0) {
                $code .= strtoupper(substr($word, 0, 1));
            }
        }
        
        // If code too short, use first 3-5 chars of full name
        if (strlen($code) < 3) {
            $code = strtoupper(substr(str_replace(' ', '', $subjectName), 0, 5));
        }
        
        // Ensure unique
        $originalCode = $code;
        $counter = 1;
        while (Subject::where('code', $code)->exists()) {
            $code = $originalCode . $counter;
            $counter++;
        }
        
        return $code;
    }
}
