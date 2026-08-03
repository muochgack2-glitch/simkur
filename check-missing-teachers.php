<?php
// Check which teachers from TXT file don't have schedules in database
// Run: php check-missing-teachers.php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== CHECKING TEACHERS WITHOUT SCHEDULES ===\n\n";

// Teachers from TXT file (based on Jadwal_Guru_Terintegrasi_FIX.txt)
$txtTeachers = [
    'Drs. Suseno.',
    'Budi Siswanto, S.Pd.I.',
    'Dewi Wartini, S.Pd.',
    'Tri Mulyaniningsih, S.E.',
    'Munisah, S.Pd.',
    'Ari Yunitasari, S.Pd.',
    'Meiranti Trisnaning S., S.Pd.',
    'M. Huda Muttaqin, S.Pd.I.',
    'Yully Setyo. A., S.Pd.',
    'Ilham Hardiyan P., S.Pd.',
    'Pancawati Puji L., A.Md.',
    'Nia Dani Rahayu, S.Pd.',
    'Ade Rua Nur Lemoniar, S.Pd.',
    'Liliyana Ayu W., S.Pd.',
    'Dhani Kisworo Jati, S.Pd.',
    'Wiwit Mergi W., A.Md',
    'Debby Furi Wijayanti, S. Pd.',
    'Ervinda Sekar Asmara, S.Pd',
    'Adela Wulan Kurniasari, S.Pd',
    'Marista Bela Octaviana, S.Pd.',
    'GURU BTQ',
    'Eko Budhi Lestari, S. Pd. B.',
    'Rinawati, S. Pd.',
];

echo "Total teachers in TXT file: " . count($txtTeachers) . "\n\n";

// Get all teachers with schedules
$teachersWithSchedules = \App\Models\TeachingSchedule::with('teacher')
    ->get()
    ->pluck('teacher.name')
    ->unique()
    ->sort()
    ->values();

echo "Teachers with schedules in database (" . $teachersWithSchedules->count() . "):\n";
foreach ($teachersWithSchedules as $name) {
    echo "  ✅ {$name}\n";
}

echo "\n=== MISSING TEACHERS (in TXT but no schedules) ===\n";

$missing = [];
foreach ($txtTeachers as $txtName) {
    // Try exact match first
    $found = $teachersWithSchedules->contains($txtName);
    
    // Try fuzzy match (remove dots, spaces, case insensitive)
    if (!$found) {
        $normalizedTxt = strtolower(preg_replace('/[.\s]+/', '', $txtName));
        foreach ($teachersWithSchedules as $dbName) {
            $normalizedDb = strtolower(preg_replace('/[.\s]+/', '', $dbName));
            if ($normalizedTxt === $normalizedDb) {
                $found = true;
                echo "  ⚠️  {$txtName} → Matched to: {$dbName} (name format different)\n";
                break;
            }
        }
    }
    
    if (!$found) {
        $missing[] = $txtName;
    }
}

if (empty($missing)) {
    echo "  ✅ All teachers from TXT have schedules!\n";
} else {
    echo "\n";
    foreach ($missing as $name) {
        echo "  ❌ {$name}\n";
        
        // Check if teacher exists in users table
        $user = \App\Models\User::where('name', 'LIKE', '%' . trim($name, '.') . '%')
            ->orWhere('name', $name)
            ->first();
        
        if ($user) {
            $scheduleCount = \App\Models\TeachingSchedule::where('teacher_id', $user->id)->count();
            echo "      → Found in users (ID: {$user->id}, Role: {$user->role})\n";
            echo "      → Schedule count: {$scheduleCount}\n";
        } else {
            echo "      → NOT found in users table\n";
        }
    }
    
    echo "\nTotal missing: " . count($missing) . "\n";
}

echo "\n=== SCHEDULE STATS ===\n";
$totalSchedules = \App\Models\TeachingSchedule::count();
echo "Total schedules in database: {$totalSchedules}\n";

$schedulesByTeacher = \App\Models\TeachingSchedule::select('teacher_id')
    ->selectRaw('COUNT(*) as count')
    ->groupBy('teacher_id')
    ->with('teacher:id,name')
    ->orderBy('count', 'desc')
    ->get();

echo "\nTop 5 teachers by schedule count:\n";
foreach ($schedulesByTeacher->take(5) as $stat) {
    echo "  {$stat->teacher->name}: {$stat->count} schedules\n";
}
