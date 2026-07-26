<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TimeSlot;
use App\Models\TeachingSchedule;

class CheckTimeSlots extends Command
{
    protected $signature = 'check:timeslots';
    protected $description = 'Check time slots with order 1 and schedules using them';

    public function handle()
    {
        $this->info('=== TIME SLOTS WITH ORDER=1 (Pre-class activities) ===');
        $slotsOrder1 = TimeSlot::where('order', 1)->get();
        
        foreach ($slotsOrder1 as $slot) {
            $this->line("ID: {$slot->id} | {$slot->name} | {$slot->start_time}-{$slot->end_time}");
        }
        
        if ($slotsOrder1->isEmpty()) {
            $this->warn('No time slots with order=1 found');
            return 0;
        }
        
        $this->newLine();
        $this->info('=== TEACHING SCHEDULES USING ORDER=1 SLOTS ===');
        
        $order1SlotIds = $slotsOrder1->pluck('id');
        $schedulesWithOrder1 = TeachingSchedule::with(['teacher', 'schoolClass', 'subject', 'timeSlot'])
            ->whereIn('time_slot_id', $order1SlotIds)
            ->get();
        
        $this->line("Total schedules using order=1 slots: {$schedulesWithOrder1->count()}");
        
        if ($schedulesWithOrder1->count() > 0) {
            $this->newLine();
            foreach ($schedulesWithOrder1->take(10) as $schedule) {
                $this->line(
                    "{$schedule->teacher->name} | " .
                    "{$schedule->subject->name} | " .
                    "{$schedule->schoolClass->name} | " .
                    "{$schedule->day_of_week} | " .
                    "{$schedule->timeSlot->name}"
                );
            }
            
            if ($schedulesWithOrder1->count() > 10) {
                $this->line('... and ' . ($schedulesWithOrder1->count() - 10) . ' more');
            }
        }
        
        return 0;
    }
}
