<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\EffectiveDaysValidationController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\PublicCalendarController;
use App\Http\Controllers\AssessmentSubmitController;
use App\Http\Controllers\CredentialsController;
use App\Livewire\AcademicYear\Create as AcademicYearCreate;
use App\Livewire\AcademicYear\Edit as AcademicYearEdit;
use App\Livewire\AcademicYear\Index as AcademicYearIndex;
use App\Livewire\Activity\Create as ActivityCreate;
use App\Livewire\Activity\Edit as ActivityEdit;
use App\Livewire\Activity\Export as ActivityExport;
use App\Livewire\Activity\Import as ActivityImport;
use App\Livewire\Activity\Index as ActivityIndex;
use App\Livewire\ActivityType\Create as ActivityTypeCreate;
use App\Livewire\ActivityType\Edit as ActivityTypeEdit;
use App\Livewire\ActivityType\Index as ActivityTypeIndex;
use App\Livewire\Auth\ChangePassword;
use App\Livewire\Auth\Login;
use App\Livewire\Dashboard\Index as DashboardIndex;
use App\Livewire\Dashboard\KepsekIndex;
use App\Livewire\Dashboard\GuruIndex;
use App\Livewire\Dashboard\SiswaIndex;
use App\Livewire\EffectiveDay\Index as EffectiveDayIndex;
use App\Livewire\Settings\Index as SettingsIndex;
use App\Livewire\Settings\TimeSlots;
use App\Livewire\User\Create as UserCreate;
use App\Livewire\User\Edit as UserEdit;
use App\Livewire\User\Import as UserImport;
use App\Livewire\User\Index as UserIndex;
use App\Livewire\Assessment\Index as AssessmentIndex;
use App\Livewire\Assessment\Create as AssessmentCreate;
use App\Livewire\Assessment\Edit as AssessmentEdit;
use App\Livewire\Assessment\Monitoring as AssessmentMonitoring;
use App\Livewire\Assessment\ManageQuestions as AssessmentManageQuestions;
use App\Livewire\Assessment\ClassReport as AssessmentClassReport;
use App\Livewire\Assessment\StudentProfile as AssessmentStudentProfile;
use App\Livewire\StudentAssessment\Index as StudentAssessmentIndex;
use App\Livewire\StudentAssessment\Take as StudentAssessmentTake;
use App\Livewire\StudentAssessment\Result as StudentAssessmentResult;
use App\Livewire\Subject\Index as SubjectIndex;
use App\Livewire\Subject\Create as SubjectCreate;
use App\Livewire\Subject\Edit as SubjectEdit;
use App\Livewire\SchoolClass\Index as SchoolClassIndex;
use App\Livewire\SchoolClass\Create as SchoolClassCreate;
use App\Livewire\SchoolClass\Edit as SchoolClassEdit;
use App\Livewire\TeachingJournal\Index as TeachingJournalIndex;
use App\Livewire\TeachingJournal\Create as TeachingJournalCreate;
use App\Livewire\TeachingJournal\Edit as TeachingJournalEdit;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Debug/Test Routes (REMOVE IN PRODUCTION!)
|--------------------------------------------------------------------------
*/

Route::get('/test-session', function () {
    $output = [];
    
    // Session info
    $output['session_id'] = session()->getId();
    $output['session_driver'] = config('session.driver');
    $output['session_all'] = session()->all();
    
    // Test write
    session(['test_time' => time()]);
    $output['test_written'] = session('test_time');
    
    // Auth info
    $output['auth_check'] = Auth::check();
    $output['auth_id'] = Auth::id();
    
    return response()->json($output, 200, [], JSON_PRETTY_PRINT);
})->name('test.session');

Route::match(['get', 'post'], '/test-login-controller', function () {
    if (request()->isMethod('post')) {
        $credentials = [
            'username' => request('username'),
            'password' => request('password'),
        ];
        
        $result = Auth::attempt($credentials);
        
        if ($result) {
            request()->session()->regenerate();
            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'user_id' => Auth::id(),
                'session_id' => session()->getId(),
                'redirect' => route('dashboard'),
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials',
        ], 401);
    }
    
    return view('test-login');
})->name('test.login');

Route::match(['get', 'post'], '/test-login-simple', function () {
    if (request()->isMethod('post')) {
        $credentials = [
            'username' => request('username'),
            'password' => request('password'),
        ];
        
        $debug = "Attempting login with username: " . request('username') . "\n";
        
        $result = Auth::attempt($credentials);
        $debug .= "Auth::attempt() result: " . ($result ? 'true' : 'false') . "\n";
        
        if ($result) {
            $debug .= "User ID: " . Auth::id() . "\n";
            $debug .= "User name: " . Auth::user()->name . "\n";
            $debug .= "Session ID before regenerate: " . session()->getId() . "\n";
            
            request()->session()->regenerate();
            
            $debug .= "Session ID after regenerate: " . session()->getId() . "\n";
            $debug .= "Auth::check() after regenerate: " . (Auth::check() ? 'true' : 'false') . "\n";
            
            return redirect()->route('test.login.simple')
                ->with('success', 'Login berhasil! User ID: ' . Auth::id())
                ->with('debug', $debug);
        }
        
        $debug .= "Login failed - invalid credentials\n";
        
        return redirect()->route('test.login.simple')
            ->with('error', 'Username atau password salah')
            ->with('debug', $debug);
    }
    
    return view('test-login-simple-form');
})->name('test.login.simple');

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

/*
|--------------------------------------------------------------------------
| Public Routes (No Authentication Required)
|--------------------------------------------------------------------------
*/

// New: Kalender Pendidikan Public Routes
Route::get('/kaldik', [PublicCalendarController::class, 'index'])->name('kaldik.index');
Route::get('/kaldik/download', [PublicCalendarController::class, 'downloadPdf'])->name('kaldik.download');

// Credentials Page - Password Protected (untuk distribusi akun)
Route::get('/user', [CredentialsController::class, 'index'])->name('credentials');
Route::post('/user/verify', [CredentialsController::class, 'verify'])->name('credentials.verify');

// Legacy: Redirect old URLs to new ones
Route::get('/calendar/official', function () {
    return redirect()->route('kaldik.index', [], 301);
});
Route::get('/calendar/official/download', function () {
    return redirect()->route('kaldik.download', [], 301);
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'check.role'])->group(function () {
    // Root redirect to dashboard based on role
    Route::get('/', function () {
        $user = auth()->user();
        
        if ($user->isKepalaSekolah()) {
            return redirect()->route('dashboard.kepsek');
        } elseif ($user->isGuru()) {
            return redirect()->route('dashboard.guru');
        } elseif ($user->role === 'siswa') {
            return redirect()->route('dashboard.siswa');
        }
        
        return redirect()->route('dashboard');
    });
    
    // Dashboard - Redirect based on role
    Route::get('/dashboard', function () {
        $user = auth()->user();
        
        if ($user->isKepalaSekolah()) {
            return redirect()->route('dashboard.kepsek');
        } elseif ($user->isGuru()) {
            return redirect()->route('dashboard.guru');
        } elseif ($user->role === 'siswa') {
            return redirect()->route('dashboard.siswa');
        }
        
        // Admin and Waka Kurikulum
        return redirect()->route('dashboard.admin');
    })->name('dashboard');
    
    // Dashboard - Admin/Waka Kurikulum
    Route::get('/dashboard/admin', DashboardIndex::class)
        ->middleware('check.role:admin,waka_kurikulum')
        ->name('dashboard.admin');
    
    // Kepala Sekolah Dashboard
    Route::get('/dashboard/kepsek', KepsekIndex::class)
        ->middleware('check.role:kepala_sekolah')
        ->name('dashboard.kepsek');
    
    // Guru Dashboard
    Route::get('/dashboard/guru', GuruIndex::class)
        ->middleware('check.role:guru')
        ->name('dashboard.guru');
    
    // Siswa Dashboard
    Route::get('/dashboard/siswa', SiswaIndex::class)
        ->middleware('check.role:siswa')
        ->name('dashboard.siswa');
    
    // Academic Years
    Route::prefix('academic-years')->name('academic-years.')->group(function () {
        Route::get('/', AcademicYearIndex::class)->name('index');
        Route::get('/create', AcademicYearCreate::class)->name('create');
        Route::get('/{id}/edit', AcademicYearEdit::class)->name('edit');
    });
    
    // Activity Types
    Route::prefix('activity-types')->name('activity-types.')->group(function () {
        Route::get('/', ActivityTypeIndex::class)->name('index');
        Route::get('/create', ActivityTypeCreate::class)->name('create');
        Route::get('/{id}/edit', ActivityTypeEdit::class)->name('edit');
    });
    
    // Activities (Calendar)
    Route::prefix('activities')->name('activities.')->group(function () {
        Route::get('/', ActivityIndex::class)->name('index');
        Route::get('/create', ActivityCreate::class)->name('create');
        Route::get('/import', ActivityImport::class)->name('import');
        Route::get('/export', ActivityExport::class)->name('export');
        
        // Export download routes
        Route::get('/export/yearly', [ExportController::class, 'yearly'])->name('export.yearly');
        Route::get('/export/monthly', [ExportController::class, 'monthly'])->name('export.monthly');
        Route::get('/export/list', [ExportController::class, 'list'])->name('export.list');
        Route::get('/export/excel', [ExportController::class, 'excel'])->name('export.excel');
        
        Route::get('/{id}/edit', ActivityEdit::class)->name('edit');
    });
    
    // Effective Days
    Route::get('/effective-days', EffectiveDayIndex::class)->name('effective-days.index');
    Route::get('/effective-days/validation', [EffectiveDaysValidationController::class, 'index'])->name('effective-days.validation');
    
    // Settings (Admin Only)
    Route::middleware('check.role:admin')->group(function () {
        Route::get('/settings', SettingsIndex::class)->name('settings.index');
        Route::get('/settings/time-slots', TimeSlots::class)->name('settings.time-slots');
    });
    
    // Master Data - Mata Pelajaran (Admin & Kepala Sekolah)
    Route::middleware('check.role:admin,kepala_sekolah')->prefix('subjects')->name('subjects.')->group(function () {
        Route::get('/', SubjectIndex::class)->name('index');
        Route::get('/create', SubjectCreate::class)->name('create');
        Route::get('/{id}/edit', SubjectEdit::class)->name('edit');
    });
    
    // Master Data - Kelas (Admin & Kepala Sekolah)
    Route::middleware('check.role:admin,kepala_sekolah')->prefix('classes')->name('classes.')->group(function () {
        Route::get('/', SchoolClassIndex::class)->name('index');
        Route::get('/create', SchoolClassCreate::class)->name('create');
        Route::get('/{id}/edit', SchoolClassEdit::class)->name('edit');
    });
    
    // Users (Admin & Kepala Sekolah)
    Route::middleware('check.role:admin,kepala_sekolah')->prefix('users')->name('users.')->group(function () {
        Route::get('/', UserIndex::class)->name('index');
        Route::get('/create', UserCreate::class)->name('create');
        Route::get('/import', UserImport::class)->name('import');
        Route::get('/{id}/edit', UserEdit::class)->name('edit');
    });
    
    // Teaching Journal (Guru, Waka, Kepsek, Admin)
    Route::middleware('check.role:admin,waka_kurikulum,kepala_sekolah,guru')->prefix('teaching-journal')->name('teaching-journal.')->group(function () {
        Route::get('/', TeachingJournalIndex::class)->name('index');
        Route::get('/create', TeachingJournalCreate::class)->name('create');
        Route::get('/{id}/edit', TeachingJournalEdit::class)->name('edit');
    });
    
    // Profile & Password
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/change-password', ChangePassword::class)->name('change-password');
    });
    
    // Assessments - Admin/Waka Kurikulum Only
    Route::middleware('check.role:admin,waka_kurikulum')->prefix('assessments')->name('assessment.')->group(function () {
        Route::get('/', AssessmentIndex::class)->name('index');
        Route::get('/create', AssessmentCreate::class)->name('create');
        Route::get('/{id}/edit', AssessmentEdit::class)->name('edit');
        Route::get('/{id}/questions', AssessmentManageQuestions::class)->name('questions');
        Route::get('/{id}/monitoring', AssessmentMonitoring::class)->name('monitoring');
    });
    
    // Assessment Class Report - Teachers, Waka, Kepsek, Admin
    Route::middleware('check.role:admin,waka_kurikulum,kepala_sekolah,guru')->group(function () {
        Route::get('/assessment/class-report', AssessmentClassReport::class)->name('assessment.class-report');
        Route::get('/assessment/student-profile/{userId}/{assessmentId}', AssessmentStudentProfile::class)->name('assessment.student-profile');
    });
    
    // Student Assessment - Siswa Only
    Route::middleware('check.role:siswa')->prefix('student/assessment')->name('student.assessment.')->group(function () {
        Route::get('/', StudentAssessmentIndex::class)->name('index');
        // Route::get('/{id}/take', StudentAssessmentTake::class)->name('take'); // OLD Livewire version
        Route::get('/{id}/take', [AssessmentSubmitController::class, 'show'])->name('take'); // NEW non-Livewire version
        Route::post('/{id}/submit', [AssessmentSubmitController::class, 'submit'])->name('submit');
        Route::get('/{id}/result', StudentAssessmentResult::class)->name('result');
    });
    
    // Logout
    Route::post('/logout', LogoutController::class)->name('logout');
});
