<?php

namespace App\Console\Commands;

use App\Services\EffectiveDayService;
use Illuminate\Console\Command;

class CalculateEffectiveDays extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ekaldik:calculate-days';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate effective days for all semesters in active academic year';

    /**
     * Execute the console command.
     */
    public function handle(EffectiveDayService $service)
    {
        $this->info('Calculating effective days...');
        
        $count = $service->recalculateAll();
        
        if ($count > 0) {
            $this->info("✓ Successfully calculated effective days for {$count} semester(s)");
        } else {
            $this->warn('No active semesters found to calculate');
        }
        
        return Command::SUCCESS;
    }
}
