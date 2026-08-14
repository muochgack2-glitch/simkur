<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\TeachingJournal;
use App\Models\TeachingSchedule;
use App\Models\TimeSlot;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class JournalMonitoringDebugSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('=== JOURNAL MONITORING DEBUG SEEDER v2 ===');
        $this->command->newLine();

        $academicYear = AcademicYear::firstOrCreate(
            ['year' => '2026/2027'],
            ['start_date' => '2026-07-01', 'end_date' => '2027-06-30', 'is_active' => true]
        );
        AcademicYear::where('id', '!=', $academicYear->id)->update(['is_active' => false]);
        $academicYear->update(['is_active' => true]);
        $this->command->info("Academic Year: {$academicYear->year} (ID: {$academicYear->id})");

        $teacher = User::firstOrCreate(
            ['username' => 'adela.debug'],
            ['name' => 'Adela Wulan Kurniasari, S.Pd', 'email' => 'adela@test.local', 'password' => Hash::make('password'), 'role' => 'guru', 'is_active' => true]
        );
        $this->command->info("Teacher: {$teacher->name} (ID: {$teacher->id})");

        // Subject A (di schedule)
        $subjectA = Subject::firstOrCreate(['name' => 'Dasar-Dasar MPLB'], ['code' => 'DD-MPLB', 'is_active' => true]);
        // Subject B (mungkin yang dipilih guru di form)
        $subjectB = Subject::firstOrCreate(['name' => 'Projek Kreatif'], ['code' => 'PK', 'is_active' => true]);
        $this->command->info("Subject A (schedule): {$subjectA->name} (ID: {$subjectA->id})");
        $this->command->info("Subject B (alt):      {$subjectB->name} (ID: {$subjectB->id})");

        // Attach subjects to teacher via pivot
        DB::table('teacher_subjects')->where('user_id', $teacher->id)->delete();
        DB::table('teacher_subjects')->insert([
            ['user_id' => $teacher->id, 'subject_id' => $subjectA->id],
            ['user_id' => $teacher->id, 'subject_id' => $subjectB->id],
        ]);

        // Classes
        $classA = SchoolClass::firstOrCreate(
            ['name' => 'X MPLB', 'academic_year_id' => $academicYear->id],
            ['grade' => 'X', 'major' => 'MPLB', 'is_active' => true]
        );
        $classB = SchoolClass::firstOrCreate(
            ['name' => 'XI MPLB', 'academic_year_id' => $academicYear->id],
            ['grade' => 'XI', 'major' => 'MPLB', 'is_active' => true]
        );
        $this->command->info("Class A (schedule): {$classA->name} (ID: {$classA->id})");
        $this->command->info("Class B (alt):      {$classB->name} (ID: {$classB->id})");

        // Time Slots
        $slot = TimeSlot::firstOrCreate(
            ['name' => 'Jam ke-1', 'day_of_week' => 'Jumat'],
            ['start_time' => '07:00', 'end_time' => '07:40', 'order' => 2, 'is_active' => true]
        );

        // Schedule: X MPLB + Dasar-Dasar MPLB
        TeachingSchedule::where('teacher_id', $teacher->id)->where('day_of_week', 'Jumat')->delete();
        $schedule = TeachingSchedule::create([
            'teacher_id' => $teacher->id,
            'class_id' => $classA->id,       // X MPLB
            'subject_id' => $subjectA->id,    // Dasar-Dasar MPLB
            'day_of_week' => 'Jumat',
            'academic_year_id' => $academicYear->id,
            'time_slot_id' => $slot->id,
            'is_active' => true,
        ]);
        $this->command->info("Schedule: class={$classA->name}, subject={$subjectA->name}");

        $today = Carbon::today()->toDateString();
        TeachingJournal::where('teacher_id', $teacher->id)->where('date', $today)->delete();

        // =====================================================
        // TEST CASE 1: Jurnal MATCH (same class + subject as schedule)
        // =====================================================
        $journal1 = TeachingJournal::create([
            'teacher_id' => $teacher->id,
            'class_id' => $classA->id,       // X MPLB (SAME)
            'subject_id' => $subjectA->id,    // Dasar-Dasar MPLB (SAME)
            'academic_year_id' => $academicYear->id,
            'date' => $today,
            'time_slot' => ['Jam ke-1 (07:00 - 07:40)'],
            'topic' => 'Test Match',
            'teaching_method' => 'Ceramah',
        ]);
        $this->command->info("Journal 1 (SHOULD MATCH): class={$classA->name}, subject={$subjectA->name}");

        // =====================================================
        // SIMULATE MONITORING LOOKUP
        // =====================================================
        $this->command->newLine();
        $this->command->info('=== TEST 1: SAME CLASS + SUBJECT ===');
        $this->runMonitoringSimulation($academicYear, $teacher, $today);

        // =====================================================
        // TEST CASE 2: Jurnal with DIFFERENT subject
        // =====================================================
        TeachingJournal::where('teacher_id', $teacher->id)->where('date', $today)->delete();
        $journal2 = TeachingJournal::create([
            'teacher_id' => $teacher->id,
            'class_id' => $classA->id,       // X MPLB (SAME)
            'subject_id' => $subjectB->id,    // Projek Kreatif (DIFFERENT!)
            'academic_year_id' => $academicYear->id,
            'date' => $today,
            'time_slot' => ['Jam ke-1 (07:00 - 07:40)'],
            'topic' => 'Test Mismatch Subject',
            'teaching_method' => 'Ceramah',
        ]);

        $this->command->newLine();
        $this->command->info('=== TEST 2: SAME CLASS, DIFFERENT SUBJECT ===');
        $this->command->warn("  Schedule: class=X MPLB ({$classA->id}), subject=Dasar-Dasar MPLB ({$subjectA->id})");
        $this->command->warn("  Journal:  class=X MPLB ({$classA->id}), subject=Projek Kreatif ({$subjectB->id})");
        $this->runMonitoringSimulation($academicYear, $teacher, $today);

        // =====================================================
        // TEST CASE 3: Jurnal with DIFFERENT class
        // =====================================================
        TeachingJournal::where('teacher_id', $teacher->id)->where('date', $today)->delete();
        $journal3 = TeachingJournal::create([
            'teacher_id' => $teacher->id,
            'class_id' => $classB->id,       // XI MPLB (DIFFERENT!)
            'subject_id' => $subjectA->id,    // Dasar-Dasar MPLB (SAME)
            'academic_year_id' => $academicYear->id,
            'date' => $today,
            'time_slot' => ['Jam ke-1 (07:00 - 07:40)'],
            'topic' => 'Test Mismatch Class',
            'teaching_method' => 'Ceramah',
        ]);

        $this->command->newLine();
        $this->command->info('=== TEST 3: DIFFERENT CLASS, SAME SUBJECT ===');
        $this->command->warn("  Schedule: class=X MPLB ({$classA->id}), subject=Dasar-Dasar MPLB ({$subjectA->id})");
        $this->command->warn("  Journal:  class=XI MPLB ({$classB->id}), subject=Dasar-Dasar MPLB ({$subjectA->id})");
        $this->runMonitoringSimulation($academicYear, $teacher, $today);

        // =====================================================
        // TEST CASE 4: Jurnal with DIFFERENT academic_year_id
        // =====================================================
        TeachingJournal::where('teacher_id', $teacher->id)->where('date', $today)->delete();
        $journal4 = TeachingJournal::create([
            'teacher_id' => $teacher->id,
            'class_id' => $classA->id,
            'subject_id' => $subjectA->id,
            'academic_year_id' => 9999,  // WRONG academic year!
            'date' => $today,
            'time_slot' => ['Jam ke-1 (07:00 - 07:40)'],
            'topic' => 'Test Wrong Year',
            'teaching_method' => 'Ceramah',
        ]);

        $this->command->newLine();
        $this->command->info('=== TEST 4: DIFFERENT ACADEMIC YEAR ===');
        $this->command->warn("  Schedule academic_year_id: {$academicYear->id}");
        $this->command->warn("  Journal academic_year_id:  9999");
        $this->runMonitoringSimulation($academicYear, $teacher, $today);

        // Cleanup
        TeachingJournal::where('teacher_id', $teacher->id)->where('date', $today)->delete();

        $this->command->newLine();
        $this->command->info('=== ALL TESTS COMPLETE ===');
    }

    private function runMonitoringSimulation($academicYear, $teacher, $today)
    {
        $dayName = Carbon::today()->locale('id')->dayName;

        $schedules = TeachingSchedule::with(['teacher', 'schoolClass', 'subject'])
            ->where('day_of_week', $dayName)
            ->where('is_active', true)
            ->where('academic_year_id', $academicYear->id)
            ->where('teacher_id', $teacher->id)
            ->get();

        $journals = TeachingJournal::where('date', $today)
            ->where('academic_year_id', $academicYear->id)
            ->where('teacher_id', $teacher->id)
            ->get();

        // Build lookup
        $journalLookup = [];
        foreach ($journals as $j) {
            $key = "{$j->teacher_id}_{$j->class_id}_{$j->subject_id}";
            $journalLookup[$key] = $j;
        }

        $this->command->info("  Schedules: {$schedules->count()}, Journals (filtered): {$journals->count()}");

        foreach ($schedules as $s) {
            $schedKey = "{$s->teacher_id}_{$s->class_id}_{$s->subject_id}";
            $found = isset($journalLookup[$schedKey]);
            $symbol = $found ? 'MATCH' : 'NOT FOUND';
            $this->command->info("  Schedule key=[{$schedKey}] => {$symbol}");
        }

        // Also show unfiltered journal count
        $unfilteredJournals = TeachingJournal::where('date', $today)
            ->where('teacher_id', $teacher->id)
            ->count();
        if ($unfilteredJournals > $journals->count()) {
            $this->command->error("  WARNING: {$unfilteredJournals} total journals exist but only {$journals->count()} match academic_year filter!");
        }
    }
}
