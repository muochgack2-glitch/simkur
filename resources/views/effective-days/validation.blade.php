<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validasi Perhitungan Hari Efektif - {{ $academicYear->year }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            
            body {
                font-size: 9px;
            }
            
            table {
                page-break-inside: avoid;
            }
        }
        
        table {
            border-collapse: collapse;
            width: 100%;
            font-size: 0.75rem;
        }
        
        th, td {
            border: 1px solid #333;
            padding: 4px 6px;
            text-align: center;
        }
        
        th {
            background: #2563eb;
            color: white;
            font-weight: 600;
        }
        
        .semester-header {
            background: #60a5fa;
            font-weight: 700;
        }
        
        .total-row {
            background: #dbeafe;
            font-weight: 700;
        }
        
        .yearly-total {
            background: #fbbf24;
            font-weight: 700;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <div class="no-print bg-white shadow-sm border-b border-gray-200 mb-6">
        <div class="max-w-full mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Validasi Perhitungan Hari Efektif</h1>
                    <p class="text-gray-600 mt-1">Tahun Ajaran {{ $academicYear->year }}</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                        ← Kembali
                    </a>
                    <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Print
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="max-w-full mx-auto px-6 pb-8">
        <!-- Comparison Box -->
        <div class="mb-6 bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">📊 Perbandingan Sistem vs Excel Referensi</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <!-- Semester 1 -->
                <div class="border rounded-lg p-4 {{ $comparison['ganjil']['match'] ?? false ? 'bg-green-50 border-green-300' : 'bg-red-50 border-red-300' }}">
                    <h4 class="font-bold text-gray-700 mb-2">Semester Ganjil</h4>
                    <div class="space-y-1 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Target Excel:</span>
                            <span class="font-semibold">{{ $expectedValues['ganjil']['hari_belajar_efektif'] }} hari</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Sistem Hitung:</span>
                            <span class="font-semibold">{{ $actualValues['ganjil']['hari_belajar_efektif'] ?? 0 }} hari</span>
                        </div>
                        <div class="flex justify-between border-t pt-1">
                            <span class="text-gray-600">Selisih:</span>
                            <span class="font-bold {{ ($comparison['ganjil']['difference'] ?? 0) === 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ ($comparison['ganjil']['difference'] ?? 0) > 0 ? '+' : '' }}{{ $comparison['ganjil']['difference'] ?? 0 }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Semester 2 -->
                <div class="border rounded-lg p-4 {{ $comparison['genap']['match'] ?? false ? 'bg-green-50 border-green-300' : 'bg-red-50 border-red-300' }}">
                    <h4 class="font-bold text-gray-700 mb-2">Semester Genap</h4>
                    <div class="space-y-1 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Target Excel:</span>
                            <span class="font-semibold">{{ $expectedValues['genap']['hari_belajar_efektif'] }} hari</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Sistem Hitung:</span>
                            <span class="font-semibold">{{ $actualValues['genap']['hari_belajar_efektif'] ?? 0 }} hari</span>
                        </div>
                        <div class="flex justify-between border-t pt-1">
                            <span class="text-gray-600">Selisih:</span>
                            <span class="font-bold {{ ($comparison['genap']['difference'] ?? 0) === 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ ($comparison['genap']['difference'] ?? 0) > 0 ? '+' : '' }}{{ $comparison['genap']['difference'] ?? 0 }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Total 1 Tahun -->
                <div class="border rounded-lg p-4 {{ $comparison['yearly']['match'] ?? false ? 'bg-green-50 border-green-300' : 'bg-red-50 border-red-300' }}">
                    <h4 class="font-bold text-gray-700 mb-2">Total 1 Tahun</h4>
                    <div class="space-y-1 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Target Excel:</span>
                            <span class="font-semibold">{{ $expectedValues['yearly']['hari_belajar_efektif'] }} hari</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Sistem Hitung:</span>
                            <span class="font-semibold">{{ $comparison['yearly']['actual'] ?? 0 }} hari</span>
                        </div>
                        <div class="flex justify-between border-t pt-1">
                            <span class="text-gray-600">Selisih:</span>
                            <span class="font-bold {{ ($comparison['yearly']['difference'] ?? 0) === 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ ($comparison['yearly']['difference'] ?? 0) > 0 ? '+' : '' }}{{ $comparison['yearly']['difference'] ?? 0 }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Status Message -->
            @if($comparison['yearly']['match'] ?? false)
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    <span class="font-bold">✅ Perhitungan SESUAI!</span> Sistem sudah menghitung dengan benar sesuai Excel referensi.
                </div>
            @else
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-3 rounded">
                    <span class="font-bold">⚠️ Perhitungan BELUM SESUAI!</span>
                    <span class="block mt-1 text-sm">
                        Kemungkinan penyebab: Data kegiatan libur belum lengkap atau tanggal semester belum tepat. 
                        Periksa data kegiatan dengan <code class="bg-yellow-200 px-1">is_holiday = true</code>.
                    </span>
                </div>
            @endif
            
            <!-- Detailed Breakdown -->
            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                @foreach(['ganjil', 'genap'] as $type)
                    @if(isset($actualValues[$type]))
                    <div class="border rounded p-3 bg-gray-50">
                        <h5 class="font-semibold mb-2 text-gray-700">Detail Semester {{ ucfirst($type) }}</h5>
                        <table class="w-full text-xs">
                            <tr>
                                <td class="py-1">Total Hari</td>
                                <td class="text-right font-mono">{{ $actualValues[$type]['total_days'] }}</td>
                            </tr>
                            <tr>
                                <td class="py-1">Weekend (Sabtu+Minggu)</td>
                                <td class="text-right font-mono">{{ $actualValues[$type]['weekend_days'] }}</td>
                            </tr>
                            <tr>
                                <td class="py-1">Hari Libur (Weekday)</td>
                                <td class="text-right font-mono">{{ $actualValues[$type]['holiday_days'] }}</td>
                            </tr>
                            <tr>
                                <td class="py-1">Hari Ujian (tracked)</td>
                                <td class="text-right font-mono">{{ $actualValues[$type]['exam_days'] }}</td>
                            </tr>
                            <tr class="border-t font-semibold">
                                <td class="py-1">Hari Efektif</td>
                                <td class="text-right font-mono">{{ $actualValues[$type]['hari_belajar_efektif'] }}</td>
                            </tr>
                            <tr class="text-gray-500 text-xs">
                                <td colspan="2" class="py-1">
                                    Update: {{ \Carbon\Carbon::parse($actualValues[$type]['calculated_at'])->format('d/m/Y H:i') }}
                                </td>
                            </tr>
                        </table>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
        
        <!-- Title -->
        <div class="text-center mb-6">
            <h2 class="text-xl font-bold uppercase">PERHITUNGAN HARI EFEKTIF BELAJAR</h2>
            <p class="text-sm font-semibold">TAHUN AJARAN {{ $academicYear->year }} UNTUK SMA/SMK/SMALB/MA/MAK</p>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table>
                    <thead>
                        <tr>
                            <th rowspan="3" style="width: 40px;">NO</th>
                            <th rowspan="3" style="width: 100px;">SEMESTER</th>
                            <th rowspan="3" style="width: 120px;">BULAN, TAHUN</th>
                            <th colspan="7">JUMLAH HARI EFEKTIF DAN HARI UNTUK KEGIATAN LAIN</th>
                            <th colspan="7">JUMLAH HARI LIBUR</th>
                        </tr>
                        <tr>
                            <th rowspan="2" style="width: 60px;">HARI<br>BELAJAR<br>EFEKTIF</th>
                            <th rowspan="2" style="width: 60px;">HARI-<br>HARI<br>PERTAMA<br>MASUK</th>
                            <th rowspan="2" style="width: 60px;">TKA (TES<br>KEMAMPU<br>AN<br>AKADEMIK)</th>
                            <th rowspan="2" style="width: 60px;">MENGI-<br>KUTI<br>UPACARA</th>
                            <th rowspan="2" style="width: 80px;">PENYERAH<br>AN RAPOR<br>DAN<br>PENGUMU<br>MAN<br>KELULUISA<br>N</th>
                            <th rowspan="2" style="width: 60px;">JUMLA<br>HARI<br>EFEKTIF</th>
                            <th rowspan="2" style="width: 60px;">LIBUR<br>AKHIR<br>SEMESTER</th>
                            <th rowspan="2" style="width: 60px;">HARI<br>MINGGU</th>
                            <th rowspan="2" style="width: 60px;">HARI<br>SABTU</th>
                            <th rowspan="2" style="width: 60px;">LIBUR<br>UMUM</th>
                            <th rowspan="2" style="width: 80px;">LIBUR<br>RAMADHAN<br>DAN<br>SEBELUM/SE<br>SUDAH HARI<br>RAYA IDUL<br>FITRI</th>
                            <th rowspan="2" style="width: 60px;">JML HARI<br>LIBUR</th>
                            <th rowspan="2" style="width: 60px;">JUMLAH<br>HARI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($semesters as $index => $semesterData)
                            @foreach($semesterData['months'] as $monthIndex => $month)
                                <tr>
                                    @if($monthIndex === 0)
                                        <td rowspan="{{ count($semesterData['months']) + 1 }}">{{ $index + 1 }}</td>
                                        <td rowspan="{{ count($semesterData['months']) + 1 }}" class="semester-header">
                                            {{ strtoupper($semesterData['semester']->type) }}
                                        </td>
                                    @endif
                                    <td style="text-align: left; padding-left: 10px;">
                                        <strong>{{ strtoupper($month['bulan']) }}</strong>
                                        <span style="margin-left: 20px;">{{ $month['tahun'] }}</span>
                                    </td>
                                    <td>{{ $month['hari_belajar_efektif'] }}</td>
                                    <td>{{ $month['hari_pertama_masuk'] ?: '-' }}</td>
                                    <td>{{ $month['tka_kemampuan'] ?: '-' }}</td>
                                    <td>{{ $month['mengikuti_upacara'] ?: '-' }}</td>
                                    <td>{{ $month['penyerahan_rapor'] ?: '-' }}</td>
                                    <td>{{ $month['jumla_hari_efektif'] }}</td>
                                    <td>{{ $month['libur_akhir_semester'] ?: '-' }}</td>
                                    <td>{{ $month['hari_minggu'] }}</td>
                                    <td>{{ $month['hari_sabtu'] }}</td>
                                    <td>{{ $month['libur_umum'] ?: '-' }}</td>
                                    <td>{{ $month['libur_ramadhan'] ?: '-' }}</td>
                                    <td>{{ $month['jumlah_hari_libur'] }}</td>
                                    <td>{{ $month['jumlah_hari'] }}</td>
                                </tr>
                            @endforeach
                            
                            <!-- Semester Total Row -->
                            <tr class="total-row">
                                <td colspan="1" style="text-align: left; padding-left: 10px;"><strong>JUMLAH</strong></td>
                                <td>{{ $semesterData['total']['hari_belajar_efektif'] }}</td>
                                <td>{{ $semesterData['total']['hari_pertama_masuk'] ?: '-' }}</td>
                                <td>{{ $semesterData['total']['tka_kemampuan'] ?: '-' }}</td>
                                <td>{{ $semesterData['total']['mengikuti_upacara'] ?: '-' }}</td>
                                <td>{{ $semesterData['total']['penyerahan_rapor'] ?: '-' }}</td>
                                <td>{{ $semesterData['total']['jumla_hari_efektif'] }}</td>
                                <td>{{ $semesterData['total']['libur_akhir_semester'] ?: '-' }}</td>
                                <td>{{ $semesterData['total']['hari_minggu'] }}</td>
                                <td>{{ $semesterData['total']['hari_sabtu'] }}</td>
                                <td>{{ $semesterData['total']['libur_umum'] ?: '-' }}</td>
                                <td>{{ $semesterData['total']['libur_ramadhan'] ?: '-' }}</td>
                                <td>{{ $semesterData['total']['jumlah_hari_libur'] }}</td>
                                <td>{{ $semesterData['total']['jumlah_hari'] }}</td>
                            </tr>
                        @endforeach
                        
                        <!-- Yearly Total Row -->
                        <tr class="yearly-total">
                            <td colspan="3" style="text-align: left; padding-left: 10px;"><strong>JUMLAH DALAM 1 TAHUN</strong></td>
                            <td>{{ $yearlyTotal['hari_belajar_efektif'] }}</td>
                            <td>{{ $yearlyTotal['hari_pertama_masuk'] ?: '-' }}</td>
                            <td>{{ $yearlyTotal['tka_kemampuan'] ?: '-' }}</td>
                            <td>{{ $yearlyTotal['mengikuti_upacara'] ?: '-' }}</td>
                            <td>{{ $yearlyTotal['penyerahan_rapor'] ?: '-' }}</td>
                            <td>{{ $yearlyTotal['jumla_hari_efektif'] }}</td>
                            <td>{{ $yearlyTotal['libur_akhir_semester'] ?: '-' }}</td>
                            <td>{{ $yearlyTotal['hari_minggu'] }}</td>
                            <td>{{ $yearlyTotal['hari_sabtu'] }}</td>
                            <td>{{ $yearlyTotal['libur_umum'] ?: '-' }}</td>
                            <td>{{ $yearlyTotal['libur_ramadhan'] ?: '-' }}</td>
                            <td>{{ $yearlyTotal['jumlah_hari_libur'] }}</td>
                            <td>{{ $yearlyTotal['jumlah_hari'] }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Info -->
        <div class="mt-6 space-y-4">
            <!-- How to Use -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm">
                <p class="font-semibold text-blue-900 mb-2">📊 Cara Menggunakan Halaman Ini:</p>
                <ul class="text-blue-800 space-y-1 ml-4">
                    <li>• <strong>Lihat Box Perbandingan</strong> di atas untuk cek apakah sistem sudah sesuai dengan Excel</li>
                    <li>• <strong>Warna Hijau</strong> = Sudah sesuai ✅ | <strong>Warna Merah</strong> = Belum sesuai ⚠️</li>
                    <li>• <strong>Tabel Detail</strong> di bawah untuk cross-check per bulan (mirip format Excel)</li>
                    <li>• Jika belum sesuai, lakukan langkah perbaikan di bawah</li>
                </ul>
            </div>
            
            <!-- Troubleshooting -->
            @if(!($comparison['yearly']['match'] ?? false))
            <div class="bg-orange-50 border border-orange-300 rounded-lg p-4 text-sm">
                <p class="font-semibold text-orange-900 mb-3">🔧 Langkah Perbaikan Jika Belum Sesuai:</p>
                
                <div class="space-y-3">
                    <!-- Step 1 -->
                    <div class="bg-white rounded p-3 border border-orange-200">
                        <p class="font-semibold text-orange-900 mb-1">1️⃣ Periksa Tanggal Semester</p>
                        <p class="text-orange-800 text-xs mb-2">Pastikan tanggal semester sesuai dengan hari pertama masuk sekolah:</p>
                        <ul class="text-orange-700 text-xs ml-4 space-y-1">
                            <li>• Semester Ganjil: <code class="bg-orange-100 px-1">13 Juli 2026 - 20 Desember 2026</code></li>
                            <li>• Semester Genap: <code class="bg-orange-100 px-1">5 Januari 2027 - 20 Juni 2027</code></li>
                        </ul>
                    </div>
                    
                    <!-- Step 2 -->
                    <div class="bg-white rounded p-3 border border-orange-200">
                        <p class="font-semibold text-orange-900 mb-1">2️⃣ Tambahkan Data Libur Nasional</p>
                        <p class="text-orange-800 text-xs mb-2">Masukkan semua hari libur nasional/umum di menu <strong>Kegiatan</strong> dengan <strong>Jenis = Libur</strong>:</p>
                        
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div>
                                <p class="font-semibold text-orange-800">Semester Ganjil (butuh {{ $expectedValues['ganjil']['holiday_days'] }} hari):</p>
                                <ul class="text-orange-700 ml-4 mt-1">
                                    <li>• Agustus 2026: 2 hari libur</li>
                                    <li>• Desember 2026: 2 hari libur</li>
                                </ul>
                                <p class="text-orange-600 mt-1">Saat ini: <strong>{{ $actualValues['ganjil']['holiday_days'] ?? 0 }} hari</strong></p>
                            </div>
                            <div>
                                <p class="font-semibold text-orange-800">Semester Genap (butuh {{ $expectedValues['genap']['holiday_days'] }} hari):</p>
                                <ul class="text-orange-700 ml-4 mt-1">
                                    <li>• Januari: 2 hari</li>
                                    <li>• Februari: 1-2 hari</li>
                                    <li>• Maret: 7 hari</li>
                                    <li>• Mei: 4 hari</li>
                                    <li>• Juni: 2 hari</li>
                                </ul>
                                <p class="text-orange-600 mt-1">Saat ini: <strong>{{ $actualValues['genap']['holiday_days'] ?? 0 }} hari</strong></p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Step 3 -->
                    <div class="bg-white rounded p-3 border border-orange-200">
                        <p class="font-semibold text-orange-900 mb-1">3️⃣ Jalankan Recalculate</p>
                        <p class="text-orange-800 text-xs mb-2">Setelah data diperbaiki, hitung ulang dengan command:</p>
                        <code class="block bg-gray-800 text-green-400 px-3 py-2 rounded text-xs">php artisan ekaldik:calculate-days</code>
                        <p class="text-orange-700 text-xs mt-1">Atau melalui: Dashboard → Menu Hari Efektif → Tombol "Hitung Ulang"</p>
                    </div>
                    
                    <!-- Step 4 -->
                    <div class="bg-white rounded p-3 border border-orange-200">
                        <p class="font-semibold text-orange-900 mb-1">4️⃣ Refresh Halaman Ini</p>
                        <p class="text-orange-800 text-xs">Setelah recalculate selesai, refresh halaman ini untuk lihat hasil terbaru.</p>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Formula Info -->
            <div class="bg-gray-50 border border-gray-300 rounded-lg p-4 text-sm">
                <p class="font-semibold text-gray-900 mb-2">📐 Formula Perhitungan:</p>
                <div class="bg-white rounded p-3 border">
                    <code class="text-gray-800">
                        <strong>Hari Efektif</strong> = Total Hari - Weekend (Sabtu+Minggu) - Hari Libur - Hari Ujian
                    </code>
                    <p class="text-gray-600 text-xs mt-2">
                        ℹ️ <strong>Catatan:</strong> Baik Hari Libur maupun Hari Ujian akan dikurangkan dari perhitungan hari efektif (hanya dihitung hari weekday).
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
