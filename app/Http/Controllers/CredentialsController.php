<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class CredentialsController extends Controller
{
    public function index()
    {
        // Get all students grouped by class
        $students = User::where('role', 'siswa')
            ->with('schoolClass.homeroomTeacher')
            ->orderBy('class_id')
            ->orderBy('name')
            ->get()
            ->groupBy(function($student) {
                return $student->schoolClass ? $student->schoolClass->name : 'Belum Ada Kelas';
            });

        // Get all teachers
        $teachers = User::where('role', 'guru')
            ->orderBy('name')
            ->get();

        // Get all staff (admin, kepsek, waka)
        $staff = User::whereIn('role', ['admin', 'kepsek', 'waka_kurikulum'])
            ->orderBy('role')
            ->orderBy('name')
            ->get();

        return view('credentials', compact('students', 'teachers', 'staff'));
    }
}
