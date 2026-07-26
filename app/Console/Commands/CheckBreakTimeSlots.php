<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TimeSlot;
use App\Models\TeachingSchedule;
use Illuminate\Support\Str;

class CheckBreakTimeSlots extends Command
{
    protected $signature = 'check:breaks';
    protected $description = 'Check time slots for break/rest periods';

    public function handle()
    {
        $this->info('=== TIME SLOTS FOR BREAK/REST PERIODS ===');
        
        // Find break time slots (Istirahat, BREAK, etc.)
        $breakSlots = TimeSlot::where(function($q) {
            $q->where('name', 'LIKE', '%Istirahat%')
              ->orWhere('name', 'LIKE', '%BREAK%')
              ->orWhere('name', 'LIKE', '%Break%')
              ->orWhere('name', 'LIKE', '%istirahat%');
        })->get();
        
        if ($breakSlots->isEmpty()) {
            $this->warn('No break time slots found');
            return 0;
        }
        
        foreach ($breakSlots as $slot) {
            $this->line("ID: {$slot->id} | {$slot->name} (order={$slot->order}) | {$slot->start_time}-{$slot->end_time}");
        }
        
        $this->newLine();
        $this->info('=== TEACHING SCHEDULES USING BREAK SLOTS ===');
        
        $breakSlotIds = $breakSlots->pluck('id');
        $schedulesWithBreaks = TeachingSchedule::with(['teacher', 'schoolClass', 'subject', 'timeSlot'])
            ->whereIn('time_slot_id', $breakSlotIds)
            ->get();
        
        $this->line("Total schedules using break slots: {$schedulesWithBreaks->count()}");
        
        if ($schedulesWithBreaks->count() > 0) {
            $this->newLine();
            foreach ($schedulesWithBreaks->take(20) as $schedule) {
                $this->line(
                    "{$schedule->teacher->name} | " .
                    "{$schedule->subject->name} | " .
                    "{$schedule->schoolClass->name} | " .
                    "{$schedule->day_of_week} | " .
                    "{$schedule->timeSlot->name}"
                );
            }
            
            if ($schedulesWithBreaks->count() > 20) {
                $this->line('... and ' . ($schedulesWithBreaks->count() - 20) . ' more');
            }
        }
        
        return 0;
    }
}
