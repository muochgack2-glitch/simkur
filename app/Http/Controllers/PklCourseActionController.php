<?php

namespace App\Http\Controllers;

use App\Models\PklCourse;
use Illuminate\Http\Request;

class PklCourseActionController extends Controller
{
    public function togglePublish(PklCourse $course)
    {
        $user = auth()->user();
        if ($user->role === 'guru' && $course->teacher_id !== $user->id) {
            abort(403);
        }

        $course->update(['is_published' => !$course->is_published]);
        $status = $course->is_published ? 'dipublikasikan' : 'dijadikan draft';
        return redirect()->route('pkl-learning.dashboard')->with('success', "Course berhasil {$status}");
    }

    public function destroy(PklCourse $course)
    {
        $user = auth()->user();
        if ($user->role === 'guru' && $course->teacher_id !== $user->id) {
            abort(403);
        }

        $title = $course->title;
        $course->delete();
        return redirect()->route('pkl-learning.dashboard')->with('success', "Course \"{$title}\" berhasil dihapus");
    }
}
