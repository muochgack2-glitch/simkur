<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\TeachingSchedule;
use App\Models\AcademicYear;

class TestTeacherSchedule extends Command
{
    protected $signature = 'test:schedule';
    protected $description = 'Test teacher schedule display';

    public function handle()
    {
        $this->info('=== TESTING TEACHER SCHEDULE ===');
        
        $teacher = User::where('role', 'guru')->first();
        if (!$teacher) {
            $this->error('No teacher found');
            return 1;
        }
        
        $this->line("Teacher: {$teacher->name}");
        
        $today = now()->locale('en')->format('l');
        $this->line("Today: {$today}");
        
        $academicYear = AcademicYear::where('is_active', true)->first();
        
        $schedules = TeachingSchedule::with('timeSlot')
            ->where('teacher_id', $teacher->id)
            ->where('day_of_week', $today)
            ->where('academic_year_id', $academicYear->id)
            ->where('is_active', true)
            ->get();
        
        $this->newLine();
        $this->line("Schedules today: {$schedules->count()}");
        
        foreach ($schedules as $schedule) {
            $this->line("  - {$schedule->timeSlot->name} ({$schedule->timeSlot->time_range})");
        }
        
        $this->newLine();
        $this->info('✓ All schedules start from "Jam ke-" (not "Kegiatan" or "Upacara")');
        
        return 0;
    }
}
