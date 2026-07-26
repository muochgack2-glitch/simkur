<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TeachingSchedule;
use App\Models\AcademicYear;
use Illuminate\Support\Facades\DB;

class CheckScheduleOverlap extends Command
{
    protected $signature = 'check:overlap';
    protected $description = 'Check for schedule overlaps (multiple teachers in same class at same time)';

    public function handle()
    {
        $this->info('=== CHECKING SCHEDULE OVERLAPS ===');
        $this->newLine();
        
        $academicYear = AcademicYear::where('is_active', true)->first();
        
        if (!$academicYear) {
            $this->error('No active academic year found!');
            return 1;
        }
        
        // Find overlaps: same class, same day, same time slot
        $overlaps = DB::table('teaching_schedules as ts1')
            ->join('teaching_schedules as ts2', function($join) {
                $join->on('ts1.class_id', '=', 'ts2.class_id')
                     ->on('ts1.day_of_week', '=', 'ts2.day_of_week')
                     ->on('ts1.time_slot_id', '=', 'ts2.time_slot_id')
                     ->on('ts1.id', '<', 'ts2.id'); // Avoid duplicate pairs
            })
            ->join('classes', 'ts1.class_id', '=', 'classes.id')
            ->join('users as u1', 'ts1.teacher_id', '=', 'u1.id')
            ->join('users as u2', 'ts2.teacher_id', '=', 'u2.id')
            ->join('subjects as s1', 'ts1.subject_id', '=', 's1.id')
            ->join('subjects as s2', 'ts2.subject_id', '=', 's2.id')
            ->join('time_slots', 'ts1.time_slot_id', '=', 'time_slots.id')
            ->where('ts1.academic_year_id', $academicYear->id)
            ->where('ts1.is_active', true)
            ->where('ts2.is_active', true)
            ->select(
                'classes.name as class_name',
                'ts1.day_of_week',
                'time_slots.name as time_slot_name',
                'time_slots.start_time',
                'time_slots.end_time',
                'time_slots.order',
                'u1.name as teacher1',
                's1.name as subject1',
                'u2.name as teacher2',
                's2.name as subject2',
                'ts1.id as schedule1_id',
                'ts2.id as schedule2_id'
            )
            ->orderBy('classes.name')
            ->orderBy('ts1.day_of_week')
            ->orderBy('time_slots.order')
            ->get();
        
        if ($overlaps->isEmpty()) {
            $this->info('✓ No overlaps found! All schedules are clean.');
            return 0;
        }
        
        $this->warn("Found {$overlaps->count()} overlap(s):");
        $this->newLine();
        
        foreach ($overlaps as $overlap) {
            $timeRange = date('H:i', strtotime($overlap->start_time)) . ' - ' . date('H:i', strtotime($overlap->end_time));
            
            $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
            $this->line("Class: {$overlap->class_name}");
            $this->line("Day: {$overlap->day_of_week}");
            $this->line("Time: {$overlap->time_slot_name} ({$timeRange}) [Order: {$overlap->order}]");
            $this->newLine();
            $this->line("  [1] {$overlap->teacher1} - {$overlap->subject1} (ID: {$overlap->schedule1_id})");
            $this->line("  [2] {$overlap->teacher2} - {$overlap->subject2} (ID: {$overlap->schedule2_id})");
            $this->newLine();
        }
        
        $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->warn("Total overlaps: {$overlaps->count()}");
        $this->newLine();
        $this->info('To fix: Check the original schedules and remove incorrect entries.');
        
        return 0;
    }
}
