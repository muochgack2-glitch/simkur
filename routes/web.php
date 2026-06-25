<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\EffectiveDaysValidationController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\PublicCalendarController;
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
use App\Livewire\EffectiveDay\Index as EffectiveDayIndex;
use App\Livewire\Settings\Index as SettingsIndex;
use App\Livewire\User\Create as UserCreate;
use App\Livewire\User\Edit as UserEdit;
use App\Livewire\User\Index as UserIndex;
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
    Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
    Route::get('/login', [LoginController::class, 'showLoginForm']);
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

/*
|--------------------------------------------------------------------------
| Public Routes (No Authentication Required)
|--------------------------------------------------------------------------
*/

Route::get('/calendar/official', [PublicCalendarController::class, 'index'])->name('calendar.official');
Route::get('/calendar/official/download', [PublicCalendarController::class, 'downloadPdf'])->name('calendar.official.download');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'check.role'])->group(function () {
    // Dashboard - Redirect based on role
    Route::get('/dashboard', function () {
        if (auth()->user()->isKepalaSekolah()) {
            return redirect()->route('dashboard.kepsek');
        }
        return app(DashboardIndex::class);
    })->name('dashboard');
    
    // Kepala Sekolah Dashboard
    Route::get('/dashboard/kepsek', KepsekIndex::class)
        ->middleware('check.role:kepala_sekolah')
        ->name('dashboard.kepsek');
    
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
    });
    
    // Users (Admin & Kepala Sekolah)
    Route::middleware('check.role:admin,kepala_sekolah')->prefix('users')->name('users.')->group(function () {
        Route::get('/', UserIndex::class)->name('index');
        Route::get('/create', UserCreate::class)->name('create');
        Route::get('/{id}/edit', UserEdit::class)->name('edit');
    });
    
    // Profile & Password
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/change-password', ChangePassword::class)->name('change-password');
    });
    
    // Logout
    Route::post('/logout', LogoutController::class)->name('logout');
});
