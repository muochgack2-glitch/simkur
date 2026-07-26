<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TeachingSchedule;
use App\Models\SchoolClass;
use App\Models\AcademicYear;

class CheckScheduleData extends Command
{
    protected $signature = 'check:schedule-data {day?} {class?}';
    protected $description = 'Check teaching schedule data';

    public function handle()
    {
        $day = $this->argument('day') ?? 'Friday';
        $className = $this->argument('class') ?? 'X AKL';
        
        $this->info("=== CHECKING SCHEDULE DATA ===");
        $this->line("Day: {$day}");
        $this->line("Class: {$className}");
        $this->newLine();
        
        // Get active academic year
        $academicYear = AcademicYear::where('is_active', true)->first();
        
        if (!$academicYear) {
            $this->error('No active academic year found!');
            return 1;
        }
        
        $this->info("Academic Year: {$academicYear->year}");
        $this->newLine();
        
        // Find class
        $class = SchoolClass::where('name', 'LIKE', "%{$className}%")->first();
        
        if (!$class) {
            $this->error("Class '{$className}' not found!");
            $this->line('Available classes:');
            SchoolClass::all()->each(function($c) {
                $this->line("  - {$c->name}");
            });
            return 1;
        }
        
        $this->info("Class found: {$class->name} (ID: {$class->id})");
        $this->newLine();
        
        // Check schedules
        $schedules = TeachingSchedule::with(['teacher', 'subject', 'timeSlot'])
            ->where('academic_year_id', $academicYear->id)
            ->where('class_id', $class->id)
            ->where('day_of_week', $day)
            ->where('is_active', true)
            ->get();
        
        $this->info("Schedules found: {$schedules->count()}");
        
        if ($schedules->count() > 0) {
            $this->newLine();
            $this->line('Schedule details:');
            foreach ($schedules as $schedule) {
                $this->line(
                    "  - {$schedule->teacher->name} | " .
                    "{$schedule->subject->name} | " .
                    "{$schedule->timeSlot->name} ({$schedule->timeSlot->time_range})"
                );
            }
        } else {
            $this->newLine();
            $this->warn('No schedules found for this combination!');
            
            // Check if there are any schedules for this class on other days
            $otherDays = TeachingSchedule::where('academic_year_id', $academicYear->id)
                ->where('class_id', $class->id)
                ->where('is_active', true)
                ->pluck('day_of_week')
                ->unique();
            
            if ($otherDays->count() > 0) {
                $this->line('This class has schedules on: ' . $otherDays->implode(', '));
            } else {
                $this->error('This class has NO schedules at all!');
            }
        }
        
        return 0;
    }
}
