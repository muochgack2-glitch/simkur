<?php
/**
 * SIMPLE LOGIN TEST - BYPASS LIVEWIRE
 * 
 * Akses: https://simkur.smkpgriblora.sch.id/test-login-simple.php
 * 
 * Test login tanpa Livewire untuk isolasi masalah
 */

// Bootstrap Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$app->instance('request', $request);

// Start session
session_start();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Simple Login Test</title>
    <style>
        body { font-family: Arial; max-width: 600px; margin: 50px auto; padding: 20px; }
        .info { background: #e3f2fd; padding: 10px; margin: 10px 0; border-radius: 4px; }
        .error { background: #ffebee; padding: 10px; margin: 10px 0; border-radius: 4px; color: #c62828; }
        .success { background: #e8f5e9; padding: 10px; margin: 10px 0; border-radius: 4px; color: #2e7d32; }
        input { padding: 8px; margin: 5px 0; width: 100%; }
        button { padding: 10px 20px; background: #2196F3; color: white; border: none; cursor: pointer; }
        button:hover { background: #1976D2; }
        pre { background: #f5f5f5; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🔍 Simple Login Test</h1>
    <p>Test login tanpa Livewire untuk diagnosa masalah</p>

    <?php
    $messages = [];
    $sessionData = [];
    
    // SESSION INFO
    $messages[] = ['type' => 'info', 'msg' => '<strong>Session ID:</strong> ' . session_id()];
    $messages[] = ['type' => 'info', 'msg' => '<strong>Session Save Path:</strong> ' . session_save_path()];
    $messages[] = ['type' => 'info', 'msg' => '<strong>PHP Session Module:</strong> ' . ini_get('session.save_handler')];
    
    // CONFIG INFO
    $messages[] = ['type' => 'info', 'msg' => '<strong>Laravel Session Driver:</strong> ' . config('session.driver')];
    $messages[] = ['type' => 'info', 'msg' => '<strong>Session Cookie:</strong> ' . config('session.cookie')];
    $messages[] = ['type' => 'info', 'msg' => '<strong>Session Secure:</strong> ' . (config('session.secure') ? 'true' : 'false')];
    
    // HANDLE LOGIN
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        $messages[] = ['type' => 'info', 'msg' => "Login attempt for: <strong>$username</strong>"];
        
        // TEST 1: Native PHP Session
        $_SESSION['test_native'] = 'native_' . time();
        $messages[] = ['type' => 'success', 'msg' => '✓ Native PHP session write: ' . $_SESSION['test_native']];
        
        // TEST 2: Laravel Session
        session(['test_laravel' => 'laravel_' . time()]);
        $test = session('test_laravel');
        if ($test) {
            $messages[] = ['type' => 'success', 'msg' => '✓ Laravel session write/read: ' . $test];
        } else {
            $messages[] = ['type' => 'error', 'msg' => '✗ Laravel session FAILED to persist!'];
        }
        
        // TEST 3: Auth Attempt
        try {
            $attempt = Auth::attempt(['username' => $username, 'password' => $password]);
            
            if ($attempt) {
                $messages[] = ['type' => 'success', 'msg' => '✓ Auth::attempt() SUCCESS'];
                $messages[] = ['type' => 'success', 'msg' => '✓ Logged in as: ' . Auth::user()->name . ' (ID: ' . Auth::id() . ')'];
                
                // Check if session persists
                $_SESSION['logged_in_user'] = Auth::user()->username;
                
                // Try redirect
                $messages[] = ['type' => 'success', 'msg' => '✓ Now try to access dashboard: <a href="/dashboard">Go to Dashboard</a>'];
                
            } else {
                $messages[] = ['type' => 'error', 'msg' => '✗ Auth::attempt() FAILED - Invalid credentials'];
            }
        } catch (Exception $e) {
            $messages[] = ['type' => 'error', 'msg' => '✗ Exception: ' . $e->getMessage()];
        }
        
        // TEST 4: Check if Auth persists
        if (Auth::check()) {
            $messages[] = ['type' => 'success', 'msg' => '✓ Auth::check() returns TRUE'];
        } else {
            $messages[] = ['type' => 'error', 'msg' => '✗ Auth::check() returns FALSE'];
        }
    }
    
    // DISPLAY SESSION DATA
    $sessionData['PHP $_SESSION'] = $_SESSION ?? [];
    $sessionData['Laravel session()->all()'] = session()->all();
    $sessionData['Auth::check()'] = Auth::check();
    $sessionData['Auth::id()'] = Auth::id();
    
    // Display messages
    foreach ($messages as $msg) {
        echo "<div class='{$msg['type']}'>{$msg['msg']}</div>";
    }
    ?>

    <hr>
    
    <h2>Login Form</h2>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" value="admin" required>
        <input type="password" name="password" placeholder="Password" value="password" required>
        <button type="submit">Test Login</button>
    </form>
    
    <hr>
    
    <h2>Session Data</h2>
    <pre><?php print_r($sessionData); ?></pre>
    
    <hr>
    
    <h2>Cookie Headers</h2>
    <pre><?php
    echo "Set-Cookie headers that will be sent:\n";
    foreach (headers_list() as $header) {
        if (stripos($header, 'cookie') !== false) {
            echo $header . "\n";
        }
    }
    
    echo "\nReceived cookies:\n";
    print_r($_COOKIE);
    ?></pre>
    
    <hr>
    
    <p><a href="?refresh=1">Refresh Page</a> | <a href="/dashboard">Go to Dashboard</a></p>
    
</body>
</html>
