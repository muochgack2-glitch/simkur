<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - SIM Kurikulum SMK PGRI Blora</title>
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
            padding: 20px;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        
        .header p {
            font-size: 1rem;
            opacity: 0.9;
        }
        
        .badges {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 15px;
            flex-wrap: wrap;
        }
        
        .badge {
            background: rgba(255,255,255,0.2);
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .tabs {
            display: flex;
            background: #f3f4f6;
            border-bottom: 2px solid #e5e7eb;
            overflow-x: auto;
        }
        
        .tab {
            padding: 15px 25px;
            cursor: pointer;
            border: none;
            background: transparent;
            font-size: 1rem;
            font-weight: 600;
            color: #6b7280;
            transition: all 0.3s;
            white-space: nowrap;
        }
        
        .tab:hover {
            background: #e5e7eb;
            color: #374151;
        }
        
        .tab.active {
            background: white;
            color: #2563eb;
            border-bottom: 3px solid #2563eb;
        }
        
        .tab-content {
            display: none;
            padding: 30px;
            animation: fadeIn 0.3s;
        }
        
        .tab-content.active {
            display: block;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .info-box {
            background: #eff6ff;
            border-left: 4px solid #2563eb;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .info-box h2 {
            color: #1e40af;
            margin-bottom: 10px;
        }
        
        .info-box p {
            color: #1e3a8a;
            line-height: 1.6;
            margin-bottom: 8px;
        }
        
        .class-section {
            margin-bottom: 30px;
        }
        
        .class-title {
            background: #f9fafb;
            padding: 15px;
            border-left: 4px solid #3b82f6;
            font-size: 1.2rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 15px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        thead {
            background: #1f2937;
            color: white;
        }
        
        th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        tbody tr:hover {
            background: #f9fafb;
        }
        
        .password {
            font-family: 'Courier New', monospace;
            background: #fef3c7;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: 600;
            color: #92400e;
        }
        
        .username {
            font-family: 'Courier New', monospace;
            background: #dbeafe;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: 600;
            color: #1e40af;
        }
        
        .role-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .role-admin { background: #fee2e2; color: #991b1b; }
        .role-kepsek { background: #ddd6fe; color: #5b21b6; }
        .role-waka { background: #fef3c7; color: #92400e; }
        .role-guru { background: #d1fae5; color: #065f46; }
        
        .search-box {
            margin-bottom: 20px;
            padding: 15px;
            background: #f9fafb;
            border-radius: 8px;
        }
        
        .search-box input {
            width: 100%;
            padding: 10px 15px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        .search-box input:focus {
            outline: none;
            border-color: #2563eb;
        }
        
        .footer {
            background: #f9fafb;
            padding: 20px;
            text-align: center;
            color: #6b7280;
            font-size: 0.9rem;
        }
        
        .btn-login {
            display: inline-block;
            background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);
            color: white;
            padding: 12px 30px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: transform 0.3s;
            margin-top: 10px;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
        }
        
        @media print {
            body { background: white; padding: 0; }
            .tabs, .search-box, .btn-login { display: none; }
            .tab-content { display: block !important; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🔐 Daftar Akun SIM Kurikulum</h1>
            <p>SMK PGRI Blora - Tahun Pelajaran 2026/2027</p>
            <div class="badges">
                <span class="badge">MPLB</span>
                <span class="badge">AKL</span>
                <span class="badge">BUSANA</span>
            </div>
        </div>

        <!-- Tabs -->
        <div class="tabs">
            <button class="tab active" onclick="openTab(event, 'info')">📋 Info Umum</button>
            <button class="tab" onclick="openTab(event, 'siswa')">👨‍🎓 Akun Siswa ({{ $students->sum(fn($s) => $s->count()) }})</button>
            <button class="tab" onclick="openTab(event, 'guru')">👨‍🏫 Akun Guru ({{ $teachers->count() }})</button>
            <button class="tab" onclick="openTab(event, 'staff')">👔 Admin & Staff ({{ $staff->count() }})</button>
        </div>

        <!-- Tab Content: Info Umum -->
        <div id="info" class="tab-content active">
            <div class="info-box">
                <h2>📌 Informasi Penting</h2>
                <p><strong>URL Sistem:</strong> <a href="{{ route('login') }}" target="_blank">{{ url('/') }}</a></p>
                <p><strong>Password Default:</strong> <span class="password">password</span></p>
                <p><strong>Catatan:</strong> Semua user wajib mengganti password setelah login pertama kali melalui menu "Ganti Password".</p>
                <a href="{{ route('login') }}" class="btn-login">🚀 Masuk ke Sistem</a>
            </div>

            <div class="info-box">
                <h2>📊 Statistik Akun</h2>
                <p>✅ Total Siswa: <strong>{{ $students->sum(fn($s) => $s->count()) }} siswa</strong> ({{ $students->count() }} kelas)</p>
                <p>✅ Total Guru: <strong>{{ $teachers->count() }} guru</strong></p>
                <p>✅ Admin & Staff: <strong>{{ $staff->count() }} akun</strong></p>
            </div>

            <div class="info-box">
                <h2>🎯 Cara Login</h2>
                <p><strong>1.</strong> Buka halaman login sistem</p>
                <p><strong>2.</strong> Masukkan <strong>Username</strong> sesuai daftar (tanpa spasi, huruf kecil)</p>
                <p><strong>3.</strong> Masukkan password: <span class="password">password</span></p>
                <p><strong>4.</strong> Klik tombol <strong>Masuk</strong></p>
                <p><strong>5.</strong> Segera ganti password di menu <strong>Ganti Password</strong></p>
            </div>
        </div>

        <!-- Tab Content: Siswa -->
        <div id="siswa" class="tab-content">
            <div class="search-box">
                <input type="text" id="searchSiswa" onkeyup="searchTable('siswa')" 
                       placeholder="🔍 Cari nama siswa, NIS, atau kelas...">
            </div>

            @foreach($students as $className => $classStudents)
            <div class="class-section">
                <div class="class-title">
                    Kelas {{ $className }} ({{ $classStudents->count() }} siswa)
                    @if($classStudents->first()->class && $classStudents->first()->class->homeroomTeacher)
                        - Wali: {{ $classStudents->first()->class->homeroomTeacher->name }}
                    @endif
                </div>
                <table class="table-siswa">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Lengkap</th>
                            <th>NIS</th>
                            <th>Username</th>
                            <th>Password</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($classStudents as $index => $student)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $student->name }}</td>
                            <td>{{ $student->nisn ?: '-' }}</td>
                            <td><span class="username">{{ $student->username }}</span></td>
                            <td><span class="password">password</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endforeach
        </div>

        <!-- Tab Content: Guru -->
        <div id="guru" class="tab-content">
            <div class="search-box">
                <input type="text" id="searchGuru" onkeyup="searchTable('guru')" 
                       placeholder="🔍 Cari nama guru atau username...">
            </div>
            
            <div class="class-section">
                <div class="class-title">Daftar Akun Guru ({{ $teachers->count() }} guru)</div>
                <table class="table-guru">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Lengkap</th>
                            <th>NIP</th>
                            <th>Username</th>
                            <th>Password</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($teachers as $index => $teacher)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $teacher->name }}</td>
                            <td>{{ $teacher->nip ?: '-' }}</td>
                            <td><span class="username">{{ $teacher->username }}</span></td>
                            <td><span class="password">password</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab Content: Staff -->
        <div id="staff" class="tab-content">
            <div class="search-box">
                <input type="text" id="searchStaff" onkeyup="searchTable('staff')" 
                       placeholder="🔍 Cari nama atau role...">
            </div>
            
            <div class="class-section">
                <div class="class-title">Daftar Akun Admin & Staff ({{ $staff->count() }} akun)</div>
                <table class="table-staff">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Lengkap</th>
                            <th>Role</th>
                            <th>Username</th>
                            <th>Password</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($staff as $index => $user)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $user->name }}</td>
                            <td>
                                <span class="role-badge role-{{ $user->role }}">
                                    {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                </span>
                            </td>
                            <td><span class="username">{{ $user->username }}</span></td>
                            <td><span class="password">password</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>© {{ date('Y') }} SMK PGRI Blora</strong></p>
            <p>Sistem Informasi Manajemen Kurikulum v2.0</p>
            <p style="margin-top: 10px; font-size: 0.85rem;">
                <strong>⚠️ PENTING:</strong> Dokumen ini berisi informasi kredensial. Harap dijaga kerahasiaannya.
            </p>
        </div>
    </div>

    <script>
        // Tab switching
        function openTab(evt, tabName) {
            const tabContents = document.getElementsByClassName('tab-content');
            for (let i = 0; i < tabContents.length; i++) {
                tabContents[i].classList.remove('active');
            }
            
            const tabs = document.getElementsByClassName('tab');
            for (let i = 0; i < tabs.length; i++) {
                tabs[i].classList.remove('active');
            }
            
            document.getElementById(tabName).classList.add('active');
            evt.currentTarget.classList.add('active');
        }
        
        // Search functionality
        function searchTable(type) {
            const input = document.getElementById('search' + type.charAt(0).toUpperCase() + type.slice(1));
            const filter = input.value.toUpperCase();
            const tables = document.getElementsByClassName('table-' + type);
            
            for (let table of tables) {
                const tr = table.getElementsByTagName('tr');
                
                for (let i = 1; i < tr.length; i++) {
                    let txtValue = tr[i].textContent || tr[i].innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = '';
                    } else {
                        tr[i].style.display = 'none';
                    }
                }
            }
        }
    </script>
</body>
</html>
