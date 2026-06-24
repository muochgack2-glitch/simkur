<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Test Login - No Livewire</title>
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
            background: #2196F3;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }
        button:hover {
            background: #1976D2;
        }
        button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        .info {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border-left: 4px solid #2196F3;
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
        .debug {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #eee;
        }
        a {
            color: #2196F3;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Test Login (No Livewire)</h1>
        
        <div class="info">
            <strong>Test login menggunakan vanilla JavaScript + Laravel controller</strong><br>
            Bypass Livewire untuk diagnosa masalah session.<br><br>
            <strong>Session ID:</strong> {{ session()->getId() }}<br>
            <strong>Session Driver:</strong> {{ config('session.driver') }}<br>
            <strong>CSRF Token:</strong> {{ csrf_token() }}
        </div>

        <form id="loginForm">
            @csrf
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="admin" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" value="password" required>
            </div>
            
            <button type="submit" id="submitBtn">Test Login</button>
        </form>

        <div id="result"></div>
        
        <div class="debug">
            <h3>Debug Info</h3>
            <pre id="debugInfo">Waiting for login attempt...</pre>
        </div>
        
        <div style="margin-top: 20px;">
            <a href="{{ route('test.session') }}" target="_blank">→ Test Session Endpoint</a><br>
            <a href="{{ route('dashboard') }}">→ Go to Dashboard (normal)</a><br>
            <a href="{{ route('login') }}">→ Back to Normal Login</a>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submitBtn');
            const resultDiv = document.getElementById('result');
            const debugDiv = document.getElementById('debugInfo');
            
            submitBtn.disabled = true;
            submitBtn.textContent = 'Logging in...';
            resultDiv.innerHTML = '';
            
            const formData = new FormData(this);
            
            try {
                debugDiv.textContent = 'Sending request to: {{ route('test.login') }}';
                
                const response = await fetch('{{ route('test.login') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    credentials: 'same-origin',
                });
                
                debugDiv.textContent += '\nResponse status: ' + response.status;
                debugDiv.textContent += '\nResponse headers: ' + JSON.stringify([...response.headers.entries()]);
                
                if (!response.ok) {
                    const errorText = await response.text();
                    throw new Error('HTTP ' + response.status + ': ' + errorText);
                }
                
                const data = await response.json();
                
                debugDiv.textContent = JSON.stringify(data, null, 2);
                
                if (data.success) {
                    resultDiv.innerHTML = `
                        <div class="success">
                            <strong>✓ LOGIN SUCCESSFUL!</strong><br>
                            User ID: ${data.user_id}<br>
                            Session ID: ${data.session_id}<br>
                            <br>
                            <a href="${data.redirect}">→ Go to Dashboard</a>
                        </div>
                    `;
                } else {
                    resultDiv.innerHTML = `
                        <div class="error">
                            <strong>✗ LOGIN FAILED</strong><br>
                            ${data.message}
                        </div>
                    `;
                }
            } catch (error) {
                resultDiv.innerHTML = `
                    <div class="error">
                        <strong>✗ ERROR</strong><br>
                        ${error.message}
                    </div>
                `;
                debugDiv.textContent = 'Error details:\n' + error.toString() + '\n\n' + error.stack;
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Test Login';
            }
        });
    </script>
</body>
</html>
