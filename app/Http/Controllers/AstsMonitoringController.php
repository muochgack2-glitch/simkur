<?php

namespace App\Http\Controllers;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AstsScheduleTemplateExport;

class AstsMonitoringController extends Controller
{
    public function downloadTemplate()
    {
        return Excel::download(new AstsScheduleTemplateExport(), 'template_jadwal_asts.xlsx');
    }
}