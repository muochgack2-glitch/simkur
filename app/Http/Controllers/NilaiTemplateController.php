<?php

namespace App\Http\Controllers;

use App\Exports\NilaiTemplateExport;
use App\Models\Assessment;
use App\Models\SchoolClass;
use Maatwebsite\Excel\Facades\Excel;

class NilaiTemplateController extends Controller
{
    public function __invoke(int $assessmentId, int $classId)
    {
        $assessment = Assessment::with(['subject'])->findOrFail($assessmentId);
        $class      = SchoolClass::findOrFail($classId);

        $user = auth()->user();
        if ($user->role === 'guru' && $assessment->teacher_id !== $user->id) {
            abort(403);
        }

        $filename = 'template-nilai-' . str($assessment->title)->slug() . '-' . str($class->name)->slug() . '.xlsx';

        return Excel::download(
            new NilaiTemplateExport($assessment, $class),
            $filename
        );
    }
}