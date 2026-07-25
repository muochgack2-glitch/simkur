<?php

/**
 * Manual Rollback Script for Old Promotions
 * HATI-HATI: Backup database sebelum menjalankan script ini!
 * 
 * Cara menjalankan: php manual-rollback.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ClassPromotion;
use App\Models\AcademicYear;
use App\Models\User;
use App\Models\SchoolClass;
use Illuminate\Support\Facades\DB;

try {
    echo "========================================\n";
    echo "MANUAL ROLLBACK KENAIKAN KELAS\n";
    echo "========================================\n\n";

    // Get latest promotion
    $promotion = ClassPromotion::with(['fromAcademicYear', 'toAcademicYear'])
        ->orderBy('processed_at', 'desc')
        ->first();

    if (!$promotion) {
        echo "❌ Tidak ada promosi yang ditemukan.\n";
        exit(1);
    }

    echo "Promosi Terakhir:\n";
    echo "- Tanggal: {$promotion->processed_at->format('d M Y H:i')}\n";
    echo "- Dari: {$promotion->fromAcademicYear->year}\n";
    echo "- Ke: {$promotion->toAcademicYear->year}\n";
    echo "- Total Naik: {$promotion->total_promoted} siswa\n";
    echo "- Total Lulus: {$promotion->total_graduated} siswa\n\n";

    // Confirmation
    echo "⚠️  PERINGATAN: Proses ini akan:\n";
    echo "1. Mengembalikan alumni ke siswa kelas XII\n";
    echo "2. Mengembalikan siswa kelas XI ke kelas X\n";
    echo "3. Mengembalikan siswa kelas XII ke kelas XI\n";
    echo "4. Mengaktifkan kembali tahun ajaran lama\n\n";

    echo "Ketik 'YA' untuk melanjutkan: ";
    $handle = fopen("php://stdin", "r");
    $line = trim(fgets($handle));
    fclose($handle);

    if (strtoupper($line) !== 'YA') {
        echo "\n❌ Dibatalkan.\n";
        exit(0);
    }

    echo "\n🔄 Memulai rollback...\n\n";

    DB::beginTransaction();

    $fromYearId = $promotion->from_academic_year_id;
    $toYearId = $promotion->to_academic_year_id;

    // Get classes from old year (from)
    $oldClasses = SchoolClass::where('academic_year_id', $fromYearId)->get()->keyBy('id');
    
    // Get classes from new year (to)
    $newClasses = SchoolClass::where('academic_year_id', $toYearId)->get();

    // Create mapping: new grade/major/rombel -> old class_id
    $classMapping = [];
    foreach ($newClasses as $newClass) {
        $oldGrade = match($newClass->grade) {
            'XI' => 'X',
            'XII' => 'XI',
            default => null,
        };

        if ($oldGrade) {
            // Find corresponding old class
            $oldClass = $oldClasses->first(function($class) use ($oldGrade, $newClass) {
                return $class->grade === $oldGrade 
                    && $class->major === $newClass->major
                    && $class->rombel === $newClass->rombel;
            });

            if ($oldClass) {
                $classMapping[$newClass->id] = $oldClass->id;
            }
        }
    }

    // 1. Restore ALUMNI back to XII students
    $alumni = User::where('is_alumni', true)
        ->where('graduation_year', date('Y'))
        ->get();

    $restoredAlumni = 0;
    foreach ($alumni as $student) {
        // Find their old XII class
        $oldClass = $oldClasses->first(function($class) use ($student) {
            return $class->grade === 'XII' 
                && $class->major === $student->major;
        });

        if ($oldClass) {
            $student->update([
                'is_alumni' => false,
                'graduation_year' => null,
                'alumni_notes' => null,
                'class_id' => $oldClass->id,
                'grade' => 'XII',
            ]);
            $restoredAlumni++;
            echo "✓ Alumni: {$student->name} → {$oldClass->name}\n";
        }
    }

    // 2. Restore promoted students (XI and XII)
    $promotedStudents = User::where('role', 'siswa')
        ->where('is_active', true)
        ->where('is_alumni', false)
        ->whereIn('grade', ['XI', 'XII'])
        ->get();

    $restoredPromoted = 0;
    foreach ($promotedStudents as $student) {
        if ($student->class_id && isset($classMapping[$student->class_id])) {
            $oldClassId = $classMapping[$student->class_id];
            $oldClass = $oldClasses[$oldClassId];
            
            $oldGrade = match($student->grade) {
                'XI' => 'X',
                'XII' => 'XI',
                default => $student->grade,
            };

            $student->update([
                'class_id' => $oldClassId,
                'grade' => $oldGrade,
            ]);
            $restoredPromoted++;
            echo "✓ Siswa: {$student->name} ({$student->grade}) → {$oldClass->name} ({$oldGrade})\n";
        }
    }

    // 3. Mark promotion as rolled back
    $promotion->update([
        'is_rolled_back' => true,
        'rolled_back_at' => now(),
        'rolled_back_by' => 1, // Assume admin ID 1
    ]);

    // 4. Switch active academic year
    AcademicYear::where('id', $toYearId)->update(['is_active' => false]);
    AcademicYear::where('id', $fromYearId)->update(['is_active' => true]);

    DB::commit();

    echo "\n========================================\n";
    echo "✅ ROLLBACK BERHASIL!\n";
    echo "========================================\n";
    echo "- Alumni dikembalikan: {$restoredAlumni} siswa\n";
    echo "- Siswa dikembalikan: {$restoredPromoted} siswa\n";
    echo "- Total: " . ($restoredAlumni + $restoredPromoted) . " siswa\n";
    echo "- Tahun ajaran aktif: {$promotion->fromAcademicYear->year}\n\n";
    echo "✅ Sekarang Anda bisa proses kenaikan kelas lagi dengan fitur tracking aktif!\n";

} catch (Exception $e) {
    DB::rollBack();
    echo "\n❌ ERROR: {$e->getMessage()}\n";
    echo "Trace: {$e->getTraceAsString()}\n";
    exit(1);
}
