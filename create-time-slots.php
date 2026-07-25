<?php
/**
 * Script untuk membuat Time Slots (Jam Mengajar)
 * Run: php create-time-slots.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TimeSlot;

echo "=== Creating Time Slots for SMK ===\n\n";

// Hapus time slots lama (jika ada)
$deleted = TimeSlot::count();
if ($deleted > 0) {
    echo "Deleting {$deleted} old time slots...\n";
    TimeSlot::truncate();
}

// Jam Mengajar Standar SMK (Berlaku semua hari Senin-Sabtu)
$timeSlots = [
    ['name' => 'Jam ke-1', 'start' => '07:00', 'end' => '07:45', 'order' => 1],
    ['name' => 'Jam ke-2', 'start' => '07:45', 'end' => '08:30', 'order' => 2],
    ['name' => 'Jam ke-3', 'start' => '08:30', 'end' => '09:15', 'order' => 3],
    ['name' => 'ISTIRAHAT', 'start' => '09:15', 'end' => '09:30', 'order' => 4],
    ['name' => 'Jam ke-4', 'start' => '09:30', 'end' => '10:15', 'order' => 5],
    ['name' => 'Jam ke-5', 'start' => '10:15', 'end' => '11:00', 'order' => 6],
    ['name' => 'Jam ke-6', 'start' => '11:00', 'end' => '11:45', 'order' => 7],
    ['name' => 'ISTIRAHAT', 'start' => '11:45', 'end' => '12:00', 'order' => 8],
    ['name' => 'Jam ke-7', 'start' => '12:00', 'end' => '12:45', 'order' => 9],
    ['name' => 'Jam ke-8', 'start' => '12:45', 'end' => '13:30', 'order' => 10],
    ['name' => 'Jam ke-9', 'start' => '13:30', 'end' => '14:15', 'order' => 11],
    ['name' => 'Jam ke-10', 'start' => '14:15', 'end' => '15:00', 'order' => 12],
];

$created = 0;

foreach ($timeSlots as $slot) {
    TimeSlot::create([
        'name' => $slot['name'],
        'start_time' => $slot['start'] . ':00',
        'end_time' => $slot['end'] . ':00',
        'order' => $slot['order'],
        'day_of_week' => 'all', // Berlaku semua hari
        'is_active' => true,
    ]);
    
    echo "✅ Created: {$slot['name']} ({$slot['start']} - {$slot['end']})\n";
    $created++;
}

echo "\n=== Success! ===\n";
echo "Created {$created} time slots\n";
echo "\nTime slots berlaku untuk semua hari (Senin - Sabtu)\n";
echo "Admin bisa edit via Settings → Jam Mengajar\n";
