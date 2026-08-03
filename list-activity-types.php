<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\ActivityType;

echo "\n========================================\n";
echo "DAFTAR JENIS AKTIVITAS (ACTIVITY TYPES)\n";
echo "========================================\n\n";

$activityTypes = ActivityType::orderBy('category')->orderBy('sort_order')->orderBy('name')->get();

$counter = 1;
foreach ($activityTypes as $at) {
    echo "[$counter] {$at->name}\n";
    echo "    Code: {$at->code}\n";
    echo "    Category: {$at->category}\n";
    echo "    Is Holiday: " . ($at->is_holiday ? '✅ Yes' : '❌ No') . "\n";
    echo "    Is Exam: " . ($at->is_exam ? '✅ Yes' : '❌ No') . "\n";
    echo "    Marks End Period: " . ($at->marks_end_of_period ? '✅ Yes' : '❌ No') . "\n";
    
    if ($at->affects_grades && is_array($at->affects_grades) && count($at->affects_grades) > 0) {
        echo "    Affects Grades: " . implode(', ', $at->affects_grades) . "\n";
    } else {
        echo "    Affects Grades: -\n";
    }
    
    echo "    Color: {$at->default_color}\n";
    echo "\n";
    $counter++;
}

echo "\nTotal: " . $activityTypes->count() . " jenis aktivitas\n\n";

echo "========================================\n";
echo "SUMMARY BY CATEGORY\n";
echo "========================================\n\n";

$categories = $activityTypes->groupBy('category');
foreach ($categories as $category => $types) {
    echo strtoupper($category) . ": " . $types->count() . " types\n";
}

echo "\n========================================\n";
echo "SPECIAL MARKERS\n";
echo "========================================\n\n";

echo "HOLIDAY MARKERS:\n";
$holidays = $activityTypes->where('is_holiday', true);
foreach ($holidays as $h) {
    echo "  - {$h->name} ({$h->code})\n";
}

echo "\nEXAM MARKERS:\n";
$exams = $activityTypes->where('is_exam', true);
foreach ($exams as $e) {
    echo "  - {$e->name} ({$e->code})\n";
}

echo "\nEND PERIOD MARKERS:\n";
$endPeriods = $activityTypes->where('marks_end_of_period', true);
if ($endPeriods->count() > 0) {
    foreach ($endPeriods as $ep) {
        $grades = $ep->affects_grades ? implode(', ', $ep->affects_grades) : 'None';
        echo "  - {$ep->name} ({$ep->code}) → Affects: {$grades}\n";
    }
} else {
    echo "  - (Tidak ada)\n";
}

echo "\n";
