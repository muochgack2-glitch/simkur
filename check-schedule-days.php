<?php
// Quick script to check day_of_week values in teaching_schedules
// Run on production: php check-schedule-days.php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== CHECKING TEACHING SCHEDULE DAY VALUES ===\n\n";

$total = \App\Models\TeachingSchedule::count();
echo "Total schedules: {$total}\n\n";

echo "Breakdown by day_of_week:\n";
$days = \App\Models\TeachingSchedule::select('day_of_week')
    ->selectRaw('COUNT(*) as count')
    ->groupBy('day_of_week')
    ->orderBy('count', 'desc')
    ->get();

foreach ($days as $day) {
    echo "  {$day->day_of_week}: {$day->count}\n";
}

echo "\n=== Filter Test ===\n";
echo "Monday (capitalized): " . \App\Models\TeachingSchedule::where('day_of_week', 'Monday')->count() . "\n";
echo "monday (lowercase): " . \App\Models\TeachingSchedule::where('day_of_week', 'monday')->count() . "\n";
echo "Senin (Indonesian): " . \App\Models\TeachingSchedule::where('day_of_week', 'Senin')->count() . "\n";

echo "\n=== Sample Records ===\n";
$samples = \App\Models\TeachingSchedule::limit(5)->get(['id', 'day_of_week', 'teacher_id', 'class_id']);
foreach ($samples as $s) {
    echo "ID {$s->id}: day_of_week = '{$s->day_of_week}'\n";
}
