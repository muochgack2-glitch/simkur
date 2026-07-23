<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Daftar Akun - SIM Kurikulum SMK PGRI Blora</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 450px;
            width: 100%;
            padding: 40px;
        }
        
        .logo-section {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(37, 99, 235, 0.3);
        }
        
        h1 {
            color: #1f2937;
            font-size: 1.8rem;
            margin-bottom: 10px;
        }
        
        .subtitle {
            color: #6b7280;
            font-size: 0.95rem;
            margin-bottom: 10px;
        }
        
        .school-name {
            color: #2563eb;
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .badges {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 15px;
        }
        
        .badge {
            background: #eff6ff;
            color: #2563eb;
            padding: 6px 14px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .form-section {
            margin-top: 30px;
        }
        
        .info-box {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        
        .info-box p {
            color: #92400e;
            font-size: 0.9rem;
            line-height: 1.5;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            color: #374151;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 0.95rem;
        }
        
        input[type="password"] {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s;
            font-family: 'Courier New', monospace;
        }
        
        input[type="password"]:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        
        .error-message {
            background: #fee2e2;
            border-left: 4px solid #ef4444;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .error-message p {
            color: #991b1b;
            font-size: 0.9rem;
        }
        
        button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
        }
        
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
        }
        
        button:active {
            transform: translateY(0);
        }
        
        .footer {
            text-align: center;
            margin-top: 25px;
            padding-top: 25px;
            border-top: 1px solid #e5e7eb;
        }
        
        .footer p {
            color: #6b7280;
            font-size: 0.85rem;
        }
        
        .warning {
            background: #fef2f2;
            border: 1px solid #fecaca;
            padding: 12px;
            border-radius: 8px;
            margin-top: 15px;
        }
        
        .warning p {
            color: #991b1b;
            font-size: 0.85rem;
            text-align: center;
        }
        
        @media (max-width: 480px) {
            .login-container {
                padding: 30px 20px;
            }
            h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Logo Section -->
        <div class="logo-section">
            <div class="logo">🔐</div>
            <h1>Akses Daftar Akun</h1>
            <p class="subtitle">SIM Kurikulum</p>
            <p class="school-name">SMK PGRI Blora</p>
            <div class="badges">
                <span class="badge">MPLB</span>
                <span class="badge">AKL</span>
                <span class="badge">BUSANA</span>
            </div>
        </div>

        <!-- Info Box -->
        <div class="info-box">
            <p><strong>ℹ️ Informasi:</strong> Halaman ini berisi daftar username dan password untuk semua user (Admin, Guru, dan Siswa). Masukkan password akses untuk melanjutkan.</p>
        </div>

        <!-- Error Message -->
        @if($errors->has('password'))
            <div class="error-message">
                <p><strong>❌ Error:</strong> {{ $errors->first('password') }}</p>
            </div>
        @endif

        <!-- Form Section -->
        <div class="form-section">
            <form method="POST" action="{{ route('credentials.verify') }}">
                @csrf
                <div class="form-group">
                    <label for="password">🔑 Password Akses</label>
                    <input type="password" id="password" name="password" 
                           placeholder="Masukkan password akses" 
                           required autofocus>
                </div>
                
                <button type="submit">
                    🚀 Masuk
                </button>
            </form>
        </div>

        <!-- Warning -->
        <div class="warning">
            <p><strong>⚠️ PERINGATAN:</strong> Halaman ini berisi informasi rahasia. Jangan bagikan password ke pihak yang tidak berwenang.</p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>© {{ date('Y') }} SMK PGRI Blora</strong></p>
            <p>Password bisa didapatkan dari Admin/Kepala Sekolah</p>
        </div>
    </div>
</body>
</html>
