<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TimeSlot;

class ShowTimeSlots extends Command
{
    protected $signature = 'show:timeslots {day?}';
    protected $description = 'Show time slots structure';

    public function handle()
    {
        $day = $this->argument('day') ?? 'Senin';
        
        $this->info("=== TIME SLOTS FOR {$day} ===");
        $this->newLine();
        
        $slots = TimeSlot::where('day_of_week', $day)->orderBy('order')->get();
        
        foreach ($slots as $slot) {
            $label = '';
            if (stripos($slot->name, 'upacara') !== false || stripos($slot->name, 'kegiatan') !== false) {
                $label = ' ← Jam ke-0 (SKIP)';
            } elseif (stripos($slot->name, 'istirahat') !== false || stripos($slot->name, 'break') !== false) {
                $label = ' ← ISTIRAHAT (SKIP)';
            } elseif (stripos($slot->name, 'jam ke-') !== false) {
                $label = ' ← Jam Pelajaran';
            }
            
            $this->line(
                sprintf(
                    "Order %2d: %-20s (%s - %s)%s",
                    $slot->order,
                    $slot->name,
                    substr($slot->start_time, 0, 5),
                    substr($slot->end_time, 0, 5),
                    $label
                )
            );
        }
        
        $this->newLine();
        
        // Show mapping
        $this->info('MAPPING JAM PELAJARAN KE ORDER:');
        $jamPelajaran = $slots->filter(function($slot) {
            return stripos($slot->name, 'jam ke-') !== false;
        });
        
        foreach ($jamPelajaran as $slot) {
            $this->line("  {$slot->name} → order {$slot->order}");
        }
        
        return 0;
    }
}
