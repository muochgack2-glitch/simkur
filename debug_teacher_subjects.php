<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Cari Adela
$adela = App\Models\User::where('role', 'guru')->where('name', 'like', '%Adela%')->first();
if (!$adela) {
    echo "Guru 'Adela' tidak ditemukan.\n";
    // Tampilkan semua guru
    $gurus = App\Models\User::where('role', 'guru')->get(['id', 'name']);
    foreach ($gurus as $g) echo "Guru: [{$g->id}] {$g->name}\n";
    exit;
}

echo "Guru: [{$adela->id}] {$adela->name}\n";

$ts = DB::table('teaching_schedules')->where('teacher_id', $adela->id)->get();
echo "teaching_schedules: " . $ts->count() . " rows\n";
foreach ($ts as $r) echo "  - subject_id={$r->subject_id} is_active={$r->is_active}\n";

$pivot = DB::table('teacher_subjects')->where('user_id', $adela->id)->get();
echo "teacher_subjects: " . $pivot->count() . " rows\n";
foreach ($pivot as $r) echo "  - subject_id={$r->subject_id}\n";