<?php
/**
 * Check how many teachers have Monday schedule
 * Run: php check-monday-teachers.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TeachingSchedule;
use App\Models\AcademicYear;

echo "=== CHECKING MONDAY SCHEDULE ===\n\n";

// Get active academic year
$academicYear = AcademicYear::where('is_active', true)->first();

if (!$academicYear) {
    echo "❌ No active academic year found!\n";
    exit(1);
}

echo "Academic Year: {$academicYear->year}\n";
echo "Day: Senin (Monday)\n\n";

// Get all schedules for Monday
$schedules = TeachingSchedule::with(['teacher', 'schoolClass', 'subject'])
    ->where('day_of_week', 'Senin')
    ->where('is_active', true)
    ->where('academic_year_id', $academicYear->id)
    ->get();

echo "Total schedule records for Monday: " . $schedules->count() . "\n\n";

// Group by teacher
$teacherSchedules = $schedules->groupBy('teacher_id');

echo "=== TEACHERS WITH MONDAY SCHEDULE ===\n";
echo "Total unique teachers: " . $teacherSchedules->count() . "\n\n";

$counter = 1;
foreach ($teacherSchedules as $teacherId => $teacherScheduleList) {
    $teacher = $teacherScheduleList->first()->teacher;
    $scheduleCount = $teacherScheduleList->count();
    
    echo "{$counter}. {$teacher->name}\n";
    echo "   Schedule count: {$scheduleCount}\n";
    
    // Show details
    foreach ($teacherScheduleList as $schedule) {
        $timeSlotInfo = is_array($schedule->time_slot_id) 
            ? count($schedule->time_slot_id) . ' JP' 
            : '1 JP';
        
        echo "   - {$schedule->schoolClass->name} • {$schedule->subject->name} ({$timeSlotInfo})\n";
    }
    
    echo "\n";
    $counter++;
}

echo "\n=== SUMMARY ===\n";
echo "If ALL teachers haven't filled journals:\n";
echo "❌ 'Belum Isi' section will show: {$teacherSchedules->count()} cards\n";
echo "✅ 'Sudah Isi' section will show: 0 cards\n";
