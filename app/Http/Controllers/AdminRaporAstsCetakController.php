<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Assessment;
use App\Models\AssessmentStudentSession;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\Setting;
use App\Models\Subject;
use App\Models\TeachingSchedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminRaporAstsCetakController extends Controller
{
    public function __invoke(Request $request, int $classId, int $studentId)
    {
        // Admin/waka: boleh cetak kelas manapun (tanpa cek homeroom)
        $myClass = SchoolClass::findOrFail($classId);

        $student = User::where('id', $studentId)
            ->where('class_id', $classId)
            ->where('role', 'siswa')
            ->firstOrFail();

        $academicYear = AcademicYear::where('is_active', true)->first();
        $semType      = (now()->month >= 7) ? 'ganjil' : 'genap';
        $semester     = $academicYear
            ? Semester::with('academicYear')
                ->where('academic_year_id', $academicYear->id)
                ->where('type', $semType)
                ->first()
              ?? Semester::with('academicYear')->where('academic_year_id', $academicYear->id)->orderByDesc('id')->first()
            : Semester::with('academicYear')->where('type', $semType)->orderByDesc('id')->first()
              ?? Semester::with('academicYear')->orderByDesc('id')->first();

        // Mapel dari jadwal kelas
        $allSubjects = TeachingSchedule::with('subject')
            ->where('class_id', $classId)
            ->where('is_active', true)
            ->when($academicYear?->id, fn($q) => $q->where('academic_year_id', $academicYear->id))
            ->get()
            ->pluck('subject')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->values();

        // Filter agama: siswa mendapat mapel agama sesuai agamanya
        try {
            $agamaSubjects = $allSubjects->filter(fn($s) => !is_null($s->agama_filter))->values();
            $nonAgama      = $allSubjects->filter(fn($s) => is_null($s->agama_filter))->values();
            if ($agamaSubjects->isNotEmpty()) {
                // Cari mapel agama yang cocok dengan agama siswa
                $matchedAgama = $agamaSubjects->filter(fn($s) => $s->agama_filter === $student->agama)->values();
                // Jika tidak ada guru agama yang cocok di kelas ini, tampilkan semua agama yang ada di kelas
                if ($matchedAgama->isEmpty()) {
                    $matchedAgama = $agamaSubjects;
                }
                $subjects = $nonAgama->merge($matchedAgama)->sortBy('name')->values();
            } else {
                // agama_filter belum dikonfigurasi: tampilkan semua mapel
                $subjects = $allSubjects;
            }
        } catch (\Throwable $e) {
            $subjects = $allSubjects;
        }

        $grade = $myClass->grade;
        $major = $myClass->major;

        $assessments = Assessment::with(['subject', 'assessmentLabel'])
            ->whereHas('assessmentLabel', fn($q) => $q->where('name', 'like', '%ASTS%'))
            ->when($semester?->id, fn($q) => $q->where('semester_id', $semester->id))
            ->where(function ($q) use ($grade) {
                $q->whereJsonContains('target_grades', $grade)->orWhereNull('target_grades');
            })
            ->where(function ($q) use ($major) {
                $q->whereJsonContains('target_majors', $major)->orWhereNull('target_majors');
            })
            ->get();

        $nilaiPerMapel = [];
        foreach ($subjects as $subject) {
            $assessmentIds = $assessments->where('subject_id', $subject->id)->pluck('id');
            $sessions = AssessmentStudentSession::whereIn('assessment_id', $assessmentIds)
                ->where('user_id', $student->id)
                ->whereNotNull('submitted_at')
                ->get();

            if ($sessions->isEmpty() || $assessmentIds->isEmpty()) {
                $nilai = 0;
            } else {
                $total = $sessions->sum(function ($s) {
                    if ($s->max_score > 0) return round($s->total_score / $s->max_score * 100);
                    return min(100, max(0, (int) round((float) $s->total_score)));
                });
                $nilai = (int) round($total / $sessions->count());
            }
            $nilaiPerMapel[$subject->id] = $nilai;
        }

        $schoolName    = Setting::getValue('school_name', 'SMK PGRI Blora');
        $schoolAddress = Setting::getValue('school_address', '');
        $schoolPhone   = Setting::getValue('school_phone', '');
        $schoolLogo    = Setting::getValue('school_logo', '');
        $principalName = Setting::getValue('principal_name', '');
        $principalNiy  = Setting::getValue('principal_niy', '');
        $tanggalCetak = Setting::getValue('rapor_tanggal_cetak', '');

        $kopSuratRaw = Setting::getValue('kop_surat_rapor', '');
        $kopSuratUrl = ($kopSuratRaw && Storage::disk('public')->exists($kopSuratRaw))
            ? Storage::disk('public')->url($kopSuratRaw)
            : null;

        // Wali kelas kelas ini
        $waliKelas = $myClass->homeroomTeacher ?? auth()->user();

        return view('rapor.asts-per-siswa', compact(
            'student', 'myClass', 'semester', 'subjects', 'nilaiPerMapel',
            'schoolName', 'schoolAddress', 'schoolPhone', 'schoolLogo',
            'principalName', 'principalNiy', 'waliKelas', 'kopSuratUrl', 'tanggalCetak'
        ));
    }
}