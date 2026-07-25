<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\SchoolClass;

$classes = SchoolClass::where('academic_year_id', 2)
    ->orderBy('created_at')
    ->get();

echo "=== WAKTU PEMBUATAN KELAS ===\n\n";
foreach ($classes as $class) {
    echo "{$class->name} - Created: {$class->created_at}\n";
}

echo "\n=== DIAGNOSIS ===\n";
echo "Jika semua kelas dibuat di waktu yang sama, berarti autoGenerateClasses dipanggil.\n";
echo "Jika kelas X dibuat duluan sebelum konfigurasi rombel, itu masalahnya!\n";
