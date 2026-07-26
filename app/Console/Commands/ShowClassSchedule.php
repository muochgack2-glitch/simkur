<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TeachingSchedule;
use App\Models\SchoolClass;
use App\Models\AcademicYear;
use App\Models\TimeSlot;

class ShowClassSchedule extends Command
{
    protected $signature = 'show:class-schedule {className?}';
    protected $description = 'Show complete schedule for a class';

    public function handle()
    {
        $className = $this->argument('className');
        $academicYear = AcademicYear::where('is_active', true)->first();
        
        if (!$academicYear) {
            $this->error('No active academic year found!');
            return 1;
        }

        if ($className) {
            $classes = SchoolClass::where('academic_year_id', $academicYear->id)
                ->where('name', 'like', "%{$className}%")
                ->get();
        } else {
            $classes = SchoolClass::where('academic_year_id', $academicYear->id)
                ->whereIn('name', ['X AKL', 'X MPLB', 'X BUSANA', 'XI AKL', 'XI MPLB', 'XI BUSANA'])
                ->orderBy('name')
                ->get();
        }

        if ($classes->isEmpty()) {
            $this->error('No classes found!');
            return 1;
        }

        foreach ($classes as $class) {
            $this->showClassSchedule($class, $academicYear);
            $this->newLine();
        }

        return 0;
    }

    private function showClassSchedule($class, $academicYear)
    {
        $this->info("═══════════════════════════════════════════════");
        $this->info("JADWAL KELAS: {$class->name}");
        $this->info("═══════════════════════════════════════════════");
        $this->newLine();

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        $totalJP = 0;

        foreach ($days as $day) {
            $this->line("───── {$day} ─────");
            
            // Get all schedules for this class on this day, ordered by time slot
            $schedules = TeachingSchedule::where('class_id', $class->id)
                ->where('academic_year_id', $academicYear->id)
                ->where('day_of_week', $day)
                ->where('is_active', true)
                ->with(['timeSlot', 'teacher', 'subject'])
                ->get()
                ->sortBy('timeSlot.order');

            if ($schedules->isEmpty()) {
                $this->warn("  (No schedules)");
            } else {
                $dayJP = 0;
                foreach ($schedules as $schedule) {
                    $timeSlot = $schedule->timeSlot;
                    $teacher = $schedule->teacher;
                    $subject = $schedule->subject;
                    
                    // Skip non-teaching slots (order 1, 5, 10)
                    if ($timeSlot->order <= 1 || $timeSlot->order == 5 || $timeSlot->order == 10) {
                        continue;
                    }
                    
                    $dayJP++;
                    
                    $jamNumber = $this->getJamNumber($timeSlot->order);
                    $timeRange = date('H:i', strtotime($timeSlot->start_time)) . '-' . date('H:i', strtotime($timeSlot->end_time));
                    
                    $this->line(sprintf(
                        "  Jam ke-%d [%s] %s - %s",
                        $jamNumber,
                        $timeRange,
                        $teacher->name,
                        $subject->name
                    ));
                }
                
                $this->line("  Total: {$dayJP} JP");
                $totalJP += $dayJP;
            }
            
            $this->newLine();
        }

        $target = 50;
        $diff = $totalJP - $target;
        $diffStr = $diff > 0 ? "+{$diff}" : ($diff < 0 ? "{$diff}" : "0");
        $status = $diff == 0 ? '✓ PERFECT' : ($diff > 0 ? '⚠ OVER' : '⚠ UNDER');
        
        $this->info("TOTAL: {$totalJP} JP (Target: {$target}, Diff: {$diffStr}) {$status}");
    }

    private function getJamNumber($order)
    {
        $orderToJam = [
            2 => 1,
            3 => 2,
            4 => 3,
            6 => 4,
            7 => 5,
            8 => 6,
            9 => 7,
            11 => 8,
            12 => 9,
            13 => 10,
        ];
        
        return $orderToJam[$order] ?? '?';
    }
}
