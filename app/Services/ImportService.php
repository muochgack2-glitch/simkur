<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\ActivityType;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\ImportLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ImportService
{
    /**
     * Generate Excel template untuk import
     */
    public function generateTemplate(): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set judul
        $sheet->setTitle('Template Import');
        
        // Header columns
        $headers = [
            'Nama Kegiatan',
            'Jenis Kegiatan',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Semester',
            'Keterangan'
        ];
        
        // Set header row
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $column++;
        }
        
        // Style header
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2563EB'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        
        // Add sample data
        $sampleData = [
            ['MPLS 2024', 'MPLS', '2024-07-08', '2024-07-10', 'Ganjil', 'Masa Pengenalan Lingkungan Sekolah'],
            ['Libur Idul Adha', 'Libur Nasional', '2024-06-17', '2024-06-19', 'Ganjil', 'Libur Hari Raya Idul Adha'],
            ['UTS Semester Ganjil', 'UTS', '2024-10-01', '2024-10-07', 'Ganjil', 'Ujian Tengah Semester'],
        ];
        
        $row = 2;
        foreach ($sampleData as $data) {
            $column = 'A';
            foreach ($data as $value) {
                $sheet->setCellValue($column . $row, $value);
                $column++;
            }
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Add instructions sheet
        $instructionSheet = $spreadsheet->createSheet();
        $instructionSheet->setTitle('Petunjuk');
        $instructionSheet->setCellValue('A1', 'PETUNJUK PENGGUNAAN TEMPLATE IMPORT');
        $instructionSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        
        $instructions = [
            '',
            'Format Data:',
            '1. Nama Kegiatan: Teks, maksimal 255 karakter',
            '2. Jenis Kegiatan: Harus sesuai dengan jenis kegiatan yang ada di sistem',
            '   (MPLS, Libur Nasional, UTS, UAS, dll)',
            '3. Tanggal Mulai: Format YYYY-MM-DD (contoh: 2024-07-08)',
            '4. Tanggal Selesai: Format YYYY-MM-DD, harus >= Tanggal Mulai',
            '5. Semester: "Ganjil" atau "Genap"',
            '6. Keterangan: Teks, opsional',
            '',
            'Catatan Penting:',
            '- Jangan mengubah header kolom (baris 1)',
            '- Hapus data contoh sebelum mengisi data asli',
            '- Pastikan tanggal dalam rentang tahun pelajaran aktif',
            '- Jenis Kegiatan harus sudah terdaftar di sistem',
            '- File maksimal 2MB',
            '- Format file: .xlsx atau .xls',
        ];
        
        $row = 1;
        foreach ($instructions as $instruction) {
            $instructionSheet->setCellValue('A' . $row, $instruction);
            $row++;
        }
        
        $instructionSheet->getColumnDimension('A')->setWidth(80);
        
        // Set active sheet back to template
        $spreadsheet->setActiveSheetIndex(0);
        
        // Save to temp file
        $filename = 'template_import_kegiatan_' . date('Ymd_His') . '.xlsx';
        $filepath = storage_path('app/temp/' . $filename);
        
        // Ensure temp directory exists
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($filepath);
        
        return $filepath;
    }
    
    /**
     * Parse dan validasi Excel file
     */
    public function parseAndValidate(string $filepath): array
    {
        $spreadsheet = IOFactory::load($filepath);
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray();
        
        // Remove header row
        $headers = array_shift($data);
        
        // Get reference data
        $activityTypes = ActivityType::pluck('id', 'name')->toArray();
        $academicYear = AcademicYear::active()->with('semesters')->first();
        
        if (!$academicYear) {
            return [
                'success' => false,
                'message' => 'Tidak ada tahun pelajaran aktif',
                'data' => [],
            ];
        }
        
        $semesters = $academicYear->semesters->keyBy('type');
        
        // Debug logging
        \Log::info('Import validation started', [
            'activity_types' => array_keys($activityTypes),
            'academic_year' => $academicYear->name,
            'semesters' => $semesters->pluck('name', 'type')->toArray(),
        ]);
        
        $results = [];
        $rowNumber = 2; // Start from row 2 (after header)
        
        foreach ($data as $row) {
            // Skip empty rows
            if (empty(array_filter($row))) {
                continue;
            }
            
            $rowData = [
                'row' => $rowNumber,
                'name' => $row[0] ?? '',
                'activity_type_name' => $row[1] ?? '',
                'start_date' => $row[2] ?? '',
                'end_date' => $row[3] ?? '',
                'semester_type' => $row[4] ?? '',
                'description' => $row[5] ?? '',
                'status' => 'pending',
                'errors' => [],
            ];
            
            // Validate
            $validator = Validator::make($rowData, [
                'name' => 'required|string|max:255',
                'activity_type_name' => 'required|string',
                'start_date' => 'required|date_format:Y-m-d',
                'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
                'semester_type' => 'required|in:Ganjil,Genap',
            ], [
                'name.required' => 'Nama kegiatan wajib diisi',
                'name.max' => 'Nama kegiatan maksimal 255 karakter',
                'activity_type_name.required' => 'Jenis kegiatan wajib diisi',
                'start_date.required' => 'Tanggal mulai wajib diisi',
                'start_date.date_format' => 'Format tanggal mulai harus YYYY-MM-DD',
                'end_date.required' => 'Tanggal selesai wajib diisi',
                'end_date.date_format' => 'Format tanggal selesai harus YYYY-MM-DD',
                'end_date.after_or_equal' => 'Tanggal selesai harus >= tanggal mulai',
                'semester_type.required' => 'Semester wajib diisi',
                'semester_type.in' => 'Semester harus "Ganjil" atau "Genap"',
            ]);
            
            if ($validator->fails()) {
                $rowData['status'] = 'error';
                $rowData['errors'] = $validator->errors()->all();
            } else {
                // Additional validations
                
                // Check if activity type exists
                if (!isset($activityTypes[$rowData['activity_type_name']])) {
                    $rowData['status'] = 'error';
                    $rowData['errors'][] = 'Jenis kegiatan "' . $rowData['activity_type_name'] . '" tidak ditemukan';
                } else {
                    $rowData['activity_type_id'] = $activityTypes[$rowData['activity_type_name']];
                }
                
                // Check if semester exists
                $semesterType = strtolower($rowData['semester_type']) === 'ganjil' ? 'ganjil' : 'genap';
                if (!isset($semesters[$semesterType])) {
                    $rowData['status'] = 'error';
                    $rowData['errors'][] = 'Semester tidak ditemukan';
                } else {
                    $semester = $semesters[$semesterType];
                    $rowData['semester_id'] = $semester->id;
                    
                    // Check if dates are within semester range
                    $startDate = Carbon::parse($rowData['start_date']);
                    $endDate = Carbon::parse($rowData['end_date']);
                    $semesterStart = Carbon::parse($semester->start_date);
                    $semesterEnd = Carbon::parse($semester->end_date);
                    
                    if ($startDate->lt($semesterStart) || $endDate->gt($semesterEnd)) {
                        $rowData['status'] = 'warning';
                        $rowData['errors'][] = 'Tanggal di luar rentang semester (' . 
                            $semesterStart->format('d M Y') . ' - ' . 
                            $semesterEnd->format('d M Y') . ')';
                    }
                }
                
                // Set status to valid if no errors
                if (empty($rowData['errors'])) {
                    $rowData['status'] = 'valid';
                }
            }
            
            $results[] = $rowData;
            $rowNumber++;
        }
        
        return [
            'success' => true,
            'data' => $results,
            'summary' => [
                'total' => count($results),
                'valid' => count(array_filter($results, fn($r) => $r['status'] === 'valid')),
                'warning' => count(array_filter($results, fn($r) => $r['status'] === 'warning')),
                'error' => count(array_filter($results, fn($r) => $r['status'] === 'error')),
            ],
        ];
    }
    
    /**
     * Process import (insert ke database)
     */
    public function processImport(array $validatedData, int $userId): array
    {
        $imported = 0;
        $failed = 0;
        $errors = [];
        
        DB::beginTransaction();
        
        try {
            $activeAcademicYear = AcademicYear::active()->first();
            
            if (!$activeAcademicYear) {
                return [
                    'success' => false,
                    'message' => 'Tidak ada tahun pelajaran aktif',
                    'imported' => 0,
                    'failed' => count($validatedData),
                ];
            }
            
            foreach ($validatedData as $row) {
                if ($row['status'] !== 'valid' && $row['status'] !== 'warning') {
                    $failed++;
                    $errors[] = [
                        'row' => $row['row'],
                        'errors' => $row['errors'],
                    ];
                    continue;
                }
                
                try {
                    Activity::create([
                        'academic_year_id' => $activeAcademicYear->id,
                        'semester_id' => $row['semester_id'],
                        'activity_type_id' => $row['activity_type_id'],
                        'name' => $row['name'],
                        'start_date' => $row['start_date'],
                        'end_date' => $row['end_date'],
                        'description' => $row['description'] ?? null,
                        'target_grades' => null, // Default: semua kelas
                        'is_active' => true,
                        'created_by' => $userId,
                    ]);
                    
                    $imported++;
                } catch (\Exception $e) {
                    $failed++;
                    $errors[] = [
                        'row' => $row['row'],
                        'errors' => [$e->getMessage()],
                    ];
                    
                    \Log::error('Import error for row ' . $row['row'], [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                        'data' => $row,
                    ]);
                }
            }
            
            // Log import
            ImportLog::create([
                'user_id' => $userId,
                'filename' => 'import_activities_' . date('YmdHis') . '.xlsx',
                'total_rows' => count($validatedData),
                'success_rows' => $imported,
                'failed_rows' => $failed,
                'status' => $failed === 0 ? 'success' : ($imported > 0 ? 'partial' : 'failed'),
                'error_log' => $failed > 0 ? json_encode($errors) : null,
            ]);
            
            DB::commit();
            
            return [
                'success' => true,
                'imported' => $imported,
                'failed' => $failed,
                'errors' => $errors,
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Import process failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'imported' => 0,
                'failed' => count($validatedData),
            ];
        }
    }
    
    /**
     * Generate error log Excel
     */
    public function generateErrorLog(array $errors): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Error Log');
        
        // Header
        $sheet->setCellValue('A1', 'Baris');
        $sheet->setCellValue('B1', 'Error');
        
        // Style header
        $sheet->getStyle('A1:B1')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'EF4444'],
            ],
        ]);
        
        // Data
        $row = 2;
        foreach ($errors as $error) {
            $sheet->setCellValue('A' . $row, $error['row']);
            $sheet->setCellValue('B' . $row, implode('; ', $error['errors']));
            $row++;
        }
        
        // Auto-size
        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        
        // Save
        $filename = 'error_log_' . date('Ymd_His') . '.xlsx';
        $filepath = storage_path('app/temp/' . $filename);
        
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($filepath);
        
        return $filepath;
    }
}
