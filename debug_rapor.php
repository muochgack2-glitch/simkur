<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Semester aktif
$ay = App\Models\AcademicYear::where('is_active', true)->first();
$sem = $ay ? App\Models\Semester::where('academic_year_id', $ay->id)->orderByDesc('id')->first() : null;
echo "AY aktif: " . ($ay->name ?? 'NULL') . "\n";
echo "Semester aktif: " . ($sem->name ?? 'NULL') . " (id=" . ($sem->id ?? 'NULL') . ")\n\n";

// Kelas X AKL
$kelas = App\Models\SchoolClass::where('grade','X')->where('major','AKL')->where('is_active',true)->first();
echo "Kelas: " . ($kelas->name ?? 'NULL') . " (id=" . ($kelas->id ?? 'NULL') . ")\n\n";

// Semua asesmen ASTS
$assessments = App\Models\Assessment::with(['assessmentLabel','subject'])
    ->whereHas('assessmentLabel', fn($q) => $q->where('name','like','%ASTS%'))
    ->get();
echo "Total asesmen ASTS: " . $assessments->count() . "\n";
foreach ($assessments as $a) {
    echo "  [{$a->id}] {$a->title} | subject=" . ($a->subject->name ?? '?') .
         " | semester_id=" . ($a->semester_id ?? 'NULL') .
         " | is_published=" . ($a->is_published ? 'true' : 'false') .
         " | target_grades=" . json_encode($a->target_grades) .
         " | target_majors=" . json_encode($a->target_majors) . "\n";
}

// Cek session Santi
$santi = App\Models\User::where('nis','2035')->first();
echo "\nSanti: " . ($santi->name ?? 'NULL') . " id=" . ($santi->id ?? '?') . "\n";
$sessions = App\Models\AssessmentStudentSession::where('user_id', $santi?->id)->get();
foreach ($sessions as $s) {
    echo "  session assessment_id={$s->assessment_id} total={$s->total_score} max={$s->max_score} submitted=" . ($s->submitted_at ?? 'NULL') . "\n";
}