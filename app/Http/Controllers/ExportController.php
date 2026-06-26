<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Services\ExportExcelService;
use App\Services\ExportPdfService;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function yearly(Request $request)
    {
        try {
            // Increase timeout and memory limit for PDF generation
            set_time_limit(300); // 5 minutes
            ini_set('memory_limit', '512M');
            
            $academicYearId = $request->get('year');
            
            $exportService = new ExportPdfService();
            $pdfContent = $exportService->exportYearly($academicYearId);
            
            // Log activity
            ActivityLog::createLog(
                'export',
                'calendar_yearly',
                null,
                'Export kalender tahunan ke PDF'
            );
            
            $filename = 'kalender_tahunan_' . now()->format('Y_m_d_His') . '.pdf';
            
            return response()->streamDownload(function() use ($pdfContent) {
                echo $pdfContent;
            }, $filename, [
                'Content-Type' => 'application/pdf',
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Export Yearly Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'error' => 'Gagal export: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
    
    public function monthly(Request $request)
    {
        try {
            $year = $request->get('year');
            $month = $request->get('month');
            
            $exportService = new ExportPdfService();
            $pdfContent = $exportService->exportMonthly($year, $month);
            
            // Log activity
            ActivityLog::createLog(
                'export',
                'calendar_monthly',
                null,
                'Export kalender bulanan ke PDF'
            );
            
            $filename = 'kalender_bulanan_' . $year . '_' . str_pad($month, 2, '0', STR_PAD_LEFT) . '_' . now()->format('His') . '.pdf';
            
            return response()->streamDownload(function() use ($pdfContent) {
                echo $pdfContent;
            }, $filename, [
                'Content-Type' => 'application/pdf',
            ]);
            
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal export: ' . $e->getMessage());
        }
    }
    
    public function list(Request $request)
    {
        try {
            $filters = $request->only(['academic_year_id', 'semester_id', 'activity_type_id']);
            
            $exportService = new ExportPdfService();
            $pdfContent = $exportService->exportActivityList($filters);
            
            // Log activity
            ActivityLog::createLog(
                'export',
                'activity_list',
                null,
                'Export daftar kegiatan ke PDF'
            );
            
            $filename = 'daftar_kegiatan_' . now()->format('Y_m_d_His') . '.pdf';
            
            return response()->streamDownload(function() use ($pdfContent) {
                echo $pdfContent;
            }, $filename, [
                'Content-Type' => 'application/pdf',
            ]);
            
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal export: ' . $e->getMessage());
        }
    }
    
    public function excel(Request $request)
    {
        try {
            $filters = $request->only(['academic_year_id', 'semester_id', 'activity_type_id']);
            
            $exportService = new ExportExcelService();
            $filepath = $exportService->exportActivities($filters);
            
            // Log activity
            ActivityLog::createLog(
                'export',
                'activity_excel',
                null,
                'Export kegiatan ke Excel'
            );
            
            return response()->download($filepath, basename($filepath))->deleteFileAfterSend();
            
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal export: ' . $e->getMessage());
        }
    }
}
