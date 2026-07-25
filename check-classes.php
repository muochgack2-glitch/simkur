<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\SchoolClass;
use App\Models\AcademicYear;

$activeYear = AcademicYear::where('is_active', true)->first();
echo "Tahun Ajaran Aktif: {$activeYear->year} (ID: {$activeYear->id})\n\n";

$classes = SchoolClass::where('academic_year_id', $activeYear->id)
    ->orderBy('grade')
    ->orderBy('major')
    ->orderBy('rombel')
    ->get();

echo "Total Kelas: " . $classes->count() . "\n\n";

echo "=== DAFTAR KELAS ===\n";
foreach ($classes as $class) {
    $rombelText = $class->rombel ? "Rombel {$class->rombel}" : "No Rombel";
    echo "{$class->name} - Grade: {$class->grade}, Major: {$class->major}, {$rombelText}\n";
}

echo "\n=== BREAKDOWN PER GRADE ===\n";
$gradeX = $classes->where('grade', 'X')->count();
$gradeXI = $classes->where('grade', 'XI')->count();
$gradeXII = $classes->where('grade', 'XII')->count();

echo "Kelas X: {$gradeX}\n";
echo "Kelas XI: {$gradeXI}\n";
echo "Kelas XII: {$gradeXII}\n";

echo "\n=== KELAS X DETAIL ===\n";
$kelasX = $classes->where('grade', 'X');
foreach (['MPLB', 'AKL', 'BUSANA'] as $major) {
    $count = $kelasX->where('major', $major)->count();
    echo "{$major}: {$count} kelas\n";
    foreach ($kelasX->where('major', $major) as $k) {
        echo "  - {$k->name} (Rombel: " . ($k->rombel ?: 'NULL') . ")\n";
    }
}
