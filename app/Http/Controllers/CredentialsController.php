<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class CredentialsController extends Controller
{
    public function index()
    {
        // Check if password is verified in session
        if (!session('credentials_verified')) {
            return view('credentials-login');
        }

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

    public function verify(Request $request)
    {
        $request->validate([
            'password' => 'required',
        ]);

        $correctPassword = env('CREDENTIALS_PASSWORD', 'smkpgri2026');

        if ($request->password === $correctPassword) {
            session(['credentials_verified' => true]);
            return redirect()->route('credentials');
        }

        return back()->withErrors([
            'password' => 'Password salah. Silakan coba lagi.',
        ]);
    }
}
