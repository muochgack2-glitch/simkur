<?php

namespace Database\Seeders;

use App\Models\TeachingJournal;
use App\Models\StudentAttendance;
use App\Models\User;
use App\Models\SchoolClass;
use App\Models\AcademicYear;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SampleTeachingJournalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get active academic year
        $academicYear = AcademicYear::where('is_active', true)->first();
        
        if (!$academicYear) {
            $this->command->error('No active academic year found!');
            return;
        }

        // Get some teachers with their subjects
        $teachers = User::where('role', 'guru')->with('subjects')->limit(5)->get();
        
        if ($teachers->isEmpty()) {
            $this->command->error('No teachers found!');
            return;
        }

        // Get some classes
        $classes = SchoolClass::with('students')->limit(3)->get();
        
        if ($classes->isEmpty()) {
            $this->command->error('No classes found!');
            return;
        }

        $journalCount = 0;

        // Create sample journals for last 2 weeks
        foreach ($teachers as $teacher) {
            if ($teacher->subjects->isEmpty()) {
                continue;
            }

            // Create 3-5 journals per teacher
            $numJournals = rand(3, 5);
            
            for ($i = 0; $i < $numJournals; $i++) {
                $class = $classes->random();
                $subject = $teacher->subjects->random();
                $date = Carbon::now()->subDays(rand(1, 14));

                // Skip if class has no students
                if ($class->students->isEmpty()) {
                    continue;
                }

                $journal = TeachingJournal::create([
                    'teacher_id' => $teacher->id,
                    'class_id' => $class->id,
                    'subject_id' => $subject->id,
                    'academic_year_id' => $academicYear->id,
                    'date' => $date,
                    'time_slot' => '07:00 - 08:30',
                    'learning_objective' => 'Peserta didik mampu memahami dan menjelaskan konsep dasar ' . $subject->name . ' pertemuan ke-' . rand(1, 12),
                    'topic' => 'Materi tentang ' . $subject->name . ' pertemuan ke-' . rand(1, 12),
                    'teaching_method' => ['Ceramah', 'Diskusi', 'Praktik', 'Presentasi'][rand(0, 3)],
                    'notes' => rand(0, 1) ? 'Siswa antusias mengikuti pembelajaran' : null,
                    'total_students' => $class->students->count(),
                ]);

                // Create attendance for each student
                foreach ($class->students as $student) {
                    // Randomize attendance status (mostly hadir)
                    $rand = rand(1, 100);
                    if ($rand <= 85) {
                        $status = 'hadir';
                    } elseif ($rand <= 92) {
                        $status = 'sakit';
                    } elseif ($rand <= 97) {
                        $status = 'izin';
                    } else {
                        $status = 'alpha';
                    }

                    StudentAttendance::create([
                        'teaching_journal_id' => $journal->id,
                        'student_id' => $student->id,
                        'status' => $status,
                        'notes' => $status !== 'hadir' ? 'Keterangan ' . $status : null,
                    ]);
                }

                // Update attendance stats
                $journal->updateAttendanceStats();
                $journalCount++;

                $this->command->info("✓ Journal created: {$teacher->name} - {$class->name} - {$subject->name}");
            }
        }

        $this->command->info("\n✅ Sample teaching journals seeding completed!");
        $this->command->info("📊 Total journals created: {$journalCount}");
    }
}
