<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\TeachingSchedule;

class ShowTeacherSchedule extends Command
{
    protected $signature = 'show:teacher-schedule {name}';
    protected $description = 'Show complete schedule for a teacher';

    public function handle()
    {
        $name = $this->argument('name');
        
        $teacher = User::where('name', 'LIKE', "%{$name}%")->first();
        
        if (!$teacher) {
            $this->error("Teacher '{$name}' not found!");
            return 1;
        }
        
        $this->info("=== JADWAL: {$teacher->name} ===");
        $this->newLine();
        
        $schedules = TeachingSchedule::with(['schoolClass', 'subject', 'timeSlot'])
            ->where('teacher_id', $teacher->id)
            ->orderBy('day_of_week')
            ->orderBy('time_slot_id')
            ->get();
        
        $this->info("Total JP: {$schedules->count()}");
        $this->newLine();
        
        $byDay = $schedules->groupBy('day_of_week');
        
        foreach ($byDay as $day => $daySchedules) {
            $this->line("--- {$day} ({$daySchedules->count()} JP) ---");
            
            foreach ($daySchedules as $schedule) {
                $this->line(sprintf(
                    "  %s | %s | %s (%s)",
                    $schedule->schoolClass->name,
                    $schedule->subject->name,
                    $schedule->timeSlot->name,
                    $schedule->timeSlot->time_range
                ));
            }
            
            $this->newLine();
        }
        
        return 0;
    }
}
