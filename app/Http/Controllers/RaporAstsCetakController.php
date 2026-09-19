<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Assessment;
use App\Models\AssessmentStudentSession;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\Setting;
use App\Models\TeachingSchedule;
use App\Models\User;
use Illuminate\Http\Request;

class RaporAstsCetakController extends Controller
{
    public function __invoke(Request $request, int $studentId)
    {
        // Pastikan user adalah wali kelas aktif
        $myClass = SchoolClass::where("homeroom_teacher_id", auth()->id())
            ->where("is_active", true)
            ->first();

        abort_unless($myClass, 403, "Anda bukan wali kelas aktif.");

        // Ambil siswa - pastikan ada di kelas ini
        $student = User::where("id", $studentId)
            ->where("class_id", $myClass->id)
            ->where("role", "siswa")
            ->firstOrFail();

        // Semester aktif
        $academicYear = AcademicYear::where("is_active", true)->first();
        $semType      = (now()->month >= 7) ? "ganjil" : "genap";
        $semester     = $academicYear
            ? Semester::with("academicYear")
                ->where("academic_year_id", $academicYear->id)
                ->where("type", $semType)
                ->first()
              ?? Semester::with("academicYear")->where("academic_year_id", $academicYear->id)->orderByDesc("id")->first()
            : Semester::with("academicYear")->where("type", $semType)->orderByDesc("id")->first()
              ?? Semester::with("academicYear")->orderByDesc("id")->first();

        // Semua mapel dari jadwal mengajar kelas ini
        $subjects = TeachingSchedule::with("subject")
            ->where("class_id", $myClass->id)
            ->where("is_active", true)
            ->when($academicYear?->id, fn($q) => $q->where("academic_year_id", $academicYear->id))
            ->get()
            ->pluck("subject")
            ->filter()
            ->unique("id")
            ->sortBy("name")
            ->values();

        // Asesmen ASTS berlaku untuk kelas ini (untuk hitung nilai)
        $grade = $myClass->grade;
        $major = $myClass->major;

        $assessments = Assessment::with(["subject", "assessmentLabel"])
            ->whereHas("assessmentLabel", fn($q) => $q->where("name", "like", "%ASTS%"))
            ->when($semester?->id, fn($q) => $q->where("semester_id", $semester->id))
            ->where(function ($q) use ($grade) {
                $q->whereJsonContains("target_grades", $grade)->orWhereNull("target_grades");
            })
            ->where(function ($q) use ($major) {
                $q->whereJsonContains("target_majors", $major)->orWhereNull("target_majors");
            })
            ->get();

        // Hitung nilai per mapel
        $nilaiPerMapel = [];
        foreach ($subjects as $subject) {
            $assessmentIds = $assessments->where("subject_id", $subject->id)->pluck("id");
            $sessions = AssessmentStudentSession::whereIn("assessment_id", $assessmentIds)
                ->where("user_id", $student->id)
                ->whereNotNull("submitted_at")
                ->get();

            if ($sessions->isEmpty() || $assessmentIds->isEmpty()) {
                $nilai = 0;
            } else {
                $total = $sessions->sum(function ($s) {
                    if ($s->max_score > 0) {
                        return round($s->total_score / $s->max_score * 100);
                    }
                    return min(100, max(0, (int) round((float) $s->total_score)));
                });
                $nilai = (int) round($total / $assessmentIds->count());
            }
            $nilaiPerMapel[$subject->id] = $nilai;
        }

        // Setting sekolah
        $schoolName    = Setting::getValue("school_name", "SMK PGRI Blora");
        $schoolAddress = Setting::getValue("school_address", "");
        $schoolPhone   = Setting::getValue("school_phone", "");
        $schoolLogo    = Setting::getValue("school_logo", "");
        $principalName = Setting::getValue("principal_name", "");
        $principalNiy  = Setting::getValue("principal_niy", "");

        $waliKelas = auth()->user();

        return view("rapor.asts-per-siswa", compact(
            "student", "myClass", "semester", "subjects", "nilaiPerMapel",
            "schoolName", "schoolAddress", "schoolPhone", "schoolLogo",
            "principalName", "principalNiy", "waliKelas"
        ));
    }
}