<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TeachingSchedule;
use App\Models\TimeSlot;

class CleanScheduleSlotZero extends Command
{
    protected $signature = 'schedule:clean-slot-zero';
    protected $description = 'Remove teaching schedules with invalid time slots (pre-class activities and break times)';

    public function handle()
    {
        $this->info('Cleaning schedules with invalid time slots...');

        // Get invalid time slot IDs:
        // 1. Order <= 1 (Upacara/Kegiatan Pagi/Kegiatan Jumat)
        // 2. Break times (Istirahat)
        $invalidSlotIds = TimeSlot::where(function($q) {
            $q->where('order', '<=', 1)
              ->orWhere('name', 'LIKE', '%Istirahat%')
              ->orWhere('name', 'LIKE', '%BREAK%')
              ->orWhere('name', 'LIKE', '%Break%')
              ->orWhere('name', 'LIKE', '%istirahat%');
        })->pluck('id');

        if ($invalidSlotIds->isEmpty()) {
            $this->info('No invalid time slots found.');
            return 0;
        }

        $this->info('Found ' . $invalidSlotIds->count() . ' invalid time slot(s)');
        
        // Show which slots will be cleaned
        $slots = TimeSlot::whereIn('id', $invalidSlotIds)->get();
        foreach ($slots as $slot) {
            $this->line("  - {$slot->name} (order={$slot->order}, {$slot->start_time}-{$slot->end_time})");
        }

        // Delete schedules using those slots
        $deleted = TeachingSchedule::whereIn('time_slot_id', $invalidSlotIds)->delete();

        $this->info("✓ Deleted {$deleted} schedule(s) with invalid slots");
        $this->info('✓ Cleanup completed! Teaching schedules now only use actual teaching periods');

        return 0;
    }
}
