<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Form Test Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 { color: #333; margin-top: 0; }
        .info {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border-left: 4px solid #2196F3;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }
        button:hover {
            background: #45a049;
        }
        .success {
            background: #e8f5e9;
            padding: 15px;
            border-radius: 4px;
            margin-top: 15px;
            border-left: 4px solid #4CAF50;
            color: #2e7d32;
        }
        .error {
            background: #ffebee;
            padding: 15px;
            border-radius: 4px;
            margin-top: 15px;
            border-left: 4px solid #f44336;
            color: #c62828;
        }
        pre {
            background: #f5f5f5;
            padding: 10px;
            border-radius: 4px;
            overflow-x: auto;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Simple Form Test (No JavaScript)</h1>
        
        <div class="info">
            <strong>Test login dengan FORM SUBMIT BIASA</strong><br>
            Tanpa JavaScript, tanpa Fetch API - murni HTML form.<br><br>
            <strong>Session ID:</strong> {{ session()->getId() }}<br>
            <strong>Session Driver:</strong> {{ config('session.driver') }}
        </div>

        @if(session('success'))
            <div class="success">
                <strong>✓ LOGIN SUCCESSFUL!</strong><br>
                {{ session('success') }}<br>
                <br>
                <a href="{{ route('dashboard') }}">→ Go to Dashboard</a>
            </div>
        @endif

        @if(session('error'))
            <div class="error">
                <strong>✗ LOGIN FAILED</strong><br>
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('test.login.simple') }}">
            @csrf
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="admin" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" value="password" required>
            </div>
            
            <button type="submit">Test Login (Form Submit)</button>
        </form>
        
        @if(session('debug'))
        <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #eee;">
            <h3>Debug Info</h3>
            <pre>{{ session('debug') }}</pre>
        </div>
        @endif
        
        <div style="margin-top: 20px;">
            <a href="{{ route('test.session') }}" target="_blank">→ Test Session Endpoint</a><br>
            <a href="{{ route('dashboard') }}">→ Go to Dashboard (normal)</a><br>
            <a href="{{ route('login') }}">→ Back to Normal Login</a>
        </div>
    </div>
</body>
</html>
