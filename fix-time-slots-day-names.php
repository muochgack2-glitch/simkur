<?php
// Fix time_slots day_of_week: English lowercase → Indonesian
// Run: php fix-time-slots-day-names.php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== FIXING TIME SLOTS DAY NAMES ===\n\n";

$mapping = [
    'monday' => 'Senin',
    'tuesday' => 'Selasa',
    'wednesday' => 'Rabu',
    'thursday' => 'Kamis',
    'friday' => 'Jumat',
    'saturday' => 'Sabtu',
    'sunday' => 'Minggu',
];

echo "Current time_slots:\n";
$current = \App\Models\TimeSlot::select('day_of_week')
    ->selectRaw('COUNT(*) as count')
    ->groupBy('day_of_week')
    ->get();

foreach ($current as $item) {
    echo "  {$item->day_of_week}: {$item->count} slots\n";
}

echo "\n🔄 Starting update...\n\n";

$totalUpdated = 0;

foreach ($mapping as $english => $indonesian) {
    $count = \App\Models\TimeSlot::where('day_of_week', $english)
        ->update(['day_of_week' => $indonesian]);
    
    if ($count > 0) {
        echo "  ✅ {$english} → {$indonesian}: {$count} slots updated\n";
        $totalUpdated += $count;
    }
}

echo "\n📊 Total updated: {$totalUpdated} time slots\n";

echo "\nNew time_slots:\n";
$updated = \App\Models\TimeSlot::select('day_of_week')
    ->selectRaw('COUNT(*) as count')
    ->groupBy('day_of_week')
    ->orderByRaw("FIELD(day_of_week, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
    ->get();

foreach ($updated as $item) {
    echo "  {$item->day_of_week}: {$item->count} slots\n";
}

echo "\n✅ Done! Time slots now use Indonesian day names.\n";
