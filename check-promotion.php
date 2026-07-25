<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ClassPromotion;

$latest = ClassPromotion::orderBy('processed_at', 'desc')->first();

echo "Promosi Terakhir:\n";
echo "ID: {$latest->id}\n";
echo "Tanggal: {$latest->processed_at}\n";
echo "Is Rolled Back: " . ($latest->is_rolled_back ? 'YES' : 'NO') . "\n";
echo "Has Student Details: " . ($latest->student_details ? 'YES' : 'NO') . "\n";
echo "Can Rollback: " . ($latest->canRollback() ? 'YES ✅' : 'NO ❌') . "\n";

echo "\n--- All Promotions ---\n";
$all = ClassPromotion::orderBy('processed_at', 'desc')->get();
foreach ($all as $p) {
    echo "ID {$p->id}: {$p->processed_at} - Rolled: " . ($p->is_rolled_back ? 'YES' : 'NO') . " - Can Rollback: " . ($p->canRollback() ? 'YES' : 'NO') . "\n";
}
