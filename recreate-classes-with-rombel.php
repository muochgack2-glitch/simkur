<?php

/**
 * Recreate Classes with Rombel Configuration
 * This script will delete all classes for academic year 2027/2028
 * and recreate them with proper rombel configuration
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Support\Facades\DB;

try {
    echo "========================================\n";
    echo "RECREATE CLASSES WITH ROMBEL CONFIG\n";
    echo "========================================\n\n";

    $academicYearId = 2; // 2027/2028

    // Check if there are students in these classes
    $studentsCount = User::whereNotNull('class_id')
        ->whereHas('schoolClass', function($q) use ($academicYearId) {
            $q->where('academic_year_id', $academicYearId);
        })
        ->count();

    echo "Jumlah siswa di tahun ajaran ini: {$studentsCount}\n";
    
    if ($studentsCount > 0) {
        echo "\n⚠️  PERINGATAN: Ada {$studentsCount} siswa yang terdaftar di kelas tahun ini!\n";
        echo "Siswa-siswa ini akan TIDAK MEMILIKI KELAS setelah penghapusan.\n";
        echo "Anda perlu proses kenaikan kelas ulang untuk menempatkan mereka.\n\n";
    }

    echo "Konfigurasi Rombel yang akan dibuat:\n";
    echo "- MPLB: 2 rombel (X MPLB 1, X MPLB 2)\n";
    echo "- AKL: 2 rombel (X AKL 1, X AKL 2)\n";
    echo "- BUSANA: 2 rombel (X BUSANA 1, X BUSANA 2)\n";
    echo "- Grade XI & XII: 1 rombel per jurusan (tanpa angka)\n\n";

    echo "Ketik 'YA' untuk melanjutkan: ";
    $handle = fopen("php://stdin", "r");
    $line = trim(fgets($handle));
    fclose($handle);

    if (strtoupper($line) !== 'YA') {
        echo "\n❌ Dibatalkan.\n";
        exit(0);
    }

    DB::beginTransaction();

    // Delete existing classes
    $deleted = SchoolClass::where('academic_year_id', $academicYearId)->delete();
    echo "\n✓ Menghapus {$deleted} kelas lama\n";

    // Recreate with rombel configuration
    $gradeXRombelConfig = [
        'MPLB' => 2,
        'AKL' => 2,
        'BUSANA' => 2,
    ];

    SchoolClass::autoGenerateClasses($academicYearId, $gradeXRombelConfig);
    
    $newCount = SchoolClass::where('academic_year_id', $academicYearId)->count();
    echo "✓ Membuat {$newCount} kelas baru dengan rombel\n";

    // Show created classes
    echo "\n=== KELAS BARU ===\n";
    $classes = SchoolClass::where('academic_year_id', $academicYearId)
        ->orderBy('grade')
        ->orderBy('major')
        ->orderBy('rombel')
        ->get();
    
    foreach ($classes as $class) {
        echo "- {$class->name}\n";
    }

    DB::commit();

    echo "\n========================================\n";
    echo "✅ BERHASIL!\n";
    echo "========================================\n";
    echo "Sekarang proses kenaikan kelas ulang untuk menempatkan siswa.\n";

} catch (Exception $e) {
    DB::rollBack();
    echo "\n❌ ERROR: {$e->getMessage()}\n";
    exit(1);
}
