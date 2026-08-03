<?php

$file = 'Jadwal_Guru_Terintegrasi_FIX.txt';

if (!file_exists($file)) {
    die("❌ File not found: $file\n");
}

$content = file_get_contents($file);
$lines = explode("\n", $content);

$teacherCount = 0;
$scheduleCount = 0;
$teachers = [];
$currentTeacher = null;

foreach ($lines as $lineNum => $line) {
    $line = trim($line);
    
    // Teacher line
    if (preg_match('/^(\d+)\.\s+(.+)$/', $line, $matches)) {
        $teacherCount++;
        $teacherName = trim($matches[2]);
        $currentTeacher = $teacherName;
        $teachers[$teacherName] = 0;
        echo sprintf("[%02d] Teacher: %s\n", $teacherCount, $teacherName);
    }
    
    // Schedule line
    if (preg_match('/^-\s+Jam ke-(\d+)(?:\s+s\/d\s+(\d+))?:\s+(.+?)\s+-\s+(.+)$/', $line, $matches)) {
        $scheduleCount++;
        if ($currentTeacher) {
            $teachers[$currentTeacher]++;
        }
        
        $slotStart = $matches[1];
        $slotEnd = $matches[2] ?: $slotStart;
        $class = trim($matches[3]);
        $subject = trim($matches[4]);
        
        // echo "     → Slot $slotStart-$slotEnd: $class - $subject\n";
    }
}

echo "\n";
echo "=====================================\n";
echo "SUMMARY\n";
echo "=====================================\n";
echo "✅ File parsed successfully!\n";
echo "   Teachers: $teacherCount\n";
echo "   Schedule entries: $scheduleCount\n";
echo "\n";

echo "Teachers with schedule count:\n";
foreach ($teachers as $name => $count) {
    echo sprintf("   - %-40s : %3d schedules\n", $name, $count);
}

echo "\n";
echo "Average: " . round($scheduleCount / $teacherCount, 1) . " schedules per teacher\n";
