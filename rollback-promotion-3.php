<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ClassPromotion;
use App\Models\AcademicYear;
use App\Models\User;
use Illuminate\Support\Facades\DB;

try {
    DB::beginTransaction();

    $promotion = ClassPromotion::find(3);
    
    if (!$promotion) {
        echo "Promosi ID 3 tidak ditemukan\n";
        exit(1);
    }

    echo "Rollback promosi ID 3...\n";
    echo "Dari: {$promotion->fromAcademicYear->year}\n";
    echo "Ke: {$promotion->toAcademicYear->year}\n\n";

    // Restore students
    if (!empty($promotion->student_details)) {
        $restored = 0;
        foreach ($promotion->student_details as $detail) {
            $student = User::find($detail['student_id']);
            if ($student) {
                $student->update([
                    'class_id' => $detail['previous_class_id'],
                    'grade' => $detail['previous_grade'],
                    'is_alumni' => $detail['previous_is_alumni'],
                    'graduation_year' => null,
                    'alumni_notes' => null,
                ]);
                $restored++;
            }
        }
        echo "✓ {$restored} siswa dikembalikan\n";
    }

    // Mark as rolled back
    $promotion->update([
        'is_rolled_back' => true,
        'rolled_back_at' => now(),
        'rolled_back_by' => 1,
    ]);

    // Switch academic year
    $promotion->toAcademicYear->update(['is_active' => false]);
    $promotion->fromAcademicYear->update(['is_active' => true]);

    echo "✓ Tahun ajaran dikembalikan ke {$promotion->fromAcademicYear->year}\n";

    DB::commit();

    echo "\n✅ Rollback berhasil!\n";
    echo "Sekarang proses kenaikan kelas lagi dengan konfigurasi rombel yang benar.\n";

} catch (Exception $e) {
    DB::rollBack();
    echo "❌ ERROR: {$e->getMessage()}\n";
    exit(1);
}
