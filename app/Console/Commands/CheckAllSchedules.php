<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TeachingSchedule;
use App\Models\SchoolClass;
use App\Models\AcademicYear;
use Illuminate\Support\Facades\DB;

class CheckAllSchedules extends Command
{
    protected $signature = 'check:all-schedules';
    protected $description = 'Check all teaching schedules in database';

    public function handle()
    {
        $this->info('=== TEACHING SCHEDULES SUMMARY ===');
        $this->newLine();
        
        // Get active academic year
        $academicYear = AcademicYear::where('is_active', true)->first();
        
        if (!$academicYear) {
            $this->error('No active academic year found!');
            return 1;
        }
        
        $this->info("Academic Year: {$academicYear->year} (ID: {$academicYear->id})");
        $this->newLine();
        
        // Total schedules
        $total = TeachingSchedule::where('academic_year_id', $academicYear->id)->count();
        $this->info("Total schedules: {$total}");
        
        // By day
        $this->newLine();
        $this->line('Schedules by day:');
        $byDay = TeachingSchedule::where('academic_year_id', $academicYear->id)
            ->select('day_of_week', DB::raw('count(*) as total'))
            ->groupBy('day_of_week')
            ->orderByRaw("FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')")
            ->get();
        
        foreach ($byDay as $day) {
            $this->line("  - {$day->day_of_week}: {$day->total}");
        }
        
        // By class
        $this->newLine();
        $this->line('Schedules by class:');
        $byClass = TeachingSchedule::with('schoolClass')
            ->where('academic_year_id', $academicYear->id)
            ->select('class_id', DB::raw('count(*) as total'))
            ->groupBy('class_id')
            ->orderBy('total', 'desc')
            ->get();
        
        foreach ($byClass as $item) {
            $className = $item->schoolClass->name ?? 'Unknown';
            $this->line("  - {$className}: {$item->total} schedules");
        }
        
        // Classes with NO schedules
        $this->newLine();
        $classesWithSchedules = $byClass->pluck('class_id');
        $classesWithoutSchedules = SchoolClass::where('academic_year_id', $academicYear->id)
            ->whereNotIn('id', $classesWithSchedules)
            ->get();
        
        if ($classesWithoutSchedules->count() > 0) {
            $this->warn('Classes with NO schedules:');
            foreach ($classesWithoutSchedules as $class) {
                $this->line("  - {$class->name}");
            }
        } else {
            $this->info('All classes have schedules!');
        }
        
        return 0;
    }
}
