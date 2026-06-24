<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Setting;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportExcelService
{
    /**
     * Export activities to Excel with filters
     */
    public function exportActivities(array $filters = []): string
    {
        $query = Activity::with(['activityType', 'semester', 'academicYear']);
        
        // Apply filters
        if (!empty($filters['academic_year_id'])) {
            $query->where('academic_year_id', $filters['academic_year_id']);
        } else {
            $academicYear = AcademicYear::active()->first();
            if ($academicYear) {
                $query->where('academic_year_id', $academicYear->id);
            }
        }
        
        if (!empty($filters['semester_id'])) {
            $query->where('semester_id', $filters['semester_id']);
        }
        
        if (!empty($filters['activity_type_id'])) {
            $query->where('activity_type_id', $filters['activity_type_id']);
        }
        
        $activities = $query->orderBy('start_date')->get();
        
        $spreadsheet = new Spreadsheet();
        
        // Sheet 1: Daftar Kegiatan
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Daftar Kegiatan');
        
        // Header
        $headers = ['No', 'Nama Kegiatan', 'Jenis Kegiatan', 'Tanggal Mulai', 'Tanggal Selesai', 'Semester', 'Keterangan'];
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $column++;
        }
        
        // Style header
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
        ]);
        
        // Data
        $row = 2;
        foreach ($activities as $index => $activity) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $activity->name);
            $sheet->setCellValue('C' . $row, $activity->activityType->name ?? '-');
            $sheet->setCellValue('D' . $row, Carbon::parse($activity->start_date)->format('Y-m-d'));
            $sheet->setCellValue('E' . $row, Carbon::parse($activity->end_date)->format('Y-m-d'));
            $sheet->setCellValue('F' . $row, $activity->semester->name ?? '-');
            $sheet->setCellValue('G' . $row, $activity->description ?? '-');
            
            // Alternate row colors
            if ($row % 2 === 0) {
                $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F9FAFB']],
                ]);
            }
            
            $row++;
        }
        
        // Borders for data
        $sheet->getStyle('A1:G' . ($row - 1))->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]],
        ]);
        
        // Auto-size columns
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Sheet 2: Hari Efektif (if applicable)
        $this->addEffectiveDaysSheet($spreadsheet, $filters);
        
        // Save
        $filename = 'kegiatan_' . date('Ymd_His') . '.xlsx';
        $filepath = storage_path('app/temp/' . $filename);
        
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        
        $writer = new Xlsx($spreadsheet);
        $writer->save($filepath);
        
        return $filepath;
    }
    
    /**
     * Add Effective Days sheet
     */
    private function addEffectiveDaysSheet(Spreadsheet $spreadsheet, array $filters): void
    {
        $academicYear = null;
        
        if (!empty($filters['academic_year_id'])) {
            $academicYear = AcademicYear::find($filters['academic_year_id']);
        } else {
            $academicYear = AcademicYear::active()->first();
        }
        
        if (!$academicYear) {
            return;
        }
        
        $effectiveDayService = new EffectiveDayService();
        $data = $effectiveDayService->calculateForAcademicYear($academicYear);
        
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Hari Efektif');
        
        // Title
        $sheet->setCellValue('A1', 'Perhitungan Hari Efektif');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        
        $sheet->setCellValue('A2', 'Tahun Pelajaran: ' . $academicYear->year);
        $sheet->mergeCells('A2:F2');
        
        // Headers
        $headers = ['Semester', 'Total Hari', 'Hari Belajar', 'Hari Libur', 'Hari Ujian', 'Minggu Efektif'];
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '4', $header);
            $column++;
        }
        
        // Style header
        $sheet->getStyle('A4:F4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        
        // Data
        $row = 5;
        foreach ($data as $semesterData) {
            $sheet->setCellValue('A' . $row, $semesterData['semester_name']);
            $sheet->setCellValue('B' . $row, $semesterData['total_days']);
            $sheet->setCellValue('C' . $row, $semesterData['study_days']);
            $sheet->setCellValue('D' . $row, $semesterData['holiday_days']);
            $sheet->setCellValue('E' . $row, $semesterData['exam_days']);
            $sheet->setCellValue('F' . $row, number_format($semesterData['effective_weeks'], 1));
            
            $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F9FAFB']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]],
            ]);
            
            $row++;
        }
        
        // Auto-size
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }
}
