<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SchoolClass;
use App\Models\TeachingSchedule;

class CheckClasses extends Command
{
    protected $signature = 'check:classes';
    protected $description = 'Check all classes';

    public function handle()
    {
        $this->info('=== ALL CLASSES ===');
        $this->newLine();
        
        $classes = SchoolClass::orderBy('academic_year_id')->orderBy('name')->get();
        
        foreach ($classes as $class) {
            $scheduleCount = TeachingSchedule::where('class_id', $class->id)->count();
            $this->line(
                "ID: {$class->id} | " .
                "Name: {$class->name} | " .
                "Academic Year ID: {$class->academic_year_id} | " .
                "Schedules: {$scheduleCount}"
            );
        }
        
        $this->newLine();
        $this->info("Total classes: {$classes->count()}");
        
        return 0;
    }
}
