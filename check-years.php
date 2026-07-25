<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== ACADEMIC YEARS DATA ===\n\n";

$years = App\Models\AcademicYear::all();

foreach($years as $year) {
    echo "ID: {$year->id}\n";
    echo "Year: [{$year->year}]\n";
    echo "Start Date: {$year->start_date}\n";
    echo "End Date: {$year->end_date}\n";
    echo "Active: " . ($year->is_active ? 'YES' : 'no') . "\n";
    echo "---\n";
}
