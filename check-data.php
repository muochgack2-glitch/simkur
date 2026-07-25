<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CHECK DATA FOR CLASS PROMOTION ===\n\n";

// Academic Years
echo "1. Academic Years:\n";
$years = App\Models\AcademicYear::orderBy('start_date', 'desc')->get();
foreach($years as $year) {
    echo "   - {$year->name} ";
    echo "(Active: " . ($year->is_active ? 'YES' : 'no') . ", ";
    echo "Archived: " . ($year->is_archived ? 'yes' : 'NO') . ")\n";
}

// Classes
echo "\n2. Classes per Academic Year:\n";
foreach($years as $year) {
    $classCount = App\Models\SchoolClass::where('academic_year_id', $year->id)->count();
    echo "   - {$year->name}: {$classCount} classes\n";
    
    if ($classCount > 0) {
        $classes = App\Models\SchoolClass::where('academic_year_id', $year->id)
            ->orderBy('grade')->orderBy('major')->get();
        foreach($classes as $class) {
            $studentCount = App\Models\User::where('class_id', $class->id)
                ->where('role', 'siswa')
                ->where('is_active', true)
                ->where('is_alumni', false)
                ->count();
            echo "      * {$class->name}: {$studentCount} students\n";
        }
    }
}

// Total Students
echo "\n3. Total Students:\n";
$activeStudents = App\Models\User::where('role', 'siswa')
    ->where('is_active', true)
    ->where('is_alumni', false)
    ->count();
$alumni = App\Models\User::where('is_alumni', true)->count();
echo "   - Active Students: {$activeStudents}\n";
echo "   - Alumni: {$alumni}\n";

echo "\n=== END ===\n";
